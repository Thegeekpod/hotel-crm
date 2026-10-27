@extends('frontoffice.layouts.app')

@section('title', 'POS Sales - Sea Breeze Dining & In-Room Dining - Hotel Sagar Sonnet')

@push('styles')
<style>
  :root {
    --pos-emerald: #10b981;
    --pos-emerald-dark: #059669;
    --pos-cyan: #06b6d4;
    --pos-amber: #f59e0b;
    --pos-rose: #f43f5e;
    --pos-indigo: #6366f1;
  }

  .pos-wrapper {
    padding: 16px 20px;
    background: #f1f5f9;
    min-height: calc(100vh - 110px);
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  /* POS KPI Summary Strip */
  .pos-kpi-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
  }

  .pos-kpi-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  .pos-kpi-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  .pos-kpi-icon {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  .pos-kpi-info h5 {
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
  }

  .pos-kpi-info p {
    font-size: 17px;
    font-weight: 900;
    font-family: var(--font-mono, monospace);
    color: #0f172a;
    margin: 2px 0 0;
  }

  /* Main POS Grid Card */
  .pos-main-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .pos-header-custom {
    padding: 14px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
  }

  .pos-header-title {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .pos-header-title h2 {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .pos-header-controls {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .pos-order-type-tabs {
    display: flex;
    background: #f1f5f9;
    padding: 3px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
  }

  .pos-order-type-btn {
    border: none;
    background: transparent;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .pos-order-type-btn.active {
    background: #ffffff;
    color: #10b981;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
  }

  /* Body 2-Column POS Area */
  .pos-work-body {
    padding: 18px 20px;
    display: flex;
    gap: 20px;
    min-height: 580px;
  }

  @media (max-width: 1024px) {
    .pos-work-body {
      flex-direction: column;
    }
  }

  .pos-left-catalog {
    flex: 2.2;
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .pos-category-nav {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
  }

  .pos-cat-pill {
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #475569;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .pos-cat-pill:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #0f172a;
  }

  .pos-cat-pill.active {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
  }

  .pos-search-box {
    position: relative;
    max-width: 380px;
  }

  .pos-search-box input {
    width: 100%;
    height: 36px;
    padding: 0 14px 0 34px;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
    font-size: 12px;
    font-weight: 600;
    outline: none;
    transition: all 0.15s ease;
  }

  .pos-search-box input:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
  }

  .pos-search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 12px;
  }

  /* Food Cards Grid */
  .pos-food-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
    gap: 14px;
    max-height: 520px;
    overflow-y: auto;
    padding-right: 4px;
  }

  .pos-dish-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 12px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: center;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    position: relative;
    user-select: none;
  }

  .pos-dish-card:hover {
    transform: translateY(-4px);
    border-color: #10b981;
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.15);
    background: #f0fdf4;
  }

  .pos-dish-card:active {
    transform: scale(0.97);
  }

  .pos-dish-emoji {
    font-size: 34px;
    line-height: 1;
    margin-bottom: 8px;
    transition: transform 0.2s ease;
  }

  .pos-dish-card:hover .pos-dish-emoji {
    transform: scale(1.15) rotate(4deg);
  }

  .pos-dish-title {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
    margin-bottom: 6px;
    min-height: 32px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .pos-dish-price {
    font-size: 14px;
    font-weight: 900;
    color: #059669;
    font-family: var(--font-mono, monospace);
  }

  .pos-dish-cat-tag {
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    color: #64748b;
    background: #f1f5f9;
    padding: 2px 6px;
    border-radius: 4px;
    align-self: center;
    margin-top: 6px;
  }

  /* Right Side: Cart */
  .pos-right-cart {
    flex: 1.3;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  }

  .cart-head-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 12px;
  }

  .cart-title-text {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .cart-items-scroll {
    flex: 1;
    max-height: 280px;
    overflow-y: auto;
    padding-right: 4px;
  }

  .cart-row {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 12px;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.15s ease;
  }

  .cart-row:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
  }

  .cart-item-info strong {
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
    display: block;
    line-height: 1.25;
  }

  .cart-item-info small {
    font-size: 11px;
    color: #64748b;
    font-family: var(--font-mono, monospace);
  }

  .cart-qty-ctrl {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    border-radius: 6px;
    padding: 2px 4px;
  }

  .cart-btn-qty {
    width: 22px;
    height: 22px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0f172a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    transition: all 0.1s ease;
  }

  .cart-btn-qty:hover {
    background: #e2e8f0;
  }

  .cart-qty-val {
    font-size: 12px;
    font-weight: 800;
    min-width: 16px;
    text-align: center;
    font-family: var(--font-mono, monospace);
  }

  .cart-total-box {
    border-top: 1px solid #e2e8f0;
    padding-top: 12px;
    margin-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .cart-calc-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    color: #475569;
    font-weight: 600;
  }

  .cart-grand-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 17px;
    font-weight: 900;
    color: #059669;
    font-family: var(--font-mono, monospace);
    border-top: 1px dashed #cbd5e1;
    padding-top: 8px;
    margin-top: 4px;
  }

  /* Payment Pill Selector */
  .billing-method-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin: 10px 0;
  }

  .billing-method-group label {
    font-size: 11px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .billing-pills-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
  }

  .billing-pill {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    border-radius: 6px;
    padding: 6px 8px;
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
    text-align: center;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
  }

  .billing-pill:hover {
    border-color: #94a3b8;
  }

  .billing-pill.active {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    box-shadow: 0 1px 4px rgba(16, 185, 129, 0.25);
  }

  /* Thermal Print Layout Preview */
  .thermal-ticket-card {
    background: #ffffff;
    border: 1px solid #000000;
    padding: 16px;
    font-family: 'Courier New', Courier, monospace;
    color: #000000;
    max-width: 360px;
    margin: 0 auto;
    font-size: 12px;
    line-height: 1.4;
  }

  .thermal-ticket-card h3 {
    text-align: center;
    margin: 0 0 4px;
    font-size: 15px;
    font-weight: 900;
  }

  .thermal-ticket-card .center-sub {
    text-align: center;
    font-size: 10.5px;
    margin-bottom: 8px;
  }

  .thermal-ticket-card .divider-line {
    border-bottom: 1px dashed #000000;
    margin: 6px 0;
  }

  /* Modals */
  .pos-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 20px;
  }

  .pos-modal-window {
    background: #ffffff;
    border-radius: 12px;
    width: 600px;
    max-width: 95vw;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
  }

  .pos-modal-head {
    padding: 14px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .pos-modal-head h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .pos-modal-body {
    padding: 20px;
  }

  .pos-modal-foot {
    padding: 14px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }

  /* ==========================================================================
     RESPONSIVE MEDIA QUERIES FOR POS
     ========================================================================== */

  /* Tablet & Medium Screens (< 1024px) */
  @media (max-width: 1024px) {
    .pos-wrapper {
      padding: 12px 14px;
      gap: 12px;
    }
    .pos-work-body {
      flex-direction: column;
      padding: 14px;
      min-height: auto;
    }
    .pos-left-catalog {
      flex: 1;
      width: 100%;
    }
    .pos-right-cart {
      flex: 1;
      width: 100%;
      margin-top: 10px;
    }
    .pos-food-grid {
      max-height: 480px;
    }
    .cart-items-scroll {
      max-height: 250px;
    }
  }

  /* Small Tablet & Mobile Screens (< 768px) */
  @media (max-width: 768px) {
    .pos-wrapper {
      padding: 8px 10px;
      gap: 10px;
    }
    .pos-kpi-bar {
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }
    .pos-kpi-pill {
      padding: 8px 10px;
      gap: 8px;
    }
    .pos-kpi-icon {
      width: 32px;
      height: 32px;
      font-size: 14px;
    }
    .pos-kpi-info h5 {
      font-size: 9px;
    }
    .pos-kpi-info p {
      font-size: 14px;
    }

    .pos-header-custom {
      padding: 10px 12px;
      flex-direction: column;
      align-items: stretch;
      gap: 10px;
    }
    .pos-header-title {
      justify-content: space-between;
      width: 100%;
    }
    .pos-header-title h2 {
      font-size: 15px;
    }
    .pos-header-controls {
      flex-direction: column;
      align-items: stretch;
      width: 100%;
      gap: 8px;
    }
    .pos-order-type-tabs {
      width: 100%;
      display: flex;
    }
    .pos-order-type-btn {
      flex: 1;
      justify-content: center;
      padding: 6px 4px;
      font-size: 10.5px;
    }
    #room-select-group, #table-select-group, #takeaway-name-group {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: stretch !important;
      gap: 4px !important;
    }
    #pos-room-select, #pos-table-select, #pos-takeaway-name {
      width: 100% !important;
      min-width: 0 !important;
    }
    .pos-header-controls .btn-ui-secondary {
      width: 100%;
      justify-content: center;
    }

    .pos-work-body {
      padding: 10px;
      gap: 12px;
    }
    .pos-category-nav {
      overflow-x: auto;
      flex-wrap: nowrap;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 4px;
      width: 100%;
    }
    .pos-cat-pill {
      flex-shrink: 0;
      white-space: nowrap;
      padding: 6px 10px;
      font-size: 11px;
    }
    .pos-search-box {
      width: 100%;
      max-width: 100%;
    }
    .pos-food-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
      max-height: 420px;
    }
    .pos-dish-card {
      padding: 10px 8px;
    }
    .pos-dish-emoji {
      font-size: 26px;
      margin-bottom: 4px;
    }
    .pos-dish-title {
      font-size: 11px;
      min-height: 28px;
    }
    .pos-dish-price {
      font-size: 13px;
    }
    .pos-modal-window {
      width: 95vw !important;
      margin: 10px auto;
      padding: 0;
    }
    .pos-modal-body {
      padding: 12px;
    }
    .billing-method-group {
      grid-template-columns: 1fr !important;
      gap: 6px !important;
    }
  }

  /* Extra Small Mobile (< 480px) */
  @media (max-width: 480px) {
    .pos-kpi-bar {
      grid-template-columns: 1fr 1fr;
    }
    .pos-dish-price {
      font-size: 12px;
    }
    .pos-dish-cat-tag {
      font-size: 8.5px;
      padding: 2px 4px;
    }
  }
