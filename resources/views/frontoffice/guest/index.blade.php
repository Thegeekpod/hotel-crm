@extends('frontoffice.layouts.app')

@section('title', 'Guest CRM & Master Directory - Hotel Sagar Sonnet CRM / PMS')

@push('styles')
<style>
  /* Header styling */
  .guest-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 10px -3px rgba(0,0,0,0.04);
  }

  /* Toolbar styling */
  .guest-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 18px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }

  .search-box-wrap {
    position: relative;
    flex: 1;
    min-width: 260px;
    max-width: 440px;
  }

  .search-box-wrap i {
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
    padding: 6px 12px 6px 36px;
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
    outline: none;
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
  .guest-row {
    transition: background 0.15s ease;
  }

  .guest-row:hover {
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

  /* Segment Badges */
  .badge-vip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(217, 119, 6, 0.1));
    color: #b45309;
    border: 1px solid rgba(245, 158, 11, 0.3);
  }

  .badge-corporate {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    background: rgba(99, 102, 241, 0.1);
    color: #4338ca;
    border: 1px solid rgba(99, 102, 241, 0.25);
  }

  .badge-regular {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
  }

  /* Action buttons */
  .action-flex-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

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
  .pms-page-nav {
    display: flex;
    gap: 4px;
    align-items: center;
  }

  .pms-page-btn {
    min-width: 30px;
    height: 30px;
    padding: 0 6px;
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
    transition: all 0.15s ease;
  }

  .pms-page-btn:hover:not(:disabled) {
    border-color: #6366f1;
    color: #6366f1;
    background: #eef2ff;
  }

  .pms-page-btn.active {
    background: #6366f1;
    border-color: #6366f1;
    color: #ffffff;
  }

  .pms-page-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
  }

  /* Guest Dossier Modal Styles */
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

  /* Highlight Banner */
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

  /* Media Print Rules */
  @media print {
    body > *:not(#print-area) {
      display: none !important;
    }
    #print-area {
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
@php
  $serializedGuests = $guests->map(function($g) {
    return [
      'id' => $g->id,
      'reserve_id' => $g->reserve_id ?: ('RES-' . $g->id),
      'folio_number' => $g->folio_number ?: ('FOL-' . ($g->room?->room_number ?? '100') . '-' . $g->id),
      'guest_name' => ($g->title?->name ? $g->title->name . ' ' : '') . $g->guest_name,
      'raw_name' => $g->guest_name,
      'title' => $g->title?->name ?? '',
      'mobile' => $g->mobile ?: 'N/A',
      'email' => $g->email ?: 'N/A',
      'city' => $g->city ?: 'N/A',
      'guest_address' => $g->guest_address ?: 'N/A',
      'nationality' => $g->nationality?->name ?? 'Indian',
      'id_card_type' => $g->idCardType?->name ?? 'N/A',
      'id_card_number' => $g->id_card_number ?: 'N/A',
      'registration_type' => $g->registrationType?->name ?? 'Regular',
      'reservation_mode' => $g->reservationMode?->name ?? 'Front Desk',
      'has_privilege_card' => (bool)$g->has_privilege_card,
      'privilege_card_no' => $g->privilege_card_no ?: 'N/A',
      'room_number' => (string)($g->room?->room_number ?? 'Pending'),
      'room_category' => strtoupper($g->room?->categoryRelation?->name ?? ($g->room?->category ?? 'DELUXE')),
      'room_floor' => (string)($g->room?->floorRelation?->floor ?? ($g->room?->floor ?? '1')),
      'room_bedding' => $g->room?->beddingConfigRelation?->name ?? 'Standard',
      'room_rate' => (float)($g->room_rate ?: ($g->room?->rate ?: 0)),
      'reserve_date' => $g->reserve_date ? $g->reserve_date->format('d M Y') : date('d M Y'),
      'reserve_time' => $g->reserve_time ? date('h:i A', strtotime($g->reserve_time)) : '12:00 PM',
      'checkout_date' => $g->checkout_date ? $g->checkout_date->format('d M Y') : date('d M Y', strtotime('+1 day')),
      'checkout_time' => $g->checkout_time ? date('h:i A', strtotime($g->checkout_time)) : '11:00 AM',
      'total_nights' => (int)($g->total_nights ?: 1),
      'total_amount' => (float)$g->total_amount,
      'discount_percentage' => (float)$g->discount_percentage,
      'discount_amount' => (float)$g->discount_amount,
      'payable_amount' => (float)$g->payable_amount,
      'advance_amount' => (float)$g->advance_amount,
      'balance' => (float)$g->balance,
      'payment_mode' => $g->paymentMode?->name ?? 'Cash / Direct',
      'payment_remarks' => $g->payment_remarks ?: 'None',
      'status' => $g->status ?: 'Confirmed',
      'created_at_fmt' => $g->created_at ? $g->created_at->format('d M Y, h:i A') : 'N/A',
      'company' => $g->company ? [
        'name' => $g->company->name,
        'gstin' => $g->company->gstin ?: 'N/A',
        'address' => $g->company->address ?: 'N/A',
        'phone' => $g->company->phone ?: 'N/A',
        'discount_type' => $g->company->discount_type,
        'discount_value' => (float)$g->company->discount_value,
      ] : ($g->new_company_name ? [
        'name' => $g->new_company_name,
        'gstin' => $g->new_company_gstin ?: 'N/A',
        'address' => $g->new_company_address ?: 'N/A',
        'phone' => $g->new_company_phone ?: 'N/A',
        'discount_type' => 'percentage',
        'discount_value' => 0,
      ] : null),
    ];
  })->values();
@endphp

<main class="pms-main-viewport">
  <div class="pms-page-container">
    
    <!-- Clean Header Card -->
    <div class="guest-header-card">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
          <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-address-card" style="color: #6366f1;"></i> 
            Guest Master & CRM Directory
            <span style="font-size: 11px; font-weight: 700; background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 2px 10px; border-radius: 20px;">
              {{ count($guests) }} Total Profiles
            </span>
          </h2>
          <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
            Comprehensive guest directory, stay history dossiers, allotted rooms, billing statements, and identity records.
          </div>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
          <button type="button" class="btn-ui-primary" onclick="if(typeof openReservationModal === 'function') openReservationModal(); else openModal('reserve-modal');">
            <i class="fa-solid fa-calendar-plus"></i> New Reservation
          </button>
          <a href="{{ route('frontoffice.arrivals') }}" class="btn-ui-secondary" style="border: 1px solid #cbd5e1; background: #ffffff; color: #334155; text-decoration: none; padding: 7px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-plane-arrival"></i> View Arrivals
          </a>
          <a href="{{ route('frontoffice.dashboard') }}" class="btn-ui-secondary" style="border: 1px solid #cbd5e1; background: #ffffff; color: #334155; text-decoration: none; padding: 7px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-table-cells"></i> Room Rack
          </a>
        </div>
      </div>
    </div>

    <!-- Toolbar: Search Filter & Dropdowns & Entries Selector -->
    <div class="guest-toolbar">
      <div style="display: flex; gap: 12px; align-items: center; flex: 1; flex-wrap: wrap;">
        <!-- Search Box -->
        <div class="search-box-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="guest-crm-search-input" placeholder="Search by Guest Name, Phone, Email, Room, Reserve ID, Folio, City..." oninput="onCrmSearchOrFilterChange()">
        </div>

        <!-- Status Filter Dropdown -->
        <div style="display: inline-flex; align-items: center; gap: 6px;">
          <label for="crm-status-filter" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
            <i class="fa-solid fa-filter" style="color: #6366f1;"></i> Status:
          </label>
          <select id="crm-status-filter" class="pms-filter-select" onchange="onCrmSearchOrFilterChange()">
            <option value="all">All Statuses</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Checked In">Checked In</option>
            <option value="Arrived">Arrived</option>
            <option value="Stay Over">Stay Over</option>
            <option value="Checked Out">Checked Out</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Registration Type Filter -->
        <div style="display: inline-flex; align-items: center; gap: 6px;">
          <label for="crm-regtype-filter" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
            <i class="fa-solid fa-id-badge" style="color: #6366f1;"></i> Reg Type:
          </label>
          <select id="crm-regtype-filter" class="pms-filter-select" onchange="onCrmSearchOrFilterChange()">
            <option value="all">All Reg Types</option>
            @foreach($registrationTypes ?? [] as $rt)
              <option value="{{ strtolower($rt->name) }}">{{ $rt->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- Reset Button -->
        <button type="button" class="btn-ui-secondary" onclick="resetCrmFilters()" style="padding: 7px 14px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 11px; font-weight: 700; color: #475569; display: inline-flex; align-items: center; gap: 5px;">
          <i class="fa-solid fa-rotate-left"></i> Reset
        </button>
      </div>

      <!-- Page Size Selector -->
      <div style="display: flex; gap: 12px; align-items: center;">
        <div class="pms-per-page-wrap">
          <span>Show</span>
          <select class="pms-per-page-select" id="crm-per-page-select" onchange="changeCrmPageSize(this.value)">
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
          </select>
          <span>entries</span>
        </div>
      </div>
    </div>

    <!-- Main Guests CRM Table with SL Indexing -->
    <div class="pms-page-content">
      <div class="pms-table-box" style="border-radius: 14px; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.06); background: #ffffff; overflow-x: auto;">
        <table class="pms-table" id="guest-crm-table">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">SL</th>
              <th style="width: 120px;">Guest ID</th>
              <th style="min-width: 160px;">Name</th>
              <th style="min-width: 130px;">Phone</th>
              <th style="min-width: 160px;">Email</th>
              <th style="min-width: 130px;">ID Proof</th>
              <th style="min-width: 130px; text-align: center;">Registration Type</th>
              <th style="min-width: 100px; text-align: center;">Status</th>
              <th style="width: 70px; text-align: center;">Action</th>
            </tr>
          </thead>
          <tbody id="guest-crm-tbody">
            <!-- Rendered dynamically by renderTable() with SL Indexing & Pagination -->
          </tbody>
        </table>

        <!-- Table Pagination Footer -->
        <div class="pms-table-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; border-top: 1px solid #e2e8f0; background: #ffffff; flex-wrap: wrap; gap: 10px;">
          <div class="pms-pagination-info" id="crm-pagination-info" style="font-size: 12px; font-weight: 600; color: #64748b;">
            Showing 0 to 0 of 0 records
          </div>
          <div class="pms-page-nav" id="crm-pagination-nav"></div>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Bottom Summary Bar -->
<footer class="pms-bottom-bar">
  <div style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
    <span><i class="fa-solid fa-users" style="color: #6366f1;"></i> Total Guests: <strong>{{ $stats['total'] ?? count($guests) }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-key" style="color: #0891b2;"></i> Checked In: <strong>{{ $stats['checked_in'] ?? 0 }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-circle-check" style="color: #4338ca;"></i> Confirmed: <strong>{{ $stats['confirmed'] ?? 0 }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-bed" style="color: #d97706;"></i> Stay Over: <strong>{{ $stats['stay_over'] ?? 0 }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-gem" style="color: #f59e0b;"></i> VIP / Privilege: <strong>{{ $stats['vip'] ?? 0 }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-building" style="color: #10b981;"></i> Corporate: <strong>{{ $stats['corporate'] ?? 0 }}</strong></span>
  </div>
</footer>

<!-- ========================================== -->
<!-- UNIFORM GUEST & RESERVATION DETAILS MODAL  -->
<!-- ========================================== -->
<div class="modal-backdrop" id="guest-crm-details-modal">
  <div class="modal-window large" style="width: 900px; max-width: 95vw; max-height: 92vh; display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3); border: none; box-sizing: border-box;">
    
    <!-- Modal Header -->
    <div class="modal-top" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: #ffffff; padding: 14px 22px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.15);">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center; font-size: 16px; color: #ffffff;">
          <i class="fa-solid fa-id-card-clip"></i>
        </div>
        <div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <h3 id="crm-mdl-guest-name" style="margin: 0; font-size: 16px; font-weight: 800; color: #ffffff;">Guest Profile Dossier</h3>
            <span id="crm-mdl-status-badge" class="status-badge" style="background: #ffffff; color: #4338ca; font-weight: 800;">Confirmed</span>
          </div>
          <div style="font-size: 11px; opacity: 0.85; margin-top: 2px; font-family: var(--font-mono, monospace);">
            Reserve Ref: <span id="crm-mdl-reserve-id" style="font-weight: 700;">-</span> &nbsp;|&nbsp; Folio: <span id="crm-mdl-folio-number" style="font-weight: 700;">-</span>
          </div>
        </div>
      </div>
      <button type="button" class="modal-close" onclick="closeModal('guest-crm-details-modal')" style="background: rgba(255,255,255,0.2); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">&times;</button>
    </div>

    <!-- Modal Body Area -->
    <div class="modal-content-area" style="padding: 20px 24px; background: #f8fafc; overflow-y: auto; max-height: calc(92vh - 130px);">
      
      <!-- Top Highlight Banner -->
      <div class="dossier-highlight-box">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 44px; height: 44px; border-radius: 10px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa-solid fa-door-open"></i>
          </div>
          <div>
            <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Allotted Room & Category</div>
            <div style="font-size: 16px; font-weight: 900; color: #0f172a; margin-top: 1px;">
              Room <span id="crm-mdl-room-number">-</span> • <span id="crm-mdl-room-category">-</span>
            </div>
          </div>
        </div>

        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
          <span style="font-size: 11px; font-weight: 800; background: #ffffff; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; color: #475569;">
            <i class="fa-solid fa-stairs" style="color: #6366f1;"></i> Floor <span id="crm-mdl-room-floor">-</span>
          </span>
          <span style="font-size: 11px; font-weight: 800; background: #ffffff; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; color: #475569;">
            <i class="fa-solid fa-bed" style="color: #6366f1;"></i> <span id="crm-mdl-room-bedding">-</span>
          </span>
          <span style="font-size: 11px; font-weight: 800; background: #e0e7ff; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 6px; color: #4338ca;">
            <i class="fa-solid fa-moon"></i> <span id="crm-mdl-total-nights">1</span> Night(s) Stay
          </span>
        </div>
      </div>

      <!-- 2-Column Uniform Dossier Grid -->
      <div class="dossier-grid">
        
        <!-- CARD 1: Guest Profile Information -->
        <div class="dossier-card">
          <div class="dossier-card-title">
            <i class="fa-solid fa-user-check"></i> Guest Profile Details
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Name</span>
            <span class="dossier-val" id="crm-mdl-card-name">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Mobile Number</span>
            <span class="dossier-val mono" id="crm-mdl-card-phone">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Email Address</span>
            <span class="dossier-val" id="crm-mdl-card-email">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">City / Location</span>
            <span class="dossier-val" id="crm-mdl-card-city">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Address</span>
            <span class="dossier-val" id="crm-mdl-card-address" style="max-width: 220px; white-space: normal;">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Nationality</span>
            <span class="dossier-val" id="crm-mdl-card-nationality">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Identity Proof</span>
            <span class="dossier-val" id="crm-mdl-card-idproof">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Registration Type</span>
            <span class="dossier-val" id="crm-mdl-card-regtype">-</span>
          </div>

          <div class="dossier-field-row" id="crm-mdl-card-privilege-row" style="display: none;">
            <span class="dossier-label">Privilege / VIP Card</span>
            <span class="dossier-val mono" id="crm-mdl-card-privilege" style="color: #b45309;">-</span>
          </div>
        </div>

        <!-- CARD 2: Stay Schedule & Corporate Account -->
        <div class="dossier-card">
          <div class="dossier-card-title">
            <i class="fa-solid fa-calendar-days"></i> Stay & Corporate Details
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-In (Arrival)</span>
            <span class="dossier-val mono" id="crm-mdl-card-checkin" style="color: #4f46e5;">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-Out (Departure)</span>
            <span class="dossier-val mono" id="crm-mdl-card-checkout" style="color: #059669;">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Duration of Stay</span>
            <span class="dossier-val" id="crm-mdl-card-duration">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Reservation Mode</span>
            <span class="dossier-val" id="crm-mdl-card-resmode">-</span>
          </div>

          <!-- Corporate Account Block -->
          <div id="crm-mdl-corporate-card-section" style="margin-top: 10px; padding: 10px; border-radius: 8px; background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.15); display: none;">
            <div style="font-size: 10px; font-weight: 800; color: #4338ca; text-transform: uppercase; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-building"></i> Corporate Account
            </div>
            <div class="dossier-field-row" style="padding: 3px 0;">
              <span class="dossier-label">Company Name</span>
              <span class="dossier-val" id="crm-mdl-company-name" style="color: #4338ca;">-</span>
            </div>
            <div class="dossier-field-row" style="padding: 3px 0;">
              <span class="dossier-label">Company GSTIN</span>
              <span class="dossier-val mono" id="crm-mdl-company-gstin">-</span>
            </div>
            <div class="dossier-field-row" style="padding: 3px 0;">
              <span class="dossier-label">Corporate Discount</span>
              <span class="dossier-val" id="crm-mdl-company-discount" style="color: #059669;">-</span>
            </div>
          </div>
        </div>

      </div>

      <!-- CARD 3: Pricing & Financial Breakdown -->
      <div class="dossier-card" style="margin-bottom: 0;">
        <div class="dossier-card-title">
          <i class="fa-solid fa-file-invoice-dollar"></i> Pricing & Billing Breakdown
        </div>

        <!-- 4-Stat Metric Boxes -->
        <div class="billing-summary-grid">
          <div class="bill-stat-item">
            <div class="bill-stat-label">Daily Room Rate</div>
            <div class="bill-stat-value" id="crm-mdl-bill-rate" style="color: #334155;">₹ 0.00</div>
          </div>

          <div class="bill-stat-item">
            <div class="bill-stat-label">Gross Amount</div>
            <div class="bill-stat-value" id="crm-mdl-bill-gross" style="color: #1e293b;">₹ 0.00</div>
          </div>

          <div class="bill-stat-item">
            <div class="bill-stat-label">Advance Paid</div>
            <div class="bill-stat-value" id="crm-mdl-bill-advance" style="color: #059669;">₹ 0.00</div>
          </div>

          <div class="bill-stat-item" id="crm-mdl-balance-box">
            <div class="bill-stat-label" id="crm-mdl-balance-label">Balance Due</div>
            <div class="bill-stat-value" id="crm-mdl-bill-balance" style="color: #e11d48;">₹ 0.00</div>
          </div>
        </div>

        <div style="margin-top: 14px; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 8px; padding: 10px 14px;">
          <div class="dossier-field-row">
            <span class="dossier-label">Room Rate × Nights</span>
            <span class="dossier-val mono" id="crm-mdl-bill-calc-row">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Discount Applied</span>
            <span class="dossier-val mono" id="crm-mdl-bill-discount" style="color: #059669;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Net Payable Amount</span>
            <span class="dossier-val mono" id="crm-mdl-bill-payable" style="font-size: 13px; font-weight: 900; color: #0f172a;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Payment Mode</span>
            <span class="dossier-val" id="crm-mdl-bill-paymode">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Payment Remarks / Reference</span>
            <span class="dossier-val" id="crm-mdl-bill-remarks" style="color: #64748b;">-</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Footer Actions -->
    <div class="modal-bot" style="padding: 12px 22px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
      <div style="font-size: 11px; color: #64748b; font-weight: 600;">
        <i class="fa-solid fa-clock-rotate-left"></i> Record Registered: <span id="crm-mdl-registered-at">-</span>
      </div>

      <div style="display: flex; gap: 8px; align-items: center;">
        <button type="button" class="btn-ui-secondary" onclick="printCrmGuestDossier()" style="padding: 7px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 12px; font-weight: 700; color: #334155; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-print"></i> Print Dossier
        </button>
        <button type="button" class="btn-ui-secondary" onclick="closeModal('guest-crm-details-modal')" style="padding: 7px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 12px; font-weight: 700; color: #64748b;">
          Close
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Hidden Clean Print Template Area -->
<div id="print-area" style="display: none;"></div>

@endsection

@push('scripts')
<script>
  window.allCrmGuests = @json($serializedGuests);
  let currentActiveCrmGuest = null;
  let currentCrmPage = 1;
  let crmPageSize = 10;

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

  // Format currency in INR
  function formatINR(val) {
    return '₹ ' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // Status badge color mapper
  function getStatusBadgeColor(status) {
    switch (status) {
      case 'Confirmed': return 'confirmed';
      case 'Arrived': return 'arrived';
      case 'Checked In': return 'checkedin';
      case 'Stay Over': return 'stayover';
      case 'Checked Out': return 'checkedout';
      case 'Cancelled': return 'cancelled';
      default: return 'confirmed';
    }
  }

  // Filter records based on search, status, and registration type
  function getFilteredCrmRecords() {
    const query = (document.getElementById('guest-crm-search-input')?.value || '').toLowerCase().trim();
    const selectedStatus = document.getElementById('crm-status-filter')?.value || 'all';
    const selectedRegType = document.getElementById('crm-regtype-filter')?.value || 'all';

    return (window.allCrmGuests || []).filter(item => {
      // Status dropdown filter
      if (selectedStatus !== 'all') {
        if (!item.status || item.status.toLowerCase() !== selectedStatus.toLowerCase()) {
          return false;
        }
      }

      // Registration Type filter
      if (selectedRegType !== 'all') {
        if (!item.registration_type || item.registration_type.toLowerCase() !== selectedRegType.toLowerCase()) {
          return false;
        }
      }

      // Search keyword filter
      if (!query) return true;
      const searchBlob = `${item.guest_name} ${item.mobile} ${item.email} ${item.city} ${item.reserve_id} ${item.folio_number} ${item.id_card_number} ${item.registration_type} ${item.status}`.toLowerCase();
      return searchBlob.includes(query);
    });
  }

  // Render Table with SL Indexing and Pagination
  function renderCrmTable() {
    const filtered = getFilteredCrmRecords();
    const totalRecords = filtered.length;
    let totalPages = 1;
    let paginatedRecords = filtered;

    if (crmPageSize !== 'all') {
      const limit = parseInt(crmPageSize, 10);
      totalPages = Math.ceil(totalRecords / limit) || 1;
      if (currentCrmPage > totalPages) currentCrmPage = totalPages;
      if (currentCrmPage < 1) currentCrmPage = 1;
      const startIdx = (currentCrmPage - 1) * limit;
      paginatedRecords = filtered.slice(startIdx, startIdx + limit);
    }

    const tbody = document.getElementById('guest-crm-tbody');
    if (!tbody) return;

    if (paginatedRecords.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 36px 20px;">
            <div style="font-size: 28px; color: #cbd5e1; margin-bottom: 8px;"><i class="fa-solid fa-users-slash"></i></div>
            <div style="font-size: 13px; font-weight: 700; color: #64748b;">No matching guests found.</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Try modifying your search query or filter options.</div>
          </td>
        </tr>
      `;
      renderCrmPagination(totalRecords, totalPages, 0, 0);
      return;
    }

    const startIndex = crmPageSize === 'all' ? 0 : (currentCrmPage - 1) * parseInt(crmPageSize, 10);

    tbody.innerHTML = paginatedRecords.map((item, idx) => {
      const sl = startIndex + idx + 1;
      const badgeColor = getStatusBadgeColor(item.status);

      return `
        <tr class="guest-row" id="row-guest-${item.id}">
          <!-- SL Indexing -->
          <td style="text-align: center; font-family: var(--font-mono, monospace); font-weight: 800; color: #64748b; font-size: 11px;">
            ${sl}
          </td>

          <!-- Guest ID (Reserve ID & Folio) -->
          <td>
            <div style="font-family: var(--font-mono, monospace); font-weight: 800; color: #4f46e5; font-size: 12px;">
              ${escapeHtml(item.reserve_id)}
            </div>
            <div style="font-size: 10px; color: #64748b; font-family: var(--font-mono, monospace); margin-top: 2px;">
              <i class="fa-regular fa-file-lines"></i> ${escapeHtml(item.folio_number)}
            </div>
          </td>

          <!-- Name -->
          <td>
            <div style="font-weight: 800; color: #0f172a; font-size: 13px;">
              ${escapeHtml(item.guest_name)}
            </div>
            ${item.city && item.city !== 'N/A' ? `
              <div style="font-size: 10px; color: #94a3b8; margin-top: 2px;">
                <i class="fa-solid fa-location-dot" style="font-size: 9px;"></i> ${escapeHtml(item.city)}
              </div>` : ''}
          </td>

          <!-- Phone -->
          <td>
            <div style="font-family: var(--font-mono, monospace); font-weight: 700; font-size: 12px; color: #1e293b; display: inline-flex; align-items: center; gap: 5px;">
              <i class="fa-solid fa-phone" style="font-size: 10px; color: #10b981;"></i>
              <span>${escapeHtml(item.mobile)}</span>
            </div>
          </td>

          <!-- Email -->
          <td>
            <div style="font-size: 11px; color: #475569;">
              ${item.email && item.email !== 'N/A' ? `
                <span title="${escapeHtml(item.email)}">
                  <i class="fa-regular fa-envelope" style="font-size: 10px; color: #6366f1; margin-right: 4px;"></i>${escapeHtml(item.email)}
                </span>
              ` : '<span style="color: #94a3b8;">N/A</span>'}
            </div>
          </td>

          <!-- ID Proof -->
          <td>
            <div style="font-size: 11px; font-weight: 700; color: #334155;">
              ${escapeHtml(item.id_card_type)}
            </div>
            <div style="font-size: 10px; color: #64748b; font-family: var(--font-mono, monospace); margin-top: 2px;">
              ${escapeHtml(item.id_card_number)}
            </div>
          </td>

          <!-- Registration Type -->
          <td style="text-align: center;">
            <span style="font-size: 10px; padding: 3px 9px; border-radius: 6px; background: #f1f5f9; color: #334155; font-weight: 700; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; gap: 4px;">
              <i class="fa-solid fa-id-badge" style="font-size: 9px; color: #6366f1;"></i>
              ${escapeHtml(item.registration_type || 'Regular')}
            </span>
          </td>

          <!-- Status Badge -->
          <td style="text-align: center;">
            <span class="status-badge ${badgeColor}">
              <i class="fa-solid fa-circle" style="font-size: 6px;"></i>
              <span>${escapeHtml(item.status)}</span>
            </span>
          </td>

          <!-- Action: Uniform Eye View Button -->
          <td style="text-align: center;">
            <button type="button" 
                    class="btn-action-view" 
                    onclick="openGuestDetailsModalById(${item.id})" 
                    title="View Complete Guest Dossier (Rooms, Stay, Billing, Company)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </td>
        </tr>
      `;
    }).join('');

    const fromItem = totalRecords === 0 ? 0 : startIndex + 1;
    const toItem = crmPageSize === 'all' ? totalRecords : Math.min(startIndex + parseInt(crmPageSize, 10), totalRecords);
    renderCrmPagination(totalRecords, totalPages, fromItem, toItem);
  }

  // Render Pagination Navigation Controls
  function renderCrmPagination(total, pages, from, to) {
    const info = document.getElementById('crm-pagination-info');
    if (info) {
      info.textContent = total === 0 ? 'Showing 0 records' : `Showing ${from} to ${to} of ${total} records`;
    }

    const nav = document.getElementById('crm-pagination-nav');
    if (!nav) return;

    if (pages <= 1) {
      nav.innerHTML = '';
      return;
    }

    let buttons = `
      <button type="button" class="pms-page-btn" ${currentCrmPage === 1 ? 'disabled' : ''} onclick="goToCrmPage(${currentCrmPage - 1})" title="Previous Page">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
    `;

    for (let p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || (p >= currentCrmPage - 1 && p <= currentCrmPage + 1)) {
        buttons += `
          <button type="button" class="pms-page-btn ${p === currentCrmPage ? 'active' : ''}" onclick="goToCrmPage(${p})">
            ${p}
          </button>
        `;
      } else if (p === currentCrmPage - 2 || p === currentCrmPage + 2) {
        buttons += `<span class="pms-page-btn" style="border:none; cursor:default; background:transparent;">...</span>`;
      }
    }

    buttons += `
      <button type="button" class="pms-page-btn" ${currentCrmPage === pages ? 'disabled' : ''} onclick="goToCrmPage(${currentCrmPage + 1})" title="Next Page">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    `;

    nav.innerHTML = buttons;
  }

  // Handle page size change
  function changeCrmPageSize(val) {
    crmPageSize = val === 'all' ? 'all' : parseInt(val, 10);
    currentCrmPage = 1;
    renderCrmTable();
  }

  // Handle page jump
  function goToCrmPage(p) {
    currentCrmPage = p;
    renderCrmTable();
  }

  // Triggered on search input or filter changes
  function onCrmSearchOrFilterChange() {
    currentCrmPage = 1;
    renderCrmTable();
  }

  // Reset all filters
  function resetCrmFilters() {
    const searchInp = document.getElementById('guest-crm-search-input');
    const statusSelect = document.getElementById('crm-status-filter');
    const regTypeSelect = document.getElementById('crm-regtype-filter');
    if (searchInp) searchInp.value = '';
    if (statusSelect) statusSelect.value = 'all';
    if (regTypeSelect) regTypeSelect.value = 'all';
    currentCrmPage = 1;
    renderCrmTable();
  }

  // Open Uniform Guest Dossier Modal by Guest ID
  function openGuestDetailsModalById(guestId) {
    const guest = (window.allCrmGuests || []).find(g => String(g.id) === String(guestId));
    if (!guest) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('Guest record not found', 'error');
      } else {
        alert('Guest record not found');
      }
      return;
    }

    currentActiveCrmGuest = guest;

    // Header info
    document.getElementById('crm-mdl-guest-name').textContent = guest.guest_name;
    document.getElementById('crm-mdl-reserve-id').textContent = guest.reserve_id;
    document.getElementById('crm-mdl-folio-number').textContent = guest.folio_number;
    document.getElementById('crm-mdl-registered-at').textContent = guest.created_at_fmt || 'N/A';

    const statusBadge = document.getElementById('crm-mdl-status-badge');
    if (statusBadge) {
      statusBadge.textContent = guest.status;
      statusBadge.className = `status-badge ${getStatusBadgeColor(guest.status)}`;
    }

    // Highlight Banner
    document.getElementById('crm-mdl-room-number').textContent = guest.room_number;
    document.getElementById('crm-mdl-room-category').textContent = guest.room_category;
    document.getElementById('crm-mdl-room-floor').textContent = guest.room_floor;
    document.getElementById('crm-mdl-room-bedding').textContent = guest.room_bedding;
    document.getElementById('crm-mdl-total-nights').textContent = guest.total_nights;

    // Card 1: Guest Profile
    document.getElementById('crm-mdl-card-name').textContent = guest.guest_name;
    document.getElementById('crm-mdl-card-phone').textContent = guest.mobile;
    document.getElementById('crm-mdl-card-email').textContent = guest.email;
    document.getElementById('crm-mdl-card-city').textContent = guest.city;
    document.getElementById('crm-mdl-card-address').textContent = guest.guest_address;
    document.getElementById('crm-mdl-card-nationality').textContent = guest.nationality;
    document.getElementById('crm-mdl-card-idproof').textContent = `${guest.id_card_type} • ${guest.id_card_number}`;
    document.getElementById('crm-mdl-card-regtype').textContent = guest.registration_type;

    const privRow = document.getElementById('crm-mdl-card-privilege-row');
    if (guest.has_privilege_card) {
      privRow.style.display = 'flex';
      document.getElementById('crm-mdl-card-privilege').textContent = guest.privilege_card_no;
    } else {
      privRow.style.display = 'none';
    }

    // Card 2: Stay & Corporate
    document.getElementById('crm-mdl-card-checkin').textContent = `${guest.reserve_date} (${guest.reserve_time})`;
    document.getElementById('crm-mdl-card-checkout').textContent = `${guest.checkout_date} (${guest.checkout_time})`;
    document.getElementById('crm-mdl-card-duration').textContent = `${guest.total_nights} Night(s)`;
    document.getElementById('crm-mdl-card-resmode').textContent = guest.reservation_mode;

    const corpSection = document.getElementById('crm-mdl-corporate-card-section');
    if (guest.company) {
      corpSection.style.display = 'block';
      document.getElementById('crm-mdl-company-name').textContent = guest.company.name;
      document.getElementById('crm-mdl-company-gstin').textContent = guest.company.gstin;
      const discVal = guest.company.discount_type === 'percentage' 
        ? `${guest.company.discount_value}%` 
        : `₹ ${Number(guest.company.discount_value).toLocaleString('en-IN')}`;
      document.getElementById('crm-mdl-company-discount').textContent = `${discVal} (${guest.company.discount_type})`;
    } else {
      corpSection.style.display = 'none';
    }

    // Card 3: Pricing & Billing Breakdown
    document.getElementById('crm-mdl-bill-rate').textContent = formatINR(guest.room_rate);
    document.getElementById('crm-mdl-bill-gross').textContent = formatINR(guest.total_amount);
    document.getElementById('crm-mdl-bill-advance').textContent = formatINR(guest.advance_amount);

    const balElem = document.getElementById('crm-mdl-bill-balance');
    balElem.textContent = formatINR(guest.balance);
    balElem.style.color = guest.balance > 0 ? '#e11d48' : '#059669';

    const balLabel = document.getElementById('crm-mdl-balance-label');
    if (balLabel) balLabel.textContent = guest.balance > 0 ? 'Balance Due' : 'Fully Settled';

    document.getElementById('crm-mdl-bill-calc-row').textContent = `${formatINR(guest.room_rate)} × ${guest.total_nights} Night(s) = ${formatINR(guest.room_rate * guest.total_nights)}`;
    
    let discText = 'None';
    if (guest.discount_amount > 0 || guest.discount_percentage > 0) {
      discText = `${formatINR(guest.discount_amount)} (${guest.discount_percentage}%)`;
    }
    document.getElementById('crm-mdl-bill-discount').textContent = discText;
    document.getElementById('crm-mdl-bill-payable').textContent = formatINR(guest.payable_amount);
    document.getElementById('crm-mdl-bill-paymode').textContent = guest.payment_mode;
    document.getElementById('crm-mdl-bill-remarks').textContent = guest.payment_remarks;

    // Show modal
    openModal('guest-crm-details-modal');
  }

  // Print Guest Dossier
  function printCrmGuestDossier() {
    if (!currentActiveCrmGuest) return;
    const g = currentActiveCrmGuest;

    const printArea = document.getElementById('print-area');
    if (!printArea) return;

    printArea.innerHTML = `
      <div style="font-family: Arial, sans-serif; padding: 30px; color: #1e293b; max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #4f46e5; padding-bottom: 16px; margin-bottom: 20px;">
          <div>
            <h1 style="margin: 0; font-size: 22px; color: #1e293b; font-weight: 800;">HOTEL SAGAR SONNET</h1>
            <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Luxury Living & Hospitality • Guest Master CRM Record</div>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 14px; font-weight: 800; color: #4f46e5;">DOSSIER REF: ${escapeHtml(g.reserve_id)}</div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Folio: ${escapeHtml(g.folio_number)}</div>
            <div style="font-size: 11px; color: #64748b;">Printed: ${new Date().toLocaleString()}</div>
          </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 20px;">
          <h2 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: #334155; text-transform: uppercase;">1. Guest Profile</h2>
          <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
            <tr>
              <td style="padding: 4px 0; width: 25%; font-weight: 600; color: #64748b;">Full Name:</td>
              <td style="padding: 4px 0; width: 25%; font-weight: 700;">${escapeHtml(g.guest_name)}</td>
              <td style="padding: 4px 0; width: 25%; font-weight: 600; color: #64748b;">Mobile:</td>
              <td style="padding: 4px 0; width: 25%; font-weight: 700;">${escapeHtml(g.mobile)}</td>
            </tr>
            <tr>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Email:</td>
              <td style="padding: 4px 0;">${escapeHtml(g.email)}</td>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">City / Nationality:</td>
              <td style="padding: 4px 0; font-weight: 700;">${escapeHtml(g.city)} / ${escapeHtml(g.nationality)}</td>
            </tr>
            <tr>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Address:</td>
              <td style="padding: 4px 0;" colspan="3">${escapeHtml(g.guest_address)}</td>
            </tr>
            <tr>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Identity Proof:</td>
              <td style="padding: 4px 0;">${escapeHtml(g.id_card_type)}: ${escapeHtml(g.id_card_number)}</td>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">VIP Card:</td>
              <td style="padding: 4px 0;">${g.has_privilege_card ? escapeHtml(g.privilege_card_no) : 'None'}</td>
            </tr>
          </table>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 20px;">
          <h2 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: #334155; text-transform: uppercase;">2. Accommodation & Stay Schedule</h2>
          <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
            <tr>
              <td style="padding: 4px 0; width: 25%; font-weight: 600; color: #64748b;">Allotted Room:</td>
              <td style="padding: 4px 0; width: 25%; font-weight: 700;">Room #${escapeHtml(g.room_number)} (Floor ${escapeHtml(g.room_floor)})</td>
              <td style="padding: 4px 0; width: 25%; font-weight: 600; color: #64748b;">Category & Bedding:</td>
              <td style="padding: 4px 0; width: 25%; font-weight: 700;">${escapeHtml(g.room_category)} (${escapeHtml(g.room_bedding)})</td>
            </tr>
            <tr>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Check-In Date/Time:</td>
              <td style="padding: 4px 0;">${escapeHtml(g.reserve_date)} at ${escapeHtml(g.reserve_time)}</td>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Check-Out Date/Time:</td>
              <td style="padding: 4px 0;">${escapeHtml(g.checkout_date)} at ${escapeHtml(g.checkout_time)}</td>
            </tr>
            <tr>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Duration of Stay:</td>
              <td style="padding: 4px 0;">${g.total_nights} Night(s)</td>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Reservation Mode:</td>
              <td style="padding: 4px 0;">${escapeHtml(g.reservation_mode)}</td>
            </tr>
            ${g.company ? `
            <tr>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Corporate Account:</td>
              <td style="padding: 4px 0;" colspan="3"><strong>${escapeHtml(g.company.name)}</strong> (GSTIN: ${escapeHtml(g.company.gstin)})</td>
            </tr>
            ` : ''}
          </table>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 24px;">
          <h2 style="margin: 0 0 10px 0; font-size: 14px; font-weight: 800; color: #334155; text-transform: uppercase;">3. Billing & Financial Summary</h2>
          <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid #e2e8f0;">
              <td style="padding: 6px 0; font-weight: 600; color: #64748b;">Nightly Room Tariff:</td>
              <td style="padding: 6px 0; text-align: right; font-weight: 700;">${formatINR(g.room_rate)}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e2e8f0;">
              <td style="padding: 6px 0; font-weight: 600; color: #64748b;">Total Gross (${g.total_nights} nights):</td>
              <td style="padding: 6px 0; text-align: right; font-weight: 700;">${formatINR(g.total_amount)}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e2e8f0;">
              <td style="padding: 6px 0; font-weight: 600; color: #64748b;">Corporate / Special Discount:</td>
              <td style="padding: 6px 0; text-align: right; color: #059669; font-weight: 700;">-${formatINR(g.discount_amount)}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e2e8f0;">
              <td style="padding: 6px 0; font-weight: 800; color: #1e293b;">Net Payable Amount:</td>
              <td style="padding: 6px 0; text-align: right; font-weight: 800; font-size: 14px;">${formatINR(g.payable_amount)}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e2e8f0;">
              <td style="padding: 6px 0; font-weight: 600; color: #059669;">Advance Deposit Received:</td>
              <td style="padding: 6px 0; text-align: right; font-weight: 700; color: #059669;">${formatINR(g.advance_amount)}</td>
            </tr>
            <tr>
              <td style="padding: 6px 0; font-weight: 800; color: ${g.balance > 0 ? '#e11d48' : '#059669'};">Outstanding Balance Due:</td>
              <td style="padding: 6px 0; text-align: right; font-weight: 800; font-size: 14px; color: ${g.balance > 0 ? '#e11d48' : '#059669'};">${formatINR(g.balance)}</td>
            </tr>
            <tr>
              <td style="padding: 4px 0; font-size: 11px; color: #64748b;" colspan="2">Payment Mode: ${escapeHtml(g.payment_mode)} • Remarks: ${escapeHtml(g.payment_remarks)}</td>
            </tr>
          </table>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 40px; font-size: 12px; color: #64748b;">
          <div style="border-top: 1px solid #cbd5e1; width: 200px; text-align: center; padding-top: 6px;">
            Guest Signature
          </div>
          <div style="border-top: 1px solid #cbd5e1; width: 200px; text-align: center; padding-top: 6px;">
            Duty Manager Signature
          </div>
        </div>
      </div>
    `;

    window.print();
  }

  // Initialize on document ready
  document.addEventListener('DOMContentLoaded', function() {
    renderCrmTable();
  });
</script>
@endpush
