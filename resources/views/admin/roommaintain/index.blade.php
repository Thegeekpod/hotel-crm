@extends('admin.layouts.app')

@section('title', 'Room Maintenance - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 p-4 lg:p-6 space-y-6">
  <!-- Page Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <div class="w-2.5 h-8 bg-indigo-600 rounded-full"></div>
      <div>
        <h1 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight">Administrator • Room Maintenance Management</h1>
        <p class="text-xs lg:text-sm text-slate-500 font-medium">Track, assign, inspect, and update room maintenance work orders and engineer logs.</p>
      </div>
    </div>
    <div class="flex items-center gap-2.5">
      <button type="button" onclick="exportDataCSV()" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-xs hover:border-slate-300 transition-all flex items-center gap-2 cursor-pointer">
        <i class="fa-solid fa-file-csv text-slate-500"></i> Export CSV
      </button>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">
    @include('admin.includes.sidebar')

    <section class="min-w-0 space-y-6">
      <!-- Quick KPI Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Records</div>
            <div class="text-2xl font-black text-slate-800 mt-1" id="kpi-total">{{ count($items) }}</div>
          </div>
          <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-list-check"></i>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs flex items-center justify-between border-l-4 border-l-rose-500">
          <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active Work</div>
            <div class="text-2xl font-black text-rose-600 mt-1" id="kpi-active">{{ $activeCount ?? 0 }}</div>
          </div>
          <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-triangle-exclamation"></i>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs flex items-center justify-between border-l-4 border-l-amber-500">
          <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Checking / Testing</div>
            <div class="text-2xl font-black text-amber-600 mt-1" id="kpi-checking">{{ $checkingCount ?? 0 }}</div>
          </div>
          <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-spinner"></i>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-4.5 border border-slate-200/80 shadow-xs flex items-center justify-between border-l-4 border-l-emerald-500">
          <div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Completed</div>
            <div class="text-2xl font-black text-emerald-600 mt-1" id="kpi-completed">{{ $completedCount ?? 0 }}</div>
          </div>
          <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
            <i class="fa-solid fa-circle-check"></i>
          </div>
        </div>
      </div>

      <div id="crud-view-container" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Header Card -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
          <div>
            <div class="flex items-center gap-2.5">
              <h2 class="text-base font-bold text-slate-800 tracking-tight">Room Maintenance Tasks</h2>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60" id="view-count">{{ count($items) }} Logs</span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Manage work descriptions, assigned engineers, completion schedules, and resolution notes.</p>
          </div>
          <button type="button" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 self-start sm:self-auto cursor-pointer" onclick="openAddModal()">
            <i class="fa-solid fa-plus text-xs"></i> Schedule Maintenance
          </button>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
          <div class="flex flex-wrap items-center gap-2.5 flex-1 max-w-2xl">
            <div class="relative flex-1 min-w-[200px]">
              <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
              <input type="text" id="table-search" class="w-full h-9.5 pl-9 pr-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Search room, engineer, reason..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
            </div>
            <select id="status-filter" class="h-9.5 px-3 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handleStatusFilter(this.value)">
              <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
              <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
              <option value="Checking" {{ request('status') === 'Checking' ? 'selected' : '' }}>Checking</option>
              <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <select id="floor-filter" class="h-9.5 px-3 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handleFloorFilter(this.value)">
              <option value="all">All Floors</option>
              @foreach($floors as $fl)
                <option value="{{ $fl->floor }}">Floor {{ $fl->floor }}</option>
              @endforeach
            </select>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" class="h-9.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-all hidden items-center gap-2 cursor-pointer" id="btn-bulk-delete" onclick="handleBulkDelete()">
              <i class="fa-solid fa-trash-can text-xs"></i> Delete Selected (<span id="bulk-selected-count">0</span>)
            </button>
            <button type="button" class="h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer" onclick="exportDataCSV()">
              <i class="fa-solid fa-file-csv text-slate-500 text-xs"></i> Export
            </button>
            <button type="button" class="h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all flex items-center gap-2 cursor-pointer" onclick="loadTableData()">
              <i class="fa-solid fa-rotate text-slate-500 text-xs"></i> Refresh
            </button>
          </div>
        </div>

        <!-- Responsive Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse" id="crud-table">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                <th class="w-10 px-4 py-3.5 text-center">
                  <input type="checkbox" id="select-all-check" onchange="toggleSelectAll(this)" class="cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                </th>
                <th class="w-12 px-3 py-3.5 text-slate-400 font-mono">#</th>
                <th class="px-4 py-3.5">Room Details</th>
                <th class="px-4 py-3.5">Reason / Work Description</th>
                <th class="px-4 py-3.5">Assigned Personnel / Team</th>
                <th class="px-4 py-3.5">Expected Completion</th>
                <th class="px-4 py-3.5">Status</th>
                <th class="px-4 py-3.5 text-right w-32">Actions</th>
              </tr>
            </thead>
            <tbody id="table-body" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              @forelse($items as $idx => $item)
              <tr class="hover:bg-slate-50/70 transition-colors" data-id="{{ $item->id }}">
                <td class="px-4 py-3 text-center">
                  <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" value="{{ $item->id }}" onchange="handleRowSelect(this, '{{ $item->id }}')">
                </td>
                <td class="px-3 py-3 font-mono font-bold text-slate-400 text-[11px]">{{ $idx + 1 }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                      #{{ $item->room->room_number ?? $item->room_id }}
                    </span>
                    <div>
                      <div class="font-bold text-slate-800 text-xs">{{ $item->room->category ?? 'Standard Room' }}</div>
                      <div class="text-[11px] text-slate-400">Floor {{ $item->room->floor ?? '-' }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 font-semibold text-slate-800 max-w-[220px] truncate" title="{{ $item->reason }}">
                  {{ $item->reason ?? 'Maintenance Check' }}
                </td>
                <td class="px-4 py-3 font-medium text-slate-600">
                  <span class="inline-flex items-center gap-1.5 text-xs">
                    <i class="fa-solid fa-user-gear text-slate-400 text-[11px]"></i> {{ $item->assign ?: 'Not Assigned' }}
                  </span>
                </td>
                <td class="px-4 py-3 font-medium text-slate-600">
                  <span class="inline-flex items-center gap-1.5 text-xs">
                    <i class="fa-regular fa-clock text-slate-400 text-[11px]"></i>
                    {{ $item->expected_date_time ? \Carbon\Carbon::parse($item->expected_date_time)->format('d M Y, h:i A') : '-' }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  @php
                    $st = strtolower($item->status ?? 'active');
                  @endphp
                  @if($st === 'completed')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                      <i class="fa-solid fa-circle-check text-[9px]"></i> Completed
                    </span>
                  @elseif($st === 'checking')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                      <i class="fa-solid fa-spinner text-[9px]"></i> Checking
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                      <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Active
                    </span>
                  @endif
                </td>
                <td class="px-4 py-3 text-right">
                  <div class="inline-flex items-center gap-1 justify-end">
                    <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openViewModal({{ $item->id }})" title="View Details & Note">
                      <i class="fa-solid fa-eye text-xs"></i>
                    </button>
                    <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openEditModal({{ $item->id }})" title="Edit Status / Assignment">
                      <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="deleteItem({{ $item->id }})" title="Delete Task">
                      <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                  <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>
                  No room maintenance logs found. Click "+ Schedule Maintenance" to create one.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination Bar -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50" id="table-pagination-bar">
          <div class="text-xs text-slate-500 font-medium" id="pagination-info">
            Showing 1 to {{ count($items) }} of {{ count($items) }} records
          </div>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <span class="text-xs text-slate-500 font-semibold">Rows:</span>
              <select id="rows-per-page" class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handlePageSizeChange(this.value)">
                <option value="10" selected>10</option>
                <option value="20">20</option>
                <option value="100">100</option>
                <option value="all">All</option>
              </select>
            </div>
            <div class="flex items-center gap-1" id="pagination-nav"></div>
          </div>
        </div>
      </div>
    </section>
  </div>
</main>

<!-- ADD MAINTENANCE MODAL -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="add-modal">
  <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-screwdriver-wrench"></i>
        </span>
        <span>Schedule Room Maintenance</span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base" onclick="closeModal('add-modal')">&times;</button>
    </div>
    <form id="add-form" onsubmit="handleAddSubmit(event)">
      <div class="p-6 space-y-4 bg-slate-50/50">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Select Room <span class="text-rose-500">*</span></label>
          <select id="add-room-id" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
            <option value="">-- Choose Room --</option>
            @foreach($rooms as $r)
              <option value="{{ $r->id }}">Room #{{ $r->room_number }} (Floor {{ $r->floor }} • {{ $r->category }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Maintenance Reason / Work Description <span class="text-rose-500">*</span></label>
          <input type="text" id="add-reason" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. AC Repair, Plumbing Leak, Painting" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Assigned Personnel / Team</label>
            <input type="text" id="add-assign" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. John Doe, MEP Team">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Status <span class="text-rose-500">*</span></label>
            <select id="add-status" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
              <option value="Active" selected>Active (Under Maintenance)</option>
              <option value="Checking">Checking (Testing)</option>
              <option value="Completed">Completed (Normal)</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Expected Completion Date & Time</label>
          <input type="datetime-local" id="add-expected" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Work Note / Instructions</label>
          <textarea id="add-note" class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" rows="2" placeholder="Add any specific instructions, spare parts needed, or inspection details..."></textarea>
        </div>
      </div>
      <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex justify-end items-center gap-2.5">
        <button type="button" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('add-modal')">Cancel</button>
        <button type="submit" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 cursor-pointer" id="btn-add-save">
          <i class="fa-solid fa-floppy-disk text-xs"></i> Schedule Task
        </button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT STATUS & MAINTENANCE MODAL -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="edit-modal">
  <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-pen-to-square"></i>
        </span>
        <span>Edit Maintenance Task</span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base" onclick="closeModal('edit-modal')">&times;</button>
    </div>
    <form id="edit-form" onsubmit="handleEditSubmit(event)">
      <input type="hidden" id="edit-id">
      <div class="p-6 space-y-4 bg-slate-50/50">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Room</label>
          <input type="text" id="edit-room-display" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-slate-100 text-xs font-bold text-slate-700 cursor-not-allowed" readonly>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Maintenance Status <span class="text-rose-500">*</span></label>
            <select id="edit-status" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
              <option value="Active">Active (Under Maintenance)</option>
              <option value="Checking">Checking (Testing)</option>
              <option value="Completed">Completed (Room Normal Mode)</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Assigned Personnel / Team</label>
            <input type="text" id="edit-assign" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. John Doe, MEP Team">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Work Description / Reason <span class="text-rose-500">*</span></label>
          <input type="text" id="edit-reason" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" required>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Expected Completion Date & Time</label>
          <input type="datetime-local" id="edit-expected" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Work Note / Resolution Details</label>
          <textarea id="edit-note" class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" rows="2" placeholder="Enter notes or completion summary..."></textarea>
        </div>
      </div>
      <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex justify-end items-center gap-2.5">
        <button type="button" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('edit-modal')">Cancel</button>
        <button type="submit" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 cursor-pointer" id="btn-edit-save">
          <i class="fa-solid fa-floppy-disk text-xs"></i> Update Task
        </button>
      </div>
    </form>
  </div>
</div>

<!-- VIEW DETAILS MODAL -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="view-modal">
  <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-file-lines"></i>
        </span>
        <span id="view-modal-title">Maintenance Task Details</span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base" onclick="closeModal('view-modal')">&times;</button>
    </div>
    <div class="p-6 space-y-4 bg-slate-50/50">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Room Information</div>
          <div class="text-lg font-black text-indigo-600 mt-1 font-mono" id="view-room-num">-</div>
          <div class="text-xs text-slate-500 font-medium" id="view-room-details">-</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Task Status</div>
          <div class="mt-1.5" id="view-status-badge">-</div>
          <div class="text-[11px] text-slate-400 mt-1" id="view-created-at">-</div>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Reason / Work Description</div>
        <div class="text-xs font-bold text-slate-800" id="view-reason">-</div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Assigned Personnel</div>
          <div class="text-xs font-bold text-slate-800 mt-1" id="view-assign">-</div>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Expected Completion</div>
          <div class="text-xs font-bold text-slate-800 mt-1" id="view-expected">-</div>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3.5 shadow-xs">
        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Detailed Work Note / History</div>
        <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-wrap max-h-40 overflow-y-auto" id="view-note">-</div>
      </div>
    </div>
    <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex justify-between items-center">
      <button type="button" class="h-9 px-3.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer" id="view-btn-delete">
        <i class="fa-solid fa-trash text-xs"></i> Delete
      </button>
      <div class="flex items-center gap-2">
        <button type="button" class="h-9 px-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs transition-all flex items-center gap-1.5 cursor-pointer" id="view-btn-edit">
          <i class="fa-solid fa-pen text-xs"></i> Edit
        </button>
        <button type="button" class="h-9 px-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('view-modal')">Close</button>
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
    let badgeHtml = '';
    if (st === 'completed') {
      badgeHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><i class="fa-solid fa-circle-check text-[9px]"></i> Completed</span>';
    } else if (st === 'checking') {
      badgeHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60"><i class="fa-solid fa-spinner text-[9px]"></i> Checking</span>';
    } else {
      badgeHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60"><i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Active</span>';
    }
    document.getElementById('view-status-badge').innerHTML = badgeHtml;

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
      if (selectedIds.size > 0) {
        btn.classList.remove('hidden');
        btn.classList.add('inline-flex');
      } else {
        btn.classList.add('hidden');
        btn.classList.remove('inline-flex');
      }
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
      tbody.innerHTML = `<tr><td colspan="8" class="px-6 py-12 text-center text-slate-400"><i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>No matching maintenance records found.</td></tr>`;
      updateBulkActionUI();
    } else {
      let html = '';
      pageItems.forEach((item, idx) => {
        const rowNumber = startIdx + idx + 1;
        const roomNum = item.room ? item.room.room_number : item.room_id;
        const roomCat = item.room ? item.room.category : 'Standard';
        const roomFl = item.room ? item.room.floor : '-';

        const st = (item.status || 'Active').toLowerCase();
        let statusBadge = '';
        if (st === 'completed') {
          statusBadge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><i class="fa-solid fa-circle-check text-[9px]"></i> Completed</span>';
        } else if (st === 'checking') {
          statusBadge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60"><i class="fa-solid fa-spinner text-[9px]"></i> Checking</span>';
        } else {
          statusBadge = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60"><i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Active</span>';
        }

        let expText = '-';
        if (item.expected_date_time) {
          const d = new Date(item.expected_date_time);
          if (!isNaN(d.getTime())) {
            expText = d.toLocaleString('en-US', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
          }
        }

        html += `
          <tr class="hover:bg-slate-50/70 transition-colors" data-id="${item.id}">
            <td class="px-4 py-3 text-center">
              <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" value="${item.id}" ${selectedIds.has(String(item.id)) ? 'checked' : ''} onchange="handleRowSelect(this, '${item.id}')">
            </td>
            <td class="px-3 py-3 font-mono font-bold text-slate-400 text-[11px]">${rowNumber}</td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                  #${escapeHtml(roomNum)}
                </span>
                <div>
                  <div class="font-bold text-slate-800 text-xs">${escapeHtml(roomCat)}</div>
                  <div class="text-[11px] text-slate-400">Floor ${escapeHtml(roomFl)}</div>
                </div>
              </div>
            </td>
            <td class="px-4 py-3 font-semibold text-slate-800 max-w-[220px] truncate" title="${escapeHtml(item.reason || '')}">
              ${escapeHtml(item.reason || 'Maintenance Check')}
            </td>
            <td class="px-4 py-3 font-medium text-slate-600">
              <span class="inline-flex items-center gap-1.5 text-xs">
                <i class="fa-solid fa-user-gear text-slate-400 text-[11px]"></i> ${escapeHtml(item.assign || 'Not Assigned')}
              </span>
            </td>
            <td class="px-4 py-3 font-medium text-slate-600">
              <span class="inline-flex items-center gap-1.5 text-xs">
                <i class="fa-regular fa-clock text-slate-400 text-[11px]"></i> ${escapeHtml(expText)}
              </span>
            </td>
            <td class="px-4 py-3">
              ${statusBadge}
            </td>
            <td class="px-4 py-3 text-right">
              <div class="inline-flex items-center gap-1 justify-end">
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openViewModal(${item.id})" title="View Details & Note">
                  <i class="fa-solid fa-eye text-xs"></i>
                </button>
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openEditModal(${item.id})" title="Edit Status / Assignment">
                  <i class="fa-solid fa-pen text-xs"></i>
                </button>
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="deleteItem(${item.id})" title="Delete Task">
                  <i class="fa-solid fa-trash text-xs"></i>
                </button>
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
      infoEl.innerHTML = `Showing <strong class="text-slate-800">${startIdx + 1}</strong> to <strong class="text-slate-800">${endIdx}</strong> of <strong class="text-slate-800">${totalCount}</strong> records${totalCount !== tableRecords.length ? ' (filtered from ' + tableRecords.length + ' total)' : ''}`;
    }

    renderPaginationNav(totalPages);
  }

  function renderPaginationNav(totalPages) {
    const nav = document.getElementById('pagination-nav');
    if (!nav) return;

    if (totalPages <= 1) {
      nav.innerHTML = `
        <button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-300 text-xs flex items-center justify-center cursor-not-allowed" disabled><i class="fa-solid fa-chevron-left text-[10px]"></i></button>
        <button class="w-8 h-8 rounded-lg bg-indigo-600 text-white text-xs font-bold flex items-center justify-center shadow-xs">1</button>
        <button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-300 text-xs flex items-center justify-center cursor-not-allowed" disabled><i class="fa-solid fa-chevron-right text-[10px]"></i></button>
      `;
      return;
    }

    let html = '';
    html += `<button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} title="Previous Page"><i class="fa-solid fa-chevron-left text-[10px]"></i></button>`;

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
        html += `<span class="w-8 h-8 text-slate-400 text-xs flex items-center justify-center font-bold">...</span>`;
      } else {
        const isActive = page === currentPage;
        html += `<button class="w-8 h-8 rounded-lg text-xs font-bold flex items-center justify-center transition-all cursor-pointer ${isActive ? 'bg-indigo-600 text-white shadow-xs' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50'}" onclick="goToPage(${page})">${page}</button>`;
      }
    });

    html += `<button class="w-8 h-8 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} title="Next Page"><i class="fa-solid fa-chevron-right text-[10px]"></i></button>`;

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
