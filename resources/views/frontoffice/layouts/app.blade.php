<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Front Office - Hotel Sagar Sonnet CRM / PMS')</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <style>
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
      <span><i class="fa-regular fa-calendar-days"></i> {{ date('l, d M Y') }}</span>
      <span><i class="fa-solid fa-sun" style="color: #fbc531;"></i> Morning Shift (07:00 - 15:00)</span>
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
    <a href="{{ route('frontoffice.dashboard') }}" class="pms-tab-link active">
      <i class="fa-solid fa-desktop tab-ico" style="color: #6366f1;"></i> Front Office
    </a>
    <a href="{{ route('admin.roommanagement.index') }}" class="pms-tab-link">
      <i class="fa-solid fa-door-open tab-ico" style="color: #ec4899;"></i> Room Management
    </a>
    <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('POS Sales module queued.', 'info')">
      <i class="fa-solid fa-utensils tab-ico" style="color: #10b981;"></i> POS Sales
    </a>
    <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('House Keeping module queued.', 'info')">
      <i class="fa-solid fa-broom tab-ico" style="color: #f59e0b;"></i> House Keeping
    </a>
    <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Stores module queued.', 'info')">
      <i class="fa-solid fa-boxes-stacked tab-ico" style="color: #22d3ee;"></i> Stores
    </a>
    <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Accounts module queued.', 'info')">
      <i class="fa-solid fa-file-invoice-dollar tab-ico" style="color: #a78bfa;"></i> Accounts
    </a>
    <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Banquet module queued.', 'info')">
      <i class="fa-solid fa-champagne-glasses tab-ico" style="color: #fbbf24;"></i> Banquet & Services
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

  <!-- NEW RESERVATION MODAL -->
  <div class="modal-backdrop" id="reserve-modal">
    <div class="modal-window large" style="width: 1140px; max-width: 96vw; max-height: 94vh; display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3); border: none; box-sizing: border-box;">
      <div class="modal-top" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; padding: 14px 22px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.15);">
        <div style="display: flex; align-items: center; gap: 10px;">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 14px;">
            <i class="fa-solid fa-calendar-plus"></i>
          </div>
          <div>
            <h3 style="margin: 0; font-size: 15px; font-weight: 800; letter-spacing: 0.3px; color: #ffffff;">New Reservation</h3>
            <div style="font-size: 11px; opacity: 0.85; font-weight: 500;">Hotel Sagar Sonnet • Front Desk PMS</div>
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
                    <span id="stay-nights-badge" style="font-size: 11px; font-weight: 800; background: #e0e7ff; color: #4338ca; padding: 2px 10px; border-radius: 20px; border: 1px solid #c7d2fe; display: inline-flex; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-moon"></i> 1 Night Stay
                    </span>
                  </div>

                  <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px;">
                    <!-- Check-In -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; box-sizing: border-box; min-width: 0;">
                      <label style="display: flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 800; color: #4f46e5; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-plane-arrival"></i> Check-in (Arrival)
                      </label>
                      <div style="display: flex; gap: 6px; align-items: center;">
                        <input type="date" id="reserve-date-input" name="reserve_date" class="pms-input-field" onchange="onCheckInDateChange()" style="flex: 3; min-width: 0; width: 100%; height: 36px; padding: 4px 8px; font-size: 12px; font-weight: 700; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; box-sizing: border-box;">
                        <input type="time" id="reserve-time-input" name="reserve_time" class="pms-input-field" style="flex: 2; min-width: 0; width: 100%; height: 36px; padding: 4px 6px; font-size: 12px; font-weight: 700; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; box-sizing: border-box;">
                      </div>
                    </div>

                    <!-- Check-Out -->
                    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; box-sizing: border-box; min-width: 0;">
                      <label style="display: flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 800; color: #059669; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-plane-departure"></i> Check-out (Departure)
                      </label>
                      <div style="display: flex; gap: 6px; align-items: center;">
                        <input type="date" id="checkout-date-input" name="checkout_date" class="pms-input-field" onchange="onCheckOutDateChange()" style="flex: 3; min-width: 0; width: 100%; height: 36px; padding: 4px 8px; font-size: 12px; font-weight: 700; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; box-sizing: border-box;">
                        <input type="time" id="checkout-time-input" name="checkout_time" class="pms-input-field" style="flex: 2; min-width: 0; width: 100%; height: 36px; padding: 4px 6px; font-size: 12px; font-weight: 700; color: #1e293b; border: 1px solid #cbd5e1; border-radius: 6px; background: #ffffff; box-sizing: border-box;" value="11:00">
                      </div>
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
                        <option value="{{ $cmp->id }}">{{ $cmp->name }}</option>
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
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 12px;">
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">GSTIN</label>
                        <input type="text" name="new_company_gstin" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; font-family: monospace; box-sizing: border-box;" placeholder="29ABCDE1234F1Z5">
                      </div>
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Corporate Phone</label>
                        <input type="text" name="new_company_phone" style="width:100%; height:38px; padding:6px 12px; font-size:13px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;" placeholder="Office / Mobile">
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

                    <!-- Row 5: DOB + Anniversary + Status -->
                    <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr); gap: 12px; margin-bottom: 14px;">
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Date of Birth</label>
                        <input type="date" name="dob" class="guest-dob-input" style="width:100%; height:38px; padding:4px 8px; font-size:12px; font-weight: 600; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;">
                      </div>
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Anniversary</label>
                        <input type="date" name="anniversary" class="guest-anniversary-input" style="width:100%; height:38px; padding:4px 8px; font-size:12px; font-weight: 600; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; box-sizing: border-box;">
                      </div>
                      <div style="min-width: 0;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Status</label>
                        <select name="status" class="pms-select-status" style="width:100%; height:38px; padding:6px 10px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; font-weight: 700; color: #059669; box-sizing: border-box;">
                          <option value="Confirmed">Confirmed</option>
                          <option value="Tentative">Tentative</option>
                          <option value="Waitlisted">Waitlisted</option>
                        </select>
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
                        <h4 style="font-size: 11px; color: #4f46e5; margin: 0; text-transform: uppercase; font-weight: 800; letter-spacing: 0.8px; display: flex; align-items: center; gap: 6px;">
                          <i class="fa-solid fa-hotel"></i> Room Assignment
                        </h4>
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
                              <option value="{{ $rm['id'] ?? $rm['room'] }}" data-room-no="{{ $rm['room'] }}" data-rate="{{ $rm['rate'] }}" data-bedding="{{ $rm['bedding'] ?? 'King Size Master (72x78)' }}" data-max-adults="{{ $rm['max_adults'] ?? 2 }}" data-max-children="{{ $rm['max_children'] ?? 1 }}" data-max-pax="{{ $rm['max_pax'] ?? 3 }}">{{ $rm['room'] }} ({{ $rm['type'] }})</option>
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
                
                <div style="margin-bottom: 10px;">
                  <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 5px; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Payment Type</label>
                  <select name="payment_mode_id" id="payment-type-select" onchange="togglePaymentFields(this)" style="width:100%; height:38px; padding:6px 12px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 700; box-sizing: border-box;">
                    <option value="None" selected>None</option>
                    @foreach($paymentModes ?? \App\Models\PaymentMode::where('status', 'Active')->get() as $pm)
                      <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                    @endforeach
                  </select>
                </div>

                <!-- Dynamic Calculation & Billing Breakdown Panel -->
                <div id="payment-details-container" style="display: none; flex-direction: column; gap: 8px; margin-bottom: 4px;">
                  
                  <!-- Total Gross Tariff Card -->
                  <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 10px; box-sizing: border-box;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                      <span style="font-size: 9px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Room Tariff</span>
                      <span id="calc-nights-rate-label" style="font-size: 10px; font-weight: 700; color: #64748b; font-family: monospace;">₹ 4,500 x 1 Night</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline;">
                      <span style="font-size: 11px; font-weight: 700; color: #1e293b;">Total Gross:</span>
                      <span id="calc-gross-display" style="font-size: 14px; font-weight: 900; color: #1e293b; font-family: monospace;">₹ 4,500.00</span>
                    </div>
                    <input type="hidden" name="total_amount" id="calc-total-amount" value="4500">
                  </div>

                  <!-- Discount Selection from discounts table -->
                  <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Discount</label>
                      <span id="calc-discount-val-label" style="font-size: 10px; font-weight: 800; color: #059669; font-family: monospace;">- ₹ 0.00</span>
                    </div>
                    <select name="discount_id" id="payment-discount-select" onchange="calculateReservationBilling()" style="width:100%; height:36px; padding:4px 10px; font-size:12px; border:1px solid #cbd5e1; border-radius: 8px; background: #ffffff; color: #1e293b; font-weight: 700; box-sizing: border-box;">
                      <option value="" data-pct="0">No Discount (0%)</option>
                      @php
                        $discountsList = $discounts ?? \App\Models\Discount::where('status', 'Active')->orderBy('discount_percentage', 'asc')->get();
                      @endphp
                      @foreach($discountsList as $disc)
                        <option value="{{ $disc->id }}" data-pct="{{ $disc->discount_percentage }}">{{ (float)$disc->discount_percentage }}% Discount</option>
                      @endforeach
                    </select>
                    <input type="hidden" name="discount_percentage" id="calc-discount-percentage" value="0">
                    <input type="hidden" name="discount_amount" id="calc-discount-amount" value="0">
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
                <button type="submit" class="btn-ui-success" style="width: 100%; height: 44px; justify-content: center; margin-bottom: 8px; font-size: 13px; font-weight: 800; border-radius: 8px; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                  <i class="fa-solid fa-floppy-disk"></i> Save Reservation
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
