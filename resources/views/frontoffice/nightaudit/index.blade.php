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

  /* Print */
  @media print {
    body * { visibility: hidden !important; }
    #printable-night-audit-report, #printable-night-audit-report * { visibility: visible !important; }
    #printable-night-audit-report {
      position: absolute !important;
      left: 0 !important;
      top: 0 !important;
      width: 100% !important;
      background: #ffffff !important;
      display: block !important;
      padding: 20px !important;
      color: #000000 !important;
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
              <button type="button" class="btn-ui-secondary" disabled style="opacity: 0.85; cursor: not-allowed; background: #ecfdf5; color: #047857; border-color: #a7f3d0; font-weight: 800;" title="Today's audit is completed and locked. Re-running is not supported.">
                <i class="fa-solid fa-lock"></i> Audit Completed (Locked)
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

    <!-- 2. Clean 4 KPI Cards -->
    <div class="audit-kpi-grid">
      <!-- Total Payments -->
      <div class="audit-kpi-card" style="border-left: 3px solid #10b981;">
        <div class="audit-kpi-top">
          <span class="audit-kpi-title">Total Payments</span>
          <span class="audit-kpi-icon" style="background: #ecfdf5; color: #059669;">
            <i class="fa-solid fa-cash-register"></i>
          </span>
        </div>
        <div class="audit-kpi-val" style="color: #059669;">₹ {{ number_format($totalPayments, 2) }}</div>
        <div class="audit-kpi-sub">
          Cash: ₹ {{ number_format($cashCollection, 0) }} • UPI: ₹ {{ number_format($upiCollection, 0) }} • Card: ₹ {{ number_format($cardCollection, 0) }}
        </div>
      </div>

      <!-- Room Occupancy -->
      <div class="audit-kpi-card" style="border-left: 3px solid #6366f1;">
        <div class="audit-kpi-top">
          <span class="audit-kpi-title">Room Occupancy</span>
          <span class="audit-kpi-icon" style="background: #eef2ff; color: #4338ca;">
            <i class="fa-solid fa-door-open"></i>
          </span>
        </div>
        <div class="audit-kpi-val" style="color: #4338ca;">{{ $occupancyRate }}%</div>
        <div class="audit-kpi-sub">
          Occupied: {{ $occupiedRoomsCount }} • Vacant: {{ $vacantRoomsCount }} • Total: {{ $totalRoomsCount }}
        </div>
      </div>

      <!-- Meal Plans -->
      <div class="audit-kpi-card" style="border-left: 3px solid #f59e0b;">
        <div class="audit-kpi-top">
          <span class="audit-kpi-title">Meal Plans</span>
          <span class="audit-kpi-icon" style="background: #fffbeb; color: #d97706;">
            <i class="fa-solid fa-utensils"></i>
          </span>
        </div>
        <div class="audit-kpi-val" style="color: #d97706;">₹ {{ number_format($dailyFoodRevenue, 2) }}</div>
        <div class="audit-kpi-sub">
          CP: {{ $mealPlanCounts['CP'] }} • MAP: {{ $mealPlanCounts['MAP'] }} • AP: {{ $mealPlanCounts['AP'] }} • EP: {{ $mealPlanCounts['EP'] }}
        </div>
      </div>

      <!-- Room Revenue -->
      <div class="audit-kpi-card" style="border-left: 3px solid #8b5cf6;">
        <div class="audit-kpi-top">
          <span class="audit-kpi-title">Room Revenue</span>
          <span class="audit-kpi-icon" style="background: #f5f3ff; color: #7c3aed;">
            <i class="fa-solid fa-chart-line"></i>
          </span>
        </div>
        <div class="audit-kpi-val" style="color: #7c3aed;">₹ {{ number_format($dailyRoomRevenue, 2) }}</div>
        <div class="audit-kpi-sub">
          Total Revenue: ₹ {{ number_format($totalDailyRevenue, 2) }} • ADR: ₹ {{ number_format($adr, 0) }}
        </div>
      </div>
    </div>

    <!-- 3. Navigation Tabs -->
    <div class="audit-nav-tabs">
      <button type="button" class="audit-nav-tab active" onclick="switchAuditTab('tab-payments', this)">
        <i class="fa-solid fa-receipt"></i> Payments ({{ count($paymentsList) }})
      </button>
      <button type="button" class="audit-nav-tab" onclick="switchAuditTab('tab-rooms', this)">
        <i class="fa-solid fa-bed"></i> Rooms ({{ $occupiedRoomsCount }}/{{ $totalRoomsCount }})
      </button>
      <button type="button" class="audit-nav-tab" onclick="switchAuditTab('tab-food', this)">
        <i class="fa-solid fa-bowl-food"></i> Food &amp; Meals ({{ count($mealPlansList) }})
      </button>
      <button type="button" class="audit-nav-tab" onclick="switchAuditTab('tab-guests', this)">
        <i class="fa-solid fa-users"></i> Guests ({{ $inHouseGuestsCount }})
      </button>
    </div>

    <!-- TAB 1: PAYMENTS -->
    <div id="tab-payments" class="audit-tab-pane active">
      <div class="pms-table-box" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; overflow-x: auto;">
        <table class="pms-table" style="width: 100%;">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">SL</th>
              <th>Time</th>
              <th>Guest Name</th>
              <th>Room</th>
              <th>Folio #</th>
              <th style="text-align: right;">Total Balance</th>
              <th style="text-align: right;">Paid</th>
              <th style="text-align: right;">Remaining</th>
              <th style="text-align: center;">Payment Type</th>
              <th>Mode</th>
              <th style="text-align: right;">Amount</th>
            </tr>
          </thead>
          <tbody>
            @forelse($paymentsList as $index => $p)
              @php
                $modeRaw = strtolower($p->paymentMode?->name ?? 'other');
                $modeClass = str_contains($modeRaw, 'cash') ? 'cash' :
                             (str_contains($modeRaw, 'upi') || str_contains($modeRaw, 'gpay') ? 'upi' :
                             (str_contains($modeRaw, 'card') ? 'card' :
                             (str_contains($modeRaw, 'bank') ? 'bank' : 'other')));
                $roomNum = $p->booking?->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($p->booking->room->room_number))) : '-';

                $booking = $p->booking;
                $totalBill = $booking ? (float)($booking->payable_amount ?: $booking->total_amount) : (float)$p->amount;
                $remaining = $booking ? max(0, (float)$booking->balance) : 0.00;
                $isSettled = ($booking && in_array($booking->status, ['Settlement Done', 'Checked Out'])) || ($remaining <= 0.01);

                if ($booking) {
                    $totalPaid = max(0, $totalBill - $remaining);
                    if ($totalPaid <= 0 && $booking->payments) {
                        $totalPaid = (float)$booking->payments->sum('amount');
                    }
                } else {
                    $totalPaid = (float)$p->amount;
                }
              @endphp
              <tr>
                <td style="text-align: center; font-weight: 800; color: #64748b; font-size: 11px; font-family: var(--font-mono, monospace);">
                  {{ $index + 1 }}
                </td>
                <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #64748b;">
                  {{ $p->payment_time ? date('h:i A', strtotime($p->payment_time)) : ($p->created_at ? $p->created_at->format('h:i A') : 'N/A') }}
                </td>
                <td style="font-weight: 700; color: #0f172a;">
                  {{ ($p->guest?->title?->name ? $p->guest->title->name . ' ' : '') . ($p->guest?->guest_name ?? 'Guest') }}
                </td>
                <td>
                  <span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">
                    {{ $roomNum }}
                  </span>
                </td>
                <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #6366f1; font-weight: 700;">
                  {{ $p->folio_number ?: ($p->booking?->folio_number ?: '-') }}
                </td>
                <!-- Total Balance (Total Bill) -->
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
                <!-- Payment Type: Settled / Unsettled (No Advance shown) -->
                <td style="text-align: center;">
                  @if($isSettled)
                    <span class="status-pill settled" title="Settlement Done">
                      <i class="fa-solid fa-circle-check"></i> Settled
                    </span>
                  @else
                    <span class="status-pill unsettled" title="Remaining balance of ₹ {{ number_format($remaining, 2) }}">
                      <i class="fa-solid fa-clock"></i> Unsettled
                    </span>
                  @endif
                </td>
                <td>
                  <span class="mode-pill {{ $modeClass }}">
                    {{ $p->paymentMode?->name ?? 'Cash' }}
                  </span>
                </td>
                <td style="text-align: right; font-weight: 900; font-family: var(--font-mono, monospace); font-size: 12px; color: #059669;">
                  ₹ {{ number_format($p->amount, 2) }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" style="text-align: center; padding: 24px; color: #94a3b8; font-size: 12px;">
                  No payments recorded for this date.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($paymentsList) > 0)
            <tfoot>
              <tr style="background: #f8fafc; font-weight: 800;">
                <td colspan="10" style="text-align: right; text-transform: uppercase; font-size: 11px; color: #64748b;">Total Collections:</td>
                <td style="text-align: right; font-size: 13px; font-family: var(--font-mono, monospace); color: #059669;">
                  ₹ {{ number_format($totalPayments, 2) }}
                </td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

    <!-- TAB 2: ROOMS -->
    <div id="tab-rooms" class="audit-tab-pane">
      <div class="pms-table-box" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; overflow-x: auto;">
        <table class="pms-table" style="width: 100%;">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">SL</th>
              <th>Room</th>
              <th>Category</th>
              <th>Floor</th>
              <th>Guest Name</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Meal Plan</th>
              <th style="text-align: right;">Rate / Day</th>
            </tr>
          </thead>
          <tbody>
            @forelse($inHouseBookings as $idx => $b)
              @php
                $cleanNum = $b->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number))) : 'Unassigned';
                $n = max(1, (int)$b->total_nights);
                $dayRate = (float)($b->room_rate ?: ($b->total_amount / $n));
              @endphp
              <tr>
                <td style="text-align: center; font-weight: 800; color: #64748b; font-size: 11px; font-family: var(--font-mono, monospace);">
                  {{ $idx + 1 }}
                </td>
                <td>
                  <span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">
                    {{ $cleanNum }}
                  </span>
                </td>
                <td style="font-weight: 600;">{{ $b->room?->categoryRelation?->name ?? 'Standard' }}</td>
                <td style="font-size: 11px; color: #64748b;">Floor {{ $b->room?->floorRelation?->floor ?? '1' }}</td>
                <td style="font-weight: 700; color: #0f172a;">
                  {{ ($b->guest?->title?->name ? $b->guest->title->name . ' ' : '') . ($b->guest?->guest_name ?? 'Guest') }}
                </td>
                <td style="font-size: 11px; color: #475569;">
                  {{ $b->reserve_date ? \Carbon\Carbon::parse($b->reserve_date)->format('d M Y') : 'N/A' }}
                </td>
                <td style="font-size: 11px; color: #475569;">
                  {{ $b->checkout_date ? \Carbon\Carbon::parse($b->checkout_date)->format('d M Y') : 'N/A' }}
                </td>
                <td>
                  <span class="plan-pill cp">
                    {{ $b->plan?->plan_name ?: 'Room Only (EP)' }}
                  </span>
                </td>
                <td style="text-align: right; font-weight: 900; font-family: var(--font-mono, monospace); font-size: 12px; color: #1e293b;">
                  ₹ {{ number_format($dayRate, 2) }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align: center; padding: 24px; color: #94a3b8; font-size: 12px;">
                  No occupied rooms on this date.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: FOOD & MEALS -->
    <div id="tab-food" class="audit-tab-pane">
      <div class="pms-table-box" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; overflow-x: auto;">
        <table class="pms-table" style="width: 100%;">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">SL</th>
              <th>Room</th>
              <th>Guest Name</th>
              <th>Plan Code</th>
              <th>Meal Plan Name</th>
              <th>Meals Included</th>
              <th style="text-align: right;">Price / Day</th>
            </tr>
          </thead>
          <tbody>
            @forelse($mealPlansList as $idx => $mp)
              @php
                $code = strtolower($mp['plan_code']);
                $pillCls = ($code === 'cp') ? 'cp' : (($code === 'map') ? 'map' : (($code === 'ap') ? 'ap' : 'ep'));
                $mealsText = ($code === 'ap') ? 'Breakfast + Lunch + Dinner' : (($code === 'map') ? 'Breakfast + Dinner' : (($code === 'cp') ? 'Breakfast Included' : 'Room Only'));
              @endphp
              <tr>
                <td style="text-align: center; font-weight: 800; color: #64748b; font-size: 11px; font-family: var(--font-mono, monospace);">
                  {{ $idx + 1 }}
                </td>
                <td>
                  <span style="font-weight: 800; color: #4338ca; background: #e0e7ff; padding: 2px 7px; border-radius: 6px; font-family: var(--font-mono, monospace); font-size: 11px;">
                    {{ $mp['room_number'] }}
                  </span>
                </td>
                <td style="font-weight: 700; color: #0f172a;">
                  {{ $mp['guest_name'] }}
                </td>
                <td>
                  <span class="plan-pill {{ $pillCls }}">
                    {{ $mp['plan_code'] }}
                  </span>
                </td>
                <td style="font-weight: 600; color: #334155;">
                  {{ $mp['plan_name'] }}
                </td>
                <td style="font-size: 11px; color: #64748b;">
                  {{ $mealsText }}
                </td>
                <td style="text-align: right; font-weight: 800; font-family: var(--font-mono, monospace); font-size: 12px;">
                  ₹ {{ number_format($mp['plan_price'], 2) }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align: center; padding: 24px; color: #94a3b8; font-size: 12px;">
                  No meal plan bookings recorded for this date.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 4: GUESTS -->
    <div id="tab-guests" class="audit-tab-pane">
      <div class="pms-table-box" style="border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; overflow-x: auto;">
        <table class="pms-table" style="width: 100%;">
          <thead>
            <tr>
              <th style="width: 45px; text-align: center;">SL</th>
              <th>Room</th>
              <th>Guest Name</th>
              <th>Phone</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Nights</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($inHouseBookings as $idx => $b)
              @php
                $cleanNum = $b->room?->room_number ? ('R' . preg_replace('/^r[-_\s]?/i', '', trim($b->room->room_number))) : 'Unassigned';
              @endphp
              <tr>
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
                </td>
                <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #475569;">
                  {{ $b->guest?->mobile ?: 'N/A' }}
                </td>
                <td style="font-size: 11px; color: #475569;">
                  {{ $b->reserve_date ? \Carbon\Carbon::parse($b->reserve_date)->format('d M Y') : 'N/A' }}
                </td>
                <td style="font-size: 11px; color: #475569;">
                  {{ $b->checkout_date ? \Carbon\Carbon::parse($b->checkout_date)->format('d M Y') : 'N/A' }}
                </td>
                <td style="text-align: center; font-weight: 700; font-size: 11px;">
                  {{ $b->total_nights }}
                </td>
                <td>
                  <span style="font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 12px; background: #ecfdf5; color: #065f46;">
                    {{ $b->status }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="text-align: center; padding: 24px; color: #94a3b8; font-size: 12px;">
                  No in-house guests currently checked in.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

@if($isToday && !$existingAudit)
<!-- SIMPLE COMPLETE AUDIT MODAL -->
<div class="modal-backdrop" id="modal-complete-audit" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(2px); z-index: 99999; align-items: center; justify-content: center;">
  <div style="background: #ffffff; width: 440px; max-width: 95vw; border-radius: 14px; overflow: hidden; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2); border: 1px solid #e2e8f0;">
    <div style="background: #ffffff; padding: 14px 18px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0; font-size: 15px; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 7px;">
        <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> Complete Today's Night Audit
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
        <div style="display: flex; justify-content: space-between; padding-top: 4px;">
          <span style="color: #64748b;">Total Revenue:</span>
          <strong style="color: #7c3aed;">₹ {{ number_format($totalDailyRevenue, 2) }}</strong>
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
                    placeholder="Closing notes (optional)..."></textarea>
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

<!-- PRINT REPORT CONTENT -->
<div id="printable-night-audit-report" style="display: none;">
  <div style="max-width: 800px; margin: 0 auto; font-family: sans-serif; padding: 20px;">
    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 14px;">
      <h2 style="margin: 0; font-size: 18px;">Hotel Sagar Sonnet</h2>
      <div style="font-size: 12px; margin-top: 2px;">Daily Night Audit Report</div>
      <div style="font-size: 13px; font-weight: bold; margin-top: 4px;">Date: {{ \Carbon\Carbon::parse($selectedDate)->format('d F Y') }}</div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 12px;">
      <tr>
        <td style="padding: 6px; border: 1px solid #ddd;"><strong>Total Payments:</strong></td>
        <td style="padding: 6px; border: 1px solid #ddd;"><strong>₹ {{ number_format($totalPayments, 2) }}</strong> (Cash: ₹ {{ number_format($cashCollection, 2) }}, UPI: ₹ {{ number_format($upiCollection, 2) }}, Card: ₹ {{ number_format($cardCollection, 2) }})</td>
      </tr>
      <tr>
        <td style="padding: 6px; border: 1px solid #ddd;"><strong>Room Revenue:</strong></td>
        <td style="padding: 6px; border: 1px solid #ddd;">₹ {{ number_format($dailyRoomRevenue, 2) }}</td>
      </tr>
      <tr>
        <td style="padding: 6px; border: 1px solid #ddd;"><strong>Meal Plans:</strong></td>
        <td style="padding: 6px; border: 1px solid #ddd;">₹ {{ number_format($dailyFoodRevenue, 2) }} (CP: {{ $mealPlanCounts['CP'] }}, MAP: {{ $mealPlanCounts['MAP'] }}, AP: {{ $mealPlanCounts['AP'] }}, EP: {{ $mealPlanCounts['EP'] }})</td>
      </tr>
      <tr>
        <td style="padding: 6px; border: 1px solid #ddd;"><strong>Total Revenue:</strong></td>
        <td style="padding: 6px; border: 1px solid #ddd;"><strong>₹ {{ number_format($totalDailyRevenue, 2) }}</strong></td>
      </tr>
      <tr>
        <td style="padding: 6px; border: 1px solid #ddd;"><strong>Occupancy:</strong></td>
        <td style="padding: 6px; border: 1px solid #ddd;">{{ $occupancyRate }}% ({{ $occupiedRoomsCount }}/{{ $totalRoomsCount }} Rooms)</td>
      </tr>
    </table>

    <div style="margin-top: 40px; display: flex; justify-content: space-between; font-size: 11px;">
      <div style="border-top: 1px solid #000; width: 150px; text-align: center; padding-top: 4px;">Night Auditor</div>
      <div style="border-top: 1px solid #000; width: 150px; text-align: center; padding-top: 4px;">Manager Approval</div>
    </div>
  </div>
</div>
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
