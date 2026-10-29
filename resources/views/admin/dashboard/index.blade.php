@extends('admin.layouts.app')

@section('title', 'Administrator Master - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 p-4 lg:p-6 space-y-6">

  <!-- Page Title Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
    <div class="flex items-center gap-3">
      <div class="w-2.5 h-8 bg-indigo-600 rounded-full"></div>
      <div>
        <h1 class="text-xl font-black text-slate-800 tracking-tight">Enterprise System Administrator</h1>
        <p class="text-xs font-semibold text-slate-400 mt-0.5">Configure Core Master Datasets, Front Office Channels, Room Masters & Entity Workflows</p>
      </div>
    </div>
    <div class="flex items-center gap-2.5">
      <button type="button" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:text-indigo-600 rounded-xl shadow-xs transition-all cursor-pointer" onclick="exportDatabaseJSON()">
        <i class="fa-solid fa-download text-indigo-500"></i> Export Master JSON
      </button>
    </div>
  </div>

  <!-- Admin Split Layout: Sidebar + Dynamic Content -->
  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">

    <!-- Include Sidebar -->
    @include('admin.includes.sidebar')

    <!-- Dynamic Content Pane -->
    <section class="min-w-0 space-y-6">

      <!-- Active CRUD View Container -->
      <div id="crud-view-container" class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
        <!-- Header Card -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <div class="flex items-center gap-2.5">
              <h2 class="text-base font-extrabold text-slate-800" id="view-title">Mode of Reserve</h2>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200" id="view-count">4 Records</span>
            </div>
            <p class="text-xs text-slate-500 mt-1" id="view-desc">
              Manage reservation booking modes (Phone Call, Physical, Website, Online).
            </p>
          </div>
          <button type="button" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-all cursor-pointer" onclick="openCurrentAddModal()">
            <i class="fa-solid fa-plus"></i> <span id="btn-add-label">Add Entry</span>
          </button>
        </div>

        <!-- Toolbar -->
        <div class="p-4 bg-slate-50/50 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="flex-1 max-w-md">
            <div class="relative">
              <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
              <input type="text" id="table-search" class="w-full pl-9 pr-3.5 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all placeholder:text-slate-400" placeholder="Search active records..." oninput="handleSearch(this.value)">
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-indigo-600 rounded-xl shadow-xs transition-all cursor-pointer" onclick="exportCurrentTableCSV()">
              <i class="fa-solid fa-file-csv text-emerald-600"></i> Export CSV
            </button>
            <button type="button" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-indigo-600 rounded-xl shadow-xs transition-all cursor-pointer" onclick="renderCurrentTable()">
              <i class="fa-solid fa-rotate text-indigo-500"></i> Refresh
            </button>
          </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse" id="main-crud-table">
            <!-- Dynamically rendered -->
          </table>
        </div>

        <!-- Pagination & Rows Controls -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/30 flex flex-col sm:flex-row items-center justify-between gap-4" id="table-pagination-bar">
          <div class="text-xs font-semibold text-slate-500" id="pagination-info">
            Showing 0 to 0 of 0 records
          </div>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
              <span>Rows:</span>
              <select id="rows-per-page" class="px-2.5 py-1 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 cursor-pointer" onchange="handlePageSizeChange(this.value)">
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="100">100</option>
                <option value="all">All</option>
              </select>
            </div>
            <div class="flex items-center gap-1" id="pagination-nav">
              <!-- Injected via JavaScript -->
            </div>
          </div>
        </div>
      </div>

      <!-- Pending / Under Review Container -->
      <div id="pending-view-container" class="hidden bg-white border border-slate-200/80 rounded-2xl shadow-xs text-center py-16 px-6">
        <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-4 text-2xl text-emerald-600">
          <i id="pending-icon" class="fa-solid fa-utensils"></i>
        </div>
        <h3 id="pending-title" class="text-lg font-black text-slate-800 mb-2">POS Sales</h3>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 mb-3">
          <i class="fa-solid fa-clock"></i> Module Settings Queued for Client Review
        </div>
        <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
          This module's configuration parameters and workflow schemas will be integrated after discussion and sign-off with the client.
        </p>
      </div>

    </section>
  </div>
</main>

<!-- UNIVERSAL CRUD MODAL -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="admin-crud-modal">
  <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-[580px] overflow-hidden animate-in fade-in zoom-in-95 duration-150">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
        <i class="fa-solid fa-pen-to-square text-indigo-600"></i>
        <span id="modal-title">Add Record</span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-all cursor-pointer text-lg leading-none" onclick="closeModal('admin-crud-modal')">&times;</button>
    </div>
    <form onsubmit="handleSaveCrud(event)">
      <input type="hidden" id="crud-id">
      <input type="hidden" id="crud-entity">
      <div class="p-6 bg-slate-50/30 max-h-[70vh] overflow-y-auto space-y-4" id="modal-form-fields">
        <!-- Dynamically generated fields based on active entity -->
      </div>
      <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-2.5">
        <button type="button" class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition-all cursor-pointer" onclick="closeModal('admin-crud-modal')">Cancel</button>
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition-all cursor-pointer">
          <i class="fa-solid fa-floppy-disk"></i> Save Entry
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Active Master Database for Front Office and Room Management
  const adminDB = {
    // 1. FRONT OFFICE
    fo_channels: [
      { id: '1', name: 'Phone Call', status: 'Active' },
      { id: '2', name: 'Physical', status: 'Active' },
      { id: '3', name: 'Website', status: 'Active' },
      { id: '4', name: 'Online', status: 'Active' }
    ],
    fo_id_types: [
      { id: '1', name: 'Aadhaar Card (UIDAI)', code: 'AADHAAR', status: 'Active' },
      { id: '2', name: 'Passport (International)', code: 'PASSPORT', status: 'Active' },
      { id: '3', name: 'Driving License', code: 'DL', status: 'Active' },
      { id: '4', name: 'Voter ID Card', code: 'VOTER', status: 'Active' },
      { id: '5', name: 'PAN Card (Income Tax)', code: 'PAN', status: 'Active' }
    ],
    fo_payments: [
      { id: '1', name: 'QR Scanner', status: 'Active' },
      { id: '2', name: 'Cash', status: 'Active' },
      { id: '3', name: 'Phone pay', status: 'Active' },
      { id: '4', name: 'G pay', status: 'Active' },
      { id: '5', name: 'Paytm', status: 'Active' },
      { id: '6', name: 'Bank Transfer', status: 'Active' },
      { id: '7', name: 'Check Payment', status: 'Active' }
    ],

    // 2. ROOM MANAGEMENT
    rm_categories: [
      { id: '1', name: 'Deluxe Room', code: 'DELUXE', bedding: 'King Size Master (72x78)', pax: '2 Adults', status: 'Active' },
      { id: '2', name: 'Super Deluxe', code: 'SUPER DELUXE', bedding: 'King Size Master (72x78)', pax: '2 Adults + 1 Child', status: 'Active' },
      { id: '3', name: 'Suite Luxury', code: 'SUITE', bedding: 'Suite Triple / Extra Bed', pax: '3 Adults', status: 'Active' },
      { id: '4', name: 'Executive Penthouse', code: 'EXECUTIVE', bedding: 'King Size Master (72x78)', pax: '4 Adults / Family', status: 'Active' }
    ],
    rm_floors: [
      { id: '1', floor: '1', name: 'First Floor Sea Deck', rooms: '12 Rooms (101 - 112)', status: 'Active' },
      { id: '2', floor: '2', name: 'Second Floor Ocean View', rooms: '12 Rooms (201 - 212)', status: 'Active' },
      { id: '3', floor: '3', name: 'Third Floor Executive Suites', rooms: '12 Rooms (301 - 312)', status: 'Active' },
      { id: '4', floor: '4', name: 'Fourth Floor Royal Penthouse', rooms: '12 Rooms (401 - 412)', status: 'Active' }
    ],
    rm_bedding_configs: [
      { id: '1', name: 'King Size Master (72x78)', status: 'Active' },
      { id: '2', name: 'Queen Size Double (60x78)', status: 'Active' },
      { id: '3', name: 'Twin Single Beds (36x78 x 2)', status: 'Active' },
      { id: '4', name: 'Single Bed (36x78)', status: 'Active' },
      { id: '5', name: 'Suite Triple / Extra Bed', status: 'Active' }
    ],
    rm_pax_capacities: [
      { id: '1', name: '2 Adults', adults: '2 Adults', children: '0 Children', total: '2 Total Pax', status: 'Active' },
      { id: '2', name: '2 Adults + 1 Child', adults: '2 Adults', children: '1 Child', total: '3 Total Pax', status: 'Active' },
      { id: '3', name: '1 Adult (Single)', adults: '1 Adult', children: '0 Children', total: '1 Total Pax', status: 'Active' },
      { id: '4', name: '3 Adults', adults: '3 Adults', children: '0 Children', total: '3 Total Pax', status: 'Active' },
      { id: '5', name: '4 Adults / Family', adults: '4 Adults', children: '2 Children', total: '6 Total Pax', status: 'Active' }
    ],
    rm_amenities: [
      { id: '1', icon: 'fa-wifi', name: 'Free Wi-Fi', status: 'Active' },
      { id: '2', icon: 'fa-door-open', name: 'Balcony', status: 'Active' },
      { id: '3', icon: 'fa-bath', name: 'Jacuzzi', status: 'Active' },
      { id: '4', icon: 'fa-faucet-drip', name: 'Hot Water & Cold Water', status: 'Active' },
      { id: '5', icon: 'fa-snowflake', name: 'AC', status: 'Active' },
      { id: '6', icon: 'fa-fan', name: 'Non AC', status: 'Active' },
      { id: '7', icon: 'fa-wine-bottle', name: 'Mini Bar', status: 'Active' },
      { id: '8', icon: 'fa-temperature-arrow-down', name: 'Refrigerator', status: 'Active' },
      { id: '9', icon: 'fa-tv', name: 'Smart TV', status: 'Active' },
      { id: '10', icon: 'fa-phone', name: 'Room Telephone', status: 'Active' },
      { id: '11', icon: 'fa-vault', name: 'Safe Locker', status: 'Active' },
      { id: '12', icon: 'fa-mug-hot', name: 'Coffee / Tea Maker', status: 'Active' }
    ],
    rm_maintenance_types: [
      { id: '1', name: 'HVAC Air Conditioning & Compressor Service', dept: 'Engineering', priority: 'High', sla: '4 Hours' },
      { id: '2', name: 'Bathroom Plumbing & Water Pressure', dept: 'Facilities', priority: 'High', sla: '2 Hours' },
      { id: '3', name: 'Deep Steam Cleaning & Sanitization', dept: 'Housekeeping', priority: 'Medium', sla: '3 Hours' },
      { id: '4', name: 'Interior Wall Painting & Touch-up', dept: 'Civil / Maintenance', priority: 'Low', sla: '24 Hours' },
      { id: '5', name: 'RFID Smart Lock & Reader Repair', dept: 'IT / Security', priority: 'Immediate', sla: '1 Hour' }
    ]
  };

  const entityDescriptions = {
    fo_channels: 'Manage reservation booking modes (Phone Call, Physical, Website, Online).',
    fo_id_types: 'Manage acceptable guest ID Card types and KYC documents.',
    fo_payments: 'Configure accepted payment modes and transaction methods.',
    rm_categories: 'Manage room categories and categorization codes.',
    rm_floors: 'Configure floor distributions, room wings, and building blocks.',
    rm_bedding_configs: 'Manage room bed types and configuration setups.',
    rm_pax_capacities: 'Configure guest occupancy capacities, adults, and child thresholds.',
    rm_amenities: 'Manage room facilities and amenities directory with visual icons.',
    rm_maintenance_types: 'Configure maintenance task reasons, department routing, and SLA turnaround.'
  };

  let currentEntity = 'fo_channels';
  let currentEntityTitle = 'Mode of Reserve';
  let searchQuery = '';
  let currentPage = 1;
  let pageSize = 10;

  const entitySchemas = {
    fo_channels: [
      { key: 'name', label: 'Mode of Reserve', type: 'text', placeholder: 'e.g. Phone Call' },
      { key: 'status', label: 'Status', type: 'select', options: ['Active', 'Inactive'] }
    ],
    fo_id_types: [
      { key: 'name', label: 'ID Card Type', type: 'text', placeholder: 'e.g. Aadhaar Card' },
      { key: 'code', label: 'Code', type: 'text', placeholder: 'e.g. AADHAAR' },
      { key: 'status', label: 'Status', type: 'select', options: ['Active', 'Inactive'] }
    ],
    fo_payments: [
      { key: 'name', label: 'Payment Mode Name', type: 'text', placeholder: 'e.g. QR Scanner' },
      { key: 'status', label: 'Status', type: 'select', options: ['Active', 'Inactive'] }
    ],
    rm_categories: [
      { key: 'name', label: 'Category Name', type: 'text', placeholder: 'e.g. Deluxe Room' },
      { key: 'code', label: 'Category Code', type: 'text', placeholder: 'e.g. DELUXE' },
      { key: 'bedding', label: 'Bedding Config', type: 'text', placeholder: 'e.g. King Size Master (72x78)' },
      { key: 'pax', label: 'Pax Capacity', type: 'text', placeholder: 'e.g. 2 Adults' },
      { key: 'status', label: 'Status', type: 'select', options: ['Active', 'Inactive'] }
    ],
    rm_floors: [
      { key: 'floor', label: 'Floor Number', type: 'text', placeholder: '1' },
      { key: 'name', label: 'Wing / Floor Name', type: 'text', placeholder: 'First Floor Ocean Deck' },
      { key: 'rooms', label: 'Room Range / Count', type: 'text', placeholder: '12 Rooms (101 - 112)' },
      { key: 'status', label: 'Status', type: 'select', options: ['Active', 'Maintenance'] }
    ],
    rm_bedding_configs: [
      { key: 'name', label: 'Bedding Configuration Name', type: 'text', placeholder: 'e.g. King Size Master (72x78)' },
      { key: 'status', label: 'Status', type: 'select', options: ['Active', 'Inactive'] }
    ],
    rm_pax_capacities: [
      { key: 'name', label: 'Pax Capacity Title', type: 'text', placeholder: 'e.g. 2 Adults' },
      { key: 'adults', label: 'Max Adults', type: 'text', placeholder: 'e.g. 2 Adults' },
      { key: 'children', label: 'Max Children', type: 'text', placeholder: 'e.g. 0 Children' },
      { key: 'total', label: 'Total Occupancy', type: 'text', placeholder: 'e.g. 2 Total Pax' },
      { key: 'status', label: 'Status', type: 'select', options: ['Active', 'Inactive'] }
    ],
    rm_amenities: [
      { key: 'icon', label: 'Icon', type: 'text', placeholder: 'fa-wifi' },
      { key: 'name', label: 'Facility / Amenity Name', type: 'text', placeholder: 'e.g. Free Wi-Fi' },
      { key: 'status', label: 'Status', type: 'select', options: ['Active', 'Inactive'] }
    ],
    rm_maintenance_types: [
      { key: 'name', label: 'Maintenance Issue Type', type: 'text', placeholder: 'e.g. AC Repair' },
      { key: 'dept', label: 'Assigned Department', type: 'select', options: ['Engineering', 'Facilities', 'Housekeeping', 'Civil / Maintenance', 'IT / Security'] },
      { key: 'priority', label: 'Priority Level', type: 'select', options: ['Low', 'Medium', 'High', 'Immediate'] },
      { key: 'sla', label: 'SLA Turnaround Time', type: 'text', placeholder: 'e.g. 4 Hours' }
    ]
  };

  function toggleNavGroup(headerElem) {
    const subList = headerElem.nextElementSibling;
    if (subList && subList.tagName === 'UL') {
      const isHidden = subList.classList.contains('hidden');
      if (isHidden) {
        subList.classList.remove('hidden');
      } else {
        subList.classList.add('hidden');
      }
      const icon = headerElem.querySelector('.fa-chevron-down, .fa-chevron-right');
      if (icon) {
        icon.className = isHidden ? 'fa-solid fa-chevron-down text-[10px] text-slate-400' : 'fa-solid fa-chevron-right text-[10px] text-slate-400';
      }
    }
  }

  function switchSection(entityKey, title, clickedItem) {
    currentEntity = entityKey;
    currentEntityTitle = title;
    currentPage = 1;

    document.querySelectorAll('.admin-sub-item, .admin-nav-flat').forEach(el => el.classList.remove('active'));
    if (clickedItem) clickedItem.classList.add('active');

    const pendingCont = document.getElementById('pending-view-container');
    const crudCont = document.getElementById('crud-view-container');
    if (pendingCont) pendingCont.classList.add('hidden');
    if (crudCont) crudCont.classList.remove('hidden');

    document.getElementById('view-title').textContent = title;
    document.getElementById('view-desc').textContent = entityDescriptions[entityKey] || 'Configure and manage master records.';
    document.getElementById('btn-add-label').textContent = 'Add Entry';

    document.getElementById('table-search').value = '';
    searchQuery = '';

    renderCurrentTable();
  }

  function showPendingModule(moduleTitle, iconClass, iconColor, clickedItem) {
    document.querySelectorAll('.admin-sub-item, .admin-nav-flat').forEach(el => el.classList.remove('active'));
    if (clickedItem) clickedItem.classList.add('active');

    const pendingCont = document.getElementById('pending-view-container');
    const crudCont = document.getElementById('crud-view-container');
    if (crudCont) crudCont.classList.add('hidden');
    if (pendingCont) pendingCont.classList.remove('hidden');

    const pendingIcon = document.getElementById('pending-icon');
    if (pendingIcon) pendingIcon.className = `fa-solid ${iconClass}`;
    document.getElementById('pending-title').textContent = moduleTitle;
  }

  function handleSearch(val) {
    searchQuery = (val || '').toLowerCase().trim();
    currentPage = 1;
    renderCurrentTable();
  }

  function handlePageSizeChange(val) {
    pageSize = val === 'all' ? 'all' : parseInt(val, 10);
    currentPage = 1;
    renderCurrentTable();
  }

  function goToPage(page) {
    currentPage = page;
    renderCurrentTable();
  }

  function renderCurrentTable() {
    const records = adminDB[currentEntity] || [];
    const schema = entitySchemas[currentEntity] || [];
    const table = document.getElementById('main-crud-table');

    let filtered = records;
    if (searchQuery) {
      filtered = filtered.filter(item => {
        return Object.values(item).some(v => String(v).toLowerCase().includes(searchQuery));
      });
    }

    const totalCount = filtered.length;
    const totalPages = pageSize === 'all' ? 1 : Math.max(1, Math.ceil(totalCount / pageSize));

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIdx = pageSize === 'all' ? 0 : (currentPage - 1) * pageSize;
    const endIdx = pageSize === 'all' ? totalCount : Math.min(startIdx + pageSize, totalCount);
    const pageItems = filtered.slice(startIdx, endIdx);

    document.getElementById('view-count').textContent = `${records.length} Record${records.length === 1 ? '' : 's'}`;

    if (schema.length === 0 || (records.length === 0 && !searchQuery)) {
      table.innerHTML = `<tr><td colspan="6" class="text-center py-12 px-4 text-xs font-semibold text-slate-400"><i class="fa-solid fa-inbox text-3xl mb-2.5 block text-slate-300"></i>No records found. Click "+ Add Entry" to create one.</td></tr>`;
      document.getElementById('pagination-info').textContent = 'Showing 0 to 0 of 0 records';
      renderPaginationNav(1);
      return;
    }

    let thead = '<thead><tr class="bg-slate-50/80 border-b border-slate-200/80">';
    thead += '<th class="w-12 px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">#</th>';
    schema.forEach(col => {
      let thClass = col.key === 'icon' ? 'w-20 ' : '';
      thead += `<th class="${thClass}px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">${col.label}</th>`;
    });
    thead += '<th class="w-28 px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>';
    thead += '</tr></thead>';

    let tbody = '<tbody>';
    if (filtered.length === 0) {
      tbody += `<tr><td colspan="${schema.length + 2}" class="text-center py-8 px-4 text-xs font-semibold text-slate-400">No records match your search query "${searchQuery}".</td></tr>`;
    } else {
      pageItems.forEach((row, idx) => {
        const rowNumber = startIdx + idx + 1;
        tbody += '<tr class="border-b border-slate-100 hover:bg-slate-50/60 transition-colors">';
        tbody += `<td class="px-4 py-3 font-mono text-[11px] font-bold text-slate-400">${rowNumber}</td>`;
        schema.forEach(col => {
          let val = row[col.key] || '-';
          let formattedVal = val;
          if (col.key === 'status') {
            let isAct = (val === 'Active' || val === 'Online' || val === 'Available');
            formattedVal = isAct 
              ? `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-circle-check text-[8px]"></i>${val}</span>`
              : `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">${val}</span>`;
          } else if (col.key === 'priority') {
            let isCrit = (val === 'Immediate' || val === 'High');
            formattedVal = isCrit
              ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-50 text-rose-700 border border-rose-200">${val}</span>`
              : `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">${val}</span>`;
          } else if (col.key === 'icon') {
            formattedVal = `<div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-sm"><i class="fa-solid ${val}"></i></div>`;
          }
          tbody += `<td class="px-4 py-3 text-xs font-medium text-slate-700">${formattedVal}</td>`;
        });
        tbody += `
          <td class="px-4 py-3 text-right">
            <div class="inline-flex items-center gap-1 justify-end">
              <button type="button" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all cursor-pointer" onclick="openEditModal('${row.id}')" title="Edit Record"><i class="fa-solid fa-pen text-xs"></i></button>
              <button type="button" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-all cursor-pointer" onclick="deleteCrudItem('${row.id}')" title="Delete Record"><i class="fa-solid fa-trash text-xs"></i></button>
            </div>
          </td>
        `;
        tbody += '</tr>';
      });
    }
    tbody += '</tbody>';

    table.innerHTML = thead + tbody;

    const infoEl = document.getElementById('pagination-info');
    if (totalCount === 0) {
      infoEl.textContent = 'Showing 0 to 0 of 0 records';
    } else {
      infoEl.innerHTML = `Showing <strong class="text-slate-800">${startIdx + 1}</strong> to <strong class="text-slate-800">${endIdx}</strong> of <strong class="text-slate-800">${totalCount}</strong> records${totalCount !== records.length ? ' (filtered from ' + records.length + ' total)' : ''}`;
    }

    renderPaginationNav(totalPages);
  }

  function renderPaginationNav(totalPages) {
    const nav = document.getElementById('pagination-nav');
    if (!nav) return;

    if (totalPages <= 1) {
      nav.innerHTML = `
        <button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all text-slate-400 opacity-40 cursor-not-allowed" disabled><i class="fa-solid fa-chevron-left"></i></button>
        <button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-extrabold transition-all bg-indigo-600 text-white shadow-xs">1</button>
        <button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all text-slate-400 opacity-40 cursor-not-allowed" disabled><i class="fa-solid fa-chevron-right"></i></button>
      `;
      return;
    }

    let html = '';
    html += `<button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} title="Previous Page"><i class="fa-solid fa-chevron-left"></i></button>`;

    const delta = 1;
    const range = [];
    const rangeWithDots = [];

    for (let i = 1; i <= totalPages; i++) {
      if (i === 1 || i === totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
        range.push(i);
      }
    }

    let prev = 0;
    for (const i of range) {
      if (prev) {
        if (i - prev === 2) {
          rangeWithDots.push(prev + 1);
        } else if (i - prev !== 1) {
          rangeWithDots.push('...');
        }
      }
      rangeWithDots.push(i);
      prev = i;
    }

    rangeWithDots.forEach(page => {
      if (page === '...') {
        html += `<span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400">...</span>`;
      } else {
        const isActive = page === currentPage;
        const btnClass = isActive 
          ? 'bg-indigo-600 text-white shadow-xs font-extrabold' 
          : 'text-slate-600 hover:bg-slate-100 font-bold';
        html += `<button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs transition-all cursor-pointer ${btnClass}" onclick="goToPage(${page})">${page}</button>`;
      }
    });

    html += `<button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all text-slate-600 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} title="Next Page"><i class="fa-solid fa-chevron-right"></i></button>`;

    nav.innerHTML = html;
  }

  function openCurrentAddModal() {
    const schema = entitySchemas[currentEntity] || [];
    document.getElementById('modal-title').textContent = `Add New ${currentEntityTitle}`;
    document.getElementById('crud-entity').value = currentEntity;
    document.getElementById('crud-id').value = '';

    let formHtml = '';
    schema.forEach(field => {
      formHtml += `<div>`;
      formHtml += `<label class="block text-xs font-bold text-slate-700 mb-1.5">${field.label}</label>`;
      if (field.type === 'select') {
        formHtml += `<select id="f_${field.key}" class="w-full px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">`;
        field.options.forEach(opt => {
          formHtml += `<option value="${opt}">${opt}</option>`;
        });
        formHtml += `</select>`;
      } else {
        formHtml += `<input type="${field.type || 'text'}" id="f_${field.key}" class="w-full px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 placeholder:text-slate-400" placeholder="${field.placeholder || ''}" required>`;
      }
      formHtml += `</div>`;
    });

    document.getElementById('modal-form-fields').innerHTML = formHtml;
    openModal('admin-crud-modal');
  }

  function openEditModal(id) {
    const record = (adminDB[currentEntity] || []).find(r => String(r.id) === String(id));
    if (!record) {
      console.warn('Record not found for id:', id);
      return;
    }

    const schema = entitySchemas[currentEntity] || [];
    document.getElementById('modal-title').textContent = `Edit ${currentEntityTitle}`;
    document.getElementById('crud-entity').value = currentEntity;
    document.getElementById('crud-id').value = record.id;

    let formHtml = '';
    schema.forEach(field => {
      formHtml += `<div>`;
      formHtml += `<label class="block text-xs font-bold text-slate-700 mb-1.5">${field.label}</label>`;
      if (field.type === 'select') {
        formHtml += `<select id="f_${field.key}" class="w-full px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">`;
        field.options.forEach(opt => {
          const isSel = (String(record[field.key]) === String(opt)) ? 'selected' : '';
          formHtml += `<option value="${opt}" ${isSel}>${opt}</option>`;
        });
        formHtml += `</select>`;
      } else {
        const val = record[field.key] || '';
        formHtml += `<input type="${field.type || 'text'}" id="f_${field.key}" class="w-full px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" value="${val}" required>`;
      }
      formHtml += `</div>`;
    });

    document.getElementById('modal-form-fields').innerHTML = formHtml;
    openModal('admin-crud-modal');
  }

  function handleSaveCrud(e) {
    e.preventDefault();
    const entity = document.getElementById('crud-entity').value;
    const id = document.getElementById('crud-id').value;
    const schema = entitySchemas[entity] || [];
    const records = adminDB[entity] || [];

    const newRecord = { id: id || String(Date.now()) };
    schema.forEach(field => {
      const input = document.getElementById(`f_${field.key}`);
      if (input) {
        newRecord[field.key] = input.value;
      }
    });

    if (id) {
      const index = records.findIndex(r => String(r.id) === String(id));
      if (index !== -1) {
        records[index] = newRecord;
        if (window.PmsAlert) PmsAlert.toast(`Record updated successfully in ${currentEntityTitle}!`);
      }
    } else {
      records.unshift(newRecord);
      if (window.PmsAlert) PmsAlert.toast(`New record created in ${currentEntityTitle}!`);
    }

    closeModal('admin-crud-modal');
    renderCurrentTable();
  }

  function deleteCrudItem(id) {
    const records = adminDB[currentEntity] || [];
    const record = records.find(r => String(r.id) === String(id));
    if (!record) return;

    if (window.PmsAlert && PmsAlert.confirmDelete) {
      PmsAlert.confirmDelete(`Delete Entry?`, `Are you sure you want to delete this entry from ${currentEntityTitle}?`).then(result => {
        if (result.isConfirmed) {
          adminDB[currentEntity] = records.filter(r => String(r.id) !== String(id));
          PmsAlert.toast(`Record deleted from ${currentEntityTitle}.`);
          renderCurrentTable();
        }
      });
    } else {
      if (confirm(`Are you sure you want to delete this entry from ${currentEntityTitle}?`)) {
        adminDB[currentEntity] = records.filter(r => String(r.id) !== String(id));
        renderCurrentTable();
      }
    }
  }

  function exportCurrentTableCSV() {
    const records = adminDB[currentEntity] || [];
    const schema = entitySchemas[currentEntity] || [];
    if (records.length === 0) {
      if (window.PmsAlert) PmsAlert.toast('No records available to export.', 'info');
      return;
    }

    let csv = schema.map(c => `"${c.label}"`).join(',') + '\n';
    records.forEach(r => {
      csv += schema.map(c => `"${r[c.key] || ''}"`).join(',') + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('href', url);
    a.setAttribute('download', `${currentEntity}_Master_Data.csv`);
    a.click();
    if (window.PmsAlert) PmsAlert.toast(`Exported ${currentEntityTitle} as CSV!`);
  }

  function exportDatabaseJSON() {
    const blob = new Blob([JSON.stringify(adminDB, null, 2)], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('href', url);
    a.setAttribute('download', 'Hotel_Sagar_Sonnet_Administrator_Master_DB.json');
    a.click();
    if (window.PmsAlert) PmsAlert.toast('Master Database exported as JSON!');
  }

  function openModal(id) {
    const m = document.getElementById(id);
    if (m) {
      m.classList.remove('hidden');
      m.classList.add('flex');
    }
  }

  function closeModal(id) {
    const m = document.getElementById(id);
    if (m) {
      m.classList.add('hidden');
      m.classList.remove('flex');
    }
  }

  window.addEventListener('DOMContentLoaded', () => {
    renderCurrentTable();
  });
</script>
@endpush
