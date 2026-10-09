<!-- Action Sub-Ribbon (Only Front Office Options) -->
<div class="pms-sub-ribbon">
  <div class="ribbon-left-section">
    <div class="ribbon-action-buttons">
      <a href="{{ route('frontoffice.dashboard') }}" class="ribbon-btn {{ request()->routeIs('frontoffice.dashboard') || request()->routeIs('frontoffice.index') || request()->routeIs('frontoffice.fdesk') ? 'active-btn' : '' }}">
        <span class="ico-box"><i class="fa-solid fa-table-cells-large" style="color: #10b981;"></i></span>
        <span class="lbl-box">F Desk</span>
      </a>
      <a href="{{ route('frontoffice.arrivals') }}" class="ribbon-btn {{ request()->routeIs('frontoffice.arrivals') ? 'active-btn' : '' }}">
        <span class="ico-box"><i class="fa-solid fa-plane-arrival" style="color: #f43f5e;"></i></span>
        <span class="lbl-box">Arrivals</span>
      </a>
      <a href="{{ route('frontoffice.inhouse') }}" class="ribbon-btn {{ request()->routeIs('frontoffice.inhouse') ? 'active-btn' : '' }}">
        <span class="ico-box"><i class="fa-solid fa-users" style="color: #0284c7;"></i></span>
        <span class="lbl-box">Inhouse</span>
      </a>
      <a href="{{ route('frontoffice.guest-crm') }}" class="ribbon-btn {{ request()->routeIs('frontoffice.guest-crm') || request()->routeIs('frontoffice.guest') ? 'active-btn' : '' }}">
        <span class="ico-box"><i class="fa-solid fa-address-card" style="color: #f59e0b;"></i></span>
        <span class="lbl-box">Guest</span>
      </a>
      <a href="{{ route('frontoffice.reserve') }}" class="ribbon-btn {{ request()->routeIs('frontoffice.reserve') ? 'active-btn' : '' }}">
        <span class="ico-box"><i class="fa-solid fa-calendar-check" style="color: #a78bfa;"></i></span>
        <span class="lbl-box">Reserve</span>
      </a>
     <!-- <button type="button" class="ribbon-btn" onclick="openReservationModal()">
        <span class="ico-box"><i class="fa-solid fa-key" style="color: #6366f1;"></i></span>
        <span class="lbl-box">Check In</span>
      </button> 
      <a href="{{ route('frontoffice.inhouse') }}" class="ribbon-btn">
        <span class="ico-box"><i class="fa-solid fa-right-from-bracket" style="color: #f43f5e;"></i></span>
        <span class="lbl-box">Check Out</span>
      </a> -->
    </div>
  </div>
  <div style="font-size: 11px; font-weight: 700; color: var(--accent-emerald); display: flex; align-items: center; gap: 6px;">
    <span style="width: 6px; height: 6px; background: var(--accent-emerald); border-radius: 50%; display: inline-block; box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);"></span>
    Hotel Sagar Sonnet Live PMS
  </div>
</div>
