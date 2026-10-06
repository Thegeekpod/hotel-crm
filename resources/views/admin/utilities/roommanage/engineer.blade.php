@extends('admin.layouts.app')

@section('title', 'Maintain Engineers Master - Hotel Sagar Sonnet PMS')

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
    flex-wrap: wrap;
  }
  .crud-toolbar-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .crud-toolbar-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .crud-search-wrap {
    position: relative;
    display: flex;
    align-items: center;
  }
  .crud-search-wrap i {
    position: absolute;
    left: 12px;
    color: var(--text-muted);
    font-size: 12px;
  }
  .crud-search-input {
    height: 38px;
    padding: 8px 14px 8px 34px;
    font-size: 13px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
  }
  .crud-search-input:focus {
    outline: none;
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
  }
  .crud-filter-select {
    height: 38px;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    cursor: pointer;
  }
  .crud-filter-select:focus {
    outline: none;
    border-color: var(--accent-primary);
  }
  .pms-crud-table-wrap {
    overflow-x: auto;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #ffffff;
  }
  .pms-crud-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }
  .pms-crud-table th {
    background: #f8fafc;
    padding: 12px 16px;
    font-size: 11px;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 1px solid var(--border-medium);
    text-align: left;
  }
  .pms-crud-table td {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-subtle, #f1f5f9);
    color: var(--text-primary);
    vertical-align: middle;
  }
  .pms-crud-table tbody tr:hover {
    background: #f8fafc;
  }
  .table-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
  }
  .table-badge.active { background: rgba(16, 185, 129, 0.12); color: #059669; }
  .table-badge.inactive { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
  .table-badge.dept { background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); }

  /* Table Pagination Styles */
  .table-pagination-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    border-top: 1px solid var(--border-medium);
    background: #f8fafc;
    flex-wrap: wrap;
    gap: 12px;
  }
  .pagination-controls-group {
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .pms-page-btn {
    min-width: 32px;
    height: 32px;
    padding: 0 6px;
    border-radius: 6px;
    border: 1px solid var(--border-medium);
    background: #ffffff;
    color: var(--text-secondary);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }
  .pms-page-btn:hover:not(:disabled) {
    border-color: var(--accent-primary);
    color: var(--accent-primary);
    background: rgba(99, 102, 241, 0.05);
  }
  .pms-page-btn.active {
    background: var(--accent-primary);
    color: #ffffff;
    border-color: var(--accent-primary);
    box-shadow: 0 2px 6px rgba(99, 102, 241, 0.35);
  }
  .pms-page-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
  }
  .pms-page-dots {
    padding: 0 4px;
    color: var(--text-muted);
    font-weight: 800;
    font-size: 12px;
  }
