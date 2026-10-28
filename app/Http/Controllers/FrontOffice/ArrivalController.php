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

        // Arrivals Scoping: Exclude any bookings that are already Checked In, Stay Over, Checked Out, or Settled
        $query->whereNotIn('status', ['Checked In', 'Stay Over', 'Checked Out', 'Settlement Done']);

        if ($request->filled('date')) {
            $query->whereDate('reserve_date', $selectedDate);
            $baseQuery = Booking::whereNull('primary_booking_id')
                ->whereNotIn('status', ['Checked In', 'Stay Over', 'Checked Out', 'Settlement Done'])
                ->whereDate('reserve_date', $selectedDate);
        } else {
            // Hotel PMS Arrival Scoping:
            // 1. All upcoming arrivals (checkin/reserve date >= today)
            // 2. Any pending arrival whose status is confirmed or arrived from earlier dates
            $arrivalScope = function ($q) use ($today) {
                $q->where(function ($sub) use ($today) {
                    $sub->whereDate('reserve_date', '>=', $today)
                        ->orWhereIn('status', ['Confirmed', 'Arrived', 'Pending']);
                });
            };
            $query->where($arrivalScope);
            $baseQuery = Booking::whereNull('primary_booking_id')
                ->whereNotIn('status', ['Checked In', 'Stay Over', 'Checked Out', 'Settlement Done'])
                ->where($arrivalScope);
        }

        // Order chronologically by arrival date, then by ID
        $query->orderBy('reserve_date', 'asc')->orderBy('id', 'asc');

        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $cleanRoom = ltrim(ltrim($s, 'Rr'), '-');
            $query->where(function ($q) use ($s, $cleanRoom) {
                $q->where('reserve_id', 'like', "%{$s}%")
                  ->orWhere('settle_number', 'like', "%{$s}%")
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

        // Find ALL related bookings under this reservation / room / guest dossier:
        $masterBookingId = $booking->primary_booking_id ?: $booking->id;
        $reserveId = $booking->reserve_id;
        $settleNumber = $booking->settle_number ?: $booking->folio_number;

        $allRelatedBookings = Booking::with(['room', 'guest', 'settlements', 'registrationType'])
            ->where(function ($q) use ($masterBookingId, $reserveId, $settleNumber) {
                $q->where('id', $masterBookingId)
                  ->orWhere('primary_booking_id', $masterBookingId);
                if (!empty($reserveId)) {
                    $q->orWhere('reserve_id', $reserveId);
                }
                if (!empty($settleNumber)) {
                    $q->orWhere('settle_number', $settleNumber);
                }
            })
            ->get();

        if ($allRelatedBookings->isEmpty()) {
            $allRelatedBookings = collect([$booking]);
        }

        // Strict Lock: Disallow modifying record once any related booking is Settlement Done, Settled, or Checked Out
        foreach ($allRelatedBookings as $relB) {
            $hasSettled = $relB->settlements()->where('status', 'Settled')->exists()
                || ($relB->settlements()->exists() && (float)$relB->balance <= 0.01)
                || $relB->payments()->where('payment_type', 'Settlement')->exists()
                || in_array(strtolower($relB->status ?? ''), ['settlement done', 'checked out'])
                || ((float)$relB->balance <= 0.01 && (float)$relB->payable_amount > 0 && ((float)$relB->advance_amount > 0 || $relB->payments()->exists()));

            if ($hasSettled || in_array($relB->status, ['Settlement Done', 'Checked Out'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Record is locked. This reservation has completed settlement (status: '{$relB->status}') and cannot be modified.",
                    'error' => "Record is locked. This reservation has completed settlement (status: '{$relB->status}') and cannot be modified."
                ], 403);
            }
        }

        // Prevent checkout if there is still outstanding balance on any related booking (Allowed without settlement if registration type is Company)
        $isCompanyReg = $allRelatedBookings->contains(function ($b) {
            $regName = strtolower(trim($b->registrationType?->name ?? ''));
            return $regName === 'company' || !empty($b->company_id);
        });
        $totalOutstanding = $allRelatedBookings->sum(fn($b) => (float)$b->balance);
        if ($newStatus === 'Checked Out' && $totalOutstanding > 0.01 && !$isCompanyReg) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot check out reservation with outstanding balance of ₹ ' . number_format($totalOutstanding, 2) . '. Complete settlement first.',
                'error' => 'Cannot check out reservation with outstanding balance of ₹ ' . number_format($totalOutstanding, 2) . '. Complete settlement first.'
            ], 422);
        }

        // Synchronize status across ALL related bookings and guests
        foreach ($allRelatedBookings as $b) {
            $b->status = $newStatus;
            if ($newStatus === 'Checked In') {
                if (empty($b->checkin_date)) {
                    $b->checkin_date = now()->toDateString();
                }
                if (empty($b->checkin_time)) {
                    $b->checkin_time = now()->format('H:i');
                }
            } elseif ($newStatus === 'Checked Out') {
                $b->checkout_date = now()->toDateString();
                $b->checkout_time = now()->format('H:i');
            }
            $b->save();
        }

        // Collect all distinct rooms allocated across this reservation
        $allRoomIds = $allRelatedBookings->pluck('room_id')->filter()->unique();

        // If Arrived or Checked In, update room operational state to Active for ALL related rooms
        if (in_array($newStatus, ['Arrived', 'Checked In']) && $allRoomIds->isNotEmpty()) {
            $activeOp = OperationalStatus::where('name', 'like', '%Active%')->first();
            if ($activeOp) {
                foreach ($allRoomIds as $rId) {
                    RoomOperationalHistory::create([
                        'room_id' => $rId,
                        'operational_status_id' => $activeOp->id,
                    ]);
                }
            }
        }

        // If Checked Out, create checkout records & set dirty housekeeping state for ALL related bookings & rooms
        if ($newStatus === 'Checked Out') {
            $latestSettlement = Settlement::whereIn('booking_id', $allRelatedBookings->pluck('id'))->latest('id')->first();
            $dirtyState = HousekeepingState::where('name', 'like', '%dirty%')->first();

            foreach ($allRelatedBookings as $b) {
                $totalPaid = round((float)$b->advance_amount + (float)Payment::where('booking_id', $b->id)->where('payment_type', 'Settlement')->sum('amount'), 2);
                Checkout::firstOrCreate(
                    ['booking_id' => $b->id],
                    [
                        'guest_id' => $b->guest_id,
                        'room_id' => $b->room_id,
                        'settlement_id' => $latestSettlement?->id,
                        'checkout_date' => now()->toDateString(),
                        'checkout_time' => now()->format('H:i'),
                        'actual_checkout_at' => now(),
                        'total_nights' => $b->total_nights ?: 1,
                        'room_charges' => $b->total_amount ?: 0.00,
                        'extra_charges' => 0.00,
                        'late_checkout_charge' => 0.00,
                        'discount_amount' => $b->discount_amount ?: 0.00,
                        'tax_amount' => $b->gst_amount ?: 0.00,
                        'grand_total' => $b->payable_amount ?: 0.00,
                        'paid_amount' => $totalPaid ?: $b->payable_amount,
                        'balance_amount' => 0.00,
                        'status' => 'Completed',
                        'remarks' => 'Checked out via Arrivals manager',
                    ]
                );
            }

            if ($dirtyState && $allRoomIds->isNotEmpty()) {
                foreach ($allRoomIds as $rId) {
                    RoomHousekeepingHistory::create([
                        'room_id' => $rId,
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

        $guestNames = $allRelatedBookings->map(fn($b) => $b->guest?->guest_name)->filter()->unique()->values();
        $namesStr = $guestNames->implode(', ');

        return response()->json([
            'success' => true,
            'message' => "Reservation status updated to '{$newStatus}' for all {$allRelatedBookings->count()} guest(s) ({$namesStr}) and related rooms successfully.",
            'status' => $newStatus,
            'badge_class' => $badgeClass,
            'updated_count' => $allRelatedBookings->count(),
            'guests' => $guestNames,
            'is_locked' => in_array($newStatus, ['Settlement Done', 'Checked Out'])
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
