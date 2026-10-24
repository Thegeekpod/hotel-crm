<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BanquetHall;
use App\Models\BanquetBooking;
use App\Models\HotelService;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceOrderItem;
use App\Models\Room;
use App\Models\Booking;

class BanquetController extends Controller
{
    /**
     * Display the main Banquet & Services dashboard.
     */
    public function index(Request $request)
    {
        $halls = BanquetHall::with(['bookings' => function ($q) {
            $q->latest();
        }])->get();

        $services = HotelService::where('status', 'Active')->orderBy('category')->orderBy('name')->get();

        // Room Service Orders
        $roomOrdersQuery = RoomServiceOrder::with(['room', 'booking.guest', 'items.service'])->latest();

        if ($request->filled('order_status') && $request->order_status !== 'all') {
            $roomOrdersQuery->where('order_status', $request->order_status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $roomOrdersQuery->where(function ($q) use ($s) {
                $q->where('order_number', 'like', "%{$s}%")
                  ->orWhere('room_number', 'like', "%{$s}%")
                  ->orWhere('guest_name', 'like', "%{$s}%");
            });
        }

        $roomOrders = $roomOrdersQuery->get();

        // Banquet Bookings
        $banquetBookings = BanquetBooking::with('hall')->latest()->get();

        // Currently Occupied / Active Rooms for quick order placement
        $activeRooms = Room::with(['activeBooking.guest', 'categoryRelation'])->orderBy('room_number')->get();

        // Metrics
        $totalHalls = BanquetHall::count();
        $totalEventsBooked = BanquetBooking::where('status', 'Confirmed')->count();
        $activeRoomOrdersCount = RoomServiceOrder::whereIn('order_status', ['Received', 'Preparing', 'Out for Delivery'])->count();
        $totalServicesRevenue = RoomServiceOrder::sum('total_amount') + BanquetBooking::sum('total_amount');

        return view('banquet.index', compact(
            'halls',
            'services',
            'roomOrders',
            'banquetBookings',
            'activeRooms',
            'totalHalls',
            'totalEventsBooked',
            'activeRoomOrdersCount',
            'totalServicesRevenue'
        ));
    }

    /**
     * Store a new Room Service / Amenity Order.
     */
    public function storeRoomOrder(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:hotel_services,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:255',
            'payment_status' => 'required|in:Charged to Room Folio,Paid Cash,Paid UPI/Card,Pending',
            'special_instructions' => 'nullable|string|max:500',
            'served_by' => 'nullable|string|max:100',
        ]);

        $room = Room::with('activeBooking.guest')->findOrFail($validated['room_id']);
        $booking = $room->activeBooking;
        $guestName = $booking?->guest?->guest_name ?? 'In-House Guest';

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += ($item['price'] * $item['quantity']);
        }

        $tax = round($subtotal * 0.05, 2); // 5% F&B GST
        $total = $subtotal + $tax;

        $nextOrderNo = 'ORD-' . date('Y') . '-' . str_pad((RoomServiceOrder::count() + 1), 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $order = RoomServiceOrder::create([
                'order_number' => $nextOrderNo,
                'room_id' => $room->id,
                'booking_id' => $booking?->id,
                'guest_name' => $guestName,
                'room_number' => $room->room_number,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'payment_status' => $validated['payment_status'],
                'order_status' => 'Received',
                'delivery_time' => '30-45 mins',
                'special_instructions' => $validated['special_instructions'] ?? null,
                'served_by' => $validated['served_by'] ?? 'Room Service Desk',
            ]);

            foreach ($validated['items'] as $it) {
                RoomServiceOrderItem::create([
                    'room_service_order_id' => $order->id,
                    'hotel_service_id' => $it['service_id'] ?? null,
                    'item_name' => $it['name'],
                    'unit_price' => $it['price'],
                    'quantity' => $it['quantity'],
                    'line_total' => $it['price'] * $it['quantity'],
                    'instructions' => $it['instructions'] ?? null,
                ]);
            }

            // If charged to room folio, we can append to booking remarks or balance if needed
            if ($validated['payment_status'] === 'Charged to Room Folio' && $booking) {
                $booking->balance = ($booking->balance ?: 0) + $total;
                $booking->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Room Order {$order->order_number} for Room {$room->room_number} placed successfully!",
                'order' => $order->load('items', 'room')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error placing room order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update room order status (Received -> Preparing -> Out for Delivery -> Delivered -> Cancelled).
     */
    public function updateOrderStatus(Request $request, RoomServiceOrder $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:Received,Preparing,Out for Delivery,Delivered,Cancelled',
        ]);

        $order->order_status = $validated['order_status'];
        $order->save();

        return response()->json([
            'success' => true,
            'message' => "Order #{$order->order_number} status updated to {$order->order_status}.",
            'order' => $order
        ]);
    }

    /**
     * Get room order details for modal view.
     */
    public function getOrderDetails(RoomServiceOrder $order)
    {
        $order->load(['room', 'booking.guest', 'items.service']);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Store a new Banquet Event Hall booking.
     */
    public function storeBanquetBooking(Request $request)
    {
        $validated = $request->validate([
            'banquet_hall_id' => 'required|exists:banquet_halls,id',
            'client_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'time_slot' => 'required|string|max:100',
            'guaranteed_pax' => 'required|integer|min:1',
            'hall_rental' => 'required|numeric|min:0',
            'catering_per_plate' => 'nullable|numeric|min:0',
            'advance_paid' => 'nullable|numeric|min:0',
            'function_sheet_notes' => 'nullable|string|max:1000',
        ]);

        $hallRental = (float) $validated['hall_rental'];
        $plate = (float) ($validated['catering_per_plate'] ?? 0);
        $pax = (int) $validated['guaranteed_pax'];
        $total = $hallRental + ($plate * $pax);
        $advance = (float) ($validated['advance_paid'] ?? 0);
        $balance = max(0, $total - $advance);

        $nextRef = 'BNQ-' . date('Y') . '-' . str_pad((BanquetBooking::count() + 1), 3, '0', STR_PAD_LEFT);

        $booking = BanquetBooking::create([
            'booking_reference' => $nextRef,
            'banquet_hall_id' => $validated['banquet_hall_id'],
            'client_name' => $validated['client_name'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'event_name' => $validated['event_name'],
            'event_date' => $validated['event_date'],
            'time_slot' => $validated['time_slot'],
            'guaranteed_pax' => $pax,
            'hall_rental' => $hallRental,
            'catering_per_plate' => $plate,
            'total_amount' => $total,
            'advance_paid' => $advance,
            'balance_due' => $balance,
            'status' => 'Confirmed',
            'function_sheet_notes' => $validated['function_sheet_notes'] ?? null,
        ]);

        // Update hall status
        $hall = BanquetHall::find($validated['banquet_hall_id']);
        if ($hall) {
            $hall->status = 'Reserved';
            $hall->save();
        }

        return response()->json([
            'success' => true,
            'message' => "Banquet Hall Booking {$booking->booking_reference} registered successfully!",
            'booking' => $booking->load('hall')
        ]);
    }

    /**
     * Get function sheet for an event.
     */
    public function getFunctionSheet(BanquetBooking $booking)
    {
        $booking->load('hall');

        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }
}
