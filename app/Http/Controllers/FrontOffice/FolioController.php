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
            $booking->balance = $newBalance;
            if ($newBalance <= 0) {
                $booking->status = 'Settlement Done';
            }
            $booking->save();

            // 2. Also update secondary bookings under the same reservation
            if ($booking->reserve_id && $newBalance <= 0) {
                Booking::where('reserve_id', $booking->reserve_id)
                    ->where('id', '!=', $booking->id)
                    ->update([
                        'balance' => 0.00,
                        'status' => 'Settlement Done',
                    ]);
            }

            // 2b. If balance is fully settled, mark associated room service orders as settled
            if ($newBalance <= 0) {
                \App\Models\RoomServiceOrder::whereIn('booking_id', $allBookingIds)
                    ->where('payment_status', 'Charged to Room Folio')
                    ->update(['payment_status' => 'Settled in Folio']);
            }

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
        $booking = Booking::with(['guest', 'additionalBookings', 'room'])->find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'error' => 'Booking record not found'], 404);
        }

        if ($booking->status === 'Checked Out') {
            return response()->json(['success' => false, 'error' => 'This reservation is already Checked Out and locked.'], 400);
        }

        $currentBalance = (float)$booking->balance;
        if ($currentBalance > 0.01) {
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
        $occupantBooking = Booking::with(['guest', 'room'])->find($bookingId);
        if (!$occupantBooking) {
            return response()->json(['success' => false, 'error' => 'Guest booking record not found.'], 404);
        }

        if ($occupantBooking->status === 'Checked Out') {
            return response()->json(['success' => false, 'error' => 'This guest is already checked out.'], 400);
        }

        // Validate that reservation settlement is completed before early checkout
        $primaryBooking = $occupantBooking->primary_booking_id
            ? Booking::find($occupantBooking->primary_booking_id)
            : $occupantBooking;

        if ($primaryBooking && (float)$primaryBooking->balance > 0.01 && !in_array($primaryBooking->status, ['Settlement Done', 'Checked Out'])) {
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
            ->latest()
            ->get();

        $serviceOrdersList = $roomServiceOrders->map(function ($ro) {
            return [
                'id' => $ro->id,
                'order_number' => $ro->order_number,
                'room_number' => $ro->room_number,
                'guest_name' => $ro->guest_name,
                'date' => $ro->created_at ? $ro->created_at->format('d M Y, h:i A') : 'N/A',
                'subtotal' => (float)$ro->subtotal,
                'tax_amount' => (float)$ro->tax_amount,
                'total_amount' => (float)$ro->total_amount,
                'payment_status' => $ro->payment_status,
                'order_status' => $ro->order_status,
                'served_by' => $ro->served_by,
                'special_instructions' => $ro->special_instructions,
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
            'plan_name' => $booking->plan?->plan_name ?: 'Room Only (EP)',
            'plan_code' => $booking->plan?->plan_code ?: '',
            'plan_price' => (float)($booking->plan?->price ?? 0),
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
        ];
    }
}
