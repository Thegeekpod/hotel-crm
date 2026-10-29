@extends('frontoffice.layouts.app')

@section('title', 'Guest Arrivals & Reservations - Hotel Sagar Sonnet CRM / PMS')


@section('content')
@php
  $serializedGuests = $guests->map(function($g) {
    $hasSettled = ($g->settlements && $g->settlements->where('status', 'Settled')->isNotEmpty())
        || ($g->settlements && $g->settlements->isNotEmpty() && (float)$g->balance <= 0.01)
        || ($g->payments && $g->payments->where('payment_type', 'Settlement')->isNotEmpty())
        || strtolower($g->status ?? '') === 'settlement done'
        || ((float)$g->balance <= 0.01 && (float)$g->payable_amount > 0 && ((float)$g->advance_amount > 0 || ($g->payments && $g->payments->isNotEmpty())));
    $currentStatus = ($hasSettled && $g->status !== 'Checked Out') ? 'Settlement Done' : ($g->status ?: 'Confirmed');
    $isLocked = $hasSettled || in_array($currentStatus, ['Settlement Done', 'Checked Out']);

    // Assemble all associated bookings for this reservation dossier
    $allBookings = collect([$g])->concat($g->additionalBookings ?? collect());

    // Group rooms and their registered occupants room-wise
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
                'floor' => (string)($r?->floorRelation?->floor ?? ($r?->floor ?? '1')),
                'category' => strtoupper($r?->categoryRelation?->name ?? ($r?->category ?? 'DELUXE')),
                'bedding' => $r?->beddingConfigRelation?->name ?? 'Standard',
                'rate' => (float)($r?->rate ?? $b->room_rate),
                'occupants' => [],
            ];
        }

        $occGuest = $b->guest;
        $gName = ($occGuest?->title?->name ? $occGuest->title->name . ' ' : '') . ($occGuest?->guest_name ?? 'Guest');
        $roomsMap[$rKey]['occupants'][] = [
            'booking_id' => $b->id,
            'guest_id' => $occGuest?->id,
            'is_primary' => ($b->id === $g->id),
            'name' => $gName,
            'mobile' => $occGuest?->mobile ?: 'N/A',
            'email' => $occGuest?->email ?: 'N/A',
            'city' => $occGuest?->city ?: 'N/A',
            'id_card_type' => $occGuest?->idCardType?->name ?? 'ID Proof',
            'id_card_number' => $occGuest?->id_card_number ?: 'N/A',
            'status' => $b->status ?: 'Confirmed',
        ];
    }

    $roomsList = array_values($roomsMap);
    $totalGuestsCount = $allBookings->count();
    $extraGuestsCount = max(0, $totalGuestsCount - 1);

    return [
      'id' => $g->id,
      'guest_id' => $g->guest_id,
      'reserve_id' => $g->reserve_id ?: ('RES-' . $g->id),
      'folio_number' => $g->folio_number ?: ('FOL-' . ($g->room?->room_number ?? '100') . '-' . $g->id),
      'guest_name' => ($g->guest?->title?->name ? $g->guest->title->name . ' ' : '') . ($g->guest?->guest_name ?? 'Guest'),
      'raw_name' => $g->guest?->guest_name ?? '',
      'title' => $g->guest?->title?->name ?? '',
      'mobile' => $g->guest?->mobile ?: 'N/A',
      'email' => $g->guest?->email ?: 'N/A',
      'city' => $g->guest?->city ?: 'N/A',
      'guest_address' => $g->guest?->guest_address ?: 'N/A',
      'nationality' => $g->guest?->nationality?->name ?? 'Indian',
      'id_card_type' => $g->guest?->idCardType?->name ?? 'N/A',
      'id_card_number' => $g->guest?->id_card_number ?: 'N/A',
      'registration_type' => $g->registrationType?->name ?? 'Regular',
      'reservation_mode' => $g->reservationMode?->name ?? 'Front Desk',
      'has_privilege_card' => (bool)$g->guest?->has_privilege_card,
      'privilege_card_no' => $g->guest?->privilege_card_no ?: 'N/A',
      'plan_name' => $g->plan_name ?: ($g->plan?->plan_name ?: 'Room Only (EP)'),
      'plan_code' => $g->plan?->plan_code ?: '',
      'plan_price' => (float)($g->plan_price ?? ($g->plan?->price ?? 0)),
      'plan_original_price' => (float)($g->plan_original_price ?? ($g->plan?->price ?? 0)),
      'plan_discount_amount' => (float)($g->plan_discount_amount ?? 0),
      'total_guests_count' => $totalGuestsCount,
      'extra_guests_count' => $extraGuestsCount,
      'rooms' => $roomsList,
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
      'advance_amount' => (float)$g->advance_amount,
      'balance' => (float)$g->balance,
      'payment_mode' => $g->paymentMode?->name ?? 'Cash / Direct',
      'payment_remarks' => $g->payment_remarks ?: 'None',
      'status' => $currentStatus,
      'is_locked' => $isLocked,
      'has_settled' => $hasSettled,
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
    
    <!-- Clean Header (Pill ribbon removed as requested) -->
    <div class="arrivals-header-card">
      <div class="flex justify-between items-center flex-wrap gap-3">
        <div>
          <h2 class="m-0 text-lg font-extrabold text-slate-800 flex items-center gap-2.5 flex-wrap">
            <i class="fa-solid fa-plane-arrival text-indigo-600"></i> 
            Guest Arrivals &amp; Advance Bookings
            @if(!empty($selectedDate))
              <span class="text-[11px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5">
                <i class="fa-regular fa-calendar-check"></i> {{ \Carbon\Carbon::parse($selectedDate)->format('D, d M Y') }}
              </span>
            @else
              <span class="text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5">
                <i class="fa-solid fa-users-viewfinder"></i> Confirmed &amp; Upcoming Arrivals
              </span>
            @endif
          </h2>
          <div class="text-xs text-slate-500 mt-0.5">
            @if(!empty($selectedDate))
              Showing guests arriving on <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}</strong>.
            @else
              Confirmed bookings awaiting check-in &amp; upcoming guest arrivals arranged chronologically with indexing, search, status filter, and live reservation dossiers.
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Toolbar: Search Filter & Status Dropdown Filter -->
    <div class="arrivals-toolbar">
      <div class="flex gap-3 items-center flex-1 flex-wrap">
        <!-- Search Box -->
        <div class="search-box-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="guest-search-input" placeholder="Search by Guest Name, Phone, Room, Reserve ID, Folio..." oninput="onSearchOrFilterChange()">
        </div>

        <!-- Date Choose Filter -->
        <div class="inline-flex items-center gap-1.5">
          <label for="arrival-date-filter" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
            <i class="fa-regular fa-calendar-days text-indigo-600"></i> Arrival Date:
          </label>
          <div class="inline-flex items-center gap-1.5">
            <input type="date" 
                   id="arrival-date-filter" 
                   class="pms-filter-date" 
                   value="{{ $selectedDate ?? '' }}" 
                   onchange="onArrivalDateFilterChanged(this.value)"
                   title="Choose date to see guests arriving on that date">
            @if(!empty($selectedDate))
              <button type="button" 
                      class="btn-ui-secondary px-2.5 py-1.5 text-[11px] h-[38px] rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 font-bold inline-flex items-center gap-1" 
                      onclick="clearArrivalDateFilter()" 
                      title="Clear date & view all arrivals">
                <i class="fa-solid fa-xmark"></i> All Arrivals
              </button>
            @endif
          </div>
        </div>

        <!-- Single Status Filter Dropdown -->
        <div class="inline-flex items-center gap-1.5">
          <label for="status-filter-select" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider whitespace-nowrap">
            <i class="fa-solid fa-filter text-indigo-600"></i> Status:
          </label>
          <select id="status-filter-select" class="pms-filter-select" onchange="onSearchOrFilterChange()">
            <option value="all">All Statuses</option>
            <option value="Confirmed">Confirm</option>
            <option value="Arrived">Arrived</option>
            <option value="Cancelled">Cancel</option>
          </select>
        </div>

        <!-- Reset Button -->
        <button type="button" class="btn-ui-secondary px-3.5 py-2 rounded-lg border border-slate-300 bg-white text-[11px] font-bold text-slate-600 inline-flex items-center gap-1.5 hover:bg-slate-50" onclick="resetFilters()">
          <i class="fa-solid fa-rotate-left"></i> Reset
        </button>
      </div>

      <!-- Page Size Selector -->
      <div class="flex gap-3 items-center">
        <div class="pms-per-page-wrap">
          <span>Show</span>
          <select class="pms-per-page-select" id="per-page-select" onchange="changePageSize(this.value)">
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
          </select>
          <span>entries</span>
        </div>
      </div>
    </div>

    <!-- Main Guests Arrivals Table with SL Indexing -->
    <div class="pms-page-content">
      <div class="pms-table-box rounded-xl shadow-xs bg-white">
        <table class="pms-table" id="arrivals-guest-table">
          <thead>
            <tr>
              <th class="w-11 text-center">SL</th>
              <th class="min-w-[140px]">Allotted Room</th>
              <th class="min-w-[145px] whitespace-nowrap">Booking Ref</th>
              <th class="min-w-[175px]">Guest Details</th>
              <th class="min-w-[140px]">Stay Dates</th>
              <th class="min-w-[90px] text-right">Total Gross</th>
              <th class="min-w-[90px] text-right">Advance</th>
              <th class="min-w-[90px] text-right">Balance</th>
              <th class="min-w-[105px] text-center">Status</th>
              <th class="w-[70px] text-center">Action</th>
            </tr>
          </thead>
          <tbody id="arrivals-guest-tbody">
            <!-- Rendered dynamically by renderTable() with SL Indexing & Pagination -->
          </tbody>
        </table>

        <!-- Table Pagination Footer -->
        <div class="pms-table-pagination flex justify-between items-center py-3 px-4.5 border-t border-slate-200 bg-white flex-wrap gap-2.5">
          <div class="pms-pagination-info text-xs font-semibold text-slate-500" id="pagination-info">
            Showing 0 to 0 of 0 records
          </div>
          <div class="pms-page-nav" id="pagination-nav"></div>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Bottom Summary Bar -->
