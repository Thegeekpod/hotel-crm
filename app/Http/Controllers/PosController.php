<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PosItem;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Room;
use App\Models\Booking;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceOrderItem;
use App\Models\StoreItem;
use App\Models\StoreMovement;
use App\Models\TableManage;

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

            $selectableRooms->push((object)[
                'id' => $room->id,
                'room_number' => $room->room_number,
                'guest_name' => trim($primaryGuest->guest_name),
                'booking_id' => $primaryBooking->id,
                'status' => $primaryBooking->status,
            ]);
        }

        // 3. Today's POS Orders
        $recentOrders = PosOrder::with(['items', 'room', 'booking.guest'])
            ->latest()
            ->take(25)
            ->get();

        // 4. Metrics
        $todayOrdersCount = PosOrder::whereDate('created_at', today())->count();
        $todaySalesTotal = PosOrder::whereDate('created_at', today())
            ->where('status', '!=', 'Cancelled')
            ->sum('total_amount');
        $activeKotCount = PosOrder::whereIn('status', ['Received', 'In Kitchen'])->count();
        $chargedToRoomCount = PosOrder::whereDate('created_at', today())
            ->where('billing_type', 'Charged to Room Folio')
            ->count();

        // Categories list directly from Store Menus
        $menuCategories = \App\Models\StoreCategory::where('status', 'Active')->orderBy('name')->pluck('name')->toArray();
        if (empty($menuCategories)) {
            $menuCategories = ['Breakfast', 'Starters', 'Main Course', 'Beverages', 'Desserts'];
        }

        // 5. Tables: Available for dining selection, and all tables for management modal
        $availableTables = TableManage::where('status', 'available')->orderBy('table_number')->get();
        $allTables = TableManage::orderBy('table_number')->get();

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
            'allTables'
        ));
    }

    /**
     * Store and process a POS Order (Room Folio or Walk-in Cash/UPI).
     */
    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'order_type' => 'required|in:Room Dining,Table Dine-in,Takeaway',
            'room_id' => 'nullable|exists:rooms,id',
            'table_number' => 'nullable|string|max:150',
            'table_id' => 'nullable|integer',
            'customer_name' => 'nullable|string|max:150',
            'customer_phone' => 'nullable|string|max:30',
            'billing_type' => 'required|in:Charged to Room Folio,Cash Paid,Digital UPI / Card Paid',
            'special_instructions' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.pos_item_id' => 'nullable|exists:pos_items,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $room = null;
            $booking = null;
            $roomNumber = null;
            $customerName = $validated['customer_name'] ?? 'Walk-in Guest';

            // Manage and synchronize table booking (table_number & table_name)
            $finalTableString = null;
            if (!empty($validated['table_number'])) {
                $rawTable = trim($validated['table_number']);
                $tableRecord = null;
                if (!empty($validated['table_id'] ?? $request->input('table_id'))) {
                    $tableRecord = TableManage::find($validated['table_id'] ?? $request->input('table_id'));
                }
                if (!$tableRecord) {
                    $tableRecord = TableManage::where('table_number', $rawTable)
                        ->orWhere(DB::raw("CONCAT(table_number, ' (', table_name, ')')"), $rawTable)
                        ->orWhere(DB::raw("CONCAT(table_number, ' - ', table_name)"), $rawTable)
                        ->first();
                }

                if ($tableRecord) {
                    $tableRecord->update([
                        'status' => 'booked',
                    ]);
                    $finalTableString = $tableRecord->table_number . ' (' . $tableRecord->table_name . ')';
                } else {
                    $finalTableString = $rawTable;
                }
            }

            if ($validated['order_type'] === 'Table Dine-in' && ($customerName === 'Walk-in Guest' || empty($customerName)) && $finalTableString) {
                $customerName = $finalTableString . ' Guest';
            }

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

            // Calculate Subtotal & 12% F&B GST
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
            }

            $taxRate = 12.00;
            $taxAmount = round($subtotal * ($taxRate / 100), 2);
            $totalAmount = round($subtotal + $taxAmount, 2);

            // Generate Order Reference: POS-YYYYMMDD-XXXX
            $orderSequence = PosOrder::whereDate('created_at', today())->count() + 1;
            $orderNumber = 'POS-' . date('Ymd') . '-' . str_pad($orderSequence, 4, '0', STR_PAD_LEFT);

            $paymentStatus = match ($validated['billing_type']) {
                'Charged to Room Folio' => 'Charged to Room Folio',
                'Cash Paid', 'Digital UPI / Card Paid' => 'Paid',
                default => 'Pending',
            };

            // Sync to room_service_orders if room and active booking exist
            // - If Charged to Room Folio: payment_status = 'Charged to Room Folio' (adds to outstanding folio balance)
            // - If Cash/UPI Paid: payment_status = 'Paid via Cash' / 'Paid via UPI/Card' (shows under booking as PAID, doesn't add to remaining balance)
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

                $rsOrderStatus = ($validated['billing_type'] === 'Charged to Room Folio') ? 'Preparing' : 'Completed';

                $instructionsNote = ($validated['billing_type'] === 'Charged to Room Folio')
                    ? 'POS Dining (Folio): '
                    : 'POS Dining (' . $validated['billing_type'] . ' - Settled on Spot): ';
                $instructionsNote .= (!empty($finalTableString) ? '[' . $finalTableString . '] ' : '') . ($validated['special_instructions'] ?? 'None');

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

            // Create Master POS Order
            $posOrder = PosOrder::create([
                'order_number' => $orderNumber,
                'order_type' => $validated['order_type'],
                'table_number' => $finalTableString,
                'room_id' => $room?->id,
                'booking_id' => $booking?->id,
                'customer_name' => $customerName,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'room_number' => $roomNumber,
                'subtotal' => $subtotal,
                'tax_percentage' => $taxRate,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'billing_type' => $validated['billing_type'],
                'payment_status' => $paymentStatus,
                'status' => 'In Kitchen',
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
                            'reference_no' => 'POS-' . $orderNumber,
                            'issued_to_department' => 'Restaurant Dining / Room Service',
                            'remarks' => "POS Order #{$orderNumber}: {$item['name']} ({$platesOrdered} plates served. Remaining: {$platesAfter} plates)",
                            'performed_by' => 'POS Auto Kitchen Deduction',
                        ]);
                    }
                }
            }

            $posOrder->load('items');

            return response()->json([
                'success' => true,
                'message' => ($validated['billing_type'] === 'Charged to Room Folio')
                    ? "Charge of ₹" . number_format($totalAmount, 2) . " successfully posted to Room {$roomNumber} Folio! Outstanding balance updated for final settlement."
                    : ($validated['order_type'] === 'Takeaway'
                        ? "Takeaway Order {$orderNumber} (₹" . number_format($totalAmount, 2) . ") successfully placed and settled via {$validated['billing_type']} for {$customerName}!"
                        : ($room ? "Order {$orderNumber} (₹" . number_format($totalAmount, 2) . ") settled via {$validated['billing_type']} and recorded under Room {$roomNumber} ({$customerName}) in Settlement!" : "Order {$orderNumber} successfully placed and settled via {$validated['billing_type']}! KOT generated.")),
                'order' => $posOrder,
            ]);
        });
    }

    /**
     * Get order details for KOT ticket and thermal print slip.
     */
    public function getOrderDetails(PosOrder $order)
    {
        $order->load(['items', 'room', 'booking.guest']);
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
        $tables = TableManage::orderBy('table_number')->get();
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

        $allTables = TableManage::orderBy('table_number')->get();
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
}
