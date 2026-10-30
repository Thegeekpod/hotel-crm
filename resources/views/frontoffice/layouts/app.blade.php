<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Front Office - Hotel Sagar Sonnet CRM / PMS')</title>
  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  @stack('styles')
</head>
<body class="min-h-full flex flex-col font-sans text-slate-800 bg-slate-100 antialiased overflow-x-hidden">

  <!-- Top System Header Bar -->
  <header class="h-13 bg-white/95 backdrop-blur-md border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between shadow-2xs sticky top-0 z-40 transition-colors shrink-0 pms-app-header">
    <div class="flex items-center gap-3 pms-header-brand">
      <div class="w-8 h-8 rounded-lg bg-linear-to-br from-indigo-600 to-indigo-700 text-white flex items-center justify-center shadow-xs">
        <i class="fa-solid fa-hotel text-sm text-amber-300 gold-star"></i>
      </div>
      <div class="flex items-center gap-2">
        <span class="font-black text-sm tracking-wider text-slate-900">HOTEL SAGAR SONNET</span>
        <span class="bg-amber-400 text-slate-950 text-[10px] px-1.5 py-0.5 rounded font-black tracking-wider uppercase shadow-2xs">PMS 2026</span>
      </div>
    </div>
    
    <div class="hidden md:flex items-center gap-5 text-xs font-semibold text-slate-600 pms-header-center">
      <span class="inline-flex items-center gap-1.5">
        <i class="fa-regular fa-calendar-days text-indigo-500"></i>
        {{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format('l, d M Y') : date('l, d M Y') }}
        @if(isset($isToday) && $isToday)
          <span class="text-emerald-600 font-extrabold text-[10px] uppercase ml-1 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">(Today)</span>
        @endif
      </span>
      <span class="text-slate-300">|</span>
      <span class="inline-flex items-center gap-1.5">
        <i class="fa-solid {{ $shiftIcon ?? 'fa-sun' }} text-amber-500"></i>
        {{ $currentShift ?? 'Morning Shift (07:00 - 15:00)' }}
      </span>
      <span class="text-slate-300">|</span>
      <span class="inline-flex items-center gap-1.5">
        <i class="fa-solid fa-location-dot text-cyan-500"></i>
        Main Luxury Wing
      </span>
    </div>

    <div class="flex items-center gap-3 pms-header-right">
      <div class="inline-flex items-center gap-2 bg-slate-100/90 border border-slate-200 px-3 py-1 rounded-full text-xs font-bold text-slate-700 pms-user-tag">
        <i class="fa-solid fa-user-tie text-indigo-600"></i>
        <span class="hidden sm:inline">Debashis Roy (Front Desk)</span>
      </div>
      <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1 rounded-lg text-xs font-bold transition-all hover:scale-102 btn-pms-logout">
        <i class="fa-solid fa-power-off"></i>
        <span class="hidden sm:inline">Logout</span>
      </a>
    </div>
  </header>

  <!-- Top Module Navigation Tabs Ribbon -->
  <nav class="bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center gap-1.5 overflow-x-auto scrollbar-none py-1.5 shadow-2xs shrink-0 pms-tabs-ribbon">
    @php
      $tabBase = "inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap pms-tab-link";
      $tabActive = "bg-indigo-50 text-indigo-700 shadow-2xs border border-indigo-200/80 active";
      $tabInactive = "text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent";
    @endphp
    <a href="{{ route('frontoffice.dashboard') }}" class="{{ $tabBase }} {{ (((request()->routeIs('frontoffice.*') && !request()->routeIs('frontoffice.nightaudit*')) || (request()->is('front-office*') && !request()->is('front-office/night-audit*'))) || request()->routeIs('banquet*')) ? $tabActive : $tabInactive }}">
      <i class="fa-solid fa-desktop text-indigo-600 tab-ico"></i> Front Office
    </a>
    <a href="{{ route('pos.index') }}" class="{{ $tabBase }} {{ (request()->routeIs('pos.*') || request()->routeIs('stores.*')) ? $tabActive : $tabInactive }}">
      <i class="fa-solid fa-utensils text-emerald-600 tab-ico"></i> POS Sales
    </a>
    <a href="{{ route('accounts.index') }}" class="{{ $tabBase }} {{ request()->routeIs('accounts.*') ? $tabActive : $tabInactive }}">
      <i class="fa-solid fa-file-invoice-dollar text-purple-600 tab-ico"></i> Accounts
    </a>
    <a href="{{ route('frontoffice.nightaudit') }}" class="{{ $tabBase }} {{ (request()->routeIs('frontoffice.nightaudit*') || request()->is('front-office/night-audit*') || request()->routeIs('admin.nightaudit*')) ? $tabActive : $tabInactive }}">
      <i class="fa-solid fa-moon text-purple-600 tab-ico"></i> Night Audit
    </a>
    <a href="{{ route('admin.dashboard') }}" class="{{ $tabBase }} {{ (request()->routeIs('admin.*') && !request()->routeIs('admin.nightaudit*')) ? $tabActive : $tabInactive }}">
      <i class="fa-solid fa-user-gear text-slate-500 tab-ico"></i> Administrator
    </a>
  </nav>

  <!-- Subheader Component -->
  @if(!request()->routeIs('accounts.*') && !request()->is('accounts*') && !request()->routeIs('frontoffice.nightaudit*') && !request()->is('front-office/night-audit*'))
    @include('frontoffice.includes.subheader')
  @endif

  <!-- Main Content Viewport -->
  <div class="flex-1 flex flex-col min-w-0">
    @yield('content')
  </div>

  <!-- ROOM ACTION MODAL -->
  <div class="modal-backdrop" id="room-modal">
    <div class="modal-window w-full max-w-3xl rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
      <div class="modal-top bg-linear-to-r from-indigo-600 to-indigo-700 text-white px-5 py-3.5 flex justify-between items-center shrink-0">
        <div id="m-title" class="m-0 text-sm font-extrabold flex items-center gap-2 text-white flex-1 min-w-0">
          <i class="fa-solid fa-door-open"></i> Room Details
        </div>
        <button type="button" class="modal-close" onclick="closeModal('room-modal')">&times;</button>
      </div>
      <div class="modal-content-area p-5 bg-slate-50 flex-1 overflow-y-auto" id="m-body"></div>
      <div class="modal-bot px-5 py-3 bg-white border-t border-slate-200 flex justify-end gap-2 shrink-0" id="m-footer">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('room-modal')">Close</button>
      </div>
    </div>
  </div>

  <!-- ROOM STATUS QUICK POPOVER (HOUSEKEEPING & OPERATIONAL) -->
  <div id="room-card-status-popover" class="room-status-popover hidden" onclick="event.stopPropagation()">
    <div class="rsp-header">
      <div class="rsp-title">
        <i class="fa-solid fa-door-open"></i>
        <span>Room <strong id="rsp-room-num">--</strong></span>
        <span class="rsp-room-type" id="rsp-room-type">--</span>
      </div>
      <button type="button" class="rsp-close" onclick="closeRoomStatusPopover(event)">&times;</button>
    </div>
    <div class="rsp-body">
      <!-- Housekeeping Status -->
      <div class="rsp-field">
        <label class="rsp-label">
          <i class="fa-solid fa-broom text-amber-500"></i> Housekeeping Status
        </label>
        <select id="rsp-hk-select" class="rsp-select" onchange="onRspSelectChanged('hk')">
          <option value="">Select Housekeeping Status</option>
        </select>
      </div>

      <!-- Operational Status -->
      <div class="rsp-field">
        <label class="rsp-label">
          <i class="fa-solid fa-shield-halved text-indigo-500"></i> Operational Status
        </label>
        <select id="rsp-op-select" class="rsp-select" onchange="onRspSelectChanged('op')">
          <option value="">Select Operational Status</option>
        </select>
      </div>

      <!-- Quick Shortcuts -->
      <div class="rsp-quick-section">
        <div class="rsp-quick-title">Quick Actions</div>
        <div class="rsp-quick-chips">
          <button type="button" class="rsp-chip chip-clean" onclick="rspQuickSet('clean', event)" title="Mark Clean & Active">
            <i class="fa-solid fa-check"></i> Clean
          </button>
          <button type="button" class="rsp-chip chip-dirty" onclick="rspQuickSet('dirty', event)" title="Mark Dirty">
            <i class="fa-solid fa-broom"></i> Dirty
          </button>
          <button type="button" class="rsp-chip chip-maint" onclick="rspQuickSet('maintenance', event)" title="Under Maintenance">
            <i class="fa-solid fa-wrench"></i> Maint.
          </button>
          <button type="button" class="rsp-chip chip-block" onclick="rspQuickSet('blocked', event)" title="Out of Order / Blocked">
            <i class="fa-solid fa-ban"></i> Block
          </button>
        </div>
      </div>
    </div>
    <div class="rsp-footer">
      <span class="rsp-status-hint"><i class="fa-solid fa-clock-rotate-left"></i> Updates status history</span>
      <span class="rsp-spinner hidden" id="rsp-spinner">
        <i class="fa-solid fa-spinner fa-spin"></i> Saving...
      </span>
    </div>
  </div>

  <!-- NEW RESERVATION MODAL -->
  <div class="modal-backdrop" id="reserve-modal">
    <div class="modal-window w-full max-w-6xl max-h-[94vh] flex flex-col rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
      <div class="modal-top bg-linear-to-r from-indigo-600 to-indigo-700 text-white px-6 py-4 flex justify-between items-center shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-sm shadow-xs">
            <i class="fa-solid fa-calendar-plus text-amber-300"></i>
          </div>
          <div>
            <h3 id="reserve-modal-title" class="m-0 text-sm font-black tracking-wide text-white">Guest Check-In</h3>
            <div id="reserve-modal-subtitle" class="text-[11px] text-indigo-100 font-medium">Hotel Sagar Sonnet • Immediate Check-In (Status: <strong>Checked In</strong>)</div>
          </div>
        </div>
        <button type="button" class="modal-close" onclick="closeModal('reserve-modal')">&times;</button>
      </div>
      
      <div class="modal-content-area flex-1 overflow-y-auto p-5 sm:p-6 bg-slate-50">
        <form id="reservation-form" onsubmit="handleReserve(event)" class="w-full m-0">
          @csrf
          <!-- Dynamic Registration Type Pills -->
          <div class="flex flex-wrap gap-2 mb-4 p-2 bg-white border border-slate-200 rounded-xl shadow-2xs items-center">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider mr-1.5">Guest Type:</span>
            @php
              $regTypesList = $registrationTypes ?? \App\Models\RegistrationType::where('status', 'Active')->get();
            @endphp
            @forelse($regTypesList as $idx => $rt)
              @php
                $rtName = $rt->name;
                $slug = 'new';
                if (stripos($rtName, 'company') !== false || stripos($rtName, 'corporate') !== false) {
                  $slug = 'company';
                } elseif (stripos($rtName, 'regular') !== false) {
                  $slug = 'regular';
                } elseif (stripos($rtName, 'privilege') !== false || stripos($rtName, 'vip') !== false) {
                  $slug = 'privilege';
                }
              @endphp
              <label class="res-type-pill inline-flex items-center gap-1.5 font-bold cursor-pointer text-xs px-3 py-1.5 rounded-lg transition-all {{ $idx === 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-transparent text-slate-600 border border-transparent hover:bg-slate-50' }}">
                <input type="radio" name="res_type" value="{{ $rt->id }}" data-slug="{{ $slug }}" data-registration-id="{{ $rt->id }}" data-type-name="{{ $rtName }}" {{ $idx === 0 ? 'checked' : '' }} onchange="toggleResType()" class="accent-indigo-600 m-0"> {{ $rtName }}
              </label>
            @empty
              <label class="res-type-pill inline-flex items-center gap-1.5 font-bold cursor-pointer text-xs px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                <input type="radio" name="res_type" value="8" data-slug="new" data-registration-id="8" checked onchange="toggleResType()" class="accent-indigo-600 m-0"> New
              </label>
            @endforelse
          </div>

          <!-- Main 2-Column Responsive Grid -->
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 w-full items-start">
            
            <!-- Left Column: Form Fields (2 Cols on lg) -->
            <div class="lg:col-span-2 min-w-0 w-full flex flex-col gap-4">
              
              <!-- Reservation Meta Fields Card -->
              <div id="section-reservation-details" class="bg-white p-4 sm:p-5 border border-slate-200 rounded-xl shadow-2xs flex flex-col gap-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div class="min-w-0">
                    <label class="block text-[10px] font-black mb-1.5 text-slate-500 uppercase tracking-wider">Reserve ID</label>
                    <input type="text" id="reserve-id-input" name="reserve_id" class="pms-input-field w-full font-mono font-bold text-indigo-600 bg-slate-50" value="{{ $nextReserveId ?? \App\Models\Guest::generateNextReserveId() }}" readonly>
                  </div>
                  <div class="min-w-0">
                    <label class="block text-[10px] font-black mb-1.5 text-slate-500 uppercase tracking-wider">Mode of Reserve</label>
                    <select name="reservation_mode_id" class="pms-input-field w-full font-semibold">
                      @foreach($reservationModes ?? \App\Models\ReservationMode::where('status', 'Active')->get() as $rm)
                        <option value="{{ $rm->id }}">{{ $rm->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <!-- Stay Schedule & Duration Box -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 sm:p-4">
                  <div class="flex flex-wrap justify-between items-center gap-2 mb-3">
                    <span class="text-xs font-black text-slate-700 uppercase tracking-wide flex items-center gap-2">
                      <i class="fa-solid fa-calendar-days text-indigo-600"></i> Stay Schedule & Duration
                    </span>
                    <div class="flex items-center gap-2">
                      <span id="reservation-status-badge" class="text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-key"></i> Status: Checked In
                      </span>
                      <span id="stay-nights-badge" class="text-xs font-extrabold bg-indigo-100 text-indigo-800 px-2.5 py-0.5 rounded-full border border-indigo-200 inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-moon"></i> 1 Night Stay
                      </span>
                    </div>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php
                      $activeCheckInDate = $selectedDate ?? ($fromDate ?? date('Y-m-d'));
                      $activeCheckOutDate = \Carbon\Carbon::parse($activeCheckInDate)->addDay()->toDateString();
                    @endphp
                    <!-- Check-In -->
                    <div class="bg-white border border-slate-200 rounded-lg p-2.5">
                      <label class="flex items-center gap-1.5 text-[10px] font-black text-indigo-600 mb-1.5 uppercase tracking-wide">
                        <i class="fa-solid fa-plane-arrival"></i> Check-in (Arrival)
                      </label>
                      <div class="flex gap-2 items-center">
                        <input type="date" id="reserve-date-input" name="reserve_date" value="{{ $activeCheckInDate }}" min="{{ $activeCheckInDate }}" class="pms-input-field flex-3" onchange="onCheckInDateChange()">
                        <input type="time" id="reserve-time-input" name="reserve_time" class="pms-input-field flex-2">
                      </div>
                    </div>

                    <!-- Check-Out -->
                    <div class="bg-white border border-slate-200 rounded-lg p-2.5">
                      <label class="flex items-center gap-1.5 text-[10px] font-black text-emerald-600 mb-1.5 uppercase tracking-wide">
                        <i class="fa-solid fa-plane-departure"></i> Check-out (Departure)
                      </label>
                      <div class="flex gap-2 items-center">
                        <input type="date" id="checkout-date-input" name="checkout_date" value="{{ $activeCheckOutDate }}" min="{{ $activeCheckInDate }}" class="pms-input-field flex-3" onchange="onCheckOutDateChange()">
                        <input type="time" id="checkout-time-input" name="checkout_time" class="pms-input-field flex-2" value="11:00">
                      </div>
                    </div>
                  </div>

                  <!-- Date Selection Conflict Alert -->
                  <div id="dates-conflict-alert" class="hidden mt-3 bg-red-50 border border-red-300 rounded-lg p-3 shadow-2xs">
                    <div class="flex items-center justify-between gap-2">
                      <div class="flex items-center gap-2 min-w-0 flex-1 text-red-700 text-xs font-bold">
                        <i class="fa-solid fa-triangle-exclamation text-red-600 shrink-0"></i>
                        <span id="dates-conflict-text" class="leading-snug"></span>
                      </div>
                      <span class="text-[10px] font-black bg-red-600 text-white px-2 py-0.5 rounded uppercase tracking-wider shrink-0">
                        Dates Conflict
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Company Specific Fields -->
                <div id="section-company-fields" class="hidden p-4 bg-emerald-50/50 border border-dashed border-emerald-400 rounded-xl">
                  <div class="mb-3">
                    <label class="block text-[10px] font-black mb-1.5 text-emerald-700 uppercase tracking-wider"><i class="fa-solid fa-building"></i> Company Name</label>
                    <select name="company_id" id="company-select" class="pms-input-field w-full border-emerald-400 font-bold" onchange="toggleNewCompany(this)">
                      <option value="">-- Select Corporate Account --</option>
                      @foreach($companies ?? \App\Models\Company::where('status', 'Active')->get() as $cmp)
                        <option value="{{ $cmp->id }}" data-discount-type="{{ $cmp->discount_type ?? 'percentage' }}" data-discount-value="{{ (float)($cmp->discount_value ?? 0) }}" data-meal-discounts="{{ json_encode($cmp->meal_discounts ?? []) }}">{{ $cmp->name }}</option>
                      @endforeach
                      <option value="new" class="text-indigo-600 font-black">+ Register New Company</option>
                    </select>
                  </div>
                  <div id="new-company-details" class="hidden border-t border-emerald-200 pt-3">
                    <div class="mb-3">
                      <label class="block text-[10px] font-black mb-1.5 text-slate-500 uppercase tracking-wider">New Company Name</label>
                      <input type="text" name="new_company_name" class="pms-input-field w-full" placeholder="Full Corporate Entity Name">
                    </div>
                    <div class="mb-3">
                      <label class="block text-[10px] font-black mb-1.5 text-slate-500 uppercase tracking-wider">Company Address</label>
                      <input type="text" name="new_company_address" class="pms-input-field w-full" placeholder="Billing Address">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                      <div>
                        <label class="block text-[10px] font-black mb-1.5 text-slate-500 uppercase tracking-wider">GSTIN</label>
                        <input type="text" name="new_company_gstin" class="pms-input-field w-full font-mono" placeholder="29ABCDE1234F1Z5">
                      </div>
                      <div>
                        <label class="block text-[10px] font-black mb-1.5 text-slate-500 uppercase tracking-wider">Corporate Phone</label>
                        <input type="text" name="new_company_phone" class="pms-input-field w-full" placeholder="Office / Mobile">
                      </div>
                    </div>
                    <!-- New Company Corporate Discount Configuration -->
                    <div class="p-3 bg-white border border-emerald-300 rounded-lg">
                      <label class="block text-[10px] font-black mb-1.5 text-emerald-700 uppercase tracking-wider">Corporate Discount Rate</label>
                      <div class="flex gap-4 items-center mb-2">
                        <label class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 cursor-pointer">
                          <input type="radio" name="new_company_discount_type" value="percentage" checked onchange="toggleNewCompanyDiscountType(this.value)" class="accent-emerald-600">
                          <span>Percentage (%)</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-700 cursor-pointer">
                          <input type="radio" name="new_company_discount_type" value="amount" onchange="toggleNewCompanyDiscountType(this.value)" class="accent-emerald-600">
                          <span>Amount (₹)</span>
                        </label>
                      </div>
                      <div id="new-company-disc-pct-wrap">
                        <input type="number" name="new_company_discount_pct" id="new-comp-disc-pct" oninput="calculateReservationBilling()" min="0" max="100" step="any" class="pms-input-field w-full" placeholder="Discount Percentage % (e.g. 10)">
                      </div>
                      <div id="new-company-disc-amt-wrap" class="hidden">
                        <input type="number" name="new_company_discount_amt" id="new-comp-disc-amt" oninput="calculateReservationBilling()" min="0" step="any" class="pms-input-field w-full" placeholder="Amount in ₹ (e.g. 500)">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Guests Container -->
              <div id="guests-container">
                <div class="guest-block mb-4">
                  <!-- Guest Details Card -->
                  <div class="section-guest-details bg-white p-4 sm:p-5 border border-slate-200 rounded-xl shadow-2xs flex flex-col gap-3">
                    
                    <div class="flex justify-between items-center border-b border-slate-100 pb-2.5 mb-1">
                      <h4 class="guest-heading text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2 m-0">
                        <i class="fa-solid fa-user"></i> Guest #1
                      </h4>
                    </div>

                    <!-- Regular Search Section -->
                    <div class="section-regular-search hidden mb-3 p-3 bg-indigo-50/50 border border-dashed border-indigo-300 rounded-lg">
                      <label class="block text-[10px] font-black mb-1.5 text-slate-500 uppercase tracking-wider">Search Regular Guest</label>
                      <div class="flex gap-2">
                        <input type="text" class="regular-search-input pms-input-field flex-1" placeholder="Enter Mobile Number">
                        <button type="button" class="btn-ui-primary" onclick="searchRegularGuest(this)"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                      </div>
                    </div>

                    <!-- Row 1: Title + Guest Name -->
                    <div class="flex gap-3">
                      <div class="w-24 shrink-0">
                        <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Title</label>
                        <select name="title_id" class="pms-select-title pms-input-field w-full font-semibold">
                          @foreach($titles ?? \App\Models\Title::where('status', 'Active')->get() as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="flex-1 min-w-0">
                        <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Guest Name <span class="text-rose-600">*</span></label>
                        <input type="text" name="guest_name" class="guest-name-input pms-input-field w-full" placeholder="First and Last Name" required>
                      </div>
                    </div>

                    <!-- Row 2: Address -->
                    <div>
                      <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Guest Address</label>
                      <input type="text" name="guest_address" class="guest-address-input pms-input-field w-full" placeholder="House No, Street, Landmark">
                    </div>

                    <!-- Row 3: Nationality + City -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <div>
                        <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Nationality</label>
                        <select name="nationality_id" class="pms-select-nationality pms-input-field w-full font-semibold">
                          @foreach($nationalities ?? \App\Models\Nationality::where('status', 'Active')->get() as $nat)
                            <option value="{{ $nat->id }}" {{ strtolower($nat->name) === 'indian' ? 'selected' : '' }}>{{ $nat->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div>
                        <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">City</label>
                        <input type="text" name="city" class="guest-city-input pms-input-field w-full" placeholder="e.g. Kolkata">
                      </div>
                    </div>

                    <!-- Row 4: Mobile + Email -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      <div>
                        <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Telephone / Mobile <span class="text-rose-600">*</span></label>
                        <input type="text" name="mobile" class="guest-mobile-input pms-input-field w-full font-mono" placeholder="+91 9876543210" required>
                      </div>
                      <div>
                        <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Email ID</label>
                        <input type="email" name="email" class="guest-email-input pms-input-field w-full" placeholder="guest@email.com">
                      </div>
                    </div>

                    <!-- Privilege Card Row -->
                    <div class="privilege-checkbox-container flex flex-col gap-2 p-3 bg-emerald-50/50 border border-emerald-200 rounded-lg">
                      <label class="flex items-center gap-2 font-bold text-xs cursor-pointer text-emerald-800">
                        <input type="checkbox" class="chk-privilege accent-emerald-600 cursor-pointer" onchange="togglePrivilegeInput(this)"> Has Privilege Card
                      </label>
                      <div class="privilege-input-container hidden w-full">
                        <input type="text" name="privilege_card_no" class="pms-input-field w-full max-w-xs border-emerald-400" placeholder="Privilege Card No.">
                      </div>
                    </div>

                    <!-- Dynamic Cascading Room Assign: Category -> Floor -> Room No -->
                    <div class="border-t border-slate-100 pt-3">
                      <div class="room-assign-header-row flex justify-between items-center mb-2.5">
                        <div class="flex items-center gap-2">
                          <h4 class="text-[11px] font-black text-indigo-600 uppercase tracking-wider m-0 flex items-center gap-1.5">
                            <i class="fa-solid fa-hotel"></i> Room Assignment
                          </h4>
                          <span class="room-lock-indicator hidden items-center gap-1 text-[10px] font-bold text-indigo-700 bg-indigo-100 border border-indigo-200 px-2 py-0.5 rounded-md">
                            <i class="fa-solid fa-lock text-[9px]"></i> <span class="room-lock-text">Room Locked</span>
                          </span>
                        </div>
                        <div class="same-room-checkbox-wrapper hidden">
                          <label class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-700 cursor-pointer bg-indigo-50 px-2 py-1 rounded-md border border-indigo-200">
                            <input type="checkbox" class="chk-same-room accent-indigo-600" onchange="toggleSameRoom(this)">
                            <span>Same Room</span>
                          </label>
                        </div>
                      </div>

                      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                        <div>
                          <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Room Category</label>
                          <select name="category_id" class="select-room-category pms-input-field w-full font-semibold" onchange="onCategoryChange(this)">
                            <option value="">Select Category</option>
                            @php
                              $layoutRoomCategories = (isset($categories) && $categories instanceof \Illuminate\Support\Collection && ($categories->first() instanceof \App\Models\RoomCategory))
                                ? $categories 
                                : \App\Models\RoomCategory::where('status', 'Active')->get();
                            @endphp
                            @foreach($layoutRoomCategories as $cat)
                              <option value="{{ $cat->id }}" data-category-name="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div>
                          <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Floor</label>
                          <select name="floor_id" class="select-room-floor pms-input-field w-full font-semibold" onchange="onFloorChange(this)">
                            <option value="">Select Floor</option>
                            @foreach($floors ?? \App\Models\Floor::where('status', 'Active')->orderBy('floor', 'asc')->get() as $fl)
                              <option value="{{ $fl->id }}" data-floor-no="{{ $fl->floor }}">Floor {{ $fl->floor }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div>
                          <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Room No.</label>
                          <select name="room_id" class="select-room-no pms-input-field w-full font-bold text-slate-900" onchange="onRoomChange(this)">
                            <option value="">Select Room No.</option>
                            @foreach($rackRooms ?? [] as $rm)
                              <option value="{{ $rm['id'] ?? $rm['room'] }}" data-room-no="{{ $rm['room'] }}" data-rate="{{ $rm['rate'] }}" data-bedding="{{ $rm['bedding'] ?? 'King Size Master (72x78)' }}" data-max-adults="{{ $rm['max_adults'] ?? 2 }}" data-max-children="{{ $rm['max_children'] ?? 1 }}" data-max-pax="{{ $rm['max_pax'] ?? 3 }}" data-plan-id="{{ $rm['plan_id'] ?? '' }}" data-plan-name="{{ $rm['plan_name'] ?? '' }}" data-plan-price="{{ $rm['plan_price'] ?? 0 }}">{{ $rm['room'] }} ({{ $rm['type'] }})</option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <!-- Dynamic Bedding Configuration & Pax Capacity Info Box -->
                      <div class="room-capacity-banner hidden bg-indigo-50/50 border border-indigo-200 rounded-xl p-3 mb-3">
                        <div class="flex justify-between items-center flex-wrap gap-2">
                          <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-md bg-indigo-600 text-white flex items-center justify-center text-xs shadow-xs">
                              <i class="fa-solid fa-bed"></i>
                            </div>
                            <div>
                              <div class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Bedding Configuration</div>
                              <div class="bedding-name-text text-xs font-bold text-slate-800">-</div>
                            </div>
                          </div>
                          
                          <div class="flex items-center gap-2 flex-wrap">
                            <div class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-700 bg-indigo-100/80 px-2 py-0.5 rounded-md">
                              <i class="fa-solid fa-user-group"></i>
                              <span>Max: <strong class="max-pax-count">3 Pax</strong></span>
                            </div>
                            <div class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md">
                              <i class="fa-solid fa-person"></i>
                              <span>Adults: <strong class="max-adults-count">2</strong></span>
                            </div>
                            <div class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded-md">
                              <i class="fa-solid fa-child"></i>
                              <span>Kids: <strong class="max-children-count">1</strong></span>
                            </div>
                            <div class="inline-flex items-center gap-1 text-xs font-extrabold text-emerald-700 bg-white border border-emerald-300 px-2.5 py-0.5 rounded-md">
                              <i class="fa-solid fa-tag"></i>
                              <span class="room-rate-text">₹ 4,500/night</span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Real-Time Room Booking Conflict & Matching Alternatives Alert Box -->
                      <div class="room-conflict-alert-box hidden mb-3 rounded-xl overflow-hidden border border-red-300 bg-red-50/70 shadow-xs">
                        <div class="bg-linear-to-r from-red-600 to-red-700 text-white px-3.5 py-2 flex items-center justify-between">
                          <div class="flex items-center gap-2 text-xs font-black uppercase tracking-wider">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>Room Already Booked / Conflict Detected!</span>
                          </div>
                          <span class="conflict-badge bg-white/20 text-[10px] font-extrabold px-2 py-0.5 rounded uppercase">Not Available</span>
                        </div>
                        <div class="p-3 text-red-900 text-xs">
                          <div class="conflict-message-text font-bold mb-1"></div>
                          <div class="conflict-sub-text text-[11px] text-red-700">Please choose an alternative room or adjust your check-in / check-out dates.</div>
                        </div>
                        <div class="conflict-alternatives-section bg-white border-t border-red-200 p-3">
                          <div class="text-[11px] font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-wand-magic-sparkles text-indigo-600"></i>
                            <span>Available Alternative Rooms Matching Your Stay Schedule:</span>
                          </div>
                          <div class="conflict-alt-rooms-list flex flex-col gap-2 max-h-56 overflow-y-auto pr-1"></div>
                        </div>
                      </div>
                    </div>

                    <!-- Identity Details -->
                    <div class="border-t border-slate-100 pt-3">
                      <h4 class="text-[11px] font-black text-indigo-600 uppercase tracking-wider flex items-center gap-1.5 mb-2.5 m-0">
                        <i class="fa-solid fa-id-card"></i> Identity Details
                      </h4>
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                          <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Identity Card Type</label>
                          <select name="id_card_type_id" class="pms-select-idtype pms-input-field w-full font-semibold">
                            <option value="">Select ID Type</option>
                            @foreach($idCardTypes ?? \App\Models\IdCardType::where('status', 'Active')->get() as $idc)
                              <option value="{{ $idc->id }}">{{ $idc->name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div>
                          <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Card Number</label>
                          <input type="text" name="id_card_number" class="guest-id-number pms-input-field w-full font-mono" placeholder="ID Card Number">
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <button type="button" class="btn-ui-secondary w-full h-10 border-dashed border-indigo-400 text-indigo-600 bg-indigo-50/70 hover:bg-indigo-100/70 font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition-all shadow-none mb-4" onclick="addAnotherGuest()">
                <i class="fa-solid fa-user-plus"></i> Add Another Guest
              </button>

            </div>

            <!-- Right Column: Digital Passes & Payment Settlement -->
            <div class="bg-white p-4 sm:p-5 rounded-xl border border-slate-200 shadow-2xs flex flex-col gap-4 w-full">
              
              <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <span class="text-xs font-black text-indigo-600 uppercase tracking-wider flex items-center gap-2">
                  <i class="fa-solid fa-qrcode"></i> Digital Passes & Mobile KYC
                </span>
                <span id="qr-live-status-pill" class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                  <i class="fa-solid fa-circle-check"></i> Live Ready
                </span>
              </div>

              <!-- Guest QR Pass Card -->
              <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 relative" id="guest-qr-pass-card">
                <div class="flex justify-between items-center mb-1.5">
                  <span class="inline-flex items-center gap-1 text-[10px] font-extrabold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded uppercase tracking-wide">
                    <i class="fa-solid fa-user"></i> <span id="pass-guest-badge">Guest #1</span>
                  </span>
                  <div class="flex items-center gap-1.5">
                    <span id="qr-timer-badge" class="text-[10px] font-mono font-bold text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded flex items-center gap-1">
                      <i class="fa-regular fa-clock"></i> <span id="qr-countdown-text">05:00</span>
                    </span>
                    <span class="text-[9px] text-slate-400 font-mono font-bold">PASS-01</span>
                  </div>
                </div>
                
                <div class="text-xs font-extrabold text-slate-800 leading-tight" id="pass-guest-name-text">Souvik Paul</div>
                <div class="text-[11px] text-slate-500 font-mono font-semibold mt-0.5 flex items-center gap-1">
                  <i class="fa-solid fa-phone text-[9px] text-indigo-500"></i> <span id="pass-guest-mobile-text">+91 62955 87845</span>
                </div>

                <!-- QR Container with Expiry Overlay -->
                <div class="bg-white border border-dashed border-slate-300 rounded-lg p-2.5 mt-2 flex flex-col items-center justify-center relative">
                  <div class="relative w-28 h-28 flex items-center justify-center">
                    <img id="guest-qr-image" src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=Initializing" alt="Scan QR" class="w-28 h-28 rounded block">
                    <!-- Expired Overlay -->
                    <div id="qr-expired-overlay" class="absolute inset-0 bg-white/95 backdrop-blur-xs rounded flex flex-col items-center justify-center text-center p-2 hidden">
                      <i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg mb-1"></i>
                      <div class="text-[10px] font-black text-slate-800 leading-tight">Link Expired</div>
                      <div class="text-[8px] text-slate-500 mb-1.5">5m Security Window</div>
                      <button type="button" onclick="regenerateGuestQrSession()" class="text-[9px] bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1 px-2 rounded flex items-center gap-1 shadow-xs cursor-pointer">
                        <i class="fa-solid fa-rotate-right"></i> Refresh QR
                      </button>
                    </div>
                  </div>

                  <div class="text-[9px] font-bold text-slate-500 mt-2 flex items-center gap-1 text-center">
                    <i class="fa-solid fa-camera text-indigo-600"></i> Scan to open mobile camera & upload KYC
                  </div>
                </div>

                <!-- Actions: Copy Link & Regenerate QR -->
                <div class="flex items-center gap-1.5 mt-2">
                  <button type="button" onclick="copyGuestUploadLink()" class="flex-1 py-1 px-2 text-[10px] font-bold bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-lg flex items-center justify-center gap-1 transition-all cursor-pointer">
                    <i class="fa-regular fa-copy"></i> Copy Link
                  </button>
                  <button type="button" onclick="regenerateGuestQrSession()" class="py-1 px-2 text-[10px] font-bold bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 rounded-lg flex items-center justify-center gap-1 transition-all cursor-pointer" title="Generate New QR">
                    <i class="fa-solid fa-rotate"></i> Regenerate
                  </button>
                </div>
              </div>

              <!-- Uploaded KYC Documents Preview Gallery in Desktop Modal -->
              <div id="guest-uploaded-docs-section" class="border border-emerald-200 rounded-xl p-3 bg-emerald-50/40 hidden">
                <div class="flex items-center justify-between mb-2">
                  <span class="text-[11px] font-black text-emerald-800 uppercase tracking-wide flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Uploaded Documents (<span id="uploaded-docs-count">0</span>)
                  </span>
                  <span class="text-[9px] text-emerald-700 font-extrabold bg-emerald-100 px-2 py-0.5 rounded-full">Auto-Attached</span>
                </div>
                <div id="guest-uploaded-docs-grid" class="grid grid-cols-3 gap-2">
                  <!-- Real-time uploaded thumbnail images appear here -->
                </div>
              </div>

              <!-- Hidden token and uploaded documents JSON storage for submission -->
              <input type="hidden" name="guest_upload_token" id="reserve-guest-upload-token" value="">
              <input type="hidden" name="uploaded_documents" id="reserve-uploaded-documents" value="[]">

              <!-- Instant Dispatch Notice -->
              <div class="bg-indigo-50/60 border border-dashed border-indigo-200 rounded-lg p-2.5 text-[10px] text-slate-600 flex gap-2 items-start leading-snug">
                <i class="fa-solid fa-paper-plane text-indigo-500 mt-0.5 text-[10px]"></i>
                <span>Passes sent to WhatsApp & SMS automatically after booking.</span>
              </div>
              
              <!-- Payment & Advance Collection Section -->
              <div class="border-t border-slate-100 pt-3 flex flex-col gap-3">
                
                <!-- Plan Searchable Dropdown -->
                <div>
                  <label class="flex items-center justify-between text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">
                    <span><i class="fa-solid fa-list-check text-indigo-500 mr-1"></i> Meal Plan</span>
                    <span class="text-[9px] font-bold text-indigo-600 normal-case"><i class="fa-solid fa-magnifying-glass"></i> Searchable</span>
                  </label>
                  <select name="plan_id" id="plan-id-select" onchange="calculateReservationBilling()" class="pms-input-field w-full font-bold">
                    <option value="" data-price="0">-- Select Meal Plan --</option>
                    @foreach($plans ?? \App\Models\Plan::where('status', 'Active')->get() as $p)
                      <option value="{{ $p->id }}" data-price="{{ (float)($p->price ?? 0) }}" data-name="{{ $p->plan_name }}">{{ $p->plan_name }}{{ (float)($p->price ?? 0) > 0 ? ' (₹' . number_format($p->price, 2) . ')' : '' }}</option>
                    @endforeach
                  </select>
                </div>

                <div>
                  <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Payment Type</label>
                  <select name="payment_mode_id" id="payment-type-select" onchange="togglePaymentFields(this)" class="pms-input-field w-full font-bold">
                    <option value="None" selected>None</option>
                    @foreach($paymentModes ?? \App\Models\PaymentMode::where('status', 'Active')->get() as $pm)
                      <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Dynamic Calculation & Billing Breakdown Panel -->
                <div id="payment-details-container" class="flex flex-col gap-2">
                  
                  <!-- Total Gross Tariff Card -->
                  <div class="bg-slate-50 border border-slate-200 rounded-lg p-2.5">
                    <div class="flex justify-between items-center mb-0.5 text-[9px] font-black text-slate-500 uppercase tracking-wide">
                      <span>Room Tariff</span>
                      <span id="calc-nights-rate-label" class="font-mono text-slate-600">₹ 4,500 x 1 Night</span>
                    </div>
                    <div class="flex justify-between items-baseline">
                      <span class="text-xs font-bold text-slate-800">Total Room Tariff:</span>
                      <span id="calc-gross-display" class="text-sm font-black text-slate-900 font-mono">₹ 4,500.00</span>
                    </div>
                    <input type="hidden" name="total_amount" id="calc-total-amount" value="4500">
                  </div>

                  <!-- Meal Plan Price Card -->
                  <div id="calc-meal-plan-card" class="hidden bg-amber-50/70 border border-amber-200 rounded-lg p-2.5 transition-all">
                    <div class="flex justify-between items-center mb-0.5 text-[9px] font-black text-amber-800 uppercase tracking-wide">
                      <span class="flex items-center gap-1"><i class="fa-solid fa-utensils text-amber-600"></i> <span id="calc-meal-plan-name">Meal Plan</span></span>
                      <span id="calc-meal-plan-rate-label" class="font-mono text-amber-700">₹ 0.00 x 1 Night</span>
                    </div>
                    <div class="flex justify-between items-baseline">
                      <span class="text-xs font-bold text-amber-900">Meal Plan Total:</span>
                      <span id="calc-meal-plan-display" class="text-xs font-black text-amber-800 font-mono">+ ₹ 0.00</span>
                    </div>
                    <input type="hidden" name="meal_plan_amount" id="calc-meal-plan-amount" value="0">
                  </div>

                  <!-- Combined Gross -->
                  <div id="calc-combined-gross-card" class="hidden bg-slate-100 border border-slate-200 rounded-lg p-2">
                    <div class="flex justify-between items-baseline">
                      <span class="text-[10px] font-black text-slate-600 uppercase tracking-wider">Gross Total (Room + Meals):</span>
                      <span id="calc-combined-gross-display" class="text-xs font-black text-slate-900 font-mono">₹ 4,500.00</span>
                    </div>
                  </div>

                  <!-- Discount Configuration Panel -->
                  <div class="bg-white border border-slate-200 rounded-lg p-2.5">
                    <div class="flex justify-between items-center mb-2">
                      <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider m-0">
                        <i class="fa-solid fa-tags text-indigo-500 mr-1"></i> Discount
                      </label>
                      <span id="calc-discount-val-label" class="text-xs font-black text-emerald-700 font-mono">- ₹ 0.00</span>
                    </div>

                    <!-- Discount Type Radio Selector -->
                    <div class="flex gap-2 mb-2">
                      <label id="discount-type-pct-label" class="flex-1 flex items-center justify-center gap-1.5 p-1.5 bg-indigo-50 border border-indigo-400 rounded-md text-[11px] font-extrabold text-indigo-700 cursor-pointer select-none">
                        <input type="radio" name="discount_type" value="percentage" checked onchange="onDiscountTypeChange(this.value)" class="accent-indigo-600 m-0 w-3.5 h-3.5">
                        <span>%</span>
                      </label>
                      <label id="discount-type-amt-label" class="flex-1 flex items-center justify-center gap-1.5 p-1.5 bg-white border border-slate-300 rounded-md text-[11px] font-bold text-slate-600 cursor-pointer select-none">
                        <input type="radio" name="discount_type" value="amount" onchange="onDiscountTypeChange(this.value)" class="accent-indigo-600 m-0 w-3.5 h-3.5">
                        <span>₹</span>
                      </label>
                    </div>

                    <!-- Percentage Input Box -->
                    <div id="disc-percentage-input-wrapper">
                      <div class="relative">
                        <input type="number" name="discount_percentage" id="calc-discount-percentage" oninput="calculateReservationBilling()" min="0" max="100" step="any" value="0" class="pms-input-field w-full font-mono pr-7" placeholder="Discount % (e.g. 10)">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 pointer-events-none">%</span>
                      </div>
                    </div>

                    <!-- Amount Input Box -->
                    <div id="disc-amount-input-wrapper" class="hidden">
                      <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-xs font-black text-slate-400 pointer-events-none">₹</span>
                        <input type="number" name="discount_amount" id="calc-discount-amount" oninput="calculateReservationBilling()" min="0" step="any" value="0" class="pms-input-field w-full font-mono pl-6" placeholder="Amount in ₹ (e.g. 500)">
                      </div>
                    </div>

                    <!-- Corporate Pre-fill Notice Note -->
                    <div id="company-discount-applied-text" class="hidden text-[10px] font-bold text-emerald-700 mt-1.5 items-center gap-1">
                      <i class="fa-solid fa-building-circle-check"></i> <span id="company-discount-note">Corporate discount auto-applied</span>
                    </div>
                  </div>

                  <!-- GST / Tax Configuration Panel -->
                  <div class="bg-white border border-slate-200 rounded-lg p-2.5">
                    <div class="flex justify-between items-center mb-2">
                      <label class="block text-[10px] font-black text-slate-600 uppercase tracking-wider m-0">
                        <i class="fa-solid fa-receipt text-indigo-500 mr-1"></i> GST / Tax
                      </label>
                      <span id="calc-gst-val-label" class="text-xs font-black text-indigo-700 font-mono">+ ₹ 0.00 (0%)</span>
                    </div>

                    <!-- GST Type Radio Selector -->
                    <div class="flex gap-2 mb-2">
                      <label id="gst-type-inc-label" class="flex-1 flex items-center justify-center gap-1.5 p-1.5 bg-white border border-slate-300 rounded-md text-[11px] font-bold text-slate-600 cursor-pointer select-none">
                        <input type="radio" name="gst_type" value="inclusive" onchange="onGstTypeChange(this.value)" class="accent-indigo-600 m-0 w-3.5 h-3.5">
                        <span>Inc. GST</span>
                      </label>
                      <label id="gst-type-excl-label" class="flex-1 flex items-center justify-center gap-1.5 p-1.5 bg-indigo-50 border border-indigo-400 rounded-md text-[11px] font-extrabold text-indigo-700 cursor-pointer select-none">
                        <input type="radio" name="gst_type" value="exclusive" checked onchange="onGstTypeChange(this.value)" class="accent-indigo-600 m-0 w-3.5 h-3.5">
                        <span>Excl. GST</span>
                      </label>
                    </div>

                    @php
                      $defaultRoomGst = (float)\App\Models\AppSetting::get('default_gst_room', 12.00);
                    @endphp
                    <div>
                      <select name="gst_percentage_id" id="calc-gst-rate" onchange="calculateReservationBilling()" class="pms-input-field w-full font-mono font-bold cursor-pointer">
                        <option value="" data-rate="0" {{ $defaultRoomGst == 0 ? 'selected' : '' }}>0% (No GST / Exempted)</option>
                        @foreach($gstPercentages ?? \App\Models\GstPercentage::where('status', 'Active')->orderBy('gst_percentage', 'asc')->get() as $gst)
                          @if((float)$gst->gst_percentage > 0)
                            <option value="{{ $gst->id }}" data-rate="{{ (float)$gst->gst_percentage }}" {{ (float)$gst->gst_percentage == $defaultRoomGst ? 'selected' : '' }}>
                              {{ rtrim(rtrim(number_format($gst->gst_percentage, 2), '0'), '.') }}% GST
                            </option>
                          @endif
                        @endforeach
                      </select>
                    </div>

                    <input type="hidden" name="gst_percentage" id="calc-gst-percentage" value="0">
                    <input type="hidden" name="gst_amount" id="calc-gst-amount" value="0">
                  </div>

                  <!-- Net Payable Amount -->
                  <div class="bg-linear-to-r from-indigo-50 to-emerald-50 border border-indigo-200 rounded-lg p-2.5 flex justify-between items-center">
                    <span class="text-[10px] font-black text-indigo-700 uppercase tracking-wider">Payable Amount:</span>
                    <span id="calc-payable-display" class="text-sm font-black text-indigo-800 font-mono">₹ 4,500.00</span>
                    <input type="hidden" name="payable_amount" id="calc-payable-amount" value="4500">
                  </div>

                  <!-- Advance Amount Input -->
                  <div>
                    <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Advance Amount (₹)</label>
                    <input type="number" name="advance_amount" id="payment-advance-amount" oninput="calculateReservationBilling()" min="0" step="any" class="pms-input-field w-full font-mono font-bold" placeholder="0.00">
                  </div>

                  <!-- Remaining / Balance Amount Display -->
                  <div id="calc-balance-box" class="bg-rose-50 border border-rose-200 rounded-lg p-2.5 flex justify-between items-center">
                    <span class="text-[10px] font-black text-rose-700 uppercase tracking-wider">Remaining Amount:</span>
                    <span id="calc-balance-display" class="text-sm font-black text-rose-800 font-mono">₹ 4,500.00</span>
                    <input type="hidden" name="balance" id="calc-balance-amount" value="4500">
                  </div>

                  <!-- Remarks -->
                  <div>
                    <label class="block text-[10px] font-black mb-1 text-slate-500 uppercase tracking-wider">Remarks</label>
                    <input type="text" name="payment_remarks" id="payment-remarks" class="pms-input-field w-full" placeholder="Transaction ID, UPI Ref, or Notes">
                  </div>

                </div>

              </div>
              
              <div class="w-full mt-auto pt-2 flex flex-col gap-2">
                <button type="submit" id="reserve-submit-btn" class="btn-ui-success w-full h-11 justify-center text-xs font-black rounded-xl shadow-md">
                  <i class="fa-solid fa-key"></i> Complete Check-In
                </button>
                <button type="button" class="btn-ui-secondary w-full h-9 justify-center text-xs font-bold rounded-xl" onclick="closeModal('reserve-modal')">
                  Cancel
                </button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="print-area" class="hidden"></div>

  <!-- SweetAlert2 and Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const PmsAlert = {
      toast: function(title, icon = 'success') {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: icon,
          title: title,
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true
        });
      }
    };
  </script>

  @include('frontoffice.includes.script')
  @stack('scripts')

</body>
</html>