<footer class="bg-white border-t border-slate-200 px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-3 shadow-2xs z-30 shrink-0 pms-bottom-bar">
  <div class="text-[11px] font-bold text-slate-500 flex items-center gap-3.5 flex-wrap">
    <span><i class="fa-solid fa-plane-arrival text-indigo-600"></i> Total Arrivals: <strong id="footer-total" class="text-slate-800">{{ $stats['total'] ?? count($guests) }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-circle-check text-indigo-700"></i> Confirmed: <strong id="footer-confirmed" class="text-slate-800">{{ $stats['confirmed'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-bell-concierge text-emerald-600"></i> Arrived: <strong id="footer-arrived" class="text-slate-800">{{ $stats['arrived'] ?? 0 }}</strong></span>
    <span class="text-slate-300">•</span>
    <span><i class="fa-solid fa-ban text-rose-500"></i> Cancelled: <strong id="footer-cancelled" class="text-slate-800">{{ $stats['cancelled'] ?? 0 }}</strong></span>
  </div>
</footer>

<!-- ========================================== -->
<!-- UNIFORM GUEST & RESERVATION DETAILS MODAL  -->
<!-- ========================================== -->
<div class="modal-backdrop" id="guest-details-modal">
  <div class="modal-window max-w-4xl w-full max-h-[92vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200">
    
    <!-- Modal Header -->
    <div class="modal-top bg-gradient-to-r from-indigo-600 to-indigo-800 text-white px-5 py-3.5 flex justify-between items-center border-b border-indigo-500/30">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-base text-white flex-shrink-0">
          <i class="fa-solid fa-plane-arrival"></i>
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h3 id="mdl-guest-name" class="m-0 text-base font-extrabold text-white">Guest Profile &amp; Arrival Dossier</h3>
            <span id="mdl-status-badge" class="status-badge bg-white text-indigo-700 font-extrabold shadow-xs">Confirmed</span>
          </div>
          <div class="text-[11px] text-indigo-100 font-mono mt-0.5">
            Reserve Ref: <span id="mdl-reserve-id" class="font-bold">-</span> &nbsp;|&nbsp; Folio: <span id="mdl-folio-number" class="font-bold">-</span>
          </div>
        </div>
      </div>
      <button type="button" class="modal-close bg-white/20 hover:bg-white/30 text-white w-8 h-8 rounded-full text-lg cursor-pointer flex items-center justify-center transition border-none" onclick="closeModal('guest-details-modal')">&times;</button>
    </div>

    <!-- Modal Body Area -->
    <div class="modal-content-area p-5 bg-slate-50 overflow-y-auto max-h-[calc(92vh-130px)]" id="arrival-modal-body">
      
      <!-- Top Highlight Banner -->
      <div class="dossier-highlight-box bg-gradient-to-r from-slate-50 to-slate-100 border border-slate-200 rounded-xl p-3.5 mb-4 flex justify-between items-center flex-wrap gap-3">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl flex-shrink-0">
            <i class="fa-solid fa-door-open"></i>
          </div>
          <div>
            <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Allotted Room(s) &amp; Category</div>
            <div class="text-sm font-black text-slate-900 mt-0.5" id="mdl-highlight-rooms">
              -
            </div>
          </div>
        </div>

        <div class="flex gap-2 items-center flex-wrap">
          <span class="text-xs font-extrabold bg-white border border-slate-300 px-2.5 py-1 rounded-md text-slate-700 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-users text-indigo-600"></i> <span id="mdl-total-guests">1 Person</span>
          </span>
          <span class="text-xs font-extrabold bg-orange-50 border border-orange-200 px-2.5 py-1 rounded-md text-orange-800 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-utensils text-orange-600"></i> <span id="mdl-plan-name">Room Only (EP)</span>
          </span>
          <span class="text-xs font-extrabold bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-md text-indigo-800 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-moon text-indigo-600"></i> <span id="mdl-total-nights">1</span> Night(s) Stay
          </span>
          <span class="text-xs font-extrabold bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-md text-emerald-800 inline-flex items-center gap-1.5 shadow-2xs">
            <i class="fa-solid fa-headset text-emerald-600"></i> <span id="mdl-reservation-mode">Front Desk</span>
          </span>
        </div>
      </div>

      <!-- 2-Column Uniform Dossier Grid -->
      <div class="dossier-grid">
        
        <!-- CARD 1: Primary Guest Profile Information -->
        <div class="dossier-card">
          <div class="dossier-card-title">
            <i class="fa-solid fa-user-check"></i> Primary Guest Profile Details
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Name</span>
            <span class="dossier-val" id="mdl-card-name">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Registration Type</span>
            <span class="dossier-val text-indigo-700 font-extrabold" id="mdl-card-regtype">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Mobile Number</span>
            <span class="dossier-val mono" id="mdl-card-phone">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Email Address</span>
            <span class="dossier-val" id="mdl-card-email">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">City / Location</span>
            <span class="dossier-val" id="mdl-card-city">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Address</span>
            <span class="dossier-val max-w-[220px] whitespace-normal" id="mdl-card-address">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Nationality</span>
            <span class="dossier-val" id="mdl-card-nationality">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Identity Proof</span>
            <span class="dossier-val" id="mdl-card-idproof">-</span>
          </div>

          <div class="dossier-field-row hidden" id="mdl-card-privilege-row">
            <span class="dossier-label">Privilege / VIP Card</span>
            <span class="dossier-val mono text-amber-700 font-bold" id="mdl-card-privilege">-</span>
          </div>
        </div>

        <!-- CARD 2: Stay Schedule & Corporate Account -->
        <div class="dossier-card">
          <div class="dossier-card-title flex justify-between items-center">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-calendar-days"></i> Stay &amp; Corporate Details
            </div>
            <button type="button" 
                    id="mdl-btn-extend-stay" 
                    onclick="if(currentActiveGuest) openExtendStayModal(currentActiveGuest.id)" 
                    class="border border-indigo-200 bg-indigo-50 text-indigo-700 text-[10px] font-extrabold px-2 py-0.5 rounded-md cursor-pointer inline-flex items-center gap-1 hover:bg-indigo-100 transition" 
                    title="Extend Stay Duration (Live Room Availability Check)">
              <i class="fa-solid fa-calendar-plus"></i> Extend Stay
            </button>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-In (Arrival)</span>
            <span class="dossier-val mono text-indigo-600 font-bold" id="mdl-card-checkin">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-Out (Departure)</span>
            <span class="dossier-val mono text-emerald-600 font-bold" id="mdl-card-checkout">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Duration of Stay</span>
            <span class="dossier-val" id="mdl-card-duration">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Reservation Mode</span>
            <span class="dossier-val" id="mdl-card-resmode">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Meal Plan</span>
            <span class="dossier-val text-orange-700 font-extrabold" id="mdl-card-mealplan">-</span>
          </div>

          <!-- Corporate Account Block -->
          <div id="mdl-corporate-card-section" class="mt-2.5 p-2.5 rounded-lg bg-indigo-50/50 border border-indigo-100 hidden">
            <div class="text-[10px] font-extrabold text-indigo-800 uppercase mb-1.5 flex items-center gap-1.5">
              <i class="fa-solid fa-building"></i> Corporate Account
            </div>
            <div class="dossier-field-row py-1">
              <span class="dossier-label">Company Name</span>
              <span class="dossier-val text-indigo-700 font-bold" id="mdl-company-name">-</span>
            </div>
            <div class="dossier-field-row py-1">
              <span class="dossier-label">Company GSTIN</span>
              <span class="dossier-val mono" id="mdl-company-gstin">-</span>
            </div>
            <div class="dossier-field-row py-1">
              <span class="dossier-label">Corporate Discount</span>
              <span class="dossier-val text-emerald-600 font-bold" id="mdl-company-discount">-</span>
            </div>
          </div>
        </div>

      </div>

      <!-- CARD 3: Allocated Rooms & Registered Occupants (Room-Wise) -->
      <div class="dossier-card mb-0">
        <div class="dossier-card-title">
          <i class="fa-solid fa-bed"></i> Allocated Rooms &amp; Registered Occupants (Room-Wise)
        </div>
        <div id="mdl-rooms-container">
          <!-- Populated dynamically room-wise -->
        </div>
      </div>

    </div>

    <!-- Modal Footer Actions -->
    <div class="modal-bot py-3 px-5 bg-white border-t border-slate-200 flex justify-between items-center flex-wrap gap-2.5">
      
      <!-- Quick Status Changer or Locked Badge from inside Modal -->
      <div id="mdl-status-control-container" class="flex items-center gap-2">
        <span class="text-[11px] font-extrabold text-slate-500 uppercase">Status:</span>
        <div id="mdl-status-control-wrapper" class="inline-flex items-center">
          <select class="status-quick-select" id="mdl-quick-status-select" onchange="onModalStatusChange(this.value)">
            <option value="Confirmed">Confirm</option>
            <option value="Checked In">Checkin</option>
            <option value="Cancelled">Cancel</option>
          </select>
          <span id="mdl-status-locked-indicator" class="badge-locked settled hidden py-1 px-2.5 text-xs font-bold">
            <i class="fa-solid fa-lock"></i> <span id="mdl-status-locked-text">Settlement Done (Locked)</span>
          </span>
        </div>
      </div>

      <div class="flex gap-2 items-center">
        <a id="mdl-btn-goto-crm" href="{{ url('front-office/guest') }}" class="btn-ui-secondary hidden px-3.5 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 text-xs font-bold no-underline items-center gap-1.5 hover:bg-indigo-100 transition" title="Open Guest Profile & Folio in CRM">
          <i class="fa-solid fa-users"></i> Guest CRM
        </a>
        <button type="button" class="btn-ui-secondary px-4 py-1.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-700 text-xs font-bold inline-flex items-center gap-1.5 hover:bg-slate-100 transition" onclick="printGuestDossier()">
          <i class="fa-solid fa-print"></i> Print Dossier
        </button>
        <button type="button" class="btn-ui-primary px-5 py-1.5 rounded-lg text-xs font-bold" onclick="closeModal('guest-details-modal')">
          Close
        </button>
      </div>

    </div>

  </div>
</div>

  </div>
</div>

<!-- ========================================================================= -->
<!-- ROOM SWIPE / SHIFT MODAL                                                  -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="room-swipe-modal">
  <div class="room-swipe-card max-w-xl w-full max-h-[90vh] bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-slate-200">
    <div class="room-swipe-header bg-gradient-to-r from-indigo-700 to-indigo-900 px-5 py-3.5 flex justify-between items-center text-white">
      <div class="flex items-center gap-2.5">
        <div class="w-8.5 h-8.5 rounded-lg bg-white/20 flex items-center justify-center text-sm text-white">
          <i class="fa-solid fa-arrow-right-arrow-left"></i>
        </div>
        <div>
          <h4 class="m-0 text-sm font-extrabold text-white">Room Swipe / Shift Manager</h4>
          <div class="text-[10px] text-indigo-100 opacity-90">Transfer room mid-stay with prorated tariff &amp; housekeeping sync</div>
        </div>
      </div>
      <button type="button" onclick="closeRoomSwipeModal()" class="bg-white/20 hover:bg-white/30 border-none text-white w-7 h-7 rounded-full text-base cursor-pointer flex items-center justify-center transition">&times;</button>
    </div>

    <!-- Hidden state inputs -->
    <input type="hidden" id="rswipe-master-booking-id">
    <input type="hidden" id="rswipe-from-room-id">
    <input type="hidden" id="rswipe-old-rate">
    <input type="hidden" id="rswipe-checkout-date">

    <div class="room-swipe-body p-4.5 overflow-y-auto flex flex-col gap-3.5">
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
            <div class="text-[11px] text-slate-500">
              <i class="fa-regular fa-calendar text-indigo-600"></i> Stay: <strong id="rswipe-stay-dates" class="text-slate-900">-</strong>
            </div>
          </div>
          <div class="flex justify-between items-center border-t border-dashed border-slate-200 pt-2 flex-wrap gap-2">
            <div class="flex items-center gap-2">
              <span class="room-tag status-cyan text-xs py-1 px-2.5">
                <i class="fa-solid fa-door-open"></i> Current Room: <strong id="rswipe-from-room-num">-</strong>
              </span>
              <span id="rswipe-from-room-meta" class="text-[11px] text-slate-600">-</span>
            </div>
            <div class="text-xs font-mono font-extrabold text-indigo-700">
              Current Rate: <span id="rswipe-from-room-rate">₹ 0.00</span> / night
            </div>
          </div>
        </div>

        <!-- Occupants to Transfer Checkbox Box -->
        <div>
          <div class="flex justify-between items-center mb-1.5">
            <label class="text-[11px] font-extrabold text-slate-900 uppercase tracking-wide">
              <i class="fa-solid fa-users"></i> Select Occupant(s) to Shift:
            </label>
            <label class="text-[11px] text-indigo-700 cursor-pointer font-bold inline-flex items-center gap-1">
              <input type="checkbox" id="rswipe-select-all-occ" onchange="toggleAllSwipeOccupants(this)"> All in Room
            </label>
          </div>
          <div id="rswipe-occupants-list" class="flex flex-col gap-1.5">
            <!-- Populated via JS -->
          </div>
          <div id="rswipe-shift-mode-hint" class="text-[10px] text-slate-500 italic mt-1">
            Whole room shift: Old room vacated &amp; scheduled for cleaning.
          </div>
        </div>

        <!-- Stay Dates & Transfer Date Input Grid -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">
              <i class="fa-regular fa-calendar-check text-indigo-700"></i> Effective Shift Date:
            </label>
            <input type="date" id="rswipe-transfer-date" class="form-input w-full py-1.5 px-2.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-900" onchange="onSwipeTransferDateChange()">
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">
              <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i> Nights Partition:
            </label>
            <div class="bg-slate-100 border border-slate-200 rounded-lg py-1.5 px-2.5 text-xs font-bold text-slate-700 flex justify-between">
              <span>Past: <strong id="rswipe-past-nights">1</strong>n</span>
              <span class="text-slate-300">|</span>
              <span class="text-indigo-700">Remaining: <strong id="rswipe-rem-nights">1</strong>n</span>
            </div>
          </div>
        </div>

        <!-- New Destination Room Selection -->
        <div class="grid grid-cols-3 gap-3 items-start">
          <div class="col-span-2">
            <label class="flex justify-between items-center text-[11px] font-bold text-slate-700 mb-1">
              <span><i class="fa-solid fa-door-open text-emerald-500"></i> Destination Room (Available Only):</span>
              <span id="rswipe-avail-count-badge" class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 py-0.5 px-2 rounded-full">Available Rooms Only</span>
            </label>
            <select id="rswipe-target-room-select" class="form-input w-full pms-select">
              <option value="">-- Search &amp; Choose Available Room --</option>
            </select>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">
              <i class="fa-solid fa-indian-rupee-sign text-emerald-500"></i> New Daily Rate:
            </label>
            <input type="number" id="rswipe-new-rate-input" step="0.01" min="0" class="form-input w-full py-1.5 px-2.5 rounded-lg border border-slate-300 text-xs font-bold font-mono text-slate-900" oninput="calculateSwipePricing()">
          </div>
        </div>

        <!-- Live Tariff Recalculation & Impact Preview Box -->
        <div class="swipe-preview-box" id="rswipe-preview-box">
          <div class="flex justify-between items-center font-extrabold text-xs text-slate-900 border-b border-dashed border-slate-300 pb-1.5">
            <span><i class="fa-solid fa-calculator text-indigo-700"></i> Tariff Impact Breakdown:</span>
            <span id="rswipe-preview-tag" class="bg-indigo-100 text-indigo-700 py-0.5 px-2 rounded text-[10px]">Shift Calculations</span>
          </div>
          <div class="flex flex-col gap-1 text-[11px]">
            <div class="flex justify-between text-slate-600">
              <span>Past Stay in Room <span id="rswipe-prev-src-room">302</span>:</span>
              <strong id="rswipe-prev-past-calc" class="font-mono">-</strong>
            </div>
            <div class="flex justify-between text-slate-600">
              <span>New Stay in Room <span id="rswipe-prev-dst-room">-</span>:</span>
              <strong id="rswipe-prev-new-calc" class="font-mono">-</strong>
            </div>
            <div class="flex justify-between border-t border-dashed border-slate-300 pt-1 font-extrabold">
              <span>Net Tariff Difference (<span id="rswipe-prev-rem-n">1</span> nights):</span>
              <span id="rswipe-prev-diff" class="font-mono text-xs text-indigo-700">₹ 0.00</span>
            </div>
          </div>
          <!-- Housekeeping Notification Alert -->
          <div id="rswipe-hk-notice" class="bg-red-50 border border-red-200 rounded-md p-2 text-[10px] text-red-700 flex items-center gap-1.5">
            <i class="fa-solid fa-broom"></i>
            <span>Room <strong id="rswipe-hk-room-name">-</strong> will be automatically marked <strong>Dirty (Pending Cleaning)</strong> in Housekeeping history.</span>
          </div>
        </div>

        <!-- Reason & Remarks -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">
              Reason for Shift:
            </label>
            <select id="rswipe-reason" class="form-input w-full pms-select text-xs">
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
            <label class="block text-[11px] font-bold text-slate-700 mb-1">
              Staff Remarks (Optional):
            </label>
            <input type="text" id="rswipe-remarks" placeholder="Optional notes for transfer dossier..." class="form-input w-full pms-input text-xs">
          </div>
        </div>

      </div>
    </div>

    <!-- Modal Footer Actions -->
    <div class="bg-slate-50 border-t border-slate-200 py-3 px-5 flex justify-end gap-2.5 items-center">
      <button type="button" onclick="closeRoomSwipeModal()" class="btn-ui-secondary px-3.5 py-1.5 rounded-lg text-xs font-bold cursor-pointer">
        Cancel
      </button>
      <button type="button" id="btn-submit-room-swipe" onclick="submitRoomSwipe()" class="btn-ui-primary px-4.5 py-1.5 rounded-lg text-xs font-extrabold cursor-pointer inline-flex items-center gap-1.5">
        <i class="fa-solid fa-arrow-right-arrow-left"></i> Confirm Room Swipe
      </button>
    </div>

  </div>
</div>

<!-- Floating Room Status Dropdown Menu Portal (Click room to change status) -->
<div id="floating-room-status-dropdown" class="room-status-dropdown-portal hidden" style="display: none;">
  <div class="room-status-item confirmed" data-status="Confirmed" onclick="selectRoomStatus('Confirmed')">
    <div class="flex items-center">
      <span class="dot"></span>
      <span>Confirm</span>
    </div>
    <i class="fa-solid fa-check check-icon text-[10px] opacity-0"></i>
  </div>
  <div class="room-status-item checkedin" data-status="Checked In" onclick="selectRoomStatus('Checked In')">
    <div class="flex items-center">
      <span class="dot"></span>
      <span>Checkin</span>
    </div>
    <i class="fa-solid fa-check check-icon text-[10px] opacity-0"></i>
  </div>
  <div class="room-status-item cancelled" data-status="Cancelled" onclick="selectRoomStatus('Cancelled')">
    <div class="flex items-center">
      <span class="dot"></span>
      <span>Cancel</span>
    </div>
    <i class="fa-solid fa-check check-icon text-[10px] opacity-0"></i>
  </div>
</div>

<!-- Shared Extend Stay Modal -->
@include('frontoffice.includes.extend-stay-modal')

@push('scripts')
<script>
  // All guests array loaded from database
  window.allArrivalGuests = @json($serializedGuests);
  let currentActiveGuest = null;
  let activeStatusDropdownGuestId = null;
  let currentPage = 1;
  let pageSize = 10;

  // Open Room Status Dropdown when a room number tag is clicked
  function openRoomStatusDropdown(e, guestId, currentStatus, isLocked) {
    e.stopPropagation();
    e.preventDefault();

    const item = (window.allArrivalGuests || []).find(g => String(g.id) === String(guestId));
    const locked = isLocked || (item && (item.is_locked || ['settlement done', 'checked out'].includes((item.status || '').toLowerCase())));
    if (locked) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(`Reservation is locked in '${item?.status || currentStatus}' status and cannot be modified.`, 'warning');
      } else {
        alert(`Reservation is locked in '${item?.status || currentStatus}' status and cannot be modified.`);
      }
      return;
    }

    activeStatusDropdownGuestId = guestId;
    const portal = document.getElementById('floating-room-status-dropdown');
    if (!portal) return;

    const btn = e.currentTarget;
    const rect = btn.getBoundingClientRect();

    const effectiveStatus = (item?.status || currentStatus || '').toLowerCase();
    portal.querySelectorAll('.room-status-item').forEach(el => {
      const st = el.getAttribute('data-status').toLowerCase();
      if (st === effectiveStatus || (st === 'checked in' && (effectiveStatus === 'checked in' || effectiveStatus === 'checkedin'))) {
        el.classList.add('active');
      } else {
        el.classList.remove('active');
      }
    });

    portal.style.display = 'flex';
    
    let top = rect.bottom + 4;
    let left = rect.left;
    const portalWidth = 145;
    if (left + portalWidth > window.innerWidth - 10) {
      left = window.innerWidth - portalWidth - 10;
    }
    
    portal.style.top = `${top}px`;
    portal.style.left = `${left}px`;
  }

  // Select a status option (Confirm, Checkin, Cancel)
  function selectRoomStatus(status) {
    const portal = document.getElementById('floating-room-status-dropdown');
    if (portal) portal.style.display = 'none';

    if (!activeStatusDropdownGuestId) return;
    updateGuestStatus(activeStatusDropdownGuestId, status);
  }

  // Close floating dropdown on click outside or page scroll
  window.addEventListener('click', function(e) {
    const portal = document.getElementById('floating-room-status-dropdown');
    if (portal && portal.style.display !== 'none' && !portal.contains(e.target) && !e.target.closest('.room-tag-clickable')) {
      portal.style.display = 'none';
    }
  });

  window.addEventListener('scroll', function() {
    const portal = document.getElementById('floating-room-status-dropdown');
    if (portal && portal.style.display !== 'none') {
      portal.style.display = 'none';
    }
  }, true);

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

  // Status badge color mapper
  function getStatusBadgeColor(status) {
    switch (status) {
      case 'Settlement Done': return 'settled';
      case 'Confirmed': return 'confirmed';
      case 'Arrived': return 'arrived';
      case 'Checked In': return 'checkedin';
      case 'Stay Over': return 'stayover';
      case 'Checked Out': return 'checkedout';
      case 'Cancelled': return 'cancelled';
      default: return 'confirmed';
    }
  }

  // Filter records based on search and status dropdown
  function getFilteredRecords() {
    const query = (document.getElementById('guest-search-input')?.value || '').toLowerCase().trim();
    const selectedStatus = document.getElementById('status-filter-select')?.value || 'all';

    return (window.allArrivalGuests || []).filter(item => {
      // Status dropdown filter
      const matchesStatus = (selectedStatus === 'all') || (item.status && item.status.toLowerCase() === selectedStatus.toLowerCase());
      if (!matchesStatus) return false;

      // Search keyword filter
      if (!query) return true;
      const roomsStr = (item.rooms || []).map(r => `${r.room_number} ${r.category} ${(r.occupants || []).map(o => o.name).join(' ')}`).join(' ');
      const searchBlob = `${item.guest_name} ${item.mobile} ${item.email} ${item.city} ${item.reserve_id} ${item.folio_number} ${item.plan_name} ${roomsStr} ${item.status}`.toLowerCase();
      return searchBlob.includes(query);
    });
  }

  // Render Table with SL Indexing and Pagination
  function renderTable() {
    const filtered = getFilteredRecords();
    const totalRecords = filtered.length;
    let totalPages = 1;
    let paginatedRecords = filtered;

    if (pageSize !== 'all') {
      const limit = parseInt(pageSize, 10);
      totalPages = Math.ceil(totalRecords / limit) || 1;
      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;
      const startIdx = (currentPage - 1) * limit;
      paginatedRecords = filtered.slice(startIdx, startIdx + limit);
    }

    const tbody = document.getElementById('arrivals-guest-tbody');
    if (!tbody) return;

    if (paginatedRecords.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="10" class="text-center text-slate-400 py-9 px-5">
            <div class="text-2xl text-slate-300 mb-2"><i class="fa-solid fa-users-slash"></i></div>
            <div class="text-xs font-bold text-slate-500">No matching arrivals found.</div>
            <div class="text-[11px] text-slate-400 mt-1">Try modifying your search or status filter.</div>
          </td>
        </tr>
      `;
      renderPagination(totalRecords, totalPages, 0, 0);
      return;
    }

    const startIndex = pageSize === 'all' ? 0 : (currentPage - 1) * parseInt(pageSize, 10);

    tbody.innerHTML = paginatedRecords.map((item, idx) => {
      const sl = startIndex + idx + 1;
      const formatINR = val => '₹ ' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      const badgeColor = getStatusBadgeColor(item.status);

      return `
        <tr class="guest-row" id="row-guest-${item.id}">
          <!-- SL Indexing -->
          <td class="text-center font-mono font-extrabold text-slate-500 text-xs">
            ${sl}
          </td>

          <!-- Allotted Room (Clickable Room Number to Change Status: Confirm, Checkin, Cancel) -->
          <td>
            ${item.rooms && item.rooms.length > 0 ? `
              <div class="flex flex-col gap-1">
                ${item.rooms.map(r => `
                  <div class="inline-flex items-center gap-1.5">
                    <button type="button" 
                            class="room-tag room-tag-clickable status-${badgeColor} ${(item.is_locked || ['settlement done', 'checked out'].includes((item.status || '').toLowerCase())) ? 'disabled' : ''}" 
                            onclick="openRoomStatusDropdown(event, ${item.id}, '${escapeHtml(item.status)}', ${item.is_locked ? 'true' : 'false'})"
                            title="${(item.is_locked || ['settlement done', 'checked out'].includes((item.status || '').toLowerCase())) ? 'Reservation is locked' : 'Click to change status (Confirm, Checkin, Cancel)'}">
                      <i class="fa-solid fa-door-open text-[10px]"></i>
                      <span>${escapeHtml(r.room_number)}</span>
                      ${(item.is_locked || ['settlement done', 'checked out'].includes((item.status || '').toLowerCase())) ? `
                        <i class="fa-solid fa-lock text-[8px] opacity-60 ml-0.5"></i>
                      ` : `
                        <i class="fa-solid fa-chevron-down text-[8px] opacity-70 ml-0.5"></i>
                      `}
                    </button>
                    <span class="text-[10px] text-slate-500 font-bold">
                      Floor ${escapeHtml(r.floor)}
                    </span>
                    <span class="text-slate-300">•</span>
                    <span class="text-[10px] text-slate-400">${escapeHtml(r.category)}</span>
                  </div>
                `).join('')}
              </div>
            ` : `
              <button type="button" 
                      class="room-tag room-tag-clickable status-${badgeColor} ${(item.is_locked || ['settlement done', 'checked out'].includes((item.status || '').toLowerCase())) ? 'disabled' : ''}"
                      onclick="openRoomStatusDropdown(event, ${item.id}, '${escapeHtml(item.status)}', ${item.is_locked ? 'true' : 'false'})"
                      title="${(item.is_locked || ['settlement done', 'checked out'].includes((item.status || '').toLowerCase())) ? 'Reservation is locked' : 'Click to change status (Confirm, Checkin, Cancel)'}">
                <i class="fa-solid fa-hourglass-half text-[10px]"></i>
                <span>Unassigned</span>
                <i class="fa-solid fa-chevron-down text-[8px] opacity-70 ml-0.5"></i>
              </button>
            `}
          </td>

          <!-- Booking Ref & Folio (Not in text wrap) -->
          <td class="whitespace-nowrap">
            <div class="font-mono font-extrabold text-indigo-600 text-xs cursor-pointer whitespace-nowrap inline-flex items-center gap-1 hover:underline" onclick="openGuestDetailsModalById(${item.id})" title="Click to view Arrival Dossier">
              <i class="fa-solid fa-receipt text-indigo-500 text-[11px]"></i>
              <span>${escapeHtml(item.reserve_id)}</span>
            </div>
            <div class="text-[10px] text-slate-500 font-mono mt-0.5 whitespace-nowrap flex items-center gap-1">
              <i class="fa-regular fa-file-lines"></i>
              <span>${escapeHtml(item.folio_number)}</span>
            </div>
          </td>

          <!-- Guest Details with +N persons -->
          <td>
            <div class="font-extrabold text-slate-900 text-xs inline-flex items-center flex-wrap gap-1">
              <span>${escapeHtml(item.guest_name)}</span>
              ${item.extra_guests_count > 0 ? `
                <span class="badge-extra-guests" title="${item.extra_guests_count} additional person(s) registered under this reservation">
                  <i class="fa-solid fa-user-plus text-[9px]"></i>
                  +${item.extra_guests_count} ${item.extra_guests_count === 1 ? 'person' : 'persons'}
                </span>
              ` : ''}
            </div>
            <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
              ${item.mobile && item.mobile !== 'N/A' ? `<span><i class="fa-solid fa-phone text-[9px] text-emerald-500"></i> ${escapeHtml(item.mobile)}</span>` : ''}
              ${item.registration_type ? `<span class="text-[9px] py-0.5 px-1.5 rounded bg-slate-200 text-slate-700 font-bold">${escapeHtml(item.registration_type)}</span>` : ''}
              ${item.city && item.city !== 'N/A' ? `<span class="text-slate-400"><i class="fa-solid fa-location-dot text-[9px]"></i> ${escapeHtml(item.city)}</span>` : ''}
            </div>
          </td>

          <!-- Stay Dates & Plan -->
          <td>
            <div class="text-[11px] font-bold text-slate-800">
              ${escapeHtml(item.reserve_date)}
            </div>
            <div class="text-[10px] text-slate-500 flex items-center gap-1 mt-0.5">
              <span>to ${escapeHtml(item.checkout_date)}</span>
              <span class="bg-slate-100 py-0.5 px-1.5 rounded font-extrabold text-[9px]">
                ${item.total_nights || 1}N
              </span>
            </div>
          </td>

          <!-- Total Gross -->
          <td class="text-right font-mono font-bold text-xs text-slate-700">
            ${formatINR(item.total_amount)}
          </td>

          <!-- Advance Paid -->
          <td class="text-right font-mono font-bold text-xs text-emerald-600">
            ${formatINR(item.advance_amount)}
          </td>

          <!-- Balance Pending -->
          <td class="text-right font-mono font-extrabold text-xs ${item.balance > 0 ? 'text-rose-600' : 'text-emerald-600'}">
            ${formatINR(item.balance)}
          </td>

          <!-- Status Badge -->
          <td class="text-center">
            <span id="badge-status-${item.id}" class="status-badge ${badgeColor}">
              ${(item.is_locked || ['settlement done', 'checked out'].includes((item.status || '').toLowerCase())) ? '<i class="fa-solid fa-lock text-[8px]"></i>' : '<i class="fa-solid fa-circle text-[6px]"></i>'}
              <span class="status-text">${escapeHtml(item.status)}</span>
            </span>
          </td>

          <!-- Actions: Eye View Button & Extend Stay Button -->
          <td class="text-center whitespace-nowrap">
            <div class="inline-flex items-center gap-1.5">
              <button type="button" 
                      class="btn-action-view" 
                      onclick="openGuestDetailsModalById(${item.id})" 
                      title="View Complete Arrival Dossier (Rooms, Occupants, Meal Plan, Schedule)">
                <i class="fa-solid fa-eye"></i>
              </button>
              ${!['checked out', 'cancelled', 'settlement done'].includes((item.status || '').toLowerCase()) ? `
                <button type="button" 
                        class="btn-action-extend" 
                        onclick="openExtendStayModal(${item.id})" 
                        title="Extend Stay Duration (Live Room Availability Check)">
                  <i class="fa-solid fa-calendar-plus"></i>
                </button>
              ` : ''}
            </div>
          </td>
        </tr>
      `;
    }).join('');

    const fromItem = totalRecords === 0 ? 0 : startIndex + 1;
    const toItem = pageSize === 'all' ? totalRecords : Math.min(startIndex + parseInt(pageSize, 10), totalRecords);
    renderPagination(totalRecords, totalPages, fromItem, toItem);
  }

  // Render Pagination Navigation Controls
  function renderPagination(total, pages, from, to) {
    const info = document.getElementById('pagination-info');
    if (info) {
      info.textContent = total === 0 ? 'Showing 0 records' : `Showing ${from} to ${to} of ${total} records`;
    }

    const nav = document.getElementById('pagination-nav');
    if (!nav) return;

    if (pages <= 1) {
      nav.innerHTML = '';
      return;
    }

    let buttons = `
      <button type="button" class="pms-page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="goToPage(${currentPage - 1})" title="Previous Page">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
    `;

    for (let p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || (p >= currentPage - 1 && p <= currentPage + 1)) {
        buttons += `
          <button type="button" class="pms-page-btn ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">
            ${p}
          </button>
        `;
      } else if (p === currentPage - 2 || p === currentPage + 2) {
        buttons += `<span class="pms-page-btn border-none cursor-default bg-transparent">...</span>`;
      }
    }

    buttons += `
      <button type="button" class="pms-page-btn" ${currentPage === pages ? 'disabled' : ''} onclick="goToPage(${currentPage + 1})" title="Next Page">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    `;

    nav.innerHTML = buttons;
  }

  // Handle page size change
  function changePageSize(val) {
    pageSize = val === 'all' ? 'all' : parseInt(val, 10);
    currentPage = 1;
    renderTable();
  }

  // Handle page jump
  function goToPage(p) {
    currentPage = p;
    renderTable();
  }

  // Triggered on search input or status dropdown change
  function onSearchOrFilterChange() {
    currentPage = 1;
    renderTable();
  }

  // Date Filter Change Handler
  function onArrivalDateFilterChanged(dateVal) {
    if (!dateVal) {
      clearArrivalDateFilter();
      return;
    }

    const todayStr = '{{ $today ?? date('Y-m-d') }}';
    if (dateVal < todayStr) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('Past dates cannot be selected for arrivals.', 'warning');
      } else {
        alert('Past dates cannot be selected for arrivals.');
      }
      const el = document.getElementById('arrival-date-filter');
      if (el) el.value = '';
      return;
    }

    const url = new URL(window.location.href);
    url.searchParams.set('date', dateVal);
    window.location.href = url.toString();
  }

  // Clear date filter to return to upcoming future dates
  function clearArrivalDateFilter() {
    const url = new URL(window.location.href);
    url.searchParams.delete('date');
    window.location.href = url.toString();
  }

  // Reset all filters (search, status, and arrival date)
  function resetFilters() {
    const searchInp = document.getElementById('guest-search-input');
    const statusSelect = document.getElementById('status-filter-select');
    if (searchInp) searchInp.value = '';
    if (statusSelect) statusSelect.value = 'all';

    const url = new URL(window.location.href);
    if (url.searchParams.has('date')) {
      url.searchParams.delete('date');
      window.location.href = url.toString();
      return;
    }

    currentPage = 1;
    renderTable();
  }

  // Update guest status via AJAX
  function updateGuestStatus(guestId, newStatus, selectElement) {
    const item = (window.allArrivalGuests || []).find(g => String(g.id) === String(guestId));
    const isLocked = item && (item.is_locked || ['settlement done', 'checked out'].includes((item.status || '').toLowerCase()));
    if (isLocked) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(`This reservation is locked in '${item.status}' status and cannot be modified.`, 'warning');
      } else {
        alert(`This reservation is locked in '${item.status}' status and cannot be modified.`);
      }
      if (selectElement) selectElement.value = item.status;
      return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (selectElement) selectElement.disabled = true;

    fetch(`{{ url('front-office/arrivals') }}/${guestId}/status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
      if (selectElement) selectElement.disabled = false;
      if (data.success) {
        // If checked in, remove from arrivals list immediately
        if (data.status === 'Checked In') {
          window.allArrivalGuests = (window.allArrivalGuests || []).filter(g => String(g.id) !== String(guestId));
          const totalEl = document.getElementById('footer-total');
          if (totalEl) totalEl.textContent = window.allArrivalGuests.length;
        } else {
          const item = (window.allArrivalGuests || []).find(g => String(g.id) === String(guestId));
          if (item) {
            item.status = data.status;
            item.is_locked = ['Settlement Done', 'Checked Out'].includes(data.status);
            if (Array.isArray(item.rooms)) {
              item.rooms.forEach(r => {
                if (Array.isArray(r.occupants)) {
                  r.occupants.forEach(occ => {
                    occ.status = data.status;
                  });
                }
              });
            }
          }
        }

        // Re-render table
        renderTable();

        // Update modal if currently opened with this guest
        if (currentActiveGuest && String(currentActiveGuest.id) === String(guestId)) {
          currentActiveGuest.status = data.status;
          currentActiveGuest.is_locked = ['Settlement Done', 'Checked Out'].includes(data.status);
          if (Array.isArray(currentActiveGuest.rooms)) {
            currentActiveGuest.rooms.forEach(r => {
              if (Array.isArray(r.occupants)) {
                r.occupants.forEach(occ => {
                  occ.status = data.status;
                });
              }
            });
          }
          const mdlBadge = document.getElementById('mdl-status-badge');
          if (mdlBadge) {
            mdlBadge.textContent = data.status;
            mdlBadge.className = `status-badge ${getStatusBadgeColor(data.status)}`;
          }
          openGuestDetailsModal(currentActiveGuest);
        }

        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        } else {
          alert(data.message);
        }

        // Reload page on status change to keep room states synced
        setTimeout(() => {
          window.location.reload();
        }, 600);
      } else {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message || data.error || 'Error updating status', 'error');
        } else {
          alert(data.message || data.error || 'Error updating status');
        }
      }
    })
    .catch(err => {
      if (selectElement) selectElement.disabled = false;
      console.error('Error updating status:', err);
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('Failed to update status. Please try again.', 'error');
      } else {
        alert('Failed to update status.');
      }
    });
  }

  // Helper to open modal by ID from table row click
  function openGuestDetailsModalById(id) {
    const guest = (window.allArrivalGuests || []).find(g => String(g.id) === String(id));
    if (guest) {
      openGuestDetailsModal(guest);
    }
  }

  // When status changed inside the Eye Details Modal
  function onModalStatusChange(newStatus) {
    if (!currentActiveGuest) return;
    const isLocked = currentActiveGuest.is_locked || ['settlement done', 'checked out'].includes((currentActiveGuest.status || '').toLowerCase());
    if (isLocked) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(`This reservation is locked in '${currentActiveGuest.status}' status and cannot be modified.`, 'warning');
      } else {
        alert(`This reservation is locked in '${currentActiveGuest.status}' status and cannot be modified.`);
      }
      return;
    }
    const guestId = currentActiveGuest.id;
    const rowSelect = document.getElementById(`status-select-${guestId}`);
    if (rowSelect) rowSelect.value = newStatus;
    updateGuestStatus(guestId, newStatus, rowSelect);
  }

  // Render Room-wise Allocated Rooms and Occupants in Modal
  function renderRoomsOccupants(rooms) {
    const container = document.getElementById('mdl-rooms-container');
    if (!container) return;
    if (!rooms || rooms.length === 0) {
      container.innerHTML = '<div class="text-slate-400 text-xs italic p-2.5">No specific room allocated.</div>';
      return;
    }

    let html = '';
    rooms.forEach(r => {
      const canSwipe = currentActiveGuest && currentActiveGuest.status !== 'Checked Out' && currentActiveGuest.status !== 'Cancelled' && r.room_id;
      const swipeBtnMarkup = canSwipe ? `
        <button type="button" 
                class="btn-room-swipe" 
                onclick="openRoomSwipeModal(${currentActiveGuest ? currentActiveGuest.id : 0}, ${r.room_id}, '${escapeHtml(r.room_number).replace(/'/g, "\\'")}')" 
                title="Swipe / Shift Room ${escapeHtml(r.room_number)}">
          <i class="fa-solid fa-arrow-right-arrow-left"></i> Room Swipe
        </button>
      ` : '';

      html += `
        <div class="room-alloc-card">
          <div class="room-alloc-header">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="room-tag ${currentActiveGuest ? 'status-' + getStatusBadgeColor(currentActiveGuest.status) : ''}">
                <i class="fa-solid fa-door-open"></i> ${escapeHtml(r.room_number)}
              </span>
              <span class="text-xs font-extrabold text-slate-900">Floor ${escapeHtml(r.floor)}</span>
              <span class="text-slate-300">•</span>
              <span class="text-[11px] text-slate-500 font-semibold">${escapeHtml(r.category)}</span>
              <span class="text-slate-300">•</span>
              <span class="text-[11px] text-slate-500">${escapeHtml(r.bedding)}</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="text-[10px] font-extrabold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">
                ${(r.occupants || []).length} Occupant(s)
              </span>
              ${swipeBtnMarkup}
            </div>
          </div>
          <div>
            <div class="text-[10px] font-extrabold text-slate-500 uppercase mb-1.5 tracking-wide">
              Registered Occupants in this Room:
            </div>
            <div class="flex flex-col gap-1">
      `;

      if (r.occupants && r.occupants.length > 0) {
        r.occupants.forEach(occ => {
          const swipeGuestMarkup = (canSwipe && occ.status !== 'Checked Out') ? `
            <button type="button" 
                    class="btn-room-swipe-mini" 
                    onclick="openRoomSwipeModal(${currentActiveGuest ? currentActiveGuest.id : 0}, ${r.room_id}, '${escapeHtml(r.room_number).replace(/'/g, "\\'")}', ${occ.booking_id})" 
                    title="Swipe / Transfer ${escapeHtml(occ.name)} to another room">
              <i class="fa-solid fa-arrow-right-arrow-left"></i> Shift
            </button>
          ` : '';

          html += `
            <div class="occupant-badge-row">
              <div class="flex items-center gap-1.5">
                <i class="fa-solid ${occ.is_primary ? 'fa-star' : 'fa-user'} ${occ.is_primary ? 'text-amber-500' : 'text-slate-400'}"></i>
                <strong class="text-slate-800">${escapeHtml(occ.name)}</strong>
                ${occ.is_primary ? '<span class="bg-amber-100 text-amber-800 text-[9px] font-extrabold px-1.5 py-0.5 rounded">Primary</span>' : ''}
              </div>
              <div class="flex items-center gap-2.5 text-slate-500 font-mono text-[11px] flex-wrap">
                <span><i class="fa-solid fa-phone text-[9px] text-emerald-500"></i> ${escapeHtml(occ.mobile)}</span>
                <span class="text-slate-300">•</span>
                <span>${escapeHtml(occ.id_card_type)}: ${escapeHtml(occ.id_card_number)}</span>
                <span class="text-slate-300">•</span>
                <span class="status-badge ${getStatusBadgeColor(occ.status || 'Confirmed')} text-[9px] py-0.5 px-2 font-extrabold rounded">
                  <i class="fa-solid fa-circle text-[5px]"></i> ${escapeHtml(occ.status || 'Confirmed')}
                </span>
                ${swipeGuestMarkup}
              </div>
            </div>
          `;
        });
      } else {
        html += '<div class="text-xs text-slate-400 p-1">No occupants assigned yet.</div>';
      }

      html += `
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
  }

  // Open Uniform Guest Details Modal (Without Payments Details, With Room-Wise Occupants & Meal Plan)
  function openGuestDetailsModal(g) {
    if (!g) return;
    currentActiveGuest = g;

    // Header info
    document.getElementById('mdl-guest-name').textContent = g.guest_name || 'Guest Details';
    document.getElementById('mdl-reserve-id').textContent = g.reserve_id || '-';
    document.getElementById('mdl-folio-number').textContent = g.folio_number || '-';

    const statusBadge = document.getElementById('mdl-status-badge');
    statusBadge.textContent = g.status || 'Confirmed';
    statusBadge.className = `status-badge ${getStatusBadgeColor(g.status)}`;

    // Top Highlight Info
    const hlRooms = (g.rooms || []).map(r => `${r.room_number} (Floor ${r.floor} • ${r.category})`).join(' • ');
    document.getElementById('mdl-highlight-rooms').textContent = hlRooms || 'Unassigned';

    const elTotalGuests = document.getElementById('mdl-total-guests');
    if (elTotalGuests) {
      const roomCnt = (g.rooms || []).length;
      elTotalGuests.textContent = `${g.total_guests_count || 1} Person(s) (${roomCnt} Room(s))`;
    }

    const elPlan = document.getElementById('mdl-plan-name');
    const formatArrivalMealPlanHtml = (name, price, origPrice) => {
      if (!name) return 'Room Only (EP)';
      const clean = String(name).trim();
      const lower = clean.toLowerCase();
      const effPrice = Number(price || 0);
      const original = Number(origPrice !== undefined ? origPrice : effPrice);
      if (lower === 'none' || lower === 'no meal plan' || lower === 'room only (ep)' || lower === 'ep' || (effPrice <= 0 && original <= 0)) {
        return typeof escapeHtml === 'function' ? escapeHtml(clean) : clean;
      }
      const escName = typeof escapeHtml === 'function' ? escapeHtml(clean) : clean;
      if (original > effPrice) {
        return `${escName} (+<span class="line-through opacity-65 mr-1">₹${original.toLocaleString('en-IN')}</span><strong>₹${effPrice.toLocaleString('en-IN')}</strong>)`;
      }
      if (effPrice > 0) {
        return `${escName} (+₹${effPrice.toLocaleString('en-IN')})`;
      }
      return escName;
    };
    if (elPlan) {
      elPlan.innerHTML = formatArrivalMealPlanHtml(g.plan_name, g.plan_price, g.plan_original_price);
    }

    const elNights = document.getElementById('mdl-total-nights');
    if (elNights) {
      elNights.textContent = `${g.total_nights || 1} Night(s) Stay`;
    }

    const elResMode = document.getElementById('mdl-reservation-mode');
    if (elResMode) {
      elResMode.textContent = g.reservation_mode || 'Front Desk';
    }

    // Profile Card (Primary Guest)
    document.getElementById('mdl-card-name').textContent = g.guest_name || '-';
    document.getElementById('mdl-card-phone').textContent = g.mobile || 'N/A';
    document.getElementById('mdl-card-email').textContent = g.email || 'N/A';
    document.getElementById('mdl-card-city').textContent = g.city || 'N/A';
    document.getElementById('mdl-card-address').textContent = g.guest_address || 'N/A';
    document.getElementById('mdl-card-nationality').textContent = g.nationality || 'Indian';
    document.getElementById('mdl-card-idproof').textContent = (g.id_card_type && g.id_card_type !== 'N/A') ? `${g.id_card_type}: ${g.id_card_number}` : (g.id_card_number || 'N/A');
    document.getElementById('mdl-card-regtype').textContent = g.registration_type || 'Regular';

    const privRow = document.getElementById('mdl-card-privilege-row');
    if (g.has_privilege_card && g.privilege_card_no && g.privilege_card_no !== 'N/A') {
      privRow.style.display = 'flex';
      document.getElementById('mdl-card-privilege').textContent = g.privilege_card_no;
    } else {
      privRow.style.display = 'none';
    }

    // Stay Card
    document.getElementById('mdl-card-checkin').textContent = `${g.reserve_date || 'N/A'} (${g.reserve_time || ''})`;
    document.getElementById('mdl-card-checkout').textContent = `${g.checkout_date || 'N/A'} (${g.checkout_time || ''})`;
    document.getElementById('mdl-card-duration').textContent = `${g.total_nights || 1} Night(s) Stay`;
    document.getElementById('mdl-card-resmode').textContent = g.reservation_mode || 'Front Desk';
    document.getElementById('mdl-card-mealplan').innerHTML = formatArrivalMealPlanHtml(g.plan_name, g.plan_price, g.plan_original_price);

    // Corporate Card
    const corpSection = document.getElementById('mdl-corporate-card-section');
    if (g.company && g.company.name) {
      corpSection.style.display = 'block';
      document.getElementById('mdl-company-name').textContent = g.company.name;
      document.getElementById('mdl-company-gstin').textContent = g.company.gstin || 'N/A';
      const discText = g.company.discount_type === 'amount' 
        ? `₹ ${Number(g.company.discount_value || 0).toLocaleString('en-IN')}` 
        : `${g.company.discount_value || 0}% Corporate Discount`;
      document.getElementById('mdl-company-discount').textContent = discText;
    } else {
      corpSection.style.display = 'none';
    }

    // Render Allocated Rooms and Registered Occupants Room-Wise
    renderRoomsOccupants(g.rooms || []);

    // Configure quick status changer vs locked badge in modal footer
    const isLocked = g.is_locked || ['settlement done', 'checked out'].includes((g.status || '').toLowerCase());
    const modalSelect = document.getElementById('mdl-quick-status-select');
    const lockedIndicator = document.getElementById('mdl-status-locked-indicator');
    const lockedText = document.getElementById('mdl-status-locked-text');
    const gotoCrmBtn = document.getElementById('mdl-btn-goto-crm');

    if (gotoCrmBtn) {
      gotoCrmBtn.style.display = 'inline-flex';
      gotoCrmBtn.href = `{{ url('front-office/guest') }}?search=${encodeURIComponent(g.raw_name || g.mobile || '')}`;
    }

    if (isLocked) {
      if (modalSelect) modalSelect.style.display = 'none';
      if (lockedIndicator) {
        lockedIndicator.style.display = 'inline-flex';
        lockedIndicator.className = `badge-locked ${g.status === 'Settlement Done' ? 'settled' : ''}`;
        if (lockedText) lockedText.textContent = `${g.status} (Locked)`;
      }
    } else {
      if (modalSelect) {
        modalSelect.style.display = 'inline-block';
        modalSelect.value = g.status || 'Confirmed';
        modalSelect.disabled = false;
        modalSelect.title = 'Change Reservation Status';
      }
      if (lockedIndicator) {
        lockedIndicator.style.display = 'none';
      }
    }

    // Reset scroll position to top
    const modalBody = document.getElementById('arrival-modal-body');
    if (modalBody) {
      modalBody.scrollTop = 0;
    }

    // Show modal
    openModal('guest-details-modal');
  }

  // Backdrop and Escape listeners
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('guest-details-modal')?.addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal('guest-details-modal');
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal('guest-details-modal');
      }
    });

    // Render table on initial load
    renderTable();
  });

  // Print Guest Dossier (Room-Wise without payment details)
  function printGuestDossier() {
    if (!currentActiveGuest) return;
    const g = currentActiveGuest;
    const printArea = document.getElementById('print-area');
    if (!printArea) return;

    let roomsHtml = '';
    (g.rooms || []).forEach(r => {
      roomsHtml += `
        <div class="mb-3 border border-slate-300 rounded-lg p-2.5">
          <div class="font-extrabold text-xs text-slate-800 mb-1.5">
            Room ${r.room_number} (Floor ${r.floor} • ${r.category} • ${r.bedding})
          </div>
          <table class="w-full border-collapse text-[11px]">
            <thead>
              <tr class="bg-slate-100 text-left">
                <th class="p-1 px-1.5">Occupant Name</th>
                <th class="p-1 px-1.5">Phone</th>
                <th class="p-1 px-1.5">ID Proof</th>
                <th class="p-1 px-1.5">Type</th>
              </tr>
            </thead>
            <tbody>
              ${(r.occupants || []).map(occ => `
                <tr class="border-b border-slate-100">
                  <td class="p-1 px-1.5 font-bold">${occ.name}</td>
                  <td class="p-1 px-1.5">${occ.mobile}</td>
                  <td class="p-1 px-1.5">${occ.id_card_type} - ${occ.id_card_number}</td>
                  <td class="p-1 px-1.5">${occ.is_primary ? 'Primary Guest' : 'Co-occupant'}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      `;
    });

    printArea.innerHTML = `
      <div class="p-8 font-sans text-slate-800 bg-white">
        <div class="text-center border-b-2 border-slate-700 pb-3 mb-5">
          <div class="text-2xl font-black text-indigo-700">HOTEL SAGAR SONNET</div>
          <div class="text-xs text-slate-500 mt-1">Front Desk Property Management System • Arrival &amp; Stay Dossier</div>
        </div>
        <div class="flex justify-between mb-4 bg-slate-50 p-2.5 px-3.5 border border-slate-300 text-xs font-bold rounded-lg">
          <span>Reserve ID: ${g.reserve_id}</span>
          <span>Folio No: ${g.folio_number}</span>
          <span>Status: ${g.status}</span>
        </div>
        <div class="mb-4 border border-slate-200 rounded-xl p-3.5">
          <div class="text-xs font-extrabold uppercase mb-2 text-indigo-700 border-b border-slate-100 pb-1">Primary Guest Profile</div>
          <table class="w-full border-collapse text-xs">
            <tr><td class="w-[35%] text-slate-500 font-semibold p-1 px-1.5">Guest Name:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.guest_name}</td></tr>
            <tr><td class="text-slate-500 font-semibold p-1 px-1.5">Phone / Mobile:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.mobile}</td></tr>
            <tr><td class="text-slate-500 font-semibold p-1 px-1.5">Email:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.email}</td></tr>
            <tr><td class="text-slate-500 font-semibold p-1 px-1.5">Address:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.guest_address}, ${g.city}</td></tr>
            <tr><td class="text-slate-500 font-semibold p-1 px-1.5">ID Proof:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.id_card_type} - ${g.id_card_number}</td></tr>
            <tr><td class="text-slate-500 font-semibold p-1 px-1.5">Nationality:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.nationality}</td></tr>
          </table>
        </div>
        <div class="mb-4 border border-slate-200 rounded-xl p-3.5">
          <div class="text-xs font-extrabold uppercase mb-2 text-indigo-700 border-b border-slate-100 pb-1">Stay &amp; Meal Plan Information</div>
          <table class="w-full border-collapse text-xs">
            <tr><td class="w-[35%] text-slate-500 font-semibold p-1 px-1.5">Check-In:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.reserve_date} ${g.reserve_time}</td></tr>
            <tr><td class="text-slate-500 font-semibold p-1 px-1.5">Check-Out:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.checkout_date} ${g.checkout_time}</td></tr>
            <tr><td class="text-slate-500 font-semibold p-1 px-1.5">Duration:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.total_nights} Night(s)</td></tr>
            <tr><td class="text-slate-500 font-semibold p-1 px-1.5">Meal Plan:</td><td class="font-bold text-orange-700 p-1 px-1.5">${g.plan_name}</td></tr>
            <tr><td class="text-slate-500 font-semibold p-1 px-1.5">Reservation Mode:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.reservation_mode}</td></tr>
            ${g.company ? `<tr><td class="text-slate-500 font-semibold p-1 px-1.5">Company:</td><td class="font-bold text-slate-900 p-1 px-1.5">${g.company.name} (GSTIN: ${g.company.gstin})</td></tr>` : ''}
          </table>
        </div>
        <div class="mb-4 border border-slate-200 rounded-xl p-3.5">
          <div class="text-xs font-extrabold uppercase mb-2 text-indigo-700 border-b border-slate-100 pb-1">Allocated Room(s) &amp; Registered Occupants</div>
          ${roomsHtml}
        </div>
        <div class="flex justify-between mt-10 text-xs font-bold text-slate-600">
          <div>Guest Signature: __________________</div>
          <div>Front Desk Staff: __________________</div>
        </div>
      </div>
    `;
    window.print();
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
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'Room Swipe Unavailable',
            text: data.message || 'Could not load room shift information.',
            confirmButtonColor: '#4338ca'
          });
        } else {
          alert(data.message || 'Could not load room shift information.');
        }
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
          <label class="swipe-occ-item flex items-center justify-between py-1.5 px-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition">
            <div class="flex items-center gap-2">
              <input type="checkbox" name="rswipe_occupants[]" value="${occ.booking_id}" class="rswipe-occ-chk w-3.5 h-3.5 accent-indigo-600" ${isSelected ? 'checked' : ''} onchange="calculateSwipePricing()">
              <span class="font-bold text-slate-900 text-xs">${escapeHtml(occ.guest_name)}</span>
              ${occ.is_primary ? '<span class="bg-amber-100 text-amber-800 text-[9px] font-extrabold px-1.5 py-0.5 rounded">Primary</span>' : ''}
            </div>
            <div class="text-[11px] text-slate-500 font-mono">
              <i class="fa-solid fa-phone text-[9px] text-emerald-500"></i> ${escapeHtml(occ.mobile)}
            </div>
          </label>
        `;
      });
      occList.innerHTML = occHtml || '<div class="text-xs text-slate-400 p-1.5">No occupants found in this room.</div>';

      const selectAllChk = document.getElementById('rswipe-select-all-occ');
      if (selectAllChk) selectAllChk.checked = !specificBookingId;

      populateSwipeAvailableRooms(data.available_rooms || []);

      if (loadingEl) loadingEl.style.display = 'none';
      if (contentEl) contentEl.style.display = 'flex';

      calculateSwipePricing();
    })
    .catch(err => {
      console.error(err);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Connection Error',
          text: 'Failed to communicate with hotel server.',
          confirmButtonColor: '#4338ca'
        });
      } else {
        alert('Failed to communicate with hotel server.');
      }
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
        <span class="font-extrabold font-mono text-xs text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-md">
          <i class="fa-solid fa-door-open text-[9px]"></i> R${roomNum}
        </span>
        <span class="font-bold text-slate-800 text-xs">Floor ${floor} • ${cat}</span>
        ${bedding ? `<span class="text-[10px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">${bedding}</span>` : ''}
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
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Destination Room Required',
          text: 'Please select a clean/vacant destination room from the dropdown.',
          confirmButtonColor: '#4338ca'
        });
      } else {
        alert('Please select a clean/vacant destination room.');
      }
      return;
    }

    if (transferredBookingIds.length === 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Select Occupant(s)',
          text: 'Please check at least one occupant to shift to the new room.',
          confirmButtonColor: '#4338ca'
        });
      } else {
        alert('Please check at least one occupant to shift.');
      }
      return;
    }

    const btn = document.getElementById('btn-submit-room-swipe');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Room Swipe...';
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    fetch(`{{ url('front-office/room-swipe') }}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
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
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Room Swipe Complete!',
            text: data.message || 'Room successfully transferred and billing updated.',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.reload();
          });
        } else {
          alert(data.message || 'Room successfully transferred and billing updated.');
          window.location.reload();
        }
      } else {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'Room Swipe Notice',
            text: data.message || 'Room shift could not be completed.',
            confirmButtonColor: '#4338ca'
          });
        } else {
          alert(data.message || 'Room shift could not be completed.');
        }
      }
    })
    .catch(err => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-arrow-right-arrow-left"></i> Confirm Room Swipe';
      }
      console.error(err);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'System Error',
          text: 'Network error while executing room swipe. Please try again.',
          confirmButtonColor: '#4338ca'
        });
      } else {
        alert('Network error while executing room swipe.');
      }
    });
  }

  // Backdrop click listener for room swipe modal
  document.addEventListener('DOMContentLoaded', function() {
    const swipeModalBackdrop = document.getElementById('room-swipe-modal');
    if (swipeModalBackdrop) {
      swipeModalBackdrop.addEventListener('click', function(e) {
        if (e.target === this) {
          closeRoomSwipeModal();
        }
      });
    }
  });

  // Explicit window bindings
  window.openRoomSwipeModal = openRoomSwipeModal;
  window.closeRoomSwipeModal = closeRoomSwipeModal;
  window.toggleAllSwipeOccupants = toggleAllSwipeOccupants;
  window.onSwipeRoomSelected = onSwipeRoomSelected;
  window.calculateSwipePricing = calculateSwipePricing;
  window.submitRoomSwipe = submitRoomSwipe;
</script>
@endpush
@endsection
