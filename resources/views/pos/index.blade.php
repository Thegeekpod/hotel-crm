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
          <i class="fa-solid fa-receipt text-emerald-500 text-[10px]"></i> Total Kitchen Orders
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
          <i class="fa-solid fa-indian-rupee-sign text-cyan-500 text-[10px]"></i> Total Dining Revenue
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
        <h2 class="text-base font-extrabold text-slate-900">Sea Breeze Dining &amp; In-Room Dining POS</h2>
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

        <!-- Room Selector (Hidden in top bar as room is selected in the Billing section) -->
        <div id="room-select-group" class="hidden" style="display: none !important;">
          <span class="font-bold text-xs text-slate-500 whitespace-nowrap">Bill To Room:</span>
          <select class="h-9.5 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 min-w-[240px]" id="pos-room-select">
            @forelse($selectableRooms as $rm)
              <option value="{{ $rm->id }}" data-room="{{ $rm->room_number }}" data-guest="{{ $rm->guest_name }}">
                Room {{ $rm->room_number }} - {{ $rm->guest_name }}
              </option>
            @empty
              <option value="">No Checked In Guests</option>
            @endforelse
          </select>
        </div>

        <!-- Table Selector (Visible for Table Dine-in & Takeaway) -->
        <div id="table-select-group" class="items-center gap-2 hidden">
          <span class="font-bold text-xs text-slate-500 whitespace-nowrap">Table / Host:</span>
          <select class="h-9.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 px-3 focus:outline-none focus:border-indigo-500 min-w-[220px]" id="pos-table-select">
            <option value="">-- Select Available Table --</option>
            @foreach($availableTables as $tbl)
              <option value="{{ $tbl->table_number }} ({{ $tbl->table_name }})" data-table-id="{{ $tbl->id }}" data-table-num="{{ $tbl->table_number }}" data-table-name="{{ $tbl->table_name }}">
                {{ $tbl->table_number }} ({{ $tbl->table_name }})
              </option>
            @endforeach
          </select>
        </div>

        <!-- Customer Name for Takeaway -->
        <div id="takeaway-name-group" class="items-center gap-2 hidden">
          <span class="font-bold text-xs text-slate-500 whitespace-nowrap">Customer:</span>
          <input type="text" id="pos-takeaway-name" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-44" placeholder="Guest Name / Phone">
        </div>

        <!-- Quick Modals Buttons -->
        <a href="{{ route('stores.index') }}" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs" title="Manage Store & Menus">
          <i class="fa-solid fa-boxes-stacked text-emerald-600"></i> Manage Store
        </a>
        <a href="{{ route('pos.orders.index') }}" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs" title="View All KOT Tickets & Orders">
          <i class="fa-solid fa-clock-rotate-left text-cyan-600"></i> KOT History
        </a>
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
            <input type="text" id="menu-search-input" placeholder="Search dish, beverage..." onkeyup="searchMenu(this.value)" class="h-9 pl-8 pr-3 text-xs font-medium rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all w-full">
          </div>
        </div>

        <!-- Food Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5" id="pos-menu-grid">
          @forelse($menuItems as $dish)
            @php
              $plates = $dish->available_plates;
              $isSoldOut = ($plates <= 0);
              $isLow = ($plates > 0 && $plates <= ($dish->min_alert_plates ?: 10));
            @endphp
            <div class="pos-dish-card bg-white rounded-xl border border-slate-200 hover:border-emerald-500 hover:shadow-md transition-all p-3.5 flex flex-col justify-between cursor-pointer group {{ $isSoldOut ? 'opacity-55 border-rose-200 bg-rose-50/20' : '' }}" 
                 data-cat="{{ $dish->category }}" 
                 data-name="{{ strtolower($dish->name) }}"
                 onclick="{{ $isSoldOut ? 'PmsAlert.toast(\'This dish is OUT OF STOCK in kitchen store!\', \'warning\')' : 'addToCart(' . $dish->id . ', \'' . addslashes($dish->name) . '\', ' . $dish->price . ', ' . $plates . ')' }}">
              <div>
                <div class="text-3xl text-center py-1 group-hover:scale-110 transition-transform">{{ $dish->emoji ?: '🍽️' }}</div>
                <div class="font-bold text-xs text-slate-800 truncate group-hover:text-emerald-700 transition-colors mt-1" title="{{ $dish->name }}">{{ $dish->name }}</div>
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

      <!-- Right Column: Active Cart / KOT Ticket -->
      <div class="lg:col-span-5 xl:col-span-4 p-5 bg-slate-50/50 flex flex-col justify-between">
        <div>
          <!-- Cart Header -->
          <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-cart-shopping text-emerald-600"></i>
              <span class="font-extrabold text-xs text-slate-900">Active Order / KOT Ticket</span>
              <span id="cart-count-badge" class="bg-emerald-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full">0</span>
            </div>
            <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 rounded-lg transition-all cursor-pointer" onclick="clearCart()">
              <i class="fa-solid fa-trash-can text-[10px]"></i> Clear
            </button>
          </div>

          <!-- Scrollable Cart Line Items -->
          <div id="cart-items" class="h-64 overflow-y-auto divide-y divide-slate-100 pr-1 my-2">
            <div class="text-center text-slate-400 py-12 text-xs">
              <i class="fa-solid fa-basket-shopping text-3xl block mb-2 text-slate-300"></i>
              Cart is empty.<br>Click any food card on the left to add items.
            </div>
          </div>

          <!-- Special Instructions / Notes -->
          <div class="mt-2">
            <input type="text" id="order-instructions" class="h-9 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full placeholder:text-slate-400" placeholder="Kitchen instructions (e.g., Less spicy, no onion)...">
          </div>

          <!-- Billing Mode Selector -->
          <div class="mt-4">
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

            <!-- Room Folio Picker (Visible for Room Folio [Required] or Cash/UPI [Optional]) -->
            <div id="room-folio-picker-box" class="mt-3 bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 space-y-2">
              <label id="room-picker-label" class="text-xs font-bold text-emerald-800 flex items-center justify-between">
                <span id="room-picker-title" class="flex items-center gap-1.5"><i class="fa-solid fa-door-open text-emerald-600"></i> Select Room to Charge Folio: *</span>
                <span id="room-picker-badge" class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold">In-House Folio</span>
              </label>
              <select class="w-full h-9 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500" id="cart-room-folio-select">
                <option value="">-- Direct Walk-in / No Room --</option>
                @forelse($selectableRooms as $rm)
                  <option value="{{ $rm->id }}" data-room="{{ $rm->room_number }}" data-guest="{{ $rm->guest_name }}">
                    Room {{ $rm->room_number }} - {{ $rm->guest_name }}
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
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-user-tag text-amber-600"></i> Walk-in Takeaway Guest Details: *</span>
                <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-bold">Takeaway Customer</span>
              </label>
              <div class="grid grid-cols-2 gap-2">
                <div>
                  <input type="text" id="takeaway-customer-name" class="w-full h-8.5 px-3 text-xs font-medium rounded-lg border border-amber-300 bg-white focus:outline-none focus:border-amber-500" placeholder="Customer Name *">
                </div>
                <div>
                  <input type="text" id="takeaway-customer-phone" class="w-full h-8.5 px-3 text-xs font-medium rounded-lg border border-amber-300 bg-white focus:outline-none focus:border-amber-500" placeholder="Phone Number">
                </div>
              </div>
              <div class="text-[10px] text-amber-700 font-medium flex items-center gap-1 mt-1">
                <i class="fa-solid fa-circle-info"></i> Direct restaurant walk-in order. Recorded in Night Audit payments with direct Cash/UPI settlement.
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
            <div class="flex items-center justify-between text-slate-500 font-medium">
              <span>GST (12%):</span>
              <span id="tax-val" class="font-mono text-slate-500">₹ 0</span>
            </div>
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-sm font-black text-slate-900">
              <span>Total Bill:</span>
              <span id="tot-val" class="font-mono text-emerald-600">₹ 0</span>
            </div>
          </div>

          <!-- Main Submission Button -->
          <button type="button" class="w-full h-11 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer mt-3" id="btn-post-order" onclick="postOrder()">
            <i class="fa-solid fa-check"></i> <span id="btn-order-label">Post Bill to Room Folio</span>
          </button>
        </div>
      </div>

    </div>

    <!-- Footer Bar matching static -->
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

