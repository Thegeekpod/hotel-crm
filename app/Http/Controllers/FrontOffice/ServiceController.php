<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HotelService;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceOrderItem;
use App\Models\Room;
use App\Models\Booking;

class ServiceController extends Controller
{
    /**
     * Display the Front Office Extra Services & In-Room Dining module.
     */
    public function index(Request $request)
    {
        // 1. Hotel Services Catalog (Active only for guest orders)
        $servicesQuery = HotelService::where('status', 'Active')->orderBy('category')->orderBy('name');
        if ($request->filled('category') && $request->category !== 'all') {
            $servicesQuery->where('category', $request->category);
        }
        $services = $servicesQuery->get();

        // 2. Room Service & Extra Amenity Orders
        $ordersQuery = RoomServiceOrder::with(['room', 'booking.guest', 'items.service'])->latest();

        if ($request->filled('order_status') && $request->order_status !== 'all') {
            $ordersQuery->where('order_status', $request->order_status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $ordersQuery->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhere('room_number', 'like', "%{$s}%")
                  ->orWhere('guest_name', 'like', "%{$s}%");
            });
        }

        $roomOrders = $ordersQuery->get();

        // 3. Occupied & Active Rooms for fast order placement
        $activeRooms = Room::with(['activeBooking.guest', 'categoryRelation'])->orderBy('room_number')->get();

        // 4. KPIs
        $totalServicesCount = HotelService::where('status', 'Active')->count();
        $activeRoomOrdersCount = RoomServiceOrder::whereIn('order_status', ['Received', 'Preparing', 'Out for Delivery'])->count();
        $deliveredOrdersCount = RoomServiceOrder::where('order_status', 'Delivered')->count();
        $totalServicesRevenue = (float) RoomServiceOrder::sum('total_amount');

        $allCategories = HotelService::where('status', 'Active')
            ->distinct()
            ->pluck('category')
            ->toArray();

        return view('frontoffice.services.index', compact(
            'services',
            'roomOrders',
            'activeRooms',
            'totalServicesCount',
            'activeRoomOrdersCount',
            'deliveredOrdersCount',
            'totalServicesRevenue',
            'allCategories'
        ));
    }

    /**
     * Store a new in-room dining or guest amenity order.
     */
    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'payment_status' => 'required|string',
            'served_by' => 'nullable|string|max:100',
            'special_instructions' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.service_id' => 'nullable|exists:hotel_services,id',
            'items.*.name' => 'nullable|string|max:255',
        ]);

        $room = Room::with('activeBooking.guest')->findOrFail($validated['room_id']);
        $booking = $room->activeBooking;
        $guestName = $booking?->guest?->guest_name ?? ($request->guest_name ?: 'Room ' . $room->room_number . ' Guest');

        return DB::transaction(function () use ($validated, $room, $booking, $guestName) {
            // Generate Order Number
            $orderCount = RoomServiceOrder::count() + 1;
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad($orderCount, 3, '0', STR_PAD_LEFT);

            // Compute line totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $service = !empty($item['service_id']) ? HotelService::find($item['service_id']) : null;
                $name = $service ? $service->name : ($item['name'] ?? 'Extra Service Item');
                $price = (float) ($item['price'] ?? ($service ? $service->price : 0));
                $qty = (int) ($item['quantity'] ?? 1);
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'hotel_service_id' => $service?->id,
                    'item_name' => $name,
                    'unit_price' => $price,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                    'instructions' => null,
                ];
            }

            $taxAmount = round($subtotal * 0.05, 2); // 5% GST on hotel F&B / services
            $totalAmount = $subtotal + $taxAmount;

            $order = RoomServiceOrder::create([
                'order_number' => $orderNumber,
                'room_id' => $room->id,
                'booking_id' => $booking?->id,
                'guest_name' => $guestName,
                'room_number' => (string) $room->room_number,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_status' => $validated['payment_status'],
                'order_status' => 'Received',
                'delivery_time' => now()->addMinutes(35)->format('h:i A'),
                'special_instructions' => $validated['special_instructions'] ?? null,
                'served_by' => $validated['served_by'] ?? 'Room Service Desk',
            ]);

            foreach ($itemsData as $it) {
                $order->items()->create($it);
            }

            return response()->json([
                'success' => true,
                'message' => "Order #{$orderNumber} placed successfully for Room {$room->room_number}!",
                'order' => $order->load('items'),
            ]);
        });
    }

    /**
     * Retrieve order details for modal dossier view.
     */
    public function getOrderDetails($id)
    {
        $order = RoomServiceOrder::with(['room', 'booking.guest', 'items.service'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    /**
     * Update order delivery progress status.
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:Received,Preparing,Out for Delivery,Delivered,Cancelled',
        ]);

        $order = RoomServiceOrder::findOrFail($id);
        $order->order_status = $validated['order_status'];
        $order->save();

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} status updated to {$order->order_status}.",
            'order' => $order,
        ]);
    }
}
