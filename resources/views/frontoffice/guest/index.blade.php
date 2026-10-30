@extends('frontoffice.layouts.app')

@section('title', 'Guest CRM & Master Directory - Hotel Sagar Sonnet CRM / PMS')


@section('content')
@php
  // Map page guests for instant view modal opening
  $serializedGuests = collect($guests->items())->map(function($g) {
    $advVal = (float)$g->advance_amount;
    $settlePayments = (float)($g->settlements?->sum('paid_amount') ?: $g->latestBooking?->payments?->where('payment_type', 'Settlement')->sum('amount'));
    $totalPaid = round($advVal + $settlePayments, 2);
    $balVal = (float)($g->balance ?? 0);

    $hasSettled = ($g->settlements && $g->settlements->where('status', 'Settled')->isNotEmpty())
      || ($g->latestBooking?->settlements && $g->latestBooking->settlements->where('status', 'Settled')->isNotEmpty())
      || ($g->latestBooking?->payments && $g->latestBooking->payments->where('payment_type', 'Settlement')->isNotEmpty())
      || strtolower($g->status ?? '') === 'settlement done'
      || ((float)$balVal <= 0.01 && (float)$g->payable_amount > 0 && ($advVal > 0 || ($g->latestBooking?->payments && $g->latestBooking->payments->isNotEmpty())));

    $isLocked = $hasSettled || in_array($g->status, ['Settlement Done', 'Checked Out']);

    return [
      'id' => $g->id,
      'title_id' => $g->title_id,
      'title' => $g->title?->name ?? '',
      'raw_name' => $g->guest_name,
      'guest_name' => ($g->title?->name ? $g->title->name . ' ' : '') . $g->guest_name,
      'reserve_id' => $g->reserve_id ?: ('RES-' . $g->id),
      'folio_number' => $g->folio_number ?: ('FOL-' . ($g->room?->room_number ?? '100') . '-' . $g->id),
      'mobile' => $g->mobile ?: 'N/A',
      'raw_mobile' => $g->mobile ?? '',
      'email' => $g->email ?: 'N/A',
      'raw_email' => $g->email ?? '',
      'city' => $g->city ?: 'N/A',
      'raw_city' => $g->city ?? '',
      'guest_address' => $g->guest_address ?: 'N/A',
      'raw_address' => $g->guest_address ?? '',
      'nationality_id' => $g->nationality_id,
      'nationality' => $g->nationality?->name ?? 'Indian',
      'id_card_type_id' => $g->id_card_type_id,
      'id_card_type' => $g->idCardType?->name ?? 'N/A',
      'id_card_number' => $g->id_card_number ?: 'N/A',
      'raw_id_card_number' => $g->id_card_number ?? '',
      'registration_type' => $g->registrationType?->name ?? 'Regular',
      'reservation_mode' => $g->reservationMode?->name ?? 'Front Desk',
      'has_privilege_card' => (bool)$g->has_privilege_card,
      'privilege_card_no' => $g->privilege_card_no ?: 'N/A',
      'raw_privilege_card_no' => $g->privilege_card_no ?? '',
      'company_id' => $g->company_id,
      'new_company_name' => $g->new_company_name ?? '',
      'new_company_address' => $g->new_company_address ?? '',
      'new_company_gstin' => $g->new_company_gstin ?? '',
      'new_company_phone' => $g->new_company_phone ?? '',
      'is_locked' => $isLocked,
      'room_number' => (string)($g->room?->room_number ?? 'Pending'),
      'room_category' => strtoupper($g->room?->categoryRelation?->name ?? ($g->room?->category ?? 'DELUXE')),
      'room_floor' => (string)($g->room?->floorRelation?->floor ?? ($g->room?->floor ?? '1')),
      'room_bedding' => $g->room?->beddingConfigRelation?->name ?? 'Standard',
      'plan_name' => $g->plan?->plan_name ?? ($g->latestBooking?->plan?->plan_name ?? 'Room Only (EP)'),
      'room_rate' => (float)($g->room_rate ?: ($g->room?->rate ?: 0)),
      'reserve_date' => $g->reserve_date ? $g->reserve_date->format('d M Y') : date('d M Y'),
      'reserve_time' => $g->reserve_time ? date('h:i A', strtotime($g->reserve_time)) : '12:00 PM',
      'checkout_date' => $g->checkout_date ? $g->checkout_date->format('d M Y') : date('d M Y', strtotime('+1 day')),
      'checkout_time' => $g->checkout_time ? date('h:i A', strtotime($g->checkout_time)) : '11:00 AM',
      'total_nights' => (int)($g->total_nights ?: 1),
      'total_amount' => (float)$g->total_amount,
      'discount_percentage' => (float)$g->discount_percentage,
      'discount_amount' => (float)$g->discount_amount,
      'gst_type' => $g->gst_type ?: 'exclusive',
      'gst_percentage' => (float)$g->gst_percentage,
      'gst_amount' => (float)$g->gst_amount,
      'payable_amount' => (float)$g->payable_amount,
      'advance_amount' => $advVal,
      'settlement_paid' => $settlePayments,
      'total_paid' => $totalPaid,
      'balance' => $balVal,
      'payment_mode' => $g->paymentMode?->name ?? 'Cash / Direct',
      'payment_remarks' => $g->payment_remarks ?: 'None',
      'status' => $g->status ?: 'Confirmed',
      'created_at_fmt' => $g->created_at ? $g->created_at->format('d M Y, h:i A') : 'N/A',
      'company' => $g->company ? [
        'name' => $g->company->name,
        'gstin' => $g->company->gstin ?: 'N/A',
        'address' => $g->company->address ?: 'N/A',
        'phone' => $g->company->phone ?: 'N/A',
        'discount_type' => $g->company->discount_type,
        'discount_value' => (float)$g->company->discount_value,
      ] : ($g->new_company_name ? [
        'name' => $g->new_company_name,
        'gstin' => $g->new_company_gstin ?: 'N/A',
        'address' => $g->new_company_address ?: 'N/A',
        'phone' => $g->new_company_phone ?: 'N/A',
        'discount_type' => 'percentage',
        'discount_value' => 0,
      ] : null),
    ];
  })->values();
@endphp

