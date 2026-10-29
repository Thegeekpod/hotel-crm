@extends('admin.layouts.app')

@section('title', 'Hotel Services Master - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 p-4 lg:p-6 space-y-6">
  <!-- Page Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <div class="w-2.5 h-8 bg-indigo-600 rounded-full"></div>
      <div>
        <h1 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight">Administrator • Hotel Services Master</h1>
        <p class="text-xs lg:text-sm text-slate-500 font-medium">Configure chargeable &amp; complimentary hotel guest services, laundry, dining, and pricing units.</p>
      </div>
    </div>
    <div class="flex items-center gap-2.5">
      <button type="button" onclick="exportDataCSV()" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs hover:border-slate-300 transition-all flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-file-csv text-slate-500"></i> Export CSV
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">
    @include('admin.includes.sidebar')

    <section class="min-w-0 space-y-6">

      <!-- KPI Stats Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-bell-concierge"></i>
          </div>
          <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Services</div>
            <div class="text-xl font-black text-slate-800 mt-0.5" id="kpi-total">{{ $stats['total'] ?? count($items) }}</div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Active Services</div>
            <div class="text-xl font-black text-emerald-600 mt-0.5" id="kpi-active">{{ $stats['active'] ?? 0 }}</div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-circle-xmark"></i>
          </div>
          <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Inactive Services</div>
            <div class="text-xl font-black text-rose-600 mt-0.5" id="kpi-inactive">{{ $stats['inactive'] ?? 0 }}</div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs flex items-center gap-3.5">
          <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-tags"></i>
          </div>
          <div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Categories</div>
            <div class="text-xl font-black text-slate-800 mt-0.5" id="kpi-categories">{{ $stats['categories'] ?? 0 }}</div>
          </div>
        </div>
      </div>

      <!-- Main CRUD Container -->
      <div id="crud-view-container" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">

        <!-- Header Card -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
          <div>
            <div class="flex items-center gap-2.5">
              <h2 class="text-base font-bold text-slate-800 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-bell-concierge text-indigo-600"></i> Hotel Services &amp; Facilities Directory
              </h2>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60" id="view-count">{{ count($items) }} Records</span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
              Manage in-room dining dishes, laundry tariffs, extra amenities, transport and room add-ons.
            </p>
          </div>
          <button type="button" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 self-start sm:self-auto cursor-pointer" onclick="openAddModal()">
            <i class="fa-solid fa-plus text-xs"></i> Add New Service
          </button>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
          <div class="flex flex-wrap items-center gap-2.5 flex-1 max-w-xl">
            <div class="relative flex-1 min-w-[200px]">
              <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
              <input type="text" id="table-search" class="w-full h-9.5 pl-9 pr-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Search service name, code, category..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
            </div>

            <select id="category-filter" class="h-9.5 px-3 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handleCategoryFilter(this.value)">
              <option value="all">All Categories</option>
              @foreach($allCategories ?? [] as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
              @endforeach
            </select>

            <select id="status-filter" class="h-9.5 px-3 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handleStatusFilter(this.value)">
              <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
              <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
              <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <button type="button" class="h-9.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-all hidden items-center gap-2 cursor-pointer" id="btn-bulk-delete" onclick="handleBulkDelete()">
              <i class="fa-solid fa-trash-can text-xs"></i> Delete Selected (<span id="bulk-selected-count">0</span>)
            </button>
            <button type="button" class="h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer" onclick="exportDataCSV()">
              <i class="fa-solid fa-file-csv text-slate-500 text-xs"></i> Export
            </button>
            <button type="button" class="h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer" onclick="location.reload()">
              <i class="fa-solid fa-rotate text-slate-500 text-xs"></i> Refresh
            </button>
          </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse" id="crud-table">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                <th class="w-10 px-4 py-3.5 text-center">
                  <input type="checkbox" id="select-all-check" onchange="toggleSelectAll(this)" class="cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                </th>
                <th class="w-12 px-3 py-3.5 text-slate-400 font-mono">#</th>
                <th class="px-4 py-3.5 min-w-[200px]">Service Name &amp; Code</th>
                <th class="px-4 py-3.5 min-w-[130px]">Category</th>
                <th class="px-4 py-3.5 text-right min-w-[100px]">Rate (₹)</th>
                <th class="px-4 py-3.5 min-w-[100px]">Billing Unit</th>
                <th class="px-4 py-3.5 min-w-[160px]">Description</th>
                <th class="px-4 py-3.5 text-center min-w-[100px]">Status</th>
                <th class="px-4 py-3.5 text-right w-28">Actions</th>
              </tr>
            </thead>
            <tbody id="table-body" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              @forelse($items as $idx => $item)
                <tr class="hover:bg-slate-50/70 transition-colors" data-id="{{ $item->id }}" id="row-service-{{ $item->id }}">
                  <td class="px-4 py-3 text-center">
                    <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" value="{{ $item->id }}" onchange="handleRowSelect(this, '{{ $item->id }}')">
                  </td>
                  <td class="px-3 py-3 font-mono font-bold text-slate-400 text-[11px]">
                    {{ $idx + 1 }}
                  </td>
                  <td class="px-4 py-3">
                    <div class="font-bold text-slate-800 text-xs">{{ $item->name }}</div>
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                      {{ $item->code ?: 'SRV-' . sprintf('%04d', $item->id) }}
                    </div>
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                      <i class="fa-solid fa-tag text-[9px]"></i>
                      {{ $item->category ?: 'General' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-right font-mono font-bold text-slate-800 text-xs">
                    @if((float)$item->price <= 0)
                      <span class="text-emerald-600 font-bold text-[11px]">Complimentary</span>
                    @else
                      ₹ {{ number_format($item->price, 2) }}
                    @endif
                  </td>
                  <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600">
                      {{ $item->unit ?: 'Per Order' }}
                    </span>
                  </td>
                  <td class="px-4 py-3 text-slate-500 text-[11px]">
                    {{ Str::limit($item->description, 50) ?: '-' }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    @if($item->status === 'Active')
                      <button type="button" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 cursor-pointer" onclick="toggleServiceStatus({{ $item->id }})" title="Click to toggle status">
                        <i class="fa-solid fa-circle-check text-[9px]"></i> Active
                      </button>
                    @else
                      <button type="button" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60 cursor-pointer" onclick="toggleServiceStatus({{ $item->id }})" title="Click to toggle status">
                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Inactive
                      </button>
                    @endif
                  </td>
                  <td class="px-4 py-3 text-right">
                    <div class="inline-flex items-center gap-1 justify-end">
                      <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openEditModal({{ $item->id }})" title="Edit Service">
                        <i class="fa-solid fa-pen text-xs"></i>
                      </button>
                      <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="deleteItem({{ $item->id }})" title="Delete Service">
                        <i class="fa-solid fa-trash text-xs"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                    <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>
                    No hotel services found matching your filters. Click "+ Add New Service" to create one.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination Bar -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50" id="table-pagination-bar">
          <div class="text-xs text-slate-500 font-medium" id="pagination-info">
            Showing 1 to {{ count($items) }} of {{ count($items) }} records
          </div>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <span class="text-xs text-slate-500 font-semibold">Rows:</span>
              <select id="rows-per-page" class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handlePageSizeChange(this.value)">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="all">All</option>
              </select>
            </div>
            <div class="flex items-center gap-1" id="pagination-nav"></div>
          </div>
        </div>

      </div>
    </section>
  </div>
</main>

<!-- ADD / EDIT SERVICE MODAL -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="crud-modal">
  <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col">
    
    <!-- Modal Header -->
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <div class="flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-bell-concierge"></i>
        </span>
        <div>
          <h3 class="text-base font-bold text-slate-800" id="modal-title">Add Hotel Service</h3>
          <p class="text-[11px] text-slate-400">Configure chargeable or complimentary hotel guest service</p>
        </div>
      </div>
      <button type="button" onclick="closeModal('crud-modal')" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base">&times;</button>
    </div>

    <!-- Modal Form -->
    <form id="crud-form" onsubmit="handleFormSubmit(event)">
      <input type="hidden" id="item-id" value="">

      <div class="p-6 space-y-4 bg-slate-50/50">

        <!-- Row 1: Name and Code -->
        <div class="grid grid-cols-1 sm:grid-cols-[2fr_1fr] gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5" for="form-name">Service Name <span class="text-rose-500">*</span></label>
            <input type="text" id="form-name" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. Continental Breakfast Tray" required>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5" for="form-code">Service Code</label>
            <input type="text" id="form-code" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white font-mono text-xs font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all uppercase" placeholder="e.g. RS-01">
          </div>
        </div>

        <!-- Row 2: Category and Charge Unit -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5" for="form-category">Category <span class="text-rose-500">*</span></label>
            <select id="form-category" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
              <option value="Room Dining">Room Dining</option>
              <option value="Laundry & Dry Clean">Laundry & Dry Clean</option>
              <option value="Concierge & Transport">Concierge & Transport</option>
              <option value="Room Addon">Room Addon</option>
              <option value="Spa & Wellness">Spa & Wellness</option>
              <option value="Banquet Addon">Banquet Addon</option>
              <option value="Housekeeping Amenity">Housekeeping Amenity</option>
              @foreach(($allCategories ?? []) as $cat)
                @if(!in_array($cat, ['Room Dining', 'Laundry & Dry Clean', 'Concierge & Transport', 'Room Addon', 'Spa & Wellness', 'Banquet Addon', 'Housekeeping Amenity']))
                  <option value="{{ $cat }}">{{ $cat }}</option>
                @endif
              @endforeach
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5" for="form-unit">Pricing Unit / Serving <span class="text-rose-500">*</span></label>
            <select id="form-unit" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
              <option value="Per Order">Per Order</option>
              <option value="Per Item">Per Item</option>
              <option value="Per Piece">Per Piece</option>
              <option value="Per Pair">Per Pair</option>
              <option value="Per Person">Per Person</option>
              <option value="Per Cup">Per Cup</option>
              <option value="Per Platter">Per Platter</option>
              <option value="Per Trip">Per Trip</option>
              <option value="Per Night">Per Night</option>
              <option value="Per Hour">Per Hour</option>
              <option value="Per Session">Per Session</option>
              <option value="Complementary">Complementary (Free)</option>
            </select>
          </div>
        </div>

        <!-- Row 3: Rate and Status -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5" for="form-price">Rate / Price (₹) <span class="text-rose-500">*</span></label>
            <input type="number" step="0.01" min="0" id="form-price" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white font-mono font-bold text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="0.00" required>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5" for="form-status">Status <span class="text-rose-500">*</span></label>
            <select id="form-status" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>

        <!-- Row 4: Description -->
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5" for="form-desc">Description / Menu Details</label>
          <textarea id="form-desc" rows="3" placeholder="Brief service description, ingredients, delivery guidelines or timing for front desk staff..." class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"></textarea>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex justify-end items-center gap-2.5">
        <button type="button" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('crud-modal')">Cancel</button>
        <button type="submit" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 cursor-pointer" id="btn-save">
          <i class="fa-solid fa-floppy-disk text-xs"></i> Save Service
        </button>
      </div>

    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  let tableRecords = @json($items);
  let allCategories = @json($allCategories ?? []);
  const baseUrl = "{{ url('admin/services') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

  let searchQuery = '';
  let categoryFilter = 'all';
  let statusFilter = 'all';
  let currentPage = 1;
  let pageSize = 10;
  let selectedIds = new Set();

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  let searchDebounceTimer = null;
  function handleSearch(val) {
    searchQuery = (val || '').toLowerCase().trim();
    currentPage = 1;
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
      renderTable();
    }, 200);
  }

  function handleCategoryFilter(val) {
    categoryFilter = val;
    currentPage = 1;
    renderTable();
  }

  function handleStatusFilter(val) {
    statusFilter = val;
    currentPage = 1;
    renderTable();
  }

  function handlePageSizeChange(val) {
    pageSize = val === 'all' ? 'all' : parseInt(val, 10);
    currentPage = 1;
    renderTable();
  }

  function goToPage(p) {
    currentPage = p;
    renderTable();
  }

  function getFilteredRecords() {
    return tableRecords.filter(item => {
      const matchSearch = !searchQuery || 
        (item.name || '').toLowerCase().includes(searchQuery) ||
        (item.code || '').toLowerCase().includes(searchQuery) ||
        (item.category || '').toLowerCase().includes(searchQuery) ||
        (item.description || '').toLowerCase().includes(searchQuery);

      const matchCategory = categoryFilter === 'all' || item.category === categoryFilter;
      const matchStatus = statusFilter === 'all' || item.status === statusFilter;

      return matchSearch && matchCategory && matchStatus;
    });
  }

  function renderTable() {
    const filtered = getFilteredRecords();
    const tbody = document.getElementById('table-body');
    const viewCount = document.getElementById('view-count');
    if (viewCount) viewCount.textContent = `${filtered.length} Records`;

    document.getElementById('kpi-total').textContent = tableRecords.length;
    document.getElementById('kpi-active').textContent = tableRecords.filter(r => r.status === 'Active').length;
    document.getElementById('kpi-inactive').textContent = tableRecords.filter(r => r.status === 'Inactive').length;
    const cats = [...new Set(tableRecords.map(r => r.category).filter(Boolean))];
    document.getElementById('kpi-categories').textContent = cats.length;

    if (filtered.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="9" class="px-6 py-12 text-center text-slate-400">
            <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>
            No services found matching your filters. Click "+ Add New Service" to create one.
          </td>
        </tr>
      `;
      document.getElementById('pagination-info').textContent = 'Showing 0 records';
      document.getElementById('pagination-nav').innerHTML = '';
      updateBulkActionUI();
      return;
    }

    let displayList = filtered;
    let totalPages = 1;
    if (pageSize !== 'all') {
      totalPages = Math.ceil(filtered.length / pageSize) || 1;
      if (currentPage > totalPages) currentPage = totalPages;
      const startIdx = (currentPage - 1) * pageSize;
      displayList = filtered.slice(startIdx, startIdx + pageSize);
      const endIdx = Math.min(startIdx + pageSize, filtered.length);
      document.getElementById('pagination-info').innerHTML = `Showing <strong class="text-slate-800">${startIdx + 1}</strong> to <strong class="text-slate-800">${endIdx}</strong> of <strong class="text-slate-800">${filtered.length}</strong> records`;
    } else {
      document.getElementById('pagination-info').innerHTML = `Showing all <strong class="text-slate-800">${filtered.length}</strong> records`;
    }

    tbody.innerHTML = displayList.map((item, idx) => {
      const globalIdx = pageSize === 'all' ? (idx + 1) : ((currentPage - 1) * pageSize + idx + 1);
      const isChecked = selectedIds.has(String(item.id));

      const priceVal = parseFloat(item.price || 0);
      const priceHtml = priceVal <= 0
        ? `<span class="text-emerald-600 font-bold text-[11px]">Complimentary</span>`
        : `₹ ${priceVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

      const statusBadge = item.status === 'Active'
        ? `<button type="button" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 cursor-pointer" onclick="toggleServiceStatus(${item.id})" title="Click to toggle status"><i class="fa-solid fa-circle-check text-[9px]"></i> Active</button>`
        : `<button type="button" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60 cursor-pointer" onclick="toggleServiceStatus(${item.id})" title="Click to toggle status"><i class="fa-solid fa-circle-xmark text-[9px]"></i> Inactive</button>`;

      return `
        <tr class="hover:bg-slate-50/70 transition-colors" data-id="${item.id}" id="row-service-${item.id}">
          <td class="px-4 py-3 text-center">
            <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" value="${item.id}" ${isChecked ? 'checked' : ''} onchange="handleRowSelect(this, '${item.id}')">
          </td>
          <td class="px-3 py-3 font-mono font-bold text-slate-400 text-[11px]">
            ${globalIdx}
          </td>
          <td class="px-4 py-3">
            <div class="font-bold text-slate-800 text-xs">${escapeHtml(item.name)}</div>
            <div class="text-[10px] text-slate-400 font-mono mt-0.5">
              ${escapeHtml(item.code || ('SRV-' + String(item.id).padStart(4, '0')))}
            </div>
          </td>
          <td class="px-4 py-3">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
              <i class="fa-solid fa-tag text-[9px]"></i>
              ${escapeHtml(item.category || 'General')}
            </span>
          </td>
          <td class="px-4 py-3 text-right font-mono font-bold text-slate-800 text-xs">
            ${priceHtml}
          </td>
          <td class="px-4 py-3">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600">
              ${escapeHtml(item.unit || 'Per Order')}
            </span>
          </td>
          <td class="px-4 py-3 text-slate-500 text-[11px]">
            ${escapeHtml(item.description ? item.description.substring(0, 50) : '-')}
          </td>
          <td class="px-4 py-3 text-center">
            ${statusBadge}
          </td>
          <td class="px-4 py-3 text-right">
            <div class="inline-flex items-center gap-1 justify-end">
              <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openEditModal(${item.id})" title="Edit Service">
                <i class="fa-solid fa-pen text-xs"></i>
              </button>
              <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="deleteItem(${item.id})" title="Delete Service">
                <i class="fa-solid fa-trash text-xs"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');

    renderPaginationButtons(totalPages);
    updateBulkActionUI();
  }

  function renderPaginationButtons(totalPages) {
    const nav = document.getElementById('pagination-nav');
    if (!nav) return;
    if (pageSize === 'all' || totalPages <= 1) {
      nav.innerHTML = '';
      return;
    }

    let btns = '';
    btns += `<button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer" ${currentPage === 1 ? 'disabled' : `onclick="goToPage(${currentPage - 1})"`} title="Previous Page"><i class="fa-solid fa-chevron-left text-[10px]"></i></button>`;

    for (let p = 1; p <= totalPages; p++) {
      if (p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)) {
        btns += `<button class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center transition-all cursor-pointer ${p === currentPage ? 'bg-indigo-600 text-white shadow-xs' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50'}" onclick="goToPage(${p})">${p}</button>`;
      } else if (p === currentPage - 2 || p === currentPage + 2) {
        btns += `<span class="w-8 h-8 text-slate-400 text-xs flex items-center justify-center font-bold">...</span>`;
      }
    }

    btns += `<button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer" ${currentPage === totalPages ? 'disabled' : `onclick="goToPage(${currentPage + 1})"`} title="Next Page"><i class="fa-solid fa-chevron-right text-[10px]"></i></button>`;
    nav.innerHTML = btns;
  }

  function openAddModal() {
    document.getElementById('modal-title').textContent = 'Add Hotel Service';
    document.getElementById('item-id').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-code').value = '';
    if ($('#form-category').data('select2')) {
      $('#form-category').val('Room Dining').trigger('change');
    } else {
      document.getElementById('form-category').value = 'Room Dining';
    }
    document.getElementById('form-price').value = '0.00';
    if ($('#form-unit').data('select2')) {
      $('#form-unit').val('Per Order').trigger('change');
    } else {
      document.getElementById('form-unit').value = 'Per Order';
    }
    if ($('#form-status').data('select2')) {
      $('#form-status').val('Active').trigger('change');
    } else {
      document.getElementById('form-status').value = 'Active';
    }
    document.getElementById('form-desc').value = '';

    openModal('crud-modal');
  }

  function openEditModal(id) {
    const item = tableRecords.find(r => String(r.id) === String(id));
    if (!item) return;

    document.getElementById('modal-title').textContent = 'Edit Hotel Service';
    document.getElementById('item-id').value = item.id;
    document.getElementById('form-name').value = item.name || '';
    document.getElementById('form-code').value = item.code || '';
    
    if ($('#form-category').find(`option[value="${item.category}"]`).length === 0 && item.category) {
      $('#form-category').append(new Option(item.category, item.category, true, true));
    }
    if ($('#form-category').data('select2')) {
      $('#form-category').val(item.category || 'Room Dining').trigger('change');
    } else {
      document.getElementById('form-category').value = item.category || 'Room Dining';
    }

    document.getElementById('form-price').value = parseFloat(item.price || 0).toFixed(2);
    
    if ($('#form-unit').data('select2')) {
      $('#form-unit').val(item.unit || 'Per Order').trigger('change');
    } else {
      document.getElementById('form-unit').value = item.unit || 'Per Order';
    }
    
    if ($('#form-status').data('select2')) {
      $('#form-status').val(item.status || 'Active').trigger('change');
    } else {
      document.getElementById('form-status').value = item.status || 'Active';
    }
    document.getElementById('form-desc').value = item.description || '';

    openModal('crud-modal');
  }

  async function handleFormSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('item-id').value;
    const name = document.getElementById('form-name').value.trim();
    const code = document.getElementById('form-code').value.trim();
    const category = $('#form-category').val() || document.getElementById('form-category').value.trim();
    const price = parseFloat(document.getElementById('form-price').value || 0);
    const unit = $('#form-unit').val() || document.getElementById('form-unit').value;
    const status = $('#form-status').val() || document.getElementById('form-status').value;
    const description = document.getElementById('form-desc').value.trim();

    const saveBtn = document.getElementById('btn-save');
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Saving...`;

    try {
      const url = id ? `${baseUrl}/${id}` : baseUrl;
      const method = id ? 'PUT' : 'POST';

      const response = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          name,
          code,
          category,
          price,
          unit,
          status,
          description,
        })
      });

      const res = await response.json();

      if (response.ok && res.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        } else if (typeof Swal !== 'undefined') {
          Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: res.message, timer: 2000, showConfirmButton: false });
        }

        closeModal('crud-modal');

        if (id) {
          const idx = tableRecords.findIndex(r => String(r.id) === String(id));
          if (idx !== -1) {
            tableRecords[idx] = res.service;
          }
        } else {
          tableRecords.unshift(res.service);
        }

        renderTable();
      } else {
        const errorMsg = res.message || (res.errors ? Object.values(res.errors).flat().join('<br>') : 'Validation error.');
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'error', title: 'Save Failed', html: errorMsg });
        } else {
          alert(errorMsg);
        }
      }
    } catch (err) {
      console.error(err);
      alert('Network or server error while saving service.');
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerHTML = originalText;
    }
  }

  async function toggleServiceStatus(id) {
    try {
      const response = await fetch(`${baseUrl}/${id}/toggle-status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        }
      });
      const res = await response.json();
      if (response.ok && res.success) {
        const item = tableRecords.find(r => String(r.id) === String(id));
        if (item) item.status = res.status;
        renderTable();
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        }
      }
    } catch (err) {
      console.error(err);
    }
  }

  async function deleteItem(id) {
    const confirmed = typeof Swal !== 'undefined' 
      ? (await Swal.fire({
          title: 'Delete Service?',
          text: 'Are you sure you want to remove this service from directory?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#64748b',
          confirmButtonText: 'Yes, delete it'
        })).isConfirmed
      : confirm('Are you sure you want to delete this service?');

    if (!confirmed) return;

    try {
      const response = await fetch(`${baseUrl}/${id}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        }
      });
      const res = await response.json();
      if (response.ok && res.success) {
        tableRecords = tableRecords.filter(r => String(r.id) !== String(id));
        selectedIds.delete(String(id));
        renderTable();
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        }
      } else {
        alert(res.message || 'Error deleting service.');
      }
    } catch (err) {
      console.error(err);
      alert('Server error.');
    }
  }

  function handleRowSelect(checkbox, id) {
    if (checkbox.checked) {
      selectedIds.add(String(id));
    } else {
      selectedIds.delete(String(id));
    }
    updateBulkActionUI();
  }

  function toggleSelectAll(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.row-select-check');
    checkboxes.forEach(cb => {
      cb.checked = masterCheckbox.checked;
      if (masterCheckbox.checked) {
        selectedIds.add(String(cb.value));
      } else {
        selectedIds.delete(String(cb.value));
      }
    });
    updateBulkActionUI();
  }

  function updateBulkActionUI() {
    const btn = document.getElementById('btn-bulk-delete');
    const countSpan = document.getElementById('bulk-selected-count');
    if (!btn || !countSpan) return;

    if (selectedIds.size > 0) {
      btn.classList.remove('hidden');
      btn.classList.add('inline-flex');
      countSpan.textContent = selectedIds.size;
    } else {
      btn.classList.add('hidden');
      btn.classList.remove('inline-flex');
      countSpan.textContent = '0';
    }
  }

  async function handleBulkDelete() {
    if (selectedIds.size === 0) return;

    const confirmed = typeof Swal !== 'undefined'
      ? (await Swal.fire({
          title: `Delete ${selectedIds.size} Services?`,
          text: 'This will permanently remove the selected hotel services.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#64748b',
          confirmButtonText: 'Yes, delete all'
        })).isConfirmed
      : confirm(`Delete ${selectedIds.size} selected services?`);

    if (!confirmed) return;

    try {
      const response = await fetch(`${baseUrl}/bulk-delete`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ ids: Array.from(selectedIds) })
      });
      const res = await response.json();
      if (response.ok && res.success) {
        tableRecords = tableRecords.filter(r => !selectedIds.has(String(r.id)));
        selectedIds.clear();
        renderTable();
        const selectAll = document.getElementById('select-all-check');
        if (selectAll) selectAll.checked = false;
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        }
      }
    } catch (err) {
      console.error(err);
      alert('Server error.');
    }
  }

  function exportDataCSV() {
    const records = getFilteredRecords();
    if (!records.length) {
      alert('No data to export.');
      return;
    }

    const headers = ['ID', 'Service Name', 'Service Code', 'Category', 'Price (INR)', 'Billing Unit', 'Status', 'Description'];
    const rows = records.map(r => [
      r.id,
      `"${(r.name || '').replace(/"/g, '""')}"`,
      `"${(r.code || '').replace(/"/g, '""')}"`,
      `"${(r.category || '').replace(/"/g, '""')}"`,
      parseFloat(r.price || 0).toFixed(2),
      `"${(r.unit || '').replace(/"/g, '""')}"`,
      r.status,
      `"${(r.description || '').replace(/"/g, '""')}"`
    ]);

    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `hotel_services_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  $(document).ready(function() {
    renderTable();

    $(document).on('select2:open', () => {
      setTimeout(() => {
        const searchField = document.querySelector('.select2-container--open .select2-search__field');
        if (searchField) searchField.focus();
      }, 50);
    });

    $('#category-filter').select2({
      width: '180px',
      placeholder: 'All Categories'
    }).on('select2:select change', function() {
      handleCategoryFilter(this.value);
    });

    $('#status-filter').select2({
      width: '140px',
      placeholder: 'All Statuses'
    }).on('select2:select change', function() {
      handleStatusFilter(this.value);
    });

    $('#form-category').select2({
      dropdownParent: $('#crud-modal'),
      width: '100%',
      tags: true,
      placeholder: 'Search or type category...'
    });

    $('#form-unit').select2({
      dropdownParent: $('#crud-modal'),
      width: '100%',
      placeholder: 'Select pricing unit'
    });

    $('#form-status').select2({
      dropdownParent: $('#crud-modal'),
      width: '100%'
    });
  });
</script>
@endpush
