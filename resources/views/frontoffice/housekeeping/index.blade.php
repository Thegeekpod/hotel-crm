@extends('frontoffice.layouts.app')

@section('title', 'Housekeeping Operations & Tasks - Hotel Sagar Sonnet CRM / PMS')

@push('styles')
<style>
  :root {
    --hk-emerald: #10b981;
    --hk-emerald-bg: #ecfdf5;
    --hk-amber: #f59e0b;
    --hk-amber-bg: #fffbeb;
    --hk-rose: #f43f5e;
    --hk-rose-bg: #fff1f2;
    --hk-indigo: #6366f1;
    --hk-indigo-bg: #eef2ff;
    --hk-slate: #64748b;
    --hk-slate-bg: #f8fafc;
  }

  /* Header banner */
  .hk-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 18px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }

  .hk-header-title {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .hk-header-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 10px rgba(245, 158, 11, 0.25);
  }

  .hk-title-text h2 {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .hk-title-text p {
    margin: 2px 0 0 0;
    font-size: 11.5px;
    color: #64748b;
  }

  /* Main Task Tabs Switcher */
  .hk-main-tabs-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 6px 12px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    flex-wrap: wrap;
    gap: 10px;
  }

  .hk-tabs-nav {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .hk-tab-btn {
    border: none;
    background: #f8fafc;
    color: #475569;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease-in-out;
    border: 1px solid transparent;
  }

  .hk-tab-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
  }

  .hk-tab-btn.active-tab-pending {
    background: #fffbeb;
    color: #b45309;
    border-color: #fde68a;
    box-shadow: 0 1px 4px rgba(245, 158, 11, 0.12);
  }

  .hk-tab-btn.active-tab-completed {
    background: #ecfdf5;
    color: #047857;
    border-color: #a7f3d0;
    box-shadow: 0 1px 4px rgba(16, 185, 129, 0.12);
  }

  .hk-tab-btn.active-tab-all {
    background: #eef2ff;
    color: #4338ca;
    border-color: #c7d2fe;
    box-shadow: 0 1px 4px rgba(99, 102, 241, 0.12);
  }

  .hk-tab-badge {
    padding: 1px 7px;
    border-radius: 12px;
    font-size: 10.5px;
    font-weight: 800;
    line-height: 1.4;
  }

  .badge-pending-count {
    background: #f59e0b;
    color: #ffffff;
  }

  .badge-completed-count {
    background: #10b981;
    color: #ffffff;
  }

  .badge-all-count {
    background: #6366f1;
    color: #ffffff;
  }

  /* KPI Cards Grid */
  .hk-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 10px;
    margin-bottom: 12px;
  }

  .hk-kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    transition: transform 0.15s, box-shadow 0.15s;
    cursor: pointer;
  }

  .hk-kpi-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  .hk-kpi-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
  }

  .hk-kpi-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .hk-kpi-val {
    font-size: 16px;
    font-weight: 800;
    line-height: 1.1;
    color: #0f172a;
  }

  .hk-kpi-lbl {
    font-size: 10px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Filters & Actions Bar */
  .hk-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 12px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }

  .hk-filters-group {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    flex: 1;
  }

  .hk-search-wrap {
    position: relative;
    min-width: 180px;
    max-width: 260px;
    flex: 1;
  }

  .hk-search-wrap input {
    width: 100%;
    height: 32px;
    padding: 4px 10px 4px 30px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #f8fafc;
    font-size: 11.5px;
    color: #1e293b;
    outline: none;
    transition: all 0.2s;
  }

  .hk-search-wrap input:focus {
    background: #ffffff;
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.12);
  }

  .hk-search-wrap i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 11px;
    color: #94a3b8;
  }

  .hk-select-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
  }

  .hk-select {
    height: 32px;
    padding: 0 24px 0 10px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #ffffff;
    font-size: 11.5px;
    font-weight: 500;
    color: #334155;
    outline: none;
    cursor: pointer;
    transition: all 0.15s;
    appearance: none;
    -webkit-appearance: none;
  }

  .hk-select-wrap::after {
    content: "\f078";
    font-family: "Font Awesome 6 Free";
    font-weight: 900;
    font-size: 9px;
    color: #64748b;
    position: absolute;
    right: 9px;
    pointer-events: none;
  }

  .hk-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.12);
  }

  .hk-view-toggles {
    display: flex;
    align-items: center;
    gap: 2px;
    background: #f1f5f9;
    padding: 2px;
    border-radius: 6px;
  }

  .hk-view-btn {
    border: none;
    background: transparent;
    padding: 4px 10px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: 600;
    color: #64748b;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
  }

  .hk-view-btn.active {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
  }

  /* Section Titles */
  .hk-section-bar {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .hk-section-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .hk-section-badge {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
  }

  .hk-section-hint {
    font-size: 11px;
    color: #64748b;
  }

  /* Bulk Floating Toolbar */
  .hk-bulk-bar {
    display: none;
    align-items: center;
    justify-content: space-between;
    background: #0f172a;
    color: #ffffff;
    border-radius: 10px;
    padding: 8px 14px;
    margin-bottom: 12px;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.2);
    animation: slideDown 0.2s ease-out;
  }

  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .hk-bulk-info {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    font-weight: 600;
  }

  .hk-bulk-pill {
    background: #6366f1;
    color: #fff;
    padding: 2px 7px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 700;
  }

  .hk-bulk-actions {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .btn-bulk-action {
    border: none;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
  }

  .btn-bulk-clean { background: #10b981; color: #fff; }
  .btn-bulk-clean:hover { background: #059669; }

  /* Room Cards Grid */
  .hk-rooms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 10px;
    margin-bottom: 16px;
  }

  .hk-room-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    transition: all 0.15s ease;
    position: relative;
    border-top: 3.5px solid #cbd5e1;
  }

  .hk-room-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
  }

  .hk-room-card.state-clean { border-top-color: #10b981; }
  .hk-room-card.state-dirty { border-top-color: #f59e0b; }
  .hk-room-card.state-inspect { border-top-color: #6366f1; }
  .hk-room-card.state-maintenance { border-top-color: #f43f5e; }

  .hk-room-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
  }

  .hk-room-title-box {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .hk-check-box {
    width: 15px;
    height: 15px;
    cursor: pointer;
    accent-color: #6366f1;
    margin: 0;
  }

  .hk-room-number {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
  }

  .hk-room-cat {
    font-size: 10.5px;
    color: #64748b;
    font-weight: 600;
    margin-top: 2px;
  }

  .hk-badges-col {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 3px;
  }

  .hk-badge {
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  .badge-clean { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
  .badge-dirty { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
  .badge-inspect { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
  .badge-occupied { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; }
  .badge-vacant { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
  .badge-maintenance { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }

  .hk-room-body {
    padding: 6px 0;
    border-top: 1px dashed #e2e8f0;
    border-bottom: 1px dashed #e2e8f0;
    margin-bottom: 8px;
    font-size: 11px;
  }

  .hk-detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 3px;
    color: #475569;
  }

  .hk-detail-row:last-child {
    margin-bottom: 0;
  }

  .hk-detail-row strong {
    color: #1e293b;
    font-weight: 600;
  }

  .hk-room-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 4px;
  }

  .hk-btn-quick {
    flex: 1;
    padding: 5px 4px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #334155;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    transition: all 0.15s;
  }

  .hk-btn-quick:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  }

  .hk-btn-quick.btn-mark-clean:hover {
    background: #10b981;
    color: #ffffff;
    border-color: #10b981;
  }

  .hk-btn-quick.btn-mark-dirty:hover {
    background: #f59e0b;
    color: #ffffff;
    border-color: #f59e0b;
  }

  .hk-btn-history {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 10px;
    transition: all 0.15s;
    flex-shrink: 0;
  }

  .hk-btn-history:hover {
    color: #6366f1;
    border-color: #c7d2fe;
    background: #eef2ff;
  }

  /* Table View Styles */
  .hk-table-view {
    display: none;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }

  .hk-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
    text-align: left;
  }

  .hk-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    padding: 8px 12px;
    border-bottom: 1px solid #e2e8f0;
    text-transform: uppercase;
    font-size: 10.5px;
    letter-spacing: 0.3px;
  }

  .hk-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: middle;
  }

  .hk-table tr:hover td {
    background: #fafafa;
  }

  /* Collapsible Activity Logs Card */
  .hk-logs-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    overflow: hidden;
    margin-top: 14px;
    margin-bottom: 24px;
  }

  .hk-logs-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 11px 16px;
    cursor: pointer;
    user-select: none;
    background: #ffffff;
    transition: background 0.15s;
  }

  .hk-logs-header:hover {
    background: #f8fafc;
  }

  .hk-logs-header h3 {
    margin: 0;
    font-size: 13.5px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .hk-logs-header-right {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .hk-chevron-icon {
    font-size: 11px;
    color: #64748b;
    transition: transform 0.25s ease;
  }

  .hk-logs-body {
    display: none;
    border-top: 1px solid #e2e8f0;
    padding: 0;
    animation: fadeIn 0.2s ease-out;
  }

  /* Modals */
  .hk-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(4px);
    z-index: 1050;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.2s ease-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  .hk-modal-box {
    background: #ffffff;
    border-radius: 12px;
    width: 90%;
    max-width: 540px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 16px 36px rgba(0,0,0,0.18);
    overflow: hidden;
  }

  .hk-modal-header {
    padding: 14px 18px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
  }

  .hk-modal-title {
    font-size: 14.5px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
  }

  .hk-modal-close {
    background: transparent;
    border: none;
    font-size: 18px;
    color: #64748b;
    cursor: pointer;
  }

  .hk-modal-body {
    padding: 16px 18px;
    overflow-y: auto;
    flex: 1;
  }

  /* Timeline */
  .timeline-list {
    list-style: none;
    padding: 0;
    margin: 0;
    position: relative;
  }

  .timeline-list::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 15px;
    width: 2px;
    background: #e2e8f0;
  }

  .timeline-item {
    position: relative;
    padding-left: 34px;
    margin-bottom: 14px;
  }

  .timeline-item:last-child {
    margin-bottom: 0;
  }

  .timeline-dot {
    position: absolute;
    left: 9px;
    top: 4px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #ffffff;
    border: 3px solid #6366f1;
    z-index: 1;
  }

  .timeline-time {
    font-size: 10.5px;
    color: #94a3b8;
    font-weight: 600;
  }

  .timeline-content {
    font-size: 12px;
    color: #1e293b;
    font-weight: 600;
    margin-top: 2px;
  }
</style>
@endpush

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">
    
    <!-- Header Banner -->
    <div class="hk-header-card">
      <div class="hk-header-title">
        <div class="hk-header-icon">
          <i class="fa-solid fa-broom"></i>
        </div>
        <div class="hk-title-text">
          <h2>Housekeeping Operations & Tasks <span style="font-size: 11px; font-weight: 600; color: #10b981; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 2px 7px; border-radius: 12px;"><i class="fa-solid fa-circle" style="font-size: 7px;"></i> Instant Status Transfer</span></h2>
          <p>Real-time room cleaning queue: change any status to immediately transition rooms between Tasks & Completed Tasks</p>
        </div>
      </div>
      <div style="display: flex; gap: 8px; align-items: center;">
        <button class="ribbon-btn" onclick="location.reload()" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 11.5px; display: flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-rotate" style="color: #6366f1; font-size: 11px;"></i> Refresh
        </button>
      </div>
    </div>

    <!-- MAIN TABS: Tasks vs Completed Tasks vs All -->
    <div class="hk-main-tabs-bar">
      <div class="hk-tabs-nav">
        <!-- Active Cleaning Tasks Tab -->
        <button type="button" class="hk-tab-btn active-tab-pending" id="tabBtnPending" onclick="switchSection('pending')">
          <i class="fa-solid fa-broom" style="color: #f59e0b; font-size: 12px;"></i>
          <span>Tasks (Cleaning & Due)</span>
          <span class="hk-tab-badge badge-pending-count" id="badgePendingCount">{{ count($pendingRooms) }}</span>
        </button>

        <!-- Completed Tasks Tab -->
        <button type="button" class="hk-tab-btn" id="tabBtnCompleted" onclick="switchSection('completed')">
          <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 12px;"></i>
          <span>Completed Tasks</span>
          <span class="hk-tab-badge badge-completed-count" id="badgeCompletedCount">{{ count($completedRooms) }}</span>
        </button>

        <!-- All Rooms Tab -->
        <button type="button" class="hk-tab-btn" id="tabBtnAll" onclick="switchSection('all')">
          <i class="fa-solid fa-border-all" style="color: #6366f1; font-size: 12px;"></i>
          <span>All Rooms</span>
          <span class="hk-tab-badge badge-all-count" id="badgeAllCount">{{ count($filteredRooms) }}</span>
        </button>
      </div>

      <!-- View Switcher -->
      <div class="hk-view-toggles">
        <button type="button" class="hk-view-btn active" id="btnViewGrid" onclick="switchView('grid')">
          <i class="fa-solid fa-table-cells-large"></i> Grid
        </button>
        <button type="button" class="hk-view-btn" id="btnViewTable" onclick="switchView('table')">
          <i class="fa-solid fa-list"></i> Table
        </button>
      </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="hk-kpi-grid">
      <div class="hk-kpi-card" onclick="switchSection('all')">
        <div class="hk-kpi-icon" style="background: #eff6ff; color: #3b82f6;">
          <i class="fa-solid fa-hotel"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $totalRooms }}</span>
          <span class="hk-kpi-lbl">Total Rooms</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="switchSection('completed')">
        <div class="hk-kpi-icon" style="background: var(--hk-emerald-bg); color: var(--hk-emerald);">
          <i class="fa-solid fa-sparkles"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $cleanCount }}</span>
          <span class="hk-kpi-lbl">Clean / Completed</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="switchSection('pending')">
        <div class="hk-kpi-icon" style="background: var(--hk-amber-bg); color: var(--hk-amber);">
          <i class="fa-solid fa-broom"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $dirtyCount }}</span>
          <span class="hk-kpi-lbl">Dirty / Due Tasks</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="applyQuickFilter('hk_state', '3')">
        <div class="hk-kpi-icon" style="background: var(--hk-indigo-bg); color: var(--hk-indigo);">
          <i class="fa-solid fa-clipboard-check"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $inspectCount }}</span>
          <span class="hk-kpi-lbl">Inspecting</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="applyQuickFilter('occupancy', 'occupied')">
        <div class="hk-kpi-icon" style="background: #fdf2f8; color: #ec4899;">
          <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $occupiedCount }}</span>
          <span class="hk-kpi-lbl">Occupied</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="applyQuickFilter('occupancy', 'vacant')">
        <div class="hk-kpi-icon" style="background: #f0fdf4; color: #16a34a;">
          <i class="fa-solid fa-door-open"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $vacantCount }}</span>
          <span class="hk-kpi-lbl">Vacant</span>
        </div>
      </div>

      <div class="hk-kpi-card" onclick="applyQuickFilter('op_status', 'maintenance')">
        <div class="hk-kpi-icon" style="background: var(--hk-rose-bg); color: var(--hk-rose);">
          <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div class="hk-kpi-info">
          <span class="hk-kpi-val">{{ $maintenanceCount }}</span>
          <span class="hk-kpi-lbl">Maintenance</span>
        </div>
      </div>
    </div>

    <!-- Filters Toolbar with custom styled select arrow icons -->
    <form method="GET" action="{{ route('frontoffice.housekeeping') }}" id="hkFilterForm" class="hk-toolbar">
      <div class="hk-filters-group">
        <!-- Search -->
        <div class="hk-search-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="search" value="{{ $search }}" placeholder="Search room # or guest..." onkeydown="if(event.key==='Enter') this.form.submit()">
        </div>

        <!-- Floor Filter -->
        <div class="hk-select-wrap">
          <select name="floor" class="hk-select" onchange="this.form.submit()">
            <option value="all">All Floors</option>
            @foreach($floors as $fl)
              <option value="{{ $fl->id }}" {{ $selectedFloor == $fl->id ? 'selected' : '' }}>Floor {{ $fl->floor }}</option>
            @endforeach
          </select>
        </div>

        <!-- Category Filter -->
        <div class="hk-select-wrap">
          <select name="category" class="hk-select" onchange="this.form.submit()">
            <option value="all">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- HK State Filter -->
        <div class="hk-select-wrap">
          <select name="hk_state" class="hk-select" onchange="this.form.submit()">
            <option value="all">All Cleaning States</option>
            @foreach($housekeepingStates as $st)
              <option value="{{ $st->id }}" {{ $selectedHkState == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- Occupancy Filter -->
        <div class="hk-select-wrap">
          <select name="occupancy" class="hk-select" onchange="this.form.submit()">
            <option value="all" {{ empty($selectedOccupancy) || $selectedOccupancy == 'all' ? 'selected' : '' }}>All Occupancy</option>
            <option value="vacant" {{ $selectedOccupancy == 'vacant' ? 'selected' : '' }}>Vacant Rooms</option>
            <option value="occupied" {{ $selectedOccupancy == 'occupied' ? 'selected' : '' }}>Occupied Rooms</option>
          </select>
        </div>

        @if($search || ($selectedFloor && $selectedFloor != 'all') || ($selectedCategory && $selectedCategory != 'all') || ($selectedHkState && $selectedHkState != 'all') || ($selectedOccupancy && $selectedOccupancy != 'all'))
          <a href="{{ route('frontoffice.housekeeping') }}" style="font-size: 11px; font-weight: 700; color: #ef4444; text-decoration: none; padding: 5px 9px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px;">
            <i class="fa-solid fa-xmark"></i> Clear Filters
          </a>
        @endif
      </div>
    </form>

    <!-- Bulk Floating Actions Bar -->
    <div class="hk-bulk-bar" id="hkBulkBar">
      <div class="hk-bulk-info">
        <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
        <span><strong id="selectedRoomsCount">0</strong> rooms selected</span>
        <span class="hk-bulk-pill">Batch Operations</span>
      </div>
      <div class="hk-bulk-actions">
        <select id="bulkHkStatusSelect" class="hk-select" style="background: #1e293b; color: #fff; border-color: #334155; height: 30px; font-size: 11px;">
          @foreach($housekeepingStates as $st)
            <option value="{{ $st->id }}">{{ $st->name }}</option>
          @endforeach
        </select>
        <button type="button" class="btn-bulk-action btn-bulk-clean" onclick="submitBulkUpdate()">
          <i class="fa-solid fa-check-double"></i> Apply State
        </button>
        <button type="button" class="btn-bulk-action" onclick="clearSelection()" style="background: #334155; color: #fff;">
          <i class="fa-solid fa-xmark"></i> Cancel
        </button>
      </div>
    </div>

    <!-- TAB 1: PENDING TASKS SECTION -->
    <div id="sectionPendingTasks">
      <div class="hk-section-bar">
        <h3 class="hk-section-title">
          <span class="hk-section-badge" style="background: #f59e0b;"></span>
          Cleaning & Due Tasks Queue (<span class="pending-count-lbl">{{ count($pendingRooms) }}</span>)
        </h3>
        <span class="hk-section-hint">Mark as clean to automatically move room to Completed Tasks</span>
      </div>

      <!-- Grid View for Pending Tasks -->
      <div class="hk-rooms-grid pending-grid-target">
        @forelse($pendingRooms as $room)
          @include('frontoffice.housekeeping.room_card', ['room' => $room, 'isCompleteSection' => false])
        @empty
          <div class="hk-empty-box" style="grid-column: 1 / -1; padding: 28px; text-align: center; background: #fff; border-radius: 10px; border: 1px dashed #cbd5e1;">
            <i class="fa-solid fa-circle-check" style="font-size: 26px; color: #10b981; margin-bottom: 8px;"></i>
            <h4 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 13.5px;">No Pending Cleaning Tasks!</h4>
            <p style="margin: 3px 0 0 0; font-size: 11.5px; color: #64748b;">All rooms are currently cleaned, inspected, or sanitized.</p>
          </div>
        @endforelse
      </div>

      <!-- Table View for Pending Tasks -->
      <div class="hk-table-view pending-table-target">
        <table class="hk-table">
          <thead>
            <tr>
              <th style="width: 36px;"><input type="checkbox" onchange="toggleSelectAllInContainer(this, '#sectionPendingTasks')"></th>
              <th>Room</th>
              <th>Floor</th>
              <th>Category</th>
              <th>Occupancy</th>
              <th>Current State</th>
              <th>Task Status</th>
              <th>Last Updated</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pendingRooms as $room)
              @include('frontoffice.housekeeping.room_table_row', ['room' => $room, 'isCompleteSection' => false])
            @empty
              <tr>
                <td colspan="9" style="text-align: center; padding: 20px; color: #64748b;">No pending cleaning tasks.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 2: COMPLETED TASKS SECTION -->
    <div class="d-none" id="sectionCompletedTasks">
      <div class="hk-section-bar">
        <h3 class="hk-section-title">
          <span class="hk-section-badge" style="background: #10b981;"></span>
          Completed Cleaning Tasks (<span class="completed-count-lbl">{{ count($completedRooms) }}</span>)
        </h3>
        <span class="hk-section-hint">Rooms cleaned and inspected, ready for guest allocation</span>
      </div>

      <!-- Grid View for Completed Tasks -->
      <div class="hk-rooms-grid completed-grid-target">
        @forelse($completedRooms as $room)
          @include('frontoffice.housekeeping.room_card', ['room' => $room, 'isCompleteSection' => true])
        @empty
          <div class="hk-empty-box" style="grid-column: 1 / -1; padding: 28px; text-align: center; background: #fff; border-radius: 10px; border: 1px dashed #cbd5e1;">
            <i class="fa-solid fa-broom" style="font-size: 26px; color: #f59e0b; margin-bottom: 8px;"></i>
            <h4 style="margin: 0; color: #0f172a; font-weight: 700; font-size: 13.5px;">No Completed Tasks Recorded</h4>
            <p style="margin: 3px 0 0 0; font-size: 11.5px; color: #64748b;">Complete any pending room cleaning above to populate this section.</p>
          </div>
        @endforelse
      </div>

      <!-- Table View for Completed Tasks -->
      <div class="hk-table-view completed-table-target">
        <table class="hk-table">
          <thead>
            <tr>
              <th style="width: 36px;"><input type="checkbox" onchange="toggleSelectAllInContainer(this, '#sectionCompletedTasks')"></th>
              <th>Room</th>
              <th>Floor</th>
              <th>Category</th>
              <th>Occupancy</th>
              <th>Current State</th>
              <th>Task Status</th>
              <th>Completed At</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($completedRooms as $room)
              @include('frontoffice.housekeeping.room_table_row', ['room' => $room, 'isCompleteSection' => true])
            @empty
              <tr>
                <td colspan="9" style="text-align: center; padding: 20px; color: #64748b;">No completed tasks yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: ALL ROOMS SECTION -->
    <div class="d-none" id="sectionAllRooms">
      <div class="hk-section-bar">
        <h3 class="hk-section-title">
          <span class="hk-section-badge" style="background: #6366f1;"></span>
          All Hotel Rooms ({{ count($filteredRooms) }})
        </h3>
        <span class="hk-section-hint">Combined overview of all inventory</span>
      </div>

      <!-- Grid View for All Rooms -->
      <div class="hk-rooms-grid all-grid-target">
        @forelse($filteredRooms as $room)
          @include('frontoffice.housekeeping.room_card', ['room' => $room, 'isCompleteSection' => null])
        @empty
          <div style="grid-column: 1 / -1; padding: 28px; text-align: center; background: #fff; border-radius: 10px; border: 1px dashed #cbd5e1;">
            <p style="margin: 0; color: #64748b; font-size: 12px;">No rooms matched your criteria.</p>
          </div>
        @endforelse
      </div>

      <!-- Table View for All Rooms -->
      <div class="hk-table-view all-table-target">
        <table class="hk-table">
          <thead>
            <tr>
              <th style="width: 36px;"><input type="checkbox" onchange="toggleSelectAllInContainer(this, '#sectionAllRooms')"></th>
              <th>Room</th>
              <th>Floor</th>
              <th>Category</th>
              <th>Occupancy</th>
              <th>Current State</th>
              <th>Operational Status</th>
              <th>Last Updated</th>
              <th class="text-right">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($filteredRooms as $room)
              @include('frontoffice.housekeeping.room_table_row', ['room' => $room, 'isCompleteSection' => null])
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Collapsible Today's Housekeeping Audit Trail -->
    <div class="hk-logs-card">
      <div class="hk-logs-header" onclick="toggleActivityLogs()">
        <h3>
          <i class="fa-solid fa-clipboard-list" style="color: #6366f1; font-size: 13px;"></i>
          Today's Housekeeping Activity Trail ({{ count($todayLogs) }})
        </h3>
        <div class="hk-logs-header-right">
          <span style="font-size: 11px; color: #64748b; font-weight: 600;">Click to view audit log</span>
          <i class="fa-solid fa-chevron-down hk-chevron-icon" id="activityLogsChevron"></i>
        </div>
      </div>

      <div class="hk-logs-body" id="activityLogsBody">
        <div style="overflow-x: auto;">
          <table class="hk-table" style="font-size: 11.5px;">
            <thead>
              <tr>
                <th>Timestamp</th>
                <th>Room #</th>
                <th>Category & Floor</th>
                <th>Status Applied</th>
                <th>Task Status</th>
                <th>Completed At</th>
              </tr>
            </thead>
            <tbody id="todayLogsTbody">
              @forelse($todayLogs as $log)
                <tr>
                  <td style="color: #64748b; font-weight: 600;">
                    <i class="fa-regular fa-clock" style="margin-right: 4px;"></i> {{ $log->created_at ? $log->created_at->format('h:i A') : 'N/A' }}
                  </td>
                  <td>
                    <strong style="color: #0f172a; font-size: 12.5px;">Room {{ $log->room?->room_number ?? 'N/A' }}</strong>
                  </td>
                  <td>
                    {{ $log->room?->categoryRelation?->name ?? 'Standard' }} (Floor {{ $log->room?->floorRelation?->floor ?? '1' }})
                  </td>
                  <td>
                    @php
                      $logStatusName = $log->housekeepingStatus?->name ?? 'Clean';
                      $isLogClean = (bool)preg_match('/clean|sanit|ready/i', strtolower($logStatusName));
                      $isLogDirty = (bool)preg_match('/dirty|due/i', strtolower($logStatusName));
                    @endphp
                    <span class="hk-badge {{ $isLogClean ? 'badge-clean' : ($isLogDirty ? 'badge-dirty' : 'badge-inspect') }}">
                      {{ $logStatusName }}
                    </span>
                  </td>
                  <td>
                    <span style="font-weight: 600; text-transform: capitalize; color: {{ $log->status === 'complete' ? '#10b981' : '#f59e0b' }};">
                      <i class="fa-solid {{ $log->status === 'complete' ? 'fa-check' : 'fa-spinner fa-spin' }}"></i> {{ $log->status ?? 'pending' }}
                    </span>
                  </td>
                  <td class="text-secondary">
                    {{ $log->completion_time ? \Carbon\Carbon::parse($log->completion_time)->format('h:i A') : '—' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align: center; padding: 20px; color: #94a3b8;">
                    No housekeeping updates logged today yet.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Room Status Change Modal -->
<div class="hk-modal-overlay" id="hkStatusModal">
  <div class="hk-modal-box">
    <div class="hk-modal-header">
      <h4 class="hk-modal-title">
        <i class="fa-solid fa-spray-can-sparkles" style="color: #f59e0b;"></i> Update Room Status: <span id="modalRoomNumber"></span>
      </h4>
      <button type="button" class="hk-modal-close" onclick="closeStatusModal()">&times;</button>
    </div>
    <div class="hk-modal-body">
      <form id="singleRoomStatusForm" onsubmit="event.preventDefault(); submitSingleRoomStatus();">
        <input type="hidden" id="modalRoomId" name="room_id">

        <div style="margin-bottom: 12px;">
          <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 5px;">
            Housekeeping Cleaning State <span style="color: #ef4444;">*</span>
          </label>
          <div class="hk-select-wrap w-full">
            <select id="modalHkStatusSelect" name="housekeeping_status_id" class="hk-select" style="width: 100%; height: 36px; font-size: 12px;" required>
              @foreach($housekeepingStates as $st)
                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->type === 'completed_task' ? 'Completed' : 'Assign' }})</option>
              @endforeach
            </select>
          </div>
        </div>

        <div style="margin-bottom: 12px;">
          <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 5px;">
            Task Progress State
          </label>
          <div class="hk-select-wrap w-full">
            <select id="modalTaskStatusSelect" name="task_status" class="hk-select" style="width: 100%; height: 36px; font-size: 12px;">
              <option value="pending">Pending Cleaning</option>
              <option value="in_progress">In Progress</option>
              <option value="complete">Complete (Mark as Done)</option>
            </select>
          </div>
        </div>

        <div style="margin-bottom: 12px;">
          <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 5px;">
            Operational Status (Optional)
          </label>
          <div class="hk-select-wrap w-full">
            <select id="modalOpStatusSelect" name="operational_status_id" class="hk-select" style="width: 100%; height: 36px; font-size: 12px;">
              <option value="">Keep Existing Operational Status</option>
              @foreach($operationalStatuses as $op)
                <option value="{{ $op->id }}">{{ $op->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 5px;">
            Cleaning / Inspection Remarks
          </label>
          <textarea id="modalRemarks" name="remarks" rows="2" style="width: 100%; padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11.5px; outline: none;" placeholder="e.g. Linen changed, floor mopped, sanitized & inspected"></textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px;">
          <button type="button" class="btn-pms-logout" style="background: #e2e8f0; color: #334155; padding: 6px 14px; font-size: 11.5px;" onclick="closeStatusModal()">Cancel</button>
          <button type="submit" id="btnSubmitSingleStatus" class="ribbon-btn" style="background: #6366f1; color: #ffffff; padding: 6px 16px; border-radius: 6px; font-weight: 700; font-size: 11.5px;">
            <i class="fa-solid fa-floppy-disk"></i> Save & Transfer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- History Modal -->
<div class="hk-modal-overlay" id="hkHistoryModal">
  <div class="hk-modal-box">
    <div class="hk-modal-header">
      <h4 class="hk-modal-title">
        <i class="fa-solid fa-clock-rotate-left text-indigo"></i> Room <span id="historyRoomNumber"></span> Cleaning Log
      </h4>
      <button type="button" class="hk-modal-close" onclick="closeHistoryModal()">&times;</button>
    </div>
    <div class="hk-modal-body">
      <div id="historyLoading" style="text-align: center; padding: 24px; color: #64748b;">
        <i class="fa-solid fa-spinner fa-spin" style="font-size: 20px; color: #6366f1;"></i>
        <div style="margin-top: 6px; font-size: 11.5px;">Loading room audit trail...</div>
      </div>
      <div class="d-none" id="historyContent">
        <ul class="timeline-list" id="timelineList"></ul>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const UPDATE_STATUS_URL = '{{ route("frontoffice.housekeeping.update-status") }}';
  const BULK_UPDATE_URL = '{{ route("frontoffice.housekeeping.bulk-update") }}';
  const ROOM_HISTORY_BASE_URL = '{{ url("front-office/housekeeping") }}';

  let currentViewMode = 'grid';

  // Toggle Activity Logs Collapsible Card
  function toggleActivityLogs() {
    const body = document.getElementById('activityLogsBody');
    const chevron = document.getElementById('activityLogsChevron');

    if (body.style.display === 'block') {
      body.style.display = 'none';
      chevron.style.transform = 'rotate(0deg)';
    } else {
      body.style.display = 'block';
      chevron.style.transform = 'rotate(180deg)';
    }
  }

  // Section Tab Switcher (Pending Tasks vs Completed Tasks vs All)
  function switchSection(section) {
    const secPending = document.getElementById('sectionPendingTasks');
    const secCompleted = document.getElementById('sectionCompletedTasks');
    const secAll = document.getElementById('sectionAllRooms');

    const btnPending = document.getElementById('tabBtnPending');
    const btnCompleted = document.getElementById('tabBtnCompleted');
    const btnAll = document.getElementById('tabBtnAll');

    // Reset tab active classes
    btnPending.classList.remove('active-tab-pending');
    btnCompleted.classList.remove('active-tab-completed');
    btnAll.classList.remove('active-tab-all');

    if (section === 'pending') {
      secPending.style.display = 'block';
      secCompleted.style.display = 'none';
      secAll.style.display = 'none';
      btnPending.classList.add('active-tab-pending');
    } else if (section === 'completed') {
      secPending.style.display = 'none';
      secCompleted.style.display = 'block';
      secAll.style.display = 'none';
      btnCompleted.classList.add('active-tab-completed');
    } else {
      secPending.style.display = 'none';
      secCompleted.style.display = 'none';
      secAll.style.display = 'block';
      btnAll.classList.add('active-tab-all');
    }

    clearSelection();
  }

  // Switch View between Grid and Table across all sections
  function switchView(viewMode) {
    currentViewMode = viewMode;
    const btnGrid = document.getElementById('btnViewGrid');
    const btnTable = document.getElementById('btnViewTable');

    const allGrids = document.querySelectorAll('.hk-rooms-grid');
    const allTables = document.querySelectorAll('.hk-table-view');

    if (viewMode === 'grid') {
      allGrids.forEach(g => g.style.display = 'grid');
      allTables.forEach(t => t.style.display = 'none');
      btnGrid.classList.add('active');
      btnTable.classList.remove('active');
    } else {
      allGrids.forEach(g => g.style.display = 'none');
      allTables.forEach(t => t.style.display = 'block');
      btnTable.classList.add('active');
      btnGrid.classList.remove('active');
    }
  }

  // Quick Filter via KPI
  function applyQuickFilter(field, value) {
    const form = document.getElementById('hkFilterForm');
    const input = form.querySelector(`[name="${field}"]`);
    if (input) {
      input.value = value;
      form.submit();
    }
  }

  // Selection Checkbox Handler for Bulk Actions
  function onRoomCheckboxChange() {
    const checkedBoxes = document.querySelectorAll('.room-select-box:checked');
    const bulkBar = document.getElementById('hkBulkBar');
    const countSpan = document.getElementById('selectedRoomsCount');

    countSpan.textContent = checkedBoxes.length;

    if (checkedBoxes.length > 0) {
      bulkBar.style.display = 'flex';
    } else {
      bulkBar.style.display = 'none';
    }
  }

  function toggleSelectAllInContainer(masterBox, containerSelector) {
    const container = document.querySelector(containerSelector);
    if (!container) return;
    const checkboxes = container.querySelectorAll('.room-select-box');
    checkboxes.forEach(cb => {
      cb.checked = masterBox.checked;
    });
    onRoomCheckboxChange();
  }

  function clearSelection() {
    const checkboxes = document.querySelectorAll('.room-select-box');
    checkboxes.forEach(cb => { cb.checked = false; });
    onRoomCheckboxChange();
  }

  // Instant 1-click status update (Immediately transfers room to target section)
  async function quickUpdateStatus(roomId, hkStatusId, label) {
    try {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(`Updating room to ${label}...`, 'info');
      }

      const res = await fetch(UPDATE_STATUS_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          room_id: roomId,
          housekeeping_status_id: hkStatusId,
          task_status: (label === 'Clean' ? 'complete' : 'pending')
        })
      });

      const data = await res.json();
      if (data.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        }
        setTimeout(() => location.reload(), 400);
      } else {
        alert(data.message || 'Update failed');
      }
    } catch (err) {
      console.error(err);
      alert('Error updating status: ' + err.message);
    }
  }

  // Open Room Status Modal
  function openRoomStatusModal(roomId, roomNumber, currentHkId, currentTaskStatus) {
    document.getElementById('modalRoomId').value = roomId;
    document.getElementById('modalRoomNumber').textContent = roomNumber;
    document.getElementById('modalHkStatusSelect').value = currentHkId;
    if (currentTaskStatus) {
      document.getElementById('modalTaskStatusSelect').value = currentTaskStatus;
    }
    document.getElementById('hkStatusModal').style.display = 'flex';
  }

  function closeStatusModal() {
    document.getElementById('hkStatusModal').style.display = 'none';
  }

  // Submit Single Room Status Change
  async function submitSingleRoomStatus() {
    const btn = document.getElementById('btnSubmitSingleStatus');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
    btn.disabled = true;

    const roomId = document.getElementById('modalRoomId').value;
    const hkStatusId = document.getElementById('modalHkStatusSelect').value;
    const taskStatus = document.getElementById('modalTaskStatusSelect').value;
    const opStatusId = document.getElementById('modalOpStatusSelect').value;
    const remarks = document.getElementById('modalRemarks').value;

    try {
      const res = await fetch(UPDATE_STATUS_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          room_id: roomId,
          housekeeping_status_id: hkStatusId,
          task_status: taskStatus,
          operational_status_id: opStatusId || null,
          remarks: remarks
        })
      });

      const data = await res.json();
      if (data.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        }
        closeStatusModal();
        setTimeout(() => location.reload(), 400);
      } else {
        alert(data.message || 'Status update failed.');
      }
    } catch (err) {
      console.error(err);
      alert('Error updating room status: ' + err.message);
    } finally {
      btn.innerHTML = originalText;
      btn.disabled = false;
    }
  }

  // Bulk Status Update
  async function submitBulkUpdate() {
    const checkedBoxes = Array.from(document.querySelectorAll('.room-select-box:checked'));
    if (checkedBoxes.length === 0) {
      alert('Please select at least one room.');
      return;
    }

    const roomIds = checkedBoxes.map(cb => cb.value);
    const hkStatusId = document.getElementById('bulkHkStatusSelect').value;

    if (!confirm(`Apply this cleaning status to ${roomIds.length} selected rooms?`)) {
      return;
    }

    try {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('Applying bulk update...', 'info');
      }

      const res = await fetch(BULK_UPDATE_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          room_ids: roomIds,
          housekeeping_status_id: hkStatusId
        })
      });

      const data = await res.json();
      if (data.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        }
        setTimeout(() => location.reload(), 400);
      } else {
        alert(data.message || 'Bulk update failed');
      }
    } catch (err) {
      console.error(err);
      alert('Error during bulk update: ' + err.message);
    }
  }

  // View Room History Modal
  async function viewRoomHistory(roomId, roomNumber) {
    document.getElementById('historyRoomNumber').textContent = roomNumber;
    const modal = document.getElementById('hkHistoryModal');
    const loading = document.getElementById('historyLoading');
    const content = document.getElementById('historyContent');
    const list = document.getElementById('timelineList');

    modal.style.display = 'flex';
    loading.style.display = 'block';
    content.style.display = 'none';
    list.innerHTML = '';

    try {
      const res = await fetch(`${ROOM_HISTORY_BASE_URL}/${roomId}/history`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();

      if (data.success) {
        const logs = data.hk_logs || [];
        if (logs.length === 0) {
          list.innerHTML = '<li style="color: #94a3b8; font-size: 11.5px; text-align: center; padding: 14px;">No recorded history for this room.</li>';
        } else {
          logs.forEach(log => {
            const timeStr = log.created_at ? new Date(log.created_at).toLocaleString('en-US', {
              month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
            }) : 'Unknown date';

            const statusName = log.housekeeping_status ? log.housekeeping_status.name : 'Updated';
            const isClean = /clean|sanit|ready/i.test(statusName);

            const li = document.createElement('li');
            li.className = 'timeline-item';
            li.innerHTML = `
              <div class="timeline-dot" style="border-color: ${isClean ? '#10b981' : '#f59e0b'};"></div>
              <div class="timeline-time">${timeStr}</div>
              <div class="timeline-content">
                Marked as <strong style="color: ${isClean ? '#059669' : '#d97706'};">${statusName}</strong>
                ${log.status ? ` &bull; <span style="font-size: 10.5px; text-transform: uppercase; color: #64748b;">${log.status}</span>` : ''}
              </div>
            `;
            list.appendChild(li);
          });
        }
      }
    } catch (err) {
      console.error(err);
      list.innerHTML = `<li style="color: #ef4444; font-size: 11.5px;">Failed to load logs: ${err.message}</li>`;
    } finally {
      loading.style.display = 'none';
      content.style.display = 'block';
    }
  }

  function closeHistoryModal() {
    document.getElementById('hkHistoryModal').style.display = 'none';
  }

  window.addEventListener('click', function(e) {
    const statusModal = document.getElementById('hkStatusModal');
    const historyModal = document.getElementById('hkHistoryModal');
    if (e.target === statusModal) closeStatusModal();
    if (e.target === historyModal) closeHistoryModal();
  });
</script>
@endpush
@endsection
