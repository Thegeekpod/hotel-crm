@php
  $btnBase = "inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-2xs whitespace-nowrap ribbon-btn";
  $btnActive = "bg-white text-indigo-700 border border-indigo-300/80 shadow-xs ring-1 ring-indigo-500/10 active-btn";
  $btnDefault = "bg-white/90 hover:bg-white text-slate-700 border border-slate-200/90 hover:border-slate-300 hover:text-slate-900";
@endphp

@if(request()->routeIs('banquet.*'))
<!-- Action Sub-Ribbon (Banquet Halls & Room Service Orders) -->
<div class="bg-slate-50/95 backdrop-blur-xs border-b border-slate-200/80 px-4 sm:px-6 py-2 flex items-center justify-between gap-4 overflow-x-auto scrollbar-none pms-sub-ribbon">
  <div class="flex items-center gap-2 ribbon-left-section">
    <div class="flex items-center gap-2 ribbon-action-buttons">
      <a href="{{ route('banquet.index') }}" class="{{ $btnBase }} {{ $btnActive }}">
        <span class="w-5 h-5 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-bell-concierge"></i></span>
        <span class="lbl-box">Room Service &amp; Banquet</span>
      </a>
      <button type="button" class="{{ $btnBase }} {{ $btnDefault }}" onclick="openNewRoomOrderModal()">
        <span class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-utensils"></i></span>
        <span class="lbl-box">New Room Order</span>
      </button>
      <button type="button" class="{{ $btnBase }} {{ $btnDefault }}" onclick="openNewBanquetModal()">
        <span class="w-5 h-5 rounded-md bg-pink-50 text-pink-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-calendar-plus"></i></span>
        <span class="lbl-box">Book Event Hall</span>
      </button>
      <button type="button" class="{{ $btnBase }} {{ $btnDefault }}" onclick="switchBanquetTab('orders')">
        <span class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-receipt"></i></span>
        <span class="lbl-box">Active Orders</span>
      </button>
      <button type="button" class="{{ $btnBase }} {{ $btnDefault }}" onclick="switchBanquetTab('menu')">
        <span class="w-5 h-5 rounded-md bg-cyan-50 text-cyan-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-book-open"></i></span>
        <span class="lbl-box">Room Dining Menu</span>
      </button>
    </div>
  </div>
  <div class="hidden sm:inline-flex items-center gap-2 text-xs font-bold text-amber-600 bg-amber-50/80 px-2.5 py-1 rounded-full border border-amber-200/80 shrink-0">
    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
    Banquet &amp; In-Room Dining Live
  </div>
</div>
@elseif(request()->routeIs('frontoffice.services.*'))
<!-- Action Sub-Ribbon (Extra Services & Room Dining) -->
<div class="bg-slate-50/95 backdrop-blur-xs border-b border-slate-200/80 px-4 sm:px-6 py-2 flex items-center justify-between gap-4 overflow-x-auto scrollbar-none pms-sub-ribbon">
  <div class="flex items-center gap-2 ribbon-left-section">
    <div class="flex items-center gap-2 ribbon-action-buttons">
      <a href="{{ route('frontoffice.services.index') }}" class="{{ $btnBase }} {{ $btnActive }}">
        <span class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-bell-concierge"></i></span>
        <span class="lbl-box">Active Orders</span>
      </a>
      <button type="button" class="{{ $btnBase }} {{ $btnDefault }}" onclick="openNewRoomOrderModal()">
        <span class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-utensils"></i></span>
        <span class="lbl-box">New Room Order</span>
      </button>
    </div>
  </div>
  <div class="hidden sm:inline-flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50/80 px-2.5 py-1 rounded-full border border-emerald-200/80 shrink-0">
    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
    In-Room Dining &amp; Extra Services Live
  </div>
</div>
@elseif(request()->routeIs('pos.*'))
<!-- Action Sub-Ribbon (POS Sales & Dining) -->
<div class="bg-slate-50/95 backdrop-blur-xs border-b border-slate-200/80 px-4 sm:px-6 py-2 flex items-center justify-between gap-4 overflow-x-auto scrollbar-none pms-sub-ribbon">
  <div class="flex items-center gap-2 ribbon-left-section">
    <div class="flex items-center gap-2 ribbon-action-buttons">
      <a href="{{ route('pos.index') }}" class="{{ $btnBase }} {{ request()->routeIs('pos.index') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-utensils"></i></span>
        <span class="lbl-box">POS Terminal</span>
      </a>
      <a href="{{ route('pos.orders.index') }}" class="{{ $btnBase }} {{ request()->routeIs('pos.orders.*') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-cyan-50 text-cyan-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-clock-rotate-left"></i></span>
        <span class="lbl-box">KOT History</span>
      </a>
      <button type="button" class="{{ $btnBase }} {{ $btnDefault }}" onclick="openTableManageModal()">
        <span class="w-5 h-5 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-chair"></i></span>
        <span class="lbl-box">Table Manage</span>
      </button>
      <a href="{{ route('stores.index') }}" class="{{ $btnBase }} {{ $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-boxes-stacked"></i></span>
        <span class="lbl-box">Manage Menus &amp; Stock</span>
      </a>
    </div>
  </div>
  <div class="hidden sm:inline-flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50/80 px-2.5 py-1 rounded-full border border-emerald-200/80 shrink-0">
    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
    Restaurant &amp; In-Room Dining POS Live
  </div>
