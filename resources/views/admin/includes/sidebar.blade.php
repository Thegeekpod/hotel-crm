<!-- Administrator Master Sidebar -->
<aside class="bg-white border border-slate-200 rounded-2xl shadow-xs p-3.5 h-fit sticky top-3 max-h-[calc(100vh-120px)] overflow-y-auto w-full admin-sidebar">
  <!-- Group 1: Front Office Master -->
  <div class="mb-2 admin-nav-group">
    @php
      $isFoActive = request()->routeIs('admin.utilities.frontoffice.*');
    @endphp
    <div class="text-[11px] font-black uppercase tracking-wider px-3 py-2 rounded-xl flex items-center justify-between cursor-pointer transition-all border select-none admin-nav-header {{ $isFoActive ? 'bg-indigo-50/80 text-indigo-700 border-indigo-200 active' : 'bg-slate-50/70 text-slate-600 border-slate-100 hover:bg-slate-100 hover:text-indigo-600' }}" onclick="toggleNavGroup(this)">
      <span class="flex items-center gap-2"><i class="fa-solid fa-desktop text-indigo-600"></i> FRONT OFFICE</span>
      <i class="fa-solid {{ $isFoActive ? 'fa-chevron-down' : 'fa-chevron-right' }} text-[10px] text-slate-400"></i>
    </div>
    <ul class="admin-sub-list flex flex-col gap-0.5 mt-1 pl-1 list-none {{ $isFoActive ? '' : 'hidden' }}">
      <li>
        <a href="{{ route('admin.utilities.frontoffice.registration-type.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.registration-type.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-address-card text-indigo-500 text-[11px]"></i> Registration Type
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.reservation-mode.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.reservation-mode.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-clipboard-check text-indigo-500 text-[11px]"></i> Mode of Reserve
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.title.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.title.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-user-tag text-indigo-500 text-[11px]"></i> Titles
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.nationality.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.nationality.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-flag text-indigo-500 text-[11px]"></i> Nationality
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.idcard-type.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.idcard-type.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-id-card text-indigo-500 text-[11px]"></i> ID Card Type
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.payment-mode.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.payment-mode.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-cash-register text-indigo-500 text-[11px]"></i> Payment Modes
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.gst-percentage.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.gst-percentage.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-percent text-indigo-500 text-[11px]"></i> GST %
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.company.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.company.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-building text-indigo-500 text-[11px]"></i> Company Manage
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.plans.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.plans.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-list-check text-indigo-500 text-[11px]"></i> Meal Plans
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.frontoffice.table-manage.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.table-manage.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-chair text-indigo-500 text-[11px]"></i> Table Manage
        </a>
      </li>
    </ul>
  </div>

  <!-- Main Item: Night Audit -->
  <a href="{{ route('admin.nightaudit.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold rounded-xl transition-all border mb-2 select-none admin-nav-flat {{ request()->routeIs('admin.nightaudit.*') ? 'bg-indigo-50 text-indigo-700 border-indigo-200 active' : 'bg-slate-50/70 text-slate-700 border-slate-100 hover:bg-slate-100 hover:text-indigo-600' }}">
    <i class="fa-solid fa-moon text-purple-600 text-xs"></i>
    <span>Night Audit</span>
  </a>

  <!-- Group 2: Room Management Master -->
  <div class="mb-2 admin-nav-group">
    @php
      $isRmActive = request()->routeIs('admin.utilities.roommanage.*') || request()->routeIs('admin.roommanagement.*') || request()->routeIs('admin.roommaintain.*');
    @endphp
    <div class="text-[11px] font-black uppercase tracking-wider px-3 py-2 rounded-xl flex items-center justify-between cursor-pointer transition-all border select-none admin-nav-header {{ $isRmActive ? 'bg-indigo-50/80 text-indigo-700 border-indigo-200 active' : 'bg-slate-50/70 text-slate-600 border-slate-100 hover:bg-slate-100 hover:text-indigo-600' }}" onclick="toggleNavGroup(this)">
      <span class="flex items-center gap-2"><i class="fa-solid fa-door-open text-purple-600"></i> ROOM MANAGEMENT</span>
      <i class="fa-solid {{ $isRmActive ? 'fa-chevron-down' : 'fa-chevron-right' }} text-[10px] text-slate-400"></i>
    </div>
    <ul class="admin-sub-list flex flex-col gap-0.5 mt-1 pl-1 list-none {{ $isRmActive ? '' : 'hidden' }}">
      <li>
        <a href="{{ route('admin.utilities.roommanage.category.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.category.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-tags text-purple-500 text-[11px]"></i> Room Categories
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.roommanage.floor.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.floor.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-layer-group text-purple-500 text-[11px]"></i> Floors Manage
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.roommanage.bedding-config.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.bedding-config.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-bed text-purple-500 text-[11px]"></i> Bedding Config
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.roommanage.amenity.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.amenity.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-wifi text-purple-500 text-[11px]"></i> Amenities Master
        </a>
      </li>
      <li>
        <a href="{{ route('admin.roommanagement.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.roommanagement.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-door-open text-purple-500 text-[11px]"></i> Rooms Add
        </a>
      </li>
      <li>
        <a href="{{ route('admin.roommaintain.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.roommaintain.*') ? 'bg-indigo-50 text-indigo-700 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-screwdriver-wrench text-purple-500 text-[11px]"></i> Room Maintenance
        </a>
      </li>
    </ul>
  </div>

  <!-- Group 3: POS Flat -->
  <div class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold rounded-xl transition-all border mb-2 select-none cursor-pointer bg-slate-50/70 text-slate-700 border-slate-100 hover:bg-slate-100 hover:text-emerald-700 admin-nav-flat" onclick="PmsAlert.toast('POS Sales master module queued for client review.', 'info')">
    <i class="fa-solid fa-utensils text-emerald-600 text-xs"></i>
    <span>POS Sales</span>
  </div>

  <!-- Group 4: House Keeping Master -->
  <div class="mb-2 admin-nav-group">
    @php
      $isHkActive = request()->routeIs('admin.utilities.housekeeping.*');
    @endphp
    <div class="text-[11px] font-black uppercase tracking-wider px-3 py-2 rounded-xl flex items-center justify-between cursor-pointer transition-all border select-none admin-nav-header {{ $isHkActive ? 'bg-amber-50/80 text-amber-700 border-amber-200 active' : 'bg-slate-50/70 text-slate-600 border-slate-100 hover:bg-slate-100 hover:text-amber-600' }}" onclick="toggleNavGroup(this)">
      <span class="flex items-center gap-2"><i class="fa-solid fa-broom text-amber-500"></i> HOUSE KEEPING</span>
      <i class="fa-solid {{ $isHkActive ? 'fa-chevron-down' : 'fa-chevron-right' }} text-[10px] text-slate-400"></i>
    </div>
    <ul class="admin-sub-list flex flex-col gap-0.5 mt-1 pl-1 list-none {{ $isHkActive ? '' : 'hidden' }}">
      <li>
        <a href="{{ route('admin.utilities.housekeeping.state.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.housekeeping.state.*') ? 'bg-amber-50 text-amber-800 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-spray-can-sparkles text-amber-500 text-[11px]"></i> Housekeeping State
        </a>
      </li>
      <li>
        <a href="{{ route('admin.utilities.housekeeping.operational.index') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.housekeeping.operational.*') ? 'bg-amber-50 text-amber-800 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-circle-nodes text-amber-500 text-[11px]"></i> Operational Status
        </a>
      </li>
      <li>
        <a href="{{ route('frontoffice.housekeeping') }}" class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold rounded-lg transition-all admin-sub-item {{ request()->routeIs('frontoffice.housekeeping*') ? 'bg-amber-50 text-amber-800 font-extrabold active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
          <i class="fa-solid fa-table-cells text-amber-500 text-[11px]"></i> Live HK Dashboard
        </a>
      </li>
    </ul>
  </div>

  <!-- Main Item: Extra Services -->
  <a href="{{ route('admin.services.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold rounded-xl transition-all border mb-2 select-none admin-nav-flat {{ request()->routeIs('admin.services.*') ? 'bg-indigo-50 text-indigo-700 border-indigo-200 active' : 'bg-slate-50/70 text-slate-700 border-slate-100 hover:bg-slate-100 hover:text-indigo-600' }}">
    <i class="fa-solid fa-bell-concierge text-indigo-600 text-xs"></i>
    <span>Extra Services</span>
  </a>

  <!-- Main Item: Stores & Inventory -->
  <a href="{{ route('stores.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold rounded-xl transition-all border mb-2 select-none admin-nav-flat {{ request()->routeIs('stores.*') ? 'bg-indigo-50 text-indigo-700 border-indigo-200 active' : 'bg-slate-50/70 text-slate-700 border-slate-100 hover:bg-slate-100 hover:text-cyan-600' }}">
    <i class="fa-solid fa-boxes-stacked text-cyan-600 text-xs"></i>
    <span>Stores &amp; Inventory</span>
  </a>

  <!-- Main Item: Accounts & Finance -->
  <a href="{{ route('accounts.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold rounded-xl transition-all border mb-2 select-none admin-nav-flat {{ request()->routeIs('accounts.*') ? 'bg-indigo-50 text-indigo-700 border-indigo-200 active' : 'bg-slate-50/70 text-slate-700 border-slate-100 hover:bg-slate-100 hover:text-purple-600' }}">
    <i class="fa-solid fa-file-invoice-dollar text-purple-600 text-xs"></i>
    <span>Accounts &amp; Finance</span>
  </a>

  <!-- Main Item: Banquet & Services -->
  <a href="{{ route('banquet.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold rounded-xl transition-all border mb-2 select-none admin-nav-flat {{ request()->routeIs('banquet.*') ? 'bg-indigo-50 text-indigo-700 border-indigo-200 active' : 'bg-slate-50/70 text-slate-700 border-slate-100 hover:bg-slate-100 hover:text-amber-600' }}">
    <i class="fa-solid fa-champagne-glasses text-amber-500 text-xs"></i>
    <span>Banquet &amp; Services</span>
  </a>

  <div class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold rounded-xl transition-all border select-none cursor-pointer bg-slate-50/70 text-slate-600 border-slate-100 hover:bg-slate-100 hover:text-slate-800 admin-nav-flat" onclick="PmsAlert.toast('System & Roles master module queued for client review.', 'info')">
    <i class="fa-solid fa-shield-halved text-slate-500 text-xs"></i>
    <span>System &amp; Roles</span>
  </div>
</aside>

<script>
  function toggleNavGroup(headerElem) {
    const subList = headerElem.nextElementSibling;
    if (subList && subList.tagName === 'UL') {
      const isHidden = subList.classList.contains('hidden') || subList.style.display === 'none';
      if (isHidden) {
        subList.classList.remove('hidden');
        subList.style.display = 'flex';
      } else {
        subList.classList.add('hidden');
        subList.style.display = 'none';
      }
      const icon = headerElem.querySelector('.fa-chevron-down, .fa-chevron-right');
      if (icon) {
        icon.className = isHidden ? 'fa-solid fa-chevron-down text-[10px] text-slate-400' : 'fa-solid fa-chevron-right text-[10px] text-slate-400';
      }
    }
  }
</script>