<!-- MODAL 1: KOT / Thermal Invoice Slip Modal -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="kot-modal">
  <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
    <div class="bg-slate-900 text-white px-5 py-3.5 flex items-center justify-between">
      <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
        <i class="fa-solid fa-print text-indigo-400"></i> Kitchen Order Ticket (KOT)
      </h3>
      <button type="button" onclick="closeKotModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-base cursor-pointer transition-colors">&times;</button>
    </div>
    <div class="p-5 bg-slate-50 overflow-y-auto max-h-[70vh]">
      <div class="thermal-ticket-card" id="printable-kot-area">
        <h3>HOTEL SAGAR SONNET</h3>
        <div class="center-sub">Sea Breeze Dining &amp; Room Dining<br>*** KITCHEN ORDER TICKET (KOT) ***</div>
        <div class="divider-line"></div>
        <div class="flex justify-between">
          <span id="kot-order-num"><strong>KOT: POS-2026-0001</strong></span>
          <span id="kot-time">Time: --:--</span>
        </div>
        <div><strong>Type:</strong> <span id="kot-dest">Room 102</span></div>
        <div><strong>Guest / Host:</strong> <span id="kot-guest">In-House Guest</span></div>
        <div><strong>Billing Mode:</strong> <span id="kot-billing" class="font-bold text-sky-600">Charged to Room Folio</span></div>
        <div class="divider-line"></div>
        <table class="w-full border-collapse text-left">
          <thead>
            <tr class="border-b border-black">
              <th>Item</th>
              <th class="text-center w-10">Qty</th>
              <th class="text-right w-16">Amount</th>
            </tr>
          </thead>
          <tbody id="kot-items-table">
            <!-- Dynamic KOT Items -->
          </tbody>
        </table>
        <div class="divider-line"></div>
        <div class="flex justify-between font-bold">
          <span>Total Qty: <span id="kot-total-qty">0</span></span>
          <span>Grand Total: ₹ <span id="kot-grand-total">0</span></span>
        </div>
        <div class="divider-line"></div>
        <div id="kot-notes-line" class="italic text-[11px]">
          Instructions: None
        </div>
        <div class="divider-line"></div>
        <div class="center-sub mt-1.5">Served by: Debashis Roy | System Printed</div>
      </div>
    </div>
    <div class="px-5 py-3.5 bg-white border-t border-slate-200 flex justify-end items-center gap-2">
      <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeKotModal()">Close</button>
      <button type="button" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer" onclick="printThermalTicket()">
        <i class="fa-solid fa-print text-[11px]"></i> Print KOT Ticket
      </button>
    </div>
  </div>
