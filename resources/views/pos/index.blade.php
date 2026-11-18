@extends('frontoffice.layouts.app')

@section('title', 'POS Sales - Dining & In-Room Dining - Hotel Sagar Sonnet')

@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">

  <!-- 1. Top KPI Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Today's Orders -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's Orders</span>
        <div class="text-2xl font-black font-mono text-emerald-600 mt-1">{{ $todayOrdersCount }}</div>
        <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-receipt text-emerald-500 text-[10px]"></i> Total Orders Placed
        </div>
      </div>
      <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-receipt"></i>
      </div>
    </div>

    <!-- Today's POS Sales -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Today's POS Sales</span>
        <div class="text-2xl font-black font-mono text-cyan-600 mt-1">₹ {{ number_format($todaySalesTotal, 2) }}</div>
        <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-indian-rupee-sign text-cyan-500 text-[10px]"></i> Settled Dining Revenue
        </div>
      </div>
      <div class="w-12 h-12 rounded-xl bg-cyan-50 border border-cyan-200/80 text-cyan-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-indian-rupee-sign"></i>
      </div>
    </div>

    <!-- KOTs in Kitchen -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">KOTs in Kitchen</span>
        <div class="text-2xl font-black font-mono text-amber-600 mt-1">{{ $activeKotCount }}</div>
        <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-fire-burner text-amber-500 text-[10px]"></i> Active Live Prep
        </div>
      </div>
      <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-fire-burner"></i>
      </div>
    </div>

    <!-- Room Folio Bills -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Room Folio Bills</span>
        <div class="text-2xl font-black font-mono text-indigo-600 mt-1">{{ $chargedToRoomCount }}</div>
        <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-door-open text-indigo-500 text-[10px]"></i> Charged to Room Ledger
        </div>
      </div>
      <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-door-open"></i>
      </div>
    </div>
  </div>

  <!-- 2. Main POS Workspace Card -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    
    <!-- Top Header Bar -->
    <div class="bg-slate-50/80 px-5 py-4 border-b border-slate-200/80 flex flex-col xl:flex-row xl:items-center justify-between gap-4">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-base shrink-0">
          <i class="fa-solid fa-utensils"></i>
        </div>
        <div>
          <h2 class="text-base font-extrabold text-slate-900">Sea Breeze Dining &amp; In-Room Dining POS</h2>
          <p class="text-[11px] text-slate-500 font-medium">Multiple KOT Kitchen Routing with Single Final Bill Reconciliation</p>
        </div>
      </div>

      <div class="flex items-center gap-3 flex-wrap">
        <!-- Order Type Switcher -->
        <div class="inline-flex items-center p-1 bg-slate-200/70 rounded-xl gap-1">
          <button type="button" class="pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-emerald-700 shadow-xs cursor-pointer" id="type-room" onclick="selectOrderType('Room Dining')">
            <i class="fa-solid fa-bell-concierge mr-1 text-[11px]"></i> Room Dining
          </button>
          <button type="button" class="pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition-all cursor-pointer" id="type-dinein" onclick="selectOrderType('Table Dine-in')">
            <i class="fa-solid fa-chair mr-1 text-[11px]"></i> Table Dine-in
          </button>
          <button type="button" class="pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition-all cursor-pointer" id="type-takeaway" onclick="selectOrderType('Takeaway')">
            <i class="fa-solid fa-bag-shopping mr-1 text-[11px]"></i> Takeaway
          </button>
        </div>

        <!-- Room Selector (Used for syncing) -->
        <div id="room-select-group" class="hidden" style="display: none !important;">
          <select class="h-9.5 text-xs font-medium rounded-xl border border-slate-200 bg-white min-w-[240px]" id="pos-room-select">
            <option value="">-- Choose Room / Deselect --</option>
            @forelse($selectableRooms as $rm)
              <option value="{{ $rm->id }}" data-room="{{ $rm->room_number }}" data-guest="{{ $rm->guest_name }}" data-has-food-adv="{{ $rm->has_food_advance ? 1 : 0 }}" data-food-adv="{{ $rm->food_advance_amount ?? 0 }}" data-rem-adv="{{ $rm->remaining_advance ?? 0 }}" data-used-adv="{{ $rm->used_food_advance ?? 0 }}">
                Room {{ $rm->room_number }} - {{ $rm->guest_name }}{{ $rm->has_food_advance ? ' [Food Allowance: ₹' . number_format($rm->food_advance_amount, 0) . ']' : '' }}
              </option>
            @empty
              <option value="">No Checked In Guests</option>
            @endforelse
          </select>
        </div>

        <!-- Table Selector (Visible for Table Dine-in & Takeaway) -->
        <div id="table-select-group" class="items-center gap-2 hidden">
          <span class="font-bold text-xs text-slate-500 whitespace-nowrap"><i class="fa-solid fa-chair text-purple-600 mr-1"></i> Table:</span>
          <select class="h-9.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 px-3 focus:outline-none focus:border-indigo-500 min-w-[240px]" id="pos-table-select" onchange="onTableSelectDropdownChange(this.value)">
            <option value="">-- Select Dining Table --</option>
            @foreach($allTables as $tbl)
              @php
                $sess = $tableSessions[$tbl->id] ?? null;
                $isBooked = ($tbl->status === 'booked' || $sess !== null);
              @endphp
              <option value="{{ $tbl->id }}" 
                      data-table-id="{{ $tbl->id }}" 
                      data-table-num="{{ $tbl->table_number }}" 
                      data-table-name="{{ $tbl->table_name }}"
                      data-status="{{ $tbl->status }}"
                      data-is-booked="{{ $isBooked ? '1' : '0' }}">
                {{ $tbl->table_number }} ({{ $tbl->table_name }}) - {{ $isBooked ? '[Occupied - ' . ($sess['kots_count'] ?? 1) . ' KOTs]' : '[Available]' }}
              </option>
            @endforeach
          </select>
        </div>

        <!-- Customer Name for Takeaway -->
        <div id="takeaway-name-group" class="items-center gap-2 hidden">
          <span class="font-bold text-xs text-slate-500 whitespace-nowrap">Customer:</span>
          <input type="text" id="pos-takeaway-name" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-44" placeholder="Guest Name / Phone">
        </div>

        <!-- Quick Action Buttons -->
        <button type="button" onclick="openTableManageModal()" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" title="Manage Tables">
          <i class="fa-solid fa-chair text-purple-600"></i> Tables
        </button>
        <a href="{{ route('stores.index') }}" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs" title="Manage Store & Menus">
          <i class="fa-solid fa-boxes-stacked text-emerald-600"></i> Store
        </a>
        <a href="{{ route('pos.orders.index') }}" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs" title="View All KOT Tickets & Orders">
          <i class="fa-solid fa-clock-rotate-left text-cyan-600"></i> KOT History
        </a>
      </div>
    </div>

    <!-- Live Table Quick-Bar (Visible for Table Dine-In and Takeaway) -->
    <div id="pos-live-table-bar" class="bg-slate-100/80 px-5 py-2.5 border-b border-slate-200/80 flex items-center justify-between gap-3 overflow-x-auto hidden">
      <div class="flex items-center gap-2 shrink-0">
        <span class="text-xs font-black text-slate-700 flex items-center gap-1.5 uppercase tracking-wider">
          <i class="fa-solid fa-chair text-purple-600"></i> Active Tables:
        </span>
      </div>
      <div class="flex items-center gap-2 overflow-x-auto pb-0.5" id="pos-table-pills-container">
        @foreach($allTables as $tbl)
          @php
            $sess = $tableSessions[$tbl->id] ?? null;
            $isBooked = ($tbl->status === 'booked' || $sess !== null);
          @endphp
          <button type="button" 
                  onclick="onTablePillClick({{ $tbl->id }})" 
                  id="tbl-pill-{{ $tbl->id }}"
                  class="pos-table-pill px-3 py-1.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 whitespace-nowrap {{ $isBooked ? 'bg-amber-50 text-amber-900 border-amber-300 hover:bg-amber-100 shadow-2xs' : 'bg-white text-slate-700 border-slate-200 hover:border-emerald-500 hover:text-emerald-700' }}">
            <span class="w-2 h-2 rounded-full {{ $isBooked ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
            <span>{{ $tbl->table_number }}</span>
            <span class="text-[10px] font-medium opacity-75">({{ $tbl->table_name }})</span>
            @if($isBooked && $sess)
              <span class="bg-amber-200/80 text-amber-900 text-[10px] font-black px-1.5 py-0.2 rounded-md">
                {{ $sess['kots_count'] }} KOT{{ $sess['kots_count'] > 1 ? 's' : '' }}
              </span>
              @if(!empty($sess['room_number']))
                <span class="text-[10px] font-bold text-indigo-700">R-{{ $sess['room_number'] }}</span>
              @endif
            @else
              <span class="text-[10px] font-semibold text-emerald-600">Free</span>
            @endif
          </button>
        @endforeach
      </div>
      <div class="shrink-0 text-[11px] text-slate-500 font-medium">
        Click any table to view or add orders
      </div>
    </div>

    <!-- 2-Column POS Body -->
    <div class="grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-slate-200/80">
      
      <!-- Left Column: Menu Catalog -->
      <div class="lg:col-span-7 xl:col-span-8 p-5 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <!-- Category Pills -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full">
            <button class="pos-cat-pill inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap bg-emerald-600 text-white shadow-xs" onclick="filterMenu('all', this)">
              <i class="fa-solid fa-grip text-[10px]"></i> All Menu
            </button>
            @foreach($menuCategories as $cat)
              <button class="pos-cat-pill inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer whitespace-nowrap" onclick="filterMenu('{{ $cat }}', this)">
                {{ $cat }}
              </button>
            @endforeach
          </div>

          <!-- Instant Search Input -->
          <div class="relative w-full sm:w-64 shrink-0">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" id="menu-search-input" placeholder="Search dish, code, SKU..." onkeyup="searchMenu(this.value)" class="h-9 pl-8 pr-3 text-xs font-medium rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all w-full">
          </div>
        </div>

        <!-- Food Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5" id="pos-menu-grid">
          @forelse($menuItems as $dish)
            @php
              $plates = $dish->available_plates;
              $isSoldOut = ($plates <= 0);
              $isLow = ($plates > 0 && $plates <= ($dish->min_alert_plates ?: 10));
              $dishCode = $dish->code ?: ($dish->storeItem?->sku ?? '');
            @endphp
            <div class="pos-dish-card bg-white rounded-xl border border-slate-200 hover:border-emerald-500 hover:shadow-md transition-all p-3.5 flex flex-col justify-between cursor-pointer group {{ $isSoldOut ? 'opacity-55 border-rose-200 bg-rose-50/20' : '' }}" 
                 data-cat="{{ $dish->category }}" 
                 data-name="{{ strtolower($dish->name) }}"
                 data-code="{{ strtolower($dish->code ?? '') }}"
                 data-sku="{{ strtolower($dish->storeItem?->sku ?? '') }}"
                 onclick="{{ $isSoldOut ? 'if(typeof PmsAlert!==\'undefined\'){PmsAlert.toast(\'This dish is OUT OF STOCK in kitchen store!\', \'warning\');}else{alert(\'This dish is OUT OF STOCK in kitchen store!\');}' : 'addToCart(' . $dish->id . ', \'' . addslashes($dish->name) . '\', ' . $dish->price . ', ' . $plates . ')' }}">
              <div>
                <div class="text-3xl text-center py-1 group-hover:scale-110 transition-transform">{{ $dish->emoji ?: '🍽️' }}</div>
                <div class="font-bold text-xs text-slate-800 truncate group-hover:text-emerald-700 transition-colors mt-1" title="{{ $dish->name }}">{{ $dish->name }}</div>
                @if(!empty($dishCode))
                  <div class="text-[10px] font-mono font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded border border-indigo-100 inline-flex items-center gap-1 mt-0.5" title="Code / SKU: {{ $dishCode }}">
                    <i class="fa-solid fa-barcode text-[9px] opacity-70"></i> {{ $dishCode }}
                  </div>
                @endif
                <div class="text-sm font-black font-mono text-slate-900 mt-0.5">₹ {{ number_format($dish->price, 0) }}</div>
                @if($dish->ingredient_name)
                  <div class="text-[10px] text-slate-400 truncate mt-1 flex items-center gap-1" title="{{ $dish->ingredient_display }}">
                    <i class="fa-solid fa-mortar-pestle text-slate-300 text-[9px]"></i> {{ $dish->ingredient_display }}
                  </div>
                @endif
              </div>
              <div class="flex items-center justify-between gap-1.5 mt-2.5 pt-2 border-t border-slate-100">
                <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">{{ $dish->category }}</span>
                @if($isSoldOut)
                  <span class="text-[10px] font-bold font-mono text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200 whitespace-nowrap">
                    <i class="fa-solid fa-circle-xmark"></i> Sold Out
                  </span>
                @elseif($isLow)
                  <span class="text-[10px] font-bold font-mono text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200 whitespace-nowrap">
                    <i class="fa-solid fa-triangle-exclamation"></i> Low: {{ $plates }}
                  </span>
                @else
                  <span class="text-[10px] font-bold font-mono text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 whitespace-nowrap">
                    <i class="fa-solid fa-circle-check"></i> {{ $plates }} Plates
                  </span>
                @endif
              </div>
            </div>
          @empty
            <div class="col-span-full text-center py-12 text-slate-400">
              <i class="fa-solid fa-utensils text-4xl mb-3 text-slate-300 block"></i>
              <div class="text-sm font-bold text-slate-600">No dishes found in menu catalog.</div>
              <div class="text-xs text-slate-400 mt-1">Please create items in <a href="{{ route('stores.index') }}" class="text-emerald-600 font-bold hover:underline">Kitchen Store</a>.</div>
            </div>
          @endforelse
        </div>
      </div>

      <!-- Right Column: Active Cart / KOT Ticket / Table Session -->
      <div class="lg:col-span-5 xl:col-span-4 p-5 bg-slate-50/50 flex flex-col justify-between">
        <div>
          
          <!-- Active Table Session Summary Box (Shown when occupied table is selected) -->
          <div id="active-table-session-box" class="mb-3 bg-linear-to-r from-amber-50 to-orange-50 border border-amber-300 rounded-2xl p-3.5 space-y-2.5 hidden shadow-xs">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                <span class="font-extrabold text-xs text-amber-950" id="sess-table-title">Table Active Dining Session</span>
                <span class="bg-amber-200 text-amber-900 text-[10px] font-black px-2 py-0.5 rounded-full border border-amber-300" id="sess-kot-count-badge">0 KOTs</span>
              </div>
              <div class="text-right">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 block">Running Bill:</span>
                <span class="text-sm font-mono font-black text-amber-900" id="sess-running-subtotal">₹ 0.00</span>
              </div>
            </div>

            <div class="text-[11px] text-amber-900 flex items-center justify-between flex-wrap gap-1 bg-white/70 p-2 rounded-xl border border-amber-200">
              <span id="sess-guest-line"><i class="fa-solid fa-user text-amber-600 mr-1"></i> Guest: In-House Guest</span>
              <span id="sess-room-line" class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">Room --</span>
            </div>

            <div class="grid grid-cols-3 gap-2 pt-1">
              <button type="button" onclick="openRunningKotsModal()" class="h-8.5 text-[11px] font-bold text-amber-900 bg-white hover:bg-amber-100 border border-amber-300 rounded-xl transition-colors flex items-center justify-center gap-1 cursor-pointer" title="View Active KOTs">
                <i class="fa-solid fa-list-check text-amber-600"></i> KOTs
              </button>
              <button type="button" onclick="openShiftTableModal()" class="h-8.5 text-[11px] font-bold text-blue-900 bg-blue-50 hover:bg-blue-100 border border-blue-300 rounded-xl transition-colors flex items-center justify-center gap-1 cursor-pointer" title="Shift Table">
                <i class="fa-solid fa-arrows-split-up-and-left text-blue-600"></i> Shift
              </button>
              <button type="button" onclick="openTableSettleModal()" class="h-8.5 text-[11px] font-black text-white bg-linear-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl shadow-xs transition-all flex items-center justify-center gap-1 cursor-pointer" title="Settle Bill">
                <i class="fa-solid fa-receipt"></i> Settle
              </button>
            </div>
          </div>

          <!-- Cart Header -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-cart-shopping text-emerald-600"></i>
              <span class="font-extrabold text-xs text-slate-900" id="cart-header-title">Active Order / KOT Items</span>
              <span id="cart-count-badge" class="bg-emerald-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full">0</span>
            </div>
            <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 rounded-lg transition-all cursor-pointer" onclick="clearCart()">
              <i class="fa-solid fa-trash-can text-[10px]"></i> Clear
            </button>
          </div>

          <!-- Scrollable Cart Line Items -->
          <div id="cart-items" class="h-60 overflow-y-auto divide-y divide-slate-100 pr-1 my-2">
            <div class="text-center text-slate-400 py-12 text-xs">
              <i class="fa-solid fa-basket-shopping text-3xl block mb-2 text-slate-300"></i>
              Cart is empty.<br>Click any food card on the left to add items.
            </div>
          </div>

          <!-- Special Instructions / Notes -->
          <div class="mt-2">
            <input type="text" id="order-instructions" class="h-9 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full placeholder:text-slate-400" placeholder="Kitchen instructions (e.g., Less spicy, no onion)...">
          </div>

          <!-- Section: Billing Mode Selector (Visible for Room Dining, or informative for Dine-in) -->
          <div class="mt-4" id="billing-mode-container">
            <!-- Room Dining Immediate Billing Mode -->
            <div id="room-dining-billing-options">
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Billing &amp; Payment Mode</label>
              <div class="grid grid-cols-3 gap-2">
                <div class="billing-pill active px-3 py-2 rounded-xl text-xs font-bold text-center cursor-pointer transition-all border border-emerald-300 bg-emerald-50 text-emerald-700 shadow-xs" id="bill-folio" onclick="setBillingType('Charged to Room Folio')">
                  <i class="fa-solid fa-door-closed mr-1"></i> Room Folio
                </div>
                <div class="billing-pill px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 text-center cursor-pointer transition-all border border-slate-200 bg-white hover:bg-slate-50" id="bill-cash" onclick="setBillingType('Cash Paid')">
                  <i class="fa-solid fa-money-bill-wave mr-1"></i> Cash Paid
                </div>
                <div class="billing-pill px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 text-center cursor-pointer transition-all border border-slate-200 bg-white hover:bg-slate-50" id="bill-digital" onclick="setBillingType('Digital UPI / Card Paid')">
                  <i class="fa-solid fa-qrcode mr-1"></i> UPI / Card
                </div>
              </div>
            </div>

            <!-- Table Dine-In / Takeaway Multiple KOT Guide Note -->
            <div id="table-kot-mode-note" class="bg-indigo-50/80 border border-indigo-200 rounded-xl p-3 text-[11px] text-indigo-900 hidden space-y-1">
              <div class="font-extrabold flex items-center gap-1.5 text-xs text-indigo-950">
                <i class="fa-solid fa-kitchen-set text-indigo-600"></i> Running Table KOT Mode
              </div>
              <div>
                Order items to generate a <strong>KOT ticket</strong> for the kitchen. Guests can order multiple times during their stay.
              </div>
              <div class="text-[10.5px] text-indigo-800 font-semibold pt-0.5">
                • <strong>Table Dine-in:</strong> Room Attach (unpaid folio), Cash, or UPI selected during final bill settlement.<br>
                • <strong>Takeaway:</strong> Cash or UPI selected during final bill settlement.
              </div>
            </div>

            <!-- Room Folio Picker -->
            <div id="room-folio-picker-box" class="mt-3 bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 space-y-2">
              <label id="room-picker-label" class="text-xs font-bold text-emerald-800 flex items-center justify-between">
                <span id="room-picker-title" class="flex items-center gap-1.5"><i class="fa-solid fa-door-open text-emerald-600"></i> Select Residing Guest Room: *</span>
                <span id="room-picker-badge" class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold">In-House Guest</span>
              </label>
              <select class="w-full h-9 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500" id="cart-room-folio-select" onchange="onCartRoomChange(this.value)">
                <option value="">-- Choose Room / Deselect --</option>
                @forelse($selectableRooms as $rm)
                  <option value="{{ $rm->id }}" data-room="{{ $rm->room_number }}" data-guest="{{ $rm->guest_name }}" data-has-food-adv="{{ $rm->has_food_advance ? 1 : 0 }}" data-food-adv="{{ $rm->food_advance_amount ?? 0 }}" data-rem-adv="{{ $rm->remaining_advance ?? 0 }}" data-used-adv="{{ $rm->used_food_advance ?? 0 }}">
                    Room {{ $rm->room_number }} - {{ $rm->guest_name }}{{ $rm->has_food_advance ? ' [Food Allowance: ₹' . number_format($rm->food_advance_amount, 0) . ']' : '' }}
                  </option>
                @empty
                  <option value="">No Checked In Guests Found</option>
                @endforelse
              </select>
              <div id="room-picker-info-note" class="text-[11px] text-slate-500 mt-1"></div>
            </div>

            <!-- Takeaway Customer Form (Visible ONLY for Takeaway) -->
            <div id="takeaway-customer-box" class="mt-3 bg-amber-50/70 border border-amber-200 rounded-xl p-3 space-y-2 hidden">
              <label class="text-xs font-bold text-amber-800 flex items-center justify-between">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-user-tag text-amber-600"></i> Outside Takeaway Guest Details: *</span>
                <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-bold">Outside Customer</span>
              </label>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div>
                  <input type="text" id="takeaway-customer-name" class="w-full h-8.5 px-3 text-xs font-medium rounded-lg border border-amber-300 bg-white focus:outline-none focus:border-amber-500" placeholder="Customer Name *">
                </div>
                <div>
                  <input type="text" id="takeaway-customer-phone" class="w-full h-8.5 px-3 text-xs font-medium rounded-lg border border-amber-300 bg-white focus:outline-none focus:border-amber-500" placeholder="Phone Number">
                </div>
                <div>
                  <input type="text" id="takeaway-customer-gstin" class="w-full h-8.5 px-3 text-xs font-medium uppercase rounded-lg border border-amber-300 bg-white focus:outline-none focus:border-amber-500" placeholder="GST Number (Optional)">
                </div>
              </div>
              <div class="text-[10px] text-amber-700 font-medium flex items-center gap-1 mt-1">
                <i class="fa-solid fa-circle-info"></i> Direct restaurant walk-in order. Settle via Cash or UPI when dining concludes.
              </div>
            </div>
          </div>
        </div>

        <div>
          <!-- Cart Calculation Breakdown -->
          <div class="bg-white rounded-xl border border-slate-200 p-3 mt-4 space-y-1.5 text-xs">
            <div class="flex items-center justify-between text-slate-500 font-medium">
              <span>Subtotal:</span>
              <span id="sub-val" class="font-mono font-bold text-slate-800">₹ 0</span>
            </div>
            <div id="row-food-adv-cut" class="flex items-center justify-between text-emerald-700 font-bold hidden">
              <span><i class="fa-solid fa-utensils text-emerald-600 mr-1"></i> Cut from Food Advance:</span>
              <span id="food-adv-cut-val" class="font-mono">- ₹ 0</span>
            </div>
            <div class="flex items-center justify-between text-slate-500 font-medium" id="row-pos-cgst">
              <span>CGST (<span id="pos-cgst-label-rate">{{ rtrim(rtrim(number_format(($defaultGstPos ?? 5) / 2, 2), '0'), '.') }}</span>%):</span>
              <span id="cgst-val" class="font-mono text-slate-500">₹ 0.00</span>
            </div>
            <div class="flex items-center justify-between text-slate-500 font-medium" id="row-pos-sgst">
              <span>SGST (<span id="pos-sgst-label-rate">{{ rtrim(rtrim(number_format(($defaultGstPos ?? 5) / 2, 2), '0'), '.') }}</span>%):</span>
              <span id="sgst-val" class="font-mono text-slate-500">₹ 0.00</span>
            </div>
            <div class="hidden" id="row-pos-gst">
              <span id="pos-gst-label-rate">{{ rtrim(rtrim(number_format($defaultGstPos ?? 5, 2), '0'), '.') }}</span>
              <span id="tax-val">₹ 0</span>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-sm font-black text-slate-900">
              <span id="tot-label">Order Total:</span>
              <span id="tot-val" class="font-mono text-emerald-600">₹ 0</span>
            </div>
            <div id="row-folio-charge-due" class="flex items-center justify-between text-xs font-black text-indigo-700 bg-indigo-50/70 p-1.5 rounded-lg border border-indigo-200 hidden">
              <span>Net Charge to Room Folio:</span>
              <span id="folio-charge-due-val" class="font-mono">₹ 0</span>
            </div>
          </div>

          <!-- Main Submission Button -->
          <button type="button" class="w-full h-11 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer mt-3" id="btn-post-order" onclick="postOrder()">
            <i class="fa-solid fa-check" id="btn-order-icon"></i> <span id="btn-order-label">Post Bill to Room Folio</span>
          </button>
        </div>
      </div>

    </div>

    <!-- Footer Bar -->
    <div class="px-5 py-3 bg-slate-50/80 border-t border-slate-200/80 text-xs font-semibold text-slate-500 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
      <div class="flex items-center gap-2">
        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
        <span>Hotel Sagar Sonnet Kitchen Order Routing: KOT Active &amp; Ready</span>
      </div>
      <div>
        <span>Cashier: <strong class="text-slate-700">Debashis Roy</strong> | Terminal: POS-01 (Sea Breeze Dining)</span>
      </div>
    </div>

  </div>

  </div>
