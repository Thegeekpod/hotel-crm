@extends('frontoffice.layouts.app')

@section('title', 'Front Office - Hotel Sagar Sonnet CRM / PMS')

@section('content')
<!-- Main Viewport: Room Rack Grid & Right Sidebar -->
<main class="flex-1 p-3 sm:p-4 lg:p-5 flex flex-col lg:flex-row items-start gap-4 pms-main-viewport">
  
  <!-- Left: All Rooms Rack Panel -->
  <div class="flex-1 bg-white border border-slate-200 rounded-2xl shadow-xs flex flex-col overflow-hidden pms-rack-panel">
    <div class="p-3.5 sm:px-5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 bg-slate-50/70 rack-title-bar">
      <div>
        <h2 class="text-sm sm:text-base font-black text-slate-900 flex items-center gap-2 m-0">
          <i class="fa-solid fa-th-large text-indigo-600"></i> All Rooms
        </h2>
        <span class="text-[11px] text-slate-500 font-bold">Hotel Sagar Sonnet • {{ $stats['total'] }} Total Rooms</span>
      </div>

      <!-- Real-Time Date Filter Navigator & Search -->
      <div class="flex items-center gap-2 pms-date-filter-group flex-wrap">
        <!-- Redesigned Search Input (Moved before Date Filter) -->
        <div class="relative flex items-center">
          <i class="fa-solid fa-magnifying-glass absolute left-3 text-slate-400 text-xs pointer-events-none"></i>
          <input type="text" 
                 id="top-room-search" 
                 placeholder="Search room no, guest..." 
                 value="{{ $searchQuery ?? '' }}" 
                 oninput="onLiveRoomSearch(this.value)" 
                 onkeydown="if(event.key === 'Enter'){ event.preventDefault(); submitTopRoomSearch(this.value); }" 
                 class="h-8.5 pl-8.5 pr-8 text-xs font-semibold rounded-xl border border-slate-200 bg-white hover:border-slate-300 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/15 focus:outline-none transition-all shadow-2xs w-44 sm:w-56 text-slate-800 placeholder:text-slate-400">
          <button type="button" 
                  id="top-search-clear-btn" 
                  onclick="clearTopRoomSearch()" 
                  class="absolute right-2.5 text-slate-400 hover:text-slate-600 text-xs cursor-pointer {{ empty($searchQuery) ? 'hidden' : '' }}" 
                  title="Clear search">
            <i class="fa-solid fa-circle-xmark"></i>
          </button>
        </div>

        <div class="flex items-center bg-white border border-slate-300 rounded-xl px-1 py-0.5 shadow-2xs">
          <button type="button" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-all btn-date-step" onclick="changeDateFilter(-1)" title="Previous Day ({{ \Carbon\Carbon::parse($prevDate)->format('d M') }})">
            <i class="fa-solid fa-chevron-left text-[11px]"></i>
          </button>
          
          <button type="button" class="px-2.5 py-1 text-xs font-black rounded-lg transition-all btn-date-today {{ $isToday ? 'bg-emerald-100 text-emerald-800' : 'text-slate-600 hover:bg-slate-100' }}" onclick="setDateFilterToday()" title="Jump to Today">
            Today
          </button>
          
          <div class="flex items-center gap-1.5 px-2 border-x border-slate-200">
            <i class="fa-regular fa-calendar-days text-indigo-500 text-xs"></i>
            <input type="date" id="pms-calendar-input" value="{{ $selectedDate }}" onchange="onDateFilterChanged(this.value)" class="border-none bg-transparent font-bold text-xs text-slate-800 cursor-pointer outline-none font-sans">
          </div>

          <button type="button" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-slate-800 transition-all btn-date-step" onclick="changeDateFilter(1)" title="Next Day ({{ \Carbon\Carbon::parse($nextDate)->format('d M') }})">
            <i class="fa-solid fa-chevron-right text-[11px]"></i>
          </button>
        </div>

        @if($isToday)
          <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 font-extrabold text-[11px] px-2.5 py-1 rounded-xl border border-emerald-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Live Today
          </span>
        @else
          <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 font-extrabold text-[11px] px-2.5 py-1 rounded-xl border border-indigo-200">
            <i class="fa-solid fa-calendar-day text-[11px]"></i>
            {{ \Carbon\Carbon::parse($selectedDate)->format('D, d M Y') }}
          </span>
        @endif
      </div>
    </div>

    <div class="flex-1 overflow-y-auto p-4 bg-slate-50/50 rack-grid-viewport">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-8 gap-2.5 rack-rooms-container" id="rooms-grid">
        
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
               data-room-json="{!! $roomJsonData !!}"
               data-room="{{ $rm['room'] }}" 
               data-room-id="{{ $rm['id'] ?? '' }}"
               data-floor="{{ $rm['floor'] }}" 
               data-type="{{ $rm['type'] }}" 
               data-category="{{ $rm['category'] }}"
               data-guest="{{ $rm['guest']['name'] ?? '' }}"
               data-status="{{ $rm['status'] }}"
               data-cleaning="{{ $rm['cleaning'] }}"
               data-operational="{{ $rm['operational_status'] ?? '' }}"
               data-operational-id="{{ $rm['operational_status_id'] ?? '' }}"
               data-housekeeping="{{ $rm['housekeeping_status'] ?? '' }}"
               data-housekeeping-id="{{ $rm['housekeeping_status_id'] ?? '' }}">
            <div class="rack-card-top flex justify-between items-baseline">
              <div class="rack-room-num">{{ $rm['room'] }}</div>
              <div class="rack-room-type truncate max-w-[80px]" title="{{ $rm['type'] }}">{{ $rm['type'] }}</div>
            </div>
            <div class="rack-card-mid my-1 min-h-[1.2rem]">
              @if(!empty($rm['guest']['name']))
                <div class="rack-guest-name truncate" title="{{ $rm['guest']['name'] }}">{{ $rm['guest']['name'] }}</div>
              @elseif($rm['status'] === 'blocked')
                <div class="rack-guest-name text-indigo-600" title="Blocked">Blocked</div>
              @endif
            </div>
            <div class="rack-card-bot flex justify-end">
              <div class="rack-badge" onclick="openRoomStatusPopover(event, this)" title="Click to update Housekeeping / Operational status">
                <span class="rack-badge-label">{{ $badgeText }}</span>
                <i class="fa-solid fa-chevron-down rack-badge-arrow text-[8px] opacity-70"></i>
              </div>
            </div>
          </div>
        @endforeach

      </div>
    </div>
  </div>

  <!-- Right Sidebar: Statistics & Occupancy Pie Chart (Sticky on Desktop) -->
  <aside class="w-full lg:w-72 bg-white border border-slate-200 rounded-2xl shadow-xs p-4 flex flex-col gap-3 shrink-0 lg:sticky lg:top-18 lg:self-start max-h-[calc(100vh-5.5rem)] overflow-y-auto scrollbar-none z-20 pms-stats-sidebar">
    <div class="bg-linear-to-r from-amber-500 to-amber-600 text-slate-950 font-black text-xs uppercase tracking-wider px-3 py-2 rounded-xl flex items-center justify-center gap-1.5 shadow-xs stats-gold-top">
      <i class="fa-solid fa-crown text-amber-100"></i> SAGAR SONNET
    </div>

    <div class="flex flex-col gap-1.5 stats-rows-list">
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-bed text-rose-500"></i>Occupied</span>
        <span class="font-mono text-slate-900 val">{{ $stats['occupied'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-plane-arrival text-cyan-500"></i>Expected Arrival</span>
        <span class="font-mono text-slate-900 val">{{ $stats['expected_arrival'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-plane-departure text-amber-500"></i>Expected Departure</span>
        <span class="font-mono text-slate-900 val">{{ $stats['expected_departure'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-tags text-emerald-500"></i>Rooms to Sale</span>
        <span class="font-mono text-slate-900 val">{{ $stats['rooms_to_sale'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-arrow-right-to-bracket text-emerald-600"></i>Today's In</span>
        <span class="font-mono text-slate-900 val">{{ $stats['checked_in'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-arrow-right-from-bracket text-rose-500"></i>Today's Out</span>
        <span class="font-mono text-slate-900 val">{{ $stats['checked_out'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-user-group text-indigo-500"></i>Total Pax</span>
        <span class="font-mono text-slate-900 val">{{ $stats['total_pax'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-broom text-amber-500"></i>Cleaned</span>
        <span class="font-mono text-slate-900 val">{{ $stats['cleaned_ratio'] }}</span>
      </div>
    </div>

    <!-- Occupancy & Status Pie Chart Section -->
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col items-center stats-pie-section">
      <div class="w-full flex justify-between items-center pb-2 mb-2 border-b border-slate-200">
        <span class="text-[10px] font-black uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
          <i class="fa-solid fa-chart-pie text-indigo-500"></i> Room Occupancy
        </span>
        <span class="text-xs font-black text-indigo-700 font-mono">
          {{ $stats['occupied'] }}/{{ $stats['total'] }}
        </span>
      </div>

      <!-- Donut Chart -->
      <div class="relative w-28 h-28 my-1 flex items-center justify-center pie-donut-container">
        <div class="w-full h-full rounded-full shadow-xs" style="background: conic-gradient(
            #e11d48 0% {{ $stats['percentages']['occupied'] }}%,
            #10b981 {{ $stats['percentages']['occupied'] }}% {{ $stats['percentages']['occupied'] + $stats['percentages']['available'] }}%,
            #f59e0b {{ $stats['percentages']['occupied'] + $stats['percentages']['available'] }}% {{ $stats['percentages']['occupied'] + $stats['percentages']['available'] + $stats['percentages']['dirty'] }}%,
            #6366f1 {{ $stats['percentages']['occupied'] + $stats['percentages']['available'] + $stats['percentages']['dirty'] }}% 100%
          );">
        </div>
        <!-- Center Hole -->
        <div class="absolute w-14 h-14 rounded-full bg-white flex flex-col items-center justify-center shadow-xs">
          <span class="text-sm font-black text-slate-900 font-mono leading-none">{{ $stats['percentages']['occupied'] }}%</span>
          <span class="text-[8px] font-extrabold text-slate-400 uppercase tracking-tight mt-0.5">Occupied</span>
        </div>
      </div>

      <!-- Breakdown Grid -->
      <div class="grid grid-cols-2 gap-1.5 w-full mt-2">
        <div class="bg-rose-50/80 border border-rose-200 rounded-lg p-1.5 flex flex-col">
          <div class="flex items-center justify-between text-[10px] font-bold text-rose-800">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Occ</span>
            <span class="font-mono font-black text-rose-700">{{ $stats['occupied'] }}</span>
          </div>
          <div class="text-[9px] font-bold text-rose-600 text-right opacity-80">{{ $stats['percentages']['occupied'] }}%</div>
        </div>

        <div class="bg-emerald-50/80 border border-emerald-200 rounded-lg p-1.5 flex flex-col">
          <div class="flex items-center justify-between text-[10px] font-bold text-emerald-800">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Clean</span>
            <span class="font-mono font-black text-emerald-700">{{ $stats['available'] }}</span>
          </div>
          <div class="text-[9px] font-bold text-emerald-600 text-right opacity-80">{{ $stats['percentages']['available'] }}%</div>
        </div>

        <div class="bg-amber-50/80 border border-amber-200 rounded-lg p-1.5 flex flex-col">
          <div class="flex items-center justify-between text-[10px] font-bold text-amber-800">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Dirty</span>
            <span class="font-mono font-black text-amber-700">{{ $stats['dirty'] }}</span>
          </div>
          <div class="text-[9px] font-bold text-amber-600 text-right opacity-80">{{ $stats['percentages']['dirty'] }}%</div>
        </div>

        <div class="bg-indigo-50/80 border border-indigo-200 rounded-lg p-1.5 flex flex-col">
          <div class="flex items-center justify-between text-[10px] font-bold text-indigo-800">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Block</span>
            <span class="font-mono font-black text-indigo-700">{{ $stats['blocked'] }}</span>
          </div>
          <div class="text-[9px] font-bold text-indigo-600 text-right opacity-80">{{ $stats['percentages']['blocked'] }}%</div>
        </div>
      </div>
    </div>
  </aside>

</main>

<!-- Bottom Toolbar (Sticky, Single Line, No Wrapping, Same Height) -->
<footer class="bg-white/95 backdrop-blur-md border-t border-slate-200 px-3 sm:px-4 lg:px-5 py-2 flex items-center justify-between gap-3 shadow-sm z-30 sticky bottom-0 shrink-0 overflow-x-auto scrollbar-none flex-nowrap pms-bottom-bar">
  @php
    $chipBase = "h-8.5 px-3 rounded-lg border text-xs font-bold cursor-pointer transition-all inline-flex items-center gap-1.5 whitespace-nowrap leading-none legend-chip";
  @endphp
  <div class="flex items-center gap-1.5 legend-chips shrink-0 flex-nowrap">
    <div class="{{ $chipBase }} {{ $selectedStatus === 'occupied' ? 'bg-rose-50 border-rose-300 text-rose-700 ring-2 ring-rose-400 active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-occ" onclick="applyBackendFilter('occupied')">
      <span class="chip-lbl">Reserved</span>
      <span class="h-5 px-1.5 rounded-md bg-white border font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['occupied'] }}</span>
    </div>
    <div class="{{ $chipBase }} {{ $selectedStatus === 'blocked' ? 'bg-indigo-50 border-indigo-300 text-indigo-700 ring-2 ring-indigo-400 active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-blk" onclick="applyBackendFilter('blocked')">
      <span class="chip-lbl">Blocked</span>
      <span class="h-5 px-1.5 rounded-md bg-white border font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['blocked'] }}</span>
    </div>
    <div class="{{ $chipBase }} {{ $selectedStatus === 'vacant' ? 'bg-cyan-50 border-cyan-300 text-cyan-700 ring-2 ring-cyan-400 active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-vac" onclick="applyBackendFilter('vacant')">
      <span class="chip-lbl">Vacant</span>
      <span class="h-5 px-1.5 rounded-md bg-white border font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['vacant'] }}</span>
    </div>
    <div class="{{ $chipBase }} {{ $selectedStatus === 'dirty' ? 'bg-amber-50 border-amber-300 text-amber-700 ring-2 ring-amber-400 active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-drt" onclick="applyBackendFilter('dirty')">
      <span class="chip-lbl">Dirty</span>
      <span class="h-5 px-1.5 rounded-md bg-white border font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['dirty'] }}</span>
    </div>
    <div class="{{ $chipBase }} {{ $selectedStatus === 'available' ? 'bg-emerald-50 border-emerald-300 text-emerald-700 ring-2 ring-emerald-400 active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-avl" onclick="applyBackendFilter('available')">
      <span class="chip-lbl">Available</span>
      <span class="h-5 px-1.5 rounded-md bg-white border font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['available'] }}</span>
    </div>
    <div class="{{ $chipBase }} {{ in_array($selectedStatus, ['all', '']) ? 'bg-slate-900 border-slate-900 text-white active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-all" onclick="applyBackendFilter('all')">
      <span class="chip-lbl">All</span>
      <span class="h-5 px-1.5 rounded-md bg-slate-800 text-white font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['total'] }}</span>
    </div>
  </div>

  <form method="GET" action="{{ route('frontoffice.dashboard') }}" id="backend-filter-form" class="flex items-center gap-1.5 shrink-0 flex-nowrap bottom-actions">
    <input type="hidden" name="date" id="filter-date-val" value="{{ $selectedDate }}">
    <input type="hidden" name="status" id="filter-status-val" value="{{ $selectedStatus ?? 'all' }}">
    <input type="hidden" name="search" id="filter-search-val" value="{{ $searchQuery ?? '' }}">

    <button type="button" class="btn-ui-secondary h-8.5 px-3 text-xs inline-flex items-center gap-1.5 font-bold rounded-lg border leading-none whitespace-nowrap cursor-pointer pms-btn-action" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Notification sent to floor supervisors!', 'info'); else alert('Notification sent to floor supervisors!');">
      <i class="fa-solid fa-bell text-indigo-600"></i> Notify
    </button>
    <a href="{{ route('frontoffice.dashboard') }}" class="btn-ui-secondary h-8.5 px-3 text-xs inline-flex items-center gap-1.5 font-bold rounded-lg border leading-none whitespace-nowrap cursor-pointer pms-btn-action">
      <i class="fa-solid fa-rotate text-cyan-600"></i> Refresh
    </a>
    
    <select name="type" class="pms-select h-8.5 text-xs px-2.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-type" onchange="submitBackendFilter()" title="Filter by Room Type">
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

    <select name="floor" class="pms-select h-8.5 text-xs px-2.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-floor" onchange="submitBackendFilter()" title="Filter by Floor">
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

    <select name="bedding" class="pms-select h-8.5 text-xs px-2.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-bedding" onchange="submitBackendFilter()" title="Filter by Bedding Configuration">
      <option value="ALL" {{ ($selectedBedding ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Bedding</option>
      @foreach($beddingConfigs as $bc)
        <option value="{{ $bc->id }}" {{ strval($selectedBedding ?? '') === strval($bc->id) ? 'selected' : '' }}>{{ $bc->name }}</option>
      @endforeach
    </select>

    <select name="pax" class="pms-select h-8.5 text-xs px-2.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-pax" onchange="submitBackendFilter()" title="Filter by Pax Capacity">
      <option value="ALL" {{ ($selectedPax ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Pax</option>
      <option value="1" {{ strval($selectedPax ?? '') === '1' ? 'selected' : '' }}>1 Pax</option>
      <option value="2" {{ strval($selectedPax ?? '') === '2' ? 'selected' : '' }}>2 Pax</option>
      <option value="3" {{ strval($selectedPax ?? '') === '3' ? 'selected' : '' }}>3 Pax</option>
      <option value="4" {{ strval($selectedPax ?? '') === '4' ? 'selected' : '' }}>4 Pax</option>
      <option value="6" {{ strval($selectedPax ?? '') === '6' ? 'selected' : '' }}>6 Pax</option>
    </select>

    <select name="amenity" class="pms-select h-8.5 text-xs px-2.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-amenity" onchange="submitBackendFilter()" title="Filter by Amenities">
      <option value="ALL" {{ ($selectedAmenity ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Amenities</option>
      @foreach($amenitiesList as $amn)
        <option value="{{ $amn->id }}" {{ strval($selectedAmenity ?? '') === strval($amn->id) ? 'selected' : '' }}>{{ $amn->name }}</option>
      @endforeach
    </select>
  </form>
</footer>
@endsection
