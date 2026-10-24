@extends('frontoffice.layouts.app')

@section('title', 'Banquet & Event Services, Room Dining & Amenities - Hotel Sagar Sonnet')

@push('styles')
<style>
  :root {
    --bnq-gold: #f59e0b;
    --bnq-gold-dark: #d97706;
    --bnq-gold-light: #fffbeb;
    --bnq-rose: #f43f5e;
    --bnq-emerald: #10b981;
    --bnq-indigo: #6366f1;
    --bnq-cyan: #06b6d4;
  }

  .banquet-container {
    padding: 16px 20px;
    background: #f1f5f9;
    min-height: calc(100vh - 110px);
  }

  /* Metric KPI Cards */
  .banquet-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
    margin-bottom: 16px;
  }

  .kpi-card-bnq {
    background: #ffffff;
    border-radius: 12px;
    padding: 14px 18px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  .kpi-card-bnq:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }

  .kpi-icon-bnq {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  .kpi-meta-bnq h4 {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 700;
    color: #64748b;
    margin: 0 0 2px 0;
    letter-spacing: 0.5px;
  }

  .kpi-meta-bnq .kpi-num {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    font-family: var(--font-mono, monospace);
  }

  /* Header Banner */
  .bnq-header-banner {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 20px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }

  .bnq-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .bnq-header-badge {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
  }

  .bnq-header-left h2 {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .bnq-header-left p {
    margin: 2px 0 0 0;
    font-size: 12px;
    color: #64748b;
  }

  .bnq-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  /* Segmented Nav Tabs */
  .bnq-nav-tabs {
    display: flex;
    gap: 6px;
    background: #e2e8f0;
    padding: 4px;
    border-radius: 10px;
    margin-bottom: 16px;
    width: fit-content;
  }

  .bnq-tab-btn {
    border: none;
    background: transparent;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.15s ease;
  }

  .bnq-tab-btn:hover {
    color: #0f172a;
  }

  .bnq-tab-btn.active {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }

  .bnq-tab-btn .badge-pill {
    padding: 2px 6px;
    font-size: 10px;
    border-radius: 12px;
    font-weight: 800;
  }

  /* Filter Toolbar */
  .bnq-filter-toolbar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
  }

  .bnq-search-box {
    min-width: 260px;
    position: relative;
    display: flex;
    align-items: center;
  }

  .bnq-search-box i {
    position: absolute;
    left: 12px;
    color: #94a3b8;
    font-size: 13px;
  }

  .bnq-search-box input {
    width: 100%;
    padding: 7px 12px 7px 34px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
  }

  .bnq-search-box input:focus {
    border-color: var(--bnq-gold);
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
  }

  /* Table styling */
  .bnq-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }

  .bnq-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
  }

  .bnq-table th {
    background: #f8fafc;
    padding: 10px 14px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #475569;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
    white-space: nowrap;
  }

  .bnq-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
  }

  .bnq-table tr:hover td {
    background: #f8fafc;
  }

  .room-tag-pill {
    background: #eef2ff;
    color: #4338ca;
    padding: 3px 8px;
    border-radius: 6px;
    font-family: var(--font-mono, monospace);
    font-weight: 800;
    font-size: 12px;
    border: 1px solid #c7d2fe;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .order-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 8px;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 700;
  }

  .order-status-badge.received { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
  .order-status-badge.preparing { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
  .order-status-badge.out { background: #fdf4ff; color: #c026d3; border: 1px solid #f5d0fe; }
  .order-status-badge.delivered { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
  .order-status-badge.cancelled { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }

  /* Hall Card Grid for Venues */
  .halls-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(290px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
  }

  .hall-venue-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.15s, box-shadow 0.15s;
  }

  .hall-venue-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
  }

  .hall-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 10px;
  }

  .hall-card-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.3;
  }

  .hall-meta-row {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 12px;
    color: #64748b;
    margin: 8px 0;
  }

  .hall-amenities-text {
    font-size: 11px;
    color: #475569;
    background: #f8fafc;
    padding: 8px 10px;
    border-radius: 8px;
    border: 1px solid #f1f5f9;
    margin: 10px 0;
    line-height: 1.4;
  }

  .hall-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
  }

  /* Modals */
  .bnq-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .bnq-modal-window {
    background: #ffffff;
    border-radius: 14px;
    width: 680px;
    max-width: 95vw;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    overflow: hidden;
    animation: bnqModalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  @keyframes bnqModalPop {
    from { opacity: 0; transform: scale(0.96); }
    to { opacity: 1; transform: scale(1); }
  }

  .bnq-modal-header {
    background: linear-gradient(135deg, #d97706, #b45309);
    color: #ffffff;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .bnq-modal-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .bnq-modal-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: #ffffff;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .bnq-modal-body {
    padding: 20px;
    max-height: calc(85vh - 120px);
    overflow-y: auto;
  }

  .bnq-modal-footer {
    padding: 12px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
  }

  .form-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
  }

  .form-group-bnq {
    margin-bottom: 12px;
  }

  .form-group-bnq label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    margin-bottom: 5px;
  }

  .form-group-bnq input,
  .form-group-bnq select,
  .form-group-bnq textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #0f172a;
    background: #ffffff;
    box-sizing: border-box;
  }

  .order-item-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    background: #f8fafc;
    padding: 8px 10px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
  }
</style>
@endpush

