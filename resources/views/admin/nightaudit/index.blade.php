@extends('admin.layouts.app')

@section('title', 'Night Audit History - Hotel Sagar Sonnet Admin')

@push('styles')
<style>
  /* Standard Admin Layout System */
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
    border: 1px solid var(--border-medium, #e2e8f0);
    border-radius: var(--radius-lg, 12px);
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
    color: var(--text-muted, #64748b);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 9px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: var(--radius-md, 8px);
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
  }
  .admin-nav-header:hover { background: #f1f5f9; color: var(--accent-primary, #6366f1); }
  .admin-nav-header.active {
    color: var(--accent-primary, #6366f1);
    font-weight: 900;
    background: rgba(99, 102, 241, 0.08);
    border-color: rgba(99, 102, 241, 0.2);
  }
  .admin-nav-flat {
    font-size: 11px;
    font-weight: 800;
    color: var(--text-secondary, #475569);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: var(--radius-md, 8px);
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
    background: #ffffff;
    border: 1px solid #f1f5f9;
    margin-bottom: 6px;
    text-decoration: none;
  }
  .admin-nav-flat:hover { 
    background: #f8fafc; 
    color: var(--accent-primary, #6366f1);
    border-color: var(--border-medium, #e2e8f0);
  }
  .admin-nav-flat.active {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.06)) !important;
    color: var(--accent-primary, #6366f1) !important;
    border-color: rgba(99, 102, 241, 0.3) !important;
    font-weight: 900 !important;
    box-shadow: 0 2px 6px rgba(99, 102, 241, 0.08);
  }
  .admin-sub-list {
    list-style: none;
    padding: 4px 0 4px 8px;
    margin: 4px 0 0 4px;
    border-left: 2px solid var(--border-medium, #e2e8f0);
  }
  .admin-sub-item {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary, #475569);
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
    color: var(--accent-primary, #6366f1);
    transform: translateX(3px);
  }
  .admin-sub-item.active {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(168, 85, 247, 0.08));
    color: var(--accent-primary, #6366f1);
    font-weight: 800;
    border-left: 3px solid var(--accent-primary, #6366f1);
  }

  /* Admin Content Pane */
  .admin-content-pane {
    background: #ffffff;
    border: 1px solid var(--border-medium, #e2e8f0);
    border-radius: var(--radius-lg, 12px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    padding: 24px;
    min-width: 0;
  }

  /* Header Card */
  .crud-header-card {
    background: #ffffff;
    border: 1px solid var(--border-medium, #e2e8f0);
    border-radius: var(--radius-md, 8px);
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    flex-wrap: wrap;
    gap: 12px;
  }

  /* KPI Cards Grid */
  .room-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
  }
  @media (max-width: 1200px) {
    .room-stats-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 640px) {
    .room-stats-grid { grid-template-columns: 1fr; }
  }

  .room-kpi-card {
    background: #ffffff;
    border: 1px solid var(--border-medium, #e2e8f0);
    border-radius: var(--radius-lg, 12px);
    padding: 18px 20px;
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
  .kpi-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    color: var(--text-muted, #64748b);
    letter-spacing: 0.6px;
    margin-bottom: 4px;
  }
  .kpi-value {
    font-size: 22px;
    font-weight: 900;
    font-family: var(--font-mono, monospace);
    line-height: 1.2;
    margin-bottom: 4px;
  }
  .kpi-subtext {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted, #94a3b8);
    display: flex;
    align-items: center;
    gap: 5px;
  }
  .kpi-icon-bubble {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
  }

  /* Control Toolbar & Filters */
  .control-toolbar {
    background: #f8fafc;
    border: 1px solid var(--border-medium, #e2e8f0);
    border-radius: var(--radius-md, 8px);
    padding: 12px 16px;
    margin-bottom: 16px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }
  .filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    flex: 1;
  }
  .date-field-chip {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 0 10px;
    height: 38px;
    gap: 8px;
    transition: all 0.2s;
  }
  .date-field-chip:focus-within {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
  }
  .date-chip-label {
    font-size: 11px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
  }
  .date-chip-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: 12px;
    font-weight: 600;
    color: #1e293b;
    font-family: inherit;
    cursor: pointer;
  }

  .crud-search-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    flex: 1;
    min-width: 220px;
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
    width: 100%;
    height: 38px;
    padding: 8px 14px 8px 34px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    color: #0f172a;
    transition: all 0.2s ease;
    outline: none;
  }
  .crud-search-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
  }

  /* Badge Tags */
  .badge-tag {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    line-height: 1;
    gap: 5px;
    white-space: nowrap;
  }
  .badge-tag.green { 
    background: rgba(16, 185, 129, 0.12); 
    color: #059669; 
    border: 1px solid rgba(16, 185, 129, 0.3); 
  }
  .badge-tag.purple { 
    background: rgba(168, 85, 247, 0.12); 
    color: #7c3aed; 
    border: 1px solid rgba(168, 85, 247, 0.3); 
  }
  .badge-tag.amber { 
    background: rgba(245, 158, 11, 0.12); 
    color: #d97706; 
    border: 1px solid rgba(245, 158, 11, 0.3); 
  }
  .badge-tag.blue { 
    background: rgba(59, 130, 246, 0.12); 
    color: #2563eb; 
    border: 1px solid rgba(59, 130, 246, 0.3); 
  }

  /* Occupancy Pill */
  .occupancy-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 800;
    font-family: var(--font-mono, monospace);
    letter-spacing: 0.3px;
    white-space: nowrap;
    min-width: 78px;
    justify-content: center;
  }
  .occupancy-pill.occupancy-high {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #86efac;
  }
  .occupancy-pill.occupancy-mid {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
  }
  .occupancy-pill.occupancy-low {
    background: #e0e7ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
  }

  /* Action Buttons */
  .btn-action-view {
    background: rgba(99, 102, 241, 0.1);
    color: #4f46e5;
    border: 1px solid rgba(99, 102, 241, 0.25);
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
    text-decoration: none;
  }
  .btn-action-view:hover {
    background: #4f46e5;
    color: #ffffff;
    border-color: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(79, 70, 229, 0.25);
  }

  .btn-action-print {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.25);
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
    text-decoration: none;
  }
  .btn-action-print:hover {
    background: #059669;
    color: #ffffff;
    border-color: #059669;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(16, 185, 129, 0.25);
  }

  .btn-action-external {
    background: rgba(100, 116, 139, 0.1);
    color: #475569;
    border: 1px solid rgba(100, 116, 139, 0.25);
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s;
    text-decoration: none;
  }
  .btn-action-external:hover {
    background: #334155;
    color: #ffffff;
    border-color: #334155;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(51, 65, 85, 0.25);
  }

  /* Table Customization */
  .pms-table-box {
    border-radius: var(--radius-md, 8px);
    background: #ffffff;
    border: 1px solid var(--border-medium, #e2e8f0);
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  }
  .pms-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
  }
  .pms-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 10.5px;
    letter-spacing: 0.6px;
    padding: 12px 14px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
  }
  .pms-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    color: #1e293b;
  }
  .pms-table tbody tr {
    transition: background-color 0.15s ease;
  }
  .pms-table tbody tr:hover {
    background-color: #f8faff;
  }

  /* Dossier Modal */
  .dossier-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
    margin-bottom: 16px;
  }
  .dossier-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }
  .dossier-sec-title {
    font-size: 11px;
    font-weight: 800;
    color: #4f46e5;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 7px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 8px;
  }
  .dossier-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    font-size: 12px;
    border-bottom: 1px dashed #f1f5f9;
  }
  .dossier-label {
    color: #64748b;
    font-weight: 600;
  }
  .dossier-val {
    font-weight: 800;
    color: #0f172a;
    font-family: var(--font-mono, monospace);
  }

  /* Dossier Tabs & Tables */
  .dossier-nav-tabs {
    display: flex;
    gap: 4px;
    border-bottom: 2px solid #e2e8f0;
    padding: 0 16px;
    background: #ffffff;
    overflow-x: auto;
  }
  .dossier-tab-btn {
    padding: 10px 14px;
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    border: none;
    background: transparent;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
  }
  .dossier-tab-btn:hover {
    color: #4f46e5;
  }
  .dossier-tab-btn.active {
    color: #4f46e5;
    font-weight: 800;
    border-bottom-color: #4f46e5;
  }
  .dossier-tab-pane {
    display: none;
  }
  .dossier-tab-pane.active {
    display: block;
  }
  .dossier-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
  }
  .dossier-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 800;
    text-transform: uppercase;
    font-size: 10.5px;
    letter-spacing: 0.3px;
    padding: 9px 12px;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
    white-space: nowrap;
  }
  .dossier-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
  }
  .dossier-table tbody tr:hover {
    background: #f8fafc;
  }

  /* Print Styles */
  @media print {
    body * { visibility: hidden !important; }
    #printable-audit-container, #printable-audit-container * { visibility: visible !important; }
    #printable-audit-container {
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      width: 100% !important;
      background: #ffffff !important;
      display: block !important;
      padding: 24px !important;
      color: #000000 !important;
    }
  }
