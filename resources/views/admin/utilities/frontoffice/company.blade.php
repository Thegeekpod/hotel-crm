@extends('admin.layouts.app')

@section('title', 'Company Manage - Hotel Sagar Sonnet PMS')

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
  }
  .crud-search-input {
    height: 38px;
    padding: 8px 14px 8px 34px;
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
  .crud-actions { display: flex; gap: 8px; justify-content: flex-end; }
  .btn-action-edit {
    padding: 6px 11px;
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
    padding: 6px 11px;
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
  .admin-form-textarea {
    width: 100%;
    min-height: 70px;
    padding: 8px 12px;
    font-size: 13px;
    border: 1px solid var(--border-medium);
    border-radius: var(--radius-md);
    background: #fff;
    color: var(--text-primary);
    box-sizing: border-box;
    font-family: inherit;
    resize: vertical;
  }
  .meal-discount-row {
    display: grid;
    grid-template-columns: 2fr 1.3fr 1.1fr 36px;
    gap: 8px;
    align-items: center;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 10px;
    transition: all 0.2s ease;
  }
  .meal-discount-row:hover {
    border-color: #cbd5e1;
    background: #f1f5f9;
  }
  .btn-remove-meal-row {
    width: 36px;
    height: 38px;
    border-radius: 8px;
    border: 1px solid #fecaca;
    background: #fef2f2;
    color: #dc2626;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    box-sizing: border-box;
  }
  .btn-remove-meal-row:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(220, 38, 38, 0.25);
  }
  .meal-val-wrap {
    position: relative;
    display: flex;
    align-items: center;
  }
  .meal-val-suffix {
    position: absolute;
    right: 10px;
    font-weight: 800;
    color: var(--text-muted);
    font-size: 11px;
    pointer-events: none;
  }
  .meal-val-wrap input {
    padding-right: 24px;
    height: 38px;
    font-family: var(--font-mono);
    font-weight: 700;
  }
  #crud-modal .select2-container {
    width: 100% !important;
  }
  #crud-modal .select2-container--default .select2-selection--single {
    height: 38px !important;
    padding: 4px 10px !important;
    border-radius: 8px !important;
    font-size: 12px !important;
  }
  #crud-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
  }
  #crud-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
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
          <h1 class="pms-page-title">Administrator • Master Data Management</h1>
          <div class="pms-page-subtitle">Configure and manage live records for active hotel modules.</div>
        </div>
      </div>
      <div style="display: flex; gap: 10px;">
        <button class="btn-ui-secondary" onclick="exportDataCSV()"><i class="fa-solid fa-file-csv"></i> Export CSV</button>
      </div>
    </div>

    <div class="admin-layout">
      @include('admin.includes.sidebar')

      <section class="admin-content-pane">
        <div id="crud-view-container">
          <div class="crud-header-card">
            <div>
              <div style="display: flex; align-items: center; gap: 8px;">
                <h2 style="font-size: 16px; font-weight: 800; color: var(--text-primary); margin: 0;">Company Directory</h2>
                <span class="badge-tag blue" id="view-count">{{ count($items) }} Companies</span>
              </div>
              <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px;">
                Manage corporate accounts, corporate billing details, addresses, and GSTIN numbers.
              </div>
            </div>
            <button class="btn-ui-primary" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Add Company</button>
          </div>

          <div class="crud-toolbar">
            <div class="crud-toolbar-left">
              <div class="crud-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="table-search" class="crud-search-input" placeholder="Search company, GSTIN, phone..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
              </div>
              <select id="status-filter" class="crud-filter-select" onchange="handleStatusFilter(this.value)">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
                  <th>Company Name</th>
                  <th>GSTIN</th>
                  <th>Phone Number</th>
                  <th>Address</th>
                  <th>Discount</th>
                  <th>Meal Discounts</th>
                  <th>Status</th>
                  <th style="text-align: right; width: 120px;">Actions</th>
                </tr>
              </thead>
              <tbody id="table-body">
                @forelse($items as $idx => $item)
                <tr data-id="{{ $item->id }}">
                  <td style="text-align: center;">
                    <input type="checkbox" class="row-select-check" value="{{ $item->id }}" onchange="handleRowSelect(this, '{{ $item->id }}')" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
                  </td>
                  <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">{{ $idx + 1 }}</td>
                  <td style="font-weight: 700; color: var(--text-primary);">
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <i class="fa-solid fa-building" style="color: var(--accent-primary);"></i>
                      <span>{{ $item->name }}</span>
                    </div>
                  </td>
                  <td>
                    @if($item->gstin)
                      <span class="badge-tag blue" style="font-family: var(--font-mono); font-size: 11px;">{{ $item->gstin }}</span>
                    @else
                      <span style="color: var(--text-muted); font-size: 11px;">-</span>
                    @endif
                  </td>
                  <td style="font-weight: 600; color: var(--text-secondary); font-size: 12px;">
                    @if($item->phone)
                      <i class="fa-solid fa-phone" style="font-size: 10px; margin-right: 4px; color: #64748b;"></i> {{ $item->phone }}
                    @else
                      <span style="color: var(--text-muted);">-</span>
                    @endif
                  </td>
                  <td style="color: var(--text-secondary); font-size: 12px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $item->address }}">
                    {{ $item->address ?: '-' }}
                  </td>
                  <td>
                    @if($item->discount_type === 'amount' && (float)$item->discount_value > 0)
                      <span class="badge-tag green" style="font-family: var(--font-mono); font-size: 11px; font-weight: 700;">
                        <i class="fa-solid fa-indian-rupee-sign" style="font-size: 9px;"></i> {{ number_format($item->discount_value, 2) }}
                      </span>
                    @elseif((float)$item->discount_value > 0)
                      <span class="badge-tag purple" style="font-family: var(--font-mono); font-size: 11px; font-weight: 700;">
                        <i class="fa-solid fa-percent" style="font-size: 9px;"></i> {{ (float)$item->discount_value }}%
                      </span>
                    @else
                      <span style="color: var(--text-muted); font-size: 11px;">0% (None)</span>
                    @endif
                  </td>
                  <td>
                    @if(!empty($item->meal_discounts) && is_array($item->meal_discounts) && count($item->meal_discounts) > 0)
                      <div style="display: flex; flex-direction: column; gap: 4px;">
                        @foreach($item->meal_discounts as $md)
                          @php
                            $mPlan = $mealPlans->firstWhere('id', $md['meal_plan_id'] ?? null);
                            $pName = $mPlan ? $mPlan->plan_name : 'Plan #' . ($md['meal_plan_id'] ?? '');
                            $mVal = (float)($md['discount_value'] ?? 0);
                            $mType = $md['discount_type'] ?? 'percentage';
                          @endphp
                          <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px;">
                            <span style="font-weight: 700; color: #475569;">{{ $pName }}:</span>
                            @if($mType === 'amount')
                              <span class="badge-tag green" style="font-family: var(--font-mono); font-size: 10px; font-weight: 700; padding: 1px 6px;">
                                ₹ {{ number_format($mVal, 2) }}
                              </span>
                            @else
                              <span class="badge-tag purple" style="font-family: var(--font-mono); font-size: 10px; font-weight: 700; padding: 1px 6px;">
                                {{ $mVal }}%
                              </span>
                            @endif
                          </div>
                        @endforeach
                      </div>
                    @else
                      <span style="color: var(--text-muted); font-size: 11px;">-</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge-tag {{ $item->status === 'Active' ? 'green' : 'yellow' }}">
                      <i class="fa-solid fa-circle-check" style="font-size: 6px; margin-right: 4px;"></i>{{ $item->status }}
                    </span>
                  </td>
                  <td style="text-align: right;">
                    <div class="crud-actions">
                      <button class="btn-action-edit" onclick="openEditModal({{ $item->id }})" title="Edit Record"><i class="fa-solid fa-pen"></i></button>
                      <button class="btn-action-del" onclick="deleteItem({{ $item->id }})" title="Delete Record"><i class="fa-solid fa-trash"></i></button>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                    <i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>
                    No companies found. Click "+ Add Company" to create one.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination & Rows Controls -->
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

