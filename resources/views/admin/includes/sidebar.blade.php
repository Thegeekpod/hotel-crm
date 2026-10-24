<!-- Administrator Master Sidebar -->
<aside class="admin-sidebar">
  <!-- Group 1: Front Office Master -->
  <div class="admin-nav-group">
    <div class="admin-nav-header {{ request()->routeIs('admin.utilities.frontoffice.*') ? 'active' : '' }}" onclick="toggleNavGroup(this)">
      <span><i class="fa-solid fa-desktop" style="margin-right: 6px; color: var(--accent-primary);"></i> FRONT OFFICE</span>
      <i class="fa-solid {{ request()->routeIs('admin.utilities.frontoffice.*') ? 'fa-chevron-down' : 'fa-chevron-right' }}" style="font-size: 10px;"></i>
    </div>
    <ul class="admin-sub-list" style="{{ request()->routeIs('admin.utilities.frontoffice.*') ? 'display: block;' : 'display: none;' }}">

      <li>
        <a href="{{ route('admin.utilities.frontoffice.registration-type.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.registration-type.*') ? 'active' : '' }}">
          <i class="fa-solid fa-address-card"></i> Registration Type
        </a>
      </li>

      <li>
        <a href="{{ route('admin.utilities.frontoffice.reservation-mode.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.reservation-mode.*') ? 'active' : '' }}">
          <i class="fa-solid fa-clipboard-check"></i> Mode of Reserve
        </a>
      </li>

      <li>
        <a href="{{ route('admin.utilities.frontoffice.title.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.title.*') ? 'active' : '' }}">
          <i class="fa-solid fa-user-tag"></i> Titles
        </a>
      </li>

      <li>
        <a href="{{ route('admin.utilities.frontoffice.nationality.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.nationality.*') ? 'active' : '' }}">
          <i class="fa-solid fa-flag"></i> Nationality
        </a>
      </li>

      <li>
        <a href="{{ route('admin.utilities.frontoffice.idcard-type.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.idcard-type.*') ? 'active' : '' }}">
          <i class="fa-solid fa-id-card"></i> ID Card Type
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.payment-mode.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.payment-mode.*') ? 'active' : '' }}">
          <i class="fa-solid fa-cash-register"></i> Payment Modes
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.gst-percentage.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.gst-percentage.*') ? 'active' : '' }}">
          <i class="fa-solid fa-percent"></i> GST %
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.company.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.company.*') ? 'active' : '' }}">
          <i class="fa-solid fa-building"></i> Company Manage
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.plans.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.plans.*') ? 'active' : '' }}">
          <i class="fa-solid fa-list-check"></i> Meal Plans
        </a>
      </li>
    </ul>
  </div>

  <!-- Main Item: Night Audit -->
  <a href="{{ route('admin.nightaudit.index') }}" class="admin-nav-flat {{ request()->routeIs('admin.nightaudit.*') ? 'active' : '' }}" style="text-decoration: none; margin-bottom: 8px;">
    <i class="fa-solid fa-moon" style="color: #8b5cf6; font-size: 13px;"></i>
    <span>Night Audit</span>
  </a>

  <!-- Group 2: Room Management Master -->
  <div class="admin-nav-group">
    <div class="admin-nav-header {{ request()->routeIs('admin.utilities.roommanage.*') || request()->routeIs('admin.roommanagement.*') || request()->routeIs('admin.roommaintain.*') ? 'active' : '' }}" onclick="toggleNavGroup(this)">
      <span><i class="fa-solid fa-door-open" style="margin-right: 6px; color: var(--accent-secondary);"></i> ROOM MANAGEMENT</span>
      <i class="fa-solid {{ request()->routeIs('admin.utilities.roommanage.*') || request()->routeIs('admin.roommanagement.*') || request()->routeIs('admin.roommaintain.*') ? 'fa-chevron-down' : 'fa-chevron-right' }}" style="font-size: 10px;"></i>
    </div>
    <ul class="admin-sub-list" style="{{ request()->routeIs('admin.utilities.roommanage.*') || request()->routeIs('admin.roommanagement.*') || request()->routeIs('admin.roommaintain.*') ? 'display: block;' : 'display: none;' }}">
     
      <li>
        <a href="{{ route('admin.utilities.roommanage.category.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.category.*') ? 'active' : '' }}">
          <i class="fa-solid fa-tags"></i> Room Categories
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.roommanage.floor.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.floor.*') ? 'active' : '' }}">
          <i class="fa-solid fa-layer-group"></i> Floors Manage
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.roommanage.bedding-config.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.bedding-config.*') ? 'active' : '' }}">
          <i class="fa-solid fa-bed"></i> Bedding Config
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.roommanage.amenity.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.amenity.*') ? 'active' : '' }}">
          <i class="fa-solid fa-wifi"></i> Amenities Master
        </a>
      </li>
      <li>
        <a href="{{ route('admin.roommanagement.index') }}" class="admin-sub-item {{ request()->routeIs('admin.roommanagement.*') ? 'active' : '' }}">
          <i class="fa-solid fa-door-open"></i> Rooms Add
        </a>
      </li>
      <li>
        <a href="{{ route('admin.roommaintain.index') }}" class="admin-sub-item {{ request()->routeIs('admin.roommaintain.*') ? 'active' : '' }}">
          <i class="fa-solid fa-screwdriver-wrench"></i> Room Maintenance
        </a>
      </li>
    </ul>
  </div>

  <!-- Group 3: Upcoming / Flat Nav Modules -->
  <div class="admin-nav-flat" onclick="PmsAlert.toast('POS Sales master module queued for client review.', 'info')">
    <i class="fa-solid fa-utensils" style="color: #10b981;"></i>
    <span>POS Sales</span>
  </div>

  <!-- Group 4: House Keeping Master -->
  <div class="admin-nav-group">
    <div class="admin-nav-header {{ request()->routeIs('admin.utilities.housekeeping.*') ? 'active' : '' }}" onclick="toggleNavGroup(this)">
      <span><i class="fa-solid fa-broom" style="margin-right: 6px; color: #f59e0b;"></i> HOUSE KEEPING</span>
      <i class="fa-solid {{ request()->routeIs('admin.utilities.housekeeping.*') ? 'fa-chevron-down' : 'fa-chevron-right' }}" style="font-size: 10px;"></i>
    </div>
    <ul class="admin-sub-list" style="{{ request()->routeIs('admin.utilities.housekeeping.*') ? 'display: block;' : 'display: none;' }}">
      <li>
        <a href="{{ route('admin.utilities.housekeeping.state.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.housekeeping.state.*') ? 'active' : '' }}">
          <i class="fa-solid fa-spray-can-sparkles"></i> Housekeeping State
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.housekeeping.operational.index') }}" class="admin-sub-item {{ request()->routeIs('admin.utilities.housekeeping.operational.*') ? 'active' : '' }}">
          <i class="fa-solid fa-circle-nodes"></i> Operational Status
        </a>
      </li>
      <li>
        <a href="{{ route('frontoffice.housekeeping') }}" class="admin-sub-item {{ request()->routeIs('frontoffice.housekeeping*') ? 'active' : '' }}">
          <i class="fa-solid fa-table-cells"></i> Live HK Dashboard
        </a>
      </li>
    </ul>
  </div>

  <!-- Main Item: Extra Services -->
  <a href="{{ route('admin.nightaudit.index') }}" class="admin-nav-flat {{ request()->routeIs('admin.nightaudit.*') ? 'active' : '' }}" style="text-decoration: none; margin-bottom: 8px;">
    <i class="fa-solid fa-moon" style="color: #8b5cf6; font-size: 13px;"></i>
    <span>Extra Services</span>
  </a>

  <div class="admin-nav-flat" onclick="PmsAlert.toast('Stores & Inventory master module queued for client review.', 'info')">
    <i class="fa-solid fa-boxes-stacked" style="color: #22d3ee;"></i>
    <span>Stores & Inventory</span>
  </div>

  <div class="admin-nav-flat" onclick="PmsAlert.toast('Accounts & Finance master module queued for client review.', 'info')">
    <i class="fa-solid fa-file-invoice-dollar" style="color: #a78bfa;"></i>
    <span>Accounts & Finance</span>
  </div>

  <a href="{{ route('banquet.index') }}" class="admin-nav-flat {{ request()->routeIs('banquet.*') ? 'active' : '' }}" style="text-decoration: none; color: inherit;">
    <i class="fa-solid fa-champagne-glasses" style="color: #fbbf24;"></i>
    <span>Banquet & Services</span>
  </a>

  <div class="admin-nav-flat" onclick="PmsAlert.toast('System & Roles master module queued for client review.', 'info')">
    <i class="fa-solid fa-shield-halved" style="color: #64748b;"></i>
    <span>System & Roles</span>
  </div>
</aside>

<script>
  function toggleNavGroup(headerElem) {
    const subList = headerElem.nextElementSibling;
    if (subList && subList.tagName === 'UL') {
      const isHidden = subList.style.display === 'none' || getComputedStyle(subList).display === 'none';
      subList.style.display = isHidden ? 'block' : 'none';
      const icon = headerElem.querySelector('.fa-chevron-down, .fa-chevron-right');
      if (icon) {
        icon.className = isHidden ? 'fa-solid fa-chevron-down' : 'fa-solid fa-chevron-right';
      }
    }
  }
</script>
