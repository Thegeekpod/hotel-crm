@extends('frontoffice.layouts.app')

@section('title', 'Front Office - Hotel Sagar Sonnet CRM / PMS')

@section('content')
<!-- Main Viewport: Room Rack Grid & Right Sidebar -->
<main class="pms-main-viewport">
  
  <!-- Left: All Rooms Rack Panel -->
  <div class="pms-rack-panel">
    <div class="rack-title-bar">
      <h2><i class="fa-solid fa-th-large" style="color: var(--accent-primary);"></i> All Rooms</h2>
      <span style="font-size: 11px; color: var(--text-muted); font-weight: 600;">Hotel Sagar Sonnet • {{ $stats['total'] }} Total Rooms</span>
    </div>

    <div class="rack-grid-viewport">
      <div class="rack-rooms-container" id="rooms-grid">
        
        @foreach($rackRooms as $rm)
          @php
            $cardClass = 'c-available';
            $badgeText = $rm['cleaning'];
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
              @if(!empty($rm['guest']['name']))
                <div class="rack-guest-name" title="{{ $rm['guest']['name'] }}">{{ $rm['guest']['name'] }}</div>
              @elseif($rm['status'] === 'blocked')
                <div class="rack-guest-name" title="Blocked">Blocked</div>
              @endif
            </div>
            <div class="rack-card-bot">
              <div class="rack-badge">{{ $badgeText }}</div>
            </div>
          </div>
        @endforeach

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
        <span class="lbl"><i class="fa-solid fa-bed" style="color: var(--room-occupied); margin-right: 6px;"></i>Occupied Rooms</span>
        <span class="val">{{ $stats['occupied'] }}</span>
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
        <span class="lbl"><i class="fa-solid fa-tags" style="color: var(--room-available); margin-right: 6px;"></i>Rooms to Sale</span>
        <span class="val">{{ $stats['rooms_to_sale'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-arrow-right-to-bracket" style="color: #10b981; margin-right: 6px;"></i>Today's Checked In</span>
        <span class="val">{{ $stats['checked_in'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-arrow-right-from-bracket" style="color: #f43f5e; margin-right: 6px;"></i>Today's Checked Out</span>
        <span class="val">{{ $stats['checked_out'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-user-group" style="color: var(--accent-primary); margin-right: 6px;"></i>Total Pax</span>
        <span class="val">{{ $stats['total_pax'] }}</span>
      </div>
      <div class="stat-strip">
        <span class="lbl"><i class="fa-solid fa-broom" style="color: var(--accent-amber); margin-right: 6px;"></i>Cleaned</span>
        <span class="val">{{ $stats['cleaned_ratio'] }}</span>
      </div>
    </div>

    <!-- Occupancy & Status Pie Chart Section -->
    <div class="stats-pie-section">
      <div style="width: 100%; display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid var(--border-subtle);">
        <span style="font-size: 11px; font-weight: 800; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">
          <i class="fa-solid fa-chart-pie" style="color: var(--accent-primary); margin-right: 4px;"></i> Room Occupancy
        </span>
        <span style="font-size: 11px; font-weight: 800; color: var(--accent-primary); font-family: var(--font-mono);">
          {{ $stats['occupied'] }}/{{ $stats['total'] }}
        </span>
      </div>

      <!-- Perfect Circular Donut Pie Chart -->
      <div class="pie-donut-container" style="position: relative; width: 112px; height: 112px; margin: 2px auto 10px; display: flex; align-items: center; justify-content: center;">
        <div style="width: 100%; height: 100%; border-radius: 50%; background: conic-gradient(
            #e11d48 0% {{ $stats['percentages']['occupied'] }}%,
            #10b981 {{ $stats['percentages']['occupied'] }}% {{ $stats['percentages']['occupied'] + $stats['percentages']['available'] }}%,
            #f59e0b {{ $stats['percentages']['occupied'] + $stats['percentages']['available'] }}% {{ $stats['percentages']['occupied'] + $stats['percentages']['available'] + $stats['percentages']['dirty'] }}%,
            #6366f1 {{ $stats['percentages']['occupied'] + $stats['percentages']['available'] + $stats['percentages']['dirty'] }}% 100%
          ); box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);">
        </div>
        <!-- Center Hole for Donut Style -->
        <div style="position: absolute; width: 58px; height: 58px; border-radius: 50%; background: #ffffff; display: flex; flex-direction: column; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);">
          <span style="font-size: 14px; font-weight: 900; color: #0f172a; font-family: var(--font-mono); line-height: 1;">{{ $stats['percentages']['occupied'] }}%</span>
          <span style="font-size: 8px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-top: 2px;">Occupied</span>
        </div>
      </div>

      <!-- Status Breakdown Grid (2x2 Clean Stacked Design) -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px; width: 100%;">
        <div style="background: rgba(225, 29, 72, 0.05); border: 1px solid rgba(225, 29, 72, 0.2); border-radius: 6px; padding: 5px 8px; display: flex; flex-direction: column; gap: 1px;">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #9f1239;">
              <span style="width: 6px; height: 6px; border-radius: 50%; background: #e11d48; display: inline-block;"></span> Occupied
            </span>
            <span style="font-size: 11px; font-weight: 900; color: #e11d48; font-family: var(--font-mono);">{{ $stats['occupied'] }}</span>
          </div>
          <div style="font-size: 9px; font-weight: 700; color: #e11d48; text-align: right; opacity: 0.85;">{{ $stats['percentages']['occupied'] }}%</div>
        </div>

        <div style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 6px; padding: 5px 8px; display: flex; flex-direction: column; gap: 1px;">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #065f46;">
              <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block;"></span> Cleaned
            </span>
            <span style="font-size: 11px; font-weight: 900; color: #059669; font-family: var(--font-mono);">{{ $stats['available'] }}</span>
          </div>
          <div style="font-size: 9px; font-weight: 700; color: #059669; text-align: right; opacity: 0.85;">{{ $stats['percentages']['available'] }}%</div>
        </div>

        <div style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.2); border-radius: 6px; padding: 5px 8px; display: flex; flex-direction: column; gap: 1px;">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #92400e;">
              <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span> Dirty
            </span>
            <span style="font-size: 11px; font-weight: 900; color: #d97706; font-family: var(--font-mono);">{{ $stats['dirty'] }}</span>
          </div>
          <div style="font-size: 9px; font-weight: 700; color: #d97706; text-align: right; opacity: 0.85;">{{ $stats['percentages']['dirty'] }}%</div>
        </div>

        <div style="background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.2); border-radius: 6px; padding: 5px 8px; display: flex; flex-direction: column; gap: 1px;">
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <span style="display: flex; align-items: center; gap: 4px; font-size: 10px; font-weight: 700; color: #3730a3;">
              <span style="width: 6px; height: 6px; border-radius: 50%; background: #6366f1; display: inline-block;"></span> Blocked
            </span>
            <span style="font-size: 11px; font-weight: 900; color: #4f46e5; font-family: var(--font-mono);">{{ $stats['blocked'] }}</span>
          </div>
          <div style="font-size: 9px; font-weight: 700; color: #4f46e5; text-align: right; opacity: 0.85;">{{ $stats['percentages']['blocked'] }}%</div>
        </div>
      </div>
    </div>
  </aside>

</main>

<!-- Bottom Toolbar -->
<footer class="pms-bottom-bar">
  <div class="legend-chips">
    <div class="legend-chip chip-occ {{ $selectedStatus === 'occupied' ? 'active-chip' : '' }}" onclick="applyBackendFilter('occupied')" style="cursor: pointer;">
      <div class="chip-lbl">Occupied</div>
      <div class="chip-cnt">{{ $stats['occupied'] }}</div>
    </div>
    <div class="legend-chip chip-blk {{ $selectedStatus === 'blocked' ? 'active-chip' : '' }}" onclick="applyBackendFilter('blocked')" style="cursor: pointer;">
      <div class="chip-lbl">Blocked</div>
      <div class="chip-cnt">{{ $stats['blocked'] }}</div>
    </div>
    <div class="legend-chip chip-vac {{ $selectedStatus === 'vacant' ? 'active-chip' : '' }}" onclick="applyBackendFilter('vacant')" style="cursor: pointer;">
      <div class="chip-lbl">Vacant</div>
      <div class="chip-cnt">{{ $stats['vacant'] }}</div>
    </div>
    <div class="legend-chip chip-drt {{ $selectedStatus === 'dirty' ? 'active-chip' : '' }}" onclick="applyBackendFilter('dirty')" style="cursor: pointer;">
      <div class="chip-lbl">Dirty</div>
      <div class="chip-cnt">{{ $stats['dirty'] }}</div>
    </div>
    <div class="legend-chip chip-avl {{ $selectedStatus === 'available' ? 'active-chip' : '' }}" onclick="applyBackendFilter('available')" style="cursor: pointer;">
      <div class="chip-lbl">Available</div>
      <div class="chip-cnt">{{ $stats['available'] }}</div>
    </div>
    <div class="legend-chip chip-all {{ in_array($selectedStatus, ['all', '']) ? 'active-chip' : '' }}" onclick="applyBackendFilter('all')" style="cursor: pointer;">
      <div class="chip-lbl">All</div>
      <div class="chip-cnt">{{ $stats['total'] }}</div>
    </div>
  </div>

  <form method="GET" action="{{ route('frontoffice.dashboard') }}" id="backend-filter-form" class="bottom-actions">
    <input type="hidden" name="status" id="filter-status-val" value="{{ $selectedStatus ?? 'all' }}">

    <button type="button" class="pms-btn-action" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Notification sent to floor supervisors!', 'info'); else alert('Notification sent to floor supervisors!');">
      <i class="fa-solid fa-bell" style="color: var(--accent-primary);"></i> Notify
    </button>
    <a href="{{ route('frontoffice.dashboard') }}" class="pms-btn-action" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
      <i class="fa-solid fa-rotate" style="color: var(--accent-cyan);"></i> Refresh
    </a>
    
    <select name="type" class="pms-select" id="filter-type" onchange="submitBackendFilter()" title="Filter by Room Type">
      <option value="ALL" {{ ($selectedType ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Types</option>
      @foreach($categories as $cat)
        <option value="{{ strtoupper($cat->name) }}" {{ ($selectedType ?? '') === strtoupper($cat->name) ? 'selected' : '' }}>{{ $cat->name }}</option>
      @endforeach
      @if($categories->isEmpty())
        <option value="DELUXE" {{ ($selectedType ?? '') === 'DELUXE' ? 'selected' : '' }}>Deluxe</option>
        <option value="SUPER DELUXE" {{ ($selectedType ?? '') === 'SUPER DELUXE' ? 'selected' : '' }}>Super Deluxe</option>
        <option value="SUITE" {{ ($selectedType ?? '') === 'SUITE' ? 'selected' : '' }}>Suite</option>
        <option value="EXECUTIVE" {{ ($selectedType ?? '') === 'EXECUTIVE' ? 'selected' : '' }}>Executive</option>
      @endif
    </select>

    <select name="floor" class="pms-select" id="filter-floor" onchange="submitBackendFilter()" title="Filter by Floor">
      <option value="ALL" {{ ($selectedFloor ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Floor</option>
      @foreach($floors as $fl)
        <option value="{{ $fl->floor }}" {{ strval($selectedFloor ?? '') === strval($fl->floor) ? 'selected' : '' }}>Floor {{ $fl->floor }}</option>
      @endforeach
      @if($floors->isEmpty())
        <option value="1" {{ strval($selectedFloor ?? '') === '1' ? 'selected' : '' }}>Floor 1</option>
        <option value="2" {{ strval($selectedFloor ?? '') === '2' ? 'selected' : '' }}>Floor 2</option>
        <option value="3" {{ strval($selectedFloor ?? '') === '3' ? 'selected' : '' }}>Floor 3</option>
        <option value="4" {{ strval($selectedFloor ?? '') === '4' ? 'selected' : '' }}>Floor 4</option>
        <option value="5" {{ strval($selectedFloor ?? '') === '5' ? 'selected' : '' }}>Floor 5</option>
      @endif
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
