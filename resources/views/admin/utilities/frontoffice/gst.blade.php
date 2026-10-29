@extends('admin.layouts.app')

@section('title', 'GST % Management - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 p-4 lg:p-6 space-y-6">

  <!-- Page Header -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></span>
      <div>
        <h1 class="text-base font-extrabold text-slate-900 tracking-tight">Administrator • Master Data Management</h1>
        <p class="text-xs text-slate-500 font-medium">Configure and manage live records for active hotel modules.</p>
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
              <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">GST Tax Slabs Master</h2>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80" id="view-count">{{ count($items) }} Records</span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
              Configure Goods &amp; Services Tax percentages for room tariffs and extra services billing.
            </p>
          </div>
          <button type="button" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer shrink-0" onclick="openAddModal()">
            <i class="fa-solid fa-plus"></i> Add Entry
          </button>
        </div>

        <!-- Toolbar -->
        <div class="p-4 bg-slate-50/70 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
          <div class="flex items-center gap-2.5 flex-1 min-w-[260px]">
            <div class="relative flex-1 max-w-sm">
              <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
              <input type="text" id="table-search" class="h-9.5 pl-8 pr-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="Search GST percentage..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
            </div>
            <select id="status-filter" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 cursor-pointer" onchange="handleStatusFilter(this.value)">
              <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
              <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
              <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
                <th class="py-3 px-3">GST Percentage Slab</th>
                <th class="py-3 px-3 w-32">Status</th>
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
                <td class="py-3 px-3">
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black font-mono bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                    <i class="fa-solid fa-percent text-[10px]"></i> {{ number_format((float)$item->gst_percentage, 2) }}%
                  </span>
                </td>
                <td class="py-3 px-3">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' : 'bg-slate-100 text-slate-700 border border-slate-200/80' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $item->status === 'Active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ $item->status }}
                  </span>
                </td>
                <td class="py-3 px-3 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button type="button" class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors flex items-center justify-center cursor-pointer" onclick="openEditModal({{ $item->id }})" title="Edit Record"><i class="fa-solid fa-pen text-xs"></i></button>
                    <button type="button" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors flex items-center justify-center cursor-pointer" onclick="deleteItem({{ $item->id }})" title="Delete Record"><i class="fa-solid fa-trash text-xs"></i></button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="py-8 text-center text-slate-400 font-medium">
                  <i class="fa-solid fa-inbox text-2xl mb-2 block text-slate-300"></i>
                  No records found. Click "+ Add Entry" to create one.
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
        <i class="fa-solid fa-pen-to-square text-indigo-600"></i> <span id="modal-title">Add GST % Slab</span>
      </h3>
      <button type="button" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-base cursor-pointer transition-colors" onclick="closeModal('crud-modal')">&times;</button>
    </div>
    <form id="crud-form" onsubmit="handleFormSubmit(event)">
      <input type="hidden" id="item-id">
      <div class="p-6 space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">GST Percentage (%) <span class="text-rose-500">*</span></label>
          <div class="relative">
            <input type="number" step="0.01" min="0" max="100" id="form-percentage" class="h-9.5 pl-3 pr-8 text-xs font-mono font-bold rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="e.g. 5, 12, 18, 28" required>
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Status <span class="text-rose-500">*</span></label>
          <select id="form-status" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full cursor-pointer" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="px-6 py-3 bg-slate-50/80 border-t border-slate-200/80 flex items-center justify-end gap-2">
        <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeModal('crud-modal')">Cancel</button>
        <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer" id="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save GST Slab</button>
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

  const baseUrl = "{{ url('admin/utilities/front-office/gst-percentage') }}";
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
    document.getElementById('modal-title').textContent = 'Add GST % Slab';
    document.getElementById('item-id').value = '';
    document.getElementById('form-percentage').value = '';
    document.getElementById('form-status').value = 'Active';
    openModal('crud-modal');
  }

  function openEditModal(id) {
    const item = tableRecords.find(r => String(r.id) === String(id));
    if (!item) {
      console.warn('Record not found for id:', id);
      return;
    }

    document.getElementById('modal-title').textContent = 'Edit GST % Slab';
    document.getElementById('item-id').value = item.id;
    document.getElementById('form-percentage').value = parseFloat(item.gst_percentage);
    document.getElementById('form-status').value = item.status;
    openModal('crud-modal');
  }

  async function handleFormSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('item-id').value;
    const percentage = parseFloat(document.getElementById('form-percentage').value);
    const status = document.getElementById('form-status').value;

    if (isNaN(percentage) || percentage < 0) {
      PmsAlert.error('Validation Error', 'Please provide a valid GST percentage.');
      return;
    }

    const payload = { gst_percentage: percentage, status };
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
        PmsAlert.error('Error', data.message || 'Failed to save record.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  async function deleteItem(id) {
    const result = await PmsAlert.confirmDelete('Delete GST Slab?', 'This GST percentage slab will be removed permanently.');
    if (result.isConfirmed) {
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
          PmsAlert.toast(data.message || 'Record deleted successfully!');
          await loadTableData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete record.');
          await loadTableData();
        }
      } catch (err) {
        console.error(err);
        PmsAlert.error('Server Error', 'An error occurred during deletion.');
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
    const result = await PmsAlert.confirmDelete(`Delete ${count} Selected Records?`, 'All selected GST slabs will be removed.');
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
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success !== false) {
          PmsAlert.toast(data.message || `${count} record(s) deleted successfully!`);
          selectedIds.clear();
          await loadTableData();
        } else {
          PmsAlert.error('Bulk Delete Failed', data.message || 'Could not delete selected records.');
          await loadTableData();
        }
      } catch (err) {
        console.error(err);
        PmsAlert.error('Server Error', 'Failed to communicate with the server.');
      }
    }
  }

  function getFilteredRecords() {
    return tableRecords.filter(item => {
      const matchSearch = !searchQuery ||
        (item.gst_percentage && String(item.gst_percentage).toLowerCase().includes(searchQuery)) ||
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
          <td colspan="5" class="py-8 text-center text-slate-400 font-medium">
            <i class="fa-solid fa-inbox text-2xl mb-2 block text-slate-300"></i>
            No matching GST percentage slabs found.
          </td>
        </tr>
      `;
      renderPagination(totalRecords, totalPages, 0, 0);
      updateBulkActionUI();
      return;
    }

    const startIndex = pageSize === 'all' ? 0 : (currentPage - 1) * parseInt(pageSize, 10);

    tbody.innerHTML = paginatedRecords.map((item, idx) => {
      const formattedGst = parseFloat(item.gst_percentage).toFixed(2);

      return `
        <tr class="hover:bg-slate-50/60 transition-colors" data-id="${item.id}">
          <td class="py-3 px-3 text-center">
            <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500" value="${item.id}" ${selectedIds.has(String(item.id)) ? 'checked' : ''} onchange="handleRowSelect(this, '${item.id}')">
          </td>
          <td class="py-3 px-3 font-mono font-bold text-slate-400 text-xs">
            ${startIndex + idx + 1}
          </td>
          <td class="py-3 px-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black font-mono bg-indigo-50 text-indigo-700 border border-indigo-200/80">
              <i class="fa-solid fa-percent text-[10px]"></i> ${escapeHtml(formattedGst)}%
            </span>
          </td>
          <td class="py-3 px-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold ${item.status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' : 'bg-slate-100 text-slate-700 border border-slate-200/80'}">
              <span class="w-1.5 h-1.5 rounded-full {{ $item->status === 'Active' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
              ${escapeHtml(item.status)}
            </span>
          </td>
          <td class="py-3 px-3 text-right">
            <div class="flex items-center justify-end gap-1">
              <button type="button" class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors flex items-center justify-center cursor-pointer" onclick="openEditModal(${item.id})" title="Edit Record"><i class="fa-solid fa-pen text-xs"></i></button>
              <button type="button" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors flex items-center justify-center cursor-pointer" onclick="deleteItem(${item.id})" title="Delete Record"><i class="fa-solid fa-trash text-xs"></i></button>
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
      PmsAlert.warning('Export CSV', 'No records to export.');
      return;
    }

    const headers = ['#', 'GST Percentage (%)', 'Status'];
    const rows = records.map((r, i) => [
      i + 1,
      parseFloat(r.gst_percentage).toFixed(2),
      `"${(r.status || '').replace(/"/g, '""')}"`
    ]);

    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `gst_slabs_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    PmsAlert.toast('CSV exported successfully!');
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