<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">
    
    <!-- Clean Header Card -->
    <div class="guest-header-card">
      <div class="flex justify-between items-center flex-wrap gap-3">
        <div>
          <h2 class="m-0 text-lg font-extrabold text-slate-800 flex items-center gap-2.5 flex-wrap">
            <i class="fa-solid fa-address-card text-indigo-600"></i> 
            Guest Master &amp; CRM Directory
            <span class="text-[11px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200 px-2.5 py-0.5 rounded-full">
              {{ $guests->total() }} Records
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
          <div class="text-xs text-slate-500 mt-0.5">
            @if(!empty($fromDate) && !empty($toDate) && $fromDate === $toDate)
              Showing guest records with check-in on <strong>{{ \Carbon\Carbon::parse($fromDate)->format('D, d F Y') }}</strong> ({{ $fromDate === $today ? 'Today' : 'Selected Date' }}).
            @elseif(!empty($fromDate) || !empty($toDate))
              Showing guest records with check-in between <strong>{{ !empty($fromDate) ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }}</strong> and <strong>{{ !empty($toDate) ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'End' }}</strong>.
            @else
              Comprehensive guest directory, stay history dossiers, allotted rooms, billing statements, and identity records.
            @endif
          </div>
        </div>
        <div class="flex gap-2 items-center">
          <a href="{{ route('frontoffice.dashboard') }}" class="btn-ui-secondary border border-slate-300 bg-white text-slate-700 no-underline px-3.5 py-1.5 rounded-full text-xs font-bold inline-flex items-center gap-1.5 hover:bg-slate-50 transition">
            <i class="fa-solid fa-table-cells"></i> Room Rack
          </a>
        </div>
      </div>
    </div>

    <!-- Backend Filter Form & Toolbar -->
    <form method="GET" action="{{ route('frontoffice.guest') }}" id="guest-filter-form" class="guest-toolbar">
      <div class="flex justify-between items-center flex-wrap gap-3 w-full">
        <div class="flex gap-3 items-center flex-1 flex-wrap">
          <!-- Search Box -->
          <div class="search-box-wrap">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" 
                   name="search" 
                   id="guest-crm-search-input" 
                   value="{{ request('search') }}" 
                   placeholder="Search by Name, Phone, Email, ID, Folio, City..." 
                   onkeydown="if(event.key==='Enter'){this.form.submit();}">
            @if(request()->filled('search'))
              <a href="{{ route('frontoffice.guest', request()->except('search', 'page')) }}" 
                 title="Clear search" 
                 class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 no-underline text-xs hover:text-slate-600">
                 <i class="fa-solid fa-xmark"></i>
              </a>
            @endif
          </div>

          <!-- Check-in From & To Date Filter (Default: Today) -->
          <div class="inline-flex items-center gap-1.5">
            <label for="crm-from-date" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              <i class="fa-regular fa-calendar-days text-indigo-600"></i> Check-in From:
            </label>
            <input type="date" 
                   name="from_date" 
                   id="crm-from-date" 
                   class="pms-filter-date" 
                   value="{{ $fromDate ?? '' }}" 
                   onchange="this.form.submit()" 
                   title="Filter by Check-in Date From">

            <label for="crm-to-date" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              To:
            </label>
            <input type="date" 
                   name="to_date" 
                   id="crm-to-date" 
                   class="pms-filter-date" 
                   value="{{ $toDate ?? '' }}" 
                   onchange="this.form.submit()" 
                   title="Filter by Check-in Date To">
          </div>

          <!-- Status Filter Dropdown -->
          <div class="inline-flex items-center gap-1.5">
            <label for="crm-status-filter" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              <i class="fa-solid fa-filter text-indigo-600"></i> Status:
            </label>
            <select name="status" id="crm-status-filter" class="pms-filter-select" onchange="this.form.submit()">
              <option value="all" {{ request('status') === 'all' || !request()->filled('status') ? 'selected' : '' }}>All Statuses</option>
              <option value="Confirmed" {{ request('status') === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
              <option value="Checked In" {{ request('status') === 'Checked In' ? 'selected' : '' }}>Checked In</option>
              <option value="Arrived" {{ request('status') === 'Arrived' ? 'selected' : '' }}>Arrived</option>
              <option value="Stay Over" {{ request('status') === 'Stay Over' ? 'selected' : '' }}>Stay Over</option>
              <option value="Settlement Done" {{ request('status') === 'Settlement Done' ? 'selected' : '' }}>Settlement Done</option>
              <option value="Checked Out" {{ request('status') === 'Checked Out' ? 'selected' : '' }}>Checked Out</option>
              <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>

          <!-- Registration Type Filter -->
          <div class="inline-flex items-center gap-1.5">
            <label for="crm-regtype-filter" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
              <i class="fa-solid fa-id-badge text-indigo-600"></i> Reg Type:
            </label>
            <select name="registration_type" id="crm-regtype-filter" class="pms-filter-select" onchange="this.form.submit()">
              <option value="all" {{ request('registration_type') === 'all' || !request()->filled('registration_type') ? 'selected' : '' }}>All Reg Types</option>
              @foreach($registrationTypes ?? [] as $rt)
                <option value="{{ $rt->name }}" {{ request('registration_type') === $rt->name ? 'selected' : '' }}>{{ $rt->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- All Dates Link (if date filter active) -->
          @if(!empty($fromDate) || !empty($toDate))
            <a href="{{ route('frontoffice.guest', array_merge(request()->except(['from_date', 'to_date', 'page']), ['all_dates' => 1])) }}" 
               class="btn-ui-secondary px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-[11px] font-bold text-slate-600 inline-flex items-center gap-1.5 no-underline hover:bg-slate-50"
               title="View all records without date restrictions">
              <i class="fa-regular fa-calendar"></i> All Dates
            </a>
          @endif

          <!-- Reset Button (defaults back to today) -->
          <a href="{{ route('frontoffice.guest') }}" 
             class="btn-ui-secondary px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-[11px] font-bold text-slate-600 inline-flex items-center gap-1.5 no-underline hover:bg-slate-50"
             title="Reset filters to today's check-ins">
            <i class="fa-solid fa-rotate-left"></i> Today / Reset
          </a>
        </div>

        <!-- Page Size Selector -->
        <div class="flex gap-3 items-center">
          <div class="pms-per-page-wrap">
            <span>Show</span>
            <select name="per_page" class="pms-per-page-select" id="crm-per-page-select" onchange="this.form.submit()">
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

    <!-- Main Guests CRM Table with SL Indexing -->
    <div class="pms-page-content">
      <div class="pms-table-box rounded-xl shadow-xs bg-white overflow-x-auto">
        <table class="pms-table" id="guest-crm-table">
          <thead>
            <tr>
              <th class="w-11 text-center whitespace-nowrap">SL</th>
              <th class="min-w-[130px] text-left whitespace-nowrap">Registration Type</th>
              <th class="min-w-[160px] whitespace-nowrap">Name</th>
              <th class="min-w-[130px] whitespace-nowrap">Phone</th>
              <th class="min-w-[130px] whitespace-nowrap">Room &amp; Floor</th>
              <th class="min-w-[140px] whitespace-nowrap">Check-in</th>
              <th class="min-w-[140px] whitespace-nowrap">Check-out</th>
              <th class="min-w-[110px] text-center whitespace-nowrap">Status</th>
              <th class="min-w-[160px] text-center whitespace-nowrap">Action</th>
            </tr>
          </thead>
          <tbody id="guest-crm-tbody">
            @forelse($guests as $index => $item)
              @php
                $sl = ($guests->currentPage() - 1) * $guests->perPage() + $loop->iteration;
                $statusNormalized = strtolower($item->status ?? 'confirmed');
                $statusClass = match($statusNormalized) {
                  'settlement done', 'settled' => 'settled',
                  'confirmed' => 'confirmed',
                  'arrived' => 'arrived',
                  'checked in', 'checked-in', 'checkedin' => 'checkedin',
                  'stay over', 'stayover' => 'stayover',
                  'checked out', 'checked-out', 'checkedout' => 'checkedout',
                  'cancelled' => 'cancelled',
                  default => 'confirmed'
                };

                // Room & Floor (Start from R then number)
                $rawRoom = $item->room?->room_number ?? null;
                $cleanRoomNum = $rawRoom ? preg_replace('/^r[-_\s]?/i', '', trim($rawRoom)) : null;
                $rRoomDisplay = $cleanRoomNum ? ('R' . $cleanRoomNum) : null;
                $floorDisplay = $item->room?->floorRelation?->floor ?? ($item->room?->floor ?? null);

                // Financials: Paid (Advance + Settlement from settlement table) & Remaining
                $advVal = (float)$item->advance_amount;
                $settleVal = (float)($item->settlements?->sum('paid_amount') ?: $item->latestBooking?->payments?->where('payment_type', 'Settlement')->sum('amount'));
                $paidVal = round($advVal + $settleVal, 2);
                $balanceVal = (float)($item->balance ?? 0);
              @endphp
              <tr class="guest-row" id="row-guest-{{ $item->id }}">
                <!-- SL Indexing -->
                <td class="text-center font-mono font-extrabold text-slate-500 text-xs whitespace-nowrap">
                  {{ $sl }}
                </td>

                <!-- 1. Registration Type -->
                <td class="text-left whitespace-nowrap">
                  <span class="text-[10px] py-1 px-2.5 rounded-md bg-slate-100 text-slate-700 font-bold border border-slate-200 inline-flex items-center gap-1 whitespace-nowrap">
                    <i class="fa-solid fa-id-badge text-[9px] text-indigo-600"></i>
                    {{ $item->registrationType?->name ?? 'Regular' }}
                  </span>
                </td>

                <!-- 2. Name -->
                <td class="whitespace-nowrap">
                  <div id="guest-name-display-{{ $item->id }}" class="font-extrabold text-slate-900 text-xs whitespace-nowrap">
                    {{ ($item->title?->name ? $item->title->name . ' ' : '') . $item->guest_name }}
                  </div>
                  <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1.5 font-mono">
                    <span class="text-indigo-600 font-bold">{{ $item->reserve_id ?: ('RES-' . $item->id) }}</span>
                    <span id="guest-city-wrap-{{ $item->id }}" class="{{ $item->city ? '' : 'hidden' }}">
                      <span class="text-slate-300">•</span>
                      <span class="text-slate-400"><i class="fa-solid fa-location-dot text-[9px]"></i> <span id="guest-city-display-{{ $item->id }}">{{ $item->city }}</span></span>
                    </span>
                  </div>
                </td>

                <!-- 3. Phone -->
                <td class="whitespace-nowrap">
                  <div class="font-mono font-bold text-xs text-slate-800 inline-flex items-center gap-1 whitespace-nowrap">
                    <i class="fa-solid fa-phone text-[10px] text-emerald-500"></i>
                    <span id="guest-phone-display-{{ $item->id }}">{{ $item->mobile ?: 'N/A' }}</span>
                  </div>
                </td>

                <!-- 4. Room & Floor (Start from R then number) -->
                <td class="whitespace-nowrap">
                  @if($rRoomDisplay)
                    <div class="inline-flex items-center gap-1.5">
                      <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded-md font-extrabold text-xs font-mono tracking-wide">
                        <i class="fa-solid fa-door-open text-[10px] mr-1"></i>{{ $rRoomDisplay }}
                      </span>
                    </div>
                    <div class="text-[10px] text-slate-500 font-bold mt-1 flex items-center gap-1">
                      <i class="fa-solid fa-stairs text-[9px] text-slate-400"></i>
                      <span>{{ $floorDisplay ? 'Floor ' . $floorDisplay : 'Floor 1' }}</span>
                      @if($item->room?->categoryRelation?->name || $item->room?->category)
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-400">{{ $item->room?->categoryRelation?->name ?? $item->room?->category }}</span>
                      @endif
                    </div>
                  @else
                    <span class="text-xs text-slate-400 italic">
                      <i class="fa-solid fa-hourglass-half text-[10px]"></i> Unassigned
                    </span>
                  @endif
                </td>

                <!-- 5. Checkin (date & time) -->
                <td class="whitespace-nowrap">
                  <div class="font-bold text-slate-800 text-xs inline-flex items-center gap-1 whitespace-nowrap">
                    <i class="fa-regular fa-calendar-check text-indigo-600 text-[11px]"></i>
                    <span>{{ $item->reserve_date ? \Carbon\Carbon::parse($item->reserve_date)->format('d M Y') : 'N/A' }}</span>
                  </div>
                  @if($item->reserve_time)
                    <div class="text-[10px] text-slate-500 font-mono mt-0.5 whitespace-nowrap">
                      <i class="fa-regular fa-clock text-[9px] text-slate-400"></i> {{ date('h:i A', strtotime($item->reserve_time)) }}
                    </div>
                  @endif
                </td>

                <!-- 6. Checkout (date & time) -->
                <td class="whitespace-nowrap">
                  <div class="font-bold text-slate-800 text-xs inline-flex items-center gap-1 whitespace-nowrap">
                    <i class="fa-regular fa-calendar-xmark text-rose-600 text-[11px]"></i>
                    <span>{{ $item->checkout_date ? \Carbon\Carbon::parse($item->checkout_date)->format('d M Y') : 'N/A' }}</span>
                  </div>
                  @if($item->checkout_time)
                    <div class="text-[10px] text-slate-500 font-mono mt-0.5 whitespace-nowrap">
                      <i class="fa-regular fa-clock text-[9px] text-slate-400"></i> {{ date('h:i A', strtotime($item->checkout_time)) }}
                    </div>
                  @endif
                </td>

                <!-- Status -->
                <td class="text-center whitespace-nowrap">
                  <span id="badge-status-{{ $item->id }}" class="status-badge {{ $statusClass }}">
                    @if(in_array($item->status, ['Settlement Done', 'Checked Out']))
                      <i class="fa-solid fa-lock text-[8px]"></i>
                    @else
                      <i class="fa-solid fa-circle text-[6px]"></i>
                    @endif
                    <span class="status-text">{{ $item->status ?? 'Confirmed' }}</span>
                  </span>
                </td>

                <!-- Action: Edit Button (unlocked guests) + Eye View Button -->
                <td class="text-center whitespace-nowrap">
                  <div class="action-flex-wrap">
                    @php
                      $isItemLocked = in_array($item->status, ['Settlement Done', 'Checked Out'])
                        || ($item->settlements && $item->settlements->where('status', 'Settled')->isNotEmpty())
                        || ($item->latestBooking?->settlements && $item->latestBooking->settlements->where('status', 'Settled')->isNotEmpty())
                        || ($item->latestBooking?->payments && $item->latestBooking->payments->where('payment_type', 'Settlement')->isNotEmpty())
                        || ((float)$balanceVal <= 0.01 && (float)$item->payable_amount > 0 && ($paidVal > 0));
                    @endphp

                    @if(!$isItemLocked)
                      <button type="button" 
                              class="btn-action-edit" 
                              id="btn-edit-guest-{{ $item->id }}"
                              onclick="openGuestEditModal({{ $item->id }})" 
                              title="Edit Guest Details">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                    @else
                      <span class="badge-locked settled py-1 px-2 text-[10px]" title="Guest reservation is settled and locked. Edit is disabled.">
                        <i class="fa-solid fa-lock text-[9px]"></i> Settled
                      </span>
                    @endif

                    <button type="button" 
                            class="btn-action-view" 
                            onclick="openGuestDetailsModalById({{ $item->id }})" 
                            title="View Complete Guest Dossier">
                      <i class="fa-solid fa-eye"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-slate-400 py-9 px-5">
                  <div class="text-2xl text-slate-300 mb-2"><i class="fa-solid fa-users-slash"></i></div>
                  <div class="text-xs font-bold text-slate-500">No matching guests found.</div>
                  <div class="text-[11px] text-slate-400 mt-1">Try modifying your search query or filter options.</div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <!-- Backend Table Pagination Footer -->
        <div class="pms-table-pagination flex justify-between items-center py-3 px-4.5 border-t border-slate-200 bg-white flex-wrap gap-2.5">
          <div class="pms-pagination-info text-xs font-semibold text-slate-500">
            Showing {{ $guests->firstItem() ?? 0 }} to {{ $guests->lastItem() ?? 0 }} of {{ $guests->total() }} records
          </div>
          <div class="pms-page-nav">
            @if ($guests->hasPages())
              {{-- Previous Page Link --}}
              @if ($guests->onFirstPage())
                <span class="pms-page-btn opacity-40 cursor-not-allowed"><i class="fa-solid fa-chevron-left"></i></span>
              @else
                <a href="{{ $guests->previousPageUrl() }}" class="pms-page-btn" title="Previous Page"><i class="fa-solid fa-chevron-left"></i></a>
              @endif

              {{-- Pagination Number Elements --}}
              @foreach ($guests->getUrlRange(max(1, $guests->currentPage() - 2), min($guests->lastPage(), $guests->currentPage() + 2)) as $page => $url)
                @if ($page == $guests->currentPage())
                  <span class="pms-page-btn active">{{ $page }}</span>
                @else
                  <a href="{{ $url }}" class="pms-page-btn">{{ $page }}</a>
                @endif
              @endforeach

              {{-- Next Page Link --}}
              @if ($guests->hasMorePages())
                <a href="{{ $guests->nextPageUrl() }}" class="pms-page-btn" title="Next Page"><i class="fa-solid fa-chevron-right"></i></a>
              @else
                <span class="pms-page-btn opacity-40 cursor-not-allowed"><i class="fa-solid fa-chevron-right"></i></span>
              @endif
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Bottom Summary Bar -->
<footer class="bg-white border-t border-slate-200 px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 shadow-2xs z-30 shrink-0 pms-bottom-bar">
  <div class="text-[11px] font-bold text-slate-500 flex items-center gap-3.5 flex-wrap">
    <span><i class="fa-solid fa-users text-indigo-600"></i> Total Guests: <strong class="text-slate-800">{{ $stats['total'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-key text-cyan-600"></i> Checked In: <strong class="text-slate-800">{{ $stats['checked_in'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-circle-check text-indigo-700"></i> Confirmed: <strong class="text-slate-800">{{ $stats['confirmed'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-bed text-amber-600"></i> Stay Over: <strong class="text-slate-800">{{ $stats['stay_over'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-gem text-amber-500"></i> VIP / Privilege: <strong class="text-slate-800">{{ $stats['vip'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-building text-emerald-600"></i> Corporate: <strong class="text-slate-800">{{ $stats['corporate'] ?? 0 }}</strong></span>
  </div>
</footer>

<!-- ========================================== -->
<!-- UNIFORM GUEST & RESERVATION DETAILS MODAL  -->
<!-- ========================================== -->
<div class="modal-backdrop" id="guest-crm-details-modal">
  <div class="modal-window max-w-4xl w-full max-h-[92vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200">
    
    <!-- Modal Header -->
    <div class="modal-top bg-gradient-to-r from-indigo-600 to-indigo-800 text-white px-5 py-3.5 flex justify-between items-center border-b border-indigo-500/30">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-base text-white flex-shrink-0">
          <i class="fa-solid fa-id-card-clip"></i>
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h3 id="crm-mdl-guest-name" class="m-0 text-base font-extrabold text-white">Guest Profile Dossier</h3>
            <span id="crm-mdl-status-badge" class="status-badge bg-white text-indigo-700 font-extrabold shadow-xs">Confirmed</span>
          </div>
          <div class="text-[11px] text-indigo-100 font-mono mt-0.5">
            Reserve Ref: <span id="crm-mdl-reserve-id" class="font-bold">-</span> &nbsp;|&nbsp; Folio: <span id="crm-mdl-folio-number" class="font-bold">-</span>
          </div>
        </div>
      </div>
      <button type="button" class="modal-close bg-white/20 hover:bg-white/30 text-white w-8 h-8 rounded-full text-lg cursor-pointer flex items-center justify-center transition border-none" onclick="closeModal('guest-crm-details-modal')">&times;</button>
    </div>

    <!-- Modal Body Area -->
    <div class="modal-content-area p-5 bg-slate-50 overflow-y-auto max-h-[calc(92vh-130px)]">
      
      <!-- Top Highlight Banner -->
      <div class="dossier-highlight-box bg-gradient-to-r from-slate-50 to-slate-100 border border-slate-200 rounded-xl p-3.5 mb-4 flex justify-between items-center flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fa-solid fa-door-open"></i>
          </div>
          <div>
            <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Allotted Room &amp; Category</div>
            <div class="text-base font-black text-slate-900 mt-0.5">
              Room <span id="crm-mdl-room-number">-</span> • <span id="crm-mdl-room-category">-</span>
            </div>
          </div>
        </div>

        <div class="flex gap-2 items-center flex-wrap">
          <span class="text-xs font-extrabold bg-white border border-slate-300 px-2.5 py-1 rounded-md text-slate-700 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-stairs text-indigo-600"></i> Floor <span id="crm-mdl-room-floor">-</span>
          </span>
          <span class="text-xs font-extrabold bg-white border border-slate-300 px-2.5 py-1 rounded-md text-slate-700 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-bed text-indigo-600"></i> <span id="crm-mdl-room-bedding">-</span>
          </span>
          <span class="text-xs font-extrabold bg-orange-50 border border-orange-200 px-2.5 py-1 rounded-md text-orange-800 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-utensils text-orange-600"></i> <span id="crm-mdl-meal-plan">-</span>
          </span>
          <span class="text-xs font-extrabold bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-md text-indigo-800 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-moon text-indigo-600"></i> <span id="crm-mdl-total-nights">1</span> Night(s) Stay
          </span>
        </div>
      </div>

      <!-- 2-Column Uniform Dossier Grid -->
      <div class="dossier-grid">
        
        <!-- CARD 1: Guest Profile Information -->
        <div class="dossier-card">
          <div class="dossier-card-title">
            <i class="fa-solid fa-user-check"></i> Guest Profile Details
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Name</span>
            <span class="dossier-val" id="crm-mdl-card-name">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Registration Type</span>
            <span class="dossier-val text-indigo-700 font-extrabold" id="crm-mdl-card-regtype">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Mobile Number</span>
            <span class="dossier-val mono" id="crm-mdl-card-phone">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Email Address</span>
            <span class="dossier-val" id="crm-mdl-card-email">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">City / Location</span>
            <span class="dossier-val" id="crm-mdl-card-city">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Address</span>
            <span class="dossier-val max-w-[220px] whitespace-normal" id="crm-mdl-card-address">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Nationality</span>
            <span class="dossier-val" id="crm-mdl-card-nationality">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Identity Proof</span>
            <span class="dossier-val" id="crm-mdl-card-idproof">-</span>
          </div>

          <!-- KYC Document Images Row -->
          <div class="dossier-field-row" id="crm-mdl-card-docs-row">
            <span class="dossier-label">KYC Documents</span>
            <span class="dossier-val" id="crm-mdl-card-docs-val">
              <button type="button" onclick="openCurrentGuestDocsModal()" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded-lg transition cursor-pointer">
                <i class="fa-solid fa-images text-indigo-600"></i> View Document Images (<span class="crm-docs-count-pill">0</span>)
              </button>
            </span>
          </div>

          <div class="dossier-field-row hidden" id="crm-mdl-card-privilege-row">
            <span class="dossier-label">Privilege / VIP Card</span>
            <span class="dossier-val mono text-amber-700 font-bold" id="crm-mdl-card-privilege">-</span>
          </div>
        </div>

        <!-- CARD 2: Stay Schedule & Corporate Account -->
        <div class="dossier-card">
          <div class="dossier-card-title">
            <i class="fa-solid fa-calendar-days"></i> Stay &amp; Corporate Details
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-In (Arrival)</span>
            <span class="dossier-val mono text-indigo-600" id="crm-mdl-card-checkin">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-Out (Departure)</span>
            <span class="dossier-val mono text-emerald-600" id="crm-mdl-card-checkout">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Duration of Stay</span>
            <span class="dossier-val" id="crm-mdl-card-duration">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Reservation Mode</span>
            <span class="dossier-val" id="crm-mdl-card-resmode">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Meal Plan</span>
            <span class="dossier-val text-orange-700 font-extrabold" id="crm-mdl-card-mealplan">-</span>
          </div>

          <!-- Corporate Account Block -->
          <div id="crm-mdl-corporate-card-section" class="mt-2.5 p-2.5 rounded-lg bg-indigo-50/50 border border-indigo-200 hidden">
            <div class="text-[10px] font-extrabold text-indigo-700 uppercase mb-1.5 flex items-center gap-1.5">
              <i class="fa-solid fa-building"></i> Corporate Account
            </div>
            <div class="dossier-field-row py-1">
              <span class="dossier-label">Company Name</span>
              <span class="dossier-val text-indigo-700 font-bold" id="crm-mdl-company-name">-</span>
            </div>
            <div class="dossier-field-row py-1">
              <span class="dossier-label">Company GSTIN</span>
              <span class="dossier-val mono" id="crm-mdl-company-gstin">-</span>
            </div>
            <div class="dossier-field-row py-1">
              <span class="dossier-label">Corporate Discount</span>
              <span class="dossier-val text-emerald-600 font-bold" id="crm-mdl-company-discount">-</span>
            </div>
          </div>
        </div>

      </div>

      <!-- CARD 3: Allotted Room & Stay Details (NO PAYMENT DETAILS) -->
      <div class="dossier-card mb-0">
        <div class="dossier-card-title">
          <i class="fa-solid fa-door-open"></i> Allotted Room &amp; Accommodation Details
        </div>

        <div id="crm-mdl-rooms-container" class="flex flex-col gap-2">
          <!-- Dynamically populated room entries -->
        </div>

        <!-- Remarks / Special Requests Box -->
        <div id="crm-mdl-remarks-box" class="mt-3 p-2.5 px-3.5 bg-white border border-dashed border-slate-300 rounded-lg hidden">
          <div class="text-[10px] font-extrabold text-slate-500 uppercase mb-1 flex items-center gap-1.5">
            <i class="fa-regular fa-comment-dots text-indigo-500"></i> Special Requests &amp; Booking Remarks
          </div>
          <div id="crm-mdl-remarks-text" class="text-xs text-slate-700 font-semibold">-</div>
        </div>
      </div>

    </div>

    <!-- Modal Footer Actions -->
    <div class="modal-bot px-5.5 py-3 bg-white border-t border-slate-200 flex justify-between items-center flex-wrap gap-2.5">
      <div class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
        <i class="fa-solid fa-clock-rotate-left text-slate-400"></i> Record Registered: <span id="crm-mdl-registered-at">-</span>
      </div>

      <div class="flex gap-2 items-center flex-wrap">
        <button type="button" id="crm-mdl-footer-btn-docs" class="btn-ui-secondary px-3.5 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50/80 text-xs font-bold text-indigo-700 hover:bg-indigo-100 inline-flex items-center gap-1.5 cursor-pointer shadow-2xs transition" onclick="openCurrentGuestDocsModal()">
          <i class="fa-solid fa-images text-indigo-600"></i> View Document Images (<span class="crm-docs-count-pill">0</span>)
        </button>
        <button type="button" class="btn-ui-secondary px-4 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 inline-flex items-center gap-1.5 cursor-pointer shadow-2xs transition" onclick="printCrmGuestDossier()">
          <i class="fa-solid fa-print"></i> Print Dossier
        </button>
        <button type="button" class="btn-ui-secondary px-4 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-50 cursor-pointer shadow-2xs transition" onclick="closeModal('guest-crm-details-modal')">
          Close
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ========================================== -->
<!-- GUEST DOCUMENT IMAGES VIEWER MODAL -->
<!-- ========================================== -->
<div class="modal-backdrop" id="guest-documents-viewer-modal" style="z-index: 999999 !important;">
  <div class="modal-window max-w-4xl w-full max-h-[92vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200 bg-white" style="z-index: 1000000 !important;">
    <!-- Header -->
    <div class="bg-linear-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3.5 flex justify-between items-center shrink-0">
      <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-sm shadow-xs">
          <i class="fa-solid fa-images text-amber-300"></i>
        </div>
        <div>
          <h3 class="m-0 text-sm font-black text-white" id="doc-viewer-modal-title">Guest Document Images</h3>
          <p class="m-0 text-[11px] text-indigo-100 font-medium" id="doc-viewer-modal-subtitle">KYC &amp; Verification Documents</p>
        </div>
      </div>
      <button type="button" class="modal-close bg-white/20 hover:bg-white/30 text-white w-8 h-8 rounded-full text-lg cursor-pointer flex items-center justify-center transition border-none" onclick="closeModal('guest-documents-viewer-modal')">&times;</button>
    </div>

    <!-- Content Area -->
    <div class="p-5 overflow-y-auto flex-1 bg-slate-50" id="doc-viewer-content-area">
      <!-- Grid of document cards or empty state injected dynamically -->
      <div id="doc-viewer-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5"></div>
      <div id="doc-viewer-empty" class="hidden py-12 text-center">
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
        <i class="fa-solid fa-circle-check text-emerald-600"></i> Total Documents: <strong id="doc-viewer-total-count" class="text-slate-900">0</strong>
      </span>
      <button type="button" class="btn-ui-secondary px-4 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-600 hover:bg-slate-100 cursor-pointer shadow-2xs" onclick="closeModal('guest-documents-viewer-modal')">
        Close
      </button>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- UNIFORM GUEST EDIT MODAL (GUEST DETAILS ONLY) -->
<!-- ========================================== -->
<div class="modal-backdrop" id="guest-crm-edit-modal">
  <div class="modal-window max-w-3xl w-full max-h-[92vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200">
    
    <!-- Modal Header -->
    <div class="modal-top bg-gradient-to-r from-blue-600 to-blue-700 text-white px-5 py-3.5 flex justify-between items-center border-b border-blue-500/30">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-base text-white flex-shrink-0">
          <i class="fa-solid fa-user-pen"></i>
        </div>
        <div>
          <h3 class="m-0 text-base font-extrabold text-white">Edit Guest Profile Details</h3>
          <div class="text-[11px] text-blue-100 mt-0.5">
            Guest: <strong id="edit-mdl-guest-title-name">-</strong> &nbsp;|&nbsp; Ref: <span id="edit-mdl-reserve-ref" class="font-mono font-bold">-</span>
          </div>
        </div>
      </div>
      <button type="button" class="modal-close bg-white/20 hover:bg-white/30 text-white w-8 h-8 rounded-full text-lg cursor-pointer flex items-center justify-center transition border-none" onclick="closeModal('guest-crm-edit-modal')">&times;</button>
    </div>

    <!-- Modal Form Body -->
    <form id="guest-crm-edit-form" onsubmit="submitGuestEdit(event)" class="flex flex-col flex-1 overflow-hidden m-0">
      <input type="hidden" id="edit-guest-id" name="id">

      <div class="modal-content-area p-5 bg-slate-50 overflow-y-auto max-h-[calc(92vh-130px)]">
        
        <!-- Context Alert Banner -->
        <div class="text-xs text-blue-800 bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 flex items-center gap-2.5 leading-relaxed">
          <i class="fa-solid fa-circle-info text-blue-600 text-sm flex-shrink-0"></i>
          <span>Modify personal information, contact credentials, identity proof, and corporate account for this guest only. Room allotment, dates, and billing amounts are preserved and managed separately.</span>
        </div>

        <!-- SECTION 1: Personal Profile Details -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 mb-3.5 shadow-2xs">
          <div class="text-xs font-extrabold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
            <i class="fa-solid fa-id-card text-blue-600"></i> Personal Details
          </div>

          <!-- Title + Full Name + Nationality -->
          <div class="flex gap-3 mb-3 flex-wrap">
            <div class="w-24 flex-shrink-0">
              <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">Title</label>
              <select name="title_id" id="edit-title-id" class="pms-filter-select w-full h-[38px] text-xs bg-white">
                <option value="">Select</option>
                @foreach($titles ?? \App\Models\Title::where('status', 'Active')->get() as $t)
                  <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="flex-1 min-w-[220px]">
              <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">Guest Full Name <span class="text-red-500">*</span></label>
              <input type="text" name="guest_name" id="edit-guest-name" required placeholder="First and Last Name" class="w-full h-[38px] px-3 text-xs font-semibold border border-slate-300 rounded-lg bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
            </div>
            <div class="w-40 min-w-[140px] flex-shrink-0">
              <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">Nationality</label>
              <select name="nationality_id" id="edit-nationality-id" class="pms-filter-select w-full h-[38px] text-xs bg-white">
                @foreach($nationalities ?? \App\Models\Nationality::where('status', 'Active')->get() as $nat)
                  <option value="{{ $nat->id }}">{{ $nat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <!-- SECTION 2: Contact Information -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 mb-3.5 shadow-2xs">
          <div class="text-xs font-extrabold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
            <i class="fa-solid fa-address-book text-emerald-500"></i> Contact &amp; Location
          </div>

          <!-- Phone + Email + City -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
            <div>
              <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">Mobile Phone <span class="text-red-500">*</span></label>
              <input type="text" name="mobile" id="edit-mobile" required placeholder="+91 9876543210" class="w-full h-[38px] px-3 text-xs font-bold font-mono border border-slate-300 rounded-lg bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
            </div>
            <div>
              <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">Email Address</label>
              <input type="email" name="email" id="edit-email" placeholder="guest@example.com" class="w-full h-[38px] px-3 text-xs border border-slate-300 rounded-lg bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
            </div>
            <div>
              <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">City / Town</label>
              <input type="text" name="city" id="edit-city" placeholder="e.g. Kolkata" class="w-full h-[38px] px-3 text-xs border border-slate-300 rounded-lg bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
            </div>
          </div>

          <!-- Full Address -->
          <div>
            <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">Full Postal Address</label>
            <input type="text" name="guest_address" id="edit-address" placeholder="Flat / House No, Street, Landmark, Pin code" class="w-full h-[38px] px-3 text-xs border border-slate-300 rounded-lg bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
          </div>
        </div>

        <!-- SECTION 3: Identity & VIP Privileges -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 mb-3.5 shadow-2xs">
          <div class="text-xs font-extrabold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
            <i class="fa-solid fa-passport text-indigo-500"></i> Identity Proof &amp; VIP Privilege
          </div>

          <!-- ID Card Type + ID Card Number -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div>
              <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">ID Proof Type</label>
              <select name="id_card_type_id" id="edit-id-card-type-id" class="pms-filter-select w-full h-[38px] text-xs bg-white">
                <option value="">Select ID Proof</option>
                @foreach($idCardTypes ?? \App\Models\IdCardType::where('status', 'Active')->get() as $idt)
                  <option value="{{ $idt->id }}">{{ $idt->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">ID Proof Number</label>
              <input type="text" name="id_card_number" id="edit-id-card-number" placeholder="Aadhaar / Passport / Voter ID No" class="w-full h-[38px] px-3 text-xs font-mono font-bold border border-slate-300 rounded-lg bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
            </div>
          </div>

          <!-- VIP Privilege Card Checkbox & Number -->
          <div class="bg-amber-500/10 border border-amber-300/60 rounded-lg p-2.5">
            <label class="inline-flex items-center gap-2 font-extrabold text-xs cursor-pointer text-amber-800 mb-1.5">
              <input type="checkbox" name="has_privilege_card" id="edit-has-privilege-card" onchange="toggleEditPrivilegeInput(this.checked)" class="w-4 h-4 accent-amber-600 cursor-pointer rounded">
              <i class="fa-solid fa-gem"></i> Guest has Privilege / VIP Card
            </label>
            <div id="edit-privilege-card-wrap" class="hidden mt-1.5">
              <input type="text" name="privilege_card_no" id="edit-privilege-card-no" placeholder="Enter Privilege Card Number" class="w-full max-w-xs h-[34px] px-2.5 text-xs font-bold font-mono border border-amber-400 rounded-md bg-white text-amber-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20">
            </div>
          </div>
        </div>

        <!-- SECTION 4: Corporate / Company Details -->
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 mb-0 shadow-2xs">
          <div class="text-xs font-extrabold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
            <i class="fa-solid fa-building text-indigo-600"></i> Corporate Account Affiliation
          </div>

          <div class="mb-3">
            <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase tracking-wider">Company Affiliation</label>
            <select name="company_id" id="edit-company-id" onchange="onEditCompanyChange(this.value)" class="pms-filter-select w-full h-[38px] text-xs bg-white">
              <option value="">Individual / None</option>
              @foreach($companies ?? \App\Models\Company::where('status', 'Active')->get() as $comp)
                <option value="{{ $comp->id }}">{{ $comp->name }} (GSTIN: {{ $comp->gstin ?: 'N/A' }})</option>
              @endforeach
              <option value="new">+ Add Custom / New Company</option>
            </select>
          </div>

          <!-- Custom Company Fields -->
          <div id="edit-new-company-section" class="hidden p-3 bg-slate-50 border border-dashed border-indigo-200 rounded-lg mt-2.5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-2.5">
              <div>
                <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase">Company Name</label>
                <input type="text" name="new_company_name" id="edit-new-company-name" placeholder="Company Name" class="w-full h-[34px] px-2.5 text-xs border border-slate-300 rounded-md bg-white text-slate-800">
              </div>
              <div>
                <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase">Company GSTIN</label>
                <input type="text" name="new_company_gstin" id="edit-new-company-gstin" placeholder="GSTIN" class="w-full h-[34px] px-2.5 text-xs font-mono border border-slate-300 rounded-md bg-white text-slate-800">
              </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase">Company Phone</label>
                <input type="text" name="new_company_phone" id="edit-new-company-phone" placeholder="Office Phone" class="w-full h-[34px] px-2.5 text-xs border border-slate-300 rounded-md bg-white text-slate-800">
              </div>
              <div>
                <label class="block text-[10px] font-extrabold mb-1 text-slate-500 uppercase">Company Address</label>
                <input type="text" name="new_company_address" id="edit-new-company-address" placeholder="Address" class="w-full h-[34px] px-2.5 text-xs border border-slate-300 rounded-md bg-white text-slate-800">
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-bot px-5.5 py-3 bg-white border-t border-slate-200 flex justify-between items-center gap-2.5 flex-wrap">
        <span class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
          <i class="fa-solid fa-shield-halved text-emerald-500"></i> Updates apply strictly to this guest record.
        </span>
        <div class="flex gap-2 items-center">
          <button type="button" class="btn-ui-secondary px-4 py-2 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-500 hover:text-slate-700 hover:bg-slate-50 cursor-pointer shadow-2xs transition" onclick="closeModal('guest-crm-edit-modal')">
            Cancel
          </button>
          <button type="submit" id="btn-save-guest-edit" class="btn-ui-primary px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white border-none text-xs font-bold inline-flex items-center gap-1.5 cursor-pointer shadow-sm transition">
            <i class="fa-solid fa-floppy-disk"></i> Save Changes
          </button>
        </div>
      </div>
    </form>

  </div>
</div>

<!-- Hidden Clean Print Template Area -->
<div id="print-area" class="hidden"></div>

@endsection

@push('scripts')
<script>
  window.allCrmGuests = @json($serializedGuests);
  let currentActiveCrmGuest = null;

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

  // Format currency in INR
  function formatINR(val) {
    return '₹ ' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // Status badge color mapper
  function getStatusBadgeColor(status) {
    switch ((status || '').toLowerCase()) {
      case 'settlement done': case 'settled': return 'settled';
      case 'confirmed': return 'confirmed';
      case 'arrived': return 'arrived';
      case 'checked in': case 'checked-in': case 'checkedin': return 'checkedin';
      case 'stay over': case 'stayover': return 'stayover';
      case 'checked out': case 'checked-out': case 'checkedout': return 'checkedout';
      case 'cancelled': return 'cancelled';
      default: return 'confirmed';
    }
  }

  // Populate and open the Dossier Modal
  function populateAndOpenModal(guest) {
    currentActiveCrmGuest = guest;

    // Header info
    document.getElementById('crm-mdl-guest-name').textContent = guest.guest_name || guest.name;
    document.getElementById('crm-mdl-reserve-id').textContent = guest.reserve_id || ('RES-' + guest.id);
    document.getElementById('crm-mdl-folio-number').textContent = guest.folio_number || '-';
    document.getElementById('crm-mdl-registered-at').textContent = guest.created_at_fmt || 'N/A';

    const statusBadge = document.getElementById('crm-mdl-status-badge');
    const isLocked = guest.is_locked || ['settlement done', 'checked out', 'settled'].includes((guest.status || '').toLowerCase());

    if (statusBadge) {
      statusBadge.innerHTML = (isLocked ? '<i class="fa-solid fa-lock text-[8px]"></i> ' : '<i class="fa-solid fa-circle text-[6px]"></i> ') + escapeHtml(guest.status || 'Confirmed');
      statusBadge.className = `status-badge ${getStatusBadgeColor(guest.status)}`;
    }

    // Highlight Banner
    document.getElementById('crm-mdl-room-number').textContent = guest.room_number || (guest.room?.room_number ?? 'Pending');
    document.getElementById('crm-mdl-room-category').textContent = guest.room_category || (guest.room?.category_relation?.name ?? 'DELUXE');
    document.getElementById('crm-mdl-room-floor').textContent = guest.room_floor || (guest.room?.floor_relation?.floor ?? '1');
    document.getElementById('crm-mdl-room-bedding').textContent = guest.room_bedding || (guest.room?.bedding_config_relation?.name ?? 'Standard');
    document.getElementById('crm-mdl-total-nights').textContent = guest.total_nights || 1;

    // Card 1: Guest Profile
    document.getElementById('crm-mdl-card-name').textContent = guest.guest_name || guest.name;
    document.getElementById('crm-mdl-card-regtype').textContent = guest.registration_type || (guest.registration_type?.name ?? 'Regular');
    document.getElementById('crm-mdl-card-phone').textContent = guest.mobile || 'N/A';
    document.getElementById('crm-mdl-card-email').textContent = guest.email || 'N/A';
    document.getElementById('crm-mdl-card-city').textContent = guest.city || 'N/A';
    document.getElementById('crm-mdl-card-address').textContent = guest.guest_address || 'N/A';
    document.getElementById('crm-mdl-card-nationality').textContent = guest.nationality || (guest.nationality?.name ?? 'Indian');
    
    const idProofType = guest.id_card_type || (guest.id_card_type?.name ?? 'ID Proof');
    const idProofNum = guest.id_card_number || 'N/A';
    document.getElementById('crm-mdl-card-idproof').textContent = `${idProofType} • ${idProofNum}`;

    // KYC Documents Count
    let docs = guest.documents;
    if (typeof docs === 'string') {
      try { docs = JSON.parse(docs); } catch (e) { docs = []; }
    }
    if (!Array.isArray(docs)) docs = [];
    guest.documents = docs;
    currentActiveCrmGuest.documents = docs;

    document.querySelectorAll('.crm-docs-count-pill').forEach(el => {
      el.textContent = docs.length;
    });

    const privRow = document.getElementById('crm-mdl-card-privilege-row');
    if (guest.has_privilege_card) {
      privRow.style.display = 'flex';
      document.getElementById('crm-mdl-card-privilege').textContent = guest.privilege_card_no || 'Active';
    } else {
      privRow.style.display = 'none';
    }

    // Card 2: Stay & Corporate
    document.getElementById('crm-mdl-card-checkin').textContent = `${guest.reserve_date} (${guest.reserve_time || '12:00 PM'})`;
    document.getElementById('crm-mdl-card-checkout').textContent = `${guest.checkout_date} (${guest.checkout_time || '11:00 AM'})`;
    document.getElementById('crm-mdl-card-duration').textContent = `${guest.total_nights || 1} Night(s)`;
    document.getElementById('crm-mdl-card-resmode').textContent = guest.reservation_mode || (guest.reservation_mode?.name ?? 'Front Desk');
    
    const rawPlanName = guest.plan_name || (guest.plan?.plan_name ?? 'None');
    const rawPlanPrice = Number(guest.plan_price || 0);
    const rawPlanOrigPrice = Number(guest.plan_original_price !== undefined ? guest.plan_original_price : rawPlanPrice);
    const isNonePlanCrm = !rawPlanName || rawPlanName.trim().toLowerCase() === 'none' || rawPlanName.trim().toLowerCase() === 'room only (ep)' || rawPlanName.trim().toLowerCase() === 'ep' || (rawPlanPrice <= 0 && rawPlanOrigPrice <= 0);
    
    let mealPlanHtml = escapeHtml(rawPlanName);
    if (!isNonePlanCrm) {
      if (rawPlanOrigPrice > rawPlanPrice) {
        mealPlanHtml = `${escapeHtml(rawPlanName)} (+<span class="line-through opacity-65 mr-1">₹${rawPlanOrigPrice.toLocaleString('en-IN')}</span><strong>₹${rawPlanPrice.toLocaleString('en-IN')}</strong>)`;
      } else if (rawPlanPrice > 0) {
        mealPlanHtml = `${escapeHtml(rawPlanName)} (+₹${rawPlanPrice.toLocaleString('en-IN')})`;
      }
    }

    const mealPlanElem = document.getElementById('crm-mdl-meal-plan');
    if (mealPlanElem) mealPlanElem.innerHTML = mealPlanHtml;
    const cardMealPlanElem = document.getElementById('crm-mdl-card-mealplan');
    if (cardMealPlanElem) cardMealPlanElem.innerHTML = mealPlanHtml;

    const corpSection = document.getElementById('crm-mdl-corporate-card-section');
    if (guest.company) {
      corpSection.style.display = 'block';
      document.getElementById('crm-mdl-company-name').textContent = guest.company.name;
      document.getElementById('crm-mdl-company-gstin').textContent = guest.company.gstin || 'N/A';
      const discVal = guest.company.discount_type === 'percentage' 
        ? `${guest.company.discount_value}%` 
        : `₹ ${Number(guest.company.discount_value || 0).toLocaleString('en-IN')}`;
      document.getElementById('crm-mdl-company-discount').textContent = `${discVal} (${guest.company.discount_type || 'percentage'})`;
    } else {
      corpSection.style.display = 'none';
    }

    // Card 3: Allotted Rooms & Accommodation Details (No payment details)
    const roomsContainer = document.getElementById('crm-mdl-rooms-container');
    if (roomsContainer) {
      roomsContainer.innerHTML = '';
      const roomsList = (guest.all_rooms && guest.all_rooms.length > 0) ? guest.all_rooms : [{
        room_number: guest.room_number || (guest.room?.room_number ?? 'Pending'),
        category: guest.room_category || (guest.room?.category_relation?.name ?? 'Standard'),
        floor: guest.room_floor || (guest.room?.floor_relation?.floor ?? '1'),
        bedding: guest.room_bedding || (guest.room?.bedding_config_relation?.name ?? 'Standard'),
        occupant_name: guest.guest_name || guest.name,
        occupant_phone: guest.mobile || 'N/A',
        status: guest.status || 'Confirmed'
      }];

      roomsList.forEach(rm => {
        const item = document.createElement('div');
        item.className = 'flex justify-between items-center bg-white border border-slate-200 rounded-lg px-3.5 py-2.5 flex-wrap gap-2.5 shadow-2xs';
        item.innerHTML = `
          <div class="flex items-center gap-3">
            <div class="min-w-11 h-[38px] px-2 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-extrabold text-xs font-mono">
              ${escapeHtml(rm.room_number)}
            </div>
            <div>
              <div class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5 flex-wrap">
                <span>Floor ${escapeHtml(rm.floor)} • ${escapeHtml(rm.category)}</span>
                <span class="text-[11px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded">
                  <i class="fa-solid fa-bed text-indigo-500"></i> ${escapeHtml(rm.bedding)}
                </span>
              </div>
              <div class="text-[11px] text-slate-500 mt-0.5">
                <i class="fa-solid fa-user text-slate-400"></i> Occupant: <strong class="text-slate-700">${escapeHtml(rm.occupant_name)}</strong>
                ${rm.occupant_phone && rm.occupant_phone !== 'N/A' ? ` • <span class="font-mono"><i class="fa-solid fa-phone text-[9px] text-slate-400"></i> ${escapeHtml(rm.occupant_phone)}</span>` : ''}
              </div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <span class="status-badge ${getStatusBadgeColor(rm.status || guest.status)} text-[11px]">
              ${escapeHtml(rm.status || guest.status || 'Confirmed')}
            </span>
          </div>
        `;
        roomsContainer.appendChild(item);
      });
    }

    // Remarks box
    const remarksBox = document.getElementById('crm-mdl-remarks-box');
    const remarksText = document.getElementById('crm-mdl-remarks-text');
    const guestRemarks = (guest.remarks || guest.payment_remarks || '').trim();
    if (remarksBox && remarksText) {
      if (guestRemarks && guestRemarks.toLowerCase() !== 'none' && guestRemarks.toLowerCase() !== 'n/a') {
        remarksText.textContent = guestRemarks;
        remarksBox.style.display = 'block';
      } else {
        remarksBox.style.display = 'none';
      }
    }

    // Show modal
    openModal('guest-crm-details-modal');
  }

  // Open Document Images Viewer Modal
  async function openCurrentGuestDocsModal() {
    if (!currentActiveCrmGuest) return;
    const g = currentActiveCrmGuest;
    let docs = Array.isArray(g.documents) ? g.documents : [];

    // If 0 documents cached in browser, fetch fresh from backend details API immediately
    if (docs.length === 0 && g.id) {
      try {
        const res = await fetch(`{{ url('front-office/guest') }}/${g.id}/details`, {
          headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success && data.guest && Array.isArray(data.guest.documents) && data.guest.documents.length > 0) {
          docs = data.guest.documents;
          g.documents = docs;
          currentActiveCrmGuest.documents = docs;
          document.querySelectorAll('.crm-docs-count-pill').forEach(el => el.textContent = docs.length);
        }
      } catch (err) {
        console.error('Error fetching guest documents:', err);
      }
    }

    const titleElem = document.getElementById('doc-viewer-modal-title');
    const subElem = document.getElementById('doc-viewer-modal-subtitle');
    const countElem = document.getElementById('doc-viewer-total-count');

    if (titleElem) titleElem.textContent = (g.guest_name || g.name || 'Guest') + ' - Document Images';
    if (subElem) subElem.textContent = `Ref: ${g.reserve_id || ('RES-' + g.id)} • Folio: ${g.folio_number || '-'} • ${docs.length} Document(s)`;
    if (countElem) countElem.textContent = docs.length;

    const grid = document.getElementById('doc-viewer-grid');
    const empty = document.getElementById('doc-viewer-empty');

    if (!grid || !empty) return;

    if (docs.length === 0) {
      grid.innerHTML = '';
      empty.classList.remove('hidden');
    } else {
      empty.classList.add('hidden');
      grid.innerHTML = docs.map((doc, idx) => {
        const rawUrl = doc.url || '';
        const imgUrl = (typeof window.resolveAppAsset === 'function')
          ? window.resolveAppAsset(rawUrl)
          : (rawUrl.startsWith('http://') || rawUrl.startsWith('https://') || rawUrl.startsWith('//')
              ? rawUrl
              : (("{{ asset('') }}".replace(/\/+$/, '') + '/') + rawUrl.replace(/^\/+/, '')));

        return `
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-xs flex flex-col group hover:border-indigo-300 transition">
          <div class="relative bg-slate-900/5 aspect-4/3 overflow-hidden flex items-center justify-center p-2">
            <a href="${imgUrl}" target="_blank" title="Click to view full image in new tab" class="block w-full h-full flex items-center justify-center">
              <img src="${imgUrl}" alt="${escapeHtml(doc.name || 'Document')}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-300 rounded">
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
              <a href="${imgUrl}" target="_blank" class="flex-1 py-1 px-2 text-[10px] font-bold text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-md transition flex items-center justify-center gap-1">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Open Full View
              </a>
              <a href="${imgUrl}" download="${escapeHtml(doc.name || 'document')}" class="py-1 px-2.5 text-[10px] font-bold bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-md transition" title="Download Image">
                <i class="fa-solid fa-download"></i>
              </a>
            </div>
          </div>
        </div>
      `;
      }).join('');
    }

    openModal('guest-documents-viewer-modal');
  }

  // Open Settlement Modal
  function openCrmSettlementModal() {
    if (!currentActiveCrmGuest) return;
    const g = currentActiveCrmGuest;
    const balance = Number(g.balance || 0);

    if (balance <= 0) {
      alert('This guest has already cleared all outstanding balance.');
      return;
    }

    document.getElementById('crm-settle-guest-name').textContent = g.guest_name || g.name;
    document.getElementById('crm-settle-folio-num').textContent = g.folio_number || g.reserve_id || '-';
    document.getElementById('crm-settle-balance-display').textContent = formatINR(balance);
    document.getElementById('crm-settle-payable-display').textContent = formatINR(g.payable_amount);
    document.getElementById('crm-settle-paid-display').textContent = formatINR(g.total_paid || g.advance_amount);

    const amountInput = document.getElementById('crm-settle-amount-input');
    amountInput.value = balance.toFixed(2);
    amountInput.max = balance.toFixed(2);

    document.getElementById('crm-settle-txn-ref').value = '';
    document.getElementById('crm-settle-remarks').value = '';

    openModal('guest-crm-settlement-modal');
  }

  function setFullSettlementAmount() {
    if (!currentActiveCrmGuest) return;
    const balance = Number(currentActiveCrmGuest.balance || 0);
    document.getElementById('crm-settle-amount-input').value = balance.toFixed(2);
  }

  // Submit Settlement
  async function submitCrmSettlement() {
    if (!currentActiveCrmGuest) return;
    const g = currentActiveCrmGuest;
    const amountInput = document.getElementById('crm-settle-amount-input');
    const settleAmount = parseFloat(amountInput.value);
    const maxBalance = Number(g.balance || 0);

    if (isNaN(settleAmount) || settleAmount <= 0) {
      alert('Please enter a valid settlement amount greater than 0.');
      return;
    }

    if (settleAmount > (maxBalance + 0.01)) {
      alert(`Settlement amount cannot exceed outstanding balance of ${formatINR(maxBalance)}.`);
      return;
    }

    const modeSelect = document.getElementById('crm-settle-mode-select');
    const txnRefInput = document.getElementById('crm-settle-txn-ref');
    const remarksInput = document.getElementById('crm-settle-remarks');
    const confirmBtn = document.getElementById('crm-btn-confirm-settlement');

    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
      const response = await fetch(`{{ url('front-office/guest') }}/${g.id}/settle`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken || ''
        },
        body: JSON.stringify({
          amount: settleAmount,
          payment_mode_id: modeSelect.value,
          transaction_id: txnRefInput.value,
          remarks: remarksInput.value
        })
      });

      const data = await response.json();
      if (!response.ok || !data.success) {
        throw new Error(data.error || 'Failed to process settlement');
      }

      // Close settlement modal
      closeModal('guest-crm-settlement-modal');

      // Update current active guest data
      g.balance = data.new_balance;
      g.advance_amount = data.advance_amount;
      g.settlement_paid = data.settlement_paid;
      g.total_paid = data.total_paid;
      if (data.status) g.status = data.status;

      // Update in memory list
      const cached = (window.allCrmGuests || []).find(x => String(x.id) === String(g.id));
      if (cached) {
        cached.balance = data.new_balance;
        cached.advance_amount = data.advance_amount;
        cached.settlement_paid = data.settlement_paid;
        cached.total_paid = data.total_paid;
        if (data.status) cached.status = data.status;
      }

      // Re-populate dossier modal with updated financials and switch buttons
      populateAndOpenModal(g);

      // Update table row in UI
      updateTableRowFinancials(g.id, data.total_paid, data.new_balance);
      if (data.status) {
        updateTableRowStatus(g.id, data.status);
      }

      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(data.message || 'Settlement processed successfully!', 'success');
      } else {
        alert(data.message || 'Settlement processed successfully!');
      }

      // Must reload page on settlement as required
      setTimeout(() => {
        window.location.reload();
      }, 700);

    } catch (err) {
      alert(err.message || 'Error processing settlement');
    } finally {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Confirm Settlement';
    }
  }

  // Execute Check Out
  async function executeCrmCheckout() {
    if (!currentActiveCrmGuest) return;
    const g = currentActiveCrmGuest;
    const isCompanyGuest = (g.registration_type || '').toLowerCase() === 'company' || !!g.company;

    if (Number(g.balance || 0) > 0.01 && !isCompanyGuest) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Outstanding Balance Due',
          html: `<div class="text-xs text-slate-600 leading-relaxed">Cannot check out with outstanding balance of <strong class="text-rose-600 font-extrabold">${formatINR(g.balance)}</strong>.<br><br>Please complete folio settlement first before checkout.</div>`,
          confirmButtonColor: '#e11d48',
          confirmButtonText: '<i class="fa-solid fa-calculator mr-1.5"></i> Settle Folio Now',
          showCancelButton: true,
          cancelButtonText: 'Dismiss',
          heightAuto: false,
          scrollbarPadding: false
        }).then(res => {
          if (res.isConfirmed) {
            openCrmSettlementModal();
          }
        });
      } else {
        alert(`Cannot check out with outstanding balance of ${formatINR(g.balance)}. Please complete settlement first.`);
      }
      return;
    }

    let isConfirmed = false;
    if (typeof Swal !== 'undefined') {
      const companyNoticeHtml = (isCompanyGuest && Number(g.balance || 0) > 0.01)
        ? `<div class="bg-fuchsia-50 border border-fuchsia-300 text-fuchsia-800 p-2 px-2.5 rounded-lg text-xs font-bold mb-2.5"><i class="fa-solid fa-building mr-1"></i> Company Account: Outstanding balance of ${formatINR(g.balance)} will be retained on company account without pre-settlement.</div>`
        : '';
      const result = await Swal.fire({
        title: 'Confirm Guest Check-Out',
        html: `
          <div class="text-xs text-slate-700 leading-relaxed text-left p-1">
            ${companyNoticeHtml}
            <p class="m-0 mb-3 text-xs text-slate-600">Are you sure you want to proceed with checking out this guest?</p>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 px-3.5 mb-3">
              <div class="flex justify-between items-center mb-1.5">
                <span class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Guest Name</span>
                <strong class="text-slate-900 text-xs">${escapeHtml(g.guest_name)}</strong>
              </div>
              <div class="flex justify-between items-center mb-1.5">
                <span class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Room & Category</span>
                <span class="text-indigo-600 font-extrabold text-xs">${escapeHtml(g.room_number || '-')} (${escapeHtml(g.room_category || 'Standard')})</span>
              </div>
              <div class="flex justify-between items-center mb-1.5">
                <span class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Folio / Reserve ID</span>
                <span class="font-mono text-xs font-bold text-slate-700">${escapeHtml(g.reserve_id || '-')}</span>
              </div>
              <div class="flex justify-between items-center border-t border-dashed border-slate-300 pt-1.5">
                <span class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Billing Status</span>
                <span class="text-emerald-600 font-extrabold text-[11px] inline-flex items-center gap-1">
                  <i class="fa-solid fa-circle-check"></i> Fully Settled (₹ 0.00 Due)
                </span>
              </div>
            </div>
            <div class="flex items-center gap-2 text-[11px] text-amber-800 bg-amber-50 p-2.5 px-3 rounded-lg border border-amber-200">
              <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
              <span>Status will be marked as <strong>'Checked Out'</strong> and room scheduled for housekeeping.</span>
            </div>
          </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: '<i class="fa-solid fa-right-from-bracket mr-1.5"></i> Yes, Check Out',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        focusCancel: false,
        heightAuto: false,
        scrollbarPadding: false,
        customClass: {
          popup: 'pms-swal-checkout-popup',
          title: 'pms-swal-checkout-title',
          confirmButton: 'pms-swal-checkout-confirm-btn',
          cancelButton: 'pms-swal-checkout-cancel-btn'
        }
      });
      isConfirmed = result.isConfirmed;
    } else {
      isConfirmed = confirm(`Are you sure you want to Check Out ${g.guest_name || 'this guest'}?\n\nThis will update status to 'Checked Out' and schedule the room for housekeeping.`);
    }

    if (!isConfirmed) return;

    const btnCheckout = document.getElementById('crm-btn-checkout');
    if (btnCheckout) {
      btnCheckout.disabled = true;
      btnCheckout.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking Out...';
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
      const response = await fetch(`{{ url('front-office/guest') }}/${g.id}/checkout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken || ''
        },
        body: JSON.stringify({
          remarks: 'Checked out via Front Desk Master CRM'
        })
      });

      const data = await response.json();
      if (!response.ok || !data.success) {
        throw new Error(data.error || 'Failed to complete check out');
      }

      // Update guest in state
      g.status = 'Checked Out';
      if (data.checkout_date) g.checkout_date = data.checkout_date;
      if (data.checkout_time) g.checkout_time = data.checkout_time;

      const cached = (window.allCrmGuests || []).find(x => String(x.id) === String(g.id));
      if (cached) {
        cached.status = 'Checked Out';
      }

      // Re-populate modal: this automatically locks and shows Checked Out badge
      populateAndOpenModal(g);

      // Update table row in UI
      updateTableRowStatus(g.id, 'Checked Out');

      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(data.message || 'Guest checked out successfully!', 'success');
      } else {
        alert(data.message || 'Guest checked out successfully!');
      }

      // Must reload page on checkout as required
      setTimeout(() => {
        window.location.reload();
      }, 700);

    } catch (err) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Check Out Failed',
          text: err.message || 'Error during checkout',
          confirmButtonColor: '#4f46e5'
        });
      } else {
        alert(err.message || 'Error during checkout');
      }
      if (btnCheckout) {
        btnCheckout.disabled = false;
        btnCheckout.innerHTML = '<i class="fa-solid fa-right-from-bracket"></i> Check Out';
      }
    }
  }

  // Helper to dynamically update table row financials after settlement
  function updateTableRowFinancials(guestId, paid, balance) {
    const row = document.getElementById(`row-guest-${guestId}`);
    if (!row) return;

    // Paid cell (7th index in row)
    const paidCell = row.children[7];
    if (paidCell) {
      paidCell.innerHTML = `
        <div class="font-mono font-extrabold text-xs text-emerald-600">
          ₹ ${Number(paid || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
        </div>
      `;
    }

    // Remaining cell (8th index in row)
    const remCell = row.children[8];
    if (remCell) {
      const isDue = Number(balance || 0) > 0;
      remCell.innerHTML = `
        <div class="font-mono font-extrabold text-xs ${isDue ? 'text-rose-600' : 'text-emerald-600'}">
          ₹ ${Number(balance || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
        </div>
        <div class="text-[10px] font-bold mt-0.5">
          ${isDue 
            ? '<span class="text-rose-700 bg-rose-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold">Due</span>' 
            : '<span class="text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded text-[9px] font-extrabold">Cleared</span>'}
        </div>
      `;
    }
  }

  // Helper to dynamically update table row status after checkout / settlement
  function updateTableRowStatus(guestId, newStatus) {
    const row = document.getElementById(`row-guest-${guestId}`);
    if (!row) return;

    // Status cell (9th index in row)
    const statusCell = row.children[9];
    if (statusCell) {
      const isLocked = ['settlement done', 'checked out', 'settled'].includes((newStatus || '').toLowerCase());
      const icon = isLocked ? '<i class="fa-solid fa-lock text-[8px]"></i>' : '<i class="fa-solid fa-circle text-[6px]"></i>';
      statusCell.innerHTML = `
        <span class="status-badge ${getStatusBadgeColor(newStatus)}">
          ${icon}
          <span>${escapeHtml(newStatus)}</span>
        </span>
      `;
    }
  }

  // Open Uniform Guest Dossier Modal by Guest ID (with fallback to backend JSON API)
  function openGuestDetailsModalById(guestId) {
    const guest = (window.allCrmGuests || []).find(g => String(g.id) === String(guestId));
    if (guest && Array.isArray(guest.documents) && guest.documents.length > 0) {
      populateAndOpenModal(guest);
      return;
    }

    // Fetch via backend API if not in current page memory or if documents not yet cached
    fetch(`{{ url('front-office/guest') }}/${guestId}/details`, {
      headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success && data.guest) {
        if (Array.isArray(window.allCrmGuests)) {
          const idx = window.allCrmGuests.findIndex(g => String(g.id) === String(guestId));
          if (idx >= 0) window.allCrmGuests[idx] = data.guest;
          else window.allCrmGuests.push(data.guest);
        }
        populateAndOpenModal(data.guest);
      } else if (guest) {
        populateAndOpenModal(guest);
      } else {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast('Guest record not found', 'error');
        } else {
          alert('Guest record not found');
        }
      }
    })
    .catch(() => {
      if (guest) populateAndOpenModal(guest);
      else alert('Unable to load guest profile');
    });
  }

  // Open Guest Edit Modal
  function openGuestEditModal(guestId) {
    const guest = (window.allCrmGuests || []).find(g => String(g.id) === String(guestId));
    if (!guest) {
      // Fallback: fetch details via API
      fetch(`{{ url('front-office/guest') }}/${guestId}/details`, {
        headers: { 'Accept': 'application/json' }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.guest) {
          fillAndShowEditModal(data.guest);
        } else {
          alert('Guest record not found.');
        }
      })
      .catch(() => alert('Failed to load guest data.'));
      return;
    }
    fillAndShowEditModal(guest);
  }

  function fillAndShowEditModal(guest) {
    const isLocked = guest.is_locked || ['settlement done', 'checked out', 'settled'].includes((guest.status || '').toLowerCase());
    if (isLocked) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('This guest reservation is settled and locked. Profile details cannot be modified.', 'warning');
      } else {
        alert('This guest reservation is settled and locked. Profile details cannot be modified.');
      }
      return;
    }

    document.getElementById('edit-guest-id').value = guest.id;
    document.getElementById('edit-mdl-guest-title-name').textContent = guest.guest_name || guest.raw_name || 'Guest';
    document.getElementById('edit-mdl-reserve-ref').textContent = guest.reserve_id || ('RES-' + guest.id);

    // Personal details
    document.getElementById('edit-title-id').value = guest.title_id || '';
    document.getElementById('edit-guest-name').value = guest.raw_name || guest.guest_name || '';
    if (guest.nationality_id) {
      document.getElementById('edit-nationality-id').value = guest.nationality_id;
    }

    // Contact details
    document.getElementById('edit-mobile').value = (guest.raw_mobile !== undefined ? guest.raw_mobile : (guest.mobile === 'N/A' ? '' : guest.mobile)) || '';
    document.getElementById('edit-email').value = (guest.raw_email !== undefined ? guest.raw_email : (guest.email === 'N/A' ? '' : guest.email)) || '';
    document.getElementById('edit-city').value = (guest.raw_city !== undefined ? guest.raw_city : (guest.city === 'N/A' ? '' : guest.city)) || '';
    document.getElementById('edit-address').value = (guest.raw_address !== undefined ? guest.raw_address : (guest.guest_address === 'N/A' ? '' : guest.guest_address)) || '';

    // ID proof
    document.getElementById('edit-id-card-type-id').value = guest.id_card_type_id || '';
    document.getElementById('edit-id-card-number').value = (guest.raw_id_card_number !== undefined ? guest.raw_id_card_number : (guest.id_card_number === 'N/A' ? '' : guest.id_card_number)) || '';

    // Privilege card
    const hasPrivilege = Boolean(guest.has_privilege_card);
    const privChk = document.getElementById('edit-has-privilege-card');
    privChk.checked = hasPrivilege;
    toggleEditPrivilegeInput(hasPrivilege);
    document.getElementById('edit-privilege-card-no').value = (guest.raw_privilege_card_no !== undefined ? guest.raw_privilege_card_no : (guest.privilege_card_no === 'N/A' ? '' : guest.privilege_card_no)) || '';

    // Company
    const compSelect = document.getElementById('edit-company-id');
    if (guest.company_id) {
      compSelect.value = guest.company_id;
      onEditCompanyChange(guest.company_id);
    } else if (guest.new_company_name) {
      compSelect.value = 'new';
      onEditCompanyChange('new');
      document.getElementById('edit-new-company-name').value = guest.new_company_name || '';
      document.getElementById('edit-new-company-gstin').value = guest.new_company_gstin || '';
      document.getElementById('edit-new-company-phone').value = guest.new_company_phone || '';
      document.getElementById('edit-new-company-address').value = guest.new_company_address || '';
    } else {
      compSelect.value = '';
      onEditCompanyChange('');
    }

    openModal('guest-crm-edit-modal');
  }

  function toggleEditPrivilegeInput(isChecked) {
    const wrap = document.getElementById('edit-privilege-card-wrap');
    if (wrap) {
      wrap.style.display = isChecked ? 'block' : 'none';
      if (!isChecked) {
        document.getElementById('edit-privilege-card-no').value = '';
      }
    }
  }

  function onEditCompanyChange(val) {
    const section = document.getElementById('edit-new-company-section');
    if (!section) return;
    if (val === 'new') {
      section.style.display = 'block';
    } else {
      section.style.display = 'none';
      if (val === '') {
        document.getElementById('edit-new-company-name').value = '';
        document.getElementById('edit-new-company-gstin').value = '';
        document.getElementById('edit-new-company-phone').value = '';
        document.getElementById('edit-new-company-address').value = '';
      }
    }
  }

  async function submitGuestEdit(event) {
    event.preventDefault();
    const guestId = document.getElementById('edit-guest-id').value;
    if (!guestId) return;

    const form = document.getElementById('guest-crm-edit-form');
    const formData = new FormData(form);
    const saveBtn = document.getElementById('btn-save-guest-edit');

    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const payload = {
      title_id: formData.get('title_id') || null,
      guest_name: formData.get('guest_name'),
      nationality_id: formData.get('nationality_id') || null,
      mobile: formData.get('mobile') || null,
      email: formData.get('email') || null,
      city: formData.get('city') || null,
      guest_address: formData.get('guest_address') || null,
      id_card_type_id: formData.get('id_card_type_id') || null,
      id_card_number: formData.get('id_card_number') || null,
      has_privilege_card: formData.get('has_privilege_card') ? 1 : 0,
      privilege_card_no: formData.get('privilege_card_no') || null,
      company_id: formData.get('company_id') || null,
      new_company_name: formData.get('new_company_name') || null,
      new_company_gstin: formData.get('new_company_gstin') || null,
      new_company_phone: formData.get('new_company_phone') || null,
      new_company_address: formData.get('new_company_address') || null,
    };

    try {
      const response = await fetch(`{{ url('front-office/guest') }}/${guestId}/update`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken || ''
        },
        body: JSON.stringify(payload)
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error(data.message || data.error || 'Failed to update guest details');
      }

      // Update in memory guest record
      const g = (window.allCrmGuests || []).find(x => String(x.id) === String(guestId));
      if (g) {
        Object.assign(g, data.guest);
      }

      // Update table row in UI
      const nameElem = document.getElementById(`guest-name-display-${guestId}`);
      if (nameElem) {
        nameElem.textContent = data.guest.guest_name;
      }

      const cityWrap = document.getElementById(`guest-city-wrap-${guestId}`);
      const cityElem = document.getElementById(`guest-city-display-${guestId}`);
      if (cityElem) {
        cityElem.textContent = data.guest.city !== 'N/A' ? data.guest.city : '';
        if (cityWrap) {
          cityWrap.style.display = (data.guest.city && data.guest.city !== 'N/A') ? 'inline' : 'none';
        }
      }

      const phoneElem = document.getElementById(`guest-phone-display-${guestId}`);
      if (phoneElem) {
        phoneElem.textContent = data.guest.mobile || 'N/A';
      }

      // If active guest in details modal is open, re-populate
      if (currentActiveCrmGuest && String(currentActiveCrmGuest.id) === String(guestId)) {
        Object.assign(currentActiveCrmGuest, data.guest);
        populateAndOpenModal(currentActiveCrmGuest);
      }

      closeModal('guest-crm-edit-modal');

      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(data.message || 'Guest details updated successfully!', 'success');
      } else {
        alert(data.message || 'Guest details updated successfully!');
      }

    } catch (err) {
      alert(err.message || 'Error updating guest details');
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
    }
  }

  // Print Guest Dossier
  function printCrmGuestDossier() {
    if (!currentActiveCrmGuest) return;
    const g = currentActiveCrmGuest;

    const printArea = document.getElementById('print-area');
    if (!printArea) return;

    printArea.innerHTML = `
      <div class="font-sans p-8 text-slate-800 max-w-4xl mx-auto">
        <div class="flex justify-between items-start border-b-2 border-indigo-600 pb-4 mb-5">
          <div>
            <h1 class="m-0 text-2xl text-slate-900 font-black">HOTEL SAGAR SONNET</h1>
            <div class="text-xs text-slate-500 mt-1">Luxury Living & Hospitality • Guest Master CRM Record</div>
          </div>
          <div class="text-right">
            <div class="text-sm font-extrabold text-indigo-600">DOSSIER REF: ${escapeHtml(g.reserve_id)}</div>
            <div class="text-xs text-slate-500 mt-0.5">Folio: ${escapeHtml(g.folio_number)}</div>
            <div class="text-xs text-slate-500">Printed: ${new Date().toLocaleString()}</div>
          </div>
        </div>

        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 mb-5">
          <h2 class="m-0 mb-2.5 text-xs font-black text-slate-700 uppercase tracking-wider">1. Guest Profile</h2>
          <table class="w-full text-xs border-collapse">
            <tr>
              <td class="py-1 w-1/4 font-semibold text-slate-500">Full Name:</td>
              <td class="py-1 w-1/4 font-bold text-slate-900">${escapeHtml(g.guest_name)}</td>
              <td class="py-1 w-1/4 font-semibold text-slate-500">Mobile:</td>
              <td class="py-1 w-1/4 font-bold text-slate-900">${escapeHtml(g.mobile)}</td>
            </tr>
            <tr>
              <td class="py-1 font-semibold text-slate-500">Registration Type:</td>
              <td class="py-1 font-bold text-indigo-600">${escapeHtml(g.registration_type)}</td>
              <td class="py-1 font-semibold text-slate-500">City / Nationality:</td>
              <td class="py-1 font-bold text-slate-900">${escapeHtml(g.city)} / ${escapeHtml(g.nationality)}</td>
            </tr>
            <tr>
              <td class="py-1 font-semibold text-slate-500">Email:</td>
              <td class="py-1 text-slate-900" colspan="3">${escapeHtml(g.email)}</td>
            </tr>
            <tr>
              <td class="py-1 font-semibold text-slate-500">Address:</td>
              <td class="py-1 text-slate-900" colspan="3">${escapeHtml(g.guest_address)}</td>
            </tr>
            <tr>
              <td class="py-1 font-semibold text-slate-500">Identity Proof:</td>
              <td class="py-1 text-slate-900">${escapeHtml(g.id_card_type)}: ${escapeHtml(g.id_card_number)}</td>
              <td class="py-1 font-semibold text-slate-500">VIP Card:</td>
              <td class="py-1 text-slate-900">${g.has_privilege_card ? escapeHtml(g.privilege_card_no) : 'None'}</td>
            </tr>
          </table>
        </div>

        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 mb-5">
          <h2 class="m-0 mb-2.5 text-xs font-black text-slate-700 uppercase tracking-wider">2. Accommodation & Stay Schedule</h2>
          <table class="w-full text-xs border-collapse">
            <tr>
              <td class="py-1 w-1/4 font-semibold text-slate-500">Allotted Room:</td>
              <td class="py-1 w-1/4 font-bold text-slate-900">Room #${escapeHtml(g.room_number)} (Floor ${escapeHtml(g.room_floor)})</td>
              <td class="py-1 w-1/4 font-semibold text-slate-500">Category & Bedding:</td>
              <td class="py-1 w-1/4 font-bold text-slate-900">${escapeHtml(g.room_category)} (${escapeHtml(g.room_bedding)})</td>
            </tr>
            <tr>
              <td class="py-1 font-semibold text-slate-500">Check-In Date/Time:</td>
              <td class="py-1 text-slate-900">${escapeHtml(g.reserve_date)} at ${escapeHtml(g.reserve_time)}</td>
              <td class="py-1 font-semibold text-slate-500">Check-Out Date/Time:</td>
              <td class="py-1 text-slate-900">${escapeHtml(g.checkout_date)} at ${escapeHtml(g.checkout_time)}</td>
            </tr>
            <tr>
              <td class="py-1 font-semibold text-slate-500">Duration of Stay:</td>
              <td class="py-1 text-slate-900">${g.total_nights} Night(s)</td>
              <td class="py-1 font-semibold text-slate-500">Reservation Mode:</td>
              <td class="py-1 text-slate-900">${escapeHtml(g.reservation_mode)}</td>
            </tr>
            <tr>
              <td class="py-1 font-semibold text-slate-500">Meal Plan:</td>
              <td class="py-1 font-bold text-orange-700" colspan="3">${escapeHtml(g.plan_name || 'Room Only (EP)')}</td>
            </tr>
            ${g.company ? `
            <tr>
              <td class="py-1 font-semibold text-slate-500">Corporate Account:</td>
              <td class="py-1 text-slate-900" colspan="3"><strong>${escapeHtml(g.company.name)}</strong> (GSTIN: ${escapeHtml(g.company.gstin)})</td>
            </tr>
            ` : ''}
          </table>
        </div>

        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 mb-6">
          <h2 class="m-0 mb-2.5 text-xs font-black text-slate-700 uppercase tracking-wider">3. Room Allocations &amp; Occupant Details</h2>
          <table class="w-full text-xs border-collapse">
            <thead>
              <tr class="border-b border-slate-300 text-left text-slate-500 text-[11px] uppercase">
                <th class="py-1.5 px-1">Room</th>
                <th class="py-1.5 px-1">Floor &amp; Category</th>
                <th class="py-1.5 px-1">Bedding</th>
                <th class="py-1.5 px-1">Assigned Occupant</th>
                <th class="py-1.5 px-1">Status</th>
              </tr>
            </thead>
            <tbody>
              ${(g.all_rooms && g.all_rooms.length > 0 ? g.all_rooms : [{
                room_number: g.room_number || 'Pending',
                floor: g.room_floor || '1',
                category: g.room_category || 'Standard',
                bedding: g.room_bedding || 'Standard',
                occupant_name: g.guest_name || 'Primary Guest',
                occupant_phone: g.mobile || 'N/A',
                status: g.status || 'Confirmed'
              }]).map(rm => `
                <tr class="border-b border-slate-200">
                  <td class="py-1.5 px-1 font-bold text-indigo-700 font-mono">${escapeHtml(rm.room_number)}</td>
                  <td class="py-1.5 px-1 font-bold text-slate-900">Floor ${escapeHtml(rm.floor)} • ${escapeHtml(rm.category)}</td>
                  <td class="py-1.5 px-1 text-slate-700">${escapeHtml(rm.bedding)}</td>
                  <td class="py-1.5 px-1 text-slate-700">${escapeHtml(rm.occupant_name)} ${rm.occupant_phone && rm.occupant_phone !== 'N/A' ? `(${escapeHtml(rm.occupant_phone)})` : ''}</td>
                  <td class="py-1.5 px-1 font-bold text-emerald-600">${escapeHtml(rm.status || g.status || 'Confirmed')}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
          ${(g.remarks || g.payment_remarks) && (g.remarks || g.payment_remarks).toLowerCase() !== 'none' ? `
            <div class="mt-2.5 text-[11px] text-slate-500">
              <strong>Remarks:</strong> ${escapeHtml(g.remarks || g.payment_remarks)}
            </div>
          ` : ''}
        </div>

        <div class="flex justify-between mt-10 text-xs text-slate-500">
          <div class="border-t border-slate-300 w-48 text-center pt-1.5">
            Guest Signature
          </div>
          <div class="border-t border-slate-300 w-48 text-center pt-1.5">
            Duty Manager Signature
          </div>
        </div>
      </div>
    `;

    window.print();
  }
</script>
@endpush