</style>
@endpush

@section('content')
<div class="pos-wrapper">

  <!-- 1. Top KPI Summary Cards -->
  <div class="pos-kpi-bar">
    <div class="pos-kpi-pill">
      <div class="pos-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
        <i class="fa-solid fa-receipt"></i>
      </div>
      <div class="pos-kpi-info">
        <h5>Today's Orders</h5>
        <p>{{ $todayOrdersCount }}</p>
      </div>
    </div>

    <div class="pos-kpi-pill">
      <div class="pos-kpi-icon" style="background: rgba(6, 182, 212, 0.12); color: #06b6d4;">
        <i class="fa-solid fa-indian-rupee-sign"></i>
      </div>
      <div class="pos-kpi-info">
        <h5>Today's POS Sales</h5>
        <p>₹ {{ number_format($todaySalesTotal, 2) }}</p>
      </div>
    </div>

    <div class="pos-kpi-pill">
      <div class="pos-kpi-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
        <i class="fa-solid fa-fire-burner"></i>
      </div>
      <div class="pos-kpi-info">
        <h5>KOTs in Kitchen</h5>
        <p>{{ $activeKotCount }}</p>
      </div>
    </div>

    <div class="pos-kpi-pill">
      <div class="pos-kpi-icon" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
        <i class="fa-solid fa-door-open"></i>
      </div>
      <div class="pos-kpi-info">
        <h5>Room Folio Bills</h5>
        <p>{{ $chargedToRoomCount }}</p>
      </div>
    </div>
  </div>

  <!-- 2. Main POS Workspace Card -->
  <div class="pos-main-card">
    
    <!-- Top Header Bar -->
    <div class="pos-header-custom">
      <div class="pos-header-title">
        <h2><i class="fa-solid fa-utensils" style="color: #10b981;"></i> Sea Breeze Dining &amp; In-Room Dining POS</h2>
      </div>

      <div class="pos-header-controls">
        <!-- Order Type Switcher -->
        <div class="pos-order-type-tabs">
          <button type="button" class="pos-order-type-btn active" id="type-room" onclick="selectOrderType('Room Dining')">
            <i class="fa-solid fa-bell-concierge"></i> Room Dining
          </button>
          <button type="button" class="pos-order-type-btn" id="type-dinein" onclick="selectOrderType('Table Dine-in')">
            <i class="fa-solid fa-chair"></i> Table Dine-in
          </button>
          <button type="button" class="pos-order-type-btn" id="type-takeaway" onclick="selectOrderType('Takeaway')">
            <i class="fa-solid fa-bag-shopping"></i> Takeaway
          </button>
        </div>

        <!-- Room Selector (Visible for Room Dining or when Room Folio is chosen) -->
        <div id="room-select-group" style="display: flex; gap: 8px; align-items: center;">
          <span style="font-weight: 700; font-size: 11px; color: #64748b; white-space: nowrap;">Bill To Room:</span>
          <select class="pms-select" id="pos-room-select" style="height: 34px; min-width: 220px; font-weight: 600;" onchange="syncRoomSelectors(this.value)">
            @forelse($selectableRooms as $rm)
              @php
                $gName = $rm->activeBooking?->guest?->guest_name ?? 'In-House Guest';
              @endphp
              <option value="{{ $rm->id }}" data-room="{{ $rm->room_number }}" data-guest="{{ $gName }}">
                Room {{ $rm->room_number }} - {{ $gName }}
              </option>
            @empty
              <option value="">No Active Rooms</option>
            @endforelse
          </select>
        </div>

        <!-- Table Selector (Visible for Table Dine-in) -->
        <div id="table-select-group" style="display: none; gap: 8px; align-items: center;">
          <span style="font-weight: 700; font-size: 11px; color: #64748b;">Table / Host:</span>
          <select class="pms-select" id="pos-table-select" style="height: 34px; min-width: 170px; font-weight: 600;">
            <option value="Table 1 (Sea Breeze Deck)">Table 1 (Sea Breeze Deck)</option>
            <option value="Table 2 (Lawn View)">Table 2 (Lawn View)</option>
            <option value="Table 3 (Poolside)">Table 3 (Poolside)</option>
            <option value="Table 4 (Indoor AC)">Table 4 (Indoor AC)</option>
            <option value="Table 5 (Family Cluster)">Table 5 (Family Cluster)</option>
            <option value="VIP Booth 01">VIP Booth 01</option>
          </select>
        </div>

        <!-- Customer Name for Takeaway -->
        <div id="takeaway-name-group" style="display: none; gap: 8px; align-items: center;">
          <span style="font-weight: 700; font-size: 11px; color: #64748b;">Customer:</span>
          <input type="text" id="pos-takeaway-name" class="pms-input" placeholder="Guest Name / Phone" style="height: 34px; width: 180px;">
        </div>

        <!-- Quick Modals Buttons -->
        <a href="{{ route('stores.index') }}" class="btn-ui-secondary" style="height: 34px; font-size: 11.5px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-boxes-stacked" style="color: #10b981;"></i> Manage Store & Menus
        </a>
        <button type="button" class="btn-ui-secondary" onclick="openRecentOrdersModal()" style="height: 34px; font-size: 11.5px; font-weight: 700;">
          <i class="fa-solid fa-clock-rotate-left"></i> KOT History
        </button>
      </div>
    </div>

    <!-- 2-Column POS Body -->
    <div class="pos-work-body">
      
      <!-- Left Column: Menu Catalog -->
      <div class="pos-left-catalog">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
          <!-- Category Pills -->
          <div class="pos-category-nav">
            <button class="pos-cat-pill active" onclick="filterMenu('all', this)"><i class="fa-solid fa-grip"></i> All Menu</button>
            @foreach($menuCategories as $cat)
              <button class="pos-cat-pill" onclick="filterMenu('{{ $cat }}', this)">{{ $cat }}</button>
            @endforeach
          </div>

          <!-- Instant Search Input -->
          <div class="pos-search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="menu-search-input" placeholder="Search dish, mocktail, beverage..." onkeyup="searchMenu(this.value)">
          </div>
        </div>

        <!-- Food Grid -->
        <div class="pos-food-grid" id="pos-menu-grid">
          @forelse($menuItems as $dish)
            @php
              $plates = $dish->available_plates;
              $isSoldOut = ($plates <= 0);
              $isLow = ($plates > 0 && $plates <= ($dish->min_alert_plates ?: 10));
            @endphp
            <div class="pos-dish-card {{ $isSoldOut ? 'pos-dish-soldout' : '' }}" 
                 data-cat="{{ $dish->category }}" 
                 data-name="{{ strtolower($dish->name) }}"
                 style="{{ $isSoldOut ? 'opacity: 0.55; border-color: #fecdd3;' : '' }}"
                 onclick="{{ $isSoldOut ? 'PmsAlert.toast(\'This dish is OUT OF STOCK in kitchen store!\', \'warning\')' : 'addToCart(' . $dish->id . ', \'' . addslashes($dish->name) . '\', ' . $dish->price . ', ' . $plates . ')' }}">
              <div class="pos-dish-emoji">{{ $dish->emoji ?: '🍽️' }}</div>
              <div class="pos-dish-title" title="{{ $dish->name }}">{{ $dish->name }}</div>
              <div class="pos-dish-price">₹ {{ number_format($dish->price, 0) }}</div>
              @if($dish->ingredient_name)
                <div style="font-size: 10px; color: #64748b; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $dish->ingredient_display }}">
                  <i class="fa-solid fa-mortar-pestle" style="color: #94a3b8; font-size: 9px;"></i> {{ $dish->ingredient_display }}
                </div>
              @endif
              <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px; gap: 4px;">
                <span class="pos-dish-cat-tag" style="margin-top: 0;">{{ $dish->category }}</span>
                @if($isSoldOut)
                  <span style="font-size: 9.5px; font-weight: 800; font-family: var(--font-mono, monospace); color: #e11d48; background: #fff1f2; padding: 2px 6px; border-radius: 4px; white-space: nowrap;">
                    <i class="fa-solid fa-circle-xmark"></i> Sold Out
                  </span>
                @elseif($isLow)
                  <span style="font-size: 9.5px; font-weight: 800; font-family: var(--font-mono, monospace); color: #d97706; background: #fef3c7; padding: 2px 6px; border-radius: 4px; white-space: nowrap;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Low: {{ $plates }}
                  </span>
                @else
                  <span style="font-size: 9.5px; font-weight: 800; font-family: var(--font-mono, monospace); color: #059669; background: #ecfdf5; padding: 2px 6px; border-radius: 4px; white-space: nowrap;">
                    <i class="fa-solid fa-circle-check"></i> {{ $plates }} Plates
                  </span>
                @endif
              </div>
            </div>
          @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #94a3b8;">
              <i class="fa-solid fa-utensils" style="font-size: 36px; margin-bottom: 12px; display: block;"></i>
              No dishes found in menu catalog. Please create items in <a href="{{ route('stores.index') }}" style="color: #10b981; font-weight: 700;">Kitchen Store</a>.
            </div>
          @endforelse
        </div>
      </div>

      <!-- Right Column: Active Cart / KOT Ticket -->
      <div class="pos-right-cart">
        <div class="cart-head-bar">
          <div class="cart-title-text">
            <i class="fa-solid fa-cart-shopping" style="color: #10b981;"></i>
            <span>Active Order / KOT Ticket</span>
            <span id="cart-count-badge" style="background: #10b981; color: #ffffff; font-size: 10px; padding: 2px 7px; border-radius: 10px; font-weight: 800;">0</span>
          </div>
          <button type="button" class="btn-ui-secondary" style="padding: 4px 8px; font-size: 11px; height: 26px;" onclick="clearCart()">
            <i class="fa-solid fa-trash-can"></i> Clear
          </button>
        </div>

        <!-- Scrollable Cart Line Items -->
        <div id="cart-items" class="cart-items-scroll">
          <div style="text-align: center; color: #94a3b8; padding: 40px 10px; font-size: 12px;">
            <i class="fa-solid fa-basket-shopping" style="font-size: 32px; display: block; margin-bottom: 12px; color: #cbd5e1;"></i>
            Cart is empty.<br>Click any food card on the left to add items.
          </div>
        </div>

        <!-- Special Instructions / Notes -->
        <div style="margin-top: 10px;">
          <input type="text" id="order-instructions" class="pms-input" placeholder="Kitchen instructions (e.g., Less spicy, no onion)..." style="height: 32px; font-size: 11.5px; width: 100%;">
        </div>

        <!-- Billing Mode Selector -->
        <div class="billing-method-group">
          <label>Billing &amp; Payment Mode</label>
          <div class="billing-pills-row">
            <div class="billing-pill active" id="bill-folio" onclick="setBillingType('Charged to Room Folio')">
              <i class="fa-solid fa-door-closed"></i> Room Folio
            </div>
            <div class="billing-pill" id="bill-cash" onclick="setBillingType('Cash Paid')">
              <i class="fa-solid fa-money-bill-wave"></i> Cash Paid
            </div>
            <div class="billing-pill" id="bill-digital" onclick="setBillingType('Digital UPI / Card Paid')">
              <i class="fa-solid fa-qrcode"></i> UPI / Card
            </div>
          </div>

          <!-- Room Folio Picker (Visible when Room Folio is chosen, even for Table Dine-in & Takeaway) -->
          <div id="room-folio-picker-box" style="margin-top: 10px; background: #ecfdf5; border: 1.5px solid #86efac; border-radius: 8px; padding: 10px; display: block;">
            <label style="font-size: 11px; font-weight: 800; color: #166534; display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
              <span><i class="fa-solid fa-door-open" style="color: #10b981;"></i> Select Room to Charge Folio: *</span>
              <span style="font-size: 9.5px; background: #dcfce7; color: #15803d; padding: 1px 6px; border-radius: 4px; font-weight: 700;">In-House Folio</span>
            </label>
            <select class="pms-select" id="cart-room-folio-select" style="width: 100%; height: 36px; font-weight: 700; font-size: 12px; border-color: #10b981; background: #ffffff;" onchange="syncRoomSelectors(this.value)">
              @forelse($selectableRooms as $rm)
                @php
                  $gName = $rm->activeBooking?->guest?->guest_name ?? 'In-House Guest';
                @endphp
                <option value="{{ $rm->id }}" data-room="{{ $rm->room_number }}" data-guest="{{ $gName }}">
                  Room {{ $rm->room_number }} - {{ $gName }}
                </option>
              @empty
                <option value="">No Active Rooms Found</option>
              @endforelse
            </select>
          </div>
        </div>

        <!-- Cart Calculation Breakdown -->
        <div class="cart-total-box">
          <div class="cart-calc-line">
            <span>Subtotal:</span>
            <span id="sub-val" style="font-family: var(--font-mono, monospace); color: #0f172a; font-weight: 700;">₹ 0</span>
          </div>
          <div class="cart-calc-line">
            <span>GST (12%):</span>
            <span id="tax-val" style="font-family: var(--font-mono, monospace); color: #64748b;">₹ 0</span>
          </div>
          <div class="cart-grand-total">
            <span>Total Bill:</span>
            <span id="tot-val">₹ 0</span>
          </div>
        </div>

        <!-- Main Submission Button -->
        <button type="button" class="btn-ui-primary" id="btn-post-order" style="width: 100%; height: 44px; font-weight: 800; font-size: 13px; justify-content: center; margin-top: 12px;" onclick="postOrder()">
          <i class="fa-solid fa-check"></i> <span id="btn-order-label">Post Bill to Room Folio</span>
        </button>
      </div>

    </div>

    <!-- Footer Bar matching static -->
    <div style="padding: 10px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 11px; font-weight: 700; color: #64748b; display: flex; align-items: center; justify-content: space-between;">
      <div style="display: flex; align-items: center; gap: 6px;">
        <span style="width: 7px; height: 7px; background: #10b981; border-radius: 50%; display: inline-block; box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);"></span>
        <span>Hotel Sagar Sonnet Kitchen Order Routing: KOT Active &amp; Ready</span>
      </div>
      <div>
        <span>Cashier: <strong>Debashis Roy</strong> | Terminal: POS-01 (Sea Breeze Dining)</span>
      </div>
    </div>

  </div>

