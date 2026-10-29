<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\PaymentMode;
use App\Models\Payment;
use App\Models\Settlement;
use App\Models\Checkout;
use App\Models\RoomHousekeepingHistory;
use App\Models\HousekeepingState;
use App\Models\RoomTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FolioController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->getFolioIndexData($request);
        return view('frontoffice.folio.index', $data);
    }

    public function getFolioIndexData(Request $request)
    {
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $registrationTypes = \App\Models\RegistrationType::where('status', 'Active')->get();

        // Query primary bookings (each represents a reservation dossier)
        $query = Booking::with([
            'guest.title',
            'guest.nationality',
            'guest.idCardType',
            'guest.company',
            'company',
            'room.floorRelation',
            'room.categoryRelation',
            'room.beddingConfigRelation',
            'registrationType',
            'reservationMode',
            'plan',
            'payments.paymentMode',
            'settlements.paymentMode',
            'additionalBookings.guest.title',
            'additionalBookings.guest.nationality',
            'additionalBookings.guest.idCardType',
            'additionalBookings.room.floorRelation',
            'additionalBookings.room.categoryRelation',
            'additionalBookings.room.beddingConfigRelation',
            'roomServiceOrders.items',
            'additionalBookings.roomServiceOrders.items',
        ])
        ->whereNull('primary_booking_id')
        ->orderBy('id', 'desc');

        $today = now()->toDateString();

        // Date Filter (defaults to TODAY only, or all_dates / custom range)
        if ($request->has('all_dates') || $request->query('all_dates') == '1') {
            $fromDate = null;
            $toDate = null;
        } elseif ($request->filled('from_date') || $request->filled('to_date')) {
            $fromDate = $request->query('from_date');
            $toDate = $request->query('to_date');
        } else {
            // Default to TODAY's date only!
            $fromDate = $today;
            $toDate = $today;
        }

        if (!empty($fromDate)) {
            $query->where(function ($q) use ($fromDate) {
                $q->where(function ($sub) use ($fromDate) {
                    $sub->whereNotNull('checkin_date')->whereDate('checkin_date', '>=', $fromDate);
                })->orWhere(function ($sub) use ($fromDate) {
                    $sub->whereNull('checkin_date')->whereDate('reserve_date', '>=', $fromDate);
                });
            });
        }
        if (!empty($toDate)) {
            $query->where(function ($q) use ($toDate) {
                $q->where(function ($sub) use ($toDate) {
                    $sub->whereNotNull('checkin_date')->whereDate('checkin_date', '<=', $toDate);
                })->orWhere(function ($sub) use ($toDate) {
                    $sub->whereNull('checkin_date')->whereDate('reserve_date', '<=', $toDate);
                });
            });
        }

        // Status Filter (Settled / Unsettled / Legacy statuses)
        if ($request->filled('status') && $request->status !== 'all') {
            $statusVal = strtolower(trim($request->status));
            if (in_array($statusVal, ['settled', 'sattled'])) {
                $query->where(function ($q) {
                    $q->where('balance', '<=', 0.01)
                      ->orWhere('status', 'Settlement Done');
                });
            } elseif (in_array($statusVal, ['unsettled', 'unsattled'])) {
                $query->where(function ($q) {
                    $q->where('balance', '>', 0.01)
                      ->where('status', '!=', 'Settlement Done');
                });
            } else {
                $query->where('status', $request->status);
            }
        }

        // Registration Type Filter
        if ($request->filled('registration_type') && $request->registration_type !== 'all') {
            $query->whereHas('registrationType', function ($rq) use ($request) {
                $rq->where('name', $request->registration_type);
            });
        }

        // Search Filter
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
                         ->orWhere('city', 'like', "%{$s}%")
                         ->orWhere('id_card_number', 'like', "%{$s}%");
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

        // Pagination
        $perPage = $request->query('per_page', 10);
        if ($perPage === 'all') {
            $folios = $query->paginate(1000)->withQueryString();
        } else {
            $perPage = (int)$perPage > 0 ? (int)$perPage : 10;
            $folios = $query->paginate($perPage)->withQueryString();
        }

        // Filter-Aware Dynamic KPI Stats (Must reflect active filters including Room Service Orders)
        $kpiQuery = (clone $query);
        $kpiQuery->getQuery()->orders = null; // Drop ordering for faster aggregate calculation
        $filteredBookings = $kpiQuery->select(['id', 'advance_amount', 'payable_amount', 'balance', 'status'])->get();
        $filteredIds = $filteredBookings->pluck('id')->filter()->all();

        // Include any additional bookings under these primary folios for extra services
        $allAssociatedIds = !empty($filteredIds)
            ? Booking::whereIn('primary_booking_id', $filteredIds)->pluck('id')->concat($filteredIds)->unique()->all()
            : [];

        $totalExtraServices = !empty($allAssociatedIds)
            ? (float)\App\Models\RoomServiceOrder::whereIn('booking_id', $allAssociatedIds)
                ->where('payment_status', 'Charged to Room Folio')
                ->where('order_status', '!=', 'Cancelled')
                ->sum('total_amount')
            : 0.0;

        $totalReceivable = round((float)$filteredBookings->sum('payable_amount') + $totalExtraServices, 2);
        $totalAdvance = (float)$filteredBookings->sum('advance_amount');
        $totalSettled = !empty($filteredIds) 
            ? (float)Settlement::whereIn('booking_id', $filteredIds)->sum('paid_amount') 
            : 0.0;
        $totalPaid = round($totalAdvance + $totalSettled, 2);
        $totalOutstanding = round((float)$filteredBookings->sum('balance') + $totalExtraServices, 2);
        $settledCount = $filteredBookings->filter(function ($b) {
            return (float)$b->balance <= 0.01 || in_array(strtolower($b->status ?? ''), ['settlement done', 'settled']);
        })->count();
        $pendingSettleCount = $filteredBookings->count() - $settledCount;
        $checkedOutCount = $filteredBookings->where('status', 'Checked Out')->count();

        // Baseline all-time reference
        $allPrimaryCount = Booking::whereNull('primary_booking_id')->count();
        $todayCheckinCount = Booking::whereNull('primary_booking_id')->where(function ($q) use ($today) {
            $q->whereDate('checkin_date', $today)->orWhereDate('reserve_date', $today);
        })->count();

        $isFiltered = $request->filled('search') 
            || ($request->filled('status') && $request->status !== 'all') 
            || ($request->filled('registration_type') && $request->registration_type !== 'all')
            || ($request->has('all_dates') && $request->query('all_dates') == '1')
            || ($request->filled('from_date') && $request->query('from_date') !== $today)
            || ($request->filled('to_date') && $request->query('to_date') !== $today);

        $stats = [
            'total_folios' => $filteredBookings->count(),
            'all_time_folios' => $allPrimaryCount,
            'today_folios' => $todayCheckinCount,
            'is_filtered' => $isFiltered,
            'total_receivable' => $totalReceivable,
            'total_advance' => $totalAdvance,
            'total_settled' => $totalSettled,
            'total_paid' => $totalPaid,
            'total_outstanding' => $totalOutstanding,
            'settled_count' => $settledCount,
            'pending_settle_count' => $pendingSettleCount,
            'checked_out_count' => $checkedOutCount,
        ];

        // Format and serialize folios for quick modal dossier access
        $serializedFolios = $folios->getCollection()->map(function ($booking) {
            return $this->formatFolioData($booking);
        });

        return compact(
            'folios',
            'serializedFolios',
            'stats',
            'paymentModes',
            'registrationTypes',
            'fromDate',
            'toDate',
            'today'
        );
    }

    public function getDetails($id)
    {
        $booking = Booking::with([
            'guest.title',
            'guest.nationality',
            'guest.idCardType',
            'guest.company',
            'company',
            'room.floorRelation',
            'room.categoryRelation',
            'room.beddingConfigRelation',
            'registrationType',
            'reservationMode',
            'plan',
            'payments.paymentMode',
            'settlements.paymentMode',
            'additionalBookings.guest.title',
            'additionalBookings.guest.nationality',
            'additionalBookings.guest.idCardType',
            'additionalBookings.room.floorRelation',
            'additionalBookings.room.categoryRelation',
            'additionalBookings.room.beddingConfigRelation',
            'roomServiceOrders.items',
        ])->find($id);

        if (!$booking) {
            return response()->json(['success' => false, 'error' => 'Booking not found'], 404);
        }

        $formatted = $this->formatFolioData($booking);

        return response()->json([
            'success' => true,
            'folio' => $formatted,
        ]);
    }

    public function settle(Request $request, $id)
    {
        $booking = Booking::with(['guest', 'additionalBookings'])->find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'error' => 'Booking record not found'], 404);
        }

        if (in_array($booking->status, ['Settlement Done', 'Checked Out'])) {
            return response()->json(['success' => false, 'error' => 'This reservation has already completed settlement and is locked.'], 400);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_mode_id' => 'nullable|exists:payment_modes,id',
            'remarks' => 'nullable|string|max:500',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        $settleAmount = (float)$request->input('amount');

        $allBookings = collect([$booking])->concat($booking->additionalBookings ?? []);
        $allBookingIds = $allBookings->pluck('id')->filter()->toArray();

        $totalExtra = (float)\App\Models\RoomServiceOrder::whereIn('booking_id', $allBookingIds)
            ->where('payment_status', 'Charged to Room Folio')
            ->where('order_status', '!=', 'Cancelled')
            ->sum('total_amount');

        $currentBalance = round((float)$booking->balance + $totalExtra, 2);

        if ($currentBalance <= 0) {
            return response()->json(['success' => false, 'error' => 'This booking balance has already been fully settled.'], 400);
        }

        if ($settleAmount > ($currentBalance + 0.01)) {
            return response()->json(['success' => false, 'error' => 'Settlement amount cannot exceed outstanding balance of ₹ ' . number_format($currentBalance, 2)], 422);
        }

        DB::beginTransaction();
        try {
            $newBalance = max(0, round($currentBalance - $settleAmount, 2));

            // 1. Update primary booking balance & status
            if ($newBalance <= 0) {
                $booking->balance = 0.00;
                $booking->status = 'Settlement Done';

                // 2. Also update secondary bookings under the same reservation
                if ($booking->reserve_id) {
                    Booking::where('reserve_id', $booking->reserve_id)
                        ->where('id', '!=', $booking->id)
                        ->update([
                            'balance' => 0.00,
                            'status' => 'Settlement Done',
                        ]);
                }

                // 2b. If balance is fully settled, mark associated room service orders as settled
                \App\Models\RoomServiceOrder::whereIn('booking_id', $allBookingIds)
                    ->where('payment_status', 'Charged to Room Folio')
                    ->update(['payment_status' => 'Settled in Folio']);
            } else {
                // Partial settlement: reduce room balance first, then extra services
                $roomBalance = (float)$booking->balance;
                $remainingPayment = $settleAmount;

                if ($roomBalance > 0) {
                    $deductRoom = min($roomBalance, $remainingPayment);
                    $booking->balance = max(0, round($roomBalance - $deductRoom, 2));
                    $remainingPayment = round($remainingPayment - $deductRoom, 2);
                }

                if ($remainingPayment > 0) {
                    $orders = \App\Models\RoomServiceOrder::whereIn('booking_id', $allBookingIds)
                        ->where('payment_status', 'Charged to Room Folio')
                        ->where('order_status', '!=', 'Cancelled')
                        ->orderBy('id', 'asc')
                        ->get();

                    foreach ($orders as $ord) {
                        if ($remainingPayment >= (float)$ord->total_amount) {
                            $ord->payment_status = 'Settled in Folio';
                            $ord->save();
                            $remainingPayment = round($remainingPayment - (float)$ord->total_amount, 2);
                        }
                    }

                    if ($remainingPayment > 0) {
                        $booking->balance = round($booking->balance - $remainingPayment, 2);
                    }
                }
            }
            $booking->save();

            // 3. Create Payment Record (payment_type = 'Settlement')
            $paymentModeId = $request->input('payment_mode_id') ?: PaymentMode::where('name', 'Cash')->value('id');
            $txnId = $request->input('transaction_id') ?: ('TXN-' . strtoupper(uniqid()));

            Payment::create([
                'guest_id' => $booking->guest_id,
                'booking_id' => $booking->id,
                'payment_mode_id' => $paymentModeId,
                'amount' => $settleAmount,
                'advance_amount' => (float)$booking->advance_amount,
                'balance' => $newBalance,
                'settle_number' => $booking->settle_number ?: $booking->folio_number,
                'payment_remarks' => $request->input('remarks', 'Settlement payment'),
                'payment_date' => now()->toDateString(),
                'payment_time' => now()->format('H:i:s'),
                'payment_type' => 'Settlement',
                'transaction_id' => $txnId,
                'status' => 'Success',
            ]);

            // 4. Create Settlement Record
            $settlementNumber = 'SET-' . date('Ymd') . '-' . sprintf('%04d', $booking->id) . '-' . rand(10, 99);
            Settlement::create([
                'settlement_number' => $settlementNumber,
                'settle_number' => $booking->settle_number ?: $booking->folio_number,
                'guest_id' => $booking->guest_id,
                'booking_id' => $booking->id,
                'payment_mode_id' => $paymentModeId,
                'total_room_charges' => $booking->total_amount,
                'total_extra_charges' => $totalExtra,
                'discount_amount' => $booking->discount_amount,
                'tax_amount' => $booking->gst_amount,
                'payable_amount' => round((float)$booking->payable_amount + $totalExtra, 2),
                'advance_adjusted' => (float)$booking->advance_amount,
                'paid_amount' => $settleAmount,
                'balance_amount' => $newBalance,
                'settlement_date' => now()->toDateString(),
                'settlement_time' => now()->format('H:i:s'),
                'status' => $newBalance <= 0 ? 'Settled' : 'Partial',
                'remarks' => $request->input('remarks', 'Settlement received'),
            ]);

            DB::commit();

            $totalSettled = (float)Settlement::where('booking_id', $booking->id)->sum('paid_amount');
            if ($totalSettled <= 0) {
                $totalSettled = (float)Payment::where('booking_id', $booking->id)->where('payment_type', 'Settlement')->sum('amount');
            }
            $totalPaid = round((float)$booking->advance_amount + $totalSettled, 2);

            return response()->json([
                'success' => true,
                'message' => 'Settlement of ₹ ' . number_format($settleAmount, 2) . ' processed successfully!',
                'new_balance' => $newBalance,
                'advance_amount' => (float)$booking->advance_amount,
                'settlement_paid' => $totalSettled,
                'total_paid' => $totalPaid,
                'status' => $booking->status,
                'is_fully_settled' => ($newBalance <= 0),
                'settlement_number' => $settlementNumber,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => 'Settlement failed: ' . $e->getMessage()], 500);
        }
    }

    public function checkout(Request $request, $id)
    {
        $booking = Booking::with(['guest', 'additionalBookings', 'room', 'registrationType'])->find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'error' => 'Booking record not found'], 404);
        }

        if ($booking->status === 'Checked Out') {
            return response()->json(['success' => false, 'error' => 'This reservation is already Checked Out and locked.'], 400);
        }

        $regName = strtolower(trim($booking->registrationType?->name ?? ''));
        $isCompanyReg = ($regName === 'company' || !empty($booking->company_id));

        $currentBalance = (float)$booking->balance;
        if ($currentBalance > 0.01 && !$isCompanyReg) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot check out reservation with outstanding balance of ₹ ' . number_format($currentBalance, 2) . '. Complete settlement first.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $checkoutDate = now()->toDateString();
            $checkoutTime = now()->format('H:i:s');

            // Find all bookings under this reservation
            $allBookings = Booking::where('reserve_id', $booking->reserve_id)->get();
            if ($allBookings->isEmpty()) {
                $allBookings = collect([$booking]);
            }

            $dirtyState = HousekeepingState::where('name', 'like', '%dirty%')->first();

            foreach ($allBookings as $b) {
                $b->status = 'Checked Out';
                $b->checkout_date = $checkoutDate;
                $b->checkout_time = $checkoutTime;
                $b->save();

                // Room Housekeeping
                if ($b->room_id && $dirtyState) {
                    RoomHousekeepingHistory::create([
                        'room_id' => $b->room_id,
                        'housekeeping_status_id' => $dirtyState->id,
                        'start_time' => now(),
                        'status' => 'pending',
                    ]);
                }
            }

            // Create Checkout record
            $latestSettlement = Settlement::where('booking_id', $booking->id)->latest('id')->first();
            $totalSettled = (float)Settlement::where('booking_id', $booking->id)->sum('paid_amount');
            $totalPaid = round((float)$booking->advance_amount + $totalSettled, 2);

            Checkout::create([
                'guest_id' => $booking->guest_id,
                'booking_id' => $booking->id,
                'room_id' => $booking->room_id,
                'settlement_id' => $latestSettlement?->id,
                'checkout_date' => $checkoutDate,
                'checkout_time' => $checkoutTime,
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
                'remarks' => $request->input('remarks', 'Checked out via Folio Management'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation successfully checked out! Room(s) scheduled for housekeeping.',
                'status' => 'Checked Out',
                'checkout_date' => Carbon::parse($checkoutDate)->format('d M Y'),
                'checkout_time' => date('h:i A', strtotime($checkoutTime)),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => 'Checkout failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Early check-out for a single individual guest/occupant in a multi-guest room
     */
    public function earlyCheckoutGuest(Request $request, $bookingId)
    {
        $occupantBooking = Booking::with(['guest', 'room', 'registrationType'])->find($bookingId);
        if (!$occupantBooking) {
            return response()->json(['success' => false, 'error' => 'Guest booking record not found.'], 404);
        }

        if ($occupantBooking->status === 'Checked Out') {
            return response()->json(['success' => false, 'error' => 'This guest is already checked out.'], 400);
        }

        // Validate that reservation settlement is completed before early checkout (Allowed without settlement if registration type is Company)
        $primaryBooking = $occupantBooking->primary_booking_id
            ? Booking::with('registrationType')->find($occupantBooking->primary_booking_id)
            : $occupantBooking;

        $regName = strtolower(trim($primaryBooking?->registrationType?->name ?? ($occupantBooking->registrationType?->name ?? '')));
        $isCompanyReg = ($regName === 'company' || !empty($primaryBooking?->company_id) || !empty($occupantBooking->company_id));

        if ($primaryBooking && (float)$primaryBooking->balance > 0.01 && !in_array($primaryBooking->status, ['Settlement Done', 'Checked Out']) && !$isCompanyReg) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot early check out guest until all reservation settlement is completed. Outstanding balance: ₹ ' . number_format($primaryBooking->balance, 2)
            ], 422);
        }

        DB::beginTransaction();
        try {
            $checkoutDate = now()->toDateString();
            $checkoutTime = now()->format('H:i:s');

            // 1. Update this specific occupant booking record
            $occupantBooking->status = 'Checked Out';
            $occupantBooking->checkout_date = $checkoutDate;
            $occupantBooking->checkout_time = $checkoutTime;
            $occupantBooking->save();

            // 2. Create Checkout record for this specific guest
            Checkout::create([
                'guest_id' => $occupantBooking->guest_id,
                'booking_id' => $occupantBooking->id,
                'room_id' => $occupantBooking->room_id,
                'settlement_id' => null,
                'checkout_date' => $checkoutDate,
                'checkout_time' => $checkoutTime,
                'actual_checkout_at' => now(),
                'total_nights' => $occupantBooking->total_nights ?: 1,
                'room_charges' => 0.00,
                'extra_charges' => 0.00,
                'late_checkout_charge' => 0.00,
                'discount_amount' => 0.00,
                'tax_amount' => 0.00,
                'grand_total' => 0.00,
                'paid_amount' => 0.00,
                'balance_amount' => 0.00,
                'status' => 'Completed',
                'remarks' => $request->input('remarks', 'Early check-out for individual occupant'),
            ]);

            // 3. Check if any other occupants remain active in the same room under this reservation
            $roomVacated = false;
            $roomObj = $occupantBooking->room;
            if ($occupantBooking->room_id) {
                $remainingInRoom = Booking::where('room_id', $occupantBooking->room_id)
                    ->where('reserve_id', $occupantBooking->reserve_id)
                    ->where('id', '!=', $occupantBooking->id)
                    ->whereIn('status', ['Confirmed', 'Arrived', 'Stay Over', 'Checked In', 'Settlement Done'])
                    ->count();

                // If no remaining active occupants in this room, mark room as dirty for housekeeping and release room
                if ($remainingInRoom === 0) {
                    $roomVacated = true;
                    $dirtyState = HousekeepingState::where('name', 'Dirty')->first()
                        ?: HousekeepingState::where('name', 'like', '%dirty%')->first();
                    if ($dirtyState) {
                        RoomHousekeepingHistory::create([
                            'room_id' => $occupantBooking->room_id,
                            'housekeeping_status_id' => $dirtyState->id,
                            'start_time' => now(),
                            'status' => 'pending',
                        ]);
                    }
                }
            }

            // 4. Check if all bookings across the entire reservation are now checked out
            $remainingInReserve = Booking::where('reserve_id', $occupantBooking->reserve_id)
                ->where('id', '!=', $occupantBooking->id)
                ->whereIn('status', ['Confirmed', 'Arrived', 'Stay Over', 'Checked In', 'Settlement Done'])
                ->count();

            $isAllCheckedOut = ($remainingInReserve === 0);
            if ($isAllCheckedOut) {
                $masterBooking = Booking::where('reserve_id', $occupantBooking->reserve_id)
                    ->whereNull('primary_booking_id')
                    ->first();
                if ($masterBooking && $masterBooking->status !== 'Checked Out') {
                    $masterBooking->status = 'Checked Out';
                    if (!$masterBooking->checkout_date) {
                        $masterBooking->checkout_date = $checkoutDate;
                        $masterBooking->checkout_time = $checkoutTime;
                    }
                    $masterBooking->save();
                }
            }

            DB::commit();

            $guestName = $occupantBooking->guest?->guest_name ?? 'Guest';
            $roomLabel = $roomObj?->room_number ? ('Room ' . $roomObj->room_number) : 'the room';
            $msg = "Guest {$guestName} has been checked out early successfully.";
            if ($roomVacated) {
                $msg .= " {$roomLabel} is now completely vacated and scheduled for Housekeeping (Dirty).";
            } else {
                $msg .= " {$roomLabel} remains active and reserved for remaining occupant(s).";
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
                'guest_name' => $guestName,
                'booking_id' => $occupantBooking->id,
                'room_number' => $roomObj?->room_number,
                'room_vacated' => $roomVacated,
                'checkout_date' => Carbon::parse($checkoutDate)->format('d M Y'),
                'checkout_time' => date('h:i A', strtotime($checkoutTime)),
                'is_all_checked_out' => $isAllCheckedOut,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Early check out failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Room Swipe Details & Available Clean/Vacant Destination Rooms
     */
    public function getSwipeInfo(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $fromRoomId = $request->query('from_room_id');

        $booking = Booking::with([
            'guest',
            'room.categoryRelation',
            'room.floorRelation',
            'room.beddingConfigRelation',
            'primaryBooking.additionalBookings.guest',
            'primaryBooking.additionalBookings.room',
            'additionalBookings.guest',
            'additionalBookings.room',
        ])->find($bookingId);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        $masterBooking = $booking->primary_booking_id ? $booking->primaryBooking : $booking;
        $allBookings = collect([$masterBooking])->concat($masterBooking->additionalBookings ?? collect());

        $fromRoom = $fromRoomId ? Room::with(['categoryRelation', 'floorRelation', 'beddingConfigRelation'])->find($fromRoomId) : $booking->room;
        if (!$fromRoom && $booking->room) {
            $fromRoom = $booking->room;
        }

        // Occupants currently active in from_room under this reservation
        $occupantsInRoom = $allBookings->filter(function ($b) use ($fromRoom) {
            return $fromRoom && (int)$b->room_id === (int)$fromRoom->id && !in_array($b->status, ['Checked Out', 'Cancelled']);
        })->map(function ($b) {
            return [
                'booking_id' => $b->id,
                'guest_id' => $b->guest_id,
                'guest_name' => $b->guest?->guest_name ?? 'Guest',
                'mobile' => $b->guest?->mobile ?? 'N/A',
                'is_primary' => ($b->primary_booking_id === null),
                'room_rate' => (float)$b->room_rate,
                'status' => $b->status,
            ];
        })->values();

        $checkinDate = $masterBooking->checkin_date ?: $masterBooking->reserve_date;
        $checkoutDate = $masterBooking->checkout_date;
        
        $dCheckin = $checkinDate ? Carbon::parse($checkinDate)->startOfDay() : now()->startOfDay();
        $dCheckout = $checkoutDate ? Carbon::parse($checkoutDate)->startOfDay() : $dCheckin->copy()->addDay();
        $totalNights = max(1, (int)$dCheckin->diffInDays($dCheckout, false));

        $rawTransferDate = $request->query('transfer_date');
        $dTransfer = $rawTransferDate ? Carbon::parse($rawTransferDate)->startOfDay() : now()->startOfDay();

        // If reservation is in future, default shift date to checkin date
        if ($dTransfer->lt($dCheckin)) {
            $dTransfer = $dCheckin->copy();
        }
        $transferDate = $dTransfer->toDateString();

        $pastNights = max(0, min($totalNights, (int)$dCheckin->diffInDays($dTransfer, false)));
        $remainingNights = ($dTransfer->lte($dCheckout))
            ? max(1, $totalNights - $pastNights)
            : max(1, (int)$dTransfer->diffInDays($dCheckout, false));

        // Fetch conflicting rooms between transferDate and checkoutDate from OTHER reservations
        $conflictingRoomIds = Booking::where(function ($q) use ($transferDate, $checkoutDate) {
                $q->whereDate('reserve_date', '<', $checkoutDate)
                  ->where(function ($sub) use ($transferDate) {
                      $sub->whereDate('checkout_date', '>', $transferDate)
                          ->orWhereNull('checkout_date');
                  });
            })
            ->where('reserve_id', '!=', $masterBooking->reserve_id)
            ->whereIn('status', ['Confirmed', 'Checked In', 'Arrived', 'Stay Over', 'Settlement Done'])
            ->whereNotNull('room_id')
            ->pluck('room_id')
            ->filter()
            ->unique()
            ->all();

        if ($fromRoom) {
            $conflictingRoomIds[] = $fromRoom->id;
        }

        // Also exclude other rooms already occupied under this same reservation
        $sameResOccupiedRoomIds = $allBookings->filter(function ($b) {
            return !in_array($b->status, ['Checked Out', 'Cancelled']) && !empty($b->room_id);
        })->pluck('room_id')->filter()->all();

        $excludedRoomIds = array_unique(array_merge($conflictingRoomIds, $sameResOccupiedRoomIds));

        $availableRooms = Room::with(['categoryRelation', 'floorRelation', 'beddingConfigRelation'])
            ->where('status', 'Active')
            ->whereNotIn('id', $excludedRoomIds)
            ->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'room_number' => $r->room_number,
                    'floor' => $r->floorRelation?->floor ?? ($r->floor ?? '1'),
                    'category' => $r->categoryRelation?->name ?? 'Standard',
                    'bedding' => $r->beddingConfigRelation?->name ?? 'Standard',
                    'rate' => (float)$r->rate,
                    'pax' => $r->pax_capacity,
                ];
            });

        return response()->json([
            'success' => true,
            'master_booking_id' => $masterBooking->id,
            'reserve_id' => $masterBooking->reserve_id,
            'from_room' => $fromRoom ? [
                'id' => $fromRoom->id,
                'room_number' => $fromRoom->room_number,
                'category' => $fromRoom->categoryRelation?->name ?? 'Standard',
                'floor' => $fromRoom->floorRelation?->floor ?? '1',
                'rate' => (float)($fromRoom->rate ?? $booking->room_rate),
            ] : null,
            'occupants' => $occupantsInRoom,
            'checkin_date' => $checkinDate ? Carbon::parse($checkinDate)->format('Y-m-d') : null,
            'checkout_date' => $checkoutDate ? Carbon::parse($checkoutDate)->format('Y-m-d') : null,
            'transfer_date' => $transferDate,
            'past_nights' => $pastNights,
            'remaining_nights' => $remainingNights,
            'available_rooms' => $availableRooms,
        ]);
    }

    /**
     * Process Room Swipe / Shift with Tariff Recalculation & Housekeeping Status Transition
     */
    public function processRoomSwipe(Request $request)
    {
        $request->validate([
            'master_booking_id' => 'required|exists:bookings,id',
            'from_room_id' => 'required|exists:rooms,id',
            'to_room_id' => 'required|exists:rooms,id|different:from_room_id',
            'transfer_date' => 'required|date',
            'transferred_booking_ids' => 'required|array|min:1',
            'transferred_booking_ids.*' => 'exists:bookings,id',
            'new_room_rate' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $masterBooking = Booking::lockForUpdate()->find($request->master_booking_id);
            if (!$masterBooking) {
                return response()->json(['success' => false, 'message' => 'Master reservation record not found.'], 404);
            }
            if ($masterBooking->primary_booking_id) {
                $masterBooking = Booking::lockForUpdate()->find($masterBooking->primary_booking_id);
            }

            $fromRoom = Room::find($request->from_room_id);
            $toRoom = Room::find($request->to_room_id);
            $transferDate = Carbon::parse($request->transfer_date)->toDateString();
            $newRate = (float)$request->new_room_rate;

            $checkinDate = $masterBooking->checkin_date ?: $masterBooking->reserve_date;
            $checkoutDate = $masterBooking->checkout_date ? Carbon::parse($masterBooking->checkout_date)->toDateString() : now()->addDay()->toDateString();
            
            $dCheckin = $checkinDate ? Carbon::parse($checkinDate)->startOfDay() : now()->startOfDay();
            $dCheckout = Carbon::parse($checkoutDate)->startOfDay();
            $totalNights = max(1, (int)$dCheckin->diffInDays($dCheckout, false));

            $dTransfer = Carbon::parse($request->transfer_date)->startOfDay();
            if ($dTransfer->lt($dCheckin)) {
                $dTransfer = $dCheckin->copy();
            }
            $transferDate = $dTransfer->toDateString();

            $pastNights = max(0, min($totalNights, (int)$dCheckin->diffInDays($dTransfer, false)));
            $remainingNights = ($dTransfer->lte($dCheckout))
                ? max(1, $totalNights - $pastNights)
                : max(1, (int)$dTransfer->diffInDays($dCheckout, false));

            // Double check no conflict on destination room
            $conflict = Booking::where('room_id', $toRoom->id)
                ->where('reserve_id', '!=', $masterBooking->reserve_id)
                ->whereIn('status', ['Confirmed', 'Checked In', 'Arrived', 'Stay Over', 'Settlement Done'])
                ->where(function ($q) use ($transferDate, $checkoutDate) {
                    $q->whereDate('reserve_date', '<', $checkoutDate)
                      ->where(function ($sub) use ($transferDate) {
                          $sub->whereDate('checkout_date', '>', $transferDate)
                              ->orWhereNull('checkout_date');
                      });
                })
                ->exists();

            if ($conflict) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Room #{$toRoom->room_number} is occupied or reserved during this stay duration. Please select an available room.",
                ], 422);
            }

            // Target bookings being swiped
            $transferredBookingIds = $request->transferred_booking_ids;
            $targetBookings = Booking::whereIn('id', $transferredBookingIds)
                ->where('room_id', $fromRoom->id)
                ->get();

            if ($targetBookings->isEmpty()) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'No matching bookings found in the source room.'], 422);
            }

            // Count total active occupants in the source room before the shift
            $totalInFromRoom = Booking::where('room_id', $fromRoom->id)
                ->where('reserve_id', $masterBooking->reserve_id)
                ->whereIn('status', ['Confirmed', 'Arrived', 'Stay Over', 'Checked In', 'Settlement Done'])
                ->count();

            $isWholeRoom = ($targetBookings->count() >= $totalInFromRoom);

            $oldRate = (float)($fromRoom->rate ?? $targetBookings->first()->room_rate);

            if ($isWholeRoom) {
                // Room shifted: old room is vacated for remaining nights, new room taken
                // Difference per night = new_rate - old_rate
                $rateDiffPerNight = $newRate - $oldRate;
                $totalTariffAdjustment = round($rateDiffPerNight * $remainingNights, 2);
            } else {
                // Partial split: some guests remain in fromRoom, moving guest takes toRoom as an extra room
                $totalTariffAdjustment = round($newRate * $remainingNights, 2);
            }

            $guestNames = [];
            foreach ($targetBookings as $tb) {
                $tb->room_id = $toRoom->id;
                $tb->room_rate = $newRate;
                $tb->save();
                if ($tb->guest) {
                    $guestNames[] = $tb->guest->guest_name;
                }
            }

            // Recalculate Master Booking Financials
            $oldMasterGross = (float)$masterBooking->total_amount;
            $newMasterGross = max(0, round($oldMasterGross + $totalTariffAdjustment, 2));
            $masterBooking->total_amount = $newMasterGross;

            // Discount recalculation
            $discountPct = (float)$masterBooking->discount_percentage;
            $discountAmt = $discountPct > 0 
                ? round(($newMasterGross * $discountPct) / 100, 2)
                : min($newMasterGross, (float)$masterBooking->discount_amount);
            $masterBooking->discount_amount = $discountAmt;

            $netAfterDiscount = max(0, $newMasterGross - $discountAmt);

            // GST recalculation
            $gstPct = (float)$masterBooking->gst_percentage;
            $gstType = $masterBooking->gst_type ?? 'exclusive';
            if ($gstPct > 0) {
                if ($gstType === 'inclusive') {
                    $gstAmt = round(($netAfterDiscount - ($netAfterDiscount / (1 + ($gstPct / 100)))), 2);
                    $newPayable = $netAfterDiscount;
                } else {
                    $gstAmt = round((($netAfterDiscount * $gstPct) / 100), 2);
                    $newPayable = $netAfterDiscount + $gstAmt;
                }
            } else {
                $gstAmt = 0.00;
                $newPayable = $netAfterDiscount;
            }
            $masterBooking->gst_amount = $gstAmt;
            $masterBooking->payable_amount = round($newPayable, 2);

            // Recalculate Balance = Payable - (Advance + All Settlements)
            $advance = (float)$masterBooking->advance_amount;
            $totalSettled = (float)Settlement::where('booking_id', $masterBooking->id)->sum('paid_amount');
            $masterBooking->balance = max(0, round($newPayable - ($advance + $totalSettled), 2));
            $masterBooking->save();

            // Record in room_transfers audit table
            $roomTransfer = RoomTransfer::create([
                'booking_id' => $targetBookings->first()->id,
                'primary_booking_id' => $masterBooking->id,
                'from_room_id' => $fromRoom->id,
                'to_room_id' => $toRoom->id,
                'transfer_date' => $transferDate,
                'transfer_time' => now()->format('H:i:s'),
                'old_room_rate' => $oldRate,
                'new_room_rate' => $newRate,
                'nights_in_old_room' => $pastNights ?: 1,
                'remaining_nights' => $remainingNights,
                'tariff_difference' => $totalTariffAdjustment,
                'transferred_by' => auth()->id(),
                'transferred_guests' => $guestNames,
                'reason' => $request->reason ?: 'Room Swipe / Transfer',
                'remarks' => $request->remarks,
            ]);

            // Housekeeping transition: If source room now has 0 remaining active occupants, mark Dirty
            $remainingInFromRoom = Booking::where('room_id', $fromRoom->id)
                ->whereIn('status', ['Confirmed', 'Arrived', 'Stay Over', 'Checked In', 'Settlement Done'])
                ->count();

            if ($remainingInFromRoom === 0) {
                $dirtyState = HousekeepingState::where('name', 'Dirty')->first()
                    ?: HousekeepingState::where('name', 'like', '%dirty%')->first();
                if ($dirtyState) {
                    RoomHousekeepingHistory::create([
                        'room_id' => $fromRoom->id,
                        'housekeeping_status_id' => $dirtyState->id,
                        'start_time' => now(),
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully swiped to Room #{$toRoom->room_number}. Updated reservation balance: ₹ " . number_format($masterBooking->balance, 2),
                'transfer' => $roomTransfer,
                'updated_balance' => (float)$masterBooking->balance,
                'updated_payable' => (float)$masterBooking->payable_amount,
                'tariff_adjustment' => $totalTariffAdjustment,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process room swipe: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper to assemble a comprehensive, rich Folio data structure
     */
    protected function formatFolioData(Booking $booking): array
    {
        $primaryGuest = $booking->guest;
        $allBookings = collect([$booking])->concat($booking->additionalBookings ?? []);

        // Financial calculations
        $advAmount = (float)$booking->advance_amount;
        $settleAmount = (float)$booking->settlements?->sum('paid_amount');
        if ($settleAmount <= 0) {
            $settleAmount = (float)$booking->payments?->where('payment_type', 'Settlement')->sum('amount');
        }
        $totalPaid = round($advAmount + $settleAmount, 2);
        $balance = (float)$booking->balance;

        // Room Service Orders & Additional Bills
        $allBookingIds = $allBookings->pluck('id')->filter()->toArray();
        $roomServiceOrders = \App\Models\RoomServiceOrder::with('items')
            ->whereIn('booking_id', $allBookingIds)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $serviceOrdersList = $roomServiceOrders->map(function ($ro) {
            $itemsSummary = $ro->items->map(fn($it) => "{$it->quantity}x {$it->item_name}")->implode(', ');
            return [
                'id' => $ro->id,
                'order_number' => $ro->order_number,
                'room_number' => $ro->room_number,
                'table_number' => $ro->table_number,
                'guest_name' => $ro->guest_name,
                'date' => $ro->created_at ? $ro->created_at->format('d M Y, h:i A') : 'N/A',
                'date_short' => $ro->created_at ? $ro->created_at->format('d M Y') : 'N/A',
                'subtotal' => (float)$ro->subtotal,
                'tax_amount' => (float)$ro->tax_amount,
                'total_amount' => (float)$ro->total_amount,
                'payment_status' => $ro->payment_status,
                'order_status' => $ro->order_status,
                'served_by' => $ro->served_by,
                'special_instructions' => $ro->special_instructions,
                'items_summary' => $itemsSummary,
                'items' => $ro->items->map(function ($it) {
                    return [
                        'item_name' => $it->item_name,
                        'unit_price' => (float)$it->unit_price,
                        'quantity' => (int)$it->quantity,
                        'line_total' => (float)$it->line_total,
                    ];
                })->all(),
            ];
        })->values()->all();

        $totalRoomServiceCharges = (float)$roomServiceOrders
            ->where('payment_status', 'Charged to Room Folio')
            ->where('order_status', '!=', 'Cancelled')
            ->sum('total_amount');

        $effectiveBalance = round($balance + $totalRoomServiceCharges, 2);
        $effectivePayable = round((float)$booking->payable_amount + $totalRoomServiceCharges, 2);
        $effectiveGross = round((float)$booking->total_amount + $totalRoomServiceCharges, 2);

        // Group rooms and their occupants
        $roomsMap = [];
        foreach ($allBookings as $b) {
            $r = $b->room;
            $rKey = $r ? $r->id : ('unassigned_' . $b->id);

            if (!isset($roomsMap[$rKey])) {
                $rawRoom = $r?->room_number ?? null;
                $cleanRoomNum = $rawRoom ? preg_replace('/^r[-_\s]?/i', '', trim($rawRoom)) : null;
                $roomsMap[$rKey] = [
                    'room_id' => $r?->id,
                    'room_number' => $cleanRoomNum ? ('R' . $cleanRoomNum) : 'Unassigned',
                    'raw_number' => $rawRoom ?: 'Unassigned',
                    'floor' => $r?->floorRelation?->floor ?? ($r?->floor ?? '1'),
                    'category' => $r?->categoryRelation?->name ?? ($r?->category ?? 'Standard'),
                    'bedding' => $r?->beddingConfigRelation?->name ?? 'Standard',
                    'rate' => (float)($r?->rate ?? $b->room_rate),
                    'occupants' => [],
                ];
            }

            $g = $b->guest;
            $gName = ($g?->title?->name ? $g->title->name . ' ' : '') . ($g?->guest_name ?? 'Guest');
            $roomsMap[$rKey]['occupants'][] = [
                'booking_id' => $b->id,
                'guest_id' => $g?->id,
                'is_primary' => ($b->id === $booking->id),
                'name' => $gName,
                'mobile' => $g?->mobile ?: 'N/A',
                'email' => $g?->email ?: 'N/A',
                'city' => $g?->city ?: 'N/A',
                'id_card_type' => $g?->idCardType?->name ?? 'ID Proof',
                'id_card_number' => $g?->id_card_number ?: 'N/A',
                'status' => $b->status,
                'is_checked_out' => ($b->status === 'Checked Out'),
                'checkout_date' => $b->checkout_date ? Carbon::parse($b->checkout_date)->format('d M Y') : null,
                'checkout_time' => $b->checkout_time ? date('h:i A', strtotime($b->checkout_time)) : null,
            ];
        }

        // Annotate each room with is_vacated and active_occupants_count
        foreach ($roomsMap as $k => $rmData) {
            $occCount = count($rmData['occupants']);
            $rmCheckedOutCount = collect($rmData['occupants'])->where('is_checked_out', true)->count();
            $roomsMap[$k]['is_vacated'] = ($occCount > 0 && $rmCheckedOutCount >= $occCount);
            $roomsMap[$k]['active_occupants_count'] = max(0, $occCount - $rmCheckedOutCount);
        }

        $totalOccupantsCount = $allBookings->count();
        $checkedOutOccupantsCount = $allBookings->where('status', 'Checked Out')->count();
        $isAllCheckedOut = ($checkedOutOccupantsCount >= $totalOccupantsCount);
        $effectiveStatus = $isAllCheckedOut ? 'Checked Out' : ($booking->status === 'Checked Out' ? 'Settlement Done' : $booking->status);

        // Settlements history
        $settlementsHistory = ($booking->settlements ?? collect())->map(function ($s) {
            return [
                'settlement_number' => $s->settlement_number ?: ('SET-' . $s->id),
                'date' => $s->settlement_date ? Carbon::parse($s->settlement_date)->format('d M Y') : 'N/A',
                'time' => $s->settlement_time ? date('h:i A', strtotime($s->settlement_time)) : 'N/A',
                'amount' => (float)$s->paid_amount,
                'payment_mode' => $s->paymentMode?->name ?? 'Cash',
                'remarks' => $s->remarks ?: 'Settlement payment',
                'balance_after' => (float)$s->balance_amount,
            ];
        })->values()->all();

        // Checkin / Checkout dates
        $checkinDateVal = $booking->checkin_date ?: $booking->reserve_date;
        $checkinTimeVal = $booking->checkin_time ?: $booking->reserve_time;
        $checkoutDateVal = $booking->checkout_date;
        $checkoutTimeVal = $booking->checkout_time;

        $checkinDateFmt = $checkinDateVal ? Carbon::parse($checkinDateVal)->format('d M Y') : 'N/A';
        $checkinTimeFmt = $checkinTimeVal ? date('h:i A', strtotime($checkinTimeVal)) : '12:00 PM';
        $checkoutDateFmt = $checkoutDateVal ? Carbon::parse($checkoutDateVal)->format('d M Y') : 'N/A';
        $checkoutTimeFmt = $checkoutTimeVal ? date('h:i A', strtotime($checkoutTimeVal)) : '11:00 AM';

        // Room transfers (Swipes) history
        $roomTransfersHistory = RoomTransfer::with(['fromRoom', 'toRoom', 'staffUser'])
            ->where(function ($q) use ($booking, $allBookings) {
                $q->where('primary_booking_id', $booking->id)
                  ->orWhereIn('booking_id', $allBookings->pluck('id'));
            })
            ->latest('id')
            ->get()
            ->map(function ($rt) {
                return [
                    'id' => $rt->id,
                    'from_room' => $rt->fromRoom?->room_number ?? 'N/A',
                    'to_room' => $rt->toRoom?->room_number ?? 'N/A',
                    'date' => $rt->transfer_date ? $rt->transfer_date->format('d M Y') : 'N/A',
                    'time' => $rt->transfer_time ? date('h:i A', strtotime($rt->transfer_time)) : '',
                    'old_rate' => (float)$rt->old_room_rate,
                    'new_rate' => (float)$rt->new_room_rate,
                    'remaining_nights' => (int)$rt->remaining_nights,
                    'tariff_difference' => (float)$rt->tariff_difference,
                    'guests' => $rt->transferred_guests ?? [],
                    'reason' => $rt->reason,
                    'remarks' => $rt->remarks,
                    'staff' => $rt->staffUser?->name ?? 'Front Desk',
                ];
            })->all();

        return [
            'id' => $booking->id,
            'reserve_id' => $booking->reserve_id ?: ('RES-' . $booking->id),
            'settle_number' => $booking->settle_number ?: ($booking->folio_number ?: ('SET-' . ($booking->room?->room_number ?? '100') . '-' . $booking->id)),
            'folio_number' => $booking->settle_number ?: ($booking->folio_number ?: ('SET-' . ($booking->room?->room_number ?? '100') . '-' . $booking->id)),
            'status' => $effectiveStatus ?: 'Confirmed',
            'is_all_checked_out' => $isAllCheckedOut,
            'active_occupants_count' => max(0, $totalOccupantsCount - $checkedOutOccupantsCount),
            'created_at_fmt' => $booking->created_at ? $booking->created_at->format('d M Y, h:i A') : 'N/A',
            'registration_type' => $booking->registrationType?->name ?? 'Regular',
            'reservation_mode' => $booking->reservationMode?->name ?? 'Front Desk',
            'primary_guest' => [
                'id' => $primaryGuest?->id,
                'name' => ($primaryGuest?->title?->name ? $primaryGuest->title->name . ' ' : '') . ($primaryGuest?->guest_name ?? 'Guest'),
                'mobile' => $primaryGuest?->mobile ?: 'N/A',
                'email' => $primaryGuest?->email ?: 'N/A',
                'city' => $primaryGuest?->city ?: 'N/A',
                'address' => $primaryGuest?->guest_address ?: 'N/A',
                'nationality' => $primaryGuest?->nationality?->name ?? 'Indian',
                'id_card_type' => $primaryGuest?->idCardType?->name ?? 'ID Proof',
                'id_card_number' => $primaryGuest?->id_card_number ?: 'N/A',
                'has_privilege_card' => (bool)$primaryGuest?->has_privilege_card,
                'privilege_card_no' => $primaryGuest?->privilege_card_no ?: '',
            ],
            'company' => $booking->company ? [
                'name' => $booking->company->name,
                'gstin' => $booking->company->gstin,
                'phone' => $booking->company->phone,
            ] : ($booking->new_company_name ? [
                'name' => $booking->new_company_name,
                'gstin' => $booking->new_company_gstin,
                'phone' => $booking->new_company_phone,
            ] : null),
            'total_guests_count' => $allBookings->count(),
            'additional_guests_count' => max(0, $allBookings->count() - 1),
            'rooms' => array_values($roomsMap),
            'total_nights' => (int)($booking->total_nights ?: 1),
            'room_rate' => (float)$booking->room_rate,
            'plan_name' => $booking->plan_name ?: ($booking->plan?->plan_name ?: 'Room Only (EP)'),
            'plan_code' => $booking->plan?->plan_code ?: '',
            'plan_price' => (float)($booking->plan_price ?? ($booking->plan?->price ?? 0)),
            'plan_original_price' => (float)($booking->plan_original_price ?? ($booking->plan?->price ?? 0)),
            'plan_discount_amount' => (float)($booking->plan_discount_amount ?? 0),
            'total_amount' => (float)$booking->total_amount,
            'room_total_amount' => (float)$booking->total_amount,
            'effective_gross' => $effectiveGross,
            'discount_percentage' => (float)$booking->discount_percentage,
            'discount_amount' => (float)$booking->discount_amount,
            'gst_type' => $booking->gst_type ?: 'exclusive',
            'gst_percentage' => (float)$booking->gst_percentage,
            'gst_amount' => (float)$booking->gst_amount,
            'payable_amount' => (float)$booking->payable_amount,
            'room_payable_amount' => (float)$booking->payable_amount,
            'effective_payable' => $effectivePayable,
            'advance_amount' => $advAmount,
            'settlement_paid' => $settleAmount,
            'total_paid' => $totalPaid,
            'balance' => $effectiveBalance,
            'room_balance' => $balance,
            'payment_mode' => $booking->payments?->first()?->paymentMode?->name ?? 'Cash / Direct',
            'payment_remarks' => $booking->remarks ?: ($booking->payments?->first()?->payment_remarks ?? 'None'),
            'checkin_date_fmt' => $checkinDateFmt,
            'checkin_time_fmt' => $checkinTimeFmt,
            'checkout_date_fmt' => $checkoutDateFmt,
            'checkout_time_fmt' => $checkoutTimeFmt,
            'settlements_history' => $settlementsHistory,
            'room_service_orders' => $serviceOrdersList,
            'total_room_service_charges' => $totalRoomServiceCharges,
            'room_transfers' => $roomTransfersHistory,
        ];
    }

    /**
     * Fetch stay extension modal initial dossier data
     */
    public function getExtendStayInfo(Request $request)
    {
        $bookingId = $request->query('booking_id');
        if (!$bookingId) {
            return response()->json(['success' => false, 'message' => 'Booking ID is required.'], 400);
        }

        $booking = Booking::with([
            'guest',
            'room.categoryRelation',
            'room.floorRelation',
            'room.beddingConfigRelation',
            'primaryBooking.additionalBookings.guest',
            'primaryBooking.additionalBookings.room',
            'additionalBookings.guest',
            'additionalBookings.room',
        ])->find($bookingId);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        $masterBooking = $booking->primary_booking_id ? $booking->primaryBooking : $booking;
        $allBookings = collect([$masterBooking])->concat($masterBooking->additionalBookings ?? collect());

        $checkinDate = $masterBooking->checkin_date ?: $masterBooking->reserve_date;
        $checkoutDate = $masterBooking->checkout_date;
        $dCheckin = $checkinDate ? Carbon::parse($checkinDate)->startOfDay() : now()->startOfDay();
        $dCheckout = $checkoutDate ? Carbon::parse($checkoutDate)->startOfDay() : $dCheckin->copy()->addDay();
        $totalNights = max(1, (int)$dCheckin->diffInDays($dCheckout, false));

        $roomsMap = [];
        $activeTariffPerNight = 0;
        foreach ($allBookings as $b) {
            if (in_array($b->status, ['Checked Out', 'Cancelled']) || !$b->room_id) {
                continue;
            }
            $rm = $b->room;
            if (!$rm) continue;
            if (!isset($roomsMap[$rm->id])) {
                $rRate = (float)($rm->rate > 0 ? $rm->rate : ($b->room_rate > 0 ? $b->room_rate : 4500));
                $roomsMap[$rm->id] = [
                    'id' => $rm->id,
                    'room_number' => (string)$rm->room_number,
                    'category' => $rm->categoryRelation?->name ?? 'Standard',
                    'floor' => (string)($rm->floorRelation?->floor ?? '1'),
                    'rate' => $rRate,
                    'occupants' => [],
                ];
                $activeTariffPerNight += $rRate;
            }
            $roomsMap[$rm->id]['occupants'][] = [
                'name' => $b->guest?->guest_name ?? 'Guest',
                'status' => $b->status,
            ];
        }

        $minNewCheckout = $dCheckout->copy()->addDay()->toDateString();
        $advance = (float)$masterBooking->advance_amount;
        $settled = (float)Settlement::where('booking_id', $masterBooking->id)->sum('paid_amount');
        $totalPaid = round($advance + $settled, 2);

        return response()->json([
            'success' => true,
            'master_booking_id' => $masterBooking->id,
            'booking_id' => $booking->id,
            'reserve_id' => $masterBooking->reserve_id ?: ('RES-' . $masterBooking->id),
            'folio_number' => $masterBooking->folio_number ?: '-',
            'guest_name' => $masterBooking->guest?->guest_name ?? 'Guest',
            'mobile' => $masterBooking->guest?->mobile ?? 'N/A',
            'checkin_date' => $dCheckin->toDateString(),
            'checkin_date_fmt' => $dCheckin->format('d M Y'),
            'checkout_date' => $dCheckout->toDateString(),
            'checkout_date_fmt' => $dCheckout->format('d M Y'),
            'total_nights' => $totalNights,
            'status' => $masterBooking->status,
            'is_locked' => in_array($masterBooking->status, ['Settlement Done', 'Checked Out']),
            'rooms' => array_values($roomsMap),
            'active_tariff_per_night' => $activeTariffPerNight,
            'min_checkout_date' => $minNewCheckout,
            'total_amount' => (float)$masterBooking->total_amount,
            'payable_amount' => (float)$masterBooking->payable_amount,
            'advance_amount' => $advance,
            'settlement_paid' => $settled,
            'total_paid' => $totalPaid,
            'balance' => (float)$masterBooking->balance,
            'discount_percentage' => (float)$masterBooking->discount_percentage,
            'gst_percentage' => (float)$masterBooking->gst_percentage,
        ]);
    }

    /**
     * Real-time availability & conflict check for extended stay dates
     */
    public function checkExtendStayAvailability(Request $request)
    {
        $bookingId = $request->query('booking_id');
        $newCheckoutStr = $request->query('new_checkout_date');

        if (!$bookingId || !$newCheckoutStr) {
            return response()->json(['success' => false, 'message' => 'Booking ID and New Check-out Date are required.'], 400);
        }

        $booking = Booking::with([
            'guest',
            'room',
            'primaryBooking.additionalBookings.room',
            'additionalBookings.room',
        ])->find($bookingId);

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
        }

        $masterBooking = $booking->primary_booking_id ? $booking->primaryBooking : $booking;
        $allBookings = collect([$masterBooking])->concat($masterBooking->additionalBookings ?? collect());

        $currentCheckout = $masterBooking->checkout_date ? Carbon::parse($masterBooking->checkout_date)->startOfDay() : null;
        if (!$currentCheckout) {
            return response()->json(['success' => false, 'message' => 'Current booking check-out date is missing.'], 400);
        }

        $newCheckout = Carbon::parse($newCheckoutStr)->startOfDay();
        if ($newCheckout->lte($currentCheckout)) {
            return response()->json([
                'success' => false,
                'message' => 'New check-out date must be strictly after current check-out date (' . $currentCheckout->format('d M Y') . ').'
            ], 422);
        }

        $additionalNights = (int)$currentCheckout->diffInDays($newCheckout, false);
        $windowStart = $currentCheckout->toDateString();
        $windowEnd = $newCheckout->toDateString();

        $activeRoomIds = $allBookings->filter(function ($b) {
            return !in_array($b->status, ['Checked Out', 'Cancelled']) && !empty($b->room_id);
        })->pluck('room_id')->unique()->all();

        // Check conflicts for existing rooms in reservation
        $conflicts = Booking::whereIn('room_id', $activeRoomIds)
            ->where('reserve_id', '!=', $masterBooking->reserve_id)
            ->whereIn('status', ['Confirmed', 'Checked In', 'Arrived', 'Stay Over', 'Settlement Done'])
            ->where(function ($q) use ($windowStart, $windowEnd) {
                $q->whereDate('reserve_date', '<', $windowEnd)
                  ->where(function ($sub) use ($windowStart) {
                      $sub->whereDate('checkout_date', '>', $windowStart)
                          ->orWhereNull('checkout_date');
                  });
            })
            ->with(['room.categoryRelation', 'room.floorRelation', 'guest.title'])
            ->get();

        if ($conflicts->isNotEmpty()) {
            $conflictItem = $conflicts->first();
            $confRoom = $conflictItem->room;
            $confIn = $conflictItem->reserve_date ? Carbon::parse($conflictItem->reserve_date)->format('d M Y') : $windowStart;
            $confOut = $conflictItem->checkout_date ? Carbon::parse($conflictItem->checkout_date)->format('d M Y') : $windowEnd;
            $guestTitle = $conflictItem->guest?->title?->name ? $conflictItem->guest->title->name . ' ' : '';
            $guestName = $guestTitle . ($conflictItem->guest?->guest_name ?? 'Another Guest');

            $allHotelConflictingRoomIds = Booking::whereNotNull('room_id')
                ->where('reserve_id', '!=', $masterBooking->reserve_id)
                ->whereIn('status', ['Confirmed', 'Checked In', 'Arrived', 'Stay Over', 'Settlement Done'])
                ->where(function ($q) use ($windowStart, $windowEnd) {
                    $q->whereDate('reserve_date', '<', $windowEnd)
                      ->where(function ($sub) use ($windowStart) {
                          $sub->whereDate('checkout_date', '>', $windowStart)
                              ->orWhereNull('checkout_date');
                      });
                })
                ->pluck('room_id')
                ->unique()
                ->toArray();

            $excludedRoomIds = array_unique(array_merge($allHotelConflictingRoomIds, $activeRoomIds));

            $availableRooms = Room::with(['categoryRelation', 'floorRelation', 'beddingConfigRelation'])
                ->where('status', 'Active')
                ->whereNotIn('id', $excludedRoomIds)
                ->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')
                ->get()
                ->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'room_number' => (string)$r->room_number,
                        'category' => $r->categoryRelation?->name ?? 'Standard',
                        'floor' => (string)($r->floorRelation?->floor ?? '1'),
                        'rate' => (float)($r->rate > 0 ? $r->rate : 4500),
                        'bedding' => $r->beddingConfigRelation?->name ?? 'Standard',
                        'pax' => (int)($r->pax_capacity ?? 2),
                    ];
                });

            return response()->json([
                'success' => true,
                'available' => false,
                'additional_nights' => $additionalNights,
                'new_checkout_date' => $newCheckout->toDateString(),
                'new_checkout_date_fmt' => $newCheckout->format('d M Y'),
                'conflict' => [
                    'room_number' => $confRoom ? $confRoom->room_number : 'Room',
                    'guest_name' => $guestName,
                    'reserve_id' => $conflictItem->reserve_id ?: ('RES-' . $conflictItem->id),
                    'checkin_date' => $confIn,
                    'checkout_date' => $confOut,
                    'status' => $conflictItem->status,
                ],
                'message' => "Room #{$confRoom->room_number} is already booked from {$confIn} to {$confOut} by {$guestName}.",
                'available_rooms' => $availableRooms,
                'available_count' => $availableRooms->count(),
            ]);
        }

        // Available! Calculate tariff estimates
        $activeRooms = Room::whereIn('id', $activeRoomIds)->get();
        $dailyTariff = $activeRooms->sum(function ($r) {
            return (float)($r->rate > 0 ? $r->rate : 4500);
        });

        $additionalTariff = round($dailyTariff * $additionalNights, 2);
        $currentGross = (float)$masterBooking->total_amount;
        $estGross = round($currentGross + $additionalTariff, 2);

        $discPct = (float)$masterBooking->discount_percentage;
        $estDiscount = $discPct > 0 ? round(($estGross * $discPct) / 100, 2) : (float)$masterBooking->discount_amount;
        $estNet = max(0, $estGross - $estDiscount);

        $gstPct = (float)$masterBooking->gst_percentage;
        $gstType = $masterBooking->gst_type ?? 'exclusive';
        if ($gstPct > 0) {
            if ($gstType === 'inclusive') {
                $estGst = round($estNet - ($estNet / (1 + ($gstPct / 100))), 2);
                $estPayable = $estNet;
            } else {
                $estGst = round(($estNet * $gstPct) / 100, 2);
                $estPayable = $estNet + $estGst;
            }
        } else {
            $estGst = 0.00;
            $estPayable = $estNet;
        }

        $advance = (float)$masterBooking->advance_amount;
        $settled = (float)Settlement::where('booking_id', $masterBooking->id)->sum('paid_amount');
        $estBalance = max(0, round($estPayable - ($advance + $settled), 2));

        $roomsText = $activeRooms->pluck('room_number')->map(fn($n) => "#{$n}")->implode(', ');

        return response()->json([
            'success' => true,
            'available' => true,
            'additional_nights' => $additionalNights,
            'daily_tariff' => $dailyTariff,
            'additional_tariff' => $additionalTariff,
            'new_checkout_date' => $newCheckout->toDateString(),
            'new_checkout_date_fmt' => $newCheckout->format('d M Y'),
            'estimated_gross' => $estGross,
            'estimated_discount' => $estDiscount,
            'estimated_gst' => $estGst,
            'estimated_payable' => round($estPayable, 2),
            'estimated_balance' => $estBalance,
            'message' => "Room {$roomsText} is completely available for the extended {$additionalNights} night(s) until {$newCheckout->format('d M Y')}."
        ]);
    }

    /**
     * Process stay extension and update master ledger/folio financials
     */
    public function processExtendStay(Request $request)
    {
        $request->validate([
            'booking_id' => 'required',
            'new_checkout_date' => 'required|date',
            'extension_room_id' => 'nullable|exists:rooms,id',
            'remarks' => 'nullable|string|max:500',
        ]);

        $bookingId = $request->input('booking_id');
        $newCheckoutStr = $request->input('new_checkout_date');
        $altRoomId = $request->input('extension_room_id');

        DB::beginTransaction();
        try {
            $booking = Booking::with(['room', 'primaryBooking', 'additionalBookings.room'])->lockForUpdate()->find($bookingId);
            if (!$booking) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Booking not found.'], 404);
            }

            $masterBooking = $booking->primary_booking_id ? $booking->primaryBooking : $booking;
            if (in_array($masterBooking->status, ['Settlement Done', 'Checked Out'])) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'This reservation is already locked or checked out and cannot be extended.'], 403);
            }

            $allBookings = collect([$masterBooking])->concat($masterBooking->additionalBookings ?? collect());

            $currentCheckout = $masterBooking->checkout_date ? Carbon::parse($masterBooking->checkout_date)->startOfDay() : null;
            if (!$currentCheckout) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Invalid current checkout date.'], 422);
            }

            $newCheckout = Carbon::parse($newCheckoutStr)->startOfDay();
            if ($newCheckout->lte($currentCheckout)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'New check-out date must be after current check-out date.'], 422);
            }

            $additionalNights = (int)$currentCheckout->diffInDays($newCheckout, false);
            $windowStart = $currentCheckout->toDateString();
            $windowEnd = $newCheckout->toDateString();

            $activeBookings = $allBookings->filter(function ($b) {
                return !in_array($b->status, ['Checked Out', 'Cancelled']) && !empty($b->room_id);
            });

            $activeRoomIds = $activeBookings->pluck('room_id')->unique()->all();
            $additionalTariff = 0;

            if ($altRoomId) {
                $altRoom = Room::lockForUpdate()->find($altRoomId);
                if (!$altRoom) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Selected alternative room not found.'], 404);
                }

                $altConflict = Booking::where('room_id', $altRoom->id)
                    ->where('reserve_id', '!=', $masterBooking->reserve_id)
                    ->whereIn('status', ['Confirmed', 'Checked In', 'Arrived', 'Stay Over', 'Settlement Done'])
                    ->where(function ($q) use ($windowStart, $windowEnd) {
                        $q->whereDate('reserve_date', '<', $windowEnd)
                          ->where(function ($sub) use ($windowStart) {
                              $sub->whereDate('checkout_date', '>', $windowStart)
                                  ->orWhereNull('checkout_date');
                          });
                    })
                    ->exists();

                if ($altConflict) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => "Room #{$altRoom->room_number} is no longer available for the extended period. Please pick another room."], 422);
                }

                $altRate = (float)($altRoom->rate > 0 ? $altRoom->rate : 4500);
                $additionalTariff = round($altRate * $additionalNights, 2);

                $fromRoom = $booking->room ?: Room::find($activeRoomIds[0] ?? null);
                if ($fromRoom && $fromRoom->id != $altRoom->id) {
                    RoomTransfer::create([
                        'booking_id' => $booking->id,
                        'primary_booking_id' => $masterBooking->id,
                        'from_room_id' => $fromRoom->id,
                        'to_room_id' => $altRoom->id,
                        'transfer_date' => $windowStart,
                        'reason' => 'Stay extended with alternative room shift',
                        'notes' => "Stay extended by {$additionalNights} night(s) to {$newCheckout->toDateString()}. Source Room #{$fromRoom->room_number} was booked, shifted to Room #{$altRoom->room_number}.",
                        'shifted_by' => auth()->id() ?? 1,
                    ]);

                    $booking->room_id = $altRoom->id;
                    $booking->room_rate = $altRate;
                    $booking->save();
                }

            } else {
                $hasConflict = Booking::whereIn('room_id', $activeRoomIds)
                    ->where('reserve_id', '!=', $masterBooking->reserve_id)
                    ->whereIn('status', ['Confirmed', 'Checked In', 'Arrived', 'Stay Over', 'Settlement Done'])
                    ->where(function ($q) use ($windowStart, $windowEnd) {
                        $q->whereDate('reserve_date', '<', $windowEnd)
                          ->where(function ($sub) use ($windowStart) {
                              $sub->whereDate('checkout_date', '>', $windowStart)
                                  ->orWhereNull('checkout_date');
                          });
                    })
                    ->exists();

                if ($hasConflict) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Current room has a booking conflict for the extended period. Please select an alternative room.'], 422);
                }

                $activeRooms = Room::whereIn('id', $activeRoomIds)->get();
                $dailyTariff = $activeRooms->sum(function ($r) {
                    return (float)($r->rate > 0 ? $r->rate : 4500);
                });
                $additionalTariff = round($dailyTariff * $additionalNights, 2);
            }

            $dCheckin = $masterBooking->checkin_date ? Carbon::parse($masterBooking->checkin_date)->startOfDay() : Carbon::parse($masterBooking->reserve_date)->startOfDay();
            $newTotalNights = max(1, (int)$dCheckin->diffInDays($newCheckout, false));

            foreach ($allBookings as $b) {
                if (in_array($b->status, ['Checked Out', 'Cancelled'])) continue;
                $b->checkout_date = $newCheckout->toDateString();
                $b->total_nights = $newTotalNights;
                if ($b->status === 'Arrived' || $b->status === 'Checked In') {
                    $b->status = 'Stay Over';
                }
                $b->save();
            }

            $oldGross = (float)$masterBooking->total_amount;
            $newGross = max(0, round($oldGross + $additionalTariff, 2));
            $masterBooking->total_amount = $newGross;
            $masterBooking->checkout_date = $newCheckout->toDateString();
            $masterBooking->total_nights = $newTotalNights;

            $discPct = (float)$masterBooking->discount_percentage;
            $discAmt = $discPct > 0 ? round(($newGross * $discPct) / 100, 2) : min($newGross, (float)$masterBooking->discount_amount);
            $masterBooking->discount_amount = $discAmt;

            $netAfterDisc = max(0, $newGross - $discAmt);
            $gstPct = (float)$masterBooking->gst_percentage;
            $gstType = $masterBooking->gst_type ?? 'exclusive';
            if ($gstPct > 0) {
                if ($gstType === 'inclusive') {
                    $gstAmt = round($netAfterDisc - ($netAfterDisc / (1 + ($gstPct / 100))), 2);
                    $newPayable = $netAfterDisc;
                } else {
                    $gstAmt = round(($netAfterDisc * $gstPct) / 100, 2);
                    $newPayable = $netAfterDisc + $gstAmt;
                }
            } else {
                $gstAmt = 0.00;
                $newPayable = $netAfterDisc;
            }
            $masterBooking->gst_amount = $gstAmt;
            $masterBooking->payable_amount = round($newPayable, 2);

            $advance = (float)$masterBooking->advance_amount;
            $totalSettled = (float)Settlement::where('booking_id', $masterBooking->id)->sum('paid_amount');
            $masterBooking->balance = max(0, round($newPayable - ($advance + $totalSettled), 2));

            if ($request->filled('remarks')) {
                $prevRemarks = $masterBooking->remarks ? $masterBooking->remarks . ' | ' : '';
                $masterBooking->remarks = $prevRemarks . 'Extended stay: ' . trim($request->input('remarks'));
            }

            $masterBooking->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Stay successfully extended to {$newCheckout->format('d M Y')} (+{$additionalNights} Night" . ($additionalNights > 1 ? 's' : '') . ")!",
                'new_checkout_date' => $newCheckout->toDateString(),
                'new_checkout_date_fmt' => $newCheckout->format('d M Y'),
                'additional_nights' => $additionalNights,
                'additional_tariff' => $additionalTariff,
                'total_amount' => $newGross,
                'payable_amount' => round($newPayable, 2),
                'balance' => (float)$masterBooking->balance,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process stay extension: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
