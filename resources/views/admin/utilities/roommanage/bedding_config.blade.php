@extends('admin.layouts.app')

@section('title', 'Bedding Configurations Master - Hotel Sagar Sonnet PMS')

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
  .admin-form-input[disabled], .admin-form-input[readonly] {
    background: #f1f5f9;
    color: var(--text-muted);
    cursor: not-allowed;
    border-color: #cbd5e1;
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
                <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">Bedding Configurations</h2>
                <span class="badge-tag blue" id="view-count">{{ count($items) }} Records</span>
              </div>
              <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px;">
                Manage bed types and guest capacity limits (Max Adults, Children, Total Pax).
              </div>
            </div>
            <button class="btn-ui-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Entry</button>
          </div>

          <div class="crud-toolbar">
            <div class="crud-toolbar-left">
              <div class="crud-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="table-search" class="crud-search-input" placeholder="Search bedding config..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
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
                  <th>Bedding Configuration Name</th>
                  <th style="width: 120px;">Max Adults</th>
                  <th style="width: 120px;">Max Children</th>
                  <th style="width: 130px;">Total Max Pax</th>
                  <th style="width: 110px;">Status</th>
                  <th style="text-align: right; width: 120px;">Actions</th>
                </tr>
              </thead>
              <tbody id="table-body">
                @forelse($items as $idx => $item)
                <tr data-id="{{ $item->id }}">
                  <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">{{ $idx + 1 }}</td>
                  <td style="font-weight: 700; color: var(--text-primary);">
                    <i class="fa-solid fa-bed" style="color: var(--accent-primary); margin-right: 6px; font-size: 13px;"></i>
                    {{ $item->name }}
                  </td>
                  <td>
                    <span style="font-weight: 700; color: var(--text-primary); font-size: 12px;">
                      <i class="fa-solid fa-user" style="color: var(--accent-primary); margin-right: 4px; font-size: 11px;"></i>{{ $item->max_adults ?? 2 }} Adults
                    </span>
                  </td>
                  <td>
                    <span style="font-weight: 700; color: var(--text-secondary); font-size: 12px;">
                      <i class="fa-solid fa-child" style="color: var(--accent-amber); margin-right: 4px; font-size: 11px;"></i>{{ $item->max_children ?? 0 }} Child
                    </span>
                  </td>
                  <td>
                    <span class="badge-tag purple" style="font-weight: 800;">
                      <i class="fa-solid fa-users" style="margin-right: 4px;"></i>{{ $item->max_total ?? (($item->max_adults ?? 2) + ($item->max_children ?? 0)) }} Pax
                    </span>
                  </td>
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
                  <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
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
  <div class="modal-window" style="width: 540px; max-width: 95vw;">
    <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0;"><i class="fa-solid fa-pen-to-square"></i> <span id="modal-title">Add Bedding Config</span></h3>
      <button class="modal-close" onclick="closeModal('crud-modal')">&times;</button>
    </div>
    <form id="crud-form" onsubmit="handleFormSubmit(event)">
      <input type="hidden" id="item-id">
      <div class="modal-content-area" style="padding: 20px; background: #f8fafc;">
        
        <div class="admin-form-group">
          <label class="admin-form-label">Bedding Configuration Name <span style="color: var(--accent-rose);">*</span></label>
          <input type="text" id="form-name" class="admin-form-input" placeholder="e.g. King Size Master (72x78), Queen Size Double (60x78)" required>
        </div>

        <!-- PAX CAPACITY CARD -->
        <div style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 16px; margin-bottom: 16px;">
          <div style="font-size: 11px; font-weight: 800; color: var(--accent-primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-user-group"></i>
            <span>PAX OCCUPANCY CAPACITY</span>
          </div>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
            <div class="admin-form-group" style="margin-bottom: 0;">
              <label class="admin-form-label">Max Adults <span style="color: var(--accent-rose);">*</span></label>
              <input type="number" id="form-max-adults" class="admin-form-input" min="1" max="20" value="2" required oninput="calculateTotalPax()">
            </div>
            <div class="admin-form-group" style="margin-bottom: 0;">
              <label class="admin-form-label">Max Children</label>
              <input type="number" id="form-max-children" class="admin-form-input" min="0" max="10" value="0" oninput="calculateTotalPax()">
            </div>
            <div class="admin-form-group" style="margin-bottom: 0;">
              <label class="admin-form-label">Total Max Pax</label>
              <input type="number" id="form-max-total" class="admin-form-input" value="2" readonly disabled style="font-weight: 800; color: var(--accent-primary);">
            </div>
          </div>
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

  const baseUrl = "{{ url('admin/utilities/room-management/bedding-config') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function calculateTotalPax() {
    const adults = parseInt(document.getElementById('form-max-adults').value, 10) || 0;
    const children = parseInt(document.getElementById('form-max-children').value, 10) || 0;
    document.getElementById('form-max-total').value = adults + children;
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
    document.getElementById('modal-title').textContent = 'Add Bedding Config';
    document.getElementById('item-id').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-max-adults').value = '2';
    document.getElementById('form-max-children').value = '0';
    document.getElementById('form-max-total').value = '2';
    document.getElementById('form-status').value = 'Active';
    openModal('crud-modal');
  }

  function openEditModal(id) {
    const item = tableRecords.find(r => String(r.id) === String(id));
    if (!item) {
      console.warn('Record not found for id:', id);
      return;
    }

    document.getElementById('modal-title').textContent = 'Edit Bedding Config';
    document.getElementById('item-id').value = item.id;
    document.getElementById('form-name').value = item.name;
    document.getElementById('form-max-adults').value = item.max_adults ?? 2;
    document.getElementById('form-max-children').value = item.max_children ?? 0;
    document.getElementById('form-max-total').value = item.max_total ?? ((item.max_adults ?? 2) + (item.max_children ?? 0));
    document.getElementById('form-status').value = item.status;
    openModal('crud-modal');
  }

  async function handleFormSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('item-id').value;
    const name = document.getElementById('form-name').value.trim();
    const max_adults = parseInt(document.getElementById('form-max-adults').value, 10) || 1;
    const max_children = parseInt(document.getElementById('form-max-children').value, 10) || 0;
    const max_total = max_adults + max_children;
    const status = document.getElementById('form-status').value;

    if (!name) {
      PmsAlert.error('Validation Error', 'Please provide bedding configuration name.');
      return;
    }

    const payload = { name, max_adults, max_children, max_total, status };
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
    const result = await PmsAlert.confirmDelete('Delete Bedding Configuration?', 'This bedding setup will be removed.');
    if (result && (result.isConfirmed || result === true)) {
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

  function getFilteredRecords() {
    return tableRecords.filter(item => {
      const matchSearch = !searchQuery ||
        (item.name && item.name.toLowerCase().includes(searchQuery)) ||
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
          <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            No matching bedding configurations found.
          </td>
        </tr>
      `;
      renderPagination(totalRecords, totalPages, 0, 0);
      return;
    }

    const startIndex = pageSize === 'all' ? 0 : (currentPage - 1) * parseInt(pageSize, 10);

    tbody.innerHTML = paginatedRecords.map((item, idx) => {
      const adults = item.max_adults ?? 2;
      const children = item.max_children ?? 0;
      const total = item.max_total ?? (adults + children);

      return `
        <tr data-id="${item.id}">
          <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">
            ${startIndex + idx + 1}
          </td>
          <td style="font-weight: 700; color: var(--text-primary);">
            <i class="fa-solid fa-bed" style="color: var(--accent-primary); margin-right: 6px; font-size: 13px;"></i>
            ${escapeHtml(item.name)}
          </td>
          <td>
            <span style="font-weight: 700; color: var(--text-primary); font-size: 12px;">
              <i class="fa-solid fa-user" style="color: var(--accent-primary); margin-right: 4px; font-size: 11px;"></i>${adults} Adults
            </span>
          </td>
          <td>
            <span style="font-weight: 700; color: var(--text-secondary); font-size: 12px;">
              <i class="fa-solid fa-child" style="color: var(--accent-amber); margin-right: 4px; font-size: 11px;"></i>${children} Child
            </span>
          </td>
          <td>
            <span class="badge-tag purple" style="font-weight: 800;">
              <i class="fa-solid fa-users" style="margin-right: 4px;"></i>${total} Pax
            </span>
          </td>
          <td>
            <span class="badge-tag ${item.status === 'Active' ? 'green' : 'yellow'}">
              <i class="fa-solid fa-circle-check" style="font-size: 6px; margin-right: 4px;"></i>${escapeHtml(item.status)}
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
    }).join('');

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
      <button class="pms-page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="goToPage(${currentPage - 1})">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
    `;

    for (let p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || (p >= currentPage - 1 && p <= currentPage + 1)) {
        buttons += `
          <button class="pms-page-btn ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">
            ${p}
          </button>
        `;
      } else if (p === currentPage - 2 || p === currentPage + 2) {
        buttons += `<span class="pms-page-btn" style="border:none; cursor:default;">...</span>`;
      }
    }

    buttons += `
      <button class="pms-page-btn" ${currentPage === pages ? 'disabled' : ''} onclick="goToPage(${currentPage + 1})">
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

    const headers = ['#', 'Bedding Configuration', 'Max Adults', 'Max Children', 'Total Max Pax', 'Status'];
    const rows = records.map((r, i) => [
      i + 1,
      `"${(r.name || '').replace(/"/g, '""')}"`,
      r.max_adults ?? 2,
      r.max_children ?? 0,
      r.max_total ?? ((r.max_adults ?? 2) + (r.max_children ?? 0)),
      `"${(r.status || '').replace(/"/g, '""')}"`
    ]);

    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `bedding_configs_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    PmsAlert.toast('CSV exported successfully!');
  }

  function escapeHtml(str) {
    if (!str && str !== 0) return '';
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
