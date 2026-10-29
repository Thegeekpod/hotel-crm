@extends('frontoffice.layouts.app')

@section('title', 'Booked & Reserved Rooms - Hotel Sagar Sonnet CRM / PMS')

@section('content')
<!-- Main Viewport: Booked & Reserved Rooms Rack Grid & Right Statistics -->
<main class="flex-1 p-3 sm:p-4 lg:p-5 flex flex-col lg:flex-row items-start gap-4 pms-main-viewport">
  
  <!-- Left: Reserved Rooms Rack Panel -->
  <div class="flex-1 bg-white border border-slate-200 rounded-2xl shadow-xs flex flex-col overflow-hidden pms-rack-panel">
    <div class="p-3.5 sm:px-5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 bg-slate-50/70 rack-title-bar">
      <div>
        <h2 class="text-sm sm:text-base font-black text-slate-900 flex items-center gap-2 m-0">
          <i class="fa-solid fa-calendar-check text-purple-600"></i> Booked & Reserved Rooms
        </h2>
        <span class="text-[11px] text-slate-500 font-bold">
          Hotel Sagar Sonnet • {{ $stats['total_booked'] }} Active Booked Rooms (Check-ins & Stay-overs)
        </span>
      </div>

      <!-- Real-Time From & To Date Filter Navigator -->
      <div class="flex items-center gap-2 flex-wrap pms-date-filter-group">
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

        <form method="GET" action="{{ route('frontoffice.reserve') }}" id="reserve-date-filter-form" class="flex items-center gap-2 flex-wrap m-0">
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

          <div class="flex items-center bg-white border border-slate-300 rounded-xl px-2.5 py-1 shadow-2xs gap-2">
            <label for="reserve-from-date" class="text-[10px] font-black uppercase tracking-wider text-slate-500 flex items-center gap-1.5 whitespace-nowrap">
              <i class="fa-regular fa-calendar-days text-indigo-500"></i> From:
            </label>
            <input type="date" 
                   name="from_date" 
                   id="reserve-from-date" 
                   class="border-none bg-transparent font-bold text-xs text-slate-800 cursor-pointer outline-none font-sans" 
                   value="{{ $fromDate }}" 
                   onchange="this.form.submit()" 
                   title="Check-in / Reservation From Date">

            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">To:</span>
            <input type="date" 
                   name="to_date" 
                   id="reserve-to-date" 
                   class="border-none bg-transparent font-bold text-xs text-slate-800 cursor-pointer outline-none font-sans" 
                   value="{{ $toDate }}" 
                   onchange="this.form.submit()" 
                   title="Check-in / Reservation To Date">
          </div>

          <button type="button" 
                  onclick="setReserveDatesToday()" 
                  title="Reset date filter to today" 
                  class="px-2.5 py-1.5 text-xs font-bold rounded-xl transition-all border cursor-pointer {{ $isToday ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50' }}">
            <i class="fa-solid fa-rotate-left"></i> Today
          </button>

          @if($isToday)
            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 font-extrabold text-[11px] px-2.5 py-1.5 rounded-xl border border-emerald-200 whitespace-nowrap">
              <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
              Live Today ({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }})
            </span>
          @else
            <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 font-extrabold text-[11px] px-2.5 py-1.5 rounded-xl border border-indigo-200 whitespace-nowrap">
              <i class="fa-solid fa-calendar-check text-[11px]"></i>
              {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} &rarr; {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
            </span>
          @endif
        </form>

        <button type="button" class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-all cursor-pointer ml-auto" onclick="if(typeof openReservationModal === 'function') openReservationModal(); else openModal('reserve-modal');">
          <i class="fa-solid fa-calendar-plus"></i> New Reservation
        </button>
      </div>
    </div>

    <!-- Rooms Grid Viewport -->
    <div class="flex-1 overflow-y-auto p-4 bg-slate-50/50 rack-grid-viewport">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-8 gap-2.5 rack-rooms-container" id="rooms-grid">
        
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
              @if($rm['status'] === 'blocked')
                <div class="rack-guest-name text-indigo-600" title="Blocked">Blocked</div>
              @elseif(!empty($rm['guest']['name']))
                <div class="rack-guest-name truncate" title="{{ $rm['guest']['name'] }}">{{ $rm['guest']['name'] }}</div>
              @endif
            </div>
            <div class="rack-card-bot flex justify-end">
              <div class="rack-badge" onclick="openRoomStatusPopover(event, this)" title="Click to update Housekeeping / Operational status">
                <span class="rack-badge-label">{{ $badgeText }}</span>
                <i class="fa-solid fa-chevron-down rack-badge-arrow text-[8px] opacity-70"></i>
              </div>
            </div>
          </div>
        @empty
          <div class="col-span-full bg-white rounded-2xl border-2 border-dashed border-slate-200 p-12 text-center my-4">
            <div class="text-4xl text-slate-300 mb-3">
              <i class="fa-solid fa-calendar-xmark"></i>
            </div>
            <div class="text-base font-extrabold text-slate-800">No Booked / Checked-in Rooms Found</div>
            <div class="text-xs text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
              There are no active guest bookings or check-ins recorded for the selected date range 
              <strong class="text-slate-700">({{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }})</strong>.
            </div>
            <div class="mt-4 flex justify-center gap-2.5">
              <button type="button" class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl transition-all cursor-pointer" onclick="setReserveDatesToday()">
                <i class="fa-solid fa-rotate-left"></i> Reset to Today
              </button>
              <button type="button" class="px-3.5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-all cursor-pointer" onclick="if(typeof openReservationModal === 'function') openReservationModal(); else openModal('reserve-modal');">
                <i class="fa-solid fa-calendar-plus"></i> New Reservation
              </button>
            </div>
          </div>
        @endforelse

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
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-bed text-rose-700"></i>Booked Rooms</span>
        <span class="font-mono text-slate-900 val">{{ $stats['total_booked'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-key text-emerald-600"></i>Checked In</span>
        <span class="font-mono text-slate-900 val">{{ $stats['checked_in'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-hotel text-amber-500"></i>Stay Over</span>
        <span class="font-mono text-slate-900 val">{{ $stats['stay_over'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-calendar-check text-indigo-500"></i>Confirmed</span>
        <span class="font-mono text-slate-900 val">{{ $stats['confirmed'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-user-check text-cyan-600"></i>Arrived</span>
        <span class="font-mono text-slate-900 val">{{ $stats['arrived'] }}</span>
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
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-user-group text-indigo-600"></i>In-House Pax</span>
        <span class="font-mono text-slate-900 val">{{ $stats['total_pax'] }}</span>
      </div>
      <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-100 text-xs font-bold stat-strip">
        <span class="flex items-center gap-2 text-slate-600 lbl"><i class="fa-solid fa-percent text-emerald-600"></i>Occupancy</span>
        <span class="font-mono text-slate-900 val">{{ $stats['occupancy_percentage'] }}%</span>
      </div>
    </div>

    <!-- Occupancy & Status Pie Chart Section -->
    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col items-center stats-pie-section">
      <div class="w-full flex justify-between items-center pb-2 mb-2 border-b border-slate-200">
        <span class="text-[10px] font-black uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
          <i class="fa-solid fa-chart-pie text-indigo-500"></i> Booked Status
        </span>
        <span class="text-xs font-black text-indigo-700 font-mono">
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
      <div class="relative w-28 h-28 my-1 flex items-center justify-center pie-donut-container">
        <div class="w-full h-full rounded-full shadow-xs" style="background: conic-gradient(
            #10b981 0% {{ $stop1 }}%,
            #f59e0b {{ $stop1 }}% {{ $stop2 }}%,
            #6366f1 {{ $stop2 }}% {{ $stop3 }}%,
            #06b6d4 {{ $stop3 }}% 100%
          );">
        </div>
        <!-- Center Hole for Donut Style -->
        <div class="absolute w-14 h-14 rounded-full bg-white flex flex-col items-center justify-center shadow-xs">
          <span class="text-sm font-black text-slate-900 font-mono leading-none">{{ $stats['total_booked'] }}</span>
          <span class="text-[8px] font-extrabold text-slate-400 uppercase tracking-tight mt-0.5">Booked</span>
        </div>
      </div>

      <!-- Status Breakdown Grid (2x2 Clean Stacked Design) -->
      <div class="grid grid-cols-2 gap-1.5 w-full mt-2">
        <div class="bg-emerald-50/80 border border-emerald-200 rounded-lg p-1.5 flex flex-col">
          <div class="flex items-center justify-between text-[10px] font-bold text-emerald-800">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Checked In</span>
            <span class="font-mono font-black text-emerald-700">{{ $stats['checked_in'] }}</span>
          </div>
          <div class="text-[9px] font-bold text-emerald-600 text-right opacity-80">{{ $stats['percentages']['checked_in'] }}%</div>
        </div>

        <div class="bg-amber-50/80 border border-amber-200 rounded-lg p-1.5 flex flex-col">
          <div class="flex items-center justify-between text-[10px] font-bold text-amber-800">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Stay Over</span>
            <span class="font-mono font-black text-amber-700">{{ $stats['stay_over'] }}</span>
          </div>
          <div class="text-[9px] font-bold text-amber-600 text-right opacity-80">{{ $stats['percentages']['stay_over'] }}%</div>
        </div>

        <div class="bg-indigo-50/80 border border-indigo-200 rounded-lg p-1.5 flex flex-col">
          <div class="flex items-center justify-between text-[10px] font-bold text-indigo-800">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Confirmed</span>
            <span class="font-mono font-black text-indigo-700">{{ $stats['confirmed'] }}</span>
          </div>
          <div class="text-[9px] font-bold text-indigo-600 text-right opacity-80">{{ $stats['percentages']['confirmed'] }}%</div>
        </div>

        <div class="bg-cyan-50/80 border border-cyan-200 rounded-lg p-1.5 flex flex-col">
          <div class="flex items-center justify-between text-[10px] font-bold text-cyan-800">
            <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-cyan-500"></span> Arrived</span>
            <span class="font-mono font-black text-cyan-700">{{ $stats['arrived'] }}</span>
          </div>
          <div class="text-[9px] font-bold text-cyan-600 text-right opacity-80">{{ $stats['percentages']['arrived'] }}%</div>
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
    <div class="{{ $chipBase }} {{ in_array($selectedStatus, ['all', '']) ? 'bg-slate-900 border-slate-900 text-white active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-all" onclick="applyReserveStatusFilter('all')">
      <span class="chip-lbl">All Booked</span>
      <span class="h-5 px-1.5 rounded-md {{ in_array($selectedStatus, ['all', '']) ? 'bg-slate-800 text-white' : 'bg-white border' }} font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['total_booked'] }}</span>
    </div>
    <div class="{{ $chipBase }} {{ $selectedStatus === 'checked in' ? 'bg-emerald-50 border-emerald-300 text-emerald-700 ring-2 ring-emerald-400 active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-avl" onclick="applyReserveStatusFilter('checked in')">
      <span class="chip-lbl">Checked In</span>
      <span class="h-5 px-1.5 rounded-md bg-white border font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['checked_in'] }}</span>
    </div>
    <div class="{{ $chipBase }} {{ $selectedStatus === 'stay over' ? 'bg-amber-50 border-amber-300 text-amber-700 ring-2 ring-amber-400 active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-drt" onclick="applyReserveStatusFilter('stay over')">
      <span class="chip-lbl">Stay Over</span>
      <span class="h-5 px-1.5 rounded-md bg-white border font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['stay_over'] }}</span>
    </div>
    <div class="{{ $chipBase }} {{ $selectedStatus === 'confirmed' ? 'bg-indigo-50 border-indigo-300 text-indigo-700 ring-2 ring-indigo-400 active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-blk" onclick="applyReserveStatusFilter('confirmed')">
      <span class="chip-lbl">Confirmed</span>
      <span class="h-5 px-1.5 rounded-md bg-white border font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['confirmed'] }}</span>
    </div>
    @if($stats['arrived'] > 0)
      <div class="{{ $chipBase }} {{ $selectedStatus === 'arrived' ? 'bg-cyan-50 border-cyan-300 text-cyan-700 ring-2 ring-cyan-400 active-chip' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }} chip-occ" onclick="applyReserveStatusFilter('arrived')">
        <span class="chip-lbl">Arrived</span>
        <span class="h-5 px-1.5 rounded-md bg-white border font-mono text-[10px] inline-flex items-center justify-center chip-cnt">{{ $stats['arrived'] }}</span>
      </div>
    @endif
  </div>

  <form method="GET" action="{{ route('frontoffice.reserve') }}" id="backend-filter-form" class="flex items-center gap-1.5 shrink-0 flex-nowrap bottom-actions">
    <input type="hidden" name="from_date" value="{{ $fromDate }}">
    <input type="hidden" name="to_date" value="{{ $toDate }}">
    <input type="hidden" name="status" id="filter-status-val" value="{{ $selectedStatus ?? 'all' }}">
    <input type="hidden" name="search" id="filter-search-val" value="{{ $searchQuery ?? '' }}">

    <button type="button" class="btn-ui-secondary h-8.5 px-3 text-xs inline-flex items-center gap-1.5 font-bold rounded-lg border leading-none whitespace-nowrap cursor-pointer pms-btn-action" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Notification sent to front desk supervisors!', 'info'); else alert('Notification sent to front desk supervisors!');">
      <i class="fa-solid fa-bell text-indigo-600"></i> Notify
    </button>
    <a href="{{ route('frontoffice.reserve') }}" class="btn-ui-secondary h-8.5 px-3 text-xs inline-flex items-center gap-1.5 font-bold rounded-lg border leading-none whitespace-nowrap cursor-pointer pms-btn-action">
      <i class="fa-solid fa-rotate text-cyan-600"></i> Refresh
    </a>
    
    <select name="type" class="pms-select h-8.5 text-xs pl-3 pr-7.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-type" onchange="submitBackendFilter()" title="Filter by Room Type">
      <option value="ALL" {{ ($selectedType ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Types</option>
      @foreach($categories as $cat)
        <option value="{{ strtoupper($cat->name) }}" {{ ($selectedType ?? '') === strtoupper($cat->name) ? 'selected' : '' }}>{{ $cat->name }}</option>
      @endforeach
    </select>

    <select name="floor" class="pms-select h-8.5 text-xs pl-3 pr-7.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-floor" onchange="submitBackendFilter()" title="Filter by Floor">
      <option value="ALL" {{ ($selectedFloor ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Floor</option>
      @foreach($floors as $fl)
        <option value="{{ $fl->floor }}" {{ strval($selectedFloor ?? '') === strval($fl->floor) ? 'selected' : '' }}>Floor {{ $fl->floor }}</option>
      @endforeach
    </select>

    <select name="bedding" class="pms-select h-8.5 text-xs pl-3 pr-7.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-bedding" onchange="submitBackendFilter()" title="Filter by Bedding Configuration">
      <option value="ALL" {{ ($selectedBedding ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Bedding</option>
      @foreach($beddingConfigs as $bc)
        <option value="{{ $bc->id }}" {{ strval($selectedBedding ?? '') === strval($bc->id) ? 'selected' : '' }}>{{ $bc->name }}</option>
      @endforeach
    </select>

    <select name="pax" class="pms-select h-8.5 text-xs pl-3 pr-7.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-pax" onchange="submitBackendFilter()" title="Filter by Pax Capacity">
      <option value="ALL" {{ ($selectedPax ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Pax</option>
      <option value="1" {{ strval($selectedPax ?? '') === '1' ? 'selected' : '' }}>1 Pax</option>
      <option value="2" {{ strval($selectedPax ?? '') === '2' ? 'selected' : '' }}>2 Pax</option>
      <option value="3" {{ strval($selectedPax ?? '') === '3' ? 'selected' : '' }}>3 Pax</option>
      <option value="4" {{ strval($selectedPax ?? '') === '4' ? 'selected' : '' }}>4 Pax</option>
      <option value="6" {{ strval($selectedPax ?? '') === '6' ? 'selected' : '' }}>6 Pax</option>
    </select>

    <select name="amenity" class="pms-select h-8.5 text-xs pl-3 pr-7.5 rounded-lg border border-slate-200 bg-white font-semibold cursor-pointer leading-none" id="filter-amenity" onchange="submitBackendFilter()" title="Filter by Amenities">
      <option value="ALL" {{ ($selectedAmenity ?? 'ALL') === 'ALL' ? 'selected' : '' }}>All Amenities</option>
      @foreach($amenitiesList as $amn)
        <option value="{{ $amn->id }}" {{ strval($selectedAmenity ?? '') === strval($amn->id) ? 'selected' : '' }}>{{ $amn->name }}</option>
      @endforeach
    </select>
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