<div class="modal-backdrop" id="crud-modal">
  <div class="modal-window" style="width: 620px; max-width: 95vw;">
    <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
      <h3 style="margin: 0;"><i class="fa-solid fa-building"></i> <span id="modal-title">Add Company</span></h3>
      <button class="modal-close" onclick="closeModal('crud-modal')">&times;</button>
    </div>
    <form id="crud-form" onsubmit="handleFormSubmit(event)">
      <input type="hidden" id="item-id">
      <div class="modal-content-area" style="padding: 20px; background: #f8fafc;">
        <div class="admin-form-group">
          <label class="admin-form-label">Company Name <span style="color: var(--accent-rose);">*</span></label>
          <input type="text" id="form-name" class="admin-form-input" placeholder="e.g. Tata Consultancy Services, Infosys Ltd" required>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div class="admin-form-group">
            <label class="admin-form-label">GSTIN Number</label>
            <input type="text" id="form-gstin" class="admin-form-input" placeholder="e.g. 19ABCDE1234F1Z5">
          </div>
          <div class="admin-form-group">
            <label class="admin-form-label">Phone Number</label>
            <input type="text" id="form-phone" class="admin-form-input" placeholder="e.g. +91 98765 43210">
          </div>
        </div>
        <div class="admin-form-group">
          <label class="admin-form-label">Office Address</label>
          <textarea id="form-address" class="admin-form-textarea" placeholder="e.g. Plot No 4, Sector V, Salt Lake City, Kolkata, WB"></textarea>
        </div>
        <!-- Discount Configuration (Two radio buttons: Percentage / Solid Amount) -->
        <div class="admin-form-group" style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 14px; margin-bottom: 14px;">
          <label class="admin-form-label" style="margin-bottom: 8px;">Discount Type <span style="color: var(--accent-rose);">*</span></label>
          <div style="display: flex; gap: 24px; align-items: center; margin-bottom: 12px;">
            <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-primary); cursor: pointer;">
              <input type="radio" name="form_discount_type" id="radio-discount-percentage" value="percentage" checked onchange="handleDiscountTypeChange('percentage')" style="accent-color: var(--accent-primary); width: 16px; height: 16px; cursor: pointer;">
              <span><i class="fa-solid fa-percent" style="color: var(--accent-primary); font-size: 11px;"></i> Percentage (%)</span>
            </label>
            <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-primary); cursor: pointer;">
              <input type="radio" name="form_discount_type" id="radio-discount-amount" value="amount" onchange="handleDiscountTypeChange('amount')" style="accent-color: var(--accent-primary); width: 16px; height: 16px; cursor: pointer;">
              <span><i class="fa-solid fa-indian-rupee-sign" style="color: #059669; font-size: 11px;"></i> Solid Amount (₹)</span>
            </label>
          </div>

          <!-- Dynamic Input: Percentage -->
          <div id="container-discount-percentage">
            <label class="admin-form-label" style="font-size: 10px; color: var(--text-secondary);">Discount Percentage (%)</label>
            <div style="position: relative;">
              <input type="number" id="form-discount-percentage" class="admin-form-input" placeholder="e.g. 10.00" min="0" max="100" step="any" style="padding-right: 36px; font-family: var(--font-mono); font-weight: 700;">
              <span style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-weight: 800; color: var(--text-muted); font-size: 13px;">%</span>
            </div>
            <small style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">Specify applicable percentage discount (0 to 100%).</small>
          </div>

          <!-- Dynamic Input: Solid Amount -->
          <div id="container-discount-amount" style="display: none;">
            <label class="admin-form-label" style="font-size: 10px; color: var(--text-secondary);">Solid Discount Amount (₹)</label>
            <div style="position: relative;">
              <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 800; color: #059669; font-size: 13px;">₹</span>
              <input type="number" id="form-discount-amount" class="admin-form-input" placeholder="e.g. 500.00" min="0" step="any" style="padding-left: 32px; font-family: var(--font-mono); font-weight: 700;">
            </div>
            <small style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">Specify flat solid amount deduction per reservation in INR.</small>
          </div>
        </div>

        <!-- Meal Plan Discounts Configuration (Searchable meal dropdown, discount type, discount value, re-generate add more & remove) -->
        <div class="admin-form-group" style="background: #ffffff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 14px; margin-bottom: 14px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
            <div>
              <label class="admin-form-label" style="margin-bottom: 2px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-utensils" style="color: #ea580c; font-size: 13px;"></i>
                <span>Meal Plan Discounts</span>
              </label>
              <small style="font-size: 11px; color: var(--text-muted);">Configure specific corporate meal plan discounts.</small>
            </div>
            <button type="button" class="btn-ui-secondary" onclick="addMealDiscountRow()" style="font-size: 11px; height: 32px; padding: 0 12px; display: inline-flex; align-items: center; gap: 6px; border-color: #cbd5e1; color: var(--accent-primary); font-weight: 700; cursor: pointer;">
              <i class="fa-solid fa-plus"></i> Add Meal Discount
            </button>
          </div>

          <div id="meal-discounts-container" style="display: flex; flex-direction: column; gap: 10px;">
            <!-- Dynamic rows will be rendered here -->
          </div>
        </div>
        <div class="admin-form-group" style="margin-bottom: 0;">
          <label class="admin-form-label">Status <span style="color: var(--accent-rose);">*</span></label>
          <select id="form-status" class="admin-form-input" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-bot" style="padding: 14px 20px; background: #fff; border-top: 1px solid var(--border-medium); display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" class="btn-ui-secondary" onclick="closeModal('crud-modal')">Cancel</button>
        <button type="submit" class="btn-ui-primary" id="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Company</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const urlParams = new URLSearchParams(window.location.search);
  let searchQuery = urlParams.get('search') || "{{ request('search') }}" || '';
  let statusFilter = urlParams.get('status') || "{{ request('status', 'all') }}" || 'all';
  let currentPage = parseInt(urlParams.get('page') || '1', 10);
  let pageSize = urlParams.get('per_page') || "{{ request('per_page', '10') }}" || '10';
  let tableRecords = @json($items);
  let availableMealPlans = @json($mealPlans ?? []);
  let mealRowCounter = 0;

  const baseUrl = "{{ url('admin/utilities/front-office/company') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  function updateUrlParams() {
    const params = new URLSearchParams();
    if (searchQuery) params.set('search', searchQuery);
    if (statusFilter && statusFilter !== 'all') params.set('status', statusFilter);
    if (currentPage > 1) params.set('page', currentPage);
    if (pageSize !== '10') params.set('per_page', pageSize);

    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState(null, '', newUrl);
  }

  function handleDiscountTypeChange(type) {
    const pctWrap = document.getElementById('container-discount-percentage');
    const amtWrap = document.getElementById('container-discount-amount');
    if (type === 'amount') {
      pctWrap.style.display = 'none';
      amtWrap.style.display = 'block';
    } else {
      pctWrap.style.display = 'block';
      amtWrap.style.display = 'none';
    }
  }

  /* Dynamic Meal Discount Rows Management */
  function addMealDiscountRow(selectedPlanId = '', discountType = 'percentage', discountValue = '') {
    mealRowCounter++;
    const rowId = mealRowCounter;
    const container = document.getElementById('meal-discounts-container');

    const row = document.createElement('div');
    row.className = 'meal-discount-row';
    row.id = `meal-row-${rowId}`;

    let planOptions = '<option value="">Select Meal Plan...</option>';
    (availableMealPlans || []).forEach(mp => {
      const priceText = Number(mp.price) > 0 ? ` (₹${Number(mp.price).toFixed(2)})` : '';
      const isSelected = String(mp.id) === String(selectedPlanId) ? 'selected' : '';
      planOptions += `<option value="${mp.id}" ${isSelected}>${escapeHtml(mp.plan_name)}${priceText}</option>`;
    });

    const isAmount = discountType === 'amount';
    const valStr = (discountValue !== '' && discountValue !== null && discountValue !== undefined) ? discountValue : '';

    row.innerHTML = `
      <div>
        <select class="admin-form-input meal-plan-select" style="width: 100%;">
          ${planOptions}
        </select>
      </div>
      <div>
        <select class="admin-form-input meal-discount-type" onchange="updateMealDiscountRowType(this)" style="height: 38px; font-weight: 600;">
          <option value="percentage" ${!isAmount ? 'selected' : ''}>Percentage (%)</option>
          <option value="amount" ${isAmount ? 'selected' : ''}>Solid Amount (₹)</option>
        </select>
      </div>
      <div class="meal-val-wrap">
        <input type="number" class="admin-form-input meal-discount-val" placeholder="${!isAmount ? 'e.g. 10' : 'e.g. 100'}" value="${valStr}" min="0" ${!isAmount ? 'max="100"' : ''} step="any">
        <span class="meal-val-suffix">${!isAmount ? '%' : '₹'}</span>
      </div>
      <div>
        <button type="button" class="btn-remove-meal-row" onclick="removeMealDiscountRow('${rowId}')" title="Remove or Clear Meal Discount">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
    `;

    container.appendChild(row);

    // Initialize searchable Select2 on the newly created meal plan dropdown
    $(`#meal-row-${rowId} .meal-plan-select`).select2({
      dropdownParent: $('#crud-modal'),
      placeholder: 'Select Meal Plan...',
      width: '100%'
    });
  }

  function removeMealDiscountRow(rowId) {
    const container = document.getElementById('meal-discounts-container');
    const rows = container ? container.querySelectorAll('.meal-discount-row') : [];

    // Keep at least one row: if only 1 row remains, reset its inputs instead of removing it
    if (rows.length <= 1) {
      const row = document.getElementById(`meal-row-${rowId}`);
      if (row) {
        const selectEl = $(row).find('.meal-plan-select');
        selectEl.val('').trigger('change');
        const typeSelect = row.querySelector('.meal-discount-type');
        if (typeSelect) {
          typeSelect.value = 'percentage';
          updateMealDiscountRowType(typeSelect);
        }
        const valInput = row.querySelector('.meal-discount-val');
        if (valInput) valInput.value = '';
      }
      return;
    }

    const row = document.getElementById(`meal-row-${rowId}`);
    if (!row) return;
    const selectEl = $(row).find('.meal-plan-select');
    if (selectEl.data('select2')) {
      selectEl.select2('destroy');
    }
    row.remove();
  }

  function updateMealDiscountRowType(selectEl) {
    const row = selectEl.closest('.meal-discount-row');
    if (!row) return;
    const isPercent = selectEl.value === 'percentage';
    const valInput = row.querySelector('.meal-discount-val');
    const suffix = row.querySelector('.meal-val-suffix');
    if (suffix) suffix.textContent = isPercent ? '%' : '₹';
    if (valInput) {
      valInput.placeholder = isPercent ? 'e.g. 10' : 'e.g. 100';
      if (isPercent) {
        valInput.setAttribute('max', '100');
      } else {
        valInput.removeAttribute('max');
      }
    }
  }

  function resetMealDiscounts() {
    const container = document.getElementById('meal-discounts-container');
    if (!container) return;
    $(container).find('.meal-plan-select').each(function() {
      if ($(this).data('select2')) {
        $(this).select2('destroy');
      }
    });
    container.innerHTML = '';
  }

  function openAddModal() {
    document.getElementById('modal-title').textContent = 'Add Company';
    document.getElementById('item-id').value = '';
    document.getElementById('form-name').value = '';
    document.getElementById('form-gstin').value = '';
    document.getElementById('form-phone').value = '';
    document.getElementById('form-address').value = '';
    document.getElementById('form-status').value = 'Active';
    document.getElementById('radio-discount-percentage').checked = true;
    document.getElementById('form-discount-percentage').value = '';
    document.getElementById('form-discount-amount').value = '';
    handleDiscountTypeChange('percentage');
    resetMealDiscounts();
    addMealDiscountRow(); // Keep one at least!
    openModal('crud-modal');
  }

  function openEditModal(id) {
    const item = tableRecords.find(r => String(r.id) === String(id));
    if (!item) return;

    document.getElementById('modal-title').textContent = 'Edit Company';
    document.getElementById('item-id').value = item.id;
    document.getElementById('form-name').value = item.name || '';
    document.getElementById('form-gstin').value = item.gstin || '';
    document.getElementById('form-phone').value = item.phone || '';
    document.getElementById('form-address').value = item.address || '';
    document.getElementById('form-status').value = item.status || 'Active';

    const discType = item.discount_type === 'amount' ? 'amount' : 'percentage';
    const discVal = item.discount_value !== undefined && item.discount_value !== null ? parseFloat(item.discount_value) : '';
    if (discType === 'amount') {
      document.getElementById('radio-discount-amount').checked = true;
      document.getElementById('form-discount-amount').value = discVal;
      document.getElementById('form-discount-percentage').value = '';
    } else {
      document.getElementById('radio-discount-percentage').checked = true;
      document.getElementById('form-discount-percentage').value = discVal;
      document.getElementById('form-discount-amount').value = '';
    }
    handleDiscountTypeChange(discType);

    // Populate Meal Discounts (keep at least one row)
    resetMealDiscounts();
    if (Array.isArray(item.meal_discounts) && item.meal_discounts.length > 0) {
      item.meal_discounts.forEach(md => {
        addMealDiscountRow(md.meal_plan_id, md.discount_type, md.discount_value);
      });
    } else {
      addMealDiscountRow(); // Keep one at least!
    }

    openModal('crud-modal');
  }

  async function handleFormSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('item-id').value;
    const name = document.getElementById('form-name').value.trim();
    const gstin = document.getElementById('form-gstin').value.trim();
    const phone = document.getElementById('form-phone').value.trim();
    const address = document.getElementById('form-address').value.trim();
    const status = document.getElementById('form-status').value;

    const discountType = document.querySelector('input[name="form_discount_type"]:checked')?.value || 'percentage';
    let discountValue = 0;
    if (discountType === 'amount') {
      const rawVal = document.getElementById('form-discount-amount').value.trim();
      discountValue = rawVal !== '' ? parseFloat(rawVal) : 0;
      if (isNaN(discountValue) || discountValue < 0) {
        PmsAlert.error('Validation Error', 'Please enter a valid discount amount (minimum 0).');
        return;
      }
    } else {
      const rawVal = document.getElementById('form-discount-percentage').value.trim();
      discountValue = rawVal !== '' ? parseFloat(rawVal) : 0;
      if (isNaN(discountValue) || discountValue < 0 || discountValue > 100) {
        PmsAlert.error('Validation Error', 'Please enter a valid discount percentage (0 to 100).');
        return;
      }
    }

    if (!name) {
      PmsAlert.error('Validation Error', 'Please enter a valid company name.');
      return;
    }

    // Collect Meal Discounts
    const mealDiscountRows = document.querySelectorAll('.meal-discount-row');
    const mealDiscounts = [];
    const seenPlanIds = new Set();

    for (const row of mealDiscountRows) {
      const planSelect = row.querySelector('.meal-plan-select');
      const typeSelect = row.querySelector('.meal-discount-type');
      const valInput = row.querySelector('.meal-discount-val');

      const planId = planSelect ? parseInt(planSelect.value, 10) : 0;
      const dType = typeSelect ? typeSelect.value : 'percentage';
      const rawVal = valInput ? valInput.value.trim() : '';

      // Skip row if untouched (no plan chosen and value left blank)
      if (!planId && rawVal === '') {
        continue;
      }

      if (!planId && rawVal !== '') {
        PmsAlert.error('Validation Error', 'Please select a meal plan for the entered discount value.');
        return;
      }

      if (planId && rawVal === '') {
        PmsAlert.error('Validation Error', 'Please enter a discount value for the selected meal plan.');
        return;
      }

      if (seenPlanIds.has(planId)) {
        PmsAlert.error('Validation Error', 'Duplicate meal plan detected in meal discount rows. Please configure each meal plan once.');
        return;
      }
      seenPlanIds.add(planId);

      const dVal = parseFloat(rawVal);
      if (isNaN(dVal) || dVal < 0) {
        PmsAlert.error('Validation Error', 'Please enter a valid discount value for each selected meal plan.');
        return;
      }
      if (dType === 'percentage' && dVal > 100) {
        PmsAlert.error('Validation Error', 'Meal discount percentage cannot exceed 100%.');
        return;
      }

      mealDiscounts.push({
        meal_plan_id: planId,
        discount_type: dType,
        discount_value: dVal
      });
    }

    const payload = { 
      name, 
      gstin, 
      phone, 
      address, 
      status, 
      discount_type: discountType, 
      discount_value: discountValue,
      meal_discounts: mealDiscounts
    };
    const url = id ? `${baseUrl}/${id}` : baseUrl;
    const method = id ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (res.ok && data.success) {
        closeModal('crud-modal');
        PmsAlert.toast(data.message || 'Saved successfully!');
        await loadTableData();
      } else {
        PmsAlert.error('Error', data.message || 'Failed to save company record.');
      }
    } catch (err) {
      PmsAlert.error('Server Error', 'Failed to communicate with the server.');
    }
  }

  async function deleteItem(id) {
    const result = await PmsAlert.confirmDelete('Delete Company?', 'This company record will be removed permanently.');
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
          PmsAlert.toast('Company deleted successfully!');
          await loadTableData();
        } else {
          PmsAlert.error('Delete Failed', data.message || 'Could not delete company.');
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

      const res = await fetch(`${baseUrl}?${params.toString()}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success) {
        tableRecords = data.data;
        if (Array.isArray(data.meal_plans)) {
          availableMealPlans = data.meal_plans;
        }
        renderTable();
      }
    } catch (err) {
      console.error(err);
    }
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
    const result = await PmsAlert.confirmDelete(`Delete ${count} Selected Companies?`, 'All selected company records will be permanently removed.');
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
      const matchSearch = !searchQuery ||
        (item.name && item.name.toLowerCase().includes(searchQuery)) ||
        (item.gstin && item.gstin.toLowerCase().includes(searchQuery)) ||
        (item.phone && item.phone.toLowerCase().includes(searchQuery)) ||
        (item.address && item.address.toLowerCase().includes(searchQuery)) ||
        (item.discount_value !== undefined && String(item.discount_value).includes(searchQuery)) ||
        (item.status && item.status.toLowerCase().includes(searchQuery));

      const matchStatus = statusFilter === 'all' || item.status === statusFilter;
      return matchSearch && matchStatus;
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

    document.getElementById('view-count').textContent = `${tableRecords.length} Compan${tableRecords.length === 1 ? 'y' : 'ies'}`;

    const tbody = document.getElementById('table-body');
    if (!pageItems.length) {
      tbody.innerHTML = `<tr><td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);"><i class="fa-solid fa-inbox" style="font-size: 28px; margin-bottom: 10px; display: block;"></i>No matching companies found.</td></tr>`;
      updateBulkActionUI();
    } else {
      let html = '';
      pageItems.forEach((item, idx) => {
        const rowNumber = startIdx + idx + 1;
        const badgeClass = item.status === 'Active' ? 'green' : 'yellow';
        const dVal = parseFloat(item.discount_value) || 0;
        let discountHtml = '<span style="color: var(--text-muted); font-size: 11px;">0% (None)</span>';
        if (dVal > 0) {
          if (item.discount_type === 'amount') {
            discountHtml = `<span class="badge-tag green" style="font-family: var(--font-mono); font-size: 11px; font-weight: 700;"><i class="fa-solid fa-indian-rupee-sign" style="font-size: 9px;"></i> ${dVal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
          } else {
            discountHtml = `<span class="badge-tag purple" style="font-family: var(--font-mono); font-size: 11px; font-weight: 700;"><i class="fa-solid fa-percent" style="font-size: 9px;"></i> ${dVal}%</span>`;
          }
        }

        // Meal Discounts HTML
        let mealDiscountsHtml = '<span style="color: var(--text-muted); font-size: 11px;">-</span>';
        if (Array.isArray(item.meal_discounts) && item.meal_discounts.length > 0) {
          mealDiscountsHtml = '<div style="display: flex; flex-direction: column; gap: 4px;">' +
            item.meal_discounts.map(md => {
              const mPlan = (availableMealPlans || []).find(p => Number(p.id) === Number(md.meal_plan_id));
              const pName = mPlan ? escapeHtml(mPlan.plan_name) : `Plan #${md.meal_plan_id}`;
              const mVal = parseFloat(md.discount_value) || 0;
              const badge = md.discount_type === 'amount' 
                ? `<span class="badge-tag green" style="font-family: var(--font-mono); font-size: 10px; font-weight: 700; padding: 1px 6px;">₹ ${mVal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`
                : `<span class="badge-tag purple" style="font-family: var(--font-mono); font-size: 10px; font-weight: 700; padding: 1px 6px;">${mVal}%</span>`;
              return `<div style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px;"><span style="font-weight: 700; color: #475569;">${pName}:</span> ${badge}</div>`;
            }).join('') +
          '</div>';
        }

        html += `
          <tr data-id="${item.id}">
            <td style="text-align: center;">
              <input type="checkbox" class="row-select-check" value="${item.id}" ${selectedIds.has(String(item.id)) ? 'checked' : ''} onchange="handleRowSelect(this, '${item.id}')" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent-primary);">
            </td>
            <td style="font-family: var(--font-mono); font-weight: 700; color: var(--text-muted); font-size: 11px;">${rowNumber}</td>
            <td style="font-weight: 700; color: var(--text-primary);">
              <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-building" style="color: var(--accent-primary);"></i>
                <span>${escapeHtml(item.name)}</span>
              </div>
            </td>
            <td>
              ${item.gstin ? `<span class="badge-tag blue" style="font-family: var(--font-mono); font-size: 11px;">${escapeHtml(item.gstin)}</span>` : '<span style="color: var(--text-muted); font-size: 11px;">-</span>'}
            </td>
            <td style="font-weight: 600; color: var(--text-secondary); font-size: 12px;">
              ${item.phone ? `<i class="fa-solid fa-phone" style="font-size: 10px; margin-right: 4px; color: #64748b;"></i> ${escapeHtml(item.phone)}` : '<span style="color: var(--text-muted);">-</span>'}
            </td>
            <td style="color: var(--text-secondary); font-size: 12px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${escapeHtml(item.address || '')}">
              ${escapeHtml(item.address || '-')}
            </td>
            <td>
              ${discountHtml}
            </td>
            <td>
              ${mealDiscountsHtml}
            </td>
            <td>
              <span class="badge-tag ${badgeClass}">
                <i class="fa-solid fa-circle-check" style="font-size: 6px; margin-right: 4px;"></i>${escapeHtml(item.status)}
              </span>
            </td>
            <td style="text-align: right;">
              <div class="crud-actions">
                <button class="btn-action-edit" onclick="openEditModal(${item.id})" title="Edit Record"><i class="fa-solid fa-pen"></i></button>
                <button class="btn-action-del" onclick="deleteItem(${item.id})" title="Delete Record"><i class="fa-solid fa-trash"></i></button>
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
    let csv = '"ID","Company Name","GSTIN","Phone","Address","Discount Type","Discount Value","Meal Discounts","Status"\n';
    filtered.forEach(r => {
      const cleanAddr = (r.address || '').replace(/"/g, '""');
      const dType = r.discount_type === 'amount' ? 'Solid Amount' : 'Percentage';
      const dVal = r.discount_value !== undefined && r.discount_value !== null ? r.discount_value : 0;
      let mealDiscText = '';
      if (Array.isArray(r.meal_discounts) && r.meal_discounts.length > 0) {
        mealDiscText = r.meal_discounts.map(md => {
          const mPlan = (availableMealPlans || []).find(p => Number(p.id) === Number(md.meal_plan_id));
          const pName = mPlan ? mPlan.plan_name : `Plan #${md.meal_plan_id}`;
          return `${pName}: ${md.discount_type === 'amount' ? '₹' + md.discount_value : md.discount_value + '%'}`;
        }).join('; ');
      }
      csv += `"${r.id}","${r.name}","${r.gstin || ''}","${r.phone || ''}","${cleanAddr}","${dType}","${dVal}","${mealDiscText.replace(/"/g, '""')}","${r.status}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `Companies_${Date.now()}.csv`;
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
    renderTable();
  });
</script>
@endpush