</main>

<!-- MODAL 1: KOT & POS Thermal Slip Modal (Supports KOT tickets and Final Bills) -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="kot-modal">
  <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
    <div class="bg-slate-900 text-white px-5 py-3.5 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-extrabold text-white flex items-center gap-2" id="kot-modal-title">
          <i class="fa-solid fa-print text-indigo-400"></i> KOT Ticket &amp; POS Guest Bill
        </h3>
        <p class="text-[11px] text-slate-400 mt-0.5" id="kot-modal-subtitle">Original Copy (Guest) &amp; Duplicate Copy (Kitchen)</p>
      </div>
      <button type="button" onclick="closeKotModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-base cursor-pointer transition-colors">&times;</button>
    </div>

    <!-- Copy Filter Switcher -->
    <div class="flex items-center justify-between px-5 py-2.5 bg-slate-100 border-b border-slate-200 text-xs">
      <span class="font-bold text-slate-600"><i class="fa-solid fa-copy text-slate-400 mr-1"></i> Preview Copies:</span>
      <div class="inline-flex p-0.5 bg-white border border-slate-200 rounded-lg shadow-2xs">
        <button type="button" id="tab-kot-both" onclick="switchKotPreview('both')" class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-600 text-white shadow-xs cursor-pointer transition-all">Both Copies</button>
        <button type="button" id="tab-kot-guest" onclick="switchKotPreview('guest')" class="px-2.5 py-1 rounded-md text-[11px] font-bold text-slate-600 hover:text-slate-900 cursor-pointer transition-all">Guest Copy</button>
        <button type="button" id="tab-kot-kitchen" onclick="switchKotPreview('kitchen')" class="px-2.5 py-1 rounded-md text-[11px] font-bold text-slate-600 hover:text-slate-900 cursor-pointer transition-all">Kitchen Copy</button>
      </div>
    </div>

    <div class="p-5 bg-slate-50 overflow-y-auto max-h-[68vh]">
      <div id="printable-kot-area" class="space-y-4">
        
        <!-- 1. ORIGINAL COPY (FOR GUEST / INVOICE) -->
        <div id="kot-ticket-guest" class="kot-ticket-slip bg-white p-4 border border-dashed border-slate-300 rounded-xl font-mono text-xs shadow-xs text-slate-900">
          <h3 class="text-center font-black text-sm tracking-wide">HOTEL SAGAR SONNET</h3>
          <div class="center-sub text-center text-[11px] text-slate-500 font-semibold my-1">
            Sea Breeze Dining &amp; Room Dining<br>
            <strong class="text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200 inline-block mt-0.5" id="kot-guest-badge-header">*** ORIGINAL COPY (FOR GUEST) ***</strong>
          </div>
          <div class="divider-line border-b border-dashed border-slate-300 my-2"></div>
          <div class="flex justify-between text-[11px]">
            <span id="kot-guest-order-num"><strong>BILL: POS-2026-0001</strong></span>
            <span id="kot-guest-time">Time: --:--</span>
          </div>
          <div class="text-[11px] mt-0.5"><strong>Destination:</strong> <span id="kot-guest-dest">Room 102</span></div>
          <div class="text-[11px] mt-0.5"><strong>Guest / Host:</strong> <span id="kot-guest-name">In-House Guest</span></div>
          <div class="text-[11px] mt-0.5" id="row-kot-guest-gstin" style="display:none;"><strong>GSTIN:</strong> <span id="kot-guest-gstin" class="font-bold font-mono text-indigo-900">--</span></div>
          <div class="text-[11px] mt-0.5"><strong>Billing Mode:</strong> <span id="kot-guest-billing" class="font-bold text-indigo-700">Charged to Room Folio</span></div>
          <div class="divider-line border-b border-dashed border-slate-300 my-2"></div>
          <table class="w-full border-collapse text-left text-[11px]">
            <thead>
              <tr class="border-b border-slate-300">
                <th class="py-1">Item</th>
                <th class="text-center w-12 py-1">Qty</th>
                <th class="text-right w-16 py-1" id="kot-guest-amount-header">Amount</th>
              </tr>
            </thead>
            <tbody id="kot-guest-items-table" class="divide-y divide-slate-100">
              <!-- Dynamic Guest Items -->
            </tbody>
          </table>
          <div class="divider-line border-b border-dashed border-slate-300 my-2" id="divider-kot-guest-tax"></div>
          <div id="kot-guest-tax-breakdown" class="space-y-1 text-[11px] text-slate-700">
            <div class="flex justify-between font-medium">
              <span>Subtotal:</span>
              <span class="font-mono">₹ <span id="kot-guest-subtotal">0.00</span></span>
            </div>
            <div class="flex justify-between font-medium text-slate-600" id="row-kot-guest-cgst">
              <span>CGST (<span id="kot-guest-cgst-rate">2.5</span>%):</span>
              <span class="font-mono">₹ <span id="kot-guest-cgst-amount">0.00</span></span>
            </div>
            <div class="flex justify-between font-medium text-slate-600" id="row-kot-guest-sgst">
              <span>SGST (<span id="kot-guest-sgst-rate">2.5</span>%):</span>
              <span class="font-mono">₹ <span id="kot-guest-sgst-amount">0.00</span></span>
            </div>
          </div>
          <div class="divider-line border-b border-dashed border-slate-300 my-2" id="divider-kot-guest-total"></div>
          <div class="flex justify-between font-black text-xs" id="row-kot-guest-totals">
            <span>Total Qty: <span id="kot-guest-total-qty">0</span></span>
            <span id="wrap-kot-guest-grand-total">Grand Total: ₹ <span id="kot-guest-grand-total">0.00</span></span>
          </div>
          <div id="kot-guest-unsettled-note" class="text-center font-bold text-[10px] text-amber-800 bg-amber-50 p-1.5 rounded border border-amber-200 mt-2" style="display:none;">
            *** KITCHEN ORDER TICKET (KOT) • RATES SHOWN ON FINAL BILL ***
          </div>
          <div class="divider-line border-b border-dashed border-slate-300 my-2"></div>
          <div id="kot-guest-notes-line" class="italic text-[10px] text-slate-600">
            Instructions: None
          </div>
          <div class="divider-line border-b border-dashed border-slate-300 my-2"></div>
          <div class="center-sub text-center text-[10px] text-slate-400 mt-1">Served by: Debashis Roy | Hotel Sagar Sonnet Dining</div>
        </div>

        <!-- Perforation / Tear Strip -->
        <div id="kot-tear-strip" class="kot-tear-strip text-center py-2 text-[10px] text-slate-400 font-mono select-none">
          - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>
          ✂ TEAR / CUT HERE (KITCHEN COPY BELOW) ✂<br>
          - - - - - - - - - - - - - - - - - - - - - - - - - - -
        </div>

        <!-- 2. DUPLICATE COPY (FOR KITCHEN) -->
        <div id="kot-ticket-kitchen" class="kot-ticket-slip bg-white p-4 border border-dashed border-amber-300 rounded-xl font-mono text-xs shadow-xs text-slate-900 bg-amber-50/20">
          <h3 class="text-center font-black text-sm tracking-wide">HOTEL SAGAR SONNET</h3>
          <div class="center-sub text-center text-[11px] text-slate-500 font-semibold my-1">
            Kitchen Order Routing (KOT)<br>
            <strong class="text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 inline-block mt-0.5" id="kot-kitchen-badge-header">*** DUPLICATE COPY (FOR KITCHEN) ***</strong>
          </div>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <div class="flex justify-between text-[11px]">
            <span id="kot-kitchen-order-num"><strong>KOT: POS-2026-0001</strong></span>
            <span id="kot-kitchen-time">Time: --:--</span>
          </div>
          <div class="text-[11px] mt-0.5"><strong>Destination:</strong> <span id="kot-kitchen-dest" class="font-extrabold text-indigo-800">Room 102</span></div>
          <div class="text-[11px] mt-0.5"><strong>Guest / Host:</strong> <span id="kot-kitchen-name">In-House Guest</span></div>
          <div class="text-[11px] mt-0.5"><strong>Billing Mode:</strong> <span id="kot-kitchen-billing">Pending Settlement</span></div>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <table class="w-full border-collapse text-left text-[11px]">
            <thead>
              <tr class="border-b border-slate-300">
                <th class="py-1">Kitchen Preparation Item</th>
                <th class="text-center w-16 py-1">Qty</th>
              </tr>
            </thead>
            <tbody id="kot-kitchen-items-table" class="divide-y divide-slate-100">
              <!-- Dynamic Kitchen Items -->
            </tbody>
          </table>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <div class="flex justify-between font-black text-xs">
            <span>Total Items / Qty:</span>
            <span class="font-mono text-sm" id="kot-kitchen-total-qty">0</span>
          </div>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <div id="kot-kitchen-notes-line" class="text-[10.5px] font-bold text-amber-900 bg-amber-50 p-2 rounded-lg border border-amber-200">
            Kitchen Instructions: None
          </div>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <div class="center-sub text-center text-[10px] text-slate-400 mt-1">Kitchen Order Routing | DUPLICATE KITCHEN COPY</div>
        </div>

      </div>
    </div>
    
    <!-- Modal Footer Actions -->
    <div class="px-5 py-3.5 bg-white border-t border-slate-200 flex justify-between items-center flex-wrap gap-2">
      <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeKotModal()">Close</button>
      
      <div class="flex items-center gap-2 flex-wrap">
        <button type="button" class="inline-flex items-center gap-1.5 h-9 px-3 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all shadow-xs cursor-pointer" onclick="printThermalTicket('guest')">
          <i class="fa-solid fa-receipt text-indigo-600"></i> Guest Copy
        </button>
        <button type="button" class="inline-flex items-center gap-1.5 h-9 px-3 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all shadow-xs cursor-pointer" onclick="printThermalTicket('kitchen')">
          <i class="fa-solid fa-kitchen-set text-rose-600"></i> Kitchen Copy
        </button>
        <button type="button" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer" onclick="printThermalTicket('both')">
          <i class="fa-solid fa-print text-[11px]"></i> Print Both Copies
        </button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL 2: Single Final Bill Settlement Modal for Table Dine-In & Takeaway -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="table-settle-modal">
  <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
    <!-- Header -->
    <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
          <i class="fa-solid fa-receipt text-emerald-400"></i> Final Table Bill Settlement
        </h3>
        <p class="text-xs text-slate-400 mt-0.5" id="settle-modal-subtitle">
          Consolidate all running KOTs into a single bill and release table.
        </p>
      </div>
      <button type="button" onclick="closeTableSettleModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-base cursor-pointer">&times;</button>
    </div>

    <!-- Body -->
    <div class="p-6 overflow-y-auto space-y-4">
      <!-- Session Destination Info Card -->
      <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between flex-wrap gap-2">
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Dining Table</span>
          <span class="text-xs font-black text-slate-900" id="settle-dest-label">Table 1</span>
        </div>
        <div id="settle-room-block">
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Guest / Attached Room</span>
          <span class="text-xs font-bold text-indigo-700" id="settle-guest-label">Room 102 - Saklin Mustak</span>
        </div>
        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">KOTs Consolidated</span>
          <span class="text-xs font-mono font-black text-amber-700" id="settle-kots-badge">2 KOT Tickets</span>
        </div>
      </div>

      <!-- Itemized Table of ALL dishes across all KOTs -->
      <div>
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Consolidated Ordered Dishes (All KOTs):</span>
          <span class="text-[11px] text-slate-400 font-medium" id="settle-items-total-qty">0 items</span>
        </div>
        <div class="border border-slate-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto">
          <table class="w-full text-left text-xs border-collapse">
            <thead class="sticky top-0 bg-slate-100/90 text-slate-600 font-bold border-b border-slate-200">
              <tr>
                <th class="px-3 py-2">Dish Item</th>
                <th class="px-3 py-2 text-center w-16">Qty</th>
                <th class="px-3 py-2 text-right w-20">Rate</th>
                <th class="px-3 py-2 text-right w-24">Amount</th>
              </tr>
            </thead>
            <tbody id="settle-items-tbody" class="divide-y divide-slate-100">
              <!-- Rendered dynamically -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Bill Breakdown Card -->
      <div class="bg-slate-50/80 border border-slate-200 rounded-xl p-3.5 space-y-1.5 text-xs">
        <div class="flex justify-between text-slate-500 font-medium">
          <span>Subtotal:</span>
          <span id="settle-subtotal-val" class="font-mono font-bold text-slate-800">₹ 0.00</span>
        </div>
        <div class="flex justify-between text-slate-500 font-medium" id="row-settle-cgst">
          <span>CGST (<span id="settle-cgst-rate-val">2.5</span>%):</span>
          <span id="settle-cgst-val" class="font-mono">₹ 0.00</span>
        </div>
        <div class="flex justify-between text-slate-500 font-medium" id="row-settle-sgst">
          <span>SGST (<span id="settle-sgst-rate-val">2.5</span>%):</span>
          <span id="settle-sgst-val" class="font-mono">₹ 0.00</span>
        </div>
        <div class="hidden">
          <span id="settle-gst-rate-val">5</span>
          <span id="settle-tax-val">₹ 0.00</span>
        </div>
        <div class="flex justify-between pt-2 border-t border-slate-200 text-sm font-black text-slate-900">
          <span>Final Bill Amount:</span>
          <span id="settle-grand-total-val" class="font-mono text-emerald-600 text-base">₹ 0.00</span>
        </div>
      </div>

      <!-- Payment / Settlement Mode Selection -->
      <div>
        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider mb-2">
          Choose Settlement Mode: *
        </label>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5" id="settle-mode-grid">
          <!-- Option 1: Room Attach (Available for Table Dine-In with room) -->
          <label id="opt-settle-folio" class="settle-mode-card border-2 border-indigo-500 bg-indigo-50/60 rounded-xl p-3 cursor-pointer transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
              <span class="text-xs font-black text-indigo-900 flex items-center gap-1.5">
                <i class="fa-solid fa-door-closed text-indigo-600"></i> Room Attach
              </span>
              <input type="radio" name="settle_billing_type" value="Charged to Room Folio" checked class="text-indigo-600">
            </div>
            <div class="mt-2 text-[10.5px] text-indigo-800/90 leading-tight">
              <strong>Unpaid Bill:</strong> Attached to guest room folio. Settle during room checkout.
            </div>
          </label>

          <!-- Option 2: Cash -->
          <label id="opt-settle-cash" class="settle-mode-card border border-slate-200 bg-white hover:bg-slate-50 rounded-xl p-3 cursor-pointer transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
              <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-money-bill-wave text-emerald-600"></i> Cash Paid
              </span>
              <input type="radio" name="settle_billing_type" value="Cash Paid" class="text-emerald-600">
            </div>
            <div class="mt-2 text-[10.5px] text-slate-500 leading-tight" id="settle-cash-note">
              Paid in Cash on spot. Attached to room as Paid.
            </div>
          </label>

          <!-- Option 3: UPI -->
          <label id="opt-settle-upi" class="settle-mode-card border border-slate-200 bg-white hover:bg-slate-50 rounded-xl p-3 cursor-pointer transition-all flex flex-col justify-between">
            <div class="flex items-center justify-between">
              <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-qrcode text-cyan-600"></i> Digital UPI
              </span>
              <input type="radio" name="settle_billing_type" value="Digital UPI / Card Paid" class="text-cyan-600">
            </div>
            <div class="mt-2 text-[10.5px] text-slate-500 leading-tight" id="settle-upi-note">
              Paid via UPI / Card on spot. Attached to room as Paid.
            </div>
          </label>
        </div>
      </div>

      <!-- Optional Customer GSTIN field for Tax Invoice -->
      <div id="settle-gstin-box" class="pt-1">
        <label class="block text-xs font-bold text-slate-700 mb-1">
          Guest / Company GSTIN (Optional for Tax Invoice):
        </label>
        <input type="text" id="settle-customer-gstin" class="w-full h-8.5 px-3 text-xs font-medium uppercase rounded-lg border border-slate-300 bg-white focus:outline-none focus:border-indigo-500" placeholder="e.g. 19ABCDE1234F1Z5">
      </div>
    </div>

    <!-- Footer -->
    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3">
      <button type="button" onclick="closeTableSettleModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 hover:bg-slate-100 transition-colors cursor-pointer">
        Cancel
      </button>
      <button type="button" onclick="confirmTableSettlement()" id="btn-confirm-settlement" class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition-all flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-check"></i> Confirm Settlement &amp; Print Final Bill
      </button>
    </div>
  </div>