@section('content')
<main class="banquet-container">

  <!-- KPI Overview Cards -->
  <div class="banquet-kpi-grid">
    <div class="kpi-card-bnq">
      <div class="kpi-icon-bnq" style="background: #fffbeb; color: #d97706;">
        <i class="fa-solid fa-champagne-glasses"></i>
      </div>
      <div class="kpi-meta-bnq">
        <h4>Banquet Halls &amp; Lawns</h4>
        <div class="kpi-num">{{ $totalHalls }} <span style="font-size: 11px; color: #64748b; font-weight: 500;">Venues</span></div>
      </div>
    </div>

    <div class="kpi-card-bnq">
      <div class="kpi-icon-bnq" style="background: #eff6ff; color: #2563eb;">
        <i class="fa-solid fa-bell-concierge"></i>
      </div>
      <div class="kpi-meta-bnq">
        <h4>Active Room Orders</h4>
        <div class="kpi-num">{{ $activeRoomOrdersCount }} <span style="font-size: 11px; color: #64748b; font-weight: 500;">In Progress</span></div>
      </div>
    </div>

    <div class="kpi-card-bnq">
      <div class="kpi-icon-bnq" style="background: #fdf2f8; color: #db2777;">
        <i class="fa-solid fa-calendar-check"></i>
      </div>
      <div class="kpi-meta-bnq">
        <h4>Confirmed Events</h4>
        <div class="kpi-num">{{ $totalEventsBooked }} <span style="font-size: 11px; color: #64748b; font-weight: 500;">Bookings</span></div>
      </div>
    </div>

    <div class="kpi-card-bnq">
      <div class="kpi-icon-bnq" style="background: #ecfdf5; color: #059669;">
        <i class="fa-solid fa-indian-rupee-sign"></i>
      </div>
      <div class="kpi-meta-bnq">
        <h4>Services Revenue</h4>
        <div class="kpi-num" style="color: #059669;">₹ {{ number_format($totalServicesRevenue, 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Header Action Banner -->
  <div class="bnq-header-banner">
    <div class="bnq-header-left">
      <div class="bnq-header-badge">
        <i class="fa-solid fa-bell-concierge"></i>
      </div>
      <div>
        <h2>Banquet Halls, Event Catering &amp; In-Room Dining</h2>
        <p>Manage guest room service orders, kitchen dispatch, event halls &amp; function sheets</p>
      </div>
    </div>
    <div class="bnq-header-actions">
      <button type="button" class="btn-ui-primary" onclick="openNewRoomOrderModal()" style="background: #10b981; border-color: #10b981;">
        <i class="fa-solid fa-utensils"></i> New Room Order
      </button>
      <button type="button" class="btn-ui-primary" onclick="openNewBanquetModal()" style="background: #d97706; border-color: #d97706;">
        <i class="fa-solid fa-calendar-plus"></i> Book Event Hall
      </button>
    </div>
  </div>

  <!-- Segmented Tabs Navigation -->
  <div class="bnq-nav-tabs">
    <button type="button" class="bnq-tab-btn active" id="tab-btn-orders" onclick="switchBanquetTab('orders')">
      <i class="fa-solid fa-receipt"></i> Room Service Orders
      <span class="badge-pill" style="background: #ecfdf5; color: #059669;">{{ $roomOrders->count() }}</span>
    </button>
    <button type="button" class="bnq-tab-btn" id="tab-btn-halls" onclick="switchBanquetTab('halls')">
      <i class="fa-solid fa-champagne-glasses"></i> Banquet Halls &amp; Events
      <span class="badge-pill" style="background: #fffbeb; color: #d97706;">{{ $banquetBookings->count() }} Booked</span>
    </button>
    <button type="button" class="bnq-tab-btn" id="tab-btn-menu" onclick="switchBanquetTab('menu')">
      <i class="fa-solid fa-book-open"></i> In-Room Menu &amp; Services Catalog
      <span class="badge-pill" style="background: #f1f5f9; color: #64748b;">{{ $services->count() }}</span>
    </button>
  </div>

  <!-- TAB 1: ROOM SERVICE & AMENITY ORDERS -->
  <div id="bnq-tab-orders" class="bnq-tab-pane">
    <div class="bnq-filter-toolbar">
      <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; flex: 1;">
        <div class="bnq-search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="order-search-box" placeholder="Search Room #, Guest, Order ID..." onkeyup="filterOrdersTable()">
        </div>

        <select id="filter-order-status" style="padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 12px; font-weight: 600; color: #334155; background: #f8fafc;" onchange="filterOrdersTable()">
          <option value="all">All Order Statuses</option>
          <option value="Received">Received / New</option>
          <option value="Preparing">Preparing in Kitchen</option>
          <option value="Out for Delivery">Out for Delivery</option>
          <option value="Delivered">Delivered</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>

      <button type="button" class="btn-ui-primary" onclick="openNewRoomOrderModal()" style="background: #10b981; border-color: #10b981;">
        <i class="fa-solid fa-plus"></i> Place Room Order
      </button>
    </div>

    <!-- Orders Table -->
    <div class="bnq-table-card">
      <table class="bnq-table">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Room</th>
            <th>Guest Name</th>
            <th>Order Items &amp; Qty</th>
            <th style="text-align: right;">Total Bill</th>
            <th>Billing Mode</th>
            <th style="text-align: center;">Delivery Status</th>
            <th>Order Time</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody id="orders-table-body">
          @forelse($roomOrders as $ord)
            <tr data-order="{{ strtolower($ord->order_number) }}"
                data-room="{{ strtolower($ord->room_number ?? '') }}"
                data-guest="{{ strtolower($ord->guest_name ?? '') }}"
                data-status="{{ $ord->order_status }}">
              <td>
                <span style="font-family: var(--font-mono, monospace); font-weight: 700; color: #2563eb;">{{ $ord->order_number }}</span>
              </td>
              <td>
                <span class="room-tag-pill">
                  <i class="fa-solid fa-door-open" style="font-size: 10px;"></i>
                  Room {{ $ord->room_number }}
                </span>
              </td>
              <td>
                <div style="font-weight: 700; color: #0f172a;">{{ $ord->guest_name ?? 'In-House Guest' }}</div>
                @if($ord->booking?->settle_number)
                  <div style="font-size: 10px; color: #64748b;">Folio: {{ $ord->booking->settle_number }}</div>
                @endif
              </td>
              <td>
                <div style="font-size: 11px; color: #334155; line-height: 1.4;">
                  @foreach($ord->items as $it)
                    <div>&bull; <strong>{{ $it->quantity }}x</strong> {{ $it->item_name }}</div>
                  @endforeach
                </div>
              </td>
              <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 700; color: #0f172a;">
                ₹ {{ number_format($ord->total_amount, 2) }}
              </td>
              <td>
                <span style="font-size: 11px; font-weight: 600; color: {{ $ord->payment_status === 'Charged to Room Folio' ? '#6366f1' : '#059669' }};">
                  <i class="fa-solid {{ $ord->payment_status === 'Charged to Room Folio' ? 'fa-file-invoice-dollar' : 'fa-check' }}"></i>
                  {{ $ord->payment_status }}
                </span>
              </td>
              <td style="text-align: center;">
                @if($ord->order_status === 'Received')
                  <span class="order-status-badge received"><i class="fa-solid fa-bell"></i> Received</span>
                @elseif($ord->order_status === 'Preparing')
                  <span class="order-status-badge preparing"><i class="fa-solid fa-fire"></i> Preparing</span>
                @elseif($ord->order_status === 'Out for Delivery')
                  <span class="order-status-badge out"><i class="fa-solid fa-person-walking-luggage"></i> Out for Delivery</span>
                @elseif($ord->order_status === 'Delivered')
                  <span class="order-status-badge delivered"><i class="fa-solid fa-circle-check"></i> Delivered</span>
                @else
                  <span class="order-status-badge cancelled">Cancelled</span>
                @endif
              </td>
              <td style="font-size: 11px; color: #64748b; font-family: var(--font-mono, monospace);">
                {{ $ord->created_at->format('h:i A') }}
              </td>
              <td style="text-align: right;">
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 6px;">
                  <button type="button" class="btn-ui-secondary" style="padding: 4px 8px; font-size: 11px;" onclick="viewOrderDossier({{ $ord->id }})">
                    <i class="fa-solid fa-eye"></i> Details
                  </button>
                  @if($ord->order_status === 'Received')
                    <button type="button" class="btn-ui-primary" style="padding: 4px 8px; font-size: 11px; background: #f59e0b; border-color: #f59e0b;" onclick="updateOrderStatusAjax({{ $ord->id }}, 'Preparing')">
                      Prep
                    </button>
                  @elseif($ord->order_status === 'Preparing')
                    <button type="button" class="btn-ui-primary" style="padding: 4px 8px; font-size: 11px; background: #c026d3; border-color: #c026d3;" onclick="updateOrderStatusAjax({{ $ord->id }}, 'Out for Delivery')">
                      Dispatch
                    </button>
                  @elseif($ord->order_status === 'Out for Delivery')
                    <button type="button" class="btn-ui-primary" style="padding: 4px 8px; font-size: 11px; background: #10b981; border-color: #10b981;" onclick="updateOrderStatusAjax({{ $ord->id }}, 'Delivered')">
                      Delivered
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="text-align: center; padding: 30px; color: #64748b;">
                <i class="fa-solid fa-bell-concierge" style="font-size: 24px; margin-bottom: 8px; color: #cbd5e1; display: block;"></i>
                No room service orders placed today. Click <strong>"New Room Order"</strong> above to send food, beverages, or amenities directly to an in-house room.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- TAB 2: BANQUET HALLS & EVENT BOOKINGS -->
  <div id="bnq-tab-halls" class="bnq-tab-pane" style="display: none;">
    <!-- Available Venues Grid -->
    <div style="font-size: 13px; font-weight: 800; color: #0f172a; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
      <i class="fa-solid fa-building-columns" style="color: #d97706;"></i> Banquet Venues &amp; Conference Facilities
    </div>

    <div class="halls-grid">
      @foreach($halls as $hall)
        <div class="hall-venue-card">
          <div>
            <div class="hall-card-header">
              <h3>{{ $hall->name }}</h3>
              <span class="badge-tag {{ $hall->status === 'Available' ? 'green' : 'amber' }}">
                {{ $hall->status }}
              </span>
            </div>
            <div class="hall-meta-row">
              <span><i class="fa-solid fa-users" style="color: #6366f1;"></i> Up to <strong>{{ $hall->capacity_pax }} Pax</strong></span>
              <span><i class="fa-solid fa-layer-group" style="color: #06b6d4;"></i> {{ $hall->type }}</span>
            </div>
            <div class="hall-amenities-text">
              <i class="fa-solid fa-circle-check" style="color: #10b981;"></i> {{ $hall->amenities }}
            </div>
          </div>
          <div class="hall-card-footer">
            <div>
              <div style="font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 700;">Base Rental</div>
              <div style="font-family: var(--font-mono, monospace); font-weight: 800; color: #0f172a; font-size: 14px;">₹ {{ number_format($hall->base_tariff, 2) }}</div>
            </div>
            <button type="button" class="btn-ui-primary" style="background: #d97706; border-color: #d97706; padding: 6px 12px; font-size: 11px;" onclick="openBookSpecificHallModal({{ $hall->id }}, '{{ addslashes($hall->name) }}', {{ $hall->base_tariff }})">
              <i class="fa-solid fa-calendar-plus"></i> Reserve Hall
            </button>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Active Event Bookings Table -->
    <div class="bnq-filter-toolbar">
      <div style="font-weight: 800; color: #0f172a; font-size: 13px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-calendar-days" style="color: #d97706;"></i> Confirmed Event Reservations &amp; Function Sheets
      </div>
      <button type="button" class="btn-ui-primary" onclick="openNewBanquetModal()" style="background: #d97706; border-color: #d97706;">
        <i class="fa-solid fa-plus"></i> Book Event Hall
      </button>
    </div>

    <div class="bnq-table-card">
      <table class="bnq-table">
        <thead>
          <tr>
            <th>Ref. No</th>
            <th>Hall Name</th>
            <th>Event Name</th>
            <th>Client &amp; Contact</th>
            <th>Event Date &amp; Slot</th>
            <th style="text-align: center;">Pax</th>
            <th style="text-align: right;">Total Tariff</th>
            <th style="text-align: right;">Advance Paid</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($banquetBookings as $bk)
            <tr>
              <td><span style="font-family: var(--font-mono, monospace); font-weight: 700; color: #d97706;">{{ $bk->booking_reference }}</span></td>
              <td><strong>{{ $bk->hall?->name ?? 'Grand Ballroom' }}</strong></td>
              <td>
                <div style="font-weight: 700; color: #0f172a;">{{ $bk->event_name }}</div>
                <div style="font-size: 10px; color: #64748b;">{{ $bk->company_name ?? '' }}</div>
              </td>
              <td>
                <div style="font-weight: 600;">{{ $bk->client_name }}</div>
                <div style="font-size: 10px; color: #64748b;">{{ $bk->phone }}</div>
              </td>
              <td>
                <div style="font-weight: 700;">{{ $bk->event_date->format('d M Y') }}</div>
                <div style="font-size: 10px; color: #64748b;">{{ $bk->time_slot }}</div>
              </td>
              <td style="text-align: center; font-family: var(--font-mono, monospace); font-weight: 700;">{{ $bk->guaranteed_pax }}</td>
              <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 700; color: #0f172a;">₹ {{ number_format($bk->total_amount, 2) }}</td>
              <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 700; color: #059669;">₹ {{ number_format($bk->advance_paid, 2) }}</td>
              <td style="text-align: center;">
                <span class="badge-tag green"><i class="fa-solid fa-circle-check"></i> {{ $bk->status }}</span>
              </td>
              <td style="text-align: right;">
                <button type="button" class="btn-ui-secondary" style="padding: 5px 10px; font-size: 11px;" onclick="viewFunctionSheet({{ $bk->id }})">
                  <i class="fa-solid fa-file-lines"></i> Function Sheet
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" style="text-align: center; padding: 25px; color: #64748b;">
                No banquet event bookings recorded.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- TAB 3: IN-ROOM MENU & SERVICES CATALOG -->
  <div id="bnq-tab-menu" class="bnq-tab-pane" style="display: none;">
    <div class="bnq-filter-toolbar">
      <div style="font-weight: 800; color: #0f172a; font-size: 13px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-book-open" style="color: #0891b2;"></i> Available Room Dining Dishes &amp; Guest Amenities
      </div>
      <div style="font-size: 11px; color: #64748b;">
        Synchronized with Front Desk, Housekeeping and Room Service Kitchen
      </div>
    </div>

    <div class="bnq-table-card">
      <table class="bnq-table">
        <thead>
          <tr>
            <th style="width: 80px;">Code</th>
            <th>Service / Dish Name</th>
            <th>Category</th>
            <th>Unit / Serving</th>
            <th style="text-align: right;">Tariff / Price</th>
            <th>Description</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($services as $srv)
            <tr>
              <td><span style="font-family: var(--font-mono, monospace); font-weight: 700; color: #0891b2; background: #ecfeff; padding: 2px 6px; border-radius: 4px;">{{ $srv->code }}</span></td>
              <td><strong>{{ $srv->name }}</strong></td>
              <td><span class="badge-tag blue">{{ $srv->category }}</span></td>
              <td style="color: #64748b;">{{ $srv->unit }}</td>
              <td style="text-align: right; font-family: var(--font-mono, monospace); font-weight: 800; color: #0f172a;">₹ {{ number_format($srv->price, 2) }}</td>
              <td style="font-size: 11px; color: #475569; max-width: 300px;">{{ $srv->description }}</td>
              <td style="text-align: center;"><span class="badge-tag green">Available</span></td>
              <td style="text-align: right;">
                <button type="button" class="btn-ui-primary" style="padding: 4px 10px; font-size: 11px; background: #10b981; border-color: #10b981;" onclick="quickOrderThisService({{ $srv->id }}, '{{ addslashes($srv->name) }}', {{ $srv->price }})">
                  <i class="fa-solid fa-plus"></i> Order to Room
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</main>

<!-- MODAL 1: NEW ROOM SERVICE ORDER -->
<div class="bnq-modal-overlay" id="modal-new-room-order">
  <div class="bnq-modal-window" style="width: 720px;">
    <div class="bnq-modal-header" style="background: linear-gradient(135deg, #10b981, #059669);">
      <h3><i class="fa-solid fa-utensils"></i> In-Room Dining &amp; Service Order</h3>
      <button type="button" class="bnq-modal-close" onclick="closeBnqModal('modal-new-room-order')">&times;</button>
    </div>
    <form id="form-room-order" onsubmit="submitRoomOrder(event)">
      @csrf
      <div class="bnq-modal-body">
        <div class="form-grid-2">
          <div class="form-group-bnq">
            <label>Select In-House Room *</label>
            <select name="room_id" id="ro-room-select" required onchange="updateRoomOrderGuest()">
              <option value="">Choose Occupied Room</option>
              @foreach($activeRooms as $rm)
                @php
                  $occGuest = $rm->activeBooking?->guest?->guest_name ?? ($rm->status === 'Occupied' ? 'Occupied Guest' : 'Vacant Room');
                  $isOcc = $rm->status === 'Occupied' || $rm->activeBooking;
                @endphp
                <option value="{{ $rm->id }}" data-guest="{{ $occGuest }}" data-room="{{ $rm->room_number }}">
                  Room #{{ $rm->room_number }} ({{ $rm->categoryRelation?->name ?? 'Standard' }}) - {{ $occGuest }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group-bnq">
            <label>Guest Name</label>
            <input type="text" id="ro-guest-display" readonly placeholder="Guest name will load here" style="background: #f8fafc;">
          </div>
        </div>

        <div style="font-size: 12px; font-weight: 800; color: #0f172a; margin: 12px 0 8px 0; display: flex; justify-content: space-between; align-items: center;">
          <span><i class="fa-solid fa-bowl-food" style="color: #10b981;"></i> Order Line Items</span>
          <button type="button" class="btn-ui-secondary" style="font-size: 11px; padding: 3px 8px;" onclick="addOrderLineItem()">
            <i class="fa-solid fa-plus"></i> Add Item
          </button>
        </div>

        <!-- Dynamic Order Line Items -->
        <div id="order-line-items-container">
          <!-- Populated by JS -->
        </div>

        <div class="form-grid-2" style="margin-top: 14px;">
          <div class="form-group-bnq">
            <label>Billing &amp; Settlement *</label>
            <select name="payment_status" required>
              <option value="Charged to Room Folio">Charged to Room Folio (Bill at Checkout)</option>
              <option value="Paid Cash">Cash Paid upon Delivery</option>
              <option value="Paid UPI/Card">Digital UPI / POS Card Paid</option>
              <option value="Pending">Pending Collection</option>
            </select>
          </div>
          <div class="form-group-bnq">
            <label>Delivery Staff / Order Taker</label>
            <input type="text" name="served_by" placeholder="Server Name" value="Room Service Desk">
          </div>
        </div>

        <div class="form-group-bnq">
          <label>Kitchen / Preparation Instructions</label>
          <textarea name="special_instructions" rows="2" placeholder="e.g. Less spicy, send extra napkins & cutlery, deliver at 8:30 PM"></textarea>
        </div>

        <!-- Order Total Summary -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 11px; color: #64748b;">Subtotal + 5% F&amp;B Tax:</div>
            <div style="font-size: 16px; font-weight: 800; font-family: var(--font-mono, monospace); color: #0f172a;" id="ro-total-display">₹ 0.00</div>
          </div>
          <div style="font-size: 11px; font-weight: 700; color: #10b981;">
            <i class="fa-solid fa-clock"></i> Est. Delivery: 30-40 Mins
          </div>
        </div>
      </div>
      <div class="bnq-modal-footer">
        <button type="button" class="btn-ui-secondary" onclick="closeBnqModal('modal-new-room-order')">Cancel</button>
        <button type="submit" class="btn-ui-primary" style="background: #10b981; border-color: #10b981;">
          <i class="fa-solid fa-check"></i> Place Room Order
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 2: BOOK BANQUET HALL / EVENT -->
<div class="bnq-modal-overlay" id="modal-new-banquet">
  <div class="bnq-modal-window">
    <div class="bnq-modal-header">
      <h3><i class="fa-solid fa-calendar-plus"></i> Reserve Banquet Hall or Lawn</h3>
      <button type="button" class="bnq-modal-close" onclick="closeBnqModal('modal-new-banquet')">&times;</button>
    </div>
    <form id="form-banquet-booking" onsubmit="submitBanquetBooking(event)">
      @csrf
      <div class="bnq-modal-body">
        <div class="form-grid-2">
          <div class="form-group-bnq">
            <label>Select Banquet Hall / Venue *</label>
            <select name="banquet_hall_id" id="bq-hall-select" required onchange="calculateBanquetTariff()">
              <option value="">Select Venue</option>
              @foreach($halls as $hl)
                <option value="{{ $hl->id }}" data-tariff="{{ $hl->base_tariff }}">
                  {{ $hl->name }} (Up to {{ $hl->capacity_pax }} Pax)
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group-bnq">
            <label>Event Name / Occasion *</label>
            <input type="text" name="event_name" required placeholder="e.g. Annual Gala, Wedding, Conference">
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-group-bnq">
            <label>Client / Host Name *</label>
            <input type="text" name="client_name" required placeholder="Full Name">
          </div>
          <div class="form-group-bnq">
            <label>Company / Organization</label>
            <input type="text" name="company_name" placeholder="Optional Company Name">
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-group-bnq">
            <label>Contact Phone *</label>
            <input type="text" name="phone" required placeholder="+91 XXXXX XXXXX">
          </div>
          <div class="form-group-bnq">
            <label>Contact Email</label>
            <input type="email" name="email" placeholder="client@example.com">
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-group-bnq">
            <label>Event Date *</label>
            <input type="date" name="event_date" required value="{{ date('Y-m-d', strtotime('+3 days')) }}">
          </div>
          <div class="form-group-bnq">
            <label>Time Slot *</label>
            <select name="time_slot" required>
              <option value="Evening (6:30 PM - 11:30 PM)">Evening (6:30 PM - 11:30 PM)</option>
              <option value="Morning (9:00 AM - 2:00 PM)">Morning (9:00 AM - 2:00 PM)</option>
              <option value="Afternoon (1:00 PM - 5:00 PM)">Afternoon (1:00 PM - 5:00 PM)</option>
              <option value="Full Day (9:00 AM - 10:00 PM)">Full Day (9:00 AM - 10:00 PM)</option>
            </select>
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-group-bnq">
            <label>Guaranteed Guest Count (Pax) *</label>
            <input type="number" name="guaranteed_pax" id="bq-pax" required min="10" value="100" onkeyup="calculateBanquetTariff()" onchange="calculateBanquetTariff()">
          </div>
          <div class="form-group-bnq">
            <label>Catering Plate Rate (₹)</label>
            <input type="number" name="catering_per_plate" id="bq-plate" placeholder="e.g. 1200" value="1000" onkeyup="calculateBanquetTariff()" onchange="calculateBanquetTariff()">
          </div>
        </div>

        <div class="form-grid-2">
          <div class="form-group-bnq">
            <label>Base Hall Rental (₹) *</label>
            <input type="number" name="hall_rental" id="bq-rental" required value="50000" onkeyup="calculateBanquetTariff()" onchange="calculateBanquetTariff()">
          </div>
          <div class="form-group-bnq">
            <label>Advance Payment Paid (₹)</label>
            <input type="number" name="advance_paid" id="bq-advance" value="25000" onkeyup="calculateBanquetTariff()" onchange="calculateBanquetTariff()">
          </div>
        </div>

        <div class="form-group-bnq">
          <label>Function Sheet &amp; Audio-Visual Notes</label>
          <textarea name="function_sheet_notes" rows="2" placeholder="Stage setup, buffet menu, projectors, welcome drinks..."></textarea>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 11px; color: #64748b;">Calculated Total Event Estimate:</div>
            <div style="font-size: 16px; font-weight: 800; font-family: var(--font-mono, monospace); color: #d97706;" id="bq-total-display">₹ 0.00</div>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 11px; color: #64748b;">Balance Due at Event:</div>
            <div style="font-size: 13px; font-weight: 700; font-family: var(--font-mono, monospace); color: #0f172a;" id="bq-balance-display">₹ 0.00</div>
          </div>
        </div>
      </div>
      <div class="bnq-modal-footer">
        <button type="button" class="btn-ui-secondary" onclick="closeBnqModal('modal-new-banquet')">Cancel</button>
        <button type="submit" class="btn-ui-primary" style="background: #d97706; border-color: #d97706;">Confirm Reservation</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 3: VIEW ORDER DOSSIER -->
<div class="bnq-modal-overlay" id="modal-order-dossier">
  <div class="bnq-modal-window">
    <div class="bnq-modal-header" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
      <h3 id="dossier-order-title"><i class="fa-solid fa-receipt"></i> Room Order Dossier</h3>
      <button type="button" class="bnq-modal-close" onclick="closeBnqModal('modal-order-dossier')">&times;</button>
    </div>
    <div class="bnq-modal-body" id="dossier-order-content">
      <!-- Loaded dynamically -->
    </div>
    <div class="bnq-modal-footer">
      <button type="button" class="btn-ui-secondary" onclick="closeBnqModal('modal-order-dossier')">Close</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Services catalog JSON for order builder
  const servicesCatalog = @json($services);

  // Tab switching
  function switchBanquetTab(tabName) {
    document.querySelectorAll('.bnq-tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.bnq-tab-btn').forEach(btn => btn.classList.remove('active'));

    const targetPane = document.getElementById('bnq-tab-' + tabName);
    const targetBtn = document.getElementById('tab-btn-' + tabName);

    if (targetPane) targetPane.style.display = 'block';
    if (targetBtn) targetBtn.classList.add('active');
  }

  // Modal handlers
  function openBnqModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'flex';
  }

  function closeBnqModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  }

  function openNewRoomOrderModal() {
    document.getElementById('form-room-order').reset();
    document.getElementById('order-line-items-container').innerHTML = '';
    // add first 2 default rows
    addOrderLineItem(servicesCatalog[0]?.id || null);
    addOrderLineItem(servicesCatalog[1]?.id || null);
    calculateRoomOrderTotal();
    openBnqModal('modal-new-room-order');
  }

  function openNewBanquetModal() {
    document.getElementById('form-banquet-booking').reset();
    calculateBanquetTariff();
    openBnqModal('modal-new-banquet');
  }

  function openBookSpecificHallModal(hallId, hallName, baseTariff) {
    openNewBanquetModal();
    const sel = document.getElementById('bq-hall-select');
    sel.value = hallId;
    document.getElementById('bq-rental').value = baseTariff;
    calculateBanquetTariff();
  }

  function quickOrderThisService(serviceId, name, price) {
    openNewRoomOrderModal();
    document.getElementById('order-line-items-container').innerHTML = '';
    addOrderLineItem(serviceId);
    calculateRoomOrderTotal();
  }

  function updateRoomOrderGuest() {
    const select = document.getElementById('ro-room-select');
    const opt = select.options[select.selectedIndex];
    const guestInput = document.getElementById('ro-guest-display');
    if (opt && opt.value) {
      guestInput.value = opt.getAttribute('data-guest') || 'In-House Guest';
    } else {
      guestInput.value = '';
    }
  }

  // Order Line Items Builder
  let itemCounter = 0;
  function addOrderLineItem(selectedServiceId = null) {
    itemCounter++;
    const container = document.getElementById('order-line-items-container');
    const row = document.createElement('div');
    row.className = 'order-item-row';
    row.id = `item-row-${itemCounter}`;

    let optionsHtml = '<option value="">Custom Food / Amenity Item</option>';
    servicesCatalog.forEach(s => {
      const isSelected = selectedServiceId && s.id == selectedServiceId ? 'selected' : '';
      optionsHtml += `<option value="${s.id}" data-price="${s.price}" data-name="${s.name}" ${isSelected}>[${s.code}] ${s.name} (₹ ${s.price})</option>`;
    });

    row.innerHTML = `
      <div style="flex: 2;">
        <select class="item-service-select" onchange="onServiceSelectChange(${itemCounter})" style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; width: 100%;">
          ${optionsHtml}
        </select>
        <input type="hidden" name="items[${itemCounter}][service_id]" id="input-service-id-${itemCounter}" value="${selectedServiceId || ''}">
        <input type="text" name="items[${itemCounter}][name]" id="input-item-name-${itemCounter}" placeholder="Item description" style="padding: 5px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; width: 100%; margin-top: 4px; display: none;">
      </div>
      <div style="width: 80px;">
        <input type="number" step="0.01" name="items[${itemCounter}][price]" id="input-item-price-${itemCounter}" placeholder="Price" value="0.00" onkeyup="calculateRoomOrderTotal()" onchange="calculateRoomOrderTotal()" style="padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; width: 100%; font-family: var(--font-mono, monospace); font-weight: 700;">
      </div>
      <div style="width: 60px;">
        <input type="number" name="items[${itemCounter}][quantity]" id="input-item-qty-${itemCounter}" value="1" min="1" onkeyup="calculateRoomOrderTotal()" onchange="calculateRoomOrderTotal()" style="padding: 6px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; width: 100%; font-family: var(--font-mono, monospace); font-weight: 700;">
      </div>
      <button type="button" onclick="removeOrderLineItem(${itemCounter})" style="background: #fff1f2; border: 1px solid #fecdd3; color: #e11d48; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
        <i class="fa-solid fa-trash-can" style="font-size: 11px;"></i>
      </button>
    `;

    container.appendChild(row);

    if (selectedServiceId) {
      onServiceSelectChange(itemCounter);
    }
  }

  function removeOrderLineItem(index) {
    const row = document.getElementById(`item-row-${index}`);
    if (row) row.remove();
    calculateRoomOrderTotal();
  }

  function onServiceSelectChange(index) {
    const row = document.getElementById(`item-row-${index}`);
    const select = row.querySelector('.item-service-select');
    const selectedOpt = select.options[select.selectedIndex];
    const nameInput = document.getElementById(`input-item-name-${index}`);
    const priceInput = document.getElementById(`input-item-price-${index}`);
    const serviceIdInput = document.getElementById(`input-service-id-${index}`);

    if (selectedOpt && selectedOpt.value) {
      nameInput.value = selectedOpt.getAttribute('data-name');
      priceInput.value = selectedOpt.getAttribute('data-price');
      serviceIdInput.value = selectedOpt.value;
      nameInput.style.display = 'none';
    } else {
      nameInput.style.display = 'block';
      serviceIdInput.value = '';
    }

    calculateRoomOrderTotal();
  }

  function calculateRoomOrderTotal() {
    let subtotal = 0;
    const rows = document.querySelectorAll('.order-item-row');
    rows.forEach(r => {
      const price = parseFloat(r.querySelector('[id^="input-item-price-"]').value) || 0;
      const qty = parseInt(r.querySelector('[id^="input-item-qty-"]').value) || 1;
      subtotal += (price * qty);
    });

    const tax = subtotal * 0.05;
    const total = subtotal + tax;

    document.getElementById('ro-total-display').innerText = `₹ ${total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  function calculateBanquetTariff() {
    const hallSelect = document.getElementById('bq-hall-select');
    const opt = hallSelect.options[hallSelect.selectedIndex];
    if (opt && opt.value && !document.getElementById('bq-rental').value) {
      document.getElementById('bq-rental').value = opt.getAttribute('data-tariff') || 50000;
    }

    const rental = parseFloat(document.getElementById('bq-rental').value) || 0;
    const pax = parseInt(document.getElementById('bq-pax').value) || 0;
    const plate = parseFloat(document.getElementById('bq-plate').value) || 0;
    const advance = parseFloat(document.getElementById('bq-advance').value) || 0;

    const total = rental + (pax * plate);
    const balance = Math.max(0, total - advance);

    document.getElementById('bq-total-display').innerText = `₹ ${total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    document.getElementById('bq-balance-display').innerText = `₹ ${balance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  // Filter orders
  function filterOrdersTable() {
    const q = document.getElementById('order-search-box').value.toLowerCase().trim();
    const st = document.getElementById('filter-order-status').value;

    const rows = document.querySelectorAll('#orders-table-body tr[data-order]');
    rows.forEach(r => {
      const order = r.getAttribute('data-order') || '';
      const room = r.getAttribute('data-room') || '';
      const guest = r.getAttribute('data-guest') || '';
      const status = r.getAttribute('data-status') || '';

      const matchQ = !q || order.includes(q) || room.includes(q) || guest.includes(q);
      const matchSt = st === 'all' || status === st;

      r.style.display = (matchQ && matchSt) ? '' : 'none';
    });
  }

  // AJAX Submissions
  async function submitRoomOrder(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
      const response = await fetch("{{ route('banquet.room-orders.store') }}", {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: formData
      });

      const res = await response.json();
      if (response.ok && res.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        } else {
          alert(res.message);
        }
        closeBnqModal('modal-new-room-order');
        setTimeout(() => location.reload(), 800);
      } else {
        const msg = res.message || (res.errors ? Object.values(res.errors).flat().join('<br>') : 'Error placing room order.');
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(msg, 'error');
        } else {
          alert(msg);
        }
      }
    } catch (err) {
      console.error(err);
      alert('Network error.');
    }
  }

  async function submitBanquetBooking(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
      const response = await fetch("{{ route('banquet.bookings.store') }}", {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: formData
      });

      const res = await response.json();
      if (response.ok && res.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        } else {
          alert(res.message);
        }
        closeBnqModal('modal-new-banquet');
        setTimeout(() => location.reload(), 800);
      } else {
        const msg = res.message || (res.errors ? Object.values(res.errors).flat().join('<br>') : 'Error creating event booking.');
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(msg, 'error');
        } else {
          alert(msg);
        }
      }
    } catch (err) {
      console.error(err);
      alert('Network error.');
    }
  }

  async function updateOrderStatusAjax(orderId, newStatus) {
    try {
      const response = await fetch(`/banquet/room-orders/${orderId}/status`, {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ order_status: newStatus })
      });

      const res = await response.json();
      if (response.ok && res.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        } else {
          alert(res.message);
        }
        setTimeout(() => location.reload(), 600);
      } else {
        alert(res.message || 'Error updating order status.');
      }
    } catch (err) {
      console.error(err);
      alert('Network error.');
    }
  }

  async function viewOrderDossier(id) {
    try {
      const res = await fetch(`/banquet/room-orders/${id}`);
      const data = await res.json();
      if (data.success && data.order) {
        const o = data.order;
        document.getElementById('dossier-order-title').innerHTML = `<i class="fa-solid fa-receipt"></i> Order #${o.order_number} (Room ${o.room_number})`;

        let itemsHtml = '';
        o.items.forEach(it => {
          itemsHtml += `
            <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px;">
              <div><strong>${it.quantity}x</strong> ${it.item_name}</div>
              <div style="font-family: var(--font-mono, monospace); font-weight: 700;">₹ ${parseFloat(it.line_total).toFixed(2)}</div>
            </div>
          `;
        });

        document.getElementById('dossier-order-content').innerHTML = `
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 14px; font-size: 12px;">
            <div><strong>Guest:</strong> ${o.guest_name}</div>
            <div><strong>Room #:</strong> ${o.room_number}</div>
            <div><strong>Billing:</strong> ${o.payment_status}</div>
            <div><strong>Status:</strong> ${o.order_status}</div>
            <div style="grid-column: 1 / -1;"><strong>Special Instructions:</strong> ${o.special_instructions || 'None'}</div>
          </div>
          <div style="font-weight: 800; font-size: 12px; margin-bottom: 8px; color: #0f172a;">Order Items</div>
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; margin-bottom: 12px;">
            ${itemsHtml}
            <div style="display: flex; justify-content: space-between; padding-top: 8px; font-weight: 800; font-size: 13px; color: #0f172a;">
              <div>Total (inc. taxes):</div>
              <div style="font-family: var(--font-mono, monospace); color: #059669;">₹ ${parseFloat(o.total_amount).toFixed(2)}</div>
            </div>
          </div>
        `;

        openBnqModal('modal-order-dossier');
      }
    } catch (err) {
      console.error(err);
      alert('Unable to load order dossier.');
    }
  }

  async function viewFunctionSheet(id) {
    try {
      const res = await fetch(`/banquet/bookings/${id}/function-sheet`);
      const data = await res.json();
      if (data.success && data.booking) {
        const b = data.booking;
        document.getElementById('dossier-order-title').innerHTML = `<i class="fa-solid fa-file-lines"></i> Function Sheet - ${b.event_name}`;

        document.getElementById('dossier-order-content').innerHTML = `
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 14px; font-size: 12px;">
            <div><strong>Venue:</strong> ${b.hall ? b.hall.name : 'Grand Hall'}</div>
            <div><strong>Client:</strong> ${b.client_name} (${b.phone || '-'})</div>
            <div><strong>Event Date:</strong> ${b.event_date}</div>
            <div><strong>Time Slot:</strong> ${b.time_slot}</div>
            <div><strong>Pax Capacity:</strong> ${b.guaranteed_pax} Guests</div>
            <div><strong>Status:</strong> ${b.status}</div>
          </div>
          <div style="font-weight: 800; font-size: 12px; margin-bottom: 8px; color: #0f172a;">Event Setup &amp; AV Notes</div>
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; font-size: 12px; line-height: 1.5; color: #334155; margin-bottom: 12px;">
            ${b.function_sheet_notes || 'Standard banquet cluster setup with audio-visual system and stage lighting.'}
          </div>
          <div style="display: flex; justify-content: space-between; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 14px; font-size: 12px; font-weight: 700;">
            <div>Total Amount: <strong>₹ ${parseFloat(b.total_amount).toFixed(2)}</strong></div>
            <div style="color: #059669;">Advance Paid: <strong>₹ ${parseFloat(b.advance_paid).toFixed(2)}</strong></div>
            <div style="color: #d97706;">Balance Due: <strong>₹ ${parseFloat(b.balance_due).toFixed(2)}</strong></div>
          </div>
        `;

        openBnqModal('modal-order-dossier');
      }
    } catch (err) {
      console.error(err);
      alert('Unable to load function sheet.');
    }
  }
</script>
@endpush
