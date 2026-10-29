@extends('frontoffice.layouts.app')

@section('title', 'Checkout Management - Hotel Sagar Sonnet CRM / PMS')


@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">

    <!-- Clean Header Card (like guest CRM) -->
    <div class="folio-header-card bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-xs mb-4">
      <div class="flex justify-between items-center flex-wrap gap-3">
        <div>
          <h2 class="m-0 text-lg font-extrabold text-slate-800 flex items-center gap-2.5 flex-wrap">
            <i class="fa-solid fa-person-walking-dashed-line-arrow-right text-rose-500"></i> 
            Checkout Management
            <span class="text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200 px-2.5 py-0.5 rounded-full">
              {{ $folios->total() }} Records
            </span>
            @if(!empty($fromDate) && !empty($toDate) && $fromDate === $toDate)
              <span class="text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                <i class="fa-regular fa-calendar-check"></i> {{ $fromDate === $today ? "Today's Check-ins (" . \Carbon\Carbon::parse($fromDate)->format('d M Y') . ")" : "Check-in: " . \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
              </span>
            @elseif(!empty($fromDate) || !empty($toDate))
              <span class="text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                <i class="fa-regular fa-calendar-check"></i> Check-in: {{ !empty($fromDate) ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }} &rarr; {{ !empty($toDate) ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'End' }}
              </span>
            @else
              <span class="text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 px-2.5 py-0.5 rounded-full">
                <i class="fa-regular fa-calendar"></i> All Check-in Dates
              </span>
            @endif
          </h2>
          <div class="text-xs text-slate-500 mt-1">
            @if(!empty($fromDate) && !empty($toDate) && $fromDate === $toDate)
              Showing checkout records with check-in on <strong class="text-slate-700">{{ \Carbon\Carbon::parse($fromDate)->format('D, d F Y') }}</strong> ({{ $fromDate === $today ? 'Today' : 'Selected Date' }}).
            @elseif(!empty($fromDate) || !empty($toDate))
              Showing checkout records with check-in between <strong class="text-slate-700">{{ !empty($fromDate) ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }}</strong> and <strong class="text-slate-700">{{ !empty($toDate) ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'End' }}</strong>.
            @else
              Guest departures and check-out directory with room status, settlement verification, and checkout control.
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- Search & Filter Controls Toolbar (like guest) -->
    <!-- ========================================== -->
    <form method="GET" action="{{ route('frontoffice.checkout') }}" id="folio-filter-form" class="folio-toolbar bg-white border border-slate-200/80 rounded-2xl p-3 sm:p-4 shadow-xs mb-4">
      <div class="flex justify-between items-center flex-wrap gap-3 w-full">
        <div class="flex gap-3 items-center flex-1 flex-wrap">
          
          <!-- Search Box with Clear Button -->
          <div class="search-box-wrap relative min-w-[280px] max-w-md flex-1">
            <i class="fa-solid fa-magnifying-glass search-icon absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" 
                   name="search" 
                   id="folio-search-input"
                   value="{{ request('search') }}" 
                   placeholder="Search by Booking #, Settlement #, Guest, Phone, Room..." 
                   class="w-full pl-8 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-hidden transition-all"
                   onkeydown="if(event.key==='Enter'){this.form.submit();}"
                   autocomplete="off">
            @if(request()->filled('search'))
              <a href="{{ route('frontoffice.checkout', request()->except('search', 'page')) }}" 
                 title="Clear search" 
                 class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
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
                   class="pms-filter-date text-xs py-1.5 px-2 bg-slate-50 border border-slate-200 rounded-lg font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:outline-hidden" 
                   value="{{ $fromDate ?? '' }}" 
                   onchange="this.form.submit()"
                   title="Filter by Check-in Date From">

            <label for="folio-to-date" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              To:
            </label>
            <input type="date" 
                   name="to_date" 
                   id="folio-to-date"
                   class="pms-filter-date text-xs py-1.5 px-2 bg-slate-50 border border-slate-200 rounded-lg font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:outline-hidden" 
                   value="{{ $toDate ?? '' }}" 
                   onchange="this.form.submit()"
                   title="Filter by Check-in Date To">
          </div>

          <!-- Status Filter Dropdown (Guest Status & Settlement Status) -->
          <div class="inline-flex items-center gap-1.5">
            <label for="folio-status-filter" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              <i class="fa-solid fa-filter text-indigo-500"></i> Status:
            </label>
            <select name="status" id="folio-status-filter" class="pms-filter-select text-xs py-1.5 px-2.5 bg-slate-50 border border-slate-200 rounded-lg font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:outline-hidden" onchange="this.form.submit()">
              <option value="all" {{ request('status') === 'all' || !request()->filled('status') ? 'selected' : '' }}>All Statuses</option>
              <optgroup label="Guest / Reservation Status">
                <option value="Checked In" {{ request('status') === 'Checked In' ? 'selected' : '' }}>Checked In</option>
                <option value="Checked Out" {{ request('status') === 'Checked Out' ? 'selected' : '' }}>Checked Out</option>
                <option value="Stay Over" {{ request('status') === 'Stay Over' ? 'selected' : '' }}>Stay Over</option>
                <option value="Confirmed" {{ request('status') === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="Arrived" {{ request('status') === 'Arrived' ? 'selected' : '' }}>Arrived</option>
                <option value="Settlement Done" {{ request('status') === 'Settlement Done' ? 'selected' : '' }}>Settlement Done</option>
                <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
              </optgroup>
              <optgroup label="Settlement Payment Status">
                <option value="Settled" {{ in_array(strtolower(request('status') ?? ''), ['settled', 'sattled']) ? 'selected' : '' }}>Settled (Payments Clear)</option>
                <option value="Unsettled" {{ in_array(strtolower(request('status') ?? ''), ['unsettled', 'unsattled']) ? 'selected' : '' }}>Unsettled (Payment Due)</option>
              </optgroup>
            </select>
          </div>

          <!-- Registration Type Filter -->
          <div class="inline-flex items-center gap-1.5">
            <label for="folio-regtype-filter" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              <i class="fa-solid fa-id-badge text-indigo-500"></i> Reg Type:
            </label>
            <select name="registration_type" id="folio-regtype-filter" class="pms-filter-select text-xs py-1.5 px-2.5 bg-slate-50 border border-slate-200 rounded-lg font-medium text-slate-700 focus:bg-white focus:border-indigo-500 focus:outline-hidden" onchange="this.form.submit()">
              <option value="all" {{ request('registration_type') === 'all' || !request()->filled('registration_type') ? 'selected' : '' }}>All Reg Types</option>
              @foreach($registrationTypes ?? [] as $rt)
                <option value="{{ $rt->name }}" {{ request('registration_type') === $rt->name ? 'selected' : '' }}>{{ $rt->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- All Dates Quick Button (if date filter active) -->
          @if(!empty($fromDate) || !empty($toDate))
            <a href="{{ route('frontoffice.checkout', array_merge(request()->except(['from_date', 'to_date', 'page']), ['all_dates' => 1])) }}" 
               class="btn-ui-secondary text-xs font-bold py-1.5 px-3 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:border-slate-300 inline-flex items-center gap-1.5 transition-colors" 
               title="View all records without date restrictions">
              <i class="fa-regular fa-calendar"></i> All Dates
            </a>
          @endif

          <!-- Today / Reset Button (resets to today's data) -->
          <a href="{{ route('frontoffice.checkout') }}" 
             class="btn-ui-secondary text-xs font-bold py-1.5 px-3 bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:border-slate-300 inline-flex items-center gap-1.5 transition-colors" 
             title="Reset filters to today's check-ins">
            <i class="fa-solid fa-rotate-left"></i> Today / Reset
          </a>
        </div>

        <!-- Page Size Selector -->
        <div class="flex gap-3 items-center">
          <div class="pms-per-page-wrap flex items-center gap-1.5 text-xs text-slate-500 font-medium">
            <span>Show</span>
            <select name="per_page" class="pms-per-page-select text-xs py-1 px-2 bg-slate-50 border border-slate-200 rounded-md font-semibold text-slate-700 focus:bg-white focus:outline-hidden" id="folio-per-page-select" onchange="this.form.submit()">
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
      <div class="pms-table-box bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-x-auto">
        <table class="pms-table w-full text-left border-collapse" id="folio-master-table">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
              <th class="w-[45px] text-center whitespace-nowrap py-3 px-2">SL</th>
              <th class="min-w-[160px] whitespace-nowrap py-3 px-3">Room(s) &amp; Floor</th>
              <th class="min-w-[140px] text-left whitespace-nowrap py-3 px-3">Booking Number</th>
              <th class="min-w-[180px] whitespace-nowrap py-3 px-3">Primary Guest Details</th>
              <th class="min-w-[130px] whitespace-nowrap py-3 px-3">Mobile / Phone</th>
              <th class="min-w-[120px] text-right whitespace-nowrap py-3 px-3">Paid Amount</th>
              <th class="min-w-[130px] text-right whitespace-nowrap py-3 px-3">Remaining Amount</th>
              <th class="min-w-[135px] whitespace-nowrap py-3 px-3">Check-in</th>
              <th class="min-w-[135px] whitespace-nowrap py-3 px-3">Check-out</th>
              <th class="min-w-[100px] text-center whitespace-nowrap py-3 px-3">Status</th>
              <th class="min-w-[175px] text-center whitespace-nowrap py-3 px-3">Actions</th>
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

                // Financials
                $advAmount = (float)$item->advance_amount;
                $settleAmount = (float)($item->settlements?->sum('paid_amount') ?: $item->payments?->where('payment_type', 'Settlement')->sum('amount'));
                $totalPaid = round($advAmount + $settleAmount, 2);
                $balanceVal = (float)($item->balance ?? 0);

                // Status in table: Only Settled (payments all clear) & Unsettled (payment not cleared)
                $isSettled = ($balanceVal <= 0.01) || in_array(strtolower($item->status ?? ''), ['settlement done', 'settled']);

                // Checkin / Checkout date & time
                $cInDate = $item->checkin_date ?: $item->reserve_date;
                $cInTime = $item->checkin_time ?: $item->reserve_time;
                $cOutDate = $item->checkout_date;
                $cOutTime = $item->checkout_time;
              @endphp
              <tr class="folio-row hover:bg-slate-50/70 border-b border-slate-100 transition-colors" id="row-folio-{{ $item->id }}">
                
                <!-- 0. SL -->
                <td class="text-center font-mono font-extrabold text-slate-500 text-[11px] py-3 px-2">
                  {{ $sl }}
                </td>

                <!-- 1. Room(s) & Floor (First) -->
                <td class="whitespace-nowrap py-3 px-3">
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
                          <span class="room-tag inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-mono font-extrabold text-xs bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <i class="fa-solid fa-door-open text-[10px]"></i>
                            {{ $rDisp }}
                          </span>
                          <span class="text-[10px] text-slate-500 font-bold ml-0.5">
                            Floor {{ $flrDisp }}
                          </span>
                        </div>
                      @endforeach
                    </div>
                  @else
                    <span class="text-[11px] text-slate-400 italic">
                      <i class="fa-solid fa-hourglass-half text-[10px]"></i> Unassigned
                    </span>
                  @endif
                </td>

                <!-- 2. Booking Number (Then Booking Number) -->
                <td class="whitespace-nowrap py-3 px-3">
                  <div class="folio-id-text text-xs font-mono font-extrabold text-indigo-600 hover:text-indigo-800 cursor-pointer" onclick="openFolioModalById({{ $item->id }})" data-booking-id="{{ $item->id }}" title="Click to open Settlement Dossier">
                    <i class="fa-solid fa-receipt text-indigo-500 mr-1 text-[11px] pointer-events-none"></i>
                    {{ $item->reserve_id ?: ('RES-' . $item->id) }}
                  </div>
                  <div class="folio-number-sub text-[10px] font-mono text-slate-400 cursor-pointer mt-0.5" onclick="openFolioModalById({{ $item->id }})" data-booking-id="{{ $item->id }}" title="Click to open Settlement Dossier">
                    Settlement: {{ $item->folio_number ?: '-' }}
                  </div>
                </td>

                <!-- 3. Primary Guest Details with Dynamic +N persons and Settled / Unsettled Badge -->
                <td class="whitespace-nowrap py-3 px-3">
                  <div class="font-extrabold text-slate-900 text-xs inline-flex items-center gap-1.5 flex-wrap">
                    <span>{{ $primaryGuestName }}</span>
                    @if($extraCount > 0)
                      <span class="badge-extra-guests" title="{{ $extraCount }} additional person(s) registered under this booking">
                        <i class="fa-solid fa-user-plus text-[9px]"></i>
                        +{{ $extraCount }} {{ $extraCount === 1 ? 'person' : 'persons' }}
                      </span>
                    @endif
                    @if($isSettled)
                      <span class="badge-settle-pill settled" title="Settled: All payments cleared (Balance ₹0.00)">
                        <i class="fa-solid fa-circle-check text-[8px]"></i>
                        <span>Settled</span>
                      </span>
                    @else
                      <span class="badge-settle-pill unsettled" title="Unsettled: Pending balance ₹{{ number_format($balanceVal, 2) }}">
                        <i class="fa-solid fa-clock text-[8px]"></i>
                        <span>Unsettled</span>
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
                <td class="whitespace-nowrap py-3 px-3">
                  <div class="font-mono font-bold text-xs text-slate-700 inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-phone text-[10px] text-emerald-500"></i>
                    <span>{{ $item->guest?->mobile ?: 'N/A' }}</span>
                  </div>
                </td>

                <!-- 5. Paid Amount -->
                <td class="text-right whitespace-nowrap py-3 px-3">
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
                <td class="text-right whitespace-nowrap py-3 px-3">
                  <div class="font-mono font-extrabold text-xs {{ $balanceVal > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    ₹ {{ number_format($balanceVal, 2) }}
                  </div>
                  <div class="mt-0.5">
                    @if($balanceVal > 0)
                      <span class="text-rose-700 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded text-[9px] font-extrabold inline-flex items-center gap-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-[8px]"></i> Due
                      </span>
                    @else
                      <span class="text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.5 rounded text-[9px] font-extrabold inline-flex items-center gap-0.5">
                        <i class="fa-solid fa-check text-[8px]"></i> Cleared
                      </span>
                    @endif
                  </div>
                </td>

                <!-- 7. Check-in Date & Time -->
                <td class="whitespace-nowrap py-3 px-3">
                  <div class="font-bold text-slate-800 text-xs inline-flex items-center gap-1">
                    <i class="fa-regular fa-calendar-check text-indigo-500 text-[11px]"></i>
                    <span>{{ $cInDate ? \Carbon\Carbon::parse($cInDate)->format('d M Y') : 'N/A' }}</span>
                  </div>
                  @if($cInTime)
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                      <i class="fa-regular fa-clock text-[9px] text-slate-400"></i> {{ date('h:i A', strtotime($cInTime)) }}
                    </div>
                  @endif
                </td>

                <!-- 8. Check-out Date & Time -->
                <td class="whitespace-nowrap py-3 px-3">
                  <div class="font-bold text-slate-800 text-xs inline-flex items-center gap-1">
                    <i class="fa-regular fa-calendar-xmark text-rose-500 text-[11px]"></i>
                    <span>{{ $cOutDate ? \Carbon\Carbon::parse($cOutDate)->format('d M Y') : 'N/A' }}</span>
                  </div>
                  @if($cOutTime)
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                      <i class="fa-regular fa-clock text-[9px] text-slate-400"></i> {{ date('h:i A', strtotime($cOutTime)) }}
                    </div>
                  @endif
                </td>

                <!-- 9. Actual Guest / Reservation Status -->
                <td class="text-center whitespace-nowrap py-3 px-3">
                  @php
                    $guestStatusNormalized = strtolower($item->status ?? 'confirmed');
                    $guestStatusClass = match($guestStatusNormalized) {
                      'settlement done', 'settled' => 'settled',
                      'confirmed' => 'confirmed',
                      'arrived' => 'arrived',
                      'checked in', 'checked-in', 'checkedin' => 'checkedin',
                      'stay over', 'stayover' => 'stayover',
                      'checked out', 'checked-out', 'checkedout' => 'checkedout',
                      'cancelled' => 'cancelled',
                      default => 'confirmed'
                    };
                  @endphp
                  <span class="status-badge {{ $guestStatusClass }}">
                    @if(in_array($guestStatusNormalized, ['checked out', 'checked-out', 'checkedout']))
                      <i class="fa-solid fa-right-from-bracket text-[8px]"></i>
                    @elseif(in_array($guestStatusNormalized, ['checked in', 'checked-in', 'checkedin']))
                      <i class="fa-solid fa-key text-[8px]"></i>
                    @elseif(in_array($guestStatusNormalized, ['settlement done', 'settled']))
                      <i class="fa-solid fa-lock text-[8px]"></i>
                    @elseif(in_array($guestStatusNormalized, ['arrived']))
                      <i class="fa-solid fa-plane-arrival text-[8px]"></i>
                    @elseif(in_array($guestStatusNormalized, ['stay over', 'stayover']))
                      <i class="fa-solid fa-bed text-[8px]"></i>
                    @elseif(in_array($guestStatusNormalized, ['cancelled']))
                      <i class="fa-solid fa-ban text-[8px]"></i>
                    @else
                      <i class="fa-solid fa-circle text-[6px]"></i>
                    @endif
                    <span>{{ $item->status ?? 'Confirmed' }}</span>
                  </span>
                </td>

                <!-- 10. Actions: Quick Check-Out & Early Check-Out (View Button Commented) -->
                <td class="text-center whitespace-nowrap py-3 px-3">
                  <div class="inline-flex items-center gap-1.5 justify-center">

                    <!-- Quick Checkout Button -->
                    @if(in_array(strtolower($item->status ?? ''), ['checked out', 'checkedout']))
                      <button type="button" 
                              class="btn-action-checkout checked-out" 
                              disabled 
                              title="Reservation is already Checked Out">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Checked Out</span>
                      </button>
                    @else
                      @php
                        $isCompanyReg = (strtolower($item->registrationType?->name ?? '') === 'company' || !empty($item->company_id));
                      @endphp
                      <button type="button" 
                              class="btn-action-checkout" 
                              onclick="handleRowCheckout({{ $item->id }}, {{ $isSettled ? 'true' : 'false' }}, {{ $balanceVal }}, '{{ addslashes($primaryGuestName) }}', '{{ $item->reserve_id ?: ('RES-' . $item->id) }}', '{{ $item->status }}', {{ $isCompanyReg ? 'true' : 'false' }})" 
                              title="Check out booking">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Checkout</span>
                      </button>
                    @endif

                    <!-- Early Checkout Button (Opens Room & Floor wise Occupants Modal) -->
                    @if(!in_array(strtolower($item->status ?? ''), ['checked out', 'checkedout']))
                      <button type="button" 
                              class="btn-action-early-co" 
                              onclick="openEarlyCheckoutRoomsModalById({{ $item->id }})" 
                              title="Early Check-Out for Individual Guests (Room &amp; Floor Wise)">
                        <i class="fa-solid fa-person-walking-arrow-right"></i>
                        <span>Early CO</span>
                      </button>
                    @endif

                  </div>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="11" class="text-center text-slate-400 py-10 px-4">
                  <div class="text-3xl text-slate-300 mb-2"><i class="fa-solid fa-person-walking-dashed-line-arrow-right"></i></div>
                  <div class="text-sm font-bold text-slate-600">No matching checkout records found for this filter.</div>
                  <div class="text-xs text-slate-400 mt-1">
                    Showing check-ins for {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'selected dates' }}. 
                    @if(!empty($fromDate) || !empty($toDate))
                      <a href="{{ route('frontoffice.checkout', ['all_dates' => 1]) }}" class="text-indigo-600 font-extrabold underline hover:text-indigo-800">Click here to view All Dates</a>.
                    @endif
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <!-- Backend Table Pagination Footer -->
        <div class="pms-table-pagination flex justify-between items-center px-4 py-3 border-t border-slate-200/80 bg-white flex-wrap gap-2.5">
          <div class="pms-pagination-info text-xs font-semibold text-slate-500">
            Showing {{ $folios->firstItem() ?? 0 }} to {{ $folios->lastItem() ?? 0 }} of {{ $folios->total() }} records
          </div>
          <div class="pms-page-nav flex items-center gap-1">
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
  <div class="text-[11px] font-bold text-slate-500 flex items-center gap-3.5 flex-wrap">
    <span><i class="fa-solid fa-receipt text-indigo-500"></i> Total Settlements: <strong class="text-slate-800">{{ $stats['total_folios'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-file-invoice text-cyan-600"></i> Total Receivable: <strong class="text-slate-800">₹ {{ number_format($stats['total_receivable'] ?? 0, 2) }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-circle-check text-emerald-600"></i> Total Paid: <strong class="text-slate-800">₹ {{ number_format($stats['total_paid'] ?? 0, 2) }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-triangle-exclamation {{ ($stats['total_outstanding'] ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}"></i> Remaining Due: <strong class="text-slate-800">₹ {{ number_format($stats['total_outstanding'] ?? 0, 2) }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-circle-check text-emerald-600"></i> Settled: <strong class="text-slate-800">{{ $stats['settled_count'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-clock text-rose-500"></i> Unsettled: <strong class="text-slate-800">{{ $stats['pending_settle_count'] ?? 0 }}</strong></span>
  </div>
</footer>

<!-- ========================================================================= -->
<!-- EXTENSIVE SETTLEMENT DOSSIER MODAL (Design matching guest CRM)             -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="folio-details-modal">
  <div class="modal-window large w-[900px] max-w-[95vw] max-h-[92vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 box-border bg-white">
    
    <!-- Modal Header (Gradient style matching guest CRM) -->
    <div class="modal-top bg-gradient-to-r from-indigo-600 to-indigo-700 p-4 sm:p-5 flex justify-between items-center text-white shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-9.5 h-9.5 rounded-xl bg-white/20 flex items-center justify-center text-base text-white">
          <i class="fa-solid fa-receipt"></i>
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h3 id="fmdl-guest-name" class="m-0 text-base font-extrabold text-white">Guest Profile &amp; Settlement Dossier</h3>
            <span id="fmdl-status-badge" class="status-badge bg-white text-indigo-700 font-extrabold text-xs px-2.5 py-0.5 rounded-full">Confirmed</span>
          </div>
          <div class="text-xs opacity-85 mt-0.5 font-mono text-indigo-100">
            Booking Ref: <span id="fmdl-reserve-id" class="font-bold">-</span> &nbsp;|&nbsp; Settlement: <span id="fmdl-folio-number" class="font-bold">-</span>
          </div>
        </div>
      </div>
      <button type="button" class="modal-close bg-white/20 hover:bg-white/30 text-white w-8 h-8 rounded-full flex items-center justify-center text-lg transition-colors cursor-pointer border-none" onclick="closeFolioModal()">&times;</button>
    </div>

    <!-- Modal Content Area -->
    <div class="modal-content-area p-4 sm:p-5 overflow-y-auto flex-1 bg-slate-50" id="folio-modal-body">
      
      <!-- Top Highlight Banner (like guest CRM) -->
      <div class="dossier-highlight-box bg-white border border-slate-200/80 rounded-xl p-3.5 sm:p-4 mb-4 shadow-2xs flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
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
          <span class="text-xs font-bold bg-white border border-slate-200 px-2.5 py-1 rounded-lg text-slate-700 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-users text-indigo-500"></i> <span id="fmdl-total-guests-count">1 Person</span>
          </span>
          <span class="text-xs font-bold bg-orange-50 border border-orange-200 px-2.5 py-1 rounded-lg text-orange-700 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-utensils text-orange-500"></i> <span id="fmdl-plan-name">EP</span>
          </span>
          <span class="text-xs font-bold bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-lg text-indigo-700 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-moon"></i> <span id="fmdl-nights-sched">1 Night(s) Stay</span>
          </span>
          <span class="text-xs font-bold bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg text-emerald-700 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-headset text-emerald-600"></i> <span id="fmdl-reservation-mode">Front Desk</span>
          </span>
        </div>
      </div>

      <!-- 2-Column Uniform Dossier Grid (like guest CRM) -->
      <div class="dossier-grid grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        
        <!-- CARD 1: Primary Guest Profile Details -->
        <div class="dossier-card bg-white border border-slate-200/80 rounded-xl p-4 shadow-2xs">
          <div class="dossier-card-title text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2 pb-2 border-b border-slate-100">
            <i class="fa-solid fa-user-check text-indigo-500"></i> Primary Guest Profile Details
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Full Name</span>
            <span class="dossier-val font-semibold text-slate-800 text-right" id="fmdl-card-name">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Registration Type</span>
            <span class="dossier-val text-indigo-600 font-extrabold text-right" id="fmdl-card-regtype">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Mobile Number</span>
            <span class="dossier-val mono font-mono font-semibold text-slate-800 text-right" id="fmdl-guest-mobile">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Email Address</span>
            <span class="dossier-val font-semibold text-slate-800 text-right" id="fmdl-guest-email">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">City / Location</span>
            <span class="dossier-val font-semibold text-slate-800 text-right" id="fmdl-guest-city">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Full Address</span>
            <span class="dossier-val font-semibold text-slate-800 text-right max-w-[220px] whitespace-normal" id="fmdl-guest-address">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Nationality</span>
            <span class="dossier-val font-semibold text-slate-800 text-right" id="fmdl-guest-nationality">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Identity Proof</span>
            <span class="dossier-val font-semibold text-slate-800 text-right" id="fmdl-guest-idproof">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs hidden" id="fmdl-card-privilege-row">
            <span class="dossier-label text-slate-500 font-medium">Privilege / VIP Card</span>
            <span class="dossier-val mono font-mono font-bold text-amber-700 text-right" id="fmdl-card-privilege">-</span>
          </div>
        </div>

        <!-- CARD 2: Stay Schedule & Corporate Account -->
        <div class="dossier-card bg-white border border-slate-200/80 rounded-xl p-4 shadow-2xs">
          <div class="dossier-card-title text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2 pb-2 border-b border-slate-100">
            <i class="fa-solid fa-calendar-days text-indigo-500"></i> Stay &amp; Corporate Details
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Check-In (Arrival)</span>
            <span class="dossier-val mono font-mono font-bold text-indigo-600 text-right" id="fmdl-checkin-sched">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Check-Out (Departure)</span>
            <span class="dossier-val mono font-mono font-bold text-emerald-600 text-right" id="fmdl-checkout-sched">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Duration of Stay</span>
            <span class="dossier-val font-semibold text-slate-800 text-right" id="fmdl-duration-stay">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Reservation Mode</span>
            <span class="dossier-val font-semibold text-slate-800 text-right" id="fmdl-resmode-val">-</span>
          </div>

          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-50 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Meal Plan</span>
            <span class="dossier-val text-orange-700 font-extrabold text-right" id="fmdl-mealplan-val">-</span>
          </div>

          <!-- Corporate Account Block -->
          <div id="fmdl-company-row" class="mt-2.5 p-2.5 rounded-lg bg-indigo-50/50 border border-indigo-100 hidden">
            <div class="text-[10px] font-extrabold text-indigo-700 uppercase mb-1.5 flex items-center gap-1.5">
              <i class="fa-solid fa-building"></i> Corporate Account
            </div>
            <div class="dossier-field-row flex justify-between items-center py-1 text-xs">
              <span class="dossier-label text-slate-500 font-medium">Company Name</span>
              <span class="dossier-val font-bold text-indigo-700 text-right" id="fmdl-company-name">-</span>
            </div>
            <div class="dossier-field-row flex justify-between items-center py-1 text-xs">
              <span class="dossier-label text-slate-500 font-medium">Company GSTIN</span>
              <span class="dossier-val mono font-mono font-semibold text-slate-800 text-right" id="fmdl-company-gstin">-</span>
            </div>
            <div class="dossier-field-row flex justify-between items-center py-1 text-xs">
              <span class="dossier-label text-slate-500 font-medium">Phone</span>
              <span class="dossier-val mono font-mono font-semibold text-slate-800 text-right" id="fmdl-company-phone">-</span>
            </div>
          </div>
        </div>

      </div>

      <!-- CARD 3: Allocated Rooms & Registered Occupants Details -->
      <div class="dossier-card bg-white border border-slate-200/80 rounded-xl p-4 shadow-2xs mb-4">
        <div class="dossier-card-title text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2 pb-2 border-b border-slate-100">
          <i class="fa-solid fa-bed text-indigo-500"></i> Allocated Rooms &amp; Registered Occupants
        </div>
        <div id="fmdl-rooms-container">
          <!-- Room cards & occupant badges populated dynamically -->
        </div>
      </div>

      <!-- CARD 4: Pricing & Billing Breakdown (like guest CRM) -->
      <div class="dossier-card bg-white border border-slate-200/80 rounded-xl p-4 shadow-2xs">
        <div class="dossier-card-title text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2 pb-2 border-b border-slate-100">
          <i class="fa-solid fa-file-invoice-dollar text-indigo-500"></i> Pricing &amp; Billing Breakdown
        </div>

        <!-- 4-Stat Metric Boxes matching guest CRM -->
        <div class="billing-summary-grid grid grid-cols-2 sm:grid-cols-4 gap-2.5 mb-3">
          <div class="bill-stat-item bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 text-center">
            <div class="bill-stat-label text-[10px] font-extrabold uppercase text-slate-500 tracking-wider mb-1">Daily Room Rate</div>
            <div class="bill-stat-value font-mono text-sm font-extrabold text-slate-700" id="fmdl-bill-rate">₹ 0.00</div>
          </div>

          <div class="bill-stat-item bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 text-center">
            <div class="bill-stat-label text-[10px] font-extrabold uppercase text-slate-500 tracking-wider mb-1">Gross Amount</div>
            <div class="bill-stat-value font-mono text-sm font-extrabold text-slate-800" id="fmdl-gross-amount">₹ 0.00</div>
          </div>

          <div class="bill-stat-item bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 text-center">
            <div class="bill-stat-label text-[10px] font-extrabold uppercase text-slate-500 tracking-wider mb-1">Total Amount Paid</div>
            <div class="bill-stat-value font-mono text-sm font-extrabold text-emerald-600" id="fmdl-totalpaid-amount">₹ 0.00</div>
          </div>

          <div class="bill-stat-item bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 text-center" id="fmdl-balance-box">
            <div class="bill-stat-label text-[10px] font-extrabold uppercase text-slate-500 tracking-wider mb-1" id="fmdl-balance-label">Balance Due</div>
            <div class="bill-stat-value font-mono text-sm font-extrabold text-rose-600" id="fmdl-balance-amount">₹ 0.00</div>
          </div>
        </div>

        <!-- Itemized Line Rows -->
        <div class="mt-3.5 bg-slate-50/50 border border-slate-200/80 rounded-xl p-3">
          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-100 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Room Rate × Nights</span>
            <span class="dossier-val mono font-mono font-semibold text-slate-800" id="fmdl-calc-row">-</span>
          </div>
          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-100 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Advance Deposit Paid</span>
            <span class="dossier-val mono font-mono font-bold text-emerald-600" id="fmdl-advance-row">₹ 0.00</span>
          </div>
          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-100 text-xs" id="fmdl-settle-field-row">
            <span class="dossier-label text-slate-500 font-medium">Settlement Amount Paid</span>
            <span class="dossier-val mono font-mono font-bold text-indigo-600" id="fmdl-settlement-row">₹ 0.00</span>
          </div>
          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-100 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Total Amount Paid</span>
            <span class="dossier-val mono font-mono font-bold text-emerald-600" id="fmdl-totalpaid-row">₹ 0.00</span>
          </div>
          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-100 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Discount</span>
            <span class="dossier-val mono font-mono font-bold text-emerald-600" id="fmdl-discount-row">-</span>
          </div>
          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-100 text-xs">
            <span class="dossier-label text-slate-500 font-medium">GST / Tax</span>
            <span class="dossier-val mono font-mono font-bold text-indigo-600" id="fmdl-tax-row">-</span>
          </div>
          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-100 text-xs hidden" id="fmdl-services-field-row">
            <span class="dossier-label text-orange-700 font-medium"><i class="fa-solid fa-utensils text-orange-500"></i> Restaurant &amp; Room Dining (Folio)</span>
            <span class="dossier-val mono font-mono font-extrabold text-orange-600" id="fmdl-services-row">+ ₹ 0.00</span>
          </div>
          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-100 text-xs">
            <span class="dossier-label text-slate-700 font-bold">Net Payable Amount</span>
            <span class="dossier-val mono font-mono text-sm font-black text-slate-900" id="fmdl-payable-amount">-</span>
          </div>
          <div class="dossier-field-row flex justify-between items-center py-1.5 border-b border-slate-100 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Payment Mode</span>
            <span class="dossier-val font-semibold text-slate-800" id="fmdl-paymode-row">-</span>
          </div>
          <div class="dossier-field-row flex justify-between items-center py-1.5 text-xs">
            <span class="dossier-label text-slate-500 font-medium">Payment Remarks / Reference</span>
            <span class="dossier-val font-semibold text-slate-500" id="fmdl-remarks-row">-</span>
          </div>
        </div>

        <!-- Room Service & Restaurant Dining Orders Box -->
        <div class="mt-3.5 hidden" id="fmdl-room-services-box">
          <div class="text-[11px] font-extrabold text-amber-700 uppercase tracking-wider mb-1.5 flex justify-between items-center">
            <span><i class="fa-solid fa-bell-concierge"></i> Restaurant &amp; In-Room Dining Bills (<span id="fmdl-services-count">0</span> Orders)</span>
            <span class="text-[10px] text-slate-500 normal-case font-semibold">Billing Breakdown</span>
          </div>
          <div id="fmdl-room-services-list" class="text-xs">
            <!-- Populated via JS -->
          </div>
        </div>

        <!-- Settlement Receipts History List -->
        <div class="mt-3.5" id="fmdl-settle-history-box">
          <div class="text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-1.5">
            <i class="fa-solid fa-clock-rotate-left"></i> Settlement Receipts History
          </div>
          <div id="fmdl-settle-history-list" class="text-xs">
            <!-- Populated via JS -->
          </div>
        </div>

        <!-- Interactive Inline Settlement Panel -->
        <div id="fmdl-settlement-interactive-panel" class="settlement-panel bg-emerald-50/70 border border-emerald-200 rounded-xl p-3.5 mt-3.5 hidden">
          <div class="flex justify-between items-center mb-2.5 flex-wrap gap-2">
            <div class="text-xs font-extrabold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
              <i class="fa-solid fa-calculator"></i> Process Settlement
            </div>
            <button type="button" onclick="setFullFolioSettlementAmount()" class="bg-transparent border-none text-emerald-700 font-extrabold cursor-pointer underline text-[11px] p-0 hover:text-emerald-900">
              Pay Full Due (<span id="fmdl-btn-payfull-val">₹ 0.00</span>)
            </button>
          </div>

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
                       class="w-full pl-6 pr-2.5 py-1.5 rounded-lg border border-emerald-300 text-xs font-extrabold font-mono text-slate-900 bg-white focus:outline-hidden focus:ring-1 focus:ring-emerald-500">
              </div>
            </div>

            <div>
              <label class="block text-[11px] font-extrabold text-slate-800 mb-1">
                Payment Method <span class="text-rose-600">*</span>
              </label>
              <select id="fmdl-select-settle-mode" class="w-full px-2.5 py-1.5 rounded-lg border border-emerald-300 text-xs font-bold text-slate-800 bg-white focus:outline-hidden focus:ring-1 focus:ring-emerald-500">
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
                     class="w-full px-2.5 py-1.5 rounded-lg border border-emerald-300 text-xs font-mono text-slate-800 bg-white focus:outline-hidden focus:ring-1 focus:ring-emerald-500">
            </div>
          </div>

          <div class="mb-3">
            <label class="block text-[11px] font-extrabold text-slate-800 mb-1">
              Settlement Notes / Remarks
            </label>
            <input type="text" 
                   id="fmdl-input-settle-remarks" 
                   placeholder="e.g. Counter settlement finalized with primary guest." 
                   class="w-full px-2.5 py-1.5 rounded-lg border border-emerald-300 text-xs text-slate-800 bg-white focus:outline-hidden focus:ring-1 focus:ring-emerald-500">
          </div>

          <div class="flex justify-end gap-2">
            <button type="button" 
                    id="fmdl-btn-confirm-settlement" 
                    onclick="executeFolioSettlement()" 
                    class="py-2 px-5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs inline-flex items-center gap-1.5 shadow-sm transition-colors cursor-pointer border-none">
              <i class="fa-solid fa-check-circle"></i> Confirm &amp; Save Settlement
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Footer Actions (matches guest CRM) -->
    <div class="modal-bot bg-white border-t border-slate-200/80 px-4 py-3 flex justify-between items-center flex-wrap gap-2.5 shrink-0">
      <div class="text-xs text-slate-500 font-medium">
        <i class="fa-solid fa-clock-rotate-left"></i> Record Registered: <span id="fmdl-created-at">-</span>
      </div>

      <div class="flex gap-2 items-center flex-wrap">
        <!-- Settle Balance Button -->
        <button type="button" 
                id="fmdl-action-btn-settle" 
                onclick="toggleFolioSettlementPanel()" 
                class="py-1.5 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs inline-flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer border-none">
          <i class="fa-solid fa-calculator"></i> Settle Balance
        </button>

        <!-- Check Out Button -->
        <button type="button" 
                id="fmdl-action-btn-checkout" 
                onclick="executeFolioCheckout()" 
                class="py-1.5 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs inline-flex items-center gap-1.5 shadow-xs transition-colors cursor-pointer border-none hidden">
          <i class="fa-solid fa-right-from-bracket"></i> Check Out
        </button>

        <!-- Locked Badge -->
        <div id="fmdl-badge-locked" 
             class="hidden py-1.5 px-3 rounded-lg bg-slate-100 border border-slate-200 text-slate-600 text-xs font-extrabold items-center gap-1.5">
          <i class="fa-solid fa-lock text-slate-500"></i> Stay Finalized &amp; Checked Out (Locked)
        </div>

        <!-- Print Invoice Button -->
        <button type="button" class="btn-ui-secondary py-1.5 px-3.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs inline-flex items-center gap-1.5 cursor-pointer" onclick="printFolioInvoice()">
          <i class="fa-solid fa-print"></i> Print Invoice
        </button>

        <!-- Close Button -->
        <button type="button" class="btn-ui-secondary py-1.5 px-3.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-bold text-xs cursor-pointer" onclick="closeFolioModal()">
          Close
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EARLY CHECK-OUT (ROOM & FLOOR WISE OCCUPANTS)                     -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="early-checkout-rooms-modal">
  <div class="modal-window large w-[820px] max-w-[95vw] max-h-[90vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 box-border bg-white">
    
    <!-- Modal Top Header -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-4 sm:p-5 flex justify-between items-center text-white shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-9.5 h-9.5 rounded-xl bg-white/20 flex items-center justify-center text-base text-white">
          <i class="fa-solid fa-person-walking-arrow-right"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h3 class="m-0 text-base font-extrabold text-white">Early Check-Out Manager</h3>
            <span id="ecrm-status-badge" class="badge-settle-pill settled text-[10px]">Settled</span>
          </div>
          <div class="text-xs opacity-95 text-orange-100 mt-0.5">
            Reservation <strong id="ecrm-reserve-id" class="font-mono text-white">-</strong> 
            • Primary Guest: <strong id="ecrm-primary-guest" class="text-white">-</strong>
          </div>
        </div>
      </div>
      <button type="button" onclick="closeEarlyCheckoutRoomsModal()" class="bg-white/20 hover:bg-white/30 text-white w-7.5 h-7.5 rounded-full text-lg cursor-pointer flex items-center justify-center transition-colors border-none" title="Close Modal">&times;</button>
    </div>

    <!-- Modal Body: Scrollable -->
    <div class="p-4 sm:p-5 overflow-y-auto flex-1 bg-slate-50">
      
      <!-- Notice Bar -->
      <div id="ecrm-settle-notice" class="mb-3.5 p-3 rounded-xl text-xs flex items-center justify-between gap-2.5">
      </div>

      <!-- Rooms and Occupants Grid -->
      <div>
        <div class="text-xs font-extrabold text-slate-600 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
          <i class="fa-solid fa-door-closed text-orange-500"></i> Allocated Room(s), Floor &amp; Occupants
        </div>
        
        <div id="ecrm-rooms-card-grid" class="rooms-alloc-grid flex flex-col gap-3">
          <!-- Dynamically populated via JS -->
        </div>
      </div>

    </div>

    <!-- Modal Footer -->
    <div class="p-3 sm:px-5 bg-white border-t border-slate-200 flex justify-between items-center shrink-0">
      <div class="text-xs text-slate-500">
        <i class="fa-solid fa-circle-info text-indigo-500"></i> Individual occupants checked out early will schedule their respective room for housekeeping when vacated.
      </div>
      <button type="button" onclick="closeEarlyCheckoutRoomsModal()" class="btn-ui-secondary py-1.5 px-4 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-bold text-xs cursor-pointer">
        Close
      </button>
    </div>

  </div>
</div>

<!-- ========================================================================= -->
<!-- CONFIRMATION MODAL: EARLY CHECK-OUT INDIVIDUAL GUEST                     -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="early-checkout-confirm-modal">
  <div class="modal-window w-[480px] max-w-[95vw] rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 bg-white">
    
    <div class="bg-gradient-to-r from-rose-500 to-rose-700 p-4 flex justify-between items-center text-white">
      <div class="flex items-center gap-2.5">
        <div class="w-8.5 h-8.5 rounded-lg bg-white/20 flex items-center justify-center text-sm text-white">
          <i class="fa-solid fa-person-walking-arrow-right"></i>
        </div>
        <div>
          <h4 class="m-0 text-sm font-extrabold text-white">Early Check-Out Confirmation</h4>
          <div class="text-[10px] opacity-90 text-rose-100">Individual Occupant Check-Out</div>
        </div>
      </div>
      <button type="button" onclick="closeEarlyCheckoutConfirmModal()" class="bg-white/20 hover:bg-white/30 text-white w-7 h-7 rounded-full text-base cursor-pointer flex items-center justify-center transition-colors border-none">&times;</button>
    </div>

    <div class="p-5 bg-white">
      <div class="flex gap-3.5 items-start mb-4">
        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-lg shrink-0">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
          <div class="text-sm font-extrabold text-slate-900 mb-1">
            Are you sure you want to early check-out this guest?
          </div>
          <div class="text-xs text-slate-500 leading-relaxed">
            Only this particular occupant will be marked as checked out. Other occupants and room billing stay active.
          </div>
        </div>
      </div>

      <!-- Guest and Checkout Info Box -->
      <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 text-xs flex flex-col gap-2">
        <div class="flex justify-between items-center">
          <span class="text-slate-500 font-semibold">Guest Name:</span>
          <strong id="eccm-guest-name" class="text-slate-900 font-bold text-sm">-</strong>
        </div>
        <div class="flex justify-between items-center border-t border-dashed border-slate-200 pt-2">
          <span class="text-slate-500 font-semibold">Allotted Room:</span>
          <strong id="eccm-room-no" class="text-indigo-600 font-extrabold">-</strong>
        </div>
        <div class="flex justify-between items-center border-t border-dashed border-slate-200 pt-2">
          <span class="text-slate-500 font-semibold">Check-Out Date:</span>
          <span id="eccm-checkout-date" class="font-mono font-extrabold text-emerald-700">-</span>
        </div>
        <div class="flex justify-between items-center border-t border-dashed border-slate-200 pt-2">
          <span class="text-slate-500 font-semibold">Check-Out Time:</span>
          <span id="eccm-checkout-time" class="font-mono font-extrabold text-emerald-700">-</span>
        </div>
      </div>

      <input type="hidden" id="eccm-booking-id" value="">
    </div>

    <div class="py-3 px-5 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5 items-center">
      <button type="button" onclick="closeEarlyCheckoutConfirmModal()" class="btn-ui-secondary py-1.5 px-4 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-bold text-xs cursor-pointer">
        Cancel
      </button>
      <button type="button" id="eccm-btn-confirm" onclick="executeEarlyCheckout()" class="py-1.5 px-5 rounded-lg border-none bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white text-xs font-extrabold cursor-pointer inline-flex items-center gap-1.5 shadow-sm transition-all">
        <i class="fa-solid fa-check"></i> Yes, Early Check Out
      </button>
    </div>

  </div>
</div>

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

  // Open Folio Modal by Booking ID (forceFresh = true fetches fresh DB data via AJAX)
  function openFolioModalById(bookingId, forceFresh = false) {
    let folio = (!forceFresh && Array.isArray(window.allFoliosData)) 
      ? window.allFoliosData.find(f => Number(f.id) === Number(bookingId)) 
      : null;

    if (folio) {
      populateAndOpenFolioModal(folio);
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
          populateAndOpenFolioModal(data.folio);
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

  function populateAndOpenFolioModal(folio) {
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

      const formatCheckoutMealPlanHtml = (name, price, origPrice) => {
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
      if (elPlan) elPlan.innerHTML = formatCheckoutMealPlanHtml(folio.plan_name, folio.plan_price, folio.plan_original_price);
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
      if (elMealVal) elMealVal.innerHTML = formatCheckoutMealPlanHtml(folio.plan_name, folio.plan_price, folio.plan_original_price);

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

    // Reset scroll to top
    const modalBody = document.getElementById('folio-modal-body');
    if (modalBody) {
      modalBody.scrollTop = 0;
    }
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
    const isSettled = activeFolioData && (Number(activeFolioData.balance) <= 0.01 || (activeFolioData.status || '').toLowerCase() === 'settlement done');

    let html = '';
    rooms.forEach(r => {
      const isVacated = r.is_vacated === true;
      const roomStatusBadge = isVacated
        ? `<span class="bg-rose-50 text-rose-600 border border-rose-200 text-[10px] font-extrabold px-2 py-0.5 rounded-md inline-flex items-center gap-1"><i class="fa-solid fa-broom"></i> Vacated (Housekeeping Dirty)</span>`
        : `<span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold px-2 py-0.5 rounded-md inline-flex items-center gap-1"><i class="fa-solid fa-bed"></i> Reserved / Occupied</span>`;

      html += `
        <div class="room-alloc-card bg-slate-50/70 border border-slate-200/80 rounded-xl p-3 mb-2.5">
          <div class="room-alloc-header flex justify-between items-center pb-2 mb-2 border-b border-slate-200/60 flex-wrap gap-2">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="room-tag inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-mono font-extrabold text-xs bg-indigo-50 text-indigo-700 border border-indigo-200">
                <i class="fa-solid fa-door-open text-[10px]"></i> ${escapeHtml(r.room_number)}
              </span>
              <span class="text-xs font-extrabold text-slate-900">Floor ${escapeHtml(r.floor)}</span>
              <span class="text-slate-300">•</span>
              <span class="text-[11px] text-slate-500 font-semibold">${escapeHtml(r.category)}</span>
              <span class="text-slate-300">•</span>
              <span class="text-[11px] text-slate-500">${escapeHtml(r.bedding)}</span>
              ${roomStatusBadge}
            </div>
            <div class="text-[11px] font-mono font-extrabold text-indigo-600">
              ${formatINR(r.rate)} / night
            </div>
          </div>
          <div>
            <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">
              Registered Occupants in this Room:
            </div>
            <div class="flex flex-col gap-1.5">
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
                <span class="badge-checked-out-occupant text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md inline-flex items-center gap-1" title="This occupant is checked out">
                  <i class="fa-solid fa-circle-check text-emerald-500"></i> Checked Out ${dtText ? '(' + escapeHtml(dtText) + ')' : ''}
                </span>
              `;
            } else if (isSettled && activeFolioData && activeFolioData.status !== 'Checked Out' && activeFolioData.status !== 'Cancelled') {
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
              <span class="badge-checked-out-occupant text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md inline-flex items-center gap-1" title="This occupant is checked out">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> Checked Out ${dtText ? '(' + escapeHtml(dtText) + ')' : ''}
              </span>
            `;
          }

          html += `
            <div class="occupant-badge-row flex items-center justify-between py-1.5 px-2.5 bg-white border border-slate-200/80 rounded-lg text-xs ${isCheckedOut ? 'opacity-70 bg-slate-50' : ''}">
              <div class="flex items-center gap-1.5">
                <i class="fa-solid ${occ.is_primary ? 'fa-star text-amber-500' : 'fa-user text-indigo-500'}"></i>
                <strong class="${isCheckedOut ? 'text-slate-500 line-through' : 'text-slate-800'} font-bold">${escapeHtml(occ.name)}</strong>
                ${occ.is_primary ? '<span class="bg-amber-100 text-amber-800 text-[9px] font-extrabold px-1.5 py-0.5 rounded">Primary</span>' : ''}
              </div>
              <div class="flex items-center gap-2.5 text-slate-500 font-mono text-[11px]">
                <span><i class="fa-solid fa-phone text-[9px] text-emerald-500"></i> ${escapeHtml(occ.mobile)}</span>
                <span class="text-slate-300">•</span>
                <span>${escapeHtml(occ.id_card_type)}: ${escapeHtml(occ.id_card_number)}</span>
                ${actionMarkup}
              </div>
            </div>
          `;
        });
      } else {
        html += '<div class="text-[11px] text-slate-400">No occupants assigned yet.</div>';
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
    const billRateEl = document.getElementById('fmdl-bill-rate');
    if (billRateEl) billRateEl.textContent = formatINR(folio.room_rate);

    document.getElementById('fmdl-gross-amount').textContent = formatINR(folio.total_amount);
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
    document.getElementById('fmdl-calc-row').textContent = `${formatINR(folio.room_rate)} × ${folio.total_nights} Nights = ${formatINR(folio.total_amount)}`;
    document.getElementById('fmdl-advance-row').textContent = formatINR(folio.advance_amount);
    document.getElementById('fmdl-settlement-row').textContent = formatINR(folio.settlement_paid);
    
    const totalPaidRow = document.getElementById('fmdl-totalpaid-row');
    if (totalPaidRow) totalPaidRow.textContent = formatINR(folio.total_paid);

    document.getElementById('fmdl-discount-row').textContent = folio.discount_amount > 0 ? `${formatINR(folio.discount_amount)} (${folio.discount_percentage}%)` : 'None';
    document.getElementById('fmdl-tax-row').textContent = folio.gst_amount > 0 ? `${formatINR(folio.gst_amount)} (${folio.gst_percentage}% ${folio.gst_type})` : 'Zero Tax / Exempt';
    
    const totalServiceCost = Number(folio.total_room_service_charges || 0);

    const payableEl = document.getElementById('fmdl-payable-amount');
    if (payableEl) {
      const netPayable = (folio.effective_payable !== undefined) ? Number(folio.effective_payable) : (Number(folio.payable_amount || 0) + totalServiceCost);
      payableEl.textContent = formatINR(netPayable);
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

    // Render itemized In-Room Dining / Restaurant orders list
    if (folio.room_service_orders && folio.room_service_orders.length > 0) {
      if (servicesBox) servicesBox.style.display = 'block';
      if (servicesCount) servicesCount.textContent = folio.room_service_orders.length;

      const totalFolioCharged = folio.room_service_orders
        .filter(ro => ro.payment_status === 'Charged to Room Folio')
        .reduce((sum, ro) => sum + Number(ro.total_amount || 0), 0);
      const totalDirectPaid = folio.room_service_orders
        .filter(ro => ro.payment_status && ro.payment_status !== 'Charged to Room Folio')
        .reduce((sum, ro) => sum + Number(ro.total_amount || 0), 0);

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

  function toggleFolioSettlementPanel() {
    const panel = document.getElementById('fmdl-settlement-interactive-panel');
    if (panel.style.display === 'none' || panel.style.display === '') {
      panel.style.display = 'block';
      if (activeFolioData) {
        document.getElementById('fmdl-input-settle-amount').value = Number(activeFolioData.balance || 0).toFixed(2);
        document.getElementById('fmdl-btn-payfull-val').textContent = formatINR(activeFolioData.balance);
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
        html: `Cannot check out reservation with outstanding balance of <strong class="text-rose-600 font-extrabold">${formatINR(activeFolioData.balance)}</strong>.<br><br>Please complete settlement first before checking out.`,
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
          if (data.is_all_checked_out) {
            window.location.reload();
          } else if (activeFolioData && activeFolioData.id) {
            openEarlyCheckoutRoomsModalById(activeFolioData.id);
            if (document.getElementById('folio-details-modal')?.classList.contains('open')) {
              openFolioModalById(activeFolioData.id, true);
            }
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

  /**
   * Action: Quick Check-Out Button
   * Checks if reservation is settled:
   * - If settled: confirms & automatically executes check out with success alert
   * - If not settled: informs user that dues are pending
   */
  function handleRowCheckout(bookingId, isSettled, balanceVal, primaryGuestName, reserveId, status, isCompany) {
    if (status === 'Checked Out') {
      Swal.fire({
        icon: 'info',
        title: 'Already Checked Out',
        text: 'This reservation is already checked out.',
        confirmButtonColor: '#4f46e5'
      });
      return;
    }

    const canCheckout = isSettled || !!isCompany;

    if (!canCheckout && balanceVal > 0.01) {
      Swal.fire({
        icon: 'warning',
        title: 'Not Settled Yet',
        html: `Cannot check out reservation <strong>#${escapeHtml(reserveId)}</strong>.<br><br>Outstanding balance of <strong class="text-rose-600 text-base font-black">₹ ${Number(balanceVal).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong> is not settled yet.<br><br><span class="text-xs text-slate-500">Please complete all bill settlements before checking out.</span>`,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'OK'
      });
      return;
    }

    const companyNote = (isCompany && balanceVal > 0.01)
      ? `<div class="bg-fuchsia-50 border border-fuchsia-300 text-fuchsia-800 p-2 px-2.5 rounded-lg text-xs font-bold mb-2.5 text-left"><i class="fa-solid fa-building mr-1"></i> Company Billing: Balance of ₹ ${Number(balanceVal).toFixed(2)} will remain on corporate ledger.</div>`
      : 'Guest payments are settled.<br><br>';

    Swal.fire({
      title: 'Confirm Check-Out',
      html: `${companyNote}Proceed to check out Booking <strong>#${escapeHtml(reserveId)}</strong> for <strong>${escapeHtml(primaryGuestName)}</strong>?<br><br><span class="text-xs text-slate-500">All assigned room(s) will be automatically scheduled for Housekeeping.</span>`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#10b981',
      cancelButtonColor: '#64748b',
      confirmButtonText: '<i class="fa-solid fa-right-from-bracket"></i> Yes, Check Out Now',
      cancelButtonText: 'Cancel'
    }).then(result => {
      if (!result.isConfirmed) return;

      Swal.fire({
        title: 'Processing Check-Out...',
        text: 'Scheduling room housekeeping & finalizing departure.',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });

      fetch(`{{ url('front-office/checkout') }}/${bookingId}/checkout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          remarks: 'Direct checkout from Checkout Counter'
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Checked Out Successfully!',
            text: data.message || 'Reservation successfully checked out! Room(s) scheduled for housekeeping.',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({
            icon: 'warning',
            title: 'Checkout Notice',
            text: data.error || data.message || 'Checkout failed.',
            confirmButtonColor: '#4f46e5'
          });
        }
      })
      .catch(err => {
        console.error(err);
        Swal.fire({
          icon: 'error',
          title: 'System Error',
          text: 'Network error while processing checkout. Please try again.',
          confirmButtonColor: '#4f46e5'
        });
      });
    });
  }

  /**
   * Action: Early Check-Out Button
   * Opens room and floor wise occupants modal
   */
  function openEarlyCheckoutRoomsModalById(bookingId) {
    Swal.fire({
      title: 'Loading Guest Details...',
      text: 'Fetching room & occupant details.',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });

    fetch(`{{ url('front-office/checkout') }}/${bookingId}/details`)
      .then(res => res.json())
      .then(data => {
        Swal.close();
        if (!data.success || !data.folio) {
          Swal.fire({
            icon: 'error',
            title: 'Not Found',
            text: 'Could not load reservation details.',
            confirmButtonColor: '#4f46e5'
          });
          return;
        }

        activeFolioData = data.folio;
        populateEarlyCheckoutRoomsModal(data.folio);
      })
      .catch(err => {
        Swal.close();
        console.error(err);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to fetch reservation dossier.',
          confirmButtonColor: '#4f46e5'
        });
      });
  }

  function populateEarlyCheckoutRoomsModal(folio) {
    document.getElementById('ecrm-reserve-id').textContent = folio.reserve_id || ('RES-' + folio.id);
    document.getElementById('ecrm-primary-guest').textContent = folio.primary_guest?.name || 'Guest';

    const isCompanyFolio = (folio.registration_type || '').toLowerCase() === 'company' || !!folio.company;
    const isSettled = (Number(folio.balance) <= 0.01) || (folio.status || '').toLowerCase() === 'settlement done';
    const canEarlyCheckout = isSettled || isCompanyFolio;
    const statusBadge = document.getElementById('ecrm-status-badge');
    const noticeEl = document.getElementById('ecrm-settle-notice');

    if (isSettled) {
      statusBadge.className = 'badge-settle-pill settled';
      statusBadge.innerHTML = '<i class="fa-solid fa-circle-check text-emerald-500"></i> Settled (Clear)';
      noticeEl.className = 'mb-3.5 p-3 rounded-xl text-xs flex items-center justify-between gap-2.5 bg-emerald-50 border border-emerald-200 text-emerald-800';
      noticeEl.innerHTML = '<div><i class="fa-solid fa-check-circle text-emerald-600"></i> <strong>Payments Cleared:</strong> All dues are settled. You can early check-out individual occupants below.</div>';
    } else if (isCompanyFolio) {
      statusBadge.className = 'badge-settle-pill bg-fuchsia-50 text-fuchsia-800 border border-fuchsia-200';
      statusBadge.innerHTML = `<i class="fa-solid fa-building text-fuchsia-600"></i> Company Account (₹ ${Number(folio.balance).toFixed(2)} Billed)`;
      noticeEl.className = 'mb-3.5 p-3 rounded-xl text-xs flex items-center justify-between gap-2.5 bg-fuchsia-50 border border-fuchsia-200 text-fuchsia-800';
      noticeEl.innerHTML = `<div><i class="fa-solid fa-building text-fuchsia-600"></i> <strong>Company Corporate Account:</strong> Outstanding balance of <strong>₹ ${Number(folio.balance).toFixed(2)}</strong> is retained on corporate ledger. Occupants can early check-out without pre-settlement.</div>`;
    } else {
      statusBadge.className = 'badge-settle-pill unsettled';
      statusBadge.innerHTML = `<i class="fa-solid fa-clock text-rose-500"></i> Unsettled (₹ ${Number(folio.balance).toFixed(2)} Due)`;
      noticeEl.className = 'mb-3.5 p-3 rounded-xl text-xs flex items-center justify-between gap-2.5 bg-rose-50 border border-rose-200 text-rose-800';
      noticeEl.innerHTML = `<div><i class="fa-solid fa-triangle-exclamation text-rose-600"></i> <strong>Payment Due:</strong> Outstanding balance of <strong>₹ ${Number(folio.balance).toFixed(2)}</strong> is not settled yet. Complete settlement before early check-out.</div>`;
    }

    const grid = document.getElementById('ecrm-rooms-card-grid');
    const rooms = folio.rooms || [];
    if (rooms.length === 0) {
      grid.innerHTML = '<div class="text-slate-400 text-xs italic p-2.5">No allocated rooms found for this booking.</div>';
    } else {
      let totalOccupants = 0;
      rooms.forEach(r => totalOccupants += (r.occupants ? r.occupants.length : 0));
      const hasMultiple = totalOccupants > 1;

      let html = '';
      rooms.forEach(r => {
        const isVacated = r.is_vacated === true;
        const roomStatusBadge = isVacated
          ? `<span class="bg-rose-50 text-rose-600 border border-rose-200 text-[10px] font-extrabold px-2 py-0.5 rounded-md inline-flex items-center gap-1"><i class="fa-solid fa-broom"></i> Vacated (Housekeeping Dirty)</span>`
          : `<span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold px-2 py-0.5 rounded-md inline-flex items-center gap-1"><i class="fa-solid fa-bed"></i> Reserved / Occupied</span>`;

        html += `
          <div class="room-alloc-card bg-white border border-slate-200 rounded-xl p-3.5 mb-3 shadow-2xs">
            <div class="room-alloc-header flex justify-between items-center border-b border-slate-100 pb-2.5 mb-2.5 flex-wrap gap-2">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="room-tag inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-mono font-extrabold text-xs bg-indigo-50 text-indigo-700 border border-indigo-200">
                  <i class="fa-solid fa-door-open text-[10px]"></i> ${escapeHtml(r.room_number)}
                </span>
                <span class="text-xs font-extrabold text-slate-900">Floor ${escapeHtml(r.floor)}</span>
                <span class="text-slate-300">•</span>
                <span class="text-[11px] text-slate-500 font-semibold">${escapeHtml(r.category)}</span>
                <span class="text-slate-300">•</span>
                <span class="text-[11px] text-slate-500">${escapeHtml(r.bedding)}</span>
                ${roomStatusBadge}
              </div>
              <div class="text-[11px] font-mono font-extrabold text-indigo-600">
                ${formatINR(r.rate)} / night
              </div>
            </div>
            <div>
              <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1.5">
                Registered Occupants in this Room:
              </div>
              <div class="flex flex-col gap-1.5">
        `;

        if (r.occupants && r.occupants.length > 0) {
          r.occupants.forEach(occ => {
            const isCheckedOut = occ.is_checked_out || (occ.status === 'Checked Out');
            let actionMarkup = '';

            if (isCheckedOut) {
              const dtParts = [];
              if (occ.checkout_date) dtParts.push(occ.checkout_date);
              if (occ.checkout_time) dtParts.push(occ.checkout_time);
              const dtText = dtParts.join(' ');
              actionMarkup = `
                <span class="badge-checked-out-occupant text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md inline-flex items-center gap-1" title="This occupant is checked out">
                  <i class="fa-solid fa-circle-check text-emerald-500"></i> Checked Out ${dtText ? '(' + escapeHtml(dtText) + ')' : ''}
                </span>
              `;
            } else if (!canEarlyCheckout) {
              actionMarkup = `
                <button type="button" 
                        class="btn-early-checkout bg-slate-100 text-slate-500 border-slate-300 cursor-not-allowed" 
                        onclick="Swal.fire({icon: 'warning', title: 'Not Settled Yet', html: 'Cannot early check out this occupant. Reservation balance of <strong>₹ ${Number(folio.balance).toFixed(2)}</strong> is not settled yet.<br><br>Please settle all dues first.', confirmButtonColor: '#4f46e5'})" 
                        title="Dues not settled yet">
                  <i class="fa-solid fa-lock"></i> Settle Dues First
                </button>
              `;
            } else if (hasMultiple) {
              actionMarkup = `
                <button type="button" 
                        class="btn-early-checkout" 
                        onclick="openEarlyCheckoutConfirmModal(${occ.booking_id}, '${escapeHtml(occ.name).replace(/'/g, "\\'")}', '${escapeHtml(r.room_number).replace(/'/g, "\\'")}')" 
                        title="Early Check-out this specific occupant from Room ${escapeHtml(r.room_number)}">
                  <i class="fa-solid fa-person-walking-arrow-right"></i> Early Checkout
                </button>
              `;
            } else {
              actionMarkup = `
                <button type="button" 
                        class="btn-early-checkout bg-rose-50 text-rose-600 border-rose-300 hover:bg-rose-600 hover:text-white hover:border-rose-600" 
                        onclick="closeEarlyCheckoutRoomsModal(); handleRowCheckout(${folio.id}, true, 0, '${escapeHtml(occ.name).replace(/'/g, "\\'")}', '${escapeHtml(folio.reserve_id).replace(/'/g, "\\'")}', '${escapeHtml(folio.status).replace(/'/g, "\\'")}')" 
                        title="Check out this single-occupant reservation">
                  <i class="fa-solid fa-right-from-bracket"></i> Check Out
                </button>
              `;
            }

            html += `
              <div class="occupant-badge-row flex items-center justify-between py-1.5 px-2.5 bg-slate-50 border border-slate-200/80 rounded-lg text-xs ${isCheckedOut ? 'opacity-70' : ''}">
                <div class="flex items-center gap-1.5">
                  <i class="fa-solid ${occ.is_primary ? 'fa-star text-amber-500' : 'fa-user text-indigo-500'}"></i>
                  <strong class="${isCheckedOut ? 'text-slate-500 line-through' : 'text-slate-800'} font-bold">${escapeHtml(occ.name)}</strong>
                  ${occ.is_primary ? '<span class="bg-amber-100 text-amber-800 text-[9px] font-extrabold px-1.5 py-0.5 rounded">Primary</span>' : ''}
                </div>
                <div class="flex items-center gap-2.5 text-slate-500 font-mono text-[11px]">
                  <span><i class="fa-solid fa-phone text-[9px] text-emerald-500"></i> ${escapeHtml(occ.mobile || 'N/A')}</span>
                  <span class="text-slate-300">•</span>
                  <span>${escapeHtml(occ.id_card_type || 'ID')}: ${escapeHtml(occ.id_card_number || 'N/A')}</span>
                  ${actionMarkup}
                </div>
              </div>
            `;
          });
        } else {
          html += '<div class="text-[11px] text-slate-400">No occupants assigned yet.</div>';
        }

        html += `
              </div>
            </div>
          </div>
        `;
      });
      grid.innerHTML = html;
    }

    const modal = document.getElementById('early-checkout-rooms-modal');
    if (modal) {
      modal.classList.add('open');
      modal.style.setProperty('display', 'flex', 'important');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeEarlyCheckoutRoomsModal() {
    const modal = document.getElementById('early-checkout-rooms-modal');
    if (modal) {
      modal.classList.remove('open');
      modal.style.setProperty('display', 'none', 'important');
    }
    document.body.style.overflow = '';
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

    const ecrmModalBackdrop = document.getElementById('early-checkout-rooms-modal');
    if (ecrmModalBackdrop) {
      ecrmModalBackdrop.addEventListener('click', function(e) {
        if (e.target === this) {
          closeEarlyCheckoutRoomsModal();
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

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeEarlyCheckoutConfirmModal();
        closeEarlyCheckoutRoomsModal();
        closeFolioModal();
      }
    });

    // Delegated click handler for booking numbers
    document.addEventListener('click', function(e) {
      const trigger = e.target.closest('.folio-id-text, .folio-number-sub');
      if (trigger) {
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

    let roomRowsHtml = '';
    (f.rooms || []).forEach(r => {
      const occupantNames = (r.occupants || []).map(o => o.name).join(', ');
      roomRowsHtml += `
        <tr>
          <td class="p-2 border border-slate-200 font-bold">${escapeHtml(r.room_number)} (Floor ${escapeHtml(r.floor)})</td>
          <td class="p-2 border border-slate-200">${escapeHtml(r.category)} • ${escapeHtml(r.bedding)}</td>
          <td class="p-2 border border-slate-200">${escapeHtml(occupantNames || 'Primary Guest')}</td>
          <td class="p-2 border border-slate-200 text-right font-mono">${formatINR(r.rate)}</td>
        </tr>
      `;
    });

    const printArea = document.getElementById('print-area') || document.getElementById('folio-print-area');
    if (printArea) {
      printArea.innerHTML = `
        <div class="font-sans p-6 text-slate-800 max-w-4xl mx-auto">
          <div class="flex justify-between items-start border-b-2 border-indigo-600 pb-3.5 mb-5">
            <div>
              <h1 class="m-0 text-2xl text-slate-900 uppercase font-black">Hotel Sagar Sonnet</h1>
              <div class="text-xs text-slate-500 mt-1">Luxury Stays &amp; Hospitality Services • Live PMS Dossier</div>
            </div>
            <div class="text-right">
              <div class="text-lg font-bold text-indigo-600">TAX INVOICE / SETTLEMENT</div>
              <div class="text-xs font-mono text-slate-700 mt-1">Settlement #: ${escapeHtml(f.folio_number)}</div>
              <div class="text-xs font-mono text-slate-500">Booking #: ${escapeHtml(f.reserve_id)}</div>
            </div>
          </div>

          <div class="flex justify-between mb-5 text-xs leading-relaxed">
            <div class="flex-1">
              <strong class="text-indigo-600 text-sm">GUEST DETAILS:</strong><br>
              Name: <strong>${escapeHtml(pg.name)}</strong><br>
              Phone: ${escapeHtml(pg.mobile)}<br>
              Email: ${escapeHtml(pg.email)}<br>
              Address: ${escapeHtml(pg.address || 'N/A')}<br>
              ID Proof: ${escapeHtml(pg.id_card_type)} - ${escapeHtml(pg.id_card_number)}
            </div>
            <div class="flex-1 text-right">
              <strong class="text-indigo-600 text-sm">STAY INFORMATION:</strong><br>
              Check-In: <strong>${escapeHtml(f.checkin_date_fmt)} (${escapeHtml(f.checkin_time_fmt)})</strong><br>
              Check-Out: <strong>${escapeHtml(f.checkout_date_fmt)} (${escapeHtml(f.checkout_time_fmt)})</strong><br>
              Duration: <strong>${escapeHtml(f.total_nights)} Night(s)</strong><br>
              Meal Plan: <strong>${escapeHtml(f.plan_name)}</strong><br>
              Total Occupants: <strong>${escapeHtml(f.total_guests_count)} Person(s)</strong>
            </div>
          </div>

          <table class="w-full border-collapse mb-5 text-xs">
            <thead>
              <tr class="bg-slate-100 text-left">
                <th class="p-2 border border-slate-300">Room / Floor</th>
                <th class="p-2 border border-slate-300">Category &amp; Bedding</th>
                <th class="p-2 border border-slate-300">Occupants</th>
                <th class="p-2 border border-slate-300 text-right">Tariff / Night</th>
              </tr>
            </thead>
            <tbody>
              ${roomRowsHtml}
            </tbody>
          </table>

          <div class="flex justify-end mb-6">
            <div class="w-80 text-xs">
              <div class="flex justify-between py-1 border-b border-slate-200">
                <span>Gross Room Charges:</span>
                <span class="font-mono">${formatINR(f.total_amount)}</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-200 text-emerald-600">
                <span>Discount (${escapeHtml(f.discount_percentage)}%):</span>
                <span class="font-mono">- ${formatINR(f.discount_amount)}</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-200 text-indigo-600">
                <span>Tax (${escapeHtml(f.gst_percentage)}% GST):</span>
                <span class="font-mono">+ ${formatINR(f.gst_amount)}</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-300 font-bold">
                <span>Net Payable:</span>
                <span class="font-mono">${formatINR(f.payable_amount)}</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-200 text-emerald-600">
                <span>Advance Paid:</span>
                <span class="font-mono">${formatINR(f.advance_amount)}</span>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-200 text-indigo-600">
                <span>Settlements Paid:</span>
                <span class="font-mono">${formatINR(f.settlement_paid)}</span>
              </div>
              <div class="flex justify-between py-1 text-sm font-extrabold text-emerald-600">
                <span>Total Paid:</span>
                <span class="font-mono">${formatINR(f.total_paid)}</span>
              </div>
              <div class="flex justify-between py-1.5 text-base font-black ${f.balance > 0 ? 'text-rose-600' : 'text-emerald-600'}">
                <span>Balance Due:</span>
                <span class="font-mono">${formatINR(f.balance)}</span>
              </div>
            </div>
          </div>

          <div class="border-t border-dashed border-slate-300 pt-3.5 flex justify-between items-center text-xs text-slate-500">
            <div>Status: <strong class="uppercase">${escapeHtml(f.status)}</strong></div>
            <div>Authorized Front Desk Signature: ______________________</div>
          </div>
        </div>
      `;
      window.print();
    }
  }

  // Explicit window bindings
  window.openFolioModalById = openFolioModalById;
  window.populateAndOpenFolioModal = populateAndOpenFolioModal;
  window.closeFolioModal = closeFolioModal;
  window.openEarlyCheckoutConfirmModal = openEarlyCheckoutConfirmModal;
  window.closeEarlyCheckoutConfirmModal = closeEarlyCheckoutConfirmModal;
  window.executeEarlyCheckout = executeEarlyCheckout;
  window.executeFolioSettlement = executeFolioSettlement;
  window.executeFolioCheckout = executeFolioCheckout;
  window.toggleFolioSettlementPanel = toggleFolioSettlementPanel;
  window.setFullFolioSettlementAmount = setFullFolioSettlementAmount;
  window.printFolioInvoice = printFolioInvoice;
</script>
@endpush
