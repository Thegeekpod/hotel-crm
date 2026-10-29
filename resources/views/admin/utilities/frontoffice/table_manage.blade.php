@extends('admin.layouts.app')

@section('title', 'Table Management Master - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 p-4 lg:p-6 space-y-6">

  <!-- Page Header -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></span>
      <div>
        <h1 class="text-base font-extrabold text-slate-900 tracking-tight">Administrator • Master Data Management</h1>
        <p class="text-xs text-slate-500 font-medium">Configure and manage live dining tables, seating areas, and restaurant occupancy status.</p>
      </div>
    </div>
    <div>
      <button type="button" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="exportDataCSV()">
        <i class="fa-solid fa-file-csv text-slate-500"></i> Export CSV
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">
    @include('admin.includes.sidebar')

    <section class="min-w-0">
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Card Header -->
        <div class="p-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="flex items-center gap-2.5">
              <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Restaurant Table Master</h2>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80" id="view-count">{{ count($items) }} Records</span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
              Manage dining tables, seating capacities, floor locations, and live availability for POS dining.
            </p>
          </div>
          <button type="button" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer shrink-0" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> Add Table
          </button>
        </div>

        <!-- Toolbar -->
        <div class="p-4 bg-slate-50/70 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
          <div class="flex items-center gap-2.5 flex-1 min-w-[260px]">
            <div class="relative flex-1 max-w-sm">
              <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
              <input type="text" id="table-search" class="h-9.5 pl-8 pr-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="Search table # or area..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
            </div>
            <select id="status-filter" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 cursor-pointer" onchange="handleStatusFilter(this.value)">
              <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
              <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
              <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Booked</option>
              <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <button type="button" class="hidden inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-all shadow-xs cursor-pointer" id="btn-bulk-delete" onclick="handleBulkDelete()">
              <i class="fa-solid fa-trash-can"></i> Delete Selected (<span id="bulk-selected-count">0</span>)
            </button>
            <button type="button" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="exportDataCSV()">
              <i class="fa-solid fa-file-csv text-slate-500"></i> Export CSV
            </button>
            <button type="button" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="loadTableData()">
              <i class="fa-solid fa-rotate text-slate-500"></i> Refresh
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs" id="crud-table">
            <thead>
              <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                <th class="py-3 px-3 text-center w-10">
                  <input type="checkbox" id="select-all-check" onchange="toggleSelectAll(this)" class="cursor-pointer w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                </th>
                <th class="py-3 px-3 w-16">#</th>
                <th class="py-3 px-3">Table Number / Code</th>
                <th class="py-3 px-3">Table Name / Location</th>
                <th class="py-3 px-3 w-36">Status</th>
                <th class="py-3 px-3 text-right w-28">Actions</th>
              </tr>
            </thead>
            <tbody id="table-body" class="divide-y divide-slate-100">
              @forelse($items as $idx => $item)
              <tr class="hover:bg-slate-50/60 transition-colors" data-id="{{ $item->id }}">
                <td class="py-3 px-3 text-center">
                  <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500" value="{{ $item->id }}" onchange="handleRowSelect(this, '{{ $item->id }}')">
                </td>
                <td class="py-3 px-3 font-mono font-bold text-slate-400 text-xs">{{ $idx + 1 }}</td>
                <td class="py-3 px-3 font-extrabold text-slate-900 flex items-center gap-2">
                  <i class="fa-solid fa-chair text-xs {{ $item->status === 'available' ? 'text-emerald-500' : ($item->status === 'booked' ? 'text-amber-500' : 'text-slate-400') }}"></i>
                  <span>{{ $item->table_number }}</span>
                </td>
                <td class="py-3 px-3 text-slate-600 font-medium">
                  {{ $item->table_name }}
                </td>
                <td class="py-3 px-3">
                  @if($item->status === 'available')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available
                    </span>
                  @elseif($item->status === 'booked')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Booked
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200/80">
                      <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                    </span>
                  @endif
                </td>
                <td class="py-3 px-3 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button type="button" class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors flex items-center justify-center cursor-pointer" onclick="openEditModal({{ $item->id }})" title="Edit Table"><i class="fa-solid fa-pen text-xs"></i></button>
                    <button type="button" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors flex items-center justify-center cursor-pointer" onclick="deleteItem({{ $item->id }})" title="Delete Table"><i class="fa-solid fa-trash text-xs"></i></button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="py-8 text-center text-slate-400 font-medium">
                  <i class="fa-solid fa-inbox text-2xl mb-2 block text-slate-300"></i>
                  No tables found. Click "+ Add Table" to create one.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination & Rows Controls -->
        <div class="p-4 bg-slate-50/70 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-3 text-xs" id="table-pagination-bar">
          <div class="text-slate-500 font-medium" id="pagination-info">
            Showing 1 to {{ count($items) }} of {{ count($items) }} records
          </div>
          <div class="flex items-center gap-3">
            <div class="flex items-center gap-1.5 text-slate-500 font-medium">
              <span>Rows:</span>
              <select id="rows-per-page" class="h-8 px-2 text-xs font-medium rounded-lg border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 cursor-pointer" onchange="handlePageSizeChange(this.value)">
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="100">100</option>
                <option value="all">All</option>
              </select>
            </div>
            <div class="flex items-center gap-1" id="pagination-nav">
              <!-- Injected via JavaScript -->
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>

</main>

<!-- Modal -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="crud-modal">
  <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
      <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
        <i class="fa-solid fa-chair text-indigo-600"></i> <span id="modal-title">Add Table</span>
      </h3>
      <button type="button" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-base cursor-pointer transition-colors" onclick="closeModal('crud-modal')">&times;</button>
    </div>
    <form id="crud-form" onsubmit="handleFormSubmit(event)">
      <input type="hidden" id="item-id">
      <div class="p-6 space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Table Number / Code <span class="text-rose-500">*</span></label>
          <input type="text" id="form-table-number" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="e.g. Table 1, Table 10, VIP-01" required>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Table Name / Location <span class="text-rose-500">*</span></label>
          <input type="text" id="form-table-name" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="e.g. Sea Breeze Deck, Poolside View, Indoor AC" required>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status <span class="text-rose-500">*</span></label>
          <select id="form-status" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full cursor-pointer" required>
            <option value="available">Available (Default)</option>
            <option value="booked">Booked</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="px-6 py-3 bg-slate-50/80 border-t border-slate-200/80 flex items-center justify-end gap-2">
        <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeModal('crud-modal')">Cancel</button>
        <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer" id="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Table</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const urlParams = new URLSearchParams(window.location.search);
  let searchQuery = urlParams.get('search') || "{{ request('search') }}" || '';
  let statusFilter = urlParams.get('status') || "{{ request('status', 'all') }}" || 'all';
  let currentPage = parseInt(urlParams.get('page') || '1', 10);
  let pageSize = urlParams.get('per_page') || "{{ request('per_page', '10') }}" || '10';
  let tableRecords = @json($items);

  const baseUrl = "{{ url('admin/utilities/front-office/table-manage') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function updateUrlParams() {
    const params = new URLSearchParams();
    if (searchQuery) params.set('search', searchQuery);
    if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);
    if (currentPage > 1) params.set('page', currentPage);
    if (pageSize !== '10') params.set('per_page', pageSize);

    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState(null, '', newUrl);
  }

  async function loadTableData() {
    updateUrlParams();
    try {
      const params = new URLSearchParams();
      if (searchQuery) params.set('search', searchQuery);
      if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);

      const res = await fetch(`${baseUrl}?${params.toString()}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success) {
        tableRecords = data.data;
        renderTable();
      }
    } catch (err) {
      console.error(err);
    }
  }

  let searchDebounceTimer = null;
  function handleSearch(val) {
    searchQuery = (val || '').toLowerCase().trim();
    currentPage = 1;
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
      loadTableData();
    }, 250);
  }

  function handleStatusFilter(val) {
    statusFilter = val;
    currentPage = 1;
    loadTableData();
  }

  function handlePageSizeChange(val) {
    pageSize = val === 'all' ? 'all' : parseInt(val, 10);
    currentPage = 1;
    updateUrlParams();
    renderTable();
  }

  function goToPage(page) {
    currentPage = page;
    updateUrlParams();
    renderTable();
  }

  function openAddModal() {
    document.getElementById('modal-title').textContent = 'Add Table';
    document.getElementById('item-id').value = '';
    document.getElementById('form-table-number').value = '';
    document.getElementById('form-table-name').value = '';
    document.getElementById('form-status').value = 'available';
    openModal('crud-modal');
  }

  function openEditModal(id) {
    const item = tableRecords.find(r => String(r.id) === String(id));
    if (!item) {
      console.warn('Record not found for id:', id);
      return;
    }

    document.getElementById('modal-title').textContent = 'Edit Table';
    document.getElementById('item-id').value = item.id;
    document.getElementById('form-table-number').value = item.table_number;
    document.getElementById('form-table-name').value = item.table_name;
    document.getElementById('form-status').value = item.status;
    openModal('crud-modal');
  }

  async function handleFormSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('item-id').value;
    const tableNumber = document.getElementById('form-table-number').value.trim();
    const tableName = document.getElementById('form-table-name').value.trim();
    const status = document.getElementById('form-status').value;

    if (!tableNumber) {
      if (typeof PmsAlert !== 'undefined') PmsAlert.error('Validation Error', 'Please enter a table number.');
      else alert('Please enter a table number.');
      return;
    }
    if (!tableName) {
      if (typeof PmsAlert !== 'undefined') PmsAlert.error('Validation Error', 'Please enter a table name/location.');
      else alert('Please enter a table name/location.');
      return;
    }

    const payload = {
      table_number: tableNumber,
      table_name: tableName,
      status: status
    };
    const url = id ? `${baseUrl}/${id}` : baseUrl;
    const method = id ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (res.ok && data.success) {
        closeModal('crud-modal');
        if (typeof PmsAlert !== 'undefined') PmsAlert.toast(data.message || 'Table saved successfully!');
        else alert(data.message || 'Table saved successfully!');
        await loadTableData();
      } else {
        if (typeof PmsAlert !== 'undefined') PmsAlert.error('Error', data.message || 'Failed to save table.');
        else alert(data.message || 'Failed to save table.');
      }
    } catch (err) {
      if (typeof PmsAlert !== 'undefined') PmsAlert.error('Server Error', 'Failed to communicate with the server.');
      else alert('Failed to communicate with the server.');
    }
  }

  async function deleteItem(id) {
    let confirmed = false;
    if (typeof PmsAlert !== 'undefined') {
      const result = await PmsAlert.confirmDelete('Delete Table?', 'This table record will be removed permanently.');
      confirmed = result && result.isConfirmed;
    } else {
      confirmed = confirm('Are you sure you want to delete this table?');
    }

    if (confirmed) {
      try {
        const res = await fetch(`${baseUrl}/${id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          }
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success !== false) {
          if (typeof PmsAlert !== 'undefined') PmsAlert.toast(data.message || 'Table deleted successfully!');
          else alert(data.message || 'Table deleted successfully!');
          await loadTableData();
        } else {
          if (typeof PmsAlert !== 'undefined') PmsAlert.error('Delete Failed', data.message || 'Could not delete table.');
          else alert(data.message || 'Could not delete table.');
          await loadTableData();
        }
      } catch (err) {
        console.error(err);
        if (typeof PmsAlert !== 'undefined') PmsAlert.error('Server Error', 'An error occurred during deletion.');
        else alert('An error occurred during deletion.');
      }
    }
  }

  let selectedIds = new Set();

  function updateBulkActionUI() {
    const countEl = document.getElementById('bulk-selected-count');
    const btn = document.getElementById('btn-bulk-delete');
    const selectAll = document.getElementById('select-all-check');

    if (countEl) countEl.textContent = selectedIds.size;
    if (btn) {
      btn.classList.toggle('hidden', selectedIds.size === 0);
    }

    const currentVisible = getFilteredRecords();
    const allVisibleSelected = currentVisible.length > 0 && currentVisible.every(r => selectedIds.has(String(r.id)));
    if (selectAll) {
      selectAll.checked = allVisibleSelected;
      selectAll.indeterminate = selectedIds.size > 0 && !allVisibleSelected;
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
    const visible = getFilteredRecords();
    if (masterCheckbox.checked) {
      visible.forEach(r => selectedIds.add(String(r.id)));
    } else {
      visible.forEach(r => selectedIds.delete(String(r.id)));
    }
    renderTable();
  }

  async function handleBulkDelete() {
    if (selectedIds.size === 0) return;
    const count = selectedIds.size;

    let confirmed = false;
    if (typeof PmsAlert !== 'undefined') {
      const result = await PmsAlert.confirmDelete(`Delete ${count} Selected Tables?`, 'All selected tables will be removed permanently.');
      confirmed = result && result.isConfirmed;
    } else {
      confirmed = confirm(`Delete ${count} selected tables?`);
    }

    if (confirmed) {
      try {
        const res = await fetch(`${baseUrl}/bulk-delete`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ ids: Array.from(selectedIds) })
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success !== false) {
          if (typeof PmsAlert !== 'undefined') PmsAlert.toast(data.message || `${count} table(s) deleted successfully!`);
          else alert(data.message || `${count} table(s) deleted successfully!`);
          selectedIds.clear();
          await loadTableData();
        } else {
          if (typeof PmsAlert !== 'undefined') PmsAlert.error('Bulk Delete Failed', data.message || 'Could not delete selected tables.');
          else alert(data.message || 'Could not delete selected tables.');
          await loadTableData();
        }
      } catch (err) {
        console.error(err);
        if (typeof PmsAlert !== 'undefined') PmsAlert.error('Server Error', 'Failed to communicate with the server.');
        else alert('Failed to communicate with the server.');
      }
    }
  }

  function getFilteredRecords() {
    return tableRecords.filter(item => {
      const matchSearch = !searchQuery ||
        (item.table_number && String(item.table_number).toLowerCase().includes(searchQuery)) ||
        (item.table_name && String(item.table_name).toLowerCase().includes(searchQuery)) ||
        (item.status && item.status.toLowerCase().includes(searchQuery));

      const matchStatus = (statusFilter === 'all') || (item.status === statusFilter);

      return matchSearch && matchStatus;
    });
  }

  function renderTable() {
    const filtered = getFilteredRecords();
    const tbody = document.getElementById('table-body');
    const viewCount = document.getElementById('view-count');

    if (viewCount) {
      viewCount.textContent = `${filtered.length} Records`;
    }

    const totalRecords = filtered.length;
    let paginatedRecords = filtered;
    let totalPages = 1;

    if (pageSize !== 'all') {
      const limit = parseInt(pageSize, 10);
      totalPages = Math.ceil(totalRecords / limit) || 1;
      if (currentPage > totalPages) currentPage = totalPages;
      const startIdx = (currentPage - 1) * limit;
      paginatedRecords = filtered.slice(startIdx, startIdx + limit);
    }

    if (paginatedRecords.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6" class="py-8 text-center text-slate-400 font-medium">
            <i class="fa-solid fa-inbox text-2xl mb-2 block text-slate-300"></i>
            No matching tables found.
          </td>
        </tr>
      `;
      renderPagination(totalRecords, totalPages, 0, 0);
      updateBulkActionUI();
      return;
    }

    const startIndex = pageSize === 'all' ? 0 : (currentPage - 1) * parseInt(pageSize, 10);

    tbody.innerHTML = paginatedRecords.map((item, idx) => {
      const isAvail = (item.status === 'available');
      const isBooked = (item.status === 'booked');
      const iconColor = isAvail ? 'text-emerald-500' : (isBooked ? 'text-amber-500' : 'text-slate-400');
      
      const badge = isAvail
        ? `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available</span>`
        : (isBooked
          ? `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Booked</span>`
          : `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200/80"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive</span>`);

      return `
        <tr class="hover:bg-slate-50/60 transition-colors" data-id="${item.id}">
          <td class="py-3 px-3 text-center">
            <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500" value="${item.id}" ${selectedIds.has(String(item.id)) ? 'checked' : ''} onchange="handleRowSelect(this, '${item.id}')">
          </td>
          <td class="py-3 px-3 font-mono font-bold text-slate-400 text-xs">
            ${startIndex + idx + 1}
          </td>
          <td class="py-3 px-3 font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fa-solid fa-chair text-xs ${iconColor}"></i>
            <span>${escapeHtml(item.table_number)}</span>
          </td>
          <td class="py-3 px-3 text-slate-600 font-medium">
            ${escapeHtml(item.table_name)}
          </td>
          <td class="py-3 px-3">
            ${badge}
          </td>
          <td class="py-3 px-3 text-right">
            <div class="flex items-center justify-end gap-1">
              <button type="button" class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors flex items-center justify-center cursor-pointer" onclick="openEditModal(${item.id})" title="Edit Table"><i class="fa-solid fa-pen text-xs"></i></button>
              <button type="button" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors flex items-center justify-center cursor-pointer" onclick="deleteItem(${item.id})" title="Delete Table"><i class="fa-solid fa-trash text-xs"></i></button>
            </div>
          </td>
        </tr>
      `;
    }).join('');

    updateBulkActionUI();
    const fromItem = totalRecords === 0 ? 0 : startIndex + 1;
    const toItem = pageSize === 'all' ? totalRecords : Math.min(startIndex + parseInt(pageSize, 10), totalRecords);
    renderPagination(totalRecords, totalPages, fromItem, toItem);
  }

  function renderPagination(total, pages, from, to) {
    const info = document.getElementById('pagination-info');
    if (info) {
      info.textContent = total === 0 ? 'Showing 0 records' : `Showing ${from} to ${to} of ${total} records`;
    }

    const nav = document.getElementById('pagination-nav');
    if (!nav) return;

    if (pages <= 1) {
      nav.innerHTML = '';
      return;
    }

    let buttons = `
      <button type="button" class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold ${currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 cursor-pointer'}" ${currentPage === 1 ? 'disabled' : ''} onclick="goToPage(${currentPage - 1})">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
    `;

    for (let p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || (p >= currentPage - 1 && p <= currentPage + 1)) {
        buttons += `
          <button type="button" class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold transition-all cursor-pointer ${p === currentPage ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'}" onclick="goToPage(${p})">
            ${p}
          </button>
        `;
      } else if (p === currentPage - 2 || p === currentPage + 2) {
        buttons += `<span class="w-7 h-7 flex items-center justify-center text-xs text-slate-400">...</span>`;
      }
    }

    buttons += `
      <button type="button" class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold ${currentPage === pages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 cursor-pointer'}" ${currentPage === pages ? 'disabled' : ''} onclick="goToPage(${currentPage + 1})">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    `;

    nav.innerHTML = buttons;
  }

  function exportDataCSV() {
    const records = getFilteredRecords();
    if (!records.length) {
      if (typeof PmsAlert !== 'undefined') PmsAlert.warning('Export CSV', 'No records to export.');
      else alert('No records to export.');
      return;
    }

    const headers = ['#', 'Table Number', 'Table Name / Location', 'Status'];
    const rows = records.map((r, i) => [
      i + 1,
      `"${(r.table_number || '').replace(/"/g, '""')}"`,
      `"${(r.table_name || '').replace(/"/g, '""')}"`,
      `"${(r.status || '').replace(/"/g, '""')}"`
    ]);

    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `restaurant_tables_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    if (typeof PmsAlert !== 'undefined') PmsAlert.toast('CSV exported successfully!');
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  document.addEventListener('DOMContentLoaded', () => {
    renderTable();
  });
</script>
@endpush
