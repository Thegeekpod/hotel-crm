@extends('frontoffice.layouts.app')

@section('title', 'Night Audit - Hotel Sagar Sonnet CRM / PMS')

@push('styles')
<style>
  /* Tables */
  .pms-table th,
  .pms-table td {
    white-space: nowrap !important;
  }

  /* Header Card */
  .audit-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.04);
  }

  /* Date Navigator Controls */
  .audit-date-nav {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 2px 4px;
  }

  .audit-date-btn {
    background: transparent;
    border: none;
    color: #475569;
    padding: 5px 9px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: background 0.15s, color 0.15s;
  }
  .audit-date-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
  }

  .audit-date-input {
    height: 30px;
    padding: 2px 8px;
    font-size: 12px;
    font-weight: 700;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #1e293b;
    outline: none;
    cursor: pointer;
    font-family: inherit;
  }

  /* Status Badges */
  .audit-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
  }
  .audit-badge.completed {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
  }
  .audit-badge.pending {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
  }

  /* 4 KPI Summary Cards Grid */
  .audit-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 16px;
  }

  .audit-kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    transition: transform 0.15s, box-shadow 0.15s;
  }
  .audit-kpi-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  }

  .audit-kpi-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
  }

  .audit-kpi-title {
    font-size: 11px;
    font-weight: 800;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.4px;
  }

  .audit-kpi-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
  }

  .audit-kpi-val {
    font-size: 20px;
    font-weight: 900;
    color: #0f172a;
    font-family: var(--font-mono, monospace);
  }

  .audit-kpi-sub {
    font-size: 11px;
    color: #64748b;
    font-weight: 600;
    margin-top: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Action Buttons */
  .btn-run-audit {
    background: #4f46e5;
    color: #ffffff;
    border: 1px solid #4338ca;
    border-radius: 8px;
    padding: 7px 16px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
  }
  .btn-run-audit:hover {
    background: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
  }

  .btn-ui-secondary {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
  }
  .btn-ui-secondary:hover {
    background: #f8fafc;
    color: #0f172a;
  }

  /* Navigation Tabs */
  .audit-nav-tabs {
    display: flex;
    gap: 4px;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 16px;
    overflow-x: auto;
    padding-bottom: 0;
  }

  .audit-nav-tab {
    padding: 9px 16px;
    font-size: 12px;
    font-weight: 800;
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
  .audit-nav-tab:hover {
    color: #4f46e5;
  }
  .audit-nav-tab.active {
    color: #4f46e5;
    border-bottom-color: #4f46e5;
  }

  .audit-tab-pane {
    display: none;
  }
  .audit-tab-pane.active {
    display: block;
  }

  /* Sub Tabs inside panes */
  .audit-subtabs-wrap {
    display: flex;
    gap: 8px;
    margin-bottom: 14px;
    background: #f8fafc;
    padding: 6px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    flex-wrap: wrap;
  }
  .audit-subtab-btn {
    border: none;
    background: transparent;
    padding: 7px 14px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.15s;
  }
  .audit-subtab-btn:hover {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  }
  .audit-subtab-btn.active {
    background: #ffffff;
    color: #4f46e5;
    font-weight: 800;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  }
  .subtab-count-badge {
    padding: 2px 7px;
    border-radius: 9999px;
    font-size: 10px;
    font-weight: 800;
    font-family: var(--font-mono, monospace);
  }

  /* Pills */
  .mode-pill {
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
  }
  .mode-pill.cash { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .mode-pill.upi { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
  .mode-pill.card { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
  .mode-pill.bank { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
  .mode-pill.other { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

  .status-pill {
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
  }
  .status-pill.settled { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
  .status-pill.unsettled { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

  .plan-pill {
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 800;
    font-family: var(--font-mono, monospace);
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .plan-pill.ep { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
  .plan-pill.cp { background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
  .plan-pill.map { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
  .plan-pill.ap { background: #fce7f3; color: #be185d; border: 1px solid #fbcfe8; }

  /* Filter Chips */
  .audit-filter-chip {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.15s;
  }
  .audit-filter-chip:hover {
    background: #f1f5f9;
    color: #0f172a;
  }
  .audit-filter-chip.active {
    background: #4f46e5;
    color: #ffffff;
    border-color: #4338ca;
  }

  /* Responsive Rules */
  @media (max-width: 1024px) {
    .audit-kpi-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .audit-header-card {
      padding: 14px 16px;
    }
    .audit-kpi-grid {
      grid-template-columns: 1fr;
    }
    .audit-header-actions {
      width: 100%;
      margin-top: 8px;
      justify-content: flex-start;
    }
  }

  /* Proper Print Layout Stylesheet */
  @media print {
    @page {
      size: A4 portrait;
      margin: 10mm 8mm;
    }
    html, body {
      background: #ffffff !important;
      color: #0f172a !important;
      font-size: 10px !important;
      line-height: 1.3 !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    body * { visibility: hidden !important; }
    #printable-night-audit-report, #printable-night-audit-report * { visibility: visible !important; }
    #printable-night-audit-report {
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      width: 100% !important;
      background: #ffffff !important;
      display: block !important;
      padding: 0 !important;
      margin: 0 !important;
      color: #0f172a !important;
    }
    .print-avoid-break {
      page-break-inside: avoid !important;
      break-inside: avoid !important;
    }
    .print-table {
      width: 100% !important;
      border-collapse: collapse !important;
      table-layout: auto !important;
    }
    .print-table th, .print-table td {
      border: 1px solid #cbd5e1 !important;
      padding: 4px 5px !important;
      font-size: 8px !important;
    }
    .print-table th {
      background: #f1f5f9 !important;
      font-weight: 800 !important;
      text-transform: uppercase !important;
      font-size: 8.5px !important;
      letter-spacing: 0.2px !important;
    }
  }
</style>
@endpush

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container" style="padding: 16px 20px;">

    <!-- 1. Header Card (Matched to Folio & Guest CRM) -->
    <div class="audit-header-card">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
          <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <i class="fa-solid fa-moon" style="color: #6366f1;"></i> 
            Night Audit
            @if($existingAudit)
              <span class="audit-badge completed">
                <i class="fa-solid fa-lock"></i> Audit Completed
              </span>
            @elseif($isToday)
              <span class="audit-badge pending">
                <i class="fa-solid fa-clock"></i> Today (Pending)
              </span>
            @else
              <span class="audit-badge" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                <i class="fa-solid fa-clock-rotate-left"></i> Past Date
              </span>
            @endif
          </h2>
          <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
            Audit Date: <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}</strong>
            @if($existingAudit)
              • Completed by <strong>{{ $existingAudit->auditor_name ?: ($existingAudit->auditor?->name ?? 'Auditor') }}</strong> at {{ $existingAudit->completed_at ? $existingAudit->completed_at->format('h:i A') : 'N/A' }}
            @elseif(!$isToday)
              • <span style="color: #94a3b8;">Historical view (Read-only)</span>
            @endif
          </div>
        </div>

        <!-- Action Controls -->
        <div class="audit-header-actions" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
          <div class="audit-date-nav">
            <a href="{{ route('frontoffice.nightaudit', ['date' => $prevDate]) }}" class="audit-date-btn" title="Previous Day">
              <i class="fa-solid fa-chevron-left"></i>
            </a>
            <input type="date" 
                   class="audit-date-input" 
                   value="{{ $selectedDate }}" 
                   max="{{ $today }}"
                   onchange="window.location.href='{{ route('frontoffice.nightaudit') }}?date=' + this.value"
                   title="Select Audit Date">
            <a href="{{ route('frontoffice.nightaudit', ['date' => $nextDate]) }}" 
               class="audit-date-btn {{ $selectedDate >= $today ? 'disabled' : '' }}" 
               style="{{ $selectedDate >= $today ? 'opacity: 0.4; pointer-events: none;' : '' }}" 
               title="Next Day">
              <i class="fa-solid fa-chevron-right"></i>
            </a>
          </div>

          @if($selectedDate !== $today)
            <a href="{{ route('frontoffice.nightaudit') }}" class="btn-ui-secondary">
              <i class="fa-solid fa-rotate-left"></i> Today
            </a>
          @endif

          <button type="button" class="btn-ui-secondary" onclick="openPrintReportModal()">
            <i class="fa-solid fa-print"></i> Print
          </button>

          @if($isToday)
            @if($existingAudit)
              <button type="button" class="btn-ui-secondary" style="background: #f0fdf4; color: #15803d; border-color: #86efac; font-weight: 800; cursor: default;" title="Audit is completed at {{ $existingAudit->completed_at ? $existingAudit->completed_at->format('h:i A') : '' }}">
                <i class="fa-solid fa-circle-check"></i> Completed
              </button>
              <button type="button" class="btn-run-audit" style="background: #4338ca; border-color: #4338ca;" onclick="openCompleteAuditModal()" title="Recalculate and update today's audit with latest transactions">
                <i class="fa-solid fa-rotate"></i> Re-Run Audit
              </button>
            @else
              <button type="button" class="btn-run-audit" onclick="openCompleteAuditModal()">
                <i class="fa-solid fa-circle-check"></i> Complete Audit
              </button>
            @endif
          @else
            <span class="btn-ui-secondary" style="cursor: default; opacity: 0.85; background: #f8fafc; color: #64748b; font-weight: 700;" title="Night audit can only be performed for today. Past dates are view-only.">
              <i class="fa-solid fa-eye"></i> View-Only Mode
            </span>
          @endif
        </div>
      </div>
    </div>

    <!-- 2. Clean 4 KPI Cards (Total Amount, Room Occupancy, Unsettled Balance, Settled Balance) -->
    <div class="audit-kpi-grid">
      <!-- Total Amount (Sum of Total Bills) -->
      <div class="audit-kpi-card" style="border-left: 3px solid #10b981;">
        <div class="audit-kpi-top">
          <span class="audit-kpi-title">TOTAL AMOUNT</span>
          <span class="audit-kpi-icon" style="background: #ecfdf5; color: #059669;">
            <i class="fa-solid fa-receipt"></i>
          </span>
        </div>
        <div class="audit-kpi-val" style="color: #059669;">₹ {{ number_format($totalBookingsBill, 2) }}</div>
        <div class="audit-kpi-sub">
          Total Collections: ₹ {{ number_format($totalPayments, 2) }}
        </div>
      </div>

      <!-- Room Occupancy -->
      <div class="audit-kpi-card" style="border-left: 3px solid #6366f1;">
        <div class="audit-kpi-top">
          <span class="audit-kpi-title">ROOM OCCUPANCY</span>
          <span class="audit-kpi-icon" style="background: #e0e7ff; color: #4338ca;">
            <i class="fa-solid fa-bed"></i>
          </span>
        </div>
        <div class="audit-kpi-val" style="color: #4338ca;">{{ $occupancyRate }}%</div>
        <div class="audit-kpi-sub">
          Occupied: {{ $occupiedRoomsCount }} • Vacant: {{ $vacantRoomsCount }} • Total: {{ $totalRoomsCount }}
        </div>
      </div>

      <!-- Unsettled Balance (Outstanding Due) -->
      <div class="audit-kpi-card" style="border-left: 3px solid #ef4444;">
        <div class="audit-kpi-top">
          <span class="audit-kpi-title">UNSETTLED BALANCE</span>
          <span class="audit-kpi-icon" style="background: #fee2e2; color: #dc2626;">
            <i class="fa-solid fa-clock-rotate-left"></i>
          </span>
        </div>
        <div class="audit-kpi-val" style="color: #dc2626;">₹ {{ number_format($totalUnsettledAmount, 2) }}</div>
        <div class="audit-kpi-sub">
          <strong style="color: #b91c1c;">{{ $unsettledBookingsCount }}</strong> unsettled booking(s) pending payment
        </div>
      </div>

      <!-- Settled Balance -->
      <div class="audit-kpi-card" style="border-left: 3px solid #8b5cf6;">
        <div class="audit-kpi-top">
          <span class="audit-kpi-title">SETTLED BALANCE</span>
          <span class="audit-kpi-icon" style="background: #f5f3ff; color: #7c3aed;">
            <i class="fa-solid fa-circle-check"></i>
          </span>
        </div>
        <div class="audit-kpi-val" style="color: #7c3aed;">₹ {{ number_format($totalSettledAmount, 2) }}</div>
        <div class="audit-kpi-sub">
          <strong style="color: #16a34a;">{{ $settledBookingsCount }}</strong> settled booking(s) cleared
        </div>
      </div>
    </div>

    <!-- 3. Navigation Tabs -->
    <div class="audit-nav-tabs">
      <button type="button" class="audit-nav-tab active" onclick="switchAuditTab('tab-payments', this)">
        <i class="fa-solid fa-receipt"></i> Today Payments ({{ count($todayAllBookings) }})
      </button>
      <button type="button" class="audit-nav-tab" onclick="switchAuditTab('tab-rooms', this)">
        <i class="fa-solid fa-bed"></i> Occupied Rooms ({{ $occupiedRoomsCount }})
      </button>
    </div>

    <!-- TAB 1: TODAY PAYMENTS (Booking Ledgers & Settlements Grouped by Booking Number) -->
    <div id="tab-payments" class="audit-tab-pane active">
      <div class="pms-table-box" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; overflow-x: auto;">
        <table class="pms-table" style="width: 100%;">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">SL</th>
              <th style="min-width: 140px;">BOOKING NUMBER</th>
              <th style="min-width: 140px;">ROOM(S) &amp; FLOOR</th>
              <th style="min-width: 170px;">PRIMARY GUEST &amp; OCCUPANTS</th>
              <th style="min-width: 125px;">SETTLE / FOLIO #</th>
              <th style="min-width: 130px;">STAY DATES</th>
              <th style="text-align: center; min-width: 95px;">STATUS</th>
              <th style="text-align: right; min-width: 100px;">ROOM BILL</th>
              <th style="text-align: right; min-width: 110px;">EXTRA SERVICES</th>
              <th style="text-align: right; min-width: 110px;">TOTAL BILL</th>
              <th style="text-align: right; min-width: 100px;">PAID</th>
              <th style="text-align: right; min-width: 110px;">REMAINING</th>
              <th style="text-align: center; min-width: 105px;">SETTLEMENT</th>
            </tr>
          </thead>
          <tbody id="guests-tbody">
            @forelse($bookingLedgers as $idx => $b)
              <tr class="guest-row">
                <td style="text-align: center; font-weight: 800; color: #64748b; font-size: 11px; font-family: var(--font-mono, monospace);">
                  {{ $idx + 1 }}
                </td>
                <!-- Booking Number -->
                <td>
                  <div style="font-weight: 800; color: #4338ca; font-family: var(--font-mono, monospace); font-size: 12px; display: inline-flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-receipt" style="font-size: 11px; color: #6366f1;"></i>
                    <span>{{ $b['booking_number'] }}</span>
                  </div>
                </td>
                <!-- Room(s) & Floor -->
                <td>
                  <div style="display: flex; flex-wrap: wrap; gap: 4px; align-items: center;">
                    @foreach($b['rooms'] as $rm)
                      <span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px; display: inline-flex; align-items: center; gap: 3px;" title="{{ $rm['category'] }}">
                        <i class="fa-solid fa-door-open" style="font-size: 9px;"></i> {{ $rm['room_number'] }} <span style="font-size: 9px; opacity: 0.75;">(Fl {{ $rm['floor'] }})</span>
                      </span>
                    @endforeach
                    @if(!empty($b['transfers']))
                      @foreach($b['transfers'] as $tf)
                        <span style="font-size: 9px; font-weight: 800; color: #92400e; background: #fef3c7; border: 1px solid #fde68a; padding: 1px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;" title="Room swiped: R{{ $tf['from'] }} to R{{ $tf['to'] }} on {{ $tf['date'] }}">
                          <i class="fa-solid fa-repeat" style="font-size: 8px;"></i> R{{ $tf['from'] }}&rarr;R{{ $tf['to'] }}
                        </span>
                      @endforeach
                    @endif
                  </div>
                </td>
                <!-- Primary Guest & Occupants -->
                <td>
                  <div style="font-weight: 700; color: #0f172a; font-size: 12px;">
                    {{ $b['primary_guest_name'] }}
                  </div>
                  <div style="font-size: 10px; color: #64748b; margin-top: 2px; display: flex; align-items: center; gap: 6px;">
                    <span style="font-family: var(--font-mono, monospace);"><i class="fa-solid fa-phone" style="font-size: 8px; color: #10b981;"></i> {{ $b['primary_mobile'] }}</span>
                    @if($b['total_occupants'] > 1)
                      <span style="background: #f1f5f9; color: #475569; padding: 1px 5px; border-radius: 4px; font-weight: 800; font-size: 9px;" title="{{ implode(', ', $b['occupants_names']) }}">
                        +{{ $b['total_occupants'] - 1 }} guest(s)
                      </span>
                    @endif
                  </div>
                </td>
                <!-- Settle / Folio # -->
                <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #4338ca; font-weight: 700;">
                  {{ $b['settle_number'] }}
                </td>
                <!-- Stay Dates -->
                <td style="font-size: 11px; color: #475569; white-space: nowrap;">
                  <div><i class="fa-regular fa-calendar-check" style="font-size: 9px; color: #6366f1;"></i> {{ $b['checkin_date'] }}</div>
                  <div style="font-size: 10px; color: #94a3b8; margin-top: 1px;"><i class="fa-regular fa-calendar-xmark" style="font-size: 9px; color: #e11d48;"></i> {{ $b['checkout_date'] }}</div>
                </td>
                <!-- Stay Status -->
                <td style="text-align: center;">
                  <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 12px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                    {{ $b['stay_status'] }}
                  </span>
                </td>
                <!-- Room Bill -->
                <td style="text-align: right; font-weight: 700; font-family: var(--font-mono, monospace); font-size: 12px; color: #334155;">
                  ₹ {{ number_format($b['room_charges'], 2) }}
                </td>
                <!-- Extra Services -->
                <td style="text-align: right; font-family: var(--font-mono, monospace); font-size: 12px; white-space: nowrap;">
                  @if(($b['extra_orders_count'] ?? 0) > 0)
                    @if($b['extra_service_charges'] > 0)
                      <div style="font-weight: 800; color: #d97706;">
                        + ₹ {{ number_format($b['extra_service_charges'], 2) }}
                      </div>
                    @endif
                    @if(($b['extra_paid_charges'] ?? 0) > 0)
                      <div style="font-weight: 800; color: #15803d; font-size: 11px;">
                        <i class="fa-solid fa-circle-check"></i> ₹ {{ number_format($b['extra_paid_charges'], 2) }} <span style="font-size: 9px; opacity: 0.85;">(Paid)</span>
                      </div>
                    @endif
                    <div style="font-size: 9.5px; color: #b45309; font-weight: 700; margin-top: 2px;" title="{{ implode(', ', $b['extra_items_list'] ?? []) }}">
                      <i class="fa-solid fa-bell-concierge" style="font-size: 8px;"></i> {{ $b['extra_orders_count'] }} order(s)
                    </div>
                    @if(!empty($b['extra_orders_detailed']) && count($b['extra_orders_detailed']) > 0)
                      <div style="display: flex; flex-direction: column; gap: 2px; margin-top: 4px; align-items: flex-end;">
                        @foreach($b['extra_orders_detailed'] as $eOrd)
                          @if(!empty($eOrd['is_paid']))
                            <div style="font-size: 9px; color: #166534; background: #dcfce7; border: 1px solid #bbf7d0; border-radius: 4px; padding: 1px 5px; display: inline-flex; align-items: center; gap: 4px;" title="{{ $eOrd['order_number'] }}: {{ $eOrd['items_summary'] }} (Paid Direct)">
                              <span style="opacity: 0.8; font-size: 8px;"><i class="fa-solid fa-check" style="font-size: 7.5px;"></i> {{ $eOrd['date_short'] }}:</span>
                              <span style="font-weight: 700;">₹{{ number_format($eOrd['total_amount'], 2) }} (PAID)</span>
                            </div>
                          @else
                            <div style="font-size: 9px; color: #9a3412; background: #fff7ed; border: 1px solid #ffedd5; border-radius: 4px; padding: 1px 5px; display: inline-flex; align-items: center; gap: 4px;" title="{{ $eOrd['order_number'] }}: {{ $eOrd['items_summary'] }} (Charged to Folio)">
                              <span style="opacity: 0.8; font-size: 8px;"><i class="fa-regular fa-clock" style="font-size: 7.5px;"></i> {{ $eOrd['date_short'] }}:</span>
                              <span style="font-weight: 700;">+ ₹{{ number_format($eOrd['total_amount'], 2) }}</span>
                            </div>
                          @endif
                        @endforeach
                      </div>
                    @endif
                  @else
                    <span style="color: #94a3b8; font-size: 11px;">₹ 0.00</span>
                  @endif
                </td>
                <!-- Total Bill -->
                <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 12px; color: #0f172a; white-space: nowrap;">
                  ₹ {{ number_format($b['grand_total_bill'], 2) }}
                </td>
                <!-- Paid -->
                <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 12px; color: #059669; white-space: nowrap;">
                  ₹ {{ number_format($b['total_paid'] ?? $b['paid_amount'] ?? 0, 2) }}
                </td>
                <!-- Remaining -->
                @php
                  $remBal = $b['remaining_balance'] ?? $b['remaining_amount'] ?? 0;
                @endphp
                <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 12px; color: {{ $remBal > 0.01 ? '#e11d48' : '#64748b' }}; white-space: nowrap;">
                  <div>₹ {{ number_format($remBal, 2) }}</div>
                  @if($b['extra_service_charges'] > 0 && $remBal > 0.01)
                    <div style="font-size: 9px; font-weight: 700; color: #b45309; margin-top: 2px;">
                      (incl. +₹{{ number_format($b['extra_service_charges'], 2) }} serv.)
                    </div>
                  @endif
                </td>
                <!-- Settlement Status -->
                <td style="text-align: center;">
                  @if(!empty($b['is_settled']))
                    <span class="status-pill settled" title="Payment cleared in full">
                      <i class="fa-solid fa-circle-check"></i> Settled
                    </span>
                  @else
                    <span class="status-pill unsettled" title="Remaining balance of ₹ {{ number_format($remBal, 2) }}">
                      <i class="fa-solid fa-clock"></i> Unsettled
                    </span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="13" style="text-align: center; padding: 24px; color: #94a3b8; font-size: 12px;">
                  No bookings or payments recorded for this date.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($bookingLedgers) > 0)
            <tfoot>
              <tr style="background: #f8fafc; font-weight: 800;">
                <td colspan="7" style="text-align: right; font-size: 11px; color: #64748b; text-transform: uppercase; padding: 10px 12px;">
                  TOTAL ({{ count($bookingLedgers) }} BOOKINGS):
                </td>
                <td style="text-align: right; font-size: 12px; font-family: var(--font-mono, monospace); color: #334155; padding: 10px 12px;">
                  ₹ {{ number_format($totalRoomChargesAll, 2) }}
                </td>
                <td style="text-align: right; font-size: 12px; font-family: var(--font-mono, monospace); color: #d97706; padding: 10px 12px;">
                  + ₹ {{ number_format($totalExtraChargesAll, 2) }}
                </td>
                <td style="text-align: right; font-size: 12px; font-family: var(--font-mono, monospace); color: #0f172a; padding: 10px 12px;">
                  ₹ {{ number_format($totalGrandBillAll, 2) }}
                </td>
                <td style="text-align: right; font-size: 12px; font-family: var(--font-mono, monospace); color: #059669; padding: 10px 12px;">
                  ₹ {{ number_format($totalPaidAll, 2) }}
                </td>
                <td style="text-align: right; font-size: 12px; font-family: var(--font-mono, monospace); color: {{ $totalRemainingAll > 0.01 ? '#e11d48' : '#64748b' }}; padding: 10px 12px;">
                  ₹ {{ number_format($totalRemainingAll, 2) }}
                </td>
                <td style="text-align: center; font-size: 11px; color: {{ $unsettledBookingsCount > 0 ? '#b91c1c' : '#15803d' }}; padding: 10px 12px;">
                  {{ $unsettledBookingsCount }} Unsettled
                </td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

    <!-- TAB 2: TODAY ROOMS (Complete Hotel Room Inventory & Occupancy + Housekeeping & Operational Histories) -->
    <div id="tab-rooms" class="audit-tab-pane">
      <!-- Subtabs Navigation -->
      <div class="audit-subtabs-wrap">
        <button type="button" class="audit-subtab-btn active" onclick="switchRoomSubView('view-room-inventory', this)">
          <i class="fa-solid fa-bed"></i> Occupied Rooms
          <span class="subtab-count-badge" style="background: #e0e7ff; color: #4338ca;">{{ $occupiedRoomsCount }}</span>
        </button>
        <button type="button" class="audit-subtab-btn" onclick="switchRoomSubView('view-hk-history', this)">
          <i class="fa-solid fa-broom"></i> Today's Housekeeping History
          <span class="subtab-count-badge" style="background: #dcfce7; color: #15803d;">{{ count($todayHousekeepingHistories) }}</span>
        </button>
        <button type="button" class="audit-subtab-btn" onclick="switchRoomSubView('view-op-history', this)">
          <i class="fa-solid fa-sliders"></i> Today's Operational History
          <span class="subtab-count-badge" style="background: #fef3c7; color: #b45309;">{{ count($todayOperationalHistories) }}</span>
        </button>
      </div>

      <!-- SUBVIEW 1: OCCUPIED ROOMS -->
      <div id="view-room-inventory" class="room-subview">
        <div class="pms-table-box" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; overflow-x: auto;">
          <table class="pms-table" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 45px; text-align: center;">SL</th>
                <th>Room</th>
                <th>Category</th>
                <th>Floor</th>
                <th style="text-align: center;">Occupancy</th>
                <th>Guest &amp; Settle #</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th style="text-align: center;">Operational Status</th>
                <th style="text-align: center;">Housekeeping</th>
                <th style="text-align: right;">Rate / Day</th>
              </tr>
            </thead>
            <tbody id="rooms-tbody">
              @php
                $displayOccupiedRooms = isset($occupiedRooms) ? $occupiedRooms : $allRooms->filter(function($rm) use ($activeBookingsByRoom, $activeMaintenanceRooms) {
                  return $activeBookingsByRoom->has($rm->id) && !in_array($rm->id, $activeMaintenanceRooms ?? []);
                });
              @endphp
              @forelse($displayOccupiedRooms as $idx => $rm)
                @php
                  $cleanNum = 'R' . preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number));
                  $isMaint = in_array($rm->id, $activeMaintenanceRooms ?? []);
                  $activeBooking = $activeBookingsByRoom->get($rm->id);
                  $isOccupied = ($activeBooking && !$isMaint);

                  // Housekeeping State
                  $latestHk = $rm->housekeepingHistories ? $rm->housekeepingHistories->sortByDesc('id')->first() : null;
                  $hkName = $latestHk?->housekeepingStatus?->name ?? ($latestHk?->housekeepingState?->name ?? 'Clean');
                  $hkRaw = strtolower($hkName);
                  $isDirty = str_contains($hkRaw, 'dirty') || str_contains($hkRaw, 'cleaning') || str_contains($hkRaw, 'touch up');

                  // Operational State
                  $latestOp = $rm->operationalHistories ? $rm->operationalHistories->sortByDesc('id')->first() : null;
                  $opName = $latestOp?->operationalStatus?->name ?? 'Active';
                  $isOpBlocked = (bool)preg_match('/block|blok|maintenance|order/i', $opName);

                  $n = $activeBooking ? max(1, (int)$activeBooking->total_nights) : 1;
                  $dayRate = $activeBooking ? (float)($activeBooking->room_rate ?: ($activeBooking->total_amount / $n)) : 0.00;

                  $filterType = $isMaint ? 'maintenance' : ($isOccupied ? 'occupied' : 'vacant');
                  $rmPart = preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number)) ?: '100';
                  $activeSettleNo = $activeBooking ? ($activeBooking->settle_number ?: ($activeBooking->folio_number ?: ($activeBooking->primary_booking_id ?: ('SET-' . $rmPart . '-' . $activeBooking->id)))) : '-';
                @endphp
                <tr class="room-row" data-type="{{ $filterType }}" data-dirty="{{ $isDirty ? '1' : '0' }}">
                  <td style="text-align: center; font-weight: 800; color: #64748b; font-size: 11px; font-family: var(--font-mono, monospace);">
                    {{ $idx + 1 }}
                  </td>
                  <td>
                    <span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">
                      {{ $cleanNum }}
                    </span>
                  </td>
                  <td style="font-weight: 600; color: #1e293b;">
                    {{ $rm->categoryRelation?->name ?? 'Standard' }}
                  </td>
                  <td style="font-size: 11px; color: #64748b;">
                    Floor {{ $rm->floorRelation?->floor ?? '1' }}
                  </td>
                  <!-- Occupancy Status -->
                  <td style="text-align: center;">
                    @if($isMaint)
                      <span class="status-pill" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                        <i class="fa-solid fa-wrench"></i> Maintenance
                      </span>
                    @elseif($isOccupied)
                      <span class="status-pill" style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe;">
                        <i class="fa-solid fa-door-closed"></i> Occupied
                      </span>
                    @else
                      <span class="status-pill" style="background: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                        <i class="fa-solid fa-door-open"></i> Vacant
                      </span>
                    @endif
                  </td>
                  <!-- Guest & Settle # -->
                  <td>
                    @if($isOccupied && $activeBooking)
                      <div style="font-weight: 700; color: #0f172a; line-height: 1.2;">
                        {{ ($activeBooking->guest?->title?->name ? $activeBooking->guest->title->name . ' ' : '') . ($activeBooking->guest?->guest_name ?? 'Guest') }}
                      </div>
                      <div style="font-size: 10px; color: #4338ca; font-weight: 700; font-family: var(--font-mono, monospace); margin-top: 2px;">
                        {{ $activeSettleNo }}
                      </div>
                    @elseif($isMaint)
                      <span style="color: #b45309; font-size: 11px; font-weight: 600;">Out of Service</span>
                    @else
                      <span style="color: #94a3b8; font-size: 11px;">Available</span>
                    @endif
                  </td>
                  <!-- Check-in -->
                  <td style="font-size: 11px; color: #475569;">
                    {{ ($isOccupied && $activeBooking && $activeBooking->reserve_date) ? \Carbon\Carbon::parse($activeBooking->reserve_date)->format('d M Y') : '-' }}
                  </td>
                  <!-- Check-out -->
                  <td style="font-size: 11px; color: #475569;">
                    {{ ($isOccupied && $activeBooking && $activeBooking->checkout_date) ? \Carbon\Carbon::parse($activeBooking->checkout_date)->format('d M Y') : '-' }}
                  </td>
                  <!-- Operational Status -->
                  <td style="text-align: center;">
                    @if($isOpBlocked)
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;">
                        <i class="fa-solid fa-ban"></i> {{ $opName }}
                      </span>
                    @else
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">
                        <i class="fa-solid fa-circle-check"></i> {{ $opName }}
                      </span>
                    @endif
                  </td>
                  <!-- Housekeeping -->
                  <td style="text-align: center;">
                    @if($isDirty)
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;">
                        <i class="fa-solid fa-broom"></i> {{ $hkName }}
                      </span>
                    @else
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac;">
                        <i class="fa-solid fa-check"></i> {{ $hkName }}
                      </span>
                    @endif
                  </td>
                  <!-- Daily Rate -->
                  <td style="text-align: right; font-weight: 900; font-family: var(--font-mono, monospace); font-size: 12px; color: #1e293b;">
                    @if($dayRate > 0)
                      ₹ {{ number_format($dayRate, 2) }}
                    @else
                      <span style="color: #94a3b8;">-</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="11" style="text-align: center; padding: 32px; color: #94a3b8; font-size: 12px;">
                    <i class="fa-solid fa-door-open" style="font-size: 22px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                    No rooms are currently occupied on this date ({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}).
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- SUBVIEW 2: TODAY'S HOUSEKEEPING HISTORIES -->
      <div id="view-hk-history" class="room-subview" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
          <div>
            <h4 style="margin: 0; font-size: 13px; font-weight: 800; color: #1e293b;">Today's Housekeeping History Logs</h4>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Housekeeping states, cleaning events, and inspections recorded on this date.</p>
          </div>
          <span style="font-size: 11px; font-weight: 800; background: #e0e7ff; color: #4338ca; padding: 3px 10px; border-radius: 9999px;">
            {{ count($todayHousekeepingHistories) }} Record(s) Logged
          </span>
        </div>

        <div class="pms-table-box" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; overflow-x: auto;">
          <table class="pms-table" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 45px; text-align: center;">SL</th>
                <th style="width: 130px;">Time / Date</th>
                <th>Room Number</th>
                <th>Category</th>
                <th>Floor</th>
                <th style="text-align: center;">Housekeeping State</th>
                <th style="text-align: center;">Task Status</th>
                <th>Start Time</th>
                <th>Completion Time</th>
              </tr>
            </thead>
            <tbody>
              @forelse($todayHousekeepingHistories as $hkIdx => $hk)
                @php
                  $rmNum = $hk->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($hk->room->room_number))) : 'Room #' . $hk->room_id;
                  $stateName = $hk->housekeepingStatus?->name ?? 'Clean';
                  $stateRaw = strtolower($stateName);
                  $isDirtyState = str_contains($stateRaw, 'dirty') || str_contains($stateRaw, 'cleaning') || str_contains($stateRaw, 'touch up');
                @endphp
                <tr>
                  <td style="text-align: center; font-weight: 800; color: #64748b; font-size: 11px; font-family: var(--font-mono, monospace);">
                    {{ $hkIdx + 1 }}
                  </td>
                  <td style="font-size: 11px; font-weight: 600; color: #475569; font-family: var(--font-mono, monospace);">
                    {{ $hk->created_at ? $hk->created_at->format('h:i A') : '-' }}
                    <span style="display: block; font-size: 10px; color: #94a3b8;">{{ $hk->created_at ? $hk->created_at->format('d M Y') : '' }}</span>
                  </td>
                  <td>
                    <span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">
                      {{ $rmNum }}
                    </span>
                  </td>
                  <td style="font-weight: 600; color: #1e293b;">
                    {{ $hk->room?->categoryRelation?->name ?? 'Standard' }}
                  </td>
                  <td style="font-size: 11px; color: #64748b;">
                    Floor {{ $hk->room?->floorRelation?->floor ?? '1' }}
                  </td>
                  <td style="text-align: center;">
                    @if($isDirtyState)
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-broom"></i> {{ $stateName }}
                      </span>
                    @else
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-circle-check"></i> {{ $stateName }}
                      </span>
                    @endif
                  </td>
                  <td style="text-align: center;">
                    @if(strtolower($hk->status) === 'complete' || strtolower($hk->status) === 'completed')
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">
                        <i class="fa-solid fa-check"></i> Complete
                      </span>
                    @else
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                        <i class="fa-solid fa-clock"></i> {{ ucfirst($hk->status ?? 'Pending') }}
                      </span>
                    @endif
                  </td>
                  <td style="font-size: 11px; color: #475569; font-family: var(--font-mono, monospace);">
                    {{ $hk->start_time ? \Carbon\Carbon::parse($hk->start_time)->format('h:i A') : '-' }}
                  </td>
                  <td style="font-size: 11px; color: #475569; font-family: var(--font-mono, monospace);">
                    {{ $hk->completion_time ? \Carbon\Carbon::parse($hk->completion_time)->format('h:i A') : '-' }}
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" style="text-align: center; padding: 32px; color: #94a3b8; font-size: 12px;">
                    <i class="fa-solid fa-broom" style="font-size: 22px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                    No housekeeping history logs recorded for this date ({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}).
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- SUBVIEW 3: TODAY'S OPERATIONAL HISTORIES -->
      <div id="view-op-history" class="room-subview" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
          <div>
            <h4 style="margin: 0; font-size: 13px; font-weight: 800; color: #1e293b;">Today's Operational History Logs</h4>
            <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Operational status changes (Active, In-Service, Out of Order, Blocked) logged on this date.</p>
          </div>
          <span style="font-size: 11px; font-weight: 800; background: #fef3c7; color: #b45309; padding: 3px 10px; border-radius: 9999px;">
            {{ count($todayOperationalHistories) }} Record(s) Logged
          </span>
        </div>

        <div class="pms-table-box" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; overflow-x: auto;">
          <table class="pms-table" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 45px; text-align: center;">SL</th>
                <th style="width: 130px;">Time / Date</th>
                <th>Room Number</th>
                <th>Category</th>
                <th>Floor</th>
                <th style="text-align: center;">Operational Status</th>
                <th style="text-align: center;">Room Model State</th>
              </tr>
            </thead>
            <tbody>
              @forelse($todayOperationalHistories as $opIdx => $op)
                @php
                  $rmNum = $op->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($op->room->room_number))) : 'Room #' . $op->room_id;
                  $opStatusName = $op->operationalStatus?->name ?? 'Active';
                  $isOpBlocked = (bool)preg_match('/block|blok|maintenance|order/i', $opStatusName);
                  $roomStatus = $op->room?->status ?? 'Active';
                @endphp
                <tr>
                  <td style="text-align: center; font-weight: 800; color: #64748b; font-size: 11px; font-family: var(--font-mono, monospace);">
                    {{ $opIdx + 1 }}
                  </td>
                  <td style="font-size: 11px; font-weight: 600; color: #475569; font-family: var(--font-mono, monospace);">
                    {{ $op->created_at ? $op->created_at->format('h:i A') : '-' }}
                    <span style="display: block; font-size: 10px; color: #94a3b8;">{{ $op->created_at ? $op->created_at->format('d M Y') : '' }}</span>
                  </td>
                  <td>
                    <span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">
                      {{ $rmNum }}
                    </span>
                  </td>
                  <td style="font-weight: 600; color: #1e293b;">
                    {{ $op->room?->categoryRelation?->name ?? 'Standard' }}
                  </td>
                  <td style="font-size: 11px; color: #64748b;">
                    Floor {{ $op->room?->floorRelation?->floor ?? '1' }}
                  </td>
                  <td style="text-align: center;">
                    @if($isOpBlocked)
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-ban"></i> {{ $opStatusName }}
                      </span>
                    @else
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #dcfce7; color: #15803d; border: 1px solid #86efac; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-circle-check"></i> {{ $opStatusName }}
                      </span>
                    @endif
                  </td>
                  <td style="text-align: center;">
                    @if(strtolower($roomStatus) === 'active')
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;">
                        Active
                      </span>
                    @else
                      <span style="font-size: 10px; font-weight: 800; padding: 2px 7px; border-radius: 6px; background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                        {{ $roomStatus }}
                      </span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" style="text-align: center; padding: 32px; color: #94a3b8; font-size: 12px;">
                    <i class="fa-solid fa-sliders" style="font-size: 22px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                    No operational history changes recorded for this date ({{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}).
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>



    @if($isToday)
<!-- SIMPLE COMPLETE / RE-AUDIT MODAL -->
<div class="modal-backdrop" id="modal-complete-audit" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(2px); z-index: 99999; align-items: center; justify-content: center;">
  <div style="background: #ffffff; width: 440px; max-width: 95vw; border-radius: 14px; overflow: hidden; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); border: 1px solid #e2e8f0;">
    <div style="background: #ffffff; padding: 14px 18px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 7px;">
        <i class="fa-solid fa-rotate" style="color: #4338ca;"></i> {{ $existingAudit ? 'Re-Run & Update Today\'s Night Audit' : 'Complete Today\'s Night Audit' }}
      </h3>
      <button type="button" onclick="closeCompleteAuditModal()" style="background: transparent; border: none; color: #94a3b8; font-size: 18px; cursor: pointer;">&times;</button>
    </div>

    <div style="padding: 16px 18px;">
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; margin-bottom: 14px; font-size: 12px;">
        <div style="display: flex; justify-content: space-between; padding-bottom: 4px;">
          <span style="color: #64748b;">Audit Date:</span>
          <strong>{{ \Carbon\Carbon::parse($today)->format('d M Y') }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 4px 0;">
          <span style="color: #64748b;">Total Collections:</span>
          <strong style="color: #059669;">₹ {{ number_format($totalPayments, 2) }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 4px 0;">
          <span style="color: #64748b;">Occupancy:</span>
          <strong>{{ $occupancyRate }}% ({{ $occupiedRoomsCount }} Rooms)</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 4px 0;">
          <span style="color: #64748b;">Unsettled Balance:</span>
          <strong style="color: #e11d48;">₹ {{ number_format($totalUnsettledAmount, 2) }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding-top: 4px;">
          <span style="color: #64748b;">Total Bill Amount:</span>
          <strong style="color: #7c3aed;">₹ {{ number_format($totalBookingsBill, 2) }}</strong>
        </div>
      </div>

      <form id="form-complete-audit" onsubmit="submitNightAudit(event)">
        <input type="hidden" name="audit_date" value="{{ $today }}">

        <div style="margin-bottom: 12px;">
          <label style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
            Auditor Name:
          </label>
          <input type="text" 
                 name="auditor_name" 
                 style="width: 100%; height: 34px; padding: 4px 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px; font-weight: 700; box-sizing: border-box; outline: none;" 
                 value="{{ auth()->user()?->name ?? 'Front Office Night Auditor' }}" 
                 required>
        </div>

        <div style="margin-bottom: 16px;">
          <label style="display: block; font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
            Closing Remarks:
          </label>
          <textarea name="remarks" 
                    rows="2" 
                    style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 10px; font-family: inherit; font-size: 12px; box-sizing: border-box; resize: vertical; outline: none;" 
                    placeholder="Closing notes (optional)...">{{ $existingAudit?->remarks ?? '' }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 8px;">
          <button type="button" onclick="closeCompleteAuditModal()" class="btn-ui-secondary">
            Cancel
          </button>
          <button type="submit" id="btn-submit-audit" class="btn-run-audit">
            <i class="fa-solid fa-check"></i> Finalize &amp; Save
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<!-- PRINT REPORT CONTENT (FRONT OFFICE NIGHT AUDIT REPORT) -->
<div id="printable-night-audit-report" style="display: none;">
  <div style="width: 100%; margin: 0 auto; font-family: 'Segoe UI', Arial, sans-serif; padding: 0; color: #0f172a; box-sizing: border-box;">
    
    <!-- Header with Hotel Identity -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #4338ca; padding-bottom: 12px; margin-bottom: 14px;">
      <div>
        <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #1e1b4b; text-transform: uppercase; letter-spacing: 0.5px;">Hotel Sagar Sonnet</h1>
        <div style="font-size: 11px; color: #475569; margin-top: 3px;">Luxury Hospitality &amp; Premium Suites &bull; Front Office Night Audit</div>
        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Station Road &bull; Front Desk Management System</div>
      </div>
      <div style="text-align: right;">
        <div style="font-size: 15px; font-weight: 800; color: #4338ca; text-transform: uppercase; letter-spacing: 0.5px;">FRONT OFFICE NIGHT AUDIT REPORT</div>
        <div style="font-size: 11px; font-family: monospace; font-weight: 700; color: #0f172a; margin-top: 3px;">
          Audit Date: {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }} ({{ \Carbon\Carbon::parse($selectedDate)->format('l') }})
        </div>
        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
          Auditor: <strong>{{ $existingAudit?->auditor_name ?? auth()->user()?->name ?? 'Front Office Night Auditor' }}</strong> &bull; Status: <strong style="color: #059669;">{{ $existingAudit ? 'AUDIT COMPLETED' : 'LIVE AUDIT' }}</strong>
        </div>
      </div>
    </div>

    <!-- 4 Front Office KPI Summary Cards (Exactly matching Front Office screen) -->
    <div class="print-avoid-break" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 16px;">
      <div style="border: 1px solid #cbd5e1; border-left: 4px solid #10b981; border-radius: 6px; padding: 8px 10px; background: #ffffff;">
        <div style="font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase;">TOTAL AMOUNT</div>
        <div style="font-size: 15px; font-weight: 900; color: #059669; font-family: monospace; margin-top: 2px;">₹ {{ number_format($totalPayments, 2) }}</div>
        <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">Total Collections: ₹ {{ number_format($totalPayments, 2) }}</div>
      </div>
      <div style="border: 1px solid #cbd5e1; border-left: 4px solid #4f46e5; border-radius: 6px; padding: 8px 10px; background: #ffffff;">
        <div style="font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase;">ROOM OCCUPANCY</div>
        <div style="font-size: 15px; font-weight: 900; color: #4338ca; font-family: monospace; margin-top: 2px;">{{ number_format($occupancyRate, 1) }}%</div>
        <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">Occupied: {{ $occupiedRoomsCount }} &bull; Vacant: {{ $vacantRoomsCount }} &bull; Total: {{ $totalRoomsCount }}</div>
      </div>
      <div style="border: 1px solid #cbd5e1; border-left: 4px solid #e11d48; border-radius: 6px; padding: 8px 10px; background: #ffffff;">
        <div style="font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase;">UNSETTLED BALANCE</div>
        <div style="font-size: 15px; font-weight: 900; color: {{ $totalUnsettledAmount > 0.01 ? '#be123c' : '#15803d' }}; font-family: monospace; margin-top: 2px;">₹ {{ number_format($totalUnsettledAmount, 2) }}</div>
        <div style="font-size: 8.5px; color: {{ $totalUnsettledAmount > 0.01 ? '#be123c' : '#64748b' }}; margin-top: 2px;">{{ $unsettledBookingsCount }} unsettled booking(s) pending</div>
      </div>
      <div style="border: 1px solid #cbd5e1; border-left: 4px solid #7c3aed; border-radius: 6px; padding: 8px 10px; background: #ffffff;">
        <div style="font-size: 9px; font-weight: 800; color: #475569; text-transform: uppercase;">SETTLED BALANCE</div>
        <div style="font-size: 15px; font-weight: 900; color: #7c3aed; font-family: monospace; margin-top: 2px;">₹ {{ number_format(max(0, $totalBookingsBill - $totalUnsettledAmount), 2) }}</div>
        <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">{{ $settledBookingsCount }} settled booking(s) cleared</div>
      </div>
    </div>

    <!-- Section 1: Today Payments (Booking Billings & Extra Services) -->
    <div style="margin-bottom: 16px;">
      <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #1e1b4b; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
        <span>Today Bookings &amp; Payments ({{ count($bookingLedgers) }})</span>
        <span style="font-size: 9.5px; color: #64748b; text-transform: none;">Reconciled Booking Accounts (₹ INR)</span>
      </div>
      <table class="print-table" style="font-size: 8px; width: 100%; border-collapse: collapse; table-layout: fixed;">
        <thead>
          <tr>
            <th style="width: 22px; text-align: center;">SL</th>
            <th style="width: 70px;">BOOKING #</th>
            <th style="width: 65px;">ROOM(S)</th>
            <th style="width: 105px;">PRIMARY GUEST</th>
            <th style="width: 65px;">SETTLE #</th>
            <th style="width: 55px;">CHECK-IN</th>
            <th style="width: 55px;">CHECK-OUT</th>
            <th style="width: 48px; text-align: center;">STATUS</th>
            <th style="width: 58px; text-align: right;">ROOM BILL</th>
            <th style="width: 58px; text-align: right;">EXTRA SERV.</th>
            <th style="width: 60px; text-align: right;">TOTAL BILL</th>
            <th style="width: 55px; text-align: right;">PAID</th>
            <th style="width: 58px; text-align: right;">REMAINING</th>
            <th style="width: 55px; text-align: center;">SETTLE</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bookingLedgers as $idx => $b)
            @php
              $rem = (float)($b['remaining_balance'] ?? $b['remaining_amount'] ?? 0);
              $pd = (float)($b['total_paid'] ?? $b['paid_amount'] ?? 0);
              $isSet = !empty($b['is_settled']) || ($rem <= 0.01);
              $roomsStr = collect($b['rooms'] ?? [])->map(fn($r) => $r['room_number'] . ' (Fl ' . $r['floor'] . ')')->implode(', ');
              if (empty($roomsStr)) {
                $roomsStr = $b['room_number'] ?? '-';
              }
            @endphp
            <tr>
              <td style="text-align: center; font-family: monospace;">{{ $idx + 1 }}</td>
              <td style="font-family: monospace; font-weight: 700; color: #4338ca; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $b['booking_number'] ?? ('RES-' . ($b['booking_id'] ?? $b['id'])) }}</td>
              <td style="font-family: monospace; font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $roomsStr }}</td>
              <td style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                {{ $b['primary_guest_name'] ?? $b['guest_name'] ?? 'Guest' }}
                @if(($b['total_occupants'] ?? 1) > 1)
                  <span style="font-size: 7.5px; opacity: 0.8; font-weight: 600;">(+{{ ($b['total_occupants'] ?? 1) - 1 }})</span>
                @endif
              </td>
              <td style="font-family: monospace; font-weight: 700; font-size: 8px; color: #0f766e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $b['settle_number'] ?? $b['folio_number'] ?? '-' }}</td>
              <td style="font-size: 8px; color: #475569; white-space: nowrap;">{{ $b['checkin_date'] ?? '-' }}</td>
              <td style="font-size: 8px; color: #475569; white-space: nowrap;">{{ $b['checkout_date'] ?? '-' }}</td>
              <td style="text-align: center; font-size: 7.5px; white-space: nowrap;">{{ $b['stay_status'] ?? $b['status'] ?? 'Active' }}</td>
              <td style="text-align: right; font-family: monospace; font-weight: 700; white-space: nowrap;">₹ {{ number_format($b['room_charges'] ?? 0, 2) }}</td>
              <td style="text-align: right; font-family: monospace; font-weight: 700; color: {{ ($b['extra_service_charges'] ?? 0) > 0 ? '#b45309' : '#94a3b8' }}; white-space: nowrap;">
                @if(($b['extra_service_charges'] ?? 0) > 0)
                  +₹ {{ number_format($b['extra_service_charges'], 2) }}
                @else
                  ₹ 0.00
                @endif
              </td>
              <td style="text-align: right; font-family: monospace; font-weight: 800; white-space: nowrap;">₹ {{ number_format($b['grand_total_bill'] ?? $b['total_bill'] ?? 0, 2) }}</td>
              <td style="text-align: right; font-family: monospace; color: #15803d; font-weight: 700; white-space: nowrap;">₹ {{ number_format($pd, 2) }}</td>
              <td style="text-align: right; font-family: monospace; font-weight: 800; color: {{ $rem > 0.01 ? '#b91c1c' : '#15803d' }}; white-space: nowrap;">₹ {{ number_format($rem, 2) }}</td>
              <td style="text-align: center; font-size: 7.5px; font-weight: 800; color: {{ $isSet ? '#15803d' : '#b91c1c' }}; white-space: nowrap;">
                {{ $isSet ? 'SETTLED' : 'UNSETTLED' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="14" style="text-align: center; padding: 14px; color: #94a3b8;">No booking records recorded for this date.</td>
            </tr>
          @endforelse
        </tbody>
        @if(count($bookingLedgers) > 0)
          <tfoot>
            <tr style="background: #f8fafc; font-weight: 800;">
              <td colspan="8" style="text-align: right; text-transform: uppercase; font-size: 8px; color: #64748b;">TOTALS (ROOMS / EXTRA SERV / GRAND / PAID / REM):</td>
              <td style="text-align: right; font-family: monospace; font-size: 8.5px;">₹ {{ number_format($totalRoomChargesAll, 2) }}</td>
              <td style="text-align: right; font-family: monospace; font-size: 8.5px; color: #b45309;">+₹ {{ number_format($totalExtraChargesAll, 2) }}</td>
              <td style="text-align: right; font-family: monospace; font-size: 9px; font-weight: 800;">₹ {{ number_format($totalGrandBillAll, 2) }}</td>
              <td style="text-align: right; font-family: monospace; font-size: 8.5px; color: #15803d;">₹ {{ number_format($totalPaidAll, 2) }}</td>
              <td style="text-align: right; font-family: monospace; font-size: 8.5px; color: {{ $totalRemainingAll > 0.01 ? '#b91c1c' : '#15803d' }};">₹ {{ number_format($totalRemainingAll, 2) }}</td>
              <td style="text-align: center; font-size: 8px; color: {{ $unsettledBookingsCount > 0 ? '#b91c1c' : '#15803d' }};">{{ $unsettledBookingsCount }} Due</td>
            </tr>
          </tfoot>
        @endif
      </table>
    </div>

    <!-- Section 2: Occupied Rooms (Summary & Tariffs) -->
    <div class="print-avoid-break" style="margin-bottom: 16px;">
      <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #1e1b4b; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
        <span>Occupied Rooms ({{ $occupiedRoomsCount }})</span>
        <span style="font-size: 9.5px; color: #64748b; text-transform: none;">In-House Room Occupancy &amp; Rates</span>
      </div>
      <table class="print-table" style="font-size: 8px; width: 100%; border-collapse: collapse; table-layout: fixed;">
        <thead>
          <tr>
            <th style="width: 25px; text-align: center;">SL</th>
            <th style="width: 42px; text-align: center;">ROOM</th>
            <th style="width: 90px;">CATEGORY</th>
            <th style="width: 58px;">FLOOR</th>
            <th style="width: 125px;">GUEST NAME</th>
            <th style="width: 75px;">SETTLE #</th>
            <th style="width: 62px;">CHECK-IN</th>
            <th style="width: 62px;">CHECK-OUT</th>
            <th style="width: 58px; text-align: center;">STAY</th>
            <th style="width: 90px; text-align: center;">HOUSEKEEPING</th>
            <th style="width: 85px; text-align: right;">DAY TARIFF</th>
          </tr>
        </thead>
        <tbody>
          @php
            $printOccupiedList = isset($occupiedRooms) ? $occupiedRooms : $allRooms->filter(function($rm) use ($activeBookingsByRoom, $activeMaintenanceRooms) {
              return $activeBookingsByRoom->has($rm->id) && !in_array($rm->id, $activeMaintenanceRooms ?? []);
            });
          @endphp
          @forelse($printOccupiedList as $pIdx => $rm)
            @php
              $cleanNum = 'R' . preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number));
              $activeBooking = $activeBookingsByRoom->get($rm->id);
              $latestHk = $rm->housekeepingHistories ? $rm->housekeepingHistories->sortByDesc('id')->first() : null;
              $hkName = $latestHk?->housekeepingStatus?->name ?? 'Clean';
              $n = $activeBooking ? max(1, (int)$activeBooking->total_nights) : 1;
              $dayRate = $activeBooking ? (float)($activeBooking->room_rate ?: ($activeBooking->total_amount / $n)) : 0.00;
              $rmPart = preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number)) ?: '100';
              $activeSettleDisplay = $activeBooking ? ($activeBooking->settle_number ?: ($activeBooking->folio_number ?: ($activeBooking->primary_booking_id ?: ('SET-' . $rmPart . '-' . $activeBooking->id)))) : '-';
              $occGuest = $activeBooking ? (($activeBooking->guest?->title?->name ? $activeBooking->guest->title->name . ' ' : '') . ($activeBooking->guest?->guest_name ?? 'Guest')) : '-';
            @endphp
            <tr>
              <td style="text-align: center; font-family: monospace;">{{ $pIdx + 1 }}</td>
              <td style="text-align: center; font-weight: 700; font-family: monospace; color: #4338ca;">{{ $cleanNum }}</td>
              <td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $rm->categoryRelation?->name ?? 'Standard' }}</td>
              <td>Floor {{ $rm->floorRelation?->floor ?? '1' }}</td>
              <td style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $occGuest }}</td>
              <td style="font-family: monospace; font-weight: 700; font-size: 8px; color: #4338ca; word-break: break-all;">{{ $activeSettleDisplay }}</td>
              <td style="font-size: 8px; color: #475569; white-space: nowrap;">{{ $activeBooking?->reserve_date ? \Carbon\Carbon::parse($activeBooking->reserve_date)->format('d M Y') : '-' }}</td>
              <td style="font-size: 8px; color: #475569; white-space: nowrap;">{{ $activeBooking?->checkout_date ? \Carbon\Carbon::parse($activeBooking->checkout_date)->format('d M Y') : '-' }}</td>
              <td style="text-align: center; font-size: 8px;">{{ $activeBooking?->status ?? 'Active' }}</td>
              <td style="text-align: center; font-weight: 700; font-size: 8px;">{{ $hkName }}</td>
              <td style="text-align: right; font-family: monospace; font-weight: 700; white-space: nowrap;">₹ {{ number_format($dayRate, 2) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="11" style="text-align: center; padding: 14px; color: #94a3b8;">No occupied rooms recorded on this date.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Auditor Remarks & Sign-off Footer -->
    <div class="print-avoid-break" style="margin-top: 24px; padding-top: 10px; border-top: 1px solid #cbd5e1;">
      @if($existingAudit && $existingAudit->remarks)
        <div style="font-size: 10px; color: #475569; margin-bottom: 24px; background: #f8fafc; padding: 6px 10px; border: 1px solid #e2e8f0; border-radius: 4px;">
          <strong>Closing Remarks:</strong> {{ $existingAudit->remarks }}
        </div>
      @endif

      <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 36px;">
        <div style="text-align: center; width: 220px;">
          <div style="border-bottom: 1px solid #0f172a; height: 30px; margin-bottom: 6px;"></div>
          <div style="font-size: 11px; font-weight: 800; color: #0f172a;">Night Auditor / Front Desk</div>
          <div style="font-size: 9.5px; color: #64748b;">{{ $existingAudit?->auditor_name ?? auth()->user()?->name ?? 'Front Office Auditor' }}</div>
        </div>
        <div style="text-align: center; width: 160px; font-size: 9.5px; color: #64748b;">
          Hotel Sagar Sonnet PMS<br>
          Verification Stamp
        </div>
        <div style="text-align: center; width: 220px;">
          <div style="border-bottom: 1px solid #0f172a; height: 30px; margin-bottom: 6px;"></div>
          <div style="font-size: 11px; font-weight: 800; color: #0f172a;">Duty Manager / General Manager</div>
          <div style="font-size: 9.5px; color: #64748b;">Signature &amp; Verification</div>
        </div>
      </div>
    </div>

  </div>
</div>

</div>
</main>
@endsection

@push('scripts')
<script>
  function switchAuditTab(tabId, el) {
    var panes = document.querySelectorAll('.audit-tab-pane');
    for (var i = 0; i < panes.length; i++) {
      panes[i].style.display = 'none';
    }
    var tabs = document.querySelectorAll('.audit-nav-tab');
    for (var j = 0; j < tabs.length; j++) {
      tabs[j].classList.remove('active');
    }
    
    var target = document.getElementById(tabId);
    if (target) {
      target.style.display = 'block';
    }
    if (el) {
      el.classList.add('active');
    }
  }

  function switchRoomSubView(viewId, btn) {
    var container = document.getElementById('tab-rooms');
    if (!container) return;

    var btns = container.querySelectorAll('.audit-subtab-btn');
    btns.forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');

    var views = container.querySelectorAll('.room-subview');
    views.forEach(function(v) { v.style.display = 'none'; });

    var target = document.getElementById(viewId);
    if (target) {
      target.style.display = 'block';
    }
  }

  function openCompleteAuditModal() {
    var modal = document.getElementById('modal-complete-audit');
    if (modal) modal.style.display = 'flex';
  }

  function closeCompleteAuditModal() {
    var modal = document.getElementById('modal-complete-audit');
    if (modal) modal.style.display = 'none';
  }

  function openPrintReportModal() {
    window.print();
  }

  function submitNightAudit(e) {
    e.preventDefault();
    var btn = document.getElementById('btn-submit-audit');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
    }

    var form = document.getElementById('form-complete-audit');
    var formData = new FormData(form);
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    var token = tokenMeta ? tokenMeta.getAttribute('content') : '';

    fetch('{{ route("frontoffice.night-audit.complete") }}', {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: formData
    })
    .then(function(res) {
      return res.json();
    })
    .then(function(data) {
      if (btn) btn.disabled = false;
      if (data.success) {
        closeCompleteAuditModal();
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Audit Saved!',
            text: data.message,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'OK'
          }).then(function() {
            window.location.reload();
          });
        } else {
          alert(data.message);
          window.location.reload();
        }
      } else {
        if (btn) btn.innerHTML = '<i class="fa-solid fa-check"></i> Finalize &amp; Save';
        alert(data.error || 'Failed to complete night audit.');
      }
    })
    .catch(function(err) {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Finalize &amp; Save';
      }
      console.error(err);
      alert('Network or server error while completing night audit.');
    });
  }

  document.addEventListener('click', function(e) {
    var modal = document.getElementById('modal-complete-audit');
    if (e.target === modal) {
      closeCompleteAuditModal();
    }
  });
</script>
@endpush