</style>
@endpush

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">
    <div class="pms-page-header">
      <div class="pms-page-title-wrap">
        <h2 class="pms-page-title"><i class="fa-solid fa-user-gear"></i> Administrator • Master Data Management</h2>
        <p class="pms-page-desc">Configure and manage live records for active hotel modules.</p>
      </div>
    </div>

    <div class="admin-layout">
      <!-- Shared Left Sidebar -->
      @include('admin.includes.sidebar')

      <!-- Right Main Content Area -->
      <section class="admin-content-pane">
        <!-- CRUD Header Card -->
        <div class="crud-header-card">
          <div>
            <h3 style="font-size: 18px; font-weight: 800; margin: 0; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-helmet-safety" style="color: var(--accent-primary);"></i> Maintain Engineers
              <span id="header-total-count" style="font-size: 12px; font-weight: 700; background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); padding: 2px 8px; border-radius: 12px;">{{ count($items) }} records</span>
            </h3>
            <p style="font-size: 12px; color: var(--text-muted); margin: 4px 0 0 0;">Maintain dedicated engineering and technician personnel for room maintenance work orders.</p>
          </div>
          <button class="btn-ui-primary" onclick="openAddEngineerModal()" style="height: 38px; padding: 0 16px; font-size: 12px; font-weight: 700;">
            <i class="fa-solid fa-plus-circle"></i> Add Engineer
          </button>
        </div>

        <!-- Filter & Control Toolbar -->
        <div class="crud-toolbar">
          <div class="crud-toolbar-left">
            <div class="crud-search-wrap" style="width: 240px;">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="custom-search-input" class="crud-search-input" placeholder="Search engineers..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
            </div>
            <select id="custom-status-filter" class="crud-filter-select" onchange="handleStatusFilter(this.value)">
              <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Status</option>
              <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
              <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="crud-toolbar-right">
            <div style="display: flex; align-items: center; gap: 6px;">
              <span style="font-size: 12px; font-weight: 600; color: var(--text-secondary);">Rows:</span>
              <select id="rows-per-page-select" class="crud-filter-select" style="height: 38px; width: 75px; padding: 4px 8px;" onchange="handlePageSizeChange(this.value)">
                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                <option value="20" {{ request('per_page', '20') == '20' ? 'selected' : '' }}>20</option>
                <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
              </select>
            </div>
            <button class="btn-ui-secondary" onclick="exportDataCSV()" style="height: 38px; font-size: 12px;">
              <i class="fa-solid fa-file-arrow-down"></i> Export CSV
            </button>
          </div>
        </div>

        <!-- CRUD Table Container -->
        <div class="pms-crud-table-wrap">
          <table class="pms-crud-table">
            <thead>
              <tr>
                <th style="width: 50px;">#</th>
                <th>Engineer Name</th>
                <th>Department</th>
                <th>Phone Number</th>
                <th>Status</th>
                <th>Created At</th>
                <th style="text-align: right; width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody id="engineers-tbody">
              <!-- Rendered via JavaScript -->
            </tbody>
          </table>

          <!-- Table Pagination Footer -->
          <div class="table-pagination-footer">
            <div id="pagination-info" style="font-size: 12px; color: var(--text-secondary);">
              Showing 1 to 20 of {{ count($items) }} records
            </div>
            <div class="pagination-controls-group" id="pagination-controls">
              <!-- Controls dynamically rendered -->
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</main>

<!-- MODAL 1: Add Maintain Engineer -->
<div class="modal-backdrop" id="add-engineer-modal">
  <div class="modal-window" style="width: 520px; max-width: 95vw;">
    <div class="modal-top">
      <h3 style="margin: 0; font-size: 16px;"><i class="fa-solid fa-plus-circle"></i> Add Maintain Engineer</h3>
      <button class="modal-close" onclick="closeModal('add-engineer-modal')">&times;</button>
    </div>
    <form id="add-engineer-form" onsubmit="handleAddEngineer(event)">
      <div class="modal-content-area" style="padding: 20px 24px;">
        <div style="margin-bottom: 16px;">
          <label style="display:block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-secondary);">
            Engineer Full Name <span style="color: red;">*</span>
          </label>
          <input type="text" id="add-engineer-name" class="crud-search-input" placeholder="e.g. Ramesh Kumar" required style="width:100%;">
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display:block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-secondary);">
            Department / Specialization <span style="color: red;">*</span>
          </label>
          <input type="text" id="add-engineer-dept" class="crud-search-input" placeholder="e.g. Engineering Lead, HVAC, Electrical" required style="width:100%;">
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display:block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-secondary);">
            Phone Number
          </label>
          <input type="text" id="add-engineer-phone" class="crud-search-input" placeholder="e.g. +91 98765 43210" style="width:100%;">
        </div>

        <div>
          <label style="display:block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-secondary);">
            Status <span style="color: red;">*</span>
          </label>
          <select id="add-engineer-status" class="crud-filter-select" style="width:100%; height:40px;" required>
            <option value="Active" selected>Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-bot" style="padding: 14px 24px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('add-engineer-modal')">Cancel</button>
        <button type="submit" class="btn-ui-primary"><i class="fa-solid fa-check"></i> Register Engineer</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 2: Edit Maintain Engineer -->