</div>

<!-- MODAL 3: Live Table Management & Status -->
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
  let cart = [];
  let currentOrderType = 'Room Dining';
  let currentBillingType = 'Charged to Room Folio';

  // Helper to sync room selectors between Header and Cart
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
    updateRoomPickerNote();
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

    let subtotal = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
    let tax = Math.round(subtotal * 0.12);
    let grand = subtotal + tax;
    const grandFmt = '₹ ' + grand.toLocaleString();

    const btnLabel = document.getElementById('btn-order-label');

    const isTakeaway = (currentOrderType === 'Takeaway');
    const takeawayCustBox = document.getElementById('takeaway-customer-box');

    if (isTakeaway) {
      if (box) box.style.display = 'none';
      if (takeawayCustBox) takeawayCustBox.style.display = 'block';
      const isCash = (currentBillingType === 'Cash Paid');
      const modeName = isCash ? 'Cash' : 'UPI / Card';
      if (btnLabel) btnLabel.textContent = `Settle Takeaway (${modeName} ${grandFmt}) & Print KOT`;
      return;
    }

    if (takeawayCustBox) takeawayCustBox.style.display = 'none';

    if (currentBillingType === 'Charged to Room Folio') {
      if (box) {
        box.style.display = 'block';
        box.className = 'mt-3 bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 space-y-2 block transition-all';
      }
      if (title) title.innerHTML = '<span class="flex items-center gap-1.5"><i class="fa-solid fa-door-open text-emerald-600"></i> Select Room to Charge Folio: *</span>';
      if (badge) {
        badge.textContent = 'In-House Folio (Adds to Balance)';
        badge.className = 'text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold';
      }
      if (btnLabel) btnLabel.textContent = `Post Bill to Room Folio (${grandFmt})`;

      if (note) {
        if (roomId && roomNo) {
          note.innerHTML = `<span class="text-emerald-800 font-semibold text-xs flex items-center gap-1.5"><i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i> Total ${grandFmt} will be added to <strong>Room ${roomNo} (${guestName})</strong> Folio remaining balance and settled at checkout.</span>`;
        } else {
          note.innerHTML = `<span class="text-rose-600 font-semibold text-xs flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation"></i> Please select a Room to link this bill to the residing guest folio.</span>`;
        }
      }
    } else {
      // Cash Paid or Digital UPI / Card Paid
      const isCash = (currentBillingType === 'Cash Paid');
      const modeName = isCash ? 'Cash' : 'UPI / Card';

      if (box) {
        box.style.display = 'block';
        box.className = 'mt-3 bg-slate-50/70 border border-slate-200 rounded-xl p-3 space-y-2 block transition-all';
      }
      if (title) title.innerHTML = `<span class="flex items-center gap-1.5"><i class="fa-solid fa-link text-slate-500"></i> Link to Room Bill (Optional):</span>`;
      if (badge) {
        badge.textContent = `Optional (${modeName} Paid)`;
        badge.className = 'text-[10px] bg-slate-200 text-slate-700 px-2 py-0.5 rounded-full font-bold';
      }
      if (btnLabel) btnLabel.textContent = `Settle ${modeName} (${grandFmt}) & Print KOT`;

      if (note) {
        if (roomId && roomNo) {
          note.innerHTML = `<div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2 text-emerald-800 text-[11px]">
            <i class="fa-solid fa-circle-check text-emerald-600"></i> <strong>Linked to Room ${roomNo} (${guestName})</strong><br>
            <span class="opacity-90">Bill of <strong>${grandFmt}</strong> will be marked as <strong class="text-emerald-700">PAID (${modeName})</strong> under this booking in Settlement. Does NOT increase remaining balance.</span>
          </div>`;
        } else {
          note.innerHTML = `<span class="text-slate-500 text-[11px]"><i class="fa-solid fa-circle-info"></i> Direct Walk-in settlement. Select a room above if this is an in-house guest paying on the spot.</span>`;
        }
      }
    }
  }

  // Initialize Searchable Select2 Dropdowns
  $(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
      $('#pos-room-select').select2({
        width: '280px',
        placeholder: 'Search Room # or Guest Name...'
      }).on('change', function(e) {
        if (e.namespace !== 'select2Sync') {
          syncRoomSelectors($(this).val());
        }
      });

      $('#pos-table-select').select2({
        width: '220px',
        placeholder: 'Select Dining Table'
      });

      $('#cart-room-folio-select').select2({
        width: '100%',
        placeholder: 'Search Room # or Guest Name...'
      }).on('change', function(e) {
        if (e.namespace !== 'select2Sync') {
          syncRoomSelectors($(this).val());
        }
      });
    }

    // Auto-select room from URL query param if present (e.g. from Room Details modal)
    const urlParams = new URLSearchParams(window.location.search);
    const qRoomId = urlParams.get('room_id');
    if (qRoomId) {
      syncRoomSelectors(qRoomId);
    }
    updateRoomPickerNote();
  });

  // 1. Order Type Switching
  function selectOrderType(type) {
    currentOrderType = type;
    document.querySelectorAll('.pos-order-type-btn').forEach(b => {
      b.className = 'pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition-all cursor-pointer';
    });
    
    const roomGrp = document.getElementById('room-select-group');
    const tableGrp = document.getElementById('table-select-group');
    const takeawayGrp = document.getElementById('takeaway-name-group');
    const folioPill = document.getElementById('bill-folio');
    const folioBox = document.getElementById('room-folio-picker-box');
    const takeawayCustBox = document.getElementById('takeaway-customer-box');

    if (type === 'Room Dining') {
      const btn = document.getElementById('type-room');
      if (btn) btn.className = 'pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-emerald-700 shadow-xs cursor-pointer';
      if (roomGrp) roomGrp.style.display = 'none';
      tableGrp.style.display = 'none';
      if (takeawayGrp) takeawayGrp.style.display = 'none';
      if (folioPill) folioPill.style.display = 'block';
      if (takeawayCustBox) takeawayCustBox.style.display = 'none';
      if (folioBox) folioBox.style.display = 'block';
      setBillingType('Charged to Room Folio');
    } else if (type === 'Table Dine-in') {
      const btn = document.getElementById('type-dinein');
      if (btn) btn.className = 'pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-emerald-700 shadow-xs cursor-pointer';
      tableGrp.style.display = 'flex';
      const tblSelect = document.getElementById('pos-table-select');
      if (tblSelect && tblSelect.options.length > 0) {
        tblSelect.options[0].text = '-- Select Available Table --';
      }
      if (takeawayGrp) takeawayGrp.style.display = 'none';
      if (folioPill) folioPill.style.display = 'block';
      if (takeawayCustBox) takeawayCustBox.style.display = 'none';
      if (folioBox) folioBox.style.display = 'block';
      if (roomGrp) roomGrp.style.display = 'none';
      if (currentBillingType === 'Charged to Room Folio') {
        setBillingType('Charged to Room Folio');
      } else {
        setBillingType(currentBillingType || 'Cash Paid');
      }
    } else if (type === 'Takeaway') {
      const btn = document.getElementById('type-takeaway');
      if (btn) btn.className = 'pos-order-type-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-emerald-700 shadow-xs cursor-pointer';
      if (roomGrp) roomGrp.style.display = 'none';
      tableGrp.style.display = 'flex';
      const tblSelect = document.getElementById('pos-table-select');
      if (tblSelect && tblSelect.options.length > 0) {
        tblSelect.options[0].text = '-- No Table / Takeaway Counter (Optional) --';
      }
      if (takeawayGrp) takeawayGrp.style.display = 'none';
      if (folioPill) folioPill.style.display = 'none';
      if (folioBox) folioBox.style.display = 'none';
      if (takeawayCustBox) takeawayCustBox.style.display = 'block';
      if (currentBillingType === 'Charged to Room Folio') {
        setBillingType('Cash Paid');
      } else {
        setBillingType(currentBillingType || 'Cash Paid');
      }
    }
  }

  // 2. Billing Type Switching
  function setBillingType(bType) {
    currentBillingType = bType;
    document.querySelectorAll('.billing-pill').forEach(p => {
      p.className = 'billing-pill px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 text-center cursor-pointer transition-all border border-slate-200 bg-white hover:bg-slate-50';
    });
    
    const headerRoomGrp = document.getElementById('room-select-group');
    const folioBox = document.getElementById('room-folio-picker-box');
    const takeawayCustBox = document.getElementById('takeaway-customer-box');

    const activePillClass = 'billing-pill active px-3 py-2 rounded-xl text-xs font-bold text-center cursor-pointer transition-all border border-emerald-300 bg-emerald-50 text-emerald-700 shadow-xs';

    if (currentOrderType === 'Takeaway') {
      if (folioBox) folioBox.style.display = 'none';
      if (headerRoomGrp) headerRoomGrp.style.display = 'none';
      if (takeawayCustBox) takeawayCustBox.style.display = 'block';
      if (bType === 'Cash Paid') {
        document.getElementById('bill-cash').className = activePillClass;
      } else {
        document.getElementById('bill-digital').className = activePillClass;
      }
    } else {
      if (takeawayCustBox) takeawayCustBox.style.display = 'none';
      if (folioBox) folioBox.style.display = 'block';

      if (bType === 'Charged to Room Folio') {
        document.getElementById('bill-folio').className = activePillClass;
        if (headerRoomGrp) headerRoomGrp.style.display = 'none';
      } else if (bType === 'Cash Paid') {
        document.getElementById('bill-cash').className = activePillClass;
        if (headerRoomGrp) headerRoomGrp.style.display = 'none';
      } else if (bType === 'Digital UPI / Card Paid') {
        document.getElementById('bill-digital').className = activePillClass;
        if (headerRoomGrp) headerRoomGrp.style.display = 'none';
      }
    }
    updateRoomPickerNote();
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
        <div class="text-center text-slate-400 py-12 text-xs">
          <i class="fa-solid fa-basket-shopping text-3xl block mb-2 text-slate-300"></i>
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
        <div class="py-2.5 flex items-center justify-between gap-2 border-b border-slate-100 last:border-b-0">
          <div class="min-w-0 flex-1">
            <div class="font-bold text-xs text-slate-800 truncate">${i.name}</div>
            <div class="text-[11px] text-slate-400 font-mono mt-0.5">₹ ${i.price.toLocaleString()} × ${i.qty}</div>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <div class="inline-flex items-center border border-slate-200 rounded-lg bg-white overflow-hidden shadow-2xs">
              <button type="button" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 text-xs font-bold cursor-pointer" onclick="changeCartQty(${idx}, -1)">&minus;</button>
              <span class="w-7 text-center font-mono font-bold text-xs text-slate-800">${i.qty}</span>
              <button type="button" class="w-6 h-6 flex items-center justify-center text-slate-500 hover:bg-slate-100 text-xs font-bold cursor-pointer" onclick="changeCartQty(${idx}, 1)">&plus;</button>
            </div>
            <strong class="font-bold font-mono text-xs text-emerald-600 w-16 text-right">
              ₹ ${lineTot.toLocaleString()}
            </strong>
            <button type="button" class="w-6 h-6 rounded-md bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-bold cursor-pointer transition-colors" onclick="removeFromCart(${idx})">&times;</button>
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

    updateRoomPickerNote();
  }

  // 4. Menu Filter by Category
  function filterMenu(cat, btn) {
    document.querySelectorAll('.pos-cat-pill').forEach(b => {
      b.className = 'pos-cat-pill inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-all cursor-pointer whitespace-nowrap';
    });
    if (btn) {
      btn.className = 'pos-cat-pill inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap bg-emerald-600 text-white shadow-xs';
    }

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

    if (currentOrderType === 'Takeaway') {
      const custName = document.getElementById('takeaway-customer-name')?.value?.trim();
      const custPhone = document.getElementById('takeaway-customer-phone')?.value?.trim();
      if (!custName) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'warning', title: 'Customer Name Required', text: 'Please enter Customer Name for Takeaway order!' });
        } else {
          alert('Please enter Customer Name for Takeaway order!');
        }
        document.getElementById('takeaway-customer-name')?.focus();
        return;
      }
      payload.customer_name = custName;
      payload.customer_phone = custPhone || null;
      payload.room_id = null;

      // Optional table for Takeaway
      const tblSelect = document.getElementById('pos-table-select');
      if (tblSelect && tblSelect.value) {
        payload.table_number = tblSelect.value;
        const selectedOpt = tblSelect.options[tblSelect.selectedIndex];
        if (selectedOpt && selectedOpt.getAttribute('data-table-id')) {
          payload.table_id = selectedOpt.getAttribute('data-table-id');
        }
      }
    } else {
      // Room Dining or Table Dine-in
      const cartRm = document.getElementById('cart-room-folio-select');
      const headRm = document.getElementById('pos-room-select');
      const chosenRoomId = (cartRm && cartRm.value) ? cartRm.value : (headRm ? headRm.value : null);

      if (currentBillingType === 'Charged to Room Folio') {
        if (!chosenRoomId) {
          if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Room Required', text: 'Please select an active Room to charge this bill to the Room Folio!' });
          } else {
            alert('Please select an active Room to charge this bill to the Room Folio!');
          }
          return;
        }
        payload.room_id = chosenRoomId;
      } else {
        // Cash Paid or Digital UPI / Card Paid: room selection is optional
        if (chosenRoomId) {
          payload.room_id = chosenRoomId;
        }
      }

      if (currentOrderType === 'Table Dine-in') {
        const tblSelect = document.getElementById('pos-table-select');
        if (!tblSelect || !tblSelect.value) {
          if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Table Required', text: 'Please select an available dining table for Dine-in!' });
          } else {
            alert('Please select an available dining table for Dine-in!');
          }
          tblSelect?.focus();
          return;
        }
        payload.table_number = tblSelect.value;
        const selectedOpt = tblSelect.options[tblSelect.selectedIndex];
        if (selectedOpt && selectedOpt.getAttribute('data-table-id')) {
          payload.table_id = selectedOpt.getAttribute('data-table-id');
        }
        if (!payload.room_id) {
          payload.customer_name = payload.table_number + ' Guest';
        }
      }
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
        orderJustPlaced = true;
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

        // Reset cart & inputs
        cart = [];
        renderCart();
        document.getElementById('order-instructions').value = '';
        if (document.getElementById('takeaway-customer-name')) document.getElementById('takeaway-customer-name').value = '';
        if (document.getElementById('takeaway-customer-phone')) document.getElementById('takeaway-customer-phone').value = '';
        
        // Refresh tables to ensure booked table is no longer selectable
        fetchAndSyncAvailableTables();
      } else {
        alert(data.message || 'Error posting order.');
      }
    } catch (e) {
      console.error(e);
      alert('Network or server error while placing order.');
    }
  }

  // 7. KOT Thermal Slip Modal Handling
  let orderJustPlaced = false;

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
    const kotBillingEl = document.getElementById('kot-billing');
    if (kotBillingEl) kotBillingEl.textContent = order.billing_type || 'Paid';

    let tbodyHtml = '';
    let totQty = 0;
    (order.items || []).forEach(it => {
      totQty += it.quantity;
      tbodyHtml += `
        <tr>
          <td>${it.item_name}</td>
          <td class="text-center">${it.quantity}</td>
          <td class="text-right">₹ ${Number(it.line_total).toLocaleString()}</td>
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
    if (orderJustPlaced) {
      window.location.reload();
    }
  }

  function printThermalTicket() {
    const printContent = document.getElementById('printable-kot-area').innerHTML;
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
    doc.write('<!DOCTYPE html><html><' + 'head><title>KOT Ticket</title><style>');
    doc.write(`
      body { font-family: 'Courier New', Courier, monospace; padding: 10px; font-size: 12px; color: #000; margin: 0; }
      .thermal-ticket-card { width: 100%; max-width: 320px; margin: 0 auto; }
      h3 { text-align: center; margin: 0 0 4px; font-size: 15px; font-weight: 900; }
      .center-sub { text-align: center; font-size: 10.5px; margin: 3px 0; }
      .divider-line { border-bottom: 1px dashed #000; margin: 6px 0; }
      table { width: 100%; border-collapse: collapse; margin: 6px 0; font-size: 11.5px; }
      th, td { padding: 4px 0; }
      th { border-bottom: 1px solid #000; }
      @media print {
        @page { margin: 2mm; size: auto; }
        body { padding: 4px; }
      }
    `);
    doc.write('</' + 'style></' + 'head><' + 'body>');
    doc.write(printContent);
    doc.write('</' + 'body></' + 'html>');
    doc.close();

    setTimeout(() => {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    }, 250);
  }

  // 8. Table Management Modal & Real-time Status Switching
  function openTableManageModal() {
    const m = document.getElementById('table-manage-modal');
    if (m) {
      m.classList.remove('hidden');
      m.style.display = 'flex';
    }
    fetchAndSyncAvailableTables();
  }

  function closeTableManageModal() {
    const m = document.getElementById('table-manage-modal');
    if (m) {
      m.classList.add('hidden');
      m.style.display = 'none';
    }
  }

  async function updateOrderStatus(orderId, newStatus) {
    try {
      const url = "{{ url('pos/orders') }}/" + orderId + "/status";
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
      });

      const data = await res.json();
      if (res.ok && data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: data.message || 'Order status updated',
            showConfirmButton: false,
            timer: 2000
          });
        } else if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        } else {
          alert(data.message);
        }
      } else {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: data.message || 'Failed to update order status.'
          });
        } else {
          alert(data.message || 'Error updating order status.');
        }
      }
    } catch(e) {
      console.error('Error updating order status:', e);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Update Error',
          text: 'Network or server error while updating status.'
        });
      } else {
        alert('Error updating order status.');
      }
    }
  }

  async function updateLiveTableStatus(tableId, newStatus) {
    try {
      const url = "{{ url('pos/tables') }}/" + tableId + "/status";
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
      });

      const data = await res.json();
      if (res.ok && data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: data.message,
            showConfirmButton: false,
            timer: 2000
          });
        } else if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message, 'success');
        }

        const all = data.tables || [];
        const avail = data.availableTables || [];
        const booked = all.filter(t => t.status === 'booked');
        const inactive = all.filter(t => t.status === 'inactive');

        const totEl = document.getElementById('tm-total-count');
        const avEl = document.getElementById('tm-available-count');
        const bkEl = document.getElementById('tm-booked-count');
        const inEl = document.getElementById('tm-inactive-count');
        if (totEl) totEl.textContent = all.length;
        if (avEl) avEl.textContent = avail.length;
        if (bkEl) bkEl.textContent = booked.length;
        if (inEl) inEl.textContent = inactive.length;

        renderTableManageRows(all);
        refreshPosTableSelectDropdown(avail);
      } else {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'error', title: 'Update Failed', text: data.message || 'Could not update table status.' });
        } else {
          alert(data.message || 'Could not update table status.');
        }
      }
    } catch(err) {
      console.error(err);
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Server or network error updating table status.' });
      } else {
        alert('Server or network error updating table status.');
      }
    }
  }

  async function fetchAndSyncAvailableTables() {
    try {
      const url = "{{ route('pos.tables.index') }}";
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success) {
        const all = data.tables || [];
        const avail = data.availableTables || [];
        const booked = all.filter(t => t.status === 'booked');
        const inactive = all.filter(t => t.status === 'inactive');

        const totEl = document.getElementById('tm-total-count');
        const avEl = document.getElementById('tm-available-count');
        const bkEl = document.getElementById('tm-booked-count');
        const inEl = document.getElementById('tm-inactive-count');
        if (totEl) totEl.textContent = all.length;
        if (avEl) avEl.textContent = avail.length;
        if (bkEl) bkEl.textContent = booked.length;
        if (inEl) inEl.textContent = inactive.length;

        renderTableManageRows(all);
        refreshPosTableSelectDropdown(avail);
      }
    } catch(e) {
      console.error('Failed to sync tables:', e);
    }
  }

  function renderTableManageRows(tables) {
    const tbody = document.getElementById('pos-table-manage-tbody');
    if (!tbody) return;
    if (tables.length === 0) {
      tbody.innerHTML = '<tr><td colspan="4" class="text-center text-slate-400 py-8">No tables found.</td></tr>';
      return;
    }
    tbody.innerHTML = tables.map(t => {
      const isAvail = (t.status === 'available');
      const isBooked = (t.status === 'booked');
      const iconColor = isAvail ? 'text-emerald-500' : (isBooked ? 'text-amber-500' : 'text-slate-400');
      const badge = isAvail
        ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Available</span>'
        : (isBooked
          ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Booked</span>'
          : '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive</span>');

      const actionBtn = isBooked
        ? `<button type="button" class="inline-flex items-center gap-1 h-8 px-2.5 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-colors cursor-pointer" onclick="updateLiveTableStatus(${t.id}, 'available')"><i class="fa-solid fa-check text-[10px]"></i> Free Table</button>`
        : (isAvail
          ? `<button type="button" class="inline-flex items-center gap-1 h-8 px-2.5 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-lg transition-colors cursor-pointer" onclick="updateLiveTableStatus(${t.id}, 'booked')"><i class="fa-solid fa-chair text-[10px]"></i> Mark Booked</button>`
          : '');

      return `
        <tr data-table-id="${t.id}" class="hover:bg-slate-50/80 transition-colors">
          <td class="px-4 py-3 font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fa-solid fa-chair text-xs ${iconColor}"></i>
            ${escapeHtml(t.table_number)}
          </td>
          <td class="px-4 py-3 text-slate-700 font-medium">
            ${escapeHtml(t.table_name)}
          </td>
          <td class="px-4 py-3" id="tm-badge-td-${t.id}">${badge}</td>
          <td class="px-4 py-3 text-right" id="tm-action-td-${t.id}">
            <div class="inline-flex items-center gap-2">
              <select class="h-8 text-xs font-semibold rounded-lg border border-slate-200 bg-white px-2 py-1 text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer" onchange="updateLiveTableStatus(${t.id}, this.value)">
                <option value="available" ${isAvail ? 'selected' : ''}>Available (Free)</option>
                <option value="booked" ${isBooked ? 'selected' : ''}>Booked</option>
                <option value="inactive" ${t.status === 'inactive' ? 'selected' : ''}>Inactive</option>
              </select>
              ${actionBtn}
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  function refreshPosTableSelectDropdown(availableTables) {
    const sel = document.getElementById('pos-table-select');
    if (!sel) return;
    const currentVal = sel.value;
    const firstOptionText = (currentOrderType === 'Takeaway') 
      ? '-- No Table / Takeaway Counter (Optional) --' 
      : '-- Select Available Table --';

    let html = `<option value="">${firstOptionText}</option>`;
    availableTables.forEach(t => {
      const val = `${t.table_number} (${t.table_name})`;
      const selected = (val === currentVal) ? 'selected' : '';
      html += `<option value="${val}" data-table-id="${t.id}" data-table-num="${t.table_number}" data-table-name="${t.table_name}" ${selected}>${val}</option>`;
    });
    sel.innerHTML = html;
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
