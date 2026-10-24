<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Front Office - Hotel Sagar Sonnet CRM / PMS')</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <style>
    /* Responsive Viewport & Page Container Safety */
    html, body {
      max-width: 100vw !important;
      overflow-x: hidden !important;
    }
    .pms-main-viewport {
      min-width: 0 !important;
      max-width: 100vw !important;
      width: 100% !important;
      box-sizing: border-box !important;
      overflow-x: hidden !important;
    }
    .pms-page-container {
      min-width: 0 !important;
      max-width: calc(100% - 20px) !important;
      width: calc(100% - 20px) !important;
      box-sizing: border-box !important;
      margin: 10px 10px 24px 10px !important;
    }
    .pms-page-content {
      min-width: 0 !important;
      max-width: 100% !important;
      width: 100% !important;
      box-sizing: border-box !important;
    }
    .pms-table-box {
      width: 100% !important;
      max-width: 100% !important;
      overflow-x: auto !important;
      -webkit-overflow-scrolling: touch !important;
      box-sizing: border-box !important;
    }

    /* Room Card Status Badge & Popover */
    .rack-badge {
      cursor: pointer !important;
      gap: 4px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .rack-badge:hover {
      filter: brightness(0.92) !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12) !important;
    }
    .rack-badge-arrow {
      font-size: 8px !important;
      opacity: 0.65 !important;
      transition: transform 0.2s ease !important;
    }
    .rack-card.is-popover-active {
      box-shadow: 0 0 0 2.5px #6366f1, 0 12px 30px rgba(99, 102, 241, 0.25) !important;
      transform: translateY(-3px) scale(1.02) !important;
      z-index: 100 !important;
    }
    .rack-card.is-popover-active .rack-badge-arrow {
      transform: rotate(180deg) !important;
    }

    /* Fixed Floating Popover */
    .room-status-popover {
      position: fixed !important;
      z-index: 999999 !important;
      width: 285px !important;
      background: #ffffff !important;
      border: 1px solid #cbd5e1 !important;
      border-radius: 14px !important;
      box-shadow: 0 20px 45px -8px rgba(15, 23, 42, 0.32), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
      overflow: hidden !important;
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
      animation: rspPopIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    @keyframes rspPopIn {
      from { opacity: 0; transform: scale(0.92) translateY(-6px); }
      to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .rsp-header {
      background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
      color: #ffffff !important;
      padding: 10px 14px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25) !important;
    }
    .rsp-title {
      display: flex !important;
      align-items: center !important;
      gap: 7px !important;
      font-size: 12px !important;
      font-weight: 700 !important;
      color: #ffffff !important;
    }
    .rsp-title strong {
      font-family: var(--font-mono, monospace) !important;
      font-size: 14px !important;
      letter-spacing: 0.5px !important;
    }
    .rsp-room-type {
      font-size: 9px !important;
      font-weight: 800 !important;
      background: rgba(255, 255, 255, 0.22) !important;
      padding: 2px 7px !important;
      border-radius: 5px !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px !important;
    }
    .rsp-close {
      background: rgba(255, 255, 255, 0.2) !important;
      border: none !important;
      color: #ffffff !important;
      width: 22px !important;
      height: 22px !important;
      border-radius: 50% !important;
      font-size: 14px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      cursor: pointer !important;
      transition: background 0.15s !important;
    }
    .rsp-close:hover {
      background: rgba(255, 255, 255, 0.4) !important;
    }
    .rsp-body {
      padding: 12px 14px !important;
      display: flex !important;
      flex-direction: column !important;
      gap: 10px !important;
      background: #ffffff !important;
    }
    .rsp-field {
      display: flex !important;
      flex-direction: column !important;
      gap: 4px !important;
    }
    .rsp-label {
      font-size: 10px !important;
      font-weight: 800 !important;
      color: #64748b !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px !important;
      display: flex !important;
      align-items: center !important;
      gap: 5px !important;
      margin: 0 !important;
    }
    .rsp-select {
      width: 100% !important;
      height: 34px !important;
      padding: 4px 10px !important;
      font-size: 12px !important;
      font-weight: 700 !important;
      color: #0f172a !important;
      background: #f8fafc !important;
      border: 1.5px solid #cbd5e1 !important;
      border-radius: 7px !important;
      cursor: pointer !important;
      outline: none !important;
      transition: all 0.15s ease !important;
    }
    .rsp-select:focus {
      border-color: #6366f1 !important;
      background: #ffffff !important;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
    }
    .rsp-quick-section {
      border-top: 1px dashed #e2e8f0 !important;
      padding-top: 8px !important;
      margin-top: 2px !important;
    }
    .rsp-quick-title {
      font-size: 9px !important;
      font-weight: 800 !important;
      color: #94a3b8 !important;
      text-transform: uppercase !important;
      letter-spacing: 0.6px !important;
      margin-bottom: 6px !important;
    }
    .rsp-quick-chips {
      display: grid !important;
      grid-template-columns: 1fr 1fr !important;
      gap: 6px !important;
    }
    .rsp-chip {
      height: 28px !important;
      border-radius: 6px !important;
      font-size: 11px !important;
      font-weight: 800 !important;
      border: 1px solid transparent !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 5px !important;
      cursor: pointer !important;
      transition: all 0.15s ease !important;
    }
    .rsp-chip.chip-clean {
      background: #ecfdf5 !important;
      color: #059669 !important;
      border-color: #a7f3d0 !important;
    }
    .rsp-chip.chip-clean:hover {
      background: #059669 !important;
      color: #ffffff !important;
    }
    .rsp-chip.chip-dirty {
      background: #fffbeb !important;
      color: #d97706 !important;
      border-color: #fde68a !important;
    }
    .rsp-chip.chip-dirty:hover {
      background: #d97706 !important;
      color: #ffffff !important;
    }
    .rsp-chip.chip-maint {
      background: #eff6ff !important;
      color: #2563eb !important;
      border-color: #bfdbfe !important;
    }
    .rsp-chip.chip-maint:hover {
      background: #2563eb !important;
      color: #ffffff !important;
    }
    .rsp-chip.chip-block {
      background: #fff1f2 !important;
      color: #e11d48 !important;
      border-color: #fecdd3 !important;
    }
    .rsp-chip.chip-block:hover {
      background: #e11d48 !important;
      color: #ffffff !important;
    }
    .rsp-footer {
      padding: 7px 14px !important;
      background: #f8fafc !important;
      border-top: 1px solid #f1f5f9 !important;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      font-size: 9.5px !important;
      color: #94a3b8 !important;
      font-weight: 600 !important;
    }
    .rsp-spinner {
      color: #6366f1 !important;
      font-weight: 800 !important;
    }

    .legend-chip.active-chip {
      outline: 2px solid var(--accent-primary, #6366f1) !important;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    body.swal2-shown, html.swal2-shown {
      height: 100vh !important;
      overflow: hidden !important;
      padding-right: 0 !important;
    }
    body.swal2-height-auto, html.swal2-height-auto {
      height: 100vh !important;
    }
    /* Select2 Luxury Theme Enhancements for Modal */
    .modal-window .select2-container {
      width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
      height: 38px !important;
      padding: 4px 12px !important;
      border: 1px solid #cbd5e1 !important;
      border-radius: 8px !important;
      background: #ffffff !important;
      font-size: 12px !important;
      font-weight: 700 !important;
      color: #1e293b !important;
      display: flex !important;
      align-items: center !important;
      box-sizing: border-box !important;
      transition: all 0.2s ease !important;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
      border-color: #6366f1 !important;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
      outline: none !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 36px !important;
      right: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      padding-left: 0 !important;
      color: #1e293b !important;
      line-height: normal !important;
      font-weight: 700 !important;
    }
    .select2-dropdown {
      border: 1px solid #cbd5e1 !important;
      border-radius: 8px !important;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
      z-index: 999999 !important;
      background: #ffffff !important;
      overflow: hidden !important;
    }
    .select2-search--dropdown {
      padding: 8px 10px !important;
      background: #f8fafc !important;
      border-bottom: 1px solid #e2e8f0 !important;
    }
    .select2-search--dropdown .select2-search__field {
      height: 34px !important;
      padding: 6px 12px !important;
      border-radius: 6px !important;
      border: 1px solid #cbd5e1 !important;
      font-size: 12px !important;
      background: #ffffff !important;
      outline: none !important;
    }
    .select2-results__option {
      padding: 8px 14px !important;
      font-size: 12px !important;
      color: #334155 !important;
      font-weight: 600 !important;
      transition: background 0.15s ease !important;
    }
    .select2-results__option--highlighted[aria-selected] {
      background: rgba(99, 102, 241, 0.1) !important;
      color: #6366f1 !important;
      font-weight: 700 !important;
    }
    .select2-results__option[aria-selected=true] {
      background: #6366f1 !important;
      color: #ffffff !important;
    }
  </style>
  @stack('styles')
</head>
<body>

  <!-- Top System Header Bar -->
  <header class="pms-app-header">
    <div class="pms-header-brand">
      <i class="fa-solid fa-hotel gold-star"></i>
      <span>HOTEL SAGAR SONNET</span>
      <span style="background: #fbc531; color: #1e293b; font-size: 9px; padding: 2px 5px; border-radius: 3px; font-weight: 800;">PMS 2026</span>
    </div>
    <div class="pms-header-center">
      <span><i class="fa-regular fa-calendar-days"></i> {{ isset($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format('l, d M Y') : date('l, d M Y') }} @if(isset($isToday) && $isToday)<strong style="color: #10b981; font-size: 10px; margin-left: 4px; text-transform: uppercase;">(Today)</strong>@endif</span>
      <span><i class="fa-solid {{ $shiftIcon ?? 'fa-sun' }}" style="color: {{ $shiftColor ?? '#fbc531' }};"></i> {{ $currentShift ?? 'Morning Shift (07:00 - 15:00)' }}</span>
      <span><i class="fa-solid fa-location-dot" style="color: #22d3ee;"></i> Main Luxury Wing</span>
    </div>
    <div class="pms-header-right">
      <div class="pms-user-tag">
        <i class="fa-solid fa-user-tie"></i>
        <span>Debashis Roy (Front Desk)</span>
      </div>
      <a href="{{ route('admin.login') }}" class="btn-pms-logout" style="text-decoration: none;"><i class="fa-solid fa-power-off"></i> Logout</a>
    </div>
  </header>

  <!-- Top Module Navigation Tabs -->
  <nav class="pms-tabs-ribbon">
    <a href="{{ route('frontoffice.dashboard') }}" class="pms-tab-link {{ !request()->routeIs('frontoffice.services*') && !request()->routeIs('frontoffice.housekeeping*') && (request()->routeIs('frontoffice.*') || request()->is('front-office*')) ? 'active' : '' }}">
      <i class="fa-solid fa-desktop tab-ico" style="color: #6366f1;"></i> Front Office
    </a>
    <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('POS Sales module queued.', 'info')">
      <i class="fa-solid fa-utensils tab-ico" style="color: #10b981;"></i> POS Sales
    </a>
    <a href="{{ route('frontoffice.housekeeping') }}" class="pms-tab-link {{ request()->routeIs('frontoffice.housekeeping*') ? 'active' : '' }}">
      <i class="fa-solid fa-broom tab-ico" style="color: #f59e0b;"></i> House Keeping
    </a>
    <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Stores module queued.', 'info')">
      <i class="fa-solid fa-boxes-stacked tab-ico" style="color: #22d3ee;"></i> Stores
    </a>
    <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Accounts module queued.', 'info')">
      <i class="fa-solid fa-file-invoice-dollar tab-ico" style="color: #a78bfa;"></i> Accounts
    </a>
    <a href="{{ route('frontoffice.services.index') }}" class="pms-tab-link {{ request()->routeIs('frontoffice.services.*') ? 'active' : '' }}">
      <i class="fa-solid fa-bell-concierge tab-ico" style="color: #10b981;"></i> Extra Services
    </a>
    <a href="{{ route('banquet.index') }}" class="pms-tab-link {{ request()->routeIs('banquet.*') ? 'active' : '' }}">
      <i class="fa-solid fa-champagne-glasses tab-ico" style="color: #fbbf24;"></i> Banquet
    </a>
    <a href="{{ route('admin.dashboard') }}" class="pms-tab-link">
      <i class="fa-solid fa-user-gear tab-ico" style="color: #94a3b8;"></i> Administrator
    </a>
  </nav>

  <!-- Subheader Component -->
  @include('frontoffice.includes.subheader')

  <!-- Main Content Viewport -->
  @yield('content')

  <!-- ROOM ACTION MODAL -->
  <div class="modal-backdrop" id="room-modal">
    <div class="modal-window large" style="width: 820px; max-width: 95vw; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3); border: none;">
      <div class="modal-top" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.15);">
        <div id="m-title" style="margin: 0; font-size: 15px; font-weight: 800; display: flex; align-items: center; gap: 10px; color: #ffffff; flex: 1; min-width: 0;">
          <i class="fa-solid fa-door-open"></i> Room Details
        </div>
        <button type="button" class="modal-close" onclick="closeModal('room-modal')" style="background: rgba(255,255,255,0.2); border: none; color: #ffffff; width: 30px; height: 30px; border-radius: 50%; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s; margin-left: 10px; flex-shrink: 0;">&times;</button>
      </div>
      <div class="modal-content-area" id="m-body" style="padding: 18px 20px; background: #f8fafc; max-height: calc(90vh - 65px); overflow-y: auto;"></div>
      <div class="modal-bot" id="m-footer" style="padding: 12px 20px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 8px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('room-modal')">Close</button>
      </div>
    </div>
  </div>

  <!-- ROOM STATUS QUICK POPOVER (HOUSEKEEPING & OPERATIONAL) -->
  <div id="room-card-status-popover" class="room-status-popover" style="position: fixed; display: none; z-index: 999999; width: 285px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 14px; box-shadow: 0 20px 45px -8px rgba(15, 23, 42, 0.32), 0 0 0 1px rgba(0, 0, 0, 0.05); overflow: hidden;" onclick="event.stopPropagation()">
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
          <i class="fa-solid fa-broom" style="color: #f59e0b;"></i> Housekeeping Status
        </label>
        <select id="rsp-hk-select" class="rsp-select" onchange="onRspSelectChanged('hk')">
          <option value="">Select Housekeeping Status</option>
        </select>
      </div>

      <!-- Operational Status -->
      <div class="rsp-field">
        <label class="rsp-label">
          <i class="fa-solid fa-shield-halved" style="color: #6366f1;"></i> Operational Status
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
      <span class="rsp-spinner" id="rsp-spinner" style="display: none;">
        <i class="fa-solid fa-spinner fa-spin"></i> Saving...
      </span>
    </div>
  </div>

  <!-- NEW RESERVATION MODAL -->
  <div class="modal-backdrop" id="reserve-modal">
    <div class="modal-window large" style="width: 1140px; max-width: 96vw; max-height: 94vh; display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3); border: none; box-sizing: border-box;">
      <div class="modal-top" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; padding: 14px 22px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.15);">
        <div style="display: flex; align-items: center; gap: 10px;">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 14px;">
            <i class="fa-solid fa-calendar-plus"></i>
          </div>
          <div>
            <h3 id="reserve-modal-title" style="margin: 0; font-size: 15px; font-weight: 800; letter-spacing: 0.3px; color: #ffffff;">Guest Check-In</h3>
            <div id="reserve-modal-subtitle" style="font-size: 11px; opacity: 0.85; font-weight: 500;">Hotel Sagar Sonnet • Immediate Check-In (Status: <strong>Checked In</strong>)</div>
          </div>
        </div>
        <button type="button" class="modal-close" onclick="closeModal('reserve-modal')" style="background: rgba(255,255,255,0.2); border: none; color: #ffffff; width: 30px; height: 30px; border-radius: 50%; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">&times;</button>
      </div>
      
      <div class="modal-content-area" style="max-height: calc(94vh - 65px); overflow-y: auto; overflow-x: hidden; padding: 18px 22px; background: #f8fafc; box-sizing: border-box;">
        <form id="reservation-form" onsubmit="handleReserve(event)" style="width: 100%; box-sizing: border-box; margin: 0;">
          @csrf
          <!-- Dynamic Registration Type Pills -->
          <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; padding: 6px 10px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); align-items: center;">
            <span style="font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-right: 6px;">Guest Type:</span>
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
              <label class="res-type-pill" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; cursor: pointer; font-size: 12px; padding: 5px 12px; border-radius: 7px; transition: all 0.2s; background: {{ $idx === 0 ? '#eef2ff' : 'transparent' }}; color: {{ $idx === 0 ? '#4f46e5' : '#64748b' }}; border: 1px solid {{ $idx === 0 ? '#c7d2fe' : 'transparent' }};">
                <input type="radio" name="res_type" value="{{ $rt->id }}" data-slug="{{ $slug }}" data-registration-id="{{ $rt->id }}" data-type-name="{{ $rtName }}" {{ $idx === 0 ? 'checked' : '' }} onchange="toggleResType()" style="accent-color: #4f46e5; margin: 0;"> {{ $rtName }}
              </label>
            @empty
              <label class="res-type-pill" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 700; cursor: pointer; font-size: 12px; padding: 5px 12px; border-radius: 7px; background: #eef2ff; color: #4f46e5; border: 1px solid #c7d2fe;">
                <input type="radio" name="res_type" value="8" data-slug="new" data-registration-id="8" checked onchange="toggleResType()" style="accent-color: #4f46e5; margin: 0;"> New
              </label>
            @endforelse
          </div>

          <!-- Main 2-Column Responsive Grid -->
          <div style="display: grid; grid-template-columns: minmax(0, 1fr) 320px; gap: 18px; width: 100%; box-sizing: border-box; align-items: start;">
            
            <!-- Left Column: Form Fields -->
            <div style="min-width: 0; width: 100%; box-sizing: border-box;">
              
              <!-- Reservation Meta Fields Card -->
              <div id="section-reservation-details" style="display: flex; flex-direction: column; background: #ffffff; padding: 16px 18px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 16px; box-sizing: border-box;">
                
                <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px; margin-bottom: 12px;">
                  <div style="min-width: 0;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Reserve ID</label>
                    <input type="text" id="reserve-id-input" name="reserve_id" class="pms-input-field" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #f8fafc; color: #4f46e5; font-weight: 800; font-family: monospace; box-sizing: border-box;" value="{{ $nextReserveId ?? \App\Models\Guest::generateNextReserveId() }}" readonly>
                  </div>
                  <div style="min-width: 0;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Mode of Reserve</label>
                    <select name="reservation_mode_id" class="pms-input-field" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 600; box-sizing: border-box;">
                      @foreach($reservationModes ?? \App\Models\ReservationMode::where('status', 'Active')->get() as $rm)
                        <option value="{{ $rm->id }}">{{ $rm->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <!-- Stay Schedule & Duration Box -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; box-sizing: border-box;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 11px; font-weight: 800; color: #334155; text-transform: uppercase; letter-spacing: 0.6px; display: flex; align-items: center; gap: 6px;">
                      <i class="fa-solid fa-calendar-days" style="color: #6366f1;"></i> Stay Schedule & Duration
                    </span>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <span id="reservation-status-badge" style="font-size: 11px; font-weight: 800; background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 2px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px; transition: all 0.2s;">
                        <i class="fa-solid fa-key"></i> Status: Checked In
                      </span>
                      <span id="stay-nights-badge" style="font-size: 11px; font-weight: 800; background: #e0e7ff; color: #4338ca; padding: 2px 10px; border-radius: 20px; border: 1px solid #c7d2fe; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-moon"></i> 1 Night Stay
                      </span>
                    </div>
                  </div>

                  <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px;">
                    @php
                      $activeCheckInDate = $selectedDate ?? ($fromDate ?? date('Y-m-d'));
                      $activeCheckOutDate = \Carbon\Carbon::parse($activeCheckInDate)->addDay()->toDateString();
                    @endphp
                    <!-- Check-In -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; box-sizing: border-box; min-width: 0;">
                      <label style="display: flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 800; color: #4f46e5; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-plane-arrival"></i> Check-in (Arrival)
                      </label>
                      <div style="display: flex; gap: 6px; align-items: center;">
                        <input type="date" id="reserve-date-input" name="reserve_date" value="{{ $activeCheckInDate }}" min="{{ $activeCheckInDate }}" class="pms-input-field" onchange="onCheckInDateChange()" style="flex: 3; min-width: 0; width: 100%; height: 36px; padding: 4px 8px; font-size: 12px; font-weight: 700; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; box-sizing: border-box;">
                        <input type="time" id="reserve-time-input" name="reserve_time" class="pms-input-field" style="flex: 2; min-width: 0; width: 100%; height: 36px; padding: 4px 6px; font-size: 12px; font-weight: 700; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; box-sizing: border-box;">
                      </div>
                    </div>

                    <!-- Check-Out -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; box-sizing: border-box; min-width: 0;">
                      <label style="display: flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 800; color: #059669; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-plane-departure"></i> Check-out (Departure)
                      </label>
                      <div style="display: flex; gap: 6px; align-items: center;">
                        <input type="date" id="checkout-date-input" name="checkout_date" value="{{ $activeCheckOutDate }}" min="{{ $activeCheckInDate }}" class="pms-input-field" onchange="onCheckOutDateChange()" style="flex: 3; min-width: 0; width: 100%; height: 36px; padding: 4px 8px; font-size: 12px; font-weight: 700; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; box-sizing: border-box;">
                        <input type="time" id="checkout-time-input" name="checkout_time" class="pms-input-field" style="flex: 2; min-width: 0; width: 100%; height: 36px; padding: 4px 6px; font-size: 12px; font-weight: 700; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; box-sizing: border-box;" value="11:00">
                      </div>
                    </div>
                  </div>

                  <!-- Date Selection Conflict Alert (Shown in RED right below Check-in & Check-out) -->
                  <div id="dates-conflict-alert" style="display: none; margin-top: 10px; background: #fef2f2; border: 1.5px solid #ef4444; border-radius: 8px; padding: 9px 12px; box-shadow: 0 2px 8px rgba(239, 68, 68, 0.12);">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                      <div style="display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1;">
                        <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626; font-size: 15px; flex-shrink: 0;"></i>
                        <span id="dates-conflict-text" style="font-size: 11px; font-weight: 800; color: #b91c1c; line-height: 1.4;">
                          <!-- Dynamic text -->
                        </span>
                      </div>
                      <span style="font-size: 9px; font-weight: 800; background: #ef4444; color: #ffffff; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; white-space: nowrap; flex-shrink: 0; letter-spacing: 0.5px;">
                        Dates Conflict
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Company Specific Fields -->
                <div id="section-company-fields" style="display: none; padding: 14px 16px; background: rgba(16, 185, 129, 0.05); border: 1px dashed #10b981; border-radius: 10px; margin-top: 14px; box-sizing: border-box;">
                  <div style="margin-bottom: 12px;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #059669; text-transform: uppercase; letter-spacing: 0.8px;"><i class="fa-solid fa-building"></i> Company Name</label>
                    <select name="company_id" id="company-select" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #10b981; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 700; box-sizing: border-box;" onchange="toggleNewCompany(this)">
                      <option value="">-- Select Corporate Account --</option>
                      @foreach($companies ?? \App\Models\Company::where('status', 'Active')->get() as $cmp)
                        <option value="{{ $cmp->id }}" data-discount-type="{{ $cmp->discount_type ?? 'percentage' }}" data-discount-value="{{ (float)($cmp->discount_value ?? 0) }}">{{ $cmp->name }}</option>
                      @endforeach
                      <option value="new" style="color: #4f46e5; font-weight: 900;">+ Register New Company</option>
                    </select>
                  </div>
                  <div id="new-company-details" style="display: none; border-top: 1px solid rgba(16, 185, 129, 0.2); padding-top: 12px;">
                    <div style="margin-bottom: 12px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">New Company Name</label>
                      <input type="text" name="new_company_name" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;" placeholder="Full Corporate Entity Name">
                    </div>
                    <div style="margin-bottom: 12px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Company Address</label>
                      <input type="text" name="new_company_address" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;" placeholder="Billing Address">
                    </div>
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px; margin-bottom: 12px;">
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">GSTIN</label>
                        <input type="text" name="new_company_gstin" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; font-family: monospace; box-sizing: border-box;" placeholder="29ABCDE1234F1Z5">
                      </div>
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Corporate Phone</label>
                        <input type="text" name="new_company_phone" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;" placeholder="Office / Mobile">
                      </div>
                    </div>
                    <!-- New Company Corporate Discount Configuration -->
                    <div style="padding: 10px 12px; background: #ffffff; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: #059669; text-transform: uppercase; letter-spacing: 0.8px;">Corporate Discount Rate</label>
                      <div style="display: flex; gap: 18px; align-items: center; margin-bottom: 8px;">
                        <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #1e293b; cursor: pointer;">
                          <input type="radio" name="new_company_discount_type" value="percentage" checked onchange="toggleNewCompanyDiscountType(this.value)" style="accent-color: #059669; width: 15px; height: 15px;">
                          <span>Percentage (%)</span>
                        </label>
                        <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #1e293b; cursor: pointer;">
                          <input type="radio" name="new_company_discount_type" value="amount" onchange="toggleNewCompanyDiscountType(this.value)" style="accent-color: #059669; width: 15px; height: 15px;">
                          <span>Amount (₹)</span>
                        </label>
                      </div>
                      <div id="new-company-disc-pct-wrap">
                        <input type="number" name="new_company_discount_pct" id="new-comp-disc-pct" oninput="calculateReservationBilling()" min="0" max="100" step="any" style="width:100%; height:36px; padding:6px 12px; font-size:12px; border:1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;" placeholder="Discount Percentage % (e.g. 10)">
                      </div>
                      <div id="new-company-disc-amt-wrap" style="display: none;">
                        <input type="number" name="new_company_discount_amt" id="new-comp-disc-amt" oninput="calculateReservationBilling()" min="0" step="any" style="width:100%; height:36px; padding:6px 12px; font-size:12px; border:1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;" placeholder="Amount in ₹ (e.g. 500)">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Guests Container -->
              <div id="guests-container">
                <div class="guest-block" style="margin-bottom: 16px;">
                  <!-- Guest Details Card -->
                  <div class="section-guest-details" style="display: flex; flex-direction: column; background: #ffffff; padding: 18px 20px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); box-sizing: border-box;">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 14px;">
                      <h4 class="guest-heading" style="margin: 0; font-size: 13px; color: #4f46e5; font-weight: 800; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-user"></i> Guest #1
                      </h4>
                    </div>

                    <!-- Regular Search Section -->
                    <div class="section-regular-search" style="display: none; margin-bottom: 14px; padding: 12px 14px; background: rgba(99, 102, 241, 0.04); border: 1px dashed #6366f1; border-radius: 8px; box-sizing: border-box;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Search Regular Guest</label>
                      <div style="display: flex; gap: 10px;">
                        <input type="text" class="regular-search-input" style="flex: 1; min-width: 0; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; box-sizing: border-box;" placeholder="Enter Mobile Number">
                        <button type="button" class="btn-ui-primary" style="height: 38px; padding: 0 18px; border-radius: 8px;" onclick="searchRegularGuest(this)"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                      </div>
                    </div>

                    <!-- Row 1: Title + Guest Name -->
                    <div style="display: flex; gap: 12px; margin-bottom: 12px;">
                      <div style="width: 95px; flex-shrink: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Title</label>
                        <select name="title_id" class="pms-select-title" style="width:100%; height:38px; padding:6px 8px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 600; box-sizing: border-box;">
                          @foreach($titles ?? \App\Models\Title::where('status', 'Active')->get() as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div style="flex: 1; min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Guest Name <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="guest_name" class="guest-name-input" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;" placeholder="First and Last Name" required>
                      </div>
                    </div>

                    <!-- Row 2: Address -->
                    <div style="margin-bottom: 12px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Guest Address</label>
                      <input type="text" name="guest_address" class="guest-address-input" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;" placeholder="House No, Street, Landmark">
                    </div>

                    <!-- Row 3: Nationality + City -->
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px; margin-bottom: 12px;">
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Nationality</label>
                        <select name="nationality_id" class="pms-select-nationality" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 600; box-sizing: border-box;">
                          @foreach($nationalities ?? \App\Models\Nationality::where('status', 'Active')->get() as $nat)
                            <option value="{{ $nat->id }}" {{ strtolower($nat->name) === 'indian' ? 'selected' : '' }}>{{ $nat->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">City</label>
                        <input type="text" name="city" class="guest-city-input" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;" placeholder="e.g. Kolkata">
                      </div>
                    </div>

                    <!-- Row 4: Mobile + Email -->
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px; margin-bottom: 12px;">
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Telephone / Mobile <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="mobile" class="guest-mobile-input" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;" placeholder="+91 9876543210" required>
                      </div>
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Email ID</label>
                        <input type="email" name="email" class="guest-email-input" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;" placeholder="guest@email.com">
                      </div>
                    </div>

                    <!-- Privilege Card Row -->
                    <div class="privilege-checkbox-container" style="display: flex; flex-direction: column; align-items: flex-start; gap: 8px; margin-bottom: 14px; padding: 10px 14px; background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 8px; box-sizing: border-box;">
                      <div style="width: 100%;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 12px; cursor: pointer; color: #059669;">
                          <input type="checkbox" class="chk-privilege" onchange="togglePrivilegeInput(this)" style="width: 15px; height: 15px; cursor: pointer; accent-color: #059669; margin: 0;"> Has Privilege Card
                        </label>
                      </div>
                      <div class="privilege-input-container" style="display: none; width: 100%;">
                        <input type="text" name="privilege_card_no" style="width:100%; max-width: 260px; height:34px; padding:4px 10px; font-size:12px; border:1px solid #059669; border-radius: 6px; background: #ffffff; color: #1e293b; font-weight: 600; box-sizing: border-box;" placeholder="Privilege Card No.">
                      </div>
                    </div>

                    <!-- Dynamic Cascading Room Assign: Category -> Floor -> Room No -->
                    <div style="border-top: 1px solid #f1f5f9; padding-top: 12px; margin-top: 4px;">
                      <div class="room-assign-header-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                          <h4 style="font-size: 11px; color: #4f46e5; margin: 0; text-transform: uppercase; font-weight: 800; letter-spacing: 0.8px; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-hotel"></i> Room Assignment
                          </h4>
                          <span class="room-lock-indicator" style="display: none; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #4338ca; background: #e0e7ff; border: 1px solid #c7d2fe; padding: 2px 7px; border-radius: 6px;">
                            <i class="fa-solid fa-lock" style="font-size: 9px;"></i> <span class="room-lock-text">Room Locked</span>
                          </span>
                        </div>
                        <div class="same-room-checkbox-wrapper" style="display: none;">
                          <label style="display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 800; color: #4338ca; cursor: pointer; background: #eef2ff; padding: 3px 8px; border-radius: 6px; border: 1px solid #c7d2fe; user-select: none;">
                            <input type="checkbox" class="chk-same-room" onchange="toggleSameRoom(this)" style="width: 13px; height: 13px; accent-color: #4f46e5; cursor: pointer; margin: 0;">
                            <span>Same Room</span>
                          </label>
                        </div>
                      </div>

                      <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr); gap: 12px; margin-bottom: 12px;">
                        <div style="min-width: 0;">
                          <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Room Category</label>
                          <select name="category_id" class="select-room-category" onchange="onCategoryChange(this)" style="width:100%; height:38px; padding:6px 10px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 600; box-sizing: border-box;">
                            <option value="">Select Category</option>
                            @foreach($categories ?? \App\Models\RoomCategory::where('status', 'Active')->get() as $cat)
                              <option value="{{ $cat->id }}" data-category-name="{{ $cat->name }}">{{ $cat->name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div style="min-width: 0;">
                          <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Floor</label>
                          <select name="floor_id" class="select-room-floor" onchange="onFloorChange(this)" style="width:100%; height:38px; padding:6px 10px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 600; box-sizing: border-box;">
                            <option value="">Select Floor</option>
                            @foreach($floors ?? \App\Models\Floor::where('status', 'Active')->orderBy('floor', 'asc')->get() as $fl)
                              <option value="{{ $fl->id }}" data-floor-no="{{ $fl->floor }}">Floor {{ $fl->floor }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div style="min-width: 0;">
                          <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Room No.</label>
                          <select name="room_id" class="select-room-no" onchange="onRoomChange(this)" style="width:100%; height:38px; padding:6px 10px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 700; box-sizing: border-box;">
                            <option value="">Select Room No.</option>
                            @foreach($rackRooms ?? [] as $rm)
                              <option value="{{ $rm['id'] ?? $rm['room'] }}" data-room-no="{{ $rm['room'] }}" data-rate="{{ $rm['rate'] }}" data-bedding="{{ $rm['bedding'] ?? 'King Size Master (72x78)' }}" data-max-adults="{{ $rm['max_adults'] ?? 2 }}" data-max-children="{{ $rm['max_children'] ?? 1 }}" data-max-pax="{{ $rm['max_pax'] ?? 3 }}" data-plan-id="{{ $rm['plan_id'] ?? '' }}" data-plan-name="{{ $rm['plan_name'] ?? '' }}" data-plan-price="{{ $rm['plan_price'] ?? 0 }}">{{ $rm['room'] }} ({{ $rm['type'] }})</option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <!-- Dynamic Bedding Configuration & Pax Capacity Info Box -->
                      <div class="room-capacity-banner" style="display: none; background: linear-gradient(135deg, rgba(99, 102, 241, 0.05), rgba(16, 185, 129, 0.05)); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 8px; padding: 10px 14px; margin-bottom: 12px; box-sizing: border-box;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                          <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: #6366f1; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                              <i class="fa-solid fa-bed"></i>
                            </div>
                            <div>
                              <div style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Bedding Configuration</div>
                              <div class="bedding-name-text" style="font-size: 12px; font-weight: 800; color: #1e293b;">-</div>
                            </div>
                          </div>
                          
                          <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                            <div style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 800; color: #4f46e5; background: rgba(99, 102, 241, 0.1); padding: 3px 8px; border-radius: 5px;">
                              <i class="fa-solid fa-user-group"></i>
                              <span>Max: <strong class="max-pax-count">3 Pax</strong></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #059669; background: rgba(16, 185, 129, 0.1); padding: 3px 8px; border-radius: 5px;">
                              <i class="fa-solid fa-person"></i>
                              <span>Adults: <strong class="max-adults-count">2</strong></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #d97706; background: rgba(245, 158, 11, 0.1); padding: 3px 8px; border-radius: 5px;">
                              <i class="fa-solid fa-child"></i>
                              <span>Kids: <strong class="max-children-count">1</strong></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 800; color: #059669; background: #ffffff; border: 1px solid #10b981; padding: 3px 8px; border-radius: 5px;">
                              <i class="fa-solid fa-tag"></i>
                              <span class="room-rate-text">₹ 4,500/night</span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Real-Time Room Booking Conflict & Matching Alternatives Alert Box -->
                      <div class="room-conflict-alert-box" style="display: none; margin-bottom: 14px; border-radius: 10px; overflow: hidden; border: 1.5px solid #f87171; background: #fff5f5; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.08);">
                        <!-- Alert Header -->
                        <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;">
                          <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 15px;"></i>
                            <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Room Already Booked / Conflict Detected!</span>
                          </div>
                          <span class="conflict-badge" style="background: rgba(255,255,255,0.25); font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Not Available</span>
                        </div>
                        
                        <!-- Alert Message -->
                        <div style="padding: 12px 14px; color: #991b1b; font-size: 12px; line-height: 1.5;">
                          <div class="conflict-message-text" style="font-weight: 700; margin-bottom: 4px;">
                            <!-- Injected dynamically -->
                          </div>
                          <div class="conflict-sub-text" style="font-size: 11px; color: #b91c1c; opacity: 0.9;">
                            Please choose an alternative room or adjust your check-in / check-out dates.
                          </div>
                        </div>

                        <!-- Alternative Rooms Section -->
                        <div class="conflict-alternatives-section" style="background: #ffffff; border-top: 1px solid #fecaca; padding: 12px 14px;">
                          <div style="font-size: 11px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px; margin-bottom: 10px;">
                            <i class="fa-solid fa-wand-magic-sparkles" style="color: #6366f1;"></i>
                            <span>Available Alternative Rooms Matching Your Stay Schedule:</span>
                          </div>
                          <div class="conflict-alt-rooms-list" style="display: flex; flex-direction: column; gap: 10px; max-height: 240px; overflow-y: auto; padding-right: 4px;">
                            <!-- Injected dynamically grouped floor-wise -->
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Identity Details -->
                    <div style="border-top: 1px solid #f1f5f9; padding-top: 12px;">
                      <h4 style="font-size: 11px; color: #4f46e5; margin: 0 0 10px 0; text-transform: uppercase; font-weight: 800; letter-spacing: 0.8px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-id-card"></i> Identity Details
                      </h4>
                      <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px;">
                        <div style="min-width: 0;">
                          <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Identity Card Type</label>
                          <select name="id_card_type_id" class="pms-select-idtype" style="width:100%; height:38px; padding:6px 12px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 600; box-sizing: border-box;">
                            <option value="">Select ID Type</option>
                            @foreach($idCardTypes ?? \App\Models\IdCardType::where('status', 'Active')->get() as $idc)
                              <option value="{{ $idc->id }}">{{ $idc->name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div style="min-width: 0;">
                          <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Card Number</label>
                          <input type="text" name="id_card_number" class="guest-id-number" style="width:100%; height:38px; padding:6px 12px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; font-family: monospace; box-sizing: border-box;" placeholder="ID Card Number">
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <button type="button" class="btn-ui-secondary" onclick="addAnotherGuest()" style="width: 100%; height: 42px; border: 1.5px dashed #6366f1; color: #4f46e5; background: #eef2ff; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 18px; transition: all 0.2s; box-shadow: none;">
                <i class="fa-solid fa-user-plus"></i> Add Another Guest
              </button>

            </div>

            <!-- Right Column: Digital Passes & Payment Settlement -->
            <div style="display: flex; flex-direction: column; background: #ffffff; padding: 18px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); box-sizing: border-box; min-width: 0; width: 320px; flex-shrink: 0;">
              
              <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9;">
                <span style="font-size: 11px; font-weight: 800; color: #4f46e5; text-transform: uppercase; letter-spacing: 0.8px; display: flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-qrcode"></i> Digital Passes
                </span>
                <span style="font-size: 10px; font-weight: 800; color: #059669; background: rgba(16, 185, 129, 0.1); padding: 2px 8px; border-radius: 10px; display: inline-flex; align-items: center; gap: 4px;">
                  <i class="fa-solid fa-circle-check"></i> Live Ready
                </span>
              </div>

              <!-- Guest #1 QR Pass Card -->
              <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; margin-bottom: 12px; box-sizing: border-box;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                  <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 800; color: #4f46e5; background: #e0e7ff; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.4px;">
                    <i class="fa-solid fa-user"></i> Guest #1
                  </span>
                  <span style="font-size: 9px; color: #94a3b8; font-weight: 700; font-family: monospace;">PASS-01</span>
                </div>
                
                <div style="font-size: 12px; font-weight: 800; color: #1e293b; line-height: 1.2;">Souvik Paul</div>
                <div style="font-size: 11px; color: #64748b; font-weight: 600; margin-top: 2px; display: flex; align-items: center; gap: 4px; font-family: monospace;">
                  <i class="fa-solid fa-phone" style="font-size: 9px; color: #6366f1;"></i> +91 62955 87845
                </div>

                <div style="background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 8px; margin-top: 8px; display: flex; align-items: center; justify-content: center;">
                  <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=Guest1_Souvik_Paul_6295587845" alt="Guest 1 QR" style="width: 80px; height: 80px; display: block; border-radius: 4px;">
                </div>
              </div>

              <!-- Instant Dispatch Notice -->
              <div style="background: rgba(99, 102, 241, 0.04); border: 1px dashed rgba(99, 102, 241, 0.25); border-radius: 8px; padding: 8px 10px; margin-bottom: 14px; font-size: 10px; color: #64748b; line-height: 1.4; display: flex; gap: 6px; align-items: flex-start; box-sizing: border-box;">
                <i class="fa-solid fa-paper-plane" style="color: #6366f1; margin-top: 1px; font-size: 10px;"></i>
                <span>Passes sent to WhatsApp & SMS automatically after booking.</span>
              </div>
              
              <!-- Payment & Advance Collection Section -->
              <div style="width: 100%; border-top: 1px solid #f1f5f9; padding-top: 12px; margin-bottom: 14px;">
                
                <!-- Plan Searchable Dropdown (Before Payment Type) -->
                <div style="margin-bottom: 10px;">
                  <label style="display: flex; align-items: center; justify-content: space-between; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #475569; text-transform: uppercase; letter-spacing: 0.8px;">
                    <span><i class="fa-solid fa-list-check" style="color: #6366f1; margin-right: 4px;"></i> Meal Plan</span>
                    <span style="font-size: 9px; font-weight: 700; color: #6366f1; text-transform: none;"><i class="fa-solid fa-magnifying-glass"></i> Searchable</span>
                  </label>
                  <select name="plan_id" id="plan-id-select" onchange="calculateReservationBilling()" style="width:100%; height:38px; padding:6px 12px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 700; box-sizing: border-box;">
                    <option value="" data-price="0">-- Select Meal Plan --</option>
                    @foreach($plans ?? \App\Models\Plan::where('status', 'Active')->get() as $p)
                      <option value="{{ $p->id }}" data-price="{{ (float)($p->price ?? 0) }}" data-name="{{ $p->plan_name }}">{{ $p->plan_name }}{{ (float)($p->price ?? 0) > 0 ? ' (₹' . number_format($p->price, 2) . ')' : '' }}</option>
                    @endforeach
                  </select>
                </div>

                <div style="margin-bottom: 10px;">
                  <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Payment Type</label>
                  <select name="payment_mode_id" id="payment-type-select" onchange="togglePaymentFields(this)" style="width:100%; height:38px; padding:6px 12px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 700; box-sizing: border-box;">
                    <option value="None" selected>None</option>
                    @foreach($paymentModes ?? \App\Models\PaymentMode::where('status', 'Active')->get() as $pm)
                      <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Dynamic Calculation & Billing Breakdown Panel (Always visible) -->
                <div id="payment-details-container" style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 4px;">
                  
                  <!-- Total Gross Tariff Card -->
                  <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; box-sizing: border-box;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                      <span style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Room Tariff</span>
                      <span id="calc-nights-rate-label" style="font-size: 10px; font-weight: 700; color: #64748b; font-family: monospace;">₹ 4,500 x 1 Night</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                      <span style="font-size: 11px; font-weight: 700; color: #1e293b;">Total Room Tariff:</span>
                      <span id="calc-gross-display" style="font-size: 14px; font-weight: 900; color: #1e293b; font-family: monospace;">₹ 4,500.00</span>
                    </div>
                    <input type="hidden" name="total_amount" id="calc-total-amount" value="4500">
                  </div>

                  <!-- Meal Plan Price Card -->
                  <div id="calc-meal-plan-card" style="display: none; background: #fffdf5; border: 1px solid #fef08a; border-radius: 8px; padding: 8px 10px; box-sizing: border-box; transition: all 0.2s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                      <span style="font-size: 9px; font-weight: 800; color: #854d0e; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-utensils" style="color: #ca8a04;"></i> <span id="calc-meal-plan-name">Meal Plan</span>
                      </span>
                      <span id="calc-meal-plan-rate-label" style="font-size: 10px; font-weight: 700; color: #a16207; font-family: monospace;">₹ 0.00 x 1 Night</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                      <span style="font-size: 11px; font-weight: 700; color: #713f12;">Meal Plan Total:</span>
                      <span id="calc-meal-plan-display" style="font-size: 13px; font-weight: 900; color: #b45309; font-family: monospace;">+ ₹ 0.00</span>
                    </div>
                    <input type="hidden" name="meal_plan_amount" id="calc-meal-plan-amount" value="0">
                  </div>

                  <!-- Combined Gross (When Meal Plan with price is active) -->
                  <div id="calc-combined-gross-card" style="display: none; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; padding: 6px 10px; box-sizing: border-box;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                      <span style="font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px;">Gross Total (Room + Meals):</span>
                      <span id="calc-combined-gross-display" style="font-size: 13px; font-weight: 900; color: #0f172a; font-family: monospace;">₹ 4,500.00</span>
                    </div>
                  </div>

                  <!-- Discount Configuration Panel (For all registration types & auto-synced with Company) -->
                  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; box-sizing: border-box;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.8px; margin: 0;">
                        <i class="fa-solid fa-tags" style="color: #6366f1; margin-right: 4px;"></i> Discount
                      </label>
                      <span id="calc-discount-val-label" style="font-size: 11px; font-weight: 900; color: #059669; font-family: monospace;">- ₹ 0.00</span>
                    </div>

                    <!-- Discount Type Radio Selector (% vs Amount) -->
                    <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                      <label id="discount-type-pct-label" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 8px; background: #eef2ff; border: 1.5px solid #6366f1; border-radius: 6px; font-size: 11px; font-weight: 800; color: #4f46e5; cursor: pointer; user-select: none; transition: all 0.15s ease;">
                        <input type="radio" name="discount_type" value="percentage" checked onchange="onDiscountTypeChange(this.value)" style="accent-color: #4f46e5; margin: 0; width: 14px; height: 14px; cursor: pointer;">
                        <span>Percentage (%)</span>
                      </label>
                      <label id="discount-type-amt-label" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 8px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 11px; font-weight: 700; color: #64748b; cursor: pointer; user-select: none; transition: all 0.15s ease;">
                        <input type="radio" name="discount_type" value="amount" onchange="onDiscountTypeChange(this.value)" style="accent-color: #4f46e5; margin: 0; width: 14px; height: 14px; cursor: pointer;">
                        <span>Amount (₹)</span>
                      </label>
                    </div>

                    <!-- Percentage Input Box -->
                    <div id="disc-percentage-input-wrapper">
                      <div style="position: relative;">
                        <input type="number" 
                               name="discount_percentage" 
                               id="calc-discount-percentage" 
                               oninput="calculateReservationBilling()" 
                               min="0" 
                               max="100" 
                               step="any" 
                               value="0"
                               style="width: 100%; height: 36px; padding: 6px 30px 6px 12px; font-size: 12px; font-weight: 700; font-family: monospace; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; color: #1e293b; box-sizing: border-box;" 
                               placeholder="Discount % (e.g. 10)">
                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; font-weight: 800; color: #64748b; pointer-events: none;">%</span>
                      </div>
                    </div>

                    <!-- Amount Input Box -->
                    <div id="disc-amount-input-wrapper" style="display: none;">
                      <div style="position: relative;">
                        <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; font-weight: 800; color: #64748b; pointer-events: none;">₹</span>
                        <input type="number" 
                               name="discount_amount" 
                               id="calc-discount-amount" 
                               oninput="calculateReservationBilling()" 
                               min="0" 
                               step="any" 
                               value="0"
                               style="width: 100%; height: 36px; padding: 6px 12px 6px 24px; font-size: 12px; font-weight: 700; font-family: monospace; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; color: #1e293b; box-sizing: border-box;" 
                               placeholder="Amount in ₹ (e.g. 500)">
                      </div>
                    </div>

                    <!-- Corporate Pre-fill Notice Note -->
                    <div id="company-discount-applied-text" style="display: none; font-size: 10px; font-weight: 700; color: #059669; margin-top: 6px; line-height: 1.3; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-building-circle-check"></i> <span id="company-discount-note">Corporate discount auto-applied</span>
                    </div>
                  </div>

                  <!-- GST / Tax Configuration Panel (Dynamic Slabs & Inc./Excluding GST) -->
                  <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; box-sizing: border-box;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.8px; margin: 0;">
                        <i class="fa-solid fa-receipt" style="color: #6366f1; margin-right: 4px;"></i> GST / Tax
                      </label>
                      <span id="calc-gst-val-label" style="font-size: 11px; font-weight: 900; color: #6366f1; font-family: monospace;">+ ₹ 0.00 (0%)</span>
                    </div>

                    <!-- GST Type Radio Selector (Inc. GST vs Excluding GST) -->
                    <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                      <label id="gst-type-inc-label" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 8px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 6px; font-size: 11px; font-weight: 700; color: #64748b; cursor: pointer; user-select: none; transition: all 0.15s ease;">
                        <input type="radio" name="gst_type" value="inclusive" onchange="onGstTypeChange(this.value)" style="accent-color: #4f46e5; margin: 0; width: 14px; height: 14px; cursor: pointer;">
                        <span>Inc. GST</span>
                      </label>
                      <label id="gst-type-excl-label" style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 8px; background: #eef2ff; border: 1.5px solid #6366f1; border-radius: 6px; font-size: 11px; font-weight: 800; color: #4f46e5; cursor: pointer; user-select: none; transition: all 0.15s ease;">
                        <input type="radio" name="gst_type" value="exclusive" checked onchange="onGstTypeChange(this.value)" style="accent-color: #4f46e5; margin: 0; width: 14px; height: 14px; cursor: pointer;">
                        <span>Excluding GST</span>
                      </label>
                    </div>

                    <!-- Dynamic GST Slabs Dropdown Selector -->
                    <div>
                      <select name="gst_percentage_id" id="calc-gst-rate" onchange="calculateReservationBilling()" style="width: 100%; height: 36px; padding: 6px 12px; font-size: 12px; font-weight: 700; font-family: monospace; border: 1px solid #cbd5e1; border-radius: 6px; background: #f8fafc; color: #1e293b; box-sizing: border-box; cursor: pointer;">
                        <option value="" data-rate="0">0% (No GST / Exempted)</option>
                        @foreach($gstPercentages ?? \App\Models\GstPercentage::where('status', 'Active')->orderBy('gst_percentage', 'asc')->get() as $gst)
                          @if((float)$gst->gst_percentage > 0)
                            <option value="{{ $gst->id }}" data-rate="{{ (float)$gst->gst_percentage }}">
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
                  <div style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(16, 185, 129, 0.08)); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 8px; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box;">
                    <span style="font-size: 10px; font-weight: 800; color: #4f46e5; text-transform: uppercase; letter-spacing: 0.5px;">Payable Amount:</span>
                    <span id="calc-payable-display" style="font-size: 14px; font-weight: 900; color: #4f46e5; font-family: monospace;">₹ 4,500.00</span>
                    <input type="hidden" name="payable_amount" id="calc-payable-amount" value="4500">
                  </div>

                  <!-- Advance Amount Input -->
                  <div>
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 4px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Advance Amount (₹)</label>
                    <input type="number" name="advance_amount" id="payment-advance-amount" oninput="calculateReservationBilling()" min="0" step="any" style="width:100%; height:36px; padding:4px 10px; font-size:12px; font-weight: 700; font-family: monospace; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; box-sizing: border-box;" placeholder="0.00">
                  </div>

                  <!-- Remaining / Balance Amount Display -->
                  <div id="calc-balance-box" style="background: rgba(225, 29, 72, 0.06); border: 1px solid rgba(225, 29, 72, 0.2); border-radius: 8px; padding: 8px 10px; display: flex; justify-content: space-between; align-items: center; box-sizing: border-box;">
                    <span style="font-size: 10px; font-weight: 800; color: #e11d48; text-transform: uppercase; letter-spacing: 0.5px;">Remaining Amount:</span>
                    <span id="calc-balance-display" style="font-size: 14px; font-weight: 900; color: #e11d48; font-family: monospace;">₹ 4,500.00</span>
                    <input type="hidden" name="balance" id="calc-balance-amount" value="4500">
                  </div>

                  <!-- Remarks -->
                  <div>
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 4px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Remarks</label>
                    <input type="text" name="payment_remarks" id="payment-remarks" style="width:100%; height:36px; padding:4px 10px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; box-sizing: border-box;" placeholder="Transaction ID, UPI Ref, or Notes">
                  </div>

                </div>

              </div>
              
              <div style="width: 100%; margin-top: auto;">
                <button type="submit" id="reserve-submit-btn" class="btn-ui-success" style="width: 100%; height: 44px; justify-content: center; margin-bottom: 8px; font-size: 13px; font-weight: 800; border-radius: 8px; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                  <i class="fa-solid fa-key"></i> Complete Check-In
                </button>
                <button type="button" class="btn-ui-secondary" style="width: 100%; height: 38px; justify-content: center; font-size: 12px; font-weight: 700; border-radius: 8px; border: 1px solid #cbd5e1; background: #f1f5f9; color: #475569;" onclick="closeModal('reserve-modal')">
                  Cancel
                </button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="print-area" style="display: none;"></div>

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
