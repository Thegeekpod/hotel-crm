@extends('admin.layouts.app')

@section('title', 'Room Maintenance - Hotel Sagar Sonnet PMS')

@push('styles')
<style>
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
  .admin-nav-group { margin-bottom: 8px; }
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
  .admin-nav-header:hover { background: #f1f5f9; color: var(--accent-primary); }
  .admin-nav-header.active {
    color: var(--accent-primary);
    font-weight: 900;
    background: rgba(99, 102, 241, 0.08);
    border-color: rgba(99, 102, 241, 0.2);
  }
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
  .admin-nav-flat:hover { background: #f8fafc; color: var(--accent-primary); }
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
    text-decoration: none;
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
    flex-wrap: wrap;
  }
  .crud-toolbar-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .crud-search-input {
    height: 38px;
    padding: 8px 14px 8px 34px;
    font-size: 13px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    width: 260px;
    max-width: 100%;
  }
  .crud-filter-select {
    height: 38px;
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
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
    padding: 12px 14px;
    font-size: 10px;
    font-weight: 800;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 1px solid var(--border-medium);
    text-align: left;
  }
  .crud-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    color: var(--text-primary);
    vertical-align: middle;
  }
  .crud-table tr:hover td { background: rgba(99, 102, 241, 0.02); }
  .crud-actions { display: flex; gap: 6px; justify-content: flex-end; }
  .btn-action-view {
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-sm);
    background: #fff;
    color: #0284c7;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-action-view:hover { background: #0284c7; color: #fff; }
  .btn-action-edit {
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-sm);
    background: #fff;
    color: var(--accent-primary);
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-action-edit:hover { background: var(--accent-primary); color: #fff; }
  .btn-action-del {
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 700;
    border: 1px solid #fecaca;
    border-radius: var(--radius-sm);
    background: #fff;
    color: var(--accent-rose);
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-action-del:hover { background: var(--accent-rose); color: #fff; }

  .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.4px;
  }
  .status-pill.active {
    background: #fef2f2;
    color: #ef4444;
    border: 1px solid #fca5a5;
  }
  .status-pill.checking {
    background: #fffbeb;
    color: #f59e0b;
    border: 1px solid #fcd34d;
  }
  .status-pill.completed {
    background: #ecfdf5;
    color: #10b981;
    border: 1px solid #6ee7b7;
  }

  .stats-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
  }
  .stats-summary-card {
    background: #fff;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
  }
  .stats-summary-card .val {
    font-size: 20px;
    font-weight: 900;
    color: var(--text-primary);
  }
  .stats-summary-card .lbl {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
  }

  .admin-form-group { margin-bottom: 14px; }
  .admin-form-label {
    display: block;
    font-size: 11px;
    font-weight: 800;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 5px;
  }
  .admin-form-input {
    width: 100%;
    height: 38px;
    padding: 8px 12px;
    font-size: 13px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    box-sizing: border-box;
  }
  .admin-form-textarea {
    width: 100%;
    min-height: 80px;
    padding: 10px 12px;
    font-size: 13px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    box-sizing: border-box;
    font-family: inherit;
    resize: vertical;
  }
</style>
@endpush

@section('content')
<main class="pms-main-viewport">
  <div class="pms-page-container">

    <div class="pms-page-header">
      <div class="pms-header-title-block">
        <div class="pms-status-indicator"></div>
        <div>
          <h1 class="pms-page-title">Administrator • Room Maintenance Management</h1>
          <div class="pms-page-subtitle">Track, assign, inspect, and update room maintenance work orders and engineer logs.</div>
        </div>
      </div>
      <div style="display: flex; gap: 10px;">
        <button class="btn-ui-secondary" onclick="exportDataCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
      </div>
    </div>

    <div class="admin-layout">
      @include('admin.includes.sidebar')

      <section class="admin-content-pane">
        <!-- Quick KPI Cards -->
        <div class="stats-summary-grid">
          <div class="stats-summary-card">
            <div>
              <div class="lbl">Total Records</div>
              <div class="val" id="kpi-total">{{ count($items) }}</div>
            </div>
            <i class="fa-solid fa-list-check" style="font-size: 24px; color: #6366f1; opacity: 0.8;"></i>
          </div>
          <div class="stats-summary-card" style="border-left: 4px solid #ef4444;">
            <div>
              <div class="lbl">Active Work</div>
              <div class="val" style="color: #ef4444;" id="kpi-active">{{ $activeCount ?? 0 }}</div>
            </div>
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 24px; color: #ef4444; opacity: 0.8;"></i>
          </div>
          <div class="stats-summary-card" style="border-left: 4px solid #f59e0b;">
            <div>
              <div class="lbl">Checking / Testing</div>
              <div class="val" style="color: #f59e0b;" id="kpi-checking">{{ $checkingCount ?? 0 }}</div>
            </div>
            <i class="fa-solid fa-spinner" style="font-size: 24px; color: #f59e0b; opacity: 0.8;"></i>
          </div>
          <div class="stats-summary-card" style="border-left: 4px solid #10b981;">
            <div>
              <div class="lbl">Completed</div>
              <div class="val" style="color: #10b981;" id="kpi-completed">{{ $completedCount ?? 0 }}</div>
            </div>
            <i class="fa-solid fa-circle-check" style="font-size: 24px; color: #10b981; opacity: 0.8;"></i>
          </div>
        </div>

        <div id="crud-view-container">
          <div class="crud-header-card">
            <div>
              <div style="display: flex; align-items: center; gap: 8px;">
                <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">Room Maintenance Tasks</h2>
                <span class="badge-tag blue" id="view-count">{{ count($items) }} Logs</span>
              </div>
              <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px;">
                Manage work descriptions, assigned engineers, completion schedules, and resolution notes.
              </div>
            </div>
            <button class="btn-ui-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Schedule Maintenance</button>
          </div>

          <div class="crud-toolbar">
            <div class="crud-toolbar-left">
              <div class="crud-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="table-search" class="crud-search-input" placeholder="Search room, engineer, reason..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
              </div>
              <select id="status-filter" class="crud-filter-select" onchange="handleStatusFilter(this.value)">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Checking" {{ request('status') === 'Checking' ? 'selected' : '' }}>Checking</option>
                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
              </select>
              <select id="floor-filter" class="crud-filter-select" onchange="handleFloorFilter(this.value)">
                <option value="all">All Floors</option>
                @foreach($floors as $fl)
                  <option value="{{ $fl->floor }}">Floor {{ $fl->floor }}</option>
                @endforeach
              </select>
            </div>
            <div class="crud-toolbar-right">
              <button class="btn-ui-danger" id="btn-bulk-delete" style="display: none; height: 38px; padding: 0 16px; border-radius: var(--radius-md); font-weight: 700; background: #ef4444; color: #fff; border: none; align-items: center; gap: 6px; cursor: pointer;" onclick="handleBulkDelete()"><i class="fa-solid fa-trash-can"></i> Delete Selected (<span id="bulk-selected-count">0</span>)</button>
              <button class="btn-ui-secondary" onclick="exportDataCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
              <button class="btn-ui-secondary" onclick="loadTableData()"><i class="fa-solid fa-rotate"></i> Refresh</button>
            </div>
          </div>

          <div style="overflow-x: auto;">
            <table class="crud-table" id="crud-table">
              <thead>
                <tr>
                  <th style="width: 40px; text-align: center;"><input type="checkbox" id="select-all-check" onchange="toggleSelectAll(this)" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);"></th>
                  <th style="width: 50px;">#</th>
                  <th>Room Details</th>
                  <th>Reason / Work Description</th>
                  <th>Assigned Personnel / Team</th>
                  <th>Expected Completion</th>
                  <th>Status</th>
                  <th style="text-align: right; width: 130px;">Actions</th>
                </tr>
              </thead>
              <tbody id="table-body">
                @forelse($items as $idx => $item)
                <tr data-id="{{ $item->id }}">
                  <td style="text-align: center;">
                    <input type="checkbox" class="row-select-check" value="{{ $item->id }}" onchange="handleRowSelect(this, '{{ $item->id }}')" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
                  </td>
                  <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">{{ $idx + 1 }}</td>
                  <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-weight: 800; font-size: 12px; background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">
                        #{{ $item->room->room_number ?? $item->room_id }}
                      </span>
                      <div>
                        <div style="font-weight: 700; font-size: 12px; color: var(--text-primary);">{{ $item->room->category ?? 'Standard Room' }}</div>
                        <div style="font-size: 11px; color: var(--text-secondary);">Floor {{ $item->room->floor ?? '-' }}</div>
                      </div>
                    </div>
                  </td>
                  <td style="font-weight: 600; color: var(--text-primary); max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->reason }}">
                    {{ $item->reason ?? 'Maintenance Check' }}
                  </td>
                  <td>
                    <span style="font-size: 12px; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 6px;">
                      <i class="fa-solid fa-user-gear" style="color: #64748b;"></i> {{ $item->assign ?: 'Not Assigned' }}
                    </span>
                  </td>
                  <td>
                    <span style="font-size: 12px; font-weight: 600; color: var(--text-secondary);">
                      <i class="fa-regular fa-clock" style="margin-right: 4px; color: #94a3b8;"></i>
                      {{ $item->expected_date_time ? \Carbon\Carbon::parse($item->expected_date_time)->format('d M Y, h:i A') : '-' }}
                    </span>
                  </td>
                  <td>
                    @php
                      $st = strtolower($item->status ?? 'active');
                      $pillClass = $st === 'completed' ? 'completed' : ($st === 'checking' ? 'checking' : 'active');
                    @endphp
                    <span class="status-pill {{ $pillClass }}">
                      <i class="fa-solid {{ $st === 'completed' ? 'fa-circle-check' : ($st === 'checking' ? 'fa-spinner' : 'fa-triangle-exclamation') }}"></i>
                      {{ $item->status ?? 'Active' }}
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="crud-actions">
                      <button class="btn-action-view" onclick="openViewModal({{ $item->id }})" title="View Details & Note"><i class="fa-solid fa-eye"></i></button>
                      <button class="btn-action-edit" onclick="openEditModal({{ $item->id }})" title="Edit Status / Assignment"><i class="fa-solid fa-pen"></i></button>
                      <button class="btn-action-del" onclick="deleteItem({{ $item->id }})" title="Delete Task"><i class="fa-solid fa-trash"></i></button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
                    No room maintenance logs found. Click "+ Schedule Maintenance" to create one.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination Bar -->
          <div class="pms-pagination-bar" id="table-pagination-bar">
            <div class="pms-pagination-info" id="pagination-info">
              Showing 1 to {{ count($items) }} of {{ count($items) }} records
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
              <div class="pms-page-nav" id="pagination-nav"></div>
            </div>
          </div>
        </div>
      </section>
    </div>

  </div>
