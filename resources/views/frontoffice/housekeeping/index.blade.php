@extends('frontoffice.layouts.app')

@section('title', 'Housekeeping Operations & Tasks - Hotel Sagar Sonnet CRM / PMS')


@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">
    
    <!-- Header Banner -->
    <div class="hk-header-card">
      <div class="hk-header-title">
        <div class="hk-header-icon">
          <i class="fa-solid fa-broom"></i>
        </div>
        <div class="hk-title-text">
          <h2>Housekeeping Operations & Tasks <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full inline-flex items-center gap-1.5"><i class="fa-solid fa-circle text-[7px] text-emerald-500"></i> Instant Status Transfer</span></h2>
          <p>Real-time room cleaning queue: change any status to immediately transition rooms between Tasks & Completed Tasks</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <button class="ribbon-btn bg-white border border-slate-300 px-3 py-1.5 rounded-md cursor-pointer font-semibold text-xs flex items-center gap-1.5 hover:bg-slate-50 transition" onclick="location.reload()">
          <i class="fa-solid fa-rotate text-indigo-600 text-xs"></i> Refresh
        </button>
      </div>
    </div>

    <!-- MAIN TABS: Tasks vs Completed Tasks vs All -->
    <div class="hk-main-tabs-bar">
      <div class="hk-tabs-nav">
        <!-- Active Cleaning Tasks Tab -->
        <button type="button" class="hk-tab-btn active-tab-pending" id="tabBtnPending" onclick="switchSection('pending')">
          <i class="fa-solid fa-broom text-amber-500 text-xs"></i>
          <span>Tasks (Cleaning & Due)</span>
          <span class="hk-tab-badge badge-pending-count" id="badgePendingCount">{{ count($pendingRooms) }}</span>
        </button>

        <!-- Completed Tasks Tab -->
        <button type="button" class="hk-tab-btn" id="tabBtnCompleted" onclick="switchSection('completed')">
          <i class="fa-solid fa-circle-check text-emerald-500 text-xs"></i>
          <span>Completed Tasks</span>
          <span class="hk-tab-badge badge-completed-count" id="badgeCompletedCount">{{ count($completedRooms) }}</span>
        </button>

        <!-- All Rooms Tab -->
        <button type="button" class="hk-tab-btn" id="tabBtnAll" onclick="switchSection('all')">
          <i class="fa-solid fa-border-all text-indigo-600 text-xs"></i>
          <span>All Rooms</span>
          <span class="hk-tab-badge badge-all-count" id="badgeAllCount">{{ count($filteredRooms) }}</span>
        </button>
      </div>

      <!-- View Switcher -->
      <div class="hk-view-toggles">
        <button type="button" class="hk-view-btn active" id="btnViewGrid" onclick="switchView('grid')">
          <i class="fa-solid fa-table-cells-large"></i> Grid
        </button>
        <button type="button" class="hk-view-btn" id="btnViewTable" onclick="switchView('table')">
          <i class="fa-solid fa-list"></i> Table
        </button>
      </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="hk-kpi-grid">
      <div class="hk-kpi-card" onclick="switchSection('all')">
        <div class="hk-kpi-icon bg-blue-50 text-blue-600">
          <i class="fa-solid fa-hotel"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $totalRooms }}</span>
          <span class="hk-kpi-lbl">Total Rooms</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="switchSection('completed')">
        <div class="hk-kpi-icon bg-emerald-50 text-emerald-600">
          <i class="fa-solid fa-sparkles"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $cleanCount }}</span>
          <span class="hk-kpi-lbl">Clean / Completed</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="switchSection('pending')">
        <div class="hk-kpi-icon bg-amber-50 text-amber-600">
          <i class="fa-solid fa-broom"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $dirtyCount }}</span>
          <span class="hk-kpi-lbl">Dirty / Due Tasks</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="applyQuickFilter('hk_state', '3')">
        <div class="hk-kpi-icon bg-indigo-50 text-indigo-600">
          <i class="fa-solid fa-clipboard-check"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $inspectCount }}</span>
          <span class="hk-kpi-lbl">Inspecting</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="applyQuickFilter('occupancy', 'occupied')">
        <div class="hk-kpi-icon bg-pink-50 text-pink-600">
          <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $occupiedCount }}</span>
          <span class="hk-kpi-lbl">Occupied</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="applyQuickFilter('occupancy', 'vacant')">
        <div class="hk-kpi-icon bg-emerald-50 text-emerald-600">
          <i class="fa-solid fa-door-open"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $vacantCount }}</span>
          <span class="hk-kpi-lbl">Vacant</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="applyQuickFilter('op_status', 'maintenance')">
        <div class="hk-kpi-icon bg-rose-50 text-rose-600">
          <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $maintenanceCount }}</span>
          <span class="hk-kpi-lbl">Maintenance</span>
        </div>
      </div>
    </div>

    <!-- Filters Toolbar with custom styled select arrow icons -->
    <form method="GET" action="{{ route('frontoffice.housekeeping') }}" id="hkFilterForm" class="hk-toolbar">
      <div class="hk-filters-group">
        <!-- Search -->
        <div class="hk-search-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="search" value="{{ $search }}" placeholder="Search room # or guest..." onkeydown="if(event.key==='Enter') this.form.submit()">
        </div>

        <!-- Floor Filter -->
        <div class="hk-select-wrap">
          <select name="floor" class="hk-select" onchange="this.form.submit()">
            <option value="all">All Floors</option>
            @foreach($floors as $fl)
              <option value="{{ $fl->id }}" {{ $selectedFloor == $fl->id ? 'selected' : '' }}>Floor {{ $fl->floor }}</option>
            @endforeach
          </select>
        </div>

        <!-- Category Filter -->
        <div class="hk-select-wrap">
          <select name="category" class="hk-select" onchange="this.form.submit()">
            <option value="all">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- HK State Filter -->
        <div class="hk-select-wrap">
          <select name="hk_state" class="hk-select" onchange="this.form.submit()">
            <option value="all">All Cleaning States</option>
            @foreach($housekeepingStates as $st)
              <option value="{{ $st->id }}" {{ $selectedHkState == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- Occupancy Filter -->
        <div class="hk-select-wrap">
          <select name="occupancy" class="hk-select" onchange="this.form.submit()">
            <option value="all" {{ empty($selectedOccupancy) || $selectedOccupancy == 'all' ? 'selected' : '' }}>All Occupancy</option>
            <option value="vacant" {{ $selectedOccupancy == 'vacant' ? 'selected' : '' }}>Vacant Rooms</option>
            <option value="occupied" {{ $selectedOccupancy == 'occupied' ? 'selected' : '' }}>Occupied Rooms</option>
          </select>
        </div>

        @if($search || ($selectedFloor && $selectedFloor != 'all') || ($selectedCategory && $selectedCategory != 'all') || ($selectedHkState && $selectedHkState != 'all') || ($selectedOccupancy && $selectedOccupancy != 'all'))
          <a href="{{ route('frontoffice.housekeeping') }}" class="text-xs font-bold text-red-600 no-underline px-2.5 py-1.5 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 transition inline-flex items-center gap-1">
            <i class="fa-solid fa-xmark"></i> Clear Filters
          </a>
        @endif
      </div>
    </form>

    <!-- Bulk Floating Actions Bar -->
    <div class="hk-bulk-bar" id="hkBulkBar">
      <div class="hk-bulk-info">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        <span><strong id="selectedRoomsCount">0</strong> rooms selected</span>
        <span class="hk-bulk-pill">Batch Operations</span>
      </div>
      <div class="hk-bulk-actions">
        <select id="bulkHkStatusSelect" class="hk-select bg-slate-800 text-white border-slate-700 h-[30px] text-[11px]">
          @foreach($housekeepingStates as $st)
            <option value="{{ $st->id }}">{{ $st->name }}</option>
          @endforeach
        </select>
        <button type="button" class="btn-bulk-action btn-bulk-clean" onclick="submitBulkUpdate()">
          <i class="fa-solid fa-check-double"></i> Apply State
        </button>
        <button type="button" class="btn-bulk-action bg-slate-700 text-white hover:bg-slate-600" onclick="clearSelection()">
          <i class="fa-solid fa-xmark"></i> Cancel
        </button>
      </div>
    </div>

    <!-- TAB 1: PENDING TASKS SECTION -->
    <div id="sectionPendingTasks" class="block">
      <div class="hk-section-bar">
        <h3 class="hk-section-title">
          <span class="hk-section-badge bg-amber-500"></span>
          Cleaning &amp; Due Tasks Queue (<span class="pending-count-lbl">{{ count($pendingRooms) }}</span>)
        </h3>
        <span class="hk-section-hint">Mark as clean to automatically move room to Completed Tasks</span>
      </div>

      <!-- Grid View for Pending Tasks -->
      <div class="hk-rooms-grid pending-grid-target">
        @forelse($pendingRooms as $room)
          @include('frontoffice.housekeeping.room_card', ['room' => $room, 'isCompleteSection' => false])
        @empty
          <div class="hk-empty-hero col-span-full py-10 px-6 text-center bg-gradient-to-b from-white to-slate-50 rounded-2xl border border-slate-200 shadow-xs">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-500 inline-flex items-center justify-center text-2xl mb-3.5 shadow-sm shadow-emerald-500/20">
              <i class="fa-solid fa-sparkles"></i>
            </div>
            <div class="mb-2">
              <span class="bg-emerald-100 text-emerald-700 text-xs font-extrabold px-3 py-1 rounded-full inline-flex items-center gap-1.5 border border-emerald-300">
                <i class="fa-solid fa-circle-check"></i> 100% Rooms Clean &amp; Inspected
              </span>
            </div>
            <h4 class="mt-1.5 mb-1 text-slate-900 font-extrabold text-base">No Pending Cleaning Tasks in Queue!</h4>
            <p class="mx-auto mb-4.5 max-w-md text-xs text-slate-500 leading-relaxed">All {{ $totalRooms }} rooms across all floors are currently sanitized, clean, and ready for guests. You can review all completed rooms or change individual statuses anytime.</p>
            <div class="inline-flex gap-2.5 items-center flex-wrap justify-center">
              <button type="button" onclick="switchSection('completed')" class="bg-emerald-500 text-white border-none px-4.5 py-2 rounded-lg text-xs font-bold cursor-pointer inline-flex items-center gap-1.5 shadow-xs hover:bg-emerald-600 transition">
                <i class="fa-solid fa-circle-check"></i> View Completed Tasks ({{ count($completedRooms) }})
              </button>
              <button type="button" onclick="switchSection('all')" class="bg-white text-indigo-700 border border-indigo-200 px-4.5 py-2 rounded-lg text-xs font-bold cursor-pointer inline-flex items-center gap-1.5 hover:bg-indigo-50 transition">
                <i class="fa-solid fa-border-all"></i> Browse All Rooms ({{ count($filteredRooms) }})
              </button>
            </div>
          </div>
        @endforelse
      </div>

      <!-- Table View for Pending Tasks -->
      <div class="hk-table-view pending-table-target">
        <table class="hk-table">
          <thead>
            <tr>
              <th class="w-9"><input type="checkbox" onchange="toggleSelectAllInContainer(this, '#sectionPendingTasks')"></th>
              <th>Room</th>
              <th>Floor</th>
              <th>Category</th>
              <th>Occupancy</th>
              <th>Current State</th>
              <th>Task Status</th>
              <th>Last Updated</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pendingRooms as $room)
              @include('frontoffice.housekeeping.room_table_row', ['room' => $room, 'isCompleteSection' => false])
            @empty
              <tr>
                <td colspan="9" class="text-center py-6 text-slate-500">No pending cleaning tasks. All rooms are clean.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: COMPLETED TASKS SECTION -->
    <div id="sectionCompletedTasks" class="d-none hidden">
      <div class="hk-section-bar">
        <h3 class="hk-section-title">
          <span class="hk-section-badge bg-emerald-500"></span>
          Completed Cleaning Tasks (<span class="completed-count-lbl">{{ count($completedRooms) }}</span>)
        </h3>
        <span class="hk-section-hint">Rooms cleaned and inspected, ready for guest allocation</span>
      </div>

      <!-- Grid View for Completed Tasks -->
      <div class="hk-rooms-grid completed-grid-target">
        @forelse($completedRooms as $room)
          @include('frontoffice.housekeeping.room_card', ['room' => $room, 'isCompleteSection' => true])
        @empty
          <div class="hk-empty-box col-span-full py-7 text-center bg-white rounded-xl border border-dashed border-slate-300">
            <i class="fa-solid fa-broom text-2xl text-amber-500 mb-2"></i>
            <h4 class="m-0 text-slate-900 font-bold text-sm">No Completed Tasks Recorded</h4>
            <p class="mt-1 mb-0 text-xs text-slate-500">Complete any pending room cleaning above to populate this section.</p>
          </div>
        @endforelse
      </div>

      <!-- Table View for Completed Tasks -->
      <div class="hk-table-view completed-table-target">
        <table class="hk-table">
          <thead>
            <tr>
              <th class="w-9"><input type="checkbox" onchange="toggleSelectAllInContainer(this, '#sectionCompletedTasks')"></th>
              <th>Room</th>
              <th>Floor</th>
              <th>Category</th>
              <th>Occupancy</th>
              <th>Current State</th>
              <th>Task Status</th>
              <th>Completed At</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($completedRooms as $room)
              @include('frontoffice.housekeeping.room_table_row', ['room' => $room, 'isCompleteSection' => true])
            @empty
              <tr>
                <td colspan="9" class="text-center py-5 text-slate-500">No completed tasks yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: ALL ROOMS SECTION -->
    <div id="sectionAllRooms" class="d-none hidden">
      <div class="hk-section-bar">
        <h3 class="hk-section-title">
          <span class="hk-section-badge bg-indigo-600"></span>
          All Hotel Rooms ({{ count($filteredRooms) }})
        </h3>
        <span class="hk-section-hint">Combined overview of all inventory</span>
      </div>

      <!-- Grid View for All Rooms -->
      <div class="hk-rooms-grid all-grid-target">
        @forelse($filteredRooms as $room)
          @include('frontoffice.housekeeping.room_card', ['room' => $room, 'isCompleteSection' => null])
        @empty
          <div class="col-span-full py-7 text-center bg-white rounded-xl border border-dashed border-slate-300">
            <p class="m-0 text-slate-500 text-xs">No rooms matched your criteria.</p>
          </div>
        @endforelse
      </div>

      <!-- Table View for All Rooms -->
      <div class="hk-table-view all-table-target">
        <table class="hk-table">
          <thead>
            <tr>
              <th class="w-9"><input type="checkbox" onchange="toggleSelectAllInContainer(this, '#sectionAllRooms')"></th>
              <th>Room</th>
              <th>Floor</th>
              <th>Category</th>
              <th>Occupancy</th>
              <th>Current State</th>
              <th>Operational Status</th>
              <th>Last Updated</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($filteredRooms as $room)
              @include('frontoffice.housekeeping.room_table_row', ['room' => $room, 'isCompleteSection' => null])
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Collapsible Today's Housekeeping Audit Trail -->
    <div class="hk-logs-card">
      <div class="hk-logs-header" onclick="toggleActivityLogs()">
        <h3>
          <i class="fa-solid fa-clipboard-list text-indigo-600 text-xs"></i>
          Today's Housekeeping Activity Trail ({{ count($todayLogs) }})
        </h3>
        <div class="hk-logs-header-right">
          <span class="text-[11px] text-slate-500 font-semibold">Click to view audit log</span>
          <i class="fa-solid fa-chevron-down hk-chevron-icon" id="activityLogsChevron"></i>
        </div>
      </div>

      <div class="hk-logs-body" id="activityLogsBody">
        <div class="overflow-x-auto">
          <table class="hk-table text-xs">
            <thead>
              <tr>
                <th>Timestamp</th>
                <th>Room #</th>
                <th>Category & Floor</th>
                <th>Status Applied</th>
                <th>Task Status</th>
                <th>Completed At</th>
              </tr>
            </thead>
            <tbody id="todayLogsTbody">
              @forelse($todayLogs as $log)
                <tr>
                  <td class="text-slate-500 font-semibold">
                    <i class="fa-regular fa-clock mr-1"></i> {{ $log->created_at ? $log->created_at->format('h:i A') : 'N/A' }}
                  </td>
                  <td>
                    <strong class="text-slate-900 text-xs font-bold">Room {{ $log->room?->room_number ?? 'N/A' }}</strong>
                  </td>
                  <td>
                    {{ $log->room?->categoryRelation?->name ?? 'Standard' }} (Floor {{ $log->room?->floorRelation?->floor ?? '1' }})
                  </td>
                  <td>
                    @php
                      $logStatusName = $log->housekeepingStatus?->name ?? 'Clean';
                      $isLogClean = (bool)preg_match('/clean|sanit|ready/i', strtolower($logStatusName));
                      $isLogDirty = (bool)preg_match('/dirty|due/i', strtolower($logStatusName));
                    @endphp
                    <span class="hk-badge {{ $isLogClean ? 'badge-clean' : ($isLogDirty ? 'badge-dirty' : 'badge-inspect') }}">
                      {{ $logStatusName }}
                    </span>
                  </td>
                  <td>
                    <span class="font-semibold capitalize {{ $log->status === 'complete' ? 'text-emerald-600' : 'text-amber-600' }}">
                      <i class="fa-solid {{ $log->status === 'complete' ? 'fa-check' : 'fa-spinner fa-spin' }}"></i> {{ $log->status ?? 'pending' }}
                    </span>
                  </td>
                  <td class="text-secondary">
                    {{ $log->completion_time ? \Carbon\Carbon::parse($log->completion_time)->format('h:i A') : '—' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-slate-400">
                    No housekeeping updates logged today yet.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Room Status Change Modal -->
<div class="hk-modal-overlay" id="hkStatusModal">
  <div class="hk-modal-box">
    <div class="hk-modal-header">
      <h4 class="hk-modal-title">
        <i class="fa-solid fa-spray-can-sparkles text-amber-500"></i> Update Room Status: <span id="modalRoomNumber"></span>
      </h4>
      <button type="button" class="hk-modal-close" onclick="closeStatusModal()">&times;</button>
    </div>
    <div class="hk-modal-body">
      <form id="singleRoomStatusForm" onsubmit="event.preventDefault(); submitSingleRoomStatus();">
        <input type="hidden" id="modalRoomId" name="room_id">

        <div class="mb-3">
          <label class="block text-xs font-bold text-slate-700 mb-1.5">
            Housekeeping Cleaning State <span class="text-red-500">*</span>
          </label>
          <div class="hk-select-wrap w-full">
            <select id="modalHkStatusSelect" name="housekeeping_status_id" class="hk-select w-full h-9 text-xs" required>
              @foreach($housekeepingStates as $st)
                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->type === 'completed_task' ? 'Completed' : 'Assign' }})</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="block text-xs font-bold text-slate-700 mb-1.5">
            Task Progress State
          </label>
          <div class="hk-select-wrap w-full">
            <select id="modalTaskStatusSelect" name="task_status" class="hk-select w-full h-9 text-xs">
              <option value="pending">Pending Cleaning</option>
              <option value="in_progress">In Progress</option>
              <option value="complete">Complete (Mark as Done)</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="block text-xs font-bold text-slate-700 mb-1.5">
            Operational Status (Optional)
          </label>
          <div class="hk-select-wrap w-full">
            <select id="modalOpStatusSelect" name="operational_status_id" class="hk-select w-full h-9 text-xs">
              <option value="">Keep Existing Operational Status</option>
              @foreach($operationalStatuses as $op)
                <option value="{{ $op->id }}">{{ $op->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mb-4">
          <label class="block text-xs font-bold text-slate-700 mb-1.5">
            Cleaning / Inspection Remarks
          </label>
          <textarea id="modalRemarks" name="remarks" rows="2" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="e.g. Linen changed, floor mopped, sanitized & inspected"></textarea>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" class="btn-pms-logout bg-slate-200 text-slate-700 px-3.5 py-1.5 text-xs hover:bg-slate-300 rounded-md" onclick="closeStatusModal()">Cancel</button>
          <button type="submit" id="btnSubmitSingleStatus" class="ribbon-btn bg-indigo-600 text-white px-4 py-1.5 rounded-md font-bold text-xs hover:bg-indigo-700 transition">
            <i class="fa-solid fa-floppy-disk"></i> Save & Transfer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- History Modal -->
<div class="hk-modal-overlay" id="hkHistoryModal">
  <div class="hk-modal-box">
    <div class="hk-modal-header">
      <h4 class="hk-modal-title">
        <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i> Room <span id="historyRoomNumber"></span> Cleaning Log
      </h4>
      <button type="button" class="hk-modal-close" onclick="closeHistoryModal()">&times;</button>
    </div>
    <div class="hk-modal-body">
      <div id="historyLoading" class="text-center py-6 text-slate-500">
        <i class="fa-solid fa-spinner fa-spin text-xl text-indigo-600"></i>
        <div class="mt-1.5 text-xs">Loading room audit trail...</div>
      </div>
      <div class="d-none hidden" id="historyContent">
        <ul class="timeline-list" id="timelineList"></ul>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const UPDATE_STATUS_URL = '{{ route("frontoffice.housekeeping.update-status") }}';
  const BULK_UPDATE_URL = '{{ route("frontoffice.housekeeping.bulk-update") }}';
  const ROOM_HISTORY_BASE_URL = '{{ url("front-office/housekeeping") }}';

  let currentViewMode = 'grid';

  // Toggle Activity Logs Collapsible Card
  function toggleActivityLogs() {
    const body = document.getElementById('activityLogsBody');
    const chevron = document.getElementById('activityLogsChevron');

    if (body.style.display === 'block') {
      body.style.display = 'none';
      chevron.style.transform = 'rotate(0deg)';
    } else {
      body.style.display = 'block';
      chevron.style.transform = 'rotate(180deg)';
    }
  }

  // Section Tab Switcher (Pending Tasks vs Completed Tasks vs All)
  function switchSection(section) {
    const secPending = document.getElementById('sectionPendingTasks');
    const secCompleted = document.getElementById('sectionCompletedTasks');
    const secAll = document.getElementById('sectionAllRooms');

    const btnPending = document.getElementById('tabBtnPending');
    const btnCompleted = document.getElementById('tabBtnCompleted');
    const btnAll = document.getElementById('tabBtnAll');

    // Reset tab active classes
    if (btnPending) btnPending.classList.remove('active-tab-pending');
    if (btnCompleted) btnCompleted.classList.remove('active-tab-completed');
    if (btnAll) btnAll.classList.remove('active-tab-all');

    if (section === 'pending') {
      if (secPending) { secPending.style.display = 'block'; secPending.classList.remove('d-none'); }
      if (secCompleted) { secCompleted.style.display = 'none'; secCompleted.classList.add('d-none'); }
      if (secAll) { secAll.style.display = 'none'; secAll.classList.add('d-none'); }
      if (btnPending) btnPending.classList.add('active-tab-pending');
    } else if (section === 'completed') {
      if (secPending) { secPending.style.display = 'none'; secPending.classList.add('d-none'); }
      if (secCompleted) { secCompleted.style.display = 'block'; secCompleted.classList.remove('d-none'); }
      if (secAll) { secAll.style.display = 'none'; secAll.classList.add('d-none'); }
      if (btnCompleted) btnCompleted.classList.add('active-tab-completed');
    } else {
      if (secPending) { secPending.style.display = 'none'; secPending.classList.add('d-none'); }
      if (secCompleted) { secCompleted.style.display = 'none'; secCompleted.classList.add('d-none'); }
      if (secAll) { secAll.style.display = 'block'; secAll.classList.remove('d-none'); }
      if (btnAll) btnAll.classList.add('active-tab-all');
    }

    clearSelection();
  }

  // Switch View between Grid and Table across all sections
  function switchView(viewMode) {
    currentViewMode = viewMode;
    const btnGrid = document.getElementById('btnViewGrid');
    const btnTable = document.getElementById('btnViewTable');

    const allGrids = document.querySelectorAll('.hk-rooms-grid');
    const allTables = document.querySelectorAll('.hk-table-view');

    if (viewMode === 'grid') {
      allGrids.forEach(g => g.style.display = 'grid');
      allTables.forEach(t => t.style.display = 'none');
      btnGrid.classList.add('active');
      btnTable.classList.remove('active');
    } else {
      allGrids.forEach(g => g.style.display = 'none');
      allTables.forEach(t => t.style.display = 'block');
      btnTable.classList.add('active');
      btnGrid.classList.remove('active');
    }
  }

  // Quick Filter via KPI
  function applyQuickFilter(field, value) {
    const form = document.getElementById('hkFilterForm');
    const input = form.querySelector(`[name="${field}"]`);
    if (input) {
      input.value = value;
      form.submit();
    }
  }

  // Selection Checkbox Handler for Bulk Actions
  function onRoomCheckboxChange() {
    const checkedBoxes = document.querySelectorAll('.room-select-box:checked');
    const bulkBar = document.getElementById('hkBulkBar');
    const countSpan = document.getElementById('selectedRoomsCount');

    countSpan.textContent = checkedBoxes.length;

    if (checkedBoxes.length > 0) {
      bulkBar.style.display = 'flex';
    } else {
      bulkBar.style.display = 'none';
    }
  }

  function toggleSelectAllInContainer(masterBox, containerSelector) {
    const container = document.querySelector(containerSelector);
    if (!container) return;
    const checkboxes = container.querySelectorAll('.room-select-box');
    checkboxes.forEach(cb => {
      cb.checked = masterBox.checked;
    });
    onRoomCheckboxChange();
  }

  function clearSelection() {
    const checkboxes = document.querySelectorAll('.room-select-box');
    checkboxes.forEach(cb => { cb.checked = false; });
    onRoomCheckboxChange();
  }

  // Instant 1-click status update (Immediately transfers room to target section)
  async function quickUpdateStatus(roomId, hkStatusId, label) {
    try {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(`Updating room to ${label}...`, 'info');
      }

      const res = await fetch(UPDATE_STATUS_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          room_id: roomId,
          housekeeping_status_id: hkStatusId,
          task_status: (label === 'Clean' ? 'complete' : 'pending')
        })
      });

      const data = await res.json();
      if (data.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        }
        setTimeout(() => location.reload(), 400);
      } else {
        alert(data.message || 'Update failed');
      }
    } catch (err) {
      console.error(err);
      alert('Error updating status: ' + err.message);
    }
  }

  // Open Room Status Modal
  function openRoomStatusModal(roomId, roomNumber, currentHkId, currentTaskStatus) {
    document.getElementById('modalRoomId').value = roomId;
    document.getElementById('modalRoomNumber').textContent = roomNumber;
    document.getElementById('modalHkStatusSelect').value = currentHkId;
    if (currentTaskStatus) {
      document.getElementById('modalTaskStatusSelect').value = currentTaskStatus;
    }
    document.getElementById('hkStatusModal').style.display = 'flex';
  }

  function closeStatusModal() {
    document.getElementById('hkStatusModal').style.display = 'none';
  }

  // Submit Single Room Status Change
  async function submitSingleRoomStatus() {
    const btn = document.getElementById('btnSubmitSingleStatus');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
    btn.disabled = true;

    const roomId = document.getElementById('modalRoomId').value;
    const hkStatusId = document.getElementById('modalHkStatusSelect').value;
    const taskStatus = document.getElementById('modalTaskStatusSelect').value;
    const opStatusId = document.getElementById('modalOpStatusSelect').value;
    const remarks = document.getElementById('modalRemarks').value;

    try {
      const res = await fetch(UPDATE_STATUS_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          room_id: roomId,
          housekeeping_status_id: hkStatusId,
          task_status: taskStatus,
          operational_status_id: opStatusId || null,
          remarks: remarks
        })
      });

      const data = await res.json();
      if (data.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        }
        closeStatusModal();
        setTimeout(() => location.reload(), 400);
      } else {
        alert(data.message || 'Status update failed.');
      }
    } catch (err) {
      console.error(err);
      alert('Error updating room status: ' + err.message);
    } finally {
      btn.innerHTML = originalText;
      btn.disabled = false;
    }
  }

  // Bulk Status Update
  async function submitBulkUpdate() {
    const checkedBoxes = Array.from(document.querySelectorAll('.room-select-box:checked'));
    if (checkedBoxes.length === 0) {
      alert('Please select at least one room.');
      return;
    }

    const roomIds = checkedBoxes.map(cb => cb.value);
    const hkStatusId = document.getElementById('bulkHkStatusSelect').value;

    if (!confirm(`Apply this cleaning status to ${roomIds.length} selected rooms?`)) {
      return;
    }

    try {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('Applying bulk update...', 'info');
      }

      const res = await fetch(BULK_UPDATE_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          room_ids: roomIds,
          housekeeping_status_id: hkStatusId
        })
      });

      const data = await res.json();
      if (data.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        }
        setTimeout(() => location.reload(), 400);
      } else {
        alert(data.message || 'Bulk update failed');
      }
    } catch (err) {
      console.error(err);
      alert('Error during bulk update: ' + err.message);
    }
  }

  // View Room History Modal
  async function viewRoomHistory(roomId, roomNumber) {
    document.getElementById('historyRoomNumber').textContent = roomNumber;
    const modal = document.getElementById('hkHistoryModal');
    const loading = document.getElementById('historyLoading');
    const content = document.getElementById('historyContent');
    const list = document.getElementById('timelineList');

    modal.style.display = 'flex';
    loading.style.display = 'block';
    content.style.display = 'none';
    list.innerHTML = '';

    try {
      const res = await fetch(`${ROOM_HISTORY_BASE_URL}/${roomId}/history`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();

      if (data.success) {
        const logs = data.hk_logs || [];
        if (logs.length === 0) {
          list.innerHTML = '<li class="text-slate-400 text-xs text-center py-3.5">No recorded history for this room.</li>';
        } else {
          logs.forEach(log => {
            const timeStr = log.created_at ? new Date(log.created_at).toLocaleString('en-US', {
              month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
            }) : 'Unknown date';

            const statusName = log.housekeeping_status ? log.housekeeping_status.name : 'Updated';
            const isClean = /clean|sanit|ready/i.test(statusName);

            const li = document.createElement('li');
            li.className = 'timeline-item';
            li.innerHTML = `
              <div class="timeline-dot ${isClean ? '!border-emerald-500' : '!border-amber-500'}"></div>
              <div class="timeline-time">${timeStr}</div>
              <div class="timeline-content">
                Marked as <strong class="${isClean ? 'text-emerald-700' : 'text-amber-700'}">${statusName}</strong>
                ${log.status ? ` &bull; <span class="text-[10px] uppercase text-slate-500">${log.status}</span>` : ''}
              </div>
            `;
            list.appendChild(li);
          });
        }
      }
    } catch (err) {
      console.error(err);
      list.innerHTML = `<li class="text-rose-500 text-xs py-2">Failed to load logs: ${err.message}</li>`;
    } finally {
      loading.style.display = 'none';
      content.style.display = 'block';
    }
  }

  function closeHistoryModal() {
    document.getElementById('hkHistoryModal').style.display = 'none';
  }

  window.addEventListener('click', function(e) {
    const statusModal = document.getElementById('hkStatusModal');
    const historyModal = document.getElementById('hkHistoryModal');
    if (e.target === statusModal) closeStatusModal();
    if (e.target === historyModal) closeHistoryModal();
  });
</script>
@endpush
@endsection