</style>
@endpush

@section('content')
<div class="pms-page-container">

  <!-- Top App Breadcrumb Header -->
  <div class="pms-page-header">
    <div class="pms-header-title-block">
      <div class="pms-status-indicator"></div>
      <div>
        <h1 class="pms-page-title">Administrator • Master Data Management</h1>
        <div class="pms-page-subtitle">Configure, review, and inspect permanent archives of daily hotel night audits.</div>
      </div>
    </div>
    <div style="display: flex; gap: 10px;">
      <a href="{{ route('frontoffice.nightaudit') }}" class="btn-ui-secondary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 7px; height: 38px; padding: 0 16px; font-size: 12px; font-weight: 700; border-radius: 8px;" title="Open Front Desk Night Audit Screen">
        <i class="fa-solid fa-table-cells-large" style="color: var(--accent-primary);"></i> Front Desk Live Audit
      </a>
    </div>
  </div>

  <div class="admin-layout">

    <!-- Administrator Master Sidebar -->
    @include('admin.includes.sidebar')

    <!-- Main Content Pane -->
    <section class="admin-content-pane">

      <!-- Module Top Card -->
      <div class="crud-header-card">
        <div>
          <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary, #0f172a); margin: 0; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-moon" style="color: #8b5cf6;"></i> Night Audit History & Reports
            </h2>
            <span class="badge-tag purple" id="audit-count">
              <i class="fa-solid fa-database"></i> {{ $audits->total() }} Archived Audits
            </span>
          </div>
          <div style="font-size: 12px; color: var(--text-secondary, #64748b); margin-top: 4px;">
            Permanent immutable archives of daily night audits, revenue reconciliations, and manager sign-offs.
          </div>
        </div>

       
      </div>

      <!-- Top 4 KPI Metrics Row -->
      <div class="room-stats-grid">
        <!-- Card 1: Completed Audits -->
        <div class="room-kpi-card">
          <div class="room-kpi-bar" style="background: linear-gradient(90deg, #8b5cf6, #a855f7);"></div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
              <div class="kpi-label">Completed Audits</div>
              <div class="kpi-value" style="color: #7c3aed;">{{ $totalAuditsCount }}</div>
              <div class="kpi-subtext">
                <i class="fa-solid fa-lock" style="color: #10b981; font-size: 10px;"></i> Archived Locked Audits
              </div>
            </div>
            <div class="kpi-icon-bubble" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
              <i class="fa-solid fa-calendar-check"></i>
            </div>
          </div>
        </div>

        <!-- Card 2: Audited Revenue -->
        <div class="room-kpi-card">
          <div class="room-kpi-bar" style="background: linear-gradient(90deg, #6366f1, #3b82f6);"></div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
              <div class="kpi-label">Audited Revenue</div>
              <div class="kpi-value" style="color: #4338ca;">₹ {{ number_format($totalAuditedRevenue, 2) }}</div>
              <div class="kpi-subtext">
                <i class="fa-solid fa-chart-line" style="color: #6366f1; font-size: 10px;"></i> Total Hotel Sales Recorded
              </div>
            </div>
            <div class="kpi-icon-bubble" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
              <i class="fa-solid fa-coins"></i>
            </div>
          </div>
        </div>

        <!-- Card 3: Audited Collections -->
        <div class="room-kpi-card">
          <div class="room-kpi-bar" style="background: linear-gradient(90deg, #10b981, #059669);"></div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
              <div class="kpi-label">Audited Collections</div>
              <div class="kpi-value" style="color: #059669;">₹ {{ number_format($totalAuditedPayments, 2) }}</div>
              <div class="kpi-subtext">
                <i class="fa-solid fa-cash-register" style="color: #10b981; font-size: 10px;"></i> Total Payments Reconciled
              </div>
            </div>
            <div class="kpi-icon-bubble" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
              <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
          </div>
        </div>

        <!-- Card 4: Average Occupancy -->
        <div class="room-kpi-card">
          <div class="room-kpi-bar" style="background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
              <div class="kpi-label">Avg Occupancy</div>
              <div class="kpi-value" style="color: #d97706;">{{ number_format($avgOccupancy, 1) }}%</div>
              <div class="kpi-subtext">
                <i class="fa-solid fa-percent" style="color: #f59e0b; font-size: 10px;"></i> Overall Historical Average
              </div>
            </div>
            <div class="kpi-icon-bubble" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
              <i class="fa-solid fa-door-open"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Control Toolbar & Filter Form -->
      <div class="control-toolbar">
        <form action="{{ route('admin.nightaudit.index') }}" method="GET" class="filter-group">
          
          <!-- From Date Chip -->
          <div class="date-field-chip" title="Filter From Audit Date">
            <span class="date-chip-label">
              <i class="fa-regular fa-calendar" style="color: #6366f1;"></i> From:
            </span>
            <input type="date" name="from_date" value="{{ $fromDate }}" class="date-chip-input">
          </div>

          <!-- To Date Chip -->
          <div class="date-field-chip" title="Filter To Audit Date">
            <span class="date-chip-label">
              <i class="fa-regular fa-calendar-check" style="color: #6366f1;"></i> To:
            </span>
            <input type="date" name="to_date" value="{{ $toDate }}" class="date-chip-input">
          </div>

          <!-- Search Input -->
          <div class="crud-search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search auditor, remarks, date..." class="crud-search-input">
          </div>

          <!-- Submit Filter Button -->
          <button type="submit" class="btn-ui-primary" style="height: 38px; padding: 0 16px; font-size: 12px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-filter"></i> Filter
          </button>

          <!-- Reset Filter Button -->
          @if($fromDate || $toDate || $search)
            <a href="{{ route('admin.nightaudit.index') }}" class="btn-ui-secondary" style="height: 38px; padding: 0 14px; font-size: 12px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;" title="Clear Filters">
              <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
          @endif
        </form>

        <div style="font-size: 12px; font-weight: 700; color: #64748b; white-space: nowrap;">
          Showing <strong style="color: #0f172a;">{{ $audits->count() }}</strong> of <strong style="color: #0f172a;">{{ $audits->total() }}</strong> Audit Records
        </div>
      </div>

      <!-- Master Audits Table Card -->
      <div class="pms-table-box">
        <table class="pms-table">
          <thead>
            <tr>
              <th style="width: 50px; text-align: center;">SL</th>
              <th>Audit Date</th>
              <th>Completion Time</th>
              <th>Auditor Name</th>
              <th style="text-align: center;">Occupancy</th>
              <th style="text-align: right;">Collections (₹)</th>
              <th style="text-align: right;">Room Revenue (₹)</th>
              <th style="text-align: right;">Total Revenue (₹)</th>
              <th style="text-align: center;">Status</th>
              <th style="text-align: center; width: 70px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($audits as $idx => $a)
              @php
                $sl = ($audits->currentPage() - 1) * $audits->perPage() + $idx + 1;
                $occRate = (float)$a->occupancy_rate;
                $occClass = $occRate >= 70 ? 'occupancy-high' : ($occRate >= 30 ? 'occupancy-mid' : 'occupancy-low');
              @endphp
              <tr>
                <!-- SL -->
                <td style="text-align: center; font-weight: 800; color: #64748b; font-size: 11px; font-family: var(--font-mono, monospace);">
                  #{{ sprintf('%02d', $sl) }}
                </td>

                <!-- Audit Date -->
                <td>
                  <div style="font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 7px;">
                    <i class="fa-regular fa-calendar-check" style="color: #6366f1;"></i>
                    {{ $a->audit_date->format('d M Y') }}
                  </div>
                  <div style="font-size: 10px; color: #94a3b8; font-weight: 600; margin-left: 21px;">
                    {{ $a->audit_date->format('l') }}
                  </div>
                </td>

                <!-- Completion Time -->
                <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #64748b;">
                  @if($a->completed_at)
                    <span style="display: inline-flex; align-items: center; gap: 5px;">
                      <i class="fa-regular fa-clock" style="color: #94a3b8; font-size: 10px;"></i>
                      {{ $a->completed_at->format('h:i A') }}
                    </span>
                  @else
                    <span style="color: #cbd5e1;">N/A</span>
                  @endif
                </td>

                <!-- Auditor Name -->
                <td>
                  <div style="font-weight: 700; color: #334155; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-user-check" style="color: #10b981; font-size: 11px;"></i>
                    <span>{{ $a->auditor_name ?: ($a->auditor?->name ?? 'Front Office Auditor') }}</span>
                  </div>
                </td>

                <!-- Occupancy Pill -->
                <td style="text-align: center;">
                  <span class="occupancy-pill {{ $occClass }}" title="{{ $a->occupied_rooms }} of {{ $a->total_rooms }} rooms occupied">
                    <i class="fa-solid fa-bed"></i> {{ number_format($a->occupancy_rate, 1) }}%
                  </span>
                </td>

                <!-- Collections -->
                <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 12px; color: #059669;" title="Cash: ₹{{ number_format($a->cash_collection, 2) }} • UPI: ₹{{ number_format($a->upi_collection, 2) }} • Card: ₹{{ number_format($a->card_collection, 2) }} • Bank: ₹{{ number_format($a->bank_collection, 2) }}">
                  ₹ {{ number_format($a->total_payments, 2) }}
                </td>

                <!-- Room Revenue -->
                <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 12px; color: #4338ca;">
                  ₹ {{ number_format($a->room_revenue, 2) }}
                </td>

                <!-- Total Revenue -->
                <td style="text-align: right; font-weight: 900; font-family: var(--font-mono, monospace); font-size: 12.5px; color: #7c3aed;">
                  ₹ {{ number_format($a->total_revenue, 2) }}
                </td>

                <!-- Status -->
                <td style="text-align: center;">
                  <span class="badge-tag green">
                    <i class="fa-solid fa-lock"></i> COMPLETED
                  </span>
                </td>

                <!-- Actions (View Only) -->
                <td style="text-align: center;">
                  <button type="button" class="btn-action-view" onclick="viewAuditDossier({{ $a->id }})" title="View Details">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" style="text-align: center; padding: 48px 20px; color: #94a3b8;">
                  <div style="width: 56px; height: 56px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 22px; color: #94a3b8;">
                    <i class="fa-solid fa-calendar-xmark"></i>
                  </div>
                  <div style="font-size: 14px; font-weight: 800; color: #334155;">No night audit records found</div>
                  <div style="font-size: 12px; margin-top: 4px; color: #64748b;">
                    Try adjusting your date range filters or search keywords above.
                  </div>
                  @if($fromDate || $toDate || $search)
                    <div style="margin-top: 14px;">
                      <a href="{{ route('admin.nightaudit.index') }}" class="btn-ui-secondary" style="height: 32px; padding: 0 14px; font-size: 11px; font-weight: 700; border-radius: 6px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;">
                        <i class="fa-solid fa-rotate-left"></i> Clear All Filters
                      </a>
                    </div>
                  @endif
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        @if($audits->hasPages())
          <div style="padding: 14px 20px; border-top: 1px solid #f1f5f9; background: #ffffff; display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: 11px; color: #64748b; font-weight: 600;">
              Showing {{ $audits->firstItem() }} to {{ $audits->lastItem() }} of {{ $audits->total() }} entries
            </div>
            <div>
              {{ $audits->links() }}
            </div>
          </div>
        @endif
      </div>

    </section>
  </div>
