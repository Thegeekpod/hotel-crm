@extends('frontoffice.layouts.app')

@section('title', 'Restaurant Store & Menu Management - Hotel Sagar Sonnet')

@push('styles')
<style>
  :root {
    --kit-cyan: #06b6d4;
    --kit-emerald: #10b981;
    --kit-amber: #f59e0b;
    --kit-rose: #f43f5e;
    --kit-indigo: #6366f1;
  }

  .stores-wrapper {
    padding: 16px 20px;
    background: #f1f5f9;
    min-height: calc(100vh - 110px);
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  /* KPI Summary Strip */
  .stores-kpi-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 12px;
  }

  .stores-kpi-pill {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  .stores-kpi-pill:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  .stores-kpi-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    flex-shrink: 0;
  }

  .stores-kpi-info h5 {
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin: 0;
  }

  .stores-kpi-info p {
    font-size: 19px;
    font-weight: 900;
    font-family: var(--font-mono, monospace);
    color: #0f172a;
    margin: 2px 0 0;
  }

  /* Main Card Layout */
  .stores-main-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .stores-header {
    padding: 14px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
  }

  .stores-header-left h2 {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .stores-header-left p {
    font-size: 11.5px;
    color: #64748b;
    margin: 3px 0 0 0;
  }

  .stores-header-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  /* Tab Navigation */
  .stores-tabs-nav {
    display: flex;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
    padding: 0 20px;
    gap: 8px;
  }

  .stores-tab-btn {
    padding: 12px 18px;
    font-size: 12.5px;
    font-weight: 700;
    color: #64748b;
    border: none;
    background: transparent;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
  }

  .stores-tab-btn:hover {
    color: #0f172a;
  }

  .stores-tab-btn.active {
    color: #10b981;
    border-bottom-color: #10b981;
    background: #ffffff;
    border-top-left-radius: 6px;
    border-top-right-radius: 6px;
  }

  /* Filter Ribbon */
  .stores-filter-ribbon {
    padding: 12px 20px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }

  .stores-cat-nav {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    align-items: center;
  }

  .stores-cat-btn {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    transition: all 0.15s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .stores-cat-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
  }

  .stores-cat-btn.active {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    box-shadow: 0 1px 4px rgba(16, 185, 129, 0.25);
  }

  /* Table Customizations */
  .stores-table-wrap {
    overflow-x: auto;
  }

  .recipe-spec-box {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .recipe-spec-box strong {
    color: #0f172a;
    font-size: 12px;
  }

  .recipe-spec-box small {
    font-size: 10.5px;
    color: #64748b;
  }

  .plate-stock-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-family: var(--font-mono, monospace);
    font-size: 11.5px;
    font-weight: 800;
  }

  .plate-stock-badge.optimal {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
  }

  .plate-stock-badge.low {
    background: #fef3c7;
    color: #d97706;
    border: 1px solid #fde68a;
  }

  .plate-stock-badge.out {
    background: #fff1f2;
    color: #e11d48;
    border: 1px solid #fecdd3;
  }

  /* Modals */
  .stores-modal-backdrop {
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

  .stores-modal-window {
    background: #ffffff;
    border-radius: 12px;
    width: 620px;
    max-width: 95vw;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
  }

  .stores-modal-head {
    padding: 14px 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .stores-modal-head h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .stores-modal-body {
    padding: 20px;
  }

  .stores-modal-foot {
    padding: 14px 20px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }

  /* ==========================================================================
     RESPONSIVE MEDIA QUERIES FOR STORE & MENUS
     ========================================================================== */

  /* Medium Screens & Tablets (< 1024px) */
  @media (max-width: 1024px) {
    .stores-wrapper {
      padding: 12px 14px;
      gap: 12px;
    }
    .stores-kpi-bar {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  /* Small Tablets & Mobile Devices (< 768px) */
  @media (max-width: 768px) {
    .stores-wrapper {
      padding: 8px 10px;
      gap: 10px;
    }
    .stores-kpi-bar {
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }
    .stores-kpi-pill {
      padding: 8px 10px;
      gap: 8px;
    }
    .stores-kpi-icon {
      width: 32px;
      height: 32px;
      font-size: 14px;
    }
    .stores-kpi-info h5 {
      font-size: 9px;
    }
    .stores-kpi-info p {
      font-size: 14px;
    }

    .stores-header {
      padding: 10px 12px;
      flex-direction: column;
      align-items: stretch;
      gap: 10px;
    }
    .stores-header-left h2 {
      font-size: 15px;
    }
    .stores-header-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      width: 100%;
      gap: 6px;
    }
    .stores-header-actions .btn-ui-secondary,
    .stores-header-actions .btn-ui-primary {
      width: 100%;
      height: 32px;
      font-size: 11px;
      justify-content: center;
      padding: 4px 6px;
    }

    .stores-tabs-nav {
      overflow-x: auto;
      flex-wrap: nowrap;
      -webkit-overflow-scrolling: touch;
      padding: 0 10px;
    }
    .stores-tab-btn {
      white-space: nowrap;
      flex-shrink: 0;
      padding: 10px 12px;
      font-size: 11.5px;
    }

    .stores-filter-ribbon {
      padding: 10px 12px;
      flex-direction: column;
      align-items: stretch;
      gap: 10px;
    }
    .stores-cat-nav {
      overflow-x: auto;
      flex-wrap: nowrap;
      -webkit-overflow-scrolling: touch;
      padding-bottom: 4px;
      width: 100%;
    }
    .stores-cat-btn {
      white-space: nowrap;
      flex-shrink: 0;
      padding: 5px 10px;
      font-size: 10.5px;
    }

    .stores-table-wrap {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      width: 100%;
    }
    .stores-table-wrap .pms-table {
      min-width: 780px;
    }

    /* Modals Responsive */
    .stores-modal-backdrop {
      padding: 10px;
    }
    .stores-modal-window {
      width: 95vw !important;
      max-height: 92vh !important;
      margin: 10px auto;
    }
    .stores-modal-body {
      padding: 12px;
    }
    .stores-modal-body form div[style*="grid-template-columns"] {
      grid-template-columns: 1fr !important;
      gap: 8px !important;
    }
  }

  /* Extra Small Mobile (< 480px) */
  @media (max-width: 480px) {
    .stores-kpi-bar {
      grid-template-columns: 1fr 1fr;
    }
    .stores-header-actions {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@section('content')
<div class="stores-wrapper">

  <!-- 1. Top KPI Summary Strip -->
  <div class="stores-kpi-bar">
    <div class="stores-kpi-pill">
      <div class="stores-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">
        <i class="fa-solid fa-utensils"></i>
      </div>
      <div class="stores-kpi-info">
        <h5>Total Plates Ready (Kitchen Stock)</h5>
        <p style="color: #059669;">{{ number_format($totalPlatesAvailable) }} Plates</p>
      </div>
    </div>

    <div class="stores-kpi-pill">
      <div class="stores-kpi-icon" style="background: rgba(6, 182, 212, 0.12); color: #06b6d4;">
        <i class="fa-solid fa-bowl-food"></i>
      </div>
      <div class="stores-kpi-info">
        <h5>Total Food Dishes</h5>
        <p>{{ $totalItemsCount }} Dishes</p>
      </div>
    </div>

    <div class="stores-kpi-pill">
      <div class="stores-kpi-icon" style="background: rgba(99, 102, 241, 0.12); color: #6366f1;">
        <i class="fa-solid fa-layer-group"></i>
      </div>
      <div class="stores-kpi-info">
        <h5>Active Menu Categories</h5>
        <p>{{ $totalMenusCount }} Menus</p>
      </div>
    </div>

    <div class="stores-kpi-pill">
      <div class="stores-kpi-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </div>
      <div class="stores-kpi-info">
        <h5>Low Plate Alerts</h5>
        <p style="color: {{ $lowStockCount > 0 ? '#d97706' : '#10b981' }};">{{ $lowStockCount }} Dishes</p>
      </div>
    </div>

    <div class="stores-kpi-pill">
      <div class="stores-kpi-icon" style="background: rgba(244, 63, 94, 0.12); color: #f43f5e;">
        <i class="fa-solid fa-circle-xmark"></i>
      </div>
      <div class="stores-kpi-info">
        <h5>Sold Out Dishes</h5>
        <p style="color: {{ $outOfStockCount > 0 ? '#f43f5e' : '#10b981' }};">{{ $outOfStockCount }}</p>
      </div>
    </div>
  </div>

  <!-- 2. Main Store & Menu Manager Container -->
  <div class="stores-main-card">
    
    <!-- Header with Action Buttons -->
    <div class="stores-header">
      <div class="stores-header-left">
        <h2><i class="fa-solid fa-kitchen-set" style="color: #10b981;"></i> Restaurant Store &amp; POS Menu Manager</h2>
        <p>100% of POS Menus, Dishes, Ingredients &amp; Plate Stock are created and managed here.</p>
      </div>

      <div class="stores-header-actions">
        <a href="{{ route('pos.index') }}" class="btn-ui-secondary" style="height: 36px; font-weight: 700; font-size: 12px; text-decoration: none;">
          <i class="fa-solid fa-desktop" style="color: #6366f1;"></i> Open POS Terminal
        </a>
        <button type="button" class="btn-ui-secondary" onclick="openMenuCategoryModal()" style="height: 36px; font-weight: 700; font-size: 12px;">
          <i class="fa-solid fa-folder-plus" style="color: #06b6d4;"></i> + New Menu
        </button>
        <button type="button" class="btn-ui-secondary" onclick="openInwardModal()" style="height: 36px; font-weight: 700; font-size: 12px;">
          <i class="fa-solid fa-truck-ramp-box" style="color: #f59e0b;"></i> + Inward Stock
        </button>
        <button type="button" class="btn-ui-primary" onclick="openFoodItemModal()" style="height: 36px; font-weight: 800; font-size: 12px; background: #10b981; border-color: #10b981;">
          <i class="fa-solid fa-plus-circle"></i> + Add Food Item
        </button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="stores-tabs-nav">
      <button type="button" class="stores-tab-btn active" id="tab-btn-dishes" onclick="switchStoresTab('dishes')">
        <i class="fa-solid fa-utensils"></i> Menu Dishes &amp; Plate Stock ({{ $totalItemsCount }})
      </button>
      <button type="button" class="stores-tab-btn" id="tab-btn-categories" onclick="switchStoresTab('categories')">
        <i class="fa-solid fa-layer-group"></i> Menu Categories ({{ $totalMenusCount }})
      </button>
      <button type="button" class="stores-tab-btn" id="tab-btn-movements" onclick="switchStoresTab('movements')">
        <i class="fa-solid fa-clock-rotate-left"></i> Kitchen Stock Movements
      </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: MENU DISHES & PLATE STOCK (DEFAULT) -->
    <!-- ========================================== -->
    <div id="tab-pane-dishes">
      
      <!-- Filter & Search Ribbon -->
      <div class="stores-filter-ribbon">
        <!-- Category Pills -->
        <div class="stores-cat-nav">
          <a href="{{ route('stores.index') }}" class="stores-cat-btn {{ !request('category') ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i> All Menus
          </a>
          @foreach($categories as $cat)
            <a href="{{ route('stores.index', ['category' => $cat->name, 'stock_status' => request('stock_status')]) }}" 
               class="stores-cat-btn {{ request('category') == $cat->name ? 'active' : '' }}">
              {{ $cat->name }} ({{ $cat->pos_items_count }})
            </a>
          @endforeach
        </div>

        <!-- Search & Status Selectors -->
        <div style="display: flex; gap: 8px; align-items: center;">
          <select class="pms-select" style="height: 34px; font-size: 11.5px;" onchange="filterStockStatus(this.value)">
            <option value="all" {{ !request('stock_status') || request('stock_status') === 'all' ? 'selected' : '' }}>All Stock Status</option>
            <option value="optimal" {{ request('stock_status') === 'optimal' ? 'selected' : '' }}>Optimal Stock</option>
            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock Alert</option>
            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
          </select>

          <form action="{{ route('stores.index') }}" method="GET" style="display: flex; gap: 4px;">
            @if(request('category'))
              <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('stock_status'))
              <input type="hidden" name="stock_status" value="{{ request('stock_status') }}">
            @endif
            <input type="text" name="search" class="pms-input" placeholder="Search dish, ingredient..." value="{{ request('search') }}" style="height: 34px; width: 190px; font-size: 11.5px;">
            <button type="submit" class="btn-ui-secondary" style="height: 34px; padding: 0 10px;"><i class="fa-solid fa-search"></i></button>
          </form>
        </div>
      </div>

      <!-- Dishes Table with Ingredients and "Kota Plate Hobe" -->
      <div class="stores-table-wrap">
        <table class="pms-table" style="width: 100%; margin: 0;">
          <thead>
            <tr>
              <th style="width: 50px; text-align: center;">Icon</th>
              <th>Food Dish / Item</th>
              <th>Menu Category</th>
              <th>Price</th>
              <th>Ingredient &amp; Recipe Yield</th>
              <th>Raw Kitchen Stock</th>
              <th>Available Plates ("Kota Plate Hobe")</th>
              <th>Status</th>
              <th style="text-align: right; width: 220px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($items as $dish)
              @php
                $plates = $dish->available_plates;
                $isLow = ($plates <= ($dish->min_alert_plates ?: 10) && $plates > 0);
                $isOut = ($plates <= 0);
                $badgeClass = $isOut ? 'out' : ($isLow ? 'low' : 'optimal');
              @endphp
              <tr>
                <td style="font-size: 22px; text-align: center;">
                  {{ $dish->emoji ?: '🍽️' }}
                </td>
                <td>
                  <strong style="color: #0f172a; font-size: 13px;">{{ $dish->name }}</strong>
                  @if($dish->code)
                    <span style="font-family: var(--font-mono, monospace); font-size: 10.5px; color: #0891b2; display: block;">{{ $dish->code }}</span>
                  @endif
                </td>
                <td>
                  <span class="badge-tag blue" style="font-size: 11px;">{{ $dish->category }}</span>
                </td>
                <td style="font-family: var(--font-mono, monospace); font-weight: 800; font-size: 13px; color: #0f172a;">
                  ₹ {{ number_format($dish->price, 2) }}
                  @if($dish->last_purchase_price)
                    <span style="font-size: 10px; color: #64748b; font-weight: normal; display: block;" title="Last Inward Cost">Last Buy: ₹{{ number_format($dish->last_purchase_price, 2) }}</span>
                  @endif
                </td>
                <td>
                  <div class="recipe-spec-box">
                    <strong><i class="fa-solid fa-mortar-pestle" style="color: #10b981; font-size: 11px;"></i> {{ $dish->ingredient_name ?: 'Not configured' }}</strong>
                    <small>Per Plate: <span style="font-weight: 700; color: #0f172a;">{{ $dish->per_plate_qty + 0 }} {{ $dish->per_plate_unit }}</span> / serving</small>
                  </div>
                </td>
                <td style="font-family: var(--font-mono, monospace); font-size: 12.5px; font-weight: 700; color: #334155;">
                  {{ $dish->raw_stock_qty + 0 }} {{ $dish->raw_stock_unit }}
                </td>
                <td>
                  <span class="plate-stock-badge {{ $badgeClass }}">
                    @if($isOut)
                      <i class="fa-solid fa-circle-xmark"></i> 0 Plates (Sold Out)
                    @elseif($isLow)
                      <i class="fa-solid fa-triangle-exclamation"></i> Low: {{ $plates }} Plates Left
                    @else
                      <i class="fa-solid fa-circle-check"></i> {{ $plates }} Plates Ready
                    @endif
                  </span>
                  <span style="font-size: 9.5px; color: #94a3b8; display: block; margin-top: 2px;">Min Alert: {{ $dish->min_alert_plates ?: 10 }} plates</span>
                </td>
                <td>
                  @if($dish->status === 'Active' && !$isOut)
                    <span class="badge-tag green">Active</span>
                  @else
                    <span class="badge-tag red">Out of Stock</span>
                  @endif
                </td>
                <td style="text-align: right;">
                  <button type="button" class="btn-ui-secondary" style="height: 28px; font-size: 10.5px; padding: 2px 8px; color: #059669; font-weight: 700;" onclick="quickInwardForItem({{ $dish->id }}, '{{ addslashes($dish->name) }}', '{{ addslashes($dish->ingredient_name) }}', '{{ $dish->raw_stock_unit }}', {{ $dish->per_plate_qty }}, '{{ $dish->per_plate_unit }}')">
                    <i class="fa-solid fa-plus"></i> Inward
                  </button>
                  <button type="button" class="btn-ui-secondary" style="height: 28px; font-size: 10.5px; padding: 2px 7px;" onclick="editFoodItem({{ json_encode($dish) }})">
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <button type="button" class="btn-ui-secondary" style="height: 28px; font-size: 10.5px; padding: 2px 7px; color: #e11d48;" onclick="deleteFoodItem({{ $dish->id }}, '{{ addslashes($dish->name) }}')">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" style="text-align: center; color: #94a3b8; padding: 40px;">
                  <i class="fa-solid fa-utensils" style="font-size: 36px; margin-bottom: 12px; display: block;"></i>
                  No dishes found in menu catalog. Click <strong>"+ Add Food Item"</strong> to create one.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: MENU CATEGORIES                     -->
    <!-- ========================================== -->
    <div id="tab-pane-categories" style="display: none;">
      <div style="padding: 14px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: #0f172a;">Menu Sections</h4>
          <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Categories appear as tabs in the POS terminal for ordering.</p>
        </div>
        <button type="button" class="btn-ui-primary" onclick="openMenuCategoryModal()" style="height: 32px; font-size: 11.5px; background: #06b6d4; border-color: #06b6d4;">
          <i class="fa-solid fa-plus-circle"></i> + Create New Menu
        </button>
      </div>

      <div class="stores-table-wrap">
        <table class="pms-table" style="width: 100%; margin: 0;">
          <thead>
            <tr>
              <th style="width: 80px;">Code</th>
              <th>Menu Name</th>
              <th>Total Dishes Inside</th>
              <th>Description</th>
              <th>Status</th>
              <th style="text-align: right; width: 150px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($categories as $cat)
              <tr>
                <td style="font-family: var(--font-mono, monospace); font-weight: 800; color: #0891b2;">
                  {{ $cat->code ?: 'CAT-' . $cat->id }}
                </td>
                <td>
                  <strong style="color: #0f172a; font-size: 13px;">{{ $cat->name }}</strong>
                </td>
                <td>
                  <span class="badge-tag blue">{{ $cat->pos_items_count }} Dishes</span>
                </td>
                <td style="color: #64748b; font-size: 11.5px;">
                  {{ $cat->description ?: 'No description' }}
                </td>
                <td>
                  <span class="badge-tag {{ $cat->status === 'Active' ? 'green' : 'red' }}">{{ $cat->status }}</span>
                </td>
                <td style="text-align: right;">
                  <button type="button" class="btn-ui-secondary" style="height: 28px; font-size: 10.5px; padding: 2px 8px;" onclick="editMenuCategory({{ json_encode($cat) }})">
                    <i class="fa-solid fa-pen"></i> Edit
                  </button>
                  <button type="button" class="btn-ui-secondary" style="height: 28px; font-size: 10.5px; padding: 2px 8px; color: #e11d48;" onclick="deleteMenuCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px;">
                  No menus created yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: STOCK MOVEMENTS LOG                 -->
    <!-- ========================================== -->
    <div id="tab-pane-movements" style="display: none;">
      <div style="padding: 14px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
        <h4 style="margin: 0; font-size: 14px; font-weight: 800; color: #0f172a;">Kitchen Inventory Audit Trail</h4>
        <p style="margin: 2px 0 0; font-size: 11px; color: #64748b;">Live records of inward receiving and automatic POS order plate deductions.</p>
      </div>

      <div class="stores-table-wrap">
        <table class="pms-table" style="width: 100%; margin: 0;">
          <thead>
            <tr>
              <th>Date &amp; Time</th>
              <th>Type</th>
              <th>Dish / Provision</th>
              <th>Quantity / Plates</th>
              <th>Stock Before &rarr; After</th>
              <th>Reference No</th>
              <th>Performed By / Remarks</th>
            </tr>
          </thead>
          <tbody>
            @forelse($movements as $mov)
              <tr>
                <td style="font-size: 11px; color: #64748b; font-family: var(--font-mono, monospace);">
                  {{ $mov->created_at->format('d M Y, h:i A') }}
                </td>
                <td>
                  @if($mov->type === 'IN')
                    <span class="badge-tag green"><i class="fa-solid fa-arrow-down"></i> INWARD (+)</span>
                  @else
                    <span class="badge-tag red"><i class="fa-solid fa-arrow-up"></i> POS SALE (-)</span>
                  @endif
                </td>
                <td>
                  <strong>{{ $mov->posItem?->name ?? $mov->item?->name ?? 'Dish' }}</strong>
                </td>
                <td style="font-family: var(--font-mono, monospace); font-weight: 800; {{ $mov->type === 'IN' ? 'color: #059669;' : 'color: #e11d48;' }}">
                  {{ $mov->type === 'IN' ? '+' : '-' }}{{ $mov->quantity }} Plates
                  @if($mov->total_cost > 0)
                    <span style="font-size: 10px; color: #0891b2; display: block; font-weight: 700;">Cost: ₹{{ number_format($mov->total_cost, 2) }}</span>
                  @endif
                </td>
                <td style="font-family: var(--font-mono, monospace); font-size: 11.5px; color: #64748b;">
                  {{ $mov->stock_before }} &rarr; <strong>{{ $mov->stock_after }}</strong>
                </td>
                <td style="font-family: var(--font-mono, monospace); font-size: 11px; color: #0891b2;">
                  {{ $mov->reference_no }}
                </td>
                <td style="font-size: 11px; color: #475569;">
                  {{ $mov->remarks }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align: center; color: #94a3b8; padding: 30px;">
                  No stock movements recorded yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 1: ADD / EDIT FOOD ITEM WITH INGREDIENT & YIELD    -->
<!-- ======================================================== -->
<div class="stores-modal-backdrop" id="modal-food-item">
  <div class="stores-modal-window">
    <div class="stores-modal-head">
      <h3 id="modal-item-title"><i class="fa-solid fa-plus-circle" style="color: #10b981;"></i> Add Food Dish &amp; Ingredient Recipe</h3>
      <button type="button" onclick="closeFoodItemModal()" style="border: none; background: transparent; font-size: 20px; cursor: pointer;">&times;</button>
    </div>
    <div class="stores-modal-body">
      <form id="form-food-item" onsubmit="submitFoodItem(event)">
        <input type="hidden" id="item-id">

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 12px;">
          <div>
            <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Dish Name *</label>
            <input type="text" id="item-name" class="pms-input" required placeholder="e.g. Mughlai Butter Chicken" style="width: 100%;">
          </div>
          <div>
            <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Emoji Icon</label>
            <input type="text" id="item-emoji" class="pms-input" placeholder="🍗" value="🍽️" style="width: 100%; text-align: center; font-size: 16px;">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 14px;">
          <div>
            <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Menu Category *</label>
            <select id="item-category" class="pms-select" required style="width: 100%;">
              <option value="" disabled selected>-- Select Menu Category --</option>
              @foreach($categories as $c)
                <option value="{{ $c->name }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Selling Price (₹) *</label>
            <input type="number" id="item-price" class="pms-input" required min="0" step="any" placeholder="450" style="width: 100%;">
          </div>
          <div>
            <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Dish Code / SKU</label>
            <input type="text" id="item-code" class="pms-input" placeholder="MC-01" style="width: 100%;">
          </div>
        </div>

        <!-- Recipe & Plate Yield Configuration Box -->
        <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px; margin-bottom: 14px;">
          <div style="font-size: 12px; font-weight: 800; color: #0f172a; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-mortar-pestle" style="color: #10b981;"></i> Ingredient Recipe &amp; Plate Yield ("Kota Plate Hobe")
          </div>

          <div style="margin-bottom: 10px;">
            <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Main Ingredient Name *</label>
            <input type="text" id="item-ingredient-name" class="pms-input" required placeholder="e.g. Boneless Chicken, Fresh Paneer, Basmati Rice" style="width: 100%;">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px;">
            <div>
              <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Raw Stock Quantity in Kitchen *</label>
              <input type="number" id="item-raw-qty" class="pms-input" required min="0" step="any" placeholder="20" oninput="autoCalculatePlates()" style="width: 100%;">
            </div>
            <div>
              <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Raw Stock Unit *</label>
              <select id="item-raw-unit" class="pms-select" required onchange="autoCalculatePlates()" style="width: 100%;">
                <option value="Kg" selected>Kg</option>
                <option value="Liters">Liters</option>
                <option value="Pcs">Pcs</option>
                <option value="Bottles">Bottles</option>
                <option value="Packets">Packets</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px;">
            <div>
              <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Per Plate Requirement *</label>
              <input type="number" id="item-per-plate-qty" class="pms-input" required min="0.001" step="any" placeholder="250" oninput="autoCalculatePlates()" style="width: 100%;">
            </div>
            <div>
              <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Per Plate Unit *</label>
              <select id="item-per-plate-unit" class="pms-select" required onchange="autoCalculatePlates()" style="width: 100%;">
                <option value="gm" selected>gm</option>
                <option value="ml">ml</option>
                <option value="pcs">pcs</option>
                <option value="scoops">scoops</option>
                <option value="slices">slices</option>
              </select>
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding-top: 6px; border-top: 1px dashed #cbd5e1;">
            <div>
              <label style="font-size: 11px; font-weight: 800; color: #059669; display: block; margin-bottom: 4px;">Available Plates (Stock for POS) *</label>
              <input type="number" id="item-available-plates" class="pms-input" required min="0" step="1" placeholder="80" style="width: 100%; font-weight: 800; color: #059669; font-size: 14px;">
              <span id="plate-calc-hint" style="font-size: 10px; color: #64748b; margin-top: 2px; display: block;">Auto-calculated from raw stock</span>
            </div>
            <div>
              <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Low Stock Alert Below (Plates) *</label>
              <input type="number" id="item-min-alert" class="pms-input" required min="0" step="1" value="10" placeholder="10" style="width: 100%;">
            </div>
          </div>
        </div>

        <div style="margin-bottom: 12px;">
          <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Description (Optional)</label>
          <textarea id="item-desc" class="pms-input" rows="2" placeholder="Brief preparation notes..." style="width: 100%;"></textarea>
        </div>

        <div class="stores-modal-foot" style="padding: 12px 0 0; background: transparent; border: none;">
          <button type="button" class="btn-ui-secondary" onclick="closeFoodItemModal()">Cancel</button>
          <button type="submit" class="btn-ui-primary" id="btn-save-item"><i class="fa-solid fa-save"></i> Save Food Item</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 2: CREATE / EDIT MENU CATEGORY                     -->
<!-- ======================================================== -->
<div class="stores-modal-backdrop" id="modal-menu-category">
  <div class="stores-modal-window" style="width: 480px;">
    <div class="stores-modal-head">
      <h3 id="modal-cat-title"><i class="fa-solid fa-folder-plus" style="color: #06b6d4;"></i> Create Menu Category</h3>
      <button type="button" onclick="closeMenuCategoryModal()" style="border: none; background: transparent; font-size: 20px; cursor: pointer;">&times;</button>
    </div>
    <div class="stores-modal-body">
      <form id="form-menu-category" onsubmit="submitMenuCategory(event)">
        <input type="hidden" id="cat-id">

        <div style="margin-bottom: 12px;">
          <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Menu Name *</label>
          <input type="text" id="cat-name" class="pms-input" required placeholder="e.g. Biryani &amp; Rice, Tandoori Breads, Soups" style="width: 100%;">
        </div>

        <div style="margin-bottom: 12px;">
          <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Short Code (Optional)</label>
          <input type="text" id="cat-code" class="pms-input" placeholder="e.g. BIR, BREAD, SOUP" style="width: 100%;">
        </div>

        <div style="margin-bottom: 12px;">
          <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Description (Optional)</label>
          <textarea id="cat-desc" class="pms-input" rows="2" placeholder="Brief notes about this menu section..." style="width: 100%;"></textarea>
        </div>

        <div class="stores-modal-foot" style="padding: 12px 0 0; background: transparent; border: none;">
          <button type="button" class="btn-ui-secondary" onclick="closeMenuCategoryModal()">Cancel</button>
          <button type="submit" class="btn-ui-primary"><i class="fa-solid fa-save"></i> Save Menu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 3: INWARD STOCK RECEIVING ("ITEM DUKLO")           -->
<!-- ======================================================== -->
<div class="stores-modal-backdrop" id="modal-inward">
  <div class="stores-modal-window" style="width: 520px;">
    <div class="stores-modal-head">
      <h3><i class="fa-solid fa-truck-ramp-box" style="color: #f59e0b;"></i> Inward Stock Receiving ("Item Duklo")</h3>
      <button type="button" onclick="closeInwardModal()" style="border: none; background: transparent; font-size: 20px; cursor: pointer;">&times;</button>
    </div>
    <div class="stores-modal-body">
      <form id="form-inward" onsubmit="submitInwardStock(event)">
        <div style="margin-bottom: 12px;">
          <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Select Dish / Food Item *</label>
          <select id="inward-item-id" class="pms-select" required style="width: 100%;" onchange="onInwardDishSelected()">
            <option value="">-- Choose Food Dish --</option>
            @foreach($items as $dish)
              <option value="{{ $dish->id }}" 
                      data-name="{{ $dish->name }}" 
                      data-ing="{{ $dish->ingredient_name }}" 
                      data-rawunit="{{ $dish->raw_stock_unit }}" 
                      data-perplate="{{ $dish->per_plate_qty }}" 
                      data-plateunit="{{ $dish->per_plate_unit }}">
                {{ $dish->emoji }} {{ $dish->name }} ({{ $dish->ingredient_name ?: 'No ingredient' }} - Current: {{ $dish->available_plates }} plates)
              </option>
            @endforeach
          </select>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 10px;">
            <div>
              <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;" id="lbl-inward-raw">Raw Inward Quantity *</label>
              <input type="number" id="inward-raw-qty" class="pms-input" min="0" step="any" placeholder="e.g. 10" oninput="calculateInwardPlates()" style="width: 100%;">
            </div>
            <div>
              <label style="font-size: 11px; font-weight: 700; color: #059669; display: block; margin-bottom: 4px;">Plates Added (+ Plates) *</label>
              <input type="number" id="inward-plates" class="pms-input" required min="1" step="1" placeholder="e.g. 40" style="width: 100%; font-weight: 800; color: #059669;">
            </div>
          </div>

          <div style="margin-bottom: 8px;">
            <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">
              Purchase Price / Buying Dam (₹) <span style="font-size: 10px; color: #94a3b8; font-weight: 500;">(Optional - e.g. raw item er khoroch / dam)</span>
            </label>
            <div style="position: relative;">
              <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #64748b; font-size: 13px;">₹</span>
              <input type="number" id="inward-purchase-price" class="pms-input" min="0" step="any" placeholder="e.g. 240 (Optional)" oninput="calculateInwardPlates()" style="width: 100%; padding-left: 26px;">
            </div>
          </div>

          <span id="inward-yield-calc" style="font-size: 10.5px; color: #64748b; display: block;">Select a dish to see recipe yield.</span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
          <div>
            <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Vendor / Supplier Name</label>
            <input type="text" id="inward-vendor" class="pms-input" placeholder="e.g. Fresh Chicken Mart" style="width: 100%;">
          </div>
          <div>
            <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Invoice / Challan No</label>
            <input type="text" id="inward-invoice" class="pms-input" placeholder="INV-2026-901" style="width: 100%;">
          </div>
        </div>

        <div style="margin-bottom: 12px;">
          <label style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">Remarks (Optional)</label>
          <input type="text" id="inward-remarks" class="pms-input" placeholder="Morning delivery fresh stock received..." style="width: 100%;">
        </div>

        <div class="stores-modal-foot" style="padding: 12px 0 0; background: transparent; border: none;">
          <button type="button" class="btn-ui-secondary" onclick="closeInwardModal()">Cancel</button>
          <button type="submit" class="btn-ui-primary" style="background: #10b981; border-color: #10b981;"><i class="fa-solid fa-check"></i> Replenish Stock</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  // ==========================================
  // TAB SWITCHING LOGIC
  // ==========================================
  function switchStoresTab(tabKey) {
    document.getElementById('tab-btn-dishes').classList.toggle('active', tabKey === 'dishes');
    document.getElementById('tab-btn-categories').classList.toggle('active', tabKey === 'categories');
    document.getElementById('tab-btn-movements').classList.toggle('active', tabKey === 'movements');

    document.getElementById('tab-pane-dishes').style.display = (tabKey === 'dishes') ? 'block' : 'none';
    document.getElementById('tab-pane-categories').style.display = (tabKey === 'categories') ? 'block' : 'none';
    document.getElementById('tab-pane-movements').style.display = (tabKey === 'movements') ? 'block' : 'none';
  }

  function filterStockStatus(status) {
    const url = new URL(window.location.href);
    if (status && status !== 'all') {
      url.searchParams.set('stock_status', status);
    } else {
      url.searchParams.delete('stock_status');
    }
    window.location.href = url.toString();
  }

  // ==========================================
  // FOOD ITEM MODAL & AUTO PLATE CALCULATION
  // ==========================================
  function openFoodItemModal() {
    document.getElementById('modal-item-title').innerHTML = '<i class="fa-solid fa-plus-circle" style="color: #10b981;"></i> Add Food Dish & Ingredient Recipe';
    document.getElementById('form-food-item').reset();
    document.getElementById('item-id').value = '';
    document.getElementById('item-emoji').value = '🍽️';
    document.getElementById('item-min-alert').value = '10';
    document.getElementById('modal-food-item').style.display = 'flex';
  }

  function closeFoodItemModal() {
    document.getElementById('modal-food-item').style.display = 'none';
  }

  function autoCalculatePlates() {
    const rawQty = parseFloat(document.getElementById('item-raw-qty').value) || 0;
    const rawUnit = (document.getElementById('item-raw-unit').value || '').toLowerCase();
    const perPlateQty = parseFloat(document.getElementById('item-per-plate-qty').value) || 0;
    const perPlateUnit = (document.getElementById('item-per-plate-unit').value || '').toLowerCase();

    if (rawQty <= 0 || perPlateQty <= 0) return;

    let plates = 0;
    if ((rawUnit === 'kg' || rawUnit === 'kgs') && (perPlateUnit === 'gm' || perPlateUnit === 'g' || perPlateUnit === 'grams')) {
      plates = Math.floor((rawQty * 1000) / perPlateQty);
    } else if ((rawUnit === 'liters' || rawUnit === 'liter' || rawUnit === 'l') && (perPlateUnit === 'ml')) {
      plates = Math.floor((rawQty * 1000) / perPlateQty);
    } else {
      plates = Math.floor(rawQty / perPlateQty);
    }

    document.getElementById('item-available-plates').value = plates;
    document.getElementById('plate-calc-hint').textContent = `Yield: ${rawQty} ${rawUnit.toUpperCase()} yields ~${plates} plates (${perPlateQty} ${perPlateUnit}/plate)`;
  }

  function editFoodItem(dish) {
    document.getElementById('modal-item-title').innerHTML = '<i class="fa-solid fa-pen" style="color: #06b6d4;"></i> Edit Food Dish & Recipe';
    document.getElementById('item-id').value = dish.id;
    document.getElementById('item-name').value = dish.name;
    document.getElementById('item-emoji').value = dish.emoji || '🍽️';
    document.getElementById('item-category').value = dish.category;
    document.getElementById('item-price').value = dish.price;
    document.getElementById('item-code').value = dish.code || '';
    document.getElementById('item-desc').value = dish.description || '';
    
    document.getElementById('item-ingredient-name').value = dish.ingredient_name || '';
    document.getElementById('item-raw-qty').value = parseFloat(dish.raw_stock_qty) || 0;
    document.getElementById('item-raw-unit').value = dish.raw_stock_unit || 'Kg';
    document.getElementById('item-per-plate-qty').value = parseFloat(dish.per_plate_qty) || 1;
    document.getElementById('item-per-plate-unit').value = dish.per_plate_unit || 'gm';
    document.getElementById('item-available-plates').value = dish.available_plates || 0;
    document.getElementById('item-min-alert').value = dish.min_alert_plates || 10;

    document.getElementById('modal-food-item').style.display = 'flex';
  }

  async function submitFoodItem(e) {
    e.preventDefault();
    const itemId = document.getElementById('item-id').value;
    const isEdit = !!itemId;

    const payload = {
      name: document.getElementById('item-name').value.trim(),
      emoji: document.getElementById('item-emoji').value.trim() || '🍽️',
      category: document.getElementById('item-category').value,
      price: document.getElementById('item-price').value,
      code: document.getElementById('item-code').value.trim() || null,
      description: document.getElementById('item-desc').value.trim() || null,
      ingredient_name: document.getElementById('item-ingredient-name').value.trim(),
      raw_stock_qty: document.getElementById('item-raw-qty').value,
      raw_stock_unit: document.getElementById('item-raw-unit').value,
      per_plate_qty: document.getElementById('item-per-plate-qty').value,
      per_plate_unit: document.getElementById('item-per-plate-unit').value,
      available_plates: document.getElementById('item-available-plates').value,
      min_alert_plates: document.getElementById('item-min-alert').value,
      status: (parseInt(document.getElementById('item-available-plates').value) > 0) ? 'Active' : 'Out of Stock'
    };

    const url = isEdit ? `/stores/items/${itemId}` : `{{ route('stores.items.store') }}`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        closeFoodItemModal();
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Saved!', text: data.message, timer: 1500, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1200);
      } else {
        alert(data.message || 'Error saving food item.');
      }
    } catch(err) {
      alert('Network error saving food item.');
    }
  }

  async function deleteFoodItem(id, name) {
    if (!confirm(`Are you sure you want to delete dish "${name}" from store and POS?`)) return;
    try {
      const res = await fetch(`/stores/items/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Deleted', text: data.message, timer: 1500, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1000);
      } else {
        alert(data.message || 'Error deleting dish.');
      }
    } catch(err) {
      alert('Network error deleting dish.');
    }
  }

  // ==========================================
  // MENU CATEGORY MODAL
  // ==========================================
  function openMenuCategoryModal() {
    document.getElementById('modal-cat-title').innerHTML = '<i class="fa-solid fa-folder-plus" style="color: #06b6d4;"></i> Create Menu Category';
    document.getElementById('form-menu-category').reset();
    document.getElementById('cat-id').value = '';
    document.getElementById('modal-menu-category').style.display = 'flex';
  }

  function closeMenuCategoryModal() {
    document.getElementById('modal-menu-category').style.display = 'none';
  }

  function editMenuCategory(cat) {
    document.getElementById('modal-cat-title').innerHTML = '<i class="fa-solid fa-pen" style="color: #06b6d4;"></i> Edit Menu Category';
    document.getElementById('cat-id').value = cat.id;
    document.getElementById('cat-name').value = cat.name;
    document.getElementById('cat-code').value = cat.code || '';
    document.getElementById('cat-desc').value = cat.description || '';
    document.getElementById('modal-menu-category').style.display = 'flex';
  }

  async function submitMenuCategory(e) {
    e.preventDefault();
    const catId = document.getElementById('cat-id').value;
    const isEdit = !!catId;

    const payload = {
      name: document.getElementById('cat-name').value.trim(),
      code: document.getElementById('cat-code').value.trim() || null,
      description: document.getElementById('cat-desc').value.trim() || null,
      status: 'Active'
    };

    const url = isEdit ? `/stores/categories/${catId}` : `{{ route('stores.categories.store') }}`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        closeMenuCategoryModal();
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Menu Saved!', text: data.message, timer: 1500, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1200);
      } else {
        alert(data.message || 'Error saving menu.');
      }
    } catch(err) {
      alert('Network error saving menu category.');
    }
  }

  async function deleteMenuCategory(id, name) {
    if (!confirm(`Are you sure you want to delete menu category "${name}"?`)) return;
    try {
      const res = await fetch(`/stores/categories/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Deleted', text: data.message, timer: 1500, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1000);
      } else {
        alert(data.message || 'Error deleting menu category.');
      }
    } catch(err) {
      alert('Network error deleting menu category.');
    }
  }

  // ==========================================
  // INWARD STOCK MODAL ("ITEM DUKLO")
  // ==========================================
  function openInwardModal() {
    document.getElementById('form-inward').reset();
    document.getElementById('modal-inward').style.display = 'flex';
  }

  function closeInwardModal() {
    document.getElementById('modal-inward').style.display = 'none';
  }

  function quickInwardForItem(id, name, ingName, rawUnit, perPlate, plateUnit) {
    document.getElementById('form-inward').reset();
    document.getElementById('inward-item-id').value = id;
    onInwardDishSelected();
    document.getElementById('modal-inward').style.display = 'flex';
  }

  function onInwardDishSelected() {
    const sel = document.getElementById('inward-item-id');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) {
      document.getElementById('inward-yield-calc').textContent = 'Select a dish to see recipe yield.';
      return;
    }

    const ing = opt.getAttribute('data-ing') || 'Main Ingredient';
    const rawUnit = opt.getAttribute('data-rawunit') || 'Kg';
    const perPlate = parseFloat(opt.getAttribute('data-perplate')) || 1;
    const plateUnit = opt.getAttribute('data-plateunit') || 'gm';

    document.getElementById('lbl-inward-raw').textContent = `Inward Raw Quantity (${rawUnit}) *`;
    document.getElementById('inward-yield-calc').textContent = `Recipe: ${ing} (${perPlate} ${plateUnit} per plate). Entering raw ${rawUnit} auto-computes plates.`;
    calculateInwardPlates();
  }

  function calculateInwardPlates() {
    const sel = document.getElementById('inward-item-id');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    const rawQty = parseFloat(document.getElementById('inward-raw-qty').value) || 0;
    const rawUnit = (opt.getAttribute('data-rawunit') || 'Kg');
    const rawUnitLower = rawUnit.toLowerCase();
    const perPlate = parseFloat(opt.getAttribute('data-perplate')) || 1;
    const plateUnit = (opt.getAttribute('data-plateunit') || 'gm');
    const plateUnitLower = plateUnit.toLowerCase();
    const ing = opt.getAttribute('data-ing') || 'Main Ingredient';
    const purchasePrice = parseFloat(document.getElementById('inward-purchase-price').value) || 0;

    let plates = 0;
    if (rawQty > 0) {
      if ((rawUnitLower === 'kg' || rawUnitLower === 'kgs') && (plateUnitLower === 'gm' || plateUnitLower === 'g' || plateUnitLower === 'grams')) {
        plates = Math.floor((rawQty * 1000) / perPlate);
      } else if ((rawUnitLower === 'liters' || rawUnitLower === 'liter' || rawUnitLower === 'l') && (plateUnitLower === 'ml')) {
        plates = Math.floor((rawQty * 1000) / perPlate);
      } else {
        plates = Math.floor(rawQty / perPlate);
      }
      document.getElementById('inward-plates').value = plates;
    }

    let yieldText = `Recipe: ${ing} (${perPlate} ${plateUnit} per plate). Entering raw ${rawUnit} auto-computes plates.`;
    if (purchasePrice > 0) {
      if (rawQty > 0) {
        const ratePerUnit = (purchasePrice / rawQty).toFixed(2);
        yieldText += ` • Rate: ₹${ratePerUnit}/${rawUnit}`;
      }
      const currentPlates = parseInt(document.getElementById('inward-plates').value) || plates;
      if (currentPlates > 0) {
        const costPerPlate = (purchasePrice / currentPlates).toFixed(2);
        yieldText += ` (~₹${costPerPlate}/plate)`;
      }
    }
    document.getElementById('inward-yield-calc').textContent = yieldText;
  }

  async function submitInwardStock(e) {
    e.preventDefault();
    const payload = {
      pos_item_id: document.getElementById('inward-item-id').value,
      inward_raw_qty: document.getElementById('inward-raw-qty').value || 0,
      inward_plates: document.getElementById('inward-plates').value,
      purchase_price: document.getElementById('inward-purchase-price').value || null,
      vendor_name: document.getElementById('inward-vendor').value.trim() || null,
      invoice_no: document.getElementById('inward-invoice').value.trim() || null,
      remarks: document.getElementById('inward-remarks').value.trim() || null
    };

    try {
      const res = await fetch("{{ route('stores.inward') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        closeInwardModal();
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Stock Replenished!', text: data.message, timer: 1800, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1400);
      } else {
        alert(data.message || 'Error recording inward stock.');
      }
    } catch(err) {
      alert('Network error replenishing stock.');
    }
  }
</script>
@endpush
