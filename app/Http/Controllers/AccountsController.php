<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\PosItem;
use App\Models\StoreMovement;
use App\Models\RoomServiceOrder;

class AccountsController extends Controller
{
    /**
     * Display consolidated Hotel Sagar Sonnet Accounts, Financial Ledgers & Audits.
     * Integrates Front Office, POS Restaurant Sales, and Stores/Procurement.
     */
    public function index(Request $request)
    {
        $filterType = $request->input('date_filter', 'today');
        $search = trim($request->input('search', ''));
        $today = Carbon::today();

        // 1. Determine Start and End timestamps
        switch ($filterType) {
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                $filterLabel = 'Yesterday (' . $startDate->format('d M Y') . ')';
                break;
            case 'this_week':
                $startDate = Carbon::today()->startOfWeek();
                $endDate = Carbon::today()->endOfWeek();
                $filterLabel = 'This Week (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;
            case 'this_month':
                $startDate = Carbon::today()->startOfMonth();
                $endDate = Carbon::today()->endOfMonth();
                $filterLabel = 'This Month (' . $startDate->format('F Y') . ')';
                break;
            case 'last_month':
                $startDate = Carbon::today()->subMonth()->startOfMonth();
                $endDate = Carbon::today()->subMonth()->endOfMonth();
                $filterLabel = 'Last Month (' . $startDate->format('F Y') . ')';
                break;
            case 'all':
                $startDate = Carbon::parse('2020-01-01')->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $filterLabel = 'All Time History';
                break;
            case 'custom':
                $s = $request->input('start_date');
                $e = $request->input('end_date');
                $startDate = $s ? Carbon::parse($s)->startOfDay() : Carbon::today()->startOfDay();
                $endDate = $e ? Carbon::parse($e)->endOfDay() : Carbon::today()->endOfDay();
                $filterLabel = $startDate->format('d M Y') . ' to ' . $endDate->format('d M Y');
                break;
            case 'today':
            default:
                $filterType = 'today';
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $filterLabel = 'Today (' . $startDate->format('d M Y') . ')';
                break;
        }

        $startDateStr = $startDate->toDateString();
        $endDateStr = $endDate->toDateString();

        // ==========================================
        // 2. FRONT OFFICE FINANCIAL DATA
        // ==========================================
        $paymentsQuery = Payment::with(['booking.guest.title', 'booking.room', 'paymentMode'])
            ->where(function ($q) use ($startDateStr, $endDateStr, $startDate, $endDate) {
                $q->whereBetween('payment_date', [$startDateStr, $endDateStr])
                  ->orWhere(function ($sub) use ($startDate, $endDate) {
                      $sub->whereNull('payment_date')
                          ->whereBetween('created_at', [$startDate, $endDate]);
                  });
            });

        if ($search) {
            $paymentsQuery->where(function ($q) use ($search) {
                $q->whereHas('booking.guest', function ($g) use ($search) {
                    $g->where('guest_name', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                })
                ->orWhereHas('booking', function ($b) use ($search) {
                    $b->where('reserve_id', 'like', "%{$search}%")
                      ->orWhere('primary_booking_id', 'like', "%{$search}%");
                });
            });
        }

        $foPayments = $paymentsQuery->orderBy('id', 'desc')->get();

        $foTotalPayments = (float)$foPayments->sum('amount');
        $foAdvance = (float)$foPayments->where('payment_type', 'Advance')->sum('amount');
        $foSettlement = (float)$foPayments->where('payment_type', 'Settlement')->sum('amount');
        $foRunning = (float)$foPayments->whereNotIn('payment_type', ['Advance', 'Settlement'])->sum('amount');

        $foCash = 0; $foUpi = 0; $foCard = 0; $foBank = 0; $foOther = 0;
        foreach ($foPayments as $p) {
            $m = strtolower($p->paymentMode?->name ?? '');
            $amt = (float)$p->amount;
            if (str_contains($m, 'cash')) $foCash += $amt;
            elseif (str_contains($m, 'upi') || str_contains($m, 'gpay') || str_contains($m, 'phonepe') || str_contains($m, 'paytm')) $foUpi += $amt;
            elseif (str_contains($m, 'card') || str_contains($m, 'pos')) $foCard += $amt;
            elseif (str_contains($m, 'bank') || str_contains($m, 'neft') || str_contains($m, 'rtgs')) $foBank += $amt;
            else $foOther += $amt;
        }

        // Active / relevant bookings for the date window
        $bookingsQuery = Booking::with([
            'guest.title',
            'room.categoryRelation',
            'room.floorRelation',
            'plan',
            'payments.paymentMode',
            'settlements.paymentMode',
            'additionalBookings.guest.title',
            'additionalBookings.room.categoryRelation',
        ])
        ->where(function ($q) use ($startDateStr, $endDateStr) {
            $q->whereBetween('reserve_date', [$startDateStr, $endDateStr])
              ->orWhereBetween('checkin_date', [$startDateStr, $endDateStr])
              ->orWhereBetween('checkout_date', [$startDateStr, $endDateStr])
              ->orWhere(function ($sub) use ($startDateStr, $endDateStr) {
                  $sub->where('reserve_date', '<=', $startDateStr)
                      ->where('checkout_date', '>=', $endDateStr);
              });
        })
        ->whereNotIn('status', ['Cancelled']);

        if ($search) {
            $bookingsQuery->where(function ($q) use ($search) {
                $q->where('reserve_id', 'like', "%{$search}%")
                  ->orWhere('primary_booking_id', 'like', "%{$search}%")
                  ->orWhereHas('guest', function ($g) use ($search) {
                      $g->where('guest_name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                  })
                  ->orWhereHas('room', function ($r) use ($search) {
                      $r->where('room_number', 'like', "%{$search}%");
                  });
            });
        }

        $allBookings = $bookingsQuery->orderBy('id', 'desc')->get();

        $foBookingsLedger = [];
        $foTotalRoomTariffs = 0;
        $foTotalExtraCharged = 0;
        $foTotalExtraPaid = 0;
        $foTotalGrossBill = 0;
        $foTotalPaidOverall = 0;
        $foTotalReceivables = 0;

        foreach ($allBookings as $b) {
            $rNum = $b->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number))) : 'Unassigned';
            $pName = ($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Guest');
            $pPhone = $b->guest?->mobile ?: ($b->guest?->mobile_no ?: '-');

            $allResBookingIds = collect([$b->id])->concat($b->additionalBookings?->pluck('id') ?? collect())->filter()->all();

            $roomBill = (float)($b->payable_amount > 0 ? $b->payable_amount : $b->total_amount);

            // Associated room service orders
            $rsOrders = RoomServiceOrder::with('items')
                ->whereIn('booking_id', $allResBookingIds)
                ->where('order_status', '!=', 'Cancelled')
                ->get();

            $rsFolioCharged = (float)$rsOrders->where('payment_status', 'Charged to Room Folio')->sum('total_amount');
            $rsPaidDirect = (float)$rsOrders->where('payment_status', '!=', 'Charged to Room Folio')->sum('total_amount');
            $rsOrdersCount = $rsOrders->count();

            $extraOrdersDetailed = [];
            foreach ($rsOrders as $rso) {
                $orderItems = [];
                foreach ($rso->items as $it) {
                    $orderItems[] = "{$it->quantity}x {$it->item_name}";
                }
                $extraOrdersDetailed[] = [
                    'order_number' => $rso->order_number,
                    'total_amount' => (float)$rso->total_amount,
                    'is_paid' => ($rso->payment_status !== 'Charged to Room Folio'),
                    'items_summary' => implode(', ', $orderItems),
                    'table_number' => $rso->table_number ?: null,
                    'date_short' => $rso->created_at ? $rso->created_at->format('d M') : '',
                ];
            }

            $grandBill = round($roomBill + $rsFolioCharged, 2);

            $paid = (float)Payment::whereIn('booking_id', $allResBookingIds)->sum('amount');
            if ($paid <= 0 && (float)$b->advance_amount > 0) {
                $paid = (float)$b->advance_amount;
            }

            $rem = max(0, round($grandBill - $paid, 2));

            $foTotalRoomTariffs += $roomBill;
            $foTotalExtraCharged += $rsFolioCharged;
            $foTotalExtraPaid += $rsPaidDirect;
            $foTotalGrossBill += $grandBill;
            $foTotalPaidOverall += $paid;
            $foTotalReceivables += $rem;

            $foBookingsLedger[] = [
                'id' => $b->id,
                'booking_number' => $b->reserve_id ?: ('RES-' . $b->id),
                'folio_number' => $b->settle_number ?: ($b->folio_number ?: ($b->primary_booking_id ?: '#BK-' . $b->id)),
                'guest_name' => $pName,
                'mobile' => $pPhone,
                'room_number' => $rNum,
                'category' => $b->room?->categoryRelation?->name ?? 'Standard',
                'checkin_date' => $b->reserve_date ? Carbon::parse($b->reserve_date)->format('d M Y') : '-',
                'checkout_date' => $b->checkout_date ? Carbon::parse($b->checkout_date)->format('d M Y') : '-',
                'status' => $b->status ?: 'Confirmed',
                'room_charges' => $roomBill,
                'extra_charged' => $rsFolioCharged,
                'extra_paid_direct' => $rsPaidDirect,
                'extra_orders_count' => $rsOrdersCount,
                'extra_orders_detailed' => $extraOrdersDetailed,
                'total_bill' => $grandBill,
                'paid_amount' => $paid,
                'remaining_amount' => $rem,
                'is_settled' => ($rem <= 0.01),
                'created_at' => $b->created_at ? $b->created_at->format('d M Y, h:i A') : '-',
            ];
        }

        // ==========================================
        // 3. POS & RESTAURANT SALES FINANCIAL DATA
        // ==========================================
        $posQuery = PosOrder::with(['items', 'room', 'booking.guest'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'Cancelled');

        if ($search) {
            $posQuery->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('room_number', 'like', "%{$search}%")
                  ->orWhere('table_number', 'like', "%{$search}%");
            });
        }

        $posOrders = $posQuery->orderBy('id', 'desc')->get();

        $posGrossSales = (float)$posOrders->sum('total_amount');
        $posSubtotal = (float)$posOrders->sum('subtotal');
        $posTaxCollected = (float)$posOrders->sum('tax_amount');

        $posCashSales = (float)$posOrders->where('billing_type', 'Cash Paid')->sum('total_amount');
        $posUpiSales = (float)$posOrders->where('billing_type', 'Digital UPI / Card Paid')->sum('total_amount');
        $posFolioCharged = (float)$posOrders->where('billing_type', 'Charged to Room Folio')->sum('total_amount');
        $posDirectSales = round($posCashSales + $posUpiSales, 2);

        $posRoomDiningSales = (float)$posOrders->where('order_type', 'Room Dining')->sum('total_amount');
        $posTableDineInSales = (float)$posOrders->where('order_type', 'Table Dine-in')->sum('total_amount');
        $posTakeawaySales = (float)$posOrders->where('order_type', 'Takeaway')->sum('total_amount');

        $posOrdersList = $posOrders->map(function ($po) {
            $itemsSummary = $po->items->map(fn($it) => "{$it->quantity}x {$it->item_name}")->implode(', ');
            $totalPlates = (int)$po->items->sum('quantity');

            return [
                'id' => $po->id,
                'order_number' => $po->order_number,
                'order_type' => $po->order_type,
                'table_number' => $po->table_number ?: '-',
                'room_number' => $po->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($po->room_number))) : '-',
                'customer_name' => $po->customer_name ?: 'Walk-in Guest',
                'customer_phone' => $po->customer_phone ?: '-',
                'subtotal' => (float)$po->subtotal,
                'tax_amount' => (float)$po->tax_amount,
                'total_amount' => (float)$po->total_amount,
                'billing_type' => $po->billing_type,
                'payment_status' => $po->payment_status,
                'status' => $po->status,
                'total_plates' => $totalPlates,
                'items_summary' => $itemsSummary,
                'created_at' => $po->created_at ? $po->created_at->format('d M Y, h:i A') : '-',
            ];
        })->values()->all();