</main>

<!-- ADD MAINTENANCE MODAL -->
<div class="modal-backdrop" id="add-modal">
  <div class="modal-window" style="width: 560px; max-width: 95vw;">
    <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0;"><i class="fa-solid fa-screwdriver-wrench"></i> <span>Schedule Room Maintenance</span></h3>
      <button class="modal-close" onclick="closeModal('add-modal')">&times;</button>
    </div>
    <form id="add-form" onsubmit="handleAddSubmit(event)">
      <div class="modal-content-area" style="padding: 20px; background: #f8fafc;">
        <div class="admin-form-group">
          <label class="admin-form-label">Select Room <span style="color: var(--accent-rose);">*</span></label>
          <select id="add-room-id" class="admin-form-input" required>
            <option value="">-- Choose Room --</option>
            @foreach($rooms as $r)
              <option value="{{ $r->id }}">Room #{{ $r->room_number }} (Floor {{ $r->floor }} • {{ $r->category }})</option>
            @endforeach
          </select>
        </div>
        <div class="admin-form-group">
          <label class="admin-form-label">Maintenance Reason / Work Description <span style="color: var(--accent-rose);">*</span></label>
          <input type="text" id="add-reason" class="admin-form-input" placeholder="e.g. AC Repair, Plumbing Leak, Painting" required>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="admin-form-group">
            <label class="admin-form-label">Assigned Personnel / Team</label>
            <input type="text" id="add-assign" class="admin-form-input" placeholder="e.g. John Doe, MEP Team">
          </div>
          <div class="admin-form-group">
            <label class="admin-form-label">Status <span style="color: var(--accent-rose);">*</span></label>
            <select id="add-status" class="admin-form-input" required>
              <option value="Active" selected>Active (Under Maintenance)</option>
              <option value="Checking">Checking (Testing)</option>
              <option value="Completed">Completed (Normal)</option>
            </select>
          </div>
        </div>
        <div class="admin-form-group">
          <label class="admin-form-label">Expected Completion Date & Time</label>
          <input type="datetime-local" id="add-expected" class="admin-form-input">
        </div>
        <div class="admin-form-group" style="margin-bottom: 0;">
          <label class="admin-form-label">Work Note / Instructions</label>
          <textarea id="add-note" class="admin-form-textarea" placeholder="Add any specific instructions, spare parts needed, or inspection details..."></textarea>
        </div>
      </div>
      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('add-modal')">Cancel</button>
        <button type="submit" class="btn-ui-primary" id="btn-add-save"><i class="fa-solid fa-floppy-disk"></i> Schedule Task</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT STATUS & MAINTENANCE MODAL -->
