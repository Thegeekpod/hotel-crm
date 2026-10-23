<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\RoomCategory;
use App\Models\Floor;
use App\Models\Company;
use App\Models\IdCardType;
use App\Models\ReservationMode;
use App\Models\PaymentMode;
use App\Models\Guest;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Settlement;
use App\Models\Checkout;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\RoomHousekeepingHistory;
use App\Models\RoomOperationalHistory;
use Illuminate\Http\Request;

class ArrivalController extends Controller
{
    public function index(Request $request)
    {
        $categories = RoomCategory::where('status', 'Active')->get();
        $floors = Floor::where('status', 'Active')->orderBy('floor', 'asc')->get();
        $companies = Company::where('status', 'Active')->get();
        $idCardTypes = IdCardType::where('status', 'Active')->get();
        $reservationModes = ReservationMode::where('status', 'Active')->get();
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $rackRooms = [];

        $today = now()->toDateString();
        $selectedDate = $request->query('date');

        // Fetch primary bookings (folio dossiers) whose check-in date matches chosen date or strictly future dates
        $query = Booking::with([
            'guest.title',
            'guest.nationality',
            'guest.idCardType',
            'room.categoryRelation',
            'room.floorRelation',
            'room.beddingConfigRelation',
            'company',
            'payments.paymentMode',
            'settlements',
            'plan',
            'registrationType',
            'reservationMode',
            'additionalBookings.guest.title',
            'additionalBookings.guest.nationality',
            'additionalBookings.guest.idCardType',
            'additionalBookings.room.categoryRelation',
            'additionalBookings.room.floorRelation',
            'additionalBookings.room.beddingConfigRelation',
            'additionalBookings.plan',
        ])
        ->whereNull('primary_booking_id');

        if ($request->filled('date')) {
            if ($selectedDate < $today) {
                return redirect()->route('frontoffice.arrivals')
                    ->with('warning', 'Past dates cannot be selected for arrivals.');
            }
            $query->whereDate('reserve_date', $selectedDate);
            $baseQuery = Booking::whereNull('primary_booking_id')->whereDate('reserve_date', $selectedDate);
        } else {
            $query->whereDate('reserve_date', '>', $today);
            $baseQuery = Booking::whereNull('primary_booking_id')->whereDate('reserve_date', '>', $today);
        }

        $query->latest('id');

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $cleanRoom = ltrim(ltrim($s, 'Rr'), '-');
            $query->where(function ($q) use ($s, $cleanRoom) {
                $q->where('reserve_id', 'like', "%{$s}%")
                  ->orWhere('folio_number', 'like', "%{$s}%")
                  ->orWhereHas('guest', function ($gq) use ($s) {
                      $gq->where('guest_name', 'like', "%{$s}%")
                         ->orWhere('mobile', 'like', "%{$s}%")
                         ->orWhere('email', 'like', "%{$s}%")
                         ->orWhere('city', 'like', "%{$s}%");
                  })
                  ->orWhereHas('room', function ($rq) use ($s, $cleanRoom) {
                      $rq->where('room_number', 'like', "%{$s}%")
                         ->orWhere('room_number', 'like', "%{$cleanRoom}%");
                  })
                  ->orWhereHas('additionalBookings.guest', function ($agq) use ($s) {
                      $agq->where('guest_name', 'like', "%{$s}%")
                          ->orWhere('mobile', 'like', "%{$s}%");
                  })
                  ->orWhereHas('additionalBookings.room', function ($ar) use ($s, $cleanRoom) {
                      $ar->where('room_number', 'like', "%{$s}%")
                         ->orWhere('room_number', 'like', "%{$cleanRoom}%");
                  })
                  ->orWhereHas('company', function ($cq) use ($s) {
                      $cq->where('name', 'like', "%{$s}%")
                         ->orWhere('gstin', 'like', "%{$s}%");
                  });
            });
        }

        $guests = $query->get();

        // Ensure any booking that has a completed settlement reflects 'Settlement Done'
        foreach ($guests as $g) {
            $hasSettled = $g->settlements->where('status', 'Settled')->isNotEmpty()
                || ($g->settlements->isNotEmpty() && (float)$g->balance <= 0.01)
                || $g->payments->where('payment_type', 'Settlement')->isNotEmpty()
                || strtolower($g->status ?? '') === 'settlement done'
                || ((float)$g->balance <= 0.01 && (float)$g->payable_amount > 0 && ((float)$g->advance_amount > 0 || $g->payments->isNotEmpty()));
            if ($hasSettled && !in_array($g->status, ['Settlement Done', 'Checked Out'])) {
                $g->status = 'Settlement Done';
                $g->save();
            }
        }

