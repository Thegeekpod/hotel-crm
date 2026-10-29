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
        if (empty($snapshot['payments_list']) || empty($snapshot['occupied_rooms']) || !isset($snapshot['inward_stock'])) {
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

        // 1b. Direct Walk-in / Takeaway POS Payments
        $posTakeaways = \App\Models\PosOrder::with('items')
            ->whereDate('created_at', $auditDate)
            ->where(function($q) {
                $q->where('order_type', 'Takeaway')
                  ->orWhereNull('booking_id');
            })
            ->where('status', '!=', 'Cancelled')
            ->whereIn('billing_type', ['Cash Paid', 'Digital UPI / Card Paid'])
            ->orderBy('id', 'asc')
            ->get();

        foreach ($posTakeaways as $pwi) {
            $tableLabel = !empty($pwi->table_number) ? $pwi->table_number : null;
            $custName = $pwi->customer_name ?: ($tableLabel ? "{$tableLabel} Guest" : 'Takeaway Customer');
            $custPhone = $pwi->customer_phone ? ' (' . $pwi->customer_phone . ')' : '';
            $modeDesc = ($pwi->billing_type === 'Cash Paid') ? 'Cash' : 'UPI / Card';
            $orderTypeDesc = $pwi->order_type === 'Table Dine-in'
                ? ($tableLabel ? "Table Dine-in [{$tableLabel}]" : 'Table Dine-in')
                : ($tableLabel ? "Takeaway [{$tableLabel}]" : 'Takeaway POS');

            $paymentsListItems[] = [
                'id' => 'pos_' . $pwi->id,
                'time' => $pwi->created_at ? $pwi->created_at->format('h:i A') : '-',
                'guest_name' => $custName . $custPhone . ' [' . ($tableLabel ?: $pwi->order_type) . ']',
                'room_number' => $pwi->room_number ? 'Room ' . $pwi->room_number : ($tableLabel ?: '-'),
                'settle_number' => '-',
                'folio_number' => '-',
                'total_bill' => (float)$pwi->total_amount,
                'paid_amount' => (float)$pwi->total_amount,
                'remaining_amount' => 0.00,
                'is_settled' => true,
                'mode' => $modeDesc,
                'type' => $orderTypeDesc,
                'amount' => (float)$pwi->total_amount,
            ];
        }

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

        // 5. Today Booking Ledgers (Grouped by Booking Number with Rooms & Extra Services)
        $todayGuestsQuery = Booking::with([
            'guest.title',
            'room.categoryRelation',
            'room.floorRelation',
            'plan',
            'payments.paymentMode',
            'settlements.paymentMode',
            'additionalBookings.guest.title',
            'additionalBookings.room.categoryRelation',
            'additionalBookings.room.floorRelation',
            'additionalBookings.payments.paymentMode',
            'primaryBooking.guest.title',
            'primaryBooking.room.categoryRelation',
            'primaryBooking.room.floorRelation',
            'primaryBooking.payments.paymentMode',
            'primaryBooking.additionalBookings.guest.title',
            'primaryBooking.additionalBookings.room.categoryRelation',
            'primaryBooking.additionalBookings.room.floorRelation',
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

        $reservationGroups = [];
        foreach ($todayGuestsQuery as $b) {
            $isAddl = !empty($b->primary_booking_id);
            $master = ($isAddl && $b->primaryBooking) ? $b->primaryBooking : $b;
            $masterId = $master->id;

            if (!isset($reservationGroups[$masterId])) {
                $reservationGroups[$masterId] = [
                    'master' => $master,
                    'bookings' => collect(),
                ];
            }
            $reservationGroups[$masterId]['bookings']->push($b);
        }

        $guestsList = [];
        $totalUnsettledAmount = 0;
        $unsettledBookingsCount = 0;
        $settledBookingsCount = 0;

        foreach ($reservationGroups as $mId => $group) {
            $master = $group['master'];
            $allResBookings = collect([$master])
                ->concat($master->additionalBookings ?? collect())
                ->concat($group['bookings'])
                ->unique('id');
            $allResBookingIds = $allResBookings->pluck('id')->filter()->all();

            $roomsMap = [];
            foreach ($allResBookings as $resB) {
                if ($resB->room) {
                    $rNum = 'R' . preg_replace('/^r[-_\s]?/i', '', trim($resB->room->room_number));
                    $fl = $resB->room->floorRelation?->floor ?? ($resB->room->floor ?? '1');
                    $cat = $resB->room->categoryRelation?->name ?? ($resB->room->category ?? 'Standard');
                    $roomsMap[$resB->room->id] = [
                        'room_number' => $rNum,
                        'floor' => (string)$fl,
                        'category' => $cat,
                        'label' => "{$rNum} (Floor {$fl})",
                    ];
                }
            }
            if (empty($roomsMap)) {
                $roomsMap[] = [
                    'room_number' => 'Unassigned',
                    'floor' => '1',
                    'category' => 'Standard',
                    'label' => 'Unassigned',
                ];
            }

            $pGuest = $master->guest;
            $pName = ($pGuest?->title?->name ? $pGuest->title->name . ' ' : '') . ($pGuest?->guest_name ?? 'Guest');
            $pPhone = $pGuest?->mobile ?: ($pGuest?->mobile_no ?: '-');

            $occupantNames = $allResBookings->map(function($rb) {
                $g = $rb->guest;
                return ($g?->title?->name ? $g->title->name . ' ' : '') . ($g?->guest_name ?? 'Guest');
            })->filter()->unique()->values()->all();

            $totalOccupants = count($occupantNames);

            $roomPart = $master->room?->room_number ? preg_replace('/^r[-_\s]?/i', '', trim($master->room->room_number)) : '100';
            $settleNo = $master->settle_number ?: ($master->folio_number ?: ('SET-' . $roomPart . '-' . $master->id));
            $bookingNo = $master->reserve_id ?: ('RES-' . $master->id);
            $checkinFmt = $master->reserve_date ? Carbon::parse($master->reserve_date)->format('d M Y') : '-';
            $checkoutFmt = $master->checkout_date ? Carbon::parse($master->checkout_date)->format('d M Y') : '-';

            $roomCharges = (float)($master->payable_amount > 0 ? $master->payable_amount : $master->total_amount);

            $extraOrders = \App\Models\RoomServiceOrder::with('items')
                ->whereIn('booking_id', $allResBookingIds)
                ->where('order_status', '!=', 'Cancelled')
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            $extraServicesCharge = (float)$extraOrders->where('payment_status', 'Charged to Room Folio')->sum('total_amount');
            $extraPaidCharges = (float)$extraOrders->where('payment_status', '!=', 'Charged to Room Folio')->sum('total_amount');
            $extraOrdersCount = $extraOrders->count();
            $extraOrdersDetailed = [];
            $extraItemsList = [];
            foreach ($extraOrders as $eo) {
                $orderItems = [];
                foreach ($eo->items as $it) {
                    $orderItems[] = "{$it->quantity}x {$it->item_name}";
                    $extraItemsList[] = "{$it->quantity}x {$it->item_name}";
                }
                $isPaid = ($eo->payment_status !== 'Charged to Room Folio');
                $extraOrdersDetailed[] = [
                    'id' => $eo->id,
                    'order_number' => $eo->order_number,
                    'total_amount' => (float)$eo->total_amount,
                    'date_time' => $eo->created_at ? $eo->created_at->format('d M Y, h:i A') : 'N/A',
                    'date_short' => $eo->created_at ? $eo->created_at->format('d M') : 'N/A',
                    'items_summary' => implode(', ', $orderItems),
                    'table_number' => $eo->table_number ?: null,
                    'payment_status' => $eo->payment_status,
                    'is_paid' => $isPaid,
                ];
            }

            $grandTotal = round($roomCharges + $extraServicesCharge, 2);

            $paid = (float)\App\Models\Payment::whereIn('booking_id', $allResBookingIds)->sum('amount');
            if ($paid <= 0 && (float)$master->advance_amount > 0) {
                $paid = (float)$master->advance_amount;
            }

            $rem = max(0, round($grandTotal - $paid, 2));
            $isSettled = ($rem <= 0.01);

            if ($rem > 0.01) {
                $totalUnsettledAmount += $rem;
                $unsettledBookingsCount++;
            } else {
                $settledBookingsCount++;
            }

            $checkedOutCount = $allResBookings->where('status', 'Checked Out')->count();
            $stayStatus = ($checkedOutCount >= $allResBookings->count()) ? 'Checked Out' : ($master->status ?: 'Confirmed');

            $transfers = \App\Models\RoomTransfer::with(['fromRoom', 'toRoom'])
                ->where(function ($q) use ($master, $allResBookingIds) {
                    $q->where('primary_booking_id', $master->id)
                      ->orWhereIn('booking_id', $allResBookingIds);
                })
                ->get();

            $transfersList = [];
            foreach ($transfers as $t) {
                $fromR = $t->fromRoom?->room_number ?? 'Room';
                $toR = $t->toRoom?->room_number ?? 'Room';
                $transfersList[] = [
                    'from' => $fromR,
                    'to' => $toR,
                    'date' => $t->transfer_date ? $t->transfer_date->format('d M') : '',
                    'diff' => (float)$t->tariff_difference,
                ];
            }

            $guestsList[] = [
                'id' => $master->id,
                'booking_id' => $master->id,
                'booking_number' => $bookingNo,
                'settle_number' => $settleNo,
                'folio_number' => $settleNo,
                'primary_guest_name' => $pName,
                'guest_name' => $pName,
                'mobile_no' => $pPhone,
                'primary_mobile' => $pPhone,
                'rooms' => array_values($roomsMap),
                'room_number' => implode(', ', array_column($roomsMap, 'room_number')),
                'category' => $master->room?->categoryRelation?->name ?? 'Standard',
                'occupants_names' => $occupantNames,
                'total_occupants' => $totalOccupants,
                'checkin_date' => $checkinFmt,
                'checkout_date' => $checkoutFmt,
                'transfers' => $transfersList,
                'stay_status' => $stayStatus,
                'status' => $stayStatus,
                'room_charges' => $roomCharges,
                'extra_service_charges' => $extraServicesCharge,
                'extra_paid_charges' => $extraPaidCharges,
                'extra_orders_count' => $extraOrdersCount,
                'extra_orders_detailed' => $extraOrdersDetailed,
                'extra_items_list' => $extraItemsList,
                'grand_total_bill' => $grandTotal,
                'total_bill' => $grandTotal,
                'paid_amount' => $paid,
                'remaining_amount' => $rem,
                'is_settled' => $isSettled,
            ];
        }

        // Append POS Walk-in / Takeaway orders with '-' for hotel room/folio fields
        foreach ($posTakeaways as $pwi) {
            $tableLabel = !empty($pwi->table_number) ? $pwi->table_number : null;
            $custName = $pwi->customer_name ?: ($tableLabel ? "{$tableLabel} Guest" : 'Takeaway Customer');
            $custPhone = $pwi->customer_phone ?: '-';
            $modeDesc = ($pwi->billing_type === 'Cash Paid') ? 'Cash Paid' : 'UPI / Card Paid';

            $takeawayItems = [];
            foreach ($pwi->items as $it) {
                $takeawayItems[] = "{$it->quantity}x {$it->item_name}";
            }

            $amount = (float)$pwi->total_amount;
            $settledBookingsCount++;

            $stayStatus = $pwi->order_type === 'Table Dine-in'
                ? ('Table Dine-in (' . ($tableLabel ?: 'Table') . ')')
                : ($tableLabel ? ('Takeaway (' . $tableLabel . ')') : 'Takeaway POS');

            $assignedRoomOrTable = $pwi->room_number ? 'Room ' . $pwi->room_number : ($tableLabel ?: '-');

            $guestsList[] = [
                'id' => 'pos_' . $pwi->id,
                'booking_id' => null,
                'booking_number' => '-',
                'settle_number' => '-',
                'folio_number' => '-',
                'primary_guest_name' => $custName,
                'guest_name' => $custName,
                'mobile_no' => $custPhone,
                'primary_mobile' => $custPhone,
                'rooms' => [
                    [
                        'room_id' => null,
                        'room_number' => $assignedRoomOrTable,
                        'floor' => '-',
                        'category' => '-',
                        'label' => '-',
                    ]
                ],
                'room_number' => $assignedRoomOrTable,
                'category' => '-',
                'occupants_names' => [$custName],
                'total_occupants' => 1,
                'checkin_date' => '-',
                'checkout_date' => '-',
                'transfers' => [],
                'stay_status' => $stayStatus,
                'status' => $stayStatus,
                'room_charges' => 0.00,
                'extra_service_charges' => 0.00,
                'extra_paid_charges' => $amount,
                'extra_orders_count' => 1,
                'extra_orders_detailed' => [
                    [
                        'id' => $pwi->id,
                        'order_number' => $pwi->order_number,
                        'total_amount' => $amount,
                        'date_time' => $pwi->created_at ? $pwi->created_at->format('d M Y, h:i A') : 'N/A',
                        'date_short' => $pwi->created_at ? $pwi->created_at->format('d M') : 'N/A',
                        'items_summary' => implode(', ', $takeawayItems),
                        'table_number' => $tableLabel,
                        'payment_status' => $modeDesc,
                        'is_paid' => true,
                    ]
                ],
                'extra_items_list' => $takeawayItems,
                'grand_total_bill' => $amount,
                'total_bill' => $amount,
                'paid_amount' => $amount,
                'remaining_amount' => 0.00,
                'is_settled' => true,
            ];
        }

        // 6. Store Inward Stock Movements with amount (total_cost > 0)
        $inwardStockMovements = \App\Models\StoreMovement::with(['posItem', 'item'])
            ->where('type', 'IN')
            ->whereDate('created_at', $auditDate)
            ->whereNotNull('total_cost')
            ->where('total_cost', '>', 0)
            ->orderBy('id', 'desc')
            ->get();

        $totalInwardAmount = (float)$inwardStockMovements->sum('total_cost');

        $inwardStockList = $inwardStockMovements->map(function($m) {
            $itemName = $m->posItem?->name ?? ($m->item?->name ?? 'Kitchen Provision');
            return [
                'id' => $m->id,
                'time' => $m->created_at ? $m->created_at->format('h:i A') : '-',
                'date' => $m->created_at ? $m->created_at->format('d M Y') : '-',
                'item_name' => $itemName,
                'reference_no' => $m->reference_no ?: '-',
                'quantity' => (int)$m->quantity,
                'unit_cost' => $m->unit_cost ? (float)$m->unit_cost : null,
                'total_cost' => (float)$m->total_cost,
                'remarks' => $m->remarks ?: '-',
                'performed_by' => $m->performed_by ?: '-',
                'department' => $m->issued_to_department ?: 'Kitchen Store',
            ];
        })->values()->all();

        return [
            'payments_list' => $paymentsListItems,
            'occupied_rooms' => $occupiedRoomsList,
            'housekeeping_histories' => $hkHistoriesList,
            'operational_histories' => $opHistoriesList,
            'guests_list' => $guestsList,
            'inward_stock' => $inwardStockList,
            'total_inward_amount' => $totalInwardAmount,
            'unsettled_summary' => [
                'total_unsettled_amount' => $totalUnsettledAmount,
                'unsettled_bookings_count' => $unsettledBookingsCount,
                'settled_bookings_count' => $settledBookingsCount,
            ]
        ];
    }
}
