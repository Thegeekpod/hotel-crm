<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PosItem;
use App\Models\AppSetting;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Room;
use App\Models\Booking;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceOrderItem;
use App\Models\StoreItem;
use App\Models\StoreMovement;
use App\Models\TableManage;
use App\Services\FoodAdvanceService;

class PosController extends Controller
{
    /**
     * Display the Sea Breeze Dining & In-Room Dining POS screen.
     */
    public function index(Request $request)
    {
        // 1. Fetch Menu Items grouped by Category
        $itemsQuery = PosItem::where('status', '!=', 'Inactive');
        
        if ($request->filled('category') && $request->category !== 'all') {
            $itemsQuery->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $itemsQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $menuItems = $itemsQuery->with(['ingredients.storeItem', 'storeItem'])->orderBy('category')->orderBy('name')->get();

        // 2. Currently residing Checked In guests (Only checked in status & primary guests)
        $validStatuses = ['Checked In', 'Stay Over'];
        $rooms = Room::with(['bookings' => function ($q) use ($validStatuses) {
            $q->whereIn('status', $validStatuses)
              ->with(['guest', 'primaryBooking.guest'])
              ->orderByRaw('CASE WHEN primary_booking_id IS NULL THEN 0 ELSE 1 END')
              ->latest('id');
        }])
        ->whereHas('bookings', function ($q) use ($validStatuses) {
            $q->whereIn('status', $validStatuses);
        })
        ->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')
        ->get();

        $selectableRooms = collect();
        foreach ($rooms as $room) {
            $primaryBooking = $room->bookings->first(function ($b) {
                return is_null($b->primary_booking_id);
            }) ?? $room->bookings->first();

            if (!$primaryBooking) continue;

            $primaryGuest = $primaryBooking->primaryBooking?->guest ?? $primaryBooking->guest;
            if (!$primaryGuest) continue;

            $faSummary = FoodAdvanceService::calculateSummary($primaryBooking);

            $selectableRooms->push((object)[
                'id' => $room->id,
                'room_number' => $room->room_number,
                'guest_name' => trim($primaryGuest->guest_name),
                'booking_id' => $primaryBooking->id,
                'status' => $primaryBooking->status,
                'has_food_advance' => $faSummary['has_food_advance'],
                'food_advance_amount' => $faSummary['advance_amount'],
                'remaining_advance' => $faSummary['remaining_advance'],
                'used_food_advance' => $faSummary['used_amount'],
                'fa_summary' => $faSummary,
            ]);
        }

        // 3. Today's POS Orders (both Bills and KOTs)
        $recentOrders = PosOrder::with(['items', 'room', 'booking.guest'])
            ->latest()
            ->take(25)
            ->get();

        // 4. Metrics
        $todayOrdersCount = PosOrder::whereDate('created_at', today())->count();
        $todaySalesTotal = PosOrder::whereDate('created_at', today())
            ->where('is_kot', false)
            ->where('status', '!=', 'Cancelled')
            ->sum('total_amount');
        $activeKotCount = PosOrder::whereIn('status', ['Received', 'In Kitchen'])
            ->where(function ($q) {
                $q->where('is_kot', true)->orWhere('payment_status', 'Pending');
            })
            ->count();
        $chargedToRoomCount = PosOrder::whereDate('created_at', today())
            ->where('billing_type', 'Charged to Room Folio')
            ->where('is_kot', false)
            ->count();

        // Categories list directly from Store Menus
        $menuCategories = \App\Models\StoreCategory::where('status', 'Active')->orderBy('name')->pluck('name')->toArray();
        if (empty($menuCategories)) {
            $menuCategories = ['Breakfast', 'Starters', 'Main Course', 'Beverages', 'Desserts'];
        }

        // 5. Tables: With their active running KOTs
        $defaultGstPos = (float) AppSetting::get('default_gst_pos', 12.00);

        $allTables = TableManage::with(['activeKots' => function ($q) {
            $q->with(['items', 'room', 'booking.guest'])->orderBy('id', 'asc');
        }])->orderBy('table_number')->get();

        $availableTables = $allTables->where('status', 'available')->values();

        // 6. Build structured table active sessions map for the frontend
        $tableSessions = [];
        foreach ($allTables as $tbl) {
            $kots = $tbl->activeKots;
            if ($kots->isNotEmpty()) {
                $firstKot = $kots->first();
                $allItems = collect();
                $consolidatedMap = [];
                $subtotal = 0;

                foreach ($kots as $k) {
                    foreach ($k->items as $it) {
                        $lineTotal = (float) $it->line_total;
                        $subtotal += $lineTotal;

                        $allItems->push([
                            'kot_id' => $k->id,
                            'kot_number' => $k->order_number,
                            'item_name' => $it->item_name,
                            'unit_price' => (float)$it->unit_price,
                            'quantity' => (int)$it->quantity,
                            'line_total' => $lineTotal,
                            'instructions' => $it->instructions,
                        ]);

                        $key = $it->item_name . '_' . $it->unit_price;
                        if (!isset($consolidatedMap[$key])) {
                            $consolidatedMap[$key] = [
                                'pos_item_id' => $it->pos_item_id,
                                'item_name' => $it->item_name,
                                'unit_price' => (float)$it->unit_price,
                                'quantity' => 0,
                                'line_total' => 0.0,
                                'instructions' => $it->instructions,
                            ];
                        }
                        $consolidatedMap[$key]['quantity'] += (int)$it->quantity;
                        $consolidatedMap[$key]['line_total'] += $lineTotal;
                    }
                }

                $taxAmount = round($subtotal * ($defaultGstPos / 100), 2);
                $totalAmount = round($subtotal + $taxAmount, 2);

                $tableSessions[$tbl->id] = [
                    'table_id' => $tbl->id,
                    'table_number' => $tbl->table_number,
                    'table_name' => $tbl->table_name,
                    'order_type' => $firstKot->order_type,
                    'room_id' => $firstKot->room_id,
                    'room_number' => $firstKot->room_number,
                    'booking_id' => $firstKot->booking_id,
                    'customer_name' => $firstKot->customer_name,
                    'customer_phone' => $firstKot->customer_phone,
                    'kots_count' => $kots->count(),
                    'kots' => $kots->map(function ($k) {
                        return [
                            'id' => $k->id,
                            'order_number' => $k->order_number,
                            'time' => $k->created_at->format('h:i A'),
                            'items_count' => $k->items->sum('quantity'),
                            'subtotal' => (float)$k->subtotal,
                            'special_instructions' => $k->special_instructions,
                        ];
                    })->values(),
                    'raw_items' => $allItems,
                    'consolidated_items' => array_values($consolidatedMap),
                    'subtotal' => $subtotal,
                    'gst_rate' => $defaultGstPos,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                ];
            }
        }

        return view('pos.index', compact(
            'menuItems',
            'selectableRooms',
            'recentOrders',
            'todayOrdersCount',
            'todaySalesTotal',
            'activeKotCount',
            'chargedToRoomCount',
            'menuCategories',
            'availableTables',
            'allTables',
            'defaultGstPos',
            'tableSessions'
        ));
    }

    /**
     * Display dedicated POS Orders & KOT History page.
     */
    public function ordersIndex(Request $request)
    {
        $query = PosOrder::with(['items', 'room', 'booking.guest', 'parentOrder'])->latest();

        // Search
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhere('kot_number', 'like', "%{$s}%")
                  ->orWhere('customer_name', 'like', "%{$s}%")
                  ->orWhere('customer_phone', 'like', "%{$s}%")
                  ->orWhere('table_number', 'like', "%{$s}%")
                  ->orWhere('room_number', 'like', "%{$s}%")
                  ->orWhereHas('items', function ($iq) use ($s) {
                      $iq->where('item_name', 'like', "%{$s}%");
                  });
            });
        }

        // Order Type filter
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('order_type', $request->type);
        }

        // Document Nature filter (KOT vs Final Bill)
        if ($request->filled('nature') && $request->nature !== 'all') {
            if ($request->nature === 'kot') {
                $query->where('is_kot', true);
            } elseif ($request->nature === 'bill') {
                $query->where('is_kot', false);
            }
        }

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Billing Mode filter
        if ($request->filled('billing_type') && $request->billing_type !== 'all') {
            $query->where('billing_type', $request->billing_type);
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Calculate summary metrics
        $totalOrdersCount = PosOrder::count();
        $totalSales = PosOrder::where('is_kot', false)->where('status', '!=', 'Cancelled')->sum('total_amount');
        $kitchenActiveCount = PosOrder::whereIn('status', ['Received', 'In Kitchen'])->count();
        $completedCount = PosOrder::where('status', 'Completed')->count();
        $cancelledCount = PosOrder::where('status', 'Cancelled')->count();
        $roomFolioCount = PosOrder::where('billing_type', 'Charged to Room Folio')->where('is_kot', false)->count();

        // Backend Pagination
        $perPage = $request->query('per_page', 10);
        if ($perPage === 'all') {
            $orders = $query->paginate(1000)->withQueryString();
        } else {
            $perPage = (int)$perPage > 0 ? (int)$perPage : 10;
            $orders = $query->paginate($perPage)->withQueryString();
        }

        return view('pos.orders', compact(
            'orders',
            'totalOrdersCount',
            'totalSales',
            'kitchenActiveCount',
            'completedCount',
            'cancelledCount',
            'roomFolioCount'
        ));
    }

    /**
     * Store and process a POS Order or KOT Ticket.
     * 
     * Scenarios supported:
     * 1. Room Dining: Direct immediate bill. No multiple KOTs needed.
     * 2. Table Dine-in: Can punch multiple KOTs during dining session. Table marked booked.
     * 3. Takeaway with Table: Can punch multiple KOTs during dining session. Table marked booked.
     */
    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'order_type' => 'required|in:Room Dining,Table Dine-in,Takeaway',
            'action' => 'nullable|in:kot,bill',
            'is_kot' => 'nullable|boolean',
            'room_id' => 'nullable|exists:rooms,id',
            'table_number' => 'nullable|string|max:150',
            'table_id' => 'nullable|integer',
            'customer_name' => 'nullable|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'billing_type' => 'nullable|in:Charged to Room Folio,Cash Paid,Digital UPI / Card Paid,Pending Settlement',
            'special_instructions' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.pos_item_id' => 'nullable|exists:pos_items,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $isRoomDining = ($validated['order_type'] === 'Room Dining');
            
            // For Room Dining: Always a direct Bill (Scenario 1)
            // For Table Dine-in & Takeaway: Default to KOT if action is 'kot' or is_kot flag set, or if not specified
            $isKot = false;
            if ($isRoomDining) {
                $isKot = false;
            } else {
                if ($request->has('is_kot')) {
                    $isKot = filter_var($request->input('is_kot'), FILTER_VALIDATE_BOOLEAN);
                } elseif ($request->input('action') === 'kot') {
                    $isKot = true;
                } else {
                    // If no explicit billing type provided or billing type is Pending Settlement, it's a KOT
                    $isKot = empty($validated['billing_type']) || $validated['billing_type'] === 'Pending Settlement';
                }
            }

            $room = null;
            $booking = null;
            $roomNumber = null;
            $customerName = $validated['customer_name'] ?? ($isRoomDining ? 'Room Guest' : 'Walk-in Guest');

            // Manage table record & string
            $tableRecord = null;
            $finalTableString = null;
            if (!empty($validated['table_id'] ?? $request->input('table_id'))) {
                $tableRecord = TableManage::find($validated['table_id'] ?? $request->input('table_id'));
            }
            if (!$tableRecord && !empty($validated['table_number'])) {
                $rawTable = trim($validated['table_number']);
                $tableRecord = TableManage::where('table_number', $rawTable)
                    ->orWhere(DB::raw("CONCAT(table_number, ' (', table_name, ')')"), $rawTable)
                    ->orWhere(DB::raw("CONCAT(table_number, ' - ', table_name)"), $rawTable)
                    ->first();
            }

            if ($tableRecord) {
                $finalTableString = $tableRecord->table_number . ' (' . $tableRecord->table_name . ')';
                // Mark table as booked when KOT or order is placed
                $tableRecord->update(['status' => 'booked']);
            } elseif (!empty($validated['table_number'])) {
                $finalTableString = trim($validated['table_number']);
            }

            // If table has existing active KOTs, inherit room / guest if not supplied
            if ($tableRecord && empty($validated['room_id'])) {
                $existingActiveKot = PosOrder::where('table_id', $tableRecord->id)
                    ->where('is_kot', true)
                    ->whereNull('parent_order_id')
                    ->where('status', '!=', 'Cancelled')
                    ->where('payment_status', 'Pending')
                    ->latest()
                    ->first();

                if ($existingActiveKot) {
                    if ($existingActiveKot->room_id) {
                        $validated['room_id'] = $existingActiveKot->room_id;
                    }
                    if ($existingActiveKot->customer_name && $customerName === 'Walk-in Guest') {
                        $customerName = $existingActiveKot->customer_name;
                    }
                }
            }

            // Room lookup if provided
            if (!empty($validated['room_id'])) {
                $room = Room::with(['bookings' => function ($q) {
                    $q->whereIn('status', ['Checked In', 'Stay Over'])
                      ->with(['guest', 'primaryBooking.guest'])
                      ->orderByRaw('CASE WHEN primary_booking_id IS NULL THEN 0 ELSE 1 END')
                      ->latest('id');
                }])->find($validated['room_id']);

                if ($room) {
                    $roomNumber = $room->room_number;
                    $booking = $room->bookings->first(function ($b) {
                        return is_null($b->primary_booking_id);
                    }) ?? $room->bookings->first() ?? $room->activeBooking;

                    if ($booking) {
                        $primaryGuest = $booking->primaryBooking?->guest ?? $booking->guest;
                        if ($primaryGuest) {
                            $customerName = $primaryGuest->guest_name;
                        }
                    }
                }
            }

            if ($validated['order_type'] === 'Table Dine-in' && ($customerName === 'Walk-in Guest' || empty($customerName)) && $finalTableString) {
                $customerName = $finalTableString . ' Guest';
            }

            // Subtotal calculation
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            $taxRate = (float) AppSetting::get('default_gst_pos', 12.00);
            $taxAmount = round($subtotal * ($taxRate / 100), 2);
            $totalAmount = round($subtotal + $taxAmount, 2);

            // Determine KOT sequence for this table
            $kotSequence = 1;
            if ($tableRecord) {
                $prevKotsCount = PosOrder::where('table_id', $tableRecord->id)
                    ->where('is_kot', true)
                    ->whereNull('parent_order_id')
                    ->where('status', '!=', 'Cancelled')
                    ->count();
                $kotSequence = $prevKotsCount + 1;
            }

            // Reference numbers
            if ($isKot) {
                $orderNumber = $this->generateUniqueKotNumber();
                $billingType = 'Pending Settlement';
                $paymentStatus = 'Pending';
                $status = 'In Kitchen';
            } else {
                $orderNumber = $this->generateUniqueBillNumber();
                $billingType = $validated['billing_type'] ?? 'Cash Paid';
                $paymentStatus = match ($billingType) {
                    'Charged to Room Folio' => 'Charged to Room Folio',
                    'Cash Paid', 'Digital UPI / Card Paid' => 'Paid',
                    default => 'Pending',
                };
                $status = 'In Kitchen';
            }

            // Sync to room_service_orders ONLY for direct Bills (not intermediate KOTs)
            $roomServiceOrderId = null;
            if (!$isKot && $room && $booking) {
                $rsOrderSequence = RoomServiceOrder::count() + 1;
                $rsOrderNumber = 'ORD-' . date('Y') . '-' . str_pad($rsOrderSequence, 4, '0', STR_PAD_LEFT);

                $rsPaymentStatus = match ($billingType) {
                    'Charged to Room Folio' => 'Charged to Room Folio',
                    'Cash Paid' => 'Paid via Cash',
                    'Digital UPI / Card Paid' => 'Paid via UPI/Card',
                    default => 'Paid',
                };

                $rsOrderStatus = ($billingType === 'Charged to Room Folio') ? 'Preparing' : 'Completed';

                $instructionsNote = ($billingType === 'Charged to Room Folio')
                    ? 'POS Dining (Folio): '
                    : 'POS Dining (' . $billingType . ' - Settled on Spot): ';
                $instructionsNote .= (!empty($finalTableString) ? '[' . $finalTableString . '] ' : '') . ($validated['special_instructions'] ?? 'None');

                // Check Company Food Advance for folio charges
                $foodAdvImpact = null;
                if ($billingType === 'Charged to Room Folio') {
                    $foodAdvImpact = FoodAdvanceService::calculateOrderImpact($booking, $subtotal);
                    if ($foodAdvImpact && $foodAdvImpact['has_food_advance']) {
                        $taxRate = $foodAdvImpact['gst_rate'];
                        $taxAmount = $foodAdvImpact['gst_amount'];
                        $totalAmount = round($subtotal + $taxAmount, 2);
                        if ($foodAdvImpact['excess_amount'] > 0) {
                            $instructionsNote .= " [Food Advance: ₹" . number_format($foodAdvImpact['covered_amount'], 2) . " covered | Excess: ₹" . number_format($foodAdvImpact['charged_total'], 2) . " Folio Due]";
                        } else {
                            $instructionsNote .= " [Food Advance: ₹" . number_format($foodAdvImpact['covered_amount'], 2) . " cut | 100% Covered]";
                        }
                    }
                }

                $rsOrder = RoomServiceOrder::create([
                    'order_number' => $rsOrderNumber,
                    'room_id' => $room->id,
                    'booking_id' => $booking->id,
                    'guest_name' => $customerName,
                    'room_number' => $roomNumber,
                    'table_number' => $finalTableString,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'payment_status' => $rsPaymentStatus,
                    'order_status' => $rsOrderStatus,
                    'special_instructions' => $instructionsNote,
                    'served_by' => 'POS Cashier',
                ]);

                foreach ($validated['items'] as $it) {
                    RoomServiceOrderItem::create([
                        'room_service_order_id' => $rsOrder->id,
                        'hotel_service_id' => null,
                        'item_name' => $it['name'],
                        'unit_price' => $it['price'],
                        'quantity' => $it['quantity'],
                        'line_total' => $it['price'] * $it['quantity'],
                        'instructions' => $it['instructions'] ?? null,
                    ]);
                }

                $roomServiceOrderId = $rsOrder->id;
            }

            // Create Master POS Order / KOT Ticket
            $posOrder = PosOrder::create([
                'order_number' => $orderNumber,
                'order_type' => $validated['order_type'],
                'is_kot' => $isKot,
                'kot_number' => $isKot ? $orderNumber : null,
                'kot_sequence' => $kotSequence,
                'table_number' => $finalTableString,
                'table_id' => $tableRecord?->id,
                'room_id' => $room?->id,
                'booking_id' => $booking?->id,
                'customer_name' => $customerName,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'room_number' => $roomNumber,
                'subtotal' => $subtotal,
                'tax_percentage' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'billing_type' => $billingType,
                'payment_status' => $paymentStatus,
                'status' => $status,
                'server_name' => 'Debashis Roy (Cashier)',
                'special_instructions' => $validated['special_instructions'] ?? null,
                'room_service_order_id' => $roomServiceOrderId,
            ]);

            // Save line items & automatically deduct restaurant kitchen store stock!
            foreach ($validated['items'] as $item) {
                PosOrderItem::create([
                    'pos_order_id' => $posOrder->id,
                    'pos_item_id' => $item['pos_item_id'] ?? null,
                    'item_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'line_total' => $item['price'] * $item['quantity'],
                    'instructions' => $item['instructions'] ?? null,
                ]);

                // Direct Kitchen Store Deduction (Plates and Raw Ingredients)
                if (!empty($item['pos_item_id'])) {
                    $posDish = PosItem::lockForUpdate()->find($item['pos_item_id']);
                    if ($posDish) {
                        $platesBefore = $posDish->available_plates;
                        $platesOrdered = (int) $item['quantity'];
                        $platesAfter = max(0, $platesBefore - $platesOrdered);

                        $posDish->available_plates = $platesAfter;
                        if ($platesAfter <= 0) {
                            $posDish->status = 'Out of Stock';
                        }

                        // Deduct raw ingredient stock proportionally if configured
                        if ($posDish->ingredient_name && $posDish->per_plate_qty > 0) {
                            $rawUnit = strtolower($posDish->raw_stock_unit);
                            $plateUnit = strtolower($posDish->per_plate_unit);
                            $perPlate = (float) $posDish->per_plate_qty;

                            if (in_array($rawUnit, ['kg', 'kgs']) && in_array($plateUnit, ['gm', 'g', 'grams'])) {
                                $rawUsed = ($perPlate * $platesOrdered) / 1000;
                            } elseif (in_array($rawUnit, ['liter', 'liters', 'l']) && in_array($plateUnit, ['ml'])) {
                                $rawUsed = ($perPlate * $platesOrdered) / 1000;
                            } else {
                                $rawUsed = $perPlate * $platesOrdered;
                            }
                            $posDish->raw_stock_qty = max(0, round($posDish->raw_stock_qty - $rawUsed, 2));
                        }

                        $posDish->save();

                        StoreMovement::create([
                            'pos_item_id' => $posDish->id,
                            'type' => 'OUT',
                            'quantity' => $platesOrdered,
                            'stock_before' => $platesBefore,
                            'stock_after' => $platesAfter,
                            'reference_no' => $orderNumber,
                            'issued_to_department' => 'Restaurant Dining / Room Service',
                            'remarks' => "Order #{$orderNumber}: {$item['name']} ({$platesOrdered} plates. Remaining: {$platesAfter} plates)",
                            'performed_by' => 'POS Auto Kitchen Deduction',
                        ]);
                    }
                }
            }

            $posOrder->load(['items', 'room', 'booking.guest', 'tableManage']);

            $successMsg = '';
            if ($isKot) {
                $destName = $finalTableString ?: ($roomNumber ? "Room {$roomNumber}" : 'Kitchen');
                $successMsg = "KOT #{$kotSequence} ({$orderNumber}) successfully sent to Kitchen for {$destName}!";
            } elseif ($billingType === 'Charged to Room Folio') {
                $successMsg = "Charge of ₹" . number_format($totalAmount, 2) . " successfully posted to Room {$roomNumber} Folio! Outstanding balance updated.";
            } elseif ($validated['order_type'] === 'Takeaway') {
                $successMsg = "Takeaway Order {$orderNumber} (₹" . number_format($totalAmount, 2) . ") successfully placed and settled via {$billingType} for {$customerName}!";
            } else {
                $successMsg = $room
                    ? "Order {$orderNumber} (₹" . number_format($totalAmount, 2) . ") settled via {$billingType} and recorded under Room {$roomNumber} ({$customerName})!"
                    : "Order {$orderNumber} successfully placed and settled via {$billingType}!";
            }

            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'order' => $posOrder,
                'is_kot' => $isKot,
                'kot_sequence' => $kotSequence,
                'table_id' => $tableRecord?->id,
            ]);
        });
    }

    /**
     * Get active session details for a table (all pending KOTs, items, running total, room info).
     */
    public function getTableSession(TableManage $table)
    {
        $activeKots = PosOrder::where('table_id', $table->id)
            ->where('is_kot', true)
            ->whereNull('parent_order_id')
            ->where('status', '!=', 'Cancelled')
            ->where('payment_status', 'Pending')
            ->with(['items', 'room', 'booking.guest'])
            ->orderBy('id', 'asc')
            ->get();

        if ($activeKots->isEmpty()) {
            return response()->json([
                'success' => true,
                'has_active_session' => false,
                'table' => $table,
            ]);
        }

        $firstKot = $activeKots->first();
        $allItems = collect();
        $consolidatedMap = [];
        $subtotal = 0;

        foreach ($activeKots as $k) {
            foreach ($k->items as $it) {
                $lineTotal = (float) $it->line_total;
                $subtotal += $lineTotal;

                $allItems->push([
                    'kot_id' => $k->id,
                    'kot_number' => $k->order_number,
                    'kot_time' => $k->created_at->format('h:i A'),
                    'item_name' => $it->item_name,
                    'unit_price' => (float)$it->unit_price,
                    'quantity' => (int)$it->quantity,
                    'line_total' => $lineTotal,
                    'instructions' => $it->instructions,
                ]);

                $key = $it->item_name . '_' . $it->unit_price;
                if (!isset($consolidatedMap[$key])) {
                    $consolidatedMap[$key] = [
                        'pos_item_id' => $it->pos_item_id,
                        'item_name' => $it->item_name,
                        'unit_price' => (float)$it->unit_price,
                        'quantity' => 0,
                        'line_total' => 0.0,
                        'instructions' => $it->instructions,
                    ];
                }
                $consolidatedMap[$key]['quantity'] += (int)$it->quantity;
                $consolidatedMap[$key]['line_total'] += $lineTotal;
            }
        }

        $defaultGstPos = (float) AppSetting::get('default_gst_pos', 12.00);
        $taxAmount = round($subtotal * ($defaultGstPos / 100), 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

        return response()->json([
            'success' => true,
            'has_active_session' => true,
            'table' => $table,
            'order_type' => $firstKot->order_type,
            'room_id' => $firstKot->room_id,
            'room_number' => $firstKot->room_number,
            'customer_name' => $firstKot->customer_name,
            'customer_phone' => $firstKot->customer_phone,
            'kots_count' => $activeKots->count(),
            'kots' => $activeKots->map(function ($k) {
                return [
                    'id' => $k->id,
                    'order_number' => $k->order_number,
                    'time' => $k->created_at->format('h:i A'),
                    'items_count' => $k->items->sum('quantity'),
                    'subtotal' => (float)$k->subtotal,
                    'special_instructions' => $k->special_instructions,
                ];
            }),
            'raw_items' => $allItems,
            'consolidated_items' => array_values($consolidatedMap),
            'subtotal' => $subtotal,
            'gst_rate' => $defaultGstPos,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
    }

    /**
     * Settle an active table dining session into a Single Consolidated Final Bill.
     * 
     * Requirements:
     * - "Table Dine-in" (Room attached):
     *   3 options: Room attach (unpaid folio), Cash (paid on spot), UPI (paid on spot).
     *   All 3 attach to room records (unpaid or paid).
     * - "Take Away" (Outside guests):
     *   2 options: Cash (paid), UPI (paid).
     * - Bill contains all items ordered across all KOTs.
     * - Table status set to available.
     */
    public function settleTableBill(Request $request, TableManage $table)
    {
        $validated = $request->validate([
            'billing_type' => 'required|in:Charged to Room Folio,Cash Paid,Digital UPI / Card Paid',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($validated, $table, $request) {
            $activeKots = PosOrder::where('table_id', $table->id)
                ->where('is_kot', true)
                ->whereNull('parent_order_id')
                ->where('status', '!=', 'Cancelled')
                ->where('payment_status', 'Pending')
                ->with(['items', 'room', 'booking.guest'])
                ->orderBy('id', 'asc')
                ->get();

            if ($activeKots->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "No active running KOTs found on Table {$table->table_number} ({$table->table_name}).",
                ], 422);
            }

            $firstKot = $activeKots->first();
            $orderType = $firstKot->order_type;
            $roomId = $firstKot->room_id;
            $bookingId = $firstKot->booking_id;
            $customerName = $firstKot->customer_name;
            $customerPhone = $firstKot->customer_phone;
            $roomNumber = $firstKot->room_number;
            $finalTableString = $table->table_number . ' (' . $table->table_name . ')';

            // Validate billing choice: Room Attach is only allowed if room/booking exists
            if ($validated['billing_type'] === 'Charged to Room Folio' && !$roomId) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot attach bill to Room Folio because no room was linked to this table! Please choose Cash or UPI.",
                ], 422);
            }

            // Consolidate all items across all active KOTs
            $consolidatedMap = [];
            $subtotal = 0;

            foreach ($activeKots as $k) {
                foreach ($k->items as $it) {
                    $lineTotal = (float) $it->line_total;
                    $subtotal += $lineTotal;

                    $key = $it->item_name . '_' . (float)$it->unit_price;
                    if (!isset($consolidatedMap[$key])) {
                        $consolidatedMap[$key] = [
                            'pos_item_id' => $it->pos_item_id,
                            'name' => $it->item_name,
                            'price' => (float)$it->unit_price,
                            'quantity' => 0,
                            'line_total' => 0.0,
                            'instructions' => $it->instructions,
                        ];
                    }
                    $consolidatedMap[$key]['quantity'] += (int)$it->quantity;
                    $consolidatedMap[$key]['line_total'] += $lineTotal;
                }
            }

            $consolidatedItems = array_values($consolidatedMap);

            // Taxes and Food Advance impact
            $room = $roomId ? Room::find($roomId) : null;
            $booking = $bookingId ? Booking::find($bookingId) : null;

            $taxRate = (float) AppSetting::get('default_gst_pos', 12.00);
            $taxAmount = round($subtotal * ($taxRate / 100), 2);
            $totalAmount = round($subtotal + $taxAmount, 2);

            $foodAdvImpact = null;
            $advanceCoveredAmount = 0.00;
            $folioChargedAmount = $totalAmount;

            if ($validated['billing_type'] === 'Charged to Room Folio' && $booking) {
                $foodAdvImpact = FoodAdvanceService::calculateOrderImpact($booking, $subtotal);
                if ($foodAdvImpact && $foodAdvImpact['has_food_advance']) {
                    $taxRate = $foodAdvImpact['gst_rate'];
                    $taxAmount = $foodAdvImpact['gst_amount'];
                    $totalAmount = round($subtotal + $taxAmount, 2);
                    $advanceCoveredAmount = $foodAdvImpact['covered_amount'];
                    $folioChargedAmount = $foodAdvImpact['charged_total'];
                }
            }

            // Master Bill Number: POS-YYYYMMDD-XXXX
            $billNumber = $this->generateUniqueBillNumber();

            $paymentStatus = match ($validated['billing_type']) {
                'Charged to Room Folio' => 'Charged to Room Folio',
                'Cash Paid', 'Digital UPI / Card Paid' => 'Paid',
                default => 'Pending',
            };

            // Synchronize with room_service_orders if room & booking exist
            $roomServiceOrderId = null;
            if ($room && $booking) {
                $rsOrderSequence = RoomServiceOrder::count() + 1;
                $rsOrderNumber = 'ORD-' . date('Y') . '-' . str_pad($rsOrderSequence, 4, '0', STR_PAD_LEFT);

                $rsPaymentStatus = match ($validated['billing_type']) {
                    'Charged to Room Folio' => 'Charged to Room Folio',
                    'Cash Paid' => 'Paid via Cash',
                    'Digital UPI / Card Paid' => 'Paid via UPI/Card',
                    default => 'Paid',
                };

                $rsOrderStatus = ($validated['billing_type'] === 'Charged to Room Folio') ? 'Delivered' : 'Completed';

                $instructionsNote = ($validated['billing_type'] === 'Charged to Room Folio')
                    ? 'POS Dine-in Final Bill (Folio - ' . $activeKots->count() . ' KOTs Combined): '
                    : 'POS Dine-in Final Bill (' . $validated['billing_type'] . ' - Settled on Spot): ';
                $instructionsNote .= '[' . $finalTableString . '] ' . ($validated['special_instructions'] ?? 'None');

                if ($foodAdvImpact && $foodAdvImpact['has_food_advance']) {
                    if ($foodAdvImpact['excess_amount'] > 0) {
                        $instructionsNote .= " [Food Advance: ₹" . number_format($advanceCoveredAmount, 2) . " cut | Excess: ₹" . number_format($foodAdvImpact['excess_amount'], 2) . " + 5% GST = ₹" . number_format($folioChargedAmount, 2) . " Folio Due]";
                    } else {
                        $instructionsNote .= " [Food Advance: ₹" . number_format($advanceCoveredAmount, 2) . " cut | 100% Covered (Net Folio: ₹0.00)]";
                    }
                }

                $rsOrder = RoomServiceOrder::create([
                    'order_number' => $rsOrderNumber,
                    'room_id' => $room->id,
                    'booking_id' => $booking->id,
                    'guest_name' => $customerName,
                    'room_number' => $roomNumber,
                    'table_number' => $finalTableString,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'payment_status' => $rsPaymentStatus,
                    'order_status' => $rsOrderStatus,
                    'special_instructions' => $instructionsNote,
                    'served_by' => 'POS Cashier',
                ]);

                foreach ($consolidatedItems as $ci) {
                    RoomServiceOrderItem::create([
                        'room_service_order_id' => $rsOrder->id,
                        'hotel_service_id' => null,
                        'item_name' => $ci['name'],
                        'unit_price' => $ci['price'],
                        'quantity' => $ci['quantity'],
                        'line_total' => $ci['line_total'],
                        'instructions' => $ci['instructions'] ?? null,
                    ]);
                }

                $roomServiceOrderId = $rsOrder->id;
            }

            // Create Master Final Bill PosOrder
            $masterBill = PosOrder::create([
                'order_number' => $billNumber,
                'order_type' => $orderType,
                'is_kot' => false,
                'kot_number' => null,
                'kot_sequence' => 1,
                'table_number' => $finalTableString,
                'table_id' => $table->id,
                'room_id' => $roomId,
                'booking_id' => $bookingId,
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'room_number' => $roomNumber,
                'subtotal' => $subtotal,
                'tax_percentage' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'billing_type' => $validated['billing_type'],
                'payment_status' => $paymentStatus,
                'status' => 'Completed',
                'server_name' => 'Debashis Roy (Cashier)',
                'special_instructions' => $validated['special_instructions'] ?? ("Consolidated bill for {$activeKots->count()} KOTs"),
                'room_service_order_id' => $roomServiceOrderId,
            ]);

            // Save consolidated line items
            foreach ($consolidatedItems as $ci) {
                PosOrderItem::create([
                    'pos_order_id' => $masterBill->id,
                    'pos_item_id' => $ci['pos_item_id'] ?? null,
                    'item_name' => $ci['name'],
                    'unit_price' => $ci['price'],
                    'quantity' => $ci['quantity'],
                    'line_total' => $ci['line_total'],
                    'instructions' => $ci['instructions'] ?? null,
                ]);
            }

            // Mark all active KOTs as settled and linked to this Master Bill
            foreach ($activeKots as $kot) {
                $kot->update([
                    'parent_order_id' => $masterBill->id,
                    'payment_status' => 'Settled',
                    'status' => 'Completed',
                ]);
            }

            // Free table: set status to available
            $table->update(['status' => 'available']);

            $masterBill->load(['items', 'room', 'booking.guest', 'tableManage']);

            $successMsg = '';
            if ($validated['billing_type'] === 'Charged to Room Folio') {
                $successMsg = "Table {$table->table_number} Bill of ₹" . number_format($totalAmount, 2) . " successfully posted to Room {$roomNumber} Folio! Table is now Available.";
            } else {
                $successMsg = $room
                    ? "Table {$table->table_number} Bill of ₹" . number_format($totalAmount, 2) . " settled via {$validated['billing_type']} and recorded under Room {$roomNumber}! Table is now Available."
                    : "Table {$table->table_number} Bill of ₹" . number_format($totalAmount, 2) . " successfully settled via {$validated['billing_type']}! Table is now Available.";
            }

            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'bill' => $masterBill,
                'table' => $table,
                'kots_count' => $activeKots->count(),
            ]);
        });
    }

    /**
     * Get order details for KOT ticket and thermal print slip.
     */
    public function getOrderDetails(PosOrder $order)
    {
        $order->load(['items', 'room', 'booking.guest', 'tableManage', 'kotOrders.items']);
        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    /**
     * Update order status (In Kitchen, Completed, Cancelled).
     */
    public function updateOrderStatus(Request $request, PosOrder $order)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:Received,In Kitchen,Completed,Cancelled',
            ]);

            $order->update(['status' => $validated['status']]);

            // Sync to room service order if applicable
            if ($order->room_service_order_id && $order->roomServiceOrder) {
                $rsStatus = match ($validated['status']) {
                    'In Kitchen' => 'Preparing',
                    'Completed' => 'Delivered',
                    'Cancelled' => 'Cancelled',
                    default => 'Received',
                };
                $order->roomServiceOrder->update(['order_status' => $rsStatus]);
            }

            return response()->json([
                'success' => true,
                'message' => "Order #{$order->order_number} status updated to {$validated['status']}.",
                'status' => $order->status,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('POS updateOrderStatus error: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all tables list for POS Table Manage modal.
     */
    public function getTablesList()
    {
        $tables = TableManage::with(['activeKots.items', 'activeKots.room', 'activeKots.booking.guest'])
            ->orderBy('table_number')
            ->get();
        $available = TableManage::where('status', 'available')->orderBy('table_number')->get();

        return response()->json([
            'success' => true,
            'tables' => $tables,
            'availableTables' => $available,
        ]);
    }

    /**
     * Update table status (e.g. booked to available, or available to booked).
     */
    public function updateTableStatus(Request $request, TableManage $table)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,booked,inactive',
        ]);

        $status = $validated['status'];

        $table->update([
            'status' => $status,
        ]);

        $allTables = TableManage::with(['activeKots.items', 'activeKots.room', 'activeKots.booking.guest'])
            ->orderBy('table_number')
            ->get();
        $availableTables = TableManage::where('status', 'available')->orderBy('table_number')->get();

        return response()->json([
            'success' => true,
            'message' => "Table {$table->table_number} ({$table->table_name}) status changed to " . ucfirst($status) . ".",
            'table' => $table,
            'tables' => $allTables,
            'availableTables' => $availableTables,
        ]);
    }

    /**
     * Create new food menu item.
     */
    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|in:Breakfast,Starters,Main Course,Beverages,Desserts',
            'price' => 'required|numeric|min:0',
            'emoji' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:Active,Inactive,Out of Stock',
        ]);

        $item = PosItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu item added successfully!',
            'item' => $item,
        ]);
    }

    /**
     * Update food menu item.
     */
    public function updateItem(Request $request, PosItem $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|in:Breakfast,Starters,Main Course,Beverages,Desserts',
            'price' => 'required|numeric|min:0',
            'emoji' => 'nullable|string|max:10',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:Active,Inactive,Out of Stock',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully!',
            'item' => $item,
        ]);
    }

    /**
     * Delete / Deactivate food menu item.
     */
    public function deleteItem(PosItem $item)
    {
        $item->delete();
        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully.',
        ]);
    }

    /**
     * Generate unique KOT order number: KOT-YYYYMMDD-XXXX
     */
    private function generateUniqueKotNumber(): string
    {
        $datePrefix = 'KOT-' . date('Ymd') . '-';
        $lastOrder = PosOrder::where('order_number', 'like', $datePrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastOrder && preg_match('/KOT-\d+-(\d+)/', $lastOrder->order_number, $matches)) {
            $nextSeq = ((int)$matches[1]) + 1;
        }

        do {
            $num = $datePrefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            $nextSeq++;
        } while (PosOrder::where('order_number', $num)->exists());

        return $num;
    }

    /**
     * Generate unique Bill order number: POS-YYYYMMDD-XXXX
     */
    private function generateUniqueBillNumber(): string
    {
        $datePrefix = 'POS-' . date('Ymd') . '-';
        $lastOrder = PosOrder::where('order_number', 'like', $datePrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($lastOrder && preg_match('/POS-\d+-(\d+)/', $lastOrder->order_number, $matches)) {
            $nextSeq = ((int)$matches[1]) + 1;
        }

        do {
            $num = $datePrefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
            $nextSeq++;
        } while (PosOrder::where('order_number', $num)->exists());

        return $num;
    }
}
