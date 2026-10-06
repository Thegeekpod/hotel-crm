@extends('admin.layouts.app')

@section('title', 'Administrator Master - Hotel Sagar Sonnet PMS')

@push('styles')
<style>
  /* Administrator Split-Layout Styles */
  .admin-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 20px;
    min-height: calc(100vh - 165px);
  }

  .pms-page-header {
    border-bottom: none !important;
    background: transparent !important;
  }

  .admin-sidebar {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-lg);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    padding: 14px;
    height: fit-content;
    position: sticky;
    top: 10px;
    max-height: calc(100vh - 180px);
    overflow-y: auto;
  }

  .admin-nav-group {
    margin-bottom: 8px;
  }

  .admin-nav-header {
    font-size: 11px;
    font-weight: 800;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 9px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: var(--radius-md);
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
    background: #f8fafc;
    border: 1px solid #f1f5f9;
  }

  .admin-nav-header:hover {
    background: #f1f5f9;
    color: var(--accent-primary);
  }

  .admin-nav-header.active {
    color: var(--accent-primary);
    font-weight: 900;
    background: rgba(99, 102, 241, 0.08);
    border-color: rgba(99, 102, 241, 0.2);
  }

  /* Flat Non-collapsible Nav Items */
  .admin-nav-flat {
    font-size: 11px;
    font-weight: 800;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 10px 12px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: var(--radius-md);
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
    background: #ffffff;
    border: 1px solid #f1f5f9;
    margin-bottom: 6px;
  }

  .admin-nav-flat:hover {
    background: #f8fafc;
    color: var(--accent-primary);
    border-color: var(--border-medium);
  }

  .admin-nav-flat.active {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.05));
    color: var(--accent-primary);
    border-color: var(--accent-primary);
    font-weight: 900;
  }

  .admin-sub-list {
    list-style: none;
    padding: 4px 0 4px 8px;
    margin: 4px 0 0 4px;
    border-left: 2px solid var(--border-medium);
  }

  .admin-sub-item {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    padding: 7px 10px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    margin-bottom: 2px;
  }

  .admin-sub-item:hover {
    background: rgba(99, 102, 241, 0.06);
    color: var(--accent-primary);
    transform: translateX(3px);
  }

  .admin-sub-item.active {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(168, 85, 247, 0.08));
    color: var(--accent-primary);
    font-weight: 800;
    border-left: 3px solid var(--accent-primary);
  }

  .admin-content-pane {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-lg);
    box-shadow: 0 4px 16px rgba(0,0,0,0.03);
    padding: 24px;
  }

  .crud-header-card {
    background: #ffffff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  }

  .crud-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }

  .crud-search-input {
    height: 38px;
    padding: 8px 14px;
    font-size: 13px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    width: 280px;
    max-width: 100%;
  }

  .crud-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    background: #ffffff;
    border-radius: var(--radius-md);
    overflow: hidden;
    border: 1px solid var(--border-medium);
  }

  .crud-table th {
    background: #f8fafc;
    padding: 12px 16px;
    font-size: 10px;
    font-weight: 800;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 1px solid var(--border-medium);
    text-align: left;
  }

  .crud-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: var(--text-primary);
    vertical-align: middle;
  }

  .crud-table tr:hover td {
    background: rgba(99, 102, 241, 0.02);
  }

  .crud-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
  }

  .btn-action-edit {
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-sm);
    background: #fff;
    color: var(--accent-primary);
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-action-edit:hover {
    background: var(--accent-primary);
    color: #fff;
  }

  .btn-action-del {
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid #fecaca;
    border-radius: var(--radius-sm);
    background: #fff;
    color: var(--accent-rose);
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-action-del:hover {
    background: var(--accent-rose);
    color: #fff;
  }

  .admin-form-group {
    margin-bottom: 16px;
  }

  .admin-form-label {
    display: block;
    font-size: 10px;
    font-weight: 800;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 6px;
  }

  .admin-form-input {
    width: 100%;
    height: 40px;
    padding: 8px 14px;
    font-size: 13px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    box-sizing: border-box;
  }

  .admin-form-input:focus {
    outline: none;
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  }

  .toast-popup {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #0f172a;
    color: #ffffff;
    padding: 12px 20px;
    border-radius: var(--radius-md);
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    z-index: 99999;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    pointer-events: none;
  }

  .toast-popup.show {
    transform: translateY(0);
    opacity: 1;
    pointer-events: auto;
  }
</style>
@endpush

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">

    <!-- Page Title Header -->
    <div class="pms-page-header">
      <div class="pms-header-title-block">
        <div class="pms-status-indicator"></div>
        <div>
          <h1 class="pms-page-title">Enterprise System Administrator</h1>
          <div class="pms-page-subtitle">Configure Core Master Datasets, Front Office Channels, Room Masters & Entity Workflows</div>
        </div>
      </div>
      <div style="display: flex; gap: 10px;">
        <button class="btn-ui-secondary" onclick="exportDatabaseJSON()"><i class="fa-solid fa-download"></i> Export Master JSON</button>
      </div>
    </div>

    <!-- Admin Split Layout: Sidebar + Dynamic Content -->
    <div class="admin-layout">

      <!-- Include Sidebar -->
      @include('admin.includes.sidebar')

      <!-- Dynamic Content Pane -->
      <section class="admin-content-pane">

        <!-- Active CRUD View Container -->
        <div id="crud-view-container">
          <!-- Header Card -->
          <div class="crud-header-card">
            <div>
              <div style="display: flex; align-items: center; gap: 8px;">
                <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;" id="view-title">Mode of Reserve</h2>
                <span class="badge-tag blue" id="view-count">4 Records</span>
              </div>
              <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px;" id="view-desc">
                Manage reservation booking modes (Phone Call, Physical, Website, Online).
              </div>
            </div>
            <button class="btn-ui-primary" onclick="openCurrentAddModal()"><i class="fa-solid fa-plus"></i> <span id="btn-add-label">Add Entry</span></button>
          </div>

          <!-- Toolbar -->
          <div class="crud-toolbar">
            <div class="crud-toolbar-left">
              <div class="crud-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="table-search" class="crud-search-input" placeholder="Search active records..." oninput="handleSearch(this.value)">
              </div>
            </div>
            <div class="crud-toolbar-right">
              <button class="btn-ui-secondary" onclick="exportCurrentTableCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
              <button class="btn-ui-secondary" onclick="renderCurrentTable()"><i class="fa-solid fa-rotate"></i> Refresh</button>
            </div>
          </div>

          <!-- Table -->
          <div style="overflow-x: auto;">
            <table class="crud-table" id="main-crud-table">
              <!-- Dynamically rendered -->
            </table>
          </div>

          <!-- Pagination & Rows Controls -->
          <div class="pms-pagination-bar" id="table-pagination-bar">
            <div class="pms-pagination-info" id="pagination-info">
              Showing 0 to 0 of 0 records
            </div>
            <div class="pms-pagination-controls">
              <div class="pms-per-page-wrap">
                <span>Rows:</span>
                <select id="rows-per-page" class="pms-per-page-select" onchange="handlePageSizeChange(this.value)">
                  <option value="10" selected>10</option>
                  <option value="20">20</option>
                  <option value="100">100</option>
                  <option value="all">All</option>
                </select>
              </div>
              <div class="pms-page-nav" id="pagination-nav">
                <!-- Injected via JavaScript -->
              </div>
            </div>
          </div>
        </div>

        <!-- Pending / Under Review Container -->
        <div id="pending-view-container" style="display: none; text-align: center; padding: 60px 20px;">
          <div style="width: 70px; height: 70px; border-radius: 50%; background: #f8fafc; border: 1px solid var(--border-medium); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; font-size: 28px;">
            <i id="pending-icon" class="fa-solid fa-utensils" style="color: #10b981;"></i>
          </div>
          <h3 id="pending-title" style="font-size: 18px; font-weight: 800; color: var(--text-primary); margin: 0 0 8px 0;">POS Sales</h3>
          <div style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; background: rgba(245, 158, 11, 0.1); color: #b45309; margin-bottom: 14px;">
            <i class="fa-solid fa-clock"></i> Module Settings Queued for Client Review
          </div>
          <p style="font-size: 13px; color: var(--text-secondary); max-width: 480px; margin: 0 auto; line-height: 1.6;">
            This module's configuration parameters and workflow schemas will be integrated after discussion and sign-off with the client.
          </p>
        </div>

      </section>
    </div>

  </div>
</main>

<!-- UNIVERSAL CRUD MODAL -->
<div class="modal-backdrop" id="admin-crud-modal">
  <div class="modal-window" style="width: 580px; max-width: 95vw;">
    <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0;"><i class="fa-solid fa-pen-to-square"></i> <span id="modal-title">Add Record</span></h3>
      <button class="modal-close" onclick="closeModal('admin-crud-modal')">&times;</button>
    </div>
    <form onsubmit="handleSaveCrud(event)">
      <input type="hidden" id="crud-id">
      <input type="hidden" id="crud-entity">
      <div class="modal-content-area" id="modal-form-fields" style="padding: 20px; background: #f8fafc; max-height: 70vh; overflow-y: auto;">
        <!-- Dynamically generated fields based on active entity -->
      </div>
      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('admin-crud-modal')">Cancel</button>
        <button type="submit" class="btn-ui-primary"><i class="fa-solid fa-floppy-disk"></i> Save Entry</button>
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
      const isHidden = subList.style.display === 'none';
      subList.style.display = isHidden ? 'block' : 'none';
      const icon = headerElem.querySelector('.fa-chevron-down, .fa-chevron-right');
      if (icon) {
        icon.className = isHidden ? 'fa-solid fa-chevron-down' : 'fa-solid fa-chevron-right';
      }
    }
  }

  function switchSection(entityKey, title, clickedItem) {
    currentEntity = entityKey;
    currentEntityTitle = title;
    currentPage = 1;

    document.querySelectorAll('.admin-sub-item, .admin-nav-flat').forEach(el => el.classList.remove('active'));
    if (clickedItem) clickedItem.classList.add('active');

    document.getElementById('pending-view-container').style.display = 'none';
    document.getElementById('crud-view-container').style.display = 'block';

    document.getElementById('view-title').textContent = title;
    document.getElementById('view-desc').textContent = entityDescriptions[entityKey] || 'Configure and manage master records.';
    document.getElementById('btn-add-label').textContent = `Add Entry`;

    document.getElementById('table-search').value = '';
    searchQuery = '';

    renderCurrentTable();
  }

  function showPendingModule(moduleTitle, iconClass, iconColor, clickedItem) {
    document.querySelectorAll('.admin-sub-item, .admin-nav-flat').forEach(el => el.classList.remove('active'));
    if (clickedItem) clickedItem.classList.add('active');

    document.getElementById('crud-view-container').style.display = 'none';
    document.getElementById('pending-view-container').style.display = 'block';

    document.getElementById('pending-icon').className = `fa-solid ${iconClass}`;
    document.getElementById('pending-icon').style.color = iconColor;
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
      table.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);"><i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>No records found. Click "+ Add Entry" to create one.</td></tr>`;
      document.getElementById('pagination-info').textContent = 'Showing 0 to 0 of 0 records';
      renderPaginationNav(1);
      return;
    }

    let thead = '<thead><tr>';
    thead += '<th style="width: 50px;">#</th>';
    schema.forEach(col => {
      let thStyle = col.key === 'icon' ? 'style="width: 70px;"' : '';
      thead += `<th ${thStyle}>${col.label}</th>`;
    });
    thead += '<th style="text-align: right; width: 120px;">Actions</th>';
    thead += '</tr></thead>';

    let tbody = '<tbody>';
    if (filtered.length === 0) {
      tbody += `<tr><td colspan="${schema.length + 2}" style="text-align: center; padding: 30px; color: var(--text-muted);">No records match your search query "${searchQuery}".</td></tr>`;
    } else {
      pageItems.forEach((row, idx) => {
        const rowNumber = startIdx + idx + 1;
        tbody += '<tr>';
        tbody += `<td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">${rowNumber}</td>`;
        schema.forEach(col => {
          let val = row[col.key] || '-';
          let formattedVal = val;
          if (col.key === 'status') {
            let badgeClass = (val === 'Active' || val === 'Online' || val === 'Available') ? 'green' : 'yellow';
            formattedVal = `<span class="badge-tag ${badgeClass}"><i class="fa-solid fa-circle-check" style="font-size: 6px; margin-right: 4px;"></i>${val}</span>`;
          } else if (col.key === 'priority') {
            let badgeClass = (val === 'Immediate' || val === 'High') ? 'red' : 'blue';
            formattedVal = `<span class="badge-tag ${badgeClass}">${val}</span>`;
          } else if (col.key === 'icon') {
            formattedVal = `<div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(99, 102, 241, 0.08); display: flex; align-items: center; justify-content: center;"><i class="fa-solid ${val}" style="font-size: 15px; color: var(--accent-primary);"></i></div>`;
          }
          tbody += `<td>${formattedVal}</td>`;
        });
        tbody += `
          <td style="text-align: right;">
            <div class="crud-actions">
              <button class="btn-action-edit" onclick="openEditModal('${row.id}')" title="Edit Record"><i class="fa-solid fa-pen"></i></button>
              <button class="btn-action-del" onclick="deleteCrudItem('${row.id}')" title="Delete Record"><i class="fa-solid fa-trash"></i></button>
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
      infoEl.innerHTML = `Showing <strong style="color: var(--text-primary);">${startIdx + 1}</strong> to <strong style="color: var(--text-primary);">${endIdx}</strong> of <strong style="color: var(--text-primary);">${totalCount}</strong> records${totalCount !== records.length ? ' (filtered from ' + records.length + ' total)' : ''}`;
    }

    renderPaginationNav(totalPages);
  }

  function renderPaginationNav(totalPages) {
    const nav = document.getElementById('pagination-nav');
    if (!nav) return;

    if (totalPages <= 1) {
      nav.innerHTML = `
        <button class="pms-page-btn" disabled><i class="fa-solid fa-chevron-left"></i></button>
        <button class="pms-page-btn active">1</button>
        <button class="pms-page-btn" disabled><i class="fa-solid fa-chevron-right"></i></button>
      `;
      return;
    }

    let html = '';
    html += `<button class="pms-page-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} title="Previous Page"><i class="fa-solid fa-chevron-left"></i></button>`;

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
        html += `<span class="pms-page-dots">...</span>`;
      } else {
        html += `<button class="pms-page-btn ${page === currentPage ? 'active' : ''}" onclick="goToPage(${page})">${page}</button>`;
      }
    });

    html += `<button class="pms-page-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} title="Next Page"><i class="fa-solid fa-chevron-right"></i></button>`;

    nav.innerHTML = html;
  }

  function openCurrentAddModal() {
    const schema = entitySchemas[currentEntity] || [];
    document.getElementById('modal-title').textContent = `Add New ${currentEntityTitle}`;
    document.getElementById('crud-entity').value = currentEntity;
    document.getElementById('crud-id').value = '';

    let formHtml = '';
    schema.forEach(field => {
      formHtml += `<div class="admin-form-group">`;
      formHtml += `<label class="admin-form-label">${field.label}</label>`;
      if (field.type === 'select') {
        formHtml += `<select id="f_${field.key}" class="admin-form-input">`;
        field.options.forEach(opt => {
          formHtml += `<option value="${opt}">${opt}</option>`;
        });
        formHtml += `</select>`;
      } else {
        formHtml += `<input type="${field.type || 'text'}" id="f_${field.key}" class="admin-form-input" placeholder="${field.placeholder || ''}" required>`;
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
      formHtml += `<div class="admin-form-group">`;
      formHtml += `<label class="admin-form-label">${field.label}</label>`;
      if (field.type === 'select') {
        formHtml += `<select id="f_${field.key}" class="admin-form-input">`;
        field.options.forEach(opt => {
          const isSel = (String(record[field.key]) === String(opt)) ? 'selected' : '';
          formHtml += `<option value="${opt}" ${isSel}>${opt}</option>`;
        });
        formHtml += `</select>`;
      } else {
        const val = record[field.key] || '';
        formHtml += `<input type="${field.type || 'text'}" id="f_${field.key}" class="admin-form-input" value="${val}" required>`;
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
        PmsAlert.toast(`Record updated successfully in ${currentEntityTitle}!`);
      }
    } else {
      records.unshift(newRecord);
      PmsAlert.toast(`New record created in ${currentEntityTitle}!`);
    }

    closeModal('admin-crud-modal');
    renderCurrentTable();
  }

  function deleteCrudItem(id) {
    const records = adminDB[currentEntity] || [];
    const record = records.find(r => String(r.id) === String(id));
    if (!record) return;

    PmsAlert.confirmDelete(`Delete Entry?`, `Are you sure you want to delete this entry from ${currentEntityTitle}?`).then(result => {
      if (result.isConfirmed) {
        adminDB[currentEntity] = records.filter(r => String(r.id) !== String(id));
        PmsAlert.toast(`Record deleted from ${currentEntityTitle}.`);
        renderCurrentTable();
      }
    });
  }

  function exportCurrentTableCSV() {
    const records = adminDB[currentEntity] || [];
    const schema = entitySchemas[currentEntity] || [];
    if (records.length === 0) {
      PmsAlert.toast('No records available to export.', 'info');
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
    PmsAlert.toast(`Exported ${currentEntityTitle} as CSV!`);
  }

  function exportDatabaseJSON() {
    const blob = new Blob([JSON.stringify(adminDB, null, 2)], { type: 'application/json' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('href', url);
    a.setAttribute('download', 'Hotel_Sagar_Sonnet_Administrator_Master_DB.json');
    a.click();
    PmsAlert.toast('Master Database exported as JSON!');
  }

  function openModal(id) {
    const m = document.getElementById(id);
    if (m) {
      m.classList.add('open');
      m.classList.add('active');
      m.classList.add('show');
      m.style.display = 'flex';
    }
  }

  function closeModal(id) {
    const m = document.getElementById(id);
    if (m) {
      m.classList.remove('open');
      m.classList.remove('active');
      m.classList.remove('show');
      m.style.display = 'none';
    }
  }

  window.addEventListener('DOMContentLoaded', () => {
    renderCurrentTable();
  });
</script>
@endpush
