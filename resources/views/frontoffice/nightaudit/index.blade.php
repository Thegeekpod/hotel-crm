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
      margin: 12mm 10mm;
    }
    html, body {
      background: #ffffff !important;
      color: #0f172a !important;
      font-size: 11px !important;
      line-height: 1.4 !important;
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
    }
    .print-table th, .print-table td {
      border: 1px solid #cbd5e1 !important;
      padding: 6px 8px !important;
    }
    .print-table th {
      background: #f1f5f9 !important;
      font-weight: 800 !important;
      text-transform: uppercase !important;
      font-size: 10px !important;
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

    <!-- TAB 1: TODAY PAYMENTS (Guest Billings & Settlements) -->
    <div id="tab-payments" class="audit-tab-pane active">
      <div class="pms-table-box" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; overflow-x: auto;">
        <table class="pms-table" style="width: 100%;">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">SL</th>
              <th>ROOM</th>
              <th>GUEST NAME</th>
              <th>PHONE</th>
              <th>SETTLE #</th>
              <th>CHECK-IN</th>
              <th>CHECK-OUT</th>
              <th style="text-align: center;">STAY STATUS</th>
              <th style="text-align: right;">TOTAL BILL</th>
              <th style="text-align: right;">PAID</th>
              <th style="text-align: right;">REMAINING</th>
              <th style="text-align: center;">SETTLEMENT</th>
            </tr>
          </thead>
          <tbody id="guests-tbody">
            @forelse($todayAllBookings as $idx => $b)
              @php
                $cleanNum = $b->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number))) : 'Unassigned';
                $isAdditional = !empty($b->primary_booking_id);
                $master = ($isAdditional && $b->primaryBooking) ? $b->primaryBooking : $b;

                $totalBill = (float)($master->payable_amount > 0 ? $master->payable_amount : ($master->total_amount > 0 ? $master->total_amount : 0));
                $totalPaid = (float)($master->payments ? $master->payments->sum('amount') : 0);
                if ($totalPaid <= 0 && (float)$master->advance_amount > 0) {
                    $totalPaid = (float)$master->advance_amount;
                }
                $remaining = max(0, round($totalBill - $totalPaid, 2));

                // Settled ONLY if remaining <= 0.01
                $isSettled = ($remaining <= 0.01);

                $roomPart = $b->room?->room_number ? preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number)) : '100';
                $settleNo = $b->settle_number ?: ($b->folio_number ?: ($master->settle_number ?: ($master->folio_number ?: ($b->primary_booking_id ?: ('SET-' . $roomPart . '-' . $b->id)))));
              @endphp
              <tr class="guest-row">
                <td style="text-align: center; font-weight: 800; color: #64748b; font-size: 11px; font-family: var(--font-mono, monospace);">
                  {{ $idx + 1 }}
                </td>
                <td>
                  <span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">
                    {{ $cleanNum }}
                  </span>
                </td>
                <td style="font-weight: 700; color: #0f172a;">
                  {{ ($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Guest') }}
                  @if($isAdditional)
                    <span style="font-size: 10px; color: #64748b; font-weight: 600;">(Addl. Room)</span>
                  @endif
                </td>
                <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #475569;">
                  {{ $b->guest?->mobile ?: ($b->guest?->mobile_no ?: '-') }}
                </td>
                <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #4338ca; font-weight: 700;">
                  {{ $settleNo }}
                </td>
                <td style="font-size: 11px; color: #475569;">
                  {{ $b->reserve_date ? \Carbon\Carbon::parse($b->reserve_date)->format('d M Y') : '-' }}
                </td>
                <td style="font-size: 11px; color: #475569;">
                  {{ $b->checkout_date ? \Carbon\Carbon::parse($b->checkout_date)->format('d M Y') : '-' }}
                </td>
                <!-- Stay Status -->
                <td style="text-align: center;">
                  <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 12px; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                    {{ $b->status }}
                  </span>
                </td>
                <!-- Total Bill -->
                <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 12px; color: #1e293b;">
                  ₹ {{ number_format($totalBill, 2) }}
                </td>
                <!-- Paid -->
                <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 12px; color: #059669;">
                  ₹ {{ number_format($totalPaid, 2) }}
                </td>
                <!-- Remaining -->
                <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 12px; color: {{ $remaining > 0.01 ? '#e11d48' : '#64748b' }};">
                  ₹ {{ number_format($remaining, 2) }}
                </td>
                <!-- Settlement Status -->
                <td style="text-align: center;">
                  @if($isSettled)
                    <span class="status-pill settled" title="Payment cleared in full">
                      <i class="fa-solid fa-circle-check"></i> Settled
                    </span>
                  @else
                    <span class="status-pill unsettled" title="Remaining balance of ₹ {{ number_format($remaining, 2) }}">
                      <i class="fa-solid fa-clock"></i> Unsettled
                    </span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="12" style="text-align: center; padding: 24px; color: #94a3b8; font-size: 12px;">
                  No guest payments or bookings recorded for this date.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($todayAllBookings) > 0)
            <tfoot>
              <tr style="background: #f8fafc; font-weight: 800;">
                <td colspan="8" style="text-align: right; font-size: 11px; color: #64748b; text-transform: uppercase; padding: 10px 12px;">
                  TOTAL BILL / COLLECTIONS / OUTSTANDING:
                </td>
                <td style="text-align: right; font-size: 12px; font-family: var(--font-mono, monospace); color: #1e293b; padding: 10px 12px;">
                  ₹ {{ number_format($totalBookingsBill, 2) }}
                </td>
                <td style="text-align: right; font-size: 12px; font-family: var(--font-mono, monospace); color: #059669; padding: 10px 12px;">
                  ₹ {{ number_format($totalPayments, 2) }}
                </td>
                <td style="text-align: right; font-size: 12px; font-family: var(--font-mono, monospace); color: {{ $totalUnsettledAmount > 0.01 ? '#e11d48' : '#64748b' }}; padding: 10px 12px;">
                  ₹ {{ number_format($totalUnsettledAmount, 2) }}
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

<!-- PRINT REPORT CONTENT (COMPREHENSIVE EXECUTIVE DOSSIER) -->
<div id="printable-night-audit-report" style="display: none;">
  <div style="max-width: 820px; margin: 0 auto; font-family: 'Segoe UI', Arial, sans-serif; padding: 10px 0; color: #0f172a;">
    
    <!-- Header with Hotel Identity -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #4338ca; padding-bottom: 12px; margin-bottom: 16px;">
      <div>
        <h1 style="margin: 0; font-size: 22px; font-weight: 800; color: #1e1b4b; text-transform: uppercase; letter-spacing: 0.5px;">Hotel Sagar Sonnet</h1>
        <div style="font-size: 11px; color: #475569; margin-top: 3px;">Luxury Hospitality &amp; Premium Suites • Live PMS Front Desk</div>
        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Phone: +91 98765 43210 • Email: info@hotelsagarsonnet.com</div>
      </div>
      <div style="text-align: right;">
        <div style="font-size: 16px; font-weight: 800; color: #4338ca; text-transform: uppercase; letter-spacing: 0.5px;">DAILY NIGHT AUDIT REPORT</div>
        <div style="font-size: 11px; font-family: monospace; font-weight: 700; color: #0f172a; margin-top: 3px;">
          Date: {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }} ({{ \Carbon\Carbon::parse($selectedDate)->format('l') }})
        </div>
        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
          Generated: {{ now()->format('d M Y, h:i A') }} • Status: <strong>{{ $existingAudit ? 'AUDIT CLOSED' : 'LIVE / PENDING' }}</strong>
        </div>
      </div>
    </div>

    <!-- Daily Operational Activity Summary Strip -->
    <div class="print-avoid-break" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 12px;">
      <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 8px; text-align: center;">
        <div style="font-size: 8.5px; font-weight: 800; color: #475569; text-transform: uppercase;">Today Payments</div>
        <div style="font-size: 15px; font-weight: 900; color: #059669; font-family: monospace; margin-top: 1px;">{{ count($paymentsList) }}</div>
        <div style="font-size: 8px; color: #64748b;">₹ {{ number_format($totalPayments, 2) }}</div>
      </div>
      <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 8px; text-align: center;">
        <div style="font-size: 8.5px; font-weight: 800; color: #475569; text-transform: uppercase;">Occupied Rooms</div>
        <div style="font-size: 15px; font-weight: 900; color: #2563eb; font-family: monospace; margin-top: 1px;">{{ $occupiedRoomsCount }}</div>
        <div style="font-size: 8px; color: #64748b;">{{ $occupancyRate }}% Occupancy</div>
      </div>
      <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 8px; text-align: center;">
        <div style="font-size: 8.5px; font-weight: 800; color: #475569; text-transform: uppercase;">Housekeeping</div>
        <div style="font-size: 15px; font-weight: 900; color: #16a34a; font-family: monospace; margin-top: 1px;">{{ count($todayHousekeepingHistories) }}</div>
        <div style="font-size: 8px; color: #64748b;">Activity Logged</div>
      </div>
      <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px; text-align: center;">
        <div style="font-size: 8.5px; font-weight: 800; color: #475569; text-transform: uppercase;">Operational</div>
        <div style="font-size: 15px; font-weight: 900; color: #d97706; font-family: monospace; margin-top: 1px;">{{ count($todayOperationalHistories) }}</div>
        <div style="font-size: 8px; color: #64748b;">Room Status Logs</div>
      </div>
    </div>

    <!-- 4 Key Financial Summary Cards -->
    <div class="print-avoid-break" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 16px;">
      <div style="border: 1px solid #cbd5e1; border-top: 3px solid #6366f1; border-radius: 6px; padding: 8px 10px; background: #faf5ff;">
        <div style="font-size: 9px; font-weight: 800; color: #6b21a8; text-transform: uppercase;">Total Bill Amount</div>
        <div style="font-size: 14px; font-weight: 900; color: #581c87; font-family: monospace; margin-top: 2px;">₹ {{ number_format($totalBookingsBill, 2) }}</div>
        <div style="font-size: 9px; color: #7e22ce; margin-top: 2px;">{{ count($todayAllBookings) }} Active Booking(s)</div>
      </div>
      <div style="border: 1px solid #cbd5e1; border-top: 3px solid #10b981; border-radius: 6px; padding: 8px 10px; background: #f0fdf4;">
        <div style="font-size: 9px; font-weight: 800; color: #166534; text-transform: uppercase;">Total Collections</div>
        <div style="font-size: 14px; font-weight: 900; color: #14532d; font-family: monospace; margin-top: 2px;">₹ {{ number_format($totalPayments, 2) }}</div>
        <div style="font-size: 9px; color: #15803d; margin-top: 2px;">Cleared / Realized</div>
      </div>
      <div style="border: 1px solid #cbd5e1; border-top: 3px solid #e11d48; border-radius: 6px; padding: 8px 10px; background: #fff1f2;">
        <div style="font-size: 9px; font-weight: 800; color: #9f1239; text-transform: uppercase;">Unsettled Balance</div>
        <div style="font-size: 14px; font-weight: 900; color: {{ $totalUnsettledAmount > 0.01 ? '#be123c' : '#15803d' }}; font-family: monospace; margin-top: 2px;">₹ {{ number_format($totalUnsettledAmount, 2) }}</div>
        <div style="font-size: 9px; color: #be123c; margin-top: 2px;">{{ $unsettledBookingsCount }} Pending Settle(s)</div>
      </div>
      <div style="border: 1px solid #cbd5e1; border-top: 3px solid #f59e0b; border-radius: 6px; padding: 8px 10px; background: #fffbeb;">
        <div style="font-size: 9px; font-weight: 800; color: #92400e; text-transform: uppercase;">Room Occupancy</div>
        <div style="font-size: 14px; font-weight: 900; color: #78350f; font-family: monospace; margin-top: 2px;">{{ number_format($occupancyRate, 1) }}%</div>
        <div style="font-size: 9px; color: #b45309; margin-top: 2px;">{{ $occupiedRoomsCount }} / {{ $totalRoomsCount }} Rooms Sold</div>
      </div>
    </div>

    <!-- Payment Modes & Operational Overview Strip -->
    <div class="print-avoid-break" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
      <!-- Payment Collection Breakdown -->
      <table class="print-table" style="font-size: 10px;">
        <thead>
          <tr>
            <th colspan="2" style="text-align: left; background: #f8fafc; font-size: 10px;">Collection Modes Breakdown</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Cash Collection</td>
            <td style="text-align: right; font-family: monospace; font-weight: 700;">₹ {{ number_format($cashCollection, 2) }}</td>
          </tr>
          <tr>
            <td>UPI / Online (GPay/PhonePe)</td>
            <td style="text-align: right; font-family: monospace; font-weight: 700;">₹ {{ number_format($upiCollection, 2) }}</td>
          </tr>
          <tr>
            <td>Credit / Debit Card (POS)</td>
            <td style="text-align: right; font-family: monospace; font-weight: 700;">₹ {{ number_format($cardCollection, 2) }}</td>
          </tr>
          <tr>
            <td>Bank Transfer / NEFT</td>
            <td style="text-align: right; font-family: monospace; font-weight: 700;">₹ {{ number_format($bankCollection, 2) }}</td>
          </tr>
          <tr style="font-weight: 800; background: #f8fafc;">
            <td>Total Net Collections Reconciled</td>
            <td style="text-align: right; font-family: monospace; color: #16a34a;">₹ {{ number_format($totalPayments, 2) }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Room & Movements Breakdown -->
      <table class="print-table" style="font-size: 10px;">
        <thead>
          <tr>
            <th colspan="2" style="text-align: left; background: #f8fafc; font-size: 10px;">Guest Movements &amp; Room Statistics</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Total Hotel Rooms / Occupied / Vacant</td>
            <td style="text-align: right; font-family: monospace; font-weight: 700;">{{ $totalRoomsCount }} / {{ $occupiedRoomsCount }} / {{ $vacantRoomsCount }}</td>
          </tr>
          <tr>
            <td>Scheduled Arrivals / Checked In</td>
            <td style="text-align: right; font-family: monospace; font-weight: 700;">{{ $arrivalsCount }} / {{ $checkedInArrivalsCount }}</td>
          </tr>
          <tr>
            <td>Scheduled Departures / Checked Out</td>
            <td style="text-align: right; font-family: monospace; font-weight: 700;">{{ $departuresCount }} / {{ $completedCheckoutsCount }}</td>
          </tr>
          <tr>
            <td>Average Daily Rate (ADR) / RevPAR</td>
            <td style="text-align: right; font-family: monospace; font-weight: 700;">₹ {{ number_format($adr, 2) }} / ₹ {{ number_format($revpar, 2) }}</td>
          </tr>
          <tr style="font-weight: 800; background: #f8fafc;">
            <td>Meal Plans Active (EP / CP / MAP / AP)</td>
            <td style="text-align: right; font-family: monospace;">{{ $mealPlanCounts['EP'] ?? 0 }} / {{ $mealPlanCounts['CP'] ?? 0 }} / {{ $mealPlanCounts['MAP'] ?? 0 }} / {{ $mealPlanCounts['AP'] ?? 0 }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Section: Today Guest Settlements & Collections Detail Table -->
    <div style="margin-bottom: 16px;">
      <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #1e1b4b; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px; display: flex; justify-content: space-between;">
        <span>Guest Billings, Settlements &amp; Outstanding Balances ({{ count($todayAllBookings) }} Bookings)</span>
        <span style="font-size: 10px; color: #64748b; text-transform: none;">All amounts in INR (₹)</span>
      </div>
      <table class="print-table" style="font-size: 9.5px;">
        <thead>
          <tr>
            <th style="width: 25px; text-align: center;">SL</th>
            <th>Guest Name</th>
            <th style="text-align: center;">Room</th>
            <th>Settle #</th>
            <th>Dates</th>
            <th style="text-align: right;">Total Bill</th>
            <th style="text-align: right;">Paid Amt</th>
            <th style="text-align: right;">Remaining</th>
            <th style="text-align: center;">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($todayAllBookings as $idx => $b)
            @php
              $isAdditional = !empty($b->primary_booking_id);
              $master = ($isAdditional && $b->primaryBooking) ? $b->primaryBooking : $b;
              $bill = (float)($master->payable_amount > 0 ? $master->payable_amount : $master->total_amount);
              $paid = (float)($master->payments ? $master->payments->sum('amount') : 0);
              if ($paid <= 0 && (float)$master->advance_amount > 0) {
                $paid = (float)$master->advance_amount;
              }
              $remaining = max(0, round($bill - $paid, 2));
              $isSettled = ($remaining <= 0.01);
              $roomDisplay = $b->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number))) : '-';
              $guestName = ($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Guest');
              $roomPart = $b->room?->room_number ? preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number)) : '100';
              $settleDisplay = $b->settle_number ?: ($b->folio_number ?: ($master->settle_number ?: ($master->folio_number ?: ($b->primary_booking_id ?: ('SET-' . $roomPart . '-' . $b->id)))));
            @endphp
            <tr>
              <td style="text-align: center; font-family: monospace;">{{ $idx + 1 }}</td>
              <td style="font-weight: 700;">{{ $guestName }}</td>
              <td style="text-align: center; font-weight: 700; font-family: monospace;">{{ $roomDisplay }}</td>
              <td style="font-family: monospace; font-weight: 700; color: #4338ca;">{{ $settleDisplay }}</td>
              <td style="font-size: 9px; color: #475569;">
                {{ $b->reserve_date ? \Carbon\Carbon::parse($b->reserve_date)->format('d M') : '-' }} - 
                {{ $b->checkout_date ? \Carbon\Carbon::parse($b->checkout_date)->format('d M') : '-' }}
              </td>
              <td style="text-align: right; font-family: monospace; font-weight: 700;">₹ {{ number_format($bill, 2) }}</td>
              <td style="text-align: right; font-family: monospace; color: #15803d; font-weight: 700;">₹ {{ number_format($paid, 2) }}</td>
              <td style="text-align: right; font-family: monospace; font-weight: 800; color: {{ $remaining > 0.01 ? '#b91c1c' : '#15803d' }};">
                ₹ {{ number_format($remaining, 2) }}
              </td>
              <td style="text-align: center; font-size: 8.5px; font-weight: 800; text-transform: uppercase;">
                {{ $isSettled ? 'SETTLED' : 'UNSETTLED' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align: center; padding: 14px; color: #94a3b8;">No bookings recorded for this date.</td>
            </tr>
          @endforelse
        </tbody>
        @if(count($todayAllBookings) > 0)
          <tfoot>
            <tr style="background: #f8fafc; font-weight: 800;">
              <td colspan="5" style="text-align: right; text-transform: uppercase; font-size: 10px;">Audit Grand Totals:</td>
              <td style="text-align: right; font-family: monospace; font-size: 10px;">₹ {{ number_format($totalBookingsBill, 2) }}</td>
              <td style="text-align: right; font-family: monospace; font-size: 10px; color: #15803d;">₹ {{ number_format($totalPayments, 2) }}</td>
              <td style="text-align: right; font-family: monospace; font-size: 10px; color: {{ $totalUnsettledAmount > 0.01 ? '#b91c1c' : '#15803d' }};">₹ {{ number_format($totalUnsettledAmount, 2) }}</td>
              <td style="text-align: center; font-size: 9px;">{{ $settledBookingsCount }}S / {{ $unsettledBookingsCount }}U</td>
            </tr>
          </tfoot>
        @endif
      </table>
    </div>

    <!-- Section: Occupied Rooms Detail Table (if any) -->
    @if($occupiedRoomsCount > 0)
      <div class="print-avoid-break" style="margin-bottom: 16px;">
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #1e1b4b; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px;">
          Occupied Rooms Sold Summary ({{ $occupiedRoomsCount }} Rooms)
        </div>
        <table class="print-table" style="font-size: 9.5px;">
          <thead>
            <tr>
              <th style="width: 25px; text-align: center;">SL</th>
              <th>Room</th>
              <th>Category</th>
              <th>Floor</th>
              <th>Primary Guest</th>
              <th>Settle #</th>
              <th style="text-align: center;">Housekeeping</th>
              <th style="text-align: right;">Rate / Day</th>
            </tr>
          </thead>
          <tbody>
            @php
              $printOccupiedList = isset($occupiedRooms) ? $occupiedRooms : $allRooms->filter(function($rm) use ($activeBookingsByRoom, $activeMaintenanceRooms) {
                return $activeBookingsByRoom->has($rm->id) && !in_array($rm->id, $activeMaintenanceRooms ?? []);
              });
            @endphp
            @foreach($printOccupiedList as $pIdx => $rm)
              @php
                $cleanNum = 'R' . preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number));
                $activeBooking = $activeBookingsByRoom->get($rm->id);
                $latestHk = $rm->housekeepingHistories ? $rm->housekeepingHistories->sortByDesc('id')->first() : null;
                $hkName = $latestHk?->housekeepingStatus?->name ?? 'Clean';
                $n = $activeBooking ? max(1, (int)$activeBooking->total_nights) : 1;
                $dayRate = $activeBooking ? (float)($activeBooking->room_rate ?: ($activeBooking->total_amount / $n)) : 0.00;
                $rmPart = preg_replace('/^r[-_\s]?/i', '', trim($rm->room_number)) ?: '100';
                $activeSettleDisplay = $activeBooking ? ($activeBooking->settle_number ?: ($activeBooking->folio_number ?: ($activeBooking->primary_booking_id ?: ('SET-' . $rmPart . '-' . $activeBooking->id)))) : '-';
              @endphp
              <tr>
                <td style="text-align: center; font-family: monospace;">{{ $pIdx + 1 }}</td>
                <td style="font-weight: 700; font-family: monospace;">{{ $cleanNum }}</td>
                <td>{{ $rm->categoryRelation?->name ?? 'Standard' }}</td>
                <td>Floor {{ $rm->floorRelation?->floor ?? '1' }}</td>
                <td>{{ $activeBooking ? (($activeBooking->guest?->title?->name ? $activeBooking->guest->title->name . ' ' : '') . ($activeBooking->guest?->guest_name ?? 'Guest')) : '-' }}</td>
                <td style="font-family: monospace; font-weight: 700; color: #4338ca;">{{ $activeSettleDisplay }}</td>
                <td style="text-align: center;">{{ $hkName }}</td>
                <td style="text-align: right; font-family: monospace; font-weight: 700;">₹ {{ number_format($dayRate, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <!-- Section: Housekeeping History Logs (Print) -->
    @if(count($todayHousekeepingHistories) > 0)
      <div class="print-avoid-break" style="margin-bottom: 16px;">
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #1e1b4b; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px; display: flex; justify-content: space-between;">
          <span>Housekeeping Activity &amp; Status Logs ({{ count($todayHousekeepingHistories) }} Records)</span>
          <span style="font-size: 10px; color: #64748b; text-transform: none;">Audited date activity</span>
        </div>
        <table class="print-table" style="font-size: 9px;">
          <thead>
            <tr>
              <th style="width: 25px; text-align: center;">SL</th>
              <th>Time</th>
              <th>Room</th>
              <th>Category</th>
              <th>Floor</th>
              <th style="text-align: center;">Housekeeping State</th>
              <th style="text-align: center;">Status</th>
              <th>Start Time</th>
              <th>Completion Time</th>
            </tr>
          </thead>
          <tbody>
            @foreach($todayHousekeepingHistories as $hkIdx => $hk)
              @php
                $rmNum = $hk->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($hk->room->room_number))) : ('Room #' . $hk->room_id);
                $stateName = $hk->housekeepingStatus?->name ?? 'Clean';
              @endphp
              <tr>
                <td style="text-align: center; font-family: monospace;">{{ $hkIdx + 1 }}</td>
                <td style="font-family: monospace;">{{ $hk->created_at ? $hk->created_at->format('h:i A') : '-' }}</td>
                <td style="font-weight: 700; font-family: monospace;">{{ $rmNum }}</td>
                <td>{{ $hk->room?->categoryRelation?->name ?? 'Standard' }}</td>
                <td>Floor {{ $hk->room?->floorRelation?->floor ?? '1' }}</td>
                <td style="text-align: center; font-weight: 700;">{{ $stateName }}</td>
                <td style="text-align: center; text-transform: uppercase;">{{ $hk->status ?? 'Complete' }}</td>
                <td style="font-family: monospace;">{{ $hk->start_time ? \Carbon\Carbon::parse($hk->start_time)->format('h:i A') : '-' }}</td>
                <td style="font-family: monospace;">{{ $hk->completion_time ? \Carbon\Carbon::parse($hk->completion_time)->format('h:i A') : '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    <!-- Section: Operational History Logs (Print) -->
    @if(count($todayOperationalHistories) > 0)
      <div class="print-avoid-break" style="margin-bottom: 16px;">
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #1e1b4b; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin-bottom: 6px; display: flex; justify-content: space-between;">
          <span>Operational Room Status Logs ({{ count($todayOperationalHistories) }} Records)</span>
          <span style="font-size: 10px; color: #64748b; text-transform: none;">Service &amp; Maintenance Tracking</span>
        </div>
        <table class="print-table" style="font-size: 9px;">
          <thead>
            <tr>
              <th style="width: 25px; text-align: center;">SL</th>
              <th>Time</th>
              <th>Room</th>
              <th>Category</th>
              <th>Floor</th>
              <th style="text-align: center;">Operational Status</th>
              <th style="text-align: center;">Model State</th>
            </tr>
          </thead>
          <tbody>
            @foreach($todayOperationalHistories as $opIdx => $op)
              @php
                $rmNum = $op->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($op->room->room_number))) : ('Room #' . $op->room_id);
                $opStatusName = $op->operationalStatus?->name ?? 'Active';
              @endphp
              <tr>
                <td style="text-align: center; font-family: monospace;">{{ $opIdx + 1 }}</td>
                <td style="font-family: monospace;">{{ $op->created_at ? $op->created_at->format('h:i A') : '-' }}</td>
                <td style="font-weight: 700; font-family: monospace;">{{ $rmNum }}</td>
                <td>{{ $op->room?->categoryRelation?->name ?? 'Standard' }}</td>
                <td>Floor {{ $op->room?->floorRelation?->floor ?? '1' }}</td>
                <td style="text-align: center; font-weight: 700;">{{ $opStatusName }}</td>
                <td style="text-align: center; text-transform: uppercase;">{{ $op->room?->status ?? 'Active' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

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
