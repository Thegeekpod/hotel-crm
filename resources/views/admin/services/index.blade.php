@extends('admin.layouts.app')

@section('title', 'Hotel Services Master - Hotel Sagar Sonnet PMS')

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
    text-decoration: none;
  }
  .admin-nav-flat:hover { background: #f8fafc; color: var(--accent-primary); }
  .admin-nav-flat.active {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(168, 85, 247, 0.08));
    color: var(--accent-primary);
    font-weight: 900;
    border-color: rgba(99, 102, 241, 0.25);
  }
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
  }

  /* Content Pane */
  .admin-content-pane {
    display: flex;
    flex-direction: column;
    gap: 16px;
    min-width: 0;
  }
  .crud-header-card {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-lg);
    padding: 16px 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
  }

  /* KPI Cards */
  .services-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
  }
  .service-kpi-card {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  }
  .service-kpi-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  /* Toolbar */
  .crud-toolbar {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-lg);
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
  }
  .crud-toolbar-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1 1 450px;
    flex-wrap: wrap;
  }
  .crud-search-wrap {
    position: relative;
    flex: 1 1 200px;
    max-width: 300px;
  }
  .crud-search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 13px;
  }
  .crud-search-input {
    width: 100%;
    height: 38px;
    padding: 0 12px 0 34px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-medium);
    background: #f8fafc;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
    outline: none;
    transition: all 0.2s;
  }
  .crud-search-input:focus {
    background: #ffffff;
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }
  .crud-filter-select {
    height: 38px;
    padding: 0 28px 0 12px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-medium);
    background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 10px center;
    appearance: none;
    -webkit-appearance: none;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-primary);
    cursor: pointer;
    outline: none;
  }
  .crud-toolbar-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  /* Table */
  .crud-table-wrap {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-lg);
    overflow-x: auto;
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
  }
  .crud-table {
    width: 100%;
    min-width: 850px;
    border-collapse: collapse;
  }
  .crud-table th,
  .crud-table td {
    padding: 12px 14px;
    vertical-align: middle;
  }
  .crud-table th {
    background: #f8fafc;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border-medium);
  }
  .crud-table tbody tr {
    border-bottom: 1px solid var(--border-light);
    transition: background 0.15s;
  }
  .crud-table tbody tr:hover {
    background: #f8fafc;
  }

  .badge-category {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
  }

  /* Status Badges with Clickable Toggle */
  .badge-toggle-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
  }
  .badge-toggle-status.active {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
  }
  .badge-toggle-status.active:hover {
    background: #d1fae5;
    transform: scale(1.04);
  }
  .badge-toggle-status.inactive {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
  }
  .badge-toggle-status.inactive:hover {
    background: #ffe4e6;
    transform: scale(1.04);
  }

  /* Action Buttons */
  .crud-actions {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    justify-content: flex-end;
  }
  .btn-action-edit,
  .btn-action-del {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-action-edit {
    background: #eef2ff;
    color: #4f46e5;
    border: 1px solid #c7d2fe;
  }
  .btn-action-edit:hover {
    background: #4f46e5;
    color: #ffffff;
    border-color: #4f46e5;
    transform: translateY(-1px);
  }
  .btn-action-del {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
  }
  .btn-action-del:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    transform: translateY(-1px);
  }

  /* Modal styling */
  .modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    padding: 20px;
    box-sizing: border-box;
  }
  .modal-backdrop.open {
    display: flex !important;
  }
  .modal-box-card {
    background: #ffffff;
    border-radius: 16px;
    width: 600px;
    max-width: 95vw;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.35);
  }
  .modal-box-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    padding: 16px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #ffffff;
  }
  .modal-box-body {
    padding: 20px 22px;
    overflow-y: auto;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #ffffff;
  }
  .modal-box-footer {
    padding: 14px 22px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }
  .admin-form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }
  .admin-form-label {
    font-size: 11px;
    font-weight: 800;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.4px;
  }
  .admin-form-input,
  .admin-form-select,
  .admin-form-textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
    outline: none;
    transition: all 0.2s;
  }
  .admin-form-input:focus,
  .admin-form-select:focus,
  .admin-form-textarea:focus {
    background: #ffffff;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }
</style>
@endpush

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">

    <!-- Page Header -->
    <div class="pms-page-header">
      <div class="pms-header-title-block">
        <div class="pms-status-indicator"></div>
        <div>
          <h1 class="pms-page-title">Administrator • Hotel Services Master</h1>
          <div class="pms-page-subtitle">Configure chargeable &amp; complimentary hotel guest services, laundry, dining, and pricing units.</div>
        </div>
      </div>
      <div style="display: flex; gap: 10px;">
        <button class="btn-ui-secondary" onclick="exportDataCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
      </div>
    </div>

    <div class="admin-layout">
      <!-- Admin Sidebar Navigation -->
      @include('admin.includes.sidebar')

      <!-- Content Pane -->
      <section class="admin-content-pane">

        <!-- KPI Stats Cards -->
        <div class="services-stats-row">
          <div class="service-kpi-card">
            <div class="service-kpi-icon" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5;">
              <i class="fa-solid fa-bell-concierge"></i>
            </div>
            <div>
              <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase;">Total Services</div>
              <div style="font-size: 18px; font-weight: 800; color: #0f172a;" id="kpi-total">{{ $stats['total'] ?? count($items) }}</div>
            </div>
          </div>

          <div class="service-kpi-card">
            <div class="service-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
              <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
              <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase;">Active Services</div>
              <div style="font-size: 18px; font-weight: 800; color: #059669;" id="kpi-active">{{ $stats['active'] ?? 0 }}</div>
            </div>
          </div>

          <div class="service-kpi-card">
            <div class="service-kpi-icon" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
              <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
              <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase;">Inactive Services</div>
              <div style="font-size: 18px; font-weight: 800; color: #dc2626;" id="kpi-inactive">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
          </div>

          <div class="service-kpi-card">
            <div class="service-kpi-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
              <i class="fa-solid fa-tags"></i>
            </div>
            <div>
              <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase;">Categories</div>
              <div style="font-size: 18px; font-weight: 800; color: #0f172a;" id="kpi-categories">{{ $stats['categories'] ?? 0 }}</div>
            </div>
          </div>
        </div>

        <!-- Main CRUD Container -->
        <div id="crud-view-container" style="display: flex; flex-direction: column; gap: 14px;">

          <!-- Header Card -->
          <div class="crud-header-card">
            <div>
              <div style="display: flex; align-items: center; gap: 8px;">
                <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">
                  <i class="fa-solid fa-bell-concierge" style="color: #6366f1;"></i> Hotel Services &amp; Facilities Directory
                </h2>
                <span class="badge-tag green" id="view-count">{{ count($items) }} Records</span>
              </div>
              <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px;">
                Manage in-room dining dishes, laundry tariffs, extra amenities, transport and room add-ons.
              </div>
            </div>
            <button class="btn-ui-primary" onclick="openAddModal()" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; font-weight: 800;">
              <i class="fa-solid fa-plus"></i> Add New Service
            </button>
          </div>

          <!-- Toolbar -->
          <div class="crud-toolbar">
            <div class="crud-toolbar-left">
              <div class="crud-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="table-search" class="crud-search-input" placeholder="Search service name, code, category..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
              </div>

              <!-- Category Filter -->
              <select id="category-filter" class="crud-filter-select" onchange="handleCategoryFilter(this.value)">
                <option value="all">All Categories</option>
                @foreach($allCategories ?? [] as $cat)
                  <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
              </select>

              <!-- Status Filter -->
              <select id="status-filter" class="crud-filter-select" onchange="handleStatusFilter(this.value)">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>

            <div class="crud-toolbar-right">
              <button class="btn-ui-danger" id="btn-bulk-delete" style="display: none; height: 38px; padding: 0 16px; border-radius: var(--radius-md); font-weight: 700; background: #ef4444; color: #fff; border: none; align-items: center; gap: 6px; cursor: pointer;" onclick="handleBulkDelete()">
                <i class="fa-solid fa-trash-can"></i> Delete Selected (<span id="bulk-selected-count">0</span>)
              </button>
              <button class="btn-ui-secondary" onclick="exportDataCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
              <button class="btn-ui-secondary" onclick="location.reload()"><i class="fa-solid fa-rotate"></i> Refresh</button>
            </div>
          </div>

          <!-- Data Table -->
          <div class="crud-table-wrap">
            <table class="crud-table" id="crud-table">
              <thead>
                <tr>
                  <th style="width: 40px; text-align: center;">
                    <input type="checkbox" id="select-all-check" onchange="toggleSelectAll(this)" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
                  </th>
                  <th style="width: 45px;">#</th>
                  <th style="min-width: 220px;">Service Name &amp; Code</th>
                  <th style="min-width: 140px;">Category</th>
                  <th style="min-width: 110px; text-align: right;">Rate (₹)</th>
                  <th style="min-width: 110px;">Billing Unit</th>
                  <th style="min-width: 180px;">Description</th>
                  <th style="min-width: 100px; text-align: center;">Status</th>
                  <th style="text-align: right; width: 100px;">Actions</th>
                </tr>
              </thead>
              <tbody id="table-body">
                @forelse($items as $idx => $item)
                  <tr data-id="{{ $item->id }}" id="row-service-{{ $item->id }}">
                    <td style="text-align: center;">
                      <input type="checkbox" class="row-select-check" value="{{ $item->id }}" onchange="handleRowSelect(this, '{{ $item->id }}')" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
                    </td>
                    <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">
                      {{ $idx + 1 }}
                    </td>
                    <td>
                      <div style="font-weight: 800; color: var(--text-primary); font-size: 13px;">{{ $item->name }}</div>
                      <div style="font-size: 10px; color: #64748b; font-family: var(--font-mono); margin-top: 2px;">
                        {{ $item->code ?: 'SRV-' . sprintf('%04d', $item->id) }}
                      </div>
                    </td>
                    <td>
                      <span class="badge-category">
                        <i class="fa-solid fa-tag" style="font-size: 8px;"></i>
                        {{ $item->category ?: 'General' }}
                      </span>
                    </td>
                    <td style="text-align: right; font-family: var(--font-mono); font-weight: 800; color: #0f172a; font-size: 13px;">
                      @if((float)$item->price <= 0)
                        <span style="color: #059669; font-weight: 800; font-size: 11px;">Complimentary</span>
                      @else
                        ₹ {{ number_format($item->price, 2) }}
                      @endif
                    </td>
                    <td>
                      <span style="font-size: 11px; font-weight: 700; color: #475569; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">
                        {{ $item->unit ?: 'Per Order' }}
                      </span>
                    </td>
                    <td style="font-size: 11px; color: #64748b;">
                      {{ Str::limit($item->description, 50) ?: '-' }}
                    </td>
                    <td style="text-align: center;">
                      <span class="badge-toggle-status {{ $item->status === 'Active' ? 'active' : 'inactive' }}" 
                            onclick="toggleServiceStatus({{ $item->id }})" 
                            title="Click to toggle Active / Inactive status">
                        <i class="fa-solid {{ $item->status === 'Active' ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="font-size: 8px;"></i>
                        <span>{{ $item->status }}</span>
                      </span>
                    </td>
                    <td style="text-align: right;">
                      <div class="crud-actions">
                        <button type="button" class="btn-action-edit" onclick="openEditModal({{ $item->id }})" title="Edit Service">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                        <button type="button" class="btn-action-del" onclick="deleteItem({{ $item->id }})" title="Delete Service">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                      <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                      No hotel services found matching your filters. Click "+ Add New Service" to create one.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar -->
          <div class="pms-pagination-bar" id="table-pagination-bar" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; border-top: 1px solid var(--border-medium); background: #ffffff; border-radius: var(--radius-lg); flex-wrap: wrap; gap: 10px;">
            <div class="pms-pagination-info" id="pagination-info" style="font-size: 12px; font-weight: 600; color: #64748b;">
              Showing 1 to {{ count($items) }} of {{ count($items) }} records
            </div>
            <div class="pms-pagination-controls" style="display: flex; align-items: center; gap: 12px;">
              <div class="pms-per-page-wrap" style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #64748b;">
                <span>Rows:</span>
                <select id="rows-per-page" class="pms-per-page-select" onchange="handlePageSizeChange(this.value)" style="height: 32px; border-radius: 6px; border: 1px solid #cbd5e1; font-weight: 700; padding: 0 8px;">
                  <option value="10" selected>10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="all">All</option>
                </select>
              </div>
              <div class="pms-page-nav" id="pagination-nav" style="display: flex; gap: 4px;">
                <!-- Populated via JS -->
              </div>
            </div>
          </div>

        </div>
      </section>
    </div>

  </div>
</main>

<!-- ADD / EDIT SERVICE MODAL -->
<div class="modal-backdrop" id="crud-modal">
  <div class="modal-box-card">
    
    <!-- Modal Header -->
    <div class="modal-box-header">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 16px;">
          <i class="fa-solid fa-bell-concierge"></i>
        </div>
        <div>
          <h3 style="margin: 0; font-size: 16px; font-weight: 800;" id="modal-title">Add Hotel Service</h3>
          <div style="font-size: 10px; opacity: 0.9;">Configure chargeable or complimentary hotel guest service</div>
        </div>
      </div>
      <button type="button" onclick="closeModal('crud-modal')" style="background: rgba(255,255,255,0.2); border: none; color: #ffffff; width: 28px; height: 28px; border-radius: 50%; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
    </div>

    <!-- Modal Form -->
    <form id="crud-form" onsubmit="handleFormSubmit(event)" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
      <input type="hidden" id="item-id" value="">

      <div class="modal-box-body">

        <!-- Row 1: Name and Code -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
          <div class="admin-form-group">
            <label class="admin-form-label" for="form-name">Service Name <span style="color: #ef4444;">*</span></label>
            <input type="text" id="form-name" class="admin-form-input" placeholder="e.g. Continental Breakfast Tray" required>
          </div>

          <div class="admin-form-group">
            <label class="admin-form-label" for="form-code">Service Code</label>
            <input type="text" id="form-code" class="admin-form-input" placeholder="e.g. RS-01" style="font-family: var(--font-mono);">
          </div>
        </div>

        <!-- Row 2: Category and Charge Unit -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="admin-form-group">
            <label class="admin-form-label" for="form-category">Category <span style="color: #ef4444;">*</span></label>
            <input type="text" id="form-category" list="category-datalist" class="admin-form-input" placeholder="e.g. Room Dining" required>
            <datalist id="category-datalist">
              <option value="Room Dining">
              <option value="Laundry & Dry Clean">
              <option value="Concierge & Transport">
              <option value="Room Addon">
              <option value="Spa & Wellness">
              <option value="Banquet Addon">
              <option value="Housekeeping Amenity">
            </datalist>
          </div>

          <div class="admin-form-group">
            <label class="admin-form-label" for="form-unit">Pricing Unit / Serving <span style="color: #ef4444;">*</span></label>
            <select id="form-unit" class="admin-form-select" required>
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
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="admin-form-group">
            <label class="admin-form-label" for="form-price">Rate / Price (₹) <span style="color: #ef4444;">*</span></label>
            <input type="number" step="0.01" min="0" id="form-price" class="admin-form-input" placeholder="0.00" required style="font-family: var(--font-mono); font-weight: 700;">
          </div>

          <div class="admin-form-group">
            <label class="admin-form-label" for="form-status">Status <span style="color: #ef4444;">*</span></label>
            <select id="form-status" class="admin-form-select" required>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>
        </div>

        <!-- Row 4: Description -->
        <div class="admin-form-group">
          <label class="admin-form-label" for="form-desc">Description / Menu Details</label>
          <textarea id="form-desc" class="admin-form-textarea" rows="3" placeholder="Brief service description, ingredients, delivery guidelines or timing for front desk staff..."></textarea>
        </div>

      </div>

      <!-- Modal Footer -->
      <div class="modal-box-footer">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('crud-modal')" style="height: 38px; padding: 0 18px; border-radius: 8px;">Cancel</button>
        <button type="submit" class="btn-ui-primary" id="btn-save" style="height: 38px; padding: 0 22px; border-radius: 8px; background: linear-gradient(135deg, #4f46e5, #7c3aed); font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-floppy-disk"></i> Save Service
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

  // Live filter & search
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

    // Recalculate KPIs
    document.getElementById('kpi-total').textContent = tableRecords.length;
    document.getElementById('kpi-active').textContent = tableRecords.filter(r => r.status === 'Active').length;
    document.getElementById('kpi-inactive').textContent = tableRecords.filter(r => r.status === 'Inactive').length;
    const cats = [...new Set(tableRecords.map(r => r.category).filter(Boolean))];
    document.getElementById('kpi-categories').textContent = cats.length;

    if (filtered.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
            <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
            No services found matching your filters. Click "+ Add New Service" to create one.
          </td>
        </tr>
      `;
      document.getElementById('pagination-info').textContent = 'Showing 0 records';
      document.getElementById('pagination-nav').innerHTML = '';
      updateBulkActionUI();
      return;
    }

    // Pagination slice
    let displayList = filtered;
    let totalPages = 1;
    if (pageSize !== 'all') {
      totalPages = Math.ceil(filtered.length / pageSize) || 1;
      if (currentPage > totalPages) currentPage = totalPages;
      const startIdx = (currentPage - 1) * pageSize;
      displayList = filtered.slice(startIdx, startIdx + pageSize);
      const endIdx = Math.min(startIdx + pageSize, filtered.length);
      document.getElementById('pagination-info').textContent = `Showing ${startIdx + 1} to ${endIdx} of ${filtered.length} records`;
    } else {
      document.getElementById('pagination-info').textContent = `Showing all ${filtered.length} records`;
    }

    tbody.innerHTML = displayList.map((item, idx) => {
      const globalIdx = pageSize === 'all' ? (idx + 1) : ((currentPage - 1) * pageSize + idx + 1);
      const isChecked = selectedIds.has(String(item.id));

      const priceVal = parseFloat(item.price || 0);
      const priceHtml = priceVal <= 0
        ? `<span style="color: #059669; font-weight: 800; font-size: 11px;">Complimentary</span>`
        : `₹ ${priceVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

      return `
        <tr data-id="${item.id}" id="row-service-${item.id}">
          <td style="text-align: center;">
            <input type="checkbox" class="row-select-check" value="${item.id}" ${isChecked ? 'checked' : ''} onchange="handleRowSelect(this, '${item.id}')" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
          </td>
          <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">
            ${globalIdx}
          </td>
          <td>
            <div style="font-weight: 800; color: var(--text-primary); font-size: 13px;">${escapeHtml(item.name)}</div>
            <div style="font-size: 10px; color: #64748b; font-family: var(--font-mono); margin-top: 2px;">
              ${escapeHtml(item.code || ('SRV-' + String(item.id).padStart(4, '0')))}
            </div>
          </td>
          <td>
            <span class="badge-category">
              <i class="fa-solid fa-tag" style="font-size: 8px;"></i>
              ${escapeHtml(item.category || 'General')}
            </span>
          </td>
          <td style="text-align: right; font-family: var(--font-mono); font-weight: 800; color: #0f172a; font-size: 13px;">
            ${priceHtml}
          </td>
          <td>
            <span style="font-size: 11px; font-weight: 700; color: #475569; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">
              ${escapeHtml(item.unit || 'Per Order')}
            </span>
          </td>
          <td style="font-size: 11px; color: #64748b;">
            ${escapeHtml(item.description ? item.description.substring(0, 50) : '-')}
          </td>
          <td style="text-align: center;">
            <span class="badge-toggle-status ${item.status === 'Active' ? 'active' : 'inactive'}" 
                  onclick="toggleServiceStatus(${item.id})" 
                  title="Click to toggle Active / Inactive status">
              <i class="fa-solid ${item.status === 'Active' ? 'fa-circle-check' : 'fa-circle-xmark'}" style="font-size: 8px;"></i>
              <span>${item.status}</span>
            </span>
          </td>
          <td style="text-align: right;">
            <div class="crud-actions">
              <button type="button" class="btn-action-edit" onclick="openEditModal(${item.id})" title="Edit Service">
                <i class="fa-solid fa-pen"></i>
              </button>
              <button type="button" class="btn-action-del" onclick="deleteItem(${item.id})" title="Delete Service">
                <i class="fa-solid fa-trash"></i>
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
    btns += `<button class="btn-ui-secondary" style="height: 30px; width: 30px; padding: 0; font-size: 11px;" ${currentPage === 1 ? 'disabled style="opacity: 0.4;"' : `onclick="goToPage(${currentPage - 1})"`}><i class="fa-solid fa-chevron-left"></i></button>`;

    for (let p = 1; p <= totalPages; p++) {
      if (p === 1 || p === totalPages || (p >= currentPage - 1 && p <= currentPage + 1)) {
        btns += `<button class="${p === currentPage ? 'btn-ui-primary' : 'btn-ui-secondary'}" style="height: 30px; min-width: 30px; padding: 0 6px; font-size: 11px; font-weight: 800;" onclick="goToPage(${p})">${p}</button>`;
      } else if (p === currentPage - 2 || p === currentPage + 2) {
        btns += `<span style="padding: 0 4px; color: #94a3b8;">...</span>`;
      }
    }

    btns += `<button class="btn-ui-secondary" style="height: 30px; width: 30px; padding: 0; font-size: 11px;" ${currentPage === totalPages ? 'disabled style="opacity: 0.4;"' : `onclick="goToPage(${currentPage + 1})"`}><i class="fa-solid fa-chevron-right"></i></button>`;
    nav.innerHTML = btns;
  }

  // Modal Open/Close
  function openAddModal() {
    document.getElementById('modal-title').textContent = 'Add Hotel Service';
    document.getElementById('item-id').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-code').value = '';
    document.getElementById('form-category').value = 'Room Dining';
    document.getElementById('form-price').value = '0.00';
    document.getElementById('form-unit').value = 'Per Order';
    document.getElementById('form-status').value = 'Active';
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
    document.getElementById('form-category').value = item.category || 'Room Dining';
    document.getElementById('form-price').value = parseFloat(item.price || 0).toFixed(2);
    document.getElementById('form-unit').value = item.unit || 'Per Order';
    document.getElementById('form-status').value = item.status || 'Active';
    document.getElementById('form-desc').value = item.description || '';

    openModal('crud-modal');
  }

  function openModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.remove('open');
      document.body.style.overflow = '';
    }
  }

  // Form Submit (Store & Update)
  async function handleFormSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('item-id').value;
    const name = document.getElementById('form-name').value.trim();
    const code = document.getElementById('form-code').value.trim();
    const category = document.getElementById('form-category').value.trim();
    const price = parseFloat(document.getElementById('form-price').value || 0);
    const unit = document.getElementById('form-unit').value;
    const status = document.getElementById('form-status').value;
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

  // Toggle status
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

  // Delete single item
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

  // Bulk Delete
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
      btn.style.display = 'inline-flex';
      countSpan.textContent = selectedIds.size;
    } else {
      btn.style.display = 'none';
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

  // CSV Export
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

  // Initial table render
  document.addEventListener('DOMContentLoaded', () => {
    renderTable();
  });
</script>
@endpush