<div class="modal-backdrop" id="edit-modal">
  <div class="modal-window" style="width: 560px; max-width: 95vw;">
    <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0;"><i class="fa-solid fa-pen-to-square"></i> <span>Edit Maintenance Task</span></h3>
      <button class="modal-close" onclick="closeModal('edit-modal')">&times;</button>
    </div>
    <form id="edit-form" onsubmit="handleEditSubmit(event)">
      <input type="hidden" id="edit-id">
      <div class="modal-content-area" style="padding: 20px; background: #f8fafc;">
        <div class="admin-form-group">
          <label class="admin-form-label">Room</label>
          <input type="text" id="edit-room-display" class="admin-form-input" style="background: #e2e8f0; font-weight: 700;" readonly>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="admin-form-group">
            <label class="admin-form-label">Maintenance Status <span style="color: var(--accent-rose);">*</span></label>
            <select id="edit-status" class="admin-form-input" required>
              <option value="Active">Active (Under Maintenance)</option>
              <option value="Checking">Checking (Testing)</option>
              <option value="Completed">Completed (Room Normal Mode)</option>
            </select>
          </div>
          <div class="admin-form-group">
            <label class="admin-form-label">Assigned Personnel / Team</label>
            <input type="text" id="edit-assign" class="admin-form-input" placeholder="e.g. John Doe, MEP Team">
          </div>
        </div>
        <div class="admin-form-group">
          <label class="admin-form-label">Work Description / Reason <span style="color: var(--accent-rose);">*</span></label>
          <input type="text" id="edit-reason" class="admin-form-input" required>
        </div>
        <div class="admin-form-group">
          <label class="admin-form-label">Expected Completion Date & Time</label>
          <input type="datetime-local" id="edit-expected" class="admin-form-input">
        </div>
        <div class="admin-form-group" style="margin-bottom: 0;">
          <label class="admin-form-label">Work Note / Resolution Details</label>
          <textarea id="edit-note" class="admin-form-textarea" placeholder="Enter notes or completion summary..."></textarea>
        </div>
      </div>
      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('edit-modal')">Cancel</button>
        <button type="submit" class="btn-ui-primary" id="btn-edit-save"><i class="fa-solid fa-floppy-disk"></i> Update Task</button>
      </div>
    </form>
  </div>