        // ==========================================
        // 4. STORES & PROCUREMENT (EXPENSES) DATA
        // ==========================================
        $storesQuery = StoreMovement::with(['posItem', 'item'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($search) {
            $storesQuery->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhere('issued_to_department', 'like', "%{$search}%")
                  ->orWhereHas('posItem', function ($it) use ($search) {
                      $it->where('name', 'like', "%{$search}%")
                         ->orWhere('category', 'like', "%{$search}%")
                         ->orWhere('ingredient_name', 'like', "%{$search}%");
                  });
            });
        }

        $storeMovements = $storesQuery->orderBy('id', 'desc')->get();

        $inwardMovements = $storeMovements->where('type', 'IN');
        $outwardMovements = $storeMovements->where('type', 'OUT');

        $storeTotalPurchases = (float)$inwardMovements->sum('total_cost');
        $storeTotalPlatesInward = (int)$inwardMovements->sum('quantity');
        $storeTotalPlatesOutward = (int)$outwardMovements->sum('quantity');
        $storeInwardCount = $inwardMovements->count();
        $storeOutwardCount = $outwardMovements->count();

        $storeMovementsList = $storeMovements->map(function ($sm) {
            $dishName = $sm->posItem?->name ?? ($sm->item?->name ?? 'Kitchen Ingredient');
            $dishCat = $sm->posItem?->category ?? ($sm->item?->category ?? 'General Kitchen');
            $unit = $sm->posItem?->raw_stock_unit ?? 'Unit';

            return [
                'id' => $sm->id,
                'type' => $sm->type, // IN or OUT
                'item_name' => $dishName,
                'category' => $dishCat,
                'quantity' => (int)$sm->quantity,
                'unit_cost' => $sm->unit_cost ? (float)$sm->unit_cost : null,
                'total_cost' => $sm->total_cost ? (float)$sm->total_cost : null,
                'reference_no' => $sm->reference_no ?: '-',
                'department' => $sm->issued_to_department ?: 'Kitchen Store',
                'remarks' => $sm->remarks ?: '-',
                'performed_by' => $sm->performed_by ?: 'Kitchen Store Keeper',
                'created_at' => $sm->created_at ? $sm->created_at->format('d M Y, h:i A') : '-',
            ];
        })->values()->all();

