@extends('frontoffice.layouts.app')

@section('title', 'Folio Management & Master Ledger - Hotel Sagar Sonnet CRM / PMS')

@push('styles')
<style>
  /* Table styling */
  .pms-table th,
  .pms-table td {
    white-space: nowrap !important;
  }

  .folio-id-text {
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    color: #4f46e5;
    font-size: 13px;
    white-space: nowrap;
    letter-spacing: 0.3px;
  }

  .folio-number-sub {
    font-size: 10px;
    color: #64748b;
    font-family: var(--font-mono, monospace);
    margin-top: 2px;
    white-space: nowrap;
  }

  .badge-extra-guests {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 9999px;
    background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    color: #3730a3;
    border: 1px solid #a5b4fc;
    margin-left: 6px;
    vertical-align: middle;
  }

  .room-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 800;
    font-size: 11px;
    font-family: var(--font-mono, monospace);
  }

  /* Header card */
  .folio-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 10px -3px rgba(0,0,0,0.04);
  }

  .folio-stat-tile {
    flex: 1;
    min-width: 140px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.03);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .folio-stat-tile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }

  /* Toolbar styling */
  .folio-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 18px;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }

  .search-box-wrap {
    position: relative;
    flex: 1;
    min-width: 250px;
    max-width: 420px;
  }

  .search-box-wrap i.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
  }

  .search-box-wrap input {
    width: 100%;
    height: 38px;
    padding: 6px 36px 6px 36px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #1e293b;
    outline: none;
    transition: all 0.2s ease;
    box-sizing: border-box;
  }

  .search-box-wrap input:focus {
    background-color: #ffffff;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }

  .pms-filter-select {
    height: 38px;
    padding: 6px 30px 6px 12px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 10px center;
    appearance: none;
    -webkit-appearance: none;
    color: #1e293b;
    cursor: pointer;
    outline: none;
    transition: all 0.2s;
  }

  /* Status Badges */
  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 9999px;
    text-transform: capitalize;
    letter-spacing: 0.3px;
    white-space: nowrap;
  }
  .status-badge.confirmed { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
  .status-badge.arrived { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
  .status-badge.checkedin, .status-badge.checked-in { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .status-badge.stayover, .status-badge.stay-over { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
  .status-badge.settled, .status-badge.settlement-done { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
  .status-badge.checkedout, .status-badge.checked-out { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
  .status-badge.cancelled { background: #ffe4e6; color: #be123c; border: 1px solid #fecdd3; }

  /* Action button */
  .btn-action-view {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #475569;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 13px;
  }
  .btn-action-view:hover {
    background: #6366f1;
    border-color: #6366f1;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
    transform: scale(1.08);
  }

  /* Modal Details */
  .dossier-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }
  .dossier-card-title {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #334155;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .dossier-field-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    border-bottom: 1px dashed #f1f5f9;
    font-size: 12px;
  }
  .dossier-field-row:last-child {
    border-bottom: none;
  }
  .dossier-label {
    color: #64748b;
    font-weight: 600;
  }
  .dossier-val {
    color: #0f172a;
    font-weight: 700;
    text-align: right;
  }
  .dossier-val.mono {
    font-family: var(--font-mono, monospace);
  }

  /* Bill Summary Box */
  .bill-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 10px;
    margin-bottom: 14px;
  }
  .bill-stat-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    text-align: center;
  }
  .bill-stat-label {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.4px;
  }
  .bill-stat-value {
    font-size: 16px;
    font-weight: 900;
    font-family: var(--font-mono, monospace);
    color: #0f172a;
    margin-top: 3px;
  }

  /* Room breakdown card in modal */
  .room-alloc-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 14px;
    margin-bottom: 10px;
  }
  .room-alloc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 8px;
    margin-bottom: 8px;
  }

  /* Occupant micro row */
  .occupant-badge-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 8px;
    background: #f8fafc;
    border-radius: 6px;
    margin-bottom: 4px;
    font-size: 11px;
  }

  /* Settlement Inline Panel */
  .settlement-panel {
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    border-radius: 12px;
    padding: 14px 16px;
    margin-top: 14px;
  }

  /* PMS Standard Pagination Footer */
  .pms-table-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 18px;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
    flex-wrap: wrap;
    gap: 10px;
  }
  .pms-pagination-info {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
  }
  .pms-page-nav {
    display: flex;
    gap: 4px;
    align-items: center;
  }
  .pms-page-btn {
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.15s ease;
  }
  .pms-page-btn:hover:not(.active) {
    border-color: #6366f1;
    color: #6366f1;
    background: #eef2ff;
  }
  .pms-page-btn.active {
    background: #6366f1;
    border-color: #6366f1;
    color: #ffffff;
  }

  /* Universal guard: restrict any SVG icons in pagination */
  .pms-page-nav svg,
  .pms-table-pagination svg,
  nav svg {
    width: 14px !important;
    height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
  }

  /* Modal Backdrop & Enhanced Scrolling Support */
  #folio-details-modal.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 100050 !important;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(15, 23, 42, 0.5) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    overflow-y: auto !important;
    box-sizing: border-box;
  }
  #folio-details-modal.open {
    display: flex !important;
  }
  #folio-details-modal .modal-window {
    width: 960px;
    max-width: 96vw;
    height: 90vh;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: #ffffff;
    box-sizing: border-box;
    margin: auto;
  }
  #folio-details-modal .modal-top {
    flex-shrink: 0;
    flex-grow: 0;
  }
  #folio-details-modal #folio-modal-body {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch;
    padding: 20px 24px;
    background: #f8fafc;
  }
  #folio-details-modal #folio-modal-body::-webkit-scrollbar {
    width: 8px;
  }
  #folio-details-modal #folio-modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
  }
  #folio-details-modal #folio-modal-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  #folio-details-modal #folio-modal-body::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
  #folio-details-modal .modal-bot {
    flex-shrink: 0;
    flex-grow: 0;
  }