</div>

<!-- VIEW DETAILS MODAL -->
<div class="modal-backdrop" id="view-modal">
  <div class="modal-window" style="width: 580px; max-width: 95vw;">
    <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0;"><i class="fa-solid fa-file-lines"></i> <span id="view-modal-title">Maintenance Task Details</span></h3>
      <button class="modal-close" onclick="closeModal('view-modal')">&times;</button>
    </div>
    <div class="modal-content-area" style="padding: 24px; background: #f8fafc;">
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
        <div style="background: #fff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 14px;">
          <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Room Information</div>
          <div style="font-size: 16px; font-weight: 800; color: var(--accent-primary); margin-top: 4px;" id="view-room-num">-</div>
          <div style="font-size: 12px; color: var(--text-secondary);" id="view-room-details">-</div>
        </div>
        <div style="background: #fff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 14px;">
          <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Task Status</div>
          <div style="margin-top: 6px;" id="view-status-badge">-</div>
          <div style="font-size: 11px; color: var(--text-secondary); margin-top: 4px;" id="view-created-at">-</div>
        </div>
      </div>

      <div style="background: #fff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 14px; margin-bottom: 14px;">
        <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Reason / Work Description</div>
        <div style="font-size: 13px; font-weight: 700; color: var(--text-primary);" id="view-reason">-</div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
        <div style="background: #fff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 14px;">
          <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Assigned Personnel</div>
          <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-top: 4px;" id="view-assign">-</div>
        </div>
        <div style="background: #fff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 14px;">
          <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Expected Completion</div>
          <div style="font-size: 13px; font-weight: 700; color: var(--text-primary); margin-top: 4px;" id="view-expected">-</div>
        </div>
      </div>

      <div style="background: #fff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 14px;">
        <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px;">Detailed Work Note / History</div>
        <div style="font-size: 13px; color: var(--text-primary); line-height: 1.5; white-space: pre-wrap; max-height: 160px; overflow-y: auto;" id="view-note">-</div>
      </div>
    </div>
    <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: space-between; align-items: center;">
      <button type="button" class="btn-ui-danger" id="view-btn-delete" style="font-size: 12px; padding: 6px 12px;"><i class="fa-solid fa-trash"></i> Delete</button>
      <div style="display: flex; gap: 8px;">
        <button type="button" class="btn-ui-primary" id="view-btn-edit" style="font-size: 12px; padding: 6px 12px;"><i class="fa-solid fa-pen"></i> Edit</button>
        <button type="button" class="btn-ui-secondary" onclick="closeModal('view-modal')" style="font-size: 12px; padding: 6px 12px;">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const urlParams = new URLSearchParams(window.location.search);
  let searchQuery = urlParams.get('search') || "{{ request('search') }}" || '';
  let statusFilter = urlParams.get('status') || "{{ request('status', 'all') }}" || 'all';
  let floorFilter = urlParams.get('floor') || "{{ request('floor', 'all') }}" || 'all';
  let currentPage = parseInt(urlParams.get('page') || '1', 10);
  let pageSize = urlParams.get('per_page') || "{{ request('per_page', '10') }}" || '10';
  let tableRecords = @json($items);

  const baseUrl = "{{ url('admin/room-maintain') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function updateUrlParams() {
    const params = new URLSearchParams();
    if (searchQuery) params.set('search', searchQuery);
    if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);
    if (floorFilter && floorFilter !== 'all') params.set('floor', floorFilter);
    if (currentPage > 1) params.set('page', currentPage);
    if (pageSize !== '10') params.set('per_page', pageSize);

    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState(null, '', newUrl);
  }

  function openAddModal() {
    document.getElementById('add-room-id').value = '';
    document.getElementById('add-reason').value = '';
    document.getElementById('add-assign').value = '';
    document.getElementById('add-status').value = 'Active';
    document.getElementById('add-expected').value = '';
    document.getElementById('add-note').value = '';
    openModal('add-modal');
  }

  function openEditModal(id) {
    const item = tableRecords.find(r => String(r.id) === String(id));
    if (!item) return;

    document.getElementById('edit-id').value = item.id;
    const roomNum = item.room ? item.room.room_number : item.room_id;
    const roomCat = item.room ? item.room.category : '';
    const roomFl = item.room ? `Floor ${item.room.floor}` : '';
    document.getElementById('edit-room-display').value = `Room #${roomNum} (${roomFl} • ${roomCat})`;
    document.getElementById('edit-reason').value = item.reason || '';
    document.getElementById('edit-assign').value = item.assign || '';
    document.getElementById('edit-status').value = item.status || 'Active';
    document.getElementById('edit-note').value = item.note || '';

    if (item.expected_date_time) {
      const d = new Date(item.expected_date_time);
      if (!isNaN(d.getTime())) {
        const iso = new Date(d.getTime() - d.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
        document.getElementById('edit-expected').value = iso;
      } else {
        document.getElementById('edit-expected').value = '';
      }
    } else {
      document.getElementById('edit-expected').value = '';
    }

    openModal('edit-modal');
  }

  function openViewModal(id) {
    const item = tableRecords.find(r => String(r.id) === String(id));
    if (!item) return;

    const roomNum = item.room ? `#${item.room.room_number}` : `#${item.room_id}`;
    const roomDetails = item.room ? `Floor ${item.room.floor} • ${item.room.category}` : '-';

    document.getElementById('view-room-num').textContent = roomNum;
    document.getElementById('view-room-details').textContent = roomDetails;
    document.getElementById('view-reason').textContent = item.reason || 'General Maintenance';
    document.getElementById('view-assign').textContent = item.assign || 'Unassigned';
    document.getElementById('view-expected').textContent = item.expected_date_time ? new Date(item.expected_date_time).toLocaleString() : 'Not Set';
    document.getElementById('view-note').textContent = item.note || 'No additional note recorded.';
    document.getElementById('view-created-at').textContent = 'Logged: ' + (item.created_at ? new Date(item.created_at).toLocaleDateString() : '-');

    const st = (item.status || 'Active').toLowerCase();
    const pillClass = st === 'completed' ? 'completed' : (st === 'checking' ? 'checking' : 'active');
    const iconClass = st === 'completed' ? 'fa-circle-check' : (st === 'checking' ? 'fa-spinner' : 'fa-triangle-exclamation');
    document.getElementById('view-status-badge').innerHTML = `
      <span class="status-pill ${pillClass}">
        <i class="fa-solid ${iconClass}"></i> ${escapeHtml(item.status || 'Active')}
      </span>
    `;

    document.getElementById('view-btn-delete').onclick = () => {
      closeModal('view-modal');
      deleteItem(item.id);
    };

    document.getElementById('view-btn-edit').onclick = () => {
      closeModal('view-modal');
      openEditModal(item.id);
    };

    openModal('view-modal');
  }

  async function handleAddSubmit(e) {
    e.preventDefault();
    const roomId = document.getElementById('add-room-id').value;
    const reason = document.getElementById('add-reason').value.trim();
    const assign = document.getElementById('add-assign').value.trim();
    const status = document.getElementById('add-status').value;
    const expected = document.getElementById('add-expected').value;
    const note = document.getElementById('add-note').value.trim();

    if (!roomId) {
      PmsAlert.error('Validation Error', 'Please select a room.');
      return;
    }
    if (!reason) {
      PmsAlert.error('Validation Error', 'Please provide a maintenance reason.');
      return;
    }

    const payload = {
      room_id: roomId,
      reason,
      assign,
      status,
      expected_date_time: expected || null,
      note
    };

    try {
      const res = await fetch(baseUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (res.ok && data.success) {
        closeModal('add-modal');
        PmsAlert.toast(data.message || 'Maintenance scheduled successfully!');
        await loadTableData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to schedule maintenance.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  async function handleEditSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('edit-id').value;
    const reason = document.getElementById('edit-reason').value.trim();
    const assign = document.getElementById('edit-assign').value.trim();
    const status = document.getElementById('edit-status').value;
    const expected = document.getElementById('edit-expected').value;
    const note = document.getElementById('edit-note').value.trim();

    if (!reason) {
      PmsAlert.error('Validation Error', 'Please provide a maintenance reason.');
      return;
    }

    const payload = {
      reason,
      assign,
      status,
      expected_date_time: expected || null,
      note
    };

    try {
      const res = await fetch(`${baseUrl}/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (res.ok && data.success) {
        closeModal('edit-modal');
        PmsAlert.toast(data.message || 'Maintenance record updated successfully!');
        await loadTableData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to update record.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  async function deleteItem(id) {
    const result = await PmsAlert.confirmDelete('Delete Maintenance Record?', 'This maintenance entry will be removed permanently.');
    if (result.isConfirmed) {
      try {
        const res = await fetch(`${baseUrl}/${id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          }
        });
        const data = await res.json();
        if (res.ok && data.success) {
          PmsAlert.toast('Record deleted successfully!');
          await loadTableData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete record.');
        }
      } catch (err) {
        PmsAlert.error('Server Error', 'An error occurred during deletion.');
      }
    }
  }

  async function loadTableData() {
    updateUrlParams();
    try {
      const params = new URLSearchParams();
      if (searchQuery) params.set('search', searchQuery);
      if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);
      if (floorFilter && floorFilter !== 'all') params.set('floor', floorFilter);

      const res = await fetch(`${baseUrl}?${params.toString()}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success) {
        tableRecords = data.data;
        updateKpis();
        renderTable();
      }
    } catch (err) {
      console.error(err);
    }
  }

  function updateKpis() {
    const total = tableRecords.length;
    const active = tableRecords.filter(r => (r.status || '').toLowerCase() === 'active').length;
    const checking = tableRecords.filter(r => (r.status || '').toLowerCase() === 'checking').length;
    const completed = tableRecords.filter(r => (r.status || '').toLowerCase() === 'completed').length;

    const tEl = document.getElementById('kpi-total');
    const aEl = document.getElementById('kpi-active');
    const cEl = document.getElementById('kpi-checking');
    const cpEl = document.getElementById('kpi-completed');

    if (tEl) tEl.textContent = total;
    if (aEl) aEl.textContent = active;
    if (cEl) cEl.textContent = checking;
    if (cpEl) cpEl.textContent = completed;
  }

  let searchDebounceTimer = null;
  function handleSearch(val) {
    searchQuery = (val || '').toLowerCase().trim();
    currentPage = 1;
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(() => {
      loadTableData();
    }, 250);
  }

  function handleStatusFilter(val) {
    statusFilter = val;
    currentPage = 1;
    loadTableData();
  }

  function handleFloorFilter(val) {
    floorFilter = val;
    currentPage = 1;
    loadTableData();
  }

  function handlePageSizeChange(val) {
    pageSize = val === 'all' ? 'all' : parseInt(val, 10);
    currentPage = 1;
    updateUrlParams();
    renderTable();
  }

  function goToPage(page) {
    currentPage = page;
    updateUrlParams();
    renderTable();
  }

  let selectedIds = new Set();

  function updateBulkActionUI() {
    const countEl = document.getElementById('bulk-selected-count');
    const btn = document.getElementById('btn-bulk-delete');
    const selectAll = document.getElementById('select-all-check');

    if (countEl) countEl.textContent = selectedIds.size;
    if (btn) {
      btn.style.display = selectedIds.size > 0 ? 'inline-flex' : 'none';
    }

    const currentVisible = getFilteredRecords();
    const allVisibleSelected = currentVisible.length > 0 && currentVisible.every(r => selectedIds.has(String(r.id)));
    if (selectAll) {
      selectAll.checked = allVisibleSelected;
      selectAll.indeterminate = selectedIds.size > 0 && !allVisibleSelected;
    }
  }

  function handleRowSelect(checkbox, id) {
    if (checkbox.checked) {
      selectedIds.add(String(id));
    } else {
      selectedIds.delete(String(id));
    }
    updateBulkActionUI();
  }

  function toggleSelectAll(masterCheckbox) {
    const visible = getFilteredRecords();
    if (masterCheckbox.checked) {
      visible.forEach(r => selectedIds.add(String(r.id)));
    } else {
      visible.forEach(r => selectedIds.delete(String(r.id)));
    }
    renderTable();
  }

  async function handleBulkDelete() {
    if (selectedIds.size === 0) return;
    const count = selectedIds.size;
    const result = await PmsAlert.confirmDelete(`Delete ${count} Selected Maintenance Records?`, 'All selected records will be permanently removed.');
    if (result && (result.isConfirmed || result === true)) {
      try {
        const res = await fetch(`${baseUrl}/bulk-delete`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ ids: Array.from(selectedIds) })
        });
        const data = await res.json().catch(() => ({}));
        if (res.ok && data.success !== false) {
          PmsAlert.toast(data.message || `${count} record(s) deleted successfully!`);
          selectedIds.clear();
          await loadTableData();
        } else {
          PmsAlert.error('Bulk Delete Failed', data.message || 'Could not delete selected records.');
          await loadTableData();
        }
      } catch (err) {
        console.error(err);
        PmsAlert.error('Server Error', 'Failed to communicate with the server.');
      }
    }
  }

  function getFilteredRecords() {
    return tableRecords.filter(item => {
      const roomNum = item.room ? String(item.room.room_number) : String(item.room_id);
      const roomCat = item.room ? item.room.category : '';
      const roomFl = item.room ? String(item.room.floor) : '';

      const matchSearch = !searchQuery ||
        roomNum.toLowerCase().includes(searchQuery) ||
        roomCat.toLowerCase().includes(searchQuery) ||
        roomFl.toLowerCase().includes(searchQuery) ||
        (item.reason && item.reason.toLowerCase().includes(searchQuery)) ||
        (item.assign && item.assign.toLowerCase().includes(searchQuery)) ||
        (item.note && item.note.toLowerCase().includes(searchQuery)) ||
        (item.status && item.status.toLowerCase().includes(searchQuery));

      const matchStatus = statusFilter === 'all' || item.status === statusFilter;
      const matchFloor = floorFilter === 'all' || roomFl === floorFilter;

      return matchSearch && matchStatus && matchFloor;
    });
  }

  function renderTable() {
    const filtered = getFilteredRecords();
    const totalCount = filtered.length;
    const totalPages = pageSize === 'all' ? 1 : Math.max(1, Math.ceil(totalCount / pageSize));

    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;

    const startIdx = pageSize === 'all' ? 0 : (currentPage - 1) * pageSize;
    const endIdx = pageSize === 'all' ? totalCount : Math.min(startIdx + pageSize, totalCount);
    const pageItems = filtered.slice(startIdx, endIdx);

    const viewCountEl = document.getElementById('view-count');
    if (viewCountEl) {
      viewCountEl.textContent = `${tableRecords.length} Log${tableRecords.length === 1 ? '' : 's'}`;
    }

    const tbody = document.getElementById('table-body');
    if (!pageItems.length) {
      tbody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);"><i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>No matching maintenance records found.</td></tr>`;
      updateBulkActionUI();
    } else {
      let html = '';
      pageItems.forEach((item, idx) => {
        const rowNumber = startIdx + idx + 1;
        const roomNum = item.room ? item.room.room_number : item.room_id;
        const roomCat = item.room ? item.room.category : 'Standard';
        const roomFl = item.room ? item.room.floor : '-';

        const st = (item.status || 'Active').toLowerCase();
        const pillClass = st === 'completed' ? 'completed' : (st === 'checking' ? 'checking' : 'active');
        const iconClass = st === 'completed' ? 'fa-circle-check' : (st === 'checking' ? 'fa-spinner' : 'fa-triangle-exclamation');

        let expText = '-';
        if (item.expected_date_time) {
          const d = new Date(item.expected_date_time);
          if (!isNaN(d.getTime())) {
            expText = d.toLocaleString('en-US', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
          }
        }

        html += `
          <tr data-id="${item.id}">
            <td style="text-align: center;">
              <input type="checkbox" class="row-select-check" value="${item.id}" ${selectedIds.has(String(item.id)) ? 'checked' : ''} onchange="handleRowSelect(this, '${item.id}')" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
            </td>
            <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">${rowNumber}</td>
            <td>
              <div style="display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-weight: 800; font-size: 12px; background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">
                  #${escapeHtml(roomNum)}
                </span>
                <div>
                  <div style="font-weight: 700; font-size: 12px; color: var(--text-primary);">${escapeHtml(roomCat)}</div>
                  <div style="font-size: 11px; color: var(--text-secondary);">Floor ${escapeHtml(roomFl)}</div>
                </div>
              </div>
            </td>
            <td style="font-weight: 600; color: var(--text-primary); max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${escapeHtml(item.reason || '')}">
              ${escapeHtml(item.reason || 'Maintenance Check')}
            </td>
            <td>
              <span style="font-size: 12px; font-weight: 600; color: var(--text-secondary); display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-user-gear" style="color: #64748b;"></i> ${escapeHtml(item.assign || 'Not Assigned')}
              </span>
            </td>
            <td>
              <span style="font-size: 12px; font-weight: 600; color: var(--text-secondary);">
                <i class="fa-regular fa-clock" style="margin-right: 4px; color: #94a3b8;"></i> ${escapeHtml(expText)}
              </span>
            </td>
            <td>
              <span class="status-pill ${pillClass}">
                <i class="fa-solid ${iconClass}"></i> ${escapeHtml(item.status || 'Active')}
              </span>
            </td>
            <td style="text-align: right;">
              <div class="crud-actions">
                <button class="btn-action-view" onclick="openViewModal(${item.id})" title="View Details & Note"><i class="fa-solid fa-eye"></i></button>
                <button class="btn-action-edit" onclick="openEditModal(${item.id})" title="Edit Status / Assignment"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-action-del" onclick="deleteItem(${item.id})" title="Delete Task"><i class="fa-solid fa-trash"></i></button>
              </div>
            </td>
          </tr>
        `;
      });
      tbody.innerHTML = html;
      updateBulkActionUI();
    }

    const infoEl = document.getElementById('pagination-info');
    if (totalCount === 0) {
      infoEl.textContent = 'Showing 0 to 0 of 0 records';
    } else {
      infoEl.innerHTML = `Showing <strong style="color: var(--text-primary);">${startIdx + 1}</strong> to <strong style="color: var(--text-primary);">${endIdx}</strong> of <strong style="color: var(--text-primary);">${totalCount}</strong> records${totalCount !== tableRecords.length ? ' (filtered from ' + tableRecords.length + ' total)' : ''}`;
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

  function exportDataCSV() {
    const filtered = getFilteredRecords();
    if (!filtered.length) {
      PmsAlert.toast('No records to export', 'info');
      return;
    }
    let csv = '"ID","Room","Floor","Category","Reason","Assigned Personnel","Expected Completion","Status","Note"\n';
    filtered.forEach(r => {
      const roomNum = r.room ? r.room.room_number : r.room_id;
      const roomFl = r.room ? r.room.floor : '';
      const roomCat = r.room ? r.room.category : '';
      const cleanNote = (r.note || '').replace(/"/g, '""');
      csv += `"${r.id}","${roomNum}","${roomFl}","${roomCat}","${r.reason || ''}","${r.assign || ''}","${r.expected_date_time || ''}","${r.status || ''}","${cleanNote}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Room_Maintenance_${Date.now()}.csv`;
    a.click();
    PmsAlert.toast('CSV exported successfully!');
  }

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  window.addEventListener('DOMContentLoaded', () => {
    updateKpis();
    renderTable();
  });
</script>
@endpush
