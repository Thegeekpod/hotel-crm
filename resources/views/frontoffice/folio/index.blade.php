@extends('frontoffice.layouts.app')

@section('title', 'Settlement Management & Master Ledger - Hotel Sagar Sonnet CRM / PMS')

@push('styles')
<style>
  /* Prevent awkward text wrapping and provide responsive table behavior */
  .pms-table th,
  .pms-table td {
    white-space: nowrap !important;
    padding: 10px 12px !important;
  }

  .pms-page-container {
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
  }

  .pms-page-content {
    min-width: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    padding: 0 0 16px 0 !important;
  }

  .pms-table-box {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    box-sizing: border-box !important;
    border-radius: 14px !important;
    box-shadow: 0 4px 20px -5px rgba(0,0,0,0.06) !important;
    background: #ffffff !important;
  }

  /* Sleek custom scrollbar */
  .pms-table-box::-webkit-scrollbar {
    height: 7px;
  }
  .pms-table-box::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
  }
  .pms-table-box::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .pms-table-box::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }

  .pms-table {
    width: 100% !important;
    min-width: 1050px;
    border-collapse: collapse;
  }

  .folio-id-text {
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    color: #4f46e5;
    font-size: 12px;
    white-space: nowrap;
    letter-spacing: 0.3px;
    cursor: pointer;
    transition: color 0.15s ease;
  }
  .folio-id-text:hover {
    color: #3730a3;
    text-decoration: underline;
  }

  .folio-number-sub {
    font-size: 10px;
    color: #64748b;
    font-family: var(--font-mono, monospace);
    margin-top: 2px;
    white-space: nowrap;
    cursor: pointer;
  }
  .folio-number-sub:hover {
    color: #4338ca;
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

  /* Header card styling */
  .folio-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 14px;
    box-shadow: 0 2px 10px -3px rgba(0,0,0,0.04);
    box-sizing: border-box;
    width: 100%;
    max-width: 100%;
  }

  /* Toolbar styling */
  .folio-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    box-sizing: border-box;
    width: 100%;
    max-width: 100%;
  }

  .folio-toolbar-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px 14px;
    width: 100%;
    box-sizing: border-box;
  }

  .folio-toolbar-filters {
    display: flex;
    gap: 8px 12px;
    align-items: center;
    flex: 1 1 650px;
    flex-wrap: wrap;
    min-width: 0;
  }

  .search-box-wrap {
    position: relative;
    flex: 1 1 200px;
    min-width: 180px;
    max-width: 320px;
  }

  .folio-filter-group {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    flex-shrink: 0;
  }

  .folio-filter-actions {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
  }

  .folio-toolbar-pagesize {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-shrink: 0;
    margin-left: auto;
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
  .pms-filter-select:focus {
    background-color: #ffffff;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }

  .pms-filter-date {
    height: 38px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    color: #1e293b;
    cursor: pointer;
    outline: none;
    transition: all 0.2s;
    font-family: inherit;
  }
  .pms-filter-date:focus {
    background-color: #ffffff;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }

  .pms-per-page-wrap {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
  }

  .pms-per-page-select {
    height: 34px;
    padding: 2px 26px 2px 8px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 8px center;
    appearance: none;
    -webkit-appearance: none;
    color: #1e293b;
    cursor: pointer;
    outline: none;
  }

  /* Table styling */
  .folio-row {
    transition: background 0.15s ease;
  }
  .folio-row:hover {
    background-color: #f8fafc;
  }

  /* Status Badges */
  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.3px;
    text-transform: capitalize;
    white-space: nowrap;
  }
  .status-badge.confirmed, .status-badge.blue {
    background: rgba(99, 102, 241, 0.12);
    color: #4338ca;
    border: 1px solid rgba(99, 102, 241, 0.25);
  }
  .status-badge.arrived, .status-badge.green {
    background: rgba(16, 185, 129, 0.12);
    color: #047857;
    border: 1px solid rgba(16, 185, 129, 0.25);
  }
  .status-badge.checked-in, .status-badge.checkedin, .status-badge.cyan {
    background: rgba(6, 182, 212, 0.12);
    color: #0e7490;
    border: 1px solid rgba(6, 182, 212, 0.25);
  }
  .status-badge.stay-over, .status-badge.stayover, .status-badge.yellow {
    background: rgba(245, 158, 11, 0.12);
    color: #b45309;
    border: 1px solid rgba(245, 158, 11, 0.25);
  }
  .status-badge.checked-out, .status-badge.checkedout, .status-badge.purple, .status-badge.gray {
    background: rgba(100, 116, 139, 0.12);
    color: #475569;
    border: 1px solid rgba(100, 116, 139, 0.25);
  }
  .status-badge.cancelled, .status-badge.red {
    background: rgba(239, 68, 68, 0.12);
    color: #b91c1c;
    border: 1px solid rgba(239, 68, 68, 0.25);
  }
  .status-badge.settlement-done, .status-badge.settled, .status-badge.settlement_done {
    background: #ede9fe;
    color: #5b21b6;
    border: 1px solid #ddd6fe;
    font-weight: 800;
  }

  /* Action button matching guest CRM exactly */
  .btn-action-view {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #c7d2fe;
    background: #eef2ff;
    color: #4f46e5;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .btn-action-view:hover {
    background: #4f46e5;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
  }

  /* Pagination styles */
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
    min-width: 30px;
    height: 30px;
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
  .pms-page-nav svg,
  .pms-table-pagination svg,
  nav svg {
    width: 14px !important;
    height: 14px !important;
    max-width: 14px !important;
    max-height: 14px !important;
  }

  /* Dossier Modal Styles matching guest/index.blade.php */
  .dossier-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 16px;
  }
  @media (max-width: 768px) {
    .dossier-grid {
      grid-template-columns: 1fr;
    }
  }

  .dossier-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }

  .dossier-card-title {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    color: #475569;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 8px;
  }
  .dossier-card-title i {
    color: #6366f1;
    font-size: 13px;
  }

  .dossier-field-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px dashed #f1f5f9;
    font-size: 12px;
  }
  .dossier-field-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }
  .dossier-label {
    color: #64748b;
    font-weight: 600;
    font-size: 11px;
  }
  .dossier-val {
    color: #0f172a;
    font-weight: 700;
    text-align: right;
  }
  .dossier-val.mono {
    font-family: var(--font-mono, monospace);
  }

  /* Highlight Banner like guest CRM */
  .dossier-highlight-box {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }

  .billing-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 10px;
  }
  @media (max-width: 640px) {
    .billing-summary-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  .bill-stat-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 10px;
    text-align: center;
  }
  .bill-stat-label {
    font-size: 10px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
  }
  .bill-stat-value {
    font-size: 14px;
    font-weight: 900;
    font-family: var(--font-mono, monospace);
    margin-top: 2px;
  }

  /* Room card inside dossier */
  .room-alloc-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
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
    flex-wrap: wrap;
    gap: 8px;
  }

  /* Occupant row */
  .occupant-badge-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 10px;
    background: #f8fafc;
    border-radius: 8px;
    margin-bottom: 5px;
    font-size: 11px;
    flex-wrap: wrap;
    gap: 8px;
    border: 1px solid #f1f5f9;
    transition: all 0.15s ease;
  }
  .occupant-badge-row.checked-out {
    background: #f8fafc;
    border-color: #e2e8f0;
    opacity: 0.9;
  }
  .btn-early-checkout {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
    padding: 3px 9px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
    white-space: nowrap;
  }
  .btn-early-checkout:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
  }
  .badge-checked-out-occupant {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-family: var(--font-mono, monospace);
    white-space: nowrap;
  }
  #early-checkout-confirm-modal.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 100099 !important;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(15, 23, 42, 0.55) !important;
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    box-sizing: border-box;
  }
  #early-checkout-confirm-modal.open {
    display: flex !important;
  }

  /* Settlement Inline Panel */
  .settlement-panel {
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    border-radius: 12px;
    padding: 14px 16px;
    margin-top: 14px;
  }

  /* Modal Window & Backdrop like guest CRM */
  #folio-details-modal.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 100050 !important;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(15, 23, 42, 0.3) !important;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    overflow-y: auto !important;
    box-sizing: border-box;
  }
  #folio-details-modal.open {
    display: flex !important;
  }
  #folio-details-modal .modal-window.large {
    width: 900px;
    max-width: 95vw;
    max-height: 92vh;
    display: flex;
    flex-direction: column;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3);
    border: none;
    box-sizing: border-box;
    background: #ffffff;
  }
  #folio-details-modal .modal-top {
    flex-shrink: 0;
    background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
    color: #ffffff;
    padding: 14px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.15);
  }
  #folio-details-modal .modal-content-area {
    padding: 20px 24px;
    background: #f8fafc;
    overflow-y: auto;
    max-height: calc(92vh - 130px);
    -webkit-overflow-scrolling: touch;
  }
  #folio-details-modal .modal-bot {
    flex-shrink: 0;
    padding: 12px 22px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
  }
  @media (max-width: 640px) {
    #folio-details-modal.modal-backdrop {
      padding: 10px;
    }
    #folio-details-modal .modal-top {
      padding: 12px 16px;
    }
    #folio-details-modal .modal-content-area {
      padding: 14px 16px;
    }
    #folio-details-modal .modal-bot {
      padding: 10px 16px;
    }
  }

  /* Media Print Rules */
  @media print {
    body > *:not(#print-area):not(#folio-print-area) {
      display: none !important;
    }
    #print-area, #folio-print-area {
      display: block !important;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
    }
  }