</div>

<!-- MODAL 1: KOT / Thermal Invoice Slip Modal -->
<div class="pos-modal-backdrop" id="kot-modal">
  <div class="pos-modal-window" style="width: 420px;">
    <div class="pos-modal-head" style="background: #0f172a; color: #ffffff;">
      <h3 style="color: #ffffff;"><i class="fa-solid fa-print"></i> Kitchen Order Ticket (KOT)</h3>
      <button type="button" onclick="closeKotModal()" style="border: none; background: transparent; color: #ffffff; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <div class="pos-modal-body" style="background: #f8fafc;">
      <div class="thermal-ticket-card" id="printable-kot-area">
        <h3>HOTEL SAGAR SONNET</h3>
        <div class="center-sub">Sea Breeze Dining &amp; Room Dining<br>*** KITCHEN ORDER TICKET (KOT) ***</div>
        <div class="divider-line"></div>
        <div style="display: flex; justify-content: space-between;">
          <span id="kot-order-num"><strong>KOT: POS-2026-0001</strong></span>
          <span id="kot-time">Time: --:--</span>
        </div>
        <div><strong>Type:</strong> <span id="kot-dest">Room 102</span></div>
        <div><strong>Guest / Host:</strong> <span id="kot-guest">In-House Guest</span></div>
        <div class="divider-line"></div>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
          <thead>
            <tr style="border-bottom: 1px solid #000;">
              <th>Item</th>
              <th style="text-align: center; width: 40px;">Qty</th>
              <th style="text-align: right; width: 60px;">Amount</th>
            </tr>
          </thead>
          <tbody id="kot-items-table">
            <!-- Dynamic KOT Items -->
          </tbody>
        </table>
        <div class="divider-line"></div>
        <div style="display: flex; justify-content: space-between; font-weight: 700;">
          <span>Total Qty: <span id="kot-total-qty">0</span></span>
          <span>Grand Total: ₹ <span id="kot-grand-total">0</span></span>
        </div>
        <div class="divider-line"></div>
        <div id="kot-notes-line" style="font-style: italic; font-size: 11px;">
          Instructions: None
        </div>
        <div class="divider-line"></div>
        <div class="center-sub" style="margin-top: 6px;">Served by: Debashis Roy | System Printed</div>
      </div>
    </div>
    <div class="pos-modal-foot">
      <button type="button" class="btn-ui-secondary" onclick="closeKotModal()">Close</button>
      <button type="button" class="btn-ui-primary" onclick="printThermalTicket()">
        <i class="fa-solid fa-print"></i> Print KOT Ticket
      </button>
    </div>
  </div>
