<!-- Administrator Master Sidebar -->
<aside class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden h-fit sticky top-3 max-h-[calc(100vh-100px)] flex flex-col w-full admin-sidebar">
  
  <!-- Sidebar Brand / Title Header -->
  <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between gap-3 bg-gradient-to-r from-slate-50/90 via-slate-50/50 to-white">
    <div class="flex items-center gap-2.5">
      <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-700 text-white flex items-center justify-center text-xs shadow-xs font-bold shrink-0">
        <i class="fa-solid fa-sliders"></i>
      </div>
      <div class="min-w-0">
        <div class="text-xs font-black uppercase tracking-wider text-slate-800 truncate">Master Setup</div>
        <div class="text-[10px] text-slate-400 font-semibold truncate">Settings &amp; Utilities</div>
      </div>
    </div>
    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shrink-0">
      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
      Online
    </span>
  </div>

  <!-- Live Quick Search Filter Bar -->
  <div class="px-3.5 pt-3 pb-2 border-b border-slate-100/80 bg-white">
    <div class="relative">
      <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[11px]"></i>
      <input type="text" 
             id="admin-sidebar-search" 
             placeholder="Search masters &amp; settings..." 
             class="w-full pl-8 pr-7 py-1.5 bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 rounded-xl text-xs text-slate-700 placeholder-slate-400 outline-none transition-all font-medium"
             onkeyup="filterAdminSidebar(this.value)">
      <button type="button" 
              id="admin-sidebar-search-clear" 
              class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-600 flex items-center justify-center text-[9px] transition-all cursor-pointer" 
              onclick="clearSidebarSearch()" 
              title="Clear Search">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
  </div>

  <!-- Scrollable Nav Groups Container -->
  <div class="p-3 space-y-2.5 overflow-y-auto max-h-[calc(100vh-250px)] scrollbar-thin scrollbar-thumb-slate-200/80" id="admin-sidebar-nav-container">

    <!-- Section Heading -->
    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 px-2 pt-0.5 flex items-center justify-between select-none">
      <span>Master Registries</span>
    </div>

    <!-- Group 1: Front Office Master -->
    <div class="admin-nav-group rounded-xl overflow-hidden border border-slate-100 transition-all bg-white" data-group-name="front-office">
      @php
        $isFoActive = request()->routeIs('admin.utilities.frontoffice.*');
      @endphp
      <div class="px-3 py-2.5 flex items-center justify-between cursor-pointer transition-all select-none admin-nav-header group {{ $isFoActive ? 'bg-indigo-50/80 text-indigo-900 font-bold active' : 'bg-slate-50/60 text-slate-700 hover:bg-indigo-50/40 hover:text-indigo-700' }}" onclick="toggleNavGroup(this)">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-7 h-7 rounded-lg bg-indigo-100/80 text-indigo-600 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
            <i class="fa-solid fa-desktop"></i>
          </div>
          <span class="text-xs font-bold tracking-tight truncate">Front Office</span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-[9.5px] font-extrabold px-1.5 py-0.5 rounded-md bg-white border border-slate-200/80 text-slate-500 group-hover:border-indigo-200 group-hover:text-indigo-600 transition-colors">10</span>
          <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 transition-transform duration-200 {{ $isFoActive ? 'rotate-90 text-indigo-600' : '' }}"></i>
        </div>
      </div>
      <ul class="admin-sub-list flex flex-col gap-1.5 mt-2 pt-3 pb-2.5 px-2.5 list-none border-t border-slate-100 bg-slate-50/40 {{ $isFoActive ? '' : 'hidden' }}">
        <li>
          <a href="{{ route('admin.utilities.frontoffice.registration-type.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.registration-type.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-address-card text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.registration-type.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">Registration Type</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.frontoffice.reservation-mode.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.reservation-mode.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-clipboard-check text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.reservation-mode.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">Mode of Reserve</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.frontoffice.title.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.title.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-user-tag text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.title.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">Titles</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.frontoffice.nationality.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.nationality.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-flag text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.nationality.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">Nationality</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.frontoffice.idcard-type.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.idcard-type.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-id-card text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.idcard-type.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">ID Card Type</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.frontoffice.payment-mode.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.payment-mode.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-cash-register text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.payment-mode.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">Payment Modes</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.frontoffice.gst-percentage.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.gst-percentage.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-percent text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.gst-percentage.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">GST Percentage</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.frontoffice.company.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.company.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-building text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.company.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">Company Manage</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.frontoffice.plans.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.plans.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-utensils text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.plans.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">Meal Plans</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.frontoffice.table-manage.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.frontoffice.table-manage.*') ? 'active' : 'text-slate-600 hover:bg-indigo-50 hover:text-indigo-700' }}">
            <i class="fa-solid fa-chair text-xs w-4 text-center {{ request()->routeIs('admin.utilities.frontoffice.table-manage.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
            <span class="truncate">Table Manage</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Group 2: Room Management Master -->
    <div class="admin-nav-group rounded-xl overflow-hidden border border-slate-100 transition-all bg-white" data-group-name="room-management">
      @php
        $isRmActive = request()->routeIs('admin.utilities.roommanage.*') || request()->routeIs('admin.roommanagement.*') || request()->routeIs('admin.roommaintain.*');
      @endphp
      <div class="px-3 py-2.5 flex items-center justify-between cursor-pointer transition-all select-none admin-nav-header group {{ $isRmActive ? 'bg-purple-50/80 text-purple-900 font-bold active' : 'bg-slate-50/60 text-slate-700 hover:bg-purple-50/40 hover:text-purple-700' }}" onclick="toggleNavGroup(this)">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-7 h-7 rounded-lg bg-purple-100/80 text-purple-600 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
            <i class="fa-solid fa-door-open"></i>
          </div>
          <span class="text-xs font-bold tracking-tight truncate">Room Management</span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-[9.5px] font-extrabold px-1.5 py-0.5 rounded-md bg-white border border-slate-200/80 text-slate-500 group-hover:border-purple-200 group-hover:text-purple-600 transition-colors">6</span>
          <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 transition-transform duration-200 {{ $isRmActive ? 'rotate-90 text-purple-600' : '' }}"></i>
        </div>
      </div>
      <ul class="admin-sub-list flex flex-col gap-1.5 mt-2 pt-3 pb-2.5 px-2.5 list-none border-t border-slate-100 bg-slate-50/40 {{ $isRmActive ? '' : 'hidden' }}">
        <li>
          <a href="{{ route('admin.utilities.roommanage.category.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.category.*') ? 'active' : 'text-slate-600 hover:bg-purple-50 hover:text-purple-700' }}">
            <i class="fa-solid fa-tags text-xs w-4 text-center {{ request()->routeIs('admin.utilities.roommanage.category.*') ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-600' }}"></i>
            <span class="truncate">Room Categories</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.roommanage.floor.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.floor.*') ? 'active' : 'text-slate-600 hover:bg-purple-50 hover:text-purple-700' }}">
            <i class="fa-solid fa-layer-group text-xs w-4 text-center {{ request()->routeIs('admin.utilities.roommanage.floor.*') ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-600' }}"></i>
            <span class="truncate">Floors Manage</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.roommanage.bedding-config.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.bedding-config.*') ? 'active' : 'text-slate-600 hover:bg-purple-50 hover:text-purple-700' }}">
            <i class="fa-solid fa-bed text-xs w-4 text-center {{ request()->routeIs('admin.utilities.roommanage.bedding-config.*') ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-600' }}"></i>
            <span class="truncate">Bedding Config</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.roommanage.amenity.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.roommanage.amenity.*') ? 'active' : 'text-slate-600 hover:bg-purple-50 hover:text-purple-700' }}">
            <i class="fa-solid fa-wifi text-xs w-4 text-center {{ request()->routeIs('admin.utilities.roommanage.amenity.*') ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-600' }}"></i>
            <span class="truncate">Amenities Master</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.roommanagement.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.roommanagement.*') ? 'active' : 'text-slate-600 hover:bg-purple-50 hover:text-purple-700' }}">
            <i class="fa-solid fa-door-closed text-xs w-4 text-center {{ request()->routeIs('admin.roommanagement.*') ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-600' }}"></i>
            <span class="truncate">Rooms Add / Inventory</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.roommaintain.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.roommaintain.*') ? 'active' : 'text-slate-600 hover:bg-purple-50 hover:text-purple-700' }}">
            <i class="fa-solid fa-screwdriver-wrench text-xs w-4 text-center {{ request()->routeIs('admin.roommaintain.*') ? 'text-purple-600' : 'text-slate-400 group-hover:text-purple-600' }}"></i>
            <span class="truncate">Room Maintenance</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Group 3: Housekeeping Master -->
    <div class="admin-nav-group rounded-xl overflow-hidden border border-slate-100 transition-all bg-white" data-group-name="housekeeping">
      @php
        $isHkActive = request()->routeIs('admin.utilities.housekeeping.*') || request()->routeIs('frontoffice.housekeeping*');
      @endphp
      <div class="px-3 py-2.5 flex items-center justify-between cursor-pointer transition-all select-none admin-nav-header group {{ $isHkActive ? 'bg-amber-50/80 text-amber-900 font-bold active' : 'bg-slate-50/60 text-slate-700 hover:bg-amber-50/40 hover:text-amber-700' }}" onclick="toggleNavGroup(this)">
        <div class="flex items-center gap-2.5 min-w-0">
          <div class="w-7 h-7 rounded-lg bg-amber-100/80 text-amber-600 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
            <i class="fa-solid fa-broom"></i>
          </div>
          <span class="text-xs font-bold tracking-tight truncate">Housekeeping</span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
          <span class="text-[9.5px] font-extrabold px-1.5 py-0.5 rounded-md bg-white border border-slate-200/80 text-slate-500 group-hover:border-amber-200 group-hover:text-amber-600 transition-colors">3</span>
          <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 transition-transform duration-200 {{ $isHkActive ? 'rotate-90 text-amber-600' : '' }}"></i>
        </div>
      </div>
      <ul class="admin-sub-list flex flex-col gap-1.5 mt-2 pt-3 pb-2.5 px-2.5 list-none border-t border-slate-100 bg-slate-50/40 {{ $isHkActive ? '' : 'hidden' }}">
        <li>
          <a href="{{ route('admin.utilities.housekeeping.state.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.housekeeping.state.*') ? 'active' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-700' }}">
            <i class="fa-solid fa-spray-can-sparkles text-xs w-4 text-center {{ request()->routeIs('admin.utilities.housekeeping.state.*') ? 'text-amber-600' : 'text-slate-400 group-hover:text-amber-600' }}"></i>
            <span class="truncate">Housekeeping State</span>
          </a>
        </li>
        <li>
          <a href="{{ route('admin.utilities.housekeeping.operational.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('admin.utilities.housekeeping.operational.*') ? 'active' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-700' }}">
            <i class="fa-solid fa-circle-nodes text-xs w-4 text-center {{ request()->routeIs('admin.utilities.housekeeping.operational.*') ? 'text-amber-600' : 'text-slate-400 group-hover:text-amber-600' }}"></i>
            <span class="truncate">Operational Status</span>
          </a>
        </li>
        <li>
          <a href="{{ route('frontoffice.housekeeping') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold rounded-lg transition-all admin-sub-item {{ request()->routeIs('frontoffice.housekeeping*') ? 'active' : 'text-slate-600 hover:bg-amber-50 hover:text-amber-700' }}">
            <i class="fa-solid fa-table-cells text-xs w-4 text-center {{ request()->routeIs('frontoffice.housekeeping*') ? 'text-amber-600' : 'text-slate-400 group-hover:text-amber-600' }}"></i>
            <span class="truncate">Live HK Dashboard</span>
          </a>
        </li>
      </ul>
    </div>

    <!-- Section Heading: Operations & Audit -->
    <div class="text-[10px] font-black uppercase tracking-wider text-slate-400 px-2 pt-2 border-t border-slate-100 flex items-center justify-between select-none">
      <span>Operational Services</span>
    </div>

    <!-- Main Item: Extra Services Master -->
    <a href="{{ route('admin.services.index') }}" class="flex items-center justify-between px-3 py-2.5 text-xs font-bold rounded-xl transition-all border border-slate-100 select-none admin-nav-flat group {{ request()->routeIs('admin.services.*') ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs active' : 'bg-slate-50/60 text-slate-700 hover:bg-indigo-50/60 hover:text-indigo-700 hover:border-indigo-100' }}">
      <div class="flex items-center gap-2.5 min-w-0">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition-transform shadow-2xs {{ request()->routeIs('admin.services.*') ? 'bg-white/20 text-white' : 'bg-indigo-100/80 text-indigo-600' }}">
          <i class="fa-solid fa-bell-concierge"></i>
        </div>
        <span class="truncate font-bold">Extra Services</span>
      </div>
      <i class="fa-solid fa-chevron-right text-[10px] {{ request()->routeIs('admin.services.*') ? 'text-white/80' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
    </a>

    <!-- Main Item: Night Audit Master Dossier -->
    <a href="{{ route('admin.nightaudit.index') }}" class="flex items-center justify-between px-3 py-2.5 text-xs font-bold rounded-xl transition-all border border-slate-100 select-none admin-nav-flat group {{ request()->routeIs('admin.nightaudit.*') ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs active' : 'bg-slate-50/60 text-slate-700 hover:bg-indigo-50/60 hover:text-indigo-700 hover:border-indigo-100' }}">
      <div class="flex items-center gap-2.5 min-w-0">
        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition-transform shadow-2xs {{ request()->routeIs('admin.nightaudit.*') ? 'bg-white/20 text-white' : 'bg-purple-100/80 text-purple-600' }}">
          <i class="fa-solid fa-moon"></i>
        </div>
        <span class="truncate font-bold">Night Audit History</span>
      </div>
      <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded {{ request()->routeIs('admin.nightaudit.*') ? 'bg-white/20 text-white' : 'bg-purple-50 text-purple-700 border border-purple-200/60' }}">Dossier</span>
    </a>

    <!-- System & Roles Placeholder -->
    <div class="flex items-center justify-between px-3 py-2.5 text-xs font-bold rounded-xl transition-all border border-slate-100 select-none cursor-pointer bg-slate-50/60 text-slate-600 hover:bg-slate-100/80 hover:text-slate-800 admin-nav-flat group" onclick="PmsAlert.toast('System & Roles master module queued for client review.', 'info')">
      <div class="flex items-center gap-2.5 min-w-0">
        <div class="w-7 h-7 rounded-lg bg-slate-200/70 text-slate-500 flex items-center justify-center text-xs shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <span class="truncate font-bold">System &amp; Roles</span>
      </div>
      <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.5 rounded bg-slate-200/60 text-slate-600">Admin</span>
    </div>

  </div>

  <!-- Micro Footer -->
  <div class="px-4 py-2.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-500 shrink-0">
    <div class="flex items-center gap-1.5">
      <i class="fa-solid fa-circle-check text-emerald-500 text-[9px]"></i>
      <span class="font-bold">Hotel Sagar Sonnet</span>
    </div>
    <span class="font-mono text-[9.5px] font-bold text-slate-400">PMS v2026.1</span>
  </div>

