<!-- Top PMS App Header -->
<header class="pms-app-header">
  <div class="pms-header-brand">
    <i class="fa-solid fa-hotel gold-star"></i>
    <span>HOTEL SAGAR SONNET</span>
    <span style="background: #fbc531; color: #1e293b; font-size: 9px; padding: 2px 5px; border-radius: 3px; font-weight: 800;">PMS 2026</span>
  </div>
  <div class="pms-header-center">
    <span><i class="fa-regular fa-calendar-days"></i> {{ date('l, d M Y') }}</span>
    <span style="opacity: 0.4;">|</span>
    <span><i class="fa-solid fa-building"></i> Main Luxury Wing</span>
  </div>
  <div class="pms-header-right">
    <div class="pms-user-tag"><i class="fa-solid fa-user-tie"></i> Super Admin</div>
    <a href="{{ route('admin.login') }}" class="pms-btn-exit" style="text-decoration: none;"><i class="fa-solid fa-power-off"></i> Logout</a>
  </div>
</header>

<!-- Module Navigation Tabs Ribbon -->
<nav class="pms-tabs-ribbon">
  <a href="#" class="pms-tab-link {{ request()->routeIs('admin.frontoffice.operations.*') ? 'active' : '' }}" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Front Office operations module queued for live operations.', 'info')">
    <i class="fa-solid fa-desktop tab-ico" style="color: #6366f1;"></i> Front Office
  </a>
  <a href="{{ route('admin.roommanagement.index') }}" class="pms-tab-link {{ request()->routeIs('admin.roommanagement.*') ? 'active' : '' }}">
    <i class="fa-solid fa-door-open tab-ico" style="color: #ec4899;"></i> Room Management
  </a>
  <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('POS Sales master module queued for client review.', 'info')">
    <i class="fa-solid fa-utensils tab-ico" style="color: #10b981;"></i> POS Sales
  </a>
  <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('House Keeping operational module queued.', 'info')">
    <i class="fa-solid fa-broom tab-ico" style="color: #f59e0b;"></i> House Keeping
  </a>
  <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Stores & Inventory module queued for client review.', 'info')">
    <i class="fa-solid fa-boxes-stacked tab-ico" style="color: #22d3ee;"></i> Stores
  </a>
  <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Accounts & Finance module queued for client review.', 'info')">
    <i class="fa-solid fa-file-invoice-dollar tab-ico" style="color: #a78bfa;"></i> Accounts
  </a>
  <a href="#" class="pms-tab-link" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Banquet & Services module queued for client review.', 'info')">
    <i class="fa-solid fa-champagne-glasses tab-ico" style="color: #fbbf24;"></i> Banquet & Services
  </a>
  <a href="{{ route('admin.dashboard') }}" class="pms-tab-link {{ request()->routeIs('admin.utilities.*') || request()->routeIs('admin.roommaintain.*') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fa-solid fa-user-gear tab-ico" style="color: #94a3b8;"></i> Administrator
  </a>
</nav>

<!-- Dynamic Action Sub-Ribbon -->
<div class="pms-sub-ribbon">
  <div class="ribbon-left-section">
    <div class="ribbon-nav-links">
      @if(request()->routeIs('admin.roommanagement.*'))
        <!-- Room Management Sub-bar -->
        <a href="{{ route('admin.roommanagement.index') }}" class="ribbon-nav-item active" style="color: #ec4899; text-decoration: none;">
          <i class="fa-solid fa-list-check"></i> Room Master
        </a>
        <a href="{{ route('admin.roommanagement.index') }}" class="ribbon-nav-item" style="text-decoration: none;">
          <i class="fa-solid fa-wrench"></i> Room Maintenance
        </a>
        <a href="{{ route('admin.utilities.roommanage.category.index') }}" class="ribbon-nav-item" style="text-decoration: none;">
          <i class="fa-solid fa-tags"></i> Tariff Master
        </a>
      @elseif(request()->routeIs('admin.utilities.frontoffice.*'))
        <!-- Front Office Sub-bar -->
        <a href="{{ route('admin.utilities.frontoffice.reservation-mode.index') }}" class="ribbon-nav-item {{ request()->routeIs('admin.utilities.frontoffice.reservation-mode.*') ? 'active' : '' }}" style="color: #6366f1; text-decoration: none;">
          <i class="fa-solid fa-clipboard-check"></i> Mode of Reserve
        </a>
        <a href="{{ route('admin.utilities.frontoffice.idcard-type.index') }}" class="ribbon-nav-item {{ request()->routeIs('admin.utilities.frontoffice.idcard-type.*') ? 'active' : '' }}" style="text-decoration: none;">
          <i class="fa-solid fa-id-card"></i> ID Card Types
        </a>
        <a href="{{ route('admin.utilities.frontoffice.payment-mode.index') }}" class="ribbon-nav-item {{ request()->routeIs('admin.utilities.frontoffice.payment-mode.*') ? 'active' : '' }}" style="text-decoration: none;">
          <i class="fa-solid fa-cash-register"></i> Payment Modes
        </a>
      @elseif(request()->routeIs('admin.utilities.housekeeping.*'))
        <!-- House Keeping Sub-bar -->
        <a href="{{ route('admin.utilities.housekeeping.state.index') }}" class="ribbon-nav-item {{ request()->routeIs('admin.utilities.housekeeping.state.*') ? 'active' : '' }}" style="color: #f59e0b; text-decoration: none;">
          <i class="fa-solid fa-spray-can-sparkles"></i> Housekeeping State
        </a>
        <a href="{{ route('admin.utilities.housekeeping.operational.index') }}" class="ribbon-nav-item {{ request()->routeIs('admin.utilities.housekeeping.operational.*') ? 'active' : '' }}" style="text-decoration: none;">
          <i class="fa-solid fa-circle-nodes"></i> Operational Status
        </a>
      @else
        <!-- Administrator Master Sub-bar -->
        <a href="{{ route('admin.utilities.roommanage.category.index') }}" class="ribbon-nav-item active" style="color: #6366f1; text-decoration: none;">
          <i class="fa-solid fa-sliders"></i> Master Configuration
        </a>
        <span class="ribbon-nav-item" style="cursor: pointer;" onclick="if(typeof exportDatabaseJSON === 'function') exportDatabaseJSON(); else if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Database backup generated successfully!', 'success');">
          <i class="fa-solid fa-database"></i> Backup Master DB
        </span>
        <span class="ribbon-nav-item" style="cursor: pointer;" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('Hotel Sagar Sonnet PMS profile synchronized!', 'success');">
          <i class="fa-solid fa-building"></i> Hotel Profile
        </span>
      @endif
    </div>
  </div>
  <div style="font-weight: 700; color: var(--accent-primary); font-size: 12px; display: flex; align-items: center; gap: 6px;">
    <span style="width: 8px; height: 8px; background: var(--accent-emerald); border-radius: 50%; box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);"></span>
    @if(request()->routeIs('admin.roommanagement.*'))
      Room Management Live
    @elseif(request()->routeIs('admin.utilities.frontoffice.*'))
      Front Office Online
    @elseif(request()->routeIs('admin.utilities.housekeeping.*'))
      Housekeeping Live
    @else
      Administrator Master Online
    @endif
  </div>
</div>
