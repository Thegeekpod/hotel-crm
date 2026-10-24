@extends('frontoffice.layouts.app')

@section('title', 'Extra Services & In-Room Dining - Hotel Sagar Sonnet')

@push('styles')
<style>
  :root {
    --srv-emerald: #10b981;
    --srv-indigo: #6366f1;
    --srv-amber: #f59e0b;
    --srv-rose: #f43f5e;
    --srv-cyan: #06b6d4;
  }

  .services-container {
    padding: 16px 20px;
    background: #f1f5f9;
    min-height: calc(100vh - 110px);
  }

  /* Metric KPI Cards */
  .services-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 14px;
    margin-bottom: 16px;
  }

  .kpi-card-srv {
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

  .kpi-card-srv:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }

  .kpi-icon-srv {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  .kpi-meta-srv h4 {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 700;
    color: #64748b;
    margin: 0 0 2px 0;
    letter-spacing: 0.5px;
  }

  .kpi-meta-srv .kpi-num {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    font-family: var(--font-mono, monospace);
  }

  /* Header Banner */
  .srv-header-banner {
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

  .srv-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .srv-header-badge {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
  }

  .srv-header-left h2 {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .srv-header-left p {
    margin: 2px 0 0 0;
    font-size: 12px;
    color: #64748b;
  }

  .srv-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  /* Segmented Nav Tabs */
  .srv-nav-tabs {
    display: flex;
    gap: 6px;
    background: #e2e8f0;
    padding: 4px;
    border-radius: 10px;
    margin-bottom: 16px;
    width: fit-content;
  }

  .srv-tab-btn {
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

  .srv-tab-btn:hover {
    color: #0f172a;
  }

  .srv-tab-btn.active {
    background: #ffffff;
    color: #0f172a;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }

  .srv-tab-btn .badge-pill {
    padding: 2px 7px;
    font-size: 10px;
    border-radius: 12px;
    font-weight: 800;
  }

  /* Filter Toolbar */
  .srv-filter-toolbar {
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

  .srv-search-box {
    min-width: 260px;
    position: relative;
    display: flex;
    align-items: center;
  }

  .srv-search-box i {
    position: absolute;
    left: 12px;
    color: #94a3b8;
    font-size: 13px;
  }

  .srv-search-box input {
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

  .srv-search-box input:focus {
    border-color: #10b981;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
  }

  /* Table styling */
  .srv-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow-x: auto;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }

  .srv-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    min-width: 850px;
  }

  .srv-table th {
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

  .srv-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    color: #1e293b;
    vertical-align: middle;
  }

  .srv-table tr:hover td {
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

  /* Modals */
  .srv-modal-overlay {
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

  .srv-modal-window {
    background: #ffffff;
    border-radius: 14px;
    width: 680px;
    max-width: 95vw;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    overflow: hidden;
    animation: srvModalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  @keyframes srvModalPop {
    from { opacity: 0; transform: scale(0.96); }
    to { opacity: 1; transform: scale(1); }
  }

  .srv-modal-header {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #ffffff;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .srv-modal-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .srv-modal-close {
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

  .srv-modal-body {
    padding: 20px;
    max-height: calc(85vh - 120px);
    overflow-y: auto;
  }

  .srv-modal-footer {
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

  .form-group-srv {
    margin-bottom: 12px;
  }

  .form-group-srv label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    margin-bottom: 5px;
  }

  .form-group-srv input,
  .form-group-srv select,
  .form-group-srv textarea {
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

  /* Select2 Custom Styles for PMS */
  .select2-container {
    width: 100% !important;
  }
  .select2-container--default .select2-selection--single {
    height: 38px !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 8px !important;
    background-color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 4px !important;
    transition: all 0.2s ease !important;
  }
  .select2-container--default .select2-selection--single:focus,
  .select2-container--default.select2-container--focus .select2-selection--single,
  .select2-container--default.select2-container--open .select2-selection--single {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
    outline: none !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #0f172a !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    line-height: 36px !important;
    padding-left: 8px !important;
    padding-right: 24px !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
    right: 8px !important;
  }
  .select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: #64748b transparent transparent transparent !important;
  }
  .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
    border-color: transparent transparent #64748b transparent !important;
  }
  .select2-dropdown {
    border: 1px solid #cbd5e1 !important;
    border-radius: 10px !important;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15) !important;
    z-index: 1000000 !important;
    background: #ffffff !important;
    overflow: hidden !important;
  }
  .select2-search--dropdown {
    padding: 8px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #f1f5f9 !important;
  }
  .select2-search--dropdown .select2-search__field {
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    padding: 6px 10px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    outline: none !important;
  }
  .select2-search--dropdown .select2-search__field:focus {
    border-color: #10b981 !important;
  }
  .select2-results__option {
    padding: 8px 12px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #1e293b !important;
  }
  .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #10b981 !important;
    color: #ffffff !important;
  }
  .select2-container--default .select2-results__option[aria-selected="true"] {
    background-color: #ecfdf5 !important;
    color: #065f46 !important;
    font-weight: 700 !important;
  }
</style>
@endpush

@section('content')
<main class="services-container">

  <!-- KPI Overview Cards -->
  <div class="services-kpi-grid">
    <div class="kpi-card-srv">
      <div class="kpi-icon-srv" style="background: #ecfdf5; color: #059669;">
        <i class="fa-solid fa-bell-concierge"></i>
      </div>
      <div class="kpi-meta-srv">
        <h4>Available Services</h4>
        <div class="kpi-num">{{ $totalServicesCount }} <span style="font-size: 11px; color: #64748b; font-weight: 500;">Active Items</span></div>
      </div>
    </div>

    <div class="kpi-card-srv">
      <div class="kpi-icon-srv" style="background: #eff6ff; color: #2563eb;">
        <i class="fa-solid fa-fire"></i>
      </div>
      <div class="kpi-meta-srv">
        <h4>Active Room Orders</h4>
        <div class="kpi-num">{{ $activeRoomOrdersCount }} <span style="font-size: 11px; color: #64748b; font-weight: 500;">In Progress</span></div>
      </div>
    </div>

    <div class="kpi-card-srv">
      <div class="kpi-icon-srv" style="background: #f0fdf4; color: #16a34a;">
        <i class="fa-solid fa-circle-check"></i>
      </div>
      <div class="kpi-meta-srv">
        <h4>Delivered Orders</h4>
        <div class="kpi-num">{{ $deliveredOrdersCount }} <span style="font-size: 11px; color: #64748b; font-weight: 500;">Completed</span></div>
      </div>
    </div>

    <div class="kpi-card-srv">
      <div class="kpi-icon-srv" style="background: #fffbeb; color: #d97706;">
        <i class="fa-solid fa-indian-rupee-sign"></i>
      </div>
      <div class="kpi-meta-srv">
        <h4>Services Revenue</h4>
        <div class="kpi-num" style="color: #059669;">₹ {{ number_format($totalServicesRevenue, 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Header Action Banner -->
  <div class="srv-header-banner">
    <div class="srv-header-left">
      <div class="srv-header-badge">
        <i class="fa-solid fa-bell-concierge"></i>
      </div>
      <div>
        <h2>Extra Services &amp; In-Room Dining</h2>
        <p>Manage guest room service orders, food dishes, laundry tariffs, and room amenities.</p>
      </div>
    </div>
    <!-- <div class="srv-header-actions">
      <a href="{{ route('admin.services.index') }}" class="btn-ui-secondary" style="text-decoration: none;">
        <i class="fa-solid fa-sliders"></i> Manage Catalog (Admin)
      </a>
      <button type="button" class="btn-ui-primary" onclick="openNewRoomOrderModal()" style="background: #10b981; border-color: #10b981;">
        <i class="fa-solid fa-utensils"></i> Place Room Order
      </button>
    </div> -->
  </div>

  <!-- Segmented Nav Tabs -->
  <div class="srv-nav-tabs">
    <button type="button" class="srv-tab-btn active" id="tab-btn-orders" onclick="switchServicesTab('orders')">
      <i class="fa-solid fa-receipt"></i> Room Orders 
      <span class="badge-pill" style="background: #dbeafe; color: #1e40af;">{{ count($roomOrders) }}</span>
    </button>
    <button type="button" class="srv-tab-btn" id="tab-btn-menu" onclick="switchServicesTab('menu')">
      <i class="fa-solid fa-book-open"></i> Services Catalog
      <span class="badge-pill" style="background: #e0e7ff; color: #3730a3;">{{ count($services) }}</span>
    </button>
  </div>

  <!-- TAB 1: ROOM SERVICE & AMENITY ORDERS -->
  <div id="srv-tab-orders" class="srv-tab-pane">
    <div class="srv-filter-toolbar">
      <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; flex: 1;">
        <div class="srv-search-box">
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
    <div class="srv-table-card">
      <table class="srv-table">
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
                No room service orders placed today. Click <strong>"Place Room Order"</strong> above to send food, beverages, or amenities directly to an in-house room.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- TAB 2: IN-ROOM MENU & SERVICES CATALOG -->
  <div id="srv-tab-menu" class="srv-tab-pane" style="display: none;">
    <div class="srv-filter-toolbar">
      <div style="font-weight: 800; color: #0f172a; font-size: 13px; display: flex; align-items: center; gap: 8px;">
        <i class="fa-solid fa-book-open" style="color: #0891b2;"></i> Available Room Dining Dishes &amp; Guest Amenities
      </div>
      <div style="display: flex; align-items: center; gap: 10px;">
        <a href="{{ route('admin.services.index') }}" class="btn-ui-secondary" style="font-size: 11px; padding: 4px 10px; text-decoration: none;">
          <i class="fa-solid fa-plus"></i> Add New in Admin
        </a>
      </div>
    </div>

    <div class="srv-table-card">
      <table class="srv-table">
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
              <td style="font-size: 11px; color: #475569; max-width: 300px;">{{ $srv->description ?: '-' }}</td>
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
<div class="srv-modal-overlay" id="modal-new-room-order">
  <div class="srv-modal-window" style="width: 720px;">
    <div class="srv-modal-header" style="background: linear-gradient(135deg, #10b981, #059669);">
      <h3><i class="fa-solid fa-utensils"></i> In-Room Dining &amp; Service Order</h3>
      <button type="button" class="srv-modal-close" onclick="closeSrvModal('modal-new-room-order')">&times;</button>
    </div>
    <form id="form-room-order" onsubmit="submitRoomOrder(event)">
      @csrf
      <div class="srv-modal-body">
        <div class="form-grid-2">
          <div class="form-group-srv">
            <label>Select In-House Room *</label>
            <select name="room_id" id="ro-room-select" required style="width: 100%;">
              <option value="">Choose Occupied Room</option>
              @foreach($activeRooms as $rm)
                @php
                  $occGuest = $rm->activeBooking?->guest?->guest_name ?? ($rm->status === 'Occupied' ? 'Occupied Guest' : 'Vacant Room');
                @endphp
                <option value="{{ $rm->id }}" data-guest="{{ $occGuest }}" data-room="{{ $rm->room_number }}">
                  Room #{{ $rm->room_number }} ({{ $rm->categoryRelation?->name ?? 'Standard' }}) - {{ $occGuest }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="form-group-srv">
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
          <div class="form-group-srv">
            <label>Billing &amp; Settlement *</label>
            <select name="payment_status" id="ro-payment-status" required style="width: 100%;">
              <option value="Charged to Room Folio">Charged to Room Folio (Bill at Checkout)</option>
              <option value="Paid Cash">Cash Paid upon Delivery</option>
              <option value="Paid UPI/Card">Digital UPI / POS Card Paid</option>
              <option value="Pending">Pending Collection</option>
            </select>
          </div>
          <div class="form-group-srv">
            <label>Delivery Staff / Order Taker</label>
            <input type="text" name="served_by" placeholder="Server Name" value="Room Service Desk">
          </div>
        </div>

        <div class="form-group-srv">
          <label>Kitchen / Delivery Instructions</label>
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
      <div class="srv-modal-footer">
        <button type="button" class="btn-ui-secondary" onclick="closeSrvModal('modal-new-room-order')">Cancel</button>
        <button type="submit" class="btn-ui-primary" id="btn-submit-order" style="background: #10b981; border-color: #10b981;">
          <i class="fa-solid fa-check"></i> Place Room Order
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 2: VIEW ORDER DOSSIER -->
<div class="srv-modal-overlay" id="modal-order-dossier">
  <div class="srv-modal-window">
    <div class="srv-modal-header" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
      <h3 id="dossier-order-title"><i class="fa-solid fa-receipt"></i> Room Order Dossier</h3>
      <button type="button" class="srv-modal-close" onclick="closeSrvModal('modal-order-dossier')">&times;</button>
    </div>
    <div class="srv-modal-body" id="dossier-order-content">
      <!-- Loaded dynamically -->
    </div>
    <div class="srv-modal-footer">
      <button type="button" class="btn-ui-secondary" onclick="closeSrvModal('modal-order-dossier')">Close</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const servicesCatalog = @json($services);
  const orderStoreUrl = "{{ route('frontoffice.services.orders.store') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

  // Tab switching
  function switchServicesTab(tabName) {
    document.querySelectorAll('.srv-tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.srv-tab-btn').forEach(btn => btn.classList.remove('active'));

    const targetPane = document.getElementById('srv-tab-' + tabName);
    const targetBtn = document.getElementById('tab-btn-' + tabName);

    if (targetPane) targetPane.style.display = 'block';
    if (targetBtn) targetBtn.classList.add('active');
  }

  // Modal handlers
  function openSrvModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'flex';
  }

  function closeSrvModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  }

  function openNewRoomOrderModal(initialServiceId = null) {
    document.getElementById('form-room-order').reset();
    document.getElementById('order-line-items-container').innerHTML = '';
    
    // Reset Select2 fields
    if ($('#ro-room-select').data('select2')) {
      $('#ro-room-select').val('').trigger('change');
    }
    if ($('#ro-payment-status').data('select2')) {
      $('#ro-payment-status').val('Charged to Room Folio').trigger('change');
    }
    document.getElementById('ro-guest-display').value = '';

    if (initialServiceId) {
      addOrderLineItem(initialServiceId);
    } else if (servicesCatalog.length > 0) {
      addOrderLineItem(servicesCatalog[0].id);
    } else {
      addOrderLineItem(null);
    }
    calculateRoomOrderTotal();
    openSrvModal('modal-new-room-order');
  }

  function quickOrderThisService(serviceId, name, price) {
    openNewRoomOrderModal(serviceId);
  }

  function updateRoomOrderGuest() {
    const select = document.getElementById('ro-room-select');
    if (!select || select.selectedIndex < 0) return;
    const opt = select.options[select.selectedIndex];
    const guestInput = document.getElementById('ro-guest-display');
    if (opt && opt.value) {
      guestInput.value = opt.getAttribute('data-guest') || 'In-House Guest';
    } else {
      guestInput.value = '';
    }
  }

  // Order Line Items Builder with Select2
  let itemCounter = 0;
  function addOrderLineItem(selectedServiceId = null) {
    itemCounter++;
    const container = document.getElementById('order-line-items-container');
    const row = document.createElement('div');
    row.className = 'order-item-row';
    row.id = `item-row-${itemCounter}`;

    let optionsHtml = '<option value="">-- Search &amp; Choose Hotel Service / Food Item --</option>';
    servicesCatalog.forEach(s => {
      const isSelected = selectedServiceId && String(s.id) === String(selectedServiceId) ? 'selected' : '';
      optionsHtml += `<option value="${s.id}" data-price="${s.price}" data-name="${s.name}" ${isSelected}>[${s.code || 'SRV'}] ${s.name} (₹ ${parseFloat(s.price).toFixed(2)})</option>`;
    });

    row.innerHTML = `
      <div style="flex: 2; min-width: 0;">
        <select class="item-service-select" id="item-service-select-${itemCounter}" style="width: 100%;">
          ${optionsHtml}
        </select>
        <input type="hidden" name="items[${itemCounter}][service_id]" id="input-service-id-${itemCounter}" value="${selectedServiceId || ''}">
        <input type="text" name="items[${itemCounter}][name]" id="input-item-name-${itemCounter}" placeholder="Custom item description" style="padding: 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; width: 100%; margin-top: 4px; display: none; box-sizing: border-box;">
      </div>
      <div style="width: 85px; flex-shrink: 0;">
        <input type="number" step="0.01" name="items[${itemCounter}][price]" id="input-item-price-${itemCounter}" placeholder="Price" value="0.00" onkeyup="calculateRoomOrderTotal()" onchange="calculateRoomOrderTotal()" style="padding: 8px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; width: 100%; font-family: var(--font-mono, monospace); font-weight: 700; box-sizing: border-box;">
      </div>
      <div style="width: 60px; flex-shrink: 0;">
        <input type="number" name="items[${itemCounter}][quantity]" id="input-item-qty-${itemCounter}" value="1" min="1" onkeyup="calculateRoomOrderTotal()" onchange="calculateRoomOrderTotal()" style="padding: 8px 8px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 11px; width: 100%; font-family: var(--font-mono, monospace); font-weight: 700; box-sizing: border-box;">
      </div>
      <button type="button" onclick="removeOrderLineItem(${itemCounter})" style="background: #fff1f2; border: 1px solid #fecdd3; color: #e11d48; width: 34px; height: 34px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <i class="fa-solid fa-trash-can" style="font-size: 11px;"></i>
      </button>
    `;

    container.appendChild(row);

    const currIdx = itemCounter;
    $(`#item-service-select-${currIdx}`).select2({
      dropdownParent: $('#modal-new-room-order'),
      width: '100%',
      placeholder: 'Search dish, laundry, amenity...'
    }).on('select2:select change', function() {
      onServiceSelectChange(currIdx);
    });

    if (selectedServiceId) {
      $(`#item-service-select-${currIdx}`).val(selectedServiceId).trigger('change');
    }
  }

  function removeOrderLineItem(index) {
    if ($(`#item-service-select-${index}`).data('select2')) {
      $(`#item-service-select-${index}`).select2('destroy');
    }
    const row = document.getElementById(`item-row-${index}`);
    if (row) row.remove();
    calculateRoomOrderTotal();
  }

  function onServiceSelectChange(index) {
    const row = document.getElementById(`item-row-${index}`);
    if (!row) return;
    const select = row.querySelector('.item-service-select');
    if (!select || select.selectedIndex < 0) return;
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

  // Submit Order via AJAX
  async function submitRoomOrder(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('btn-submit-order');
    submitBtn.disabled = true;

    try {
      const response = await fetch(orderStoreUrl, {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': csrfToken,
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
        closeSrvModal('modal-new-room-order');
        setTimeout(() => location.reload(), 700);
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
      alert('Network or server error.');
    } finally {
      submitBtn.disabled = false;
    }
  }

  // Quick Order Status Update
  async function updateOrderStatusAjax(orderId, newStatus) {
    try {
      const response = await fetch(`{{ url('front-office/services/orders') }}/${orderId}/status`, {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
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

  // View Order Details Dossier
  async function viewOrderDossier(id) {
    try {
      const res = await fetch(`{{ url('front-office/services/orders') }}/${id}`);
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
            <div><strong>Served By:</strong> ${o.served_by || 'Room Service'}</div>
            <div><strong>Est. Delivery:</strong> ${o.delivery_time || '-'}</div>
            <div style="grid-column: 1 / -1;"><strong>Special Instructions:</strong> ${o.special_instructions || 'None'}</div>
          </div>
          <div style="font-weight: 800; font-size: 12px; margin-bottom: 8px; color: #0f172a;">Order Items</div>
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; margin-bottom: 12px;">
            ${itemsHtml}
            <div style="display: flex; justify-content: space-between; padding-top: 8px; font-weight: 800; font-size: 13px; color: #0f172a;">
              <div>Total (inc. 5% tax):</div>
              <div style="font-family: var(--font-mono, monospace); color: #059669;">₹ ${parseFloat(o.total_amount).toFixed(2)}</div>
            </div>
          </div>
        `;

        openSrvModal('modal-order-dossier');
      }
    } catch (err) {
      console.error(err);
      alert('Unable to load order dossier.');
    }
  }

  // Initialize Select2 dropdowns on document ready
  $(document).ready(function() {
    // Auto-focus search input when any Select2 opens
    $(document).on('select2:open', () => {
      setTimeout(() => {
        const searchField = document.querySelector('.select2-container--open .select2-search__field');
        if (searchField) searchField.focus();
      }, 50);
    });

    $('#filter-order-status').select2({
      width: '200px',
      placeholder: 'All Order Statuses'
    }).on('select2:select change', function() {
      filterOrdersTable();
    });

    $('#ro-room-select').select2({
      dropdownParent: $('#modal-new-room-order'),
      width: '100%',
      placeholder: 'Search Room # or Guest Name...'
    }).on('select2:select change', function() {
      updateRoomOrderGuest();
    });

    $('#ro-payment-status').select2({
      dropdownParent: $('#modal-new-room-order'),
      width: '100%'
    });
  });
</script>
@endpush