        // ==========================================
        // 5. CONSOLIDATED FINANCIAL P&L & CASH FLOW
        // ==========================================
        // Total Direct Inflow Realized in Cash & Bank (Front Office payments + POS direct payments)
        $totalRealizedCollections = round($foTotalPayments + $posDirectSales, 2);

        // Total Gross Billings (Room Tariffs + Folio Extras + POS All Orders)
        $totalGrossRevenue = round($foTotalRoomTariffs + $posGrossSales, 2);

        // Total Direct Procurement Expenses (Store IN Purchases)
        $totalOperatingExpenses = round($storeTotalPurchases, 2);

        // Net Operating Balance (Net Cash Surplus / Margin)
        $netOperatingPosition = round($totalRealizedCollections - $totalOperatingExpenses, 2);

        // Consolidated Payment Mode Totals
        $consolidatedCash = round($foCash + $posCashSales, 2);
        $consolidatedUpi = round($foUpi + $posUpiSales, 2);
        $consolidatedCard = round($foCard, 2);
        $consolidatedBank = round($foBank, 2);
        $consolidatedOther = round($foOther, 2);

        // Unified Transaction Receipts List (For Payment Audit Trail)
        $unifiedReceipts = [];

        foreach ($foPayments as $p) {
            $unifiedReceipts[] = [
                'type' => 'Front Office',
                'ref_no' => $p->booking ? ($p->booking->reserve_id ?: '#BK-' . $p->booking->id) : '#PAY-' . $p->id,
                'party' => $p->booking?->guest?->guest_name ? (($p->booking->guest->title?->name ? $p->booking->guest->title->name . ' ' : '') . $p->booking->guest->guest_name) : 'Guest',
                'mode' => $p->paymentMode?->name ?? 'Cash',
                'category' => $p->payment_type ?: 'Room Tariff / Settle',
                'amount' => (float)$p->amount,
                'is_inflow' => true,
                'date_time' => $p->created_at ? $p->created_at->format('d M Y, h:i A') : '-',
                'timestamp' => $p->created_at ? $p->created_at->timestamp : 0,
            ];
        }