<div class="modal-backdrop" id="edit-engineer-modal">
  <div class="modal-window" style="width: 520px; max-width: 95vw;">
    <div class="modal-top">
      <h3 style="margin: 0; font-size: 16px;"><i class="fa-solid fa-pen-to-square"></i> Edit Maintain Engineer</h3>
      <button class="modal-close" onclick="closeModal('edit-engineer-modal')">&times;</button>
    </div>
    <form id="edit-engineer-form" onsubmit="handleSaveEditEngineer(event)">
      <input type="hidden" id="edit-engineer-id">
      <div class="modal-content-area" style="padding: 20px 24px;">
        <div style="margin-bottom: 16px;">
          <label style="display:block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-secondary);">
            Engineer Full Name <span style="color: red;">*</span>
          </label>
          <input type="text" id="edit-engineer-name" class="crud-search-input" required style="width:100%;">
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display:block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-secondary);">
            Department / Specialization <span style="color: red;">*</span>
          </label>
          <input type="text" id="edit-engineer-dept" class="crud-search-input" required style="width:100%;">
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display:block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-secondary);">
            Phone Number
          </label>
          <input type="text" id="edit-engineer-phone" class="crud-search-input" style="width:100%;">
        </div>

        <div>
          <label style="display:block; font-size: 12px; font-weight: 700; margin-bottom: 6px; color: var(--text-secondary);">
            Status <span style="color: red;">*</span>
          </label>
          <select id="edit-engineer-status" class="crud-filter-select" style="width:100%; height:40px;" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-bot" style="padding: 14px 24px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('edit-engineer-modal')">Cancel</button>
        <button type="submit" class="btn-ui-primary"><i class="fa-solid fa-check"></i> Save Changes</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const urlParams = new URLSearchParams(window.location.search);
  let searchTerm = urlParams.get('search') || "{{ request('search') }}" || '';
  let statusFilter = urlParams.get('status') || "{{ request('status', 'all') }}" || 'all';
  let currentPage = parseInt(urlParams.get('page') || '1', 10);
  let pageSize = urlParams.get('per_page') || "{{ request('per_page', '20') }}" || '20';
  let engineersData = @json($items);

  const baseUrl = "{{ route('admin.utilities.roommanage.engineer.index') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function updateUrlParams() {
    const params = new URLSearchParams();
    if (searchTerm) params.set('search', searchTerm);
    if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);
    if (currentPage > 1) params.set('page', currentPage);
    if (pageSize !== '20') params.set('per_page', pageSize);

    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState(null, '', newUrl);
  }

  async function loadData() {
    updateUrlParams();
    try {
      const params = new URLSearchParams();
      if (searchTerm) params.set('search', searchTerm);
      if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);

      const res = await fetch(`${baseUrl}?${params.toString()}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success) {
        engineersData = data.data;
        renderTable();
      }
    } catch (err) {
      console.error(err);
    }
  }

  function getFilteredData() {
    return engineersData.filter(item => {
      const matchSearch = !searchTerm || 
        (item.name && item.name.toLowerCase().includes(searchTerm.toLowerCase())) ||
        (item.department && item.department.toLowerCase().includes(searchTerm.toLowerCase())) ||
        (item.phone && item.phone.toLowerCase().includes(searchTerm.toLowerCase()));
      
      const matchStatus = statusFilter === 'all' || item.status === statusFilter;
      return matchSearch && matchStatus;
    });
  }

  function renderTable() {
    const tbody = document.getElementById('engineers-tbody');
    const filtered = getFilteredData();
    const total = filtered.length;

    let displayItems = [];
    if (pageSize === 'all') {
      displayItems = filtered;
      currentPage = 1;
    } else {
      const size = parseInt(pageSize, 10);
      const totalPages = Math.ceil(total / size) || 1;
      if (currentPage > totalPages) currentPage = totalPages;
      const start = (currentPage - 1) * size;
      displayItems = filtered.slice(start, start + size);
    }

    if (displayItems.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 36px; color: var(--text-muted);"><i class="fa-solid fa-user-slash" style="font-size: 28px; margin-bottom: 8px; display: block;"></i> No maintain engineers found matching your search.</td></tr>`;
    } else {
      tbody.innerHTML = displayItems.map((item, index) => {
        const rowNum = pageSize === 'all' ? (index + 1) : ((currentPage - 1) * parseInt(pageSize, 10) + index + 1);
        const statusClass = item.status === 'Active' ? 'active' : 'inactive';
        const dateStr = item.created_at ? new Date(item.created_at).toLocaleDateString('en-GB') : '-';
        
        return `
          <tr>
            <td style="font-weight: 700; color: var(--text-muted);">${rowNum}</td>
            <td style="font-weight: 800; color: var(--text-primary);">
              <i class="fa-solid fa-user-gear" style="color: var(--accent-primary); margin-right: 6px;"></i> ${escapeHtml(item.name)}
            </td>
            <td>
              <span class="table-badge dept"><i class="fa-solid fa-wrench"></i> ${escapeHtml(item.department)}</span>
            </td>
            <td style="font-family: var(--font-mono); color: var(--text-secondary);">
              ${item.phone ? `<i class="fa-solid fa-phone" style="font-size: 11px; margin-right: 4px; color: var(--accent-emerald);"></i> ${escapeHtml(item.phone)}` : '<span style="color:#cbd5e1;">N/A</span>'}
            </td>
            <td><span class="table-badge ${statusClass}"><i class="fa-solid fa-circle" style="font-size: 6px;"></i> ${item.status}</span></td>
            <td style="font-size: 12px; color: var(--text-muted);">${dateStr}</td>
            <td style="text-align: right;">
              <button class="btn-ui-secondary" onclick="openEditEngineerModal(${item.id})" style="padding: 4px 8px; font-size: 11px; margin-right: 4px;" title="Edit Engineer">
                <i class="fa-solid fa-pen"></i>
              </button>
              <button class="btn-ui-secondary" onclick="handleDeleteEngineer(${item.id}, '${escapeHtml(item.name)}')" style="padding: 4px 8px; font-size: 11px; color: #dc2626;" title="Delete Engineer">
                <i class="fa-solid fa-trash"></i>
              </button>
            </td>
          </tr>
        `;
      }).join('');
    }

    renderPagination(total);
    const countBadge = document.getElementById('header-total-count');
    if (countBadge) countBadge.textContent = `${engineersData.length} records`;
  }

  function renderPagination(total) {
    const info = document.getElementById('pagination-info');
    const controls = document.getElementById('pagination-controls');

    if (pageSize === 'all') {
      info.textContent = `Showing 1 to ${total} of ${total} records`;
      controls.innerHTML = '';
      return;
    }

    const size = parseInt(pageSize, 10);
    const totalPages = Math.ceil(total / size) || 1;
    const startRecord = total === 0 ? 0 : (currentPage - 1) * size + 1;
    const endRecord = Math.min(currentPage * size, total);

    info.textContent = `Showing ${startRecord} to ${endRecord} of ${total} records`;

    if (totalPages <= 1) {
      controls.innerHTML = '';
      return;
    }

    let html = '';
    html += `<button class="pms-page-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left"></i></button>`;

    const getPageNumbers = () => {
      const pages = [];
      if (totalPages <= 7) {
        for (let i = 1; i <= totalPages; i++) pages.push(i);
      } else {
        pages.push(1);
        if (currentPage > 3) pages.push('dots1');
        const start = Math.max(2, currentPage - 1);
        const end = Math.min(totalPages - 1, currentPage + 1);
        for (let i = start; i <= end; i++) {
          if (!pages.includes(i)) pages.push(i);
        }
        if (currentPage < totalPages - 2) pages.push('dots2');
        if (!pages.includes(totalPages)) pages.push(totalPages);
      }
      return pages;
    };

    const pages = getPageNumbers();
    pages.forEach(p => {
      if (p === 'dots1' || p === 'dots2') {
        html += `<span class="pms-page-dots">...</span>`;
      } else {
        html += `<button class="pms-page-btn ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">${p}</button>`;
      }
    });

    html += `<button class="pms-page-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}><i class="fa-solid fa-chevron-right"></i></button>`;
    controls.innerHTML = html;
  }

  function goToPage(p) {
    const size = pageSize === 'all' ? engineersData.length : parseInt(pageSize, 10);
    const totalPages = Math.ceil(getFilteredData().length / size) || 1;
    if (p < 1 || p > totalPages) return;
    currentPage = p;
    updateUrlParams();
    renderTable();
  }

  let searchDebounceTimer = null;
  function handleSearch(val) {
    searchTerm = (val || '').trim();
    currentPage = 1;
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
      loadData();
    }, 250);
  }

  function handleStatusFilter(val) {
    statusFilter = val;
    currentPage = 1;
    loadData();
  }

  function handlePageSizeChange(val) {
    pageSize = val;
    currentPage = 1;
    updateUrlParams();
    renderTable();
  }

  function openAddEngineerModal() {
    document.getElementById('add-engineer-name').value = '';
    document.getElementById('add-engineer-dept').value = '';
    document.getElementById('add-engineer-phone').value = '';
    document.getElementById('add-engineer-status').value = 'Active';
    openModal('add-engineer-modal');
  }

  function openEditEngineerModal(id) {
    const item = engineersData.find(e => e.id === id);
    if (!item) return;

    document.getElementById('edit-engineer-id').value = item.id;
    document.getElementById('edit-engineer-name').value = item.name;
    document.getElementById('edit-engineer-dept').value = item.department;
    document.getElementById('edit-engineer-phone').value = item.phone || '';
    document.getElementById('edit-engineer-status').value = item.status;
    openModal('edit-engineer-modal');
  }

  async function handleAddEngineer(e) {
    e.preventDefault();
    const name = document.getElementById('add-engineer-name').value.trim();
    const department = document.getElementById('add-engineer-dept').value.trim();
    const phone = document.getElementById('add-engineer-phone').value.trim();
    const status = document.getElementById('add-engineer-status').value;

    if (!name || !department) {
      PmsAlert.error('Validation Error', 'Please complete all required fields.');
      return;
    }

    try {
      const res = await fetch(baseUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ name, department, phone, status })
      });
      const data = await res.json();

      if (res.ok && data.success) {
        closeModal('add-engineer-modal');
        PmsAlert.toast(data.message || 'Engineer registered successfully!');
        await loadData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to add engineer.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with server.');
    }
  }

  async function handleSaveEditEngineer(e) {
    e.preventDefault();
    const id = document.getElementById('edit-engineer-id').value;
    const name = document.getElementById('edit-engineer-name').value.trim();
    const department = document.getElementById('edit-engineer-dept').value.trim();
    const phone = document.getElementById('edit-engineer-phone').value.trim();
    const status = document.getElementById('edit-engineer-status').value;

    if (!name || !department) {
      PmsAlert.error('Validation Error', 'Please complete all required fields.');
      return;
    }

    try {
      const res = await fetch(`${baseUrl}/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ name, department, phone, status })
      });
      const data = await res.json();

      if (res.ok && data.success) {
        closeModal('edit-engineer-modal');
        PmsAlert.toast(data.message || 'Engineer updated successfully!');
        await loadData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to update engineer.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with server.');
    }
  }

  async function handleDeleteEngineer(id, name) {
    const result = await PmsAlert.confirmDelete(
      'Remove Engineer?',
      `Are you sure you want to remove maintain engineer "${name}"?`
    );

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
        const data = await res.json();

        if (res.ok && data.success) {
          PmsAlert.toast(data.message || 'Engineer removed successfully!');
          await loadData();
        } else {
          PmsAlert.error('Error', data.message || 'Failed to delete engineer.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'Failed to communicate with server.');
      }
    }
  }

  function exportDataCSV() {
    const filtered = getFilteredData();
    if (filtered.length === 0) {
      PmsAlert.toast('No records available to export.', 'info');
      return;
    }

    let csv = 'ID,Engineer Name,Department,Phone Number,Status,Created At\n';
    filtered.forEach(item => {
      const name = (item.name || '').replace(/"/g, '""');
      const dept = (item.department || '').replace(/"/g, '""');
      const phone = (item.phone || '').replace(/"/g, '""');
      const status = item.status || '';
      const date = item.created_at || '';
      csv += `"${item.id}","${name}","${dept}","${phone}","${status}","${date}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `maintain_engineers_${new Date().toISOString().slice(0,10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    PmsAlert.toast('Engineers CSV export completed successfully!');
  }

  function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  document.addEventListener('DOMContentLoaded', () => {
    renderTable();
  });
</script>
@endpush
