@extends('frontoffice.layouts.app')

@section('title', 'Guest CRM & Master Directory - Hotel Sagar Sonnet CRM / PMS')

@push('styles')
<style>
  /* Prevent any awkward text wrapping in table */
  .pms-table th,
  .pms-table td {
    white-space: nowrap !important;
  }

  .guest-id-text {
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    color: #4f46e5;
    font-size: 12px;
    white-space: nowrap;
    letter-spacing: 0.3px;
  }

  .guest-folio-text {
    font-size: 10px;
    color: #64748b;
    font-family: var(--font-mono, monospace);
    margin-top: 2px;
    white-space: nowrap;
  }

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
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }

  .search-box-wrap {
    position: relative;
    flex: 1;
    min-width: 260px;
    max-width: 440px;
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

  /* Action buttons */
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
  // Map page guests for instant view modal opening
  $serializedGuests = collect($guests->items())->map(function($g) {
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
      'gst_type' => $g->gst_type ?: 'exclusive',
      'gst_percentage' => (float)$g->gst_percentage,
      'gst_amount' => (float)$g->gst_amount,
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
          <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <i class="fa-solid fa-address-card" style="color: #6366f1;"></i> 
            Guest Master & CRM Directory
            <span style="font-size: 11px; font-weight: 700; background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 2px 10px; border-radius: 20px;">
              {{ $guests->total() }} Records
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
              Showing guest records with check-in on <strong>{{ \Carbon\Carbon::parse($fromDate)->format('D, d F Y') }}</strong> ({{ $fromDate === $today ? 'Today' : 'Selected Date' }}).
            @elseif(!empty($fromDate) || !empty($toDate))
              Showing guest records with check-in between <strong>{{ !empty($fromDate) ? \Carbon\Carbon::parse($fromDate)->format('d M Y') : 'Start' }}</strong> and <strong>{{ !empty($toDate) ? \Carbon\Carbon::parse($toDate)->format('d M Y') : 'End' }}</strong>.
            @else
              Comprehensive guest directory, stay history dossiers, allotted rooms, billing statements, and identity records.
            @endif
          </div>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
          <a href="{{ route('frontoffice.dashboard') }}" class="btn-ui-secondary" style="border: 1px solid #cbd5e1; background: #ffffff; color: #334155; text-decoration: none; padding: 7px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-table-cells"></i> Room Rack
          </a>
        </div>
      </div>
    </div>

    <!-- Backend Filter Form & Toolbar -->
    <form method="GET" action="{{ route('frontoffice.guest') }}" id="guest-filter-form" class="guest-toolbar">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; width: 100%;">
        <div style="display: flex; gap: 12px; align-items: center; flex: 1; flex-wrap: wrap;">
          <!-- Search Box -->
          <div class="search-box-wrap">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" 
                   name="search" 
                   id="guest-crm-search-input" 
                   value="{{ request('search') }}" 
                   placeholder="Search by Name, Phone, Email, ID, Folio, City..." 
                   onkeydown="if(event.key==='Enter'){this.form.submit();}">
            @if(request()->filled('search'))
              <a href="{{ route('frontoffice.guest', request()->except('search', 'page')) }}" 
                 title="Clear search" 
                 style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; text-decoration: none; font-size: 12px;">
                 <i class="fa-solid fa-xmark"></i>
              </a>
            @endif
          </div>

          <!-- Check-in From & To Date Filter (Default: Today) -->
          <div style="display: inline-flex; align-items: center; gap: 6px;">
            <label for="crm-from-date" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
              <i class="fa-regular fa-calendar-days" style="color: #6366f1;"></i> Check-in From:
            </label>
            <input type="date" 
                   name="from_date" 
                   id="crm-from-date" 
                   class="pms-filter-date" 
                   value="{{ $fromDate ?? '' }}" 
                   onchange="this.form.submit()" 
                   title="Filter by Check-in Date From">

            <label for="crm-to-date" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
              To:
            </label>
            <input type="date" 
                   name="to_date" 
                   id="crm-to-date" 
                   class="pms-filter-date" 
                   value="{{ $toDate ?? '' }}" 
                   onchange="this.form.submit()" 
                   title="Filter by Check-in Date To">
          </div>

          <!-- Status Filter Dropdown -->
          <div style="display: inline-flex; align-items: center; gap: 6px;">
            <label for="crm-status-filter" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
              <i class="fa-solid fa-filter" style="color: #6366f1;"></i> Status:
            </label>
            <select name="status" id="crm-status-filter" class="pms-filter-select" onchange="this.form.submit()">
              <option value="all" {{ request('status') === 'all' || !request()->filled('status') ? 'selected' : '' }}>All Statuses</option>
              <option value="Confirmed" {{ request('status') === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
              <option value="Checked In" {{ request('status') === 'Checked In' ? 'selected' : '' }}>Checked In</option>
              <option value="Arrived" {{ request('status') === 'Arrived' ? 'selected' : '' }}>Arrived</option>
              <option value="Stay Over" {{ request('status') === 'Stay Over' ? 'selected' : '' }}>Stay Over</option>
              <option value="Checked Out" {{ request('status') === 'Checked Out' ? 'selected' : '' }}>Checked Out</option>
              <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>

          <!-- Registration Type Filter -->
          <div style="display: inline-flex; align-items: center; gap: 6px;">
            <label for="crm-regtype-filter" style="font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
              <i class="fa-solid fa-id-badge" style="color: #6366f1;"></i> Reg Type:
            </label>
            <select name="registration_type" id="crm-regtype-filter" class="pms-filter-select" onchange="this.form.submit()">
              <option value="all" {{ request('registration_type') === 'all' || !request()->filled('registration_type') ? 'selected' : '' }}>All Reg Types</option>
              @foreach($registrationTypes ?? [] as $rt)
                <option value="{{ $rt->name }}" {{ request('registration_type') === $rt->name ? 'selected' : '' }}>{{ $rt->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- All Dates Link (if date filter active) -->
          @if(!empty($fromDate) || !empty($toDate))
            <a href="{{ route('frontoffice.guest', array_merge(request()->except(['from_date', 'to_date', 'page']), ['all_dates' => 1])) }}" 
               class="btn-ui-secondary" 
               style="padding: 7px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; font-size: 11px; font-weight: 700; color: #475569; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;"
               title="View all records without date restrictions">
              <i class="fa-regular fa-calendar"></i> All Dates
            </a>
          @endif

          <!-- Reset Button (defaults back to today) -->
          <a href="{{ route('frontoffice.guest') }}" 
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
            <select name="per_page" class="pms-per-page-select" id="crm-per-page-select" onchange="this.form.submit()">
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

    <!-- Main Guests CRM Table with SL Indexing -->
    <div class="pms-page-content">
      <div class="pms-table-box" style="border-radius: 14px; box-shadow: 0 4px 20px -5px rgba(0,0,0,0.06); background: #ffffff; overflow-x: auto;">
        <table class="pms-table" id="guest-crm-table">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center; white-space: nowrap;">SL</th>
              <th style="min-width: 130px; text-align: left; white-space: nowrap;">Registration Type</th>
              <th style="min-width: 160px; white-space: nowrap;">Name</th>
              <th style="min-width: 130px; white-space: nowrap;">Phone</th>
              <th style="min-width: 130px; white-space: nowrap;">Room &amp; Floor</th>
              <th style="min-width: 140px; white-space: nowrap;">Check-in</th>
              <th style="min-width: 140px; white-space: nowrap;">Check-out</th>
              <th style="min-width: 110px; text-align: right; white-space: nowrap;">Paid</th>
              <th style="min-width: 130px; text-align: right; white-space: nowrap;">Remaining</th>
              <th style="min-width: 100px; text-align: center; white-space: nowrap;">Status</th>
              <th style="width: 70px; text-align: center; white-space: nowrap;">Action</th>
            </tr>
          </thead>
          <tbody id="guest-crm-tbody">
            @forelse($guests as $index => $item)
              @php
                $sl = ($guests->currentPage() - 1) * $guests->perPage() + $loop->iteration;
                $statusNormalized = strtolower($item->status ?? 'confirmed');
                $statusClass = match($statusNormalized) {
                  'confirmed' => 'confirmed',
                  'arrived' => 'arrived',
                  'checked in', 'checked-in', 'checkedin' => 'checkedin',
                  'stay over', 'stayover' => 'stayover',
                  'checked out', 'checked-out', 'checkedout' => 'checkedout',
                  'cancelled' => 'cancelled',
                  default => 'confirmed'
                };

                // Room & Floor (Start from R then number)
                $rawRoom = $item->room?->room_number ?? null;
                $cleanRoomNum = $rawRoom ? preg_replace('/^r[-_\s]?/i', '', trim($rawRoom)) : null;
                $rRoomDisplay = $cleanRoomNum ? ('R' . $cleanRoomNum) : null;
                $floorDisplay = $item->room?->floorRelation?->floor ?? ($item->room?->floor ?? null);

                // Financials (Paid & Remaining)
                $paidVal = (float)($item->latestBooking?->payments?->sum('amount') ?: $item->advance_amount);
                $balanceVal = (float)($item->balance ?? 0);
              @endphp
              <tr class="guest-row" id="row-guest-{{ $item->id }}">
                <!-- SL Indexing -->
                <td style="text-align: center; font-family: var(--font-mono, monospace); font-weight: 800; color: #64748b; font-size: 11px; white-space: nowrap;">
                  {{ $sl }}
                </td>

                <!-- 1. Registration Type -->
                <td style="text-align: left; white-space: nowrap;">
                  <span style="font-size: 10px; padding: 3px 9px; border-radius: 6px; background: #f1f5f9; color: #334155; font-weight: 700; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                    <i class="fa-solid fa-id-badge" style="font-size: 9px; color: #6366f1;"></i>
                    {{ $item->registrationType?->name ?? 'Regular' }}
                  </span>
                </td>

                <!-- 2. Name -->
                <td style="white-space: nowrap;">
                  <div style="font-weight: 800; color: #0f172a; font-size: 13px; white-space: nowrap;">
                    {{ ($item->title?->name ? $item->title->name . ' ' : '') . $item->guest_name }}
                  </div>
                  <div style="font-size: 10px; color: #64748b; margin-top: 2px; display: flex; align-items: center; gap: 6px; font-family: var(--font-mono, monospace);">
                    <span style="color: #6366f1; font-weight: 700;">{{ $item->reserve_id ?: ('RES-' . $item->id) }}</span>
                    @if($item->city)
                      <span style="color: #cbd5e1;">•</span>
                      <span style="color: #94a3b8;"><i class="fa-solid fa-location-dot" style="font-size: 9px;"></i> {{ $item->city }}</span>
                    @endif
                  </div>
                </td>

                <!-- 3. Phone -->
                <td style="white-space: nowrap;">
                  <div style="font-family: var(--font-mono, monospace); font-weight: 700; font-size: 12px; color: #1e293b; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                    <i class="fa-solid fa-phone" style="font-size: 10px; color: #10b981;"></i>
                    <span>{{ $item->mobile ?: 'N/A' }}</span>
                  </div>
                </td>

                <!-- 4. Room & Floor (Start from R then number) -->
                <td style="white-space: nowrap;">
                  @if($rRoomDisplay)
                    <div style="display: inline-flex; align-items: center; gap: 6px;">
                      <span style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; padding: 2px 8px; border-radius: 6px; font-weight: 800; font-size: 12px; font-family: var(--font-mono, monospace); letter-spacing: 0.3px;">
                        <i class="fa-solid fa-door-open" style="font-size: 10px; margin-right: 4px;"></i>{{ $rRoomDisplay }}
                      </span>
                    </div>
                    <div style="font-size: 10px; color: #64748b; font-weight: 700; margin-top: 3px; display: flex; align-items: center; gap: 4px;">
                      <i class="fa-solid fa-stairs" style="font-size: 9px; color: #94a3b8;"></i>
                      <span>{{ $floorDisplay ? 'Floor ' . $floorDisplay : 'Floor 1' }}</span>
                      @if($item->room?->categoryRelation?->name || $item->room?->category)
                        <span style="color: #cbd5e1;">•</span>
                        <span style="color: #94a3b8;">{{ $item->room?->categoryRelation?->name ?? $item->room?->category }}</span>
                      @endif
                    </div>
                  @else
                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">
                      <i class="fa-solid fa-hourglass-half" style="font-size: 10px;"></i> Unassigned
                    </span>
                  @endif
                </td>

                <!-- 5. Checkin (date & time) -->
                <td style="white-space: nowrap;">
                  <div style="font-weight: 700; color: #1e293b; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                    <i class="fa-regular fa-calendar-check" style="color: #6366f1; font-size: 11px;"></i>
                    <span>{{ $item->reserve_date ? \Carbon\Carbon::parse($item->reserve_date)->format('d M Y') : 'N/A' }}</span>
                  </div>
                  @if($item->reserve_time)
                    <div style="font-size: 10px; color: #64748b; font-family: var(--font-mono, monospace); margin-top: 2px; white-space: nowrap;">
                      <i class="fa-regular fa-clock" style="font-size: 9px; color: #94a3b8;"></i> {{ date('h:i A', strtotime($item->reserve_time)) }}
                    </div>
                  @endif
                </td>

                <!-- 6. Checkout (date & time) -->
                <td style="white-space: nowrap;">
                  <div style="font-weight: 700; color: #1e293b; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;">
                    <i class="fa-regular fa-calendar-xmark" style="color: #e11d48; font-size: 11px;"></i>
                    <span>{{ $item->checkout_date ? \Carbon\Carbon::parse($item->checkout_date)->format('d M Y') : 'N/A' }}</span>
                  </div>
                  @if($item->checkout_time)
                    <div style="font-size: 10px; color: #64748b; font-family: var(--font-mono, monospace); margin-top: 2px; white-space: nowrap;">
                      <i class="fa-regular fa-clock" style="font-size: 9px; color: #94a3b8;"></i> {{ date('h:i A', strtotime($item->checkout_time)) }}
                    </div>
                  @endif
                </td>

                <!-- 7. Paid -->
                <td style="text-align: right; white-space: nowrap;">
                  <div style="font-family: var(--font-mono, monospace); font-weight: 800; font-size: 12px; color: #059669;">
                    ₹ {{ number_format($paidVal, 2) }}
                  </div>
                  @if($item->paymentMode?->name)
                    <div style="font-size: 10px; color: #64748b; margin-top: 2px; white-space: nowrap;">
                      {{ $item->paymentMode->name }}
                    </div>
                  @endif
                </td>

                <!-- 8. Remaining (outstanding) -->
                <td style="text-align: right; white-space: nowrap;">
                  <div style="font-family: var(--font-mono, monospace); font-weight: 800; font-size: 12px; color: {{ $balanceVal > 0 ? '#e11d48' : '#059669' }};">
                    ₹ {{ number_format($balanceVal, 2) }}
                  </div>
                  <div style="font-size: 10px; font-weight: 700; margin-top: 2px;">
                    @if($balanceVal > 0)
                      <span style="color: #e11d48; background: #ffe4e6; padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 800;">Due</span>
                    @else
                      <span style="color: #059669; background: #ecfdf5; padding: 1px 6px; border-radius: 4px; font-size: 9px; font-weight: 800;">Cleared</span>
                    @endif
                  </div>
                </td>

                <!-- 9. Status -->
                <td style="text-align: center; white-space: nowrap;">
                  <span class="status-badge {{ $statusClass }}">
                    <i class="fa-solid fa-circle" style="font-size: 6px;"></i>
                    <span>{{ $item->status ?? 'Confirmed' }}</span>
                  </span>
                </td>

                <!-- Action: Uniform Eye View Button -->
                <td style="text-align: center; white-space: nowrap;">
                  <button type="button" 
                          class="btn-action-view" 
                          onclick="openGuestDetailsModalById({{ $item->id }})" 
                          title="View Complete Guest Dossier (Rooms, Stay, Billing, Company)">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" style="text-align: center; color: var(--text-muted); padding: 36px 20px;">
                  <div style="font-size: 28px; color: #cbd5e1; margin-bottom: 8px;"><i class="fa-solid fa-users-slash"></i></div>
                  <div style="font-size: 13px; font-weight: 700; color: #64748b;">No matching guests found.</div>
                  <div style="font-size: 11px; color: #94a3b8; margin-top: 4px;">Try modifying your search query or filter options.</div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <!-- Backend Table Pagination Footer -->
        <div class="pms-table-pagination" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; border-top: 1px solid #e2e8f0; background: #ffffff; flex-wrap: wrap; gap: 10px;">
          <div class="pms-pagination-info" style="font-size: 12px; font-weight: 600; color: #64748b;">
            Showing {{ $guests->firstItem() ?? 0 }} to {{ $guests->lastItem() ?? 0 }} of {{ $guests->total() }} records
          </div>
          <div class="pms-page-nav">
            @if ($guests->hasPages())
              {{-- Previous Page Link --}}
              @if ($guests->onFirstPage())
                <span class="pms-page-btn" style="opacity: 0.4; cursor: not-allowed;"><i class="fa-solid fa-chevron-left"></i></span>
              @else
                <a href="{{ $guests->previousPageUrl() }}" class="pms-page-btn" title="Previous Page"><i class="fa-solid fa-chevron-left"></i></a>
              @endif

              {{-- Pagination Number Elements --}}
              @foreach ($guests->getUrlRange(max(1, $guests->currentPage() - 2), min($guests->lastPage(), $guests->currentPage() + 2)) as $page => $url)
                @if ($page == $guests->currentPage())
                  <span class="pms-page-btn active">{{ $page }}</span>
                @else
                  <a href="{{ $url }}" class="pms-page-btn">{{ $page }}</a>
                @endif
              @endforeach

              {{-- Next Page Link --}}
              @if ($guests->hasMorePages())
                <a href="{{ $guests->nextPageUrl() }}" class="pms-page-btn" title="Next Page"><i class="fa-solid fa-chevron-right"></i></a>
              @else
                <span class="pms-page-btn" style="opacity: 0.4; cursor: not-allowed;"><i class="fa-solid fa-chevron-right"></i></span>
              @endif
            @endif
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Bottom Summary Bar -->
<footer class="pms-bottom-bar">
  <div style="font-size: 11px; font-weight: 700; color: var(--text-secondary); display: flex; align-items: center; gap: 14px; flex-wrap: wrap;">
    <span><i class="fa-solid fa-users" style="color: #6366f1;"></i> Total Guests: <strong>{{ $stats['total'] ?? 0 }}</strong></span>
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
            <span class="dossier-label">Registration Type</span>
            <span class="dossier-val" id="crm-mdl-card-regtype" style="color: #4338ca; font-weight: 800;">-</span>
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
            <span class="dossier-label">Discount</span>
            <span class="dossier-val mono" id="crm-mdl-bill-discount" style="color: #059669;">-</span>
          </div>
          <div class="dossier-field-row">
            <span class="dossier-label">GST / Tax</span>
            <span class="dossier-val mono" id="crm-mdl-bill-gst" style="color: #6366f1;">-</span>
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
    switch ((status || '').toLowerCase()) {
      case 'confirmed': return 'confirmed';
      case 'arrived': return 'arrived';
      case 'checked in': case 'checked-in': case 'checkedin': return 'checkedin';
      case 'stay over': case 'stayover': return 'stayover';
      case 'checked out': case 'checked-out': case 'checkedout': return 'checkedout';
      case 'cancelled': return 'cancelled';
      default: return 'confirmed';
    }
  }

  // Populate and open the Dossier Modal
  function populateAndOpenModal(guest) {
    currentActiveCrmGuest = guest;

    // Header info
    document.getElementById('crm-mdl-guest-name').textContent = guest.guest_name || guest.name;
    document.getElementById('crm-mdl-reserve-id').textContent = guest.reserve_id || ('RES-' + guest.id);
    document.getElementById('crm-mdl-folio-number').textContent = guest.folio_number || '-';
    document.getElementById('crm-mdl-registered-at').textContent = guest.created_at_fmt || 'N/A';

    const statusBadge = document.getElementById('crm-mdl-status-badge');
    if (statusBadge) {
      statusBadge.textContent = guest.status || 'Confirmed';
      statusBadge.className = `status-badge ${getStatusBadgeColor(guest.status)}`;
    }

    // Highlight Banner
    document.getElementById('crm-mdl-room-number').textContent = guest.room_number || (guest.room?.room_number ?? 'Pending');
    document.getElementById('crm-mdl-room-category').textContent = guest.room_category || (guest.room?.category_relation?.name ?? 'DELUXE');
    document.getElementById('crm-mdl-room-floor').textContent = guest.room_floor || (guest.room?.floor_relation?.floor ?? '1');
    document.getElementById('crm-mdl-room-bedding').textContent = guest.room_bedding || (guest.room?.bedding_config_relation?.name ?? 'Standard');
    document.getElementById('crm-mdl-total-nights').textContent = guest.total_nights || 1;

    // Card 1: Guest Profile
    document.getElementById('crm-mdl-card-name').textContent = guest.guest_name || guest.name;
    document.getElementById('crm-mdl-card-regtype').textContent = guest.registration_type || (guest.registration_type?.name ?? 'Regular');
    document.getElementById('crm-mdl-card-phone').textContent = guest.mobile || 'N/A';
    document.getElementById('crm-mdl-card-email').textContent = guest.email || 'N/A';
    document.getElementById('crm-mdl-card-city').textContent = guest.city || 'N/A';
    document.getElementById('crm-mdl-card-address').textContent = guest.guest_address || 'N/A';
    document.getElementById('crm-mdl-card-nationality').textContent = guest.nationality || (guest.nationality?.name ?? 'Indian');
    
    const idProofType = guest.id_card_type || (guest.id_card_type?.name ?? 'ID Proof');
    const idProofNum = guest.id_card_number || 'N/A';
    document.getElementById('crm-mdl-card-idproof').textContent = `${idProofType} • ${idProofNum}`;

    const privRow = document.getElementById('crm-mdl-card-privilege-row');
    if (guest.has_privilege_card) {
      privRow.style.display = 'flex';
      document.getElementById('crm-mdl-card-privilege').textContent = guest.privilege_card_no || 'Active';
    } else {
      privRow.style.display = 'none';
    }

    // Card 2: Stay & Corporate
    document.getElementById('crm-mdl-card-checkin').textContent = `${guest.reserve_date} (${guest.reserve_time || '12:00 PM'})`;
    document.getElementById('crm-mdl-card-checkout').textContent = `${guest.checkout_date} (${guest.checkout_time || '11:00 AM'})`;
    document.getElementById('crm-mdl-card-duration').textContent = `${guest.total_nights || 1} Night(s)`;
    document.getElementById('crm-mdl-card-resmode').textContent = guest.reservation_mode || (guest.reservation_mode?.name ?? 'Front Desk');

    const corpSection = document.getElementById('crm-mdl-corporate-card-section');
    if (guest.company) {
      corpSection.style.display = 'block';
      document.getElementById('crm-mdl-company-name').textContent = guest.company.name;
      document.getElementById('crm-mdl-company-gstin').textContent = guest.company.gstin || 'N/A';
      const discVal = guest.company.discount_type === 'percentage' 
        ? `${guest.company.discount_value}%` 
        : `₹ ${Number(guest.company.discount_value || 0).toLocaleString('en-IN')}`;
      document.getElementById('crm-mdl-company-discount').textContent = `${discVal} (${guest.company.discount_type || 'percentage'})`;
    } else {
      corpSection.style.display = 'none';
    }

    // Card 3: Pricing & Billing Breakdown
    const rate = Number(guest.room_rate || 0);
    const gross = Number(guest.total_amount || 0);
    const advance = Number(guest.advance_amount || 0);
    const balance = Number(guest.balance || 0);
    const discountAmt = Number(guest.discount_amount || 0);
    const discountPct = Number(guest.discount_percentage || 0);
    const payable = Number(guest.payable_amount || 0);

    document.getElementById('crm-mdl-bill-rate').textContent = formatINR(rate);
    document.getElementById('crm-mdl-bill-gross').textContent = formatINR(gross);
    document.getElementById('crm-mdl-bill-advance').textContent = formatINR(advance);

    const balElem = document.getElementById('crm-mdl-bill-balance');
    balElem.textContent = formatINR(balance);
    balElem.style.color = balance > 0 ? '#e11d48' : '#059669';

    const balLabel = document.getElementById('crm-mdl-balance-label');
    if (balLabel) balLabel.textContent = balance > 0 ? 'Balance Due' : 'Fully Settled';

    document.getElementById('crm-mdl-bill-calc-row').textContent = `${formatINR(rate)} × ${guest.total_nights || 1} Night(s) = ${formatINR(rate * (guest.total_nights || 1))}`;
    
    let discText = '₹ 0.00';
    if (discountAmt > 0 || discountPct > 0) {
      discText = `- ${formatINR(discountAmt)} (${discountPct}%)`;
    }
    document.getElementById('crm-mdl-bill-discount').textContent = discText;

    const gstAmt = Number(guest.gst_amount || 0);
    const gstPct = Number(guest.gst_percentage || 0);
    const gstType = guest.gst_type || 'exclusive';
    let gstText = '₹ 0.00 (0%)';
    if (gstAmt > 0) {
      gstText = `${gstType === 'inclusive' ? 'Incl. ' : '+ '}${formatINR(gstAmt)} (${gstPct}%)`;
    }
    const crmGstElem = document.getElementById('crm-mdl-bill-gst');
    if (crmGstElem) crmGstElem.textContent = gstText;

    document.getElementById('crm-mdl-bill-payable').textContent = formatINR(payable);
    document.getElementById('crm-mdl-bill-paymode').textContent = guest.payment_mode || (guest.payment_mode?.name ?? 'Cash / Direct');
    document.getElementById('crm-mdl-bill-remarks').textContent = guest.payment_remarks || 'None';

    // Show modal
    openModal('guest-crm-details-modal');
  }

  // Open Uniform Guest Dossier Modal by Guest ID (with fallback to backend JSON API)
  function openGuestDetailsModalById(guestId) {
    const guest = (window.allCrmGuests || []).find(g => String(g.id) === String(guestId));
    if (guest) {
      populateAndOpenModal(guest);
      return;
    }

    // Fetch via backend API if not in current page memory
    fetch(`{{ url('front-office/guest') }}/${guestId}/details`, {
      headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success && data.guest) {
        populateAndOpenModal(data.guest);
      } else {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast('Guest record not found', 'error');
        } else {
          alert('Guest record not found');
        }
      }
    })
    .catch(() => {
      alert('Unable to load guest profile');
    });
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
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Registration Type:</td>
              <td style="padding: 4px 0; font-weight: 700; color: #4f46e5;">${escapeHtml(g.registration_type)}</td>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">City / Nationality:</td>
              <td style="padding: 4px 0; font-weight: 700;">${escapeHtml(g.city)} / ${escapeHtml(g.nationality)}</td>
            </tr>
            <tr>
              <td style="padding: 4px 0; font-weight: 600; color: #64748b;">Email:</td>
              <td style="padding: 4px 0;" colspan="3">${escapeHtml(g.email)}</td>
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
              <td style="padding: 6px 0; font-weight: 600; color: #64748b;">Discount:</td>
              <td style="padding: 6px 0; text-align: right; color: #059669; font-weight: 700;">${Number(g.discount_amount || 0) > 0 ? '-' + formatINR(g.discount_amount) + ' (' + (g.discount_percentage || 0) + '%)' : '₹ 0.00'}</td>
            </tr>
            <tr style="border-bottom: 1px solid #e2e8f0;">
              <td style="padding: 6px 0; font-weight: 600; color: #64748b;">GST / Tax:</td>
              <td style="padding: 6px 0; text-align: right; color: #6366f1; font-weight: 700;">${Number(g.gst_amount || 0) > 0 ? (g.gst_type === 'inclusive' ? 'Incl. ' : '+ ') + formatINR(g.gst_amount) + ' (' + (g.gst_percentage || 0) + '%)' : '₹ 0.00 (0%)'}</td>
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
</script>
@endpush