</div>

<!-- MODAL 3: Running KOTs Viewer Modal -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="running-kots-modal">
  <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[85vh]">
    <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
          <i class="fa-solid fa-list-check text-amber-400"></i> Active KOT Tickets on Table
        </h3>
        <p class="text-xs text-slate-400 mt-0.5" id="running-kots-modal-subtitle">
          Breakdown of individual kitchen tokens punched for this dining session.
        </p>
      </div>
      <button type="button" onclick="closeRunningKotsModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-base cursor-pointer">&times;</button>
    </div>

    <div class="p-6 overflow-y-auto space-y-4" id="running-kots-modal-content">
      <!-- Dynamic KOT cards rendered here -->
    </div>

    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-2">
      <button type="button" onclick="closeRunningKotsModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 cursor-pointer">
        Close
      </button>
      <div class="flex items-center gap-2">
        <button type="button" onclick="closeRunningKotsModal(); openShiftTableModal();" class="px-4 py-2 rounded-xl text-xs font-bold text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 cursor-pointer flex items-center gap-1.5">
          <i class="fa-solid fa-arrows-split-up-and-left text-blue-600"></i> Shift Table
        </button>
        <button type="button" onclick="closeRunningKotsModal(); openTableSettleModal();" class="px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-emerald-600 hover:bg-emerald-700 cursor-pointer flex items-center gap-1.5">
          <i class="fa-solid fa-receipt"></i> Proceed to Final Settlement
        </button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL: Shift / Transfer Table -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="shift-table-modal">
  <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
    <div class="bg-linear-to-r from-blue-700 to-indigo-700 text-white px-6 py-4 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
          <i class="fa-solid fa-arrows-split-up-and-left text-blue-200"></i> Shift Dining Table
        </h3>
        <p class="text-xs text-blue-100 mt-0.5">
          Move active KOTs and guests to another dining table
        </p>
      </div>
      <button type="button" onclick="closeShiftTableModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-base cursor-pointer">&times;</button>
    </div>

    <div class="p-6 space-y-4">
      <div class="bg-blue-50/70 border border-blue-200 rounded-xl p-3.5 space-y-1">
        <div class="text-[11px] font-bold text-blue-800 uppercase tracking-wide">Current Source Table:</div>
        <div class="text-sm font-black text-blue-950 flex items-center gap-2">
          <i class="fa-solid fa-chair text-blue-600"></i>
          <span id="shift-source-table-name">Table --</span>
        </div>
        <div class="text-xs text-blue-700" id="shift-source-table-details">-- active KOTs</div>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1.5">
          Select Target Table to Shift To: *
        </label>
        <select id="shift-target-table-select" class="w-full h-10 px-3 text-xs font-semibold rounded-xl border border-slate-300 bg-white focus:outline-none focus:border-indigo-500">
          <!-- Populated dynamically -->
        </select>
        <p class="text-[11px] text-slate-500 mt-1">
          All active kitchen order tickets (KOTs) will be transferred to the chosen table.
        </p>
      </div>
    </div>

    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
      <button type="button" onclick="closeShiftTableModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 cursor-pointer">
        Cancel
      </button>
      <button type="button" id="btn-confirm-shift-table" onclick="confirmShiftTable()" class="px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-blue-600 hover:bg-blue-700 shadow-xs cursor-pointer flex items-center gap-1.5">
        <i class="fa-solid fa-arrow-right-arrow-left"></i> Confirm Table Shift
      </button>
    </div>
  </div>