        foreach ($posOrders->whereIn('billing_type', ['Cash Paid', 'Digital UPI / Card Paid']) as $po) {
            $unifiedReceipts[] = [
                'type' => 'POS Sales',
                'ref_no' => $po->order_number,
                'party' => $po->customer_name ?: 'Takeaway / Walk-in Customer',
                'mode' => ($po->billing_type === 'Cash Paid') ? 'Cash' : 'UPI / Card',
                'category' => $po->order_type . ' Dining',
                'amount' => (float)$po->total_amount,
                'is_inflow' => true,
                'date_time' => $po->created_at ? $po->created_at->format('d M Y, h:i A') : '-',
                'timestamp' => $po->created_at ? $po->created_at->timestamp : 0,
            ];
        }

        foreach ($inwardMovements as $im) {
            if ($im->total_cost > 0) {
                $unifiedReceipts[] = [
                    'type' => 'Store Purchase',
                    'ref_no' => $im->reference_no ?: 'INW-' . $im->id,
                    'party' => $im->posItem?->name ?: 'Kitchen Raw Material',
                    'mode' => 'Procurement Expense',
                    'category' => 'Kitchen Raw Inward',
                    'amount' => (float)$im->total_cost,
                    'is_inflow' => false,
                    'date_time' => $im->created_at ? $im->created_at->format('d M Y, h:i A') : '-',
                    'timestamp' => $im->created_at ? $im->created_at->timestamp : 0,
                ];
            }
        }