</div>

<!-- AUDIT DOSSIER MODAL -->
<div class="modal-backdrop" id="audit-dossier-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(5px); z-index: 99999; align-items: center; justify-content: center;">
  <div style="background: #ffffff; width: 1050px; max-width: 96vw; max-height: 92vh; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; display: flex; flex-direction: column;">
    
    <!-- Modal Top Header -->
    <div style="background: #4f46e5; color: #ffffff; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
      <div style="display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-moon" style="font-size: 16px;"></i>
        <div>
          <h3 id="md-title" style="margin: 0; font-size: 15px; font-weight: 800; color: #ffffff;">Night Audit Details</h3>
          <div id="md-subtitle" style="font-size: 11px; opacity: 0.85; margin-top: 2px;"></div>
        </div>
      </div>
      <button type="button" onclick="closeAuditModal()" style="background: transparent; border: none; color: #ffffff; font-size: 22px; cursor: pointer; line-height: 1;">&times;</button>
    </div>

    <!-- Modal Body -->
    <div id="md-content" style="padding: 0; overflow: hidden; background: #f8fafc; flex: 1; display: flex; flex-direction: column;">
      <div style="text-align: center; padding: 40px; color: #64748b;">
        <i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; color: #6366f1;"></i>
        <div style="margin-top: 10px; font-weight: 700;">Loading...</div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div style="padding: 12px 20px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; align-items: center; gap: 8px;">
      <button type="button" id="btn-print-dossier" class="btn-ui-primary" style="height: 34px; padding: 0 14px; font-size: 12px; font-weight: 700; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fa-solid fa-print"></i> Print
      </button>
      <button type="button" onclick="closeAuditModal()" class="btn-ui-secondary" style="height: 34px; padding: 0 14px; font-size: 12px; font-weight: 700; border-radius: 6px;">
        Close
      </button>
    </div>

  </div>