</aside>

<script>
  function toggleNavGroup(headerElem) {
    const parentGroup = headerElem.closest('.admin-nav-group');
    if (!parentGroup) return;
    const subList = parentGroup.querySelector('.admin-sub-list');
    const chevron = headerElem.querySelector('.fa-chevron-right, .fa-chevron-down');
    
    if (subList) {
      const isHidden = subList.classList.contains('hidden') || subList.style.display === 'none';
      if (isHidden) {
        subList.classList.remove('hidden');
        subList.style.display = 'flex';
        if (chevron) {
          chevron.classList.add('rotate-90');
          chevron.classList.add('text-indigo-600');
        }
      } else {
        subList.classList.add('hidden');
        subList.style.display = 'none';
        if (chevron) {
          chevron.classList.remove('rotate-90');
          chevron.classList.remove('text-indigo-600');
        }
      }
    }
  }

  function filterAdminSidebar(query) {
    const term = (query || '').trim().toLowerCase();
    const clearBtn = document.getElementById('admin-sidebar-search-clear');
    if (clearBtn) {
      if (term.length > 0) {
        clearBtn.classList.remove('hidden');
      } else {
        clearBtn.classList.add('hidden');
      }
    }

    const groups = document.querySelectorAll('.admin-nav-group');
    const flatLinks = document.querySelectorAll('.admin-nav-flat');

    if (!term) {
      // Reset view to original state
      groups.forEach(g => {
        g.style.display = 'block';
        const subList = g.querySelector('.admin-sub-list');
        const header = g.querySelector('.admin-nav-header');
        const chevron = header ? header.querySelector('.fa-chevron-right, .fa-chevron-down') : null;
        const isActive = header && header.classList.contains('active');
        
        if (subList) {
          if (isActive) {
            subList.classList.remove('hidden');
            subList.style.display = 'flex';
            if (chevron) chevron.classList.add('rotate-90');
          } else {
            subList.classList.add('hidden');
            subList.style.display = 'none';
            if (chevron) chevron.classList.remove('rotate-90');
          }
          subList.querySelectorAll('li').forEach(li => li.style.display = 'block');
        }
      });
      flatLinks.forEach(fl => fl.style.display = 'flex');
      return;
    }

    // Filter sub items and groups
    groups.forEach(g => {
      const subList = g.querySelector('.admin-sub-list');
      const header = g.querySelector('.admin-nav-header');
      const chevron = header ? header.querySelector('.fa-chevron-right, .fa-chevron-down') : null;
      let groupHasMatch = false;

      if (subList) {
        const items = subList.querySelectorAll('li');
        items.forEach(li => {
          const txt = (li.textContent || '').toLowerCase();
          if (txt.includes(term)) {
            li.style.display = 'block';
            groupHasMatch = true;
          } else {
            li.style.display = 'none';
          }
        });

        if (groupHasMatch) {
          g.style.display = 'block';
          subList.classList.remove('hidden');
          subList.style.display = 'flex';
          if (chevron) chevron.classList.add('rotate-90');
        } else {
          // Check if header itself matches
          const headerTxt = (header ? header.textContent : '').toLowerCase();
          if (headerTxt.includes(term)) {
            g.style.display = 'block';
            subList.classList.remove('hidden');
            subList.style.display = 'flex';
            items.forEach(li => li.style.display = 'block');
            if (chevron) chevron.classList.add('rotate-90');
          } else {
            g.style.display = 'none';
          }
        }
      }
    });

    flatLinks.forEach(fl => {
      const txt = (fl.textContent || '').toLowerCase();
      if (txt.includes(term)) {
        fl.style.display = 'flex';
      } else {
        fl.style.display = 'none';
      }
    });
  }

  function clearSidebarSearch() {
    const input = document.getElementById('admin-sidebar-search');
    if (input) {
      input.value = '';
      filterAdminSidebar('');
      input.focus();
    }
  }
</script>
