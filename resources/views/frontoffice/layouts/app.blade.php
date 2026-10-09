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
    <div class="modal-window large" style="width: 780px; max-width: 95vw;">
      <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 id="m-title" style="margin: 0;"><i class="fa-solid fa-door-open"></i> Room Details</h3>
        <button class="modal-close" onclick="closeModal('room-modal')">&times;</button>
      </div>
      <div class="modal-content-area" id="m-body" style="padding: 20px;"></div>
      <div class="modal-bot" id="m-footer" style="padding: 14px 20px; display: flex; justify-content: flex-end; gap: 8px;">
        <button class="btn-ui-secondary" onclick="closeModal('room-modal')">Close</button>
      </div>
    </div>
  </div>

  <!-- NEW RESERVATION MODAL -->
  <div class="modal-backdrop" id="reserve-modal">
    <div class="modal-window large" style="width: 960px; max-width: 95vw;">
      <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0;"><i class="fa-solid fa-calendar-plus"></i> New Reservation • Hotel Sagar Sonnet</h3>
        </div>
        <button class="modal-close" onclick="closeModal('reserve-modal')">&times;</button>
      </div>
      <div class="modal-content-area" style="max-height: 75vh; overflow-y: auto; padding: 24px; background: #f8fafc;">
        <form onsubmit="handleReserve(event)">
          <!-- Radio Buttons -->
          <div style="display: flex; gap: 24px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-medium);">
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; cursor: pointer; font-size: 13px; color: var(--accent-primary);">
              <input type="radio" name="res_type" value="new" checked onchange="toggleResType()" style="accent-color: var(--accent-primary); transform: scale(1.2);"> New
            </label>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; cursor: pointer; font-size: 13px; color: var(--text-secondary);">
              <input type="radio" name="res_type" value="regular" onchange="toggleResType()" style="accent-color: var(--accent-primary); transform: scale(1.2);"> Regular
            </label>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; cursor: pointer; font-size: 13px; color: var(--text-secondary);">
              <input type="radio" name="res_type" value="company" onchange="toggleResType()" style="accent-color: var(--accent-primary); transform: scale(1.2);"> Company
            </label>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; cursor: pointer; font-size: 13px; color: var(--text-secondary);">
              <input type="radio" name="res_type" value="privilege" onchange="toggleResType()" style="accent-color: var(--accent-primary); transform: scale(1.2);"> Privilege
            </label>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 280px; gap: 28px;">
            <!-- Left Column: Form Fields -->
            <div>
              <!-- Reservation Meta Fields -->
              <div id="section-reservation-details" style="display: flex; flex-direction: column; background: #fff; padding: 24px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.02); margin-bottom: 24px;">
                
                <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Reserve ID</label>
                    <input type="text" class="pms-input-field" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #f1f5f9; color: var(--text-primary); font-weight: 700; opacity: 0.8;" value="829\2026-2027" disabled>
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Reserve Date</label>
                    <input type="date" id="reserve-date-input" class="pms-input-field" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Reserve Time</label>
                    <input type="time" id="reserve-time-input" class="pms-input-field" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                  </div>
                </div>
                <div style="margin-bottom: 20px;">
                  <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Mode of Reserve</label>
                  <select class="pms-input-field" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                    @foreach($reservationModes as $rm)
                      <option value="{{ $rm->name }}">{{ $rm->name }}</option>
                    @endforeach
                    @if($reservationModes->isEmpty())
                      <option>Phone</option>
                      <option>Physical</option>
                      <option>Website</option>
                      <option>Enquiry</option>
                    @endif
                  </select>
                </div>

                <!-- Company Specific Fields -->
                <div id="section-company-fields" style="display: none; padding: 20px; background: rgba(16, 185, 129, 0.05); border: 1px dashed var(--accent-emerald); border-radius: var(--radius-md); margin-bottom: 20px;">
                  <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--accent-emerald); text-transform: uppercase; letter-spacing: 1px;"><i class="fa-solid fa-building"></i> Company Name</label>
                    <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--accent-emerald); border-radius: var(--radius-md); background: #fff; color: var(--text-primary); font-weight: 700;" onchange="toggleNewCompany(this)">
                      <option value="">-- Select Corporate Account --</option>
                      @foreach($companies as $cmp)
                        <option value="{{ $cmp->id }}">{{ $cmp->name }}</option>
                      @endforeach
                      <option value="new" style="color: var(--accent-primary); font-weight: 900;">+ Register New Company</option>
                    </select>
                  </div>
                  <div id="new-company-details" style="display: none; border-top: 1px solid rgba(16, 185, 129, 0.2); padding-top: 16px;">
                    <div style="margin-bottom: 16px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">New Company Name</label>
                      <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="Full Corporate Entity Name">
                    </div>
                    <div style="margin-bottom: 16px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Company Address</label>
                      <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="Billing Address">
                    </div>
                    <div style="display: flex; gap: 16px;">
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">GSTIN</label>
                        <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; font-family: monospace;" placeholder="29ABCDE1234F1Z5">
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Corporate Phone</label>
                        <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="Office / Mobile">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div id="guests-container">
                <div class="guest-block" style="margin-bottom: 24px;">
                  <!-- General Fields Container -->
                  <div class="section-guest-details" style="display: flex; flex-direction: column; background: #fff; padding: 24px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-medium); padding-bottom: 12px; margin-bottom: 20px;">
                      <h4 class="guest-heading" style="margin: 0; font-size: 14px; color: var(--accent-primary); font-weight: 800;">Guest #1</h4>
                    </div>

                    <!-- Regular Search Section -->
                    <div class="section-regular-search" style="display: none; margin-bottom: 20px; padding: 16px; background: rgba(99, 102, 241, 0.03); border: 1px dashed var(--accent-primary); border-radius: var(--radius-md);">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 8px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Search Regular Guest</label>
                      <div style="display: flex; gap: 12px;">
                        <input type="text" style="flex: 1; height:42px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);" placeholder="Enter Mobile Number">
                        <button type="button" class="btn-ui-primary" style="height: 42px; padding: 0 24px;" onclick="alert('Searching guest directory...')"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                      </div>
                    </div>

                    <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                      <div style="width: 100px;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Title</label>
                        <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                          <option>Mr.</option>
                          <option>Mrs.</option>
                          <option>Ms.</option>
                          <option>Dr.</option>
                          <option>Prof.</option>
                        </select>
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Guest Name</label>
                        <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="First and Last Name" required>
                      </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Guest Address</label>
                      <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="House No, Street, Landmark">
                    </div>

                    <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Nationality</label>
                        <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;">
                          <option>Indian</option>
                          <option>Foreign National</option>
                          <option>NRI</option>
                        </select>
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">City</label>
                        <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="e.g. Kolkata">
                      </div>
                    </div>

                    <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Telephone / Mobile</label>
                        <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="+91" required>
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Email ID</label>
                        <input type="email" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="guest@email.com">
                      </div>
                    </div>

                    <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Date of Birth</label>
                        <input type="date" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;">
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Anniversary</label>
                        <input type="date" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;">
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Status</label>
                        <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; font-weight: 700; color: var(--accent-emerald);">
                          <option>Confirmed</option>
                          <option>Tentative</option>
                          <option>Waitlisted</option>
                        </select>
                      </div>
                    </div>

                    <div style="border-top: 1px dashed var(--border-medium); margin: 24px 0;"></div>

                    <div class="privilege-checkbox-container" style="display: flex; flex-direction: column; align-items: flex-start; gap: 8px; margin-bottom: 24px; padding: 12px 16px; background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-md);">
                      <div style="width: 100%;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 13px; cursor: pointer; color: #059669;">
                          <input type="checkbox" class="chk-privilege" onchange="togglePrivilegeInput(this)" style="width: 16px; height: 16px; cursor: pointer; accent-color: #059669;"> Has Privilege Card
                        </label>
                      </div>
                      <div class="privilege-input-container" style="display: none; width: 100%;">
                        <input type="text" style="width:100%; max-width: 250px; height:36px; padding:6px 12px; font-size:13px; border:1px solid #059669; border-radius: var(--radius-sm); background: #fff; color: var(--text-primary); font-weight: 600;" placeholder="Privilege Card No.">
                      </div>
                    </div>

                    <h4 style="font-size: 12px; color: var(--accent-primary); margin-bottom: 16px; text-transform: uppercase; font-weight: 900; letter-spacing: 1px;"><i class="fa-solid fa-hotel"></i> Room Assign</h4>
                    <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Room Category</label>
                        <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                          <option value="">Select Category</option>
                          @foreach($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Floor</label>
                        <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                          <option value="">Select Floor</option>
                          @foreach($floors as $fl)
                            <option value="{{ $fl->floor }}">Floor {{ $fl->floor }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Room No.</label>
                        <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                          <option value="">Select Room No.</option>
                          @foreach($rackRooms as $rm)
                            <option value="{{ $rm['room'] }}">{{ $rm['room'] }} ({{ $rm['type'] }})</option>
                          @endforeach
                        </select>
                      </div>
                    </div>

                    <h4 style="font-size: 12px; color: var(--accent-primary); margin-bottom: 16px; text-transform: uppercase; font-weight: 900; letter-spacing: 1px;"><i class="fa-solid fa-id-card"></i> Identity Details</h4>
                    <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Identity Card Type</label>
                        <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;">
                          @foreach($idCardTypes as $idc)
                            <option value="{{ $idc->name }}">{{ $idc->name }}</option>
                          @endforeach
                          @if($idCardTypes->isEmpty())
                            <option>Aadhar Card</option>
                            <option>PAN Card</option>
                            <option>Passport</option>
                            <option>Driving License</option>
                          @endif
                        </select>
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Card Number</label>
                        <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; font-family: monospace;" placeholder="ID Number">
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <button type="button" class="btn-ui-secondary" onclick="addAnotherGuest()" style="width: 100%; height: 48px; border: 1px dashed var(--accent-primary); color: var(--accent-primary); background: rgba(99,102,241,0.05); justify-content: center; margin-bottom: 24px; box-shadow: none;">
                <i class="fa-solid fa-user-plus"></i> Add Another Guest
              </button>

            </div>

            <!-- Right Column: Media / QR Codes / Action -->
            <div style="display: flex; flex-direction: column; background: #fff; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--border-medium); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
              
              <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border-medium);">
                <span style="font-size: 11px; font-weight: 800; color: var(--accent-primary); text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-qrcode"></i> Digital Passes
                </span>
                <span style="font-size: 10px; font-weight: 800; color: #059669; background: rgba(16, 185, 129, 0.1); padding: 2px 8px; border-radius: 10px; display: inline-flex; align-items: center; gap: 4px;">
                  <i class="fa-solid fa-circle-check"></i> Live Ready
                </span>
              </div>

              <!-- Guest #1 QR Pass Card -->
              <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: 12px; padding: 14px; margin-bottom: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                  <span style="display: inline-flex; align-items: center; gap: 5px; font-size: 10px; font-weight: 800; color: var(--accent-primary); background: rgba(99,102,241,0.08); padding: 3px 8px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <i class="fa-solid fa-user"></i> Guest #1
                  </span>
                  <span style="font-size: 10px; color: var(--text-muted); font-weight: 700; font-family: var(--font-mono);">PASS-01</span>
                </div>
                
                <div style="font-size: 13px; font-weight: 800; color: var(--text-primary); line-height: 1.2;">Souvik Paul</div>
                <div style="font-size: 11px; color: var(--text-secondary); font-weight: 600; margin-top: 3px; display: flex; align-items: center; gap: 5px; font-family: var(--font-mono);">
                  <i class="fa-solid fa-phone" style="font-size: 10px; color: var(--accent-primary);"></i> +91 62955 87845
                </div>

                <div style="background: #f8fafc; border: 1px dashed var(--border-medium); border-radius: 10px; padding: 10px; margin-top: 10px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                  <div style="background: #fff; padding: 6px; border-radius: 8px; border: 1px solid var(--border-medium); box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=96x96&data=Guest1_Souvik_Paul_6295587845" alt="Guest 1 QR" style="width: 96px; height: 96px; display: block; border-radius: 4px;">
                  </div>
                </div>
              </div>

              <!-- Instant Dispatch Notice -->
              <div style="background: rgba(99, 102, 241, 0.04); border: 1px dashed rgba(99, 102, 241, 0.3); border-radius: 8px; padding: 10px 12px; margin-bottom: 16px; font-size: 11px; color: var(--text-secondary); line-height: 1.4; display: flex; gap: 8px; align-items: flex-start;">
                <i class="fa-solid fa-paper-plane" style="color: var(--accent-primary); margin-top: 2px;"></i>
                <span>Passes are sent to WhatsApp & SMS automatically after booking.</span>
              </div>
              
              <!-- Payment & Advance Collection Section -->
              <div style="width: 100%; margin-top: auto; padding-top: 14px; margin-bottom: 16px; border-top: 1px dashed var(--border-medium);">
                
                <div style="margin-bottom: 12px;">
                  <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Payment Type</label>
                  <select id="payment-type-select" onchange="togglePaymentFields(this)" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary); font-weight: 700;">
                    <option value="None" selected>None</option>
                    @foreach($paymentModes as $pm)
                      <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                    @endforeach
                    @if($paymentModes->isEmpty())
                      <option value="Cash">Cash</option>
                      <option value="UPI">UPI</option>
                      <option value="Scanner">Scanner</option>
                      <option value="Bank Transfer">Bank Transfer</option>
                    @endif
                  </select>
                </div>

                <!-- Conditional Advance & Remarks Fields -->
                <div id="payment-details-container" style="display: none; flex-direction: column; gap: 12px; margin-bottom: 4px;">
                  <div>
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Advance Amount (₹)</label>
                    <input type="number" id="payment-advance-amount" style="width:100%; height:40px; padding:8px 14px; font-size:13px; font-weight: 700; font-family: var(--font-mono); border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);" placeholder="Enter Advance Amount">
                  </div>
                  <div>
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Remarks</label>
                    <input type="text" id="payment-remarks" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);" placeholder="Transaction ID, UPI Ref, or Notes">
                  </div>
                </div>

              </div>
              
              <div style="width: 100%;">
                <button type="submit" class="btn-ui-success" style="width: 100%; height: 48px; justify-content: center; margin-bottom: 12px; font-size: 14px; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);">
                  <i class="fa-solid fa-floppy-disk"></i> Save Reservation
                </button>
                <button type="button" class="btn-ui-secondary" style="width: 100%; height: 44px; justify-content: center;" onclick="closeModal('reserve-modal')">
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