</div>
@elseif(request()->routeIs('stores.*'))
<!-- Action Sub-Ribbon (Restaurant Kitchen & F&B Store) -->
<div class="bg-slate-50/95 backdrop-blur-xs border-b border-slate-200/80 px-4 sm:px-6 py-2 flex items-center justify-between gap-4 overflow-x-auto scrollbar-none pms-sub-ribbon">
  <div class="flex items-center gap-2 ribbon-left-section">
    <div class="flex items-center gap-2 ribbon-action-buttons">
      <a href="{{ route('stores.index') }}" class="{{ $btnBase }} {{ $btnActive }}">
        <span class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-kitchen-set"></i></span>
        <span class="lbl-box">Store &amp; Menus</span>
      </a>
      <button type="button" class="{{ $btnBase }} {{ $btnDefault }}" onclick="openFoodItemModal()">
        <span class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-plus-circle"></i></span>
        <span class="lbl-box">+ Add Food Item</span>
      </button>
      <button type="button" class="{{ $btnBase }} {{ $btnDefault }}" onclick="openMenuCategoryModal()">
        <span class="w-5 h-5 rounded-md bg-cyan-50 text-cyan-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-folder-plus"></i></span>
        <span class="lbl-box">+ New Menu</span>
      </button>
      <button type="button" class="{{ $btnBase }} {{ $btnDefault }}" onclick="openInwardModal()">
        <span class="w-5 h-5 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-truck-ramp-box"></i></span>
        <span class="lbl-box">+ Inward Stock ("Item Duklo")</span>
      </button>
      <a href="{{ route('pos.index') }}" class="{{ $btnBase }} {{ $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-desktop"></i></span>
        <span class="lbl-box">Open POS Terminal</span>
      </a>
    </div>
  </div>
  <div class="hidden sm:inline-flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50/80 px-2.5 py-1 rounded-full border border-emerald-200/80 shrink-0">
    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
    Kitchen Store &amp; Recipe Yields Live
  </div>
</div>
@elseif(!request()->routeIs('accounts.*') && !request()->is('accounts*'))
<!-- Action Sub-Ribbon (Only Front Office Options) -->
<div class="bg-slate-50/95 backdrop-blur-xs border-b border-slate-200/80 px-4 sm:px-6 py-2 flex items-center justify-between gap-4 overflow-x-auto scrollbar-none pms-sub-ribbon">
  <div class="flex items-center gap-2 ribbon-left-section">
    <div class="flex items-center gap-2 ribbon-action-buttons">
      <a href="{{ route('frontoffice.dashboard') }}" class="{{ $btnBase }} {{ request()->routeIs('frontoffice.dashboard') || request()->routeIs('frontoffice.index') || request()->routeIs('frontoffice.fdesk') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-table-cells-large"></i></span>
        <span class="lbl-box">F Desk</span>
      </a>
      <a href="{{ route('frontoffice.arrivals') }}" class="{{ $btnBase }} {{ request()->routeIs('frontoffice.arrivals') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-rose-50 text-rose-500 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-plane-arrival"></i></span>
        <span class="lbl-box">Arrivals</span>
      </a>
      <a href="{{ route('frontoffice.guest') }}" class="{{ $btnBase }} {{ request()->routeIs('frontoffice.guest-crm') || request()->routeIs('frontoffice.guest') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-address-card"></i></span>
        <span class="lbl-box">Guest</span>
      </a>
      <a href="{{ route('frontoffice.folio') }}" class="{{ $btnBase }} {{ request()->routeIs('frontoffice.folio*') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-file-invoice-dollar"></i></span>
        <span class="lbl-box">Settlements</span>
      </a>
      <a href="{{ route('frontoffice.checkout') }}" class="{{ $btnBase }} {{ request()->routeIs('frontoffice.checkout*') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-rose-50 text-rose-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-person-walking-dashed-line-arrow-right"></i></span>
        <span class="lbl-box">Checkout</span>
      </a>
      <a href="{{ route('frontoffice.services.index') }}" class="{{ $btnBase }} {{ request()->routeIs('frontoffice.services*') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-bell-concierge"></i></span>
        <span class="lbl-box">Extra Services</span>
      </a>
      <a href="{{ route('frontoffice.nightaudit') }}" class="{{ $btnBase }} {{ request()->routeIs('frontoffice.nightaudit*') || request()->routeIs('frontoffice.night-audit*') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-moon"></i></span>
        <span class="lbl-box">Night Audit</span>
      </a>
      <a href="{{ route('frontoffice.housekeeping') }}" class="{{ $btnBase }} {{ request()->routeIs('frontoffice.housekeeping*') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-broom"></i></span>
        <span class="lbl-box">House Keeping</span>
      </a>
      <a href="{{ route('banquet.index') }}" class="{{ $btnBase }} {{ request()->routeIs('banquet*') ? $btnActive : $btnDefault }}">
        <span class="w-5 h-5 rounded-md bg-amber-50 text-amber-500 flex items-center justify-center text-xs ico-box"><i class="fa-solid fa-champagne-glasses"></i></span>
        <span class="lbl-box">Banquet</span>
      </a>
    </div>
  </div>
  <div class="hidden sm:inline-flex items-center gap-2 text-xs font-bold text-emerald-600 bg-emerald-50/80 px-2.5 py-1 rounded-full border border-emerald-200/80 shrink-0">
    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
    Hotel Sagar Sonnet Live PMS
  </div>
</div>
@endif
