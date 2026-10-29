@extends('admin.layouts.app')

@section('title', 'Company Manage - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 p-4 lg:p-6 space-y-6">
  <!-- Page Header -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <div class="w-2.5 h-8 bg-indigo-600 rounded-full"></div>
      <div>
        <h1 class="text-xl lg:text-2xl font-black text-slate-800 tracking-tight">Administrator • Master Data Management</h1>
        <p class="text-xs lg:text-sm text-slate-500 font-medium">Configure corporate accounts, corporate billing details, addresses, and tax rates.</p>
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
      <div id="crud-view-container" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Header Card -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
          <div>
            <div class="flex items-center gap-2.5">
              <h2 class="text-base font-bold text-slate-800 tracking-tight">Company Directory</h2>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60" id="view-count">{{ count($items) }} Companies</span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Manage corporate accounts, corporate billing details, addresses, and GSTIN numbers.</p>
          </div>
          <button type="button" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 self-start sm:self-auto cursor-pointer" onclick="openAddModal()">
            <i class="fa-solid fa-plus text-xs"></i> Add Company
          </button>
        </div>

        <!-- Toolbar -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
          <div class="flex flex-wrap items-center gap-2.5 flex-1 max-w-xl">
            <div class="relative flex-1 min-w-[200px]">
              <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
              <input type="text" id="table-search" class="w-full h-9.5 pl-9 pr-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="Search company, GSTIN, phone..." value="{{ request('search') }}" oninput="handleSearch(this.value)">
            </div>
            <select id="status-filter" class="h-9.5 px-3 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" onchange="handleStatusFilter(this.value)">
              <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
              <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
              <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
                <th class="px-4 py-3.5">Company Name</th>
                <th class="px-4 py-3.5">GSTIN</th>
                <th class="px-4 py-3.5">Phone Number</th>
                <th class="px-4 py-3.5">Address</th>
                <th class="px-4 py-3.5">Discount</th>
                <th class="px-4 py-3.5">Meal Discounts</th>
                <th class="px-4 py-3.5">Status</th>
                <th class="px-4 py-3.5 text-right w-28">Actions</th>
              </tr>
            </thead>
            <tbody id="table-body" class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
              @forelse($items as $idx => $item)
              <tr class="hover:bg-slate-50/70 transition-colors" data-id="{{ $item->id }}">
                <td class="px-4 py-3 text-center">
                  <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" value="{{ $item->id }}" onchange="handleRowSelect(this, '{{ $item->id }}')">
                </td>
                <td class="px-3 py-3 font-mono font-bold text-slate-400 text-[11px]">{{ $idx + 1 }}</td>
                <td class="px-4 py-3 font-bold text-slate-800">
                  <div class="flex items-center gap-2.5">
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                      <i class="fa-solid fa-building text-xs"></i>
                    </span>
                    <span class="font-bold text-slate-800">{{ $item->name }}</span>
                  </div>
                </td>
                <td class="px-4 py-3">
                  @if($item->gstin)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 border border-sky-200/60 font-mono text-[11px] font-bold">{{ $item->gstin }}</span>
                  @else
                    <span class="text-slate-400 text-[11px]">-</span>
                  @endif
                </td>
                <td class="px-4 py-3 font-semibold text-slate-600 text-xs">
                  @if($item->phone)
                    <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-phone text-[10px] text-slate-400"></i> {{ $item->phone }}</span>
                  @else
                    <span class="text-slate-400">-</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-slate-500 max-w-[200px] truncate" title="{{ $item->address }}">
                  {{ $item->address ?: '-' }}
                </td>
                <td class="px-4 py-3">
                  @if($item->discount_type === 'amount' && (float)$item->discount_value > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[11px] font-bold">
                      <i class="fa-solid fa-indian-rupee-sign text-[9px]"></i> {{ number_format($item->discount_value, 2) }}
                    </span>
                  @elseif((float)$item->discount_value > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200/60 font-mono text-[11px] font-bold">
                      <i class="fa-solid fa-percent text-[9px]"></i> {{ (float)$item->discount_value }}%
                    </span>
                  @else
                    <span class="text-slate-400 text-[11px]">0% (None)</span>
                  @endif
                </td>
                <td class="px-4 py-3">
                  @if(!empty($item->meal_discounts) && is_array($item->meal_discounts) && count($item->meal_discounts) > 0)
                    <div class="flex flex-col gap-1">
                      @foreach($item->meal_discounts as $md)
                        @php
                          $mPlan = $mealPlans->firstWhere('id', $md['meal_plan_id'] ?? null);
                          $pName = $mPlan ? $mPlan->plan_name : 'Plan #' . ($md['meal_plan_id'] ?? '');
                          $mVal = (float)($md['discount_value'] ?? 0);
                          $mType = $md['discount_type'] ?? 'percentage';
                        @endphp
                        <div class="inline-flex items-center gap-1.5 text-[11px]">
                          <span class="font-bold text-slate-600">{{ $pName }}:</span>
                          @if($mType === 'amount')
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold">
                              ₹ {{ number_format($mVal, 2) }}
                            </span>
                          @else
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200/60 font-mono text-[10px] font-bold">
                              {{ $mVal }}%
                            </span>
                          @endif
                        </div>
                      @endforeach
                    </div>
                  @else
                    <span class="text-slate-400 text-[11px]">-</span>
                  @endif
                </td>
                <td class="px-4 py-3">
                  @if($item->status === 'Active')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Inactive
                    </span>
                  @endif
                </td>
                <td class="px-4 py-3 text-right">
                  <div class="inline-flex items-center gap-1 justify-end">
                    <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openEditModal({{ $item->id }})" title="Edit Record">
                      <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="deleteItem({{ $item->id }})" title="Delete Record">
                      <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="10" class="px-6 py-12 text-center text-slate-400">
                  <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>
                  No companies found. Click "+ Add Company" to create one.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination & Rows Controls -->
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

<!-- Add / Edit Modal -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="crud-modal">
  <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
      <h3 class="text-base font-bold text-slate-800 flex items-center gap-2.5">
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
          <i class="fa-solid fa-building"></i>
        </span>
        <span id="modal-title">Add Company</span>
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center cursor-pointer text-base" onclick="closeModal('crud-modal')">&times;</button>
    </div>
    <form id="crud-form" onsubmit="handleFormSubmit(event)" class="flex flex-col flex-1 overflow-hidden">
      <input type="hidden" id="item-id">
      <div class="p-6 space-y-4 overflow-y-auto bg-slate-50/50">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Company Name <span class="text-rose-500">*</span></label>
          <input type="text" id="form-name" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. Tata Consultancy Services, Infosys Ltd" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">GSTIN Number</label>
            <input type="text" id="form-gstin" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all uppercase" placeholder="e.g. 19ABCDE1234F1Z5">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Phone Number</label>
            <input type="text" id="form-phone" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. +91 98765 43210">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Office Address</label>
          <textarea id="form-address" class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" rows="2" placeholder="e.g. Plot No 4, Sector V, Salt Lake City, Kolkata, WB"></textarea>
        </div>

        <!-- Discount Configuration (Percentage / Solid Amount) -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-3 shadow-xs">
          <label class="block text-xs font-bold text-slate-700">Corporate Discount Type <span class="text-rose-500">*</span></label>
          <div class="flex items-center gap-6">
            <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
              <input type="radio" name="form_discount_type" id="radio-discount-percentage" value="percentage" checked onchange="handleDiscountTypeChange('percentage')" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 cursor-pointer">
              <span class="flex items-center gap-1.5"><i class="fa-solid fa-percent text-indigo-600 text-[11px]"></i> Percentage (%)</span>
            </label>
            <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
              <input type="radio" name="form_discount_type" id="radio-discount-amount" value="amount" onchange="handleDiscountTypeChange('amount')" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
              <span class="flex items-center gap-1.5"><i class="fa-solid fa-indian-rupee-sign text-emerald-600 text-[11px]"></i> Solid Amount (₹)</span>
            </label>
          </div>

          <!-- Dynamic Input: Percentage -->
          <div id="container-discount-percentage" class="space-y-1">
            <label class="block text-[11px] font-semibold text-slate-500">Discount Percentage (%)</label>
            <div class="relative">
              <input type="number" id="form-discount-percentage" class="w-full h-9.5 pr-8 pl-3.5 rounded-xl border border-slate-200 bg-white text-xs font-bold font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. 10.00" min="0" max="100" step="any">
              <span class="absolute right-3.5 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-xs">%</span>
            </div>
            <p class="text-[11px] text-slate-400">Specify applicable percentage discount (0 to 100%).</p>
          </div>

          <!-- Dynamic Input: Solid Amount -->
          <div id="container-discount-amount" class="space-y-1 hidden">
            <label class="block text-[11px] font-semibold text-slate-500">Solid Discount Amount (₹)</label>
            <div class="relative">
              <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-emerald-600 text-xs">₹</span>
              <input type="number" id="form-discount-amount" class="w-full h-9.5 pl-8 pr-3.5 rounded-xl border border-slate-200 bg-white text-xs font-bold font-mono text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all" placeholder="e.g. 500.00" min="0" step="any">
            </div>
            <p class="text-[11px] text-slate-400">Specify flat solid amount deduction per reservation in INR.</p>
          </div>
        </div>

        <!-- Meal Plan Discounts Configuration -->
        <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-3 shadow-xs">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                <i class="fa-solid fa-utensils text-amber-500 text-xs"></i>
                <span>Meal Plan Discounts</span>
              </label>
              <p class="text-[11px] text-slate-400">Configure specific corporate meal plan discounts.</p>
            </div>
            <button type="button" class="h-8 px-3 rounded-lg border border-indigo-200 bg-indigo-50/50 hover:bg-indigo-50 text-indigo-700 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer self-start sm:self-auto" onclick="addMealDiscountRow()">
              <i class="fa-solid fa-plus text-[10px]"></i> Add Meal Discount
            </button>
          </div>

          <div id="meal-discounts-container" class="space-y-2">
            <!-- Dynamic rows will be rendered here -->
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1.5">Status <span class="text-rose-500">*</span></label>
          <select id="form-status" class="w-full h-9.5 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer" required>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="px-6 py-3.5 bg-white border-t border-slate-100 flex justify-end items-center gap-2.5">
        <button type="button" class="h-9.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all cursor-pointer" onclick="closeModal('crud-modal')">Cancel</button>
        <button type="submit" class="h-9.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-xs shadow-indigo-200 transition-all flex items-center gap-2 cursor-pointer" id="btn-save">
          <i class="fa-solid fa-floppy-disk text-xs"></i> Save Company
        </button>
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
      pctWrap.classList.add('hidden');
      amtWrap.classList.remove('hidden');
    } else {
      pctWrap.classList.remove('hidden');
      amtWrap.classList.add('hidden');
    }
  }

  /* Dynamic Meal Discount Rows Management */
  function addMealDiscountRow(selectedPlanId = '', discountType = 'percentage', discountValue = '') {
    mealRowCounter++;
    const rowId = mealRowCounter;
    const container = document.getElementById('meal-discounts-container');

    const row = document.createElement('div');
    row.className = 'meal-discount-row flex items-center gap-2 p-2 bg-slate-50 border border-slate-200/80 rounded-xl';
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
      <div class="flex-1 min-w-[140px]">
        <select class="admin-form-input meal-plan-select h-9 px-3 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-800 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
          ${planOptions}
        </select>
      </div>
      <div class="w-36">
        <select class="admin-form-input meal-discount-type h-9 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-700 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" onchange="updateMealDiscountRowType(this)">
          <option value="percentage" ${!isAmount ? 'selected' : ''}>Percentage (%)</option>
          <option value="amount" ${isAmount ? 'selected' : ''}>Amount (₹)</option>
        </select>
      </div>
      <div class="w-28 relative">
        <input type="number" class="admin-form-input meal-discount-val h-9 pr-6 pl-2.5 rounded-lg border border-slate-200 bg-white text-xs font-bold font-mono text-slate-800 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" placeholder="${!isAmount ? '10' : '100'}" value="${valStr}" min="0" ${!isAmount ? 'max="100"' : ''} step="any">
        <span class="meal-val-suffix absolute right-2.5 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-xs">${!isAmount ? '%' : '₹'}</span>
      </div>
      <div>
        <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-all cursor-pointer" onclick="removeMealDiscountRow('${rowId}')" title="Remove or Clear Meal Discount">
          <i class="fa-solid fa-xmark text-sm"></i>
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
      valInput.placeholder = isPercent ? '10' : '100';
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
    addMealDiscountRow();
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

    // Populate Meal Discounts
    resetMealDiscounts();
    if (Array.isArray(item.meal_discounts) && item.meal_discounts.length > 0) {
      item.meal_discounts.forEach(md => {
        addMealDiscountRow(md.meal_plan_id, md.discount_type, md.discount_value);
      });
    } else {
      addMealDiscountRow();
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

      // Skip row if untouched
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
      tbody.innerHTML = `<tr><td colspan="10" class="px-6 py-12 text-center text-slate-400"><i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300 block"></i>No matching companies found.</td></tr>`;
      updateBulkActionUI();
    } else {
      let html = '';
      pageItems.forEach((item, idx) => {
        const rowNumber = startIdx + idx + 1;
        const dVal = parseFloat(item.discount_value) || 0;
        let discountHtml = '<span class="text-slate-400 text-[11px]">0% (None)</span>';
        if (dVal > 0) {
          if (item.discount_type === 'amount') {
            discountHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[11px] font-bold"><i class="fa-solid fa-indian-rupee-sign text-[9px]"></i> ${dVal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
          } else {
            discountHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200/60 font-mono text-[11px] font-bold"><i class="fa-solid fa-percent text-[9px]"></i> ${dVal}%</span>`;
          }
        }

        // Meal Discounts HTML
        let mealDiscountsHtml = '<span class="text-slate-400 text-[11px]">-</span>';
        if (Array.isArray(item.meal_discounts) && item.meal_discounts.length > 0) {
          mealDiscountsHtml = '<div class="flex flex-col gap-1">' +
            item.meal_discounts.map(md => {
              const mPlan = (availableMealPlans || []).find(p => Number(p.id) === Number(md.meal_plan_id));
              const pName = mPlan ? escapeHtml(mPlan.plan_name) : `Plan #${md.meal_plan_id}`;
              const mVal = parseFloat(md.discount_value) || 0;
              const badge = md.discount_type === 'amount' 
                ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold">₹ ${mVal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`
                : `<span class="inline-flex items-center px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200/60 font-mono text-[10px] font-bold">${mVal}%</span>`;
              return `<div class="inline-flex items-center gap-1.5 text-[11px]"><span class="font-bold text-slate-600">${pName}:</span> ${badge}</div>`;
            }).join('') +
          '</div>';
        }

        const statusBadge = item.status === 'Active'
          ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active</span>'
          : '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Inactive</span>';

        html += `
          <tr class="hover:bg-slate-50/70 transition-colors" data-id="${item.id}">
            <td class="px-4 py-3 text-center">
              <input type="checkbox" class="row-select-check cursor-pointer w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300" value="${item.id}" ${selectedIds.has(String(item.id)) ? 'checked' : ''} onchange="handleRowSelect(this, '${item.id}')">
            </td>
            <td class="px-3 py-3 font-mono font-bold text-slate-400 text-[11px]">${rowNumber}</td>
            <td class="px-4 py-3 font-bold text-slate-800">
              <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                  <i class="fa-solid fa-building text-xs"></i>
                </span>
                <span class="font-bold text-slate-800">${escapeHtml(item.name)}</span>
              </div>
            </td>
            <td class="px-4 py-3">
              ${item.gstin ? `<span class="inline-flex items-center px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 border border-sky-200/60 font-mono text-[11px] font-bold">${escapeHtml(item.gstin)}</span>` : '<span class="text-slate-400 text-[11px]">-</span>'}
            </td>
            <td class="px-4 py-3 font-semibold text-slate-600 text-xs">
              ${item.phone ? `<span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-phone text-[10px] text-slate-400"></i> ${escapeHtml(item.phone)}</span>` : '<span class="text-slate-400">-</span>'}
            </td>
            <td class="px-4 py-3 text-slate-500 max-w-[200px] truncate" title="${escapeHtml(item.address || '')}">
              ${escapeHtml(item.address || '-')}
            </td>
            <td class="px-4 py-3">
              ${discountHtml}
            </td>
            <td class="px-4 py-3">
              ${mealDiscountsHtml}
            </td>
            <td class="px-4 py-3">
              ${statusBadge}
            </td>
            <td class="px-4 py-3 text-right">
              <div class="inline-flex items-center gap-1 justify-end">
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="openEditModal(${item.id})" title="Edit Record">
                  <i class="fa-solid fa-pen text-xs"></i>
                </button>
                <button type="button" class="w-7 h-7 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 transition-all flex items-center justify-center cursor-pointer" onclick="deleteItem(${item.id})" title="Delete Record">
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
