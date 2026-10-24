<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NightAudit;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\RoomHousekeepingHistory;
use App\Models\RoomOperationalHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NightAuditController extends Controller
{
    /**
     * Display list of all historical night audits in the Admin panel
     */
    public function index(Request $request)
    {
        $query = NightAudit::with('auditor')->orderBy('audit_date', 'desc');

        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        $search = $request->query('search');

        if ($request->filled('from_date')) {
            $query->whereDate('audit_date', '>=', $fromDate);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('audit_date', '<=', $toDate);
        }

        if ($request->filled('search')) {
            $s = trim($search);
            $query->where(function ($q) use ($s) {
                $q->where('auditor_name', 'like', "%{$s}%")
                  ->orWhere('remarks', 'like', "%{$s}%")
                  ->orWhereDate('audit_date', $s);
            });
        }

        $audits = $query->paginate(20)->withQueryString();

        // Overall Historical Aggregate Metrics
        $totalAuditsCount = NightAudit::count();
        $totalAuditedRevenue = (float)NightAudit::sum('total_revenue');
        $totalAuditedPayments = (float)NightAudit::sum('total_payments');
        $avgOccupancy = (float)NightAudit::avg('occupancy_rate');

        return view('admin.nightaudit.index', compact(
            'audits',
            'totalAuditsCount',
            'totalAuditedRevenue',
            'totalAuditedPayments',
            'avgOccupancy',
            'fromDate',
            'toDate',
            'search'
        ));
    }

    /**
     * Get detailed JSON data for an audit record to view or print
     */
    public function show($id)
    {
        $audit = NightAudit::with('auditor')->findOrFail($id);

        $snapshot = $audit->audit_snapshot ?? [];
        $auditDate = $audit->audit_date ? $audit->audit_date->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        // If snapshot is missing granular lists, reconstruct them dynamically so modal displays 100% of the data
        if (empty($snapshot['payments_list']) || empty($snapshot['occupied_rooms'])) {
            $built = $this->buildFullAuditSnapshot($auditDate, $audit);
            $audit->audit_snapshot = array_merge($snapshot, $built);
            $audit->saveQuietly();
        }

        return response()->json([
            'success' => true,
            'audit' => $audit
        ]);
    }

    /**
     * Build granular arrays for historical audits if snapshot is partial
     */
    protected function buildFullAuditSnapshot($auditDate, $audit)
    {
        // 1. Payments List
        $payments = Payment::where(function($q) use ($auditDate) {
            $q->whereDate('payment_date', $auditDate)
              ->orWhere(function($sub) use ($auditDate) {
                  $sub->whereNull('payment_date')
                      ->whereDate('created_at', $auditDate);
              });
        })->with(['paymentMode', 'booking.guest.title', 'booking.room'])->get();

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
                'settle_number' => $folioNum,
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

        // 2. Occupied Rooms List
        $allRoomsList = Room::with(['categoryRelation', 'floorRelation', 'beddingConfigRelation', 'operationalHistories.operationalStatus', 'housekeepingHistories.housekeepingStatus'])->get();
        $activeBookings = Booking::with(['guest.title', 'room.categoryRelation', 'plan', 'payments'])
            ->where(function($q) use ($auditDate) {
                $q->whereDate('reserve_date', '<=', $auditDate)
                  ->whereDate('checkout_date', '>=', $auditDate);
            })
            ->whereNotIn('status', ['Cancelled', 'Checked Out'])
            ->get();

        $activeBookingsByRoom = $activeBookings->keyBy('room_id');
        $activeMaintRoomIds = RoomMaintenance::where('status', 'In Progress')->pluck('room_id')->toArray();

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

                $rmPart = preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number)) ?: '100';
                $activeSettle = $activeB->settle_number ?: ($activeB->folio_number ?: ($activeB->primary_booking_id ?: ('SET-' . $rmPart . '-' . $activeB->id)));

                $occupiedRoomsList[] = [
                    'room_id' => $rm->id,
                    'room_number' => 'R' . preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number)),
                    'category' => $rm->categoryRelation?->name ?? 'Standard',
                    'floor' => 'Floor ' . ($rm->floorRelation?->floor ?? '1'),
                    'guest_name' => ($activeB->guest?->title?->name ? $activeB->guest->title->name . ' ' : '') . ($activeB->guest?->guest_name ?? 'Guest'),
                    'settle_number' => $activeSettle,
                    'folio_number' => $activeSettle,
                    'checkin_date' => $activeB->reserve_date ? Carbon::parse($activeB->reserve_date)->format('d M Y') : '-',
                    'checkout_date' => $activeB->checkout_date ? Carbon::parse($activeB->checkout_date)->format('d M Y') : '-',
                    'operational_status' => $opName,
                    'housekeeping_status' => $hkName,
                    'day_rate' => $dayRate,
                ];
            }
        }

        // 3. Housekeeping History List
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

        // 4. Operational History List
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

        // 5. Today All Guests List
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

            $roomPart = $b->room?->room_number ? preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number)) : '100';
            $settleNo = $b->settle_number ?: ($b->folio_number ?: ($master->settle_number ?: ($master->folio_number ?: ($b->primary_booking_id ?: ('SET-' . $roomPart . '-' . $b->id)))));

            return [
                'id' => $b->id,
                'settle_number' => $settleNo,
                'folio_number' => $settleNo,
                'guest_name' => ($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Guest'),
                'mobile_no' => $b->guest?->mobile ?: ($b->guest?->mobile_no ?: '-'),
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

        return [
            'payments_list' => $paymentsListItems,
            'occupied_rooms' => $occupiedRoomsList,
            'housekeeping_histories' => $hkHistoriesList,
            'operational_histories' => $opHistoriesList,
            'guests_list' => $guestsList,
            'unsettled_summary' => [
                'total_unsettled_amount' => $totalUnsettledAmount,
                'unsettled_bookings_count' => $unsettledBookingsCount,
                'settled_bookings_count' => $settledBookingsCount,
            ]
        ];
    }
}