</div>

<!-- HIDDEN PRINT AREA -->
<div id="printable-audit-container" style="display: none;"></div>

@endsection

@push('scripts')
<script>
  let currentLoadedAudit = null;

  function viewAuditDossier(auditId) {
    const modal = document.getElementById('audit-dossier-modal');
    const content = document.getElementById('md-content');
    const title = document.getElementById('md-title');
    const subtitle = document.getElementById('md-subtitle');
    const printBtn = document.getElementById('btn-print-dossier');

    if (modal) modal.style.display = 'flex';
    content.innerHTML = `
      <div style="text-align: center; padding: 40px; color: #64748b;">
        <i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; color: #6366f1;"></i>
        <div style="margin-top: 10px; font-weight: 700; font-size: 13px;">Loading...</div>
      </div>
    `;

    fetch(`{{ url('/admin/nightaudit') }}/${auditId}`)
      .then(res => res.json())
      .then(data => {
        if (!data.success || !data.audit) {
          content.innerHTML = `<div style="text-align: center; padding: 30px; color: #ef4444; font-weight: 700;">Failed to load audit record.</div>`;
          return;
        }

        const a = data.audit;
        currentLoadedAudit = a;

        const auditDateFormatted = a.audit_date ? new Date(a.audit_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'N/A';
        const auditDay = a.audit_date ? new Date(a.audit_date).toLocaleDateString('en-US', { weekday: 'long' }) : '';
        const completedTime = a.completed_at ? new Date(a.completed_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : 'N/A';
        const auditorName = a.auditor_name || (a.auditor ? a.auditor.name : 'Auditor');

        title.textContent = `Night Audit Details • ${auditDateFormatted}`;
        subtitle.textContent = `${auditorName} • ${completedTime}`;

        const snapshot = a.audit_snapshot || {};
        const payments = snapshot.payments_list || [];
        const occupiedRooms = snapshot.occupied_rooms || [];
        const hkHistories = snapshot.housekeeping_histories || [];
        const opHistories = snapshot.operational_histories || [];
        const guestsList = snapshot.guests_list || [];
        const unsettled = snapshot.unsettled_summary || {};

        content.innerHTML = `
          <!-- Fixed Modal Navigation Tabs -->
          <div class="dossier-nav-tabs">
            <button type="button" class="dossier-tab-btn active" onclick="switchDossierTab('md-tab-summary', this)">
              <i class="fa-solid fa-chart-pie"></i> Summary &amp; Reconciliations
            </button>
            <button type="button" class="dossier-tab-btn" onclick="switchDossierTab('md-tab-payments', this)">
              <i class="fa-solid fa-receipt"></i> Payments (${payments.length})
            </button>
            <button type="button" class="dossier-tab-btn" onclick="switchDossierTab('md-tab-rooms', this)">
              <i class="fa-solid fa-bed"></i> Occupied Rooms (${occupiedRooms.length})
            </button>
            <button type="button" class="dossier-tab-btn" onclick="switchDossierTab('md-tab-hk', this)">
              <i class="fa-solid fa-broom"></i> Housekeeping (${hkHistories.length})
            </button>
            <button type="button" class="dossier-tab-btn" onclick="switchDossierTab('md-tab-op', this)">
              <i class="fa-solid fa-sliders"></i> Operational (${opHistories.length})
            </button>
            <button type="button" class="dossier-tab-btn" onclick="switchDossierTab('md-tab-guests', this)">
              <i class="fa-solid fa-users"></i> Guests (${guestsList.length})
            </button>
          </div>

          <!-- Scrollable Tab Panes Container -->
          <div style="overflow-y: auto; padding: 18px 20px; flex: 1;">

            <!-- TAB 1: SUMMARY & RECONCILIATIONS -->
            <div id="md-tab-summary" class="dossier-tab-pane active">
              
              <!-- Top Key Data Strip -->
              <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 14px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; font-size: 12px;">
                <div>
                  <div style="font-size: 11px; font-weight: 700; color: #64748b;">Audit Date</div>
                  <div style="font-size: 13px; font-weight: 800; color: #0f172a; margin-top: 2px;">${auditDateFormatted} <span style="font-size: 11px; color: #64748b; font-weight: 600;">(${auditDay})</span></div>
                </div>
                <div>
                  <div style="font-size: 11px; font-weight: 700; color: #64748b;">Completion Time</div>
                  <div style="font-size: 13px; font-weight: 800; color: #0f172a; margin-top: 2px; font-family: var(--font-mono, monospace);">${completedTime}</div>
                </div>
                <div>
                  <div style="font-size: 11px; font-weight: 700; color: #64748b;">Auditor Name</div>
                  <div style="font-size: 13px; font-weight: 800; color: #0f172a; margin-top: 2px;">${escapeHtml(auditorName)}</div>
                </div>
                <div>
                  <div style="font-size: 11px; font-weight: 700; color: #64748b;">Status</div>
                  <div style="margin-top: 2px;">
                    <span class="badge-tag green" style="font-size: 10px; padding: 2px 8px; font-weight: 800;">
                      <i class="fa-solid fa-lock"></i> ${escapeHtml(a.status || 'COMPLETED')}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Collections & Revenue 2-Columns Grid -->
              <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px; margin-bottom: 14px;">
                
                <!-- Collections Box -->
                <div class="dossier-section">
                  <div class="dossier-sec-title">
                    <i class="fa-solid fa-cash-register"></i> Collections Breakdown
                  </div>
                  <div class="dossier-row"><span class="dossier-label">Cash:</span><span class="dossier-val" style="color: #059669;">₹ ${fmt(a.cash_collection)}</span></div>
                  <div class="dossier-row"><span class="dossier-label">UPI / Digital:</span><span class="dossier-val" style="color: #4338ca;">₹ ${fmt(a.upi_collection)}</span></div>
                  <div class="dossier-row"><span class="dossier-label">Card / POS:</span><span class="dossier-val" style="color: #b45309;">₹ ${fmt(a.card_collection)}</span></div>
                  <div class="dossier-row"><span class="dossier-label">Bank Transfer:</span><span class="dossier-val" style="color: #0284c7;">₹ ${fmt(a.bank_collection)}</span></div>
                  <div class="dossier-row" style="font-weight: 800; border-top: 1px solid #e2e8f0; margin-top: 4px; padding-top: 8px;">
                    <span class="dossier-label" style="font-weight: 800; color: #0f172a;">Total Collections:</span>
                    <span class="dossier-val" style="color: #059669; font-weight: 900; font-size: 13.5px;">₹ ${fmt(a.total_payments)}</span>
                  </div>
                </div>

                <!-- Revenue Box -->
                <div class="dossier-section">
                  <div class="dossier-sec-title">
                    <i class="fa-solid fa-chart-line"></i> Revenue Reconciliation
                  </div>
                  <div class="dossier-row"><span class="dossier-label">Room Tariff Revenue:</span><span class="dossier-val" style="color: #4338ca;">₹ ${fmt(a.room_revenue)}</span></div>
                  ${Number(a.food_revenue || 0) > 0 ? `<div class="dossier-row"><span class="dossier-label">Food / Meal Plans:</span><span class="dossier-val">₹ ${fmt(a.food_revenue)}</span></div>` : ''}
                  <div class="dossier-row"><span class="dossier-label">Tax / GST:</span><span class="dossier-val">₹ ${fmt(a.tax_amount)}</span></div>
                  <div class="dossier-row"><span class="dossier-label">Discount:</span><span class="dossier-val" style="color: #ef4444;">- ₹ ${fmt(a.discount_amount)}</span></div>
                  <div class="dossier-row" style="font-weight: 800; border-top: 1px solid #e2e8f0; margin-top: 4px; padding-top: 8px;">
                    <span class="dossier-label" style="font-weight: 800; color: #0f172a;">Total Revenue:</span>
                    <span class="dossier-val" style="color: #7c3aed; font-weight: 900; font-size: 13.5px;">₹ ${fmt(a.total_revenue)}</span>
                  </div>
                </div>

              </div>

              <!-- Rooms & Occupancy KPI Grid -->
              <div class="dossier-section" style="margin-bottom: 14px;">
                <div class="dossier-sec-title">
                  <i class="fa-solid fa-bed"></i> Rooms &amp; Occupancy
                </div>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; text-align: center; margin-top: 8px;">
                  <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b;">Occupancy Rate</div>
                    <div style="font-size: 15px; font-weight: 900; color: #4338ca; font-family: var(--font-mono, monospace); margin-top: 3px;">${Number(a.occupancy_rate || 0).toFixed(1)}%</div>
                  </div>
                  <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b;">Occupied Rooms</div>
                    <div style="font-size: 15px; font-weight: 900; color: #0f172a; font-family: var(--font-mono, monospace); margin-top: 3px;">${a.occupied_rooms || 0} / ${a.total_rooms || 0}</div>
                  </div>
                  <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b;">Vacant Rooms</div>
                    <div style="font-size: 15px; font-weight: 900; color: #059669; font-family: var(--font-mono, monospace); margin-top: 3px;">${a.vacant_rooms || 0}</div>
                  </div>
                  <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b;">ADR / RevPAR</div>
                    <div style="font-size: 13px; font-weight: 800; color: #334155; font-family: var(--font-mono, monospace); margin-top: 3px;">₹ ${Number(a.adr || 0).toFixed(0)} / ₹ ${Number(a.revpar || 0).toFixed(0)}</div>
                  </div>
                </div>
              </div>

              <!-- Unsettled Folio Balances Box -->
              <div class="dossier-section" style="margin-bottom: 14px;">
                <div class="dossier-sec-title">
                  <i class="fa-solid fa-clock"></i> Folio Reconciliations
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 8px; text-align: center;">
                  <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 10px;">
                    <div style="font-size: 11px; font-weight: 700; color: #b91c1c;">Outstanding Unsettled Due</div>
                    <div style="font-size: 15px; font-weight: 900; color: #b91c1c; font-family: var(--font-mono, monospace); margin-top: 3px;">₹ ${fmt(unsettled.total_unsettled_amount || 0)}</div>
                  </div>
                  <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                    <div style="font-size: 11px; font-weight: 700; color: #64748b;">Unsettled Folios</div>
                    <div style="font-size: 15px; font-weight: 900; color: #e11d48; font-family: var(--font-mono, monospace); margin-top: 3px;">${unsettled.unsettled_bookings_count || 0} Folio(s)</div>
                  </div>
                  <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px;">
                    <div style="font-size: 11px; font-weight: 700; color: #15803d;">Settled Folios</div>
                    <div style="font-size: 15px; font-weight: 900; color: #16a34a; font-family: var(--font-mono, monospace); margin-top: 3px;">${unsettled.settled_bookings_count || 0} Folio(s)</div>
                  </div>
                </div>
              </div>

              <!-- Remarks -->
              ${a.remarks ? `
                <div class="dossier-section">
                  <div class="dossier-sec-title">
                    <i class="fa-solid fa-comment-dots"></i> Auditor Remarks
                  </div>
                  <div style="font-size: 12px; color: #334155; line-height: 1.5;">${escapeHtml(a.remarks)}</div>
                </div>
              ` : ''}

            </div>

            <!-- TAB 2: PAYMENTS LIST -->
            <div id="md-tab-payments" class="dossier-tab-pane">
              <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow-x: auto;">
                <table class="dossier-table">
                  <thead>
                    <tr>
                      <th style="width: 40px; text-align: center;">SL</th>
                      <th>Time</th>
                      <th>Guest Name</th>
                      <th>Room</th>
                      <th>Folio #</th>
                      <th style="text-align: right;">Total Bill</th>
                      <th style="text-align: right;">Paid</th>
                      <th style="text-align: right;">Remaining</th>
                      <th style="text-align: center;">Status</th>
                      <th>Mode</th>
                      <th style="text-align: right;">Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${payments.length > 0 ? payments.map((p, idx) => `
                      <tr>
                        <td style="text-align: center; font-weight: 800; color: #64748b; font-family: var(--font-mono, monospace);">${idx + 1}</td>
                        <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #475569;">${escapeHtml(p.time || '-')}</td>
                        <td style="font-weight: 700; color: #0f172a;">${escapeHtml(p.guest_name || 'Guest')}</td>
                        <td><span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">${escapeHtml(p.room_number || '-')}</span></td>
                        <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #475569;">${escapeHtml(p.folio_number || '-')}</td>
                        <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 600;">₹ ${fmt(p.total_bill)}</td>
                        <td style="text-align: right; font-family: var(--font-mono, monospace); color: #059669; font-weight: 600;">₹ ${fmt(p.paid_amount)}</td>
                        <td style="text-align: right; font-family: var(--font-mono, monospace); color: ${p.remaining_amount > 0 ? '#b91c1c' : '#059669'}; font-weight: 700;">₹ ${fmt(p.remaining_amount)}</td>
                        <td style="text-align: center;">
                          ${p.is_settled ? `
                            <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                              <i class="fa-solid fa-check"></i> Settled
                            </span>
                          ` : `
                            <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;">
                              <i class="fa-solid fa-clock"></i> Unsettled
                            </span>
                          `}
                        </td>
                        <td><span class="mode-pill ${String(p.mode).toLowerCase()}">${escapeHtml(p.mode || 'Cash')}</span></td>
                        <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); color: #059669; font-size: 12.5px;">₹ ${fmt(p.amount)}</td>
                      </tr>
                    `).join('') : `
                      <tr>
                        <td colspan="11" style="text-align: center; padding: 32px; color: #94a3b8;">No payment records recorded for this audit date.</td>
                      </tr>
                    `}
                  </tbody>
                  ${payments.length > 0 ? `
                    <tfoot>
                      <tr style="background: #f8fafc; font-weight: 800;">
                        <td colspan="10" style="text-align: right; text-transform: uppercase; font-size: 11px; color: #64748b; padding: 10px 12px;">Total Collections:</td>
                        <td style="text-align: right; font-size: 13px; font-family: var(--font-mono, monospace); color: #059669; padding: 10px 12px;">₹ ${fmt(a.total_payments)}</td>
                      </tr>
                    </tfoot>
                  ` : ''}
                </table>
              </div>
            </div>

            <!-- TAB 3: OCCUPIED ROOMS -->
            <div id="md-tab-rooms" class="dossier-tab-pane">
              <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow-x: auto;">
                <table class="dossier-table">
                  <thead>
                    <tr>
                      <th style="width: 40px; text-align: center;">SL</th>
                      <th>Room</th>
                      <th>Category</th>
                      <th>Floor</th>
                      <th>Guest &amp; Folio</th>
                      <th>Check-in</th>
                      <th>Check-out</th>
                      <th style="text-align: center;">Operational Status</th>
                      <th style="text-align: center;">Housekeeping</th>
                      <th style="text-align: right;">Rate / Day</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${occupiedRooms.length > 0 ? occupiedRooms.map((rm, idx) => `
                      <tr>
                        <td style="text-align: center; font-weight: 800; color: #64748b; font-family: var(--font-mono, monospace);">${idx + 1}</td>
                        <td><span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">${escapeHtml(rm.room_number)}</span></td>
                        <td style="font-weight: 600; color: #1e293b;">${escapeHtml(rm.category || 'Standard')}</td>
                        <td style="font-size: 11px; color: #64748b;">${escapeHtml(rm.floor || 'Floor 1')}</td>
                        <td>
                          <div style="font-weight: 700; color: #0f172a;">${escapeHtml(rm.guest_name || 'Guest')}</div>
                          <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Folio #${escapeHtml(rm.folio_number || '-')}</div>
                        </td>
                        <td style="font-size: 11px; color: #475569;">${escapeHtml(rm.checkin_date || '-')}</td>
                        <td style="font-size: 11px; color: #475569;">${escapeHtml(rm.checkout_date || '-')}</td>
                        <td style="text-align: center;">
                          <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">
                            ${escapeHtml(rm.operational_status || 'Active')}
                          </span>
                        </td>
                        <td style="text-align: center;">
                          <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                            ${escapeHtml(rm.housekeeping_status || 'Clean')}
                          </span>
                        </td>
                        <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); color: #1e293b;">₹ ${fmt(rm.day_rate)}</td>
                      </tr>
                    `).join('') : `
                      <tr>
                        <td colspan="10" style="text-align: center; padding: 32px; color: #94a3b8;">No occupied rooms recorded on this date.</td>
                      </tr>
                    `}
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 4: HOUSEKEEPING HISTORY -->
            <div id="md-tab-hk" class="dossier-tab-pane">
              <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow-x: auto;">
                <table class="dossier-table">
                  <thead>
                    <tr>
                      <th style="width: 40px; text-align: center;">SL</th>
                      <th style="width: 120px;">Time / Date</th>
                      <th>Room</th>
                      <th>Category</th>
                      <th>Floor</th>
                      <th style="text-align: center;">Housekeeping State</th>
                      <th style="text-align: center;">Task Status</th>
                      <th>Start Time</th>
                      <th>Completion Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${hkHistories.length > 0 ? hkHistories.map((hk, idx) => `
                      <tr>
                        <td style="text-align: center; font-weight: 800; color: #64748b; font-family: var(--font-mono, monospace);">${idx + 1}</td>
                        <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #475569;">
                          ${escapeHtml(hk.time || '-')}
                          <span style="display: block; font-size: 10px; color: #94a3b8;">${escapeHtml(hk.date || '')}</span>
                        </td>
                        <td><span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">${escapeHtml(hk.room_number)}</span></td>
                        <td style="font-weight: 600; color: #1e293b;">${escapeHtml(hk.category || 'Standard')}</td>
                        <td style="font-size: 11px; color: #64748b;">${escapeHtml(hk.floor || 'Floor 1')}</td>
                        <td style="text-align: center;">
                          <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                            <i class="fa-solid fa-broom"></i> ${escapeHtml(hk.housekeeping_status || 'Clean')}
                          </span>
                        </td>
                        <td style="text-align: center;">
                          <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">
                            ${escapeHtml(hk.status || 'Complete')}
                          </span>
                        </td>
                        <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #475569;">${escapeHtml(hk.start_time || '-')}</td>
                        <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #475569;">${escapeHtml(hk.completion_time || '-')}</td>
                      </tr>
                    `).join('') : `
                      <tr>
                        <td colspan="9" style="text-align: center; padding: 32px; color: #94a3b8;">No housekeeping events recorded on this date.</td>
                      </tr>
                    `}
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 5: OPERATIONAL HISTORY -->
            <div id="md-tab-op" class="dossier-tab-pane">
              <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow-x: auto;">
                <table class="dossier-table">
                  <thead>
                    <tr>
                      <th style="width: 40px; text-align: center;">SL</th>
                      <th style="width: 120px;">Time / Date</th>
                      <th>Room</th>
                      <th>Category</th>
                      <th>Floor</th>
                      <th style="text-align: center;">Operational Status</th>
                      <th style="text-align: center;">Room Model State</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${opHistories.length > 0 ? opHistories.map((op, idx) => `
                      <tr>
                        <td style="text-align: center; font-weight: 800; color: #64748b; font-family: var(--font-mono, monospace);">${idx + 1}</td>
                        <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #475569;">
                          ${escapeHtml(op.time || '-')}
                          <span style="display: block; font-size: 10px; color: #94a3b8;">${escapeHtml(op.date || '')}</span>
                        </td>
                        <td><span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">${escapeHtml(op.room_number)}</span></td>
                        <td style="font-weight: 600; color: #1e293b;">${escapeHtml(op.category || 'Standard')}</td>
                        <td style="font-size: 11px; color: #64748b;">${escapeHtml(op.floor || 'Floor 1')}</td>
                        <td style="text-align: center;">
                          <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                            <i class="fa-solid fa-circle-check"></i> ${escapeHtml(op.operational_status || 'Active')}
                          </span>
                        </td>
                        <td style="text-align: center;">
                          <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">
                            ${escapeHtml(op.room_status || 'Active')}
                          </span>
                        </td>
                      </tr>
                    `).join('') : `
                      <tr>
                        <td colspan="7" style="text-align: center; padding: 32px; color: #94a3b8;">No operational status changes logged on this date.</td>
                      </tr>
                    `}
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 6: TODAY'S GUESTS -->
            <div id="md-tab-guests" class="dossier-tab-pane">
              <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; overflow-x: auto;">
                <table class="dossier-table">
                  <thead>
                    <tr>
                      <th style="width: 40px; text-align: center;">SL</th>
                      <th>Folio #</th>
                      <th>Guest Details</th>
                      <th>Room</th>
                      <th>Category</th>
                      <th>Check-in</th>
                      <th>Check-out</th>
                      <th style="text-align: right;">Total Bill</th>
                      <th style="text-align: right;">Paid</th>
                      <th style="text-align: right;">Remaining</th>
                      <th style="text-align: center;">Folio Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${guestsList.length > 0 ? guestsList.map((g, idx) => `
                      <tr>
                        <td style="text-align: center; font-weight: 800; color: #64748b; font-family: var(--font-mono, monospace);">${idx + 1}</td>
                        <td style="font-family: var(--font-mono, monospace); font-size: 11px; font-weight: 700; color: #4338ca;">${escapeHtml(g.folio_number || '-')}</td>
                        <td>
                          <div style="font-weight: 700; color: #0f172a;">${escapeHtml(g.guest_name || 'Guest')}</div>
                          <div style="font-size: 10px; color: #64748b; margin-top: 1px;"><i class="fa-solid fa-phone" style="font-size: 9px;"></i> ${escapeHtml(g.mobile_no || '-')}</div>
                        </td>
                        <td><span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">${escapeHtml(g.room_number || '-')}</span></td>
                        <td style="font-weight: 600; color: #1e293b;">${escapeHtml(g.category || 'Standard')}</td>
                        <td style="font-size: 11px; color: #475569;">${escapeHtml(g.checkin_date || '-')}</td>
                        <td style="font-size: 11px; color: #475569;">${escapeHtml(g.checkout_date || '-')}</td>
                        <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 600;">₹ ${fmt(g.total_bill)}</td>
                        <td style="text-align: right; font-family: var(--font-mono, monospace); color: #059669; font-weight: 600;">₹ ${fmt(g.paid_amount)}</td>
                        <td style="text-align: right; font-family: var(--font-mono, monospace); color: ${g.remaining_amount > 0 ? '#b91c1c' : '#059669'}; font-weight: 700;">₹ ${fmt(g.remaining_amount)}</td>
                        <td style="text-align: center;">
                          ${g.is_settled ? `
                            <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                              <i class="fa-solid fa-check"></i> Settled
                            </span>
                          ` : `
                            <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;">
                              <i class="fa-solid fa-clock"></i> Unsettled
                            </span>
                          `}
                        </td>
                      </tr>
                    `).join('') : `
                      <tr>
                        <td colspan="11" style="text-align: center; padding: 32px; color: #94a3b8;">No guest folios active on this audit date.</td>
                      </tr>
                    `}
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        `;
        if (printBtn) {
          printBtn.onclick = () => printAuditObject(a);
        }
      })
      .catch(err => {
        content.innerHTML = `<div style="text-align: center; padding: 30px; color: #ef4444; font-weight: 700;">Error: ${err.message}</div>`;
      });
  }

  function switchDossierTab(tabId, btn) {
    const modal = document.getElementById('audit-dossier-modal');
    if (!modal) return;
    modal.querySelectorAll('.dossier-tab-btn').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    modal.querySelectorAll('.dossier-tab-pane').forEach(p => p.style.display = 'none');
    const target = document.getElementById(tabId);
    if (target) target.style.display = 'block';
  }

  function fmt(val) {
    const n = parseFloat(val);
    return isNaN(n) ? '0.00' : n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function closeAuditModal() {
    const modal = document.getElementById('audit-dossier-modal');
    if (modal) modal.style.display = 'none';
  }

  // Close modal when clicking outside
  window.addEventListener('click', function(e) {
    const modal = document.getElementById('audit-dossier-modal');
    if (e.target === modal) {
      closeAuditModal();
    }
  });

  function printAuditById(auditId) {
    fetch(`{{ url('/admin/nightaudit') }}/${auditId}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.audit) {
          printAuditObject(data.audit);
        }
      });
  }

  function printAuditObject(a) {
    const printArea = document.getElementById('printable-audit-container');
    const auditDateFormatted = a.audit_date ? new Date(a.audit_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : 'N/A';
    const completedTime = a.completed_at ? new Date(a.completed_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : 'N/A';

    printArea.innerHTML = `
      <div style="max-width: 800px; margin: 0 auto; font-family: sans-serif; padding: 20px;">
        <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 12px; margin-bottom: 16px;">
          <h2 style="margin: 0; font-size: 20px; text-transform: uppercase; letter-spacing: 1px;">Hotel Sagar Sonnet</h2>
          <div style="font-size: 12px; margin-top: 3px; font-weight: 600;">Daily Night Audit Verification & Financial Dossier</div>
          <div style="font-size: 13px; font-weight: bold; margin-top: 6px;">Audit Date: ${auditDateFormatted} • Completed at: ${completedTime}</div>
          <div style="font-size: 11px; color: #444; margin-top: 2px;">Audited & Signed by: <strong>${a.auditor_name || (a.auditor ? a.auditor.name : 'Front Office Auditor')}</strong></div>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 12px;">
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; width: 35%; background: #f9f9f9;"><strong>Total Collections Reconciled:</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; color: #059669;">₹ ${Number(a.total_payments || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})} (Cash: ₹${Number(a.cash_collection||0).toFixed(0)}, UPI: ₹${Number(a.upi_collection||0).toFixed(0)}, Card: ₹${Number(a.card_collection||0).toFixed(0)}, Bank: ₹${Number(a.bank_collection||0).toFixed(0)})</td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9;"><strong>Room Tariff Revenue:</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">₹ ${Number(a.room_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9;"><strong>Meal Plan Revenue:</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">₹ ${Number(a.food_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})} (EP: ${a.ep_plan_count||0}, CP: ${a.cp_plan_count||0}, MAP: ${a.map_plan_count||0}, AP: ${a.ap_plan_count||0})</td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9;"><strong>Total Hotel Sales (Revenue):</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; color: #7c3aed;">₹ ${Number(a.total_revenue || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
          </tr>
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9;"><strong>Occupancy Status:</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">${Number(a.occupancy_rate || 0).toFixed(1)}% (${a.occupied_rooms||0}/${a.total_rooms||0} Occupied, ${a.vacant_rooms||0} Vacant)</td>
          </tr>
          ${a.remarks ? `
          <tr>
            <td style="padding: 8px; border: 1px solid #ddd; background: #f9f9f9;"><strong>Remarks:</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">${escapeHtml(a.remarks)}</td>
          </tr>` : ''}
        </table>

        <div style="margin-top: 60px; display: flex; justify-content: space-between; font-size: 11px;">
          <div style="border-top: 1px solid #000; width: 180px; text-align: center; padding-top: 4px;">Night Auditor Signature</div>
          <div style="border-top: 1px solid #000; width: 180px; text-align: center; padding-top: 4px;">General Manager Sign-off</div>
        </div>
      </div>
    `;

    window.print();
  }

  function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
</script>
@endpush
