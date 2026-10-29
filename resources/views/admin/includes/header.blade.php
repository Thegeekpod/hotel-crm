<!-- Top PMS App Header -->
<header class="h-13 bg-white/95 backdrop-blur-md border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between shadow-2xs sticky top-0 z-40 transition-colors shrink-0 pms-app-header">
  <div class="flex items-center gap-3 pms-header-brand">
    <div class="w-8 h-8 rounded-lg bg-linear-to-br from-indigo-600 to-indigo-700 text-white flex items-center justify-center shadow-xs">
      <i class="fa-solid fa-hotel text-sm text-amber-300 gold-star"></i>
    </div>
    <div class="flex items-center gap-2">
      <span class="font-black text-sm tracking-wider text-slate-900">HOTEL SAGAR SONNET</span>
      <span class="bg-amber-400 text-slate-950 text-[10px] px-1.5 py-0.5 rounded font-black tracking-wider uppercase shadow-2xs">PMS 2026</span>
    </div>
  </div>
  
  <div class="hidden md:flex items-center gap-5 text-xs font-semibold text-slate-600 pms-header-center">
    <span class="inline-flex items-center gap-1.5">
      <i class="fa-regular fa-calendar-days text-indigo-500"></i>
      {{ date('l, d M Y') }}
    </span>
    <span class="text-slate-300">|</span>
    <span class="inline-flex items-center gap-1.5">
      <i class="fa-solid fa-building text-cyan-500"></i>
      Main Luxury Wing
    </span>
  </div>

  <div class="flex items-center gap-3 pms-header-right">
    <div class="inline-flex items-center gap-2 bg-slate-100/90 border border-slate-200 px-3 py-1 rounded-full text-xs font-bold text-slate-700 pms-user-tag">
      <i class="fa-solid fa-user-tie text-indigo-600"></i>
      <span class="hidden sm:inline">Super Admin</span>
    </div>
    <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1 rounded-lg text-xs font-bold transition-all hover:scale-102 pms-btn-exit">
      <i class="fa-solid fa-power-off"></i>
      <span class="hidden sm:inline">Logout</span>
    </a>
  </div>
</header>

<!-- Module Navigation Tabs Ribbon -->
<nav class="bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center gap-1.5 overflow-x-auto scrollbar-none py-1.5 shadow-2xs shrink-0 pms-tabs-ribbon">
  @php
    $tabBase = "inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap pms-tab-link";
    $tabActive = "bg-indigo-50 text-indigo-700 shadow-2xs border border-indigo-200/80 active";
    $tabInactive = "text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 border border-transparent";
  @endphp
  <a href="{{ route('frontoffice.dashboard') }}" class="{{ $tabBase }} {{ (request()->routeIs('frontoffice.*') || request()->is('front-office*') || request()->routeIs('banquet*')) ? $tabActive : $tabInactive }}">
    <i class="fa-solid fa-desktop text-indigo-600 tab-ico"></i> Front Office
  </a>
  <a href="{{ route('pos.index') }}" class="{{ $tabBase }} {{ (request()->routeIs('pos.*') || request()->routeIs('stores.*')) ? $tabActive : $tabInactive }}">
    <i class="fa-solid fa-utensils text-emerald-600 tab-ico"></i> POS Sales
  </a>
  <a href="{{ route('accounts.index') }}" class="{{ $tabBase }} {{ request()->routeIs('accounts.*') ? $tabActive : $tabInactive }}">
    <i class="fa-solid fa-file-invoice-dollar text-purple-600 tab-ico"></i> Accounts
  </a>
  <a href="{{ route('admin.dashboard') }}" class="{{ $tabBase }} {{ request()->routeIs('admin.utilities.*') || request()->routeIs('admin.roommanagement.*') || request()->routeIs('admin.roommaintain.*') || request()->routeIs('admin.nightaudit.*') || request()->routeIs('admin.services.*') || request()->routeIs('admin.dashboard') ? $tabActive : $tabInactive }}">
    <i class="fa-solid fa-user-gear text-slate-500 tab-ico"></i> Administrator
  </a>
</nav>