        // Calculate statistics
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'confirmed' => (clone $baseQuery)->where('status', 'Confirmed')->count(),
            'arrived' => (clone $baseQuery)->where('status', 'Arrived')->count(),
            'checked_in' => (clone $baseQuery)->where('status', 'Checked In')->count(),
            'stay_over' => (clone $baseQuery)->where('status', 'Stay Over')->count(),
            'settlement_done' => (clone $baseQuery)->where('status', 'Settlement Done')->count(),
            'checked_out' => (clone $baseQuery)->where('status', 'Checked Out')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'Cancelled')->count(),
        ];

        return view('frontoffice.arrivals.index', compact(
            'guests',
            'stats',
            'selectedDate',
            'today',
            'categories',
            'floors',
            'companies',
            'idCardTypes',
            'reservationModes',
            'paymentModes',
            'rackRooms'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $booking = Booking::with(['room', 'guest', 'settlements'])->findOrFail($id);
        $oldStatus = $booking->status ?: 'Confirmed';
        $newStatus = $validated['status'];

        $hasSettled = $booking->settlements()->where('status', 'Settled')->exists()
            || ($booking->settlements()->exists() && (float)$booking->balance <= 0.01)
            || $booking->payments()->where('payment_type', 'Settlement')->exists()
            || in_array(strtolower($booking->status ?? ''), ['settlement done', 'checked out'])
            || ((float)$booking->balance <= 0.01 && (float)$booking->payable_amount > 0 && ((float)$booking->advance_amount > 0 || $booking->payments()->exists()));

        // Strict Lock: Disallow modifying record once Settlement Done, Settled, or Checked Out
        if ($hasSettled || in_array($booking->status, ['Settlement Done', 'Checked Out'])) {
            return response()->json([
                'success' => false,
                'message' => "Record is locked. This guest has completed settlement (status: '{$booking->status}') and cannot be modified.",
                'error' => "Record is locked. This guest has completed settlement (status: '{$booking->status}') and cannot be modified."
            ], 403);
        }

        // Prevent checkout if there is still outstanding balance
        if ($newStatus === 'Checked Out' && (float)$booking->balance > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot check out guest with outstanding balance of ₹ ' . number_format($booking->balance, 2) . '. Complete settlement first.',
                'error' => 'Cannot check out guest with outstanding balance of ₹ ' . number_format($booking->balance, 2) . '. Complete settlement first.'
            ], 422);
        }

        $booking->status = $newStatus;
        $booking->save();

        // Synchronize status to all secondary bookings under the same reservation
        if ($booking->reserve_id) {
            Booking::where('reserve_id', $booking->reserve_id)
                ->where('id', '!=', $booking->id)
                ->update(['status' => $newStatus]);
        }

        if (in_array($newStatus, ['Arrived', 'Checked In']) && $booking->room) {
            $activeOp = OperationalStatus::where('name', 'like', '%Active%')->first();
            if ($activeOp) {
                RoomOperationalHistory::create([
                    'room_id' => $booking->room->id,
                    'operational_status_id' => $activeOp->id,
                ]);
            }
        }

        if ($newStatus === 'Checked Out') {
            $totalPaid = round((float)$booking->advance_amount + (float)Payment::where('booking_id', $booking->id)->where('payment_type', 'Settlement')->sum('amount'), 2);
            $latestSettlement = Settlement::where('booking_id', $booking->id)->latest('id')->first();
            Checkout::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'guest_id' => $booking->guest_id,
                    'room_id' => $booking->room_id,
                    'settlement_id' => $latestSettlement?->id,
                    'checkout_date' => now()->toDateString(),
                    'checkout_time' => now()->format('H:i'),
                    'actual_checkout_at' => now(),
                    'total_nights' => $booking->total_nights,
                    'room_charges' => $booking->total_amount,
                    'extra_charges' => 0.00,
                    'late_checkout_charge' => 0.00,
                    'discount_amount' => $booking->discount_amount,
                    'tax_amount' => $booking->gst_amount,
                    'grand_total' => $booking->payable_amount,
                    'paid_amount' => $totalPaid ?: $booking->payable_amount,
                    'balance_amount' => 0.00,
                    'status' => 'Completed',
                    'remarks' => 'Checked out via Arrivals manager',
                ]
            );

            if ($booking->room_id) {
                $dirtyState = HousekeepingState::where('name', 'like', '%dirty%')->first();
                if ($dirtyState) {
                    RoomHousekeepingHistory::create([
                        'room_id' => $booking->room_id,
                        'housekeeping_status_id' => $dirtyState->id,
                        'start_time' => now(),
                        'status' => 'pending',
                    ]);
                }
            }
        }

        $badgeClass = match ($newStatus) {
            'Settlement Done' => 'settled',
            'Confirmed' => 'blue',
            'Arrived' => 'green',
            'Checked In' => 'cyan',
            'Stay Over' => 'yellow',
            'Checked Out' => 'purple',
            'Cancelled' => 'red',
            default => 'gray',
        };

        $guestName = $booking->guest?->guest_name ?? 'Guest';

        return response()->json([
            'success' => true,
            'message' => "Guest {$guestName}'s status successfully updated from '{$oldStatus}' to '{$newStatus}'.",
            'status' => $newStatus,
            'badge_class' => $badgeClass,
        ]);
    }

    public function getDetails($id)
    {
        $booking = Booking::with([
            'guest.title',
            'guest.nationality',
            'guest.idCardType',
            'room.categoryRelation',
            'room.floorRelation',
            'room.beddingConfigRelation',
            'company',
            'plan',
            'registrationType',
            'reservationMode',
            'additionalBookings.guest.title',
            'additionalBookings.guest.nationality',
            'additionalBookings.guest.idCardType',
            'additionalBookings.room.categoryRelation',
            'additionalBookings.room.floorRelation',
            'additionalBookings.room.beddingConfigRelation',
            'additionalBookings.plan',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'guest' => $booking,
        ]);
    }
}