</div>

<!-- MODAL 2: Recent Orders & KOT History -->
<div class="pos-modal-backdrop" id="orders-history-modal">
  <div class="pos-modal-window" style="width: 800px;">
    <div class="pos-modal-head">
      <h3><i class="fa-solid fa-clock-rotate-left" style="color: #10b981;"></i> Recent POS Orders &amp; KOT Tickets</h3>
      <button type="button" onclick="closeOrdersModal()" style="border: none; background: transparent; font-size: 18px; cursor: pointer;">&times;</button>
    </div>
    <div class="pos-modal-body" style="padding: 0;">
      <table class="pms-table" style="width: 100%; margin: 0;">
        <thead>
          <tr>
            <th>Order #</th>
            <th>Type / Destination</th>
            <th>Guest / Customer</th>
            <th>Items</th>
            <th>Total Bill</th>
            <th>Billing Mode</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentOrders as $ro)
            <tr>
              <td style="font-family: var(--font-mono, monospace); font-weight: 700; color: #0284c7;">
                {{ $ro->order_number }}
              </td>
              <td>
                @if($ro->order_type === 'Room Dining')
                  <span class="badge-tag blue"><i class="fa-solid fa-door-open"></i> Room {{ $ro->room_number }}</span>
                @elseif($ro->order_type === 'Table Dine-in')
                  <span class="badge-tag green"><i class="fa-solid fa-chair"></i> {{ $ro->table_number ?: 'Dine-in' }}</span>
                @else
                  <span class="badge-tag yellow"><i class="fa-solid fa-bag-shopping"></i> Takeaway</span>
                @endif
              </td>
              <td><strong>{{ $ro->customer_name ?: 'Guest' }}</strong></td>
              <td style="font-size: 11px;">
                {{ $ro->items->pluck('item_name')->join(', ') }}
              </td>
              <td style="font-family: var(--font-mono, monospace); font-weight: 800; color: #059669;">
                ₹ {{ number_format($ro->total_amount, 2) }}
              </td>
              <td>
                <span class="badge-tag {{ $ro->billing_type === 'Charged to Room Folio' ? 'blue' : 'green' }}">
                  {{ $ro->billing_type }}
                </span>
              </td>
              <td>
                <select class="pms-select" style="height: 26px; font-size: 10px; padding: 2px 4px;" onchange="updateOrderStatus({{ $ro->id }}, this.value)">
                  <option value="Received" {{ $ro->status === 'Received' ? 'selected' : '' }}>Received</option>
                  <option value="In Kitchen" {{ $ro->status === 'In Kitchen' ? 'selected' : '' }}>In Kitchen</option>
                  <option value="Completed" {{ $ro->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                  <option value="Cancelled" {{ $ro->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
              </td>
              <td>
                <button type="button" class="btn-ui-secondary" style="height: 26px; font-size: 10.5px; padding: 2px 6px;" onclick="reprintOrderKot({{ $ro->id }})">
                  <i class="fa-solid fa-print"></i> KOT
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align: center; color: #94a3b8; padding: 30px;">
                No orders recorded yet today.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pos-modal-foot">
      <button type="button" class="btn-ui-secondary" onclick="closeOrdersModal()">Close</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  // State variables
  let cart = [];
  let currentOrderType = 'Room Dining';
  let currentBillingType = 'Charged to Room Folio';

  // Helper to sync room selectors between Header and Cart
  function syncRoomSelectors(val) {
    const headRm = document.getElementById('pos-room-select');
    const cartRm = document.getElementById('cart-room-folio-select');
    if (headRm && headRm.value !== val) headRm.value = val;
    if (cartRm && cartRm.value !== val) cartRm.value = val;
  }

  // 1. Order Type Switching
  function selectOrderType(type) {
    currentOrderType = type;
    document.querySelectorAll('.pos-order-type-btn').forEach(b => b.classList.remove('active'));
    
    const roomGrp = document.getElementById('room-select-group');
    const tableGrp = document.getElementById('table-select-group');
    const takeawayGrp = document.getElementById('takeaway-name-group');

    if (type === 'Room Dining') {
      document.getElementById('type-room').classList.add('active');
      roomGrp.style.display = 'flex';
      tableGrp.style.display = 'none';
      takeawayGrp.style.display = 'none';
      setBillingType('Charged to Room Folio');
    } else if (type === 'Table Dine-in') {
      document.getElementById('type-dinein').classList.add('active');
      tableGrp.style.display = 'flex';
      takeawayGrp.style.display = 'none';
      // If user had Room Folio active, keep it and show room selector!
      if (currentBillingType === 'Charged to Room Folio') {
        roomGrp.style.display = 'flex';
        setBillingType('Charged to Room Folio');
      } else {
        roomGrp.style.display = 'none';
        setBillingType('Cash Paid');
      }
    } else if (type === 'Takeaway') {
      document.getElementById('type-takeaway').classList.add('active');
      tableGrp.style.display = 'none';
      takeawayGrp.style.display = 'flex';
      if (currentBillingType === 'Charged to Room Folio') {
        roomGrp.style.display = 'flex';
        setBillingType('Charged to Room Folio');
      } else {
        roomGrp.style.display = 'none';
        setBillingType('Cash Paid');
      }
    }
  }

  // 2. Billing Type Switching
  function setBillingType(bType) {
    currentBillingType = bType;
    document.querySelectorAll('.billing-pill').forEach(p => p.classList.remove('active'));
    
    const btnLabel = document.getElementById('btn-order-label');
    const folioBox = document.getElementById('room-folio-picker-box');
    const headerRoomGrp = document.getElementById('room-select-group');

    if (bType === 'Charged to Room Folio') {
      document.getElementById('bill-folio').classList.add('active');
      btnLabel.textContent = 'Post Bill to Room Folio';
      if (folioBox) folioBox.style.display = 'block';
      if (headerRoomGrp) headerRoomGrp.style.display = 'flex';
    } else {
      if (folioBox) folioBox.style.display = 'none';
      if (headerRoomGrp && currentOrderType !== 'Room Dining') {
        headerRoomGrp.style.display = 'none';
      }
      if (bType === 'Cash Paid') {
        document.getElementById('bill-cash').classList.add('active');
        btnLabel.textContent = 'Settle Cash & Print KOT';
      } else if (bType === 'Digital UPI / Card Paid') {
        document.getElementById('bill-digital').classList.add('active');
        btnLabel.textContent = 'Settle UPI/Card & Print KOT';
      }
    }
  }

  // 3. Cart Operations
  function addToCart(id, name, price, maxPlates) {
    if (maxPlates !== undefined && maxPlates <= 0) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(`"${name}" is OUT OF STOCK in kitchen store!`, 'warning');
      } else {
        alert(`"${name}" is OUT OF STOCK in kitchen store!`);
      }
      return;
    }
    const existing = cart.find(i => i.id === id);
    if (existing) {
      if (maxPlates !== undefined && existing.qty >= maxPlates) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(`Cannot add more than ${maxPlates} plates for "${name}" (Kitchen limit reached)`, 'warning');
        } else {
          alert(`Cannot add more than ${maxPlates} plates for "${name}" (Kitchen limit reached)`);
        }
        return;
      }
      existing.qty++;
    } else {
      cart.push({ id, name, price: Number(price), qty: 1, maxPlates: (maxPlates !== undefined ? maxPlates : 999) });
    }
    renderCart();

    if (typeof PmsAlert !== 'undefined') {
      PmsAlert.toast(`Added ${name} to cart`, 'success');
    }
  }

  function changeCartQty(idx, delta) {
    if (cart[idx]) {
      if (delta > 0 && cart[idx].maxPlates !== undefined && cart[idx].qty >= cart[idx].maxPlates) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(`Only ${cart[idx].maxPlates} plates available in kitchen stock!`, 'warning');
        } else {
          alert(`Only ${cart[idx].maxPlates} plates available in kitchen stock!`);
        }
        return;
      }
      cart[idx].qty += delta;
      if (cart[idx].qty <= 0) {
        cart.splice(idx, 1);
      }
    }
    renderCart();
  }

  function removeFromCart(idx) {
    cart.splice(idx, 1);
    renderCart();
  }

  function clearCart() {
    if (cart.length === 0) return;
    cart = [];
    renderCart();
  }

  function renderCart() {
    const elem = document.getElementById('cart-items');
    const badge = document.getElementById('cart-count-badge');

    if (cart.length === 0) {
      badge.textContent = '0';
      elem.innerHTML = `
        <div style="text-align: center; color: #94a3b8; padding: 40px 10px; font-size: 12px;">
          <i class="fa-solid fa-basket-shopping" style="font-size: 32px; display: block; margin-bottom: 12px; color: #cbd5e1;"></i>
          Cart is empty.<br>Click any food card on the left to add items.
        </div>
      `;
      document.getElementById('sub-val').textContent = '₹ 0';
      document.getElementById('tax-val').textContent = '₹ 0';
      document.getElementById('tot-val').textContent = '₹ 0';
      return;
    }

    let totalQty = 0;
    let subtotal = 0;
    let html = '';

    cart.forEach((i, idx) => {
      totalQty += i.qty;
      const lineTot = i.price * i.qty;
      subtotal += lineTot;

      html += `
        <div class="cart-row">
          <div class="cart-item-info">
            <strong>${i.name}</strong>
            <small>₹ ${i.price.toLocaleString()} × ${i.qty}</small>
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <div class="cart-qty-ctrl">
              <button type="button" class="cart-btn-qty" onclick="changeCartQty(${idx}, -1)">&minus;</button>
              <span class="cart-qty-val">${i.qty}</span>
              <button type="button" class="cart-btn-qty" onclick="changeCartQty(${idx}, 1)">&plus;</button>
            </div>
            <strong style="color: #059669; font-family: var(--font-mono, monospace); font-size: 12px; min-width: 50px; text-align: right;">
              ₹ ${lineTot.toLocaleString()}
            </strong>
            <button type="button" style="background: rgba(244, 63, 94, 0.1); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.2); border-radius: 50%; width: 22px; height: 22px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;" onclick="removeFromCart(${idx})">&times;</button>
          </div>
        </div>
      `;
    });

    badge.textContent = totalQty;
    elem.innerHTML = html;

    const tax = Math.round(subtotal * 0.12);
    const grand = subtotal + tax;

    document.getElementById('sub-val').textContent = `₹ ${subtotal.toLocaleString()}`;
    document.getElementById('tax-val').textContent = `₹ ${tax.toLocaleString()}`;
    document.getElementById('tot-val').textContent = `₹ ${grand.toLocaleString()}`;
  }

  // 4. Menu Filter by Category
  function filterMenu(cat, btn) {
    document.querySelectorAll('.pos-cat-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const cards = document.querySelectorAll('.pos-dish-card');
    cards.forEach(card => {
      const cardCat = card.getAttribute('data-cat');
      if (cat === 'all' || cardCat === cat) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  }

  // 5. Instant Text Search
  function searchMenu(query) {
    const q = query.trim().toLowerCase();
    const cards = document.querySelectorAll('.pos-dish-card');
    cards.forEach(card => {
      const name = card.getAttribute('data-name');
      const cat = card.getAttribute('data-cat').toLowerCase();
      if (!q || name.includes(q) || cat.includes(q)) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  }

  // 6. Post Order to Server & Generate KOT
  async function postOrder() {
    if (cart.length === 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'warning', title: 'Cart is empty', text: 'Please add at least one dish or beverage before posting!' });
      } else {
        alert('Please add at least one dish or beverage before posting.');
      }
      return;
    }

    const payload = {
      order_type: currentOrderType,
      billing_type: currentBillingType,
      special_instructions: document.getElementById('order-instructions').value.trim() || null,
      items: cart.map(i => ({
        pos_item_id: i.id,
        name: i.name,
        price: i.price,
        quantity: i.qty,
      }))
    };

    // If charged to room folio, get selected room (works for Room Dining, Table Dine-in, or Takeaway!)
    if (currentBillingType === 'Charged to Room Folio') {
      const cartRm = document.getElementById('cart-room-folio-select');
      const headRm = document.getElementById('pos-room-select');
      const chosenRoomId = (cartRm && cartRm.value) ? cartRm.value : (headRm ? headRm.value : null);

      if (!chosenRoomId) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'warning', title: 'Room Required', text: 'Please select an active Room to charge this bill to the Room Folio!' });
        } else {
          alert('Please select an active Room to charge this bill to the Room Folio!');
        }
        return;
      }
      payload.room_id = chosenRoomId;
    }

    if (currentOrderType === 'Table Dine-in') {
      payload.table_number = document.getElementById('pos-table-select').value;
      payload.customer_name = payload.table_number;
    } else if (currentOrderType === 'Takeaway') {
      payload.customer_name = document.getElementById('pos-takeaway-name').value.trim() || 'Takeaway Customer';
    }

    try {
      const res = await fetch("{{ route('pos.orders.store') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Order Placed Successfully!',
            text: data.message,
            timer: 2500,
            showConfirmButton: false
          });
        }

        // Open KOT preview modal
        showKotModal(data.order);

        // Reset cart
        cart = [];
        renderCart();
        document.getElementById('order-instructions').value = '';
      } else {
        alert(data.message || 'Error posting order.');
      }
    } catch (e) {
      console.error(e);
      alert('Network or server error while placing order.');
    }
  }

  // 7. KOT Thermal Slip Modal Handling
  function showKotModal(order) {
    document.getElementById('kot-order-num').innerHTML = `<strong>KOT: ${order.order_number}</strong>`;
    document.getElementById('kot-time').textContent = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    let dest = order.order_type;
    if (order.table_number) {
      dest = order.table_number;
      if (order.room_number) {
        dest += ` (Folio: Room ${order.room_number})`;
      }
    } else if (order.room_number) {
      dest = `Room ${order.room_number}`;
    }
    document.getElementById('kot-dest').textContent = dest;
    document.getElementById('kot-guest').textContent = order.customer_name || 'Guest';

    let tbodyHtml = '';
    let totQty = 0;
    (order.items || []).forEach(it => {
      totQty += it.quantity;
      tbodyHtml += `
        <tr>
          <td>${it.item_name}</td>
          <td style="text-align: center;">${it.quantity}</td>
          <td style="text-align: right;">₹ ${Number(it.line_total).toLocaleString()}</td>
        </tr>
      `;
    });
    document.getElementById('kot-items-table').innerHTML = tbodyHtml;
    document.getElementById('kot-total-qty').textContent = totQty;
    document.getElementById('kot-grand-total').textContent = Number(order.total_amount).toLocaleString();
    document.getElementById('kot-notes-line').textContent = `Instructions: ${order.special_instructions || 'None'}`;

    document.getElementById('kot-modal').style.display = 'flex';
  }

  function closeKotModal() {
    document.getElementById('kot-modal').style.display = 'none';
  }

  function printThermalTicket() {
    const printContent = document.getElementById('printable-kot-area').innerHTML;
    const origBody = document.body.innerHTML;
    const win = window.open('', '_blank', 'width=450,height=600');
    win.document.write(`
      <html>
        <head>
          <title>KOT Print</title>
          <style>
            body { font-family: 'Courier New', Courier, monospace; padding: 20px; font-size: 12px; }
            .divider-line { border-bottom: 1px dashed #000; margin: 6px 0; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 4px 0; }
          </style>
        </head>
        <body onload="window.print(); window.close();">
          ${printContent}
        </body>
      </html>
    `);
    win.document.close();
  }

  // 8. Recent Orders & KOT History Modal
  function openRecentOrdersModal() {
    document.getElementById('orders-history-modal').style.display = 'flex';
  }

  function closeOrdersModal() {
    document.getElementById('orders-history-modal').style.display = 'none';
  }

  async function updateOrderStatus(orderId, newStatus) {
    try {
      const res = await fetch(`/pos/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
      });
      const data = await res.json();
      if (data.success && typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(data.message, 'success');
      }
    } catch(e) {
      alert('Error updating order status.');
    }
  }

  async function reprintOrderKot(orderId) {
    try {
      const res = await fetch(`/pos/orders/${orderId}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success && data.order) {
        showKotModal(data.order);
      }
    } catch(e) {
      alert('Unable to retrieve KOT.');
    }
  }
</script>
@endpush