<!-- Dynamic Action Sub-Ribbon (Hidden when Administrator tab is active) -->
@php
  $isAdminTabActive = request()->routeIs('admin.utilities.*') || request()->routeIs('admin.roommanagement.*') || request()->routeIs('admin.roommaintain.*') || request()->routeIs('admin.nightaudit.*') || request()->routeIs('admin.services.*') || request()->routeIs('admin.dashboard');
@endphp

@if(!$isAdminTabActive)
<div class="bg-slate-50/95 backdrop-blur-xs border-b border-slate-200/80 px-4 sm:px-6 py-2 flex items-center justify-between gap-4 overflow-x-auto scrollbar-none pms-sub-ribbon">
  <div class="flex items-center gap-2 ribbon-left-section">
    <div class="flex items-center gap-2 ribbon-nav-links">
      @if(request()->routeIs('admin.utilities.frontoffice.*'))
        <!-- Front Office Sub-bar -->
        <a href="{{ route('admin.utilities.frontoffice.reservation-mode.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.utilities.frontoffice.reservation-mode.*') ? 'bg-white text-indigo-700 shadow-xs border border-indigo-200' : 'text-slate-600 hover:text-slate-900' }} ribbon-nav-item">
          <i class="fa-solid fa-clipboard-check text-indigo-600"></i> Mode of Reserve
        </a>
        <a href="{{ route('admin.utilities.frontoffice.idcard-type.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.utilities.frontoffice.idcard-type.*') ? 'bg-white text-indigo-700 shadow-xs border border-indigo-200' : 'text-slate-600 hover:text-slate-900' }} ribbon-nav-item">
          <i class="fa-solid fa-id-card text-indigo-600"></i> ID Card Types
        </a>
        <a href="{{ route('admin.utilities.frontoffice.payment-mode.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.utilities.frontoffice.payment-mode.*') ? 'bg-white text-indigo-700 shadow-xs border border-indigo-200' : 'text-slate-600 hover:text-slate-900' }} ribbon-nav-item">
          <i class="fa-solid fa-cash-register text-indigo-600"></i> Payment Modes
        </a>
      @elseif(request()->routeIs('admin.utilities.housekeeping.*'))
        <!-- House Keeping Sub-bar -->
        <a href="{{ route('admin.utilities.housekeeping.state.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.utilities.housekeeping.state.*') ? 'bg-white text-amber-700 shadow-xs border border-amber-200' : 'text-slate-600 hover:text-slate-900' }} ribbon-nav-item">
          <i class="fa-solid fa-spray-can-sparkles text-amber-500"></i> Housekeeping State
        </a>
        <a href="{{ route('admin.utilities.housekeeping.operational.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ request()->routeIs('admin.utilities.housekeeping.operational.*') ? 'bg-white text-amber-700 shadow-xs border border-amber-200' : 'text-slate-600 hover:text-slate-900' }} ribbon-nav-item">
          <i class="fa-solid fa-circle-nodes text-amber-500"></i> Operational Status
        </a>
      @else
        <!-- Administrator Master Sub-bar -->
        <a href="{{ route('admin.utilities.roommanage.category.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-indigo-700 shadow-xs border border-indigo-200 ribbon-nav-item active">
          <i class="fa-solid fa-sliders text-indigo-600"></i> Master Configuration
        </a>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-slate-900 cursor-pointer ribbon-nav-item" onclick="if(typeof exportDatabaseJSON === 'function') exportDatabaseJSON(); else if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Database backup generated successfully!', 'success');">
          <i class="fa-solid fa-database text-indigo-500"></i> Backup Master DB
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-slate-900 cursor-pointer ribbon-nav-item" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Hotel Sagar Sonnet PMS profile synchronized!', 'success');">
          <i class="fa-solid fa-building text-indigo-500"></i> Hotel Profile
        </span>
      @endif
    </div>
  </div>
  <div class="hidden sm:inline-flex items-center gap-2 text-xs font-bold text-indigo-600 bg-indigo-50/80 px-2.5 py-1 rounded-full border border-indigo-200/80 shrink-0">
    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
    @if(request()->routeIs('admin.utilities.frontoffice.*'))
      Front Office Online
    @elseif(request()->routeIs('admin.utilities.housekeeping.*'))
      Housekeeping Live
    @else
      Administrator Master Online
    @endif
  </div>
</div>
@endif
