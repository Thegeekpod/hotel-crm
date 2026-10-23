<?php

namespace App\Http\Controllers\FrontOffice;

use App\Http\Controllers\Controller;
use App\Models\NightAudit;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Checkout;
use App\Models\Settlement;
use App\Models\Plan;
use App\Models\RoomMaintenance;
use App\Models\HousekeepingState;
use App\Models\OperationalStatus;
use App\Models\RoomHousekeepingHistory;
use App\Models\RoomOperationalHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NightAuditController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $selectedDate = $request->query('date', $today);

        try {
            $selectedCarbon = Carbon::parse($selectedDate);
            $selectedDate = $selectedCarbon->toDateString();
        } catch (\Exception $e) {
            $selectedDate = $today;
            $selectedCarbon = Carbon::parse($selectedDate);
        }

        $isToday = ($selectedDate === $today);
        $prevDate = $selectedCarbon->copy()->subDay()->toDateString();
        $nextDate = $selectedCarbon->copy()->addDay()->toDateString();

        // 1. Existing Audit for this date if already run
        $existingAudit = NightAudit::whereDate('audit_date', $selectedDate)->first();

        // 2. PAYMENTS & COLLECTIONS (Ajker Dine Koto Ki Payments Elo)
        $paymentsQuery = Payment::with(['guest.title', 'booking.room', 'booking.payments', 'booking.settlements', 'paymentMode'])
            ->where(function($q) use ($selectedDate) {
                $q->whereDate('payment_date', $selectedDate)
                  ->orWhere(function($sub) use ($selectedDate) {
                      $sub->whereNull('payment_date')
                          ->whereDate('created_at', $selectedDate);
                  });
            })
            ->orderBy('id', 'desc');

        $paymentsList = $paymentsQuery->get();

        $totalPayments = (float)$paymentsList->sum('amount');
        $advancePayments = (float)$paymentsList->where('payment_type', 'Advance')->sum('amount');
        $settlementPayments = (float)$paymentsList->where('payment_type', 'Settlement')->sum('amount');
        $runningPayments = (float)$paymentsList->whereNotIn('payment_type', ['Advance', 'Settlement'])->sum('amount');

        // Payment Mode breakdown
        $paymentModes = PaymentMode::where('status', 'Active')->get();
        $modeBreakdown = [];
        $cashCollection = 0;
        $upiCollection = 0;
        $cardCollection = 0;
        $bankCollection = 0;
        $otherCollection = 0;

        foreach ($paymentModes as $pm) {
            $modeTotal = (float)$paymentsList->where('payment_mode_id', $pm->id)->sum('amount');
            $modeName = strtolower($pm->name);
            $modeBreakdown[] = [
                'name' => $pm->name,
                'amount' => $modeTotal,
                'count' => $paymentsList->where('payment_mode_id', $pm->id)->count()
            ];

            if (str_contains($modeName, 'cash')) {
                $cashCollection += $modeTotal;
            } elseif (str_contains($modeName, 'upi') || str_contains($modeName, 'gpay') || str_contains($modeName, 'phonepe') || str_contains($modeName, 'paytm')) {
                $upiCollection += $modeTotal;
            } elseif (str_contains($modeName, 'card') || str_contains($modeName, 'pos') || str_contains($modeName, 'credit') || str_contains($modeName, 'debit')) {
                $cardCollection += $modeTotal;
            } elseif (str_contains($modeName, 'bank') || str_contains($modeName, 'neft') || str_contains($modeName, 'rtgs') || str_contains($modeName, 'transfer')) {
                $bankCollection += $modeTotal;
            } else {
                $otherCollection += $modeTotal;
            }
        }

        // Catch payments with no linked mode
        $unspecifiedPayments = (float)$paymentsList->whereNull('payment_mode_id')->sum('amount');
        if ($unspecifiedPayments > 0) {
            $otherCollection += $unspecifiedPayments;
            $modeBreakdown[] = [
                'name' => 'Direct / Unspecified',
                'amount' => $unspecifiedPayments,
                'count' => $paymentsList->whereNull('payment_mode_id')->count()
            ];
        }

        // 3. ROOMS & INVENTORY DATA (Rooms Data)
        $allRooms = Room::with(['categoryRelation', 'floorRelation', 'beddingConfigRelation', 'operationalHistories.operationalStatus', 'housekeepingHistories.housekeepingStatus'])->get();
        $totalRoomsCount = $allRooms->count();

        // Active bookings occupying rooms on selected date
        $activeBookingsOnDate = Booking::with(['guest.title', 'room.categoryRelation', 'plan', 'payments'])
            ->where(function($q) use ($selectedDate) {
                $q->whereDate('reserve_date', '<=', $selectedDate)
                  ->whereDate('checkout_date', '>=', $selectedDate);
            })
            ->whereNotIn('status', ['Cancelled', 'Checked Out'])
            ->get();

        $occupiedRoomIds = $activeBookingsOnDate->pluck('room_id')->filter()->unique()->toArray();
        $occupiedRoomsCount = count($occupiedRoomIds);
        $occupiedRooms = $allRooms->whereIn('id', $occupiedRoomIds)->values();

        // Maintenance / Out of order rooms
        $activeMaintenanceRooms = RoomMaintenance::where('status', 'In Progress')->pluck('room_id')->toArray();
        $maintenanceRoomsCount = count(array_unique($activeMaintenanceRooms));

        $vacantRoomsCount = max(0, $totalRoomsCount - $occupiedRoomsCount - $maintenanceRoomsCount);
        $occupancyRate = $totalRoomsCount > 0 ? round(($occupiedRoomsCount / $totalRoomsCount) * 100, 2) : 0.00;

        // Housekeeping states
        $dirtyRoomsCount = 0;
        $cleanRoomsCount = 0;
        foreach ($allRooms as $rm) {
            $latestHk = $rm->housekeepingHistories->sortByDesc('id')->first();
            $hkName = strtolower($latestHk?->housekeepingStatus?->name ?? ($latestHk?->housekeepingState?->name ?? 'clean'));
            if (str_contains($hkName, 'dirty') || str_contains($hkName, 'cleaning') || str_contains($hkName, 'touch up')) {
                $dirtyRoomsCount++;
            } else {
                $cleanRoomsCount++;
            }
        }

        // Today's Housekeeping History Logs for selected audit date
        $todayHousekeepingHistories = RoomHousekeepingHistory::with(['room.categoryRelation', 'room.floorRelation', 'housekeepingStatus'])
            ->where(function($q) use ($selectedDate) {
                $q->whereDate('created_at', $selectedDate)
                  ->orWhereDate('start_time', $selectedDate)
                  ->orWhereDate('completion_time', $selectedDate);
            })
            ->orderBy('id', 'desc')
            ->get();

        // Today's Operational History Logs for selected audit date
        $todayOperationalHistories = RoomOperationalHistory::with(['room.categoryRelation', 'room.floorRelation', 'operationalStatus'])
            ->whereDate('created_at', $selectedDate)
            ->orderBy('id', 'desc')
            ->get();

        // Room Revenue for the day
        $dailyRoomRevenue = 0;
        $dailyFoodRevenue = 0;
        $dailyTaxAmount = 0;
        $dailyDiscountAmount = 0;

        foreach ($activeBookingsOnDate as $b) {
            $nights = max(1, (int)$b->total_nights);
            $roomRatePerDay = (float)($b->room_rate ?: ($b->total_amount / $nights));
            $dailyRoomRevenue += $roomRatePerDay;

            $planObj = $b->plan;
            $planPricePerDay = (float)($planObj?->price ?? 0);
            $dailyFoodRevenue += $planPricePerDay;

            $taxPerDay = (float)($b->gst_amount / $nights);
            $dailyTaxAmount += $taxPerDay;

            $discountPerDay = (float)($b->discount_amount / $nights);
            $dailyDiscountAmount += $discountPerDay;
        }

        $dailyRoomRevenue = round($dailyRoomRevenue, 2);
        $dailyFoodRevenue = round($dailyFoodRevenue, 2);
        $dailyTaxAmount = round($dailyTaxAmount, 2);
        $dailyDiscountAmount = round($dailyDiscountAmount, 2);
        $totalDailyRevenue = round($dailyRoomRevenue + $dailyFoodRevenue + $dailyTaxAmount - $dailyDiscountAmount, 2);

        $adr = $occupiedRoomsCount > 0 ? round($dailyRoomRevenue / $occupiedRoomsCount, 2) : 0.00;
        $revpar = $totalRoomsCount > 0 ? round($dailyRoomRevenue / $totalRoomsCount, 2) : 0.00;

        // 4. FOOD & MEAL PLANS (Khabar Data)
        $mealPlanCounts = [
            'EP' => 0,  // Room Only
            'CP' => 0,  // Bed & Breakfast
            'MAP' => 0, // Half Board (Breakfast + Dinner)
            'AP' => 0,  // Full Board (All Meals)
            'OTHER' => 0
        ];

        $mealPlansList = [];
        foreach ($activeBookingsOnDate as $b) {
            $planName = $b->plan?->plan_name ?: 'Room Only (EP)';
            $planUpper = strtoupper($planName);
            $pax = 1; // 1 primary guest

            if (str_contains($planUpper, 'AP') || str_contains($planUpper, 'AMERICAN')) {
                $mealPlanCounts['AP']++;
                $planCode = 'AP';
            } elseif (str_contains($planUpper, 'MAP') || str_contains($planUpper, 'MODIFIED')) {
                $mealPlanCounts['MAP']++;
                $planCode = 'MAP';
            } elseif (str_contains($planUpper, 'CP') || str_contains($planUpper, 'CONTINENTAL') || str_contains($planUpper, 'BREAKFAST')) {
                $mealPlanCounts['CP']++;
                $planCode = 'CP';
            } else {
                $mealPlanCounts['EP']++;
                $planCode = 'EP';
            }

            $mealPlansList[] = [
                'room_number' => $b->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number))) : 'Unassigned',
                'guest_name' => ($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Guest'),
                'plan_name' => $planName,
                'plan_code' => $planCode,
                'plan_price' => (float)($b->plan?->price ?? 0),
                'pax' => $pax
            ];
        }

        // 5. GUEST MOVEMENT & ACTIVITY (Guests Sob Kichu)
        // Today's arrivals (scheduled check-in on this date)
        $arrivalsQuery = Booking::with(['guest.title', 'room.categoryRelation', 'registrationType'])
            ->whereDate('reserve_date', $selectedDate)
            ->get();
        $arrivalsCount = $arrivalsQuery->count();
        $checkedInArrivalsCount = $arrivalsQuery->whereIn('status', ['Checked In', 'Stay Over'])->count();
        $pendingArrivalsCount = $arrivalsQuery->whereIn('status', ['Confirmed', 'Arrived', 'Tentative'])->count();

        // Today's departures (scheduled or actual checkouts on this date)
        $departuresQuery = Booking::with(['guest.title', 'room.categoryRelation'])
            ->whereDate('checkout_date', $selectedDate)
            ->get();
        $departuresCount = $departuresQuery->count();

        // Actual checkouts completed today from checkouts table
        $checkoutsCompletedToday = Checkout::with(['guest.title', 'room', 'booking'])
            ->whereDate('checkout_date', $selectedDate)
            ->get();
        $completedCheckoutsCount = $checkoutsCompletedToday->count();

        // Pending checkouts (scheduled for today or earlier, but still not marked 'Checked Out')
        $pendingCheckouts = Booking::with(['guest.title', 'room'])
            ->whereDate('checkout_date', '<=', $selectedDate)
            ->whereNotIn('status', ['Checked Out', 'Cancelled', 'Settlement Done'])
            ->get();
        $pendingDeparturesCount = $pendingCheckouts->count();

        // Total In-House Guests currently in rooms
        $inHouseBookings = Booking::with(['guest.title', 'room.categoryRelation', 'room.floorRelation', 'plan'])
            ->whereIn('status', ['Checked In', 'Stay Over', 'Arrived'])
            ->get();
        $inHouseGuestsCount = $inHouseBookings->count();

        // Key active bookings by room_id for fast lookup in Today Rooms table
        $activeBookingsByRoom = $activeBookingsOnDate->keyBy('room_id');

        // 5.1. TODAY ALL GUESTS (All guests active on this date: In-House, Arriving, or Departing)
        $todayAllBookings = Booking::with([
            'guest.title',
            'room.categoryRelation',
            'room.floorRelation',
            'plan',
            'payments',
            'settlements',
            'primaryBooking.payments'
        ])
        ->where(function($q) use ($selectedDate) {
            $q->where(function($sub) use ($selectedDate) {
                $sub->whereDate('reserve_date', '<=', $selectedDate)
                    ->whereDate('checkout_date', '>=', $selectedDate);
            })
            ->orWhereDate('reserve_date', $selectedDate)
            ->orWhereDate('checkin_date', $selectedDate)
            ->orWhereDate('checkout_date', $selectedDate);
        })
        ->whereNotIn('status', ['Cancelled'])
        ->orderBy('id', 'desc')
        ->get();

        // Calculate total unsettled balance across today's active folios
        $totalUnsettledAmount = 0;
        $unsettledBookingsCount = 0;
        $settledBookingsCount = 0;

        foreach ($todayAllBookings as $b) {
            $isAdditional = !empty($b->primary_booking_id);
            $master = ($isAdditional && $b->primaryBooking) ? $b->primaryBooking : $b;

            $bill = (float)($master->payable_amount > 0 ? $master->payable_amount : $master->total_amount);
            $paid = (float)($master->payments ? $master->payments->sum('amount') : 0);
            if ($paid <= 0 && (float)$master->advance_amount > 0) {
                $paid = (float)$master->advance_amount;
            }
            $rem = max(0, round($bill - $paid, 2));

            // Only count once per primary booking to avoid duplicate counting
            if (!$isAdditional) {
                if ($rem > 0.01) {
                    $totalUnsettledAmount += $rem;
                    $unsettledBookingsCount++;
                } else {
                    $settledBookingsCount++;
                }
            }
        }

        // 6. Pre-Audit Checklist Warnings
        $checklist = [
            [
                'title' => 'Pending Expected Arrivals',
                'description' => "{$pendingArrivalsCount} guest(s) expected to arrive today have not yet completed check-in.",
                'passed' => ($pendingArrivalsCount === 0),
                'count' => $pendingArrivalsCount,
                'type' => 'warning'
            ],
            [
                'title' => 'Pending Overdue Departures',
                'description' => "{$pendingDeparturesCount} room(s) due for check-out are still occupied in-house.",
                'passed' => ($pendingDeparturesCount === 0),
                'count' => $pendingDeparturesCount,
                'type' => 'warning'
            ],
            [
                'title' => 'Housekeeping Dirty Rooms',
                'description' => "{$dirtyRoomsCount} room(s) are currently marked dirty or under cleaning.",
                'passed' => ($dirtyRoomsCount === 0),
                'count' => $dirtyRoomsCount,
                'type' => 'info'
            ],
            [
                'title' => 'Collections Audited',
                'description' => "Total collection received today: ₹ " . number_format($totalPayments, 2) . " across " . count($paymentsList) . " payment transaction(s).",
                'passed' => true,
                'count' => count($paymentsList),
                'type' => 'success'
            ],
        ];

        // 7. Recent Past Night Audits Log (Last 15 audits)
        $pastAudits = NightAudit::with('auditor')
            ->orderBy('audit_date', 'desc')
            ->limit(15)
            ->get();

        // 8. If audit is already completed, freeze and lock all values directly from the stored record
        if ($existingAudit) {
            $totalPayments = (float)$existingAudit->total_payments;
            $advancePayments = (float)$existingAudit->advance_payments;
            $settlementPayments = (float)$existingAudit->settlement_payments;
            $cashCollection = (float)$existingAudit->cash_collection;
            $upiCollection = (float)$existingAudit->upi_collection;
            $cardCollection = (float)$existingAudit->card_collection;
            $bankCollection = (float)$existingAudit->bank_collection;
            $otherCollection = (float)$existingAudit->other_collection;
            $dailyRoomRevenue = (float)$existingAudit->room_revenue;
            $dailyFoodRevenue = (float)$existingAudit->food_revenue;
            $totalDailyRevenue = (float)$existingAudit->total_revenue;
            $occupancyRate = (float)$existingAudit->occupancy_rate;
            $occupiedRoomsCount = (int)$existingAudit->occupied_rooms;
            $vacantRoomsCount = (int)$existingAudit->vacant_rooms;
            $totalRoomsCount = (int)$existingAudit->total_rooms;
            $maintenanceRoomsCount = (int)$existingAudit->maintenance_rooms;
            $adr = (float)$existingAudit->adr;
            $revpar = (float)$existingAudit->revpar;
            $mealPlanCounts['EP'] = (int)$existingAudit->ep_plan_count;
            $mealPlanCounts['CP'] = (int)$existingAudit->cp_plan_count;
            $mealPlanCounts['MAP'] = (int)$existingAudit->map_plan_count;
            $mealPlanCounts['AP'] = (int)$existingAudit->ap_plan_count;
        }

        return view('frontoffice.nightaudit.index', compact(
            'selectedDate',
            'today',
            'isToday',
            'prevDate',
            'nextDate',
            'existingAudit',
            'totalPayments',
            'advancePayments',
            'settlementPayments',
            'runningPayments',
            'paymentsList',
            'modeBreakdown',
            'cashCollection',
            'upiCollection',
            'cardCollection',
            'bankCollection',
            'otherCollection',
            'totalRoomsCount',
            'occupiedRoomsCount',
            'vacantRoomsCount',
            'maintenanceRoomsCount',
            'dirtyRoomsCount',
            'cleanRoomsCount',
            'occupancyRate',
            'adr',
            'revpar',
            'dailyRoomRevenue',
            'dailyFoodRevenue',
            'dailyTaxAmount',
            'dailyDiscountAmount',
            'totalDailyRevenue',
            'mealPlanCounts',
            'mealPlansList',
            'arrivalsCount',
            'checkedInArrivalsCount',
            'pendingArrivalsCount',
            'departuresCount',
            'completedCheckoutsCount',
            'pendingDeparturesCount',
            'inHouseGuestsCount',
            'inHouseBookings',
            'checklist',
            'pastAudits',
            'allRooms',
            'occupiedRooms',
            'occupiedRoomIds',
            'activeBookingsByRoom',
            'activeMaintenanceRooms',
            'todayHousekeepingHistories',
            'todayOperationalHistories',
            'todayAllBookings',
            'totalUnsettledAmount',
            'unsettledBookingsCount',
            'settledBookingsCount'
        ));
    }

    public function complete(Request $request)
    {
        $validated = $request->validate([
            'audit_date' => 'required|date',
            'remarks' => 'nullable|string|max:1000',
            'auditor_name' => 'nullable|string|max:100',
        ]);

        $today = Carbon::today()->format('Y-m-d');
        $auditDate = Carbon::parse($validated['audit_date'])->toDateString();

        // 1. Audit can only be performed and stored for today's date (not past, not future)
        if ($auditDate !== $today) {
            return response()->json([
                'success' => false,
                'error' => 'Night audit can only be performed and stored for today (' . Carbon::parse($today)->format('d M Y') . '). Past and future dates cannot be audited.'
            ], 422);
        }

        // 2. Re-run audit is not supported. Only one audit in today only.
        $existingAudit = NightAudit::whereDate('audit_date', $today)->first();
        if ($existingAudit) {
            return response()->json([
                'success' => false,
                'error' => "Today's night audit has already been completed at " . ($existingAudit->completed_at ? $existingAudit->completed_at->format('h:i A') : '') . ". Re-running the audit is not supported."
            ], 422);
        }

        $auditorName = $validated['auditor_name'] ?? (Auth::user()?->name ?? 'Front Office Night Auditor');
        $auditorId = Auth::id() ?? null;

        DB::beginTransaction();
        try {
            // 1. Detailed Payments List
            $payments = Payment::where(function($q) use ($auditDate) {
                $q->whereDate('payment_date', $auditDate)
                  ->orWhere(function($sub) use ($auditDate) {
                      $sub->whereNull('payment_date')
                          ->whereDate('created_at', $auditDate);
                  });
            })->with(['paymentMode', 'booking.guest.title', 'booking.room'])->get();

            $totalPayments = (float)$payments->sum('amount');
            $advancePayments = (float)$payments->where('payment_type', 'Advance')->sum('amount');
            $settlementPayments = (float)$payments->where('payment_type', 'Settlement')->sum('amount');

            $cash = 0; $upi = 0; $card = 0; $bank = 0; $other = 0;
            foreach ($payments as $p) {
                $m = strtolower($p->paymentMode?->name ?? '');
                $amt = (float)$p->amount;
                if (str_contains($m, 'cash')) $cash += $amt;
                elseif (str_contains($m, 'upi') || str_contains($m, 'gpay') || str_contains($m, 'paytm') || str_contains($m, 'phonepe')) $upi += $amt;
                elseif (str_contains($m, 'card') || str_contains($m, 'pos')) $card += $amt;
                elseif (str_contains($m, 'bank') || str_contains($m, 'neft') || str_contains($m, 'rtgs')) $bank += $amt;
                else $other += $amt;
            }

            $paymentsListItems = $payments->map(function($p) {
                $b = $p->booking;
                $totalBill = (float)($b ? ($b->payable_amount > 0 ? $b->payable_amount : ($b->total_amount > 0 ? $b->total_amount : $p->amount)) : $p->amount);
                $paid = (float)($b && $b->payments ? $b->payments->sum('amount') : $p->amount);
                if ($paid <= 0 && $b && (float)$b->advance_amount > 0) {
                    $paid = (float)$b->advance_amount;
                }
                $rem = max(0, round($totalBill - $paid, 2));
                $guestName = $b ? (($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Guest')) : '-';
                $roomNum = $b && $b->room ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number))) : '-';
                $folioNum = $b ? ($b->primary_booking_id ?: '#BK-' . $b->id) : '-';

                return [
                    'id' => $p->id,
                    'time' => $p->payment_time ? Carbon::parse($p->payment_time)->format('h:i A') : ($p->created_at ? $p->created_at->format('h:i A') : '-'),
                    'guest_name' => $guestName,
                    'room_number' => $roomNum,
                    'folio_number' => $folioNum,
                    'total_bill' => $totalBill,
                    'paid_amount' => $paid,
                    'remaining_amount' => $rem,
                    'is_settled' => ($rem <= 0.01),
                    'mode' => $p->paymentMode?->name ?? 'Cash',
                    'type' => $p->payment_type,
                    'amount' => (float)$p->amount,
                ];
            })->values()->all();

            // 2. Room metrics & Occupied Rooms List
            $allRoomsList = Room::with(['categoryRelation', 'floorRelation', 'beddingConfigRelation', 'operationalHistories.operationalStatus', 'housekeepingHistories.housekeepingStatus'])->get();
            $totalRooms = $allRoomsList->count();

            $activeBookings = Booking::with(['guest.title', 'room.categoryRelation', 'plan', 'payments'])
                ->where(function($q) use ($auditDate) {
                    $q->whereDate('reserve_date', '<=', $auditDate)
                      ->whereDate('checkout_date', '>=', $auditDate);
                })
                ->whereNotIn('status', ['Cancelled', 'Checked Out'])
                ->get();

            $activeBookingsByRoom = $activeBookings->keyBy('room_id');
            $activeMaintRoomIds = RoomMaintenance::where('status', 'In Progress')->pluck('room_id')->toArray();
            $maintenanceRooms = count(array_unique($activeMaintRoomIds));

            $occupiedRoomsList = [];
            foreach ($allRoomsList as $rm) {
                $isMaint = in_array($rm->id, $activeMaintRoomIds);
                $activeB = $activeBookingsByRoom->get($rm->id);
                if ($activeB && !$isMaint) {
                    $latestHk = $rm->housekeepingHistories ? $rm->housekeepingHistories->sortByDesc('id')->first() : null;
                    $hkName = $latestHk?->housekeepingStatus?->name ?? ($latestHk?->housekeepingState?->name ?? 'Clean');

                    $latestOp = $rm->operationalHistories ? $rm->operationalHistories->sortByDesc('id')->first() : null;
                    $opName = $latestOp?->operationalStatus?->name ?? 'Active';

                    $n = max(1, (int)$activeB->total_nights);
                    $dayRate = (float)($activeB->room_rate ?: ($activeB->total_amount / $n));

                    $occupiedRoomsList[] = [
                        'room_id' => $rm->id,
                        'room_number' => 'R' . preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number)),
                        'category' => $rm->categoryRelation?->name ?? 'Standard',
                        'floor' => 'Floor ' . ($rm->floorRelation?->floor ?? '1'),
                        'guest_name' => ($activeB->guest?->title?->name ? $activeB->guest->title->name . ' ' : '') . ($activeB->guest?->guest_name ?? 'Guest'),
                        'folio_number' => $activeB->primary_booking_id ?: '#BK-' . $activeB->id,
                        'checkin_date' => $activeB->reserve_date ? Carbon::parse($activeB->reserve_date)->format('d M Y') : '-',
                        'checkout_date' => $activeB->checkout_date ? Carbon::parse($activeB->checkout_date)->format('d M Y') : '-',
                        'operational_status' => $opName,
                        'housekeeping_status' => $hkName,
                        'day_rate' => $dayRate,
                    ];
                }
            }

            $occupiedRooms = count($occupiedRoomsList);
            $vacantRooms = max(0, $totalRooms - $occupiedRooms - $maintenanceRooms);
            $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0.00;

            // 3. Room and Food Revenues
            $roomRev = 0; $foodRev = 0; $taxRev = 0; $discRev = 0;
            $ep = 0; $cp = 0; $map = 0; $ap = 0;

            foreach ($activeBookings as $b) {
                $n = max(1, (int)$b->total_nights);
                $roomRev += (float)($b->room_rate ?: ($b->total_amount / $n));
                $foodRev += (float)($b->plan?->price ?? 0);
                $taxRev += (float)($b->gst_amount / $n);
                $discRev += (float)($b->discount_amount / $n);

                $planName = strtoupper($b->plan?->plan_name ?? '');
                if (str_contains($planName, 'AP') || str_contains($planName, 'AMERICAN')) $ap++;
                elseif (str_contains($planName, 'MAP')) $map++;
                elseif (str_contains($planName, 'CP') || str_contains($planName, 'BREAKFAST')) $cp++;
                else $ep++;
            }

            $roomRev = round($roomRev, 2);
            $foodRev = round($foodRev, 2);
            $taxRev = round($taxRev, 2);
            $discRev = round($discRev, 2);
            $totalRev = round($roomRev + $foodRev + $taxRev - $discRev, 2);
            $adr = $occupiedRooms > 0 ? round($roomRev / $occupiedRooms, 2) : 0.00;
            $revpar = $totalRooms > 0 ? round($roomRev / $totalRooms, 2) : 0.00;

            // 4. Housekeeping History List
            $hkHistories = RoomHousekeepingHistory::with(['room.categoryRelation', 'room.floorRelation', 'housekeepingStatus'])
                ->where(function($q) use ($auditDate) {
                    $q->whereDate('created_at', $auditDate)
                      ->orWhereDate('start_time', $auditDate)
                      ->orWhereDate('completion_time', $auditDate);
                })
                ->orderBy('id', 'desc')
                ->get();

            $hkHistoriesList = $hkHistories->map(function($hk) {
                return [
                    'id' => $hk->id,
                    'time' => $hk->created_at ? $hk->created_at->format('h:i A') : '-',
                    'date' => $hk->created_at ? $hk->created_at->format('d M Y') : '-',
                    'room_number' => $hk->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($hk->room->room_number))) : ('Room #' . $hk->room_id),
                    'category' => $hk->room?->categoryRelation?->name ?? 'Standard',
                    'floor' => 'Floor ' . ($hk->room?->floorRelation?->floor ?? '1'),
                    'housekeeping_status' => $hk->housekeepingStatus?->name ?? 'Clean',
                    'status' => ucfirst($hk->status ?? 'Complete'),
                    'start_time' => $hk->start_time ? Carbon::parse($hk->start_time)->format('h:i A') : '-',
                    'completion_time' => $hk->completion_time ? Carbon::parse($hk->completion_time)->format('h:i A') : '-',
                ];
            })->values()->all();

            // 5. Operational History List
            $opHistories = RoomOperationalHistory::with(['room.categoryRelation', 'room.floorRelation', 'operationalStatus'])
                ->whereDate('created_at', $auditDate)
                ->orderBy('id', 'desc')
                ->get();

            $opHistoriesList = $opHistories->map(function($op) {
                return [
                    'id' => $op->id,
                    'time' => $op->created_at ? $op->created_at->format('h:i A') : '-',
                    'date' => $op->created_at ? $op->created_at->format('d M Y') : '-',
                    'room_number' => $op->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($op->room->room_number))) : ('Room #' . $op->room_id),
                    'category' => $op->room?->categoryRelation?->name ?? 'Standard',
                    'floor' => 'Floor ' . ($op->room?->floorRelation?->floor ?? '1'),
                    'operational_status' => $op->operationalStatus?->name ?? 'Active',
                    'room_status' => $op->room?->status ?? 'Active',
                ];
            })->values()->all();

            // 6. Movements & Today All Guests List
            $arrivals = Booking::whereDate('reserve_date', $auditDate)->count();
            $departures = Booking::whereDate('checkout_date', $auditDate)->count();
            $inhouse = Booking::whereIn('status', ['Checked In', 'Stay Over', 'Arrived'])->count();
            $pendingArr = Booking::whereDate('reserve_date', $auditDate)->whereIn('status', ['Confirmed', 'Arrived'])->count();
            $pendingDep = Booking::whereDate('checkout_date', '<=', $auditDate)->whereNotIn('status', ['Checked Out', 'Cancelled'])->count();

            $todayGuestsQuery = Booking::with([
                'guest.title',
                'room.categoryRelation',
                'room.floorRelation',
                'plan',
                'payments',
                'primaryBooking.payments'
            ])
            ->where(function($q) use ($auditDate) {
                $q->where(function($sub) use ($auditDate) {
                    $sub->whereDate('reserve_date', '<=', $auditDate)
                        ->whereDate('checkout_date', '>=', $auditDate);
                })
                ->orWhereDate('reserve_date', $auditDate)
                ->orWhereDate('checkin_date', $auditDate)
                ->orWhereDate('checkout_date', $auditDate);
            })
            ->whereNotIn('status', ['Cancelled'])
            ->orderBy('id', 'desc')
            ->get();

            $totalUnsettledAmount = 0;
            $unsettledBookingsCount = 0;
            $settledBookingsCount = 0;

            $guestsList = $todayGuestsQuery->map(function($b) use (&$totalUnsettledAmount, &$unsettledBookingsCount, &$settledBookingsCount) {
                $isAdditional = !empty($b->primary_booking_id);
                $master = ($isAdditional && $b->primaryBooking) ? $b->primaryBooking : $b;

                $bill = (float)($master->payable_amount > 0 ? $master->payable_amount : $master->total_amount);
                $paid = (float)($master->payments ? $master->payments->sum('amount') : 0);
                if ($paid <= 0 && (float)$master->advance_amount > 0) {
                    $paid = (float)$master->advance_amount;
                }
                $rem = max(0, round($bill - $paid, 2));

                if (!$isAdditional) {
                    if ($rem > 0.01) {
                        $totalUnsettledAmount += $rem;
                        $unsettledBookingsCount++;
                    } else {
                        $settledBookingsCount++;
                    }
                }

                return [
                    'id' => $b->id,
                    'folio_number' => $b->primary_booking_id ?: ('#BK-' . $b->id),
                    'guest_name' => ($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Guest'),
                    'mobile_no' => $b->guest?->mobile_no ?: '-',
                    'room_number' => $b->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number))) : '-',
                    'category' => $b->room?->categoryRelation?->name ?? 'Standard',
                    'checkin_date' => $b->reserve_date ? Carbon::parse($b->reserve_date)->format('d M Y') : '-',
                    'checkout_date' => $b->checkout_date ? Carbon::parse($b->checkout_date)->format('d M Y') : '-',
                    'total_bill' => $bill,
                    'paid_amount' => $paid,
                    'remaining_amount' => $rem,
                    'is_settled' => ($rem <= 0.01),
                    'status' => $b->status,
                ];
            })->values()->all();

            // Prepare complete comprehensive JSON snapshot
            $snapshot = [
                'audit_date' => $auditDate,
                'completed_at' => now()->toIso8601String(),
                'auditor_name' => $auditorName,
                'summary' => [
                    'total_payments' => $totalPayments,
                    'advance_payments' => $advancePayments,
                    'settlement_payments' => $settlementPayments,
                    'cash_collection' => $cash,
                    'upi_collection' => $upi,
                    'card_collection' => $card,
                    'bank_collection' => $bank,
                    'other_collection' => $other,
                    'room_revenue' => $roomRev,
                    'food_revenue' => $foodRev,
                    'tax_amount' => $taxRev,
                    'discount_amount' => $discRev,
                    'total_revenue' => $totalRev,
                    'total_rooms' => $totalRooms,
                    'occupied_rooms' => $occupiedRooms,
                    'vacant_rooms' => $vacantRooms,
                    'maintenance_rooms' => $maintenanceRooms,
                    'occupancy_rate' => $occupancyRate,
                    'adr' => $adr,
                    'revpar' => $revpar,
                    'total_unsettled_amount' => $totalUnsettledAmount,
                    'unsettled_bookings_count' => $unsettledBookingsCount,
                    'settled_bookings_count' => $settledBookingsCount,
                    'arrivals_count' => $arrivals,
                    'departures_count' => $departures,
                    'inhouse_guests_count' => $inhouse,
                    'pending_arrivals' => $pendingArr,
                    'pending_departures' => $pendingDep,
                ],
                'payments_list' => $paymentsListItems,
                'occupied_rooms' => $occupiedRoomsList,
                'housekeeping_histories' => $hkHistoriesList,
                'operational_histories' => $opHistoriesList,
                'guests_list' => $guestsList,
            ];

            // Store NightAudit record (single audit only)
            $nightAudit = NightAudit::create([
                'audit_date' => $auditDate,
                'completed_at' => now(),
                'auditor_id' => $auditorId,
                'auditor_name' => $auditorName,
                'status' => 'Completed',
                'total_payments' => $totalPayments,
                'advance_payments' => $advancePayments,
                'settlement_payments' => $settlementPayments,
                'cash_collection' => $cash,
                'upi_collection' => $upi,
                'card_collection' => $card,
                'bank_collection' => $bank,
                'other_collection' => $other,
                'room_revenue' => $roomRev,
                'food_revenue' => $foodRev,
                'tax_amount' => $taxRev,
                'discount_amount' => $discRev,
                'total_revenue' => $totalRev,
                'total_rooms' => $totalRooms,
                'occupied_rooms' => $occupiedRooms,
                'vacant_rooms' => $vacantRooms,
                'maintenance_rooms' => $maintenanceRooms,
                'dirty_rooms' => 0,
                'clean_rooms' => 0,
                'occupancy_rate' => $occupancyRate,
                'adr' => $adr,
                'revpar' => $revpar,
                'arrivals_count' => $arrivals,
                'departures_count' => $departures,
                'inhouse_guests_count' => $inhouse,
                'stay_over_count' => 0,
                'cancelled_count' => 0,
                'pending_arrivals' => $pendingArr,
                'pending_departures' => $pendingDep,
                'ep_plan_count' => $ep,
                'cp_plan_count' => $cp,
                'map_plan_count' => $map,
                'ap_plan_count' => $ap,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Night Audit for ' . Carbon::parse($auditDate)->format('d M Y') . ' has been completed and permanently recorded in the database!',
                'audit' => $nightAudit
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to complete Night Audit: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getReport($id)
    {
        $audit = NightAudit::with('auditor')->findOrFail($id);

        return response()->json([
            'success' => true,
            'audit' => $audit
        ]);
    }
}
