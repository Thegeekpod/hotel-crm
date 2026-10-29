@extends('frontoffice.layouts.app')

@section('title', 'Night Audit - Hotel Sagar Sonnet CRM / PMS')


@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">

  <!-- 1. Header Card -->
  <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2.5 flex-wrap">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
          <i class="fa-solid fa-moon text-indigo-600"></i>
          Night Audit
        </h2>
        @if($existingAudit)
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
            <i class="fa-solid fa-lock text-[10px]"></i> Audit Completed
          </span>
        @elseif($isToday)
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
            <i class="fa-solid fa-clock text-[10px]"></i> Today (Pending)
          </span>
        @else
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
            <i class="fa-solid fa-clock-rotate-left text-[10px]"></i> Past Date
          </span>
        @endif
      </div>
      <div class="text-xs text-slate-500 mt-1">
        Audit Date: <strong class="text-slate-800 font-semibold">{{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}</strong>
        @if($existingAudit)
          • Completed by <strong class="text-slate-800 font-semibold">{{ $existingAudit->auditor_name ?: ($existingAudit->auditor?->name ?? 'Auditor') }}</strong> at {{ $existingAudit->completed_at ? $existingAudit->completed_at->format('h:i A') : 'N/A' }}
        @elseif(!$isToday)
          • <span class="text-slate-400">Historical view (Read-only)</span>
        @endif
      </div>
    </div>

    <!-- Action Controls -->
    <div class="flex items-center gap-2.5 flex-wrap">
      <div class="inline-flex items-center bg-slate-50 border border-slate-300 rounded-xl p-0.5 shadow-2xs">
        <a href="{{ route('frontoffice.nightaudit', ['date' => $prevDate]) }}" class="px-2.5 py-1.5 text-slate-600 hover:text-slate-900 transition-colors" title="Previous Day">
          <i class="fa-solid fa-chevron-left text-xs"></i>
        </a>
        <input type="date" 
               class="border-none bg-transparent text-xs font-mono font-bold text-slate-800 focus:outline-none px-2 py-1" 
               value="{{ $selectedDate }}" 
               max="{{ $today }}"
               onchange="window.location.href='{{ route('frontoffice.nightaudit') }}?date=' + this.value"
               title="Select Audit Date">
        <a href="{{ route('frontoffice.nightaudit', ['date' => $nextDate]) }}" 
           class="px-2.5 py-1.5 text-slate-600 hover:text-slate-900 transition-colors {{ $selectedDate >= $today ? 'opacity-40 pointer-events-none' : '' }}" 
           title="Next Day">
          <i class="fa-solid fa-chevron-right text-xs"></i>
        </a>
      </div>

      @if($selectedDate !== $today)
        <a href="{{ route('frontoffice.nightaudit') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all">
          <i class="fa-solid fa-rotate-left text-[11px]"></i> Today
        </a>
      @endif

      <button type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="openPrintReportModal()">
        <i class="fa-solid fa-print text-[11px]"></i> Print
      </button>

      @if($isToday)
        @if($existingAudit)
          <button type="button" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-700 text-xs font-bold shadow-2xs cursor-default" title="Audit is completed at {{ $existingAudit->completed_at ? $existingAudit->completed_at->format('h:i A') : '' }}">
            <i class="fa-solid fa-circle-check text-[11px]"></i> Completed
          </button>
          <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer" onclick="openCompleteAuditModal()" title="Recalculate and update today's audit with latest transactions">
            <i class="fa-solid fa-rotate text-[11px]"></i> Re-Run Audit
          </button>
        @else
          <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer" onclick="openCompleteAuditModal()">
            <i class="fa-solid fa-circle-check text-[11px]"></i> Complete Audit
          </button>
        @endif
      @else
        <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-600 text-xs font-semibold cursor-default" title="Night audit can only be performed for today. Past dates are view-only.">
          <i class="fa-solid fa-eye text-[11px]"></i> View-Only Mode
        </span>
      @endif
    </div>
  </div>

  <!-- 2. Clean 4 KPI Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Total Amount -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-receipt"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Total Amount</h4>
        <div class="text-xl font-bold font-mono text-emerald-600 mt-0.5">₹ {{ number_format($totalBookingsBill, 2) }}</div>
        <div class="text-[11px] text-slate-500 truncate mt-0.5">Total Collections: ₹ {{ number_format($totalPayments, 2) }}</div>
      </div>
    </div>

    <!-- Room Occupancy -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-bed"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Room Occupancy</h4>
        <div class="text-xl font-bold font-mono text-indigo-600 mt-0.5">{{ $occupancyRate }}%</div>
        <div class="text-[11px] text-slate-500 truncate mt-0.5">Occ: {{ $occupiedRoomsCount }} • Vac: {{ $vacantRoomsCount }} • Tot: {{ $totalRoomsCount }}</div>
      </div>
    </div>

    <!-- Unsettled Balance -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-clock-rotate-left"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Unsettled Balance</h4>
        <div class="text-xl font-bold font-mono text-rose-600 mt-0.5">₹ {{ number_format($totalUnsettledAmount, 2) }}</div>
        <div class="text-[11px] text-slate-500 truncate mt-0.5"><strong class="text-rose-600 font-semibold">{{ $unsettledBookingsCount }}</strong> unsettled booking(s)</div>
      </div>
    </div>

    <!-- Settled Balance -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-200/80 text-purple-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-circle-check"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Settled Balance</h4>
        <div class="text-xl font-bold font-mono text-purple-600 mt-0.5">₹ {{ number_format($totalSettledAmount, 2) }}</div>
        <div class="text-[11px] text-slate-500 truncate mt-0.5"><strong class="text-emerald-600 font-semibold">{{ $settledBookingsCount }}</strong> settled booking(s)</div>
      </div>
    </div>
  </div>

  <!-- 3. Navigation Tabs -->
  <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 overflow-x-auto scrollbar-none">
    <button type="button" class="audit-nav-tab active inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-slate-900 text-white shadow-sm cursor-pointer" onclick="switchAuditTab('tab-payments', this)">
      <i class="fa-solid fa-receipt"></i> Today Payments ({{ count($todayAllBookings) }})
    </button>
    <button type="button" class="audit-nav-tab inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer" onclick="switchAuditTab('tab-rooms', this)">
      <i class="fa-solid fa-bed"></i> Occupied Rooms ({{ $occupiedRoomsCount }})
    </button>
  </div>

    <!-- TAB 1: TODAY PAYMENTS (Booking Ledgers & Settlements Grouped by Booking Number) -->
    <div id="tab-payments" class="audit-tab-pane space-y-4">
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200/70 text-xs">
            <thead>
              <tr class="bg-slate-50/75 border-b border-slate-200/70">
                <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider w-12">SL</th>
                <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider min-w-[140px]">Booking Number</th>
                <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider min-w-[140px]">Room(s) &amp; Floor</th>
                <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider min-w-[170px]">Primary Guest &amp; Occupants</th>
                <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider min-w-[125px]">Settle / Folio #</th>
                <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider min-w-[130px]">Stay Dates</th>
                <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider min-w-[95px]">Status</th>
                <th class="py-3 px-3.5 text-right font-bold text-slate-500 uppercase tracking-wider min-w-[100px]">Room Bill</th>
                <th class="py-3 px-3.5 text-right font-bold text-slate-500 uppercase tracking-wider min-w-[110px]">Extra Services</th>
                <th class="py-3 px-3.5 text-right font-bold text-slate-500 uppercase tracking-wider min-w-[110px]">Total Bill</th>
                <th class="py-3 px-3.5 text-right font-bold text-slate-500 uppercase tracking-wider min-w-[100px]">Paid</th>
                <th class="py-3 px-3.5 text-right font-bold text-slate-500 uppercase tracking-wider min-w-[110px]">Remaining</th>
                <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider min-w-[105px]">Settlement</th>
              </tr>
            </thead>
            <tbody id="guests-tbody" class="divide-y divide-slate-100 bg-white">
              @forelse($bookingLedgers as $idx => $b)
                <tr class="guest-row hover:bg-slate-50/80 transition-colors">
                  <td class="py-3 px-3.5 text-center font-bold text-slate-500 font-mono text-[11px]">
                    {{ $idx + 1 }}
                  </td>
                  <!-- Booking Number -->
                  <td class="py-3 px-3.5 whitespace-nowrap">
                    <div class="font-bold text-indigo-600 font-mono text-xs inline-flex items-center gap-1.5">
                      <i class="fa-solid fa-receipt text-[11px] text-indigo-500"></i>
                      <span>{{ $b['booking_number'] }}</span>
                    </div>
                  </td>
                  <!-- Room(s) & Floor -->
                  <td class="py-3 px-3.5">
                    <div class="flex flex-wrap gap-1 items-center">
                      @foreach($b['rooms'] as $rm)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold font-mono text-[11px]" title="{{ $rm['category'] }}">
                          <i class="fa-solid fa-door-open text-[9px]"></i> {{ $rm['room_number'] }} <span class="text-[9px] opacity-75">(Fl {{ $rm['floor'] }})</span>
                        </span>
                      @endforeach
                      @if(!empty($b['transfers']))
                        @foreach($b['transfers'] as $tf)
                          <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold text-amber-800 bg-amber-50 border border-amber-200" title="Room swiped: R{{ $tf['from'] }} to R{{ $tf['to'] }} on {{ $tf['date'] }}">
                            <i class="fa-solid fa-repeat text-[8px]"></i> R{{ $tf['from'] }}&rarr;R{{ $tf['to'] }}
                          </span>
                        @endforeach
                      @endif
                    </div>
                  </td>
                  <!-- Primary Guest & Occupants -->
                  <td class="py-3 px-3.5">
                    <div class="font-bold text-slate-900 text-xs">
                      {{ $b['primary_guest_name'] }}
                    </div>
                    <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                      <span class="font-mono"><i class="fa-solid fa-phone text-[8px] text-emerald-600"></i> {{ $b['primary_mobile'] }}</span>
                      @if($b['total_occupants'] > 1)
                        <span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded font-bold text-[9px]" title="{{ implode(', ', $b['occupants_names']) }}">
                          +{{ $b['total_occupants'] - 1 }} guest(s)
                        </span>
                      @endif
                    </div>
                  </td>
                  <!-- Settle / Folio # -->
                  <td class="py-3 px-3.5 font-mono text-xs text-indigo-600 font-bold whitespace-nowrap">
                    {{ $b['settle_number'] }}
                  </td>
                  <!-- Stay Dates -->
                  <td class="py-3 px-3.5 text-xs text-slate-600 whitespace-nowrap">
                    <div><i class="fa-regular fa-calendar-check text-[9px] text-indigo-500"></i> {{ $b['checkin_date'] }}</div>
                    <div class="text-[10px] text-slate-400 mt-0.5"><i class="fa-regular fa-calendar-xmark text-[9px] text-rose-500"></i> {{ $b['checkout_date'] }}</div>
                  </td>
                  <!-- Stay Status -->
                  <td class="py-3 px-3.5 text-center whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                      {{ $b['stay_status'] }}
                    </span>
                  </td>
                  <!-- Room Bill -->
                  <td class="py-3 px-3.5 text-right font-bold font-mono text-xs text-slate-700 whitespace-nowrap">
                    ₹ {{ number_format($b['room_charges'], 2) }}
                  </td>
                  <!-- Extra Services -->
                  <td class="py-3 px-3.5 text-right font-mono text-xs whitespace-nowrap">
                    @if(($b['extra_orders_count'] ?? 0) > 0)
                      @if($b['extra_service_charges'] > 0)
                        <div class="font-bold text-amber-600">
                          + ₹ {{ number_format($b['extra_service_charges'], 2) }}
                        </div>
                      @endif
                      @if(($b['extra_paid_charges'] ?? 0) > 0)
                        <div class="font-bold text-emerald-700 text-[11px]">
                          <i class="fa-solid fa-circle-check"></i> ₹ {{ number_format($b['extra_paid_charges'], 2) }} <span class="text-[9px] opacity-75">(Paid)</span>
                        </div>
                      @endif
                      <div class="text-[10px] text-amber-700 font-bold mt-0.5" title="{{ implode(', ', $b['extra_items_list'] ?? []) }}">
                        <i class="fa-solid fa-bell-concierge text-[8px]"></i> {{ $b['extra_orders_count'] }} order(s)
                      </div>
                      @if(!empty($b['extra_orders_detailed']) && count($b['extra_orders_detailed']) > 0)
                        <div class="flex flex-col gap-1 mt-1 items-end">
                          @foreach($b['extra_orders_detailed'] as $eOrd)
                            @if(!empty($eOrd['is_paid']))
                              <div class="text-[9px] text-emerald-800 bg-emerald-50 border border-emerald-200 rounded px-1.5 py-0.5 inline-flex items-center gap-1" title="{{ $eOrd['order_number'] }}: {{ $eOrd['items_summary'] }} (Paid Direct)">
                                <span class="opacity-75 text-[8px]"><i class="fa-solid fa-check text-[7.5px]"></i> {{ $eOrd['date_short'] }}:</span>
                                <span class="font-bold">₹{{ number_format($eOrd['total_amount'], 2) }} (PAID)</span>
                                @if(!empty($eOrd['table_number']))
                                  <span class="text-[8px] bg-emerald-100 text-emerald-900 px-1 rounded font-bold"><i class="fa-solid fa-chair text-[7px]"></i> {{ $eOrd['table_number'] }}</span>
                                @endif
                              </div>
                            @else
                              <div class="text-[9px] text-amber-900 bg-amber-50 border border-amber-200 rounded px-1.5 py-0.5 inline-flex items-center gap-1" title="{{ $eOrd['order_number'] }}: {{ $eOrd['items_summary'] }} (Charged to Folio)">
                                <span class="opacity-75 text-[8px]"><i class="fa-regular fa-clock text-[7.5px]"></i> {{ $eOrd['date_short'] }}:</span>
                                <span class="font-bold">+ ₹{{ number_format($eOrd['total_amount'], 2) }}</span>
                                @if(!empty($eOrd['table_number']))
                                  <span class="text-[8px] bg-amber-100 text-amber-900 px-1 rounded font-bold"><i class="fa-solid fa-chair text-[7px]"></i> {{ $eOrd['table_number'] }}</span>
                                @endif
                              </div>
                            @endif
                          @endforeach
                        </div>
                      @endif
                    @else
                      <span class="text-slate-400 text-xs">₹ 0.00</span>
                    @endif
                  </td>
                  <!-- Total Bill -->
                  <td class="py-3 px-3.5 text-right font-extrabold font-mono text-xs text-slate-900 whitespace-nowrap">
                    ₹ {{ number_format($b['grand_total_bill'], 2) }}
                  </td>
                  <!-- Paid -->
                  <td class="py-3 px-3.5 text-right font-bold font-mono text-xs text-emerald-600 whitespace-nowrap">
                    ₹ {{ number_format($b['total_paid'] ?? $b['paid_amount'] ?? 0, 2) }}
                  </td>
                  <!-- Remaining -->
                  @php
                    $remBal = $b['remaining_balance'] ?? $b['remaining_amount'] ?? 0;
                  @endphp
                  <td class="py-3 px-3.5 text-right font-bold font-mono text-xs whitespace-nowrap {{ $remBal > 0.01 ? 'text-rose-600' : 'text-slate-500' }}">
                    <div>₹ {{ number_format($remBal, 2) }}</div>
                    @if($b['extra_service_charges'] > 0 && $remBal > 0.01)
                      <div class="text-[9px] font-bold text-amber-700 mt-0.5">
                        (incl. +₹{{ number_format($b['extra_service_charges'], 2) }} serv.)
                      </div>
                    @endif
                  </td>
                  <!-- Settlement Status -->
                  <td class="py-3 px-3.5 text-center whitespace-nowrap">
                    @if(!empty($b['is_settled']))
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" title="Payment cleared in full">
                        <i class="fa-solid fa-circle-check text-[10px]"></i> Settled
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Remaining balance of ₹ {{ number_format($remBal, 2) }}">
                        <i class="fa-solid fa-clock text-[10px]"></i> Unsettled
                      </span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="13" class="py-12 px-4 text-center text-slate-500">
                    <p class="font-medium text-slate-600">No bookings or payments recorded for this date.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
            @if(count($bookingLedgers) > 0)
              <tfoot class="bg-slate-50/80 font-bold border-t border-slate-200 text-xs">
                <tr>
                  <td colspan="7" class="py-3 px-3.5 text-right text-[11px] text-slate-500 uppercase tracking-wider">
                    Total ({{ count($bookingLedgers) }} Bookings):
                  </td>
                  <td class="py-3 px-3.5 text-right font-mono text-slate-800 whitespace-nowrap">
                    ₹ {{ number_format($totalRoomChargesAll, 2) }}
                  </td>
                  <td class="py-3 px-3.5 text-right font-mono text-amber-600 whitespace-nowrap">
                    + ₹ {{ number_format($totalExtraChargesAll, 2) }}
                  </td>
                  <td class="py-3 px-3.5 text-right font-mono text-slate-900 font-extrabold whitespace-nowrap">
                    ₹ {{ number_format($totalGrandBillAll, 2) }}
                  </td>
                  <td class="py-3 px-3.5 text-right font-mono text-emerald-600 whitespace-nowrap">
                    ₹ {{ number_format($totalPaidAll, 2) }}
                  </td>
                  <td class="py-3 px-3.5 text-right font-mono whitespace-nowrap {{ $totalRemainingAll > 0.01 ? 'text-rose-600' : 'text-slate-500' }}">
                    ₹ {{ number_format($totalRemainingAll, 2) }}
                  </td>
                  <td class="py-3 px-3.5 text-center text-[11px] font-bold whitespace-nowrap {{ $unsettledBookingsCount > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                    {{ $unsettledBookingsCount }} Unsettled
                  </td>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: TODAY ROOMS (Complete Hotel Room Inventory & Occupancy + Housekeeping & Operational Histories) -->
    <div id="tab-rooms" class="audit-tab-pane space-y-4 hidden">
      <!-- Subtabs Navigation -->
      <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 overflow-x-auto scrollbar-none">
        <button type="button" class="audit-subtab-btn active inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all bg-slate-900 text-white shadow-sm cursor-pointer" onclick="switchRoomSubView('view-room-inventory', this)">
          <i class="fa-solid fa-bed"></i> Occupied Rooms
          <span class="subtab-count-badge inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white">{{ $occupiedRoomsCount }}</span>
        </button>
        <button type="button" class="audit-subtab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer" onclick="switchRoomSubView('view-hk-history', this)">
          <i class="fa-solid fa-broom"></i> Today's Housekeeping History
          <span class="subtab-count-badge inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ count($todayHousekeepingHistories) }}</span>
        </button>
        <button type="button" class="audit-subtab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer" onclick="switchRoomSubView('view-op-history', this)">
          <i class="fa-solid fa-sliders"></i> Today's Operational History
          <span class="subtab-count-badge inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">{{ count($todayOperationalHistories) }}</span>
        </button>
      </div>

      <!-- SUBVIEW 1: OCCUPIED ROOMS -->
      <div id="view-room-inventory" class="room-subview space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200/70 text-xs">
              <thead>
                <tr class="bg-slate-50/75 border-b border-slate-200/70">
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider w-12">SL</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Room</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Category</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Floor</th>
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider">Occupancy</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Guest &amp; Settle #</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Check-in</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Check-out</th>
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider">Operational Status</th>
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider">Housekeeping</th>
                  <th class="py-3 px-3.5 text-right font-bold text-slate-500 uppercase tracking-wider">Rate / Day</th>
                </tr>
              </thead>
              <tbody id="rooms-tbody" class="divide-y divide-slate-100 bg-white">
                @php
                  $displayOccupiedRooms = isset($occupiedRooms) ? $occupiedRooms : $allRooms->filter(function($rm) use ($activeBookingsByRoom, $activeMaintenanceRooms) {
                    return $activeBookingsByRoom->has($rm->id) && !in_array($rm->id, $activeMaintenanceRooms ?? []);
                  });
                @endphp
                @forelse($displayOccupiedRooms as $idx => $rm)
                  @php
                    $cleanNum = 'R' . preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number));
                    $isMaint = in_array($rm->id, $activeMaintenanceRooms ?? []);
                    $activeBooking = $activeBookingsByRoom->get($rm->id);
                    $isOccupied = ($activeBooking && !$isMaint);

                    // Housekeeping State
                    $latestHk = $rm->housekeepingHistories ? $rm->housekeepingHistories->sortByDesc('id')->first() : null;
                    $hkName = $latestHk?->housekeepingStatus?->name ?? ($latestHk?->housekeepingState?->name ?? 'Clean');
                    $hkRaw = strtolower($hkName);
                    $isDirty = str_contains($hkRaw, 'dirty') || str_contains($hkRaw, 'cleaning') || str_contains($hkRaw, 'touch up');

                    // Operational State
                    $latestOp = $rm->operationalHistories ? $rm->operationalHistories->sortByDesc('id')->first() : null;
                    $opName = $latestOp?->operationalStatus?->name ?? 'Active';
                    $isOpBlocked = (bool)preg_match('/block|blok|maintenance|order/i', $opName);

                    $n = $activeBooking ? max(1, (int)$activeBooking->total_nights) : 1;
                    $dayRate = $activeBooking ? (float)($activeBooking->room_rate ?: ($activeBooking->total_amount / $n)) : 0.00;

                    $filterType = $isMaint ? 'maintenance' : ($isOccupied ? 'occupied' : 'vacant');
                    $rmPart = preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number)) ?: '100';
                    $activeSettleNo = $activeBooking ? ($activeBooking->settle_number ?: ($activeBooking->folio_number ?: ($activeBooking->primary_booking_id ?: ('SET-' . $rmPart . '-' . $activeBooking->id)))) : '-';
                  @endphp
                  <tr class="room-row hover:bg-slate-50/80 transition-colors" data-type="{{ $filterType }}" data-dirty="{{ $isDirty ? '1' : '0' }}">
                    <td class="py-3 px-3.5 text-center font-bold text-slate-500 font-mono text-[11px]">
                      {{ $idx + 1 }}
                    </td>
                    <td class="py-3 px-3.5 whitespace-nowrap">
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold font-mono text-[11px]">
                        {{ $cleanNum }}
                      </span>
                    </td>
                    <td class="py-3 px-3.5 font-semibold text-slate-800">
                      {{ $rm->categoryRelation?->name ?? 'Standard' }}
                    </td>
                    <td class="py-3 px-3.5 text-slate-500">
                      Floor {{ $rm->floorRelation?->floor ?? '1' }}
                    </td>
                    <!-- Occupancy Status -->
                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                      @if($isMaint)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                          <i class="fa-solid fa-wrench text-[9px]"></i> Maintenance
                        </span>
                      @elseif($isOccupied)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                          <i class="fa-solid fa-door-closed text-[9px]"></i> Occupied
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <i class="fa-solid fa-door-open text-[9px]"></i> Vacant
                        </span>
                      @endif
                    </td>
                    <!-- Guest & Settle # -->
                    <td class="py-3 px-3.5">
                      @if($isOccupied && $activeBooking)
                        <div class="font-bold text-slate-900 leading-tight">
                          {{ ($activeBooking->guest?->title?->name ? $activeBooking->guest->title->name . ' ' : '') . ($activeBooking->guest?->guest_name ?? 'Guest') }}
                        </div>
                        <div class="text-[10px] text-indigo-600 font-mono font-bold mt-0.5">
                          {{ $activeSettleNo }}
                        </div>
                      @elseif($isMaint)
                        <span class="text-amber-700 font-semibold text-xs">Out of Service</span>
                      @else
                        <span class="text-slate-400 text-xs">Available</span>
                      @endif
                    </td>
                    <!-- Check-in -->
                    <td class="py-3 px-3.5 text-slate-600 whitespace-nowrap">
                      {{ ($isOccupied && $activeBooking && $activeBooking->reserve_date) ? \Carbon\Carbon::parse($activeBooking->reserve_date)->format('d M Y') : '-' }}
                    </td>
                    <!-- Check-out -->
                    <td class="py-3 px-3.5 text-slate-600 whitespace-nowrap">
                      {{ ($isOccupied && $activeBooking && $activeBooking->checkout_date) ? \Carbon\Carbon::parse($activeBooking->checkout_date)->format('d M Y') : '-' }}
                    </td>
                    <!-- Operational Status -->
                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                      @if($isOpBlocked)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                          <i class="fa-solid fa-ban text-[9px]"></i> {{ $opName }}
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <i class="fa-solid fa-circle-check text-[9px]"></i> {{ $opName }}
                        </span>
                      @endif
                    </td>
                    <!-- Housekeeping -->
                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                      @if($isDirty)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                          <i class="fa-solid fa-broom text-[9px]"></i> {{ $hkName }}
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <i class="fa-solid fa-check text-[9px]"></i> {{ $hkName }}
                        </span>
                      @endif
                    </td>
                    <!-- Daily Rate -->
                    <td class="py-3 px-3.5 text-right font-bold font-mono text-slate-800 whitespace-nowrap">
                      @if($dayRate > 0)
                        ₹ {{ number_format($dayRate, 2) }}
                      @else
                        <span class="text-slate-400 font-normal">-</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="11" class="py-12 px-4 text-center text-slate-500">
                      <i class="fa-solid fa-door-open text-3xl text-slate-300 mb-2 block"></i>
                      No rooms are currently occupied on this date ({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}).
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- SUBVIEW 2: TODAY'S HOUSEKEEPING HISTORIES -->
      <div id="view-hk-history" class="room-subview space-y-4 hidden">
        <div class="flex items-center justify-between flex-wrap gap-2">
          <div>
            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Today's Housekeeping History Logs</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Housekeeping states, cleaning events, and inspections recorded on this date.</p>
          </div>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
            {{ count($todayHousekeepingHistories) }} Record(s) Logged
          </span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200/70 text-xs">
              <thead>
                <tr class="bg-slate-50/75 border-b border-slate-200/70">
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider w-12">SL</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider w-32">Time / Date</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Room Number</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Category</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Floor</th>
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider">Housekeeping State</th>
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider">Task Status</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Start Time</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Completion Time</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($todayHousekeepingHistories as $hkIdx => $hk)
                  @php
                    $rmNum = $hk->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($hk->room->room_number))) : 'Room #' . $hk->room_id;
                    $stateName = $hk->housekeepingStatus?->name ?? 'Clean';
                    $stateRaw = strtolower($stateName);
                    $isDirtyState = str_contains($stateRaw, 'dirty') || str_contains($stateRaw, 'cleaning') || str_contains($stateRaw, 'touch up');
                  @endphp
                  <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3 px-3.5 text-center font-bold text-slate-500 font-mono text-[11px]">
                      {{ $hkIdx + 1 }}
                    </td>
                    <td class="py-3 px-3.5 font-mono text-slate-700 whitespace-nowrap">
                      {{ $hk->created_at ? $hk->created_at->format('h:i A') : '-' }}
                      <span class="block text-[10px] text-slate-400">{{ $hk->created_at ? $hk->created_at->format('d M Y') : '' }}</span>
                    </td>
                    <td class="py-3 px-3.5 whitespace-nowrap">
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold font-mono text-[11px]">
                        {{ $rmNum }}
                      </span>
                    </td>
                    <td class="py-3 px-3.5 font-semibold text-slate-800">
                      {{ $hk->room?->categoryRelation?->name ?? 'Standard' }}
                    </td>
                    <td class="py-3 px-3.5 text-slate-500">
                      Floor {{ $hk->room?->floorRelation?->floor ?? '1' }}
                    </td>
                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                      @if($isDirtyState)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                          <i class="fa-solid fa-broom text-[9px]"></i> {{ $stateName }}
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <i class="fa-solid fa-circle-check text-[9px]"></i> {{ $stateName }}
                        </span>
                      @endif
                    </td>
                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                      @if(strtolower($hk->status) === 'complete' || strtolower($hk->status) === 'completed')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <i class="fa-solid fa-check text-[9px]"></i> Complete
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                          <i class="fa-solid fa-clock text-[9px]"></i> {{ ucfirst($hk->status ?? 'Pending') }}
                        </span>
                      @endif
                    </td>
                    <td class="py-3 px-3.5 font-mono text-slate-600 whitespace-nowrap">
                      {{ $hk->start_time ? \Carbon\Carbon::parse($hk->start_time)->format('h:i A') : '-' }}
                    </td>
                    <td class="py-3 px-3.5 font-mono text-slate-600 whitespace-nowrap">
                      {{ $hk->completion_time ? \Carbon\Carbon::parse($hk->completion_time)->format('h:i A') : '-' }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="py-12 px-4 text-center text-slate-500">
                      <i class="fa-solid fa-broom text-3xl text-slate-300 mb-2 block"></i>
                      No housekeeping history logs recorded for this date ({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}).
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- SUBVIEW 3: TODAY'S OPERATIONAL HISTORIES -->
      <div id="view-op-history" class="room-subview space-y-4 hidden">
        <div class="flex items-center justify-between flex-wrap gap-2">
          <div>
            <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Today's Operational History Logs</h4>
            <p class="text-[11px] text-slate-500 mt-0.5">Operational status changes (Active, In-Service, Out of Order, Blocked) logged on this date.</p>
          </div>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
            {{ count($todayOperationalHistories) }} Record(s) Logged
          </span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200/70 text-xs">
              <thead>
                <tr class="bg-slate-50/75 border-b border-slate-200/70">
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider w-12">SL</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider w-32">Time / Date</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Room Number</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Category</th>
                  <th class="py-3 px-3.5 text-left font-bold text-slate-500 uppercase tracking-wider">Floor</th>
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider">Operational Status</th>
                  <th class="py-3 px-3.5 text-center font-bold text-slate-500 uppercase tracking-wider">Room Model State</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($todayOperationalHistories as $opIdx => $op)
                  @php
                    $rmNum = $op->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($op->room->room_number))) : 'Room #' . $op->room_id;
                    $opStatusName = $op->operationalStatus?->name ?? 'Active';
                    $isOpBlocked = (bool)preg_match('/block|blok|maintenance|order/i', $opStatusName);
                    $roomStatus = $op->room?->status ?? 'Active';
                  @endphp
                  <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3 px-3.5 text-center font-bold text-slate-500 font-mono text-[11px]">
                      {{ $opIdx + 1 }}
                    </td>
                    <td class="py-3 px-3.5 font-mono text-slate-700 whitespace-nowrap">
                      {{ $op->created_at ? $op->created_at->format('h:i A') : '-' }}
                      <span class="block text-[10px] text-slate-400">{{ $op->created_at ? $op->created_at->format('d M Y') : '' }}</span>
                    </td>
                    <td class="py-3 px-3.5 whitespace-nowrap">
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold font-mono text-[11px]">
                        {{ $rmNum }}
                      </span>
                    </td>
                    <td class="py-3 px-3.5 font-semibold text-slate-800">
                      {{ $op->room?->categoryRelation?->name ?? 'Standard' }}
                    </td>
                    <td class="py-3 px-3.5 text-slate-500">
                      Floor {{ $op->room?->floorRelation?->floor ?? '1' }}
                    </td>
                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                      @if($isOpBlocked)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                          <i class="fa-solid fa-ban text-[9px]"></i> {{ $opStatusName }}
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          <i class="fa-solid fa-circle-check text-[9px]"></i> {{ $opStatusName }}
                        </span>
                      @endif
                    </td>
                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                      @if(strtolower($roomStatus) === 'active')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                          Active
                        </span>
                      @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                          {{ $roomStatus }}
                        </span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="py-12 px-4 text-center text-slate-500">
                      <i class="fa-solid fa-sliders text-3xl text-slate-300 mb-2 block"></i>
                      No operational history changes recorded for this date ({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}).
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>



    @if($isToday)
    <!-- SIMPLE COMPLETE / RE-AUDIT MODAL -->
    <div class="modal-backdrop hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" id="modal-complete-audit">
      <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden my-8">
        <div class="bg-linear-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex items-center justify-between text-white">
          <h3 class="text-base font-bold flex items-center gap-2 text-white">
            <i class="fa-solid fa-rotate"></i> {{ $existingAudit ? 'Re-Run & Update Today\'s Night Audit' : 'Complete Today\'s Night Audit' }}
          </h3>
          <button type="button" onclick="closeCompleteAuditModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-colors text-xl leading-none cursor-pointer">&times;</button>
        </div>

        <div class="p-6 space-y-4">
          <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5 text-xs space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Audit Date:</span>
              <strong class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($today)->format('d M Y') }}</strong>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Total Collections:</span>
              <strong class="font-mono font-bold text-emerald-600">₹ {{ number_format($totalPayments, 2) }}</strong>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Occupancy:</span>
              <strong class="font-bold text-indigo-600">{{ $occupancyRate }}% ({{ $occupiedRoomsCount }} Rooms)</strong>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500">Unsettled Balance:</span>
              <strong class="font-mono font-bold text-rose-600">₹ {{ number_format($totalUnsettledAmount, 2) }}</strong>
            </div>
            <div class="flex items-center justify-between pt-1 border-t border-slate-200/70">
              <span class="text-slate-500 font-bold">Total Bill Amount:</span>
              <strong class="font-mono font-bold text-slate-900">₹ {{ number_format($totalBookingsBill, 2) }}</strong>
            </div>
          </div>

          <form id="form-complete-audit" onsubmit="submitNightAudit(event)" class="space-y-4">
            <input type="hidden" name="audit_date" value="{{ $today }}">

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Auditor Name:
              </label>
              <input type="text" 
                     name="auditor_name" 
                     class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" 
                     value="{{ auth()->user()?->name ?? 'Front Office Night Auditor' }}" 
                     required>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Closing Remarks:
              </label>
              <textarea name="remarks" 
                        rows="2" 
                        class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 resize-y" 
                        placeholder="Closing notes (optional)...">{{ $existingAudit?->remarks ?? '' }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
              <button type="button" onclick="closeCompleteAuditModal()" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer">
                Cancel
              </button>
              <button type="submit" id="btn-submit-audit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all cursor-pointer">
                <i class="fa-solid fa-check"></i> Finalize &amp; Save
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    @endif

<!-- PRINT REPORT CONTENT (FRONT OFFICE NIGHT AUDIT REPORT) -->
<div id="printable-night-audit-report" class="hidden print:block">
  <div class="w-full mx-auto font-sans p-0 text-slate-900 box-border">
    
    <!-- Header with Hotel Identity -->
    <div class="flex justify-between items-start border-b-2 border-indigo-700 pb-3 mb-3.5">
      <div>
        <h1 class="m-0 text-xl font-black text-indigo-950 uppercase tracking-wide">Hotel Sagar Sonnet</h1>
        <div class="text-[11px] text-slate-600 mt-1">Luxury Hospitality &amp; Premium Suites &bull; Front Office Night Audit</div>
        <div class="text-[10px] text-slate-500 mt-0.5">Station Road &bull; Front Desk Management System</div>
      </div>
      <div class="text-right">
        <div class="text-sm font-black text-indigo-700 uppercase tracking-wide">FRONT OFFICE NIGHT AUDIT REPORT</div>
        <div class="text-[11px] font-mono font-bold text-slate-900 mt-1">
          Audit Date: {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }} ({{ \Carbon\Carbon::parse($selectedDate)->format('l') }})
        </div>
        <div class="text-[10px] text-slate-500 mt-0.5">
          Auditor: <strong>{{ $existingAudit?->auditor_name ?? auth()->user()?->name ?? 'Front Office Night Auditor' }}</strong> &bull; Status: <strong class="text-emerald-700">{{ $existingAudit ? 'AUDIT COMPLETED' : 'LIVE AUDIT' }}</strong>
        </div>
      </div>
    </div>

    <!-- 4 Front Office KPI Summary Cards (Exactly matching Front Office screen) -->
    <div class="print-avoid-break grid grid-cols-4 gap-2.5 mb-4">
      <div class="border border-slate-300 border-l-4 border-l-emerald-500 rounded-lg p-2.5 bg-white">
        <div class="text-[9px] font-extrabold text-slate-600 uppercase">TOTAL AMOUNT</div>
        <div class="text-base font-black text-emerald-600 font-mono mt-0.5">₹ {{ number_format($totalPayments, 2) }}</div>
        <div class="text-[8.5px] text-slate-500 mt-0.5">Total Collections: ₹ {{ number_format($totalPayments, 2) }}</div>
      </div>
      <div class="border border-slate-300 border-l-4 border-l-indigo-600 rounded-lg p-2.5 bg-white">
        <div class="text-[9px] font-extrabold text-slate-600 uppercase">ROOM OCCUPANCY</div>
        <div class="text-base font-black text-indigo-700 font-mono mt-0.5">{{ number_format($occupancyRate, 1) }}%</div>
        <div class="text-[8.5px] text-slate-500 mt-0.5">Occupied: {{ $occupiedRoomsCount }} &bull; Vacant: {{ $vacantRoomsCount }} &bull; Total: {{ $totalRoomsCount }}</div>
      </div>
      <div class="border border-slate-300 border-l-4 border-l-rose-600 rounded-lg p-2.5 bg-white">
        <div class="text-[9px] font-extrabold text-slate-600 uppercase">UNSETTLED BALANCE</div>
        <div class="text-base font-black {{ $totalUnsettledAmount > 0.01 ? 'text-rose-700' : 'text-emerald-700' }} font-mono mt-0.5">₹ {{ number_format($totalUnsettledAmount, 2) }}</div>
        <div class="text-[8.5px] {{ $totalUnsettledAmount > 0.01 ? 'text-rose-700' : 'text-slate-500' }} mt-0.5">{{ $unsettledBookingsCount }} unsettled booking(s) pending</div>
      </div>
      <div class="border border-slate-300 border-l-4 border-l-purple-600 rounded-lg p-2.5 bg-white">
        <div class="text-[9px] font-extrabold text-slate-600 uppercase">SETTLED BALANCE</div>
        <div class="text-base font-black text-purple-700 font-mono mt-0.5">₹ {{ number_format(max(0, $totalBookingsBill - $totalUnsettledAmount), 2) }}</div>
        <div class="text-[8.5px] text-slate-500 mt-0.5">{{ $settledBookingsCount }} settled booking(s) cleared</div>
      </div>
    </div>

    <!-- Section 1: Today Payments (Booking Billings & Extra Services) -->
    <div class="mb-4">
      <div class="text-[11px] font-black uppercase text-indigo-950 border-b border-slate-300 pb-1 mb-1.5 flex justify-between items-center">
        <span>Today Bookings &amp; Payments ({{ count($bookingLedgers) }})</span>
        <span class="text-[9.5px] text-slate-500 normal-case">Reconciled Booking Accounts (₹ INR)</span>
      </div>
      <table class="print-table text-[8px] w-full border-collapse table-fixed">
        <thead>
          <tr>
            <th class="w-6 text-center">SL</th>
            <th class="w-18">BOOKING #</th>
            <th class="w-16">ROOM(S)</th>
            <th class="w-26">PRIMARY GUEST</th>
            <th class="w-16">SETTLE #</th>
            <th class="w-14">CHECK-IN</th>
            <th class="w-14">CHECK-OUT</th>
            <th class="w-12 text-center">STATUS</th>
            <th class="w-14 text-right">ROOM BILL</th>
            <th class="w-14 text-right">EXTRA SERV.</th>
            <th class="w-16 text-right">TOTAL BILL</th>
            <th class="w-14 text-right">PAID</th>
            <th class="w-14 text-right">REMAINING</th>
            <th class="w-14 text-center">SETTLE</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bookingLedgers as $idx => $b)
            @php
              $rem = (float)($b['remaining_balance'] ?? $b['remaining_amount'] ?? 0);
              $pd = (float)($b['total_paid'] ?? $b['paid_amount'] ?? 0);
              $isSet = !empty($b['is_settled']) || ($rem <= 0.01);
              $roomsStr = collect($b['rooms'] ?? [])->map(fn($r) => $r['room_number'] . ' (Fl ' . $r['floor'] . ')')->implode(', ');
              if (empty($roomsStr)) {
                $roomsStr = $b['room_number'] ?? '-';
              }
            @endphp
            <tr>
              <td class="text-center font-mono">{{ $idx + 1 }}</td>
              <td class="font-mono font-bold text-indigo-700 whitespace-nowrap truncate">{{ $b['booking_number'] ?? ('RES-' . ($b['booking_id'] ?? $b['id'])) }}</td>
              <td class="font-mono font-bold truncate whitespace-nowrap">{{ $roomsStr }}</td>
              <td class="font-bold truncate whitespace-nowrap">
                {{ $b['primary_guest_name'] ?? $b['guest_name'] ?? 'Guest' }}
                @if(($b['total_occupants'] ?? 1) > 1)
                  <span class="text-[7.5px] opacity-80 font-semibold">(+{{ ($b['total_occupants'] ?? 1) - 1 }})</span>
                @endif
              </td>
              <td class="font-mono font-bold text-[8px] text-teal-700 whitespace-nowrap truncate">{{ $b['settle_number'] ?? $b['folio_number'] ?? '-' }}</td>
              <td class="text-[8px] text-slate-600 whitespace-nowrap">{{ $b['checkin_date'] ?? '-' }}</td>
              <td class="text-[8px] text-slate-600 whitespace-nowrap">{{ $b['checkout_date'] ?? '-' }}</td>
              <td class="text-center text-[7.5px] whitespace-nowrap">{{ $b['stay_status'] ?? $b['status'] ?? 'Active' }}</td>
              <td class="text-right font-mono font-bold whitespace-nowrap">₹ {{ number_format($b['room_charges'] ?? 0, 2) }}</td>
              <td class="text-right font-mono font-bold {{ ($b['extra_service_charges'] ?? 0) > 0 ? 'text-amber-700' : 'text-slate-400' }} whitespace-nowrap">
                @if(($b['extra_service_charges'] ?? 0) > 0)
                  +₹ {{ number_format($b['extra_service_charges'], 2) }}
                @else
                  ₹ 0.00
                @endif
              </td>
              <td class="text-right font-mono font-extrabold whitespace-nowrap">₹ {{ number_format($b['grand_total_bill'] ?? $b['total_bill'] ?? 0, 2) }}</td>
              <td class="text-right font-mono text-emerald-700 font-bold whitespace-nowrap">₹ {{ number_format($pd, 2) }}</td>
              <td class="text-right font-mono font-extrabold {{ $rem > 0.01 ? 'text-rose-700' : 'text-emerald-700' }} whitespace-nowrap">₹ {{ number_format($rem, 2) }}</td>
              <td class="text-center text-[7.5px] font-extrabold {{ $isSet ? 'text-emerald-700' : 'text-rose-700' }} whitespace-nowrap">
                {{ $isSet ? 'SETTLED' : 'UNSETTLED' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="14" class="text-center py-3.5 text-slate-400">No booking records recorded for this date.</td>
            </tr>
          @endforelse
        </tbody>
        @if(count($bookingLedgers) > 0)
          <tfoot>
            <tr class="bg-slate-50 font-extrabold">
              <td colspan="8" class="text-right uppercase text-[8px] text-slate-500">TOTALS (ROOMS / EXTRA SERV / GRAND / PAID / REM):</td>
              <td class="text-right font-mono text-[8.5px]">₹ {{ number_format($totalRoomChargesAll, 2) }}</td>
              <td class="text-right font-mono text-[8.5px] text-amber-700">+₹ {{ number_format($totalExtraChargesAll, 2) }}</td>
              <td class="text-right font-mono text-[9px] font-extrabold">₹ {{ number_format($totalGrandBillAll, 2) }}</td>
              <td class="text-right font-mono text-[8.5px] text-emerald-700">₹ {{ number_format($totalPaidAll, 2) }}</td>
              <td class="text-right font-mono text-[8.5px] {{ $totalRemainingAll > 0.01 ? 'text-rose-700' : 'text-emerald-700' }}">₹ {{ number_format($totalRemainingAll, 2) }}</td>
              <td class="text-center text-[8px] {{ $unsettledBookingsCount > 0 ? 'text-rose-700' : 'text-emerald-700' }}">{{ $unsettledBookingsCount }} Due</td>
            </tr>
          </tfoot>
        @endif
      </table>
    </div>

    <!-- Section 2: Occupied Rooms (Summary & Tariffs) -->
    <div class="print-avoid-break mb-4">
      <div class="text-[11px] font-black uppercase text-indigo-950 border-b border-slate-300 pb-1 mb-1.5 flex justify-between items-center">
        <span>Occupied Rooms ({{ $occupiedRoomsCount }})</span>
        <span class="text-[9.5px] text-slate-500 normal-case">In-House Room Occupancy &amp; Rates</span>
      </div>
      <table class="print-table text-[8px] w-full border-collapse table-fixed">
        <thead>
          <tr>
            <th class="w-6 text-center">SL</th>
            <th class="w-11 text-center">ROOM</th>
            <th class="w-22">CATEGORY</th>
            <th class="w-14">FLOOR</th>
            <th class="w-32">GUEST NAME</th>
            <th class="w-18">SETTLE #</th>
            <th class="w-16">CHECK-IN</th>
            <th class="w-16">CHECK-OUT</th>
            <th class="w-14 text-center">STAY</th>
            <th class="w-22 text-center">HOUSEKEEPING</th>
            <th class="w-20 text-right">DAY TARIFF</th>
          </tr>
        </thead>
        <tbody>
          @php
            $printOccupiedList = isset($occupiedRooms) ? $occupiedRooms : $allRooms->filter(function($rm) use ($activeBookingsByRoom, $activeMaintenanceRooms) {
              return $activeBookingsByRoom->has($rm->id) && !in_array($rm->id, $activeMaintenanceRooms ?? []);
            });
          @endphp
          @forelse($printOccupiedList as $pIdx => $rm)
            @php
              $cleanNum = 'R' . preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number));
              $activeBooking = $activeBookingsByRoom->get($rm->id);
              $latestHk = $rm->housekeepingHistories ? $rm->housekeepingHistories->sortByDesc('id')->first() : null;
              $hkName = $latestHk?->housekeepingStatus?->name ?? 'Clean';
              $n = $activeBooking ? max(1, (int)$activeBooking->total_nights) : 1;
              $dayRate = $activeBooking ? (float)($activeBooking->room_rate ?: ($activeBooking->total_amount / $n)) : 0.00;
              $rmPart = preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number)) ?: '100';
              $activeSettleDisplay = $activeBooking ? ($activeBooking->settle_number ?: ($activeBooking->folio_number ?: ($activeBooking->primary_booking_id ?: ('SET-' . $rmPart . '-' . $activeBooking->id)))) : '-';
              $occGuest = $activeBooking ? (($activeBooking->guest?->title?->name ? $activeBooking->guest->title->name . ' ' : '') . ($activeBooking->guest?->guest_name ?? 'Guest')) : '-';
            @endphp
            <tr>
              <td class="text-center font-mono">{{ $pIdx + 1 }}</td>
              <td class="text-center font-bold font-mono text-indigo-700">{{ $cleanNum }}</td>
              <td class="truncate whitespace-nowrap">{{ $rm->categoryRelation?->name ?? 'Standard' }}</td>
              <td>Floor {{ $rm->floorRelation?->floor ?? '1' }}</td>
              <td class="font-bold truncate whitespace-nowrap">{{ $occGuest }}</td>
              <td class="font-mono font-bold text-[8px] text-indigo-700 break-all">{{ $activeSettleDisplay }}</td>
              <td class="text-[8px] text-slate-600 whitespace-nowrap">{{ $activeBooking?->reserve_date ? \Carbon\Carbon::parse($activeBooking->reserve_date)->format('d M Y') : '-' }}</td>
              <td class="text-[8px] text-slate-600 whitespace-nowrap">{{ $activeBooking?->checkout_date ? \Carbon\Carbon::parse($activeBooking->checkout_date)->format('d M Y') : '-' }}</td>
              <td class="text-center text-[8px]">{{ $activeBooking?->status ?? 'Active' }}</td>
              <td class="text-center font-bold text-[8px]">{{ $hkName }}</td>
              <td class="text-right font-mono font-bold whitespace-nowrap">₹ {{ number_format($dayRate, 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center py-3.5 text-slate-400">No occupied rooms recorded on this date.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Auditor Remarks & Sign-off Footer -->
    <div class="print-avoid-break mt-6 pt-2.5 border-t border-slate-300">
      @if($existingAudit && $existingAudit->remarks)
        <div class="text-[10px] text-slate-600 mb-6 bg-slate-50 p-2 border border-slate-200 rounded">
          <strong>Closing Remarks:</strong> {{ $existingAudit->remarks }}
        </div>
      @endif

      <div class="flex justify-between items-end mt-9">
        <div class="text-center w-56">
          <div class="border-b border-slate-900 h-7.5 mb-1.5"></div>
          <div class="text-[11px] font-black text-slate-900">Night Auditor / Front Desk</div>
          <div class="text-[9.5px] text-slate-500">{{ $existingAudit?->auditor_name ?? auth()->user()?->name ?? 'Front Office Auditor' }}</div>
        </div>
        <div class="text-center w-40 text-[9.5px] text-slate-500">
          Hotel Sagar Sonnet PMS<br>
          Verification Stamp
        </div>
        <div class="text-center w-56">
          <div class="border-b border-slate-900 h-7.5 mb-1.5"></div>
          <div class="text-[11px] font-black text-slate-900">Duty Manager / General Manager</div>
          <div class="text-[9.5px] text-slate-500">Signature &amp; Verification</div>
        </div>
      </div>
    </div>

  </div>
</div>

</div>
</div>
</main>
@endsection

@push('scripts')
<script>
  function switchAuditTab(tabId, el) {
    var panes = document.querySelectorAll('.audit-tab-pane');
    for (var i = 0; i < panes.length; i++) {
      panes[i].classList.add('hidden');
      panes[i].style.display = 'none';
    }
    var tabs = document.querySelectorAll('.audit-nav-tab');
    for (var j = 0; j < tabs.length; j++) {
      tabs[j].classList.remove('active', 'bg-slate-900', 'text-white', 'shadow-sm');
      tabs[j].classList.add('bg-white', 'border', 'border-slate-200/80', 'text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
    }
    
    var target = document.getElementById(tabId);
    if (target) {
      target.classList.remove('hidden');
      target.style.display = 'block';
    }
    if (el) {
      el.classList.add('active', 'bg-slate-900', 'text-white', 'shadow-sm');
      el.classList.remove('bg-white', 'border', 'border-slate-200/80', 'text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
    }
  }

  function switchRoomSubView(viewId, btn) {
    var container = document.getElementById('tab-rooms');
    if (!container) return;

    var btns = container.querySelectorAll('.audit-subtab-btn');
    btns.forEach(function(b) {
      b.classList.remove('active', 'bg-slate-900', 'text-white', 'shadow-sm');
      b.classList.add('bg-white', 'border', 'border-slate-200/80', 'text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
      var badge = b.querySelector('.subtab-count-badge');
      if (badge) {
        if (b.getAttribute('onclick').includes('room-inventory')) {
          badge.className = 'subtab-count-badge inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200';
        } else if (b.getAttribute('onclick').includes('hk-history')) {
          badge.className = 'subtab-count-badge inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
        } else {
          badge.className = 'subtab-count-badge inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200';
        }
      }
    });

    btn.classList.add('active', 'bg-slate-900', 'text-white', 'shadow-sm');
    btn.classList.remove('bg-white', 'border', 'border-slate-200/80', 'text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
    var activeBadge = btn.querySelector('.subtab-count-badge');
    if (activeBadge) {
      activeBadge.className = 'subtab-count-badge inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white';
    }

    var views = container.querySelectorAll('.room-subview');
    views.forEach(function(v) {
      v.classList.add('hidden');
      v.style.display = 'none';
    });

    var target = document.getElementById(viewId);
    if (target) {
      target.classList.remove('hidden');
      target.style.display = 'block';
    }
  }

  function openCompleteAuditModal() {
    var modal = document.getElementById('modal-complete-audit');
    if (modal) {
      modal.classList.remove('hidden');
      modal.style.display = 'flex';
    }
  }

  function closeCompleteAuditModal() {
    var modal = document.getElementById('modal-complete-audit');
    if (modal) {
      modal.classList.add('hidden');
      modal.style.display = 'none';
    }
  }

  function openPrintReportModal() {
    var report = document.getElementById('printable-night-audit-report');
    if (!report) return;
    var printArea = document.getElementById('print-area');
    if (printArea) {
      printArea.innerHTML = report.innerHTML;
      printArea.classList.remove('hidden');
      window.print();
      printArea.classList.add('hidden');
      printArea.innerHTML = '';
    } else {
      window.print();
    }
  }

  function submitNightAudit(e) {
    e.preventDefault();
    var btn = document.getElementById('btn-submit-audit');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
    }

    var form = document.getElementById('form-complete-audit');
    var formData = new FormData(form);
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    var token = tokenMeta ? tokenMeta.getAttribute('content') : '';

    fetch('{{ route("frontoffice.night-audit.complete") }}', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: formData
    })
    .then(function(res) {
      return res.json();
    })
    .then(function(data) {
      if (btn) btn.disabled = false;
      if (data.success) {
        closeCompleteAuditModal();
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Audit Saved!',
            text: data.message,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK'
          }).then(function() {
            window.location.reload();
          });
        } else {
          alert(data.message);
          window.location.reload();
        }
      } else {
        if (btn) btn.innerHTML = '<i class="fa-solid fa-check"></i> Finalize &amp; Save';
        alert(data.error || 'Failed to complete night audit.');
      }
    })
    .catch(function(err) {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Finalize &amp; Save';
      }
      console.error(err);
      alert('Network or server error while completing night audit.');
    });
  }

  document.addEventListener('click', function(e) {
    var modal = document.getElementById('modal-complete-audit');
    if (e.target === modal) {
      closeCompleteAuditModal();
    }
  });
</script>
@endpush
