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

        // 2. Occupied / Checked-in Rooms for Room Charge Dropdown
        $occupiedRooms = Room::with(['activeBooking.guest', 'categoryRelation'])
            ->whereHas('activeBooking', function ($q) {
                $q->whereIn('status', ['Confirmed', 'Checked In']);
            })
            ->orderBy('room_number')
            ->get();

        // If no checked-in rooms yet, load all rooms so user can test
        $allRooms = Room::with(['activeBooking.guest', 'categoryRelation'])->orderBy('room_number')->get();
        $selectableRooms = $occupiedRooms->isNotEmpty() ? $occupiedRooms : $allRooms;

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

        return view('pos.index', compact(
            'menuItems',
            'selectableRooms',
            'recentOrders',
            'todayOrdersCount',
            'todaySalesTotal',
            'activeKotCount',
            'chargedToRoomCount',
            'menuCategories'
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
            'table_number' => 'nullable|string|max:50',
            'customer_name' => 'nullable|string|max:150',
            'billing_type' => 'required|in:Charged to Room Folio,Cash Paid,Digital UPI / Card Paid',
            'special_instructions' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.pos_item_id' => 'nullable|exists:pos_items,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($validated) {
            $room = null;
            $booking = null;
            $roomNumber = null;
            $customerName = $validated['customer_name'] ?? 'Walk-in Guest';

            if (!empty($validated['room_id'])) {
                $room = Room::with('activeBooking.guest')->find($validated['room_id']);
                if ($room) {
                    $roomNumber = $room->room_number;
                    $booking = $room->activeBooking;
                    if ($booking && $booking->guest) {
                        $customerName = $booking->guest->guest_name;
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

            // Optional: If charged to room folio and room has active booking, also sync to room_service_orders
            $roomServiceOrderId = null;
            if ($validated['billing_type'] === 'Charged to Room Folio' && $room && $booking) {
                $rsOrderSequence = RoomServiceOrder::count() + 1;
                $rsOrderNumber = 'ORD-' . date('Y') . '-' . str_pad($rsOrderSequence, 4, '0', STR_PAD_LEFT);

                $rsOrder = RoomServiceOrder::create([
                    'order_number' => $rsOrderNumber,
                    'room_id' => $room->id,
                    'booking_id' => $booking->id,
                    'guest_name' => $customerName,
                    'room_number' => $roomNumber,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'payment_status' => 'Charged to Room Folio',
                    'order_status' => 'Preparing',
                    'special_instructions' => 'POS Sales KOT: ' . (!empty($validated['table_number']) ? '[' . $validated['table_number'] . '] ' : '') . ($validated['special_instructions'] ?? 'None'),
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
                'table_number' => $validated['table_number'] ?? null,
                'room_id' => $room?->id,
                'booking_id' => $booking?->id,
                'customer_name' => $customerName,
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
                    ? "Charge of ₹" . number_format($totalAmount, 2) . " successfully posted to Room {$roomNumber} Folio" . (!empty($validated['table_number']) ? " ({$validated['table_number']})" : "") . "! KOT ticket generated."
                    : "Order {$orderNumber} successfully placed and settled! KOT ticket generated.",
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
        $validated = $request->validate([
            'status' => 'required|in:Received,In Kitchen,Completed,Cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        // Sync to room service order if applicable
        if ($order->roomServiceOrder) {
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
