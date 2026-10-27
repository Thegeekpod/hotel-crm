<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PosItem;
use App\Models\StoreCategory;
use App\Models\StoreMovement;

class StoreController extends Controller
{
    /**
     * Display Restaurant Store & Menu Management.
     * Manages Menu Categories, Food Dishes, Ingredients, and Plate Stock for POS.
     */
    public function index(Request $request)
    {
        // 1. Fetch Menu Categories
        $categories = StoreCategory::withCount('posItems')->orderBy('name')->get();

        // 2. Fetch Menu Items (Dishes with ingredients & plate counts)
        $query = PosItem::query();

        // Filter by Menu Category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by Stock Health
        if ($request->filled('stock_status') && $request->stock_status !== 'all') {
            if ($request->stock_status === 'low_stock') {
                $query->whereColumn('available_plates', '<=', 'min_alert_plates')
                      ->where('available_plates', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('available_plates', '<=', 0);
            } elseif ($request->stock_status === 'optimal') {
                $query->whereColumn('available_plates', '>', 'min_alert_plates');
            }
        }

        // Search dish name, code, or ingredient name
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('ingredient_name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $items = $query->orderBy('category')->orderBy('name')->get();

        // 3. Key Store Metrics
        $totalMenusCount = $categories->count();
        $totalItemsCount = PosItem::count();
        $totalPlatesAvailable = PosItem::sum('available_plates');
        $lowStockCount = PosItem::whereColumn('available_plates', '<=', 'min_alert_plates')
            ->where('available_plates', '>', 0)
            ->count();
        $outOfStockCount = PosItem::where('available_plates', '<=', 0)->count();
        $totalValuation = PosItem::select(DB::raw('SUM(price * available_plates) as total_val'))->value('total_val') ?? 0;
        $todayInwardCount = StoreMovement::where('type', 'IN')->whereDate('created_at', today())->count();

        // 4. Recent Stock Movements (Inward stock and POS kitchen deductions)
        $movements = StoreMovement::with('posItem')->latest()->take(25)->get();

        return view('stores.index', compact(
            'categories',
            'items',
            'totalMenusCount',
            'totalItemsCount',
            'totalPlatesAvailable',
            'lowStockCount',
            'outOfStockCount',
            'totalValuation',
            'todayInwardCount',
            'movements'
        ));
    }

    /**
     * Create a new Menu Category (e.g. Breakfast, Starters, Main Course).
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:store_categories,name',
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['status'] = 'Active';
        $category = StoreCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => "Menu Category '{$category->name}' created successfully!",
            'category' => $category,
        ]);
    }

    /**
     * Update an existing Menu Category.
     */
    public function updateCategory(Request $request, StoreCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:store_categories,name,' . $category->id,
            'code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:Active,Inactive',
        ]);

        $oldName = $category->name;
        $category->update($validated);

        // If category name changed, update items
        if ($oldName !== $category->name) {
            PosItem::where('category', $oldName)->update(['category' => $category->name]);
        }

        return response()->json([
            'success' => true,
            'message' => "Menu Category '{$category->name}' updated successfully!",
            'category' => $category,
        ]);
    }

