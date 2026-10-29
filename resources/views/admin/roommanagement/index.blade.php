@extends('admin.layouts.app')

@section('title', 'Room Management Master - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 p-4 lg:p-6 space-y-6">
  <!-- Page Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <div class="w-2.5 h-8 bg-indigo-600 rounded-full"></div>
      <div>
        <h1 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight">Administrator • Master Data Management</h1>
        <p class="text-xs lg:text-sm text-slate-500 font-medium">Configure and manage live records for active hotel modules.</p>
      </div>
    </div>
    <div class="flex items-center gap-2.5">
      <button type="button" onclick="exportRoomsCSV()" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs hover:border-slate-300 transition-all flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-file-csv text-slate-500"></i> Export CSV
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">
    @include('admin.includes.sidebar')

    <section class="min-w-0 space-y-6">
      
      <!-- Header Card -->
      <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2.5">
            <h2 class="text-base font-bold text-slate-800 tracking-tight">Room Assets & Master</h2>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200/60" id="view-count">{{ count($rooms) }} Records</span>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">
            Manage individual room numbers, bedding setups, pax capacities, tariffs, amenities, and room status.
          </p>
        </div>
        <button type="button" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 self-start sm:self-auto cursor-pointer" onclick="openAddRoomModal()">
          <i class="fa-solid fa-plus-circle text-xs"></i> Add Room Asset
        </button>
      </div>

      <!-- Top 4 KPI Metrics Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Card 1: Total Rooms & Status -->
        <div class="relative bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
          <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
              <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Room Status & Assets</span>
              <div class="text-2xl font-black text-slate-800 font-mono mt-1" id="stat-total-rooms">{{ $totalRooms }}</div>
            </div>
            <span class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 text-base">
              <i class="fa-solid fa-door-open"></i>
            </span>
          </div>
          <div class="flex flex-wrap items-center gap-1.5 mt-3 pt-3 border-t border-slate-100" id="stat-status-pills">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
              <i class="fa-solid fa-circle-check text-[8px]"></i> <span id="stat-active-rooms">{{ $activeRooms ?? 0 }}</span> Active
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
              <i class="fa-solid fa-circle-xmark text-[8px]"></i> <span id="stat-inactive-rooms">{{ $inactiveRooms ?? 0 }}</span> Inactive
            </span>
          </div>
        </div>

        <!-- Card 2: Category Wise Rooms -->
        <div class="relative bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
          <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-pink-500 to-rose-500"></div>
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
              <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Category Wise Rooms</span>
              <div class="text-2xl font-black text-rose-600 font-mono mt-1">
                <span id="stat-category-total-rooms">{{ $totalRooms }}</span>
                <span class="text-xs font-bold text-slate-400 uppercase font-sans">Rooms</span>
              </div>
            </div>
            <span class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 text-base">
              <i class="fa-solid fa-tags"></i>
            </span>
          </div>
          <div class="flex flex-wrap items-center gap-1.5 mt-3 pt-3 border-t border-slate-100 max-h-16 overflow-y-auto" id="stat-category-pills">
            @if(isset($categoryWise) && count($categoryWise) > 0)
              @foreach($categoryWise as $cat => $cnt)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                  {{ $cat }}: <strong class="ml-1 font-black">{{ $cnt }}</strong>
                </span>
              @endforeach
            @else
              <span class="text-[11px] text-slate-400">All categories</span>
            @endif
          </div>
        </div>

        <!-- Card 3: Floor Wise Rooms -->
        <div class="relative bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
          <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-cyan-500"></div>
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
              <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Floor Wise Rooms</span>
              <div class="text-2xl font-black text-sky-600 font-mono mt-1">
                <span id="stat-floor-total-rooms">{{ $totalRooms }}</span>
                <span class="text-xs font-bold text-slate-400 uppercase font-sans">Rooms</span>
              </div>
            </div>
            <span class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 text-base">
              <i class="fa-solid fa-layer-group"></i>
            </span>
          </div>
          <div class="flex flex-wrap items-center gap-1.5 mt-3 pt-3 border-t border-slate-100 max-h-16 overflow-y-auto" id="stat-floor-pills">
            @if(isset($floorWise) && count($floorWise) > 0)
              @foreach($floorWise as $fl => $cnt)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200/60">
                  Floor {{ $fl }}: <strong class="ml-1 font-black">{{ $cnt }}</strong>
                </span>
              @endforeach
            @else
              <span class="text-[11px] text-slate-400">All floors</span>
            @endif
          </div>
        </div>

        <!-- Card 4: Amenities Wise Rooms -->
        <div class="relative bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between">
          <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0 flex-1">
              <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Amenities Wise Rooms</span>
              <div class="text-2xl font-black text-emerald-600 font-mono mt-1">
                <span id="stat-amenity-total-rooms">{{ $totalRooms }}</span>
                <span class="text-xs font-bold text-slate-400 uppercase font-sans">Rooms</span>
              </div>
            </div>
            <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-base">
              <i class="fa-solid fa-wand-magic-sparkles"></i>
            </span>
          </div>
          <div class="flex flex-wrap items-center gap-1.5 mt-3 pt-3 border-t border-slate-100 max-h-16 overflow-y-auto" id="stat-amenity-pills">
            @if(isset($amenityWise) && count($amenityWise) > 0)
              @foreach($amenityWise as $amn => $cnt)
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                  {{ $amn }}: <strong class="ml-1 font-black">{{ $cnt }}</strong>
                </span>
              @endforeach
            @else
              <span class="text-[11px] text-slate-400">All amenities</span>
            @endif
          </div>
        </div>
      </div>

      <!-- Control & Filter Toolbar -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <!-- Search & Filter Options -->
        <div class="flex flex-wrap items-center gap-2.5 flex-1">
          <div class="relative min-w-[180px] flex-1 max-w-xs">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="room-search-input" class="w-full h-9.5 pl-9 pr-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" value="{{ request('search') }}" placeholder="Search room no..." oninput="handleSearch(this.value)">
          </div>

          <select id="category-filter" class="h-9.5 px-3 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handleCategoryFilter(this.value)">
            <option value="ALL">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>

          <select id="floor-filter" class="h-9.5 px-3 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handleFloorFilter(this.value)">
            <option value="all">All Floors</option>
            @foreach($floors as $fl)
              <option value="{{ $fl->floor }}" {{ request('floor') == $fl->floor ? 'selected' : '' }}>Floor {{ $fl->floor }}</option>
            @endforeach
          </select>

          <select id="status-filter" class="h-9.5 px-3 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handleStatusFilter(this.value)">
            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Status</option>
            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
          </select>

          <select id="amenity-filter" class="h-9.5 px-3 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handleAmenityFilter(this.value)">
            <option value="all" {{ request('amenity', 'all') == 'all' ? 'selected' : '' }}>All Amenities</option>
            @foreach($amenities as $amn)
              <option value="{{ $amn->name }}" {{ request('amenity') == $amn->name ? 'selected' : '' }}>{{ $amn->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- Action Tools -->
        <div class="flex items-center gap-2 self-end lg:self-auto">
          <!-- View Toggle -->
          <div class="flex items-center p-1 bg-slate-100 rounded-xl">
            <button type="button" class="h-7.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-indigo-600 shadow-xs" id="btn-view-table" onclick="setViewMode('table')" title="Table View">
              <i class="fa-solid fa-table-list mr-1"></i> Table
            </button>
            <button type="button" class="h-7.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-slate-900" id="btn-view-grid" onclick="setViewMode('grid')" title="Cards Grid View">
              <i class="fa-solid fa-border-all mr-1"></i> Grid
            </button>
          </div>

          <!-- Page Size Selector -->
          <div class="flex items-center gap-1.5">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Rows:</span>
            <select id="page-size-select" class="h-9.5 px-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handlePageSizeChange(this.value)">
              <option value="10">10</option>
              <option value="20" selected>20</option>
              <option value="100">100</option>
              <option value="all">All</option>
            </select>
          </div>

          <button type="button" id="btn-bulk-delete-rooms" class="h-9.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-all hidden items-center gap-2 cursor-pointer" onclick="handleBulkDeleteRooms()">
            <i class="fa-solid fa-trash-can text-xs"></i> Delete Selected (<span id="bulk-selected-rooms-count">0</span>)
          </button>

          <button type="button" class="h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer" onclick="loadRoomsData()" title="Refresh Data">
            <i class="fa-solid fa-rotate text-slate-500 text-xs"></i>
          </button>
        </div>
      </div>

      <!-- TABLE VIEW CONTAINER -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden" id="rooms-table-container">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                <th class="w-10 px-4 py-3.5 text-center">
                  <input type="checkbox" id="select-all-rooms-check" onchange="toggleSelectAllRooms(this)" class="cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                </th>
                <th class="w-12 px-3 py-3.5 text-slate-400 font-mono">#</th>
                <th class="px-4 py-3.5">Room</th>
                <th class="px-4 py-3.5">Floor</th>
                <th class="px-4 py-3.5">Category</th>
                <th class="px-4 py-3.5">Bedding & Pax</th>
                <th class="px-4 py-3.5">Base Tariff</th>
                <th class="px-4 py-3.5">Status</th>
                <th class="px-4 py-3.5">Amenities</th>
                <th class="px-4 py-3.5 text-right w-36">Actions</th>
              </tr>
            </thead>
            <tbody id="rooms-tbody" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              <!-- Rendered via JavaScript -->
            </tbody>
          </table>
        </div>

        <!-- Table Pagination Footer -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
          <div class="text-xs text-slate-500 font-medium" id="pagination-info">
            Showing 1 to 20 of {{ count($rooms) }} records
          </div>
          <div class="flex items-center gap-1" id="pagination-nav">
            <!-- Rendered dynamically -->
          </div>
        </div>
      </div>

      <!-- GRID VIEW CONTAINER (Alternative View) -->
      <div id="rooms-grid-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 hidden">
        <!-- Dynamically rendered cards -->
      </div>

    </section>
  </div>
</main>

<!-- MODAL 1: Add New Room Asset -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="add-room-modal">
  <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-plus-circle"></i>
        </span>
        <span>Add New Room Asset • Hotel Sagar Sonnet</span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base" onclick="closeModal('add-room-modal')">&times;</button>
    </div>
    <form id="add-room-form" onsubmit="handleAddRoom(event)" class="flex flex-col flex-1 overflow-hidden">
      <div class="p-6 space-y-4 overflow-y-auto bg-slate-50/50">
        
        <!-- Section 1: Room Identification & Classification -->
        <div class="bg-white p-5 border border-slate-200 rounded-xl space-y-4 shadow-xs">
          <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 flex items-center gap-2">
            <i class="fa-solid fa-hotel"></i> Room Asset & Classification
          </h4>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Room Number <span class="text-rose-500">*</span>
              </label>
              <input type="text" id="new-room-no" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white font-mono font-bold text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. 501" required>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Floor <span class="text-rose-500">*</span>
              </label>
              <select id="new-room-floor" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
                <option value="">-- Select Floor --</option>
                @foreach($floors as $fl)
                  <option value="{{ $fl->floor }}">Floor {{ $fl->floor }} ({{ $fl->name }})</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Room Category <span class="text-rose-500">*</span>
              </label>
              <select id="new-room-cat" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required onchange="handleCategoryChange(this.value)">
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}" data-bed="{{ $cat->bedding_config }}" data-pax="{{ $cat->pax_capacity }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Base Tariff (₹ / Night) <span class="text-rose-500">*</span>
              </label>
              <input type="number" id="new-room-rate" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white font-mono font-bold text-sm text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" value="3500" required>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Status <span class="text-rose-500">*</span>
              </label>
              <select id="new-room-status" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
                <option value="Active" selected>Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Section 2: Bedding Setup & Capacity -->
        <div class="bg-white p-5 border border-slate-200 rounded-xl space-y-4 shadow-xs">
          <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 flex items-center gap-2">
            <i class="fa-solid fa-bed"></i> Bedding Setup & Capacity
          </h4>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Bedding Configuration <span class="text-rose-500">*</span>
              </label>
              <select id="new-room-bed" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
                @foreach($beddingConfigs as $bed)
                  <option value="{{ $bed->name }}">{{ $bed->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Pax Capacity <span class="text-[11px] text-indigo-600 font-semibold">(Auto-linked)</span>
              </label>
              <div class="h-9.5 px-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-2">
                <div class="flex items-center gap-1.5">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200/60">
                    <i class="fa-solid fa-user text-[9px]"></i> <span id="new-pax-adults-text">2 Adults</span>
                  </span>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                    <i class="fa-solid fa-child text-[9px]"></i> <span id="new-pax-children-text">1 Child</span>
                  </span>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-black bg-purple-50 text-purple-700 border border-purple-200/60">
                  <i class="fa-solid fa-users text-[9px]"></i> <span id="new-pax-total-text">Total 3 Pax</span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Section: Room Plan / Meal Package -->
        <div class="bg-white p-5 border border-slate-200 rounded-xl space-y-4 shadow-xs">
          <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 flex items-center gap-2">
            <i class="fa-solid fa-utensils"></i> Meal Plan / Package
          </h4>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              Plan <span class="text-[11px] text-slate-400 font-normal">(Searchable Dropdown)</span>
            </label>
            <select id="new-room-plan" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
              <option value="">-- No Plan / Room Only (EP) --</option>
              @foreach($plans as $plan)
                <option value="{{ $plan->id }}">{{ $plan->plan_name }} @if(!empty($plan->short_code))({{ $plan->short_code }})@endif @if($plan->price !== null && $plan->price > 0)- ₹{{ number_format($plan->price, 2) }}@endif</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Section 3: Amenities & Features -->
        <div class="bg-white p-5 border border-slate-200 rounded-xl space-y-4 shadow-xs">
          <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 flex items-center gap-2">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Amenities & Features
          </h4>
          
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5" id="add-amenities-container">
            @foreach($amenities as $amn)
              <label class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition-all text-xs font-semibold text-slate-700">
                <input type="checkbox" name="add_amenities[]" value="{{ $amn->id }}" data-name="{{ $amn->name }}" {{ in_array($amn->name, ['Free Wi-Fi', 'Balcony', 'Smart 55" TV', 'AC']) ? 'checked' : '' }} class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer">
                <i class="fa-solid {{ $amn->icon ?: 'fa-check' }} text-indigo-600 text-xs w-4"></i>
                <span class="truncate">{{ $amn->name }}</span>
              </label>
            @endforeach
          </div>
        </div>

      </div>

      <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex justify-end items-center gap-2.5">
        <button type="button" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('add-room-modal')">Cancel</button>
        <button type="submit" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 cursor-pointer">
          <i class="fa-solid fa-check text-xs"></i> Register Room Asset
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 2: Edit Room Asset -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="edit-room-modal">
  <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-pen-to-square"></i>
        </span>
        <span>Edit Room Asset: <span id="edit-modal-room-num" class="font-mono font-black text-indigo-600"></span></span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base" onclick="closeModal('edit-room-modal')">&times;</button>
    </div>
    <form id="edit-room-form" onsubmit="handleSaveEditRoom(event)" class="flex flex-col flex-1 overflow-hidden">
      <input type="hidden" id="edit-room-id">
      <div class="p-6 space-y-4 overflow-y-auto bg-slate-50/50">
        
        <!-- Section 1: Room Identification & Classification -->
        <div class="bg-white p-5 border border-slate-200 rounded-xl space-y-4 shadow-xs">
          <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 flex items-center gap-2">
            <i class="fa-solid fa-hotel"></i> Room Asset & Classification
          </h4>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Room Number <span class="text-rose-500">*</span>
              </label>
              <input type="text" id="edit-room-no" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white font-mono font-bold text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" required>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Floor <span class="text-rose-500">*</span>
              </label>
              <select id="edit-room-floor" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
                @foreach($floors as $fl)
                  <option value="{{ $fl->floor }}">Floor {{ $fl->floor }} ({{ $fl->name }})</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Room Category <span class="text-rose-500">*</span>
              </label>
              <select id="edit-room-cat" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}" data-bed="{{ $cat->bedding_config }}" data-pax="{{ $cat->pax_capacity }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Base Tariff (₹ / Night) <span class="text-rose-500">*</span>
              </label>
              <input type="number" id="edit-room-rate" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white font-mono font-bold text-sm text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" required>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Status <span class="text-rose-500">*</span>
              </label>
              <select id="edit-room-status" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Section 2: Bedding Setup & Capacity -->
        <div class="bg-white p-5 border border-slate-200 rounded-xl space-y-4 shadow-xs">
          <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 flex items-center gap-2">
            <i class="fa-solid fa-bed"></i> Bedding Setup & Capacity
          </h4>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Bedding Configuration <span class="text-rose-500">*</span>
              </label>
              <select id="edit-room-bed" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
                @foreach($beddingConfigs as $bed)
                  <option value="{{ $bed->name }}">{{ $bed->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Pax Capacity <span class="text-[11px] text-indigo-600 font-semibold">(Auto-linked)</span>
              </label>
              <div class="h-9.5 px-3 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-between gap-2">
                <div class="flex items-center gap-1.5">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200/60">
                    <i class="fa-solid fa-user text-[9px]"></i> <span id="edit-pax-adults-text">2 Adults</span>
                  </span>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                    <i class="fa-solid fa-child text-[9px]"></i> <span id="edit-pax-children-text">1 Child</span>
                  </span>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-black bg-purple-50 text-purple-700 border border-purple-200/60">
                  <i class="fa-solid fa-users text-[9px]"></i> <span id="edit-pax-total-text">Total 3 Pax</span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Section: Room Plan / Meal Package -->
        <div class="bg-white p-5 border border-slate-200 rounded-xl space-y-4 shadow-xs">
          <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 flex items-center gap-2">
            <i class="fa-solid fa-utensils"></i> Meal Plan / Package
          </h4>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              Plan <span class="text-[11px] text-slate-400 font-normal">(Searchable Dropdown)</span>
            </label>
            <select id="edit-room-plan" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
              <option value="">-- No Plan / Room Only (EP) --</option>
              @foreach($plans as $plan)
                <option value="{{ $plan->id }}">{{ $plan->plan_name }} @if(!empty($plan->short_code))({{ $plan->short_code }})@endif @if($plan->price !== null && $plan->price > 0)- ₹{{ number_format($plan->price, 2) }}@endif</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Section 3: Amenities & Features -->
        <div class="bg-white p-5 border border-slate-200 rounded-xl space-y-4 shadow-xs">
          <h4 class="text-xs font-black uppercase tracking-wider text-indigo-600 flex items-center gap-2">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Amenities & Features
          </h4>
          
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5" id="edit-amenities-container">
            @foreach($amenities as $amn)
              <label class="flex items-center gap-2.5 p-2.5 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-100 transition-all text-xs font-semibold text-slate-700">
                <input type="checkbox" name="edit_amenities[]" value="{{ $amn->id }}" data-name="{{ $amn->name }}" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer">
                <i class="fa-solid {{ $amn->icon ?: 'fa-check' }} text-indigo-600 text-xs w-4"></i>
                <span class="truncate">{{ $amn->name }}</span>
              </label>
            @endforeach
          </div>
        </div>

      </div>

      <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex justify-end items-center gap-2.5">
        <button type="button" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('edit-room-modal')">Cancel</button>
        <button type="submit" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 cursor-pointer">
          <i class="fa-solid fa-check text-xs"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 3: View Room Details Modal -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="view-room-modal">
  <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-door-open"></i>
        </span>
        <span>Room Asset Details</span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base" onclick="closeModal('view-room-modal')">&times;</button>
    </div>
    <div id="view-room-body" class="p-6 space-y-4 overflow-y-auto bg-slate-50/50">
      <!-- Populated dynamically via JavaScript -->
    </div>
    <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex justify-between items-center">
      <div id="view-room-bot-left"></div>
      <button type="button" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('view-room-modal')">Close</button>
    </div>
  </div>
</div>

<!-- MODAL 4: Room Maintenance Work Order Modal -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="maint-workorder-modal">
  <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-wrench"></i>
        </span>
        <span>Room Maintenance Work Order</span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base" onclick="closeModal('maint-workorder-modal')">&times;</button>
    </div>

    <form id="maint-workorder-form" onsubmit="handleSaveMaintenanceWorkOrder(event)">
      <input type="hidden" id="maint-room-id">
      <div class="p-6 space-y-4 bg-slate-50/50">
        <div class="text-[11px] font-black uppercase tracking-wider text-indigo-600 flex items-center gap-2">
          <i class="fa-solid fa-hotel"></i> WORK ORDER SPECIFICATIONS
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">
              ROOM CATEGORY
            </label>
            <input type="text" id="maint-room-cat" class="w-full h-9 px-3 rounded-xl border border-slate-200 bg-slate-100 font-semibold text-xs text-slate-700 cursor-not-allowed" readonly>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">
              FLOOR
            </label>
            <input type="text" id="maint-room-floor" class="w-full h-9 px-3 rounded-xl border border-slate-200 bg-slate-100 font-semibold text-xs text-slate-700 cursor-not-allowed" readonly>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">
              ROOM NUMBER
            </label>
            <input type="text" id="maint-room-no-display" class="w-full h-9 px-3 rounded-xl border border-slate-200 bg-slate-100 font-mono font-black text-xs text-indigo-600 cursor-not-allowed" readonly>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">
            MAINTENANCE REASON / WORK DESCRIPTION <span class="text-rose-500">*</span>
          </label>
          <input type="text" id="maint-reason" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. HVAC Air Conditioning & Compressor Service" required>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              ASSIGNED PERSONNEL
            </label>
            <input type="text" id="maint-engineer" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. Maintenance Technician">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">
              EXPECTED COMPLETION (DATE & TIME) <span class="text-rose-500">*</span>
            </label>
            <input type="datetime-local" id="maint-completion" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">
            NOTE
          </label>
          <textarea id="maint-note" rows="3" placeholder="Enter special instructions or notes..." class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"></textarea>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 flex items-center gap-3">
          <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0"></i>
          <p class="text-xs text-amber-800 font-medium">
            Setting this room into Maintenance will automatically prevent new guest check-ins from the Front Desk rack.
          </p>
        </div>
      </div>

      <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex justify-end items-center gap-2.5">
        <button type="button" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('maint-workorder-modal')">Cancel</button>
        <button type="submit" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 cursor-pointer">
          <i class="fa-solid fa-lock text-xs"></i> Put in Maintenance
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  let roomsData = @json($rooms);
  let selectedRoomIds = new Set();
  const masterCategories = @json($categories);
  const masterFloors = @json($floors);
  const masterAmenities = @json($amenities);
  const beddingConfigsData = @json($beddingConfigs);
  const baseUrl = "{{ route('admin.roommanagement.index') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function getAmenityName(val) {
    if (!val) return '';
    if (typeof masterAmenities !== 'undefined' && Array.isArray(masterAmenities)) {
      const found = masterAmenities.find(m => String(m.id) === String(val) || String(m.name).toLowerCase() === String(val).toLowerCase());
      if (found) return found.name;
    }
    return String(val);
  }

  function getPaxDataForBedding(beddingName) {
    const config = beddingConfigsData.find(b => b.name === beddingName);
    if (!config) {
      return { adults: 2, children: 0, total: 2, display: '2 Adults (2 Pax)' };
    }
    const adults = config.max_adults ?? 2;
    const children = config.max_children ?? 0;
    const total = config.max_total ?? (adults + children);
    const display = `${adults} Adult${adults > 1 ? 's' : ''}${children > 0 ? ' + ' + children + ' Child' : ''} (Max ${total} Pax)`;
    return { adults, children, total, display };
  }

  function updateNewRoomPaxCapacity(bedName) {
    const name = bedName || $('#new-room-bed').val();
    const data = getPaxDataForBedding(name);
    const adultsEl = document.getElementById('new-pax-adults-text');
    const childrenEl = document.getElementById('new-pax-children-text');
    const totalEl = document.getElementById('new-pax-total-text');

    if (adultsEl) adultsEl.textContent = `${data.adults} Adult${data.adults > 1 ? 's' : ''}`;
    if (childrenEl) childrenEl.textContent = `${data.children} Child${data.children === 1 ? '' : 'ren'}`;
    if (totalEl) totalEl.textContent = `Total ${data.total} Pax`;
  }

  function updateEditRoomPaxCapacity(bedName) {
    const name = bedName || $('#edit-room-bed').val();
    const data = getPaxDataForBedding(name);
    const adultsEl = document.getElementById('edit-pax-adults-text');
    const childrenEl = document.getElementById('edit-pax-children-text');
    const totalEl = document.getElementById('edit-pax-total-text');

    if (adultsEl) adultsEl.textContent = `${data.adults} Adult${data.adults > 1 ? 's' : ''}`;
    if (childrenEl) childrenEl.textContent = `${data.children} Child${data.children === 1 ? '' : 'ren'}`;
    if (totalEl) totalEl.textContent = `Total ${data.total} Pax`;
  }

  const urlParams = new URLSearchParams(window.location.search);
  let searchQuery = urlParams.get('search') || '{{ request('search') }}' || '';
  let categoryFilter = urlParams.get('category') || '{{ request('category') }}' || 'ALL';
  let floorFilter = urlParams.get('floor') || '{{ request('floor') }}' || 'all';
  let statusFilter = urlParams.get('status') || '{{ request('status') }}' || 'all';
  let amenityFilter = urlParams.get('amenity') || '{{ request('amenity') }}' || 'all';
  let viewMode = urlParams.get('view') || 'table';
  let currentPage = parseInt(urlParams.get('page') || '1', 10);
  let pageSize = urlParams.get('per_page') || '20';

  function updateUrlParams() {
    const params = new URLSearchParams();
    if (searchQuery) params.set('search', searchQuery);
    if (categoryFilter && categoryFilter !== 'ALL') params.set('category', categoryFilter);
    if (floorFilter && floorFilter !== 'all') params.set('floor', floorFilter);
    if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);
    if (amenityFilter && amenityFilter !== 'all') params.set('amenity', amenityFilter);
    if (viewMode !== 'table') params.set('view', viewMode);
    if (currentPage > 1) params.set('page', currentPage);
    if (pageSize !== '20') params.set('per_page', pageSize);

    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState(null, '', newUrl);
  }

  function initSelect2() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
      $('#category-filter').select2({ width: '140px' });
      $('#floor-filter').select2({ width: '130px' });
      $('#status-filter').select2({ width: '130px' });
      $('#amenity-filter').select2({ width: '150px' });

      if (categoryFilter && categoryFilter !== 'ALL') $('#category-filter').val(categoryFilter).trigger('change.select2');
      if (floorFilter && floorFilter !== 'all') $('#floor-filter').val(floorFilter).trigger('change.select2');
      if (statusFilter && statusFilter !== 'all') $('#status-filter').val(statusFilter).trigger('change.select2');
      if (amenityFilter && amenityFilter !== 'all') $('#amenity-filter').val(amenityFilter).trigger('change.select2');

      $('#category-filter').on('change', function() { handleCategoryFilter(this.value); });
      $('#floor-filter').on('change', function() { handleFloorFilter(this.value); });
      $('#status-filter').on('change', function() { handleStatusFilter(this.value); });
      $('#amenity-filter').on('change', function() { handleAmenityFilter(this.value); });

      $('#new-room-floor, #new-room-cat, #new-room-bed, #new-room-status, #new-room-plan').select2({
        dropdownParent: $('#add-room-modal')
      });
      $('#edit-room-floor, #edit-room-cat, #edit-room-bed, #edit-room-status, #edit-room-plan').select2({
        dropdownParent: $('#edit-room-modal')
      });

      $('#new-room-bed').on('change', function() { updateNewRoomPaxCapacity(this.value); });
      $('#edit-room-bed').on('change', function() { updateEditRoomPaxCapacity(this.value); });
    }
  }

  function openAddRoomModal() {
    document.getElementById('new-room-no').value = '';
    document.getElementById('new-room-rate').value = '3500';
    
    if (typeof $ !== 'undefined' && $.fn.select2) {
      $('#new-room-floor').val($('#new-room-floor option:eq(1)').val()).trigger('change');
      $('#new-room-cat').val($('#new-room-cat option:eq(1)').val()).trigger('change');
      const defaultBed = $('#new-room-bed option:eq(0)').val();
      $('#new-room-bed').val(defaultBed).trigger('change');
      updateNewRoomPaxCapacity(defaultBed);
      $('#new-room-status').val('Active').trigger('change');
      $('#new-room-plan').val('').trigger('change');
    }

    const defaultAmn = ['Free Wi-Fi', 'Balcony', 'Smart 55" TV', 'AC'];
    document.querySelectorAll('#add-amenities-container input[type="checkbox"]').forEach(cb => {
      cb.checked = defaultAmn.includes(cb.value) || defaultAmn.includes(cb.dataset.name);
    });

    openModal('add-room-modal');
  }

  function handleCategoryChange(catName) {
    const selectedOption = $('#new-room-cat option:selected');
    const bed = selectedOption.data('bed');

    if (bed && $('#new-room-bed').length) {
      $('#new-room-bed').val(bed).trigger('change');
    }

    const defaultTariffs = {
      'Deluxe Room': 3500,
      'DELUXE': 3500,
      'Super Deluxe': 5200,
      'SUPER DELUXE': 5200,
      'Suite Luxury': 8500,
      'SUITE': 8500,
      'Executive Penthouse': 11000
    };
    if (defaultTariffs[catName]) {
      document.getElementById('new-room-rate').value = defaultTariffs[catName];
    }
  }

  function openEditRoomModal(id) {
    const room = roomsData.find(r => r.id === id);
    if (!room) return;

    document.getElementById('edit-room-id').value = room.id;
    document.getElementById('edit-modal-room-num').textContent = '#' + room.room_number;
    document.getElementById('edit-room-no').value = room.room_number;
    document.getElementById('edit-room-rate').value = room.rate;

    if (typeof $ !== 'undefined' && $.fn.select2) {
      $('#edit-room-floor').val(room.floor).trigger('change');
      $('#edit-room-cat').val(room.category).trigger('change');
      $('#edit-room-bed').val(room.bedding_config).trigger('change');
      updateEditRoomPaxCapacity(room.bedding_config);
      $('#edit-room-status').val(room.status || 'Active').trigger('change');
      $('#edit-room-plan').val(room.plan_id ? String(room.plan_id) : '').trigger('change');
    }

    const currentAmenities = Array.isArray(room.amenities) ? room.amenities : [];
    document.querySelectorAll('#edit-amenities-container input[type="checkbox"]').forEach(cb => {
      cb.checked = currentAmenities.some(a => String(a) === String(cb.value) || String(a) === String(cb.dataset.name));
    });

    openModal('edit-room-modal');
  }

  async function handleAddRoom(e) {
    e.preventDefault();
    const room_number = document.getElementById('new-room-no').value.trim();
    const floor = document.getElementById('new-room-floor').value;
    const category = document.getElementById('new-room-cat').value;
    const rate = document.getElementById('new-room-rate').value;
    const bedding_config = document.getElementById('new-room-bed').value;
    const plan_id = document.getElementById('new-room-plan') ? document.getElementById('new-room-plan').value : null;
    const status = document.getElementById('new-room-status').value || 'Active';

    const checkedAmenities = [];
    document.querySelectorAll('#add-amenities-container input[type="checkbox"]:checked').forEach(cb => {
      checkedAmenities.push(cb.value);
    });

    if (!room_number || !floor || !category || !rate) {
      PmsAlert.error('Validation Error', 'Please complete all required fields.');
      return;
    }

    const payload = {
      room_number,
      floor,
      category,
      rate,
      bedding_config,
      plan_id: plan_id || null,
      status,
      amenities: checkedAmenities
    };

    try {
      const res = await fetch(baseUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (res.ok && data.success) {
        closeModal('add-room-modal');
        PmsAlert.toast(data.message || 'Room registered successfully!');
        loadRoomsData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to add room.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  async function handleSaveEditRoom(e) {
    e.preventDefault();
    const id = document.getElementById('edit-room-id').value;
    const room_number = document.getElementById('edit-room-no').value.trim();
    const floor = document.getElementById('edit-room-floor').value;
    const category = document.getElementById('edit-room-cat').value;
    const rate = document.getElementById('edit-room-rate').value;
    const bedding_config = document.getElementById('edit-room-bed').value;
    const plan_id = document.getElementById('edit-room-plan') ? document.getElementById('edit-room-plan').value : null;
    const status = document.getElementById('edit-room-status').value || 'Active';

    const checkedAmenities = [];
    document.querySelectorAll('#edit-amenities-container input[type="checkbox"]:checked').forEach(cb => {
      checkedAmenities.push(cb.value);
    });

    if (!room_number || !floor || !category || !rate) {
      PmsAlert.error('Validation Error', 'Please complete all required fields.');
      return;
    }

    const payload = {
      room_number,
      floor,
      category,
      rate,
      bedding_config,
      plan_id: plan_id || null,
      status,
      amenities: checkedAmenities
    };

    try {
      const res = await fetch(`${baseUrl}/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (res.ok && data.success) {
        closeModal('edit-room-modal');
        PmsAlert.toast(data.message || 'Room updated successfully!');
        loadRoomsData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to update room.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  function updateRoomsBulkActionUI() {
    const btn = document.getElementById('btn-bulk-delete-rooms');
    const countSpan = document.getElementById('bulk-selected-rooms-count');
    const selectAllCheck = document.getElementById('select-all-rooms-check');

    if (btn && countSpan) {
      countSpan.textContent = selectedRoomIds.size;
      if (selectedRoomIds.size > 0) {
        btn.classList.remove('hidden');
        btn.classList.add('inline-flex');
      } else {
        btn.classList.add('hidden');
        btn.classList.remove('inline-flex');
      }
    }

    if (selectAllCheck) {
      const filtered = getFilteredRooms();
      if (filtered.length === 0) {
        selectAllCheck.checked = false;
        selectAllCheck.indeterminate = false;
      } else {
        const allSelected = filtered.every(r => selectedRoomIds.has(r.id));
        const someSelected = filtered.some(r => selectedRoomIds.has(r.id));
        selectAllCheck.checked = allSelected;
        selectAllCheck.indeterminate = someSelected && !allSelected;
      }
    }
  }

  function handleRoomSelect(checkbox, id) {
    if (checkbox.checked) {
      selectedRoomIds.add(id);
    } else {
      selectedRoomIds.delete(id);
    }
    updateRoomsBulkActionUI();
  }

  function toggleSelectAllRooms(masterCheckbox) {
    const filtered = getFilteredRooms();
    if (masterCheckbox.checked) {
      filtered.forEach(r => selectedRoomIds.add(r.id));
    } else {
      filtered.forEach(r => selectedRoomIds.delete(r.id));
    }
    renderView();
  }

  async function handleBulkDeleteRooms() {
    if (selectedRoomIds.size === 0) return;
    const count = selectedRoomIds.size;
    const result = await PmsAlert.confirmDelete(`Delete ${count} Room Asset${count > 1 ? 's' : ''}?`, 'Selected rooms will be permanently removed from inventory.');
    if (result.isConfirmed) {
      try {
        const res = await fetch(`${baseUrl}/bulk-delete`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ ids: Array.from(selectedRoomIds) })
        });
        const data = await res.json();
        if (res.ok && data.success) {
          PmsAlert.toast(data.message || 'Rooms deleted successfully!');
          selectedRoomIds.clear();
          await loadRoomsData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete selected rooms.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'An error occurred during bulk deletion.');
      }
    }
  }

  async function deleteRoom(id) {
    const room = roomsData.find(r => r.id === id);
    const result = await PmsAlert.confirmDelete(`Delete Room #${room ? room.room_number : id}?`, 'This room asset will be permanently removed from inventory.');
    if (result.isConfirmed) {
      try {
        const res = await fetch(`${baseUrl}/${id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          }
        });
        const data = await res.json();
        if (res.ok && data.success) {
          PmsAlert.toast('Room deleted successfully!');
          selectedRoomIds.delete(id);
          loadRoomsData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete room.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'An error occurred during deletion.');
      }
    }
  }

  function toggleRoomMaintenance(id) {
    const room = roomsData.find(r => r.id === id);
    if (!room) return;

    if (room.status === 'Maintenance' || room.status === 'Under Maintenance') {
      PmsAlert.confirm(
        'Return to In-Service?',
        `Room #${room.room_number} is currently Under Maintenance. Complete work order and return room to Active In-Service?`,
        '<i class="fa-solid fa-check mr-1.5"></i> Yes, Return to In-Service',
        'question'
      ).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const res = await fetch(`${baseUrl}/${id}/toggle-maintenance`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
              },
              body: JSON.stringify({})
            });
            const data = await res.json();
            if (res.ok && data.success) {
              PmsAlert.toast(data.message || `Room #${room.room_number} returned to Active In-Service!`);
              loadRoomsData();
            } else {
              PmsAlert.error('Error', data.message || 'Failed to update maintenance status.');
            }
          } catch (err) {
            PmsAlert.error('Server Error', 'Failed to update maintenance status.');
          }
        }
      });
      return;
    }

    document.getElementById('maint-room-id').value = room.id;
    document.getElementById('maint-room-cat').value = room.category || 'Standard';
    document.getElementById('maint-room-floor').value = 'Floor ' + (room.floor || '1');
    document.getElementById('maint-room-no-display').value = `Room ${room.room_number} (${room.category || 'Standard'})`;

    const futureDate = new Date(Date.now() + 48 * 3600 * 1000);
    const localIso = new Date(futureDate.getTime() - (futureDate.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
    document.getElementById('maint-completion').value = localIso;
    document.getElementById('maint-reason').value = '';
    document.getElementById('maint-engineer').value = '';
    const noteEl = document.getElementById('maint-note');
    if (noteEl) noteEl.value = '';

    openModal('maint-workorder-modal');
  }

  async function handleSaveMaintenanceWorkOrder(e) {
    e.preventDefault();
    const id = document.getElementById('maint-room-id').value;
    const reason = document.getElementById('maint-reason').value;
    const engineer = document.getElementById('maint-engineer').value;
    const completion = document.getElementById('maint-completion').value;
    const noteEl = document.getElementById('maint-note');
    const note = noteEl ? noteEl.value : '';

    try {
      const res = await fetch(`${baseUrl}/${id}/toggle-maintenance`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ reason, engineer, completion, note })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        closeModal('maint-workorder-modal');
        PmsAlert.toast(data.message || 'Room placed into maintenance successfully!');
        loadRoomsData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to place room into maintenance.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with server.');
    }
  }

  async function loadRoomsData() {
    updateUrlParams();
    try {
      const params = new URLSearchParams();
      if (searchQuery) params.set('search', searchQuery);
      if (categoryFilter && categoryFilter !== 'ALL') params.set('category', categoryFilter);
      if (floorFilter && floorFilter !== 'all') params.set('floor', floorFilter);
      if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);
      if (amenityFilter && amenityFilter !== 'all') params.set('amenity', amenityFilter);

      const res = await fetch(`${baseUrl}?${params.toString()}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success) {
        roomsData = data.data;
        renderView();
      }
    } catch (err) {
      console.error(err);
    }
  }

  let searchDebounceTimer = null;
  function handleSearch(val) {
    searchQuery = (val || '').trim();
    currentPage = 1;
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
      loadRoomsData();
    }, 250);
  }

  function handleCategoryFilter(val) {
    categoryFilter = val || 'ALL';
    currentPage = 1;
    loadRoomsData();
  }

  function handleFloorFilter(val) {
    floorFilter = val;
    currentPage = 1;
    loadRoomsData();
  }

  function handleStatusFilter(val) {
    statusFilter = val;
    currentPage = 1;
    loadRoomsData();
  }

  function handleAmenityFilter(val) {
    amenityFilter = val;
    currentPage = 1;
    loadRoomsData();
  }

  function handlePageSizeChange(val) {
    pageSize = val === 'all' ? 'all' : parseInt(val, 10);
    currentPage = 1;
    updateUrlParams();
    renderView();
  }

  function goToPage(page) {
    currentPage = page;
    updateUrlParams();
    renderView();
  }

  function setViewMode(mode) {
    viewMode = mode;
    updateUrlParams();
    const btnTable = document.getElementById('btn-view-table');
    const btnGrid = document.getElementById('btn-view-grid');
    const tableContainer = document.getElementById('rooms-table-container');
    const gridContainer = document.getElementById('rooms-grid-container');

    if (mode === 'table') {
      btnTable.className = 'h-7.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-indigo-600 shadow-xs';
      btnGrid.className = 'h-7.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-slate-900';
      tableContainer.classList.remove('hidden');
      gridContainer.classList.add('hidden');
    } else {
      btnGrid.className = 'h-7.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-indigo-600 shadow-xs';
      btnTable.className = 'h-7.5 px-3 rounded-lg text-xs font-bold transition-all cursor-pointer text-slate-600 hover:text-slate-900';
      tableContainer.classList.add('hidden');
      gridContainer.classList.remove('hidden');
    }
    renderView();
  }

  function getFilteredRooms() {
    return roomsData.filter(room => {
      const matchSearch = !searchQuery ||
        (room.room_number && room.room_number.toLowerCase().includes(searchQuery.toLowerCase())) ||
        (room.floor && room.floor.toString().toLowerCase().includes(searchQuery.toLowerCase())) ||
        (room.category && room.category.toLowerCase().includes(searchQuery.toLowerCase())) ||
        (room.bedding_config && room.bedding_config.toLowerCase().includes(searchQuery.toLowerCase())) ||
        (room.pax_capacity && room.pax_capacity.toLowerCase().includes(searchQuery.toLowerCase())) ||
        (room.status && room.status.toLowerCase().includes(searchQuery.toLowerCase())) ||
        (room.guest_name && room.guest_name.toLowerCase().includes(searchQuery.toLowerCase()));

      const matchCat = categoryFilter === 'ALL' || (room.category && room.category.toUpperCase() === categoryFilter.toUpperCase());
      const matchFloor = floorFilter === 'all' || (room.floor && room.floor.toString() === floorFilter.toString());
      const matchStatus = statusFilter === 'all' || (room.status && room.status.toLowerCase() === statusFilter.toLowerCase());
      
      const roomAmenities = Array.isArray(room.amenities) ? room.amenities : [];
      const matchAmenity = amenityFilter === 'all' || roomAmenities.includes(amenityFilter);

      return matchSearch && matchCat && matchFloor && matchStatus && matchAmenity;
    });
  }

  function updateKPIStats(filteredRooms) {
    const total = filteredRooms.length;
    const active = filteredRooms.filter(r => (r.status || 'Active').toLowerCase() === 'active').length;
    const inactive = filteredRooms.filter(r => (r.status || '').toLowerCase() === 'inactive').length;

    const catMap = {};
    masterCategories.forEach(c => { catMap[c.name] = 0; });
    filteredRooms.forEach(r => {
      const cat = r.category || 'Unassigned';
      catMap[cat] = (catMap[cat] || 0) + 1;
    });

    const floorMap = {};
    masterFloors.forEach(f => { floorMap[f.floor] = 0; });
    filteredRooms.forEach(r => {
      const fl = r.floor !== null && r.floor !== undefined ? r.floor.toString() : '1';
      floorMap[fl] = (floorMap[fl] || 0) + 1;
    });

    const amnMap = {};
    masterAmenities.forEach(a => { amnMap[a.name] = 0; });
    filteredRooms.forEach(r => {
      if (Array.isArray(r.amenities)) {
        r.amenities.forEach(a => {
          amnMap[a] = (amnMap[a] || 0) + 1;
        });
      }
    });

    const totalEl = document.getElementById('stat-total-rooms');
    const activeEl = document.getElementById('stat-active-rooms');
    const inactiveEl = document.getElementById('stat-inactive-rooms');
    const catTotalEl = document.getElementById('stat-category-total-rooms');
    const floorTotalEl = document.getElementById('stat-floor-total-rooms');
    const amnTotalEl = document.getElementById('stat-amenity-total-rooms');

    if (totalEl) totalEl.textContent = total;
    if (activeEl) activeEl.textContent = active;
    if (inactiveEl) inactiveEl.textContent = inactive;
    if (catTotalEl) catTotalEl.textContent = total;
    if (floorTotalEl) floorTotalEl.textContent = total;
    if (amnTotalEl) amnTotalEl.textContent = total;

    const statusPillsEl = document.getElementById('stat-status-pills');
    if (statusPillsEl) {
      statusPillsEl.innerHTML = `
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
          <i class="fa-solid fa-circle-check text-[8px]"></i> <span id="stat-active-rooms">${active}</span> Active
        </span>
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
          <i class="fa-solid fa-circle-xmark text-[8px]"></i> <span id="stat-inactive-rooms">${inactive}</span> Inactive
        </span>
      `;
    }

    const catPillsEl = document.getElementById('stat-category-pills');
    if (catPillsEl) {
      const entries = Object.entries(catMap);
      if (entries.length === 0) {
        catPillsEl.innerHTML = '<span class="text-[11px] text-slate-400">No categories</span>';
      } else {
        catPillsEl.innerHTML = entries.map(([cat, cnt]) => `
          <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60 whitespace-nowrap">
            ${cat}: <strong class="ml-1 font-black">${cnt}</strong>
          </span>
        `).join('');
      }
    }

    const floorPillsEl = document.getElementById('stat-floor-pills');
    if (floorPillsEl) {
      const entries = Object.entries(floorMap);
      if (entries.length === 0) {
        floorPillsEl.innerHTML = '<span class="text-[11px] text-slate-400">No floors</span>';
      } else {
        floorPillsEl.innerHTML = entries.map(([fl, cnt]) => `
          <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200/60 whitespace-nowrap">
            Floor ${fl}: <strong class="ml-1 font-black">${cnt}</strong>
          </span>
        `).join('');
      }
    }

    const amnPillsEl = document.getElementById('stat-amenity-pills');
    if (amnPillsEl) {
      const entries = Object.entries(amnMap);
      if (entries.length === 0) {
        amnPillsEl.innerHTML = '<span class="text-[11px] text-slate-400">No amenities</span>';
      } else {
        amnPillsEl.innerHTML = entries.map(([amn, cnt]) => `
          <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 whitespace-nowrap">
            ${amn}: <strong class="ml-1 font-black">${cnt}</strong>
          </span>
        `).join('');
      }
    }
  }

  function renderView() {
    const filtered = getFilteredRooms();
    const totalCount = filtered.length;
    const totalPages = pageSize === 'all' ? 1 : Math.max(1, Math.ceil(totalCount / pageSize));

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIdx = pageSize === 'all' ? 0 : (currentPage - 1) * pageSize;
    const endIdx = pageSize === 'all' ? totalCount : Math.min(startIdx + pageSize, totalCount);
    const pageItems = filtered.slice(startIdx, endIdx);

    updateKPIStats(filtered);

    const viewCountBadge = document.getElementById('view-count');
    if (viewCountBadge) {
      viewCountBadge.textContent = `${totalCount} Records`;
    }

    const tbody = document.getElementById('rooms-tbody');
    if (!pageItems.length) {
      tbody.innerHTML = `<tr><td colspan="10" class="px-6 py-12 text-center text-slate-400"><i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>No matching room assets found.</td></tr>`;
    } else {
      let tableHtml = '';
      pageItems.forEach((room, idx) => {
        const isSelected = selectedRoomIds.has(room.id);
        let catBadgeClass = 'bg-sky-50 text-sky-700 border-sky-200/60';
        if (room.category && room.category.includes('SUPER')) catBadgeClass = 'bg-purple-50 text-purple-700 border-purple-200/60';
        else if (room.category && room.category.includes('SUITE')) catBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200/60';
        else if (room.category && room.category.includes('EXEC')) catBadgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200/60';

        const isInactive = room.status && room.status.toLowerCase() === 'inactive';
        let statusBadge = isInactive
          ? '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactive</span>'
          : '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>';

        const amenitiesList = Array.isArray(room.amenities) ? room.amenities : [];
        const amenitiesHtml = amenitiesList.slice(0, 3).map(a => `<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium">${getAmenityName(a)}</span>`).join(' ') +
          (amenitiesList.length > 3 ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold">+${amenitiesList.length - 3}</span>` : '');

        tableHtml += `
          <tr class="hover:bg-slate-50/70 transition-colors" data-id="${room.id}">
            <td class="px-4 py-3 text-center">
              <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" value="${room.id}" ${isSelected ? 'checked' : ''} onchange="handleRoomSelect(this, ${room.id})">
            </td>
            <td class="px-3 py-3 font-mono font-bold text-slate-400 text-[11px]">${startIdx + idx + 1}</td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                #${room.room_number}
              </span>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">Floor ${room.floor}</span>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold border ${catBadgeClass}">${room.category}</span>
            </td>
            <td class="px-4 py-3">
              <div class="text-xs font-bold text-slate-800">${room.bedding_config || 'Standard Bed'}</div>
              <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                <i class="fa-solid fa-user-group text-[9px]"></i> ${room.pax_capacity || '2 Adults'}
              </div>
              ${(room.plan_relation || room.planRelation) ? `<div class="text-[11px] text-indigo-600 font-bold mt-0.5 flex items-center gap-1"><i class="fa-solid fa-utensils text-[9px]"></i> ${(room.plan_relation || room.planRelation).plan_name}</div>` : ''}
            </td>
            <td class="px-4 py-3">
              <div class="font-mono font-black text-xs text-indigo-600">₹ ${Number(room.rate).toLocaleString()}</div>
            </td>
            <td class="px-4 py-3">
              ${statusBadge}
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-1 max-w-[200px]">
                ${amenitiesHtml || '<span class="text-slate-400 text-[11px]">None</span>'}
              </div>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="inline-flex items-center gap-1 justify-end">
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openViewRoomModal(${room.id})" title="View Details">
                  <i class="fa-solid fa-eye text-xs"></i>
                </button>
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openEditRoomModal(${room.id})" title="Edit Room Asset">
                  <i class="fa-solid fa-pen text-xs"></i>
                </button>
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="deleteRoom(${room.id})" title="Delete Room">
                  <i class="fa-solid fa-trash text-xs"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      tbody.innerHTML = tableHtml;
    }

    // Render Grid View
    const gridContainer = document.getElementById('rooms-grid-container');
    if (!pageItems.length) {
      gridContainer.innerHTML = `<div class="col-span-full px-6 py-12 text-center text-slate-400"><i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>No matching room assets found.</div>`;
    } else {
      let gridHtml = '';
      pageItems.forEach(room => {
        let catBadgeClass = 'bg-sky-50 text-sky-700 border-sky-200/60';
        if (room.category && room.category.includes('SUPER')) catBadgeClass = 'bg-purple-50 text-purple-700 border-purple-200/60';
        else if (room.category && room.category.includes('SUITE')) catBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200/60';
        else if (room.category && room.category.includes('EXEC')) catBadgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200/60';

        const isInactive = room.status && room.status.toLowerCase() === 'inactive';
        let statusBadge = isInactive
          ? '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactive</span>'
          : '<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>';

        const amenitiesList = Array.isArray(room.amenities) ? room.amenities : [];
        const amenitiesHtml = amenitiesList.slice(0, 3).map(a => `<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium">${getAmenityName(a)}</span>`).join(' ') +
          (amenitiesList.length > 3 ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold">+${amenitiesList.length - 3}</span>` : '');

        gridHtml += `
          <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between p-4.5 border-t-4 ${isInactive ? 'border-t-rose-500' : 'border-t-emerald-500'}">
            <div class="space-y-3">
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                  <span class="font-mono font-black text-sm text-slate-800">Room ${room.room_number}</span>
                  <span class="text-[11px] font-bold text-slate-400">Floor ${room.floor}</span>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold border ${catBadgeClass}">${room.category}</span>
              </div>

              <div class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                <i class="fa-solid fa-bed text-indigo-600 text-[11px]"></i> ${room.bedding_config || 'Standard'} • ${room.pax_capacity || '2 Adults'}
              </div>

              ${(room.plan_relation || room.planRelation) ? `
              <div class="text-[11px] font-bold text-indigo-600 flex items-center gap-1.5">
                <i class="fa-solid fa-utensils text-[10px]"></i> ${(room.plan_relation || room.planRelation).plan_name}
              </div>` : ''}

              <div class="flex flex-wrap gap-1">
                ${amenitiesHtml || '<span class="text-slate-400 text-[11px]">No amenities</span>'}
              </div>

              <div>
                ${statusBadge}
              </div>
            </div>

            <div class="pt-3.5 mt-3.5 border-t border-slate-100 flex items-center justify-between">
              <div class="font-mono font-black text-sm text-slate-800">
                ₹ ${Number(room.rate).toLocaleString()} <span class="text-[10px] text-slate-400 font-sans font-normal">/ night</span>
              </div>
              <div class="inline-flex items-center gap-1">
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openViewRoomModal(${room.id})" title="View Details">
                  <i class="fa-solid fa-eye text-xs"></i>
                </button>
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openEditRoomModal(${room.id})" title="Edit">
                  <i class="fa-solid fa-pen text-xs"></i>
                </button>
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="deleteRoom(${room.id})" title="Delete">
                  <i class="fa-solid fa-trash text-xs"></i>
                </button>
              </div>
            </div>
          </div>
        `;
      });
      gridContainer.innerHTML = gridHtml;
    }

    const infoEl = document.getElementById('pagination-info');
    if (totalCount === 0) {
      infoEl.textContent = 'Showing 0 to 0 of 0 records';
    } else {
      infoEl.innerHTML = `Showing <strong class="text-slate-800">${startIdx + 1}</strong> to <strong class="text-slate-800">${endIdx}</strong> of <strong class="text-slate-800">${totalCount}</strong> records${totalCount !== roomsData.length ? ' (filtered from ' + roomsData.length + ' total)' : ''}`;
    }

    renderPaginationNav(totalPages);
    updateRoomsBulkActionUI();
  }

  function renderPaginationNav(totalPages) {
    const nav = document.getElementById('pagination-nav');
    if (!nav) return;

    if (totalPages <= 1) {
      nav.innerHTML = `
        <button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-300 text-xs flex items-center justify-center cursor-not-allowed" disabled><i class="fa-solid fa-chevron-left text-[10px]"></i></button>
        <button class="w-8 h-8 rounded-lg bg-indigo-600 text-white text-xs font-bold flex items-center justify-center shadow-xs">1</button>
        <button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-300 text-xs flex items-center justify-center cursor-not-allowed" disabled><i class="fa-solid fa-chevron-right text-[10px]"></i></button>
      `;
      return;
    }

    let html = '';
    html += `<button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} title="Previous Page"><i class="fa-solid fa-chevron-left text-[10px]"></i></button>`;

    const delta = 1;
    const range = [];
    const rangeWithDots = [];

    for (let i = 1; i <= totalPages; i++) {
      if (i === 1 || i === totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
        range.push(i);
      }
    }

    let prev = 0;
    for (const i of range) {
      if (prev) {
        if (i - prev === 2) {
          rangeWithDots.push(prev + 1);
        } else if (i - prev !== 1) {
          rangeWithDots.push('...');
        }
      }
      rangeWithDots.push(i);
      prev = i;
    }

    rangeWithDots.forEach(page => {
      if (page === '...') {
        html += `<span class="w-8 h-8 text-slate-400 text-xs flex items-center justify-center font-bold">...</span>`;
      } else {
        const isActive = page === currentPage;
        html += `<button class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center transition-all cursor-pointer ${isActive ? 'bg-indigo-600 text-white shadow-xs' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50'}" onclick="goToPage(${page})">${page}</button>`;
      }
    });

    html += `<button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} title="Next Page"><i class="fa-solid fa-chevron-right text-[10px]"></i></button>`;

    nav.innerHTML = html;
  }

  function openViewRoomModal(id) {
    const room = roomsData.find(r => r.id === id);
    if (!room) return;

    const catName = room.category || (room.categoryRelation ? room.categoryRelation.name : 'Deluxe');
    const floorName = room.floorRelation && (room.floorRelation.name || room.floorRelation.floor)
      ? (room.floorRelation.name || ('Floor ' + room.floorRelation.floor))
      : (room.floor ? ('Floor ' + room.floor) : 'Floor 1');
    const beddingName = room.bedding_config || (room.beddingConfigRelation ? room.beddingConfigRelation.name : 'Standard Configuration');
    const paxCapacityText = room.pax_capacity || (room.beddingConfigRelation ? 'Max ' + (room.beddingConfigRelation.max_adults || 2) + ' Adults' : 'Max 2 Adults (3 Pax)');

    const isInactive = room.status && room.status.toLowerCase() === 'inactive';
    const statusBadge = isInactive
      ? '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/60"><span class="w-2 h-2 rounded-full bg-rose-500"></span> Inactive</span>'
      : '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Active</span>';

    const rawAmenitiesList = Array.isArray(room.amenities_names) && room.amenities_names.length > 0
      ? room.amenities_names
      : (Array.isArray(room.amenities) ? room.amenities.map(a => getAmenityName(a)) : []);

    const amenitiesHtml = rawAmenitiesList.length > 0
      ? rawAmenitiesList.map(a => `
          <div class="inline-flex items-center gap-2 bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
            <span>${a}</span>
          </div>
        `).join('')
      : `<div class="text-xs text-slate-400 italic">No amenities assigned to this room.</div>`;

    const content = `
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-2.5 shadow-xs">
          <div class="flex items-center justify-between bg-slate-50 border border-slate-200/80 rounded-xl p-2.5">
            <span class="text-xs font-bold text-slate-500 flex items-center gap-2">
              <i class="fa-solid fa-layer-group text-indigo-600 w-4"></i> Floor
            </span>
            <span class="text-xs font-black text-slate-800">${floorName}</span>
          </div>

          <div class="flex items-center justify-between bg-slate-50 border border-slate-200/80 rounded-xl p-2.5">
            <span class="text-xs font-bold text-slate-500 flex items-center gap-2">
              <i class="fa-solid fa-door-closed text-indigo-600 w-4"></i> Room Number
            </span>
            <span class="font-mono text-xs font-black text-slate-800">Room #${room.room_number}</span>
          </div>

          <div class="flex items-center justify-between bg-slate-50 border border-slate-200/80 rounded-xl p-2.5">
            <span class="text-xs font-bold text-slate-500 flex items-center gap-2">
              <i class="fa-solid fa-crown text-indigo-600 w-4"></i> Category
            </span>
            <span class="text-xs font-black text-slate-800">${catName}</span>
          </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex flex-col justify-center items-end gap-1.5 shadow-xs">
          <span class="text-[11px] font-black uppercase tracking-wider text-slate-400">Base Tariff</span>
          <div class="font-mono text-2xl font-black text-indigo-600">
            ₹ ${Number(room.rate).toLocaleString()}
            <span class="text-xs font-semibold text-slate-400 font-sans">/ night</span>
          </div>
          <div>
            ${statusBadge}
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-base shrink-0">
            <i class="fa-solid fa-bed"></i>
          </div>
          <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bedding Configuration</div>
            <div class="text-xs font-bold text-slate-800 mt-0.5">${beddingName}</div>
          </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base shrink-0">
            <i class="fa-solid fa-users"></i>
          </div>
          <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pax / Guest Capacity</div>
            <div class="text-xs font-bold text-slate-800 mt-0.5">${paxCapacityText}</div>
          </div>
        </div>

        ${(room.plan_relation || room.planRelation) ? `
        <div class="sm:col-span-2 bg-white border border-slate-200 rounded-xl p-3 shadow-xs flex items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm shrink-0">
              <i class="fa-solid fa-utensils"></i>
            </div>
            <div>
              <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Meal Plan / Package</div>
              <div class="text-xs font-bold text-slate-800">
                ${(room.plan_relation || room.planRelation).plan_name} ${(room.plan_relation || room.planRelation).short_code ? '(' + (room.plan_relation || room.planRelation).short_code + ')' : ''}
              </div>
            </div>
          </div>
          ${(room.plan_relation || room.planRelation).price ? `
            <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200/60">
              ₹${Number((room.plan_relation || room.planRelation).price).toLocaleString()}
            </span>
          ` : ''}
        </div>
        ` : ''}
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-bold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-wand-magic-sparkles text-indigo-600"></i> Configured Amenities
          </span>
          <span class="text-[11px] font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">
            ${rawAmenitiesList.length} Total
          </span>
        </div>
        <div class="flex flex-wrap gap-2">
          ${amenitiesHtml}
        </div>
      </div>
    `;

    document.getElementById('view-room-body').innerHTML = content;

    const botLeft = document.getElementById('view-room-bot-left');
    if (botLeft) {
      botLeft.innerHTML = `
        <button type="button" class="h-9 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2 cursor-pointer" onclick="closeModal('view-room-modal'); openEditRoomModal(${room.id});">
          <i class="fa-solid fa-pen-to-square text-xs"></i> Edit Room Asset
        </button>
      `;
    }

    openModal('view-room-modal');
  }

  function exportRoomsCSV() {
    const filtered = getFilteredRooms();
    if (!filtered.length) {
      PmsAlert.toast('No room assets to export', 'info');
      return;
    }
    let csv = '"ID","Room Number","Floor","Category","Bedding Config","Pax Capacity","Rate (INR)","Status","Amenities"\n';
    filtered.forEach(r => {
      const amns = Array.isArray(r.amenities) ? r.amenities.join('; ') : '';
      csv += `"${r.id}","${r.room_number}","${r.floor}","${r.category}","${r.bedding_config || ''}","${r.pax_capacity || ''}","${r.rate}","${r.status}","${amns}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Room_Assets_${Date.now()}.csv`;
    a.click();
    PmsAlert.toast('Rooms inventory exported to CSV!');
  }

  window.addEventListener('DOMContentLoaded', () => {
    initSelect2();
    renderView();
    if (urlParams.get('action') === 'add') {
      openAddRoomModal();
    }
  });
</script>
@endpush