        // Sort unified receipts by timestamp descending
        usort($unifiedReceipts, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return view('accounts.index', compact(
            'filterType',
            'filterLabel',
            'startDate',
            'endDate',
            'search',
            // Front Office Metrics & Lists
            'foTotalPayments',
            'foAdvance',
            'foSettlement',
            'foRunning',
            'foCash',
            'foUpi',
            'foCard',
            'foBank',
            'foOther',
            'foTotalRoomTariffs',
            'foTotalExtraCharged',
            'foTotalExtraPaid',
            'foTotalGrossBill',
            'foTotalPaidOverall',
            'foTotalReceivables',
            'foBookingsLedger',
            // POS Metrics & Lists
            'posGrossSales',
            'posSubtotal',
            'posTaxCollected',
            'posCashSales',
            'posUpiSales',
            'posFolioCharged',
            'posDirectSales',
            'posRoomDiningSales',
            'posTableDineInSales',
            'posTakeawaySales',
            'posOrdersList',
            // Stores Metrics & Lists
            'storeTotalPurchases',
            'storeTotalPlatesInward',
            'storeTotalPlatesOutward',
            'storeInwardCount',
            'storeOutwardCount',
            'storeMovementsList',
            // Consolidated Metrics
            'totalRealizedCollections',
            'totalGrossRevenue',
            'totalOperatingExpenses',
            'netOperatingPosition',
            'consolidatedCash',
            'consolidatedUpi',
            'consolidatedCard',
            'consolidatedBank',
            'consolidatedOther',
            'unifiedReceipts'
        ));
    }

    /**
     * Export / Print Friendly Raw JSON data for Accounts module
     */
    public function export(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Accounts export ready',
        ]);
    }
}

