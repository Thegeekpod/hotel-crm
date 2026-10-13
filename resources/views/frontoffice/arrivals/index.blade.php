@extends('frontoffice.layouts.app')

@section('title', 'Guest Arrivals & Reservations - Hotel Sagar Sonnet CRM / PMS')

@push('styles')
<style>
  /* Header Card */
  .arrivals-header-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.9));
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 16px;
    box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
  }

  /* Filter toolbar */
  .arrivals-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    flex-wrap: wrap;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
  }

  .search-box-wrap {
    position: relative;
    flex: 1;
    min-width: 260px;
    max-width: 440px;
  }

  .search-box-wrap input {
    width: 100%;
    height: 38px;
    padding: 6px 14px 6px 36px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    background: #f8fafc;
    font-size: 12px;
    color: #1e293b;
    outline: none;
    transition: all 0.2s;
  }

  .search-box-wrap input:focus {
    background: #ffffff;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }

  .search-box-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 13px;
  }

  .pms-filter-select {
    height: 38px;
    padding: 6px 32px 6px 12px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 10px center;
    appearance: none;
    -webkit-appearance: none;
    color: #1e293b;
    cursor: pointer;
    transition: all 0.2s;
  }

  .pms-filter-select:focus {
    background-color: #ffffff;
    border-color: #6366f1;
    outline: none;
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
    outline: none;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
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

  /* Action buttons */
  .action-flex-wrap {
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .status-quick-select {
    height: 30px;
    padding: 2px 24px 2px 8px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 8px center;
    appearance: none;
    -webkit-appearance: none;
    color: #334155;
    cursor: pointer;
    transition: all 0.2s;
  }

  .status-quick-select:hover {
    border-color: #6366f1;
    background-color: #f8fafc;
  }

  .status-quick-select:focus {
    border-color: #6366f1;
    outline: none;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
  }

  .btn-action-view {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid rgba(99, 102, 241, 0.25);
    background: rgba(99, 102, 241, 0.08);
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

  /* Guest Details Modal Uniform Cards */
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
    
    <!-- Clean Header (Pill ribbon removed as requested) -->
    <div class="arrivals-header-card">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
          <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <i class="fa-solid fa-plane-arrival" style="color: #6366f1;"></i> 
            Guest Arrivals & Advance Bookings
            @if(!empty($selectedDate))
              <span style="font-size: 11px; font-weight: 700; background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 2px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-regular fa-calendar-check"></i> {{ \Carbon\Carbon::parse($selectedDate)->format('D, d M Y') }}
              </span>
            @else
              <span style="font-size: 11px; font-weight: 700; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 2px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-forward-step"></i> Upcoming Future Dates
              </span>
            @endif
          </h2>
          <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
            @if(!empty($selectedDate))
              Showing guests arriving on <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}</strong>.
            @else
              Advance & upcoming guest arrivals (Check-in: Future dates after today) arranged latest first with indexing, search, status filter, and live reservation dossiers.
            @endif
          </div>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
          <button type="button" class="btn-ui-primary" onclick="if(typeof openReservationModal === 'function') openReservationModal(); else openModal('reserve-modal');">
            <i class="fa-solid fa-calendar-plus"></i> New Reservation
          </button>
          <a href="{{ route('frontoffice.dashboard') }}" class="btn-ui-secondary" style="border: 1px solid #cbd5e1; background: #ffffff; color: #334155; text-decoration: none; padding: 7px 16px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-table-cells"></i> View Room Rack
          </a>
        </div>
      </div>
    </div>

    <!-- Toolbar: Search Filter & Status Dropdown Filter -->
    <div class="arrivals-toolbar">
      <div style="display: flex; gap: 12px; align-items: center; flex: 1; flex-wrap: wrap;">
        <!-- Search Box -->
        <div class="search-box-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="guest-search-input" placeholder="Search by Guest Name, Phone, Room, Reserve ID, Folio..." oninput="onSearchOrFilterChange()">
        </div>

        <!-- Date Choose Filter -->
        <div style="display: inline-flex; align-items: center; gap: 6px;">
          <label for="arrival-date-filter" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
            <i class="fa-regular fa-calendar-days" style="color: #6366f1;"></i> Arrival Date:
          </label>
          <div style="display: inline-flex; align-items: center; gap: 6px;">
            <input type="date" 
                   id="arrival-date-filter" 
                   class="pms-filter-date" 
                   value="{{ $selectedDate ?? '' }}" 
                   onchange="onArrivalDateFilterChanged(this.value)"
                   title="Choose date to see guests arriving on that date">
            @if(!empty($selectedDate))
              <button type="button" 
                      class="btn-ui-secondary" 
                      onclick="clearArrivalDateFilter()" 
                      style="padding: 6px 10px; font-size: 11px; height: 38px; border-radius: 8px; border: 1px solid #c7d2fe; background: #eef2ff; color: #4338ca; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;" 
                      title="Clear date & view all upcoming arrivals">
                <i class="fa-solid fa-xmark"></i> Future Only
              </button>
            @endif
          </div>
        </div>

        <!-- Single Status Filter Dropdown -->
        <div style="display: inline-flex; align-items: center; gap: 6px;">
          <label for="status-filter-select" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
            <i class="fa-solid fa-filter" style="color: #6366f1;"></i> Status:
          </label>
          <select id="status-filter-select" class="pms-filter-select" onchange="onSearchOrFilterChange()">
            <option value="all">All Statuses</option>
            <option value="Confirmed">Confirmed (Active)</option>
            <option value="Arrived">Arrived</option>
            <option value="Checked In">Checked In</option>
            <option value="Stay Over">Stay Over</option>
            <option value="Checked Out">Checked Out</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Reset Button -->
        <button type="button" class="btn-ui-secondary" onclick="resetFilters()" style="padding: 7px 14px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 11px; font-weight: 700; color: #475569; display: inline-flex; align-items: center; gap: 5px;">
          <i class="fa-solid fa-rotate-left"></i> Reset
        </button>
      </div>

      <!-- Page Size Selector -->
      <div style="display: flex; gap: 12px; align-items: center;">
        <div class="pms-per-page-wrap">
          <span>Show</span>
          <select class="pms-per-page-select" id="per-page-select" onchange="changePageSize(this.value)">
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="all">All</option>
          </select>
          <span>entries</span>
        </div>
      </div>
    </div>

    <!-- Main Guests Arrivals Table with SL Indexing -->
    <div class="pms-page-content">
      <div class="pms-table-box" style="border-radius: 14px; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.06); background: #ffffff;">
        <table class="pms-table" id="arrivals-guest-table">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">SL</th>
              <th style="width: 130px;">Booking Ref</th>
              <th style="min-width: 170px;">Guest Details</th>
              <th style="min-width: 130px;">Allotted Room</th>
              <th style="min-width: 140px;">Stay Dates</th>
              <th style="min-width: 90px; text-align: right;">Total Gross</th>
              <th style="min-width: 90px; text-align: right;">Advance</th>
              <th style="min-width: 90px; text-align: right;">Balance</th>
              <th style="min-width: 110px; text-align: center;">Status</th>
              <th style="min-width: 160px; text-align: center;">Action</th>
            </tr>
          </thead>
          <tbody id="arrivals-guest-tbody">
            <!-- Rendered dynamically by renderTable() with SL Indexing & Pagination -->
          </tbody>
        </table>

        <!-- Table Pagination Footer -->
        <div class="pms-table-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; border-top: 1px solid #e2e8f0; background: #ffffff; flex-wrap: wrap; gap: 10px;">
          <div class="pms-pagination-info" id="pagination-info" style="font-size: 12px; font-weight: 600; color: #64748b;">
            Showing 0 to 0 of 0 records
          </div>
          <div class="pms-page-nav" id="pagination-nav"></div>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Bottom Summary Bar -->
<footer class="pms-bottom-bar">
  <div style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: flex; align-items: center; gap: 14px;">
    <span><i class="fa-solid fa-users" style="color: #6366f1;"></i> Total Guests: <strong id="footer-total">{{ $stats['total'] ?? count($guests) }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-circle-check" style="color: #4338ca;"></i> Confirmed: <strong id="footer-confirmed">{{ $stats['confirmed'] ?? 0 }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-bell-concierge" style="color: #059669;"></i> Arrived: <strong id="footer-arrived">{{ $stats['arrived'] ?? 0 }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-key" style="color: #0891b2;"></i> Checked In: <strong id="footer-checkedin">{{ $stats['checked_in'] ?? 0 }}</strong></span>
    <span>•</span>
    <span><i class="fa-solid fa-bed" style="color: #d97706;"></i> Stay Over: <strong id="footer-stayover">{{ $stats['stay_over'] ?? 0 }}</strong></span>
  </div>
</footer>

<!-- ========================================== -->
<!-- UNIFORM GUEST & RESERVATION DETAILS MODAL  -->
<!-- ========================================== -->
<div class="modal-backdrop" id="guest-details-modal">
  <div class="modal-window large" style="width: 900px; max-width: 95vw; max-height: 92vh; display: flex; flex-direction: column; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.3); border: none; box-sizing: border-box;">
    
    <!-- Modal Header -->
    <div class="modal-top" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: #ffffff; padding: 14px 22px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.15);">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center; font-size: 16px; color: #ffffff;">
          <i class="fa-solid fa-id-card-clip"></i>
        </div>
        <div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <h3 id="mdl-guest-name" style="margin: 0; font-size: 16px; font-weight: 800; color: #ffffff;">Guest Details</h3>
            <span id="mdl-status-badge" class="status-badge" style="background: #ffffff; color: #4338ca; font-weight: 800;">Confirmed</span>
          </div>
          <div style="font-size: 11px; opacity: 0.85; margin-top: 2px; font-family: var(--font-mono, monospace);">
            Reserve Ref: <span id="mdl-reserve-id" style="font-weight: 700;">-</span> &nbsp;|&nbsp; Folio: <span id="mdl-folio-number" style="font-weight: 700;">-</span>
          </div>
        </div>
      </div>
      <button type="button" class="modal-close" onclick="closeModal('guest-details-modal')" style="background: rgba(255,255,255,0.2); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">&times;</button>
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
              Room <span id="mdl-room-number">-</span> • <span id="mdl-room-category">-</span>
            </div>
          </div>
        </div>

        <div style="display: flex; gap: 8px; align-items: center;">
          <span style="font-size: 11px; font-weight: 800; background: #ffffff; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; color: #475569;">
            <i class="fa-solid fa-stairs" style="color: #6366f1;"></i> Floor <span id="mdl-room-floor">-</span>
          </span>
          <span style="font-size: 11px; font-weight: 800; background: #ffffff; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 6px; color: #475569;">
            <i class="fa-solid fa-bed" style="color: #6366f1;"></i> <span id="mdl-room-bedding">-</span>
          </span>
          <span style="font-size: 11px; font-weight: 800; background: #e0e7ff; border: 1px solid #c7d2fe; padding: 4px 10px; border-radius: 6px; color: #4338ca;">
            <i class="fa-solid fa-moon"></i> <span id="mdl-total-nights">1</span> Night(s) Stay
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
            <span class="dossier-val" id="mdl-card-name">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Mobile Number</span>
            <span class="dossier-val mono" id="mdl-card-phone">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Email Address</span>
            <span class="dossier-val" id="mdl-card-email">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">City / Location</span>
            <span class="dossier-val" id="mdl-card-city">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Full Address</span>
            <span class="dossier-val" id="mdl-card-address" style="max-width: 220px; white-space: normal;">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Nationality</span>
            <span class="dossier-val" id="mdl-card-nationality">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Identity Proof</span>
            <span class="dossier-val" id="mdl-card-idproof">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Guest / Reg Type</span>
            <span class="dossier-val" id="mdl-card-regtype">-</span>
          </div>

          <div class="dossier-field-row" id="mdl-card-privilege-row" style="display: none;">
            <span class="dossier-label">Privilege / VIP Card</span>
            <span class="dossier-val mono" id="mdl-card-privilege" style="color: #b45309;">-</span>
          </div>
        </div>

        <!-- CARD 2: Stay Schedule & Corporate Account -->
        <div class="dossier-card">
          <div class="dossier-card-title">
            <i class="fa-solid fa-calendar-days"></i> Stay & Corporate Details
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-In (Arrival)</span>
            <span class="dossier-val mono" id="mdl-card-checkin" style="color: #4f46e5;">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Check-Out (Departure)</span>
            <span class="dossier-val mono" id="mdl-card-checkout" style="color: #059669;">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Duration of Stay</span>
            <span class="dossier-val" id="mdl-card-duration">-</span>
          </div>

          <div class="dossier-field-row">
            <span class="dossier-label">Reservation Mode</span>
            <span class="dossier-val" id="mdl-card-resmode">-</span>
          </div>

          <!-- Corporate Account Block -->
          <div id="mdl-corporate-card-section" style="margin-top: 10px; padding: 10px; border-radius: 8px; background: rgba(99, 102, 241, 0.05); border: 1px solid rgba(99, 102, 241, 0.15); display: none;">
            <div style="font-size: 10px; font-weight: 800; color: #4338ca; text-transform: uppercase; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-building"></i> Corporate Account
            </div>
            <div class="dossier-field-row" style="padding: 3px 0;">
              <span class="dossier-label">Company Name</span>
              <span class="dossier-val" id="mdl-company-name" style="color: #4338ca;">-</span>
            </div>
            <div class="dossier-field-row" style="padding: 3px 0;">
              <span class="dossier-label">Company GSTIN</span>
              <span class="dossier-val mono" id="mdl-company-gstin">-</span>
            </div>
            <div class="dossier-field-row" style="padding: 3px 0;">
              <span class="dossier-label">Corporate Discount</span>
              <span class="dossier-val" id="mdl-company-discount" style="color: #059669;">-</span>
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
            <div class="bill-stat-value" id="mdl-bill-rate" style="color: #334155;">₹ 0.00</div>
          </div>

          <div class="bill-stat-item">
            <div class="bill-stat-label">Gross Amount</div>
            <div class="bill-stat-value" id="mdl-bill-gross" style="color: #1e293b;">₹ 0.00</div>
          </div>

          <div class="bill-stat-item">
            <div class="bill-stat-label">Advance Paid</div>
            <div class="bill-stat-value" id="mdl-bill-advance" style="color: #059669;">₹ 0.00</div>
          </div>

          <div class="bill-stat-item" id="mdl-balance-box">
            <div class="bill-stat-label" id="mdl-balance-label">Balance Due</div>
            <div class="bill-stat-value" id="mdl-bill-balance" style="color: #e11d48;">₹ 0.00</div>
          </div>
        </div>

        <div style="margin-top: 14px; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 8px; padding: 10px 14px;">
          <div class="dossier-field-row">
            <span class="dossier-label">Room Rate × Nights</span>
            <span class="dossier-val mono" id="mdl-bill-calc-row">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Discount Applied</span>
            <span class="dossier-val mono" id="mdl-bill-discount" style="color: #059669;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Net Payable Amount</span>
            <span class="dossier-val mono" id="mdl-bill-payable" style="font-size: 13px; font-weight: 900; color: #0f172a;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Payment Mode</span>
            <span class="dossier-val" id="mdl-bill-paymode">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">Payment Remarks / Reference</span>
            <span class="dossier-val" id="mdl-bill-remarks" style="color: #64748b;">-</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Footer Actions -->
    <div class="modal-bot" style="padding: 12px 22px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
      
      <!-- Quick Status Changer from inside Modal -->
      <div style="display: flex; align-items: center; gap: 8px;">
        <span style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase;">Update Status:</span>
        <select class="status-quick-select" id="mdl-quick-status-select" onchange="onModalStatusChange(this.value)">
          <option value="Confirmed">Confirmed (Active)</option>
          <option value="Arrived">Arrived</option>
          <option value="Checked In">Checked In</option>
          <option value="Stay Over">Stay Over</option>
          <option value="Checked Out">Checked Out</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>

      <div style="display: flex; gap: 8px;">
        <button type="button" class="btn-ui-secondary" onclick="printGuestDossier()" style="padding: 7px 16px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; color: #334155; font-size: 12px; font-weight: 700;">
          <i class="fa-solid fa-print"></i> Print Dossier
        </button>
        <button type="button" class="btn-ui-primary" onclick="closeModal('guest-details-modal')" style="padding: 7px 20px; border-radius: 8px; font-size: 12px; font-weight: 700;">
          Close
        </button>
      </div>

    </div>

  </div>
</div>

@push('scripts')
<script>
  // All guests array loaded from database
  window.allArrivalGuests = @json($serializedGuests);
  let currentActiveGuest = null;
  let currentPage = 1;
  let pageSize = 10;

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

  // Filter records based on search and status dropdown
  function getFilteredRecords() {
    const query = (document.getElementById('guest-search-input')?.value || '').toLowerCase().trim();
    const selectedStatus = document.getElementById('status-filter-select')?.value || 'all';

    return (window.allArrivalGuests || []).filter(item => {
      // Status dropdown filter
      const matchesStatus = (selectedStatus === 'all') || (item.status && item.status.toLowerCase() === selectedStatus.toLowerCase());
      if (!matchesStatus) return false;

      // Search keyword filter
      if (!query) return true;
      const searchBlob = `${item.guest_name} ${item.mobile} ${item.email} ${item.room_number} ${item.reserve_id} ${item.folio_number} ${item.room_category} ${item.status}`.toLowerCase();
      return searchBlob.includes(query);
    });
  }

  // Render Table with SL Indexing and Pagination
  function renderTable() {
    const filtered = getFilteredRecords();
    const totalRecords = filtered.length;
    let totalPages = 1;
    let paginatedRecords = filtered;

    if (pageSize !== 'all') {
      const limit = parseInt(pageSize, 10);
      totalPages = Math.ceil(totalRecords / limit) || 1;
      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;
      const startIdx = (currentPage - 1) * limit;
      paginatedRecords = filtered.slice(startIdx, startIdx + limit);
    }

    const tbody = document.getElementById('arrivals-guest-tbody');
    if (!tbody) return;

    if (paginatedRecords.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 36px 20px;">
            <div style="font-size: 28px; color: #cbd5e1; margin-bottom: 8px;"><i class="fa-solid fa-users-slash"></i></div>
            <div style="font-size: 13px; font-weight: 700; color: #64748b;">No matching guests found.</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Try modifying your search or status filter.</div>
          </td>
        </tr>
      `;
      renderPagination(totalRecords, totalPages, 0, 0);
      return;
    }

    const startIndex = pageSize === 'all' ? 0 : (currentPage - 1) * parseInt(pageSize, 10);

    tbody.innerHTML = paginatedRecords.map((item, idx) => {
      const sl = startIndex + idx + 1;
      const formatINR = val => '₹ ' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      const badgeColor = getStatusBadgeColor(item.status);

      return `
        <tr class="guest-row" id="row-guest-${item.id}">
          <!-- SL Indexing -->
          <td style="text-align: center; font-family: var(--font-mono, monospace); font-weight: 800; color: #64748b; font-size: 11px;">
            ${sl}
          </td>

          <!-- Booking Ref & Folio -->
          <td>
            <div style="font-family: var(--font-mono, monospace); font-weight: 800; color: #4f46e5; font-size: 12px;">
              ${escapeHtml(item.reserve_id)}
            </div>
            <div style="font-size: 10px; color: #64748b; font-family: var(--font-mono, monospace); margin-top: 2px;">
              <i class="fa-regular fa-file-lines"></i> ${escapeHtml(item.folio_number)}
            </div>
          </td>

          <!-- Guest Details -->
          <td>
            <div style="font-weight: 800; color: #0f172a; font-size: 13px;">
              ${escapeHtml(item.guest_name)}
            </div>
            <div style="font-size: 11px; color: #64748b; display: flex; align-items: center; gap: 8px; margin-top: 2px;">
              ${item.mobile && item.mobile !== 'N/A' ? `<span><i class="fa-solid fa-phone" style="font-size: 9px; color: #10b981;"></i> ${escapeHtml(item.mobile)}</span>` : ''}
              ${item.registration_type ? `<span style="font-size: 9px; padding: 1px 6px; border-radius: 4px; background: #e2e8f0; color: #475569; font-weight: 700;">${escapeHtml(item.registration_type)}</span>` : ''}
            </div>
          </td>

          <!-- Room & Category -->
          <td>
            <div style="display: flex; align-items: center; gap: 6px;">
              <span style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 2px 8px; border-radius: 6px; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 11px;">
                Room ${escapeHtml(item.room_number)}
              </span>
            </div>
            <div style="font-size: 10px; font-weight: 700; color: #64748b; margin-top: 3px; text-transform: uppercase;">
              ${escapeHtml(item.room_category)} • Fl. ${escapeHtml(item.room_floor)}
            </div>
          </td>

          <!-- Stay Dates -->
          <td>
            <div style="font-size: 11px; font-weight: 700; color: #1e293b;">
              ${escapeHtml(item.reserve_date)}
            </div>
            <div style="font-size: 10px; color: #64748b; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
              <span>to ${escapeHtml(item.checkout_date)}</span>
              <span style="background: #f1f5f9; padding: 1px 5px; border-radius: 4px; font-weight: 800; font-size: 9px;">
                ${item.total_nights || 1}N
              </span>
            </div>
          </td>

          <!-- Total Gross -->
          <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 700; font-size: 12px; color: #334155;">
            ${formatINR(item.total_amount)}
          </td>

          <!-- Advance Paid -->
          <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 700; font-size: 12px; color: #059669;">
            ${formatINR(item.advance_amount)}
          </td>

          <!-- Balance Pending -->
          <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 800; font-size: 12px; color: ${item.balance > 0 ? '#e11d48' : '#059669'};">
            ${formatINR(item.balance)}
          </td>

          <!-- Status Badge -->
          <td style="text-align: center;">
            <span id="badge-status-${item.id}" class="status-badge ${badgeColor}">
              <i class="fa-solid fa-circle" style="font-size: 6px;"></i>
              <span class="status-text">${escapeHtml(item.status)}</span>
            </span>
          </td>

          <!-- Actions: Quick Status Selector + Eye View Button -->
          <td style="text-align: center;">
            <div class="action-flex-wrap">
              <select class="status-quick-select" 
                      id="status-select-${item.id}" 
                      onchange="updateGuestStatus(${item.id}, this.value, this)" 
                      title="Change Guest Status">
                <option value="Confirmed" ${item.status === 'Confirmed' ? 'selected' : ''}>Confirmed (Active)</option>
                <option value="Arrived" ${item.status === 'Arrived' ? 'selected' : ''}>Arrived</option>
                <option value="Checked In" ${item.status === 'Checked In' ? 'selected' : ''}>Checked In</option>
                <option value="Stay Over" ${item.status === 'Stay Over' ? 'selected' : ''}>Stay Over</option>
                <option value="Checked Out" ${item.status === 'Checked Out' ? 'selected' : ''}>Checked Out</option>
                <option value="Cancelled" ${item.status === 'Cancelled' ? 'selected' : ''}>Cancelled</option>
              </select>

              <button type="button" 
                      class="btn-action-view" 
                      onclick="openGuestDetailsModalById(${item.id})" 
                      title="View Complete Guest & Billing Dossier">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');

    const fromItem = totalRecords === 0 ? 0 : startIndex + 1;
    const toItem = pageSize === 'all' ? totalRecords : Math.min(startIndex + parseInt(pageSize, 10), totalRecords);
    renderPagination(totalRecords, totalPages, fromItem, toItem);
  }

  // Render Pagination Navigation Controls
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
      <button type="button" class="pms-page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="goToPage(${currentPage - 1})" title="Previous Page">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
    `;

    for (let p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || (p >= currentPage - 1 && p <= currentPage + 1)) {
        buttons += `
          <button type="button" class="pms-page-btn ${p === currentPage ? 'active' : ''}" onclick="goToPage(${p})">
            ${p}
          </button>
        `;
      } else if (p === currentPage - 2 || p === currentPage + 2) {
        buttons += `<span class="pms-page-btn" style="border:none; cursor:default; background:transparent;">...</span>`;
      }
    }

    buttons += `
      <button type="button" class="pms-page-btn" ${currentPage === pages ? 'disabled' : ''} onclick="goToPage(${currentPage + 1})" title="Next Page">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    `;

    nav.innerHTML = buttons;
  }

  // Handle page size change
  function changePageSize(val) {
    pageSize = val === 'all' ? 'all' : parseInt(val, 10);
    currentPage = 1;
    renderTable();
  }

  // Handle page jump
  function goToPage(p) {
    currentPage = p;
    renderTable();
  }

  // Triggered on search input or status dropdown change
  function onSearchOrFilterChange() {
    currentPage = 1;
    renderTable();
  }

  // Date Filter Change Handler
  function onArrivalDateFilterChanged(dateVal) {
    const url = new URL(window.location.href);
    if (dateVal) {
      url.searchParams.set('date', dateVal);
    } else {
      url.searchParams.delete('date');
    }
    window.location.href = url.toString();
  }

  // Clear date filter to return to upcoming future dates
  function clearArrivalDateFilter() {
    const url = new URL(window.location.href);
    url.searchParams.delete('date');
    window.location.href = url.toString();
  }

  // Reset all filters (search, status, and arrival date)
  function resetFilters() {
    const searchInp = document.getElementById('guest-search-input');
    const statusSelect = document.getElementById('status-filter-select');
    if (searchInp) searchInp.value = '';
    if (statusSelect) statusSelect.value = 'all';

    const url = new URL(window.location.href);
    if (url.searchParams.has('date')) {
      url.searchParams.delete('date');
      window.location.href = url.toString();
      return;
    }

    currentPage = 1;
    renderTable();
  }

  // Update guest status via AJAX
  function updateGuestStatus(guestId, newStatus, selectElement) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (selectElement) selectElement.disabled = true;

    fetch(`{{ url('front-office/arrivals') }}/${guestId}/status`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
      if (selectElement) selectElement.disabled = false;
      if (data.success) {
        // Update item in local state
        const item = (window.allArrivalGuests || []).find(g => String(g.id) === String(guestId));
        if (item) item.status = data.status;

        // Re-render table row
        const badge = document.getElementById(`badge-status-${guestId}`);
        if (badge) {
          badge.className = `status-badge ${getStatusBadgeColor(data.status)}`;
          const textSpan = badge.querySelector('.status-text');
          if (textSpan) textSpan.textContent = data.status;
        }

        // Update modal if currently opened with this guest
        if (currentActiveGuest && String(currentActiveGuest.id) === String(guestId)) {
          currentActiveGuest.status = data.status;
          const mdlBadge = document.getElementById('mdl-status-badge');
          if (mdlBadge) {
            mdlBadge.textContent = data.status;
            mdlBadge.className = `status-badge ${getStatusBadgeColor(data.status)}`;
          }
          const mdlSelect = document.getElementById('mdl-quick-status-select');
          if (mdlSelect) mdlSelect.value = data.status;
        }

        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        } else {
          alert(data.message);
        }
      } else {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message || 'Error updating status', 'error');
        } else {
          alert('Error updating status');
        }
      }
    })
    .catch(err => {
      if (selectElement) selectElement.disabled = false;
      console.error('Error updating status:', err);
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('Failed to update status. Please try again.', 'error');
      } else {
        alert('Failed to update status.');
      }
    });
  }

  // Helper to open modal by ID from table row click
  function openGuestDetailsModalById(id) {
    const guest = (window.allArrivalGuests || []).find(g => String(g.id) === String(id));
    if (guest) {
      openGuestDetailsModal(guest);
    }
  }

  // When status changed inside the Eye Details Modal
  function onModalStatusChange(newStatus) {
    if (!currentActiveGuest) return;
    const guestId = currentActiveGuest.id;
    const rowSelect = document.getElementById(`status-select-${guestId}`);
    if (rowSelect) rowSelect.value = newStatus;
    updateGuestStatus(guestId, newStatus, rowSelect);
  }

  // Open Uniform Guest Details Modal
  function openGuestDetailsModal(g) {
    if (!g) return;
    currentActiveGuest = g;

    // Header info
    document.getElementById('mdl-guest-name').textContent = g.guest_name || 'Guest Details';
    document.getElementById('mdl-reserve-id').textContent = g.reserve_id || '-';
    document.getElementById('mdl-folio-number').textContent = g.folio_number || '-';

    const statusBadge = document.getElementById('mdl-status-badge');
    statusBadge.textContent = g.status || 'Confirmed';
    statusBadge.className = `status-badge ${getStatusBadgeColor(g.status)}`;

    // Top Highlight Info
    document.getElementById('mdl-room-number').textContent = g.room_number || 'Pending';
    document.getElementById('mdl-room-category').textContent = g.room_category || 'Standard';
    document.getElementById('mdl-room-floor').textContent = g.room_floor || '1';
    document.getElementById('mdl-room-bedding').textContent = g.room_bedding || 'Standard';
    document.getElementById('mdl-total-nights').textContent = g.total_nights || 1;

    // Profile Card
    document.getElementById('mdl-card-name').textContent = g.guest_name || '-';
    document.getElementById('mdl-card-phone').textContent = g.mobile || 'N/A';
    document.getElementById('mdl-card-email').textContent = g.email || 'N/A';
    document.getElementById('mdl-card-city').textContent = g.city || 'N/A';
    document.getElementById('mdl-card-address').textContent = g.guest_address || 'N/A';
    document.getElementById('mdl-card-nationality').textContent = g.nationality || 'Indian';
    document.getElementById('mdl-card-idproof').textContent = (g.id_card_type && g.id_card_type !== 'N/A') ? `${g.id_card_type}: ${g.id_card_number}` : (g.id_card_number || 'N/A');
    document.getElementById('mdl-card-regtype').textContent = g.registration_type || 'Regular';

    const privRow = document.getElementById('mdl-card-privilege-row');
    if (g.has_privilege_card && g.privilege_card_no && g.privilege_card_no !== 'N/A') {
      privRow.style.display = 'flex';
      document.getElementById('mdl-card-privilege').textContent = g.privilege_card_no;
    } else {
      privRow.style.display = 'none';
    }

    // Stay Card
    document.getElementById('mdl-card-checkin').textContent = `${g.reserve_date || 'N/A'} (${g.reserve_time || ''})`;
    document.getElementById('mdl-card-checkout').textContent = `${g.checkout_date || 'N/A'} (${g.checkout_time || ''})`;
    document.getElementById('mdl-card-duration').textContent = `${g.total_nights || 1} Night(s) Stay`;
    document.getElementById('mdl-card-resmode').textContent = g.reservation_mode || 'Front Desk';

    // Corporate Card
    const corpSection = document.getElementById('mdl-corporate-card-section');
    if (g.company && g.company.name) {
      corpSection.style.display = 'block';
      document.getElementById('mdl-company-name').textContent = g.company.name;
      document.getElementById('mdl-company-gstin').textContent = g.company.gstin || 'N/A';
      const discText = g.company.discount_type === 'amount' 
        ? `₹ ${Number(g.company.discount_value || 0).toLocaleString('en-IN')}` 
        : `${g.company.discount_value || 0}% Corporate Discount`;
      document.getElementById('mdl-company-discount').textContent = discText;
    } else {
      corpSection.style.display = 'none';
    }

    // Billing Breakdown Card
    const formatINR = val => '₹ ' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('mdl-bill-rate').textContent = formatINR(g.room_rate);
    document.getElementById('mdl-bill-gross').textContent = formatINR(g.total_amount);
    document.getElementById('mdl-bill-advance').textContent = formatINR(g.advance_amount);
    
    const balanceElem = document.getElementById('mdl-bill-balance');
    const balanceLabel = document.getElementById('mdl-balance-label');
    balanceElem.textContent = formatINR(g.balance);
    if (Number(g.balance || 0) > 0) {
      balanceElem.style.color = '#e11d48';
      balanceLabel.textContent = 'Balance Due';
    } else {
      balanceElem.style.color = '#059669';
      balanceLabel.textContent = 'Settled (Nil)';
    }

    document.getElementById('mdl-bill-calc-row').textContent = `${formatINR(g.room_rate)} × ${g.total_nights || 1} Night(s) = ${formatINR(g.total_amount)}`;
    
    let discountStr = 'None (₹ 0.00)';
    if (Number(g.discount_amount || 0) > 0) {
      discountStr = `- ${formatINR(g.discount_amount)} (${g.discount_percentage || 0}%)`;
    }
    document.getElementById('mdl-bill-discount').textContent = discountStr;
    document.getElementById('mdl-bill-payable').textContent = formatINR(g.payable_amount);
    document.getElementById('mdl-bill-paymode').textContent = g.payment_mode || 'Cash / Direct';
    document.getElementById('mdl-bill-remarks').textContent = g.payment_remarks || 'None';

    // Set quick status changer in modal footer
    const modalSelect = document.getElementById('mdl-quick-status-select');
    if (modalSelect) modalSelect.value = g.status || 'Confirmed';

    // Show modal
    openModal('guest-details-modal');
  }

  // Backdrop and Escape listeners
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('guest-details-modal')?.addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal('guest-details-modal');
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeModal('guest-details-modal');
      }
    });

    // Render table on initial load
    renderTable();
  });

  // Print Guest Dossier via dedicated print-area
  function printGuestDossier() {
    if (!currentActiveGuest) return;
    const g = currentActiveGuest;
    const formatINR = val => '₹ ' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const printArea = document.getElementById('print-area');
    if (!printArea) return;

    printArea.innerHTML = `
      <div style="padding: 30px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1e293b; background: #ffffff;">
        <div style="text-align: center; border-bottom: 2px solid #334155; padding-bottom: 12px; margin-bottom: 20px;">
          <div style="font-size: 22px; font-weight: 800; color: #4338ca;">HOTEL SAGAR SONNET</div>
          <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Front Desk Property Management System • Guest Dossier</div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 16px; background: #f8fafc; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 12px; font-weight: 700; border-radius: 6px;">
          <span>Reserve ID: ${g.reserve_id}</span>
          <span>Folio No: ${g.folio_number}</span>
          <span>Status: ${g.status}</span>
        </div>
        <div style="margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px;">
          <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; color: #4338ca; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px;">Guest Profile</div>
          <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr><td style="width: 35%; color: #64748b; font-weight: 600; padding: 4px 6px;">Guest Name:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.guest_name}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Phone / Mobile:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.mobile}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Email:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.email}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Address:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.guest_address}, ${g.city}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">ID Proof:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.id_card_type} - ${g.id_card_number}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Nationality:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.nationality}</td></tr>
          </table>
        </div>
        <div style="margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px;">
          <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; color: #4338ca; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px;">Room & Stay Information</div>
          <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr><td style="width: 35%; color: #64748b; font-weight: 600; padding: 4px 6px;">Allotted Room:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">Room ${g.room_number} (${g.room_category}) - Floor ${g.room_floor}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Check-In:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.reserve_date} ${g.reserve_time}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Check-Out:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.checkout_date} ${g.checkout_time}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Duration:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.total_nights} Night(s)</td></tr>
            ${g.company ? `<tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Company:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.company.name} (GSTIN: ${g.company.gstin})</td></tr>` : ''}
          </table>
        </div>
        <div style="margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px;">
          <div style="font-size: 12px; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; color: #4338ca; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px;">Billing & Pricing Breakdown</div>
          <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <tr><td style="width: 35%; color: #64748b; font-weight: 600; padding: 4px 6px;">Room Rate:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${formatINR(g.room_rate)} / night</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Gross Total:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${formatINR(g.total_amount)}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Discount:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${formatINR(g.discount_amount)} (${g.discount_percentage}%)</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Payable Amount:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${formatINR(g.payable_amount)}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Advance Paid:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${formatINR(g.advance_amount)}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Balance Remaining:</td><td style="font-weight: 700; color: #e11d48; padding: 4px 6px;">${formatINR(g.balance)}</td></tr>
            <tr><td style="color: #64748b; font-weight: 600; padding: 4px 6px;">Payment Mode:</td><td style="font-weight: 700; color: #0f172a; padding: 4px 6px;">${g.payment_mode}</td></tr>
          </table>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 40px; font-size: 11px; font-weight: 700;">
          <div>Guest Signature: __________________</div>
          <div>Front Desk Staff: __________________</div>
        </div>
      </div>
    `;
    window.print();
  }
</script>
@endpush
@endsection
