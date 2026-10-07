@extends('admin.layouts.app')

@section('title', 'Amenities Master - Hotel Sagar Sonnet PMS')

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

  /* Custom Inline Searchable FontAwesome Picker UI */
  .fa-picker-card {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    overflow: hidden;
    transition: all 0.2s ease;
  }
  .fa-picker-trigger {
    padding: 10px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    background: #ffffff;
    user-select: none;
  }
  .fa-picker-trigger:hover {
    background: #f8fafc;
  }
  .fa-picker-active-item {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .fa-picker-icon-badge {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(168, 85, 247, 0.12));
    border: 1px solid rgba(99, 102, 241, 0.2);
    color: var(--accent-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }
  .fa-picker-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-primary);
  }
  .fa-picker-subtitle {
    font-size: 11px;
    color: var(--text-muted);
    font-family: var(--font-mono);
  }
  .fa-picker-btn {
    font-size: 11px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 6px;
    background: #f1f5f9;
    color: var(--accent-primary);
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .fa-picker-drawer {
    border-top: 1px solid var(--border-medium);
    background: #f8fafc;
    padding: 12px;
    display: block;
  }
  .fa-picker-drawer.collapsed {
    display: none;
  }
  .fa-picker-search-bar {
    position: relative;
    margin-bottom: 10px;
  }
  .fa-picker-search-bar i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 12px;
  }
  .fa-picker-search-input {
    width: 100%;
    height: 36px;
    padding: 6px 12px 6px 32px;
    font-size: 12px;
    border: 1px solid var(--border-medium);
    border-radius: 8px;
    background: #ffffff;
    color: var(--text-primary);
    outline: none;
    box-sizing: border-box;
  }
  .fa-picker-search-input:focus {
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
  }
  .fa-picker-chips {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 8px;
  }
  .fa-chip-btn {
    padding: 4px 8px;
    font-size: 10px;
    font-weight: 700;
    border-radius: 12px;
    border: 1px solid var(--border-medium);
    background: #ffffff;
    color: var(--text-secondary);
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s;
  }
  .fa-chip-btn:hover, .fa-chip-btn.active {
    background: var(--accent-primary);
    color: #ffffff;
    border-color: var(--accent-primary);
  }
  .fa-picker-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
    max-height: 180px;
    overflow-y: auto;
    padding-right: 4px;
  }
  .fa-picker-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 8px;
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .fa-picker-option:hover {
    border-color: var(--accent-primary);
    background: rgba(99, 102, 241, 0.04);
  }
  .fa-picker-option.selected {
    border-color: var(--accent-primary);
    background: rgba(99, 102, 241, 0.08);
  }
  .fa-option-mini-icon {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background: #f1f5f9;
    color: var(--accent-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
  }
  .fa-picker-option.selected .fa-option-mini-icon, .fa-picker-option:hover .fa-option-mini-icon {
    background: var(--accent-primary);
    color: #ffffff;
  }
  .fa-option-text {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--text-primary);
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
                <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">Amenities Master</h2>
                <span class="badge-tag green" id="view-count">{{ count($items) }} Records</span>
              </div>
              <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px;">
                Configure available room amenities, features, and searchable FontAwesome icons.
              </div>
            </div>
            <button class="btn-ui-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Entry</button>
          </div>

          <div class="crud-toolbar">
            <div class="crud-toolbar-left">
              <div class="crud-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="table-search" class="crud-search-input" placeholder="Search amenities & icons..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
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
                  <th style="width: 90px;">Icon</th>
                  <th>Facility / Amenity Name</th>
                  <th>Icon Class</th>
                  <th style="width: 120px;">Status</th>
                  <th style="text-align: right; width: 120px;">Actions</th>
                </tr>
              </thead>
              <tbody id="table-body">
                @forelse($items as $idx => $item)
                @php
                  $iconClass = $item->icon;
                  if (!str_contains($iconClass, 'fa-solid') && !str_contains($iconClass, 'fa-regular') && !str_contains($iconClass, 'fa-brands')) {
                    $iconClass = 'fa-solid ' . $iconClass;
                  }
                @endphp
                <tr data-id="{{ $item->id }}">
                  <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">{{ $idx + 1 }}</td>
                  <td>
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); display: flex; align-items: center; justify-content: center; font-size: 15px;">
                      <i class="{{ $iconClass }}"></i>
                    </div>
                  </td>
                  <td style="font-weight: 700; color: var(--text-primary);">
                    {{ $item->name }}
                  </td>
                  <td>
                    <code style="font-size: 11px; padding: 3px 6px; background: #f1f5f9; border-radius: 4px; color: var(--accent-indigo); font-weight: 600;">
                      {{ $item->icon }}
                    </code>
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
                  <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
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
  <div class="modal-window" style="width: 560px; max-width: 95vw; max-height: 90vh; display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.25);">
    <div class="modal-top" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: none;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 8px; color: #fff;">
        <i class="fa-solid fa-wand-magic-sparkles"></i> <span id="modal-title">Add Amenity</span>
      </h3>
      <button class="modal-close" onclick="closeModal('crud-modal')" style="background: rgba(255,255,255,0.2); color: #fff; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border: none; font-size: 16px; cursor: pointer;">&times;</button>
    </div>

    <form id="crud-form" onsubmit="handleFormSubmit(event)" style="display: flex; flex-direction: column; flex: 1; min-height: 0;">
      <input type="hidden" id="item-id">
      <input type="hidden" id="form-icon" value="fa-solid fa-wifi">
      
      <div class="modal-content-area" style="padding: 22px; background: #f8fafc; overflow-y: auto; flex: 1;">
        
        <!-- Searchable FontAwesome Icon Picker -->
        <div class="admin-form-group">
          <label class="admin-form-label">FontAwesome Icon Class <span style="color: var(--accent-rose);">*</span></label>
          <div class="fa-picker-card">
            <div class="fa-picker-trigger" onclick="toggleFaDrawer()">
              <div class="fa-picker-active-item">
                <div class="fa-picker-icon-badge" id="fa-trigger-icon-box">
                  <i class="fa-solid fa-wifi" id="fa-trigger-icon-el"></i>
                </div>
                <div>
                  <div class="fa-picker-title" id="fa-trigger-name">Free Wi-Fi / High-Speed Internet</div>
                  <div class="fa-picker-subtitle" id="fa-trigger-class">fa-solid fa-wifi</div>
                </div>
              </div>
              <div class="fa-picker-btn">
                <span id="fa-toggle-text">Change Icon</span>
                <i class="fa-solid fa-chevron-down" id="fa-toggle-icon" style="font-size: 10px;"></i>
              </div>
            </div>

            <div class="fa-picker-drawer" id="fa-picker-drawer">
              <div class="fa-picker-search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="fa-search-input" class="fa-picker-search-input" placeholder="Search icons (e.g. wifi, tv, ac, pool, coffee, bath, key)..." oninput="filterFaIcons(this.value)">
              </div>
              
              <div class="fa-picker-chips" id="fa-category-chips">
                <button type="button" class="fa-chip-btn active" onclick="filterFaCategory('all', this)">All</button>
                <button type="button" class="fa-chip-btn" onclick="filterFaCategory('Connectivity', this)">Connectivity</button>
                <button type="button" class="fa-chip-btn" onclick="filterFaCategory('Technology', this)">Tech</button>
                <button type="button" class="fa-chip-btn" onclick="filterFaCategory('Climate', this)">Climate</button>
                <button type="button" class="fa-chip-btn" onclick="filterFaCategory('Bathroom', this)">Bathroom</button>
                <button type="button" class="fa-chip-btn" onclick="filterFaCategory('Wellness', this)">Wellness</button>
                <button type="button" class="fa-chip-btn" onclick="filterFaCategory('Food & Drink', this)">Dining</button>
                <button type="button" class="fa-chip-btn" onclick="filterFaCategory('Views', this)">Views</button>
                <button type="button" class="fa-chip-btn" onclick="filterFaCategory('Security', this)">Security</button>
                <button type="button" class="fa-chip-btn" onclick="filterFaCategory('Services', this)">Services</button>
              </div>

              <div class="fa-picker-grid" id="fa-options-grid">
                <!-- Populated dynamically via JS -->
              </div>
            </div>
          </div>
        </div>

        <div class="admin-form-group">
          <label class="admin-form-label">Facility / Amenity Name <span style="color: var(--accent-rose);">*</span></label>
          <input type="text" id="form-name" class="admin-form-input" placeholder="e.g. Free Wi-Fi, Balcony, Safe Locker" required>
        </div>

        <div class="admin-form-group" style="margin-bottom: 0;">
          <label class="admin-form-label">Status <span style="color: var(--accent-rose);">*</span></label>
          <select id="form-status" class="admin-form-input" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>

      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('crud-modal')" style="height: 38px; padding: 0 18px; border-radius: 8px;">Cancel</button>
        <button type="submit" class="btn-ui-primary" id="btn-save" style="height: 38px; padding: 0 20px; border-radius: 8px; background: linear-gradient(135deg, #6366f1, #8b5cf6); font-weight: 700;">
          <i class="fa-solid fa-floppy-disk"></i> Save Entry
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/fontawesome-icons.js') }}"></script>
<script>
  const urlParams = new URLSearchParams(window.location.search);
  let searchQuery = urlParams.get('search') || "{{ request('search') }}" || '';
  let statusFilter = urlParams.get('status') || "{{ request('status', 'all') }}" || 'all';
  let currentPage = parseInt(urlParams.get('page') || '1', 10);
  let pageSize = urlParams.get('per_page') || "{{ request('per_page', '10') }}" || '10';
  let tableRecords = @json($items);

  const baseUrl = "{{ url('admin/utilities/room-management/amenity') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // FontAwesome Icons Data & Logic
  const iconsData = window.FONTAWESOME_ICONS || [];
  let selectedIconClass = 'fa-solid fa-wifi';
  let currentCategory = 'all';
  let currentSearchQuery = '';

  function renderFaGrid() {
    const grid = document.getElementById('fa-options-grid');
    if (!grid) return;

    const query = currentSearchQuery.toLowerCase().trim();
    const filtered = iconsData.filter(item => {
      const matchCat = (currentCategory === 'all') || (item.category === currentCategory);
      if (!matchCat) return false;
      if (!query) return true;
      return item.name.toLowerCase().includes(query) ||
             item.class.toLowerCase().includes(query) ||
             (item.category && item.category.toLowerCase().includes(query));
    });

    if (filtered.length === 0) {
      grid.innerHTML = `<div style="grid-column: span 2; padding: 16px; text-align: center; color: var(--text-muted); font-size: 12px;">No matching icons found.</div>`;
      return;
    }

    grid.innerHTML = filtered.map(item => `
      <div class="fa-picker-option ${item.class === selectedIconClass ? 'selected' : ''}" onclick="selectFaIcon('${item.class}', '${escapeJs(item.name)}')">
        <div class="fa-option-mini-icon">
          <i class="${item.class}"></i>
        </div>
        <div class="fa-option-text" title="${escapeHtml(item.name)} (${item.class})">
          ${escapeHtml(item.name)}
        </div>
      </div>
    `).join('');
  }

  function toggleFaDrawer(force = null) {
    const drawer = document.getElementById('fa-picker-drawer');
    const isHidden = drawer.classList.contains('collapsed');
    const shouldOpen = force !== null ? force : isHidden;

    if (shouldOpen) {
      drawer.classList.remove('collapsed');
      document.getElementById('fa-toggle-text').textContent = 'Hide Picker';
      document.getElementById('fa-toggle-icon').className = 'fa-solid fa-chevron-up';
      document.getElementById('fa-search-input').focus();
    } else {
      drawer.classList.add('collapsed');
      document.getElementById('fa-toggle-text').textContent = 'Change Icon';
      document.getElementById('fa-toggle-icon').className = 'fa-solid fa-chevron-down';
    }
  }

  function filterFaIcons(val) {
    currentSearchQuery = val;
    renderFaGrid();
  }

  function filterFaCategory(cat, btn) {
    currentCategory = cat;
    document.querySelectorAll('.fa-chip-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    renderFaGrid();
  }

  function selectFaIcon(iconClass, iconName) {
    selectedIconClass = iconClass;
    document.getElementById('form-icon').value = iconClass;

    let fullClass = iconClass;
    if (!fullClass.includes('fa-solid') && !fullClass.includes('fa-regular') && !fullClass.includes('fa-brands')) {
      fullClass = 'fa-solid ' + fullClass;
    }

    document.getElementById('fa-trigger-icon-el').className = fullClass;
    document.getElementById('fa-trigger-name').textContent = iconName;
    document.getElementById('fa-trigger-class').textContent = iconClass;

    // Auto-fill Amenity name if currently empty
    const nameInput = document.getElementById('form-name');
    if (!nameInput.value.trim()) {
      // Clean name (remove extra descriptions like "/ High-Speed Internet")
      const cleanName = iconName.split('/')[0].replace(/\(.*?\)/g, '').trim();
      nameInput.value = cleanName;
    }

    renderFaGrid();
  }

  function setDropdownIcon(iconClass) {
    let target = iconClass || 'fa-solid fa-wifi';
    let fullClass = target;
    if (!fullClass.includes('fa-solid') && !fullClass.includes('fa-regular') && !fullClass.includes('fa-brands')) {
      fullClass = 'fa-solid ' + fullClass;
    }

    const found = iconsData.find(i => i.class === target || i.class === fullClass || i.class.endsWith(target));
    const name = found ? found.name : target;
    const finalClass = found ? found.class : fullClass;

    selectedIconClass = finalClass;
    document.getElementById('form-icon').value = finalClass;
    document.getElementById('fa-trigger-icon-el').className = finalClass;
    document.getElementById('fa-trigger-name').textContent = name;
    document.getElementById('fa-trigger-class').textContent = finalClass;
    renderFaGrid();
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
    document.getElementById('modal-title').textContent = 'Add Amenity';
    document.getElementById('item-id').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-status').value = 'Active';
    setDropdownIcon('fa-solid fa-wifi');
    toggleFaDrawer(true);
    openModal('crud-modal');
  }

  function openEditModal(id) {
    const item = tableRecords.find(r => String(r.id) === String(id));
    if (!item) {
      console.warn('Record not found for id:', id);
      return;
    }

    document.getElementById('modal-title').textContent = 'Edit Amenity';
    document.getElementById('item-id').value = item.id;
    document.getElementById('form-name').value = item.name;
    document.getElementById('form-status').value = item.status;
    setDropdownIcon(item.icon);
    toggleFaDrawer(false);
    openModal('crud-modal');
  }

  async function handleFormSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('item-id').value;
    const icon = document.getElementById('form-icon').value.trim();
    const name = document.getElementById('form-name').value.trim();
    const status = document.getElementById('form-status').value;

    if (!name || !icon) {
      PmsAlert.error('Validation Error', 'Please select an icon and enter amenity name.');
      return;
    }

    const payload = { icon, name, status };
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
    const result = await PmsAlert.confirmDelete('Delete Amenity?', 'This amenity will be removed.');
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

  function getFilteredRecords() {
    return tableRecords.filter(item => {
      const matchSearch = !searchQuery ||
        (item.name && item.name.toLowerCase().includes(searchQuery)) ||
        (item.icon && item.icon.toLowerCase().includes(searchQuery)) ||
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
          <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
            No matching amenities found.
          </td>
        </tr>
      `;
      renderPagination(totalRecords, totalPages, 0, 0);
      return;
    }

    const startIndex = pageSize === 'all' ? 0 : (currentPage - 1) * parseInt(pageSize, 10);

    tbody.innerHTML = paginatedRecords.map((item, idx) => {
      let iconClass = item.icon || 'fa-solid fa-wifi';
      if (!iconClass.includes('fa-solid') && !iconClass.includes('fa-regular') && !iconClass.includes('fa-brands')) {
        iconClass = 'fa-solid ' + iconClass;
      }

      return `
        <tr data-id="${item.id}">
          <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">
            ${startIndex + idx + 1}
          </td>
          <td>
            <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); display: flex; align-items: center; justify-content: center; font-size: 15px;">
              <i class="${escapeHtml(iconClass)}"></i>
            </div>
          </td>
          <td style="font-weight: 700; color: var(--text-primary);">
            ${escapeHtml(item.name)}
          </td>
          <td>
            <code style="font-size: 11px; padding: 3px 6px; background: #f1f5f9; border-radius: 4px; color: var(--accent-indigo); font-weight: 600;">
              ${escapeHtml(item.icon)}
            </code>
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

    const headers = ['#', 'Icon Class', 'Amenity Name', 'Status'];
    const rows = records.map((r, i) => [
      i + 1,
      `"${(r.icon || '').replace(/"/g, '""')}"`,
      `"${(r.name || '').replace(/"/g, '""')}"`,
      `"${(r.status || '').replace(/"/g, '""')}"`
    ]);

    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `amenities_${new Date().toISOString().slice(0,10)}.csv`);
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

  function escapeJs(str) {
    if (!str) return '';
    return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
  }

  document.addEventListener('DOMContentLoaded', () => {
    renderFaGrid();
    renderTable();
  });
</script>
@endpush
