@extends('frontoffice.layouts.app')

@section('title', 'Accounts & Financial Ledgers - Hotel Sagar Sonnet')

@push('styles')
<style>
  :root {
    --acc-primary: #6366f1;
    --acc-primary-dark: #4f46e5;
    --acc-emerald: #10b981;
    --acc-emerald-dark: #059669;
    --acc-cyan: #06b6d4;
    --acc-amber: #f59e0b;
    --acc-rose: #f43f5e;
    --acc-violet: #8b5cf6;
    --acc-slate: #0f172a;
    --acc-border: #e2e8f0;
  }

  .accounts-wrapper {
    padding: 18px 24px 30px;
    background: #f8fafc;
    min-height: calc(100vh - 120px);
  }

  /* Filter Ribbon Card */
  .accounts-filter-card {
    background: #ffffff;
    border: 1px solid var(--acc-border);
    border-radius: 12px;
    padding: 14px 18px;
    margin-bottom: 18px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  .quick-date-pills {
    display: inline-flex;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 8px;
    gap: 2px;
  }

  .quick-date-btn {
    border: none;
    background: transparent;
    color: #475569;
    font-size: 11.5px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
  }

  .quick-date-btn:hover {
    color: #0f172a;
    background: rgba(255, 255, 255, 0.6);
  }

  .quick-date-btn.active {
    background: #ffffff;
    color: var(--acc-primary-dark);
    font-weight: 800;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  }

  .date-picker-form {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .acc-input {
    border: 1.5px solid #cbd5e1;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 11.5px;
    font-weight: 600;
    color: #1e293b;
    background: #ffffff;
    outline: none;
    height: 32px;
    transition: all 0.2s ease;
  }

  .acc-input:focus {
    border-color: var(--acc-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }

  .btn-acc-primary {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: #ffffff;
    border: none;
    border-radius: 6px;
    padding: 0 14px;
    height: 32px;
    font-size: 11.5px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
  }

  .btn-acc-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.35);
  }

  .btn-acc-print {
    background: #ffffff;
    color: #334155;
    border: 1.5px solid #cbd5e1;
    border-radius: 6px;
    padding: 0 12px;
    height: 32px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
  }

  .btn-acc-print:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0f172a;
  }

  /* 5 KPI Stat Cards Grid */
  .accounts-kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    margin-bottom: 20px;
  }

  @media (max-width: 1200px) {
    .accounts-kpi-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 768px) {
    .accounts-kpi-grid {
      grid-template-columns: 1fr;
    }
  }

  .acc-kpi-card {
    background: #ffffff;
    border: 1px solid var(--acc-border);
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.02);
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .acc-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(15, 23, 42, 0.06);
  }

  .acc-kpi-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .acc-kpi-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
  }

  .acc-kpi-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
  }

  .acc-kpi-val {
    font-size: 20px;
    font-weight: 900;
    font-family: var(--font-mono, monospace);
    color: #0f172a;
    line-height: 1.2;
  }

  .acc-kpi-sub {
    font-size: 10.5px;
    color: #64748b;
    margin-top: 4px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  /* Card Color Variations */
  .card-inflow { border-left: 4px solid #10b981; }
  .card-inflow .acc-kpi-icon { background: #d1fae5; color: #059669; }
  .card-inflow .acc-kpi-val { color: #059669; }

  .card-fo { border-left: 4px solid #6366f1; }
  .card-fo .acc-kpi-icon { background: #e0e7ff; color: #4338ca; }
  .card-fo .acc-kpi-val { color: #4338ca; }

  .card-pos { border-left: 4px solid #06b6d4; }
  .card-pos .acc-kpi-icon { background: #cffafe; color: #0891b2; }
  .card-pos .acc-kpi-val { color: #0891b2; }

  .card-expense { border-left: 4px solid #f43f5e; }
  .card-expense .acc-kpi-icon { background: #ffe4e6; color: #e11d48; }
  .card-expense .acc-kpi-val { color: #e11d48; }

  .card-net { border-left: 4px solid #8b5cf6; }
  .card-net .acc-kpi-icon { background: #ede9fe; color: #7c3aed; }
  .card-net .acc-kpi-val { color: #7c3aed; }

  /* Main Workspace Tabs Container */
  .accounts-main-card {
    background: #ffffff;
    border: 1px solid var(--acc-border);
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
    overflow: hidden;
  }

  .acc-nav-tabs {
    display: flex;
    background: #f8fafc;
    border-bottom: 1px solid var(--acc-border);
    padding: 0 16px;
    gap: 4px;
    overflow-x: auto;
  }

  .acc-tab-btn {
    border: none;
    background: transparent;
    padding: 13px 18px;
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    transition: all 0.2s ease;
  }

  .acc-tab-btn:hover {
    color: #0f172a;
    background: rgba(241, 245, 249, 0.7);
  }

  .acc-tab-btn.active {
    color: var(--acc-primary-dark);
    font-weight: 800;
    border-bottom-color: var(--acc-primary);
    background: #ffffff;
  }

  .acc-tab-badge {
    background: #e2e8f0;
    color: #475569;
    font-size: 9.5px;
    font-weight: 800;
    padding: 1px 7px;
    border-radius: 10px;
    font-family: var(--font-mono, monospace);
  }

  .acc-tab-btn.active .acc-tab-badge {
    background: #e0e7ff;
    color: #4338ca;
  }

  /* Tab Panes */
  .acc-tab-pane {
    display: none;
    padding: 20px;
  }

  .acc-tab-pane.active {
    display: block;
  }

  /* Custom Clean Table */
  .acc-table-box {
    border: 1px solid var(--acc-border);
    border-radius: 8px;
    overflow-x: auto;
    background: #ffffff;
  }

  .acc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    text-align: left;
  }

  .acc-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 11px 14px;
    border-bottom: 1.5px solid #e2e8f0;
    white-space: nowrap;
  }

  .acc-table td {
    padding: 11px 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
  }

  .acc-table tbody tr:hover {
    background-color: #f8faff;
  }

  .acc-table tfoot td {
    background: #f8fafc;
    font-weight: 800;
    border-top: 2px solid #e2e8f0;
    padding: 12px 14px;
  }

  /* Tag Badges */
  .badge-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 6px;
    white-space: nowrap;
  }

  .badge-tag.green { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .badge-tag.indigo { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
  .badge-tag.amber { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
  .badge-tag.rose { background: #ffe4e6; color: #e11d48; border: 1px solid #fecdd3; }
  .badge-tag.slate { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
  .badge-tag.cyan { background: #cffafe; color: #0e7490; border: 1px solid #a5f3fc; }

  /* Two Column Financial Sections */
  .acc-two-col {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 16px;
  }

  @media (max-width: 900px) {
    .acc-two-col {
      grid-template-columns: 1fr;
    }
  }

  .acc-card-sec {
    background: #ffffff;
    border: 1px solid var(--acc-border);
    border-radius: 10px;
    padding: 16px;
  }

  .acc-card-title {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1.5px solid #f1f5f9;
  }

  .acc-row-detail {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    font-size: 12px;
    border-bottom: 1px dashed #f1f5f9;
  }

  .acc-row-detail:last-child {
    border-bottom: none;
  }

  .acc-detail-lbl {
    color: #64748b;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .acc-detail-val {
    font-weight: 800;
    font-family: var(--font-mono, monospace);
    color: #0f172a;
  }

  /* Printable View */
  @media print {
    body * {
      visibility: hidden;
    }
    #printable-accounts-area, #printable-accounts-area * {
      visibility: visible;
    }
    #printable-accounts-area {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      background: #ffffff;
      padding: 10px;
    }
    .no-print {
      display: none !important;
    }
  }
</style>
@endpush

@section('content')
<div class="accounts-wrapper">

  <!-- 1. Header & Date Range Filter Bar -->
  <div class="accounts-filter-card">
    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
      <div>
        <h1 style="font-size: 17px; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
          <i class="fa-solid fa-file-invoice-dollar" style="color: #6366f1;"></i>
          <span>Accounts &amp; Financial Master Ledger</span>
        </h1>
        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
          Audited Real-time Reconciliations: Front Office &bull; Kitchen Stores &bull; POS Dining
        </div>
      </div>

      <!-- Quick Preset Date Pills -->
      <div class="quick-date-pills">
        <a href="{{ route('accounts.index', ['date_filter' => 'today', 'search' => $search]) }}" class="quick-date-btn {{ $filterType === 'today' ? 'active' : '' }}">Today</a>
        <a href="{{ route('accounts.index', ['date_filter' => 'yesterday', 'search' => $search]) }}" class="quick-date-btn {{ $filterType === 'yesterday' ? 'active' : '' }}">Yesterday</a>
        <a href="{{ route('accounts.index', ['date_filter' => 'this_week', 'search' => $search]) }}" class="quick-date-btn {{ $filterType === 'this_week' ? 'active' : '' }}">This Week</a>
        <a href="{{ route('accounts.index', ['date_filter' => 'this_month', 'search' => $search]) }}" class="quick-date-btn {{ $filterType === 'this_month' ? 'active' : '' }}">This Month</a>
        <a href="{{ route('accounts.index', ['date_filter' => 'all', 'search' => $search]) }}" class="quick-date-btn {{ $filterType === 'all' ? 'active' : '' }}">All Time</a>
      </div>
    </div>

    <!-- Custom Date Range & Search Form -->
    <form method="GET" action="{{ route('accounts.index') }}" class="date-picker-form">
      <input type="hidden" name="date_filter" value="custom">
      <div style="display: flex; align-items: center; gap: 6px;">
        <span style="font-size: 11px; font-weight: 700; color: #64748b;">From:</span>
        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="acc-input">
      </div>
      <div style="display: flex; align-items: center; gap: 6px;">
        <span style="font-size: 11px; font-weight: 700; color: #64748b;">To:</span>
        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="acc-input">
      </div>
      <div style="position: relative;">
        <input type="text" name="search" value="{{ $search }}" placeholder="Search guest, order, vendor..." class="acc-input" style="width: 170px;">
      </div>
      <button type="submit" class="btn-acc-primary">
        <i class="fa-solid fa-filter"></i> Apply Filter
      </button>
      <button type="button" class="btn-acc-print" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Print Ledger
      </button>
    </form>
  </div>

  <!-- Active Date Filter Pill Banner -->
  <div style="margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;">
    <div style="font-size: 11.5px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 6px;">
      <i class="fa-regular fa-calendar-check" style="color: #6366f1;"></i>
      <span>Active Accounting Period:</span>
      <span class="badge-tag indigo" style="font-size: 11px; padding: 3px 10px;">{{ $filterLabel }}</span>
      @if($search)
        <span class="badge-tag amber">Keyword: "{{ $search }}"</span>
        <a href="{{ route('accounts.index', ['date_filter' => $filterType]) }}" style="font-size: 11px; color: #e11d48; text-decoration: none; font-weight: 800; margin-left: 6px;">Clear Search &times;</a>
      @endif
    </div>
    <div style="font-size: 11px; color: #64748b; font-weight: 600;">
      Hotel Sagar Sonnet &bull; Main Luxury Wing PMS Audit
    </div>
  </div>

  <!-- 2. Top 5 Key Performance Stat Cards -->
  <div class="accounts-kpi-grid">
    
    <!-- 1. Total Direct Collections -->
    <div class="acc-kpi-card card-inflow">
      <div class="acc-kpi-top">
        <span class="acc-kpi-label">Total Inflow Collected</span>
        <div class="acc-kpi-icon"><i class="fa-solid fa-cash-register"></i></div>
      </div>
      <div class="acc-kpi-val">₹ {{ number_format($totalRealizedCollections, 2) }}</div>
      <div class="acc-kpi-sub">
        <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
        <span>Front Office (₹{{ number_format($foTotalPayments, 0) }}) + POS (₹{{ number_format($posDirectSales, 0) }})</span>
      </div>
    </div>

    <!-- 2. Front Office Revenue -->
    <div class="acc-kpi-card card-fo">
      <div class="acc-kpi-top">
        <span class="acc-kpi-label">Front Office Gross Bill</span>
        <div class="acc-kpi-icon"><i class="fa-solid fa-hotel"></i></div>
      </div>
      <div class="acc-kpi-val">₹ {{ number_format($foTotalGrossBill, 2) }}</div>
      <div class="acc-kpi-sub">
        <span>{{ count($foBookingsLedger) }} Booking(s) &bull; Pending: ₹{{ number_format($foTotalReceivables, 0) }}</span>
      </div>
    </div>

    <!-- 3. POS Dining Gross Sales -->
    <div class="acc-kpi-card card-pos">
      <div class="acc-kpi-top">
        <span class="acc-kpi-label">Restaurant POS Sales</span>
        <div class="acc-kpi-icon"><i class="fa-solid fa-utensils"></i></div>
      </div>
      <div class="acc-kpi-val">₹ {{ number_format($posGrossSales, 2) }}</div>
      <div class="acc-kpi-sub">
        <span>{{ count($posOrdersList) }} Orders &bull; Direct: ₹{{ number_format($posDirectSales, 0) }} &bull; Folio: ₹{{ number_format($posFolioCharged, 0) }}</span>
      </div>
    </div>

    <!-- 4. Kitchen Stores Procurements (Expense) -->
    <div class="acc-kpi-card card-expense">
      <div class="acc-kpi-top">
        <span class="acc-kpi-label">Store Procurement Cost</span>
        <div class="acc-kpi-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
      </div>
      <div class="acc-kpi-val">₹ {{ number_format($storeTotalPurchases, 2) }}</div>
      <div class="acc-kpi-sub">
        <span>{{ $storeInwardCount }} Inward Shipments (+{{ $storeTotalPlatesInward }} Plates Yielded)</span>
      </div>
    </div>

    <!-- 5. Net Operating Balance / Margin -->
    <div class="acc-kpi-card card-net">
      <div class="acc-kpi-top">
        <span class="acc-kpi-label">Net Operating Margin</span>
        <div class="acc-kpi-icon"><i class="fa-solid fa-chart-line"></i></div>
      </div>
      <div class="acc-kpi-val" style="color: {{ $netOperatingPosition >= 0 ? '#15803d' : '#b91c1c' }};">
        ₹ {{ number_format($netOperatingPosition, 2) }}
      </div>
      <div class="acc-kpi-sub">
        <span>(Direct Inflow &minus; Store Purchases)</span>
      </div>
    </div>

  </div>

  <!-- 3. Main Workspace Tabbed Container -->
  <div class="accounts-main-card">
    
    <!-- Tab Navigation Ribbon -->
    <div class="acc-nav-tabs">
      <button type="button" class="acc-tab-btn active" onclick="switchAccTab('tab-overview', this)">
        <i class="fa-solid fa-chart-pie"></i> Executive Overview &amp; P&amp;L
      </button>
      <button type="button" class="acc-tab-btn" onclick="switchAccTab('tab-frontoffice', this)">
        <i class="fa-solid fa-hotel"></i> Front Office Ledger <span class="acc-tab-badge">{{ count($foBookingsLedger) }}</span>
      </button>
      <button type="button" class="acc-tab-btn" onclick="switchAccTab('tab-pos', this)">
        <i class="fa-solid fa-utensils"></i> POS &amp; Restaurant Sales <span class="acc-tab-badge">{{ count($posOrdersList) }}</span>
      </button>
      <button type="button" class="acc-tab-btn" onclick="switchAccTab('tab-stores', this)">
        <i class="fa-solid fa-boxes-stacked"></i> Stores &amp; Procurement <span class="acc-tab-badge">{{ count($storeMovementsList) }}</span>
      </button>
      <button type="button" class="acc-tab-btn" onclick="switchAccTab('tab-receipts', this)">
        <i class="fa-solid fa-receipt"></i> Transaction Receipts Trail <span class="acc-tab-badge">{{ count($unifiedReceipts) }}</span>
      </button>
    </div>

    <!-- TAB 1: EXECUTIVE OVERVIEW & P&L STATEMENT -->
    <div id="tab-overview" class="acc-tab-pane active">
      
      <!-- 2 Columns: Revenue Reconciliations & Payment Drawer -->
      <div class="acc-two-col">
        
        <!-- Section 1: Department Revenue Breakdown -->
        <div class="acc-card-sec">
          <div class="acc-card-title">
            <span><i class="fa-solid fa-building-columns" style="color: #6366f1;"></i> Revenue Breakdown By Department</span>
            <span class="badge-tag indigo">Income Statement</span>
          </div>

          <div class="acc-row-detail">
            <span class="acc-detail-lbl"><i class="fa-solid fa-door-open" style="color: #6366f1;"></i> Room Tariffs &amp; Accommodations:</span>
            <span class="acc-detail-val" style="color: #4338ca;">₹ {{ number_format($foTotalRoomTariffs, 2) }}</span>
          </div>

          <div class="acc-row-detail">
            <span class="acc-detail-lbl"><i class="fa-solid fa-bell-concierge" style="color: #f59e0b;"></i> Extra Services (Folio Transferred):</span>
            <span class="acc-detail-val" style="color: #d97706;">+ ₹ {{ number_format($foTotalExtraCharged, 2) }}</span>
          </div>

          <div class="acc-row-detail">
            <span class="acc-detail-lbl"><i class="fa-solid fa-utensils" style="color: #06b6d4;"></i> POS Dining Sales (Room &bull; Table &bull; Takeaway):</span>
            <span class="acc-detail-val" style="color: #0891b2;">₹ {{ number_format($posGrossSales, 2) }}</span>
          </div>

          <div class="acc-row-detail">
            <span class="acc-detail-lbl"><i class="fa-solid fa-percent" style="color: #64748b;"></i> POS GST Tax Collected (12%):</span>
            <span class="acc-detail-val" style="color: #475569;">₹ {{ number_format($posTaxCollected, 2) }}</span>
          </div>

          <div class="acc-row-detail" style="border-top: 1.5px solid #e2e8f0; margin-top: 6px; padding-top: 10px; font-weight: 800;">
            <span class="acc-detail-lbl" style="font-weight: 800; color: #0f172a;">Total Gross Billings Incurred:</span>
            <span class="acc-detail-val" style="color: #4338ca; font-size: 14px;">₹ {{ number_format($totalGrossRevenue, 2) }}</span>
          </div>

          <div class="acc-row-detail">
            <span class="acc-detail-lbl"><i class="fa-solid fa-boxes-stacked" style="color: #f43f5e;"></i> Less: Kitchen Store Raw Procurement Expense:</span>
            <span class="acc-detail-val" style="color: #e11d48;">- ₹ {{ number_format($storeTotalPurchases, 2) }}</span>
          </div>

          <div class="acc-row-detail" style="background: #f8faff; border-radius: 8px; padding: 10px 12px; margin-top: 8px; border: 1px solid #e0e7ff;">
            <span class="acc-detail-lbl" style="font-weight: 900; color: #0f172a; font-size: 13px;">Net Realized Cash Operating Position:</span>
            <span class="acc-detail-val" style="color: {{ $netOperatingPosition >= 0 ? '#15803d' : '#b91c1c' }}; font-size: 15px; font-weight: 900;">
              ₹ {{ number_format($netOperatingPosition, 2) }}
            </span>
          </div>
        </div>

        <!-- Section 2: Unified Cash Drawer & Payment Modes Reconciliations -->
        <div class="acc-card-sec">
          <div class="acc-card-title">
            <span><i class="fa-solid fa-wallet" style="color: #10b981;"></i> Consolidated Cash Drawer &amp; Digital Collections</span>
            <span class="badge-tag green">Realized Inflow</span>
          </div>

          <div class="acc-row-detail">
            <span class="acc-detail-lbl"><i class="fa-solid fa-money-bill-wave" style="color: #10b981;"></i> Cash In Hand (Front Desk + POS Counter):</span>
            <span class="acc-detail-val" style="color: #059669;">₹ {{ number_format($consolidatedCash, 2) }}</span>
          </div>

          <div class="acc-row-detail">
            <span class="acc-detail-lbl"><i class="fa-solid fa-qrcode" style="color: #6366f1;"></i> UPI / Digital Payments (GPay &bull; PhonePe &bull; Paytm):</span>
            <span class="acc-detail-val" style="color: #4338ca;">₹ {{ number_format($consolidatedUpi, 2) }}</span>
          </div>

          <div class="acc-row-detail">
            <span class="acc-detail-lbl"><i class="fa-solid fa-credit-card" style="color: #f59e0b;"></i> Card / EDC POS Swipes:</span>
            <span class="acc-detail-val" style="color: #b45309;">₹ {{ number_format($consolidatedCard, 2) }}</span>
          </div>

          <div class="acc-row-detail">
            <span class="acc-detail-lbl"><i class="fa-solid fa-landmark" style="color: #0284c7;"></i> Direct Bank Transfers (NEFT &bull; RTGS &bull; IMPS):</span>
            <span class="acc-detail-val" style="color: #0284c7;">₹ {{ number_format($consolidatedBank, 2) }}</span>
          </div>

          @if($consolidatedOther > 0)
            <div class="acc-row-detail">
              <span class="acc-detail-lbl"><i class="fa-solid fa-receipt" style="color: #64748b;"></i> Other Payment Modes:</span>
              <span class="acc-detail-val">₹ {{ number_format($consolidatedOther, 2) }}</span>
            </div>
          @endif

          <div class="acc-row-detail" style="border-top: 1.5px solid #e2e8f0; margin-top: 6px; padding-top: 10px; font-weight: 800;">
            <span class="acc-detail-lbl" style="font-weight: 800; color: #0f172a;">Total Realized Direct Inflow:</span>
            <span class="acc-detail-val" style="color: #059669; font-size: 15px; font-weight: 900;">₹ {{ number_format($totalRealizedCollections, 2) }}</span>
          </div>

          <!-- Outstanding Receivables Warning -->
          <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 12px; margin-top: 12px; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 11.5px; color: #92400e; font-weight: 700;">
              <i class="fa-solid fa-clock-rotate-left"></i> Outstanding Pending Receivables:
            </div>
            <div style="font-size: 13.5px; font-weight: 900; color: #b45309; font-family: var(--font-mono, monospace);">
              ₹ {{ number_format($foTotalReceivables, 2) }}
            </div>
          </div>
        </div>

      </div>

      <!-- POS Order Type Breakdown Mini Grid -->
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px;">
          <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Room Dining POS Sales</div>
          <div style="font-size: 16px; font-weight: 900; color: #0f172a; font-family: var(--font-mono, monospace); margin-top: 4px;">
            ₹ {{ number_format($posRoomDiningSales, 2) }}
          </div>
          <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">Direct In-Room orders</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px;">
          <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Table Dine-in Sales</div>
          <div style="font-size: 16px; font-weight: 900; color: #0f172a; font-family: var(--font-mono, monospace); margin-top: 4px;">
            ₹ {{ number_format($posTableDineInSales, 2) }}
          </div>
          <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">Restaurant tables &amp; deck dining</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px;">
          <div style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Walk-in Takeaway Sales</div>
          <div style="font-size: 16px; font-weight: 900; color: #0f172a; font-family: var(--font-mono, monospace); margin-top: 4px;">
            ₹ {{ number_format($posTakeawaySales, 2) }}
          </div>
          <div style="font-size: 10.5px; color: #64748b; margin-top: 2px;">Non-resident takeaway patrons</div>
        </div>
      </div>

    </div>

    <!-- TAB 2: FRONT OFFICE FINANCIAL LEDGER -->
    <div id="tab-frontoffice" class="acc-tab-pane">
      <div style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0;">Front Office Guest Bookings &amp; Settlement Ledger</h3>
          <div style="font-size: 11px; color: #64748b;">All active room bookings, stay tariffs, extra charges, advance receipts &amp; balances.</div>
        </div>
        <span class="badge-tag indigo">{{ count($foBookingsLedger) }} Record(s)</span>
      </div>

      <div class="acc-table-box">
        <table class="acc-table">
          <thead>
            <tr>
              <th style="width: 35px; text-align: center;">SL</th>
              <th>BOOKING / FOLIO #</th>
              <th>PRIMARY GUEST &amp; MOBILE</th>
              <th style="text-align: center;">ROOM</th>
              <th>STAY DATES</th>
              <th style="text-align: right;">ROOM TARIFF</th>
              <th style="text-align: right;">EXTRA SERV.</th>
              <th style="text-align: right;">TOTAL BILL</th>
              <th style="text-align: right;">PAID AMT</th>
              <th style="text-align: right;">BALANCE DUE</th>
              <th style="text-align: center;">STATUS</th>
            </tr>
          </thead>
          <tbody>
            @forelse($foBookingsLedger as $idx => $fb)
              <tr>
                <td style="text-align: center; font-weight: 800; color: #64748b; font-family: var(--font-mono, monospace);">{{ $idx + 1 }}</td>
                <td>
                  <div style="font-weight: 800; color: #4338ca; font-family: var(--font-mono, monospace); font-size: 11.5px;">{{ $fb['booking_number'] }}</div>
                  <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Folio: {{ $fb['folio_number'] }}</div>
                </td>
                <td>
                  <div style="font-weight: 700; color: #0f172a;">{{ $fb['guest_name'] }}</div>
                  <div style="font-size: 11px; color: #64748b; font-family: var(--font-mono, monospace); margin-top: 2px;">
                    <i class="fa-solid fa-phone" style="font-size: 8px; opacity: 0.6;"></i> {{ $fb['mobile'] }}
                  </div>
                </td>
                <td style="text-align: center;">
                  <span class="badge-tag indigo" style="font-family: var(--font-mono, monospace);">
                    <i class="fa-solid fa-door-open" style="font-size: 8px;"></i> {{ $fb['room_number'] }}
                  </span>
                </td>
                <td style="font-size: 11px; color: #475569; white-space: nowrap;">
                  <div><i class="fa-regular fa-calendar-check" style="font-size: 8.5px; color: #10b981;"></i> {{ $fb['checkin_date'] }}</div>
                  <div style="font-size: 10px; color: #94a3b8; margin-top: 1px;"><i class="fa-regular fa-calendar-xmark" style="font-size: 8.5px; color: #e11d48;"></i> {{ $fb['checkout_date'] }}</div>
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 700;">
                  ₹ {{ number_format($fb['room_charges'], 2) }}
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); white-space: nowrap;">
                  @if(($fb['extra_orders_count'] ?? 0) > 0)
                    @if($fb['extra_charged'] > 0)
                      <div style="font-weight: 800; color: #d97706;" title="Added to Folio & Total Bill">
                        + ₹ {{ number_format($fb['extra_charged'], 2) }}
                      </div>
                    @endif
                    @if($fb['extra_paid_direct'] > 0)
                      <div style="font-size: 10px; color: #15803d; font-weight: 700;">
                        <i class="fa-solid fa-check"></i> ₹ {{ number_format($fb['extra_paid_direct'], 2) }} (Paid)
                      </div>
                    @endif
                    <div style="font-size: 9.5px; color: #b45309; font-weight: 700; margin-top: 2px;">
                      <i class="fa-solid fa-bell-concierge" style="font-size: 8px;"></i> {{ $fb['extra_orders_count'] }} extra bill(s)
                    </div>
                    @if(!empty($fb['extra_orders_detailed']))
                      <div style="display: flex; flex-direction: column; gap: 2px; margin-top: 4px; align-items: flex-end;">
                        @foreach($fb['extra_orders_detailed'] as $eOrd)
                          @if(!empty($eOrd['is_paid']))
                            <div style="font-size: 8.5px; color: #166534; background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 4px; padding: 1px 5px; display: inline-flex; align-items: center; gap: 4px;" title="{{ $eOrd['order_number'] }}: {{ $eOrd['items_summary'] }}">
                              <span>{{ $eOrd['date_short'] }}: ₹{{ number_format($eOrd['total_amount'], 2) }} (PAID)</span>
                              @if(!empty($eOrd['table_number']))
                                <span style="font-size: 7.5px; background: #bbf7d0; padding: 0 3px; border-radius: 3px; font-weight: 700;">{{ $eOrd['table_number'] }}</span>
                              @endif
                            </div>
                          @else
                            <div style="font-size: 8.5px; color: #9a3412; background: #fff7ed; border: 1px solid #ffedd5; border-radius: 4px; padding: 1px 5px; display: inline-flex; align-items: center; gap: 4px;" title="{{ $eOrd['order_number'] }}: {{ $eOrd['items_summary'] }} (Added to Folio & Total Bill)">
                              <span><i class="fa-solid fa-plus" style="font-size: 7px;"></i> {{ $eOrd['date_short'] }}: ₹{{ number_format($eOrd['total_amount'], 2) }}</span>
                              @if(!empty($eOrd['table_number']))
                                <span style="font-size: 7.5px; background: #fed7aa; padding: 0 3px; border-radius: 3px; font-weight: 700;">{{ $eOrd['table_number'] }}</span>
                              @endif
                            </div>
                          @endif
                        @endforeach
                      </div>
                    @endif
                  @else
                    <span style="color: #94a3b8;">₹ 0.00</span>
                  @endif
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 800; color: #0f172a;">
                  <div>₹ {{ number_format($fb['total_bill'], 2) }}</div>
                  @if($fb['extra_charged'] > 0)
                    <div style="font-size: 9px; color: #b45309; font-weight: 700; margin-top: 1px;" title="Tariff (₹{{ number_format($fb['room_charges'], 2) }}) + Extra Services (₹{{ number_format($fb['extra_charged'], 2) }})">
                      (Room + Extra)
                    </div>
                  @endif
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 700; color: #059669;">
                  ₹ {{ number_format($fb['paid_amount'], 2) }}
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 800; color: {{ $fb['remaining_amount'] > 0.01 ? '#b91c1c' : '#059669' }};">
                  <div>₹ {{ number_format($fb['remaining_amount'], 2) }}</div>
                  @if($fb['extra_charged'] > 0 && $fb['remaining_amount'] > 0.01)
                    <div style="font-size: 8.5px; color: #b45309; font-weight: 700; margin-top: 1px;">
                      (incl. +₹{{ number_format($fb['extra_charged'], 2) }} extra)
                    </div>
                  @endif
                </td>
                <td style="text-align: center; white-space: nowrap;">
                  @if($fb['is_settled'])
                    <span class="badge-tag green"><i class="fa-solid fa-circle-check"></i> Settled</span>
                  @else
                    <span class="badge-tag rose"><i class="fa-solid fa-clock"></i> Due</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" style="text-align: center; padding: 24px; color: #94a3b8;">
                  No front office bookings recorded for this date filter.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($foBookingsLedger) > 0)
            <tfoot>
              <tr>
                <td colspan="5" style="text-align: right; font-size: 11px; text-transform: uppercase;">Front Office Totals:</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace);">₹ {{ number_format($foTotalRoomTariffs, 2) }}</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); color: #d97706;">+₹ {{ number_format($foTotalExtraCharged, 2) }}</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-size: 13px; color: #4338ca;">₹ {{ number_format($foTotalGrossBill, 2) }}</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-size: 13px; color: #059669;">₹ {{ number_format($foTotalPaidOverall, 2) }}</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-size: 13px; color: #b91c1c;">₹ {{ number_format($foTotalReceivables, 2) }}</td>
                <td></td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

    <!-- TAB 3: POS & RESTAURANT SALES LEDGER -->
    <div id="tab-pos" class="acc-tab-pane">
      <div style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0;">POS Restaurant Dining &amp; Takeaway Sales Ledger</h3>
          <div style="font-size: 11px; color: #64748b;">Itemized restaurant orders, food items sold, tax collected, and settlement modes.</div>
        </div>
        <span class="badge-tag cyan">{{ count($posOrdersList) }} POS Order(s)</span>
      </div>

      <div class="acc-table-box">
        <table class="acc-table">
          <thead>
            <tr>
              <th style="width: 35px; text-align: center;">SL</th>
              <th>ORDER #</th>
              <th>ORDER TYPE</th>
              <th>TABLE / ROOM</th>
              <th>CUSTOMER / GUEST</th>
              <th>ORDERED ITEMS</th>
              <th style="text-align: right;">SUBTOTAL</th>
              <th style="text-align: right;">GST (12%)</th>
              <th style="text-align: right;">TOTAL BILL</th>
              <th style="text-align: center;">BILLING MODE</th>
              <th style="text-align: center;">STATUS</th>
            </tr>
          </thead>
          <tbody>
            @forelse($posOrdersList as $idx => $po)
              <tr>
                <td style="text-align: center; font-weight: 800; color: #64748b; font-family: var(--font-mono, monospace);">{{ $idx + 1 }}</td>
                <td>
                  <div style="font-weight: 800; color: #0891b2; font-family: var(--font-mono, monospace); font-size: 11.5px;">{{ $po['order_number'] }}</div>
                  <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $po['created_at'] }}</div>
                </td>
                <td>
                  @if($po['order_type'] === 'Room Dining')
                    <span class="badge-tag indigo"><i class="fa-solid fa-bell-concierge"></i> Room Dining</span>
                  @elseif($po['order_type'] === 'Table Dine-in')
                    <span class="badge-tag green"><i class="fa-solid fa-chair"></i> Table Dine-in</span>
                  @else
                    <span class="badge-tag amber"><i class="fa-solid fa-bag-shopping"></i> Takeaway</span>
                  @endif
                </td>
                <td>
                  @if($po['table_number'] !== '-')
                    <div style="font-weight: 700; color: #0f172a; font-size: 11.5px;"><i class="fa-solid fa-chair" style="color: #10b981; font-size: 9px;"></i> {{ $po['table_number'] }}</div>
                  @endif
                  @if($po['room_number'] !== '-')
                    <div style="font-size: 10.5px; color: #4338ca; font-weight: 700; font-family: var(--font-mono, monospace); margin-top: 1px;"><i class="fa-solid fa-door-open" style="font-size: 8px;"></i> {{ $po['room_number'] }}</div>
                  @endif
                  @if($po['table_number'] === '-' && $po['room_number'] === '-')
                    <span style="color: #94a3b8;">-</span>
                  @endif
                </td>
                <td>
                  <div style="font-weight: 700; color: #0f172a;">{{ $po['customer_name'] }}</div>
                  @if($po['customer_phone'] !== '-')
                    <div style="font-size: 11px; color: #64748b; font-family: var(--font-mono, monospace); margin-top: 2px;"><i class="fa-solid fa-phone" style="font-size: 8px; opacity: 0.6;"></i> {{ $po['customer_phone'] }}</div>
                  @endif
                </td>
                <td style="max-width: 250px;">
                  <div style="font-size: 11.5px; color: #1e293b; font-weight: 600;">{{ $po['items_summary'] }}</div>
                  <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $po['total_plates'] }} plate(s) served</div>
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 700;">
                  ₹ {{ number_format($po['subtotal'], 2) }}
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); color: #64748b;">
                  ₹ {{ number_format($po['tax_amount'], 2) }}
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 800; color: #0f172a; font-size: 12.5px;">
                  ₹ {{ number_format($po['total_amount'], 2) }}
                </td>
                <td style="text-align: center; white-space: nowrap;">
                  @if($po['billing_type'] === 'Cash Paid')
                    <span class="badge-tag green"><i class="fa-solid fa-money-bill-wave"></i> Cash Paid</span>
                  @elseif($po['billing_type'] === 'Digital UPI / Card Paid')
                    <span class="badge-tag indigo"><i class="fa-solid fa-qrcode"></i> UPI Paid</span>
                  @else
                    <span class="badge-tag amber"><i class="fa-solid fa-file-invoice"></i> Room Folio</span>
                  @endif
                </td>
                <td style="text-align: center;">
                  <span class="badge-tag slate">{{ $po['status'] }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" style="text-align: center; padding: 24px; color: #94a3b8;">
                  No POS dining orders found for this date filter.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($posOrdersList) > 0)
            <tfoot>
              <tr>
                <td colspan="6" style="text-align: right; font-size: 11px; text-transform: uppercase;">POS Sales Totals:</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace);">₹ {{ number_format($posSubtotal, 2) }}</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); color: #64748b;">₹ {{ number_format($posTaxCollected, 2) }}</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-size: 13.5px; color: #0891b2;">₹ {{ number_format($posGrossSales, 2) }}</td>
                <td colspan="2" style="font-size: 11px; color: #64748b;">
                  Direct Cash/UPI: <strong>₹{{ number_format($posDirectSales, 2) }}</strong> &bull; Folio: <strong>₹{{ number_format($posFolioCharged, 2) }}</strong>
                </td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

    <!-- TAB 4: STORES & PROCUREMENT (EXPENSES) LEDGER -->
    <div id="tab-stores" class="acc-tab-pane">
      <div style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0;">Kitchen Stores &amp; Procurement Expense Ledger</h3>
          <div style="font-size: 11px; color: #64748b;">Raw ingredient inward receiving, purchase rates, vendor challans, and kitchen stock deductions.</div>
        </div>
        <span class="badge-tag rose">{{ count($storeMovementsList) }} Movement(s)</span>
      </div>

      <div class="acc-table-box">
        <table class="acc-table">
          <thead>
            <tr>
              <th style="width: 35px; text-align: center;">SL</th>
              <th>DATE &amp; TIME</th>
              <th style="text-align: center;">TYPE</th>
              <th>DISH / RAW INGREDIENT</th>
              <th>CATEGORY</th>
              <th style="text-align: center;">QUANTITY / PLATES</th>
              <th>REFERENCE / INVOICE #</th>
              <th style="text-align: right;">UNIT RATE</th>
              <th style="text-align: right;">PROCUREMENT EXPENSE</th>
              <th>ISSUED / REMARKS</th>
              <th>PERFORMED BY</th>
            </tr>
          </thead>
          <tbody>
            @forelse($storeMovementsList as $idx => $sm)
              <tr>
                <td style="text-align: center; font-weight: 800; color: #64748b; font-family: var(--font-mono, monospace);">{{ $idx + 1 }}</td>
                <td style="font-size: 11px; color: #64748b; white-space: nowrap;">{{ $sm['created_at'] }}</td>
                <td style="text-align: center;">
                  @if($sm['type'] === 'IN')
                    <span class="badge-tag green"><i class="fa-solid fa-arrow-down"></i> INWARD</span>
                  @else
                    <span class="badge-tag slate"><i class="fa-solid fa-arrow-up"></i> CONSUMED</span>
                  @endif
                </td>
                <td style="font-weight: 800; color: #0f172a;">{{ $sm['item_name'] }}</td>
                <td><span class="badge-tag slate">{{ $sm['category'] }}</span></td>
                <td style="text-align: center; font-weight: 800; font-family: var(--font-mono, monospace);">
                  @if($sm['type'] === 'IN')
                    <span style="color: #059669;">+{{ $sm['quantity'] }}</span>
                  @else
                    <span style="color: #64748b;">&minus;{{ $sm['quantity'] }}</span>
                  @endif
                </td>
                <td style="font-family: var(--font-mono, monospace); font-weight: 700; color: #4338ca; font-size: 11px;">
                  {{ $sm['reference_no'] }}
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); color: #64748b;">
                  {{ $sm['unit_cost'] ? '₹ ' . number_format($sm['unit_cost'], 2) : '-' }}
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 800; color: {{ $sm['total_cost'] ? '#e11d48' : '#64748b' }};">
                  {{ $sm['total_cost'] ? '₹ ' . number_format($sm['total_cost'], 2) : '-' }}
                </td>
                <td style="max-width: 220px; font-size: 11px; color: #475569;">
                  {{ $sm['remarks'] }}
                </td>
                <td style="font-size: 11px; color: #64748b; white-space: nowrap;">
                  {{ $sm['performed_by'] }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" style="text-align: center; padding: 24px; color: #94a3b8;">
                  No store movements or procurement records logged for this period.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($storeMovementsList) > 0)
            <tfoot>
              <tr>
                <td colspan="8" style="text-align: right; font-size: 11px; text-transform: uppercase;">Total Store Procurement Purchases:</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-size: 13.5px; color: #e11d48;">
                  ₹ {{ number_format($storeTotalPurchases, 2) }}
                </td>
                <td colspan="2" style="font-size: 11px; color: #64748b;">
                  Inward Shipments: <strong>{{ $storeInwardCount }}</strong> &bull; Total Plates Yielded: <strong>{{ $storeTotalPlatesInward }}</strong>
                </td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

    <!-- TAB 5: UNIFIED PAYMENT RECEIPTS AUDIT TRAIL -->
    <div id="tab-receipts" class="acc-tab-pane">
      <div style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="font-size: 14px; font-weight: 800; color: #0f172a; margin: 0;">Consolidated Financial Transaction Receipts Trail</h3>
          <div style="font-size: 11px; color: #64748b;">Chronological audit log of all direct payments received and procurement disbursements.</div>
        </div>
        <span class="badge-tag green">{{ count($unifiedReceipts) }} Transactions</span>
      </div>

      <div class="acc-table-box">
        <table class="acc-table">
          <thead>
            <tr>
              <th style="width: 35px; text-align: center;">SL</th>
              <th>DATE &amp; TIME</th>
              <th>DEPARTMENT / DOMAIN</th>
              <th>REFERENCE #</th>
              <th>PARTY / GUEST / VENDOR</th>
              <th>CATEGORY / DETAILS</th>
              <th style="text-align: center;">PAYMENT MODE</th>
              <th style="text-align: right;">INFLOW (+)</th>
              <th style="text-align: right;">OUTFLOW (&minus;)</th>
            </tr>
          </thead>
          <tbody>
            @php
              $totInflow = 0;
              $totOutflow = 0;
            @endphp
            @forelse($unifiedReceipts as $idx => $r)
              @php
                if ($r['is_inflow']) $totInflow += $r['amount'];
                else $totOutflow += $r['amount'];
              @endphp
              <tr>
                <td style="text-align: center; font-weight: 800; color: #64748b; font-family: var(--font-mono, monospace);">{{ $idx + 1 }}</td>
                <td style="font-size: 11px; color: #64748b; white-space: nowrap;">{{ $r['date_time'] }}</td>
                <td>
                  @if($r['type'] === 'Front Office')
                    <span class="badge-tag indigo"><i class="fa-solid fa-hotel"></i> Front Office</span>
                  @elseif($r['type'] === 'POS Sales')
                    <span class="badge-tag cyan"><i class="fa-solid fa-utensils"></i> Restaurant POS</span>
                  @else
                    <span class="badge-tag rose"><i class="fa-solid fa-boxes-stacked"></i> Store Purchase</span>
                  @endif
                </td>
                <td style="font-family: var(--font-mono, monospace); font-weight: 800; color: #4338ca; font-size: 11.5px;">{{ $r['ref_no'] }}</td>
                <td style="font-weight: 700; color: #0f172a;">{{ $r['party'] }}</td>
                <td style="font-size: 11.5px; color: #475569;">{{ $r['category'] }}</td>
                <td style="text-align: center; white-space: nowrap;">
                  @php
                    $mUpper = strtoupper($r['mode']);
                  @endphp
                  @if(str_contains($mUpper, 'CASH'))
                    <span class="badge-tag green"><i class="fa-solid fa-money-bill-wave"></i> Cash</span>
                  @elseif(str_contains($mUpper, 'UPI') || str_contains($mUpper, 'GPAY'))
                    <span class="badge-tag indigo"><i class="fa-solid fa-qrcode"></i> UPI</span>
                  @elseif(str_contains($mUpper, 'CARD'))
                    <span class="badge-tag amber"><i class="fa-solid fa-credit-card"></i> Card</span>
                  @elseif(str_contains($mUpper, 'BANK') || str_contains($mUpper, 'NEFT'))
                    <span class="badge-tag cyan"><i class="fa-solid fa-landmark"></i> Bank</span>
                  @else
                    <span class="badge-tag slate">{{ $r['mode'] }}</span>
                  @endif
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 800; color: #059669;">
                  {{ $r['is_inflow'] ? '₹ ' . number_format($r['amount'], 2) : '-' }}
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 800; color: #e11d48;">
                  {{ !$r['is_inflow'] ? '₹ ' . number_format($r['amount'], 2) : '-' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align: center; padding: 24px; color: #94a3b8;">
                  No financial transactions recorded for this period.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($unifiedReceipts) > 0)
            <tfoot>
              <tr>
                <td colspan="7" style="text-align: right; font-size: 11px; text-transform: uppercase;">Total Reconciled Cash Flow:</td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-size: 13.5px; color: #059669;">
                  ₹ {{ number_format($totInflow, 2) }}
                </td>
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-size: 13.5px; color: #e11d48;">
                  &minus; ₹ {{ number_format($totOutflow, 2) }}
                </td>
              </tr>
              <tr style="background: #f1f5f9;">
                <td colspan="7" style="text-align: right; font-size: 12px; font-weight: 900; color: #0f172a;">NET SURPLUS / CASH POSITION:</td>
                <td colspan="2" style="text-align: right; font-family: var(--font-mono, monospace); font-size: 14.5px; font-weight: 900; color: {{ ($totInflow - $totOutflow) >= 0 ? '#15803d' : '#b91c1c' }};">
                  ₹ {{ number_format($totInflow - $totOutflow, 2) }}
                </td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

  </div>

</div>

<!-- PRINTABLE CONTAINER (Only visible during print) -->
<div id="printable-accounts-area" style="display: none;">
  <div style="font-family: Arial, sans-serif; padding: 20px; color: #000;">
    <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 14px;">
      <div>
        <h1 style="font-size: 18px; margin: 0; font-weight: bold;">HOTEL SAGAR SONNET</h1>
        <div style="font-size: 11px; margin-top: 3px;">Comprehensive Accounts &amp; Financial Master Statement</div>
        <div style="font-size: 10px; color: #444;">Station Road, Luxury Wing PMS Reconciliation</div>
      </div>
      <div style="text-align: right; font-size: 11px;">
        <div><strong>Accounting Period:</strong> {{ $filterLabel }}</div>
        <div>Generated: {{ now()->format('d M Y, h:i A') }}</div>
        <div>Audited Cashier / System Administrator</div>
      </div>
    </div>

    <!-- 4 Print Summary Boxes -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 14px;">
      <div style="border: 1px solid #000; padding: 8px; text-align: center;">
        <div style="font-size: 9px; font-weight: bold; text-transform: uppercase;">Total Inflow Collected</div>
        <div style="font-size: 14px; font-weight: bold; margin-top: 3px;">₹ {{ number_format($totalRealizedCollections, 2) }}</div>
      </div>
      <div style="border: 1px solid #000; padding: 8px; text-align: center;">
        <div style="font-size: 9px; font-weight: bold; text-transform: uppercase;">Gross Billings Incurred</div>
        <div style="font-size: 14px; font-weight: bold; margin-top: 3px;">₹ {{ number_format($totalGrossRevenue, 2) }}</div>
      </div>
      <div style="border: 1px solid #000; padding: 8px; text-align: center;">
        <div style="font-size: 9px; font-weight: bold; text-transform: uppercase;">Store Procurement Cost</div>
        <div style="font-size: 14px; font-weight: bold; margin-top: 3px;">₹ {{ number_format($storeTotalPurchases, 2) }}</div>
      </div>
      <div style="border: 1px solid #000; padding: 8px; text-align: center;">
        <div style="font-size: 9px; font-weight: bold; text-transform: uppercase;">Net Operating Position</div>
        <div style="font-size: 14px; font-weight: bold; margin-top: 3px;">₹ {{ number_format($netOperatingPosition, 2) }}</div>
      </div>
    </div>

    <!-- Print Tables Summary -->
    <h3 style="font-size: 12px; margin-bottom: 6px; text-transform: uppercase; border-bottom: 1px solid #000;">Department Highlights</h3>
    <table style="width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 14px;">
      <thead>
        <tr style="border-bottom: 1px solid #000; background: #eee;">
          <th style="padding: 5px; text-align: left;">Department</th>
          <th style="padding: 5px; text-align: right;">Transactions</th>
          <th style="padding: 5px; text-align: right;">Total Gross</th>
          <th style="padding: 5px; text-align: right;">Direct Paid</th>
          <th style="padding: 5px; text-align: right;">Balance / Pending</th>
        </tr>
      </thead>
      <tbody>
        <tr style="border-bottom: 1px solid #ddd;">
          <td style="padding: 5px;"><strong>Front Office Accommodations</strong></td>
          <td style="padding: 5px; text-align: right;">{{ count($foBookingsLedger) }} Bookings</td>
          <td style="padding: 5px; text-align: right;">₹ {{ number_format($foTotalGrossBill, 2) }}</td>
          <td style="padding: 5px; text-align: right;">₹ {{ number_format($foTotalPaidOverall, 2) }}</td>
          <td style="padding: 5px; text-align: right;">₹ {{ number_format($foTotalReceivables, 2) }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #ddd;">
          <td style="padding: 5px;"><strong>POS Restaurant &amp; Dining</strong></td>
          <td style="padding: 5px; text-align: right;">{{ count($posOrdersList) }} Orders</td>
          <td style="padding: 5px; text-align: right;">₹ {{ number_format($posGrossSales, 2) }}</td>
          <td style="padding: 5px; text-align: right;">₹ {{ number_format($posDirectSales, 2) }}</td>
          <td style="padding: 5px; text-align: right;">₹ {{ number_format($posFolioCharged, 2) }} (Folio)</td>
        </tr>
        <tr style="border-bottom: 1px solid #ddd;">
          <td style="padding: 5px;"><strong>Kitchen Store Procurements (Expense)</strong></td>
          <td style="padding: 5px; text-align: right;">{{ $storeInwardCount }} Shipments</td>
          <td style="padding: 5px; text-align: right;">&minus; ₹ {{ number_format($storeTotalPurchases, 2) }}</td>
          <td style="padding: 5px; text-align: right;">&minus; ₹ {{ number_format($storeTotalPurchases, 2) }}</td>
          <td style="padding: 5px; text-align: right;">0.00</td>
        </tr>
      </tbody>
    </table>

    <div style="font-size: 9px; color: #555; text-align: center; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 6px;">
      Hotel Sagar Sonnet Accounting Management System &bull; Automatically Reconciled PMS Financial Audit Trail
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function switchAccTab(tabId, btn) {
    document.querySelectorAll('.acc-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.acc-tab-pane').forEach(p => p.classList.remove('active'));

    if (btn) btn.classList.add('active');
    const pane = document.getElementById(tabId);
    if (pane) pane.classList.add('active');
  }

  // Handle print media visibility
  window.onbeforeprint = function() {
    const printArea = document.getElementById('printable-accounts-area');
    if (printArea) printArea.style.display = 'block';
  };
  window.onafterprint = function() {
    const printArea = document.getElementById('printable-accounts-area');
    if (printArea) printArea.style.display = 'none';
  };
</script>
@endpush