</div>

<!-- MODAL: Edit KOT Items & Quantities -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="edit-kot-modal">
  <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[85vh]">
    <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
          <i class="fa-solid fa-pen-to-square text-amber-400"></i> Edit KOT Items
        </h3>
        <p class="text-xs text-slate-400 mt-0.5" id="edit-kot-modal-subtitle">
          Adjust item quantities or remove food items from this KOT
        </p>
      </div>
      <button type="button" onclick="closeEditKotModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-base cursor-pointer">&times;</button>
    </div>

    <div class="p-6 overflow-y-auto space-y-4">
      <!-- Current Items in this KOT -->
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-2">Items Ordered in this KOT:</label>
        <div id="edit-kot-items-list" class="space-y-2 border border-slate-200 rounded-xl p-3 bg-slate-50/50 max-h-60 overflow-y-auto">
          <!-- Dynamic line items -->
        </div>
      </div>

      <!-- Option to add an item to this KOT -->
      <div class="border-t border-slate-200 pt-3">
        <label class="block text-xs font-bold text-slate-700 mb-1.5">Add Item to this KOT:</label>
        <div class="flex gap-2">
          <select id="edit-kot-add-item-select" class="flex-1 h-9 px-3 text-xs font-medium rounded-xl border border-slate-300 bg-white">
            <option value="">-- Choose Menu Item to Add --</option>
            @foreach($menuItems as $mi)
              @php
                $miCode = $mi->code ?: ($mi->storeItem?->sku ?? '');
              @endphp
              <option value="{{ $mi->id }}" data-name="{{ $mi->name }}" data-price="{{ $mi->price }}">
                {{ $mi->name }} {{ !empty($miCode) ? '[' . $miCode . ']' : '' }} (₹{{ number_format($mi->price, 0) }})
              </option>
            @endforeach
          </select>
          <button type="button" onclick="addDishToEditKot()" class="px-3.5 h-9 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold flex items-center gap-1 cursor-pointer">
            <i class="fa-solid fa-plus text-[10px]"></i> Add
          </button>
        </div>
      </div>
    </div>

    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-end gap-2">
      <button type="button" onclick="closeEditKotModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 cursor-pointer">
        Cancel
      </button>
      <button type="button" id="btn-save-edit-kot" onclick="saveEditKot()" class="px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs cursor-pointer flex items-center gap-1.5">
        <i class="fa-solid fa-check"></i> Save Changes
      </button>
    </div>
  </div>
</div>

