@extends('admin.layouts.app')

@section('title', 'Room Management Master - Hotel Sagar Sonnet PMS')

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

  /* Specific styles for Room KPI & Cards */
  .room-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
  }
  @media (max-width: 1024px) {
    .room-stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  @media (max-width: 576px) {
    .room-stats-grid {
      grid-template-columns: 1fr;
    }
  }
  
  .room-kpi-card {
    background: #ffffff;
    border: 1px solid var(--border-medium, #e2e8f0);
    border-radius: var(--radius-lg, 12px);
    padding: 16px 18px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
  }

  .room-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    border-color: var(--accent-primary, #6366f1);
  }

  .room-kpi-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
  }

  .kpi-pills-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    flex-wrap: wrap;
    max-height: 54px;
    overflow-y: auto;
  }
  .kpi-pills-wrap::-webkit-scrollbar {
    width: 3px;
    height: 3px;
  }
  .kpi-pills-wrap::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }

  .control-toolbar {
    background: #ffffff;
    border: 1px solid var(--border-medium, #e2e8f0);
    border-radius: var(--radius-md, 8px);
    padding: 12px 16px;
    margin-bottom: 16px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  }

  .filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .crud-search-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
  }
  .crud-search-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted, #94a3b8);
    font-size: 13px;
    pointer-events: none;
    z-index: 2;
  }
  .crud-search-input {
    height: 38px;
    padding: 8px 14px 8px 34px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid var(--border-medium, #cbd5e1);
    border-radius: var(--radius-md, 8px);
    background: #ffffff;
    color: var(--text-primary, #0f172a);
    transition: all 0.2s ease;
  }
  .crud-search-input:focus {
    outline: none;
    border-color: var(--accent-primary, #6366f1);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
  }

  .toolbar-filter-select {
    height: 38px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid var(--border-medium, #cbd5e1);
    border-radius: var(--radius-md, 8px);
    background: #ffffff;
    color: var(--text-primary, #0f172a);
    outline: none;
    cursor: pointer;
    display: inline-block;
    transition: all 0.2s ease;
  }
  .toolbar-filter-select:focus {
    border-color: var(--accent-primary, #6366f1);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
  }

  .view-toggle-btn {
    background: #f1f5f9;
    border: 1px solid var(--border-medium, #cbd5e1);
    color: var(--text-secondary, #64748b);
    padding: 7px 12px;
    border-radius: var(--radius-md, 8px);
    cursor: pointer;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
  }

  .view-toggle-btn.active {
    background: #ffffff;
    color: var(--accent-primary, #6366f1);
    border-color: var(--accent-primary, #6366f1);
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
  }

  /* Table Card and Scrollable Wrapper */
  .room-table-card {
    background: #ffffff;
    border: 1px solid var(--border-medium, #e2e8f0);
    border-radius: var(--radius-md, 8px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    overflow: hidden;
  }

  .room-table-scroll {
    overflow-x: auto;
  }

  .room-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    background: #ffffff;
  }

  .room-table th {
    background: #f8fafc;
    padding: 12px 14px;
    font-size: 10px;
    font-weight: 800;
    color: var(--text-secondary, #64748b);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 1px solid var(--border-medium, #e2e8f0);
    text-align: left;
  }

  .room-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--border-subtle, #f1f5f9);
    color: var(--text-primary, #0f172a);
    vertical-align: middle;
  }

  .room-table tbody tr:hover {
    background: #f8fafc;
  }

  /* Grid cards layout for rooms */
  .room-inventory-grid {
    display: none;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
  }

  .room-inv-card {
    background: #ffffff;
    border: 1px solid var(--border-medium, #e2e8f0);
    border-radius: var(--radius-lg, 12px);
    padding: 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 12px;
    transition: all 0.25s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    position: relative;
    overflow: hidden;
  }

  .room-inv-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    border-color: var(--accent-primary, #6366f1);
  }

  .room-inv-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
  }

  .room-inv-number {
    font-family: var(--font-mono, monospace);
    font-size: 20px;
    font-weight: 900;
    color: var(--text-primary, #0f172a);
    display: flex;
    align-items: baseline;
    gap: 6px;
  }

  .room-inv-floor {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-muted, #94a3b8);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .room-amenity-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin: 8px 0;
  }

  .amenity-chip {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    font-size: 10px;
    font-weight: 600;
    color: var(--text-secondary, #475569);
    padding: 2px 7px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .room-inv-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1px solid var(--border-subtle, #f1f5f9);
  }

  .room-inv-rate {
    font-family: var(--font-mono, monospace);
    font-weight: 900;
    font-size: 15px;
    color: var(--accent-primary, #6366f1);
  }

  .badge-tag {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1;
    gap: 4px;
  }
  .badge-tag.green { background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25); }
  .badge-tag.red { background: rgba(239, 68, 68, 0.12); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.25); }
  .badge-tag.yellow { background: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.25); }
  .badge-tag.purple { background: rgba(168, 85, 247, 0.12); color: #7c3aed; border: 1px solid rgba(168, 85, 247, 0.25); }
  .badge-tag.blue { background: rgba(59, 130, 246, 0.12); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.25); }

  .amenity-checkbox-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-primary, #1e293b);
    background: #ffffff;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid var(--border-medium, #e2e8f0);
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
  }
  .amenity-checkbox-item:hover {
    border-color: var(--accent-primary, #6366f1);
    background: rgba(99, 102, 241, 0.04);
  }
  .amenity-checkbox-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--accent-primary, #6366f1);
  }

  /* Table Pagination Styles */
  .table-pagination-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px;
    border-top: 1px solid var(--border-medium, #e2e8f0);
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
    border: 1px solid var(--border-medium, #cbd5e1);
    background: #ffffff;
    color: var(--text-secondary, #64748b);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }
  .pms-page-btn:hover:not(:disabled) {
    border-color: var(--accent-primary, #6366f1);
    color: var(--accent-primary, #6366f1);
    background: rgba(99, 102, 241, 0.05);
  }
  .pms-page-btn.active {
    background: var(--accent-primary, #6366f1);
    color: #ffffff;
    border-color: var(--accent-primary, #6366f1);
    box-shadow: 0 2px 6px rgba(99, 102, 241, 0.35);
  }
  .pms-page-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
  }
  .pms-page-dots {
    padding: 0 4px;
    color: var(--text-muted, #94a3b8);
    font-weight: 800;
    font-size: 12px;
  }

  .btn-action-view {
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 6px;
    padding: 6px 9px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    transition: all 0.2s;
  }
  .btn-action-view:hover { background: #2563eb; color: #fff; }

  .btn-action-edit {
    background: rgba(99, 102, 241, 0.1);
    color: var(--accent-primary, #6366f1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 6px;
    padding: 6px 9px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    transition: all 0.2s;
  }
  .btn-action-edit:hover { background: var(--accent-primary, #6366f1); color: #fff; }

  .btn-action-del {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    border: 1px solid rgba(239, 68, 68, 0.2);
    border-radius: 6px;
    padding: 6px 9px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    transition: all 0.2s;
  }
  .btn-action-del:hover { background: #dc2626; color: #fff; }

  .btn-action-maint {
    background: rgba(245, 158, 11, 0.1);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: 6px;
    padding: 6px 9px;
    cursor: pointer;
    font-size: 11px;
    font-weight: 700;
    transition: all 0.2s;
  }
  .btn-action-maint:hover { background: #d97706; color: #fff; }
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
        <button class="btn-ui-secondary" onclick="exportRoomsCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
      </div>
    </div>

    <div class="admin-layout">
      @include('admin.includes.sidebar')

      <section class="admin-content-pane">
        
        <!-- Header Card -->
        <div class="crud-header-card">
          <div>
            <div style="display: flex; align-items: center; gap: 8px;">
              <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">Room Assets & Master</h2>
              <span class="badge-tag purple" id="view-count">{{ count($rooms) }} Records</span>
            </div>
            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px;">
              Manage individual room numbers, bedding setups, pax capacities, tariffs, amenities, and room status.
            </div>
          </div>
          <button class="btn-ui-primary" onclick="openAddRoomModal()"><i class="fa-solid fa-plus-circle"></i> Add Room Asset</button>
        </div>

        <!-- Top 4 KPI Metrics Row (Filtered Dynamic Stats) -->
        <div class="room-stats-grid">
          <!-- Card 1: Total Rooms & Status (Active vs Inactive) -->
          <div class="room-kpi-card">
            <div class="room-kpi-bar" style="background: linear-gradient(90deg, #6366f1, #8b5cf6);"></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <div style="flex: 1; min-width: 0;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.6px;">Room Status & Assets</div>
                <div style="font-family: var(--font-mono); font-size: 26px; font-weight: 900; color: var(--text-primary); margin-top: 4px;" id="stat-total-rooms">{{ $totalRooms }}</div>
               
              </div>
              <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(99, 102, 241, 0.1); display: flex; align-items: center; justify-content: center; color: var(--accent-primary); flex-shrink: 0;">
                <i class="fa-solid fa-door-open" style="font-size: 18px;"></i>
              </div>
            </div>
          </div>

          <!-- Card 2: Category Wise Rooms -->
          <div class="room-kpi-card">
            <div class="room-kpi-bar" style="background: linear-gradient(90deg, #ec4899, #f43f5e);"></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <div style="flex: 1; min-width: 0;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.6px;">Category Wise Rooms</div>
                <div style="font-family: var(--font-mono); font-size: 26px; font-weight: 900; color: #e11d48; margin-top: 4px;">
                  <span id="stat-category-total-rooms">{{ $totalRooms }}</span>
                  <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; font-family: inherit;">Rooms</span>
                </div>
               
              </div>
              <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(244, 63, 94, 0.1); display: flex; align-items: center; justify-content: center; color: #e11d48; flex-shrink: 0;">
                <i class="fa-solid fa-tags" style="font-size: 18px;"></i>
              </div>
            </div>
          </div>

          <!-- Card 3: Floor Wise Rooms -->
          <div class="room-kpi-card">
            <div class="room-kpi-bar" style="background: linear-gradient(90deg, #3b82f6, #06b6d4);"></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <div style="flex: 1; min-width: 0;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.6px;">Floor Wise Rooms</div>
                <div style="font-family: var(--font-mono); font-size: 26px; font-weight: 900; color: #0284c7; margin-top: 4px;">
                  <span id="stat-floor-total-rooms">{{ $totalRooms }}</span>
                  <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; font-family: inherit;">Rooms</span>
                </div>
              </div>
              <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(6, 182, 212, 0.1); display: flex; align-items: center; justify-content: center; color: #0284c7; flex-shrink: 0;">
                <i class="fa-solid fa-layer-group" style="font-size: 18px;"></i>
              </div>
            </div>
          </div>

          <!-- Card 4: Amenities Wise Rooms -->
          <div class="room-kpi-card">
            <div class="room-kpi-bar" style="background: linear-gradient(90deg, #10b981, #14b8a6);"></div>
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <div style="flex: 1; min-width: 0;">
                <div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.6px;">Amenities Wise Rooms</div>
                <div style="font-family: var(--font-mono); font-size: 26px; font-weight: 900; color: #059669; margin-top: 4px;">
                  <span id="stat-amenity-total-rooms">{{ $totalRooms }}</span>
                  <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; font-family: inherit;">Rooms</span>
                </div>
              </div>
              <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: #059669; flex-shrink: 0;">
                <i class="fa-solid fa-wand-magic-sparkles" style="font-size: 18px;"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Control & Filter Toolbar -->
        <div class="control-toolbar">
          <!-- Search Bar & Filters -->
          <div class="filter-group">
            <!-- Search Input -->
            <div class="crud-search-wrap" style="width: 190px;">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="room-search-input" class="crud-search-input" style="width: 100%;" value="{{ request('search') }}" placeholder="Search room no..." oninput="handleSearch(this.value)">
            </div>

            <!-- Category Filter Dropdown -->
            <select id="category-filter" class="toolbar-filter-select" style="width: 140px;" onchange="handleCategoryFilter(this.value)">
              <option value="ALL">All Categories</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>

            <!-- Floor Filter Dropdown -->
            <select id="floor-filter" class="toolbar-filter-select" style="width: 130px;" onchange="handleFloorFilter(this.value)">
              <option value="all">All Floors</option>
              @foreach($floors as $fl)
                <option value="{{ $fl->floor }}" {{ request('floor') == $fl->floor ? 'selected' : '' }}>Floor {{ $fl->floor }}</option>
              @endforeach
            </select>

            <!-- Status Filter (Active / Inactive) -->
            <select id="status-filter" class="toolbar-filter-select" style="width: 130px;" onchange="handleStatusFilter(this.value)">
              <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Status</option>
              <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
              <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <!-- Amenities Filter Dropdown -->
            <select id="amenity-filter" class="toolbar-filter-select" style="width: 150px;" onchange="handleAmenityFilter(this.value)">
              <option value="all" {{ request('amenity', 'all') == 'all' ? 'selected' : '' }}>All Amenities</option>
              @foreach($amenities as $amn)
                <option value="{{ $amn->name }}" {{ request('amenity') == $amn->name ? 'selected' : '' }}>{{ $amn->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- Action Tools -->
          <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <!-- View Toggle -->
            <div style="display: flex; gap: 3px; background: #e2e8f0; padding: 3px; border-radius: var(--radius-md);">
              <button class="view-toggle-btn active" id="btn-view-table" onclick="setViewMode('table')" title="Table View">
                <i class="fa-solid fa-table-list"></i> Table
              </button>
              <button class="view-toggle-btn" id="btn-view-grid" onclick="setViewMode('grid')" title="Cards Grid View">
                <i class="fa-solid fa-border-all"></i> Grid
              </button>
            </div>

            <!-- Page Size Selector -->
            <div style="display: flex; align-items: center; gap: 5px;">
              <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Rows:</span>
              <select id="page-size-select" class="toolbar-filter-select" style="height: 38px; width: 70px; font-size: 12px; padding: 6px 8px;" onchange="handlePageSizeChange(this.value)">
                <option value="10">10</option>
                <option value="20" selected>20</option>
                <option value="100">100</option>
                <option value="all">All</option>
              </select>
            </div>

            <button id="btn-bulk-delete-rooms" class="btn-action-del" style="display: none; height: 38px; padding: 0 12px; font-size: 12px; align-items: center; gap: 6px;" onclick="handleBulkDeleteRooms()">
              <i class="fa-solid fa-trash-can"></i> Delete Selected (<span id="bulk-selected-rooms-count">0</span>)
            </button>

            <button class="btn-ui-secondary" onclick="loadRoomsData()" title="Refresh Data" style="height: 38px;">
              <i class="fa-solid fa-rotate"></i>
            </button>
          </div>
        </div>

        <!-- TABLE VIEW CONTAINER -->
        <div class="room-table-card" id="rooms-table-container">
          <div class="room-table-scroll">
            <table class="room-table">
              <thead>
                <tr>
                  <th style="width: 40px; text-align: center;"><input type="checkbox" id="select-all-rooms-check" onchange="toggleSelectAllRooms(this)" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);"></th>
                  <th style="width: 45px;">#</th>
                  <th>Room</th>
                  <th>Floor</th>
                  <th>Category</th>
                  <th>Bedding & Pax</th>
                  <th>Base Tariff</th>
                  <th>Status</th>
                  <th>Amenities</th>
                  <th style="text-align: right; width: 150px;">Actions</th>
                </tr>
              </thead>
              <tbody id="rooms-tbody">
                <!-- Rendered via JavaScript -->
              </tbody>
            </table>
          </div>

          <!-- Table Pagination Footer -->
          <div class="table-pagination-footer">
            <div id="pagination-info" style="font-size: 12px; color: var(--text-secondary);">
              Showing 1 to 20 of {{ count($rooms) }} records
            </div>
            <div class="pagination-controls-group" id="pagination-nav">
              <!-- Rendered dynamically -->
            </div>
          </div>
        </div>

        <!-- GRID VIEW CONTAINER (Alternative View) -->
        <div id="rooms-grid-container" class="room-inventory-grid">
          <!-- Dynamically rendered cards -->
        </div>

      </section>
    </div>

  </div>
</main>

<!-- MODAL 1: Add New Room Asset -->
<div class="modal-backdrop" id="add-room-modal">
  <div class="modal-window large" style="width: 820px; max-width: 95vw; max-height: 90vh; display: flex; flex-direction: column;">
    <div class="modal-top">
      <h3 style="margin: 0;"><i class="fa-solid fa-plus-circle"></i> Add New Room Asset • Hotel Sagar Sonnet</h3>
      <button class="modal-close" onclick="closeModal('add-room-modal')">&times;</button>
    </div>
    <form id="add-room-form" onsubmit="handleAddRoom(event)" style="display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden;">
      <div class="modal-content-area" style="overflow-y: auto !important; flex: 1 1 auto; min-height: 0; max-height: calc(88vh - 120px); padding: 24px; background: #f8fafc;">
        
        <!-- Section 1: Room Identification & Classification -->
        <div style="display: flex; flex-direction: column; background: #fff; padding: 20px 22px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.02); margin-bottom: 18px;">
          <h4 style="font-size: 12px; color: var(--accent-primary); margin: 0 0 16px 0; text-transform: uppercase; font-weight: 900; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-hotel"></i> Room Asset & Classification
          </h4>
          
          <div style="display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Room Number <span style="color: red;">*</span>
              </label>
              <input type="text" id="new-room-no" class="crud-search-input" placeholder="e.g. 501" required style="width:100%; height:40px; font-family: var(--font-mono); font-weight: 800; font-size: 14px;">
            </div>
            <div style="flex: 1; min-width: 220px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Floor <span style="color: red;">*</span>
              </label>
              <select id="new-room-floor" class="select2-field" style="width:100%;" required>
                <option value="">-- Select Floor --</option>
                @foreach($floors as $fl)
                  <option value="{{ $fl->floor }}">Floor {{ $fl->floor }} ({{ $fl->name }})</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 180px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Room Category <span style="color: red;">*</span>
              </label>
              <select id="new-room-cat" class="select2-field" style="width:100%;" required onchange="handleCategoryChange(this.value)">
                <option value="">-- Select Category --</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}" data-bed="{{ $cat->bedding_config }}" data-pax="{{ $cat->pax_capacity }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div style="flex: 1; min-width: 180px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Base Tariff (₹ / Night) <span style="color: red;">*</span>
              </label>
              <input type="number" id="new-room-rate" class="crud-search-input" value="3500" required style="width:100%; height:40px; font-family: var(--font-mono); font-weight: 800; color: var(--accent-primary);">
            </div>
            <div style="flex: 1; min-width: 180px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Status <span style="color: red;">*</span>
              </label>
              <select id="new-room-status" class="select2-field" style="width:100%;">
                <option value="Active" selected>Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Section 2: Bedding Setup & Capacity -->
        <div style="display: flex; flex-direction: column; background: #fff; padding: 20px 22px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.02); margin-bottom: 18px;">
          <h4 style="font-size: 12px; color: var(--accent-primary); margin: 0 0 16px 0; text-transform: uppercase; font-weight: 900; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-bed"></i> Bedding Setup & Capacity
          </h4>
          
          <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <!-- Column 1: Bedding Configuration -->
            <div style="flex: 1; min-width: 240px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Bedding Configuration <span style="color: red;">*</span>
              </label>
              <select id="new-room-bed" class="select2-field" style="width:100%;">
                @foreach($beddingConfigs as $bed)
                  <option value="{{ $bed->name }}">{{ $bed->name }}</option>
                @endforeach
              </select>
            </div>

            <!-- Column 2: Separate Pax Capacity (Auto Linked) -->
            <div style="flex: 1; min-width: 240px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Pax Capacity <span style="font-size: 10px; color: var(--accent-primary); font-weight: 700; text-transform: none;">(Auto-linked)</span>
              </label>
              <div style="height: 40px; padding: 6px 12px; background: #f8fafc; border: 1px solid var(--border-medium); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span class="badge-tag blue" style="font-size: 11px; font-weight: 700;">
                    <i class="fa-solid fa-user"></i> <span id="new-pax-adults-text">2 Adults</span>
                  </span>
                  <span class="badge-tag yellow" style="font-size: 11px; font-weight: 700;">
                    <i class="fa-solid fa-child"></i> <span id="new-pax-children-text">1 Child</span>
                  </span>
                </div>
                <span class="badge-tag purple" style="font-size: 11px; font-weight: 800;">
                  <i class="fa-solid fa-users"></i> <span id="new-pax-total-text">Total 3 Pax</span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Amenities & Features (Dynamic from Amenities Master) -->
        <div style="display: flex; flex-direction: column; background: #fff; padding: 20px 22px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
          <h4 style="font-size: 12px; color: var(--accent-primary); margin: 0 0 16px 0; text-transform: uppercase; font-weight: 900; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Amenities & Features
          </h4>
          
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;" id="add-amenities-container">
            @foreach($amenities as $amn)
              <label class="amenity-checkbox-item">
                <input type="checkbox" name="add_amenities[]" value="{{ $amn->name }}" {{ in_array($amn->name, ['Free Wi-Fi', 'Balcony', 'Smart 55" TV', 'AC']) ? 'checked' : '' }}>
                <i class="fa-solid {{ $amn->icon ?: 'fa-check' }}" style="color: var(--accent-primary); font-size: 13px; width: 16px;"></i>
                <span>{{ $amn->name }}</span>
              </label>
            @endforeach
          </div>
        </div>

      </div>

      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('add-room-modal')" style="height: 38px; padding: 0 18px;">Cancel</button>
        <button type="submit" class="btn-ui-primary" style="height: 38px; padding: 0 22px;"><i class="fa-solid fa-check"></i> Register Room Asset</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 2: Edit Room Asset -->
<div class="modal-backdrop" id="edit-room-modal">
  <div class="modal-window large" style="width: 820px; max-width: 95vw; max-height: 90vh; display: flex; flex-direction: column;">
    <div class="modal-top">
      <h3 style="margin: 0;"><i class="fa-solid fa-pen-to-square"></i> Edit Room Asset: <span id="edit-modal-room-num" style="font-family: var(--font-mono); font-weight: 900;"></span></h3>
      <button class="modal-close" onclick="closeModal('edit-room-modal')">&times;</button>
    </div>
    <form id="edit-room-form" onsubmit="handleSaveEditRoom(event)" style="display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden;">
      <input type="hidden" id="edit-room-id">
      <div class="modal-content-area" style="overflow-y: auto !important; flex: 1 1 auto; min-height: 0; max-height: calc(88vh - 120px); padding: 24px; background: #f8fafc;">
        
        <!-- Section 1: Room Identification & Classification -->
        <div style="display: flex; flex-direction: column; background: #fff; padding: 20px 22px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.02); margin-bottom: 18px;">
          <h4 style="font-size: 12px; color: var(--accent-primary); margin: 0 0 16px 0; text-transform: uppercase; font-weight: 900; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-hotel"></i> Room Asset & Classification
          </h4>
          
          <div style="display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Room Number <span style="color: red;">*</span>
              </label>
              <input type="text" id="edit-room-no" class="crud-search-input" required style="width:100%; height:40px; font-family: var(--font-mono); font-weight: 800; font-size: 14px;">
            </div>
            <div style="flex: 1; min-width: 220px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Floor <span style="color: red;">*</span>
              </label>
              <select id="edit-room-floor" class="select2-field" style="width:100%;" required>
                @foreach($floors as $fl)
                  <option value="{{ $fl->floor }}">Floor {{ $fl->floor }} ({{ $fl->name }})</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 180px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Room Category <span style="color: red;">*</span>
              </label>
              <select id="edit-room-cat" class="select2-field" style="width:100%;" required>
                @foreach($categories as $cat)
                  <option value="{{ $cat->name }}" data-bed="{{ $cat->bedding_config }}" data-pax="{{ $cat->pax_capacity }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div style="flex: 1; min-width: 180px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Base Tariff (₹ / Night) <span style="color: red;">*</span>
              </label>
              <input type="number" id="edit-room-rate" class="crud-search-input" required style="width:100%; height:40px; font-family: var(--font-mono); font-weight: 800; color: var(--accent-primary);">
            </div>
            <div style="flex: 1; min-width: 180px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Status <span style="color: red;">*</span>
              </label>
              <select id="edit-room-status" class="select2-field" style="width:100%;">
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Section 2: Bedding Setup & Capacity -->
        <div style="display: flex; flex-direction: column; background: #fff; padding: 20px 22px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.02); margin-bottom: 18px;">
          <h4 style="font-size: 12px; color: var(--accent-primary); margin: 0 0 16px 0; text-transform: uppercase; font-weight: 900; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-bed"></i> Bedding Setup & Capacity
          </h4>
          
          <div style="display: flex; gap: 16px; flex-wrap: wrap;">
            <!-- Column 1: Bedding Configuration -->
            <div style="flex: 1; min-width: 240px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Bedding Configuration <span style="color: red;">*</span>
              </label>
              <select id="edit-room-bed" class="select2-field" style="width:100%;">
                @foreach($beddingConfigs as $bed)
                  <option value="{{ $bed->name }}">{{ $bed->name }}</option>
                @endforeach
              </select>
            </div>

            <!-- Column 2: Separate Pax Capacity (Auto Linked) -->
            <div style="flex: 1; min-width: 240px;">
              <label style="display:block; font-size: 11px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.6px;">
                Pax Capacity <span style="font-size: 10px; color: var(--accent-primary); font-weight: 700; text-transform: none;">(Auto-linked)</span>
              </label>
              <div style="height: 40px; padding: 6px 12px; background: #f8fafc; border: 1px solid var(--border-medium); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span class="badge-tag blue" style="font-size: 11px; font-weight: 700;">
                    <i class="fa-solid fa-user"></i> <span id="edit-pax-adults-text">2 Adults</span>
                  </span>
                  <span class="badge-tag yellow" style="font-size: 11px; font-weight: 700;">
                    <i class="fa-solid fa-child"></i> <span id="edit-pax-children-text">1 Child</span>
                  </span>
                </div>
                <span class="badge-tag purple" style="font-size: 11px; font-weight: 800;">
                  <i class="fa-solid fa-users"></i> <span id="edit-pax-total-text">Total 3 Pax</span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Amenities & Features (Dynamic from Amenities Master) -->
        <div style="display: flex; flex-direction: column; background: #fff; padding: 20px 22px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
          <h4 style="font-size: 12px; color: var(--accent-primary); margin: 0 0 16px 0; text-transform: uppercase; font-weight: 900; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Amenities & Features
          </h4>
          
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;" id="edit-amenities-container">
            @foreach($amenities as $amn)
              <label class="amenity-checkbox-item">
                <input type="checkbox" name="edit_amenities[]" value="{{ $amn->name }}">
                <i class="fa-solid {{ $amn->icon ?: 'fa-check' }}" style="color: var(--accent-primary); font-size: 13px; width: 16px;"></i>
                <span>{{ $amn->name }}</span>
              </label>
            @endforeach
          </div>
        </div>

      </div>

      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('edit-room-modal')" style="height: 38px; padding: 0 18px;">Cancel</button>
        <button type="submit" class="btn-ui-primary" style="height: 38px; padding: 0 22px;"><i class="fa-solid fa-check"></i> Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 3: View Room Details Modal -->
<div class="modal-backdrop" id="view-room-modal">
  <div class="modal-window large" style="width: 680px; max-width: 95vw; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.2); border: none;">
    <div class="modal-top" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); padding: 16px 24px; color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #ffffff; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-door-open"></i> Room Asset Details
      </h3>
      <button class="modal-close" onclick="closeModal('view-room-modal')" style="background: rgba(255,255,255,0.2); color: #ffffff; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 16px;">&times;</button>
    </div>
    <div id="view-room-body" style="padding: 22px 24px; max-height: 80vh; overflow-y: auto; background: #f8fafc;">
      <!-- Populated dynamically via JavaScript -->
    </div>
    <div class="modal-bot" style="padding: 14px 24px; background: #ffffff; border-top: 1px solid var(--border-medium); display: flex; justify-content: space-between; align-items: center;">
      <div id="view-room-bot-left"></div>
      <button type="button" class="btn-ui-secondary" onclick="closeModal('view-room-modal')" style="height: 38px; padding: 0 20px; font-weight: 700; border-radius: 8px;">Close</button>
    </div>
  </div>
</div>

<!-- MODAL 4: Room Maintenance Work Order Modal -->
<div class="modal-backdrop" id="maint-workorder-modal">
  <div class="modal-window" style="width: 640px; max-width: 95vw; border-radius: 18px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,0.3);">
    <div class="modal-top" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); padding: 18px 24px; color: #ffffff; display: flex; justify-content: space-between; align-items: center; border-bottom: none;">
      <h3 style="margin: 0; font-size: 16px; font-weight: 800; display: flex; align-items: center; gap: 10px; color: #ffffff;">
        <i class="fa-solid fa-wrench"></i> Room Maintenance Work Order
      </h3>
      <button class="modal-close" onclick="closeModal('maint-workorder-modal')" style="background: rgba(255,255,255,0.2); color: #ffffff; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border: none; font-size: 18px; cursor: pointer;">&times;</button>
    </div>

    <form id="maint-workorder-form" onsubmit="handleSaveMaintenanceWorkOrder(event)">
      <input type="hidden" id="maint-room-id">
      <div class="modal-content-area" style="padding: 24px; background: #ffffff;">
        <div style="font-size: 11px; font-weight: 800; color: #6366f1; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-hotel"></i> WORK ORDER SPECIFICATIONS
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px;">
          <div>
            <label style="display:block; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
              ROOM CATEGORY
            </label>
            <input type="text" id="maint-room-cat" class="crud-search-input" readonly style="background: #f8fafc; font-weight: 700; color: var(--text-primary); cursor: not-allowed; height: 38px; width: 100%;">
          </div>
          <div>
            <label style="display:block; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
              FLOOR
            </label>
            <input type="text" id="maint-room-floor" class="crud-search-input" readonly style="background: #f8fafc; font-weight: 700; color: var(--text-primary); cursor: not-allowed; height: 38px; width: 100%;">
          </div>
          <div>
            <label style="display:block; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
              ROOM NUMBER
            </label>
            <input type="text" id="maint-room-no-display" class="crud-search-input" readonly style="background: #f8fafc; font-weight: 800; color: var(--accent-primary); cursor: not-allowed; height: 38px; width: 100%; font-family: var(--font-mono);">
          </div>
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display:block; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
            MAINTENANCE REASON / WORK DESCRIPTION <span style="color: red;">*</span>
          </label>
          <input type="text" id="maint-reason" class="crud-search-input" placeholder="e.g. HVAC Air Conditioning & Compressor Service" required style="width: 100%; height: 38px; font-weight: 600; color: var(--text-primary);">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
          <div>
            <label style="display:block; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
              ASSIGNED PERSONNEL
            </label>
            <input type="text" id="maint-engineer" class="crud-search-input" value="" placeholder="e.g. Maintenance Technician" style="width: 100%; height: 38px; font-weight: 600; color: var(--text-primary);">
          </div>
          <div>
            <label style="display:block; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
              EXPECTED COMPLETION (DATE & TIME) <span style="color: red;">*</span>
            </label>
            <input type="datetime-local" id="maint-completion" class="crud-search-input" required style="width: 100%; height: 38px; font-weight: 600; color: var(--text-primary);">
          </div>
        </div>

        <div style="margin-bottom: 18px;">
          <label style="display:block; font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">
            NOTE
          </label>
          <textarea id="maint-note" class="crud-search-input" rows="3" placeholder="Enter special instructions or notes..." style="width: 100%; height: auto; padding: 10px 14px; font-weight: 500; color: var(--text-primary); border-radius: var(--radius-md); font-family: inherit; resize: vertical;"></textarea>
        </div>

        <div style="background: #fffbeb; border: 1px solid #fed7aa; border-radius: 10px; padding: 14px 18px; display: flex; align-items: center; gap: 12px;">
          <i class="fa-solid fa-triangle-exclamation" style="color: #d97706; font-size: 18px; flex-shrink: 0;"></i>
          <span style="color: #b45309; font-size: 12px; font-weight: 600; line-height: 1.4;">
            Setting this room into Maintenance will automatically prevent new guest check-ins from the Front Desk rack.
          </span>
        </div>
      </div>

      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('maint-workorder-modal')" style="height: 38px; padding: 0 18px; border-radius: 8px;">Cancel</button>
        <button type="submit" class="btn-ui-primary" style="height: 38px; padding: 0 22px; border-radius: 8px; background: linear-gradient(135deg, #6366f1, #8b5cf6);">
          <i class="fa-solid fa-lock"></i> Put in Maintenance
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  let roomsData = @json($rooms);
  let selectedRoomIds = new Set();
  const masterCategories = @json($categories);
  const masterFloors = @json($floors);
  const masterAmenities = @json($amenities);
  const beddingConfigsData = @json($beddingConfigs);
  const baseUrl = "{{ route('admin.roommanagement.index') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function getPaxDataForBedding(beddingName) {
    const config = beddingConfigsData.find(b => b.name === beddingName);
    if (!config) {
      return { adults: 2, children: 0, total: 2, display: '2 Adults (2 Pax)' };
    }
    const adults = config.max_adults ?? 2;
    const children = config.max_children ?? 0;
    const total = config.max_total ?? (adults + children);
    const display = `${adults} Adult${adults > 1 ? 's' : ''}${children > 0 ? ' + ' + children + ' Child' : ''} (Max ${total} Pax)`;
    return { adults, children, total, display };
  }

  function updateNewRoomPaxCapacity(bedName) {
    const name = bedName || $('#new-room-bed').val();
    const data = getPaxDataForBedding(name);
    const adultsEl = document.getElementById('new-pax-adults-text');
    const childrenEl = document.getElementById('new-pax-children-text');
    const totalEl = document.getElementById('new-pax-total-text');

    if (adultsEl) adultsEl.textContent = `${data.adults} Adult${data.adults > 1 ? 's' : ''}`;
    if (childrenEl) childrenEl.textContent = `${data.children} Child${data.children === 1 ? '' : 'ren'}`;
    if (totalEl) totalEl.textContent = `Total ${data.total} Pax`;
  }

  function updateEditRoomPaxCapacity(bedName) {
    const name = bedName || $('#edit-room-bed').val();
    const data = getPaxDataForBedding(name);
    const adultsEl = document.getElementById('edit-pax-adults-text');
    const childrenEl = document.getElementById('edit-pax-children-text');
    const totalEl = document.getElementById('edit-pax-total-text');

    if (adultsEl) adultsEl.textContent = `${data.adults} Adult${data.adults > 1 ? 's' : ''}`;
    if (childrenEl) childrenEl.textContent = `${data.children} Child${data.children === 1 ? '' : 'ren'}`;
    if (totalEl) totalEl.textContent = `Total ${data.total} Pax`;
  }

  const urlParams = new URLSearchParams(window.location.search);
  let searchQuery = urlParams.get('search') || '{{ request('search') }}' || '';
  let categoryFilter = urlParams.get('category') || '{{ request('category') }}' || 'ALL';
  let floorFilter = urlParams.get('floor') || '{{ request('floor') }}' || 'all';
  let statusFilter = urlParams.get('status') || '{{ request('status') }}' || 'all';
  let amenityFilter = urlParams.get('amenity') || '{{ request('amenity') }}' || 'all';
  let viewMode = urlParams.get('view') || 'table';
  let currentPage = parseInt(urlParams.get('page') || '1', 10);
  let pageSize = urlParams.get('per_page') || '20';

  function updateUrlParams() {
    const params = new URLSearchParams();
    if (searchQuery) params.set('search', searchQuery);
    if (categoryFilter && categoryFilter !== 'ALL') params.set('category', categoryFilter);
    if (floorFilter && floorFilter !== 'all') params.set('floor', floorFilter);
    if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);
    if (amenityFilter && amenityFilter !== 'all') params.set('amenity', amenityFilter);
    if (viewMode !== 'table') params.set('view', viewMode);
    if (currentPage > 1) params.set('page', currentPage);
    if (pageSize !== '20') params.set('per_page', pageSize);

    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState(null, '', newUrl);
  }

  // Initialize Select2 on Modals and Toolbar Filters
  function initSelect2() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
      $('#category-filter').select2({ width: '140px' });
      $('#floor-filter').select2({ width: '130px' });
      $('#status-filter').select2({ width: '130px' });
      $('#amenity-filter').select2({ width: '150px' });

      // Apply initial values if present in URL
      if (categoryFilter && categoryFilter !== 'ALL') $('#category-filter').val(categoryFilter).trigger('change.select2');
      if (floorFilter && floorFilter !== 'all') $('#floor-filter').val(floorFilter).trigger('change.select2');
      if (statusFilter && statusFilter !== 'all') $('#status-filter').val(statusFilter).trigger('change.select2');
      if (amenityFilter && amenityFilter !== 'all') $('#amenity-filter').val(amenityFilter).trigger('change.select2');

      $('#category-filter').on('change', function() {
        handleCategoryFilter(this.value);
      });
      $('#floor-filter').on('change', function() {
        handleFloorFilter(this.value);
      });
      $('#status-filter').on('change', function() {
        handleStatusFilter(this.value);
      });
      $('#amenity-filter').on('change', function() {
        handleAmenityFilter(this.value);
      });

      $('#new-room-floor, #new-room-cat, #new-room-bed, #new-room-status').select2({
        dropdownParent: $('#add-room-modal')
      });
      $('#edit-room-floor, #edit-room-cat, #edit-room-bed, #edit-room-status').select2({
        dropdownParent: $('#edit-room-modal')
      });

      // Auto update pax capacity on bedding config change
      $('#new-room-bed').on('change', function() {
        updateNewRoomPaxCapacity(this.value);
      });
      $('#edit-room-bed').on('change', function() {
        updateEditRoomPaxCapacity(this.value);
      });
    }
  }

  function openAddRoomModal() {
    document.getElementById('new-room-no').value = '';
    document.getElementById('new-room-rate').value = '3500';
    
    // Reset Select2 fields
    if (typeof $ !== 'undefined' && $.fn.select2) {
      $('#new-room-floor').val($('#new-room-floor option:eq(1)').val()).trigger('change');
      $('#new-room-cat').val($('#new-room-cat option:eq(1)').val()).trigger('change');
      const defaultBed = $('#new-room-bed option:eq(0)').val();
      $('#new-room-bed').val(defaultBed).trigger('change');
      updateNewRoomPaxCapacity(defaultBed);
      $('#new-room-status').val('Active').trigger('change');
    }

    // Default checkboxes
    const defaultAmn = ['Free Wi-Fi', 'Balcony', 'Smart 55" TV', 'AC'];
    document.querySelectorAll('#add-amenities-container input[type="checkbox"]').forEach(cb => {
      cb.checked = defaultAmn.includes(cb.value);
    });

    openModal('add-room-modal');
  }

  function handleCategoryChange(catName) {
    const selectedOption = $('#new-room-cat option:selected');
    const bed = selectedOption.data('bed');
    const pax = selectedOption.data('pax');

    if (bed && $('#new-room-bed').length) {
      $('#new-room-bed').val(bed).trigger('change');
    }

    const defaultTariffs = {
      'Deluxe Room': 3500,
      'DELUXE': 3500,
      'Super Deluxe': 5200,
      'SUPER DELUXE': 5200,
      'Suite Luxury': 8500,
      'SUITE': 8500,
      'Executive Penthouse': 11000
    };
    if (defaultTariffs[catName]) {
      document.getElementById('new-room-rate').value = defaultTariffs[catName];
    }
  }

  function openEditRoomModal(id) {
    const room = roomsData.find(r => r.id === id);
    if (!room) return;

    document.getElementById('edit-room-id').value = room.id;
    document.getElementById('edit-modal-room-num').textContent = '#' + room.room_number;
    document.getElementById('edit-room-no').value = room.room_number;
    document.getElementById('edit-room-rate').value = room.rate;

    // Set Select2 values directly from dynamic records
    if (typeof $ !== 'undefined' && $.fn.select2) {
      $('#edit-room-floor').val(room.floor).trigger('change');
      $('#edit-room-cat').val(room.category).trigger('change');
      $('#edit-room-bed').val(room.bedding_config).trigger('change');
      updateEditRoomPaxCapacity(room.bedding_config);
      $('#edit-room-status').val(room.status || 'Active').trigger('change');
    }

    // Check amenities
    const currentAmenities = Array.isArray(room.amenities) ? room.amenities : [];
    document.querySelectorAll('#edit-amenities-container input[type="checkbox"]').forEach(cb => {
      cb.checked = currentAmenities.includes(cb.value);
    });

    openModal('edit-room-modal');
  }

  async function handleAddRoom(e) {
    e.preventDefault();
    const room_number = document.getElementById('new-room-no').value.trim();
    const floor = document.getElementById('new-room-floor').value;
    const category = document.getElementById('new-room-cat').value;
    const rate = document.getElementById('new-room-rate').value;
    const bedding_config = document.getElementById('new-room-bed').value;
    const status = document.getElementById('new-room-status').value || 'Active';

    const checkedAmenities = [];
    document.querySelectorAll('#add-amenities-container input[type="checkbox"]:checked').forEach(cb => {
      checkedAmenities.push(cb.value);
    });

    if (!room_number || !floor || !category || !rate) {
      PmsAlert.error('Validation Error', 'Please complete all required fields.');
      return;
    }

    const payload = {
      room_number,
      floor,
      category,
      rate,
      bedding_config,
      status,
      amenities: checkedAmenities
    };

    try {
      const res = await fetch(baseUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (res.ok && data.success) {
        closeModal('add-room-modal');
        PmsAlert.toast(data.message || 'Room registered successfully!');
        loadRoomsData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to add room.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  async function handleSaveEditRoom(e) {
    e.preventDefault();
    const id = document.getElementById('edit-room-id').value;
    const room_number = document.getElementById('edit-room-no').value.trim();
    const floor = document.getElementById('edit-room-floor').value;
    const category = document.getElementById('edit-room-cat').value;
    const rate = document.getElementById('edit-room-rate').value;
    const bedding_config = document.getElementById('edit-room-bed').value;
    const status = document.getElementById('edit-room-status').value || 'Active';

    const checkedAmenities = [];
    document.querySelectorAll('#edit-amenities-container input[type="checkbox"]:checked').forEach(cb => {
      checkedAmenities.push(cb.value);
    });

    if (!room_number || !floor || !category || !rate) {
      PmsAlert.error('Validation Error', 'Please complete all required fields.');
      return;
    }

    const payload = {
      room_number,
      floor,
      category,
      rate,
      bedding_config,
      status,
      amenities: checkedAmenities
    };

    try {
      const res = await fetch(`${baseUrl}/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (res.ok && data.success) {
        closeModal('edit-room-modal');
        PmsAlert.toast(data.message || 'Room updated successfully!');
        loadRoomsData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to update room.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  function updateRoomsBulkActionUI() {
    const btn = document.getElementById('btn-bulk-delete-rooms');
    const countSpan = document.getElementById('bulk-selected-rooms-count');
    const selectAllCheck = document.getElementById('select-all-rooms-check');

    if (btn && countSpan) {
      countSpan.textContent = selectedRoomIds.size;
      btn.style.display = selectedRoomIds.size > 0 ? 'inline-flex' : 'none';
    }

    if (selectAllCheck) {
      const filtered = getFilteredRooms();
      if (filtered.length === 0) {
        selectAllCheck.checked = false;
        selectAllCheck.indeterminate = false;
      } else {
        const allSelected = filtered.every(r => selectedRoomIds.has(r.id));
        const someSelected = filtered.some(r => selectedRoomIds.has(r.id));
        selectAllCheck.checked = allSelected;
        selectAllCheck.indeterminate = someSelected && !allSelected;
      }
    }
  }

  function handleRoomSelect(checkbox, id) {
    if (checkbox.checked) {
      selectedRoomIds.add(id);
    } else {
      selectedRoomIds.delete(id);
    }
    updateRoomsBulkActionUI();
  }

  function toggleSelectAllRooms(masterCheckbox) {
    const filtered = getFilteredRooms();
    if (masterCheckbox.checked) {
      filtered.forEach(r => selectedRoomIds.add(r.id));
    } else {
      filtered.forEach(r => selectedRoomIds.delete(r.id));
    }
    renderView();
  }

  async function handleBulkDeleteRooms() {
    if (selectedRoomIds.size === 0) return;
    const count = selectedRoomIds.size;
    const result = await PmsAlert.confirmDelete(`Delete ${count} Room Asset${count > 1 ? 's' : ''}?`, 'Selected rooms will be permanently removed from inventory.');
    if (result.isConfirmed) {
      try {
        const res = await fetch(`${baseUrl}/bulk-delete`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ ids: Array.from(selectedRoomIds) })
        });
        const data = await res.json();
        if (res.ok && data.success) {
          PmsAlert.toast(data.message || 'Rooms deleted successfully!');
          selectedRoomIds.clear();
          await loadRoomsData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete selected rooms.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'An error occurred during bulk deletion.');
      }
    }
  }

  async function deleteRoom(id) {
    const room = roomsData.find(r => r.id === id);
    const result = await PmsAlert.confirmDelete(`Delete Room #${room ? room.room_number : id}?`, 'This room asset will be permanently removed from inventory.');
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
          PmsAlert.toast('Room deleted successfully!');
          selectedRoomIds.delete(id);
          loadRoomsData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete room.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'An error occurred during deletion.');
      }
    }
  }

  function toggleRoomMaintenance(id) {
    const room = roomsData.find(r => r.id === id);
    if (!room) return;

    if (room.status === 'Maintenance' || room.status === 'Under Maintenance') {
      PmsAlert.confirm(
        'Return to In-Service?',
        `Room #${room.room_number} is currently Under Maintenance. Complete work order and return room to Active In-Service?`,
        '<i class="fa-solid fa-check" style="margin-right: 6px;"></i> Yes, Return to In-Service',
        'question'
      ).then(async (result) => {
        if (result.isConfirmed) {
          try {
            const res = await fetch(`${baseUrl}/${id}/toggle-maintenance`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
              },
              body: JSON.stringify({})
            });
            const data = await res.json();
            if (res.ok && data.success) {
              PmsAlert.toast(data.message || `Room #${room.room_number} returned to Active In-Service!`);
              loadRoomsData();
            } else {
              PmsAlert.error('Error', data.message || 'Failed to update maintenance status.');
            }
          } catch (err) {
            PmsAlert.error('Server Error', 'Failed to update maintenance status.');
          }
        }
      });
      return;
    }

    // Open Maintenance Work Order Modal
    document.getElementById('maint-room-id').value = room.id;
    document.getElementById('maint-room-cat').value = room.category || 'Standard';
    document.getElementById('maint-room-floor').value = 'Floor ' + (room.floor || '1');
    document.getElementById('maint-room-no-display').value = `Room ${room.room_number} (${room.category || 'Standard'})`;

    const futureDate = new Date(Date.now() + 48 * 3600 * 1000);
    const localIso = new Date(futureDate.getTime() - (futureDate.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
    document.getElementById('maint-completion').value = localIso;
    document.getElementById('maint-reason').value = '';
    document.getElementById('maint-engineer').value = '';
    const noteEl = document.getElementById('maint-note');
    if (noteEl) noteEl.value = '';

    openModal('maint-workorder-modal');
  }

  async function handleSaveMaintenanceWorkOrder(e) {
    e.preventDefault();
    const id = document.getElementById('maint-room-id').value;
    const reason = document.getElementById('maint-reason').value;
    const engineer = document.getElementById('maint-engineer').value;
    const completion = document.getElementById('maint-completion').value;
    const noteEl = document.getElementById('maint-note');
    const note = noteEl ? noteEl.value : '';

    try {
      const res = await fetch(`${baseUrl}/${id}/toggle-maintenance`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ reason, engineer, completion, note })
      });
      const data = await res.json();
      if (res.ok && data.success) {
        closeModal('maint-workorder-modal');
        PmsAlert.toast(data.message || 'Room placed into maintenance successfully!');
        loadRoomsData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to place room into maintenance.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with server.');
    }
  }

  async function loadRoomsData() {
    updateUrlParams();
    try {
      const params = new URLSearchParams();
      if (searchQuery) params.set('search', searchQuery);
      if (categoryFilter && categoryFilter !== 'ALL') params.set('category', categoryFilter);
      if (floorFilter && floorFilter !== 'all') params.set('floor', floorFilter);
      if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);
      if (amenityFilter && amenityFilter !== 'all') params.set('amenity', amenityFilter);

      const res = await fetch(`${baseUrl}?${params.toString()}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success) {
        roomsData = data.data;
        renderView();
      }
    } catch (err) {
      console.error(err);
    }
  }

  let searchDebounceTimer = null;
  function handleSearch(val) {
    searchQuery = (val || '').trim();
    currentPage = 1;
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
      loadRoomsData();
    }, 250);
  }

  function handleCategoryFilter(val) {
    categoryFilter = val || 'ALL';
    currentPage = 1;
    loadRoomsData();
  }

  function handleFloorFilter(val) {
    floorFilter = val;
    currentPage = 1;
    loadRoomsData();
  }

  function handleStatusFilter(val) {
    statusFilter = val;
    currentPage = 1;
    loadRoomsData();
  }

  function handleAmenityFilter(val) {
    amenityFilter = val;
    currentPage = 1;
    loadRoomsData();
  }

  function handlePageSizeChange(val) {
    pageSize = val === 'all' ? 'all' : parseInt(val, 10);
    currentPage = 1;
    updateUrlParams();
    renderView();
  }

  function goToPage(page) {
    currentPage = page;
    updateUrlParams();
    renderView();
  }

  function setViewMode(mode) {
    viewMode = mode;
    updateUrlParams();
    document.getElementById('btn-view-table').classList.toggle('active', mode === 'table');
    document.getElementById('btn-view-grid').classList.toggle('active', mode === 'grid');
    document.getElementById('rooms-table-container').style.display = mode === 'table' ? 'block' : 'none';
    document.getElementById('rooms-grid-container').style.display = mode === 'grid' ? 'grid' : 'none';
    renderView();
  }

  function getFilteredRooms() {
    return roomsData.filter(room => {
      const matchSearch = !searchQuery ||
        (room.room_number && room.room_number.toLowerCase().includes(searchQuery)) ||
        (room.floor && room.floor.toString().toLowerCase().includes(searchQuery)) ||
        (room.category && room.category.toLowerCase().includes(searchQuery)) ||
        (room.bedding_config && room.bedding_config.toLowerCase().includes(searchQuery)) ||
        (room.pax_capacity && room.pax_capacity.toLowerCase().includes(searchQuery)) ||
        (room.status && room.status.toLowerCase().includes(searchQuery)) ||
        (room.guest_name && room.guest_name.toLowerCase().includes(searchQuery));

      const matchCat = categoryFilter === 'ALL' || (room.category && room.category.toUpperCase() === categoryFilter.toUpperCase());
      const matchFloor = floorFilter === 'all' || (room.floor && room.floor.toString() === floorFilter.toString());
      const matchStatus = statusFilter === 'all' || (room.status && room.status.toLowerCase() === statusFilter.toLowerCase());
      
      const roomAmenities = Array.isArray(room.amenities) ? room.amenities : [];
      const matchAmenity = amenityFilter === 'all' || roomAmenities.includes(amenityFilter);

      return matchSearch && matchCat && matchFloor && matchStatus && matchAmenity;
    });
  }

  function updateKPIStats(filteredRooms) {
    const total = filteredRooms.length;
    const active = filteredRooms.filter(r => (r.status || 'Active').toLowerCase() === 'active').length;
    const inactive = filteredRooms.filter(r => (r.status || '').toLowerCase() === 'inactive').length;

    // Categories breakdown - initialize all active master categories
    const catMap = {};
    masterCategories.forEach(c => { catMap[c.name] = 0; });
    filteredRooms.forEach(r => {
      const cat = r.category || 'Unassigned';
      catMap[cat] = (catMap[cat] || 0) + 1;
    });

    // Floors breakdown - initialize all active master floors
    const floorMap = {};
    masterFloors.forEach(f => { floorMap[f.floor] = 0; });
    filteredRooms.forEach(r => {
      const fl = r.floor !== null && r.floor !== undefined ? r.floor.toString() : '1';
      floorMap[fl] = (floorMap[fl] || 0) + 1;
    });

    // Amenities breakdown - initialize all active master amenities
    const amnMap = {};
    masterAmenities.forEach(a => { amnMap[a.name] = 0; });
    filteredRooms.forEach(r => {
      if (Array.isArray(r.amenities)) {
        r.amenities.forEach(a => {
          amnMap[a] = (amnMap[a] || 0) + 1;
        });
      }
    });

    // Update DOM Top Numbers
    const totalEl = document.getElementById('stat-total-rooms');
    const activeEl = document.getElementById('stat-active-rooms');
    const inactiveEl = document.getElementById('stat-inactive-rooms');
    const catTotalEl = document.getElementById('stat-category-total-rooms');
    const floorTotalEl = document.getElementById('stat-floor-total-rooms');
    const amnTotalEl = document.getElementById('stat-amenity-total-rooms');

    if (totalEl) totalEl.textContent = total;
    if (activeEl) activeEl.textContent = active;
    if (inactiveEl) inactiveEl.textContent = inactive;
    if (catTotalEl) catTotalEl.textContent = total;
    if (floorTotalEl) floorTotalEl.textContent = total;
    if (amnTotalEl) amnTotalEl.textContent = total;

    // Render Status Pills
    const statusPillsEl = document.getElementById('stat-status-pills');
    if (statusPillsEl) {
      statusPillsEl.innerHTML = `
        <span class="badge-tag green" style="font-size: 10px; padding: 2px 7px;">
          <i class="fa-solid fa-circle-check"></i> <span id="stat-active-rooms">${active}</span> Active
        </span>
        <span class="badge-tag red" style="font-size: 10px; padding: 2px 7px;">
          <i class="fa-solid fa-circle-xmark"></i> <span id="stat-inactive-rooms">${inactive}</span> Inactive
        </span>
      `;
    }

    // Render Category Pills
    const catPillsEl = document.getElementById('stat-category-pills');
    if (catPillsEl) {
      const entries = Object.entries(catMap);
      if (entries.length === 0) {
        catPillsEl.innerHTML = '<span style="font-size: 11px; color: var(--text-muted);">No categories</span>';
      } else {
        catPillsEl.innerHTML = entries.map(([cat, cnt]) => `
          <span class="badge-tag" style="background: rgba(244, 63, 94, 0.1); color: #e11d48; border: 1px solid rgba(244, 63, 94, 0.2); font-size: 10px; padding: 2px 7px; white-space: nowrap;">
            ${cat}: <strong style="font-weight: 900; margin-left: 2px;">${cnt}</strong>
          </span>
        `).join('');
      }
    }

    // Render Floor Pills
    const floorPillsEl = document.getElementById('stat-floor-pills');
    if (floorPillsEl) {
      const entries = Object.entries(floorMap);
      if (entries.length === 0) {
        floorPillsEl.innerHTML = '<span style="font-size: 11px; color: var(--text-muted);">No floors</span>';
      } else {
        floorPillsEl.innerHTML = entries.map(([fl, cnt]) => `
          <span class="badge-tag" style="background: rgba(6, 182, 212, 0.1); color: #0284c7; border: 1px solid rgba(6, 182, 212, 0.2); font-size: 10px; padding: 2px 7px; white-space: nowrap;">
            Floor ${fl}: <strong style="font-weight: 900; margin-left: 2px;">${cnt}</strong>
          </span>
        `).join('');
      }
    }

    // Render Amenity Pills
    const amnPillsEl = document.getElementById('stat-amenity-pills');
    if (amnPillsEl) {
      const entries = Object.entries(amnMap);
      if (entries.length === 0) {
        amnPillsEl.innerHTML = '<span style="font-size: 11px; color: var(--text-muted);">No amenities</span>';
      } else {
        amnPillsEl.innerHTML = entries.map(([amn, cnt]) => `
          <span class="badge-tag" style="background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2); font-size: 10px; padding: 2px 7px; white-space: nowrap;">
            ${amn}: <strong style="font-weight: 900; margin-left: 2px;">${cnt}</strong>
          </span>
        `).join('');
      }
    }
  }

  function renderView() {
    const filtered = getFilteredRooms();
    const totalCount = filtered.length;
    const totalPages = pageSize === 'all' ? 1 : Math.max(1, Math.ceil(totalCount / pageSize));

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIdx = pageSize === 'all' ? 0 : (currentPage - 1) * pageSize;
    const endIdx = pageSize === 'all' ? totalCount : Math.min(startIdx + pageSize, totalCount);
    const pageItems = filtered.slice(startIdx, endIdx);

    // Update Live KPI Cards based on filtered records
    updateKPIStats(filtered);

    // Update Record Count in Header Badge
    const viewCountBadge = document.getElementById('view-count');
    if (viewCountBadge) {
      viewCountBadge.textContent = `${totalCount} Records`;
    }

    // Render Table
    const tbody = document.getElementById('rooms-tbody');
    if (!pageItems.length) {
      tbody.innerHTML = `<tr><td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);"><i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>No matching room assets found.</td></tr>`;
    } else {
      let tableHtml = '';
      pageItems.forEach((room, idx) => {
        const isSelected = selectedRoomIds.has(room.id);
        let catBadgeClass = 'blue';
        if (room.category && room.category.includes('SUPER')) catBadgeClass = 'purple';
        else if (room.category && room.category.includes('SUITE')) catBadgeClass = 'yellow';
        else if (room.category && room.category.includes('EXEC')) catBadgeClass = 'green';

        const isInactive = room.status && room.status.toLowerCase() === 'inactive';
        let statusBadgeClass = isInactive ? 'red' : 'green';
        let statusLabel = isInactive ? 'Inactive' : 'Active';

        const amenitiesList = Array.isArray(room.amenities) ? room.amenities : [];
        const amenitiesHtml = amenitiesList.slice(0, 3).map(a => `<span class="amenity-chip">${a}</span>`).join('') +
          (amenitiesList.length > 3 ? `<span class="amenity-chip">+${amenitiesList.length - 3}</span>` : '');

        tableHtml += `
          <tr data-id="${room.id}">
            <td style="text-align: center;">
              <input type="checkbox" class="row-select-check" value="${room.id}" ${isSelected ? 'checked' : ''} onchange="handleRoomSelect(this, ${room.id})" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
            </td>
            <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">${startIdx + idx + 1}</td>
            <td>
              <span style="font-family: var(--font-mono); font-size: 14px; font-weight: 900; color: var(--text-primary);">#${room.room_number}</span>
            </td>
            <td>
              <span class="badge-tag" style="background: #f1f5f9; color: var(--text-secondary); font-weight: 700; font-size: 11px;">Floor ${room.floor}</span>
            </td>
            <td><span class="badge-tag ${catBadgeClass}">${room.category}</span></td>
            <td>
              <div style="font-size: 12px; font-weight: 700; color: var(--text-primary);">${room.bedding_config || 'Standard Bed'}</div>
              <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;"><i class="fa-solid fa-user-group" style="font-size: 10px; margin-right: 4px;"></i>${room.pax_capacity || '2 Adults'}</div>
            </td>
            <td>
              <div style="font-family: var(--font-mono); font-weight: 900; font-size: 13px; color: var(--accent-primary);">₹ ${Number(room.rate).toLocaleString()}</div>
            </td>
            <td>
              <span class="badge-tag ${statusBadgeClass}">
                <i class="fa-solid fa-circle" style="font-size: 6px; margin-right: 4px;"></i>${statusLabel}
              </span>
            </td>
            <td>
              <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 200px;">
                ${amenitiesHtml || '<span style="color: var(--text-muted); font-size: 11px;">None</span>'}
              </div>
            </td>
            <td style="text-align: right;">
              <div style="display: flex; align-items: center; justify-content: flex-end; gap: 4px;">
                <button class="btn-action-view" onclick="openViewRoomModal(${room.id})" title="View Details"><i class="fa-solid fa-eye"></i></button>
                <button class="btn-action-edit" onclick="openEditRoomModal(${room.id})" title="Edit Room Asset"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-action-del" onclick="deleteRoom(${room.id})" title="Delete Room"><i class="fa-solid fa-trash"></i></button>
              </div>
            </td>
          </tr>
        `;
      });
      tbody.innerHTML = tableHtml;
    }

    // Render Grid
    const gridContainer = document.getElementById('rooms-grid-container');
    if (!pageItems.length) {
      gridContainer.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-muted);"><i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>No matching room assets found.</div>`;
    } else {
      let gridHtml = '';
      pageItems.forEach(room => {
        let catBadgeClass = 'blue';
        if (room.category && room.category.includes('SUPER')) catBadgeClass = 'purple';
        else if (room.category && room.category.includes('SUITE')) catBadgeClass = 'yellow';
        else if (room.category && room.category.includes('EXEC')) catBadgeClass = 'green';

        const isInactive = room.status && room.status.toLowerCase() === 'inactive';
        let statusBadgeClass = isInactive ? 'red' : 'green';
        let statusLabel = isInactive ? 'Inactive' : 'Active';
        let borderTopColor = isInactive ? '#ef4444' : '#10b981';

        const amenitiesList = Array.isArray(room.amenities) ? room.amenities : [];
        const amenitiesHtml = amenitiesList.slice(0, 3).map(a => `<span class="amenity-chip">${a}</span>`).join('') +
          (amenitiesList.length > 3 ? `<span class="amenity-chip">+${amenitiesList.length - 3}</span>` : '');

        gridHtml += `
          <div class="room-inv-card" style="border-top: 4px solid ${borderTopColor};">
            <div>
              <div class="room-inv-header">
                <div class="room-inv-number">
                  <span>Room ${room.room_number}</span>
                  <span class="room-inv-floor">Floor ${room.floor}</span>
                </div>
                <span class="badge-tag ${catBadgeClass}">${room.category}</span>
              </div>

              <div style="font-size: 12px; font-weight: 700; color: var(--text-secondary); margin-top: 8px;">
                <i class="fa-solid fa-bed" style="margin-right: 4px; color: var(--accent-primary);"></i> ${room.bedding_config || 'Standard'} • ${room.pax_capacity || '2 Adults'}
              </div>

              <div class="room-amenity-tags">
                ${amenitiesHtml || '<span style="color: var(--text-muted); font-size: 10px;">No amenities</span>'}
              </div>

              <div style="display: flex; gap: 6px; margin-top: 10px;">
                <span class="badge-tag ${statusBadgeClass}"><i class="fa-solid fa-circle" style="font-size: 6px; margin-right: 4px;"></i>${statusLabel}</span>
              </div>
            </div>

            <div class="room-inv-footer">
              <div class="room-inv-rate">₹ ${Number(room.rate).toLocaleString()} <span style="font-size: 10px; color: var(--text-muted); font-weight: 500;">/ night</span></div>
              <div style="display: flex; gap: 4px;">
                <button class="btn-action-view" onclick="openViewRoomModal(${room.id})" title="View Details"><i class="fa-solid fa-eye"></i></button>
                <button class="btn-action-edit" onclick="openEditRoomModal(${room.id})" title="Edit"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-action-del" onclick="deleteRoom(${room.id})" title="Delete"><i class="fa-solid fa-trash"></i></button>
              </div>
            </div>
          </div>
        `;
      });
      gridContainer.innerHTML = gridHtml;
    }

    // Pagination info
    const infoEl = document.getElementById('pagination-info');
    if (totalCount === 0) {
      infoEl.textContent = 'Showing 0 to 0 of 0 records';
    } else {
      infoEl.innerHTML = `Showing <strong style="color: var(--text-primary);">${startIdx + 1}</strong> to <strong style="color: var(--text-primary);">${endIdx}</strong> of <strong style="color: var(--text-primary);">${totalCount}</strong> records${totalCount !== roomsData.length ? ' (filtered from ' + roomsData.length + ' total)' : ''}`;
    }

    renderPaginationNav(totalPages);
    updateRoomsBulkActionUI();
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

  function openViewRoomModal(id) {
    const room = roomsData.find(r => r.id === id);
    if (!room) return;

    let catBadgeBg = '#eff6ff';
    let catBadgeColor = '#2563eb';
    let catBadgeBorder = '#bfdbfe';
    if (room.category && room.category.toUpperCase().includes('SUPER')) {
      catBadgeBg = '#faf5ff'; catBadgeColor = '#7c3aed'; catBadgeBorder = '#e9d5ff';
    } else if (room.category && (room.category.toUpperCase().includes('SUITE') || room.category.toUpperCase().includes('DELUXE'))) {
      catBadgeBg = '#f0fdf4'; catBadgeColor = '#16a34a'; catBadgeBorder = '#bbf7d0';
    } else if (room.category && room.category.toUpperCase().includes('EXEC')) {
      catBadgeBg = '#fff7ed'; catBadgeColor = '#ea580c'; catBadgeBorder = '#fed7aa';
    }

    const isInactive = room.status && room.status.toLowerCase() === 'inactive';
    const statusBg = isInactive ? '#fef2f2' : '#ecfdf5';
    const statusColor = isInactive ? '#dc2626' : '#059669';
    const statusBorder = isInactive ? '#fecaca' : '#a7f3d0';
    const statusDot = isInactive ? '#ef4444' : '#10b981';
    const statusText = isInactive ? 'Inactive' : 'Active';

    const amenitiesList = Array.isArray(room.amenities) ? room.amenities : [];
    const amenitiesHtml = amenitiesList.length > 0
      ? amenitiesList.map(a => `
          <div style="display: inline-flex; align-items: center; gap: 7px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 7px 12px; font-size: 12px; font-weight: 600; color: #334155; box-shadow: 0 1px 2px rgba(0,0,0,0.03);">
            <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 13px;"></i>
            <span>${a}</span>
          </div>
        `).join('')
      : `<div style="font-size: 12px; color: #94a3b8; font-style: italic; padding: 6px 0;">No amenities assigned to this room.</div>`;

    const content = `
      <!-- Top 2 Equal-Size Boxes Grid -->
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
        
        <!-- Left Box: Floor, Room Number & Category -->
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 14px 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: center; gap: 8px;">
          
          <!-- Item 1: Floor -->
          <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748b; display: flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-layer-group" style="color: #6366f1; font-size: 12px; width: 14px;"></i> Floor
            </span>
            <span style="font-size: 12px; font-weight: 800; color: #1e293b;">Floor ${room.floor}</span>
          </div>

          <!-- Item 2: Room Number -->
          <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748b; display: flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-door-closed" style="color: #6366f1; font-size: 12px; width: 14px;"></i> Room Number
            </span>
            <span style="font-family: var(--font-mono, monospace); font-size: 13px; font-weight: 900; color: #0f172a;">Room #${room.room_number}</span>
          </div>

          <!-- Item 3: Category -->
          <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 12px;">
            <span style="font-size: 11px; font-weight: 700; color: #64748b; display: flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-crown" style="color: #6366f1; font-size: 12px; width: 14px;"></i> Category
            </span>
            <span style="font-size: 12px; font-weight: 800; color: #1e293b;">${room.category}</span>
          </div>

        </div>

        <!-- Right Box: Base Tariff & Status -->
        <div style="background: #ffffff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 14px 18px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: center; align-items: flex-end; gap: 6px;">
          <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
            Base Tariff
          </div>
          <div style="font-family: var(--font-mono, monospace); font-size: 22px; font-weight: 900; color: #4f46e5; margin: 2px 0;">
            ₹ ${Number(room.rate).toLocaleString()}
            <span style="font-size: 12px; font-weight: 600; color: #94a3b8;">/ night</span>
          </div>
          <div style="display: inline-flex; align-items: center; gap: 6px; background: ${statusBg}; color: ${statusColor}; border: 1px solid ${statusBorder}; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 700;">
            <span style="width: 6px; height: 6px; border-radius: 50%; background: ${statusDot}; display: inline-block;"></span>
            ${statusText}
          </div>
        </div>

      </div>

      <!-- Quick Info Tiles -->
      <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 16px;">
        <!-- Bedding Tile -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 14px;">
          <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
            <i class="fa-solid fa-bed"></i>
          </div>
          <div>
            <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Bedding Configuration</div>
            <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-top: 2px;">
              ${room.bedding_config || 'Standard Configuration'}
            </div>
          </div>
        </div>

        <!-- Pax Capacity Tile -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; align-items: center; gap: 14px;">
          <div style="width: 40px; height: 40px; border-radius: 10px; background: #fdf4ff; color: #c026d3; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
            <i class="fa-solid fa-users"></i>
          </div>
          <div>
            <div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Pax / Guest Capacity</div>
            <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-top: 2px;">
              ${room.pax_capacity || 'Max 2 Adults'}
            </div>
          </div>
        </div>
      </div>

      <!-- Configured Amenities Card -->
      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 18px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
          <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-wand-magic-sparkles" style="color: #6366f1;"></i> Configured Amenities
          </div>
          <span style="font-size: 11px; font-weight: 700; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 10px;">
            ${amenitiesList.length} Total
          </span>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
          ${amenitiesHtml}
        </div>
      </div>
    `;

    document.getElementById('view-room-body').innerHTML = content;

    const botLeft = document.getElementById('view-room-bot-left');
    if (botLeft) {
      botLeft.innerHTML = `
        <button type="button" class="btn-ui-primary" onclick="closeModal('view-room-modal'); openEditRoomModal(${room.id});" style="height: 38px; padding: 0 16px; border-radius: 8px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-pen-to-square"></i> Edit Room Asset
        </button>
      `;
    }

    openModal('view-room-modal');
  }

  function exportRoomsCSV() {
    const filtered = getFilteredRooms();
    if (!filtered.length) {
      PmsAlert.toast('No room assets to export', 'info');
      return;
    }
    let csv = '"ID","Room Number","Floor","Category","Bedding Config","Pax Capacity","Rate (INR)","Status","Amenities"\n';
    filtered.forEach(r => {
      const amns = Array.isArray(r.amenities) ? r.amenities.join('; ') : '';
      csv += `"${r.id}","${r.room_number}","${r.floor}","${r.category}","${r.bedding_config || ''}","${r.pax_capacity || ''}","${r.rate}","${r.status}","${amns}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Room_Assets_${Date.now()}.csv`;
    a.click();
    PmsAlert.toast('Rooms inventory exported to CSV!');
  }

  // Initialize on load
  window.addEventListener('DOMContentLoaded', () => {
    initSelect2();
    renderView();
    if (urlParams.get('action') === 'add') {
      openAddRoomModal();
    }
  });
</script>
@endpush