</style>
@endpush

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">

  <!-- ========================================== -->
  <!-- Top Header & KPI Summary Cards             -->
  <!-- ========================================== -->
  <div class="folio-header-card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 20px; box-shadow: 0 4px 12px rgba(99,102,241,0.3);">
          <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div>
          <h1 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
            Folio &amp; Master Billing Ledger
            <span style="font-size: 11px; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 6px;">Live PMS</span>
          </h1>
          <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
            Booking numbers grouped folio directory with rooms, occupants, financial settlements &amp; check-out control.
          </div>
        </div>
      </div>

      <div style="display: flex; align-items: center; gap: 8px;">
        <button type="button" class="btn-ui-secondary" onclick="window.location.reload()" style="padding: 7px 14px; font-size: 12px; font-weight: 700; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
          <i class="fa-solid fa-rotate"></i> Refresh
        </button>
      </div>
    </div>

    <!-- KPI Metric Counters (Filter-aware: dynamically updates with active filters) -->
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
      
      <!-- KPI Tile 1: Volume -->
      <div class="folio-stat-tile">
        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">
          @if($stats['is_filtered'])
            Filtered Bookings
          @else
            Today's Check-ins
          @endif
        </div>
        <div style="font-size: 22px; font-weight: 900; color: #6366f1; margin-top: 4px; font-family: var(--font-mono, monospace);">
          {{ $stats['total_folios'] }}
        </div>
        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
          @if($stats['is_filtered'])
            Matching active filters (Total: {{ $stats['all_time_folios'] }})
          @else
            Of {{ $stats['all_time_folios'] }} all-time folios
          @endif
        </div>
      </div>

      <!-- KPI Tile 2: Gross Receivables -->
      <div class="folio-stat-tile">
        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Gross Receivables</div>
        <div style="font-size: 22px; font-weight: 900; color: #4f46e5; margin-top: 4px; font-family: var(--font-mono, monospace);">
          ₹ {{ number_format($stats['total_receivable'], 2) }}
        </div>
        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
          {{ $stats['total_folios'] }} folios billing volume
        </div>
      </div>

      <!-- KPI Tile 3: Total Paid -->
      <div class="folio-stat-tile">
        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Total Paid (Adv + Settle)</div>
        <div style="font-size: 22px; font-weight: 900; color: #059669; margin-top: 4px; font-family: var(--font-mono, monospace);">
          ₹ {{ number_format($stats['total_paid'], 2) }}
        </div>
        <div style="font-size: 10px; color: #059669; font-weight: 700; margin-top: 2px;">
          ₹ {{ number_format($stats['total_settled'], 2) }} settled ({{ $stats['settled_count'] }} finalized)
        </div>
      </div>

      <!-- KPI Tile 4: Remaining Due -->
      <div class="folio-stat-tile">
        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">Remaining Due</div>
        <div style="font-size: 22px; font-weight: 900; color: {{ $stats['total_outstanding'] > 0 ? '#e11d48' : '#059669' }}; margin-top: 4px; font-family: var(--font-mono, monospace);">
          ₹ {{ number_format($stats['total_outstanding'], 2) }}
        </div>
        <div style="font-size: 10px; color: {{ $stats['total_outstanding'] > 0 ? '#e11d48' : '#059669' }}; font-weight: 700; margin-top: 2px;">
          {{ $stats['pending_settle_count'] }} folios pending settlement
        </div>
      </div>

    </div>
  </div>

  <!-- ========================================== -->
  <!-- Search & Filter Controls Toolbar           -->
  <!-- ========================================== -->
  <form method="GET" action="{{ route('frontoffice.folio') }}" id="folio-filter-form">
    <div class="folio-toolbar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
      
      <!-- Search Input -->
      <div class="search-box-wrap">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" 
               name="search" 
               value="{{ request('search') }}" 
               placeholder="Search by Booking #, Folio #, Guest, Phone, Room..." 
               autocomplete="off">
      </div>

      <!-- Filters & Actions -->
      <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        <!-- Status Filter -->
        <select name="status" class="pms-filter-select" onchange="this.form.submit()">
          <option value="all" {{ request('status') == 'all' || !request()->filled('status') ? 'selected' : '' }}>All Statuses</option>
          <option value="Confirmed" {{ request('status') === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
          <option value="Arrived" {{ request('status') === 'Arrived' ? 'selected' : '' }}>Arrived</option>
          <option value="Checked In" {{ request('status') === 'Checked In' ? 'selected' : '' }}>Checked In</option>
          <option value="Stay Over" {{ request('status') === 'Stay Over' ? 'selected' : '' }}>Stay Over</option>
          <option value="Settlement Done" {{ request('status') === 'Settlement Done' ? 'selected' : '' }}>Settlement Done</option>
          <option value="Checked Out" {{ request('status') === 'Checked Out' ? 'selected' : '' }}>Checked Out</option>
        </select>

        <!-- Registration Type Filter -->
        <select name="registration_type" class="pms-filter-select" onchange="this.form.submit()">
          <option value="all" {{ request('registration_type') == 'all' || !request()->filled('registration_type') ? 'selected' : '' }}>All Reg Types</option>
          @foreach($registrationTypes ?? [] as $rt)
            <option value="{{ $rt->name }}" {{ request('registration_type') === $rt->name ? 'selected' : '' }}>{{ $rt->name }}</option>
          @endforeach
        </select>

        <!-- Date Range: From Date -->
        <input type="date" 
               name="from_date" 
               class="pms-filter-select" 
               style="height: 38px; padding: 6px 10px; font-family: var(--font-mono, monospace);" 
               value="{{ $fromDate }}" 
               onchange="this.form.submit()"
               title="Filter by Check-in Date From">

        <!-- Date Range: To Date -->
        <input type="date" 
               name="to_date" 
               class="pms-filter-select" 
               style="height: 38px; padding: 6px 10px; font-family: var(--font-mono, monospace);" 
               value="{{ $toDate }}" 
               onchange="this.form.submit()"
               title="Filter by Check-in Date To">

        <!-- All Dates Quick Button (if date filter active) -->
        @if(!empty($fromDate) || !empty($toDate))
          <a href="{{ route('frontoffice.folio', array_merge(request()->except(['from_date', 'to_date', 'page']), ['all_dates' => 1])) }}" 
             class="btn-ui-secondary" 
             style="height: 38px; box-sizing: border-box; padding: 0 12px; font-size: 11px; font-weight: 700; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;"
             title="View all records without date restrictions">
            <i class="fa-regular fa-calendar"></i> All Dates
          </a>
        @endif

        <!-- Today / Reset Button (resets to today's data) -->
        <a href="{{ route('frontoffice.folio') }}" 
           class="btn-ui-secondary" 
           style="height: 38px; box-sizing: border-box; padding: 0 12px; font-size: 11px; font-weight: 700; border-radius: 10px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;"
           title="Reset filters to today's data">
          <i class="fa-solid fa-rotate-left"></i> Today / Reset
        </a>

        <!-- Page Size -->
        <div class="pms-per-page-wrap" style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #64748b;">
          <span>Show</span>
          <select name="per_page" class="pms-per-page-select" onchange="this.form.submit()" style="height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 8px; font-weight: 700;">
            <option value="10" {{ request('per_page') == '10' || !request()->filled('per_page') ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
          </select>
        </div>
      </div>
    </div>
  </form>

  <!-- ========================================== -->
  <!-- Folio Master Table                         -->
  <!-- ========================================== -->
  <div class="pms-page-content" style="padding: 0; overflow: visible;">
    <div class="pms-table-box" style="border-radius: 14px; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.06); background: #ffffff; overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
      <table class="pms-table" id="folio-master-table" style="width: 100%; min-width: 1280px;">
        <thead>
          <tr>
            <th style="width: 45px; text-align: center; white-space: nowrap;">SL</th>
            <th style="min-width: 140px; text-align: left; white-space: nowrap;">Booking Number</th>
            <th style="min-width: 190px; white-space: nowrap;">Primary Guest Details</th>
            <th style="min-width: 130px; white-space: nowrap;">Mobile / Phone</th>
            <th style="min-width: 170px; white-space: nowrap;">Room(s) &amp; Floor</th>
            <th style="min-width: 120px; text-align: right; white-space: nowrap;">Paid Amount</th>
            <th style="min-width: 130px; text-align: right; white-space: nowrap;">Remaining Amount</th>
            <th style="min-width: 135px; white-space: nowrap;">Check-in</th>
            <th style="min-width: 135px; white-space: nowrap;">Check-out</th>
            <th style="min-width: 100px; text-align: center; white-space: nowrap;">Status</th>
            <th style="width: 70px; text-align: center; white-space: nowrap;">Action</th>
          </tr>
        </thead>
        <tbody id="folio-table-tbody">
          @forelse($folios as $index => $item)
            @php
              $sl = ($folios->currentPage() - 1) * $folios->perPage() + $loop->iteration;
              $statusNormalized = strtolower($item->status ?? 'confirmed');
              $statusClass = match($statusNormalized) {
                'settlement done', 'settled' => 'settled',
                'confirmed' => 'confirmed',
                'arrived' => 'arrived',
                'checked in', 'checked-in', 'checkedin' => 'checkedin',
                'stay over', 'stayover' => 'stayover',
                'checked out', 'checked-out', 'checkedout' => 'checkedout',
                'cancelled' => 'cancelled',
                default => 'confirmed'
              };

              // Additional guests count for dynamic "+N persons"
              $extraCount = $item->additionalBookings?->count() ?? 0;
              $primaryGuestName = ($item->guest?->title?->name ? $item->guest->title->name . ' ' : '') . ($item->guest?->guest_name ?? 'Guest');

              // Collect all unique rooms under this reservation
              $allReservationBookings = collect([$item])->concat($item->additionalBookings ?? []);
              $uniqueRooms = $allReservationBookings->map(function($b) {
                return $b->room;
              })->filter()->unique('id');

              // Financials
              $advAmount = (float)$item->advance_amount;
              $settleAmount = (float)($item->settlements?->sum('paid_amount') ?: $item->payments?->where('payment_type', 'Settlement')->sum('amount'));
              $totalPaid = round($advAmount + $settleAmount, 2);
              $balanceVal = (float)($item->balance ?? 0);

              // Checkin / Checkout date & time
              $cInDate = $item->checkin_date ?: $item->reserve_date;
              $cInTime = $item->checkin_time ?: $item->reserve_time;
              $cOutDate = $item->checkout_date;
              $cOutTime = $item->checkout_time;
            @endphp
            <tr class="folio-row" id="row-folio-{{ $item->id }}">
              
              <!-- 0. SL -->
              <td style="text-align: center; font-family: var(--font-mono, monospace); font-weight: 800; color: #64748b; font-size: 11px;">
                {{ $sl }}
              </td>

              <!-- 1. Booking Number -->
              <td style="white-space: nowrap;">
                <div class="folio-id-text">
                  <i class="fa-solid fa-receipt" style="color: #6366f1; margin-right: 4px; font-size: 11px;"></i>
                  {{ $item->reserve_id ?: ('RES-' . $item->id) }}
                </div>
                <div class="folio-number-sub">
                  Folio: {{ $item->folio_number ?: '-' }}
                </div>
              </td>

              <!-- 2. Primary Guest Details with Dynamic +N persons -->
              <td style="white-space: nowrap;">
                <div style="font-weight: 800; color: #0f172a; font-size: 13px; display: inline-flex; align-items: center;">
                  <span>{{ $primaryGuestName }}</span>
                  @if($extraCount > 0)
                    <span class="badge-extra-guests" title="{{ $extraCount }} additional person(s) registered under this booking">
                      <i class="fa-solid fa-user-plus" style="font-size: 9px;"></i>
                      +{{ $extraCount }} {{ $extraCount === 1 ? 'person' : 'persons' }}
                    </span>
                  @endif
                </div>
                <div style="font-size: 10px; color: #64748b; margin-top: 2px; display: flex; align-items: center; gap: 6px;">
                  @if($item->registrationType?->name)
                    <span style="background: #f1f5f9; padding: 1px 6px; border-radius: 4px; font-weight: 700; color: #475569;">
                      {{ $item->registrationType->name }}
                    </span>
                  @endif
                  @if($item->guest?->city)
                    <span style="color: #cbd5e1;">•</span>
                    <span style="color: #94a3b8;"><i class="fa-solid fa-location-dot" style="font-size: 9px;"></i> {{ $item->guest->city }}</span>
                  @endif
                </div>
              </td>

              <!-- 3. Primary Mobile / Phone -->
              <td style="white-space: nowrap;">
                <div style="font-family: var(--font-mono, monospace); font-weight: 700; font-size: 12px; color: #1e293b; display: inline-flex; align-items: center; gap: 6px;">
                  <i class="fa-solid fa-phone" style="font-size: 10px; color: #10b981;"></i>
                  <span>{{ $item->guest?->mobile ?: 'N/A' }}</span>
                </div>
              </td>

              <!-- 4. All Room Details with Floor -->
              <td style="white-space: nowrap;">
                @if($uniqueRooms->isNotEmpty())
                  <div style="display: flex; flex-direction: column; gap: 4px;">
                    @foreach($uniqueRooms as $uRoom)
                      @php
                        $cleanRNum = preg_replace('/^r[-_\s]?/i', '', trim($uRoom->room_number));
                        $rDisp = 'R' . $cleanRNum;
                        $flrDisp = $uRoom->floorRelation?->floor ?? ($uRoom->floor ?? '1');
                        $catDisp = $uRoom->categoryRelation?->name ?? ($uRoom->category ?? 'Standard');
                      @endphp
                      <div style="display: inline-flex; align-items: center; gap: 6px;">
                        <span class="room-tag">
                          <i class="fa-solid fa-door-open" style="font-size: 10px;"></i>
                          {{ $rDisp }}
                        </span>
                        <span style="font-size: 10px; color: #64748b; font-weight: 700;">
                          Floor {{ $flrDisp }}
                        </span>
                        <span style="color: #cbd5e1;">•</span>
                        <span style="font-size: 10px; color: #94a3b8;">{{ $catDisp }}</span>
                      </div>
                    @endforeach
                  </div>
                @else
                  <span style="font-size: 11px; color: #94a3b8; font-style: italic;">
                    <i class="fa-solid fa-hourglass-half" style="font-size: 10px;"></i> Unassigned
                  </span>
                @endif
              </td>

              <!-- 5. Paid Amount -->
              <td style="text-align: right; white-space: nowrap;">
                <div style="font-family: var(--font-mono, monospace); font-weight: 800; font-size: 13px; color: #059669;">
                  ₹ {{ number_format($totalPaid, 2) }}
                </div>
                <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
                  @if($settleAmount > 0)
                    <span style="color: #4f46e5; font-weight: 700;">(Incl. ₹{{ number_format($settleAmount, 0) }} Settle)</span>
                  @else
                    <span>Advance Deposit</span>
                  @endif
                </div>
              </td>

              <!-- 6. Remaining Amount -->
              <td style="text-align: right; white-space: nowrap;">
                <div style="font-family: var(--font-mono, monospace); font-weight: 800; font-size: 13px; color: {{ $balanceVal > 0 ? '#e11d48' : '#059669' }};">
                  ₹ {{ number_format($balanceVal, 2) }}
                </div>
                <div style="margin-top: 2px;">
                  @if($balanceVal > 0)
                    <span style="color: #e11d48; background: #ffe4e6; padding: 1px 7px; border-radius: 4px; font-size: 9px; font-weight: 800;">
                      <i class="fa-solid fa-triangle-exclamation" style="font-size: 8px;"></i> Due
                    </span>
                  @else
                    <span style="color: #059669; background: #ecfdf5; padding: 1px 7px; border-radius: 4px; font-size: 9px; font-weight: 800;">
                      <i class="fa-solid fa-check" style="font-size: 8px;"></i> Cleared
                    </span>
                  @endif
                </div>
              </td>

              <!-- 7. Check-in Date & Time -->
              <td style="white-space: nowrap;">
                <div style="font-weight: 700; color: #1e293b; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-regular fa-calendar-check" style="color: #6366f1; font-size: 11px;"></i>
                  <span>{{ $cInDate ? \Carbon\Carbon::parse($cInDate)->format('d M Y') : 'N/A' }}</span>
                </div>
                @if($cInTime)
                  <div style="font-size: 10px; color: #64748b; font-family: var(--font-mono, monospace); margin-top: 2px;">
                    <i class="fa-regular fa-clock" style="font-size: 9px; color: #94a3b8;"></i> {{ date('h:i A', strtotime($cInTime)) }}
                  </div>
                @endif
              </td>

              <!-- 8. Check-out Date & Time -->
              <td style="white-space: nowrap;">
                <div style="font-weight: 700; color: #1e293b; font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
                  <i class="fa-regular fa-calendar-xmark" style="color: #e11d48; font-size: 11px;"></i>
                  <span>{{ $cOutDate ? \Carbon\Carbon::parse($cOutDate)->format('d M Y') : 'N/A' }}</span>
                </div>
                @if($cOutTime)
                  <div style="font-size: 10px; color: #64748b; font-family: var(--font-mono, monospace); margin-top: 2px;">
                    <i class="fa-regular fa-clock" style="font-size: 9px; color: #94a3b8;"></i> {{ date('h:i A', strtotime($cOutTime)) }}
                  </div>
                @endif
              </td>

              <!-- 9. Status -->
              <td style="text-align: center; white-space: nowrap;">
                <span class="status-badge {{ $statusClass }}">
                  @if(in_array($item->status, ['Settlement Done', 'Checked Out']))
                    <i class="fa-solid fa-lock" style="font-size: 8px;"></i>
                  @else
                    <i class="fa-solid fa-circle" style="font-size: 6px;"></i>
                  @endif
                  <span>{{ $item->status ?? 'Confirmed' }}</span>
                </span>
              </td>

              <!-- 10. Action: View Details Modal -->
              <td style="text-align: center; white-space: nowrap;">
                <button type="button" 
                        class="btn-action-view" 
                        onclick="openFolioModalById({{ $item->id }})" 
                        title="View Full Folio Dossier (Rooms, Occupants, Billings, Settle &amp; Checkout)">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </td>

            </tr>
          @empty
            <tr>
              <td colspan="11" style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                <div style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px;"><i class="fa-solid fa-receipt"></i></div>
                <div style="font-size: 14px; font-weight: 700; color: #64748b;">No matching folios found for this filter.</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">
                  Showing check-ins for {{ $fromDate ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'selected dates' }}. 
                  @if(!empty($fromDate) || !empty($toDate))
                    <a href="{{ route('frontoffice.folio', ['all_dates' => 1]) }}" style="color: #6366f1; font-weight: 800; text-decoration: underline;">Click here to view All Dates</a>.
                  @endif
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <!-- PMS Custom Pagination Footer -->
      <div class="pms-table-pagination">
        <div class="pms-pagination-info">
          Showing {{ $folios->firstItem() ?? 0 }} to {{ $folios->lastItem() ?? 0 }} of {{ $folios->total() }} reservations
        </div>
        <div class="pms-page-nav">
          @if ($folios->hasPages())
            {{-- Previous Page Link --}}
            @if ($folios->onFirstPage())
              <span class="pms-page-btn" style="opacity: 0.4; cursor: not-allowed;" title="First Page"><i class="fa-solid fa-chevron-left"></i></span>
            @else
              <a href="{{ $folios->previousPageUrl() }}" class="pms-page-btn" title="Previous Page"><i class="fa-solid fa-chevron-left"></i></a>
            @endif

            {{-- Pagination Number Elements --}}
            @foreach ($folios->getUrlRange(max(1, $folios->currentPage() - 2), min($folios->lastPage(), $folios->currentPage() + 2)) as $page => $url)
              @if ($page == $folios->currentPage())
                <span class="pms-page-btn active">{{ $page }}</span>
              @else
                <a href="{{ $url }}" class="pms-page-btn">{{ $page }}</a>
              @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($folios->hasMorePages())
              <a href="{{ $folios->nextPageUrl() }}" class="pms-page-btn" title="Next Page"><i class="fa-solid fa-chevron-right"></i></a>
            @else
              <span class="pms-page-btn" style="opacity: 0.4; cursor: not-allowed;" title="Last Page"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
          @endif
        </div>
    </div>
  </div>

  </div>
</main>

<!-- ========================================================================= -->
<!-- EXTENSIVE FOLIO DOSSIER MODAL ("huge details", occupants, billing, settle) -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="folio-details-modal" style="z-index: 100050;">
  <div class="modal-window large" style="width: 920px; max-width: 95vw; height: 90vh; max-height: 90vh; display: flex; flex-direction: column; border-radius: 18px; overflow: hidden; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4); border: none; box-sizing: border-box; background: #ffffff;">
    
    <!-- Modal Header -->
    <div class="modal-top" style="flex-shrink: 0; background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #ffffff; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.15);">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 20px;">
          <i class="fa-solid fa-file-invoice-dollar" style="color: #a5b4fc;"></i>
        </div>
        <div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #ffffff;" id="fmdl-reserve-id">RES-000</h3>
            <span class="status-badge" id="fmdl-status-badge">Confirmed</span>
          </div>
          <div style="font-size: 11px; color: #c7d2fe; margin-top: 2px;">
            Folio: <strong id="fmdl-folio-number" style="font-family: var(--font-mono, monospace);">-</strong> • Registered: <span id="fmdl-created-at">-</span>
          </div>
        </div>
      </div>
      <button type="button" class="modal-close" onclick="closeFolioModal()" style="background: rgba(255,255,255,0.15); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center;">&times;</button>
    </div>

    <!-- Modal Scrollable Content Area -->
    <div class="modal-content-area" id="folio-modal-body" style="flex: 1 1 auto; min-height: 0; max-height: calc(90vh - 140px); overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 20px 24px; background: #f8fafc;">
      
      <!-- 1. Top Stay & Guest Overview -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; margin-bottom: 14px;">
        <!-- Primary Guest Contact -->
        <div class="dossier-card" style="margin-bottom: 0;">
          <div class="dossier-card-title">
            <i class="fa-solid fa-user-tie" style="color: #6366f1;"></i> Primary Guest Profile
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Guest Name</span>
            <span class="dossier-val" id="fmdl-guest-name" style="font-size: 13px; font-weight: 800; color: #0f172a;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Mobile Number</span>
            <span class="dossier-val mono" id="fmdl-guest-mobile">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Email Address</span>
            <span class="dossier-val" id="fmdl-guest-email">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">City &amp; Address</span>
            <span class="dossier-val" id="fmdl-guest-address">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">ID Proof Verification</span>
            <span class="dossier-val" id="fmdl-guest-idproof">-</span>
          </div>
          <div class="dossier-field-row" id="fmdl-company-row" style="display: none;">
            <span class="dossier-label">Corporate Account</span>
            <span class="dossier-val" id="fmdl-company-val" style="color: #4f46e5; font-weight: 800;">-</span>
          </div>
        </div>

        <!-- Stay Details & Schedule -->
        <div class="dossier-card" style="margin-bottom: 0;">
          <div class="dossier-card-title">
            <i class="fa-solid fa-calendar-days" style="color: #059669;"></i> Stay Schedule &amp; Plan
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Check-In Arrival</span>
            <span class="dossier-val" id="fmdl-checkin-sched">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Check-Out Departure</span>
            <span class="dossier-val" id="fmdl-checkout-sched">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Total Duration</span>
            <span class="dossier-val mono" id="fmdl-nights-sched">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Meal Plan Package</span>
            <span class="dossier-val" id="fmdl-plan-name" style="color: #d97706; font-weight: 800;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Total Guests Registered</span>
            <span class="dossier-val" id="fmdl-total-guests-count" style="font-weight: 800; color: #4338ca;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Reservation Mode</span>
            <span class="dossier-val" id="fmdl-reservation-mode">-</span>
          </div>
        </div>
      </div>

      <!-- 2. Rooms & Occupants Breakdown (kon room a kon kon guestache tader minimal details) -->
      <div class="dossier-card">
        <div class="dossier-card-title">
          <i class="fa-solid fa-bed" style="color: #4f46e5;"></i> Allocated Rooms &amp; Registered Occupants
        </div>
        <div id="fmdl-rooms-container">
          <!-- Dynamically populated room cards -->
        </div>
      </div>

      <!-- 3. Billing & Financial Dossier (total billings & all) -->
      <div class="dossier-card">
        <div class="dossier-card-title">
          <i class="fa-solid fa-receipt" style="color: #059669;"></i> Total Billings &amp; Financial Ledger
        </div>

        <!-- Metric Counter Grid -->
        <div class="bill-stat-grid">
          <div class="bill-stat-item">
            <div class="bill-stat-label">Gross Charges</div>
            <div class="bill-stat-value" id="fmdl-gross-amount">₹ 0.00</div>
          </div>
          <div class="bill-stat-item">
            <div class="bill-stat-label">Discount</div>
            <div class="bill-stat-value" id="fmdl-discount-amount" style="color: #059669;">₹ 0.00</div>
          </div>
          <div class="bill-stat-item">
            <div class="bill-stat-label">Tax (GST)</div>
            <div class="bill-stat-value" id="fmdl-tax-amount" style="color: #6366f1;">₹ 0.00</div>
          </div>
          <div class="bill-stat-item">
            <div class="bill-stat-label">Net Payable</div>
            <div class="bill-stat-value" id="fmdl-payable-amount" style="color: #0f172a; font-weight: 900;">₹ 0.00</div>
          </div>
          <div class="bill-stat-item">
            <div class="bill-stat-label">Total Paid</div>
            <div class="bill-stat-value" id="fmdl-totalpaid-amount" style="color: #059669;">₹ 0.00</div>
          </div>
          <div class="bill-stat-item" id="fmdl-balance-box">
            <div class="bill-stat-label" id="fmdl-balance-label">Outstanding Due</div>
            <div class="bill-stat-value" id="fmdl-balance-amount" style="color: #e11d48;">₹ 0.00</div>
          </div>
        </div>

        <!-- Itemized Breakdown Rows -->
        <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 8px; padding: 10px 14px;">
          <div class="dossier-field-row">
            <span class="dossier-label">Room Rate × Total Nights</span>
            <span class="dossier-val mono" id="fmdl-calc-row">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Advance Deposit (Booking Time)</span>
            <span class="dossier-val mono" id="fmdl-advance-row" style="color: #059669;">₹ 0.00</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Settlement Amount Paid</span>
            <span class="dossier-val mono" id="fmdl-settlement-row" style="color: #4f46e5; font-weight: 800;">₹ 0.00</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Applicable Discount</span>
            <span class="dossier-val mono" id="fmdl-discount-row" style="color: #059669;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">GST / Tax Calculation</span>
            <span class="dossier-val mono" id="fmdl-tax-row" style="color: #6366f1;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Payment Mode / Terms</span>
            <span class="dossier-val" id="fmdl-paymode-row">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Booking Remarks</span>
            <span class="dossier-val" id="fmdl-remarks-row" style="color: #64748b;">-</span>
          </div>
        </div>

        <!-- Settlements History Ledger -->
        <div style="margin-top: 14px;" id="fmdl-settle-history-box">
          <div style="font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            <i class="fa-solid fa-clock-rotate-left"></i> Settlement Receipts History
          </div>
          <div id="fmdl-settle-history-list" style="font-size: 11px;">
            <!-- Populated via JS -->
          </div>
        </div>
      </div>

      <!-- 4. Interactive Settlement Form Area (Shown when balance > 0) -->
      <div id="fmdl-settlement-interactive-panel" class="settlement-panel" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
          <div style="font-size: 12px; font-weight: 800; color: #065f46; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-calculator"></i> Process Folio Settlement
          </div>
          <button type="button" onclick="setFullFolioSettlementAmount()" style="background: none; border: none; color: #047857; font-weight: 800; cursor: pointer; text-decoration: underline; font-size: 11px; padding: 0;">
            Pay Full Due (<span id="fmdl-btn-payfull-val">₹ 0.00</span>)
          </button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-bottom: 10px;">
          <div>
            <label style="display: block; font-size: 11px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">
              Settlement Amount (₹) <span style="color: #e11d48;">*</span>
            </label>
            <div style="position: relative;">
              <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-weight: 800; color: #64748b; font-size: 13px;">₹</span>
              <input type="number" 
                     step="0.01" 
                     min="0.01" 
                     id="fmdl-input-settle-amount" 
                     style="width: 100%; box-sizing: border-box; padding: 8px 10px 8px 24px; border-radius: 8px; border: 1.5px solid #a7f3d0; font-size: 13px; font-weight: 800; font-family: var(--font-mono, monospace); color: #0f172a; outline: none; background: #ffffff;">
            </div>
          </div>

          <div>
            <label style="display: block; font-size: 11px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">
              Payment Method <span style="color: #e11d48;">*</span>
            </label>
            <select id="fmdl-select-settle-mode" style="width: 100%; box-sizing: border-box; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #a7f3d0; font-size: 12px; font-weight: 700; color: #1e293b; background: #ffffff; outline: none;">
              @foreach($paymentModes ?? [] as $pm)
                <option value="{{ $pm->id }}" {{ strtolower($pm->name) === 'cash' ? 'selected' : '' }}>{{ $pm->name }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label style="display: block; font-size: 11px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">
              Txn / Reference ID (Optional)
            </label>
            <input type="text" 
                   id="fmdl-input-settle-txn" 
                   placeholder="e.g. UPI-998234, CARD-1049" 
                   style="width: 100%; box-sizing: border-box; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #a7f3d0; font-size: 12px; font-family: var(--font-mono, monospace); outline: none; background: #ffffff;">
          </div>
        </div>

        <div style="margin-bottom: 12px;">
          <label style="display: block; font-size: 11px; font-weight: 800; color: #1e293b; margin-bottom: 4px;">
            Settlement Notes / Remarks
          </label>
          <input type="text" 
                 id="fmdl-input-settle-remarks" 
                 placeholder="e.g. Counter settlement finalized with primary guest." 
                 style="width: 100%; box-sizing: border-box; padding: 8px 10px; border-radius: 8px; border: 1.5px solid #a7f3d0; font-size: 12px; outline: none; background: #ffffff;">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px;">
          <button type="button" 
                  id="fmdl-btn-confirm-settlement" 
                  onclick="executeFolioSettlement()" 
                  style="padding: 8px 22px; border-radius: 8px; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; border: none; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 4px 12px rgba(5,150,105,0.25);">
            <i class="fa-solid fa-check-circle"></i> Confirm &amp; Save Settlement
          </button>
        </div>
      </div>

    </div>

    <!-- Modal Footer Actions (Settle, Checkout, Print, Close) -->
    <div class="modal-bot" style="flex-shrink: 0; padding: 12px 24px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
      <div style="font-size: 11px; color: #64748b; font-weight: 600;">
        <i class="fa-solid fa-hotel"></i> Hotel Sagar Sonnet • Folio Accounting
      </div>

      <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        <!-- Settle Balance Button: shown when balance > 0 -->
        <button type="button" 
                id="fmdl-action-btn-settle" 
                onclick="toggleFolioSettlementPanel()" 
                style="padding: 8px 20px; border-radius: 8px; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; border: none; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 4px 12px rgba(5,150,105,0.25);">
          <i class="fa-solid fa-calculator"></i> Settle Balance
        </button>

        <!-- Check Out Button: shown ONLY after settlement complete (balance <= 0) and not Checked Out -->
        <button type="button" 
                id="fmdl-action-btn-checkout" 
                onclick="executeFolioCheckout()" 
                style="padding: 8px 20px; border-radius: 8px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; border: none; font-size: 12px; font-weight: 800; display: none; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 4px 12px rgba(99,102,241,0.25);">
          <i class="fa-solid fa-right-from-bracket"></i> Check Out
        </button>

        <!-- Locked / Finalized Badge: shown when status is 'Checked Out' -->
        <div id="fmdl-badge-locked" 
             style="display: none; padding: 6px 14px; border-radius: 8px; background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; font-size: 12px; font-weight: 800; align-items: center; gap: 6px;">
          <i class="fa-solid fa-lock" style="color: #64748b;"></i> Stay Finalized &amp; Checked Out (Locked)
        </div>

        <!-- Print Folio Invoice Button -->
        <button type="button" class="btn-ui-secondary" onclick="printFolioInvoice()" style="padding: 8px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 12px; font-weight: 700; color: #334155; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
          <i class="fa-solid fa-print"></i> Print Invoice
        </button>

        <!-- Close Button -->
        <button type="button" class="btn-ui-secondary" onclick="closeFolioModal()" style="padding: 8px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 12px; font-weight: 700; color: #64748b; cursor: pointer;">
          Close
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Hidden Clean Print Template Area -->
<div id="folio-print-area" style="display: none;"></div>

@endsection

@push('scripts')
<script>
  window.allFoliosData = @json($serializedFolios);
  let activeFolioData = null;

  // Currency helper
  function formatINR(val) {
    return '₹ ' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // HTML escaping helper
  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Open Folio Modal by Booking ID (forceFresh = true fetches fresh DB data via AJAX)
  function openFolioModalById(bookingId, forceFresh = false) {
    let folio = (!forceFresh && Array.isArray(window.allFoliosData)) 
      ? window.allFoliosData.find(f => Number(f.id) === Number(bookingId)) 
      : null;

    if (folio) {
      populateAndOpenFolioModal(folio);
    } else {
      // Fetch fresh via AJAX
      fetch(`{{ url('front-office/folio') }}/${bookingId}/details`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success && data.folio) {
          if (!Array.isArray(window.allFoliosData)) {
            window.allFoliosData = [];
          }
          const idx = window.allFoliosData.findIndex(f => Number(f.id) === Number(bookingId));
          if (idx !== -1) {
            window.allFoliosData[idx] = data.folio;
          } else {
            window.allFoliosData.push(data.folio);
          }
          populateAndOpenFolioModal(data.folio);
        } else {
          alert('Could not fetch folio details.');
        }
      })
      .catch(err => {
        console.error('Error fetching folio details:', err);
        alert('Failed to load folio details.');
      });
    }
  }

  function populateAndOpenFolioModal(folio) {
    if (!folio) return;
    activeFolioData = folio;

    try {
      // Header info
      const elResId = document.getElementById('fmdl-reserve-id');
      if (elResId) elResId.textContent = folio.reserve_id || ('RES-' + folio.id);
      const elFolioNo = document.getElementById('fmdl-folio-number');
      if (elFolioNo) elFolioNo.textContent = folio.folio_number || '-';
      const elCreatedAt = document.getElementById('fmdl-created-at');
      if (elCreatedAt) elCreatedAt.textContent = folio.created_at_fmt || 'N/A';

      // Status Badge
      const statusBadge = document.getElementById('fmdl-status-badge');
      if (statusBadge) {
        const normStatus = (folio.status || 'Confirmed').toLowerCase();
        statusBadge.textContent = folio.status || 'Confirmed';
        statusBadge.className = 'status-badge ' + (
          ['settlement done', 'settled'].includes(normStatus) ? 'settled' :
          ['checked out', 'checkedout'].includes(normStatus) ? 'checkedout' :
          ['checked in', 'checkedin'].includes(normStatus) ? 'checkedin' :
          normStatus
        );
      }

      // Primary Guest Profile
      const pg = folio.primary_guest || {};
      const elName = document.getElementById('fmdl-guest-name');
      if (elName) elName.textContent = pg.name || 'Guest';
      const elMobile = document.getElementById('fmdl-guest-mobile');
      if (elMobile) elMobile.textContent = pg.mobile || 'N/A';
      const elEmail = document.getElementById('fmdl-guest-email');
      if (elEmail) elEmail.textContent = pg.email || 'N/A';
      const elAddress = document.getElementById('fmdl-guest-address');
      if (elAddress) elAddress.textContent = (pg.city ? pg.city + ' • ' : '') + (pg.address || 'N/A');
      const elIdProof = document.getElementById('fmdl-guest-idproof');
      if (elIdProof) elIdProof.textContent = (pg.id_card_type || 'ID Proof') + ': ' + (pg.id_card_number || 'N/A');

      // Corporate Account
      const compRow = document.getElementById('fmdl-company-row');
      const compVal = document.getElementById('fmdl-company-val');
      if (compRow && compVal) {
        if (folio.company && folio.company.name) {
          compRow.style.display = 'flex';
          compVal.textContent = folio.company.name + (folio.company.gstin ? ' (GSTIN: ' + folio.company.gstin + ')' : '');
        } else {
          compRow.style.display = 'none';
        }
      }

      // Stay schedule
      const elCin = document.getElementById('fmdl-checkin-sched');
      if (elCin) elCin.textContent = `${folio.checkin_date_fmt || ''} at ${folio.checkin_time_fmt || ''}`;
      const elCout = document.getElementById('fmdl-checkout-sched');
      if (elCout) elCout.textContent = `${folio.checkout_date_fmt || ''} at ${folio.checkout_time_fmt || ''}`;
      const elNights = document.getElementById('fmdl-nights-sched');
      if (elNights) elNights.textContent = `${folio.total_nights || 1} Night(s)`;
      const elPlan = document.getElementById('fmdl-plan-name');
      if (elPlan) elPlan.textContent = folio.plan_name || 'Room Only (EP)';
      const elGuests = document.getElementById('fmdl-total-guests-count');
      if (elGuests) elGuests.textContent = `${folio.total_guests_count || 1} Person(s) (${(folio.rooms || []).length} Room(s))`;
      const elResMode = document.getElementById('fmdl-reservation-mode');
      if (elResMode) elResMode.textContent = folio.reservation_mode || 'Front Desk';

      // Rooms & Occupants breakdown
      renderRoomsOccupants(folio.rooms || []);

      // Billings & Financial Ledger
      renderBillingsLedger(folio);

      // Configure Settle vs Checkout Buttons
      configureFolioActions(folio);

      // Reset settlement interactive panel
      const setPanel = document.getElementById('fmdl-settlement-interactive-panel');
      if (setPanel) setPanel.style.display = 'none';
    } catch (e) {
      console.error('Error populating folio modal details:', e);
    }

    // Reliably open modal window
    const modalEl = document.getElementById('folio-details-modal');
    if (modalEl) {
      modalEl.style.display = 'flex';
      modalEl.classList.add('open');
    }
    document.body.style.overflow = 'hidden';

    // Reset scroll to top
    const modalBody = document.getElementById('folio-modal-body');
    if (modalBody) {
      modalBody.scrollTop = 0;
    }
  }

  function renderRoomsOccupants(rooms) {
    const container = document.getElementById('fmdl-rooms-container');
    if (!rooms || rooms.length === 0) {
      container.innerHTML = '<div style="color: #94a3b8; font-size: 11px; font-style: italic;">No specific room allocated.</div>';
      return;
    }

    let html = '';
    rooms.forEach(r => {
      html += `
        <div class="room-alloc-card">
          <div class="room-alloc-header">
            <div style="display: flex; align-items: center; gap: 8px;">
              <span class="room-tag">
                <i class="fa-solid fa-door-open"></i> ${escapeHtml(r.room_number)}
              </span>
              <span style="font-size: 12px; font-weight: 800; color: #0f172a;">Floor ${escapeHtml(r.floor)}</span>
              <span style="color: #cbd5e1;">•</span>
              <span style="font-size: 11px; color: #64748b; font-weight: 600;">${escapeHtml(r.category)}</span>
              <span style="color: #cbd5e1;">•</span>
              <span style="font-size: 11px; color: #64748b;">${escapeHtml(r.bedding)}</span>
            </div>
            <div style="font-size: 11px; font-family: var(--font-mono, monospace); font-weight: 800; color: #4338ca;">
              ${formatINR(r.rate)} / night
            </div>
          </div>
          <div>
            <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 5px;">
              Registered Occupants in this Room:
            </div>
            <div style="display: flex; flex-direction: column; gap: 4px;">
      `;

      if (r.occupants && r.occupants.length > 0) {
        r.occupants.forEach(occ => {
          html += `
            <div class="occupant-badge-row">
              <div style="display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid ${occ.is_primary ? 'fa-star text-amber-500' : 'fa-user text-slate-400'}" style="color: ${occ.is_primary ? '#f59e0b' : '#94a3b8'};"></i>
                <strong style="color: #1e293b;">${escapeHtml(occ.name)}</strong>
                ${occ.is_primary ? '<span style="background: #fef3c7; color: #b45309; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 4px;">Primary</span>' : ''}
              </div>
              <div style="display: flex; align-items: center; gap: 12px; color: #64748b; font-family: var(--font-mono, monospace);">
                <span><i class="fa-solid fa-phone" style="font-size: 9px;"></i> ${escapeHtml(occ.mobile)}</span>
                <span>•</span>
                <span>${escapeHtml(occ.id_card_type)}: ${escapeHtml(occ.id_card_number)}</span>
              </div>
            </div>
          `;
        });
      } else {
        html += '<div style="font-size: 11px; color: #94a3b8;">No occupants assigned yet.</div>';
      }

      html += `
            </div>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;
  }

  function renderBillingsLedger(folio) {
    document.getElementById('fmdl-gross-amount').textContent = formatINR(folio.total_amount);
    document.getElementById('fmdl-discount-amount').textContent = formatINR(folio.discount_amount);
    document.getElementById('fmdl-tax-amount').textContent = formatINR(folio.gst_amount);
    document.getElementById('fmdl-payable-amount').textContent = formatINR(folio.payable_amount);
    document.getElementById('fmdl-totalpaid-amount').textContent = formatINR(folio.total_paid);

    const balanceBox = document.getElementById('fmdl-balance-box');
    const balanceAmountEl = document.getElementById('fmdl-balance-amount');
    const balanceLabelEl = document.getElementById('fmdl-balance-label');

    balanceAmountEl.textContent = formatINR(folio.balance);
    if (folio.balance > 0.01) {
      balanceAmountEl.style.color = '#e11d48';
      balanceLabelEl.textContent = 'Outstanding Due';
    } else {
      balanceAmountEl.style.color = '#059669';
      balanceLabelEl.textContent = 'Fully Settled';
    }

    // Breakdown details
    document.getElementById('fmdl-calc-row').textContent = `${formatINR(folio.room_rate)} × ${folio.total_nights} Nights = ${formatINR(folio.total_amount)}`;
    document.getElementById('fmdl-advance-row').textContent = formatINR(folio.advance_amount);
    document.getElementById('fmdl-settlement-row').textContent = formatINR(folio.settlement_paid);
    document.getElementById('fmdl-discount-row').textContent = folio.discount_amount > 0 ? `${formatINR(folio.discount_amount)} (${folio.discount_percentage}%)` : 'None';
    document.getElementById('fmdl-tax-row').textContent = folio.gst_amount > 0 ? `${formatINR(folio.gst_amount)} (${folio.gst_percentage}% ${folio.gst_type})` : 'Zero Tax / Exempt';
    document.getElementById('fmdl-paymode-row').textContent = folio.payment_mode || 'Cash / Direct';
    document.getElementById('fmdl-remarks-row').textContent = folio.payment_remarks || 'None';

    // Settlement History List
    const historyBox = document.getElementById('fmdl-settle-history-box');
    const historyList = document.getElementById('fmdl-settle-history-list');
    if (folio.settlements_history && folio.settlements_history.length > 0) {
      historyBox.style.display = 'block';
      let hHtml = `
        <table style="width: 100%; border-collapse: collapse; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
          <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
              <th style="padding: 6px 10px; font-size: 10px; color: #64748b;">Receipt #</th>
              <th style="padding: 6px 10px; font-size: 10px; color: #64748b;">Date &amp; Time</th>
              <th style="padding: 6px 10px; font-size: 10px; color: #64748b;">Method</th>
              <th style="padding: 6px 10px; font-size: 10px; color: #64748b; text-align: right;">Amount Paid</th>
              <th style="padding: 6px 10px; font-size: 10px; color: #64748b;">Remarks</th>
            </tr>
          </thead>
          <tbody>
      `;
      folio.settlements_history.forEach(sh => {
        hHtml += `
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 6px 10px; font-family: var(--font-mono, monospace); font-weight: 700; color: #4f46e5;">${escapeHtml(sh.settlement_number)}</td>
            <td style="padding: 6px 10px; color: #334155;">${escapeHtml(sh.date)} ${escapeHtml(sh.time)}</td>
            <td style="padding: 6px 10px; font-weight: 700; color: #0f172a;">${escapeHtml(sh.payment_mode)}</td>
            <td style="padding: 6px 10px; text-align: right; font-family: var(--font-mono, monospace); font-weight: 800; color: #059669;">${formatINR(sh.amount)}</td>
            <td style="padding: 6px 10px; color: #64748b;">${escapeHtml(sh.remarks)}</td>
          </tr>
        `;
      });
      hHtml += '</tbody></table>';
      historyList.innerHTML = hHtml;
    } else {
      historyBox.style.display = 'none';
      historyList.innerHTML = '';
    }
  }

  function configureFolioActions(folio) {
    const btnSettle = document.getElementById('fmdl-action-btn-settle');
    const btnCheckout = document.getElementById('fmdl-action-btn-checkout');
    const badgeLocked = document.getElementById('fmdl-badge-locked');

    const isCheckedOut = (folio.status || '').toLowerCase() === 'checked out';
    const isSettled = (folio.status || '').toLowerCase() === 'settlement done' || Number(folio.balance) <= 0.01;

    if (isCheckedOut) {
      btnSettle.style.display = 'none';
      btnCheckout.style.display = 'none';
      badgeLocked.style.display = 'inline-flex';
      badgeLocked.innerHTML = '<i class="fa-solid fa-lock" style="color: #64748b;"></i> Stay Finalized &amp; Checked Out (Locked)';
    } else if (folio.balance > 0.01) {
      // Balance Due: Show Settle Button, Hide Checkout
      btnSettle.style.display = 'inline-flex';
      btnSettle.innerHTML = `<i class="fa-solid fa-calculator"></i> Settle Balance (${formatINR(folio.balance)})`;
      btnCheckout.style.display = 'none';
      badgeLocked.style.display = 'none';
    } else {
      // Balance is Cleared: Settlement is Done -> Show Check Out Button!
      btnSettle.style.display = 'none';
      btnCheckout.style.display = 'inline-flex';
      btnCheckout.innerHTML = '<i class="fa-solid fa-right-from-bracket"></i> Check Out';
      badgeLocked.style.display = 'none';
    }
  }

  function toggleFolioSettlementPanel() {
    const panel = document.getElementById('fmdl-settlement-interactive-panel');
    if (panel.style.display === 'none' || panel.style.display === '') {
      panel.style.display = 'block';
      if (activeFolioData) {
        document.getElementById('fmdl-input-settle-amount').value = Number(activeFolioData.balance || 0).toFixed(2);
        document.getElementById('fmdl-btn-payfull-val').textContent = formatINR(activeFolioData.balance);
      }
      panel.scrollIntoView({ behavior: 'smooth' });
    } else {
      panel.style.display = 'none';
    }
  }

  function setFullFolioSettlementAmount() {
    if (activeFolioData && activeFolioData.balance > 0) {
      document.getElementById('fmdl-input-settle-amount').value = Number(activeFolioData.balance).toFixed(2);
    }
  }

  function executeFolioSettlement() {
    if (!activeFolioData) return;

    const amountInput = document.getElementById('fmdl-input-settle-amount');
    const settleAmount = parseFloat(amountInput.value);
    const modeSelect = document.getElementById('fmdl-select-settle-mode');
    const txnInput = document.getElementById('fmdl-input-settle-txn');
    const remarksInput = document.getElementById('fmdl-input-settle-remarks');
    const confirmBtn = document.getElementById('fmdl-btn-confirm-settlement');

    if (isNaN(settleAmount) || settleAmount <= 0) {
      alert('Please enter a valid settlement amount greater than 0.');
      amountInput.focus();
      return;
    }

    if (settleAmount > (Number(activeFolioData.balance) + 0.01)) {
      alert(`Settlement amount cannot exceed the current balance of ${formatINR(activeFolioData.balance)}.`);
      amountInput.focus();
      return;
    }

    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';

    fetch(`{{ url('front-office/folio') }}/${activeFolioData.id}/settle`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        amount: settleAmount,
        payment_mode_id: modeSelect.value,
        transaction_id: txnInput.value.trim(),
        remarks: remarksInput.value.trim(),
      })
    })
    .then(res => res.json())
    .then(data => {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Confirm &amp; Save Settlement';

      if (data.success) {
        alert(data.message || 'Settlement processed successfully!');
        // Reload details to refresh ledger and check out button state
        openFolioModalById(activeFolioData.id, true);
      } else {
        alert(data.error || 'Settlement failed. Please check your inputs.');
      }
    })
    .catch(err => {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Confirm &amp; Save Settlement';
      console.error(err);
      alert('An error occurred during settlement submission.');
    });
  }

  function executeFolioCheckout() {
    if (!activeFolioData) return;

    if (activeFolioData.balance > 0.01) {
      alert(`Cannot check out reservation with outstanding balance of ${formatINR(activeFolioData.balance)}. Please complete settlement first.`);
      return;
    }

    if (!confirm(`Are you sure you want to Check Out Booking #${activeFolioData.reserve_id} for ${activeFolioData.primary_guest?.name}? Rooms will be scheduled for Housekeeping.`)) {
      return;
    }

    const btn = document.getElementById('fmdl-action-btn-checkout');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Check Out...';

    fetch(`{{ url('front-office/folio') }}/${activeFolioData.id}/checkout`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        remarks: 'Checked out from Folio Manager Counter'
      })
    })
    .then(res => res.json())
    .then(data => {
      btn.disabled = false;
      if (data.success) {
        alert(data.message || 'Reservation successfully checked out!');
        openFolioModalById(activeFolioData.id, true);
      } else {
        alert(data.error || 'Check out failed.');
      }
    })
    .catch(err => {
      btn.disabled = false;
      console.error(err);
      alert('Network error while processing check out.');
    });
  }

  function closeFolioModal() {
    const modalEl = document.getElementById('folio-details-modal');
    if (modalEl) {
      modalEl.classList.remove('open');
      modalEl.style.display = 'none';
    }
    document.body.style.overflow = '';
    if (typeof closeModal === 'function') {
      closeModal('folio-details-modal');
    }
  }

  // Close on backdrop click & ESC key
  document.addEventListener('DOMContentLoaded', function() {
    const modalBackdrop = document.getElementById('folio-details-modal');
    if (modalBackdrop) {
      modalBackdrop.addEventListener('click', function(e) {
        if (e.target === this) {
          closeFolioModal();
        }
      });
    }
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeFolioModal();
      }
    });
  });

  function printFolioInvoice() {
    if (!activeFolioData) return;
    const f = activeFolioData;
    const pg = f.primary_guest || {};

    let roomRowsHtml = '';
    (f.rooms || []).forEach(r => {
      const occupantNames = (r.occupants || []).map(o => o.name).join(', ');
      roomRowsHtml += `
        <tr>
          <td style="padding: 8px; border: 1px solid #e2e8f0; font-weight: bold;">${escapeHtml(r.room_number)} (Floor ${escapeHtml(r.floor)})</td>
          <td style="padding: 8px; border: 1px solid #e2e8f0;">${escapeHtml(r.category)} • ${escapeHtml(r.bedding)}</td>
          <td style="padding: 8px; border: 1px solid #e2e8f0;">${escapeHtml(occupantNames || 'Primary Guest')}</td>
          <td style="padding: 8px; border: 1px solid #e2e8f0; text-align: right; font-family: monospace;">${formatINR(r.rate)}</td>
        </tr>
      `;
    });

    const printHtml = `
      <div style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; padding: 24px; max-width: 800px; margin: auto;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #312e81; padding-bottom: 14px; margin-bottom: 16px;">
          <div>
            <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #1e1b4b;">HOTEL SAGAR SONNET</h1>
            <div style="font-size: 11px; color: #64748b; margin-top: 3px;">Luxury Stays &amp; Hospitality • Front Desk Folio Invoice</div>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 18px; font-weight: 900; color: #4338ca; font-family: monospace;">${escapeHtml(f.reserve_id)}</div>
            <div style="font-size: 11px; color: #64748b;">Folio: ${escapeHtml(f.folio_number)}</div>
            <div style="font-size: 11px; color: #64748b;">Date: ${escapeHtml(f.created_at_fmt)}</div>
          </div>
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 18px; font-size: 12px; background: #f8fafc; padding: 12px; border-radius: 8px;">
          <div>
            <div style="font-weight: 800; color: #334155; text-transform: uppercase;">Guest Profile</div>
            <div style="font-size: 14px; font-weight: 800; color: #0f172a; margin-top: 2px;">${escapeHtml(pg.name)}</div>
            <div style="color: #64748b;">Phone: ${escapeHtml(pg.mobile)} | Email: ${escapeHtml(pg.email)}</div>
            <div style="color: #64748b;">${escapeHtml(pg.address)}</div>
          </div>
          <div style="text-align: right;">
            <div style="font-weight: 800; color: #334155; text-transform: uppercase;">Stay Schedule</div>
            <div><strong>Check-in:</strong> ${escapeHtml(f.checkin_date_fmt)} (${escapeHtml(f.checkin_time_fmt)})</div>
            <div><strong>Check-out:</strong> ${escapeHtml(f.checkout_date_fmt)} (${escapeHtml(f.checkout_time_fmt)})</div>
            <div><strong>Duration:</strong> ${f.total_nights} Night(s)</div>
          </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 18px;">
          <thead>
            <tr style="background: #eef2ff; color: #312e81; text-align: left;">
              <th style="padding: 8px; border: 1px solid #c7d2fe;">Room &amp; Floor</th>
              <th style="padding: 8px; border: 1px solid #c7d2fe;">Category</th>
              <th style="padding: 8px; border: 1px solid #c7d2fe;">Occupants</th>
              <th style="padding: 8px; border: 1px solid #c7d2fe; text-align: right;">Rate / Night</th>
            </tr>
          </thead>
          <tbody>
            ${roomRowsHtml}
          </tbody>
        </table>

        <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
          <div style="width: 320px; font-size: 12px;">
            <div style="display: flex; justify-content: space-between; padding: 4px 0;">
              <span>Room Rate × Nights:</span>
              <span style="font-family: monospace;">${formatINR(f.total_amount)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 4px 0; color: #059669;">
              <span>Discount Applied:</span>
              <span style="font-family: monospace;">- ${formatINR(f.discount_amount)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 4px 0; color: #6366f1;">
              <span>Tax / GST:</span>
              <span style="font-family: monospace;">+ ${formatINR(f.gst_amount)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; font-weight: 800; border-top: 1.5px solid #cbd5e1; border-bottom: 1.5px solid #cbd5e1;">
              <span>Net Grand Total:</span>
              <span style="font-family: monospace;">${formatINR(f.payable_amount)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 4px 0; color: #059669;">
              <span>Advance Paid:</span>
              <span style="font-family: monospace;">${formatINR(f.advance_amount)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 4px 0; color: #4338ca;">
              <span>Settlement Paid:</span>
              <span style="font-family: monospace;">${formatINR(f.settlement_paid)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; font-weight: 800; color: #059669;">
              <span>Total Paid:</span>
              <span style="font-family: monospace;">${formatINR(f.total_paid)}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; font-weight: 900; color: ${f.balance > 0 ? '#e11d48' : '#059669'};">
              <span>Balance Due:</span>
              <span style="font-family: monospace;">${formatINR(f.balance)}</span>
            </div>
          </div>
        </div>

        <div style="border-top: 1px dashed #cbd5e1; padding-top: 14px; display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: #64748b;">
          <div>Status: <strong style="text-transform: uppercase;">${escapeHtml(f.status)}</strong></div>
          <div>Authorized Front Desk Signature: ______________________</div>
        </div>
      </div>
    `;

    const printWin = window.open('', '_blank', 'width=900,height=800');
    if (printWin) {
      printWin.document.open();
      printWin.document.write(`
        <!DOCTYPE html>
        <html>
          <head>
            <title>Folio Invoice - ${f.reserve_id}</title>
            <style>
              @media print {
                body { margin: 0; padding: 0; }
              }
            </style>
          </head>
          <body>
            ${printHtml}
            <script>
              window.onload = function() {
                window.print();
              };
            <\/script>
          </body>
        </html>
      `);
      printWin.document.close();
    }
  }
</script>
@endpush
