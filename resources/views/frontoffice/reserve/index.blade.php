@extends('frontoffice.layouts.app')

@section('title', 'Booked & Reserved Rooms - Hotel Sagar Sonnet CRM / PMS')

@push('styles')
<style>
  .reserve-date-box {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1.5px solid var(--border-color, #cbd5e1);
    border-radius: 8px;
    padding: 3px 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    gap: 8px;
  }

  .reserve-date-input {
    border: none;
    background: transparent;
    font-weight: 700;
    font-size: 12px;
    color: var(--text-primary, #1e293b);
    cursor: pointer;
    outline: none;
    font-family: inherit;
  }

</style>
@endpush

@section('content')
<!-- Main Viewport: Booked & Reserved Rooms Rack Grid & Right Statistics -->
<main class="pms-main-viewport">
  
  <!-- Left: Reserved Rooms Rack Panel -->
  <div class="pms-rack-panel">
    <div class="rack-title-bar">
      <div>
        <h2><i class="fa-solid fa-calendar-check" style="color: #a78bfa;"></i> Booked & Reserved Rooms</h2>
        <span style="font-size: 11px; color: var(--text-muted); font-weight: 600;">
          Hotel Sagar Sonnet • {{ $stats['total_booked'] }} Active Booked Rooms (Check-ins & Stay-overs)
        </span>
      </div>

      <!-- Real-Time From & To Date Filter Navigator -->
      <div class="pms-date-filter-group" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <form method="GET" action="{{ route('frontoffice.reserve') }}" id="reserve-date-filter-form" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin: 0;">
          <!-- Preserved filters -->
          <input type="hidden" name="status" value="{{ $selectedStatus ?? 'all' }}">
          <input type="hidden" name="type" value="{{ $selectedType ?? 'ALL' }}">
          <input type="hidden" name="floor" value="{{ $selectedFloor ?? 'ALL' }}">
          <input type="hidden" name="bedding" value="{{ $selectedBedding ?? 'ALL' }}">
          <input type="hidden" name="pax" value="{{ $selectedPax ?? 'ALL' }}">
          <input type="hidden" name="amenity" value="{{ $selectedAmenity ?? 'ALL' }}">
          @if(!empty($searchQuery))
            <input type="hidden" name="search" value="{{ $searchQuery }}">
          @endif

          <div class="reserve-date-box">
            <label for="reserve-from-date" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 4px; white-space: nowrap;">
              <i class="fa-regular fa-calendar-days" style="color: #6366f1;"></i> From:
            </label>
            <input type="date" 
                   name="from_date" 
                   id="reserve-from-date" 
                   class="reserve-date-input" 
                   value="{{ $fromDate }}" 
                   onchange="this.form.submit()" 
                   title="Check-in / Reservation From Date">

            <span style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">To:</span>
            <input type="date" 
                   name="to_date" 
                   id="reserve-to-date" 
                   class="reserve-date-input" 
                   value="{{ $toDate }}" 
                   onchange="this.form.submit()" 
                   title="Check-in / Reservation To Date">
          </div>

          <button type="button" 
                  onclick="setReserveDatesToday()" 
                  title="Reset date filter to today" 
                  style="border: 1px solid #cbd5e1; background: {{ $isToday ? '#ecfdf5' : '#ffffff' }}; color: {{ $isToday ? '#059669' : '#475569' }}; font-weight: 800; font-size: 11px; padding: 6px 12px; border-radius: 6px; cursor: pointer; transition: all 0.15s ease;">
            <i class="fa-solid fa-rotate-left"></i> Today
          </button>

          @if($isToday)
            <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(16, 185, 129, 0.12); color: #059669; font-weight: 800; font-size: 11px; padding: 5px 10px; border-radius: 6px; border: 1px solid rgba(16, 185, 129, 0.25); white-space: nowrap;">
              <span style="width: 7px; height: 7px; background: #10b981; border-radius: 50%; display: inline-block; box-shadow: 0 0 6px rgba(16, 185, 129, 0.7);"></span>
              Live Today ({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }})
            </span>
          @else
            <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(99, 102, 241, 0.12); color: #4f46e5; font-weight: 800; font-size: 11px; padding: 5px 10px; border-radius: 6px; border: 1px solid rgba(99, 102, 241, 0.25); white-space: nowrap;">
              <i class="fa-solid fa-calendar-check" style="font-size: 11px;"></i>
              {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} &rarr; {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
            </span>
          @endif
        </form>

        <button type="button" class="btn-ui-primary" onclick="if(typeof openReservationModal === 'function') openReservationModal(); else openModal('reserve-modal');" style="margin-left: auto;">
          <i class="fa-solid fa-calendar-plus"></i> New Reservation
        </button>
      </div>
    </div>

    <!-- Rooms Grid Viewport -->
    <div class="rack-grid-viewport">
      <div class="rack-rooms-container" id="rooms-grid">
        
        @forelse($rackRooms as $rm)
          @php
            $cardClass = 'c-available';
            $badgeText = $rm['cleaning'] ?? 'Available';
            if ($rm['status'] === 'occupied') {
              $cardClass = 'c-occupied';
              $badgeText = $rm['guest']['state'] ?? 'Occupied';
            } elseif ($rm['status'] === 'dirty') {
              $cardClass = 'c-dirty';
              $badgeText = 'Dirty';
            } elseif ($rm['status'] === 'blocked') {
              $cardClass = 'c-blocked';
              $badgeText = 'Blocked';
            } elseif ($rm['status'] === 'available') {
              $cardClass = 'c-cleaned';
              $badgeText = 'Cleaned';
            }
            $roomJsonData = htmlspecialchars(json_encode($rm), ENT_QUOTES, 'UTF-8');
          @endphp

          <div class="rack-card {{ $cardClass }}" 
               onclick="openRoomDetailsModal(this)" 
               data-room-json="{{ $roomJsonData }}"
               data-room="{{ $rm['room'] }}" 
               data-floor="{{ $rm['floor'] }}" 
               data-type="{{ $rm['type'] }}" 
               data-category="{{ $rm['category'] }}"
               data-guest="{{ $rm['guest']['name'] ?? '' }}"
               data-status="{{ $rm['status'] }}"
               data-cleaning="{{ $rm['cleaning'] }}"
               data-operational="{{ $rm['operational_status'] ?? '' }}"
               data-housekeeping="{{ $rm['housekeeping_status'] ?? '' }}">
            <div class="rack-card-top">
              <div class="rack-room-num">{{ $rm['room'] }}</div>
              <div class="rack-room-type" title="{{ $rm['type'] }}">{{ $rm['type'] }}</div>
            </div>
            <div class="rack-card-mid">
              @if($rm['status'] === 'blocked')
                <div class="rack-guest-name" title="Blocked">Blocked</div>
              @endif
            </div>
            <div class="rack-card-bot">
              <div class="rack-badge">{{ $badgeText }}</div>
            </div>
          </div>
        @empty
          <div style="grid-column: 1 / -1; background: #ffffff; border-radius: 12px; border: 1.5px dashed #cbd5e1; padding: 45px 20px; text-align: center; margin: 15px 0;">
            <div style="font-size: 38px; color: #cbd5e1; margin-bottom: 10px;">
              <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <div style="font-size: 16px; font-weight: 800; color: #334155;">No Booked / Checked-in Rooms Found</div>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px; max-width: 480px; margin-left: auto; margin-right: auto;">
              There are no active guest bookings or check-ins recorded for the selected date range 
              <strong>({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }})</strong>.
            </div>
            <div style="margin-top: 16px; display: flex; justify-content: center; gap: 10px;">
              <button type="button" class="btn-ui-secondary" onclick="setReserveDatesToday()" style="padding: 7px 14px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 12px; font-weight: 700; cursor: pointer;">
                <i class="fa-solid fa-rotate-left"></i> Reset to Today
              </button>
              <button type="button" class="btn-ui-primary" onclick="if(typeof openReservationModal === 'function') openReservationModal(); else openModal('reserve-modal');">
                <i class="fa-solid fa-calendar-plus"></i> New Reservation
              </button>
            </div>
          </div>
        @endforelse

      </div>
    </div>
  </div>

  <!-- Right Sidebar: Statistics & Occupancy Pie Chart -->
  <aside class="pms-stats-sidebar">
    <div class="stats-gold-top">
      <i class="fa-solid fa-crown" style="margin-right: 6px;"></i> SAGAR SONNET
    </div>

    <div class="stats-rows-list">
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-bed" style="color: #be123c; margin-right: 6px;"></i>Booked Rooms</span>
        <span class="val">{{ $stats['total_booked'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-key" style="color: #10b981; margin-right: 6px;"></i>Checked In</span>
        <span class="val">{{ $stats['checked_in'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-hotel" style="color: #f59e0b; margin-right: 6px;"></i>Stay Over</span>
        <span class="val">{{ $stats['stay_over'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-calendar-check" style="color: #6366f1; margin-right: 6px;"></i>Confirmed</span>
        <span class="val">{{ $stats['confirmed'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-user-check" style="color: #06b6d4; margin-right: 6px;"></i>Arrived</span>
        <span class="val">{{ $stats['arrived'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-plane-arrival" style="color: var(--accent-cyan); margin-right: 6px;"></i>Expected Arrival</span>
        <span class="val">{{ $stats['expected_arrival'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-plane-departure" style="color: var(--accent-amber); margin-right: 6px;"></i>Expected Departure</span>
        <span class="val">{{ $stats['expected_departure'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-user-group" style="color: var(--accent-primary); margin-right: 6px;"></i>In-House Pax</span>
        <span class="val">{{ $stats['total_pax'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-percent" style="color: #10b981; margin-right: 6px;"></i>Occupancy</span>
        <span class="val">{{ $stats['occupancy_percentage'] }}%</span>
      </div>
    </div>

    <!-- Occupancy & Status Pie Chart Section -->
    <div class="stats-pie-section">
      <div style="width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid var(--border-subtle);">
        <span style="font-size: 11px; font-weight: 800; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">
          <i class="fa-solid fa-chart-pie" style="color: var(--accent-primary); margin-right: 4px;"></i> Booked Status
        </span>
        <span style="font-size: 11px; font-weight: 800; color: var(--accent-primary); font-family: var(--font-mono);">
          {{ $stats['total_booked'] }}/{{ $stats['total_rooms'] }} Rooms
        </span>
      </div>

      <!-- Perfect Circular Donut Pie Chart -->
      @php
        $pCheckedIn = $stats['percentages']['checked_in'];
        $pStayOver = $stats['percentages']['stay_over'];
        $pConfirmed = $stats['percentages']['confirmed'];
        $pArrived = $stats['percentages']['arrived'];

        $stop1 = $pCheckedIn;
        $stop2 = $stop1 + $pStayOver;
        $stop3 = $stop2 + $pConfirmed;
      @endphp
      <div class="pie-donut-container" style="position: relative; width: 112px; height: 112px; margin: 2px auto 10px; display: flex; align-items: center; justify-content: center;">
        <div style="width: 100%; height: 100%; border-radius: 50%; background: conic-gradient(
            #10b981 0% {{ $stop1 }}%,
            #f59e0b {{ $stop1 }}% {{ $stop2 }}%,
            #6366f1 {{ $stop2 }}% {{ $stop3 }}%,
            #06b6d4 {{ $stop3 }}% 100%
          ); box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);">
        </div>
        <!-- Center Hole for Donut Style -->
        <div style="position: absolute; width: 58px; height: 58px; border-radius: 50%; background: #ffffff; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);">
          <span style="font-size: 14px; font-weight: 900; color: #0f172a; font-family: var(--font-mono); line-height: 1;">{{ $stats['total_booked'] }}</span>
          <span style="font-size: 8px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-top: 2px;">Booked</span>
        </div>
      </div>

      <!-- Status Breakdown Grid (2x2 Clean Stacked Design) -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; width: 100%;">
        <div style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 6px; padding: 5px 8px; display: flex; flex-direction: column; gap: 1px;">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #065f46;">
              <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block;"></span> Checked In
            </span>
            <span style="font-size: 11px; font-weight: 900; color: #059669; font-family: var(--font-mono);">{{ $stats['checked_in'] }}</span>
          </div>
          <div style="font-size: 9px; font-weight: 700; color: #059669; text-align: right; opacity: 0.85;">{{ $stats['percentages']['checked_in'] }}%</div>
        </div>

        <div style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; padding: 5px 8px; display: flex; flex-direction: column; gap: 1px;">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #92400e;">
              <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span> Stay Over
            </span>
            <span style="font-size: 11px; font-weight: 900; color: #d97706; font-family: var(--font-mono);">{{ $stats['stay_over'] }}</span>
          </div>
          <div style="font-size: 9px; font-weight: 700; color: #d97706; text-align: right; opacity: 0.85;">{{ $stats['percentages']['stay_over'] }}%</div>
        </div>

        <div style="background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 6px; padding: 5px 8px; display: flex; flex-direction: column; gap: 1px;">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #3730a3;">
              <span style="width: 6px; height: 6px; border-radius: 50%; background: #6366f1; display: inline-block;"></span> Confirmed
            </span>
            <span style="font-size: 11px; font-weight: 900; color: #4f46e5; font-family: var(--font-mono);">{{ $stats['confirmed'] }}</span>
          </div>
          <div style="font-size: 9px; font-weight: 700; color: #4f46e5; text-align: right; opacity: 0.85;">{{ $stats['percentages']['confirmed'] }}%</div>
        </div>

        <div style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); border-radius: 6px; padding: 5px 8px; display: flex; flex-direction: column; gap: 1px;">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #0e7490;">
              <span style="width: 6px; height: 6px; border-radius: 50%; background: #06b6d4; display: inline-block;"></span> Arrived
            </span>
            <span style="font-size: 11px; font-weight: 900; color: #0891b2; font-family: var(--font-mono);">{{ $stats['arrived'] }}</span>
          </div>
          <div style="font-size: 9px; font-weight: 700; color: #0891b2; text-align: right; opacity: 0.85;">{{ $stats['percentages']['arrived'] }}%</div>
        </div>
      </div>
    </div>
  </aside>

</main>

<!-- Bottom Toolbar -->
<footer class="pms-bottom-bar">
  <div class="legend-chips">
    <div class="legend-chip chip-all {{ in_array($selectedStatus, ['all', '']) ? 'active-chip' : '' }}" onclick="applyReserveStatusFilter('all')" style="cursor: pointer;">
      <div class="chip-lbl">All Booked</div>
      <div class="chip-cnt">{{ $stats['total_booked'] }}</div>
    </div>
    <div class="legend-chip chip-avl {{ $selectedStatus === 'checked in' ? 'active-chip' : '' }}" onclick="applyReserveStatusFilter('checked in')" style="cursor: pointer;">
      <div class="chip-lbl">Checked In</div>
      <div class="chip-cnt">{{ $stats['checked_in'] }}</div>
    </div>
    <div class="legend-chip chip-drt {{ $selectedStatus === 'stay over' ? 'active-chip' : '' }}" onclick="applyReserveStatusFilter('stay over')" style="cursor: pointer;">
      <div class="chip-lbl">Stay Over</div>
      <div class="chip-cnt">{{ $stats['stay_over'] }}</div>
    </div>
    <div class="legend-chip chip-blk {{ $selectedStatus === 'confirmed' ? 'active-chip' : '' }}" onclick="applyReserveStatusFilter('confirmed')" style="cursor: pointer;">
      <div class="chip-lbl">Confirmed</div>
      <div class="chip-cnt">{{ $stats['confirmed'] }}</div>
    </div>
    @if($stats['arrived'] > 0)
      <div class="legend-chip chip-occ {{ $selectedStatus === 'arrived' ? 'active-chip' : '' }}" onclick="applyReserveStatusFilter('arrived')" style="cursor: pointer;">
        <div class="chip-lbl">Arrived</div>
        <div class="chip-cnt">{{ $stats['arrived'] }}</div>
      </div>
    @endif
  </div>

  <form method="GET" action="{{ route('frontoffice.reserve') }}" id="backend-filter-form" class="bottom-actions">
    <input type="hidden" name="from_date" value="{{ $fromDate }}">
    <input type="hidden" name="to_date" value="{{ $toDate }}">
    <input type="hidden" name="status" id="filter-status-val" value="{{ $selectedStatus ?? 'all' }}">

    <button type="button" class="pms-btn-action" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Notification sent to front desk supervisors!', 'info'); else alert('Notification sent to front desk supervisors!');">
      <i class="fa-solid fa-bell" style="color: var(--accent-primary);"></i> Notify
    </button>
    <a href="{{ route('frontoffice.reserve') }}" class="pms-btn-action" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
      <i class="fa-solid fa-rotate" style="color: var(--accent-cyan);"></i> Refresh
    </a>
    
    <select name="type" class="pms-select" id="filter-type" onchange="submitBackendFilter()" title="Filter by Room Type">
      <option value="ALL" {{ ($selectedType ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Types</option>
      @foreach($categories as $cat)
        <option value="{{ strtoupper($cat->name) }}" {{ ($selectedType ?? '') === strtoupper($cat->name) ? 'selected' : '' }}>{{ $cat->name }}</option>
      @endforeach
    </select>

    <select name="floor" class="pms-select" id="filter-floor" onchange="submitBackendFilter()" title="Filter by Floor">
      <option value="ALL" {{ ($selectedFloor ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Floor</option>
      @foreach($floors as $fl)
        <option value="{{ $fl->floor }}" {{ strval($selectedFloor ?? '') === strval($fl->floor) ? 'selected' : '' }}>Floor {{ $fl->floor }}</option>
      @endforeach
    </select>

    <select name="bedding" class="pms-select" id="filter-bedding" onchange="submitBackendFilter()" title="Filter by Bedding Configuration">
      <option value="ALL" {{ ($selectedBedding ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Bedding</option>
      @foreach($beddingConfigs as $bc)
        <option value="{{ $bc->id }}" {{ strval($selectedBedding ?? '') === strval($bc->id) ? 'selected' : '' }}>{{ $bc->name }}</option>
      @endforeach
    </select>

    <select name="pax" class="pms-select" id="filter-pax" onchange="submitBackendFilter()" title="Filter by Pax Capacity">
      <option value="ALL" {{ ($selectedPax ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Pax</option>
      <option value="1" {{ strval($selectedPax ?? '') === '1' ? 'selected' : '' }}>1 Pax</option>
      <option value="2" {{ strval($selectedPax ?? '') === '2' ? 'selected' : '' }}>2 Pax</option>
      <option value="3" {{ strval($selectedPax ?? '') === '3' ? 'selected' : '' }}>3 Pax</option>
      <option value="4" {{ strval($selectedPax ?? '') === '4' ? 'selected' : '' }}>4 Pax</option>
      <option value="6" {{ strval($selectedPax ?? '') === '6' ? 'selected' : '' }}>6 Pax</option>
    </select>

    <select name="amenity" class="pms-select" id="filter-amenity" onchange="submitBackendFilter()" title="Filter by Amenities">
      <option value="ALL" {{ ($selectedAmenity ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Amenities</option>
      @foreach($amenitiesList as $amn)
        <option value="{{ $amn->id }}" {{ strval($selectedAmenity ?? '') === strval($amn->id) ? 'selected' : '' }}>{{ $amn->name }}</option>
      @endforeach
    </select>

    <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 11px; color: var(--text-secondary);">
      <span><i class="fa-solid fa-search" style="color: var(--accent-primary);"></i></span>
      <input type="text" name="search" class="pms-input" id="filter-search" placeholder="Room No. / Guest" value="{{ $searchQuery ?? '' }}" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); submitBackendFilter(); }">
    </div>
  </form>
</footer>
@endsection

@push('scripts')
<script>
  function setReserveDatesToday() {
    const today = '{{ $today }}';
    const fromInput = document.getElementById('reserve-from-date');
    const toInput = document.getElementById('reserve-to-date');
    if (fromInput) fromInput.value = today;
    if (toInput) toInput.value = today;
    document.getElementById('reserve-date-filter-form').submit();
  }

  function applyReserveStatusFilter(statusVal) {
    const form = document.getElementById('backend-filter-form');
    const input = document.getElementById('filter-status-val');
    if (form && input) {
      input.value = statusVal;
      form.submit();
    }
  }

  function submitBackendFilter() {
    const form = document.getElementById('backend-filter-form');
    if (form) form.submit();
  }
</script>
@endpush