<!-- MODAL 4: Live Table Management & Status -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="table-manage-modal">
  <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[85vh]">
    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
          <i class="fa-solid fa-chair text-purple-600"></i> Live Restaurant Table Management
        </h3>
        <p class="text-xs text-slate-500 mt-0.5">
          Manage live table occupancy, toggle status between Booked and Available, and track dining capacity in real time.
        </p>
      </div>
      <button type="button" onclick="closeTableManageModal()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-base cursor-pointer transition-colors">&times;</button>
    </div>

    <!-- Live Status Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 px-6 py-3.5 bg-slate-50/40 border-b border-slate-100">
      <div class="bg-white border border-slate-200 rounded-xl p-3 shadow-2xs">
        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Tables</span>
        <div class="text-xl font-extrabold text-slate-900 font-mono mt-0.5" id="tm-total-count">{{ count($allTables) }}</div>
      </div>
      <div class="bg-emerald-50/60 border border-emerald-200 rounded-xl p-3 shadow-2xs">
        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider block">Available</span>
        <div class="text-xl font-extrabold text-emerald-700 font-mono mt-0.5" id="tm-available-count">{{ $allTables->where('status', 'available')->count() }}</div>
      </div>
      <div class="bg-amber-50/60 border border-amber-200 rounded-xl p-3 shadow-2xs">
        <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block">Booked / Occupied</span>
        <div class="text-xl font-extrabold text-amber-700 font-mono mt-0.5" id="tm-booked-count">{{ $allTables->where('status', 'booked')->count() }}</div>
      </div>
      <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 shadow-2xs">
        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Inactive</span>
        <div class="text-xl font-extrabold text-slate-600 font-mono mt-0.5" id="tm-inactive-count">{{ $allTables->where('status', 'inactive')->count() }}</div>
      </div>
    </div>

    <!-- Tables Table -->
    <div class="p-0 overflow-y-auto flex-1">
      <table class="w-full text-left border-collapse text-xs">
        <thead class="sticky top-0 z-10">
          <tr class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200">
            <th class="px-4 py-3">Table Number</th>
            <th class="px-4 py-3">Table Name / Area</th>
            <th class="px-4 py-3">Current Status</th>
            <th class="px-4 py-3 text-right">Quick Status Change</th>
          </tr>
        </thead>
        <tbody id="pos-table-manage-tbody" class="divide-y divide-slate-100">
          @forelse($allTables as $t)
            <tr data-table-id="{{ $t->id }}" class="hover:bg-slate-50/80 transition-colors">
              <td class="px-4 py-3 font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-chair text-xs {{ $t->status === 'available' ? 'text-emerald-500' : ($t->status === 'booked' ? 'text-amber-500' : 'text-slate-400') }}"></i>
                {{ $t->table_number }}
              </td>
              <td class="px-4 py-3 text-slate-700 font-medium">
                {{ $t->table_name }}
              </td>
              <td class="px-4 py-3" id="tm-badge-td-{{ $t->id }}">
                @if($t->status === 'available')
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available
                  </span>
                @elseif($t->status === 'booked')
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Booked
                  </span>
                @else
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                  </span>
                @endif
              </td>
              <td class="px-4 py-3 text-right" id="tm-action-td-{{ $t->id }}">
                <div class="inline-flex items-center gap-2">
                  <select class="h-8 text-xs font-semibold rounded-lg border border-slate-200 bg-white px-2 py-1 text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer" onchange="updateLiveTableStatus({{ $t->id }}, this.value)">
                    <option value="available" {{ $t->status === 'available' ? 'selected' : '' }}>Available (Free)</option>
                    <option value="booked" {{ $t->status === 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="inactive" {{ $t->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                  </select>
                  @if($t->status === 'booked')
                    <button type="button" class="inline-flex items-center gap-1 h-8 px-2.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors cursor-pointer" onclick="updateLiveTableStatus({{ $t->id }}, 'available')">
                      <i class="fa-solid fa-check text-[10px]"></i> Free Table
                    </button>
                  @elseif($t->status === 'available')
                    <button type="button" class="inline-flex items-center gap-1 h-8 px-2.5 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg transition-colors cursor-pointer" onclick="updateLiveTableStatus({{ $t->id }}, 'booked')">
                      <i class="fa-solid fa-chair text-[10px]"></i> Mark Booked
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-slate-400 py-8">No tables found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-6 py-3.5 bg-slate-50/80 border-t border-slate-200/80 flex items-center justify-between">
      <a href="{{ route('admin.utilities.frontoffice.table-manage.index') }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
        <i class="fa-solid fa-gear"></i> Open Full Admin Table Master
      </a>
      <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeTableManageModal()">Close</button>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  // State variables
  const posGstRate = {{ (float) ($defaultGstPos ?? 5.00) }};
  let cart = [];
  let currentOrderType = 'Room Dining';
  let currentBillingType = 'Charged to Room Folio';
  let selectedTableId = null;

  // Active table sessions data provided from backend
  let tableSessions = @json($tableSessions ?? []);
  let allTablesData = @json($allTables ?? []);

  // Sync room selectors between Header and Cart
  function syncRoomSelectors(val) {
    if (typeof $ !== 'undefined') {
      if ($('#pos-room-select').val() !== val) {
        $('#pos-room-select').val(val).trigger('change.select2Sync');
      }
      if ($('#cart-room-folio-select').val() !== val) {
        $('#cart-room-folio-select').val(val).trigger('change.select2Sync');
      }
    } else {
      const headRm = document.getElementById('pos-room-select');
      const cartRm = document.getElementById('cart-room-folio-select');
      if (headRm && headRm.value !== val) headRm.value = val;
      if (cartRm && cartRm.value !== val) cartRm.value = val;
    }
    renderCart();
    updateRoomPickerNote();
  }

  function onCartRoomChange(val) {
    syncRoomSelectors(val);
  }

  // Live note updating for Room Folio vs Cash/UPI Direct
  function updateRoomPickerNote() {
    const box = document.getElementById('room-folio-picker-box');
    const title = document.getElementById('room-picker-title');
    const badge = document.getElementById('room-picker-badge');
    const note = document.getElementById('room-picker-info-note');
    const cartRm = document.getElementById('cart-room-folio-select');
    const selectedOpt = (cartRm && cartRm.selectedIndex >= 0) ? cartRm.options[cartRm.selectedIndex] : null;
    const roomId = cartRm ? cartRm.value : '';
    const roomNo = selectedOpt ? selectedOpt.getAttribute('data-room') : '';
    const guestName = selectedOpt ? selectedOpt.getAttribute('data-guest') : '';
    const hasFoodAdv = selectedOpt && selectedOpt.getAttribute('data-has-food-adv') === '1';
    const foodAdvTotal = hasFoodAdv ? (parseFloat(selectedOpt.getAttribute('data-food-adv')) || 0) : 0;
    const remAdv = hasFoodAdv ? (parseFloat(selectedOpt.getAttribute('data-rem-adv')) || 0) : 0;

    let subtotal = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
    let tax = Math.round(((subtotal * posGstRate) / 100) * 100) / 100;
    let actualTotal = Math.round((subtotal + tax) * 100) / 100;
    const grandFmt = '₹ ' + actualTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const btnLabel = document.getElementById('btn-order-label');
    const btnIcon = document.getElementById('btn-order-icon');

    // 1. Scenario: Table Dine-in & Takeaway (Multiple KOT flow)
    if (currentOrderType === 'Table Dine-in' || currentOrderType === 'Takeaway') {
      const sess = selectedTableId ? tableSessions[selectedTableId] : null;
      const kotSeq = sess ? (sess.kots_count + 1) : 1;

      if (btnIcon) btnIcon.className = 'fa-solid fa-kitchen-set';

      if (cart.length > 0) {
        if (btnLabel) btnLabel.textContent = `Send KOT #${kotSeq} to Kitchen (${cart.length} item${cart.length > 1 ? 's' : ''})`;
      } else if (sess) {
        if (btnLabel) btnLabel.textContent = `Settle Table Bill & Free Table (${sess.kots_count} KOTs • ₹${Number(sess.total_amount).toLocaleString('en-IN')})`;
      } else {
        if (btnLabel) btnLabel.textContent = `Send KOT #1 to Kitchen`;
      }

      if (currentOrderType === 'Table Dine-in') {
        if (box) box.style.display = 'block';
        if (title) title.innerHTML = '<span class="flex items-center gap-1.5"><i class="fa-solid fa-door-open text-emerald-600"></i> Residing Guest Room (for Table KOT): *</span>';
        if (badge) {
          badge.textContent = sess ? 'Active Session Room' : 'Select Room';
          badge.className = 'text-[10px] bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full font-bold';
        }
        if (note) {
          if (roomId && roomNo) {
            note.innerHTML = `<span class="text-emerald-800 font-semibold text-xs flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600"></i> Linked to <strong>Room ${roomNo} (${guestName})</strong>. Multiple KOTs can be placed. Single bill will be settled at conclusion.</span>`;
          } else {
            note.innerHTML = `<span class="text-amber-700 font-semibold text-xs flex items-center gap-1.5"><i class="fa-solid fa-circle-info text-amber-500"></i> Select guest room to tag dining KOTs to their stay.</span>`;
          }
        }
      }
      return;
    }

    // 2. Scenario: Room Dining (Immediate direct billing)
    if (btnIcon) btnIcon.className = 'fa-solid fa-check';

    if (currentBillingType === 'Charged to Room Folio') {
      if (box) box.style.display = 'block';
      if (title) title.innerHTML = '<span class="flex items-center gap-1.5"><i class="fa-solid fa-door-open text-emerald-600"></i> Select Room to Charge Folio: *</span>';
      if (badge) {
        if (hasFoodAdv) {
          badge.textContent = `Food Advance (₹${foodAdvTotal.toLocaleString()})`;
          badge.className = 'text-[10px] bg-emerald-200 text-emerald-900 px-2 py-0.5 rounded-full font-bold border border-emerald-300';
        } else {
          badge.textContent = 'Room Folio Ledger';
          badge.className = 'text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold';
        }
      }
      if (btnLabel) btnLabel.textContent = `Post Bill to Room Folio (${grandFmt})`;

      if (note) {
        if (roomId && roomNo) {
          note.innerHTML = `<span class="text-emerald-800 font-semibold text-xs flex items-center gap-1.5"><i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i> Total ${grandFmt} will be added to <strong>Room ${roomNo} (${guestName})</strong> Folio remaining balance and settled at checkout.</span>`;
        } else {
          note.innerHTML = `<span class="text-rose-600 font-semibold text-xs flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation"></i> Please select a Room to link this bill.</span>`;
        }
      }
    } else {
      // Cash or UPI
      const isCash = (currentBillingType === 'Cash Paid');
      const modeName = isCash ? 'Cash' : 'UPI / Card';
      if (btnLabel) btnLabel.textContent = `Settle ${modeName} (${grandFmt}) & Print Bill`;
      if (note) {
        if (roomId && roomNo) {
          note.innerHTML = `<span class="text-emerald-800 font-semibold text-xs flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-emerald-600"></i> Linked to <strong>Room ${roomNo} (${guestName})</strong> as PAID on spot.</span>`;
        } else {
          note.innerHTML = `<span class="text-slate-500 text-[11px]"><i class="fa-solid fa-circle-info"></i> Direct settlement via ${modeName}.</span>`;
        }
      }
    }
  }

  // Initialize Select2 Dropdowns
  $(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
      $('#pos-room-select').select2({
        width: '280px',
        placeholder: 'Search Room # or Guest Name...',
        allowClear: true
      }).on('change', function(e) {
        if (e.namespace !== 'select2Sync') {
          syncRoomSelectors($(this).val() || '');
        }
      });

      $('#pos-table-select').select2({
        width: '240px',
        placeholder: 'Select Dining Table'
      }).on('change', function() {
        onTableSelectDropdownChange($(this).val());
      });

      $('#cart-room-folio-select').select2({
        width: '100%',
        placeholder: 'Search Room # or Guest Name...',
        allowClear: true
      }).on('change', function(e) {
        if (e.namespace !== 'select2Sync') {
          syncRoomSelectors($(this).val() || '');
        }
      });
    }

    const urlParams = new URLSearchParams(window.location.search);
    const qRoomId = urlParams.get('room_id');
    if (qRoomId) {
      syncRoomSelectors(qRoomId);
    }

    // Default to Room Dining
    selectOrderType('Room Dining');
  });

  // 1. Order Type Switching
  function selectOrderType(type) {
    currentOrderType = type;
    document.querySelectorAll('.pos-order-type-btn').forEach(b => {
      b.className = 'pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition-all cursor-pointer';
    });
    
    const tableGrp = document.getElementById('table-select-group');
    const tableBar = document.getElementById('pos-live-table-bar');
    const takeawayGrp = document.getElementById('takeaway-name-group');
    const folioBox = document.getElementById('room-folio-picker-box');
    const takeawayCustBox = document.getElementById('takeaway-customer-box');
    const roomDiningBillingOpts = document.getElementById('room-dining-billing-options');
    const tableKotNote = document.getElementById('table-kot-mode-note');

    if (type === 'Room Dining') {
      // Scenario 1: Immediate direct bill, no multiple KOTs
      const btn = document.getElementById('type-room');
      if (btn) btn.className = 'pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-emerald-700 shadow-xs cursor-pointer';
      
      if (tableGrp) tableGrp.style.display = 'none';
      if (tableBar) tableBar.style.display = 'none';
      if (takeawayGrp) takeawayGrp.style.display = 'none';
      if (takeawayCustBox) takeawayCustBox.style.display = 'none';
      if (folioBox) folioBox.style.display = 'block';
      if (roomDiningBillingOpts) roomDiningBillingOpts.style.display = 'block';
      if (tableKotNote) tableKotNote.style.display = 'none';
      
      selectedTableId = null;
      renderActiveTableSessionBox();
      setBillingType('Charged to Room Folio');

    } else if (type === 'Table Dine-in') {
      // Scenario 2: Room-wise guest with table, supports multiple KOTs, settled at end
      const btn = document.getElementById('type-dinein');
      if (btn) btn.className = 'pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-emerald-700 shadow-xs cursor-pointer';
      
      if (tableGrp) tableGrp.style.display = 'flex';
      if (tableBar) tableBar.style.display = 'flex';
      if (takeawayGrp) takeawayGrp.style.display = 'none';
      if (takeawayCustBox) takeawayCustBox.style.display = 'none';
      if (folioBox) folioBox.style.display = 'block';
      if (roomDiningBillingOpts) roomDiningBillingOpts.style.display = 'none';
      if (tableKotNote) tableKotNote.style.display = 'block';

      renderActiveTableSessionBox();
      updateRoomPickerNote();

    } else if (type === 'Takeaway') {
      // Scenario 3: Outside guest with table, supports multiple KOTs, settled via Cash/UPI
      const btn = document.getElementById('type-takeaway');
      if (btn) btn.className = 'pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-emerald-700 shadow-xs cursor-pointer';
      
      if (tableGrp) tableGrp.style.display = 'flex';
      if (tableBar) tableBar.style.display = 'flex';
      if (takeawayGrp) takeawayGrp.style.display = 'none';
      if (folioBox) folioBox.style.display = 'none';
      if (takeawayCustBox) takeawayCustBox.style.display = 'block';
      if (roomDiningBillingOpts) roomDiningBillingOpts.style.display = 'none';
      if (tableKotNote) tableKotNote.style.display = 'block';

      renderActiveTableSessionBox();
      updateRoomPickerNote();
    }
  }

  // 2. Billing Type Switching (for Room Dining)
  function setBillingType(bType) {
    currentBillingType = bType;
    document.querySelectorAll('.billing-pill').forEach(p => {
      p.className = 'billing-pill px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 text-center cursor-pointer transition-all border border-slate-200 bg-white hover:bg-slate-50';
    });
    
    const activePillClass = 'billing-pill active px-3 py-2 rounded-xl text-xs font-bold text-center cursor-pointer transition-all border border-emerald-300 bg-emerald-50 text-emerald-700 shadow-xs';

    if (bType === 'Charged to Room Folio') {
      const el = document.getElementById('bill-folio');
      if (el) el.className = activePillClass;
    } else if (bType === 'Cash Paid') {
      const el = document.getElementById('bill-cash');
      if (el) el.className = activePillClass;
    } else if (bType === 'Digital UPI / Card Paid') {
      const el = document.getElementById('bill-digital');
      if (el) el.className = activePillClass;
    }
    updateRoomPickerNote();
  }

  // 3. Table Pill / Dropdown Interaction
  function onTablePillClick(tableId) {
    if (currentOrderType === 'Room Dining') {
      selectOrderType('Table Dine-in');
    }
    selectedTableId = tableId;

    // Sync select dropdown
    const sel = document.getElementById('pos-table-select');
    if (sel) {
      sel.value = tableId;
      if (typeof $ !== 'undefined') {
        $(sel).val(tableId).trigger('change.select2Sync');
      }
    }

    highlightSelectedTablePill();
    renderActiveTableSessionBox();
    updateRoomPickerNote();
  }

  function onTableSelectDropdownChange(val) {
    selectedTableId = val ? parseInt(val, 10) : null;
    highlightSelectedTablePill();
    renderActiveTableSessionBox();
    updateRoomPickerNote();
  }

  function highlightSelectedTablePill() {
    document.querySelectorAll('.pos-table-pill').forEach(btn => {
      btn.classList.remove('ring-2', 'ring-indigo-600', 'scale-105');
    });
    if (selectedTableId) {
      const activePill = document.getElementById('tbl-pill-' + selectedTableId);
      if (activePill) {
        activePill.classList.add('ring-2', 'ring-indigo-600', 'scale-105');
      }
    }
  }

  function renderActiveTableSessionBox() {
    const box = document.getElementById('active-table-session-box');
    if (!box) return;

    if (!selectedTableId || (currentOrderType !== 'Table Dine-in' && currentOrderType !== 'Takeaway')) {
      box.style.display = 'none';
      return;
    }

    const sess = tableSessions[selectedTableId];
    if (!sess || sess.kots_count === 0) {
      box.style.display = 'none';
      return;
    }

    box.style.display = 'block';
    document.getElementById('sess-table-title').textContent = `${sess.table_number} (${sess.table_name})`;
    document.getElementById('sess-kot-count-badge').textContent = `${sess.kots_count} KOT${sess.kots_count > 1 ? 's' : ''} Active`;
    document.getElementById('sess-running-subtotal').textContent = '₹ ' + Number(sess.total_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 });
    
    if (sess.room_number) {
      document.getElementById('sess-guest-line').innerHTML = `<i class="fa-solid fa-user text-amber-600 mr-1"></i> Guest: <strong>${escapeHtml(sess.customer_name || 'Guest')}</strong>`;
      document.getElementById('sess-room-line').textContent = `Room ${sess.room_number}`;
      document.getElementById('sess-room-line').style.display = 'inline-block';
      // Sync room select
      if (sess.room_id) {
        syncRoomSelectors(sess.room_id.toString());
      }
    } else {
      document.getElementById('sess-guest-line').innerHTML = `<i class="fa-solid fa-user-tag text-amber-600 mr-1"></i> Outside Guest: <strong>${escapeHtml(sess.customer_name || 'Walk-in Guest')}</strong>`;
      document.getElementById('sess-room-line').style.display = 'none';
      if (document.getElementById('takeaway-customer-name') && sess.customer_name) {
        document.getElementById('takeaway-customer-name').value = sess.customer_name;
      }
    }
  }

  // 4. Cart Operations
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
    cart = [];
    renderCart();
  }

  function renderCart() {
    const container = document.getElementById('cart-items');
    const badge = document.getElementById('cart-count-badge');
    const subVal = document.getElementById('sub-val');
    const taxVal = document.getElementById('tax-val');
    const totVal = document.getElementById('tot-val');

    const totalItems = cart.reduce((acc, i) => acc + i.qty, 0);
    if (badge) badge.textContent = totalItems;

    if (cart.length === 0) {
      container.innerHTML = `
        <div class="text-center text-slate-400 py-12 text-xs">
          <i class="fa-solid fa-basket-shopping text-3xl block mb-2 text-slate-300"></i>
          Cart is empty.<br>Click any food card on the left to add items.
        </div>
      `;
      if (subVal) subVal.textContent = '₹ 0';
      if (taxVal) taxVal.textContent = '₹ 0';
      if (totVal) totVal.textContent = '₹ 0';
      updateRoomPickerNote();
      return;
    }

    let html = '';
    let subtotal = 0;

    cart.forEach((item, idx) => {
      const lineTotal = item.price * item.qty;
      subtotal += lineTotal;
      html += `
        <div class="py-2.5 flex items-center justify-between gap-2">
          <div class="flex-1 min-w-0">
            <div class="font-bold text-xs text-slate-900 truncate" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</div>
            <div class="text-[11px] font-mono text-slate-500">₹ ${item.price.toLocaleString()} × ${item.qty}</div>
          </div>
          <div class="flex items-center gap-1.5 shrink-0">
            <button type="button" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-xs font-bold transition-colors cursor-pointer" onclick="changeCartQty(${idx}, -1)">-</button>
            <span class="w-6 text-center text-xs font-bold font-mono text-slate-900">${item.qty}</span>
            <button type="button" class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-xs font-bold transition-colors cursor-pointer" onclick="changeCartQty(${idx}, 1)">+</button>
            <span class="w-16 text-right font-bold font-mono text-xs text-slate-900 ml-1">₹ ${lineTotal.toLocaleString()}</span>
            <button type="button" class="w-6 h-6 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center text-xs transition-colors cursor-pointer ml-1" onclick="removeFromCart(${idx})" title="Remove">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>
        </div>
      `;
    });

    container.innerHTML = html;

    const tax = Math.round(((subtotal * posGstRate) / 100) * 100) / 100;
    const cgst = Math.round((tax / 2) * 100) / 100;
    const sgst = Math.round((tax - cgst) * 100) / 100;
    const halfRate = posGstRate / 2;
    const grandTotal = Math.round((subtotal + tax) * 100) / 100;

    const cgstVal = document.getElementById('cgst-val');
    const sgstVal = document.getElementById('sgst-val');
    const posCgstLabelRate = document.getElementById('pos-cgst-label-rate');
    const posSgstLabelRate = document.getElementById('pos-sgst-label-rate');

    if (subVal) subVal.textContent = '₹ ' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    if (cgstVal) cgstVal.textContent = '₹ ' + cgst.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    if (sgstVal) sgstVal.textContent = '₹ ' + sgst.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    if (posCgstLabelRate) posCgstLabelRate.textContent = (halfRate % 1 === 0 ? halfRate.toString() : halfRate.toFixed(1));
    if (posSgstLabelRate) posSgstLabelRate.textContent = (halfRate % 1 === 0 ? halfRate.toString() : halfRate.toFixed(1));
    if (taxVal) taxVal.textContent = '₹ ' + tax.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    if (totVal) totVal.textContent = '₹ ' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });

    updateRoomPickerNote();
  }

  // 5. Menu Filters and Search
  function filterMenu(cat, btn) {
    document.querySelectorAll('.pos-cat-pill').forEach(b => {
      b.className = 'pos-cat-pill inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer whitespace-nowrap';
    });
    btn.className = 'pos-cat-pill inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap bg-emerald-600 text-white shadow-xs';

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

  function searchMenu(query) {
    const q = query.trim().toLowerCase();
    const cards = document.querySelectorAll('.pos-dish-card');
    cards.forEach(card => {
      const name = (card.getAttribute('data-name') || '').toLowerCase();
      const code = (card.getAttribute('data-code') || '').toLowerCase();
      const sku = (card.getAttribute('data-sku') || '').toLowerCase();
      const cat = (card.getAttribute('data-cat') || '').toLowerCase();
      if (!q || name.includes(q) || code.includes(q) || sku.includes(q) || cat.includes(q)) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  }

  // 6. Post Order or KOT Ticket
  async function postOrder() {
    // If cart is empty and table has active session in Dine-In/Takeaway, clicking button settles table
    if (cart.length === 0) {
      if ((currentOrderType === 'Table Dine-in' || currentOrderType === 'Takeaway') && selectedTableId && tableSessions[selectedTableId]) {
        openTableSettleModal();
        return;
      }
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'warning', title: 'Cart is empty', text: 'Please add dishes or beverages before placing an order!' });
      } else {
        alert('Please add dishes or beverages before placing an order.');
      }
      return;
    }

    const payload = {
      order_type: currentOrderType,
      special_instructions: document.getElementById('order-instructions')?.value?.trim() || null,
      items: cart.map(i => ({
        pos_item_id: i.id,
        name: i.name,
        price: i.price,
        quantity: i.qty,
      }))
    };

    if (currentOrderType === 'Room Dining') {
      // Scenario 1: Immediate direct bill to room folio or cash/upi
      payload.action = 'bill';
      payload.billing_type = currentBillingType;
      
      const cartRm = document.getElementById('cart-room-folio-select');
      const headRm = document.getElementById('pos-room-select');
      const chosenRoomId = (cartRm && cartRm.value) ? cartRm.value : (headRm ? headRm.value : null);

      if (currentBillingType === 'Charged to Room Folio') {
        if (!chosenRoomId) {
          if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Room Required', text: 'Please select an active Checked-In Room to charge this bill to Room Folio!' });
          } else {
            alert('Please select an active Checked-In Room to charge this bill to Room Folio!');
          }
          return;
        }
        payload.room_id = chosenRoomId;
      } else {
        if (chosenRoomId) payload.room_id = chosenRoomId;
      }

    } else if (currentOrderType === 'Table Dine-in') {
      // Scenario 2: Room-wise or Walk-in guest with table -> Send KOT
      payload.action = 'kot';
      payload.is_kot = true;

      if (!selectedTableId) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'warning', title: 'Table Required', text: 'Please select a dining table for Dine-in!' });
        } else {
          alert('Please select a dining table for Dine-in!');
        }
        return;
      }
      payload.table_id = selectedTableId;

      const cartRm = document.getElementById('cart-room-folio-select');
      const headRm = document.getElementById('pos-room-select');
      const chosenRoomId = (cartRm && cartRm.value) ? cartRm.value : (headRm ? headRm.value : null);

      if (currentBillingType === 'Charged to Room Folio' && !chosenRoomId && (!tableSessions[selectedTableId] || !tableSessions[selectedTableId].room_id)) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'warning', title: 'Room Required', text: 'Please select residing guest Room to Charge Room Folio!' });
        } else {
          alert('Please select residing guest Room to Charge Room Folio!');
        }
        return;
      }
      payload.room_id = chosenRoomId || tableSessions[selectedTableId]?.room_id || null;
      if (!payload.room_id && selectedTable) {
        payload.customer_name = `Table ${selectedTable.table_number} Guest`;
      }

    } else if (currentOrderType === 'Takeaway') {
      // Scenario 3: Outside guest with table -> Send KOT
      payload.action = 'kot';
      payload.is_kot = true;

      const custName = document.getElementById('takeaway-customer-name')?.value?.trim();
      const custPhone = document.getElementById('takeaway-customer-phone')?.value?.trim();
      const custGstin = document.getElementById('takeaway-customer-gstin')?.value?.trim();

      if (selectedTableId) {
        payload.table_id = selectedTableId;
      }
      payload.customer_name = custName || 'Outside Walk-in Guest';
      payload.customer_phone = custPhone || null;
      payload.customer_gstin = custGstin || null;
    }

    try {
      const btn = document.getElementById('btn-post-order');
      if (btn) btn.disabled = true;

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
      if (btn) btn.disabled = false;

      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: data.is_kot ? `KOT #${data.kot_sequence} Sent to Kitchen!` : 'Order Placed Successfully!',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          });
        }

        if (data.tableSessions) {
          tableSessions = data.tableSessions;
        }

        // Show KOT thermal slip modal
        showKotModal(data.order);

        // Reset cart
        cart = [];
        renderCart();
        document.getElementById('order-instructions').value = '';

        // If table was used, refresh local session
        if (data.table_id) {
          await refreshTableSessionFromServer(data.table_id);
        }
      } else {
        alert(data.message || 'Error posting order.');
      }
    } catch (e) {
      console.error(e);
      alert('Network or server error while placing order.');
      const btn = document.getElementById('btn-post-order');
      if (btn) btn.disabled = false;
    }
  }

  // Fetch updated table session from server
  async function refreshTableSessionFromServer(tableId) {
    try {
      const res = await fetch(`{{ url('pos/tables') }}/${tableId}/session`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success && data.has_active_session) {
        tableSessions[tableId] = {
          table_id: data.table.id,
          table_number: data.table.table_number,
          table_name: data.table.table_name,
          order_type: data.order_type,
          room_id: data.room_id,
          room_number: data.room_number,
          customer_name: data.customer_name,
          customer_phone: data.customer_phone,
          kots_count: data.kots_count,
          kots: data.kots,
          raw_items: data.raw_items,
          consolidated_items: data.consolidated_items,
          subtotal: data.subtotal,
          gst_rate: data.gst_rate,
          tax_amount: data.tax_amount,
          total_amount: data.total_amount,
        };
      } else {
        delete tableSessions[tableId];
      }
      updateTablePillsDom();
      renderActiveTableSessionBox();
      updateRoomPickerNote();
    } catch(e) {
      console.error('Error refreshing table session', e);
    }
  }

  // Update UI table pills with latest session info
  function updateTablePillsDom() {
    allTablesData.forEach(tbl => {
      const pill = document.getElementById('tbl-pill-' + tbl.id);
      if (!pill) return;
      const sess = tableSessions[tbl.id];
      const isBooked = (sess && sess.kots_count > 0);

      if (isBooked) {
        pill.className = 'pos-table-pill px-3 py-1.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 whitespace-nowrap bg-amber-50 text-amber-900 border-amber-300 hover:bg-amber-100 shadow-2xs';
        pill.innerHTML = `
          <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
          <span>${escapeHtml(tbl.table_number)}</span>
          <span class="text-[10px] font-medium opacity-75">(${escapeHtml(tbl.table_name)})</span>
          <span class="bg-amber-200/80 text-amber-900 text-[10px] font-black px-1.5 py-0.2 rounded-md">
            ${sess.kots_count} KOT${sess.kots_count > 1 ? 's' : ''}
          </span>
          ${sess.room_number ? `<span class="text-[10px] font-bold text-indigo-700">R-${escapeHtml(sess.room_number)}</span>` : ''}
        `;
      } else {
        pill.className = 'pos-table-pill px-3 py-1.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 whitespace-nowrap bg-white text-slate-700 border-slate-200 hover:border-emerald-500 hover:text-emerald-700';
        pill.innerHTML = `
          <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
          <span>${escapeHtml(tbl.table_number)}</span>
          <span class="text-[10px] font-medium opacity-75">(${escapeHtml(tbl.table_name)})</span>
          <span class="text-[10px] font-semibold text-emerald-600">Free</span>
        `;
      }
    });
    highlightSelectedTablePill();
  }

  // 7. Settlement Modal Logic (Single Bill from Multiple KOTs)
  function openTableSettleModal() {
    if (!selectedTableId) {
      alert('Please select a table to settle.');
      return;
    }
    const sess = tableSessions[selectedTableId];
    if (!sess || sess.kots_count === 0) {
      alert('No active running KOTs found on this table.');
      return;
    }

    document.getElementById('settle-dest-label').textContent = `${sess.table_number} (${sess.table_name})`;
    document.getElementById('settle-kots-badge').textContent = `${sess.kots_count} KOT Ticket${sess.kots_count > 1 ? 's' : ''}`;

    const roomBlock = document.getElementById('settle-room-block');
    const optFolio = document.getElementById('opt-settle-folio');
    const cashNote = document.getElementById('settle-cash-note');
    const upiNote = document.getElementById('settle-upi-note');

    if (sess.room_number) {
      // Table Dine-In: Room attached
      if (roomBlock) roomBlock.style.display = 'block';
      document.getElementById('settle-guest-label').textContent = `Room ${sess.room_number} - ${sess.customer_name || 'Guest'}`;
      if (optFolio) optFolio.style.display = 'flex';
      if (cashNote) cashNote.textContent = `Paid in Cash now. Attached to Room ${sess.room_number} as Paid.`;
      if (upiNote) upiNote.textContent = `Paid via UPI now. Attached to Room ${sess.room_number} as Paid.`;
      
      // Default to Room Attach for room guests
      const folioRadio = document.querySelector('input[name="settle_billing_type"][value="Charged to Room Folio"]');
      if (folioRadio) folioRadio.checked = true;
    } else {
      // Takeaway: Outside guest without room
      if (roomBlock) roomBlock.style.display = 'none';
      if (optFolio) optFolio.style.display = 'none';
      if (cashNote) cashNote.textContent = `Paid in Cash at restaurant counter.`;
      if (upiNote) upiNote.textContent = `Paid via UPI / Card at counter.`;
      
      // Default to Cash for takeaway
      const cashRadio = document.querySelector('input[name="settle_billing_type"][value="Cash Paid"]');
      if (cashRadio) cashRadio.checked = true;
    }

    // Pre-fill GSTIN if previously entered
    const gstinInput = document.getElementById('settle-customer-gstin');
    if (gstinInput) {
      gstinInput.value = sess.customer_gstin || '';
    }

    // Populate Consolidated Items
    const tbody = document.getElementById('settle-items-tbody');
    let itemsHtml = '';
    let totalQty = 0;

    (sess.consolidated_items || []).forEach(it => {
      totalQty += it.quantity;
      itemsHtml += `
        <tr>
          <td class="px-3 py-2 font-bold text-slate-900">${escapeHtml(it.item_name)}</td>
          <td class="px-3 py-2 text-center font-mono font-bold">${it.quantity}</td>
          <td class="px-3 py-2 text-right font-mono text-slate-600">₹ ${Number(it.unit_price).toFixed(2)}</td>
          <td class="px-3 py-2 text-right font-mono font-bold text-slate-900">₹ ${Number(it.line_total).toFixed(2)}</td>
        </tr>
      `;
    });
    tbody.innerHTML = itemsHtml;
    document.getElementById('settle-items-total-qty').textContent = `${totalQty} items total`;

    // Totals
    const gstRate = parseFloat(sess.gst_rate || 5);
    const halfRate = gstRate / 2;
    const taxAmt = Number(sess.tax_amount || 0);
    const cgstAmt = Math.round((taxAmt / 2) * 100) / 100;
    const sgstAmt = Math.round((taxAmt - cgstAmt) * 100) / 100;

    document.getElementById('settle-subtotal-val').textContent = '₹ ' + Number(sess.subtotal).toLocaleString('en-IN', { minimumFractionDigits: 2 });
    if (document.getElementById('settle-cgst-rate-val')) document.getElementById('settle-cgst-rate-val').textContent = (halfRate % 1 === 0 ? halfRate.toString() : halfRate.toFixed(1));
    if (document.getElementById('settle-sgst-rate-val')) document.getElementById('settle-sgst-rate-val').textContent = (halfRate % 1 === 0 ? halfRate.toString() : halfRate.toFixed(1));
    if (document.getElementById('settle-cgst-val')) document.getElementById('settle-cgst-val').textContent = '₹ ' + cgstAmt.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    if (document.getElementById('settle-sgst-val')) document.getElementById('settle-sgst-val').textContent = '₹ ' + sgstAmt.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    if (document.getElementById('settle-gst-rate-val')) document.getElementById('settle-gst-rate-val').textContent = sess.gst_rate;
    if (document.getElementById('settle-tax-val')) document.getElementById('settle-tax-val').textContent = '₹ ' + taxAmt.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    document.getElementById('settle-grand-total-val').textContent = '₹ ' + Number(sess.total_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 });

    document.getElementById('table-settle-modal').style.display = 'flex';
  }

  function closeTableSettleModal() {
    document.getElementById('table-settle-modal').style.display = 'none';
  }

  async function confirmTableSettlement() {
    if (!selectedTableId) return;
    const selectedMode = document.querySelector('input[name="settle_billing_type"]:checked')?.value;
    if (!selectedMode) {
      alert('Please choose a settlement mode.');
      return;
    }

    try {
      const btn = document.getElementById('btn-confirm-settlement');
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Settlement...';
      }

      const gstinVal = document.getElementById('settle-customer-gstin')?.value?.trim();

      const res = await fetch(`{{ url('pos/tables') }}/${selectedTableId}/settle`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        },
        body: JSON.stringify({
          billing_type: selectedMode,
          customer_gstin: gstinVal || null
        })
      });

      const data = await res.json();
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Confirm Settlement & Print Final Bill';
      }

      if (data.success) {
        closeTableSettleModal();

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Table Settled & Bill Generated!',
            text: data.message,
            timer: 2500,
            showConfirmButton: false
          });
        }

        // Show Single Consolidated Final Bill
        showKotModal(data.bill);

        // Update table sessions
        if (data.tableSessions) {
          tableSessions = data.tableSessions;
        } else {
          delete tableSessions[selectedTableId];
        }
        selectedTableId = null;
        updateTablePillsDom();
        renderActiveTableSessionBox();
        renderCart();
        updateRoomPickerNote();

      } else {
        alert(data.message || 'Settlement failed.');
      }
    } catch(e) {
      console.error(e);
      alert('Network or server error during table settlement.');
      const btn = document.getElementById('btn-confirm-settlement');
      if (btn) btn.disabled = false;
    }
  }

  // 8. View Running KOTs Viewer Modal (Only quantity, no rate shown; item cancel & KOT edit supported)
  function openRunningKotsModal() {
    if (!selectedTableId || !tableSessions[selectedTableId]) return;
    const sess = tableSessions[selectedTableId];

    const totalQty = (sess.raw_items || []).reduce((acc, ri) => acc + (parseInt(ri.quantity) || 1), 0);

    document.getElementById('running-kots-modal-subtitle').textContent = 
      `Running KOT items on ${sess.table_number} (${sess.table_name}) • Total Items: ${totalQty}`;

    const container = document.getElementById('running-kots-modal-content');
    let html = '';

    (sess.kots || []).forEach((k, idx) => {
      // Find raw items for this KOT
      const kotItems = (k.items && k.items.length > 0)
        ? k.items
        : (sess.raw_items || []).filter(ri => ri.kot_id === k.id);

      html += `
        <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs space-y-2.5">
          <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <div class="flex items-center gap-2">
              <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 text-xs font-black flex items-center justify-center">#${idx + 1}</span>
              <span class="font-mono font-bold text-xs text-slate-900">${escapeHtml(k.order_number)}</span>
            </div>
            <div class="flex items-center gap-2">
              <button type="button" onclick="openEditKotModal(${k.id})" class="px-2 py-0.5 text-[11px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-md transition-colors cursor-pointer flex items-center gap-1" title="Edit items in this KOT">
                <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit KOT
              </button>
              <div class="text-[11px] text-slate-500 font-medium">
                <i class="fa-regular fa-clock text-[10px]"></i> ${k.time}
              </div>
            </div>
          </div>
          <div class="divide-y divide-slate-100 text-xs">
            ${kotItems.map(it => {
              const resolvedId = it.id || (sess.raw_items || []).find(r => r.kot_id == k.id && r.item_name == it.item_name && r.id)?.id || 0;
              return `
                <div class="py-2 flex justify-between items-center gap-2">
                  <div>
                    <span class="font-bold text-slate-900 text-xs">${escapeHtml(it.item_name)}</span>
                    ${it.instructions ? `<div class="text-[10px] text-amber-700 italic">Note: ${escapeHtml(it.instructions)}</div>` : ''}
                  </div>
                  <div class="flex items-center gap-2.5">
                    <span class="px-2.5 py-0.5 bg-amber-100 text-amber-900 font-mono font-black text-xs rounded-md">Qty: ${it.quantity}</span>
                    <button type="button" onclick="cancelKotItem(${k.id}, ${resolvedId}, '${escapeHtml(it.item_name)}')" class="px-2 py-0.5 text-[10.5px] font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-md transition-colors cursor-pointer flex items-center gap-1" title="Cancel this item from KOT">
                      <i class="fa-solid fa-trash-can text-[9px]"></i> Cancel
                    </button>
                  </div>
                </div>
              `;
            }).join('')}
          </div>
        </div>
      `;
    });

    container.innerHTML = html || '<div class="text-center text-slate-400 py-6 text-xs">No active KOT items found.</div>';
    document.getElementById('running-kots-modal').style.display = 'flex';
  }

  function closeRunningKotsModal() {
    document.getElementById('running-kots-modal').style.display = 'none';
  }

  // Cancel an individual food item inside a KOT
  async function cancelKotItem(orderId, itemId, itemName) {
    const result = (typeof Swal !== 'undefined')
      ? await Swal.fire({
          title: 'Cancel Item from KOT?',
          text: `Are you sure you want to cancel "${itemName}" from this KOT? Kitchen store inventory will be restored.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#e11d48',
          cancelButtonColor: '#64748b',
          confirmButtonText: 'Yes, Cancel Item',
          cancelButtonText: 'No, Keep'
        })
      : { isConfirmed: confirm(`Cancel "${itemName}" from KOT?`) };

    if (!result.isConfirmed) return;

    try {
      const targetUrl = (itemId && itemId !== 'undefined' && parseInt(itemId) > 0)
        ? `/pos/orders/${orderId}/items/${itemId}/cancel`
        : `/pos/orders/${orderId}/cancel-item`;

      const resp = await fetch(targetUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          item_id: itemId,
          item_name: itemName
        })
      });
      const data = await resp.json();

      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Item Cancelled',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          });
        }

        tableSessions = data.tableSessions;
        if (data.allTables) allTablesData = data.allTables;

        if (data.table_released || !tableSessions[selectedTableId]) {
          closeRunningKotsModal();
          selectedTableId = null;
        }

        updateTablePillsDom();
        renderActiveTableSessionBox();
        renderCart();

        if (selectedTableId && tableSessions[selectedTableId]) {
          openRunningKotsModal();
        }
      } else {
        alert(data.message || 'Failed to cancel item.');
      }
    } catch (e) {
      console.error(e);
      alert('Error cancelling item.');
    }
  }

  // Edit KOT Items & Quantities Modal
  let activeEditingKotId = null;
  let activeEditingKotItems = [];

  function openEditKotModal(orderId) {
    if (!selectedTableId || !tableSessions[selectedTableId]) return;
    const sess = tableSessions[selectedTableId];
    const kot = (sess.kots || []).find(k => k.id == orderId);
    if (!kot) return;

    activeEditingKotId = orderId;
    activeEditingKotItems = [];

    const rawItems = (kot.items && kot.items.length > 0)
      ? kot.items
      : (sess.raw_items || []).filter(ri => ri.kot_id == orderId);

    activeEditingKotItems = rawItems.map(it => ({
      id: it.id || null,
      pos_item_id: it.pos_item_id || null,
      name: it.item_name,
      price: parseFloat(it.unit_price) || 0,
      quantity: parseInt(it.quantity) || 1,
      instructions: it.instructions || '',
    }));

    document.getElementById('edit-kot-modal-subtitle').textContent = 
      `KOT #${kot.order_number} (${kot.time}) on Table ${sess.table_number}`;

    renderEditKotItemsList();
    document.getElementById('edit-kot-modal').style.display = 'flex';
  }

  function closeEditKotModal() {
    document.getElementById('edit-kot-modal').style.display = 'none';
    activeEditingKotId = null;
    activeEditingKotItems = [];
  }

  function renderEditKotItemsList() {
    const list = document.getElementById('edit-kot-items-list');
    if (!list) return;

    if (activeEditingKotItems.length === 0) {
      list.innerHTML = `<div class="text-center py-4 text-xs text-rose-500 font-bold">No items left in this KOT. Please add an item or cancel using the cancel item button.</div>`;
      return;
    }

    list.innerHTML = activeEditingKotItems.map((it, idx) => `
      <div class="flex items-center justify-between p-2 bg-white rounded-lg border border-slate-200 text-xs">
        <div class="flex-1 pr-2">
          <div class="font-bold text-slate-800">${escapeHtml(it.name)}</div>
          ${it.instructions ? `<div class="text-[10px] text-slate-400 italic">${escapeHtml(it.instructions)}</div>` : ''}
        </div>
        <div class="flex items-center gap-2">
          <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden bg-slate-50">
            <button type="button" onclick="changeEditKotQty(${idx}, -1)" class="w-6 h-6 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-200 cursor-pointer">-</button>
            <span class="w-8 text-center font-mono font-bold text-xs">${it.quantity}</span>
            <button type="button" onclick="changeEditKotQty(${idx}, 1)" class="w-6 h-6 flex items-center justify-center font-bold text-slate-600 hover:bg-slate-200 cursor-pointer">+</button>
          </div>
          <button type="button" onclick="removeEditKotItem(${idx})" class="w-6 h-6 rounded-lg text-rose-500 hover:bg-rose-50 flex items-center justify-center cursor-pointer" title="Remove item">
            <i class="fa-solid fa-trash-can text-xs"></i>
          </button>
        </div>
      </div>
    `).join('');
  }

  function changeEditKotQty(idx, delta) {
    if (!activeEditingKotItems[idx]) return;
    activeEditingKotItems[idx].quantity = Math.max(1, activeEditingKotItems[idx].quantity + delta);
    renderEditKotItemsList();
  }

  function removeEditKotItem(idx) {
    if (!activeEditingKotItems[idx]) return;
    activeEditingKotItems.splice(idx, 1);
    renderEditKotItemsList();
  }

  function addDishToEditKot() {
    const select = document.getElementById('edit-kot-add-item-select');
    if (!select || !select.value) return;

    const opt = select.options[select.selectedIndex];
    const dishId = select.value;
    const name = opt.getAttribute('data-name');
    const price = parseFloat(opt.getAttribute('data-price')) || 0;

    const existing = activeEditingKotItems.find(it => it.pos_item_id == dishId);
    if (existing) {
      existing.quantity += 1;
    } else {
      activeEditingKotItems.push({
        id: null,
        pos_item_id: dishId,
        name: name,
        price: price,
        quantity: 1,
        instructions: ''
      });
    }

    select.value = '';
    renderEditKotItemsList();
  }

  async function saveEditKot() {
    if (!activeEditingKotId) return;

    if (activeEditingKotItems.length === 0) {
      alert('KOT must have at least 1 item! To cancel the entire KOT, use the Cancel Item button in the running KOTs modal.');
      return;
    }

    const btn = document.getElementById('btn-save-edit-kot');
    if (btn) btn.disabled = true;

    try {
      const resp = await fetch(`/pos/orders/${activeEditingKotId}/update-items`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          items: activeEditingKotItems
        })
      });

      const data = await resp.json();
      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'KOT Updated',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          });
        }

        tableSessions = data.tableSessions;
        if (data.allTables) allTablesData = data.allTables;

        closeEditKotModal();
        updateTablePillsDom();
        renderActiveTableSessionBox();
        renderCart();
        openRunningKotsModal();
      } else {
        alert(data.message || 'Failed to update KOT.');
      }
    } catch(e) {
      console.error(e);
      alert('Error saving KOT changes.');
    } finally {
      if (btn) btn.disabled = false;
    }
  }

  // Shift Dining Table (Table A -> Table B)
  function openShiftTableModal(tableId = null) {
    const tid = tableId || selectedTableId;
    if (!tid || !tableSessions[tid]) {
      alert('Please select an active table session to shift!');
      return;
    }
    const sess = tableSessions[tid];

    document.getElementById('shift-source-table-name').textContent = `Table ${sess.table_number} (${sess.table_name})`;
    document.getElementById('shift-source-table-details').textContent = 
      `${sess.kots_count} active KOT(s) • Guest: ${sess.customer_name || 'Walk-in'}`;

    const select = document.getElementById('shift-target-table-select');
    select.innerHTML = '<option value="">-- Choose Target Table --</option>';

    allTablesData.forEach(tbl => {
      if (tbl.id == tid) return; // Skip current table
      const isAvailable = tbl.status === 'available';
      const isOccupied = tbl.status === 'booked';
      const label = `Table ${tbl.table_number} (${tbl.table_name}) - ${isAvailable ? 'Available' : (isOccupied ? 'Occupied (Merge KOTs)' : 'Inactive')}`;
      if (tbl.status !== 'inactive') {
        select.innerHTML += `<option value="${tbl.id}">${label}</option>`;
      }
    });

    document.getElementById('shift-table-modal').setAttribute('data-source-table-id', tid);
    document.getElementById('shift-table-modal').style.display = 'flex';
  }

  function closeShiftTableModal() {
    document.getElementById('shift-table-modal').style.display = 'none';
  }

  async function confirmShiftTable() {
    const modal = document.getElementById('shift-table-modal');
    const sourceTableId = modal.getAttribute('data-source-table-id') || selectedTableId;
    const targetTableId = document.getElementById('shift-target-table-select').value;

    if (!targetTableId) {
      alert('Please select a target table to shift to!');
      return;
    }

    const btn = document.getElementById('btn-confirm-shift-table');
    if (btn) btn.disabled = true;

    try {
      const resp = await fetch(`/pos/tables/${sourceTableId}/shift`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          target_table_id: targetTableId
        })
      });

      const data = await resp.json();
      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Table Shifted!',
            text: data.message,
            timer: 2500,
            showConfirmButton: false
          });
        }

        tableSessions = data.tableSessions;
        allTablesData = data.allTables;

        closeShiftTableModal();

        // Switch selection to new table
        selectedTableId = parseInt(data.new_table_id);
        const tblSelect = document.getElementById('pos-table-select');
        if (tblSelect) {
          tblSelect.value = selectedTableId.toString();
          if (typeof $ !== 'undefined' && $(tblSelect).data('select2')) {
            $(tblSelect).trigger('change.select2Sync');
          }
        }

        updateTablePillsDom();
        renderActiveTableSessionBox();
        renderCart();
        updateRoomPickerNote();
      } else {
        alert(data.message || 'Failed to shift table.');
      }
    } catch(e) {
      console.error(e);
      alert('Error shifting table.');
    } finally {
      if (btn) btn.disabled = false;
    }
  }

  // 9. KOT Thermal Slip Modal Handling (Supports KOT tickets and Final Bills)
  let activeKotOrder = null;

  function showKotModal(order) {
    activeKotOrder = order;
    const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    let dest = order.order_type;
    if (order.table_number) {
      dest = order.table_number;
      if (order.room_number) {
        dest += ` (Folio: Room ${order.room_number})`;
      }
    } else if (order.room_number) {
      dest = `Room ${order.room_number}`;
    }

    const guestName = order.customer_name || 'Guest';
    const isKot = (order.is_kot === true || order.is_kot === 1);
    const billingType = isKot ? 'Pending Settlement' : (order.billing_type || 'Paid');
    const grandTotalFmt = Number(order.total_amount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const notesStr = order.special_instructions ? `Instructions: ${order.special_instructions}` : 'Instructions: None';

    // Update Modal Title & Badge
    if (isKot) {
      document.getElementById('kot-modal-title').innerHTML = '<i class="fa-solid fa-kitchen-set text-amber-400"></i> Kitchen Order Token (KOT Slip)';
      document.getElementById('kot-modal-subtitle').textContent = `Kitchen copy for order #${order.order_number}`;
      document.getElementById('kot-guest-badge-header').textContent = '*** KOT ORDER SUMMARY ***';
      document.getElementById('kot-kitchen-badge-header').textContent = `*** DUPLICATE COPY (FOR KITCHEN) - KOT #${order.kot_sequence || 1} ***`;
    } else {
      document.getElementById('kot-modal-title').innerHTML = '<i class="fa-solid fa-receipt text-emerald-400"></i> Final POS Guest Bill &amp; Tax Invoice';
      document.getElementById('kot-modal-subtitle').textContent = `Consolidated bill #${order.order_number} for customer`;
      document.getElementById('kot-guest-badge-header').textContent = '*** ORIGINAL BILL (FOR GUEST) ***';
      document.getElementById('kot-kitchen-badge-header').textContent = '*** DUPLICATE COPY (RESTAURANT AUDIT) ***';
    }

    let guestItemsHtml = '';
    let kitchenItemsHtml = '';
    let totQty = 0;

    (order.items || []).forEach(it => {
      const q = parseInt(it.quantity, 10);
      totQty += q;
      const lineTotal = Number(it.line_total || (it.unit_price * q)).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      
      if (isKot) {
        guestItemsHtml += `
          <tr>
            <td class="py-1">
              <div class="font-bold text-slate-900">${escapeHtml(it.item_name)}</div>
              ${it.instructions ? `<div class="text-[9.5px] italic text-slate-500">${escapeHtml(it.instructions)}</div>` : ''}
            </td>
            <td class="text-center font-bold font-mono py-1">${q}</td>
          </tr>
        `;
      } else {
        guestItemsHtml += `
          <tr>
            <td class="py-1">
              <div class="font-bold text-slate-900">${escapeHtml(it.item_name)}</div>
              ${it.instructions ? `<div class="text-[9.5px] italic text-slate-500">${escapeHtml(it.instructions)}</div>` : ''}
            </td>
            <td class="text-center font-bold font-mono py-1">${q}</td>
            <td class="text-right font-mono py-1">₹ ${lineTotal}</td>
          </tr>
        `;
      }

      kitchenItemsHtml += `
        <tr>
          <td class="py-1">
            <div class="font-bold text-slate-900">${escapeHtml(it.item_name)}</div>
            ${it.instructions ? `<div class="text-[10px] font-bold text-rose-700 bg-rose-50 px-1 rounded inline-block mt-0.5">Note: ${escapeHtml(it.instructions)}</div>` : ''}
          </td>
          <td class="text-center font-black text-xs font-mono py-1 text-slate-900">${q}</td>
        </tr>
      `;
    });

    // Populate Guest Ticket
    document.getElementById('kot-guest-order-num').innerHTML = `<strong>${isKot ? 'KOT' : 'BILL'}: ${order.order_number}</strong>`;
    document.getElementById('kot-guest-time').textContent = `Time: ${timeStr}`;
    document.getElementById('kot-guest-dest').textContent = dest;
    document.getElementById('kot-guest-name').textContent = guestName;
    document.getElementById('kot-guest-billing').textContent = billingType;

    // Show / hide GSTIN on guest bill
    const gstinRow = document.getElementById('row-kot-guest-gstin');
    const gstinSpan = document.getElementById('kot-guest-gstin');
    if (gstinRow && gstinSpan) {
      if (order.customer_gstin) {
        gstinRow.style.display = 'block';
        gstinSpan.textContent = order.customer_gstin;
      } else {
        gstinRow.style.display = 'none';
      }
    }

    document.getElementById('kot-guest-items-table').innerHTML = guestItemsHtml;
    document.getElementById('kot-guest-total-qty').textContent = totQty;
    document.getElementById('kot-guest-grand-total').textContent = grandTotalFmt;
    document.getElementById('kot-guest-notes-line').textContent = notesStr;

    // KOT ticket: Hide rates, taxes, grand totals (only quantities shown)
    // Bill invoice: Show rates, taxes, grand totals
    const amountHeader = document.getElementById('kot-guest-amount-header');
    const dividerTax = document.getElementById('divider-kot-guest-tax');
    const taxBreakdownRow = document.getElementById('kot-guest-tax-breakdown');
    const dividerTotal = document.getElementById('divider-kot-guest-total');
    const wrapGrandTotal = document.getElementById('wrap-kot-guest-grand-total');
    const kotUnsettledNote = document.getElementById('kot-guest-unsettled-note');

    if (isKot) {
      if (amountHeader) amountHeader.style.display = 'none';
      if (dividerTax) dividerTax.style.display = 'none';
      if (taxBreakdownRow) taxBreakdownRow.style.display = 'none';
      if (dividerTotal) dividerTotal.style.display = 'none';
      if (wrapGrandTotal) wrapGrandTotal.style.display = 'none';
      if (kotUnsettledNote) kotUnsettledNote.style.display = 'block';
    } else {
      if (amountHeader) amountHeader.style.display = 'table-cell';
      if (dividerTax) dividerTax.style.display = 'block';
      if (taxBreakdownRow) taxBreakdownRow.style.display = 'block';
      if (dividerTotal) dividerTotal.style.display = 'block';
      if (wrapGrandTotal) wrapGrandTotal.style.display = 'inline';
      if (kotUnsettledNote) kotUnsettledNote.style.display = 'none';

      // Tax breakdown for food bill (CGST 2.5% + SGST 2.5%)
      const taxAmt = Number(order.tax_amount || 0);
      const taxRate = parseFloat(order.tax_percentage || 5);
      const halfRate = taxRate / 2;
      const cgstAmt = Math.round((taxAmt / 2) * 100) / 100;
      const sgstAmt = Math.round((taxAmt - cgstAmt) * 100) / 100;
      const subtotalFmt = Number(order.subtotal || (Number(order.total_amount) - taxAmt)).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      const cgstFmt = cgstAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      const sgstFmt = sgstAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

      if (document.getElementById('kot-guest-subtotal')) document.getElementById('kot-guest-subtotal').textContent = subtotalFmt;
      if (document.getElementById('kot-guest-cgst-rate')) document.getElementById('kot-guest-cgst-rate').textContent = (halfRate % 1 === 0 ? halfRate.toString() : halfRate.toFixed(1));
      if (document.getElementById('kot-guest-sgst-rate')) document.getElementById('kot-guest-sgst-rate').textContent = (halfRate % 1 === 0 ? halfRate.toString() : halfRate.toFixed(1));
      if (document.getElementById('kot-guest-cgst-amount')) document.getElementById('kot-guest-cgst-amount').textContent = cgstFmt;
      if (document.getElementById('kot-guest-sgst-amount')) document.getElementById('kot-guest-sgst-amount').textContent = sgstFmt;
    }

    // Populate Kitchen Ticket (Only quantities, no amount)
    document.getElementById('kot-kitchen-order-num').innerHTML = `<strong>${isKot ? 'KOT' : 'BILL'}: ${order.order_number}</strong>`;
    document.getElementById('kot-kitchen-time').textContent = `Time: ${timeStr}`;
    document.getElementById('kot-kitchen-dest').textContent = dest;
    document.getElementById('kot-kitchen-name').textContent = guestName;
    document.getElementById('kot-kitchen-billing').textContent = billingType;
    document.getElementById('kot-kitchen-items-table').innerHTML = kitchenItemsHtml;
    document.getElementById('kot-kitchen-total-qty').textContent = totQty;
    document.getElementById('kot-kitchen-notes-line').textContent = notesStr;

    // If KOT: default preview to Kitchen Copy. If Bill: default to Guest Copy
    if (isKot) {
      switchKotPreview('kitchen');
    } else {
      switchKotPreview('guest');
    }
    document.getElementById('kot-modal').style.display = 'flex';
  }

  function switchKotPreview(mode) {
    const tGuest = document.getElementById('kot-ticket-guest');
    const tKitchen = document.getElementById('kot-ticket-kitchen');
    const strip = document.getElementById('kot-tear-strip');
    const bBoth = document.getElementById('tab-kot-both');
    const bGuest = document.getElementById('tab-kot-guest');
    const bKitchen = document.getElementById('tab-kot-kitchen');

    [bBoth, bGuest, bKitchen].forEach(b => {
      if (b) b.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold text-slate-600 hover:text-slate-900 cursor-pointer transition-all';
    });

    if (mode === 'guest') {
      if (tGuest) tGuest.style.display = 'block';
      if (tKitchen) tKitchen.style.display = 'none';
      if (strip) strip.style.display = 'none';
      if (bGuest) bGuest.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-600 text-white shadow-xs cursor-pointer transition-all';
    } else if (mode === 'kitchen') {
      if (tGuest) tGuest.style.display = 'none';
      if (tKitchen) tKitchen.style.display = 'block';
      if (strip) strip.style.display = 'none';
      if (bKitchen) bKitchen.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-600 text-white shadow-xs cursor-pointer transition-all';
    } else {
      if (tGuest) tGuest.style.display = 'block';
      if (tKitchen) tKitchen.style.display = 'block';
      if (strip) strip.style.display = 'block';
      if (bBoth) bBoth.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-600 text-white shadow-xs cursor-pointer transition-all';
    }
  }

  function closeKotModal() {
    document.getElementById('kot-modal').style.display = 'none';
  }

  function printThermalTicket(mode = 'both') {
    const tGuest = document.getElementById('kot-ticket-guest');
    const tKitchen = document.getElementById('kot-ticket-kitchen');
    if (!tGuest || !tKitchen) return;

    let printHtml = '';
    if (mode === 'guest') {
      printHtml = tGuest.outerHTML;
    } else if (mode === 'kitchen') {
      printHtml = tKitchen.outerHTML;
    } else {
      printHtml = `
        ${tGuest.outerHTML}
        <div class="print-cut-line">
          - - - - - - - - - - - - - - - - - - - -<br>
          ✂ CUT / TEAR HERE (KITCHEN COPY) ✂<br>
          - - - - - - - - - - - - - - - - - - - -
        </div>
        ${tKitchen.outerHTML}
      `;
    }

    let iframe = document.getElementById('kot-print-frame');
    if (!iframe) {
      iframe = document.createElement('iframe');
      iframe.id = 'kot-print-frame';
      iframe.style.position = 'fixed';
      iframe.style.right = '0';
      iframe.style.bottom = '0';
      iframe.style.width = '0';
      iframe.style.height = '0';
      iframe.style.border = '0';
      document.body.appendChild(iframe);
    }
    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write('<!DOCTYPE html><html><' + 'head><title>Thermal Ticket</title><style>');
    doc.write(`
      @page { size: 80mm auto; margin: 2mm; }
      body { font-family: 'Courier New', Courier, monospace, -apple-system; padding: 2mm; font-size: 11.5px; color: #000; margin: 0; background: #fff; }
      .kot-ticket-slip { width: 100%; max-width: 310px; margin: 0 auto; page-break-inside: avoid; }
      h3 { text-align: center; margin: 0 0 3px; font-size: 14px; font-weight: 900; }
      .center-sub { text-align: center; font-size: 10.5px; margin: 3px 0; }
      .divider-line { border-bottom: 1px dashed #000; margin: 6px 0; }
      table { width: 100%; border-collapse: collapse; margin: 5px 0; font-size: 11px; }
      th, td { padding: 3px 0; vertical-align: top; }
      th { border-bottom: 1px dashed #000; font-weight: 800; }
      .print-cut-line { text-align: center; font-family: monospace; font-size: 10px; margin: 12px 0; color: #555; }
    `);
    doc.write('</style></' + 'head><body>');
    doc.write(printHtml);
    doc.write('</body></html>');
    doc.close();

    setTimeout(() => {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    }, 350);
  }

  // 10. Table Manage Modal functions
  function openTableManageModal() {
    document.getElementById('table-manage-modal').style.display = 'flex';
  }

  function closeTableManageModal() {
    document.getElementById('table-manage-modal').style.display = 'none';
  }

  async function updateLiveTableStatus(tableId, status) {
    try {
      const res = await fetch(`{{ url('pos/tables') }}/${tableId}/status`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        },
        body: JSON.stringify({ status })
      });
      const data = await res.json();
      if (data.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        } else {
          alert(data.message);
        }
        allTablesData = data.tables;
        updateTablePillsDom();
        if (status === 'available') {
          delete tableSessions[tableId];
          renderActiveTableSessionBox();
        }
      }
    } catch(e) {
      alert('Failed to update table status.');
    }
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
  }

  async function reprintOrderKot(orderId) {
    try {
      const url = "{{ url('pos/orders') }}/" + orderId;
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success && data.order) {
        showKotModal(data.order);
      } else {
        alert(data.message || 'Unable to retrieve KOT.');
      }
    } catch(e) {
      alert('Unable to retrieve KOT.');
    }
  }
</script>
@endpush
