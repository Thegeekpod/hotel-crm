@extends('admin.layouts.app')

@section('title', 'Operational Status Master - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 p-4 lg:p-6 space-y-6">
  <!-- Page Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <div class="w-2.5 h-8 bg-indigo-600 rounded-full"></div>
      <div>
        <h1 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight">Administrator • Master Data Management</h1>
        <p class="text-xs lg:text-sm text-slate-500 font-medium">Configure room inventory operational states (Active, Maintenance, Blocked, Out of Order).</p>
      </div>
    </div>
    <div class="flex items-center gap-2.5">
      <button type="button" onclick="exportDataCSV()" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs hover:border-slate-300 transition-all flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-file-csv text-slate-500"></i> Export CSV
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">
    <!-- Sidebar -->
    @include('admin.includes.sidebar')

    <!-- Content Pane -->
    <section class="min-w-0 space-y-6">
      <div id="crud-view-container" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Header Card -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
          <div>
            <div class="flex items-center gap-2.5">
              <h2 class="text-base font-bold text-slate-800 tracking-tight">Operational Status Master</h2>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60" id="view-count">{{ count($items) }} Records</span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Configure room inventory operational states (Active, Maintenance, Blocked, Out of Order).</p>
          </div>
          <button type="button" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 self-start sm:self-auto cursor-pointer" onclick="openAddModal()">
            <i class="fa-solid fa-plus text-xs"></i> Add Entry
          </button>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
          <div class="flex flex-wrap items-center gap-2.5 flex-1 max-w-xl">
            <div class="relative flex-1 min-w-[200px]">
              <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
              <input type="text" id="table-search" class="w-full h-9.5 pl-9 pr-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Search active records..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
            </div>
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
            <div class="flex items-center gap-1.5">
              <span class="text-[11px] font-bold text-slate-400 uppercase">Rows:</span>
              <select id="page-size-select" class="h-9.5 px-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handlePageSizeChange(this.value)">
                <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10</option>
                <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
              </select>
            </div>
            <button type="button" class="h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer" onclick="exportDataCSV()">
              <i class="fa-solid fa-file-csv text-slate-500 text-xs"></i> Export
            </button>
            <button type="button" class="h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer" onclick="loadTableData()">
              <i class="fa-solid fa-rotate text-slate-500 text-xs"></i> Refresh
            </button>
          </div>
        </div>

        <!-- Responsive Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse" id="crud-table">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                <th class="w-10 px-4 py-3.5 text-center">
                  <input type="checkbox" id="select-all-check" onchange="toggleSelectAll(this)" class="cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                </th>
                <th class="w-12 px-3 py-3.5 text-slate-400 font-mono">#</th>
                <th class="px-4 py-3.5">Status Name</th>
                <th class="px-4 py-3.5">Color Indicator</th>
                <th class="px-4 py-3.5">System Status</th>
                <th class="px-4 py-3.5 text-right w-28">Actions</th>
              </tr>
            </thead>
            <tbody id="table-body" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              <!-- Dynamically loaded via JavaScript -->
            </tbody>
          </table>
        </div>

        <!-- Pagination Bar -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
          <div class="text-xs text-slate-500 font-medium" id="pagination-info">
            Showing 1 to 10 of {{ count($items) }} records
          </div>
          <div class="flex items-center gap-1" id="pagination-nav">
            <!-- Rendered dynamically -->
          </div>
        </div>

      </div>
    </section>
  </div>
</main>

<!-- Add / Edit Master Modal -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="crud-modal">
  <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-circle-nodes"></i>
        </span>
        <span id="modal-title">Add Operational Status</span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base" onclick="closeModal('crud-modal')">&times;</button>
    </div>
    <form id="crud-form" onsubmit="handleFormSubmit(event)">
      <input type="hidden" id="item-id">
      <div class="p-6 space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Name <span class="text-rose-500">*</span></label>
          <input type="text" id="form-name" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. Active In-Service" required>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Badge Theme</label>
            <select id="form-badge" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
              <option value="blue">Blue (Standard Operation)</option>
              <option value="green">Green (Operational / Active)</option>
              <option value="yellow">Yellow (Maintenance / Inspection)</option>
              <option value="red">Red (Blocked / Out of Order)</option>
              <option value="purple">Purple (Special / Reserved)</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Status <span class="text-rose-500">*</span></label>
            <select id="form-status" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>

      <div class="px-6 py-3.5 bg-slate-50/50 border-t border-slate-100 flex justify-end items-center gap-2.5">
        <button type="button" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('crud-modal')">Cancel</button>
        <button type="submit" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 cursor-pointer">
          <i class="fa-solid fa-check text-xs"></i> Save Status
        </button>
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
  let selectedIds = new Set();

  const baseUrl = "{{ route('admin.utilities.housekeeping.operational.index') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function updateBulkActionUI() {
    const btn = document.getElementById('btn-bulk-delete');
    const countSpan = document.getElementById('bulk-selected-count');
    const selectAllCheck = document.getElementById('select-all-check');

    if (btn && countSpan) {
      countSpan.textContent = selectedIds.size;
      if (selectedIds.size > 0) {
        btn.classList.remove('hidden');
        btn.classList.add('inline-flex');
      } else {
        btn.classList.add('hidden');
        btn.classList.remove('inline-flex');
      }
    }

    if (selectAllCheck) {
      const filtered = getFilteredRecords();
      if (filtered.length === 0) {
        selectAllCheck.checked = false;
        selectAllCheck.indeterminate = false;
      } else {
        const allSelected = filtered.every(r => selectedIds.has(r.id));
        const someSelected = filtered.some(r => selectedIds.has(r.id));
        selectAllCheck.checked = allSelected;
        selectAllCheck.indeterminate = someSelected && !allSelected;
      }
    }
  }

  function handleRowSelect(checkbox, id) {
    if (checkbox.checked) {
      selectedIds.add(id);
    } else {
      selectedIds.delete(id);
    }
    updateBulkActionUI();
  }

  function toggleSelectAll(masterCheckbox) {
    const filtered = getFilteredRecords();
    if (masterCheckbox.checked) {
      filtered.forEach(r => selectedIds.add(r.id));
    } else {
      filtered.forEach(r => selectedIds.delete(r.id));
    }
    renderTable();
  }

  async function handleBulkDelete() {
    if (selectedIds.size === 0) return;
    const count = selectedIds.size;
    const result = await PmsAlert.confirmDelete(`Delete ${count} Operational Status${count > 1 ? 'es' : ''}?`, 'Selected operational statuses will be permanently deleted.');
    if (result.isConfirmed) {
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
        const data = await res.json();
        if (res.ok && data.success) {
          PmsAlert.toast(data.message || 'Records deleted successfully!');
          selectedIds.clear();
          await loadTableData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete selected records.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'An error occurred during bulk deletion.');
      }
    }
  }

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
    document.getElementById('modal-title').innerHTML = '<i class="fa-solid fa-plus-circle text-indigo-600 mr-2"></i> Add Operational Status';
    document.getElementById('item-id').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-badge').value = 'blue';
    document.getElementById('form-status').value = 'Active';
    openModal('crud-modal');
  }

  function openEditModal(id) {
    const item = tableRecords.find(r => r.id === id);
    if (!item) return;

    document.getElementById('modal-title').innerHTML = '<i class="fa-solid fa-pen-to-square text-indigo-600 mr-2"></i> Edit Operational Status';
    document.getElementById('item-id').value = item.id;
    document.getElementById('form-name').value = item.name;
    document.getElementById('form-badge').value = item.badge_color || 'blue';
    document.getElementById('form-status').value = item.status;
    openModal('crud-modal');
  }

  async function handleFormSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('item-id').value;
    const name = document.getElementById('form-name').value.trim();
    const badge_color = document.getElementById('form-badge').value;
    const status = document.getElementById('form-status').value;

    if (!name) {
      PmsAlert.error('Validation Error', 'Please provide a valid status name.');
      return;
    }

    const isDuplicate = tableRecords.some(r => {
      const isSameName = r.name && r.name.toLowerCase() === name.toLowerCase();
      const isDifferentId = !id || String(r.id) !== String(id);
      return isSameName && isDifferentId;
    });

    if (isDuplicate) {
      PmsAlert.error('Duplicate Entry', `The operational status "${name}" already exists. Please use a unique name.`);
      return;
    }

    const payload = { name, badge_color, status };
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
        PmsAlert.toast(data.message || 'Saved successfully!');
        await loadTableData();
      } else {
        const errorMsg = data.errors?.name?.[0] || data.message || 'Failed to save record.';
        PmsAlert.error('Error', errorMsg);
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  async function deleteItem(id) {
    const result = await PmsAlert.confirmDelete('Delete Operational Status?', 'This operational status will be removed from master.');
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
          PmsAlert.toast('Record deleted successfully!');
          selectedIds.delete(id);
          await loadTableData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete record.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'An error occurred during deletion.');
      }
    }
  }

  function getFilteredRecords() {
    return tableRecords.filter(item => {
      const matchSearch = !searchQuery ||
        (item.name && item.name.toLowerCase().includes(searchQuery)) ||
        (item.badge_color && item.badge_color.toLowerCase().includes(searchQuery)) ||
        (item.status && item.status.toLowerCase().includes(searchQuery));

      const matchStatus = statusFilter === 'all' || item.status === statusFilter;
      return matchSearch && matchStatus;
    });
  }

  function getBadgeClasses(color) {
    switch (color) {
      case 'red':
        return 'bg-rose-50 text-rose-700 border-rose-200/60';
      case 'yellow':
        return 'bg-amber-50 text-amber-700 border-amber-200/60';
      case 'green':
        return 'bg-emerald-50 text-emerald-700 border-emerald-200/60';
      case 'purple':
        return 'bg-purple-50 text-purple-700 border-purple-200/60';
      case 'blue':
      default:
        return 'bg-sky-50 text-sky-700 border-sky-200/60';
    }
  }

  function renderTable() {
    const filtered = getFilteredRecords();
    const totalCount = filtered.length;
    const totalPages = pageSize === 'all' ? 1 : Math.max(1, Math.ceil(totalCount / pageSize));

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIdx = pageSize === 'all' ? 0 : (currentPage - 1) * pageSize;
    const endIdx = pageSize === 'all' ? totalCount : Math.min(startIdx + pageSize, totalCount);
    const pageItems = filtered.slice(startIdx, endIdx);

    document.getElementById('view-count').textContent = `${tableRecords.length} Record${tableRecords.length === 1 ? '' : 's'}`;

    const tbody = document.getElementById('table-body');
    if (!pageItems.length) {
      tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-12 text-center text-slate-400"><i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>No matching records found.</td></tr>`;
    } else {
      let html = '';
      pageItems.forEach((item, idx) => {
        const badgeColor = item.badge_color || 'blue';
        const colorClasses = getBadgeClasses(badgeColor);
        const isSelected = selectedIds.has(item.id);

        const statusBadge = item.status === 'Active'
          ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>'
          : '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Inactive</span>';

        html += `
          <tr data-id="${item.id}" class="hover:bg-slate-50/70 transition-colors ${isSelected ? 'bg-indigo-50/40' : ''}">
            <td class="px-4 py-3 text-center">
              <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" value="${item.id}" ${isSelected ? 'checked' : ''} onchange="handleRowSelect(this, ${item.id})">
            </td>
            <td class="px-3 py-3 font-mono font-bold text-slate-400 text-[11px]">${startIdx + idx + 1}</td>
            <td class="px-4 py-3 font-bold text-slate-800">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg border font-bold text-xs ${colorClasses}">
                <i class="fa-solid fa-circle-nodes text-[10px]"></i> ${escapeHtml(item.name)}
              </span>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full border text-[11px] font-bold uppercase tracking-wider ${colorClasses}">
                <span class="w-1.5 h-1.5 rounded-full bg-current"></span> ${escapeHtml(badgeColor)}
              </span>
            </td>
            <td class="px-4 py-3">
              ${statusBadge}
            </td>
            <td class="px-4 py-3 text-right">
              <div class="inline-flex items-center gap-1 justify-end">
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openEditModal(${item.id})" title="Edit Record">
                  <i class="fa-solid fa-pen text-xs"></i>
                </button>
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="deleteItem(${item.id})" title="Delete Record">
                  <i class="fa-solid fa-trash text-xs"></i>
                </button>
              </div>
            </td>
          </tr>
        `;
      });
      tbody.innerHTML = html;
    }

    const infoEl = document.getElementById('pagination-info');
    if (totalCount === 0) {
      infoEl.textContent = 'Showing 0 to 0 of 0 records';
    } else {
      infoEl.innerHTML = `Showing <strong class="text-slate-800">${startIdx + 1}</strong> to <strong class="text-slate-800">${endIdx}</strong> of <strong class="text-slate-800">${totalCount}</strong> records${totalCount !== tableRecords.length ? ' (filtered from ' + tableRecords.length + ' total)' : ''}`;
    }

    renderPaginationNav(totalPages);
    updateBulkActionUI();
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

  function exportDataCSV() {
    const filtered = getFilteredRecords();
    if (!filtered.length) {
      PmsAlert.toast('No records to export', 'info');
      return;
    }
    let csv = '"ID","Status Name","Badge Color","Status"\n';
    filtered.forEach(r => {
      csv += `"${r.id}","${r.name}","${r.badge_color || ''}","${r.status}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Operational_Statuses_${Date.now()}.csv`;
    a.click();
    PmsAlert.toast('CSV exported successfully!');
  }

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  window.addEventListener('DOMContentLoaded', () => {
    renderTable();
  });
</script>
@endpush