    /**
     * Delete a Menu Category.
     */
    public function deleteCategory(StoreCategory $category)
    {
        if (PosItem::where('category', $category->name)->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete Menu '{$category->name}' because it contains food items. Move or delete the items first.",
            ], 422);
        }

        $name = $category->name;
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => "Menu Category '{$name}' deleted successfully.",
        ]);
    }

    /**
     * Create a new food dish inside a menu with ingredient recipe and plate yield.
     */
    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'emoji' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'ingredient_name' => 'required|string|max:255',
            'raw_stock_qty' => 'required|numeric|min:0',
            'raw_stock_unit' => 'required|string|max:50',
            'per_plate_qty' => 'required|numeric|min:0.01',
            'per_plate_unit' => 'required|string|max:50',
            'available_plates' => 'nullable|integer|min:0',
            'min_alert_plates' => 'required|integer|min:1',
        ]);

        // Auto calculate plates yield if available_plates not provided or zero
        if (empty($validated['available_plates'])) {
            $raw = (float) $validated['raw_stock_qty'];
            $perPlate = (float) $validated['per_plate_qty'];
            $rawUnit = strtolower($validated['raw_stock_unit']);
            $plateUnit = strtolower($validated['per_plate_unit']);

            if (in_array($rawUnit, ['kg', 'kgs']) && in_array($plateUnit, ['gm', 'g', 'grams'])) {
                $validated['available_plates'] = (int) floor(($raw * 1000) / $perPlate);
            } elseif (in_array($rawUnit, ['liter', 'liters', 'l']) && in_array($plateUnit, ['ml', 'milliliters'])) {
                $validated['available_plates'] = (int) floor(($raw * 1000) / $perPlate);
            } else {
                $validated['available_plates'] = (int) floor($raw / $perPlate);
            }
        }

        $validated['emoji'] = $validated['emoji'] ?: '🍽️';
        $validated['status'] = ($validated['available_plates'] <= 0) ? 'Out of Stock' : 'Active';

        $item = PosItem::create($validated);

        // Record opening stock movement
        if ($item->available_plates > 0) {
            StoreMovement::create([
                'pos_item_id' => $item->id,
                'type' => 'IN',
                'quantity' => $item->available_plates,
                'stock_before' => 0,
                'stock_after' => $item->available_plates,
                'reference_no' => 'INIT-' . ($item->code ?: 'ITEM-' . $item->id),
                'issued_to_department' => 'Restaurant Kitchen Store',
                'remarks' => "Opening kitchen stock: {$item->raw_stock_qty} {$item->raw_stock_unit} {$item->ingredient_name} ({$item->per_plate_qty} {$item->per_plate_unit}/plate) = {$item->available_plates} Plates ready for POS sales",
                'performed_by' => 'Chef / F&B Store Keeper',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Food dish '{$item->name}' added to Menu '{$item->category}'! Stock: {$item->available_plates} plates ready.",
            'item' => $item,
        ]);
    }

    /**
     * Get dish details with ingredient info and stock movements.
     */
    public function getItemDetails(PosItem $item)
    {
        $movements = StoreMovement::where('pos_item_id', $item->id)->latest()->take(10)->get();
        return response()->json([
            'success' => true,
            'item' => $item,
            'movements' => $movements,
        ]);
    }

    /**
     * Update food dish details, ingredients, or plate yields.
     */
    public function updateItem(Request $request, PosItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'emoji' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'ingredient_name' => 'required|string|max:255',
            'raw_stock_qty' => 'required|numeric|min:0',
            'raw_stock_unit' => 'required|string|max:50',
            'per_plate_qty' => 'required|numeric|min:0.01',
            'per_plate_unit' => 'required|string|max:50',
            'available_plates' => 'required|integer|min:0',
            'min_alert_plates' => 'required|integer|min:1',
            'status' => 'required|in:Active,Inactive,Out of Stock',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Food dish '{$item->name}' updated! Available: {$item->available_plates} plates.",
            'item' => $item,
        ]);
    }

    /**
     * Delete a food dish from menu.
     */
    public function deleteItem(PosItem $item)
    {
        $name = $item->name;
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => "Item '{$name}' deleted from Restaurant Store.",
        ]);
    }

    /**
     * Inward Stock Receiving ("Koto ki item duklo, kota plate hobe")
     * Replenishes raw kitchen ingredient and adds plates to available inventory.
     */
    public function inwardStock(Request $request)
    {
        $validated = $request->validate([
            'pos_item_id' => 'required|exists:pos_items,id',
            'inward_raw_qty' => 'nullable|numeric|min:0',
            'inward_plates' => 'nullable|integer|min:0',
            'vendor_name' => 'nullable|string|max:150',
            'invoice_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated) {
            $item = PosItem::lockForUpdate()->findOrFail($validated['pos_item_id']);

            $rawQtyIn = (float) ($validated['inward_raw_qty'] ?? 0);
            $platesIn = (int) ($validated['inward_plates'] ?? 0);

            // If plates not provided directly, calculate from raw stock added
            if ($platesIn <= 0 && $rawQtyIn > 0) {
                $perPlate = (float) $item->per_plate_qty;
                $rawUnit = strtolower($item->raw_stock_unit);
                $plateUnit = strtolower($item->per_plate_unit);

                if (in_array($rawUnit, ['kg', 'kgs']) && in_array($plateUnit, ['gm', 'g', 'grams'])) {
                    $platesIn = (int) floor(($rawQtyIn * 1000) / $perPlate);
                } elseif (in_array($rawUnit, ['liter', 'liters', 'l']) && in_array($plateUnit, ['ml', 'milliliters'])) {
                    $platesIn = (int) floor(($rawQtyIn * 1000) / $perPlate);
                } else {
                    $platesIn = (int) floor($rawQtyIn / ($perPlate ?: 1));
                }
            }

            if ($platesIn <= 0 && $rawQtyIn <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter either Inward Raw Quantity or Inward Plates count to replenish.',
                ], 422);
            }

            $platesBefore = $item->available_plates;
            $platesAfter = $platesBefore + $platesIn;

            $item->raw_stock_qty += $rawQtyIn;
            $item->available_plates = $platesAfter;
            if ($item->available_plates > 0 && $item->status === 'Out of Stock') {
                $item->status = 'Active';
            }
            $item->save();

            $refNo = $validated['invoice_no'] ?: ('INWARD-' . date('Ymd-His'));

            StoreMovement::create([
                'pos_item_id' => $item->id,
                'type' => 'IN',
                'quantity' => $platesIn,
                'stock_before' => $platesBefore,
                'stock_after' => $platesAfter,
                'reference_no' => $refNo,
                'issued_to_department' => 'Restaurant Kitchen Store',
                'remarks' => "Inward Received: +{$rawQtyIn} {$item->raw_stock_unit} {$item->ingredient_name} (+{$platesIn} Plates added). " . (!empty($validated['remarks']) ? $validated['remarks'] : ''),
                'performed_by' => 'Chef / F&B Store Keeper',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Received inward stock for {$item->name}! Added +{$platesIn} plates. Total now available: {$platesAfter} plates.",
                'item' => $item,
            ]);
        });
    }

    /**
     * Kitchen Issue / Spoilage Adjustment.
     */
    public function issueStock(Request $request)
    {
        $validated = $request->validate([
            'pos_item_id' => 'required|exists:pos_items,id',
            'plates_issued' => 'required|integer|min:1',
            'reason' => 'required|string|max:255', // Kitchen Tasting, Spoilage, Staff Meal, Banquet Transfer
            'remarks' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated) {
            $item = PosItem::lockForUpdate()->findOrFail($validated['pos_item_id']);

            $issueQty = (int) $validated['plates_issued'];
            if ($item->available_plates < $issueQty) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot issue {$issueQty} plates. Only {$item->available_plates} plates available in kitchen stock.",
                ], 422);
            }

            $platesBefore = $item->available_plates;
            $platesAfter = $platesBefore - $issueQty;

            $item->available_plates = $platesAfter;
            if ($item->available_plates <= 0) {
                $item->status = 'Out of Stock';
            }
            $item->save();

            StoreMovement::create([
                'pos_item_id' => $item->id,
                'type' => 'OUT',
                'quantity' => $issueQty,
                'stock_before' => $platesBefore,
                'stock_after' => $platesAfter,
                'reference_no' => 'MANUAL-ISSUE-' . date('Ymd-His'),
                'issued_to_department' => 'Kitchen',
                'remarks' => "{$validated['reason']} (-{$issueQty} plates). " . (!empty($validated['remarks']) ? $validated['remarks'] : ''),
                'performed_by' => 'Chef / Kitchen Supervisor',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Deducted {$issueQty} plates for {$validated['reason']}. Remaining: {$platesAfter} plates.",
                'item' => $item,
            ]);
        });
    }
}
