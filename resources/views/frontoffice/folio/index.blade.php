@extends('frontoffice.layouts.app')

@section('title', 'Settlement Management & Master Ledger - Hotel Sagar Sonnet CRM / PMS')


@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">

    <!-- Clean Header Card (like guest CRM) -->
    <div class="folio-header-card">
      <div class="flex justify-between items-center flex-wrap gap-3">
        <div>
          <h2 class="m-0 text-lg font-extrabold text-slate-800 flex items-center gap-2.5 flex-wrap">
            <i class="fa-solid fa-file-invoice-dollar text-indigo-600"></i> 
            Settlement Management &amp; Master Ledger
            <span class="text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded-full">
              {{ $folios->total() }} Records
            </span>
            @if(!empty($fromDate) && !empty($toDate) && $fromDate === $toDate)
              <span class="text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5">
                <i class="fa-regular fa-calendar-check"></i> {{ $fromDate === $today ? "Today's Check-ins (" . \Carbon\Carbon::parse($fromDate)->format('d M Y') . ")" : "Check-in: " . \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
              </span>
            @elseif(!empty($fromDate) || !empty($toDate))
              <span class="text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5">
                <i class="fa-regular fa-calendar-check"></i> Check-in: {{ !empty($fromDate) ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }} &rarr; {{ !empty($toDate) ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'End' }}
              </span>
            @else
              <span class="text-xs font-bold bg-slate-100 text-slate-600 border border-slate-300 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5">
                <i class="fa-regular fa-calendar"></i> All Check-in Dates
              </span>
            @endif
          </h2>
          <div class="text-xs text-slate-500 mt-0.5">
            @if(!empty($fromDate) && !empty($toDate) && $fromDate === $toDate)
              Showing settlement records with check-in on <strong>{{ \Carbon\Carbon::parse($fromDate)->format('D, d F Y') }}</strong> ({{ $fromDate === $today ? 'Today' : 'Selected Date' }}).
            @elseif(!empty($fromDate) || !empty($toDate))
              Showing settlement records with check-in between <strong>{{ !empty($fromDate) ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }}</strong> and <strong>{{ !empty($toDate) ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'End' }}</strong>.
            @else
              Booking numbers grouped settlement directory with rooms, occupants, financial settlements &amp; check-out control.
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- Search & Filter Controls Toolbar (like guest) -->
    <!-- ========================================== -->
    <form method="GET" action="{{ route('frontoffice.folio') }}" id="folio-filter-form" class="folio-toolbar">
      <div class="flex justify-between items-center flex-wrap gap-3 w-full">
        <div class="flex gap-3 items-center flex-1 flex-wrap">
          
          <!-- Search Box with Clear Button -->
          <div class="search-box-wrap">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" 
                   name="search" 
                   id="folio-search-input"
                   value="{{ request('search') }}" 
                   placeholder="Search by Booking #, Settlement #, Guest, Phone, Room..." 
                   onkeydown="if(event.key==='Enter'){this.form.submit();}"
                   autocomplete="off">
            @if(request()->filled('search'))
              <a href="{{ route('frontoffice.folio', request()->except('search', 'page')) }}" 
                 title="Clear search" 
                 class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 no-underline text-xs">
                <i class="fa-solid fa-xmark"></i>
              </a>
            @endif
          </div>

          <!-- Date Range: From & To Date Filter -->
          <div class="inline-flex items-center gap-1.5">
            <label for="folio-from-date" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              <i class="fa-regular fa-calendar-days text-indigo-500"></i> Check-in From:
            </label>
            <input type="date" 
                   name="from_date" 
                   id="folio-from-date"
                   class="pms-filter-date" 
                   value="{{ $fromDate ?? '' }}" 
                   onchange="this.form.submit()"
                   title="Filter by Check-in Date From">

            <label for="folio-to-date" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              To:
            </label>
            <input type="date" 
                   name="to_date" 
                   id="folio-to-date"
                   class="pms-filter-date" 
                   value="{{ $toDate ?? '' }}" 
                   onchange="this.form.submit()"
                   title="Filter by Check-in Date To">
          </div>

          <!-- Status Filter Dropdown (Settled / Unsettled) -->
          <div class="inline-flex items-center gap-1.5">
            <label for="folio-status-filter" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              <i class="fa-solid fa-filter text-indigo-500"></i> Status:
            </label>
            <select name="status" id="folio-status-filter" class="pms-filter-select" onchange="this.form.submit()">
              <option value="all" {{ request('status') === 'all' || !request()->filled('status') ? 'selected' : '' }}>All Statuses</option>
              <option value="Settled" {{ in_array(strtolower(request('status') ?? ''), ['settled', 'sattled']) ? 'selected' : '' }}>Settled (Payments Clear)</option>
              <option value="Unsettled" {{ in_array(strtolower(request('status') ?? ''), ['unsettled', 'unsattled']) ? 'selected' : '' }}>Unsettled (Payment Due)</option>
            </select>
          </div>

          <!-- Registration Type Filter -->
          <div class="inline-flex items-center gap-1.5">
            <label for="folio-regtype-filter" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              <i class="fa-solid fa-id-badge text-indigo-500"></i> Reg Type:
            </label>
            <select name="registration_type" id="folio-regtype-filter" class="pms-filter-select" onchange="this.form.submit()">
              <option value="all" {{ request('registration_type') === 'all' || !request()->filled('registration_type') ? 'selected' : '' }}>All Reg Types</option>
              @foreach($registrationTypes ?? [] as $rt)
                <option value="{{ $rt->name }}" {{ request('registration_type') === $rt->name ? 'selected' : '' }}>{{ $rt->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- All Dates Quick Button (if date filter active) -->
          @if(!empty($fromDate) || !empty($toDate))
            <a href="{{ route('frontoffice.folio', array_merge(request()->except(['from_date', 'to_date', 'page']), ['all_dates' => 1])) }}" 
               class="btn-ui-secondary px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-600 inline-flex items-center gap-1.5 no-underline hover:bg-slate-50 transition shadow-2xs" 
               title="View all records without date restrictions">
              <i class="fa-regular fa-calendar"></i> All Dates
            </a>
          @endif

          <!-- Today / Reset Button (resets to today's data) -->
          <a href="{{ route('frontoffice.folio') }}" 
             class="btn-ui-secondary px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-600 inline-flex items-center gap-1.5 no-underline hover:bg-slate-50 transition shadow-2xs" 
             title="Reset filters to today's check-ins">
            <i class="fa-solid fa-rotate-left"></i> Today / Reset
          </a>
        </div>

        <!-- Page Size Selector -->
        <div class="flex gap-3 items-center">
          <div class="pms-per-page-wrap">
            <span>Show</span>
            <select name="per_page" class="pms-per-page-select" id="folio-per-page-select" onchange="this.form.submit()">
              <option value="10" {{ request('per_page') == '10' || !request()->filled('per_page') ? 'selected' : '' }}>10</option>
              <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
              <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
              <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
            </select>
            <span>entries</span>
          </div>
        </div>

      </div>
    </form>

    <!-- Main Settlement Master Table with SL Indexing -->
    <div class="pms-page-content">
      <div class="pms-table-box rounded-2xl shadow-sm bg-white overflow-x-auto border border-slate-200">
        <table class="pms-table" id="folio-master-table">
          <thead>
            <tr>
              <th class="w-11 text-center whitespace-nowrap">SL</th>
              <th class="min-w-[160px] whitespace-nowrap">Room(s) &amp; Floor</th>
              <th class="min-w-[140px] text-left whitespace-nowrap">Booking Number</th>
              <th class="min-w-[180px] whitespace-nowrap">Primary Guest Details</th>
              <th class="min-w-[130px] whitespace-nowrap">Mobile / Phone</th>
              <th class="min-w-[120px] text-right whitespace-nowrap">Paid Amount</th>
              <th class="min-w-[130px] text-right whitespace-nowrap">Remaining Amount</th>
              <th class="min-w-[135px] whitespace-nowrap">Check-in</th>
              <th class="min-w-[135px] whitespace-nowrap">Check-out</th>
              <th class="min-w-[100px] text-center whitespace-nowrap">Status</th>
              <th class="min-w-[130px] text-center whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody id="folio-table-tbody">
            @forelse($folios as $index => $item)
              @php
                $sl = ($folios->currentPage() - 1) * $folios->perPage() + $loop->iteration;

                // Additional guests count for dynamic "+N persons"
                $extraCount = $item->additionalBookings?->count() ?? 0;
                $primaryGuestName = ($item->guest?->title?->name ? $item->guest->title->name . ' ' : '') . ($item->guest?->guest_name ?? 'Guest');

                // Collect all unique rooms under this reservation
                $allReservationBookings = collect([$item])->concat($item->additionalBookings ?? []);
                $uniqueRooms = $allReservationBookings->map(function($b) {
                  return $b->room;
                })->filter()->unique('id');

                // Extra Services charged to room (gathered chronologically date-wise)
                $allResServiceOrders = collect();
                if ($item->roomServiceOrders) {
                    $allResServiceOrders = $allResServiceOrders->concat(
                        $item->roomServiceOrders->where('payment_status', 'Charged to Room Folio')->where('order_status', '!=', 'Cancelled')
                    );
                }
                if ($item->additionalBookings) {
                    foreach ($item->additionalBookings as $ab) {
                        if ($ab->roomServiceOrders) {
                            $allResServiceOrders = $allResServiceOrders->concat(
                                $ab->roomServiceOrders->where('payment_status', 'Charged to Room Folio')->where('order_status', '!=', 'Cancelled')
                            );
                        }
                    }
                }
                $allResServiceOrders = $allResServiceOrders->sortBy('created_at')->values();
                $extraServicesTotal = (float)$allResServiceOrders->sum('total_amount');

                // Financials
                $advAmount = (float)$item->advance_amount;
                $settleAmount = (float)($item->settlements?->sum('paid_amount') ?: $item->payments?->where('payment_type', 'Settlement')->sum('amount'));
                $totalPaid = round($advAmount + $settleAmount, 2);
                $balanceVal = round((float)($item->balance ?? 0) + $extraServicesTotal, 2);

                // Status in table: Only Settled (payments all clear) & Unsettled (payment not cleared)
                $isSettled = ($balanceVal <= 0.01) || in_array(strtolower($item->status ?? ''), ['settlement done', 'settled']);

                // Checkin / Checkout date & time
                $cInDate = $item->checkin_date ?: $item->reserve_date;
                $cInTime = $item->checkin_time ?: $item->reserve_time;
                $cOutDate = $item->checkout_date;
                $cOutTime = $item->checkout_time;
              @endphp
              <tr class="folio-row" id="row-folio-{{ $item->id }}">
                
                <!-- 0. SL -->
                <td class="text-center font-mono font-extrabold text-slate-500 text-xs">
                  {{ $sl }}
                </td>

                <!-- 1. Room(s) & Floor (First) -->
                <td class="whitespace-nowrap">
                  @if($uniqueRooms->isNotEmpty())
                    <div class="flex flex-col gap-1">
                      @foreach($uniqueRooms as $uRoom)
                        @php
                          $cleanRNum = preg_replace('/^r[-_\s]?/i', '', trim($uRoom->room_number));
                          $rDisp = 'R' . $cleanRNum;
                          $flrDisp = $uRoom->floorRelation?->floor ?? ($uRoom->floor ?? '1');
                          $catDisp = $uRoom->categoryRelation?->name ?? ($uRoom->category ?? 'Standard');
                        @endphp
                        <div class="inline-flex items-center gap-1.5 flex-row">
                          <span class="room-tag">
                            <i class="fa-solid fa-door-open text-[10px]"></i>
                            {{ $rDisp }}
                          </span>
                          <span class="text-[10px] text-slate-500 font-bold">
                            Floor {{ $flrDisp }}
                          </span>
                        </div>
                      @endforeach
                    </div>
                  @else
                    <span class="text-xs text-slate-400 italic">
                      <i class="fa-solid fa-hourglass-half text-[10px]"></i> Unassigned
                    </span>
                  @endif
                </td>

                <!-- 2. Booking Number (Then Booking Number) -->
                <td class="whitespace-nowrap">
                  <div class="folio-id-text cursor-pointer" onclick="openFolioModalById({{ $item->id }})" data-booking-id="{{ $item->id }}" title="Click to open Settlement Dossier">
                    <i class="fa-solid fa-receipt text-indigo-500 mr-1 text-[11px] pointer-events-none"></i>
                    {{ $item->reserve_id ?: ('RES-' . $item->id) }}
                  </div>
                  <div class="folio-number-sub cursor-pointer" onclick="openFolioModalById({{ $item->id }})" data-booking-id="{{ $item->id }}" title="Click to open Settlement Dossier">
                    Settlement: {{ $item->folio_number ?: '-' }}
                  </div>
                </td>

                <!-- 3. Primary Guest Details with Dynamic +N persons -->
                <td class="whitespace-nowrap">
                  <div class="font-extrabold text-slate-900 text-xs inline-flex items-center">
                    <span>{{ $primaryGuestName }}</span>
                    @if($extraCount > 0)
                      <span class="badge-extra-guests" title="{{ $extraCount }} additional person(s) registered under this booking">
                        <i class="fa-solid fa-user-plus text-[9px]"></i>
                        +{{ $extraCount }} {{ $extraCount === 1 ? 'person' : 'persons' }}
                      </span>
                    @endif
                  </div>
                  <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                    @if($item->registrationType?->name)
                      <span class="bg-slate-100 px-1.5 py-0.5 rounded font-bold text-slate-600">
                        {{ $item->registrationType->name }}
                      </span>
                    @endif
                    @if($item->guest?->city)
                      <span class="text-slate-300">•</span>
                      <span class="text-slate-400"><i class="fa-solid fa-location-dot text-[9px]"></i> {{ $item->guest->city }}</span>
                    @endif
                  </div>
                </td>

                <!-- 4. Primary Mobile / Phone -->
                <td class="whitespace-nowrap">
                  <div class="font-mono font-bold text-xs text-slate-800 inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-phone text-[10px] text-emerald-500"></i>
                    <span>{{ $item->guest?->mobile ?: 'N/A' }}</span>
                  </div>
                </td>

                <!-- 5. Paid Amount -->
                <td class="text-right whitespace-nowrap">
                  <div class="font-mono font-extrabold text-xs text-emerald-600">
                    ₹ {{ number_format($totalPaid, 2) }}
                  </div>
                  <div class="text-[10px] text-slate-500 mt-0.5">
                    @if($settleAmount > 0)
                      <span class="text-indigo-600 font-bold">(Incl. ₹{{ number_format($settleAmount, 0) }} Settle)</span>
                    @else
                      <span>Advance Deposit</span>
                    @endif
                  </div>
                </td>

                <!-- 6. Remaining Amount -->
                <td class="text-right whitespace-nowrap">
                  <div class="font-mono font-extrabold text-xs {{ $balanceVal > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    ₹ {{ number_format($balanceVal, 2) }}
                  </div>
                  <div class="mt-0.5">
                    @if($balanceVal > 0)
                      <span class="text-rose-700 bg-rose-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold">
                        <i class="fa-solid fa-triangle-exclamation text-[8px]"></i> Due
                      </span>
                      @if($extraServicesTotal > 0)
                        <div class="text-[9.5px] text-amber-700 font-bold mt-0.5" title="{{ $allResServiceOrders->map(fn($o) => ($o->created_at ? $o->created_at->format('d M, h:i A') : 'N/A') . ' - ₹' . number_format($o->total_amount, 2) . ' (' . ($o->items ? $o->items->map(fn($it)=>$it->quantity.'x '.$it->item_name)->implode(', ') : 'Order') . ')')->implode(' | ') }}">
                          <i class="fa-solid fa-bell-concierge text-[8.5px]"></i> +₹{{ number_format($extraServicesTotal, 2) }} ({{ $allResServiceOrders->count() }} {{ $allResServiceOrders->count() === 1 ? 'order' : 'orders' }})
                        </div>
                        @if($allResServiceOrders->count() > 1)
                          <div class="flex flex-col gap-0.5 mt-0.5 items-end">
                            @foreach($allResServiceOrders as $ord)
                              <span class="text-[8.5px] font-bold text-amber-800 bg-orange-50 border border-orange-200 px-1 py-0.2 rounded inline-flex items-center gap-1" title="{{ $ord->created_at ? $ord->created_at->format('d M Y, h:i A') : '-' }}">
                                <i class="fa-regular fa-clock text-[7.5px] opacity-70"></i> {{ $ord->created_at ? $ord->created_at->format('d M') : '-' }}: ₹{{ number_format($ord->total_amount, 2) }}
                              </span>
                            @endforeach
                          </div>
                        @endif
                      @endif
                    @else
                      <span class="text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold">
                        <i class="fa-solid fa-check text-[8px]"></i> Cleared
                      </span>
                    @endif
                  </div>
                </td>

                <!-- 7. Check-in Date & Time -->
                <td class="whitespace-nowrap">
                  <div class="font-bold text-slate-800 text-xs inline-flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar-check text-indigo-500 text-[11px]"></i>
                    <span>{{ $cInDate ? \Carbon\Carbon::parse($cInDate)->format('d M Y') : 'N/A' }}</span>
                  </div>
                  @if($cInTime)
                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                      <i class="fa-regular fa-clock text-[9px] text-slate-400"></i> {{ date('h:i A', strtotime($cInTime)) }}
                    </div>
                  @endif
                </td>

                <!-- 8. Check-out Date & Time -->
                <td class="whitespace-nowrap">
                  <div class="font-bold text-slate-800 text-xs inline-flex items-center gap-1.5">
                    <i class="fa-regular fa-calendar-xmark text-rose-500 text-[11px]"></i>
                    <span>{{ $cOutDate ? \Carbon\Carbon::parse($cOutDate)->format('d M Y') : 'N/A' }}</span>
                  </div>
                  @if($cOutTime)
                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                      <i class="fa-regular fa-clock text-[9px] text-slate-400"></i> {{ date('h:i A', strtotime($cOutTime)) }}
                    </div>
                  @endif
                </td>

                <!-- 9. Status: Only Settled (payments all clear) & Unsettled (not settled) -->
                <td class="text-center whitespace-nowrap">
                  @if($isSettled)
                    <span class="status-badge settled" title="Settled: All payments cleared (Balance ₹0.00)">
                      <i class="fa-solid fa-circle-check text-[8px]"></i>
                      <span>Settled</span>
                    </span>
                  @else
                    <span class="status-badge unsettled" title="Unsettled: Pending balance ₹{{ number_format($balanceVal, 2) }}">
                      <i class="fa-solid fa-clock text-[8px]"></i>
                      <span>Unsettled</span>
                    </span>
                  @endif
                </td>

                <!-- 10. Action: View Details Modal, Swipe & Extend Stay -->
                <td class="text-center whitespace-nowrap">
                  <div class="inline-flex items-center gap-1.5">
                    <button type="button" 
                            class="btn-action-view" 
                            data-booking-id="{{ $item->id }}"
                            onclick="openFolioModalById({{ $item->id }})" 
                            title="View Full Settlement Dossier (Rooms, Occupants, Billings, Settle &amp; Checkout)">
                      <i class="fa-solid fa-eye pointer-events-none"></i>
                    </button>
                    @if(!in_array($item->status, ['Checked Out', 'Cancelled', 'Settlement Done', 'Settled']) && !$isSettled)
                      <button type="button" 
                              class="btn-action-swipe" 
                              data-booking-id="{{ $item->id }}"
                              onclick="openFolioModalForSwipe({{ $item->id }})" 
                              title="Swipe / Shift Guest Room">
                        <i class="fa-solid fa-arrow-right-arrow-left pointer-events-none"></i>
                        <span>Swipe</span>
                      </button>
                    @endif
                    @if(!in_array($item->status, ['Checked Out', 'Cancelled', 'Settlement Done']))
                      <button type="button" 
                              class="btn-action-extend" 
                              onclick="openExtendStayModal({{ $item->id }})" 
                              title="Extend Stay Duration (Live Room Availability Check)">
                        <i class="fa-solid fa-calendar-plus pointer-events-none"></i>
                      </button>
                    @endif
                  </div>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center text-slate-400 py-10 px-5">
                  <div class="text-3xl text-slate-300 mb-2"><i class="fa-solid fa-receipt"></i></div>
                  <div class="text-sm font-bold text-slate-600">No matching settlements found for this filter.</div>
                  <div class="text-xs text-slate-400 mt-1">
                    Showing check-ins for {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'selected dates' }}. 
                    @if(!empty($fromDate) || !empty($toDate))
                      <a href="{{ route('frontoffice.folio', ['all_dates' => 1]) }}" class="text-indigo-600 font-extrabold underline hover:text-indigo-800">Click here to view All Dates</a>.
                    @endif
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <!-- Backend Table Pagination Footer -->
        <div class="pms-table-pagination flex justify-between items-center px-4.5 py-3 border-t border-slate-200 bg-white flex-wrap gap-2.5">
          <div class="pms-pagination-info text-xs font-semibold text-slate-500">
            Showing {{ $folios->firstItem() ?? 0 }} to {{ $folios->lastItem() ?? 0 }} of {{ $folios->total() }} records
          </div>
          <div class="pms-page-nav">
            @if ($folios->hasPages())
              {{-- Previous Page Link --}}
              @if ($folios->onFirstPage())
                <span class="pms-page-btn opacity-40 cursor-not-allowed" title="First Page"><i class="fa-solid fa-chevron-left"></i></span>
              @else
                <a href="{{ $folios->previousPageUrl() }}" class="pms-page-btn" title="Previous Page"><i class="fa-solid fa-chevron-left"></i></a>
              @endif

              {{-- Pagination Number Elements --}}
              @foreach ($folios->getUrlRange(max(1, $folios->currentPage() - 2), min($folios->lastPage(), $folios->currentPage() + 2)) as $page => $url)
                @if ($page == $folios->currentPage())
                  <span class="pms-page-btn active">{{ $page }}</span>
                @else
                  <a href="{{ $url }}" class="pms-page-btn">{{ $page }}</a>
                @endif
              @endforeach

              {{-- Next Page Link --}}
              @if ($folios->hasMorePages())
                <a href="{{ $folios->nextPageUrl() }}" class="pms-page-btn" title="Next Page"><i class="fa-solid fa-chevron-right"></i></a>
              @else
                <span class="pms-page-btn opacity-40 cursor-not-allowed" title="Last Page"><i class="fa-solid fa-chevron-right"></i></span>
              @endif
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Bottom Summary Bar (like guest CRM) -->
<footer class="bg-white border-t border-slate-200 px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 shadow-2xs z-30 shrink-0 pms-bottom-bar">
  <div class="text-xs font-bold text-slate-600 flex items-center gap-3.5 flex-wrap">
    <span><i class="fa-solid fa-receipt text-indigo-500"></i> Total Settlements: <strong>{{ $stats['total_folios'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-file-invoice text-cyan-600"></i> Total Receivable: <strong>₹ {{ number_format($stats['total_receivable'] ?? 0, 2) }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-circle-check text-emerald-600"></i> Total Paid: <strong>₹ {{ number_format($stats['total_paid'] ?? 0, 2) }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-triangle-exclamation {{ ($stats['total_outstanding'] ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}"></i> Remaining Due: <strong>₹ {{ number_format($stats['total_outstanding'] ?? 0, 2) }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-circle-check text-emerald-600"></i> Settled: <strong>{{ $stats['settled_count'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-clock text-rose-600"></i> Unsettled: <strong>{{ $stats['pending_settle_count'] ?? 0 }}</strong></span>
  </div>
</footer>

<!-- ========================================================================= -->
<!-- EXTENSIVE SETTLEMENT DOSSIER MODAL (Design matching guest CRM)             -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="folio-details-modal">
  <div class="modal-window max-w-4xl w-full max-h-[92vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200">
    
    <!-- Modal Header (Gradient style matching guest CRM) -->
    <div class="modal-top bg-gradient-to-r from-indigo-600 to-indigo-800 text-white px-5 py-3.5 flex justify-between items-center border-b border-indigo-500/30">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-base text-white flex-shrink-0">
          <i class="fa-solid fa-receipt"></i>
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h3 id="fmdl-guest-name" class="m-0 text-base font-extrabold text-white">Guest Profile &amp; Settlement Dossier</h3>
            <span id="fmdl-status-badge" class="status-badge bg-white text-indigo-700 font-extrabold shadow-xs">Confirmed</span>
          </div>
          <div class="text-[11px] text-indigo-100 font-mono mt-0.5">
            Booking Ref: <span id="fmdl-reserve-id" class="font-bold">-</span> &nbsp;|&nbsp; Settlement: <span id="fmdl-folio-number" class="font-bold">-</span>
          </div>
        </div>
      </div>
      <button type="button" class="modal-close bg-white/20 hover:bg-white/30 text-white w-8 h-8 rounded-full text-lg cursor-pointer flex items-center justify-center transition border-none" onclick="closeFolioModal()">&times;</button>
    </div>

    <!-- Modal Content Area -->
    <div class="modal-content-area p-5 bg-slate-50 overflow-y-auto max-h-[calc(92vh-130px)]" id="folio-modal-body">
      
      <!-- Top Highlight Banner (like guest CRM) -->
      <div class="dossier-highlight-box bg-gradient-to-r from-slate-50 to-slate-100 border border-slate-200 rounded-xl p-3.5 mb-4 flex justify-between items-center flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fa-solid fa-door-open"></i>
          </div>
          <div>
            <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Allotted Room(s) &amp; Status</div>
            <div class="text-base font-black text-slate-900 mt-0.5" id="fmdl-highlight-rooms">
              -
            </div>
          </div>
        </div>

        <div class="flex gap-2 items-center flex-wrap">
          <span class="text-xs font-extrabold bg-white border border-slate-300 px-2.5 py-1 rounded-md text-slate-700 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-users text-indigo-600"></i> <span id="fmdl-total-guests-count">1 Person</span>
          </span>
          <span class="text-xs font-extrabold bg-orange-50 border border-orange-200 px-2.5 py-1 rounded-md text-orange-800 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-utensils text-orange-600"></i> <span id="fmdl-plan-name">EP</span>
          </span>
          <span class="text-xs font-extrabold bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-md text-indigo-800 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-moon text-indigo-600"></i> <span id="fmdl-nights-sched">1 Night(s) Stay</span>
          </span>
          <span class="text-xs font-extrabold bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-md text-emerald-800 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-headset text-emerald-600"></i> <span id="fmdl-reservation-mode">Front Desk</span>
          </span>
        </div>
      </div>

      <!-- 2-Column Uniform Dossier Grid (like guest CRM) -->
      <div class="dossier-grid">
        
        <!-- CARD 1: Primary Guest Profile Details -->
        <div class="dossier-card">
          <div class="dossier-card-title">
            <i class="fa-solid fa-user-check"></i> Primary Guest Profile Details
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Name</span>
            <span class="dossier-val" id="fmdl-card-name">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Registration Type</span>
            <span class="dossier-val text-indigo-700 font-extrabold" id="fmdl-card-regtype">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Mobile Number</span>
            <span class="dossier-val mono" id="fmdl-guest-mobile">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Email Address</span>
            <span class="dossier-val" id="fmdl-guest-email">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">City / Location</span>
            <span class="dossier-val" id="fmdl-guest-city">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Address</span>
            <span class="dossier-val max-w-[220px] whitespace-normal" id="fmdl-guest-address">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Nationality</span>
            <span class="dossier-val" id="fmdl-guest-nationality">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Identity Proof</span>
            <span class="dossier-val" id="fmdl-guest-idproof">-</span>
          </div>

          <!-- KYC Document Images Row -->
          <div class="dossier-field-row" id="fmdl-card-docs-row">
            <span class="dossier-label">KYC Documents</span>
            <span class="dossier-val" id="fmdl-card-docs-val">
              <button type="button" onclick="openCurrentFolioDocsModal()" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded-lg transition cursor-pointer">
                <i class="fa-solid fa-images text-indigo-600"></i> View Document Images (<span class="fmdl-docs-count-pill">0</span>)
              </button>
            </span>
          </div>

          <div class="dossier-field-row hidden" id="fmdl-card-privilege-row">
            <span class="dossier-label">Privilege / VIP Card</span>
            <span class="dossier-val mono text-amber-700 font-bold" id="fmdl-card-privilege">-</span>
          </div>
        </div>

        <!-- CARD 2: Stay Schedule & Corporate Account -->
        <div class="dossier-card">
          <div class="dossier-card-title flex justify-between items-center">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-calendar-days"></i> Stay &amp; Corporate Details
            </div>
            <button type="button" 
                    id="fmdl-btn-extend-stay" 
                    onclick="if(activeFolioData) openExtendStayModal(activeFolioData.id)" 
                    class="border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[10px] font-extrabold px-2 py-0.5 rounded-md cursor-pointer inline-flex items-center gap-1 transition shadow-2xs" 
                    title="Extend Stay Duration (Live Room Availability Check)">
              <i class="fa-solid fa-calendar-plus"></i> Extend Stay
            </button>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-In (Arrival)</span>
            <span class="dossier-val mono text-indigo-600" id="fmdl-checkin-sched">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-Out (Departure)</span>
            <span class="dossier-val mono text-emerald-600" id="fmdl-checkout-sched">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Duration of Stay</span>
            <span class="dossier-val" id="fmdl-duration-stay">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Reservation Mode</span>
            <span class="dossier-val" id="fmdl-resmode-val">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Meal Plan</span>
            <span class="dossier-val text-orange-700 font-extrabold" id="fmdl-mealplan-val">-</span>
          </div>

          <!-- Corporate Account Block -->
          <div id="fmdl-company-row" class="mt-2.5 p-2.5 rounded-lg bg-indigo-50/50 border border-indigo-200 hidden">
            <div class="text-[10px] font-extrabold text-indigo-700 uppercase mb-1.5 flex items-center gap-1.5">
              <i class="fa-solid fa-building"></i> Corporate Account
            </div>
            <div class="dossier-field-row py-1">
              <span class="dossier-label">Company Name</span>
              <span class="dossier-val text-indigo-700 font-bold" id="fmdl-company-name">-</span>
            </div>
            <div class="dossier-field-row py-1">
              <span class="dossier-label">Company GSTIN</span>
              <span class="dossier-val mono" id="fmdl-company-gstin">-</span>
            </div>
            <div class="dossier-field-row py-1">
              <span class="dossier-label">Phone</span>
              <span class="dossier-val mono" id="fmdl-company-phone">-</span>
            </div>
          </div>
        </div>

      </div>

      <!-- CARD 3: Allocated Rooms & Registered Occupants Details -->
      <div class="dossier-card mb-4" id="fmdl-card-rooms-occupants">
        <div class="dossier-card-title">
          <i class="fa-solid fa-bed"></i> Allocated Rooms &amp; Registered Occupants
        </div>
        <div id="fmdl-rooms-container">
          <!-- Room cards & occupant badges populated dynamically -->
        </div>
      </div>

      <!-- CARD 4: Pricing & Billing Breakdown (like guest CRM) -->
      <div class="dossier-card mb-0">
        <div class="dossier-card-title">
          <i class="fa-solid fa-file-invoice-dollar"></i> Pricing &amp; Billing Breakdown
        </div>

        <!-- 4-Stat Metric Boxes matching guest CRM -->
        <div class="billing-summary-grid">
          <div class="bill-stat-item">
            <div class="bill-stat-label">Daily Room Rate</div>
            <div class="bill-stat-value text-slate-700" id="fmdl-bill-rate">₹ 0.00</div>
          </div>

          <div class="bill-stat-item">
            <div class="bill-stat-label">Gross Amount</div>
            <div class="bill-stat-value text-slate-800" id="fmdl-gross-amount">₹ 0.00</div>
          </div>

          <div class="bill-stat-item">
            <div class="bill-stat-label">Total Amount Paid</div>
            <div class="bill-stat-value text-emerald-600" id="fmdl-totalpaid-amount">₹ 0.00</div>
          </div>

          <div class="bill-stat-item" id="fmdl-balance-box">
            <div class="bill-stat-label" id="fmdl-balance-label">Balance Due</div>
            <div class="bill-stat-value text-rose-600" id="fmdl-balance-amount">₹ 0.00</div>
          </div>
        </div>

        <!-- Itemized Line Rows -->
        <div class="mt-3.5 bg-white border border-slate-100 rounded-lg p-2.5 px-3.5">
          <div class="dossier-field-row">
            <span class="dossier-label">Room Rate × Nights</span>
            <span class="dossier-val mono" id="fmdl-calc-row">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Advance Deposit Paid</span>
            <span class="dossier-val mono text-emerald-600" id="fmdl-advance-row">₹ 0.00</span>
          </div>
          <div class="dossier-field-row" id="fmdl-settle-field-row">
            <span class="dossier-label">Settlement Amount Paid</span>
            <span class="dossier-val mono text-indigo-600 font-extrabold" id="fmdl-settlement-row">₹ 0.00</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Total Amount Paid</span>
            <span class="dossier-val mono text-emerald-600 font-extrabold" id="fmdl-totalpaid-row">₹ 0.00</span>
          </div>
          <div class="dossier-field-row hidden" id="fmdl-services-field-row">
            <span class="dossier-label flex items-center gap-1.5 text-amber-700 font-bold">
              <i class="fa-solid fa-utensils"></i> In-Room Dining &amp; Services Total
            </span>
            <span class="dossier-val mono text-amber-700 font-extrabold" id="fmdl-services-row">₹ 0.00</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Discount</span>
            <span class="dossier-val mono text-emerald-600 font-bold" id="fmdl-discount-row">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">GST / Tax</span>
            <span class="dossier-val mono text-indigo-600 font-bold" id="fmdl-tax-row">-</span>
          </div>
          <div class="dossier-field-row bg-slate-50 p-2 rounded-md my-1">
            <span class="dossier-label font-extrabold text-slate-900">Grand Total Net Payable</span>
            <span class="dossier-val mono text-sm font-black text-indigo-700" id="fmdl-payable-amount">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Payment Mode</span>
            <span class="dossier-val" id="fmdl-paymode-row">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Payment Remarks / Reference</span>
            <span class="dossier-val text-slate-500" id="fmdl-remarks-row">-</span>
          </div>
        </div>

        <!-- Room Service & Extra Services Invoices Box -->
        <div class="mt-3.5 hidden" id="fmdl-room-services-box">
          <div class="text-xs font-extrabold text-amber-700 uppercase tracking-wider mb-1.5 flex justify-between items-center">
            <span><i class="fa-solid fa-bell-concierge"></i> In-Room Dining &amp; Service Bills (<span id="fmdl-services-count">0</span> Orders)</span>
            <span class="text-[10px] text-slate-500 normal-case font-semibold">Charged to Guest Folio</span>
          </div>
          <div id="fmdl-room-services-list" class="text-xs">
            <!-- Populated via JS -->
          </div>
        </div>

        <!-- Settlement Receipts History List -->
        <div class="mt-3.5" id="fmdl-settle-history-box">
          <div class="text-xs font-extrabold text-slate-600 uppercase tracking-wider mb-1.5">
            <i class="fa-solid fa-clock-rotate-left"></i> Settlement Receipts History
          </div>
          <div id="fmdl-settle-history-list" class="text-xs">
            <!-- Populated via JS -->
          </div>
        </div>

        <!-- Room Swipes & Transfers History Box -->
        <div class="mt-3.5 hidden" id="fmdl-room-swipes-box">
          <div class="text-xs font-extrabold text-indigo-700 uppercase tracking-wider mb-1.5 flex justify-between items-center">
            <span><i class="fa-solid fa-arrow-right-arrow-left"></i> Room Swipe / Transfer History (<span id="fmdl-swipes-count">0</span> Shifts)</span>
            <span class="text-[10px] text-slate-500 normal-case font-semibold">Prorated Daily Tariffs</span>
          </div>
          <div id="fmdl-room-swipes-list" class="text-xs">
            <!-- Populated via JS -->
          </div>
        </div>

        <!-- Interactive Inline Settlement Panel -->
        <div id="fmdl-settlement-interactive-panel" class="settlement-panel hidden">
          <div class="flex justify-between items-center mb-2.5 flex-wrap gap-2">
            <div class="text-xs font-extrabold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
              <i class="fa-solid fa-calculator"></i> Process Settlement
            </div>
            <button type="button" onclick="setFullFolioSettlementAmount()" class="bg-transparent border-none text-emerald-700 font-extrabold cursor-pointer underline text-xs p-0 hover:text-emerald-900">
              Pay Full Due (<span id="fmdl-btn-payfull-val">₹ 0.00</span>)
            </button>
          </div>

          <!-- Dynamic Settlement Price Breakdown (Room + Extra Services Date-wise) -->
          <div id="fmdl-settle-breakdown-box" class="mb-3"></div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 mb-2.5">
            <div>
              <label class="block text-[11px] font-extrabold text-slate-800 mb-1">
                Settlement Amount (₹) <span class="text-rose-600">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-extrabold text-slate-400 text-xs">₹</span>
                <input type="number" 
                       step="0.01" 
                       min="0.01" 
                       id="fmdl-input-settle-amount" 
                       class="w-full box-border py-2 pr-2.5 pl-6 rounded-lg border border-emerald-300 text-xs font-extrabold font-mono text-slate-900 outline-none bg-white focus:ring-2 focus:ring-emerald-500/20">
              </div>
            </div>

            <div>
              <label class="block text-[11px] font-extrabold text-slate-800 mb-1">
                Payment Method <span class="text-rose-600">*</span>
              </label>
              <select id="fmdl-select-settle-mode" class="w-full box-border py-2 px-2.5 rounded-lg border border-emerald-300 text-xs font-bold text-slate-800 bg-white outline-none focus:ring-2 focus:ring-emerald-500/20">
                @foreach($paymentModes ?? [] as $pm)
                  <option value="{{ $pm->id }}" {{ strtolower($pm->name) === 'cash' ? 'selected' : '' }}>{{ $pm->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-[11px] font-extrabold text-slate-800 mb-1">
                Txn / Reference ID (Optional)
              </label>
              <input type="text" 
                     id="fmdl-input-settle-txn" 
                     placeholder="e.g. UPI-998234, CARD-1049" 
                     class="w-full box-border py-2 px-2.5 rounded-lg border border-emerald-300 text-xs font-mono outline-none bg-white focus:ring-2 focus:ring-emerald-500/20">
            </div>
          </div>

          <div class="mb-3">
            <label class="block text-[11px] font-extrabold text-slate-800 mb-1">
              Settlement Notes / Remarks
            </label>
            <input type="text" 
                   id="fmdl-input-settle-remarks" 
                   placeholder="e.g. Counter settlement finalized with primary guest." 
                   class="w-full box-border py-2 px-2.5 rounded-lg border border-emerald-300 text-xs outline-none bg-white focus:ring-2 focus:ring-emerald-500/20">
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" 
                    id="fmdl-btn-confirm-settlement" 
                    onclick="executeFolioSettlement()" 
                    class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white border-none text-xs font-extrabold inline-flex items-center gap-1.5 cursor-pointer shadow-sm transition">
              <i class="fa-solid fa-check-circle"></i> Confirm &amp; Save Settlement
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Footer Actions (matches guest CRM) -->
    <div class="modal-bot px-5.5 py-3 bg-white border-t border-slate-200 flex justify-between items-center flex-wrap gap-2.5">
      <div class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Record Registered: <span id="fmdl-created-at">-</span>
      </div>

      <div class="flex gap-2 items-center flex-wrap">
        <!-- Settle Balance Button -->
        <button type="button" 
                id="fmdl-action-btn-settle" 
                onclick="toggleFolioSettlementPanel()" 
                class="px-4.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white border-none text-xs font-extrabold inline-flex items-center gap-1.5 cursor-pointer shadow-sm transition">
          <i class="fa-solid fa-calculator"></i> Settle Balance
        </button>

        <!-- Check Out Button -->
        <button type="button" 
                id="fmdl-action-btn-checkout" 
                onclick="executeFolioCheckout()" 
                class="px-4.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white border-none text-xs font-extrabold hidden items-center gap-1.5 cursor-pointer shadow-sm transition">
          <i class="fa-solid fa-right-from-bracket"></i> Check Out
        </button>

        <!-- Locked Badge -->
        <div id="fmdl-badge-locked" 
             class="hidden px-3.5 py-1.5 rounded-lg bg-slate-100 border border-slate-300 text-slate-600 text-xs font-extrabold items-center gap-1.5">
          <i class="fa-solid fa-lock text-slate-500"></i> Stay Finalized &amp; Checked Out (Locked)
        </div>

        <!-- View Document Images Button -->
        <button type="button" id="fmdl-footer-btn-docs" class="btn-ui-secondary px-3.5 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50/80 text-xs font-bold text-indigo-700 hover:bg-indigo-100 inline-flex items-center gap-1.5 cursor-pointer shadow-2xs transition" onclick="openCurrentFolioDocsModal()">
          <i class="fa-solid fa-images text-indigo-600"></i> View Document Images (<span class="fmdl-docs-count-pill">0</span>)
        </button>

        <!-- Print Invoice Button -->
        <button type="button" class="btn-ui-secondary px-4 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 inline-flex items-center gap-1.5 cursor-pointer shadow-2xs transition" onclick="printFolioInvoice()">
          <i class="fa-solid fa-print"></i> Print Invoice
        </button>
        <!-- Close Button -->
        <button type="button" class="btn-ui-secondary px-4 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-50 cursor-pointer shadow-2xs transition" onclick="closeFolioModal()">
          Close
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ========================================================================= -->
<!-- FOLIO GUEST DOCUMENT IMAGES VIEWER MODAL                                  -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="folio-documents-viewer-modal" style="z-index: 1050;">
  <div class="modal-window max-w-4xl w-full max-h-[92vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200 bg-white">
    <!-- Header -->
    <div class="bg-linear-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3.5 flex justify-between items-center shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-sm shadow-xs">
          <i class="fa-solid fa-images text-amber-300"></i>
        </div>
        <div>
          <h3 class="m-0 text-sm font-black text-white" id="fmdl-doc-viewer-modal-title">Guest Document Images</h3>
          <p class="m-0 text-[11px] text-indigo-100 font-medium" id="fmdl-doc-viewer-modal-subtitle">KYC &amp; Verification Documents</p>
        </div>
      </div>
      <button type="button" class="modal-close bg-white/20 hover:bg-white/30 text-white w-8 h-8 rounded-full text-lg cursor-pointer flex items-center justify-center transition border-none" onclick="closeModal('folio-documents-viewer-modal')">&times;</button>
    </div>

    <!-- Content Area -->
    <div class="p-5 overflow-y-auto flex-1 bg-slate-50" id="fmdl-doc-viewer-content-area">
      <div id="fmdl-doc-viewer-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5"></div>
      <div id="fmdl-doc-viewer-empty" class="hidden py-12 text-center">
        <div class="w-16 h-16 mx-auto bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center text-2xl mb-3">
          <i class="fa-solid fa-folder-open"></i>
        </div>
        <div class="text-sm font-extrabold text-slate-700">No Document Images Uploaded</div>
        <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">No photos or identity document scans have been uploaded for this guest yet.</p>
      </div>
    </div>

    <!-- Footer -->
    <div class="px-5 py-3 bg-white border-t border-slate-200 flex justify-between items-center shrink-0">
      <span class="text-xs font-bold text-slate-600 flex items-center gap-1.5">
        <i class="fa-solid fa-circle-check text-emerald-600"></i> Total Documents: <strong id="fmdl-doc-viewer-total-count" class="text-slate-900">0</strong>
      </span>
      <button type="button" class="btn-ui-secondary px-4 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-slate-100 cursor-pointer shadow-2xs" onclick="closeModal('folio-documents-viewer-modal')">
        Close
      </button>
    </div>
  </div>
</div>

<!-- ========================================================================= -->
<!-- CONFIRMATION MODAL: EARLY CHECK-OUT INDIVIDUAL GUEST                     -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="early-checkout-confirm-modal">
  <div class="modal-window max-w-md w-full rounded-2xl overflow-hidden shadow-2xl border border-slate-200 bg-white">
    
    <div class="bg-gradient-to-r from-rose-600 to-rose-700 px-5 py-3.5 flex justify-between items-center text-white border-b border-rose-500/30">
      <div class="flex items-center gap-2.5">
        <div class="w-8.5 h-8.5 rounded-lg bg-white/20 flex items-center justify-center text-sm text-white flex-shrink-0">
          <i class="fa-solid fa-person-walking-arrow-right"></i>
        </div>
        <div>
          <h4 class="m-0 text-sm font-extrabold text-white">Early Check-Out Confirmation</h4>
          <div class="text-[10px] text-rose-100">Individual Occupant Check-Out</div>
        </div>
      </div>
      <button type="button" onclick="closeEarlyCheckoutConfirmModal()" class="bg-white/20 hover:bg-white/30 text-white w-7 h-7 rounded-full text-base cursor-pointer flex items-center justify-center transition border-none">&times;</button>
    </div>

    <div class="p-5 bg-white">
      <div class="flex gap-3.5 items-start mb-4">
        <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-lg flex-shrink-0">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
          <div class="text-xs font-extrabold text-slate-900 mb-0.5">
            Are you sure you want to early check-out this guest?
          </div>
          <div class="text-[11px] text-slate-500 leading-relaxed">
            Only this particular occupant will be marked as checked out. Other occupants and room billing stay active.
          </div>
        </div>
      </div>

      <!-- Guest and Checkout Info Box -->
      <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs flex flex-col gap-2">
        <div class="flex justify-between items-center">
          <span class="text-slate-500 font-semibold">Guest Name:</span>
          <strong id="eccm-guest-name" class="text-slate-900 font-bold text-xs">-</strong>
        </div>
        <div class="flex justify-between items-center border-t border-dashed border-slate-200 pt-1.5">
          <span class="text-slate-500 font-semibold">Allotted Room:</span>
          <strong id="eccm-room-no" class="text-indigo-700 font-extrabold">-</strong>
        </div>
        <div class="flex justify-between items-center border-t border-dashed border-slate-200 pt-1.5">
          <span class="text-slate-500 font-semibold">Check-Out Date:</span>
          <span id="eccm-checkout-date" class="font-mono font-extrabold text-emerald-700">-</span>
        </div>
        <div class="flex justify-between items-center border-t border-dashed border-slate-200 pt-1.5">
          <span class="text-slate-500 font-semibold">Check-Out Time:</span>
          <span id="eccm-checkout-time" class="font-mono font-extrabold text-emerald-700">-</span>
        </div>
      </div>

      <input type="hidden" id="eccm-booking-id" value="">
    </div>

    <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5 items-center">
      <button type="button" onclick="closeEarlyCheckoutConfirmModal()" class="px-4 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer shadow-2xs transition">
        Cancel
      </button>
      <button type="button" id="eccm-btn-confirm" onclick="executeEarlyCheckout()" class="px-5 py-1.5 rounded-lg border-none bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold cursor-pointer inline-flex items-center gap-1.5 shadow-sm transition">
        <i class="fa-solid fa-check"></i> Yes, Early Check Out
      </button>
    </div>

  </div>
</div>

<!-- ========================================================================= -->
<!-- ROOM SWIPE / SHIFT MODAL                                                  -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="room-swipe-modal">
  <div class="room-swipe-card max-w-xl w-full max-h-[90vh] bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-slate-200">
    <div class="room-swipe-header bg-gradient-to-r from-indigo-700 to-indigo-900 px-5 py-3.5 flex justify-between items-center text-white border-b border-indigo-600/30">
      <div class="flex items-center gap-2.5">
        <div class="w-8.5 h-8.5 rounded-lg bg-white/20 flex items-center justify-center text-sm text-white flex-shrink-0">
          <i class="fa-solid fa-arrow-right-arrow-left"></i>
        </div>
        <div>
          <h4 class="m-0 text-sm font-extrabold text-white">Room Swipe / Shift Manager</h4>
          <div class="text-[10px] text-indigo-100">Transfer room mid-stay with prorated tariff &amp; housekeeping sync</div>
        </div>
      </div>
      <button type="button" onclick="closeRoomSwipeModal()" class="bg-white/20 hover:bg-white/30 text-white w-7 h-7 rounded-full text-base cursor-pointer flex items-center justify-center transition border-none">&times;</button>
    </div>

    <!-- Hidden state inputs -->
    <input type="hidden" id="rswipe-master-booking-id">
    <input type="hidden" id="rswipe-from-room-id">
    <input type="hidden" id="rswipe-old-rate">
    <input type="hidden" id="rswipe-checkout-date">

    <div class="room-swipe-body p-5 overflow-y-auto flex flex-col gap-3.5">
      <!-- Loading state -->
      <div id="rswipe-loading" class="flex flex-col items-center justify-center py-10 px-5 gap-2.5 text-indigo-700">
        <i class="fa-solid fa-circle-notch fa-spin text-2xl"></i>
        <span class="text-xs font-semibold">Fetching available rooms and stay info...</span>
      </div>

      <!-- Content wrapper -->
      <div id="rswipe-content" class="hidden flex-col gap-3.5">
        
        <!-- Reservation Dossier & Source Room Info Box -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
          <div class="flex justify-between items-center mb-2 flex-wrap gap-1.5">
            <div class="flex items-center gap-1.5">
              <span class="text-[10px] font-extrabold uppercase text-slate-500">Reservation:</span>
              <span id="rswipe-reserve-id" class="font-mono font-extrabold text-indigo-700 text-xs">-</span>
            </div>
            <div class="text-xs text-slate-500">
              <i class="fa-regular fa-calendar text-indigo-500"></i> Stay: <strong id="rswipe-stay-dates" class="text-slate-900">-</strong>
            </div>
          </div>
          <div class="flex justify-between items-center border-t border-dashed border-slate-200 pt-2 flex-wrap gap-2">
            <div class="flex items-center gap-2">
              <span class="room-tag status-cyan text-xs py-1 px-2.5">
                <i class="fa-solid fa-door-open"></i> Current Room: <strong id="rswipe-from-room-num">-</strong>
              </span>
              <span id="rswipe-from-room-meta" class="text-xs text-slate-600">-</span>
            </div>
            <div class="text-xs font-mono font-extrabold text-indigo-700">
              Current Rate: <span id="rswipe-from-room-rate">₹ 0.00</span> / night
            </div>
          </div>
        </div>

        <!-- Occupants to Transfer Checkbox Box -->
        <div>
          <div class="flex justify-between items-center mb-1.5">
            <label class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">
              <i class="fa-solid fa-users text-indigo-500"></i> Select Occupant(s) to Shift:
            </label>
            <label class="text-xs text-indigo-700 cursor-pointer font-bold inline-flex items-center gap-1">
              <input type="checkbox" id="rswipe-select-all-occ" onchange="toggleAllSwipeOccupants(this)" class="w-3.5 h-3.5 accent-indigo-600 rounded"> All in Room
            </label>
          </div>
          <div id="rswipe-occupants-list" class="flex flex-col gap-1.5">
            <!-- Populated via JS -->
          </div>
          <div id="rswipe-shift-mode-hint" class="text-[10.5px] text-slate-500 italic mt-1">
            Whole room shift: Old room vacated &amp; scheduled for cleaning.
          </div>
        </div>

        <!-- Stay Dates & Transfer Date Input Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              <i class="fa-regular fa-calendar-check text-indigo-600"></i> Effective Shift Date:
            </label>
            <input type="date" id="rswipe-transfer-date" class="form-input w-full py-1.5 px-2.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-900 outline-none focus:ring-2 focus:ring-indigo-500/20" onchange="onSwipeTransferDateChange()">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i> Nights Partition:
            </label>
            <div class="bg-slate-100 border border-slate-200 rounded-lg py-1.5 px-2.5 text-xs font-bold text-slate-700 flex justify-between items-center">
              <span>Past: <strong id="rswipe-past-nights">1</strong>n</span>
              <span class="text-slate-300">|</span>
              <span class="text-indigo-700">Remaining: <strong id="rswipe-rem-nights">1</strong>n</span>
            </div>
          </div>
        </div>

        <!-- New Destination Room Selection -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-start">
          <div class="sm:col-span-2">
            <label class="flex justify-between items-center text-xs font-bold text-slate-700 mb-1">
              <span><i class="fa-solid fa-door-open text-emerald-500"></i> Destination Room (Available Only):</span>
              <span id="rswipe-avail-count-badge" class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">Available Rooms Only</span>
            </label>
            <select id="rswipe-target-room-select" class="form-input w-full py-1.5 px-2.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-800 bg-white outline-none focus:ring-2 focus:ring-indigo-500/20">
              <option value="">-- Search &amp; Choose Available Room --</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              <i class="fa-solid fa-indian-rupee-sign text-emerald-500"></i> New Daily Rate:
            </label>
            <input type="number" id="rswipe-new-rate-input" step="0.01" min="0" class="form-input w-full py-1.5 px-2.5 rounded-lg border border-slate-300 text-xs font-bold font-mono text-slate-900 outline-none focus:ring-2 focus:ring-indigo-500/20" oninput="calculateSwipePricing()">
          </div>
        </div>

        <!-- Live Tariff Recalculation & Impact Preview Box -->
        <div class="swipe-preview-box bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col gap-2 text-xs" id="rswipe-preview-box">
          <div class="flex justify-between items-center font-extrabold text-xs text-slate-900 border-b border-dashed border-slate-200 pb-1.5">
            <span><i class="fa-solid fa-calculator text-indigo-600"></i> Tariff Impact Breakdown:</span>
            <span id="rswipe-preview-tag" class="bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-[10px]">Shift Calculations</span>
          </div>
          <div class="flex flex-col gap-1 text-xs text-slate-600">
            <div class="flex justify-between">
              <span>Past Stay in Room <span id="rswipe-prev-src-room">302</span>:</span>
              <strong id="rswipe-prev-past-calc" class="font-mono text-slate-800">-</strong>
            </div>
            <div class="flex justify-between">
              <span>New Stay in Room <span id="rswipe-prev-dst-room">-</span>:</span>
              <strong id="rswipe-prev-new-calc" class="font-mono text-slate-800">-</strong>
            </div>
            <div class="flex justify-between border-t border-dashed border-slate-200 pt-1 font-extrabold">
              <span>Net Tariff Difference (<span id="rswipe-prev-rem-n">1</span> nights):</span>
              <span id="rswipe-prev-diff" class="font-mono text-xs text-indigo-700 font-black">₹ 0.00</span>
            </div>
          </div>
          <!-- Housekeeping Notification Alert -->
          <div id="rswipe-hk-notice" class="bg-rose-50 border border-rose-200 rounded-lg p-2 text-xs text-rose-700 flex items-center gap-2">
            <i class="fa-solid fa-broom text-rose-500"></i>
            <span>Room <strong id="rswipe-hk-room-name">-</strong> will be automatically marked <strong>Dirty (Pending Cleaning)</strong> in Housekeeping history.</span>
          </div>
        </div>

        <!-- Reason & Remarks -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Reason for Shift:
            </label>
            <select id="rswipe-reason" class="form-input w-full py-1.5 px-2.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-800 bg-white outline-none focus:ring-2 focus:ring-indigo-500/20">
              <option value="Guest Upgrade">Guest Upgrade / Preference</option>
              <option value="Guest Request">Guest Request (View/Location)</option>
              <option value="AC / Electrical Issue">AC / Electrical Issue</option>
              <option value="Plumbing Issue">Plumbing / Water Issue</option>
              <option value="Noise Complaint">Noise / Disturbance Complaint</option>
              <option value="Operational Shift">Operational Front Desk Shift</option>
              <option value="Other">Other Reason</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">
              Staff Remarks (Optional):
            </label>
            <input type="text" id="rswipe-remarks" placeholder="Optional notes for transfer dossier..." class="form-input w-full py-1.5 px-2.5 rounded-lg border border-slate-300 text-xs text-slate-800 bg-white outline-none focus:ring-2 focus:ring-indigo-500/20">
          </div>
        </div>

      </div>
    </div>

    <!-- Modal Footer Actions -->
    <div class="bg-slate-50 border-t border-slate-200 px-5 py-3 flex justify-end gap-2.5 items-center">
      <button type="button" onclick="closeRoomSwipeModal()" class="px-4 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-600 text-xs font-bold hover:bg-slate-50 cursor-pointer shadow-2xs transition">
        Cancel
      </button>
      <button type="button" id="btn-submit-room-swipe" onclick="submitRoomSwipe()" class="px-5 py-1.5 rounded-lg border-none bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-extrabold cursor-pointer inline-flex items-center gap-1.5 shadow-sm transition">
        <i class="fa-solid fa-arrow-right-arrow-left"></i> Confirm Room Swipe
      </button>
    </div>

  </div>
</div>

<!-- Shared Extend Stay Modal -->
@include('frontoffice.includes.extend-stay-modal')

<!-- Hidden Clean Print Template Area -->
<div id="folio-print-area" class="hidden"></div>

@endsection

@push('scripts')
<script>
  window.allFoliosData = @json($serializedFolios);
  let activeFolioData = null;

  // Currency helper
  function formatINR(val) {
    return '₹ ' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // HTML escaping helper
  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Open Folio Modal by Booking ID (forceFresh = true fetches fresh DB data via AJAX, scrollToSwipe = true scrolls to rooms/occupants swipe section)
  function openFolioModalById(bookingId, forceFresh = false, scrollToSwipe = false) {
    let folio = (!forceFresh && Array.isArray(window.allFoliosData)) 
      ? window.allFoliosData.find(f => Number(f.id) === Number(bookingId)) 
      : null;

    if (folio) {
      populateAndOpenFolioModal(folio, scrollToSwipe);
    } else {
      // Fetch fresh via AJAX
      fetch(`{{ url('front-office/folio') }}/${bookingId}/details`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.folio) {
          if (!Array.isArray(window.allFoliosData)) {
            window.allFoliosData = [];
          }
          const idx = window.allFoliosData.findIndex(f => Number(f.id) === Number(bookingId));
          if (idx !== -1) {
            window.allFoliosData[idx] = data.folio;
          } else {
            window.allFoliosData.push(data.folio);
          }
          populateAndOpenFolioModal(data.folio, scrollToSwipe);
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Settlement Notice',
            text: 'Could not fetch settlement details.',
            confirmButtonColor: '#4f46e5'
          });
        }
      })
      .catch(err => {
        console.error('Error fetching settlement details:', err);
        Swal.fire({
          icon: 'error',
          title: 'System Error',
          text: 'Failed to load settlement details. Please try again.',
          confirmButtonColor: '#4f46e5'
        });
      });
    }
  }

  function openFolioModalForSwipe(bookingId) {
    openFolioModalById(bookingId, false, true);
  }

  function populateAndOpenFolioModal(folio, scrollToSwipe = false) {
    if (!folio) return;
    activeFolioData = folio;

    try {
      // Header info
      const elResId = document.getElementById('fmdl-reserve-id');
      if (elResId) elResId.textContent = folio.reserve_id || ('RES-' + folio.id);
      const elFolioNo = document.getElementById('fmdl-folio-number');
      if (elFolioNo) elFolioNo.textContent = folio.folio_number || '-';
      const elCreatedAt = document.getElementById('fmdl-created-at');
      if (elCreatedAt) elCreatedAt.textContent = folio.created_at_fmt || 'N/A';

      // Status Badge
      const statusBadge = document.getElementById('fmdl-status-badge');
      if (statusBadge) {
        const normStatus = (folio.status || 'Confirmed').toLowerCase();
        statusBadge.textContent = folio.status || 'Confirmed';
        statusBadge.className = 'status-badge ' + (
          ['settlement done', 'settled'].includes(normStatus) ? 'settled' :
          ['checked out', 'checkedout'].includes(normStatus) ? 'checkedout' :
          ['checked in', 'checkedin'].includes(normStatus) ? 'checkedin' :
          normStatus
        );
      }

      // Top highlight banner
      const hlRooms = document.getElementById('fmdl-highlight-rooms');
      if (hlRooms) {
        const roomsList = (folio.rooms || []).map(r => `${escapeHtml(r.room_number)} (Floor ${escapeHtml(r.floor)} • ${escapeHtml(r.category)})`).join(' • ');
        hlRooms.textContent = roomsList || 'Unassigned';
      }
      const elGuests = document.getElementById('fmdl-total-guests-count');
      if (elGuests) elGuests.textContent = `${folio.total_guests_count || 1} Person(s) (${(folio.rooms || []).length} Room(s))`;

      const formatFolioMealPlanHtml = (name, price, origPrice) => {
        if (!name) return 'EP';
        const clean = String(name).trim();
        const lower = clean.toLowerCase();
        const effPrice = Number(price || 0);
        const original = Number(origPrice !== undefined ? origPrice : effPrice);
        if (lower === 'none' || lower === 'no meal plan' || lower === 'room only (ep)' || lower === 'ep' || (effPrice <= 0 && original <= 0)) {
          return typeof escapeHtml === 'function' ? escapeHtml(clean) : clean;
        }
        const esc = typeof escapeHtml === 'function' ? escapeHtml(clean) : clean;
        if (original > effPrice) {
          return `${esc} (+<span class="line-through opacity-65 mr-1">₹${original.toLocaleString('en-IN')}</span><strong>₹${effPrice.toLocaleString('en-IN')}</strong>)`;
        }
        if (effPrice > 0) {
          return `${esc} (+₹${effPrice.toLocaleString('en-IN')})`;
        }
        return esc;
      };

      const elPlan = document.getElementById('fmdl-plan-name');
      if (elPlan) elPlan.innerHTML = formatFolioMealPlanHtml(folio.plan_name, folio.plan_price, folio.plan_original_price);
      const elNights = document.getElementById('fmdl-nights-sched');
      if (elNights) elNights.textContent = `${folio.total_nights || 1} Night(s) Stay`;
      const elResMode = document.getElementById('fmdl-reservation-mode');
      if (elResMode) elResMode.textContent = folio.reservation_mode || 'Front Desk';

      // Primary Guest Profile
      const pg = folio.primary_guest || {};
      const elName = document.getElementById('fmdl-guest-name');
      if (elName) elName.textContent = pg.name || 'Guest Profile Dossier';
      const cardName = document.getElementById('fmdl-card-name');
      if (cardName) cardName.textContent = pg.name || 'Guest';
      const cardRegType = document.getElementById('fmdl-card-regtype');
      if (cardRegType) cardRegType.textContent = folio.registration_type || 'Regular';
      const elMobile = document.getElementById('fmdl-guest-mobile');
      if (elMobile) elMobile.textContent = pg.mobile || 'N/A';
      const elEmail = document.getElementById('fmdl-guest-email');
      if (elEmail) elEmail.textContent = pg.email || 'N/A';
      const elCity = document.getElementById('fmdl-guest-city');
      if (elCity) elCity.textContent = pg.city || 'N/A';
      const elAddress = document.getElementById('fmdl-guest-address');
      if (elAddress) elAddress.textContent = pg.address || 'N/A';
      const elNat = document.getElementById('fmdl-guest-nationality');
      if (elNat) elNat.textContent = pg.nationality || 'Indian';
      const elIdProof = document.getElementById('fmdl-guest-idproof');
      if (elIdProof) elIdProof.textContent = (pg.id_card_type || 'ID Proof') + ': ' + (pg.id_card_number || 'N/A');

      // KYC Documents Count
      const folioDocs = Array.isArray(pg.documents) ? pg.documents : (Array.isArray(folio.documents) ? folio.documents : []);
      document.querySelectorAll('.fmdl-docs-count-pill').forEach(el => {
        el.textContent = folioDocs.length;
      });

      // Privilege / VIP Card
      const privRow = document.getElementById('fmdl-card-privilege-row');
      const privVal = document.getElementById('fmdl-card-privilege');
      if (privRow && privVal) {
        if (pg.has_privilege_card && pg.privilege_card_no) {
          privRow.style.display = 'flex';
          privVal.textContent = pg.privilege_card_no;
        } else {
          privRow.style.display = 'none';
        }
      }

      // Stay schedule
      const elCin = document.getElementById('fmdl-checkin-sched');
      if (elCin) elCin.textContent = `${folio.checkin_date_fmt || ''} at ${folio.checkin_time_fmt || ''}`;
      const elCout = document.getElementById('fmdl-checkout-sched');
      if (elCout) elCout.textContent = `${folio.checkout_date_fmt || ''} at ${folio.checkout_time_fmt || ''}`;
      const elDur = document.getElementById('fmdl-duration-stay');
      if (elDur) elDur.textContent = `${folio.total_nights || 1} Night(s)`;
      const elResModeVal = document.getElementById('fmdl-resmode-val');
      if (elResModeVal) elResModeVal.textContent = folio.reservation_mode || 'Front Desk';
      const elMealVal = document.getElementById('fmdl-mealplan-val');
      if (elMealVal) elMealVal.innerHTML = formatFolioMealPlanHtml(folio.plan_name, folio.plan_price, folio.plan_original_price);

      // Corporate Account Block
      const compRow = document.getElementById('fmdl-company-row');
      const compName = document.getElementById('fmdl-company-name');
      const compGstin = document.getElementById('fmdl-company-gstin');
      const compPhone = document.getElementById('fmdl-company-phone');
      if (compRow) {
        if (folio.company && folio.company.name) {
          compRow.style.display = 'block';
          if (compName) compName.textContent = folio.company.name;
          if (compGstin) compGstin.textContent = folio.company.gstin || 'N/A';
          if (compPhone) compPhone.textContent = folio.company.phone || 'N/A';
        } else {
          compRow.style.display = 'none';
        }
      }

      // Rooms & Occupants breakdown
      renderRoomsOccupants(folio.rooms || []);

      // Billings & Financial Ledger
      renderBillingsLedger(folio);

      // Configure Settle vs Checkout Buttons
      configureFolioActions(folio);

      // Reset settlement interactive panel
      const setPanel = document.getElementById('fmdl-settlement-interactive-panel');
      if (setPanel) setPanel.style.display = 'none';
    } catch (e) {
      console.error('Error populating folio modal details:', e);
    }

    // Reliably open modal window
    const modalEl = document.getElementById('folio-details-modal');
    if (modalEl) {
      modalEl.classList.add('open');
      modalEl.style.setProperty('display', 'flex', 'important');
      modalEl.style.setProperty('visibility', 'visible', 'important');
      modalEl.style.setProperty('opacity', '1', 'important');
      modalEl.style.setProperty('z-index', '100050', 'important');
    }
    if (typeof openModal === 'function') {
      try { openModal('folio-details-modal'); } catch (_) {}
    }
    document.body.style.overflow = 'hidden';

    // Reset or redirect scroll
    const modalBody = document.getElementById('folio-modal-body');
    if (modalBody && !scrollToSwipe) {
      modalBody.scrollTop = 0;
    }

    if (scrollToSwipe) {
      const performScrollToSwipe = () => {
        const bodyEl = document.getElementById('folio-modal-body');
        const targetSection = document.getElementById('fmdl-card-rooms-occupants');
        if (bodyEl && targetSection) {
          const cardOffset = targetSection.offsetTop;
          bodyEl.scrollTo({
            top: Math.max(0, cardOffset - 15),
            behavior: 'smooth'
          });

          // Highlight the rooms/occupants card with a pulse effect
          targetSection.classList.remove('pulse-swipe-highlight');
          void targetSection.offsetWidth;
          targetSection.classList.add('pulse-swipe-highlight');

          // Highlight the swipe / shift buttons
          const swipeBtns = targetSection.querySelectorAll('.btn-room-swipe, .btn-room-swipe-mini');
          swipeBtns.forEach(btn => btn.classList.add('pulse-swipe-btn'));

          setTimeout(() => {
            targetSection.classList.remove('pulse-swipe-highlight');
            swipeBtns.forEach(btn => btn.classList.remove('pulse-swipe-btn'));
          }, 2500);
        }
      };

      // Ensure content is rendered and layout settled
      setTimeout(performScrollToSwipe, 80);
      setTimeout(performScrollToSwipe, 260);
    }
  }

  // Open Folio Document Images Viewer Modal
  function openCurrentFolioDocsModal() {
    if (!activeFolioData) return;
    const f = activeFolioData;
    const pg = f.primary_guest || {};
    const docs = Array.isArray(pg.documents) ? pg.documents : (Array.isArray(f.documents) ? f.documents : []);

    const titleElem = document.getElementById('fmdl-doc-viewer-modal-title');
    const subElem = document.getElementById('fmdl-doc-viewer-modal-subtitle');
    const countElem = document.getElementById('fmdl-doc-viewer-total-count');

    if (titleElem) titleElem.textContent = (pg.name || 'Primary Guest') + ' - Document Images';
    if (subElem) subElem.textContent = `Booking Ref: ${f.reserve_id || ('RES-' + f.id)} • Folio: ${f.folio_number || '-'} • ${docs.length} Document(s)`;
    if (countElem) countElem.textContent = docs.length;

    const grid = document.getElementById('fmdl-doc-viewer-grid');
    const empty = document.getElementById('fmdl-doc-viewer-empty');

    if (!grid || !empty) return;

    if (docs.length === 0) {
      grid.innerHTML = '';
      empty.classList.remove('hidden');
    } else {
      empty.classList.add('hidden');
      grid.innerHTML = docs.map((doc, idx) => `
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs flex flex-col group hover:border-indigo-300 transition">
          <div class="relative bg-slate-900/5 aspect-4/3 overflow-hidden flex items-center justify-center p-2">
            <a href="${doc.url}" target="_blank" title="Click to view full image in new tab" class="block w-full h-full flex items-center justify-center">
              <img src="${doc.url}" alt="${escapeHtml(doc.name || 'Document')}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-300 rounded">
            </a>
            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-black/75 text-white backdrop-blur-xs">
              ${escapeHtml(doc.type || 'Document')}
            </span>
          </div>
          <div class="p-3 bg-white border-t border-slate-100 flex-1 flex flex-col justify-between">
            <div>
              <div class="text-xs font-black text-slate-800 truncate" title="${escapeHtml(doc.name || 'Document Image')}">${escapeHtml(doc.name || 'Document Image')}</div>
              <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1 font-mono">
                <i class="fa-regular fa-clock text-[9px]"></i> ${doc.uploaded_at ? new Date(doc.uploaded_at).toLocaleString('en-IN', { dateStyle: 'medium', timeStyle: 'short' }) : 'Verified KYC'}
              </div>
            </div>
            <div class="flex items-center gap-2 mt-2.5 pt-2 border-t border-slate-100">
              <a href="${doc.url}" target="_blank" class="flex-1 py-1 px-2 text-[10px] font-bold text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md transition flex items-center justify-center gap-1">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Open Full View
              </a>
              <a href="${doc.url}" download="${escapeHtml(doc.name || 'document')}" class="py-1 px-2.5 text-[10px] font-bold bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-md transition" title="Download Image">
                <i class="fa-solid fa-download"></i>
              </a>
            </div>
          </div>
        </div>
      `).join('');
    }

    openModal('folio-documents-viewer-modal');
  }

  function renderRoomsOccupants(rooms) {
    const container = document.getElementById('fmdl-rooms-container');
    if (!rooms || rooms.length === 0) {
      container.innerHTML = '<div class="text-slate-400 text-[11px] italic">No specific room allocated.</div>';
      return;
    }

    // Total occupants registered across all rooms under this reservation
    let totalReservationOccupants = 0;
    rooms.forEach(r => {
      totalReservationOccupants += (r.occupants ? r.occupants.length : 0);
    });

    // Check if reservation has multiple guests and if settlement is complete
    const hasMultiple = totalReservationOccupants > 1;
    const normFolioStatus = (activeFolioData?.status || '').toLowerCase();
    const isSettled = activeFolioData && (Number(activeFolioData.balance) <= 0.01 || ['settlement done', 'settled'].includes(normFolioStatus));
    const isCompanyFolio = activeFolioData && ((activeFolioData.registration_type || '').toLowerCase() === 'company' || !!activeFolioData.company);
    const canEarlyCheckout = isSettled || isCompanyFolio;

    let html = '';
    rooms.forEach(r => {
      const isVacated = r.is_vacated === true;
      const roomStatusBadge = isVacated
        ? `<span class="bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-extrabold px-2 py-0.5 rounded-md inline-flex items-center gap-1"><i class="fa-solid fa-broom"></i> Vacated (Housekeeping Dirty)</span>`
        : `<span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold px-2 py-0.5 rounded-md inline-flex items-center gap-1"><i class="fa-solid fa-bed"></i> Reserved / Occupied</span>`;

      const canSwipe = activeFolioData && 
        activeFolioData.status !== 'Checked Out' && 
        activeFolioData.status !== 'Cancelled' && 
        !isSettled && 
        !isVacated && 
        r.room_id;
      const swipeBtnMarkup = canSwipe ? `
        <button type="button" 
                class="btn-room-swipe" 
                onclick="openRoomSwipeModal(${activeFolioData.id}, ${r.room_id}, '${escapeHtml(r.room_number).replace(/'/g, "\\'")}')" 
                title="Swipe / Shift Room ${escapeHtml(r.room_number)}">
          <i class="fa-solid fa-arrow-right-arrow-left"></i> Room Swipe
        </button>
      ` : '';

      html += `
        <div class="room-alloc-card">
          <div class="room-alloc-header flex justify-between items-center flex-wrap gap-2">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="room-tag">
                <i class="fa-solid fa-door-open"></i> ${escapeHtml(r.room_number)}
              </span>
              <span class="text-xs font-extrabold text-slate-900">Floor ${escapeHtml(r.floor)}</span>
              <span class="text-slate-300">•</span>
              <span class="text-[11px] text-slate-500 font-semibold">${escapeHtml(r.category)}</span>
              <span class="text-slate-300">•</span>
              <span class="text-[11px] text-slate-500">${escapeHtml(r.bedding)}</span>
              ${roomStatusBadge}
            </div>
            <div class="flex items-center gap-2">
              <span class="text-xs font-mono font-extrabold text-indigo-700">
                ${formatINR(r.rate)} / night
              </span>
              ${swipeBtnMarkup}
            </div>
          </div>
          <div>
            <div class="text-[10px] font-extrabold text-slate-500 uppercase mb-1.5">
              Registered Occupants in this Room:
            </div>
            <div class="flex flex-col gap-1">
      `;

      if (r.occupants && r.occupants.length > 0) {
        r.occupants.forEach(occ => {
          const isCheckedOut = occ.is_checked_out || (occ.status === 'Checked Out');
          
          let actionMarkup = '';
          if (hasMultiple) {
            if (isCheckedOut) {
              const dtParts = [];
              if (occ.checkout_date) dtParts.push(occ.checkout_date);
              if (occ.checkout_time) dtParts.push(occ.checkout_time);
              const dtText = dtParts.join(' ');
              actionMarkup = `
                <span class="badge-checked-out-occupant" title="This occupant is checked out">
                  <i class="fa-solid fa-circle-check text-emerald-500"></i> Checked Out ${dtText ? '(' + escapeHtml(dtText) + ')' : ''}
                </span>
              `;
            } else if (canEarlyCheckout && activeFolioData && activeFolioData.status !== 'Checked Out' && activeFolioData.status !== 'Cancelled') {
              actionMarkup = `
                <button type="button" 
                        class="btn-early-checkout" 
                        onclick="openEarlyCheckoutConfirmModal(${occ.booking_id}, '${escapeHtml(occ.name).replace(/'/g, "\\'")}', '${escapeHtml(r.room_number).replace(/'/g, "\\'")}')" 
                        title="Early Check-out this specific guest from Room ${escapeHtml(r.room_number)}">
                  <i class="fa-solid fa-person-walking-arrow-right"></i> Early Checkout
                </button>
              `;
            }
          } else if (isCheckedOut) {
            const dtParts = [];
            if (occ.checkout_date) dtParts.push(occ.checkout_date);
            if (occ.checkout_time) dtParts.push(occ.checkout_time);
            const dtText = dtParts.join(' ');
            actionMarkup = `
              <span class="badge-checked-out-occupant" title="This occupant is checked out">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> Checked Out ${dtText ? '(' + escapeHtml(dtText) + ')' : ''}
              </span>
            `;
          }

          const swipeGuestMarkup = (canSwipe && !isCheckedOut) ? `
            <button type="button" 
                    class="btn-room-swipe-mini" 
                    onclick="openRoomSwipeModal(${activeFolioData.id}, ${r.room_id}, '${escapeHtml(r.room_number).replace(/'/g, "\\'")}', ${occ.booking_id})" 
                    title="Swipe / Transfer ${escapeHtml(occ.name)} to another room">
              <i class="fa-solid fa-arrow-right-arrow-left"></i> Shift
            </button>
          ` : '';

          html += `
            <div class="occupant-badge-row ${isCheckedOut ? 'checked-out' : ''}">
              <div class="flex items-center gap-1.5">
                <i class="fa-solid ${occ.is_primary ? 'fa-star text-amber-500' : 'fa-user text-indigo-500'} ${isCheckedOut ? 'opacity-50' : ''}"></i>
                <strong class="${isCheckedOut ? 'text-slate-500 line-through' : 'text-slate-800'}">${escapeHtml(occ.name)}</strong>
                ${occ.is_primary ? '<span class="bg-amber-100 text-amber-800 text-[9px] font-extrabold px-1.5 py-0.2 rounded">Primary</span>' : ''}
              </div>
              <div class="flex items-center gap-2.5 text-slate-500 font-mono text-xs">
                <span><i class="fa-solid fa-phone text-[9px] text-emerald-500"></i> ${escapeHtml(occ.mobile)}</span>
                <span class="text-slate-300">•</span>
                <span>${escapeHtml(occ.id_card_type)}: ${escapeHtml(occ.id_card_number)}</span>
                ${swipeGuestMarkup}
                ${actionMarkup}
              </div>
            </div>
          `;
        });
      } else {
        html += '<div class="text-xs text-slate-400 italic">No occupants assigned yet.</div>';
      }

      html += `
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
  }

  function renderBillingsLedger(folio) {
    const totalServiceCost = Number(folio.total_room_service_charges || 0);
    const roomGross = Number(folio.room_total_amount !== undefined ? folio.room_total_amount : (folio.total_amount || 0));
    const roomPayable = Number(folio.room_payable_amount !== undefined ? folio.room_payable_amount : (folio.payable_amount || 0));
    const grossTotal = roomGross + totalServiceCost;
    const netPayableTotal = roomPayable + totalServiceCost;

    const billRateEl = document.getElementById('fmdl-bill-rate');
    if (billRateEl) billRateEl.textContent = formatINR(folio.room_rate);

    document.getElementById('fmdl-gross-amount').textContent = formatINR(grossTotal);
    document.getElementById('fmdl-totalpaid-amount').textContent = formatINR(folio.total_paid);

    const balanceBox = document.getElementById('fmdl-balance-box');
    const balanceAmountEl = document.getElementById('fmdl-balance-amount');
    const balanceLabelEl = document.getElementById('fmdl-balance-label');

    balanceAmountEl.textContent = formatINR(folio.balance);
    if (folio.balance > 0.01) {
      balanceAmountEl.style.color = '#e11d48';
      balanceLabelEl.textContent = 'Balance Due';
    } else {
      balanceAmountEl.style.color = '#059669';
      balanceLabelEl.textContent = 'Fully Settled';
    }

    // Breakdown details
    document.getElementById('fmdl-calc-row').textContent = `${formatINR(folio.room_rate)} × ${folio.total_nights} Nights = ${formatINR(roomGross)}`;
    document.getElementById('fmdl-advance-row').textContent = formatINR(folio.advance_amount);
    document.getElementById('fmdl-settlement-row').textContent = formatINR(folio.settlement_paid);
    
    const totalPaidRow = document.getElementById('fmdl-totalpaid-row');
    if (totalPaidRow) totalPaidRow.textContent = formatINR(folio.total_paid);

    document.getElementById('fmdl-discount-row').textContent = folio.discount_amount > 0 ? `${formatINR(folio.discount_amount)} (${folio.discount_percentage}%)` : 'None';
    document.getElementById('fmdl-tax-row').textContent = folio.gst_amount > 0 ? `${formatINR(folio.gst_amount)} (${folio.gst_percentage}% ${folio.gst_type})` : 'Zero Tax / Exempt';
    
    const payableEl = document.getElementById('fmdl-payable-amount');
    if (payableEl) {
      if (totalServiceCost > 0) {
        payableEl.textContent = `${formatINR(netPayableTotal)} (Room: ${formatINR(roomPayable)} + Services: ${formatINR(totalServiceCost)})`;
      } else {
        payableEl.textContent = formatINR(netPayableTotal);
      }
    }

    document.getElementById('fmdl-paymode-row').textContent = folio.payment_mode || 'Cash / Direct';
    document.getElementById('fmdl-remarks-row').textContent = folio.payment_remarks || 'None';

    // In-Room Dining & Additional Services row
    const servicesRow = document.getElementById('fmdl-services-field-row');
    const servicesVal = document.getElementById('fmdl-services-row');
    const servicesBox = document.getElementById('fmdl-room-services-box');
    const servicesList = document.getElementById('fmdl-room-services-list');
    const servicesCount = document.getElementById('fmdl-services-count');

    if (totalServiceCost > 0) {
      if (servicesRow) servicesRow.style.display = 'flex';
      if (servicesVal) servicesVal.textContent = `+ ${formatINR(totalServiceCost)}`;
    } else {
      if (servicesRow) servicesRow.style.display = 'none';
    }

    // Render itemized In-Room Dining orders list
    if (folio.room_service_orders && folio.room_service_orders.length > 0) {
      if (servicesBox) servicesBox.style.display = 'block';
      if (servicesCount) servicesCount.textContent = folio.room_service_orders.length;

      let ordHtml = `
        <table class="w-full border-collapse bg-white border border-amber-200 rounded-lg overflow-hidden mb-1">
          <thead>
            <tr class="bg-amber-50 border-b border-amber-200 text-left">
              <th class="py-1.5 px-2 text-[10px] text-amber-800 w-[35px] text-center font-bold">SL</th>
              <th class="py-1.5 px-2.5 text-[10px] text-amber-800 font-bold">Order #</th>
              <th class="py-1.5 px-2.5 text-[10px] text-amber-800 font-bold">Date &amp; Time</th>
              <th class="py-1.5 px-2.5 text-[10px] text-amber-800 font-bold">Ordered Items</th>
              <th class="py-1.5 px-2.5 text-[10px] text-amber-800 text-center font-bold">Status</th>
              <th class="py-1.5 px-2.5 text-[10px] text-amber-800 text-right font-bold">Bill Amount</th>
            </tr>
          </thead>
          <tbody>
      `;

      const totalFolioCharged = (folio.room_service_orders || [])
        .filter(ro => ro.payment_status === 'Charged to Room Folio')
        .reduce((sum, ro) => sum + Number(ro.total_amount || 0), 0);
      const totalDirectPaid = (folio.room_service_orders || [])
        .filter(ro => ro.payment_status && ro.payment_status !== 'Charged to Room Folio')
        .reduce((sum, ro) => sum + Number(ro.total_amount || 0), 0);

      folio.room_service_orders.forEach((ro, oIdx) => {
        let itemsDetail = (ro.items || []).map(i => `${i.quantity}x ${escapeHtml(i.item_name)} (${formatINR(i.unit_price)})`).join(', ');
        const isPaid = (ro.payment_status && ro.payment_status !== 'Charged to Room Folio' && ro.payment_status !== 'Pending');
        const isFolio = (ro.payment_status === 'Charged to Room Folio');

        let statusBadge = '';
        let amountCol = '';

        if (isPaid) {
          statusBadge = `<span class="inline-flex items-center gap-1 py-0.5 px-1.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-circle-check"></i> PAID (${escapeHtml(ro.payment_status)})</span>`;
          amountCol = `<div class="font-mono font-extrabold text-emerald-600 text-[11px]">${formatINR(ro.total_amount)}</div><span class="text-[9px] text-emerald-600 font-bold">(Settled Direct)</span>`;
        } else if (isFolio) {
          statusBadge = `<span class="inline-flex items-center gap-1 py-0.5 px-1.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200"><i class="fa-solid fa-clock"></i> UNPAID (Charged to Room)</span>`;
          amountCol = `<div class="font-mono font-extrabold text-orange-600 text-[11px]">+ ${formatINR(ro.total_amount)}</div><span class="text-[9px] text-orange-700 font-bold">(Due at Checkout)</span>`;
        } else {
          statusBadge = `<span class="inline-block py-0.5 px-1.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">${escapeHtml(ro.payment_status || ro.order_status || 'Pending')}</span>`;
          amountCol = `<span class="font-mono font-extrabold text-slate-600">${formatINR(ro.total_amount)}</span>`;
        }

        ordHtml += `
          <tr class="border-b border-orange-100">
            <td class="py-1.5 px-2 text-center font-extrabold font-mono text-amber-800 text-[10px]">${oIdx + 1}</td>
            <td class="py-1.5 px-2.5 font-mono font-bold text-orange-700">
              ${escapeHtml(ro.order_number)}
              ${ro.table_number ? `<span class="text-[9px] bg-indigo-100 text-indigo-700 border border-indigo-200 py-px px-1.5 rounded font-bold ml-1 font-sans inline-flex items-center gap-0.5"><i class="fa-solid fa-chair text-[8px]"></i> ${escapeHtml(ro.table_number)}</span>` : ''}
            </td>
            <td class="py-1.5 px-2.5 text-slate-600 whitespace-nowrap">
              <i class="fa-regular fa-calendar-days text-[9px] text-amber-600"></i> ${escapeHtml(ro.date)}
            </td>
            <td class="py-1.5 px-2.5 text-slate-800 text-[10.5px]">${itemsDetail || escapeHtml(ro.items_summary) || 'Room Dining Service'}</td>
            <td class="py-1.5 px-2.5 text-center">
              ${statusBadge}
            </td>
            <td class="py-1.5 px-2.5 text-right whitespace-nowrap">${amountCol}</td>
          </tr>
        `;
      });
      ordHtml += `
          </tbody>
          <tfoot>
            <tr class="bg-amber-50 font-extrabold border-t-[1.5px] border-amber-200">
              <td colspan="4" class="p-2 text-[10px] text-amber-900">
                <strong>TOTAL DINING &amp; EXTRA SERVICES: ${folio.room_service_orders.length} ORDER(S)</strong>
                ${totalDirectPaid > 0 ? `<span class="ml-2.5 text-[9.5px] text-emerald-700 font-extrabold bg-emerald-100 py-0.5 px-1.5 rounded"><i class="fa-solid fa-circle-check"></i> Direct Settled: ${formatINR(totalDirectPaid)}</span>` : ''}
              </td>
              <td class="p-2 text-center text-[10px] text-amber-900 uppercase">
                Folio Due:
              </td>
              <td class="p-2 text-right font-mono text-orange-700 text-[11.5px] whitespace-nowrap">
                + ${formatINR(totalFolioCharged)}
              </td>
            </tr>
          </tfoot>
        </table>
      `;
      if (servicesList) servicesList.innerHTML = ordHtml;
    } else {
      if (servicesBox) servicesBox.style.display = 'none';
      if (servicesList) servicesList.innerHTML = '';
    }

    // Settlement History List
    const historyBox = document.getElementById('fmdl-settle-history-box');
    const historyList = document.getElementById('fmdl-settle-history-list');
    if (folio.settlements_history && folio.settlements_history.length > 0) {
      historyBox.style.display = 'block';
      let hHtml = `
        <table class="w-full border-collapse bg-white border border-slate-200 rounded-lg overflow-hidden text-xs">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-left">
              <th class="py-1.5 px-2.5 text-[10px] text-slate-500 font-bold">Receipt #</th>
              <th class="py-1.5 px-2.5 text-[10px] text-slate-500 font-bold">Date &amp; Time</th>
              <th class="py-1.5 px-2.5 text-[10px] text-slate-500 font-bold">Method</th>
              <th class="py-1.5 px-2.5 text-[10px] text-slate-500 text-right font-bold">Amount Paid</th>
              <th class="py-1.5 px-2.5 text-[10px] text-slate-500 font-bold">Remarks</th>
            </tr>
          </thead>
          <tbody>
      `;
      folio.settlements_history.forEach(sh => {
        hHtml += `
          <tr class="border-b border-slate-100">
            <td class="py-1.5 px-2.5 font-mono font-bold text-indigo-600">${escapeHtml(sh.settlement_number)}</td>
            <td class="py-1.5 px-2.5 text-slate-700">${escapeHtml(sh.date)} ${escapeHtml(sh.time)}</td>
            <td class="py-1.5 px-2.5 font-bold text-slate-900">${escapeHtml(sh.payment_mode)}</td>
            <td class="py-1.5 px-2.5 text-right font-mono font-extrabold text-emerald-600">${formatINR(sh.amount)}</td>
            <td class="py-1.5 px-2.5 text-slate-500">${escapeHtml(sh.remarks)}</td>
          </tr>
        `;
      });
      hHtml += '</tbody></table>';
      historyList.innerHTML = hHtml;
    } else {
      historyBox.style.display = 'none';
      historyList.innerHTML = '';
    }

    // Room Transfers (Swipe) History List
    const swipesBox = document.getElementById('fmdl-room-swipes-box');
    const swipesList = document.getElementById('fmdl-room-swipes-list');
    const swipesCount = document.getElementById('fmdl-swipes-count');
    if (folio.room_transfers && folio.room_transfers.length > 0) {
      if (swipesBox) swipesBox.style.display = 'block';
      if (swipesCount) swipesCount.textContent = folio.room_transfers.length;
      let sHtml = `
        <table class="w-full border-collapse bg-white border border-indigo-200 rounded-lg overflow-hidden mb-1 text-xs">
          <thead>
            <tr class="bg-indigo-50 border-b border-indigo-200 text-left">
              <th class="py-1.5 px-2.5 text-[10px] text-indigo-700 font-bold">Date &amp; Time</th>
              <th class="py-1.5 px-2.5 text-[10px] text-indigo-700 font-bold">From Room</th>
              <th class="py-1.5 px-2.5 text-[10px] text-indigo-700 font-bold">To Room</th>
              <th class="py-1.5 px-2.5 text-[10px] text-indigo-700 font-bold">Guests Transferred</th>
              <th class="py-1.5 px-2.5 text-[10px] text-indigo-700 text-right font-bold">Daily Rates</th>
              <th class="py-1.5 px-2.5 text-[10px] text-indigo-700 text-right font-bold">Tariff Adjustment</th>
              <th class="py-1.5 px-2.5 text-[10px] text-indigo-700 font-bold">Reason / Staff</th>
            </tr>
          </thead>
          <tbody>
      `;
      folio.room_transfers.forEach(st => {
        const diffPrefix = st.tariff_difference >= 0 ? '+' : '';
        const diffColorClass = st.tariff_difference > 0 ? 'text-indigo-700' : (st.tariff_difference < 0 ? 'text-emerald-600' : 'text-slate-500');
        const gList = Array.isArray(st.guests) ? st.guests.join(', ') : (st.guests || 'Occupants');
        sHtml += `
          <tr class="border-b border-indigo-50">
            <td class="py-1.5 px-2.5 text-slate-700 whitespace-nowrap"><i class="fa-regular fa-calendar-days text-[9px] text-indigo-500"></i> ${escapeHtml(st.date)} ${escapeHtml(st.time)}</td>
            <td class="py-1.5 px-2.5 font-bold text-rose-600"><i class="fa-solid fa-door-closed"></i> ${escapeHtml(st.from_room)}</td>
            <td class="py-1.5 px-2.5 font-bold text-emerald-600"><i class="fa-solid fa-door-open"></i> ${escapeHtml(st.to_room)}</td>
            <td class="py-1.5 px-2.5 text-slate-900 text-[10.5px]">${escapeHtml(gList)}</td>
            <td class="py-1.5 px-2.5 text-right font-mono text-[10px] text-slate-600 whitespace-nowrap">
              ${formatINR(st.old_rate)} &rarr; ${formatINR(st.new_rate)}
            </td>
            <td class="py-1.5 px-2.5 text-right font-mono font-extrabold ${diffColorClass} whitespace-nowrap">
              ${diffPrefix}${formatINR(st.tariff_difference)} (${st.remaining_nights}n)
            </td>
            <td class="py-1.5 px-2.5 text-slate-500 text-[10px]">${escapeHtml(st.reason || 'Shift')} <span class="text-slate-400">(${escapeHtml(st.staff)})</span></td>
          </tr>
        `;
      });
      sHtml += '</tbody></table>';
      if (swipesList) swipesList.innerHTML = sHtml;
    } else {
      if (swipesBox) swipesBox.style.display = 'none';
      if (swipesList) swipesList.innerHTML = '';
    }
  }

  function configureFolioActions(folio) {
    const btnSettle = document.getElementById('fmdl-action-btn-settle');
    const btnCheckout = document.getElementById('fmdl-action-btn-checkout');
    const badgeLocked = document.getElementById('fmdl-badge-locked');

    const isCheckedOut = (folio.is_all_checked_out !== undefined) 
      ? folio.is_all_checked_out 
      : ((folio.status || '').toLowerCase() === 'checked out');
    const isSettled = (folio.status || '').toLowerCase() === 'settlement done' || Number(folio.balance) <= 0.01;

    if (isCheckedOut) {
      btnSettle.style.display = 'none';
      btnCheckout.style.display = 'none';
      badgeLocked.style.display = 'inline-flex';
      badgeLocked.innerHTML = '<i class="fa-solid fa-lock text-slate-500"></i> Stay Finalized &amp; Checked Out (Locked)';
    } else if (folio.balance > 0.01) {
      // Balance Due: Show Settle Button, Hide Checkout
      btnSettle.style.display = 'inline-flex';
      btnSettle.innerHTML = `<i class="fa-solid fa-calculator"></i> Settle Balance (${formatINR(folio.balance)})`;
      btnCheckout.style.display = 'none';
      badgeLocked.style.display = 'none';
    } else {
      // Balance is Cleared: Settlement is Done -> Show Check Out Button!
      btnSettle.style.display = 'none';
      btnCheckout.style.display = 'inline-flex';
      btnCheckout.innerHTML = '<i class="fa-solid fa-right-from-bracket"></i> Check Out';
      badgeLocked.style.display = 'none';
    }
  }

  function renderSettlementBreakdown(folio) {
    const box = document.getElementById('fmdl-settle-breakdown-box');
    if (!box) return;

    const roomDue = Math.max(0, Number(folio.room_balance !== undefined ? folio.room_balance : ((folio.room_payable_amount || folio.payable_amount || 0) - Number(folio.total_paid || 0))));
    const serviceCost = Number(folio.total_room_service_charges || 0);
    const totalDue = Number(folio.balance || 0);
    const orders = folio.room_service_orders || [];

    let html = `
      <div class="bg-emerald-50 border border-emerald-300 rounded-lg p-3 text-xs">
        <div class="font-extrabold text-xs text-emerald-800 mb-2 uppercase tracking-wider flex justify-between items-center">
          <span><i class="fa-solid fa-file-invoice-dollar"></i> Settlement Balance Breakdown</span>
          <span class="text-xs font-extrabold text-emerald-700">Total Due: ${formatINR(totalDue)}</span>
        </div>
        <div class="flex flex-col gap-1">
          <div class="flex justify-between py-1 border-b border-dashed border-emerald-200 text-slate-800">
            <span><i class="fa-solid fa-bed text-indigo-500 w-3.5"></i> Room Charges Outstanding Balance:</span>
            <span class="font-mono font-bold">${formatINR(roomDue)}</span>
          </div>
    `;

    if (orders.length > 0) {
      html += `
        <div class="mt-1">
          <div class="font-extrabold text-amber-600 text-[11px] mb-1">
            <i class="fa-solid fa-bell-concierge w-3.5"></i> In-Room Dining &amp; Extra Services (${orders.length} order${orders.length > 1 ? 's' : ''}):
          </div>
          <div class="flex flex-col gap-1 pl-2">
      `;
      orders.forEach((ord, i) => {
        let itemsTxt = (ord.items || []).map(it => `${it.quantity}x ${escapeHtml(it.item_name)}`).join(', ');
        html += `
          <div class="flex justify-between items-center bg-amber-50/70 border border-amber-100 rounded-md py-1 px-2 text-[11px]">
            <div>
              <span class="font-extrabold text-amber-700 font-mono">${escapeHtml(ord.order_number)}</span>
              <span class="text-slate-500 text-[10px] ml-1"><i class="fa-regular fa-calendar-days text-[8px]"></i> ${escapeHtml(ord.date)}</span>
              <div class="text-[10px] text-slate-600">${itemsTxt || escapeHtml(ord.items_summary) || 'Extra Service'}</div>
            </div>
            <div class="font-mono font-extrabold text-amber-700 whitespace-nowrap">
              + ${formatINR(ord.total_amount)}
            </div>
          </div>
        `;
      });
      html += `
          </div>
          <div class="flex justify-between pt-1 pb-0.5 font-bold text-amber-700 text-[11px]">
            <span>Total Extra Services:</span>
            <span class="font-mono">+ ${formatINR(serviceCost)}</span>
          </div>
        </div>
      `;
    }

    html += `
        <div class="flex justify-between pt-1.5 border-t-[1.5px] border-emerald-300 mt-1 font-black text-[12.5px] text-emerald-800">
          <span><i class="fa-solid fa-cash-register w-3.5"></i> Net Outstanding Due to Settle:</span>
          <span class="font-mono ${totalDue > 0.01 ? 'text-rose-700' : 'text-emerald-600'}">${formatINR(totalDue)}</span>
        </div>
      </div>
    </div>
    `;

    box.innerHTML = html;
  }

  function toggleFolioSettlementPanel() {
    const panel = document.getElementById('fmdl-settlement-interactive-panel');
    if (panel.style.display === 'none' || panel.style.display === '') {
      panel.style.display = 'block';
      if (activeFolioData) {
        document.getElementById('fmdl-input-settle-amount').value = Number(activeFolioData.balance || 0).toFixed(2);
        document.getElementById('fmdl-btn-payfull-val').textContent = formatINR(activeFolioData.balance);
        renderSettlementBreakdown(activeFolioData);
      }
      panel.scrollIntoView({ behavior: 'smooth' });
    } else {
      panel.style.display = 'none';
    }
  }

  function setFullFolioSettlementAmount() {
    if (activeFolioData && activeFolioData.balance > 0) {
      document.getElementById('fmdl-input-settle-amount').value = Number(activeFolioData.balance).toFixed(2);
    }
  }

  function executeFolioSettlement() {
    if (!activeFolioData) return;

    const amountInput = document.getElementById('fmdl-input-settle-amount');
    const settleAmount = parseFloat(amountInput.value);
    const modeSelect = document.getElementById('fmdl-select-settle-mode');
    const txnInput = document.getElementById('fmdl-input-settle-txn');
    const remarksInput = document.getElementById('fmdl-input-settle-remarks');
    const confirmBtn = document.getElementById('fmdl-btn-confirm-settlement');

    if (isNaN(settleAmount) || settleAmount <= 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Invalid Amount',
        text: 'Please enter a valid settlement amount greater than 0.',
        confirmButtonColor: '#4f46e5'
      });
      amountInput.focus();
      return;
    }

    if (settleAmount > (Number(activeFolioData.balance) + 0.01)) {
      Swal.fire({
        icon: 'warning',
        title: 'Amount Exceeded',
        text: `Settlement amount cannot exceed the current balance of ${formatINR(activeFolioData.balance)}.`,
        confirmButtonColor: '#4f46e5'
      });
      amountInput.focus();
      return;
    }

    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

    fetch(`{{ url('front-office/folio') }}/${activeFolioData.id}/settle`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        amount: settleAmount,
        payment_mode_id: modeSelect.value,
        transaction_id: txnInput.value.trim(),
        remarks: remarksInput.value.trim(),
      })
    })
    .then(res => res.json())
    .then(data => {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Confirm &amp; Save Settlement';

      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Settlement Saved!',
          text: data.message || 'Settlement processed successfully!',
          confirmButtonColor: '#10b981',
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.reload();
        });
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Settlement Notice',
          text: data.error || data.message || 'Settlement failed. Please check your inputs.',
          confirmButtonColor: '#4f46e5'
        });
      }
    })
    .catch(err => {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Confirm &amp; Save Settlement';
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'System Error',
        text: 'An error occurred during settlement submission. Please try again.',
        confirmButtonColor: '#4f46e5'
      });
    });
  }

  function executeFolioCheckout() {
    if (!activeFolioData) return;

    const isCompanyFolio = (activeFolioData.registration_type || '').toLowerCase() === 'company' || !!activeFolioData.company;

    if (activeFolioData.balance > 0.01 && !isCompanyFolio) {
      Swal.fire({
        icon: 'warning',
        title: 'Outstanding Balance Due',
        html: `Cannot check out reservation with outstanding balance of <strong class="text-rose-600 font-black">${formatINR(activeFolioData.balance)}</strong>.<br><br>Please complete settlement first before checking out.`,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'OK'
      });
      return;
    }

    const companyNote = (isCompanyFolio && activeFolioData.balance > 0.01)
      ? `<div class="bg-fuchsia-50 border border-fuchsia-300 text-fuchsia-800 p-2 px-2.5 rounded-lg text-xs font-bold mb-2.5 text-left"><i class="fa-solid fa-building mr-1"></i> Company Billing Account: Outstanding balance of ${formatINR(activeFolioData.balance)} will remain on corporate ledger without pre-settlement.</div>`
      : '';

    Swal.fire({
      title: 'Confirm Room Check-Out',
      html: `
        ${companyNote}
        Are you sure you want to Check Out Booking <strong>#${escapeHtml(activeFolioData.reserve_id)}</strong> for <strong>${escapeHtml(activeFolioData.primary_guest?.name || 'Guest')}</strong>?<br><br><span class="text-xs text-slate-500">The room status will be automatically scheduled for Housekeeping.</span>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#10b981',
      cancelButtonColor: '#64748b',
      confirmButtonText: '<i class="fa-solid fa-check"></i> Yes, Check Out',
      cancelButtonText: 'Cancel'
    }).then(result => {
      if (!result.isConfirmed) return;

      const btn = document.getElementById('fmdl-action-btn-checkout');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Check Out...';
      }

      fetch(`{{ url('front-office/folio') }}/${activeFolioData.id}/checkout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          remarks: 'Checked out from Settlement Manager Counter'
        })
      })
      .then(res => res.json())
      .then(data => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-right-from-bracket"></i> Check Out';
        }
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Check Out Complete!',
            text: data.message || 'Reservation successfully checked out! Room(s) scheduled for housekeeping.',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({
            icon: 'warning',
            title: 'Check Out Notice',
            text: data.error || data.message || 'Check out failed.',
            confirmButtonColor: '#4f46e5'
          });
        }
      })
      .catch(err => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-right-from-bracket"></i> Check Out';
        }
        console.error(err);
        Swal.fire({
          icon: 'error',
          title: 'System Error',
          text: 'Network error while processing check out. Please try again.',
          confirmButtonColor: '#4f46e5'
        });
      });
    });
  }

  function closeFolioModal() {
    const modalEl = document.getElementById('folio-details-modal');
    if (modalEl) {
      modalEl.classList.remove('open');
      modalEl.style.setProperty('display', 'none', 'important');
    }
    document.body.style.overflow = '';
    if (typeof closeModal === 'function') {
      try { closeModal('folio-details-modal'); } catch (_) {}
    }
  }

  function openEarlyCheckoutConfirmModal(bookingId, guestName, roomNumber) {
    document.getElementById('eccm-booking-id').value = bookingId;
    document.getElementById('eccm-guest-name').textContent = guestName;
    document.getElementById('eccm-room-no').textContent = roomNumber;
    
    // Format current date and time
    const now = new Date();
    const dateFormatted = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    const timeFormatted = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

    document.getElementById('eccm-checkout-date').textContent = dateFormatted;
    document.getElementById('eccm-checkout-time').textContent = timeFormatted;

    const btn = document.getElementById('eccm-btn-confirm');
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-check"></i> Yes, Early Check Out';
    }

    const modalEl = document.getElementById('early-checkout-confirm-modal');
    if (modalEl) {
      modalEl.classList.add('open');
      modalEl.style.setProperty('display', 'flex', 'important');
    }
  }

  function closeEarlyCheckoutConfirmModal() {
    const modalEl = document.getElementById('early-checkout-confirm-modal');
    if (modalEl) {
      modalEl.classList.remove('open');
      modalEl.style.setProperty('display', 'none', 'important');
    }
    if (typeof closeModal === 'function') {
      try { closeModal('early-checkout-confirm-modal'); } catch (_) {}
    }
  }

  function executeEarlyCheckout() {
    const bookingId = document.getElementById('eccm-booking-id').value;
    if (!bookingId) return;

    const btn = document.getElementById('eccm-btn-confirm');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking Out...';
    }

    fetch(`{{ url('front-office/folio/early-checkout-guest') }}/${bookingId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        remarks: 'Early checked out via view dossier'
      })
    })
    .then(res => res.json())
    .then(data => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Yes, Early Check Out';
      }
      closeEarlyCheckoutConfirmModal();

      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Guest Checked Out Early!',
          text: data.message || 'Guest checked out successfully.',
          confirmButtonColor: '#10b981',
          confirmButtonText: 'OK'
        }).then(() => {
          if (activeFolioData && activeFolioData.id) {
            openFolioModalById(activeFolioData.id, true);
          }
          if (data.is_all_checked_out) {
            window.location.reload();
          }
        });
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Notice',
          text: data.error || data.message || 'Early check out could not be completed.',
          confirmButtonColor: '#4f46e5'
        });
      }
    })
    .catch(err => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Yes, Early Check Out';
      }
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'System Error',
        text: 'Network error while processing early check out. Please try again.',
        confirmButtonColor: '#4f46e5'
      });
    });
  }

  // Close on backdrop click & ESC key + Event Delegation for View Action Buttons
  document.addEventListener('DOMContentLoaded', function() {
    const modalBackdrop = document.getElementById('folio-details-modal');
    if (modalBackdrop) {
      modalBackdrop.addEventListener('click', function(e) {
        if (e.target === this) {
          closeFolioModal();
        }
      });
    }

    const earlyModalBackdrop = document.getElementById('early-checkout-confirm-modal');
    if (earlyModalBackdrop) {
      earlyModalBackdrop.addEventListener('click', function(e) {
        if (e.target === this) {
          closeEarlyCheckoutConfirmModal();
        }
      });
    }

    const swipeModalBackdrop = document.getElementById('room-swipe-modal');
    if (swipeModalBackdrop) {
      swipeModalBackdrop.addEventListener('click', function(e) {
        if (e.target === this) {
          closeRoomSwipeModal();
        }
      });
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeRoomSwipeModal();
        closeEarlyCheckoutConfirmModal();
        closeFolioModal();
      }
    });

    // Delegated click handler for view buttons, swipe buttons & booking numbers
    document.addEventListener('click', function(e) {
      const swipeTrigger = e.target.closest('.btn-action-swipe');
      if (swipeTrigger) {
        const bookingId = swipeTrigger.getAttribute('data-booking-id');
        if (bookingId) {
          e.preventDefault();
          e.stopPropagation();
          openFolioModalForSwipe(bookingId);
        }
        return;
      }

      const trigger = e.target.closest('.btn-action-view, [data-booking-id], .folio-id-text, .folio-number-sub');
      if (trigger) {
        if (trigger.closest('.btn-action-extend, .btn-action-swipe, .btn-room-swipe, .btn-room-swipe-mini, .btn-early-checkout')) {
          return;
        }
        const bookingId = trigger.getAttribute('data-booking-id');
        if (bookingId) {
          e.preventDefault();
          openFolioModalById(bookingId);
        }
      }
    });
  });

  function printFolioInvoice() {
    if (!activeFolioData) return;
    const f = activeFolioData;
    const pg = f.primary_guest || {};

    // Build room rows
    let roomRowsHtml = '';
    (f.rooms || []).forEach(r => {
      const occupantNames = (r.occupants || []).map(o => o.name).join(', ');
      const nights = Number(f.total_nights || 1);
      const roomTotal = Number(r.rate || 0) * nights;
      roomRowsHtml += `
        <tr>
          <td class="p-2 border border-slate-200 font-bold">${escapeHtml(r.room_number)} (Floor ${escapeHtml(r.floor)})</td>
          <td class="p-2 border border-slate-200">${escapeHtml(r.category)} &bull; ${escapeHtml(r.bedding)}</td>
          <td class="p-2 border border-slate-200">${escapeHtml(occupantNames || 'Primary Guest')}</td>
          <td class="p-2 border border-slate-200 text-right font-mono">${formatINR(r.rate)} &times; ${nights} night(s)</td>
          <td class="p-2 border border-slate-200 text-right font-mono font-bold">${formatINR(roomTotal)}</td>
        </tr>
      `;
    });

    // Build service order rows
    let serviceRowsHtml = '';
    let serviceTableHtml = '';
    if (f.room_service_orders && f.room_service_orders.length > 0) {
      f.room_service_orders.forEach(ro => {
        const itemsList = (ro.items || []).map(i => `${i.quantity}x ${escapeHtml(i.item_name)}`).join(', ');
        serviceRowsHtml += `
          <tr>
            <td class="p-1.5 px-2 border border-orange-200 font-mono font-bold text-orange-700">${escapeHtml(ro.order_number)}</td>
            <td class="p-1.5 px-2 border border-orange-200 text-slate-600">${escapeHtml(ro.date)}</td>
            <td class="p-1.5 px-2 border border-orange-200 text-slate-800">${itemsList}</td>
            <td class="p-1.5 px-2 border border-orange-200 text-right font-mono font-bold">${formatINR(ro.total_amount)}</td>
          </tr>
        `;
      });
      serviceTableHtml = `
        <div class="font-bold text-xs text-amber-600 my-4 mb-1.5 uppercase border-l-4 border-amber-500 pl-2">
          In-Room Dining &amp; Hospitality Services
        </div>
        <table class="w-full border-collapse mb-4 text-[11px]">
          <thead>
            <tr class="bg-orange-50 text-left">
              <th class="p-1.5 px-2 border border-orange-200 text-orange-800">Order #</th>
              <th class="p-1.5 px-2 border border-orange-200 text-orange-800">Date &amp; Time</th>
              <th class="p-1.5 px-2 border border-orange-200 text-orange-800">Items &amp; Service</th>
              <th class="p-1.5 px-2 border border-orange-200 text-orange-800 text-right">Amount</th>
            </tr>
          </thead>
          <tbody>${serviceRowsHtml}</tbody>
        </table>
      `;
    }

    // Build billing summary rows
    const grossRoom    = Number(f.room_total_amount !== undefined ? f.room_total_amount : (f.total_amount || 0));
    const serviceChrg  = Number(f.total_room_service_charges || 0);
    const discAmt      = Number(f.discount_amount || 0);
    const discPct      = Number(f.discount_percentage || 0);
    const gstAmt       = Number(f.gst_amount || 0);
    const gstPct       = Number(f.gst_percentage || 0);
    const roomPayable  = Number(f.room_payable_amount !== undefined ? f.room_payable_amount : (f.payable_amount || 0));
    const netPayable   = roomPayable + serviceChrg;
    const advancePaid  = Number(f.advance_amount || 0);
    const settlePaid   = Number(f.settlement_paid || 0);
    const totalPaid    = Number(f.total_paid || 0);
    const balance      = Number(f.balance || 0);

    const serviceChrgRow = serviceChrg > 0 ? `
      <tr class="text-amber-600">
        <td class="p-1.5 px-2.5 border-b border-orange-200">In-Room Dining &amp; Services</td>
        <td class="p-1.5 px-2.5 border-b border-orange-200 text-right font-mono">+ ${formatINR(serviceChrg)}</td>
      </tr>` : '';

    const discRow = discAmt > 0 ? `
      <tr class="text-emerald-600">
        <td class="p-1.5 px-2.5 border-b border-emerald-100">Discount (${discPct}%)</td>
        <td class="p-1.5 px-2.5 border-b border-emerald-100 text-right font-mono">- ${formatINR(discAmt)}</td>
      </tr>` : '';

    // Build full invoice body HTML for print iframe
    const invoiceBodyContent = `
<div class="inv-wrap">

  <!-- Header -->
  <div class="inv-header">
    <div>
      <div class="hotel-name">Hotel Sagar Sonnet</div>
      <div class="hotel-sub">Luxury Stays &amp; Hospitality Services &bull; Front Office PMS</div>
    </div>
    <div>
      <div class="inv-title">TAX INVOICE / SETTLEMENT</div>
      <div class="inv-ref">Settlement #: <strong>${escapeHtml(f.folio_number)}</strong></div>
      <div class="inv-ref">Booking Ref: <strong>${escapeHtml(f.reserve_id || ('RES-' + f.id))}</strong></div>
      <div class="inv-ref">Status: <span class="status-chip">${escapeHtml(f.status)}</span></div>
    </div>
  </div>

  <!-- Guest & Stay Info -->
  <div class="info-grid">
    <div class="info-block">
      <span class="info-label">Guest Details</span>
      <strong>${escapeHtml(pg.name || 'N/A')}</strong><br>
      Phone: ${escapeHtml(pg.mobile || 'N/A')}<br>
      Email: ${escapeHtml(pg.email || 'N/A')}<br>
      Address: ${escapeHtml(pg.address || 'N/A')}<br>
      ID Proof: ${escapeHtml(pg.id_card_type || 'N/A')} &ndash; ${escapeHtml(pg.id_card_number || 'N/A')}
    </div>
    <div class="info-block right">
      <span class="info-label">Stay Information</span>
      Check-In: <strong>${escapeHtml(f.checkin_date_fmt || '')} ${escapeHtml(f.checkin_time_fmt || '')}</strong><br>
      Check-Out: <strong>${escapeHtml(f.checkout_date_fmt || '')} ${escapeHtml(f.checkout_time_fmt || '')}</strong><br>
      Duration: <strong>${escapeHtml(String(f.total_nights || 1))} Night(s)</strong><br>
      Meal Plan: <strong>${escapeHtml(f.plan_name || 'EP')}</strong><br>
      Guests: <strong>${escapeHtml(String(f.total_guests_count || 1))} Person(s)</strong><br>
      Mode: <strong>${escapeHtml(f.reservation_mode || 'Front Desk')}</strong>
    </div>
  </div>

  <!-- Room Charges Table -->
  <div class="section-label">Room Charges</div>
  <table>
    <thead>
      <tr>
        <th>Room / Floor</th>
        <th>Category &amp; Bedding</th>
        <th>Occupants</th>
        <th class="text-right">Tariff &times; Nights</th>
        <th class="text-right">Room Total</th>
      </tr>
    </thead>
    <tbody class="bill-table">${roomRowsHtml}</tbody>
  </table>

  <!-- Service Orders -->
  ${serviceTableHtml}

  <!-- Bill Summary -->
  <div class="summary-wrap">
    <div class="summary-box">
      <table>
        <tr class="s-head"><td colspan="2">Bill Summary</td></tr>
        <tr>
          <td>Gross Room Charges</td>
          <td class="text-right font-mono">${formatINR(grossRoom)}</td>
        </tr>
        ${serviceChrgRow}
        ${discRow}
        <tr class="text-indigo-600">
          <td class="p-1.5 px-2.5 border-b border-indigo-100">Tax (GST ${gstPct}%)</td>
          <td class="p-1.5 px-2.5 border-b border-indigo-100 text-right font-mono">+ ${formatINR(gstAmt)}</td>
        </tr>
        <tr class="s-total">
          <td class="p-2 px-2.5 border-b-2 border-slate-300">Net Payable</td>
          <td class="p-2 px-2.5 border-b-2 border-slate-300 text-right font-mono">${formatINR(netPayable)}</td>
        </tr>
        <tr class="s-paid">
          <td class="p-1.5 px-2.5 border-b border-emerald-100">Advance Paid</td>
          <td class="p-1.5 px-2.5 border-b border-emerald-100 text-right font-mono">- ${formatINR(advancePaid)}</td>
        </tr>
        <tr class="s-paid">
          <td class="p-1.5 px-2.5 border-b border-emerald-100">Settlements Paid</td>
          <td class="p-1.5 px-2.5 border-b border-emerald-100 text-right font-mono">- ${formatINR(settlePaid)}</td>
        </tr>
        <tr class="s-paid">
          <td class="p-1.5 px-2.5 border-b-2 border-emerald-300 font-bold">Total Paid</td>
          <td class="p-1.5 px-2.5 border-b-2 border-emerald-300 text-right font-mono font-bold">${formatINR(totalPaid)}</td>
        </tr>
        <tr class="s-bal">
          <td class="p-2 px-2.5">Balance Due</td>
          <td class="p-2 px-2.5 text-right font-mono">${formatINR(balance)}</td>
        </tr>
      </table>
    </div>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div>Generated: ${new Date().toLocaleString('en-IN')} &bull; Status: <strong class="uppercase">${escapeHtml(f.status)}</strong></div>
    <div>Authorized Front Desk Signature: ________________________</div>
  </div>

</div>`;

    const printStyles = `
      * { box-sizing: border-box; margin: 0; padding: 0; }
      body { font-family: Arial, sans-serif; color: #1e293b; background: #fff; padding: 24px; }
      .inv-wrap { max-width: 820px; margin: 0 auto; }
      .inv-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #4f46e5; padding-bottom: 14px; margin-bottom: 18px; }
      .hotel-name { font-size: 22px; font-weight: 900; color: #1e1b4b; text-transform: uppercase; letter-spacing: 1px; }
      .hotel-sub { font-size: 11px; color: #64748b; margin-top: 3px; }
      .inv-title { font-size: 16px; font-weight: 800; color: #4f46e5; text-align: right; }
      .inv-ref { font-size: 11px; font-family: monospace; color: #475569; text-align: right; margin-top: 4px; }
      .info-grid { display: flex; justify-content: space-between; margin-bottom: 18px; font-size: 11.5px; line-height: 1.7; }
      .info-block { flex: 1; }
      .info-block.right { text-align: right; }
      .info-label { font-weight: 700; color: #4f46e5; font-size: 12px; display: block; margin-bottom: 4px; text-transform: uppercase; letter-spacing: .5px; }
      table { width: 100%; border-collapse: collapse; font-size: 11.5px; margin-bottom: 14px; }
      thead tr { background: #f1f5f9; }
      th { padding: 8px 10px; border: 1px solid #cbd5e1; text-align: left; font-size: 11px; color: #475569; text-transform: uppercase; }
      td { padding: 7px 10px; border: 1px solid #e2e8f0; }
      .bill-table td:last-child { text-align: right; font-family: monospace; }
      .summary-wrap { display: flex; justify-content: flex-end; margin: 10px 0 20px; }
      .summary-box { width: 340px; font-size: 12px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
      .summary-box table { margin: 0; font-size: 12px; }
      .summary-box table td { border-color: #e2e8f0; padding: 7px 12px; }
      .s-head td { background: #f8fafc; font-weight: 700; font-size: 11px; color: #475569; text-transform: uppercase; }
      .s-total td { background: #f1f5f9; font-weight: 800; font-size: 13px; color: #1e293b; }
      .s-paid td { color: #059669; }
      .s-bal td { font-weight: 900; font-size: 14px; background: ${balance > 0 ? '#fff1f2' : '#f0fdf4'}; color: ${balance > 0 ? '#e11d48' : '#059669'}; }
      .section-label { font-weight: 700; font-size: 12px; color: #7c3aed; text-transform: uppercase; letter-spacing: .5px; border-left: 4px solid #7c3aed; padding-left: 8px; margin: 12px 0 6px; }
      .footer { border-top: 1px dashed #cbd5e1; padding-top: 14px; display: flex; justify-content: space-between; align-items: center; font-size: 10.5px; color: #64748b; margin-top: 8px; }
      .status-chip { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; background: #e0e7ff; color: #3730a3; }
      @media print {
        body { padding: 10px; }
        .no-print { display: none !important; }
      }
    `;

    // Use a hidden iframe to print directly without opening a new window
    let iframe = document.getElementById('folio-print-frame');
    if (iframe) iframe.remove();

    iframe = document.createElement('iframe');
    iframe.id = 'folio-print-frame';
    iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:1px;height:1px;border:none;opacity:0;';
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow.document;
    doc.open();
    // Split tag strings to avoid regex/server HTML interceptor matches
    doc.write('<!DOCTYPE html><html><' + 'head><title>Invoice - ' + escapeHtml(f.folio_number) + '</title><style>' + printStyles + '</style></' + 'head><' + 'body>' + invoiceBodyContent + '</' + 'body></html>');
    doc.close();

    setTimeout(function() {
      try {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
      } catch (err) {
        console.error('Print iframe error:', err);
      }
      setTimeout(function() {
        if (iframe && iframe.parentNode) iframe.parentNode.removeChild(iframe);
      }, 2500);
    }, 150);
  }

  /* ========================================================================= */
  /* ROOM SWIPE / TRANSFER FUNCTIONALITY                                       */
  /* ========================================================================= */
  let currentSwipeData = null;

  function openRoomSwipeModal(bookingId, fromRoomId, fromRoomNumber, specificBookingId = null) {
    const modalEl = document.getElementById('room-swipe-modal');
    if (!modalEl) return;

    modalEl.classList.add('open');
    modalEl.style.setProperty('display', 'flex', 'important');

    const loadingEl = document.getElementById('rswipe-loading');
    const contentEl = document.getElementById('rswipe-content');
    if (loadingEl) loadingEl.style.display = 'flex';
    if (contentEl) contentEl.style.display = 'none';

    document.getElementById('rswipe-target-room-select').innerHTML = '<option value="">-- Choose Vacant Room --</option>';
    document.getElementById('rswipe-occupants-list').innerHTML = '';

    fetch(`{{ url('front-office/room-swipe/available-rooms') }}?booking_id=${bookingId}&from_room_id=${fromRoomId}`, {
      headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        Swal.fire({
          icon: 'warning',
          title: 'Room Swipe Unavailable',
          text: data.message || 'Could not load room shift information.',
          confirmButtonColor: '#4338ca'
        });
        closeRoomSwipeModal();
        return;
      }

      currentSwipeData = data;

      document.getElementById('rswipe-master-booking-id').value = data.master_booking_id;
      document.getElementById('rswipe-from-room-id').value = data.from_room ? data.from_room.id : fromRoomId;
      document.getElementById('rswipe-old-rate').value = data.from_room ? data.from_room.rate : 0;
      document.getElementById('rswipe-checkout-date').value = data.checkout_date || '';

      document.getElementById('rswipe-reserve-id').textContent = data.reserve_id;
      document.getElementById('rswipe-stay-dates').textContent = `${data.checkin_date || 'N/A'} → ${data.checkout_date || 'N/A'}`;
      document.getElementById('rswipe-from-room-num').textContent = data.from_room ? ('R' + data.from_room.room_number) : (fromRoomNumber || 'N/A');
      document.getElementById('rswipe-from-room-meta').textContent = data.from_room ? `Floor ${data.from_room.floor} • ${data.from_room.category}` : '';
      document.getElementById('rswipe-from-room-rate').textContent = formatINR(data.from_room ? data.from_room.rate : 0);
      document.getElementById('rswipe-prev-src-room').textContent = data.from_room ? data.from_room.room_number : fromRoomNumber;
      document.getElementById('rswipe-hk-room-name').textContent = data.from_room ? data.from_room.room_number : fromRoomNumber;

      const transferDateInput = document.getElementById('rswipe-transfer-date');
      transferDateInput.value = data.transfer_date;
      if (data.checkin_date) transferDateInput.min = data.checkin_date;
      if (data.checkout_date) transferDateInput.max = data.checkout_date;

      document.getElementById('rswipe-past-nights').textContent = data.past_nights;
      document.getElementById('rswipe-rem-nights').textContent = data.remaining_nights;
      document.getElementById('rswipe-prev-rem-n').textContent = data.remaining_nights;

      const occList = document.getElementById('rswipe-occupants-list');
      let occHtml = '';
      (data.occupants || []).forEach(occ => {
        const isSelected = (!specificBookingId || Number(occ.booking_id) === Number(specificBookingId));
        occHtml += `
          <label class="swipe-occ-item flex items-center justify-between py-1.5 px-3 bg-white border border-slate-200 rounded-lg cursor-pointer transition-all duration-150 hover:border-indigo-300">
            <div class="flex items-center gap-2">
              <input type="checkbox" name="rswipe_occupants[]" value="${occ.booking_id}" class="rswipe-occ-chk w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500" ${isSelected ? 'checked' : ''} onchange="calculateSwipePricing()">
              <span class="font-bold text-slate-900 text-xs">${escapeHtml(occ.guest_name)}</span>
              ${occ.is_primary ? '<span class="bg-amber-100 text-amber-800 text-[9px] font-extrabold px-1.5 py-0.5 rounded">Primary</span>' : ''}
            </div>
            <div class="text-[11px] text-slate-500 font-mono">
              <i class="fa-solid fa-phone text-[9px] text-emerald-500"></i> ${escapeHtml(occ.mobile)}
            </div>
          </label>
        `;
      });
      occList.innerHTML = occHtml || '<div class="text-[11px] text-slate-400 p-1.5">No occupants found in this room.</div>';

      const selectAllChk = document.getElementById('rswipe-select-all-occ');
      if (selectAllChk) selectAllChk.checked = !specificBookingId;

      populateSwipeAvailableRooms(data.available_rooms || []);

      if (loadingEl) loadingEl.style.display = 'none';
      if (contentEl) contentEl.style.display = 'flex';

      calculateSwipePricing();
    })
    .catch(err => {
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'Connection Error',
        text: 'Failed to communicate with hotel server.',
        confirmButtonColor: '#4338ca'
      });
      closeRoomSwipeModal();
    });
  }

  function populateSwipeAvailableRooms(rooms) {
    const $select = $('#rswipe-target-room-select');
    if ($select.data('select2')) {
      $select.select2('destroy');
    }

    $select.empty();
    $select.append('<option value="">-- Search & Choose Available Room --</option>');

    const availList = rooms || [];
    const availBadge = document.getElementById('rswipe-avail-count-badge');
    if (availBadge) {
      availBadge.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-500"></i> ${availList.length} Available Rooms`;
    }

    if (availList.length === 0) {
      $select.append('<option value="" disabled>No vacant/available rooms found for this stay period</option>');
    } else {
      availList.forEach(rm => {
        const $opt = $('<option></option>')
          .val(rm.id)
          .attr('data-rate', rm.rate)
          .attr('data-number', rm.room_number)
          .attr('data-category', rm.category)
          .attr('data-floor', rm.floor)
          .attr('data-bedding', rm.bedding)
          .text(`Room ${rm.room_number} (Floor ${rm.floor} • ${rm.category} • ₹ ${Number(rm.rate).toLocaleString('en-IN')}/night)`);
        $select.append($opt);
      });
    }

    $select.select2({
      dropdownParent: $('#room-swipe-modal'),
      dropdownCssClass: 'rswipe-select2-drop',
      placeholder: '-- Search by Room No, Floor, Category or Rate --',
      allowClear: true,
      width: '100%',
      templateResult: formatSwipeRoomOption,
      templateSelection: formatSwipeRoomSelection,
      matcher: customSwipeRoomMatcher
    });

    $select.off('.rswipe').on('select2:select.rswipe select2:clear.rswipe change.rswipe', function() {
      onSwipeRoomSelected();
    });
  }

  function formatSwipeRoomOption(opt) {
    if (!opt.id) return opt.text;
    const elem = opt.element;
    if (!elem) return opt.text;

    const roomNum = elem.dataset.number || '';
    const floor = elem.dataset.floor || '1';
    const cat = elem.dataset.category || 'Standard';
    const rate = Number(elem.dataset.rate || 0);
    const bedding = elem.dataset.bedding || '';

    const container = document.createElement('div');
    container.className = 'flex justify-between items-center py-1 px-0.5';
    container.innerHTML = `
      <div class="flex items-center gap-2">
        <span class="font-extrabold font-mono text-xs text-indigo-700 bg-indigo-100 px-1.5 py-0.5 rounded">
          <i class="fa-solid fa-door-open text-[9px]"></i> R${roomNum}
        </span>
        <span class="font-bold text-slate-800 text-xs">Floor ${floor} • ${cat}</span>
        ${bedding ? `<span class="text-[10px] text-slate-500 bg-slate-100 px-1 py-0.5 rounded">${bedding}</span>` : ''}
      </div>
      <div class="flex items-center gap-1.5">
        <span class="font-extrabold font-mono text-emerald-700 text-xs">₹ ${rate.toLocaleString('en-IN')}/n</span>
        <span class="bg-emerald-50 text-emerald-600 text-[9px] font-extrabold px-1.5 py-0.5 rounded-full border border-emerald-200">Vacant</span>
      </div>
    `;
    return $(container);
  }

  function formatSwipeRoomSelection(opt) {
    if (!opt || !opt.id) return opt ? opt.text : '';
    const elem = opt.element;
    if (elem && elem.dataset && elem.dataset.number) {
      const roomNum = elem.dataset.number;
      const floor = elem.dataset.floor || '1';
      const cat = elem.dataset.category || 'Standard';
      const rate = Number(elem.dataset.rate || 0);
      return `Room ${roomNum} (Floor ${floor} • ${cat} • ₹ ${rate.toLocaleString('en-IN')}/night)`;
    }
    return opt.text;
  }

  function customSwipeRoomMatcher(params, data) {
    if ($.trim(params.term) === '') {
      return data;
    }
    if (typeof data.text === 'undefined') {
      return null;
    }
    const term = params.term.toLowerCase().trim();
    const elem = data.element;
    if (!elem) {
      return data.text.toLowerCase().indexOf(term) > -1 ? data : null;
    }
    const roomNum = String(elem.dataset.number || '').toLowerCase();
    const category = String(elem.dataset.category || '').toLowerCase();
    const floor = String(elem.dataset.floor || '').toLowerCase();
    const bedding = String(elem.dataset.bedding || '').toLowerCase();
    const rate = String(elem.dataset.rate || '').toLowerCase();
    const fullText = (data.text || '').toLowerCase();

    if (
      roomNum.indexOf(term) > -1 ||
      category.indexOf(term) > -1 ||
      floor.indexOf(term) > -1 ||
      ('floor ' + floor).indexOf(term) > -1 ||
      ('fl ' + floor).indexOf(term) > -1 ||
      bedding.indexOf(term) > -1 ||
      rate.indexOf(term) > -1 ||
      fullText.indexOf(term) > -1
    ) {
      return data;
    }
    return null;
  }

  function onSwipeTransferDateChange() {
    if (!currentSwipeData) return;
    const masterBookingId = document.getElementById('rswipe-master-booking-id').value;
    const fromRoomId = document.getElementById('rswipe-from-room-id').value;
    const newTransferDate = document.getElementById('rswipe-transfer-date').value;

    if (!newTransferDate) return;

    const availBadge = document.getElementById('rswipe-avail-count-badge');
    if (availBadge) {
      availBadge.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking availability...';
    }

    fetch(`{{ route('frontoffice.room-swipe.available') }}?booking_id=${masterBookingId}&from_room_id=${fromRoomId}&transfer_date=${newTransferDate}`, {
      headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        currentSwipeData.transfer_date = data.transfer_date;
        currentSwipeData.past_nights = data.past_nights;
        currentSwipeData.remaining_nights = data.remaining_nights;
        currentSwipeData.available_rooms = data.available_rooms;

        document.getElementById('rswipe-past-nights').textContent = data.past_nights;
        document.getElementById('rswipe-rem-nights').textContent = data.remaining_nights;
        document.getElementById('rswipe-prev-rem-n').textContent = data.remaining_nights;

        populateSwipeAvailableRooms(data.available_rooms);
        calculateSwipePricing();
      }
    })
    .catch(() => {
      calculateSwipePricing();
    });
  }

  function closeRoomSwipeModal() {
    const modalEl = document.getElementById('room-swipe-modal');
    if (modalEl) {
      modalEl.classList.remove('open');
      modalEl.style.setProperty('display', 'none', 'important');
    }
    const $roomSelect = $('#rswipe-target-room-select');
    if ($roomSelect.data('select2')) {
      $roomSelect.select2('close');
    }
    currentSwipeData = null;
  }

  function toggleAllSwipeOccupants(masterCheckbox) {
    const chks = document.querySelectorAll('.rswipe-occ-chk');
    chks.forEach(c => c.checked = masterCheckbox.checked);
    calculateSwipePricing();
  }

  function onSwipeRoomSelected() {
    const select = document.getElementById('rswipe-target-room-select');
    if (!select) return;
    const val = select.value;
    const selectedOpt = val ? select.querySelector(`option[value="${val}"]`) : null;
    if (selectedOpt && selectedOpt.dataset.rate) {
      document.getElementById('rswipe-new-rate-input').value = selectedOpt.dataset.rate;
    } else if (!val) {
      document.getElementById('rswipe-new-rate-input').value = '';
    }
    calculateSwipePricing();
  }

  function calculateSwipePricing() {
    if (!currentSwipeData) return;

    const select = document.getElementById('rswipe-target-room-select');
    const val = select ? select.value : '';
    const selectedOpt = val ? select.querySelector(`option[value="${val}"]`) : null;
    const dstRoomNum = (selectedOpt && selectedOpt.dataset.number) ? selectedOpt.dataset.number : '-';
    document.getElementById('rswipe-prev-dst-room').textContent = dstRoomNum;

    const transferDateStr = document.getElementById('rswipe-transfer-date').value;
    const checkoutDateStr = document.getElementById('rswipe-checkout-date').value;
    const checkinDateStr = currentSwipeData.checkin_date;

    let remNights = 1;
    let pastNights = 0;
    if (transferDateStr && checkoutDateStr) {
      const dTransfer = new Date(transferDateStr);
      const dCheckout = new Date(checkoutDateStr);
      const diffTime = dCheckout - dTransfer;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      remNights = Math.max(1, diffDays);
    }
    if (transferDateStr && checkinDateStr) {
      const dTransfer = new Date(transferDateStr);
      const dCheckin = new Date(checkinDateStr);
      const diffTime = dTransfer - dCheckin;
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      pastNights = Math.max(0, diffDays);
    }

    document.getElementById('rswipe-past-nights').textContent = pastNights;
    document.getElementById('rswipe-rem-nights').textContent = remNights;
    document.getElementById('rswipe-prev-rem-n').textContent = remNights;

    const oldRate = Number(document.getElementById('rswipe-old-rate').value || 0);
    const newRate = Number(document.getElementById('rswipe-new-rate-input').value || 0);

    const allChks = document.querySelectorAll('.rswipe-occ-chk');
    const checkedChks = document.querySelectorAll('.rswipe-occ-chk:checked');
    const isWholeRoom = (checkedChks.length >= allChks.length);

    const hintEl = document.getElementById('rswipe-shift-mode-hint');
    const hkNotice = document.getElementById('rswipe-hk-notice');
    if (isWholeRoom) {
      if (hintEl) hintEl.textContent = 'Whole Room Shift: Old room will be completely vacated & scheduled for Housekeeping cleaning.';
      if (hkNotice) hkNotice.style.display = 'flex';
    } else {
      if (hintEl) hintEl.textContent = 'Partial Split Shift: Remaining occupant(s) will stay in old room. New room is added to booking.';
      if (hkNotice) hkNotice.style.display = 'none';
    }

    const pastStayTotal = pastNights * oldRate;
    document.getElementById('rswipe-prev-past-calc').textContent = `${pastNights} night(s) × ${formatINR(oldRate)} = ${formatINR(pastStayTotal)}`;

    const newStayTotal = remNights * newRate;
    document.getElementById('rswipe-prev-new-calc').textContent = (dstRoomNum !== '-') ? `${remNights} night(s) × ${formatINR(newRate)} = ${formatINR(newStayTotal)}` : 'Select a destination room';

    let diffTotal = 0;
    if (isWholeRoom) {
      const rateDiff = newRate - oldRate;
      diffTotal = rateDiff * remNights;
    } else {
      diffTotal = newStayTotal;
    }

    const diffEl = document.getElementById('rswipe-prev-diff');
    const diffPrefix = diffTotal >= 0 ? '+' : '';
    diffEl.textContent = `${diffPrefix}${formatINR(diffTotal)}`;
    diffEl.style.color = diffTotal > 0 ? '#4338ca' : (diffTotal < 0 ? '#059669' : '#0f172a');
  }

  function submitRoomSwipe() {
    const masterBookingId = document.getElementById('rswipe-master-booking-id').value;
    const fromRoomId = document.getElementById('rswipe-from-room-id').value;
    const toRoomSelect = document.getElementById('rswipe-target-room-select');
    const toRoomId = toRoomSelect.value;
    const transferDate = document.getElementById('rswipe-transfer-date').value;
    const newRate = document.getElementById('rswipe-new-rate-input').value;
    const reason = document.getElementById('rswipe-reason').value;
    const remarks = document.getElementById('rswipe-remarks').value;

    const checkedChks = document.querySelectorAll('.rswipe-occ-chk:checked');
    const transferredBookingIds = Array.from(checkedChks).map(c => c.value);

    if (!toRoomId) {
      Swal.fire({
        icon: 'warning',
        title: 'Destination Room Required',
        text: 'Please select a clean/vacant destination room from the dropdown.',
        confirmButtonColor: '#4338ca'
      });
      return;
    }

    if (transferredBookingIds.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Select Occupant(s)',
        text: 'Please check at least one occupant to shift to the new room.',
        confirmButtonColor: '#4338ca'
      });
      return;
    }

    const btn = document.getElementById('btn-submit-room-swipe');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Room Swipe...';
    }

    fetch(`{{ url('front-office/room-swipe') }}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        master_booking_id: masterBookingId,
        from_room_id: fromRoomId,
        to_room_id: toRoomId,
        transfer_date: transferDate,
        transferred_booking_ids: transferredBookingIds,
        new_room_rate: newRate,
        reason: reason,
        remarks: remarks
      })
    })
    .then(res => res.json())
    .then(data => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-arrow-right-arrow-left"></i> Confirm Room Swipe';
      }

      if (data.success) {
        closeRoomSwipeModal();
        Swal.fire({
          icon: 'success',
          title: 'Room Swipe Complete!',
          text: data.message || 'Room successfully transferred and billing updated.',
          confirmButtonColor: '#10b981',
          confirmButtonText: 'OK'
        }).then(() => {
          if (activeFolioData && activeFolioData.id) {
            openFolioModalById(activeFolioData.id, true);
          }
          window.location.reload();
        });
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Room Swipe Notice',
          text: data.message || 'Room shift could not be completed.',
          confirmButtonColor: '#4338ca'
        });
      }
    })
    .catch(err => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-arrow-right-arrow-left"></i> Confirm Room Swipe';
      }
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'System Error',
        text: 'Network error while executing room swipe. Please try again.',
        confirmButtonColor: '#4338ca'
      });
    });
  }

  // Explicit window bindings
  window.openFolioModalById = openFolioModalById;
  window.openFolioModalForSwipe = openFolioModalForSwipe;
  window.populateAndOpenFolioModal = populateAndOpenFolioModal;
  window.closeFolioModal = closeFolioModal;
  window.openEarlyCheckoutConfirmModal = openEarlyCheckoutConfirmModal;
  window.closeEarlyCheckoutConfirmModal = closeEarlyCheckoutConfirmModal;
  window.executeEarlyCheckout = executeEarlyCheckout;
  window.openRoomSwipeModal = openRoomSwipeModal;
  window.closeRoomSwipeModal = closeRoomSwipeModal;
  window.toggleAllSwipeOccupants = toggleAllSwipeOccupants;
  window.onSwipeRoomSelected = onSwipeRoomSelected;
  window.calculateSwipePricing = calculateSwipePricing;
  window.submitRoomSwipe = submitRoomSwipe;
  window.executeFolioSettlement = executeFolioSettlement;
  window.executeFolioCheckout = executeFolioCheckout;
  window.toggleFolioSettlementPanel = toggleFolioSettlementPanel;
  window.setFullFolioSettlementAmount = setFullFolioSettlementAmount;
  window.printFolioInvoice = printFolioInvoice;
</script>
@endpush
