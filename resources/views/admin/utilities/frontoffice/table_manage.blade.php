@extends('admin.layouts.app')

@section('title', 'Table Manage Master - Hotel Sagar Sonnet PMS')

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
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }
  .admin-nav-flat:hover { background: #f8fafc; color: var(--accent-primary); border-color: var(--border-light); }
  .admin-nav-flat.active {
    background: rgba(99, 102, 241, 0.08);
    color: var(--accent-primary);
    font-weight: 900;
    border-color: rgba(99, 102, 241, 0.2);
  }
  .admin-sub-menu {
    list-style: none;
    padding: 4px 0 4px 8px;
    margin: 4px 0 0 0;
    border-left: 2px solid var(--border-light);
  }
  .admin-sub-item {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-secondary);
    padding: 7px 12px;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    gap: 9px;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  .admin-sub-item:hover { color: var(--accent-primary); background: #f8fafc; }
  .admin-sub-item.active {
    color: var(--accent-primary);
    background: rgba(99, 102, 241, 0.08);
    font-weight: 800;
  }
  .admin-content-pane {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-lg);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }
  .crud-header-card {
    padding: 16px 20px;
    background: #f8fafc;
    border-bottom: 1px solid var(--border-medium);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .crud-toolbar {
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    border-bottom: 1px solid var(--border-light);
  }
  .crud-toolbar-left, .crud-toolbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .crud-search-wrap {
    position: relative;
    width: 260px;
  }
  .crud-search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 12px;
  }
  .crud-search-input {
    width: 100%;
    height: 38px;
    padding: 6px 12px 6px 34px;
    font-size: 12.5px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    outline: none;
  }
  .crud-search-input:focus {
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }
  .crud-filter-select {
    height: 38px;
    padding: 6px 12px;
    font-size: 12.5px;
    font-weight: 600;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    outline: none;
  }
  .crud-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
    text-align: left;
  }
  .crud-table th {
    background: #f8fafc;
    color: var(--text-secondary);
    font-weight: 700;
    text-transform: uppercase;
    font-size: 10.5px;
    letter-spacing: 0.5px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-medium);
    white-space: nowrap;
  }
  .crud-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-light);
    color: var(--text-primary);
    vertical-align: middle;
  }
  .crud-table tbody tr:hover { background: #fdfdfe; }
  .crud-actions {
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .btn-action-edit, .btn-action-del {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-sm);
    border: 1px solid transparent;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s ease;
  }
  .btn-action-edit {
    background: rgba(99, 102, 241, 0.08);
    color: var(--accent-primary);
  }
  .btn-action-edit:hover {
    background: var(--accent-primary);
    color: #fff;
  }
  .btn-action-del {
    background: rgba(239, 68, 68, 0.08);
    color: #ef4444;
  }
  .btn-action-del:hover {
    background: #ef4444;
    color: #fff;
  }
  .pms-pagination-bar {
    padding: 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    border-top: 1px solid var(--border-medium);
    gap: 12px;
    flex-wrap: wrap;
  }
  .pms-pagination-info { font-size: 12px; color: var(--text-muted); font-weight: 600; }
  .pms-pagination-controls { display: flex; align-items: center; gap: 14px; }
  .pms-per-page-wrap { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); font-weight: 600; }
  .pms-per-page-select {
    height: 30px;
    padding: 0 8px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-sm);
    background: #fff;
    font-size: 12px;
    font-weight: 700;
  }
  .pms-page-nav { display: flex; gap: 4px; }
  .pms-page-btn {
    min-width: 30px;
    height: 30px;
    padding: 0 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-sm);
    background: #fff;
    color: var(--text-primary);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
  }
  .pms-page-btn.active {
    background: var(--accent-primary);
    color: #fff;
    border-color: var(--accent-primary);
  }
  .pms-page-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
  }
  .admin-form-group { margin-bottom: 16px; }
  .admin-form-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-secondary);
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
          <div class="pms-page-subtitle">Configure and manage live records for active hotel and restaurant modules.</div>
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
                <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">Table Management</h2>
                <span class="badge-tag blue" id="view-count">{{ count($items) }} Tables</span>
              </div>
              <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px;">
                Configure restaurant dining tables, seating arrangements, and live booking status.
              </div>
            </div>
            <button class="btn-ui-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Table</button>
          </div>

          <div class="crud-toolbar">
            <div class="crud-toolbar-left">
              <div class="crud-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="table-search" class="crud-search-input" placeholder="Search table # or name..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
              </div>
              <select id="status-filter" class="crud-filter-select" onchange="handleStatusFilter(this.value)">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Booked</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
            <div class="crud-toolbar-right">
              <button class="btn-ui-danger" id="btn-bulk-delete" style="display: none; height: 38px; padding: 0 16px; border-radius: var(--radius-md); font-weight: 700; background: #ef4444; color: #fff; border: none; align-items: center; gap: 6px; cursor: pointer;" onclick="handleBulkDelete()"><i class="fa-solid fa-trash-can"></i> Delete Selected (<span id="bulk-selected-count">0</span>)</button>
              <button class="btn-ui-secondary" onclick="exportDataCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
              <button class="btn-ui-secondary" onclick="loadTableData()"><i class="fa-solid fa-rotate"></i> Refresh</button>
            </div>
          </div>

          <div style="overflow-x: auto;">
            <table class="crud-table" id="crud-table">
              <thead>
                <tr>
                  <th style="width: 40px; text-align: center;"><input type="checkbox" id="select-all-check" onchange="toggleSelectAll(this)" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);"></th>
                  <th style="width: 50px;">#</th>
                  <th>Table Number</th>
                  <th>Table Name / Area</th>
                  <th>Status</th>
                  <th style="text-align: right; width: 120px;">Actions</th>
                </tr>
              </thead>
              <tbody id="table-body">
                @forelse($items as $idx => $item)
                <tr data-id="{{ $item->id }}">
                  <td style="text-align: center;">
                    <input type="checkbox" class="row-select-check" value="{{ $item->id }}" onchange="handleRowSelect(this, '{{ $item->id }}')" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
                  </td>
                  <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">{{ $idx + 1 }}</td>
                  <td style="font-weight: 700; color: var(--text-primary);">
                    <i class="fa-solid fa-chair" style="margin-right: 6px; color: var(--accent-primary); font-size: 12px;"></i>
                    {{ $item->table_number }}
                  </td>
                  <td style="font-weight: 600; color: #475569;">
                    {{ $item->table_name }}
                  </td>
                  <td>
                    @if($item->status === 'available')
                      <span class="badge-tag green"><i class="fa-solid fa-circle-check" style="font-size: 6px; margin-right: 4px;"></i>Available</span>
                    @elseif($item->status === 'booked')
                      <span class="badge-tag amber"><i class="fa-solid fa-circle" style="font-size: 6px; margin-right: 4px;"></i>Booked</span>
                    @else
                      <span class="badge-tag slate"><i class="fa-solid fa-ban" style="font-size: 6px; margin-right: 4px;"></i>Inactive</span>
                    @endif
                  </td>
                  <td style="text-align: right;">
                    <div class="crud-actions">
                      <button class="btn-action-edit" onclick="openEditModal({{ $item->id }})" title="Edit Table"><i class="fa-solid fa-pen"></i></button>
                      <button class="btn-action-del" onclick="deleteItem({{ $item->id }})" title="Delete Table"><i class="fa-solid fa-trash"></i></button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
                    No tables found. Click "+ Add Table" to create one.
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
      <h3 style="margin: 0;"><i class="fa-solid fa-chair"></i> <span id="modal-title">Add Table</span></h3>
      <button class="modal-close" onclick="closeModal('crud-modal')">&times;</button>
    </div>
    <form id="crud-form" onsubmit="handleFormSubmit(event)">
      <input type="hidden" id="item-id">
      <div class="modal-content-area" style="padding: 20px; background: #f8fafc;">
        <div class="admin-form-group">
          <label class="admin-form-label">Table Number / Code <span style="color: var(--accent-rose);">*</span></label>
          <input type="text" id="form-table-number" class="admin-form-input" placeholder="e.g. Table 1, Table 10, VIP-01" required>
        </div>
        <div class="admin-form-group">
          <label class="admin-form-label">Table Name / Location <span style="color: var(--accent-rose);">*</span></label>
          <input type="text" id="form-table-name" class="admin-form-input" placeholder="e.g. Sea Breeze Deck, Poolside View, Indoor AC" required>
        </div>
        <div class="admin-form-group">
          <label class="admin-form-label">Status <span style="color: var(--accent-rose);">*</span></label>
          <select id="form-status" class="admin-form-input" required>
            <option value="available" selected>Available (Default)</option>
            <option value="booked">Booked</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('crud-modal')">Cancel</button>
        <button type="submit" class="btn-ui-primary" id="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Table</button>
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
        PmsAlert.toast(data.message || 'Saved successfully!');
        await loadTableData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to save table.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  async function deleteItem(id) {
    const result = await PmsAlert.confirmDelete('Delete Table?', 'This table will be removed from POS and table dining.');
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
          PmsAlert.toast('Table deleted successfully!');
          await loadTableData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete table.');
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
    pageSize = val;
    currentPage = 1;
    renderTable();
    updateUrlParams();
  }

  function goToPage(page) {
    currentPage = page;
    renderTable();
    updateUrlParams();
  }

  let selectedRowIds = new Set();

  function toggleSelectAll(master) {
    const checks = document.querySelectorAll('.row-select-check');
    checks.forEach(c => {
      c.checked = master.checked;
      if (master.checked) {
        selectedRowIds.add(c.value);
      } else {
        selectedRowIds.delete(c.value);
      }
    });
    updateBulkDeleteUi();
  }

  function handleRowSelect(checkbox, id) {
    if (checkbox.checked) {
      selectedRowIds.add(id);
    } else {
      selectedRowIds.delete(id);
    }
    const selectAll = document.getElementById('select-all-check');
    const allChecks = document.querySelectorAll('.row-select-check');
    if (selectAll) {
      selectAll.checked = allChecks.length > 0 && selectedRowIds.size === allChecks.length;
    }
    updateBulkDeleteUi();
  }

  function updateBulkDeleteUi() {
    const btn = document.getElementById('btn-bulk-delete');
    const countEl = document.getElementById('bulk-selected-count');
    if (!btn || !countEl) return;
    countEl.textContent = selectedRowIds.size;
    btn.style.display = selectedRowIds.size > 0 ? 'inline-flex' : 'none';
  }

  async function handleBulkDelete() {
    if (selectedRowIds.size === 0) return;
    const result = await PmsAlert.confirmDelete(`Delete ${selectedRowIds.size} table(s)?`, 'This will permanently remove the selected tables.');
    if (result.isConfirmed) {
      try {
        const res = await fetch("{{ route('admin.utilities.frontoffice.table-manage.bulk-destroy') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ ids: Array.from(selectedRowIds) })
        });
        const data = await res.json();
        if (res.ok && data.success) {
          PmsAlert.toast(data.message || 'Tables deleted successfully!');
          selectedRowIds.clear();
          updateBulkDeleteUi();
          const selectAll = document.getElementById('select-all-check');
          if (selectAll) selectAll.checked = false;
          await loadTableData();
        } else {
          PmsAlert.error('Bulk Delete Failed', data.message || 'Error deleting tables.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'Failed to communicate with the server.');
      }
    }
  }

  function renderTable() {
    const tbody = document.getElementById('table-body');
    const viewCount = document.getElementById('view-count');
    if (viewCount) viewCount.textContent = `${tableRecords.length} Tables`;

    let filtered = tableRecords;

    if (searchQuery) {
      filtered = filtered.filter(r => 
        (r.table_number && r.table_number.toLowerCase().includes(searchQuery)) ||
        (r.table_name && r.table_name.toLowerCase().includes(searchQuery))
      );
    }

    if (statusFilter && statusFilter !== 'all') {
      filtered = filtered.filter(r => r.status === statusFilter);
    }

    const totalRecords = filtered.length;
    const size = pageSize === 'all' ? totalRecords : parseInt(pageSize, 10);
    const totalPages = Math.ceil(totalRecords / size) || 1;
    if (currentPage > totalPages) currentPage = totalPages;

    const startIdx = (currentPage - 1) * size;
    const endIdx = pageSize === 'all' ? totalRecords : Math.min(startIdx + size, totalRecords);
    const pageRecords = filtered.slice(startIdx, endIdx);

    if (pageRecords.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            No matching tables found.
          </td>
        </tr>
      `;
    } else {
      tbody.innerHTML = pageRecords.map((item, i) => {
        const isChecked = selectedRowIds.has(String(item.id)) ? 'checked' : '';
        const statusBadge = item.status === 'available'
          ? '<span class="badge-tag green"><i class="fa-solid fa-circle-check" style="font-size: 6px; margin-right: 4px;"></i>Available</span>'
          : (item.status === 'booked'
            ? '<span class="badge-tag amber"><i class="fa-solid fa-circle" style="font-size: 6px; margin-right: 4px;"></i>Booked</span>'
            : '<span class="badge-tag slate"><i class="fa-solid fa-ban" style="font-size: 6px; margin-right: 4px;"></i>Inactive</span>');

        return `
          <tr data-id="${item.id}">
            <td style="text-align: center;">
              <input type="checkbox" class="row-select-check" value="${item.id}" onchange="handleRowSelect(this, '${item.id}')" ${isChecked} style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
            </td>
            <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">${startIdx + i + 1}</td>
            <td style="font-weight: 700; color: var(--text-primary);">
              <i class="fa-solid fa-chair" style="margin-right: 6px; color: var(--accent-primary); font-size: 12px;"></i>
              ${escapeHtml(item.table_number)}
            </td>
            <td style="font-weight: 600; color: #475569;">
              ${escapeHtml(item.table_name)}
            </td>
            <td>${statusBadge}</td>
            <td style="text-align: right;">
              <div class="crud-actions">
                <button class="btn-action-edit" onclick="openEditModal(${item.id})" title="Edit Table"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-action-del" onclick="deleteItem(${item.id})" title="Delete Table"><i class="fa-solid fa-trash"></i></button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    renderPagination(totalRecords, startIdx, endIdx, totalPages);
  }

  function renderPagination(total, start, end, totalPages) {
    const info = document.getElementById('pagination-info');
    if (info) {
      info.textContent = total > 0 ? `Showing ${start + 1} to ${end} of ${total} records` : '0 records';
    }

    const nav = document.getElementById('pagination-nav');
    if (!nav) return;

    if (totalPages <= 1) {
      nav.innerHTML = '';
      return;
    }

    let buttons = '';
    buttons += `<button class="pms-page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="goToPage(${currentPage - 1})"><i class="fa-solid fa-chevron-left"></i></button>`;

    for (let p = 1; p <= totalPages; p++) {
      if (totalPages > 7) {
        if (p > 1 && p < currentPage - 1 && p === 2) {
          buttons += `<span style="padding: 0 4px; color: var(--text-muted);">...</span>`;
          continue;
        }
        if (p < totalPages && p > currentPage + 1 && p === totalPages - 1) {
          buttons += `<span style="padding: 0 4px; color: var(--text-muted);">...</span>`;
          continue;
        }
        if (p > 2 && p < currentPage - 1) continue;
        if (p < totalPages - 1 && p > currentPage + 1) continue;
      }

      buttons += `<button class="pms-page-btn ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`;
    }

    buttons += `<button class="pms-page-btn ${currentPage === totalPages ? 'disabled' : ''} onclick="goToPage(${currentPage + 1})"><i class="fa-solid fa-chevron-right"></i></button>`;
    nav.innerHTML = buttons;
  }

  function exportDataCSV() {
    if (!tableRecords || tableRecords.length === 0) {
      PmsAlert.toast('No data to export', 'warning');
      return;
    }
    const headers = ['ID', 'Table Number', 'Table Name', 'Status', 'Created At'];
    const rows = tableRecords.map(r => [
      r.id,
      `"${(r.table_number || '').replace(/"/g, '""')}"`,
      `"${(r.table_name || '').replace(/"/g, '""')}"`,
      `"${r.status || ''}"`,
      `"${r.created_at || ''}"`
    ]);

    let csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `table_manages_${new Date().toISOString().slice(0,10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
  }

  function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'flex';
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  }

  document.addEventListener('DOMContentLoaded', () => {
    renderTable();
  });
</script>
@endpush

