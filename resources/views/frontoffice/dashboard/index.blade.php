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
            $guestJson = !empty($rm['guest']) ? htmlspecialchars(json_encode($rm['guest']), ENT_QUOTES, 'UTF-8') : 'null';
          @endphp

          <div class="rack-card {{ $cardClass }}" 
               onclick="openRoomDetails('{{ $rm['room'] }}', '{{ $rm['type'] }}', '{{ $rm['cleaning'] }}', '{{ ucfirst($rm['status']) }}', {{ $rm['rate'] }}, {{ $guestJson }}, '{{ $rm['operational_status'] ?? '' }}', '{{ $rm['housekeeping_status'] ?? '' }}')" 
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

    <!-- Pie Chart Section -->
    <div class="stats-pie-section">
      <div class="pie-svg-box">
        <svg viewBox="0 0 36 36" class="pie-svg">
          <circle cx="18" cy="18" r="15.91549430918954" fill="transparent" stroke="#10b981" stroke-width="14" stroke-dasharray="{{ $stats['percentages']['available'] }} {{ 100 - $stats['percentages']['available'] }}" stroke-dashoffset="0"></circle>
          <circle cx="18" cy="18" r="15.91549430918954" fill="transparent" stroke="#f43f5e" stroke-width="14" stroke-dasharray="{{ $stats['percentages']['occupied'] }} {{ 100 - $stats['percentages']['occupied'] }}" stroke-dashoffset="-{{ $stats['percentages']['available'] }}"></circle>
          <circle cx="18" cy="18" r="15.91549430918954" fill="transparent" stroke="#f59e0b" stroke-width="14" stroke-dasharray="{{ $stats['percentages']['dirty'] }} {{ 100 - $stats['percentages']['dirty'] }}" stroke-dashoffset="-{{ $stats['percentages']['available'] + $stats['percentages']['occupied'] }}"></circle>
          <circle cx="18" cy="18" r="15.91549430918954" fill="transparent" stroke="#6366f1" stroke-width="14" stroke-dasharray="{{ $stats['percentages']['blocked'] }} {{ 100 - $stats['percentages']['blocked'] }}" stroke-dashoffset="-{{ $stats['percentages']['available'] + $stats['percentages']['occupied'] + $stats['percentages']['dirty'] }}"></circle>
        </svg>
      </div>
      <div class="pie-legend-bar">
        <div class="p-red">{{ $stats['percentages']['occupied'] }}%</div>
        <div class="p-green">{{ $stats['percentages']['available'] }}%</div>
        <div class="p-navy">{{ $stats['percentages']['blocked'] }}%</div>
        <div class="p-yellow">{{ $stats['percentages']['dirty'] }}%</div>
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
    
    <select name="type" class="pms-select" id="filter-type" onchange="submitBackendFilter()">
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

    <select name="floor" class="pms-select" id="filter-floor" onchange="submitBackendFilter()">
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

    <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 11px; color: var(--text-secondary);">
      <span><i class="fa-solid fa-search" style="color: var(--accent-primary);"></i></span>
      <input type="text" name="search" class="pms-input" id="filter-search" placeholder="Room No. / Guest" value="{{ $searchQuery ?? '' }}" onkeydown="if(event.key === 'Enter'){ event.preventDefault(); submitBackendFilter(); }">
    </div>
  </form>
</footer>
@endsection
