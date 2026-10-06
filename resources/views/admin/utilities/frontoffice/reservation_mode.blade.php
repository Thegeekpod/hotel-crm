@extends('admin.layouts.app')

@section('title', 'Mode of Reserve - Hotel Sagar Sonnet PMS')

@push('styles')
<style>
  .admin-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 20px;
    min-height: calc(100vh - 165px);
  }
  .pms-page-header {
    border-bottom: none !important;
    background: transparent !important;
  }
  .admin-sidebar {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-lg);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    padding: 14px;
    height: fit-content;
    position: sticky;
    top: 10px;
    max-height: calc(100vh - 180px);
    overflow-y: auto;
  }
  .admin-nav-group { margin-bottom: 8px; }
  .admin-nav-header {
    font-size: 11px;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 9px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: var(--radius-md);
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
  }
  .admin-nav-header:hover { background: #f1f5f9; color: var(--accent-primary); }
  .admin-nav-header.active {
    color: var(--accent-primary);
    font-weight: 900;
    background: rgba(99, 102, 241, 0.08);
    border-color: rgba(99, 102, 241, 0.2);
  }
  .admin-nav-flat {
    font-size: 11px;
    font-weight: 800;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: var(--radius-md);
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
    background: #ffffff;
    border: 1px solid #f1f5f9;
    margin-bottom: 6px;
  }
  .admin-nav-flat:hover { background: #f8fafc; color: var(--accent-primary); }
  .admin-sub-list {
    list-style: none;
    padding: 4px 0 4px 8px;
    margin: 4px 0 0 4px;
    border-left: 2px solid var(--border-medium);
  }
  .admin-sub-item {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    padding: 7px 10px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    margin-bottom: 2px;
    text-decoration: none;
  }
  .admin-sub-item:hover {
    background: rgba(99, 102, 241, 0.06);
    color: var(--accent-primary);
    transform: translateX(3px);
  }
  .admin-sub-item.active {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(168, 85, 247, 0.08));
    color: var(--accent-primary);
    font-weight: 800;
    border-left: 3px solid var(--accent-primary);
  }
  .admin-content-pane {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-lg);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    padding: 24px;
  }
  .crud-header-card {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  }
  .crud-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }
  .crud-search-input {
    height: 38px;
    padding: 8px 14px 8px 34px;
    font-size: 13px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    width: 280px;
    max-width: 100%;
  }
  .crud-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    background: #ffffff;
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 1px solid var(--border-medium);
  }
  .crud-table th {
    background: #f8fafc;
    padding: 12px 16px;
    font-size: 10px;
    font-weight: 800;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 1px solid var(--border-medium);
    text-align: left;
  }
  .crud-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: var(--text-primary);
    vertical-align: middle;
  }
  .crud-table tr:hover td { background: rgba(99, 102, 241, 0.02); }
  .crud-actions { display: flex; gap: 8px; justify-content: flex-end; }
  .btn-action-edit {
    padding: 6px 11px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-sm);
    background: #fff;
    color: var(--accent-primary);
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-action-edit:hover { background: var(--accent-primary); color: #fff; }
  .btn-action-del {
    padding: 6px 11px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid #fecaca;
    border-radius: var(--radius-sm);
    background: #fff;
    color: var(--accent-rose);
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-action-del:hover { background: var(--accent-rose); color: #fff; }
  .admin-form-group { margin-bottom: 16px; }
  .admin-form-label {
    display: block;
    font-size: 11px;
    font-weight: 800;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 6px;
  }
  .admin-form-input {
    width: 100%;
    height: 40px;
    padding: 8px 14px;
    font-size: 13px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    box-sizing: border-box;
  }
  .admin-form-input:focus {
    outline: none;
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }
</style>
@endpush

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">

    <div class="pms-page-header">
      <div class="pms-header-title-block">
        <div class="pms-status-indicator"></div>
        <div>
          <h1 class="pms-page-title">Administrator • Master Data Management</h1>
          <div class="pms-page-subtitle">Configure and manage live records for active hotel modules.</div>
        </div>
      </div>
      <div style="display: flex; gap: 10px;">
        <button class="btn-ui-secondary" onclick="exportDataCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
      </div>
    </div>

    <div class="admin-layout">
      @include('admin.includes.sidebar')

      <section class="admin-content-pane">
        <div id="crud-view-container">
          <div class="crud-header-card">
            <div>
              <div style="display: flex; align-items: center; gap: 8px;">
                <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">Mode of Reserve</h2>
                <span class="badge-tag blue" id="view-count">{{ count($items) }} Records</span>
              </div>
              <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px;">
                Manage reservation booking modes (Phone Call, Physical, Website, Online).
              </div>
            </div>
            <button class="btn-ui-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Entry</button>
          </div>

          <div class="crud-toolbar">
            <div class="crud-toolbar-left">
              <div class="crud-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="table-search" class="crud-search-input" placeholder="Search active records..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
              </div>
              <select id="status-filter" class="crud-filter-select" onchange="handleStatusFilter(this.value)">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
            <div class="crud-toolbar-right">
              <button class="btn-ui-secondary" onclick="exportDataCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
              <button class="btn-ui-secondary" onclick="loadTableData()"><i class="fa-solid fa-rotate"></i> Refresh</button>
            </div>
          </div>

          <div style="overflow-x: auto;">
            <table class="crud-table" id="crud-table">
              <thead>
                <tr>
                  <th style="width: 50px;">#</th>
                  <th>Mode of Reserve</th>
                  <th>Status</th>
                  <th style="text-align: right; width: 120px;">Actions</th>
                </tr>
              </thead>
              <tbody id="table-body">
                @forelse($items as $idx => $item)
                <tr data-id="{{ $item->id }}">
                  <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">{{ $idx + 1 }}</td>
                  <td style="font-weight: 600; color: var(--text-primary);">{{ $item->name }}</td>
                  <td>
                    <span class="badge-tag {{ $item->status === 'Active' ? 'green' : 'yellow' }}">
                      <i class="fa-solid fa-circle-check" style="font-size: 6px; margin-right: 4px;"></i>{{ $item->status }}
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="crud-actions">
                      <button class="btn-action-edit" onclick="openEditModal({{ $item->id }})" title="Edit Record"><i class="fa-solid fa-pen"></i></button>
                      <button class="btn-action-del" onclick="deleteItem({{ $item->id }})" title="Delete Record"><i class="fa-solid fa-trash"></i></button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
                    No records found. Click "+ Add Entry" to create one.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination & Rows Controls -->
          <div class="pms-pagination-bar" id="table-pagination-bar">
            <div class="pms-pagination-info" id="pagination-info">
              Showing 1 to {{ count($items) }} of {{ count($items) }} records
            </div>
            <div class="pms-pagination-controls">
              <div class="pms-per-page-wrap">
                <span>Rows:</span>
                <select id="rows-per-page" class="pms-per-page-select" onchange="handlePageSizeChange(this.value)">
                  <option value="10" selected>10</option>
                  <option value="20">20</option>
                  <option value="100">100</option>
                  <option value="all">All</option>
                </select>
              </div>
              <div class="pms-page-nav" id="pagination-nav">
                <!-- Injected via JavaScript -->
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

  </div>
</main>

<div class="modal-backdrop" id="crud-modal">
  <div class="modal-window" style="width: 520px; max-width: 95vw;">
    <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0;"><i class="fa-solid fa-pen-to-square"></i> <span id="modal-title">Add Mode of Reserve</span></h3>
      <button class="modal-close" onclick="closeModal('crud-modal')">&times;</button>
    </div>
    <form id="crud-form" onsubmit="handleFormSubmit(event)">
      <input type="hidden" id="item-id">
      <div class="modal-content-area" style="padding: 20px; background: #f8fafc;">
        <div class="admin-form-group">
          <label class="admin-form-label">Mode of Reserve Name <span style="color: var(--accent-rose);">*</span></label>
          <input type="text" id="form-name" class="admin-form-input" placeholder="e.g. Phone Call, Physical, Website" required>
        </div>
        <div class="admin-form-group">
          <label class="admin-form-label">Status <span style="color: var(--accent-rose);">*</span></label>
          <select id="form-status" class="admin-form-input" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('crud-modal')">Cancel</button>
        <button type="submit" class="btn-ui-primary" id="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Entry</button>
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

  const baseUrl = "{{ url('admin/utilities/front-office/reservation-mode') }}";
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

  function openAddModal() {
    document.getElementById('modal-title').textContent = 'Add Mode of Reserve';
    document.getElementById('item-id').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-status').value = 'Active';
    openModal('crud-modal');
  }

  function openEditModal(id) {
    const item = tableRecords.find(r => String(r.id) === String(id));
    if (!item) {
      console.warn('Record not found for id:', id);
      return;
    }

    document.getElementById('modal-title').textContent = 'Edit Mode of Reserve';
    document.getElementById('item-id').value = item.id;
    document.getElementById('form-name').value = item.name;
    document.getElementById('form-status').value = item.status;
    openModal('crud-modal');
  }

  async function handleFormSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('item-id').value;
    const name = document.getElementById('form-name').value.trim();
    const status = document.getElementById('form-status').value;

    if (!name) {
      PmsAlert.error('Validation Error', 'Please provide a valid reservation mode name.');
      return;
    }

    const payload = { name, status };
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
    const result = await PmsAlert.confirmDelete('Delete Reservation Mode?', 'This reservation mode will be removed permanently.');
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
          await loadTableData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete record.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'An error occurred during deletion.');
      }
    }
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

  function getFilteredRecords() {
    return tableRecords.filter(item => {
      const matchSearch = !searchQuery ||
        (item.name && item.name.toLowerCase().includes(searchQuery)) ||
        (item.status && item.status.toLowerCase().includes(searchQuery));

      const matchStatus = statusFilter === 'all' || item.status === statusFilter;
      return matchSearch && matchStatus;
    });
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
      tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 40px; color: var(--text-muted);"><i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>No matching records found.</td></tr>`;
    } else {
      let html = '';
      pageItems.forEach((item, idx) => {
        const rowNumber = startIdx + idx + 1;
        const badgeClass = item.status === 'Active' ? 'green' : 'yellow';
        html += `
          <tr data-id="${item.id}">
            <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">${rowNumber}</td>
            <td style="font-weight: 600; color: var(--text-primary);">${item.name}</td>
            <td>
              <span class="badge-tag ${badgeClass}">
                <i class="fa-solid fa-circle-check" style="font-size: 6px; margin-right: 4px;"></i>${item.status}
              </span>
            </td>
            <td style="text-align: right;">
              <div class="crud-actions">
                <button class="btn-action-edit" onclick="openEditModal(${item.id})" title="Edit Record"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-action-del" onclick="deleteItem(${item.id})" title="Delete Record"><i class="fa-solid fa-trash"></i></button>
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
      infoEl.innerHTML = `Showing <strong style="color: var(--text-primary);">${startIdx + 1}</strong> to <strong style="color: var(--text-primary);">${endIdx}</strong> of <strong style="color: var(--text-primary);">${totalCount}</strong> records${totalCount !== tableRecords.length ? ' (filtered from ' + tableRecords.length + ' total)' : ''}`;
    }

    renderPaginationNav(totalPages);
  }

  function renderPaginationNav(totalPages) {
    const nav = document.getElementById('pagination-nav');
    if (!nav) return;

    if (totalPages <= 1) {
      nav.innerHTML = `
        <button class="pms-page-btn" disabled><i class="fa-solid fa-chevron-left"></i></button>
        <button class="pms-page-btn active">1</button>
        <button class="pms-page-btn" disabled><i class="fa-solid fa-chevron-right"></i></button>
      `;
      return;
    }

    let html = '';
    html += `<button class="pms-page-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} title="Previous Page"><i class="fa-solid fa-chevron-left"></i></button>`;

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
        html += `<span class="pms-page-dots">...</span>`;
      } else {
        html += `<button class="pms-page-btn ${page === currentPage ? 'active' : ''}" onclick="goToPage(${page})">${page}</button>`;
      }
    });

    html += `<button class="pms-page-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} title="Next Page"><i class="fa-solid fa-chevron-right"></i></button>`;

    nav.innerHTML = html;
  }

  function exportDataCSV() {
    const filtered = getFilteredRecords();
    if (!filtered.length) {
      PmsAlert.toast('No records to export', 'info');
      return;
    }
    let csv = '"ID","Mode of Reserve","Status"\n';
    filtered.forEach(r => {
      csv += `"${r.id}","${r.name}","${r.status}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Reservation_Modes_${Date.now()}.csv`;
    a.click();
    PmsAlert.toast('CSV exported successfully!');
  }

  // Initialize table on load
  window.addEventListener('DOMContentLoaded', () => {
    renderTable();
  });
</script>
@endpush