</style>
@endpush

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">

    <!-- Clean Header Card (like guest CRM) -->
    <div class="folio-header-card">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
          <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <i class="fa-solid fa-file-invoice-dollar" style="color: #6366f1;"></i> 
            Settlement Management &amp; Master Ledger
            <span style="font-size: 11px; font-weight: 700; background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 2px 10px; border-radius: 20px;">
              {{ $folios->total() }} Records
            </span>
            @if(!empty($fromDate) && !empty($toDate) && $fromDate === $toDate)
              <span style="font-size: 11px; font-weight: 700; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 2px 10px; border-radius: 20px;">
                <i class="fa-regular fa-calendar-check"></i> {{ $fromDate === $today ? "Today's Check-ins (" . \Carbon\Carbon::parse($fromDate)->format('d M Y') . ")" : "Check-in: " . \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
              </span>
            @elseif(!empty($fromDate) || !empty($toDate))
              <span style="font-size: 11px; font-weight: 700; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 2px 10px; border-radius: 20px;">
                <i class="fa-regular fa-calendar-check"></i> Check-in: {{ !empty($fromDate) ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }} &rarr; {{ !empty($toDate) ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'End' }}
              </span>
            @else
              <span style="font-size: 11px; font-weight: 700; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 2px 10px; border-radius: 20px;">
                <i class="fa-regular fa-calendar"></i> All Check-in Dates
              </span>
            @endif
          </h2>
          <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
            @if(!empty($fromDate) && !empty($toDate) && $fromDate === $toDate)
              Showing settlement records with check-in on <strong>{{ \Carbon\Carbon::parse($fromDate)->format('D, d F Y') }}</strong> ({{ $fromDate === $today ? 'Today' : 'Selected Date' }}).
            @elseif(!empty($fromDate) || !empty($toDate))
              Showing settlement records with check-in between <strong>{{ !empty($fromDate) ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }}</strong> and <strong>{{ !empty($toDate) ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'End' }}</strong>.
            @else
              Booking numbers grouped settlement directory with rooms, occupants, financial settlements &amp; check-out control.
            @endif
          </div>
        </div>

        
      </div>
    </div>

    <!-- ========================================== -->
    <!-- Search & Filter Controls Toolbar (like guest) -->
    <!-- ========================================== -->
    <form method="GET" action="{{ route('frontoffice.folio') }}" id="folio-filter-form" class="folio-toolbar">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; width: 100%;">
        <div style="display: flex; gap: 12px; align-items: center; flex: 1; flex-wrap: wrap;">
          
          <!-- Search Box with Clear Button -->
          <div class="search-box-wrap">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" 
                   name="search" 
                   id="folio-search-input"
                   value="{{ request('search') }}" 
                   placeholder="Search by Booking #, Settlement #, Guest, Phone, Room..." 
                   onkeydown="if(event.key==='Enter'){this.form.submit();}"
                   autocomplete="off">
            @if(request()->filled('search'))
              <a href="{{ route('frontoffice.folio', request()->except('search', 'page')) }}" 
                 title="Clear search" 
                 style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; text-decoration: none; font-size: 12px;">
                <i class="fa-solid fa-xmark"></i>
              </a>
            @endif
          </div>

          <!-- Date Range: From & To Date Filter -->
          <div style="display: inline-flex; align-items: center; gap: 6px;">
            <label for="folio-from-date" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
              <i class="fa-regular fa-calendar-days" style="color: #6366f1;"></i> Check-in From:
            </label>
            <input type="date" 
                   name="from_date" 
                   id="folio-from-date"
                   class="pms-filter-date" 
                   value="{{ $fromDate ?? '' }}" 
                   onchange="this.form.submit()"
                   title="Filter by Check-in Date From">

            <label for="folio-to-date" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
              To:
            </label>
            <input type="date" 
                   name="to_date" 
                   id="folio-to-date"
                   class="pms-filter-date" 
                   value="{{ $toDate ?? '' }}" 
                   onchange="this.form.submit()"
                   title="Filter by Check-in Date To">
          </div>

          <!-- Status Filter Dropdown -->
          <div style="display: inline-flex; align-items: center; gap: 6px;">
            <label for="folio-status-filter" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
              <i class="fa-solid fa-filter" style="color: #6366f1;"></i> Status:
            </label>
            <select name="status" id="folio-status-filter" class="pms-filter-select" onchange="this.form.submit()">
              <option value="all" {{ request('status') === 'all' || !request()->filled('status') ? 'selected' : '' }}>All Statuses</option>
              <option value="Confirmed" {{ request('status') === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
              <option value="Arrived" {{ request('status') === 'Arrived' ? 'selected' : '' }}>Arrived</option>
              <option value="Checked In" {{ request('status') === 'Checked In' ? 'selected' : '' }}>Checked In</option>
              <option value="Stay Over" {{ request('status') === 'Stay Over' ? 'selected' : '' }}>Stay Over</option>
              <option value="Settlement Done" {{ request('status') === 'Settlement Done' ? 'selected' : '' }}>Settlement Done</option>
              <option value="Checked Out" {{ request('status') === 'Checked Out' ? 'selected' : '' }}>Checked Out</option>
              <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>

          <!-- Registration Type Filter -->
          <div style="display: inline-flex; align-items: center; gap: 6px;">
            <label for="folio-regtype-filter" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
              <i class="fa-solid fa-id-badge" style="color: #6366f1;"></i> Reg Type:
            </label>
            <select name="registration_type" id="folio-regtype-filter" class="pms-filter-select" onchange="this.form.submit()">
              <option value="all" {{ request('registration_type') === 'all' || !request()->filled('registration_type') ? 'selected' : '' }}>All Reg Types</option>
              @foreach($registrationTypes ?? [] as $rt)
                <option value="{{ $rt->name }}" {{ request('registration_type') === $rt->name ? 'selected' : '' }}>{{ $rt->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- All Dates Quick Button (if date filter active) -->
          @if(!empty($fromDate) || !empty($toDate))
            <a href="{{ route('frontoffice.folio', array_merge(request()->except(['from_date', 'to_date', 'page']), ['all_dates' => 1])) }}" 
               class="btn-ui-secondary" 
               style="padding: 7px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 11px; font-weight: 700; color: #475569; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;"
               title="View all records without date restrictions">
              <i class="fa-regular fa-calendar"></i> All Dates
            </a>
          @endif

          <!-- Today / Reset Button (resets to today's data) -->
          <a href="{{ route('frontoffice.folio') }}" 
             class="btn-ui-secondary" 
             style="padding: 7px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 11px; font-weight: 700; color: #475569; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;"
             title="Reset filters to today's check-ins">
            <i class="fa-solid fa-rotate-left"></i> Today / Reset
          </a>
        </div>

        <!-- Page Size Selector -->
        <div style="display: flex; gap: 12px; align-items: center;">
          <div class="pms-per-page-wrap">
            <span>Show</span>
            <select name="per_page" class="pms-per-page-select" id="folio-per-page-select" onchange="this.form.submit()">
              <option value="10" {{ request('per_page') == '10' || !request()->filled('per_page') ? 'selected' : '' }}>10</option>
              <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
              <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
              <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
            </select>
            <span>entries</span>
          </div>
        </div>

      </div>
    </form>

    <!-- Main Settlement Master Table with SL Indexing -->
    <div class="pms-page-content">
      <div class="pms-table-box" style="border-radius: 14px; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.06); background: #ffffff; overflow-x: auto;">
        <table class="pms-table" id="folio-master-table">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center; white-space: nowrap;">SL</th>
              <th style="min-width: 160px; white-space: nowrap;">Room(s) &amp; Floor</th>
              <th style="min-width: 140px; text-align: left; white-space: nowrap;">Booking Number</th>
              <th style="min-width: 180px; white-space: nowrap;">Primary Guest Details</th>
              <th style="min-width: 130px; white-space: nowrap;">Mobile / Phone</th>
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

                <!-- 1. Room(s) & Floor (First) -->
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
                        <div style="display: inline-flex; align-items: center; gap: 6px; flex-direction: row; ">
                          <span class="room-tag">
                            <i class="fa-solid fa-door-open" style="font-size: 10px;"></i>
                            {{ $rDisp }}
                          </span><br>
                          <span style="font-size: 10px; color: #64748b; font-weight: 700;">
                            Floor {{ $flrDisp }}
                          </span>
                          <!-- <span style="color: #242425ff;">•</span> -->
                          <!-- <span style="font-size: 10px; color: #94a3b8;">{{ $catDisp }}</span> -->
                        </div>
                      @endforeach
                    </div>
                  @else
                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">
                      <i class="fa-solid fa-hourglass-half" style="font-size: 10px;"></i> Unassigned
                    </span>
                  @endif
                </td>

                <!-- 2. Booking Number (Then Booking Number) -->
                <td style="white-space: nowrap;">
                  <div class="folio-id-text" style="cursor: pointer;" onclick="openFolioModalById({{ $item->id }})" data-booking-id="{{ $item->id }}" title="Click to open Settlement Dossier">
                    <i class="fa-solid fa-receipt" style="color: #6366f1; margin-right: 4px; font-size: 11px; pointer-events: none;"></i>
                    {{ $item->reserve_id ?: ('RES-' . $item->id) }}
                  </div>
                  <div class="folio-number-sub" style="cursor: pointer;" onclick="openFolioModalById({{ $item->id }})" data-booking-id="{{ $item->id }}" title="Click to open Settlement Dossier">
                    Settlement: {{ $item->folio_number ?: '-' }}
                  </div>
                </td>

                <!-- 3. Primary Guest Details with Dynamic +N persons -->
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

                <!-- 4. Primary Mobile / Phone -->
                <td style="white-space: nowrap;">
                  <div style="font-family: var(--font-mono, monospace); font-weight: 700; font-size: 12px; color: #1e293b; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-phone" style="font-size: 10px; color: #10b981;"></i>
                    <span>{{ $item->guest?->mobile ?: 'N/A' }}</span>
                  </div>
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
                          data-booking-id="{{ $item->id }}"
                          onclick="openFolioModalById({{ $item->id }})" 
                          title="View Full Settlement Dossier (Rooms, Occupants, Billings, Settle &amp; Checkout)">
                    <i class="fa-solid fa-eye" style="pointer-events: none;"></i>
                  </button>
                </td>

              </tr>
            @empty
              <tr>
                <td colspan="11" style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                  <div style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px;"><i class="fa-solid fa-receipt"></i></div>
                  <div style="font-size: 14px; font-weight: 700; color: #64748b;">No matching settlements found for this filter.</div>
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

        <!-- Backend Table Pagination Footer -->
        <div class="pms-table-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; border-top: 1px solid #e2e8f0; background: #ffffff; flex-wrap: wrap; gap: 10px;">
          <div class="pms-pagination-info" style="font-size: 12px; font-weight: 600; color: #64748b;">
            Showing {{ $folios->firstItem() ?? 0 }} to {{ $folios->lastItem() ?? 0 }} of {{ $folios->total() }} records
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

  </div>
</main>

<!-- Bottom Summary Bar (like guest CRM) -->
<footer class="pms-bottom-bar">
  <div style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
    <span><i class="fa-solid fa-receipt" style="color: #6366f1;"></i> Total Settlements: <strong>{{ $stats['total_folios'] ?? 0 }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-file-invoice" style="color: #0891b2;"></i> Total Receivable: <strong>₹ {{ number_format($stats['total_receivable'] ?? 0, 2) }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-circle-check" style="color: #059669;"></i> Total Paid: <strong>₹ {{ number_format($stats['total_paid'] ?? 0, 2) }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-triangle-exclamation" style="color: {{ ($stats['total_outstanding'] ?? 0) > 0 ? '#e11d48' : '#059669' }};"></i> Remaining Due: <strong>₹ {{ number_format($stats['total_outstanding'] ?? 0, 2) }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-lock" style="color: #64748b;"></i> Finalized / Settled: <strong>{{ $stats['settled_count'] ?? 0 }}</strong></span>
  </div>
</footer>

<!-- ========================================================================= -->
<!-- EXTENSIVE SETTLEMENT DOSSIER MODAL (Design matching guest CRM)             -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="folio-details-modal">
  <div class="modal-window large" style="width: 900px; max-width: 95vw; max-height: 92vh; display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3); border: none; box-sizing: border-box;">
    
    <!-- Modal Header (Gradient style matching guest CRM) -->
    <div class="modal-top">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center; font-size: 16px; color: #ffffff;">
          <i class="fa-solid fa-receipt"></i>
        </div>
        <div>
          <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <h3 id="fmdl-guest-name" style="margin: 0; font-size: 16px; font-weight: 800; color: #ffffff;">Guest Profile &amp; Settlement Dossier</h3>
            <span id="fmdl-status-badge" class="status-badge" style="background: #ffffff; color: #4338ca; font-weight: 800;">Confirmed</span>
          </div>
          <div style="font-size: 11px; opacity: 0.85; margin-top: 2px; font-family: var(--font-mono, monospace);">
            Booking Ref: <span id="fmdl-reserve-id" style="font-weight: 700;">-</span> &nbsp;|&nbsp; Settlement: <span id="fmdl-folio-number" style="font-weight: 700;">-</span>
          </div>
        </div>
      </div>
      <button type="button" class="modal-close" onclick="closeFolioModal()" style="background: rgba(255,255,255,0.2); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">&times;</button>
    </div>

    <!-- Modal Content Area -->
    <div class="modal-content-area" id="folio-modal-body">
      
      <!-- Top Highlight Banner (like guest CRM) -->
      <div class="dossier-highlight-box">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa-solid fa-door-open"></i>
          </div>
          <div>
            <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Allotted Room(s) &amp; Status</div>
            <div style="font-size: 16px; font-weight: 900; color: #0f172a; margin-top: 1px;" id="fmdl-highlight-rooms">
              -
            </div>
          </div>
        </div>

        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
          <span style="font-size: 11px; font-weight: 800; background: #ffffff; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; color: #475569;">
            <i class="fa-solid fa-users" style="color: #6366f1;"></i> <span id="fmdl-total-guests-count">1 Person</span>
          </span>
          <span style="font-size: 11px; font-weight: 800; background: #fff7ed; border: 1px solid #fed7aa; padding: 4px 10px; border-radius: 6px; color: #c2410c;">
            <i class="fa-solid fa-utensils" style="color: #ea580c;"></i> <span id="fmdl-plan-name">EP</span>
          </span>
          <span style="font-size: 11px; font-weight: 800; background: #e0e7ff; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 6px; color: #4338ca;">
            <i class="fa-solid fa-moon"></i> <span id="fmdl-nights-sched">1 Night(s) Stay</span>
          </span>
          <span style="font-size: 11px; font-weight: 800; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 4px 10px; border-radius: 6px; color: #15803d;">
            <i class="fa-solid fa-headset" style="color: #16a34a;"></i> <span id="fmdl-reservation-mode">Front Desk</span>
          </span>
        </div>
      </div>

      <!-- 2-Column Uniform Dossier Grid (like guest CRM) -->
      <div class="dossier-grid">
        
        <!-- CARD 1: Primary Guest Profile Details -->
        <div class="dossier-card">
          <div class="dossier-card-title">
            <i class="fa-solid fa-user-check"></i> Primary Guest Profile Details
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Name</span>
            <span class="dossier-val" id="fmdl-card-name">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Registration Type</span>
            <span class="dossier-val" id="fmdl-card-regtype" style="color: #4338ca; font-weight: 800;">-</span>
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
            <span class="dossier-label">City / Location</span>
            <span class="dossier-val" id="fmdl-guest-city">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Address</span>
            <span class="dossier-val" id="fmdl-guest-address" style="max-width: 220px; white-space: normal;">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Nationality</span>
            <span class="dossier-val" id="fmdl-guest-nationality">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Identity Proof</span>
            <span class="dossier-val" id="fmdl-guest-idproof">-</span>
          </div>

          <div class="dossier-field-row" id="fmdl-card-privilege-row" style="display: none;">
            <span class="dossier-label">Privilege / VIP Card</span>
            <span class="dossier-val mono" id="fmdl-card-privilege" style="color: #b45309;">-</span>
          </div>
        </div>

        <!-- CARD 2: Stay Schedule & Corporate Account -->
        <div class="dossier-card">
          <div class="dossier-card-title">
            <i class="fa-solid fa-calendar-days"></i> Stay &amp; Corporate Details
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-In (Arrival)</span>
            <span class="dossier-val mono" id="fmdl-checkin-sched" style="color: #4f46e5;">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-Out (Departure)</span>
            <span class="dossier-val mono" id="fmdl-checkout-sched" style="color: #059669;">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Duration of Stay</span>
            <span class="dossier-val" id="fmdl-duration-stay">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Reservation Mode</span>
            <span class="dossier-val" id="fmdl-resmode-val">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Meal Plan</span>
            <span class="dossier-val" id="fmdl-mealplan-val" style="color: #c2410c; font-weight: 800;">-</span>
          </div>

          <!-- Corporate Account Block -->
          <div id="fmdl-company-row" style="margin-top: 10px; padding: 10px; border-radius: 8px; background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.15); display: none;">
            <div style="font-size: 10px; font-weight: 800; color: #4338ca; text-transform: uppercase; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-building"></i> Corporate Account
            </div>
            <div class="dossier-field-row" style="padding: 3px 0;">
              <span class="dossier-label">Company Name</span>
              <span class="dossier-val" id="fmdl-company-name" style="color: #4338ca;">-</span>
            </div>
            <div class="dossier-field-row" style="padding: 3px 0;">
              <span class="dossier-label">Company GSTIN</span>
              <span class="dossier-val mono" id="fmdl-company-gstin">-</span>
            </div>
            <div class="dossier-field-row" style="padding: 3px 0;">
              <span class="dossier-label">Phone</span>
              <span class="dossier-val mono" id="fmdl-company-phone">-</span>
            </div>
          </div>
        </div>

      </div>

      <!-- CARD 3: Allocated Rooms & Registered Occupants Details -->
      <div class="dossier-card" style="margin-bottom: 16px;">
        <div class="dossier-card-title">
          <i class="fa-solid fa-bed"></i> Allocated Rooms &amp; Registered Occupants
        </div>
        <div id="fmdl-rooms-container">
          <!-- Room cards & occupant badges populated dynamically -->
        </div>
      </div>

      <!-- CARD 4: Pricing & Billing Breakdown (like guest CRM) -->
      <div class="dossier-card" style="margin-bottom: 0;">
        <div class="dossier-card-title">
          <i class="fa-solid fa-file-invoice-dollar"></i> Pricing &amp; Billing Breakdown
        </div>

        <!-- 4-Stat Metric Boxes matching guest CRM -->
        <div class="billing-summary-grid">
          <div class="bill-stat-item">
            <div class="bill-stat-label">Daily Room Rate</div>
            <div class="bill-stat-value" id="fmdl-bill-rate" style="color: #334155;">₹ 0.00</div>
          </div>

          <div class="bill-stat-item">
            <div class="bill-stat-label">Gross Amount</div>
            <div class="bill-stat-value" id="fmdl-gross-amount" style="color: #1e293b;">₹ 0.00</div>
          </div>

          <div class="bill-stat-item">
            <div class="bill-stat-label">Total Amount Paid</div>
            <div class="bill-stat-value" id="fmdl-totalpaid-amount" style="color: #059669;">₹ 0.00</div>
          </div>

          <div class="bill-stat-item" id="fmdl-balance-box">
            <div class="bill-stat-label" id="fmdl-balance-label">Balance Due</div>
            <div class="bill-stat-value" id="fmdl-balance-amount" style="color: #e11d48;">₹ 0.00</div>
          </div>
        </div>

        <!-- Itemized Line Rows -->
        <div style="margin-top: 14px; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 8px; padding: 10px 14px;">
          <div class="dossier-field-row">
            <span class="dossier-label">Room Rate × Nights</span>
            <span class="dossier-val mono" id="fmdl-calc-row">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Advance Deposit Paid</span>
            <span class="dossier-val mono" id="fmdl-advance-row" style="color: #059669;">₹ 0.00</span>
          </div>
          <div class="dossier-field-row" id="fmdl-settle-field-row">
            <span class="dossier-label">Settlement Amount Paid</span>
            <span class="dossier-val mono" id="fmdl-settlement-row" style="color: #6366f1; font-weight: 800;">₹ 0.00</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Total Amount Paid</span>
            <span class="dossier-val mono" id="fmdl-totalpaid-row" style="color: #059669; font-weight: 800;">₹ 0.00</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Discount</span>
            <span class="dossier-val mono" id="fmdl-discount-row" style="color: #059669;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">GST / Tax</span>
            <span class="dossier-val mono" id="fmdl-tax-row" style="color: #6366f1;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Net Payable Amount</span>
            <span class="dossier-val mono" id="fmdl-payable-amount" style="font-size: 13px; font-weight: 900; color: #0f172a;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Payment Mode</span>
            <span class="dossier-val" id="fmdl-paymode-row">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Payment Remarks / Reference</span>
            <span class="dossier-val" id="fmdl-remarks-row" style="color: #64748b;">-</span>
          </div>
        </div>

        <!-- Settlement Receipts History List -->
        <div style="margin-top: 14px;" id="fmdl-settle-history-box">
          <div style="font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
            <i class="fa-solid fa-clock-rotate-left"></i> Settlement Receipts History
          </div>
          <div id="fmdl-settle-history-list" style="font-size: 11px;">
            <!-- Populated via JS -->
          </div>
        </div>

        <!-- Interactive Inline Settlement Panel -->
        <div id="fmdl-settlement-interactive-panel" class="settlement-panel" style="display: none;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
            <div style="font-size: 12px; font-weight: 800; color: #065f46; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-calculator"></i> Process Settlement
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

    </div>

    <!-- Modal Footer Actions (matches guest CRM) -->
    <div class="modal-bot">
      <div style="font-size: 11px; color: #64748b; font-weight: 600;">
        <i class="fa-solid fa-clock-rotate-left"></i> Record Registered: <span id="fmdl-created-at">-</span>
      </div>

      <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
        <!-- Settle Balance Button -->
        <button type="button" 
                id="fmdl-action-btn-settle" 
                onclick="toggleFolioSettlementPanel()" 
                style="padding: 7px 18px; border-radius: 8px; background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; border: none; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 4px 12px rgba(5,150,105,0.25);">
          <i class="fa-solid fa-calculator"></i> Settle Balance
        </button>

        <!-- Check Out Button -->
        <button type="button" 
                id="fmdl-action-btn-checkout" 
                onclick="executeFolioCheckout()" 
                style="padding: 7px 18px; border-radius: 8px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; border: none; font-size: 12px; font-weight: 800; display: none; align-items: center; gap: 6px; cursor: pointer; box-shadow: 0 4px 12px rgba(99,102,241,0.25);">
          <i class="fa-solid fa-right-from-bracket"></i> Check Out
        </button>

        <!-- Locked Badge -->
        <div id="fmdl-badge-locked" 
             style="display: none; padding: 6px 14px; border-radius: 8px; background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; font-size: 12px; font-weight: 800; align-items: center; gap: 6px;">
          <i class="fa-solid fa-lock" style="color: #64748b;"></i> Stay Finalized &amp; Checked Out (Locked)
        </div>

        <!-- Print Invoice Button -->
        <button type="button" class="btn-ui-secondary" onclick="printFolioInvoice()" style="padding: 7px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 12px; font-weight: 700; color: #334155; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
          <i class="fa-solid fa-print"></i> Print Invoice
        </button>

        <!-- Close Button -->
        <button type="button" class="btn-ui-secondary" onclick="closeFolioModal()" style="padding: 7px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 12px; font-weight: 700; color: #64748b; cursor: pointer;">
          Close
        </button>
      </div>
    </div>

  </div>
</div>

<!-- ========================================================================= -->
<!-- CONFIRMATION MODAL: EARLY CHECK-OUT INDIVIDUAL GUEST                     -->
<!-- ========================================================================= -->
<div class="modal-backdrop" id="early-checkout-confirm-modal">
  <div class="modal-window" style="width: 480px; max-width: 95vw; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 60px -15px rgba(0,0,0,0.45); border: none; background: #ffffff;">
    
    <div style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; color: #ffffff;">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 15px; color: #ffffff;">
          <i class="fa-solid fa-person-walking-arrow-right"></i>
        </div>
        <div>
          <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #ffffff;">Early Check-Out Confirmation</h4>
          <div style="font-size: 10px; opacity: 0.9; color: #fee2e2;">Individual Occupant Check-Out</div>
        </div>
      </div>
      <button type="button" onclick="closeEarlyCheckoutConfirmModal()" style="background: rgba(255,255,255,0.2); border: none; color: #ffffff; width: 28px; height: 28px; border-radius: 50%; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">&times;</button>
    </div>

    <div style="padding: 20px; background: #ffffff;">
      <div style="display: flex; gap: 14px; align-items: flex-start; margin-bottom: 16px;">
        <div style="width: 40px; height: 40px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
          <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div>
          <div style="font-size: 13px; font-weight: 800; color: #0f172a; margin-bottom: 3px;">
            Are you sure you want to early check-out this guest?
          </div>
          <div style="font-size: 11px; color: #64748b; line-height: 1.5;">
            Only this particular occupant will be marked as checked out. Other occupants and room billing stay active.
          </div>
        </div>
      </div>

      <!-- Guest and Checkout Info Box -->
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; font-size: 12px; display: flex; flex-direction: column; gap: 8px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="color: #64748b; font-weight: 600;">Guest Name:</span>
          <strong id="eccm-guest-name" style="color: #0f172a; font-size: 13px;">-</strong>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #e2e8f0; padding-top: 6px;">
          <span style="color: #64748b; font-weight: 600;">Allotted Room:</span>
          <strong id="eccm-room-no" style="color: #4338ca; font-weight: 800;">-</strong>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #e2e8f0; padding-top: 6px;">
          <span style="color: #64748b; font-weight: 600;">Check-Out Date:</span>
          <span id="eccm-checkout-date" style="font-family: var(--font-mono, monospace); font-weight: 800; color: #047857;">-</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #e2e8f0; padding-top: 6px;">
          <span style="color: #64748b; font-weight: 600;">Check-Out Time:</span>
          <span id="eccm-checkout-time" style="font-family: var(--font-mono, monospace); font-weight: 800; color: #047857;">-</span>
        </div>
      </div>

      <input type="hidden" id="eccm-booking-id" value="">
    </div>

    <div style="padding: 12px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; align-items: center;">
      <button type="button" onclick="closeEarlyCheckoutConfirmModal()" style="padding: 7px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-size: 12px; font-weight: 700; cursor: pointer;">
        Cancel
      </button>
      <button type="button" id="eccm-btn-confirm" onclick="executeEarlyCheckout()" style="padding: 7px 20px; border-radius: 8px; border: none; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; font-size: 12px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">
        <i class="fa-solid fa-check"></i> Yes, Early Check Out
      </button>
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
          Swal.fire({
            icon: 'error',
            title: 'Settlement Notice',
            text: 'Could not fetch settlement details.',
            confirmButtonColor: '#4f46e5'
          });
        }
      })
      .catch(err => {
        console.error('Error fetching settlement details:', err);
        Swal.fire({
          icon: 'error',
          title: 'System Error',
          text: 'Failed to load settlement details. Please try again.',
          confirmButtonColor: '#4f46e5'
        });
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

      // Top highlight banner
      const hlRooms = document.getElementById('fmdl-highlight-rooms');
      if (hlRooms) {
        const roomsList = (folio.rooms || []).map(r => `${escapeHtml(r.room_number)} (Floor ${escapeHtml(r.floor)} • ${escapeHtml(r.category)})`).join(' • ');
        hlRooms.textContent = roomsList || 'Unassigned';
      }
      const elGuests = document.getElementById('fmdl-total-guests-count');
      if (elGuests) elGuests.textContent = `${folio.total_guests_count || 1} Person(s) (${(folio.rooms || []).length} Room(s))`;
      const elPlan = document.getElementById('fmdl-plan-name');
      if (elPlan) elPlan.textContent = folio.plan_name || 'EP';
      const elNights = document.getElementById('fmdl-nights-sched');
      if (elNights) elNights.textContent = `${folio.total_nights || 1} Night(s) Stay`;
      const elResMode = document.getElementById('fmdl-reservation-mode');
      if (elResMode) elResMode.textContent = folio.reservation_mode || 'Front Desk';

      // Primary Guest Profile
      const pg = folio.primary_guest || {};
      const elName = document.getElementById('fmdl-guest-name');
      if (elName) elName.textContent = pg.name || 'Guest Profile Dossier';
      const cardName = document.getElementById('fmdl-card-name');
      if (cardName) cardName.textContent = pg.name || 'Guest';
      const cardRegType = document.getElementById('fmdl-card-regtype');
      if (cardRegType) cardRegType.textContent = folio.registration_type || 'Regular';
      const elMobile = document.getElementById('fmdl-guest-mobile');
      if (elMobile) elMobile.textContent = pg.mobile || 'N/A';
      const elEmail = document.getElementById('fmdl-guest-email');
      if (elEmail) elEmail.textContent = pg.email || 'N/A';
      const elCity = document.getElementById('fmdl-guest-city');
      if (elCity) elCity.textContent = pg.city || 'N/A';
      const elAddress = document.getElementById('fmdl-guest-address');
      if (elAddress) elAddress.textContent = pg.address || 'N/A';
      const elNat = document.getElementById('fmdl-guest-nationality');
      if (elNat) elNat.textContent = pg.nationality || 'Indian';
      const elIdProof = document.getElementById('fmdl-guest-idproof');
      if (elIdProof) elIdProof.textContent = (pg.id_card_type || 'ID Proof') + ': ' + (pg.id_card_number || 'N/A');

      // Privilege / VIP Card
      const privRow = document.getElementById('fmdl-card-privilege-row');
      const privVal = document.getElementById('fmdl-card-privilege');
      if (privRow && privVal) {
        if (pg.has_privilege_card && pg.privilege_card_no) {
          privRow.style.display = 'flex';
          privVal.textContent = pg.privilege_card_no;
        } else {
          privRow.style.display = 'none';
        }
      }

      // Stay schedule
      const elCin = document.getElementById('fmdl-checkin-sched');
      if (elCin) elCin.textContent = `${folio.checkin_date_fmt || ''} at ${folio.checkin_time_fmt || ''}`;
      const elCout = document.getElementById('fmdl-checkout-sched');
      if (elCout) elCout.textContent = `${folio.checkout_date_fmt || ''} at ${folio.checkout_time_fmt || ''}`;
      const elDur = document.getElementById('fmdl-duration-stay');
      if (elDur) elDur.textContent = `${folio.total_nights || 1} Night(s)`;
      const elResModeVal = document.getElementById('fmdl-resmode-val');
      if (elResModeVal) elResModeVal.textContent = folio.reservation_mode || 'Front Desk';
      const elMealVal = document.getElementById('fmdl-mealplan-val');
      if (elMealVal) elMealVal.textContent = folio.plan_name || 'Room Only (EP)';

      // Corporate Account Block
      const compRow = document.getElementById('fmdl-company-row');
      const compName = document.getElementById('fmdl-company-name');
      const compGstin = document.getElementById('fmdl-company-gstin');
      const compPhone = document.getElementById('fmdl-company-phone');
      if (compRow) {
        if (folio.company && folio.company.name) {
          compRow.style.display = 'block';
          if (compName) compName.textContent = folio.company.name;
          if (compGstin) compGstin.textContent = folio.company.gstin || 'N/A';
          if (compPhone) compPhone.textContent = folio.company.phone || 'N/A';
        } else {
          compRow.style.display = 'none';
        }
      }

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
      modalEl.classList.add('open');
      modalEl.style.setProperty('display', 'flex', 'important');
      modalEl.style.setProperty('visibility', 'visible', 'important');
      modalEl.style.setProperty('opacity', '1', 'important');
      modalEl.style.setProperty('z-index', '100050', 'important');
    }
    if (typeof openModal === 'function') {
      try { openModal('folio-details-modal'); } catch (_) {}
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

    // Total occupants registered across all rooms under this reservation
    let totalReservationOccupants = 0;
    rooms.forEach(r => {
      totalReservationOccupants += (r.occupants ? r.occupants.length : 0);
    });

    // Check if reservation has multiple guests and if settlement is complete
    const hasMultiple = totalReservationOccupants > 1;
    const isSettled = activeFolioData && (Number(activeFolioData.balance) <= 0.01 || (activeFolioData.status || '').toLowerCase() === 'settlement done');

    let html = '';
    rooms.forEach(r => {
      const isVacated = r.is_vacated === true;
      const roomStatusBadge = isVacated
        ? `<span style="background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa-solid fa-broom"></i> Vacated (Housekeeping Dirty)</span>`
        : `<span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px;"><i class="fa-solid fa-bed"></i> Reserved / Occupied</span>`;

      html += `
        <div class="room-alloc-card">
          <div class="room-alloc-header">
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
              <span class="room-tag">
                <i class="fa-solid fa-door-open"></i> ${escapeHtml(r.room_number)}
              </span>
              <span style="font-size: 12px; font-weight: 800; color: #0f172a;">Floor ${escapeHtml(r.floor)}</span>
              <span style="color: #cbd5e1;">•</span>
              <span style="font-size: 11px; color: #64748b; font-weight: 600;">${escapeHtml(r.category)}</span>
              <span style="color: #cbd5e1;">•</span>
              <span style="font-size: 11px; color: #64748b;">${escapeHtml(r.bedding)}</span>
              ${roomStatusBadge}
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
          const isCheckedOut = occ.is_checked_out || (occ.status === 'Checked Out');
          
          let actionMarkup = '';
          if (hasMultiple) {
            if (isCheckedOut) {
              const dtParts = [];
              if (occ.checkout_date) dtParts.push(occ.checkout_date);
              if (occ.checkout_time) dtParts.push(occ.checkout_time);
              const dtText = dtParts.join(' ');
              actionMarkup = `
                <span class="badge-checked-out-occupant" title="This occupant is checked out">
                  <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Checked Out ${dtText ? '(' + escapeHtml(dtText) + ')' : ''}
                </span>
              `;
            } else if (isSettled && activeFolioData && activeFolioData.status !== 'Checked Out' && activeFolioData.status !== 'Cancelled') {
              actionMarkup = `
                <button type="button" 
                        class="btn-early-checkout" 
                        onclick="openEarlyCheckoutConfirmModal(${occ.booking_id}, '${escapeHtml(occ.name).replace(/'/g, "\\'")}', '${escapeHtml(r.room_number).replace(/'/g, "\\'")}')" 
                        title="Early Check-out this specific guest from Room ${escapeHtml(r.room_number)}">
                  <i class="fa-solid fa-person-walking-arrow-right"></i> Early Checkout
                </button>
              `;
            }
          } else if (isCheckedOut) {
            const dtParts = [];
            if (occ.checkout_date) dtParts.push(occ.checkout_date);
            if (occ.checkout_time) dtParts.push(occ.checkout_time);
            const dtText = dtParts.join(' ');
            actionMarkup = `
              <span class="badge-checked-out-occupant" title="This occupant is checked out">
                <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Checked Out ${dtText ? '(' + escapeHtml(dtText) + ')' : ''}
              </span>
            `;
          }

          html += `
            <div class="occupant-badge-row ${isCheckedOut ? 'checked-out' : ''}">
              <div style="display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid ${occ.is_primary ? 'fa-star' : 'fa-user'}" style="color: ${isCheckedOut ? '#94a3b8' : (occ.is_primary ? '#f59e0b' : '#6366f1')};"></i>
                <strong style="color: ${isCheckedOut ? '#64748b' : '#1e293b'}; ${isCheckedOut ? 'text-decoration: line-through;' : ''}">${escapeHtml(occ.name)}</strong>
                ${occ.is_primary ? '<span style="background: #fef3c7; color: #b45309; font-size: 9px; font-weight: 800; padding: 1px 5px; border-radius: 4px;">Primary</span>' : ''}
              </div>
              <div style="display: flex; align-items: center; gap: 10px; color: #64748b; font-family: var(--font-mono, monospace);">
                <span><i class="fa-solid fa-phone" style="font-size: 9px;"></i> ${escapeHtml(occ.mobile)}</span>
                <span>•</span>
                <span>${escapeHtml(occ.id_card_type)}: ${escapeHtml(occ.id_card_number)}</span>
                ${actionMarkup}
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
    const billRateEl = document.getElementById('fmdl-bill-rate');
    if (billRateEl) billRateEl.textContent = formatINR(folio.room_rate);

    document.getElementById('fmdl-gross-amount').textContent = formatINR(folio.total_amount);
    document.getElementById('fmdl-totalpaid-amount').textContent = formatINR(folio.total_paid);

    const balanceBox = document.getElementById('fmdl-balance-box');
    const balanceAmountEl = document.getElementById('fmdl-balance-amount');
    const balanceLabelEl = document.getElementById('fmdl-balance-label');

    balanceAmountEl.textContent = formatINR(folio.balance);
    if (folio.balance > 0.01) {
      balanceAmountEl.style.color = '#e11d48';
      balanceLabelEl.textContent = 'Balance Due';
    } else {
      balanceAmountEl.style.color = '#059669';
      balanceLabelEl.textContent = 'Fully Settled';
    }

    // Breakdown details
    document.getElementById('fmdl-calc-row').textContent = `${formatINR(folio.room_rate)} × ${folio.total_nights} Nights = ${formatINR(folio.total_amount)}`;
    document.getElementById('fmdl-advance-row').textContent = formatINR(folio.advance_amount);
    document.getElementById('fmdl-settlement-row').textContent = formatINR(folio.settlement_paid);
    
    const totalPaidRow = document.getElementById('fmdl-totalpaid-row');
    if (totalPaidRow) totalPaidRow.textContent = formatINR(folio.total_paid);

    document.getElementById('fmdl-discount-row').textContent = folio.discount_amount > 0 ? `${formatINR(folio.discount_amount)} (${folio.discount_percentage}%)` : 'None';
    document.getElementById('fmdl-tax-row').textContent = folio.gst_amount > 0 ? `${formatINR(folio.gst_amount)} (${folio.gst_percentage}% ${folio.gst_type})` : 'Zero Tax / Exempt';
    
    const payableEl = document.getElementById('fmdl-payable-amount');
    if (payableEl) payableEl.textContent = formatINR(folio.payable_amount);

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

    const isCheckedOut = (folio.is_all_checked_out !== undefined) 
      ? folio.is_all_checked_out 
      : ((folio.status || '').toLowerCase() === 'checked out');
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
      Swal.fire({
        icon: 'warning',
        title: 'Invalid Amount',
        text: 'Please enter a valid settlement amount greater than 0.',
        confirmButtonColor: '#4f46e5'
      });
      amountInput.focus();
      return;
    }

    if (settleAmount > (Number(activeFolioData.balance) + 0.01)) {
      Swal.fire({
        icon: 'warning',
        title: 'Amount Exceeded',
        text: `Settlement amount cannot exceed the current balance of ${formatINR(activeFolioData.balance)}.`,
        confirmButtonColor: '#4f46e5'
      });
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
        Swal.fire({
          icon: 'success',
          title: 'Settlement Saved!',
          text: data.message || 'Settlement processed successfully!',
          confirmButtonColor: '#10b981',
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.reload();
        });
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Settlement Notice',
          text: data.error || data.message || 'Settlement failed. Please check your inputs.',
          confirmButtonColor: '#4f46e5'
        });
      }
    })
    .catch(err => {
      confirmBtn.disabled = false;
      confirmBtn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Confirm &amp; Save Settlement';
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'System Error',
        text: 'An error occurred during settlement submission. Please try again.',
        confirmButtonColor: '#4f46e5'
      });
    });
  }

  function executeFolioCheckout() {
    if (!activeFolioData) return;

    if (activeFolioData.balance > 0.01) {
      Swal.fire({
        icon: 'warning',
        title: 'Outstanding Balance Due',
        html: `Cannot check out reservation with outstanding balance of <strong style="color: #e11d48;">${formatINR(activeFolioData.balance)}</strong>.<br><br>Please complete settlement first before checking out.`,
        confirmButtonColor: '#4f46e5',
        confirmButtonText: 'OK'
      });
      return;
    }

    Swal.fire({
      title: 'Confirm Room Check-Out',
      html: `Are you sure you want to Check Out Booking <strong>#${escapeHtml(activeFolioData.reserve_id)}</strong> for <strong>${escapeHtml(activeFolioData.primary_guest?.name || 'Guest')}</strong>?<br><br><span style="font-size: 12px; color: #64748b;">The room status will be automatically scheduled for Housekeeping.</span>`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#10b981',
      cancelButtonColor: '#64748b',
      confirmButtonText: '<i class="fa-solid fa-check"></i> Yes, Check Out',
      cancelButtonText: 'Cancel'
    }).then(result => {
      if (!result.isConfirmed) return;

      const btn = document.getElementById('fmdl-action-btn-checkout');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Check Out...';
      }

      fetch(`{{ url('front-office/folio') }}/${activeFolioData.id}/checkout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          remarks: 'Checked out from Settlement Manager Counter'
        })
      })
      .then(res => res.json())
      .then(data => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-right-from-bracket"></i> Check Out';
        }
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Check Out Complete!',
            text: data.message || 'Reservation successfully checked out! Room(s) scheduled for housekeeping.',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'OK'
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({
            icon: 'warning',
            title: 'Check Out Notice',
            text: data.error || data.message || 'Check out failed.',
            confirmButtonColor: '#4f46e5'
          });
        }
      })
      .catch(err => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa-solid fa-right-from-bracket"></i> Check Out';
        }
        console.error(err);
        Swal.fire({
          icon: 'error',
          title: 'System Error',
          text: 'Network error while processing check out. Please try again.',
          confirmButtonColor: '#4f46e5'
        });
      });
    });
  }

  function closeFolioModal() {
    const modalEl = document.getElementById('folio-details-modal');
    if (modalEl) {
      modalEl.classList.remove('open');
      modalEl.style.setProperty('display', 'none', 'important');
    }
    document.body.style.overflow = '';
    if (typeof closeModal === 'function') {
      try { closeModal('folio-details-modal'); } catch (_) {}
    }
  }

  function openEarlyCheckoutConfirmModal(bookingId, guestName, roomNumber) {
    document.getElementById('eccm-booking-id').value = bookingId;
    document.getElementById('eccm-guest-name').textContent = guestName;
    document.getElementById('eccm-room-no').textContent = roomNumber;
    
    // Format current date and time
    const now = new Date();
    const dateFormatted = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    const timeFormatted = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });

    document.getElementById('eccm-checkout-date').textContent = dateFormatted;
    document.getElementById('eccm-checkout-time').textContent = timeFormatted;

    const btn = document.getElementById('eccm-btn-confirm');
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-check"></i> Yes, Early Check Out';
    }

    const modalEl = document.getElementById('early-checkout-confirm-modal');
    if (modalEl) {
      modalEl.classList.add('open');
      modalEl.style.setProperty('display', 'flex', 'important');
    }
  }

  function closeEarlyCheckoutConfirmModal() {
    const modalEl = document.getElementById('early-checkout-confirm-modal');
    if (modalEl) {
      modalEl.classList.remove('open');
      modalEl.style.setProperty('display', 'none', 'important');
    }
    if (typeof closeModal === 'function') {
      try { closeModal('early-checkout-confirm-modal'); } catch (_) {}
    }
  }

  function executeEarlyCheckout() {
    const bookingId = document.getElementById('eccm-booking-id').value;
    if (!bookingId) return;

    const btn = document.getElementById('eccm-btn-confirm');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking Out...';
    }

    fetch(`{{ url('front-office/folio/early-checkout-guest') }}/${bookingId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        remarks: 'Early checked out via view dossier'
      })
    })
    .then(res => res.json())
    .then(data => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Yes, Early Check Out';
      }
      closeEarlyCheckoutConfirmModal();

      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Guest Checked Out Early!',
          text: data.message || 'Guest checked out successfully.',
          confirmButtonColor: '#10b981',
          confirmButtonText: 'OK'
        }).then(() => {
          if (activeFolioData && activeFolioData.id) {
            openFolioModalById(activeFolioData.id, true);
          }
          if (data.is_all_checked_out) {
            window.location.reload();
          }
        });
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Notice',
          text: data.error || data.message || 'Early check out could not be completed.',
          confirmButtonColor: '#4f46e5'
        });
      }
    })
    .catch(err => {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Yes, Early Check Out';
      }
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'System Error',
        text: 'Network error while processing early check out. Please try again.',
        confirmButtonColor: '#4f46e5'
      });
    });
  }

  // Close on backdrop click & ESC key + Event Delegation for View Action Buttons
  document.addEventListener('DOMContentLoaded', function() {
    const modalBackdrop = document.getElementById('folio-details-modal');
    if (modalBackdrop) {
      modalBackdrop.addEventListener('click', function(e) {
        if (e.target === this) {
          closeFolioModal();
        }
      });
    }

    const earlyModalBackdrop = document.getElementById('early-checkout-confirm-modal');
    if (earlyModalBackdrop) {
      earlyModalBackdrop.addEventListener('click', function(e) {
        if (e.target === this) {
          closeEarlyCheckoutConfirmModal();
        }
      });
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeEarlyCheckoutConfirmModal();
        closeFolioModal();
      }
    });

    // Delegated click handler for view buttons & booking numbers
    document.addEventListener('click', function(e) {
      const trigger = e.target.closest('.btn-action-view, [data-booking-id], .folio-id-text, .folio-number-sub');
      if (trigger) {
        const bookingId = trigger.getAttribute('data-booking-id');
        if (bookingId) {
          e.preventDefault();
          openFolioModalById(bookingId);
        }
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

    const printArea = document.getElementById('print-area') || document.getElementById('folio-print-area');
    if (printArea) {
      printArea.innerHTML = `
        <div style="font-family: Arial, sans-serif; padding: 24px; color: #1e293b; max-width: 800px; margin: auto;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #4f46e5; padding-bottom: 14px; margin-bottom: 20px;">
            <div>
              <h1 style="margin: 0; font-size: 24px; color: #1e1b4b; text-transform: uppercase;">Hotel Sagar Sonnet</h1>
              <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Luxury Stays &amp; Hospitality Services • Live PMS Dossier</div>
            </div>
            <div style="text-align: right;">
              <div style="font-size: 18px; font-weight: bold; color: #4f46e5;">TAX INVOICE / SETTLEMENT</div>
              <div style="font-size: 12px; font-family: monospace; color: #334155; margin-top: 4px;">Settlement #: ${escapeHtml(f.folio_number)}</div>
              <div style="font-size: 12px; font-family: monospace; color: #64748b;">Booking #: ${escapeHtml(f.reserve_id)}</div>
            </div>
          </div>

          <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 12px; line-height: 1.6;">
            <div style="flex: 1;">
              <strong style="color: #4f46e5; font-size: 13px;">GUEST DETAILS:</strong><br>
              Name: <strong>${escapeHtml(pg.name)}</strong><br>
              Phone: ${escapeHtml(pg.mobile)}<br>
              Email: ${escapeHtml(pg.email)}<br>
              Address: ${escapeHtml(pg.address || 'N/A')}<br>
              ID Proof: ${escapeHtml(pg.id_card_type)} - ${escapeHtml(pg.id_card_number)}
            </div>
            <div style="flex: 1; text-align: right;">
              <strong style="color: #4f46e5; font-size: 13px;">STAY INFORMATION:</strong><br>
              Check-In: <strong>${escapeHtml(f.checkin_date_fmt)} (${escapeHtml(f.checkin_time_fmt)})</strong><br>
              Check-Out: <strong>${escapeHtml(f.checkout_date_fmt)} (${escapeHtml(f.checkout_time_fmt)})</strong><br>
              Duration: <strong>${escapeHtml(f.total_nights)} Night(s)</strong><br>
              Meal Plan: <strong>${escapeHtml(f.plan_name)}</strong><br>
              Total Occupants: <strong>${escapeHtml(f.total_guests_count)} Person(s)</strong>
            </div>
          </div>

          <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;">
            <thead>
              <tr style="background: #f1f5f9; text-align: left;">
                <th style="padding: 8px; border: 1px solid #cbd5e1;">Room / Floor</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1;">Category &amp; Bedding</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1;">Occupants</th>
                <th style="padding: 8px; border: 1px solid #cbd5e1; text-align: right;">Tariff / Night</th>
              </tr>
            </thead>
            <tbody>
              ${roomRowsHtml}
            </tbody>
          </table>

          <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
            <div style="width: 320px; font-size: 12px;">
              <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #e2e8f0;">
                <span>Gross Room Charges:</span>
                <span style="font-family: monospace;">${formatINR(f.total_amount)}</span>
              </div>
              <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #e2e8f0; color: #059669;">
                <span>Discount (${escapeHtml(f.discount_percentage)}%):</span>
                <span style="font-family: monospace;">- ${formatINR(f.discount_amount)}</span>
              </div>
              <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #e2e8f0; color: #6366f1;">
                <span>Tax (${escapeHtml(f.gst_percentage)}% GST):</span>
                <span style="font-family: monospace;">+ ${formatINR(f.gst_amount)}</span>
              </div>
              <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #cbd5e1; font-weight: bold;">
                <span>Net Payable:</span>
                <span style="font-family: monospace;">${formatINR(f.payable_amount)}</span>
              </div>
              <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #e2e8f0; color: #059669;">
                <span>Advance Paid:</span>
                <span style="font-family: monospace;">${formatINR(f.advance_amount)}</span>
              </div>
              <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #e2e8f0; color: #4f46e5;">
                <span>Settlements Paid:</span>
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
      window.print();
    }
  }

  // Explicit window bindings
  window.openFolioModalById = openFolioModalById;
  window.populateAndOpenFolioModal = populateAndOpenFolioModal;
  window.closeFolioModal = closeFolioModal;
  window.openEarlyCheckoutConfirmModal = openEarlyCheckoutConfirmModal;
  window.closeEarlyCheckoutConfirmModal = closeEarlyCheckoutConfirmModal;
  window.executeEarlyCheckout = executeEarlyCheckout;
  window.executeFolioSettlement = executeFolioSettlement;
  window.executeFolioCheckout = executeFolioCheckout;
  window.toggleFolioSettlementPanel = toggleFolioSettlementPanel;
  window.setFullFolioSettlementAmount = setFullFolioSettlementAmount;
  window.printFolioInvoice = printFolioInvoice;
</script>
@endpush
