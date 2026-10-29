@extends('frontoffice.layouts.app')

@section('title', 'Restaurant Store & Menu Management - Hotel Sagar Sonnet')


@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">

  <!-- 1. Top KPI Summary Strip -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    <!-- Total Plates Ready -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Plates Ready</span>
        <div class="text-2xl font-black font-mono text-emerald-600 mt-1">{{ number_format($totalPlatesAvailable) }}</div>
        <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-utensils text-emerald-500 text-[10px]"></i> Kitchen Stock
        </div>
      </div>
      <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-utensils"></i>
      </div>
    </div>

    <!-- Total Food Dishes -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Food Dishes</span>
        <div class="text-2xl font-black font-mono text-cyan-600 mt-1">{{ $totalItemsCount }}</div>
        <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-bowl-food text-cyan-500 text-[10px]"></i> Active Recipes
        </div>
      </div>
      <div class="w-12 h-12 rounded-xl bg-cyan-50 border border-cyan-200/80 text-cyan-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-bowl-food"></i>
      </div>
    </div>

    <!-- Active Menu Categories -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Menus</span>
        <div class="text-2xl font-black font-mono text-indigo-600 mt-1">{{ $totalMenusCount }}</div>
        <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-layer-group text-indigo-500 text-[10px]"></i> Menu Sections
        </div>
      </div>
      <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-layer-group"></i>
      </div>
    </div>

    <!-- Low Plate Alerts -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Low Plate Alerts</span>
        <div class="text-2xl font-black font-mono {{ $lowStockCount > 0 ? 'text-amber-600' : 'text-emerald-600' }} mt-1">{{ $lowStockCount }}</div>
        <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-triangle-exclamation text-amber-500 text-[10px]"></i> Needs Inward
        </div>
      </div>
      <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </div>
    </div>

    <!-- Sold Out Dishes -->
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
      <div>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sold Out Dishes</span>
        <div class="text-2xl font-black font-mono {{ $outOfStockCount > 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-1">{{ $outOfStockCount }}</div>
        <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
          <i class="fa-solid fa-circle-xmark text-rose-500 text-[10px]"></i> 0 Plates Available
        </div>
      </div>
      <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-circle-xmark"></i>
      </div>
    </div>
  </div>

  <!-- 2. Main Store & Menu Manager Container -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    
    <!-- Header with Action Buttons -->
    <div class="bg-slate-50/80 px-6 py-5 border-b border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center text-base shrink-0">
            <i class="fa-solid fa-kitchen-set"></i>
          </div>
          <h2 class="text-base font-extrabold text-slate-900">Restaurant Store &amp; POS Menu Manager</h2>
        </div>
        <p class="text-xs text-slate-500 font-medium mt-1">POS Menus, Dishes, Ingredients &amp; Plate Stock ("Kota Plate Hobe") are managed here.</p>
      </div>

      <div class="flex items-center gap-2.5 flex-wrap">
        <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs" title="Open POS Terminal">
          <i class="fa-solid fa-desktop text-indigo-600"></i> Open POS Terminal
        </a>
        <button type="button" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="openMenuCategoryModal()">
          <i class="fa-solid fa-folder-plus text-cyan-600"></i> + New Menu
        </button>
        <button type="button" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="openInwardModal()">
          <i class="fa-solid fa-truck-ramp-box text-amber-600"></i> + Inward Stock
        </button>
        <button type="button" class="inline-flex items-center gap-1.5 h-9.5 px-4 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-xs cursor-pointer" onclick="openFoodItemModal()">
          <i class="fa-solid fa-circle-plus"></i> + Add Food Item
        </button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="px-6 pt-4 border-b border-slate-200/80 bg-white">
      <div class="flex items-center gap-2 overflow-x-auto pb-3">
        <button type="button" class="stores-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap bg-emerald-50 text-emerald-700 border border-emerald-200/80" id="tab-btn-dishes" onclick="switchStoresTab('dishes')">
          <i class="fa-solid fa-utensils text-[11px]"></i> Menu Dishes &amp; Plate Stock ({{ $totalItemsCount }})
        </button>
        <button type="button" class="stores-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all cursor-pointer whitespace-nowrap" id="tab-btn-categories" onclick="switchStoresTab('categories')">
          <i class="fa-solid fa-layer-group text-[11px]"></i> Menu Categories ({{ $totalMenusCount }})
        </button>
        <button type="button" class="stores-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all cursor-pointer whitespace-nowrap" id="tab-btn-movements" onclick="switchStoresTab('movements')">
          <i class="fa-solid fa-clock-rotate-left text-[11px]"></i> Kitchen Stock Movements
        </button>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: MENU DISHES & PLATE STOCK (DEFAULT) -->
    <!-- ========================================== -->
    <div id="tab-pane-dishes">
      
      <!-- Filter & Search Ribbon -->
      <div class="p-5 border-b border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Category Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 max-w-full">
          <a href="{{ route('stores.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ !request('category') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 bg-slate-100 hover:bg-slate-200' }}">
            <i class="fa-solid fa-border-all text-[10px]"></i> All Menus
          </a>
          @foreach($categories as $cat)
            <a href="{{ route('stores.index', ['category' => $cat->name, 'stock_status' => request('stock_status')]) }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ request('category') == $cat->name ? 'bg-emerald-600 text-white shadow-xs font-bold' : 'text-slate-600 bg-slate-100 hover:bg-slate-200' }}">
              {{ $cat->name }} ({{ $cat->pos_items_count }})
            </a>
          @endforeach
        </div>

        <!-- Search & Status Selectors -->
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
          <select class="h-9 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:outline-none focus:border-indigo-500 px-3 cursor-pointer" onchange="filterStockStatus(this.value)">
            <option value="all" {{ !request('stock_status') || request('stock_status') === 'all' ? 'selected' : '' }}>All Stock Status</option>
            <option value="optimal" {{ request('stock_status') === 'optimal' ? 'selected' : '' }}>Optimal Stock</option>
            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock Alert</option>
            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
          </select>

          <form action="{{ route('stores.index') }}" method="GET" class="flex items-center gap-1.5">
            @if(request('category'))
              <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('stock_status'))
              <input type="hidden" name="stock_status" value="{{ request('stock_status') }}">
            @endif
            <div class="relative">
              <input type="text" name="search" placeholder="Search dish, ingredient..." value="{{ request('search') }}" class="h-9 pl-3 pr-8 text-xs font-medium rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:outline-none focus:border-indigo-500 w-48">
              <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 cursor-pointer text-xs"><i class="fa-solid fa-search"></i></button>
            </div>
          </form>
        </div>
      </div>

      <!-- Dishes Table with Ingredients and "Kota Plate Hobe" -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
              <th class="px-3.5 py-3 text-center w-12">Icon</th>
              <th class="px-3.5 py-3">Food Dish / Item</th>
              <th class="px-3.5 py-3">Menu Category</th>
              <th class="px-3.5 py-3">Price</th>
              <th class="px-3.5 py-3">Ingredient &amp; Recipe Yield</th>
              <th class="px-3.5 py-3">Raw Kitchen Stock</th>
              <th class="px-3.5 py-3">Available Plates ("Kota Plate Hobe")</th>
              <th class="px-3.5 py-3 text-center">Status</th>
              <th class="px-3.5 py-3 text-right w-44">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($items as $dish)
              @php
                $plates = $dish->available_plates;
                $isLow = ($plates <= ($dish->min_alert_plates ?: 10) && $plates > 0);
                $isOut = ($plates <= 0);
              @endphp
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-3.5 py-3 text-2xl text-center">
                  {{ $dish->emoji ?: '🍽️' }}
                </td>
                <td class="px-3.5 py-3">
                  <strong class="text-slate-900 text-xs block">{{ $dish->name }}</strong>
                  @if($dish->code)
                    <span class="font-mono text-[10px] text-cyan-600 block mt-0.5">{{ $dish->code }}</span>
                  @endif
                </td>
                <td class="px-3.5 py-3">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">{{ $dish->category }}</span>
                </td>
                <td class="px-3.5 py-3">
                  <span class="font-mono font-bold text-xs text-slate-900">₹ {{ number_format($dish->price, 2) }}</span>
                  @if($dish->last_purchase_price)
                    <span class="text-[10px] text-slate-400 block mt-0.5" title="Last Inward Cost">Last: ₹{{ number_format($dish->last_purchase_price, 2) }}</span>
                  @endif
                </td>
                <td class="px-3.5 py-3">
                  <div class="bg-slate-50/70 border border-slate-200/80 rounded-lg p-2 max-w-[220px]">
                    <div class="font-bold text-slate-800 flex items-center gap-1.5 truncate">
                      <i class="fa-solid fa-mortar-pestle text-emerald-600 text-[10px]"></i> {{ $dish->ingredient_name ?: 'Not configured' }}
                    </div>
                    <div class="text-[10px] text-slate-500 mt-0.5">
                      Per Plate: <strong class="text-slate-800">{{ $dish->per_plate_qty + 0 }} {{ $dish->per_plate_unit }}</strong> / serving
                    </div>
                  </div>
                </td>
                <td class="px-3.5 py-3 font-mono font-bold text-xs text-slate-700">
                  {{ $dish->raw_stock_qty + 0 }} {{ $dish->raw_stock_unit }}
                </td>
                <td class="px-3.5 py-3">
                  <div>
                    @if($isOut)
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> 0 Plates (Sold Out)
                      </span>
                    @elseif($isLow)
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Low: {{ $plates }} Plates
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                        <i class="fa-solid fa-circle-check text-[9px]"></i> {{ $plates }} Plates Ready
                      </span>
                    @endif
                  </div>
                  <span class="text-[10px] text-slate-400 block mt-1">Min Alert: {{ $dish->min_alert_plates ?: 10 }} plates</span>
                </td>
                <td class="px-3.5 py-3 text-center">
                  @if($dish->status === 'Active' && !$isOut)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">Active</span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">Out of Stock</span>
                  @endif
                </td>
                <td class="px-3.5 py-3 text-right">
                  <div class="inline-flex items-center gap-1 justify-end">
                    <button type="button" class="inline-flex items-center gap-1 h-7 px-2 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/80 rounded-lg transition-all cursor-pointer" onclick="quickInwardForItem({{ $dish->id }}, '{{ addslashes($dish->name) }}', '{{ addslashes($dish->ingredient_name) }}', '{{ $dish->raw_stock_unit }}', {{ $dish->per_plate_qty }}, '{{ $dish->per_plate_unit }}')" title="Inward Raw Stock">
                      <i class="fa-solid fa-plus text-[10px]"></i> Inward
                    </button>
                    <button type="button" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all cursor-pointer" onclick="editFoodItem({{ json_encode($dish) }})" title="Edit Dish & Recipe">
                      <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <button type="button" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer" onclick="deleteFoodItem({{ $dish->id }}, '{{ addslashes($dish->name) }}')" title="Delete Dish">
                      <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-slate-400 py-12 text-xs">
                  <i class="fa-solid fa-utensils text-4xl mb-3 text-slate-300 block"></i>
                  No dishes found in menu catalog. Click <strong>"+ Add Food Item"</strong> to create one.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: MENU CATEGORIES                     -->
    <!-- ========================================== -->
    <div id="tab-pane-categories" class="hidden">
      <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
        <div>
          <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Menu Sections</h4>
          <p class="text-xs text-slate-500 font-medium mt-0.5">Categories appear as tabs in the POS terminal for ordering.</p>
        </div>
        <button type="button" class="inline-flex items-center gap-1.5 h-8 px-3.5 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-700 rounded-xl transition-all shadow-xs cursor-pointer" onclick="openMenuCategoryModal()">
          <i class="fa-solid fa-circle-plus"></i> + Create New Menu
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
              <th class="px-3.5 py-3 w-28">Code</th>
              <th class="px-3.5 py-3">Menu Name</th>
              <th class="px-3.5 py-3">Total Dishes Inside</th>
              <th class="px-3.5 py-3">Description</th>
              <th class="px-3.5 py-3 text-center">Status</th>
              <th class="px-3.5 py-3 text-right w-36">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($categories as $cat)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-3.5 py-3 font-mono font-bold text-cyan-700">
                  {{ $cat->code ?: 'CAT-' . $cat->id }}
                </td>
                <td class="px-3.5 py-3">
                  <strong class="text-slate-900 text-xs">{{ $cat->name }}</strong>
                </td>
                <td class="px-3.5 py-3">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">{{ $cat->pos_items_count }} Dishes</span>
                </td>
                <td class="px-3.5 py-3 text-slate-500">
                  {{ $cat->description ?: 'No description' }}
                </td>
                <td class="px-3.5 py-3 text-center">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cat->status === 'Active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' : 'bg-rose-50 text-rose-700 border border-rose-200/80' }}">{{ $cat->status }}</span>
                </td>
                <td class="px-3.5 py-3 text-right">
                  <div class="inline-flex items-center gap-1.5 justify-end">
                    <button type="button" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all cursor-pointer" onclick="editMenuCategory({{ json_encode($cat) }})" title="Edit Menu Category">
                      <i class="fa-solid fa-pen text-xs"></i>
                    </button>
                    <button type="button" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-all cursor-pointer" onclick="deleteMenuCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')" title="Delete Menu Category">
                      <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-slate-400 py-10 text-xs">
                  No menus created yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: STOCK MOVEMENTS LOG                 -->
    <!-- ========================================== -->
    <div id="tab-pane-movements" class="hidden">
      <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200/80">
        <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Kitchen Inventory Audit Trail</h4>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Live records of inward receiving and automatic POS order plate deductions.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
              <th class="px-3.5 py-3">Date &amp; Time</th>
              <th class="px-3.5 py-3">Type</th>
              <th class="px-3.5 py-3">Dish / Provision</th>
              <th class="px-3.5 py-3">Quantity / Plates</th>
              <th class="px-3.5 py-3">Stock Before &rarr; After</th>
              <th class="px-3.5 py-3">Reference No</th>
              <th class="px-3.5 py-3">Performed By / Remarks</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($movements as $mov)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-3.5 py-3 font-mono text-slate-500 text-xs">
                  {{ $mov->created_at->format('d M Y, h:i A') }}
                </td>
                <td class="px-3.5 py-3">
                  @if($mov->type === 'IN')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80"><i class="fa-solid fa-arrow-down text-[9px]"></i> INWARD (+)</span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80"><i class="fa-solid fa-arrow-up text-[9px]"></i> POS SALE (-)</span>
                  @endif
                </td>
                <td class="px-3.5 py-3">
                  <strong class="text-slate-800 text-xs">{{ $mov->posItem?->name ?? $mov->item?->name ?? 'Dish' }}</strong>
                </td>
                <td class="px-3.5 py-3 font-mono font-bold text-xs {{ $mov->type === 'IN' ? 'text-emerald-600' : 'text-rose-600' }}">
                  {{ $mov->type === 'IN' ? '+' : '-' }}{{ $mov->quantity }} Plates
                  @if($mov->total_cost > 0)
                    <span class="text-[10px] text-cyan-600 block font-bold">Cost: ₹{{ number_format($mov->total_cost, 2) }}</span>
                  @endif
                </td>
                <td class="px-3.5 py-3 font-mono text-slate-500 text-xs">
                  {{ $mov->stock_before }} &rarr; <strong class="text-slate-800 font-bold">{{ $mov->stock_after }}</strong>
                </td>
                <td class="px-3.5 py-3 font-mono text-xs text-cyan-600 font-bold">
                  {{ $mov->reference_no }}
                </td>
                <td class="px-3.5 py-3 text-slate-600">
                  {{ $mov->remarks }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-slate-400 py-10 text-xs">
                  No stock movements recorded yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
  </div>
</main>

<!-- ======================================================== -->
<!-- MODAL 1: ADD / EDIT FOOD ITEM WITH INGREDIENT & YIELD    -->
<!-- ======================================================== -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="modal-food-item">
  <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
      <h3 id="modal-item-title" class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
        <i class="fa-solid fa-circle-plus text-emerald-600"></i> Add Food Dish &amp; Ingredient Recipe
      </h3>
      <button type="button" onclick="closeFoodItemModal()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-base cursor-pointer transition-colors">&times;</button>
    </div>
    <div class="p-6 overflow-y-auto flex-1">
      <form id="form-food-item" onsubmit="submitFoodItem(event)" class="space-y-4">
        <input type="hidden" id="item-id">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Dish Name *</label>
            <input type="text" id="item-name" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" required placeholder="e.g. Mughlai Butter Chicken">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Emoji Icon</label>
            <input type="text" id="item-emoji" class="h-9.5 px-3 text-base text-center rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="🍗" value="🍽️">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Menu Category *</label>
            <select id="item-category" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full cursor-pointer" required>
              <option value="" disabled selected>-- Select Category --</option>
              @foreach($categories as $c)
                <option value="{{ $c->name }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Selling Price (₹) *</label>
            <input type="number" id="item-price" class="h-9.5 px-3 text-xs font-mono font-bold rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" required min="0" step="any" placeholder="450">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Dish Code / SKU</label>
            <input type="text" id="item-code" class="h-9.5 px-3 text-xs font-mono rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="MC-01">
          </div>
        </div>

        <!-- Recipe & Plate Yield Configuration Box -->
        <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4 space-y-3">
          <div class="text-xs font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2 pb-2 border-b border-slate-200/60">
            <i class="fa-solid fa-mortar-pestle text-emerald-600"></i> Ingredient Recipe &amp; Plate Yield ("Kota Plate Hobe")
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Main Ingredient Name *</label>
            <input type="text" id="item-ingredient-name" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" required placeholder="e.g. Boneless Chicken, Fresh Paneer, Basmati Rice">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Raw Stock in Kitchen *</label>
              <input type="number" id="item-raw-qty" class="h-9.5 px-3 text-xs font-mono rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" required min="0" step="any" placeholder="20" oninput="autoCalculatePlates()">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Raw Stock Unit *</label>
              <select id="item-raw-unit" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full cursor-pointer" required onchange="autoCalculatePlates()">
                <option value="Kg" selected>Kg</option>
                <option value="Liters">Liters</option>
                <option value="Pcs">Pcs</option>
                <option value="Bottles">Bottles</option>
                <option value="Packets">Packets</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Per Plate Requirement *</label>
              <input type="number" id="item-per-plate-qty" class="h-9.5 px-3 text-xs font-mono rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" required min="0.001" step="any" placeholder="250" oninput="autoCalculatePlates()">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Per Plate Unit *</label>
              <select id="item-per-plate-unit" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full cursor-pointer" required onchange="autoCalculatePlates()">
                <option value="gm" selected>gm</option>
                <option value="ml">ml</option>
                <option value="pcs">pcs</option>
                <option value="scoops">scoops</option>
                <option value="slices">slices</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-200/80">
            <div>
              <label class="block text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">Available Plates (Stock for POS) *</label>
              <input type="number" id="item-available-plates" class="h-9.5 px-3 text-sm font-black font-mono text-emerald-700 rounded-xl border border-emerald-300 bg-emerald-50/50 focus:outline-none focus:border-emerald-500 w-full" required min="0" step="1" placeholder="80">
              <span id="plate-calc-hint" class="text-[10px] text-slate-500 block mt-1">Auto-calculated from raw stock</span>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Low Stock Alert Below *</label>
              <input type="number" id="item-min-alert" class="h-9.5 px-3 text-xs font-mono rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" required min="0" step="1" value="10" placeholder="10">
            </div>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Description (Optional)</label>
          <textarea id="item-desc" class="p-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" rows="2" placeholder="Brief preparation notes..."></textarea>
        </div>

        <div class="flex justify-end items-center gap-2 pt-2 border-t border-slate-200">
          <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeFoodItemModal()">Cancel</button>
          <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-xs cursor-pointer" id="btn-save-item"><i class="fa-solid fa-save"></i> Save Food Item</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 2: CREATE / EDIT MENU CATEGORY                     -->
<!-- ======================================================== -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="modal-menu-category">
  <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
      <h3 id="modal-cat-title" class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
        <i class="fa-solid fa-folder-plus text-cyan-600"></i> Create Menu Category
      </h3>
      <button type="button" onclick="closeMenuCategoryModal()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-base cursor-pointer transition-colors">&times;</button>
    </div>
    <div class="p-6">
      <form id="form-menu-category" onsubmit="submitMenuCategory(event)" class="space-y-4">
        <input type="hidden" id="cat-id">

        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Menu Name *</label>
          <input type="text" id="cat-name" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" required placeholder="e.g. Biryani &amp; Rice, Tandoori Breads, Soups">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Short Code (Optional)</label>
          <input type="text" id="cat-code" class="h-9.5 px-3 text-xs font-mono rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="e.g. BIR, BREAD, SOUP">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Description (Optional)</label>
          <textarea id="cat-desc" class="p-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" rows="2" placeholder="Brief notes about this menu section..."></textarea>
        </div>

        <div class="flex justify-end items-center gap-2 pt-3 border-t border-slate-200">
          <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeMenuCategoryModal()">Cancel</button>
          <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer"><i class="fa-solid fa-save"></i> Save Menu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 3: INWARD STOCK RECEIVING ("ITEM DUKLO")           -->
<!-- ======================================================== -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="modal-inward">
  <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
    <div class="bg-slate-50/80 px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
      <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
        <i class="fa-solid fa-truck-ramp-box text-amber-600"></i> Inward Stock Receiving ("Item Duklo")
      </h3>
      <button type="button" onclick="closeInwardModal()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center text-base cursor-pointer transition-colors">&times;</button>
    </div>
    <div class="p-6">
      <form id="form-inward" onsubmit="submitInwardStock(event)" class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Select Dish / Food Item *</label>
          <select id="inward-item-id" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full cursor-pointer" required onchange="onInwardDishSelected()">
            <option value="">-- Choose Food Dish --</option>
            @foreach($items as $dish)
              <option value="{{ $dish->id }}" 
                      data-name="{{ $dish->name }}" 
                      data-ing="{{ $dish->ingredient_name }}" 
                      data-rawunit="{{ $dish->raw_stock_unit }}" 
                      data-perplate="{{ $dish->per_plate_qty }}" 
                      data-plateunit="{{ $dish->per_plate_unit }}">
                {{ $dish->emoji }} {{ $dish->name }} ({{ $dish->ingredient_name ?: 'No ingredient' }} - Current: {{ $dish->available_plates }} plates)
              </option>
            @endforeach
          </select>
        </div>

        <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4 space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1" id="lbl-inward-raw">Raw Inward Quantity *</label>
              <input type="number" id="inward-raw-qty" class="h-9.5 px-3 text-xs font-mono rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" min="0" step="any" placeholder="e.g. 10" oninput="calculateInwardPlates()">
            </div>
            <div>
              <label class="block text-xs font-bold text-emerald-700 uppercase tracking-wider mb-1">Plates Added (+ Plates) *</label>
              <input type="number" id="inward-plates" class="h-9.5 px-3 text-sm font-black font-mono text-emerald-700 rounded-xl border border-emerald-300 bg-emerald-50/50 focus:outline-none focus:border-emerald-500 w-full" required min="1" step="1" placeholder="e.g. 40">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">
              Purchase Price (₹) <span class="text-[10px] text-slate-400 font-normal">(Optional - item expense)</span>
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-xs">₹</span>
              <input type="number" id="inward-purchase-price" class="h-9.5 pl-7 pr-3 text-xs font-mono rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" min="0" step="any" placeholder="e.g. 240 (Optional)" oninput="calculateInwardPlates()">
            </div>
          </div>

          <span id="inward-yield-calc" class="text-[11px] text-slate-500 block pt-1">Select a dish to see recipe yield.</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Vendor / Supplier Name</label>
            <input type="text" id="inward-vendor" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="e.g. Fresh Chicken Mart">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Invoice / Challan No</label>
            <input type="text" id="inward-invoice" class="h-9.5 px-3 text-xs font-mono rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="INV-2026-901">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Remarks (Optional)</label>
          <input type="text" id="inward-remarks" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="Morning delivery fresh stock received...">
        </div>

        <div class="flex justify-end items-center gap-2 pt-3 border-t border-slate-200">
          <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeInwardModal()">Cancel</button>
          <button type="submit" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-all shadow-xs cursor-pointer"><i class="fa-solid fa-check"></i> Replenish Stock</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  // ==========================================
  // TAB SWITCHING LOGIC
  // ==========================================
  function switchStoresTab(tabKey) {
    const tabs = [
      { key: 'dishes', btn: document.getElementById('tab-btn-dishes'), pane: document.getElementById('tab-pane-dishes') },
      { key: 'categories', btn: document.getElementById('tab-btn-categories'), pane: document.getElementById('tab-pane-categories') },
      { key: 'movements', btn: document.getElementById('tab-btn-movements'), pane: document.getElementById('tab-pane-movements') }
    ];

    tabs.forEach(t => {
      if (!t.btn || !t.pane) return;
      const isActive = (t.key === tabKey);
      if (isActive) {
        t.pane.classList.remove('hidden');
        t.btn.className = 'stores-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-xs';
      } else {
        t.pane.classList.add('hidden');
        t.btn.className = 'stores-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all cursor-pointer whitespace-nowrap shadow-xs';
      }
    });
  }

  function filterStockStatus(status) {
    const url = new URL(window.location.href);
    if (status && status !== 'all') {
      url.searchParams.set('stock_status', status);
    } else {
      url.searchParams.delete('stock_status');
    }
    window.location.href = url.toString();
  }

  // ==========================================
  // FOOD ITEM MODAL & AUTO PLATE CALCULATION
  // ==========================================
  function openFoodItemModal() {
    document.getElementById('modal-item-title').innerHTML = '<i class="fa-solid fa-plus-circle text-emerald-500"></i> Add Food Dish &amp; Ingredient Recipe';
    document.getElementById('form-food-item').reset();
    document.getElementById('item-id').value = '';
    document.getElementById('item-emoji').value = '🍽️';
    document.getElementById('item-min-alert').value = '10';
    document.getElementById('modal-food-item').classList.remove('hidden');
  }

  function closeFoodItemModal() {
    document.getElementById('modal-food-item').classList.add('hidden');
  }

  function autoCalculatePlates() {
    const rawQty = parseFloat(document.getElementById('item-raw-qty').value) || 0;
    const rawUnit = (document.getElementById('item-raw-unit').value || '').toLowerCase();
    const perPlateQty = parseFloat(document.getElementById('item-per-plate-qty').value) || 0;
    const perPlateUnit = (document.getElementById('item-per-plate-unit').value || '').toLowerCase();

    if (rawQty <= 0 || perPlateQty <= 0) return;

    let plates = 0;
    if ((rawUnit === 'kg' || rawUnit === 'kgs') && (perPlateUnit === 'gm' || perPlateUnit === 'g' || perPlateUnit === 'grams')) {
      plates = Math.floor((rawQty * 1000) / perPlateQty);
    } else if ((rawUnit === 'liters' || rawUnit === 'liter' || rawUnit === 'l') && (perPlateUnit === 'ml')) {
      plates = Math.floor((rawQty * 1000) / perPlateQty);
    } else {
      plates = Math.floor(rawQty / perPlateQty);
    }

    document.getElementById('item-available-plates').value = plates;
    document.getElementById('plate-calc-hint').textContent = `Yield: ${rawQty} ${rawUnit.toUpperCase()} yields ~${plates} plates (${perPlateQty} ${perPlateUnit}/plate)`;
  }

  function editFoodItem(dish) {
    document.getElementById('modal-item-title').innerHTML = '<i class="fa-solid fa-pen text-cyan-500"></i> Edit Food Dish &amp; Recipe';
    document.getElementById('item-id').value = dish.id;
    document.getElementById('item-name').value = dish.name;
    document.getElementById('item-emoji').value = dish.emoji || '🍽️';
    document.getElementById('item-category').value = dish.category;
    document.getElementById('item-price').value = dish.price;
    document.getElementById('item-code').value = dish.code || '';
    document.getElementById('item-desc').value = dish.description || '';
    
    document.getElementById('item-ingredient-name').value = dish.ingredient_name || '';
    document.getElementById('item-raw-qty').value = parseFloat(dish.raw_stock_qty) || 0;
    document.getElementById('item-raw-unit').value = dish.raw_stock_unit || 'Kg';
    document.getElementById('item-per-plate-qty').value = parseFloat(dish.per_plate_qty) || 1;
    document.getElementById('item-per-plate-unit').value = dish.per_plate_unit || 'gm';
    document.getElementById('item-available-plates').value = dish.available_plates || 0;
    document.getElementById('item-min-alert').value = dish.min_alert_plates || 10;

    document.getElementById('modal-food-item').classList.remove('hidden');
  }

  async function submitFoodItem(e) {
    e.preventDefault();
    const itemId = document.getElementById('item-id').value;
    const isEdit = !!itemId;

    const payload = {
      name: document.getElementById('item-name').value.trim(),
      emoji: document.getElementById('item-emoji').value.trim() || '🍽️',
      category: document.getElementById('item-category').value,
      price: document.getElementById('item-price').value,
      code: document.getElementById('item-code').value.trim() || null,
      description: document.getElementById('item-desc').value.trim() || null,
      ingredient_name: document.getElementById('item-ingredient-name').value.trim(),
      raw_stock_qty: document.getElementById('item-raw-qty').value,
      raw_stock_unit: document.getElementById('item-raw-unit').value,
      per_plate_qty: document.getElementById('item-per-plate-qty').value,
      per_plate_unit: document.getElementById('item-per-plate-unit').value,
      available_plates: document.getElementById('item-available-plates').value,
      min_alert_plates: document.getElementById('item-min-alert').value,
      status: (parseInt(document.getElementById('item-available-plates').value) > 0) ? 'Active' : 'Out of Stock'
    };

    const url = isEdit ? `/stores/items/${itemId}` : `{{ route('stores.items.store') }}`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        closeFoodItemModal();
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Saved!', text: data.message, timer: 1500, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1200);
      } else {
        alert(data.message || 'Error saving food item.');
      }
    } catch(err) {
      alert('Network error saving food item.');
    }
  }

  async function deleteFoodItem(id, name) {
    if (!confirm(`Are you sure you want to delete dish "${name}" from store and POS?`)) return;
    try {
      const res = await fetch(`/stores/items/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Deleted', text: data.message, timer: 1500, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1000);
      } else {
        alert(data.message || 'Error deleting dish.');
      }
    } catch(err) {
      alert('Network error deleting dish.');
    }
  }

  // ==========================================
  // MENU CATEGORY MODAL
  // ==========================================
  function openMenuCategoryModal() {
    document.getElementById('modal-cat-title').innerHTML = '<i class="fa-solid fa-folder-plus text-cyan-500"></i> Create Menu Category';
    document.getElementById('form-menu-category').reset();
    document.getElementById('cat-id').value = '';
    document.getElementById('modal-menu-category').classList.remove('hidden');
  }

  function closeMenuCategoryModal() {
    document.getElementById('modal-menu-category').classList.add('hidden');
  }

  function editMenuCategory(cat) {
    document.getElementById('modal-cat-title').innerHTML = '<i class="fa-solid fa-pen text-cyan-500"></i> Edit Menu Category';
    document.getElementById('cat-id').value = cat.id;
    document.getElementById('cat-name').value = cat.name;
    document.getElementById('cat-code').value = cat.code || '';
    document.getElementById('cat-desc').value = cat.description || '';
    document.getElementById('modal-menu-category').classList.remove('hidden');
  }

  async function submitMenuCategory(e) {
    e.preventDefault();
    const catId = document.getElementById('cat-id').value;
    const isEdit = !!catId;

    const payload = {
      name: document.getElementById('cat-name').value.trim(),
      code: document.getElementById('cat-code').value.trim() || null,
      description: document.getElementById('cat-desc').value.trim() || null,
      status: 'Active'
    };

    const url = isEdit ? `/stores/categories/${catId}` : `{{ route('stores.categories.store') }}`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
      const res = await fetch(url, {
        method: method,
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        closeMenuCategoryModal();
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Menu Saved!', text: data.message, timer: 1500, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1200);
      } else {
        alert(data.message || 'Error saving menu.');
      }
    } catch(err) {
      alert('Network error saving menu category.');
    }
  }

  async function deleteMenuCategory(id, name) {
    if (!confirm(`Are you sure you want to delete menu category "${name}"?`)) return;
    try {
      const res = await fetch(`/stores/categories/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      if (data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Deleted', text: data.message, timer: 1500, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1000);
      } else {
        alert(data.message || 'Error deleting menu category.');
      }
    } catch(err) {
      alert('Network error deleting menu category.');
    }
  }

  // ==========================================
  // INWARD STOCK MODAL ("ITEM DUKLO")
  // ==========================================
  function openInwardModal() {
    document.getElementById('form-inward').reset();
    document.getElementById('modal-inward').classList.remove('hidden');
  }

  function closeInwardModal() {
    document.getElementById('modal-inward').classList.add('hidden');
  }

  function quickInwardForItem(id, name, ingName, rawUnit, perPlate, plateUnit) {
    document.getElementById('form-inward').reset();
    document.getElementById('inward-item-id').value = id;
    onInwardDishSelected();
    document.getElementById('modal-inward').classList.remove('hidden');
  }

  function onInwardDishSelected() {
    const sel = document.getElementById('inward-item-id');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) {
      document.getElementById('inward-yield-calc').textContent = 'Select a dish to see recipe yield.';
      return;
    }

    const ing = opt.getAttribute('data-ing') || 'Main Ingredient';
    const rawUnit = opt.getAttribute('data-rawunit') || 'Kg';
    const perPlate = parseFloat(opt.getAttribute('data-perplate')) || 1;
    const plateUnit = opt.getAttribute('data-plateunit') || 'gm';

    document.getElementById('lbl-inward-raw').textContent = `Inward Raw Quantity (${rawUnit}) *`;
    document.getElementById('inward-yield-calc').textContent = `Recipe: ${ing} (${perPlate} ${plateUnit} per plate). Entering raw ${rawUnit} auto-computes plates.`;
    calculateInwardPlates();
  }

  function calculateInwardPlates() {
    const sel = document.getElementById('inward-item-id');
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    const rawQty = parseFloat(document.getElementById('inward-raw-qty').value) || 0;
    const rawUnit = (opt.getAttribute('data-rawunit') || 'Kg');
    const rawUnitLower = rawUnit.toLowerCase();
    const perPlate = parseFloat(opt.getAttribute('data-perplate')) || 1;
    const plateUnit = (opt.getAttribute('data-plateunit') || 'gm');
    const plateUnitLower = plateUnit.toLowerCase();
    const ing = opt.getAttribute('data-ing') || 'Main Ingredient';
    const purchasePrice = parseFloat(document.getElementById('inward-purchase-price').value) || 0;

    let plates = 0;
    if (rawQty > 0) {
      if ((rawUnitLower === 'kg' || rawUnitLower === 'kgs') && (plateUnitLower === 'gm' || plateUnitLower === 'g' || plateUnitLower === 'grams')) {
        plates = Math.floor((rawQty * 1000) / perPlate);
      } else if ((rawUnitLower === 'liters' || rawUnitLower === 'liter' || rawUnitLower === 'l') && (plateUnitLower === 'ml')) {
        plates = Math.floor((rawQty * 1000) / perPlate);
      } else {
        plates = Math.floor(rawQty / perPlate);
      }
      document.getElementById('inward-plates').value = plates;
    }

    let yieldText = `Recipe: ${ing} (${perPlate} ${plateUnit} per plate). Entering raw ${rawUnit} auto-computes plates.`;
    if (purchasePrice > 0) {
      if (rawQty > 0) {
        const ratePerUnit = (purchasePrice / rawQty).toFixed(2);
        yieldText += ` • Rate: ₹${ratePerUnit}/${rawUnit}`;
      }
      const currentPlates = parseInt(document.getElementById('inward-plates').value) || plates;
      if (currentPlates > 0) {
        const costPerPlate = (purchasePrice / currentPlates).toFixed(2);
        yieldText += ` (~₹${costPerPlate}/plate)`;
      }
    }
    document.getElementById('inward-yield-calc').textContent = yieldText;
  }

  async function submitInwardStock(e) {
    e.preventDefault();
    const payload = {
      pos_item_id: document.getElementById('inward-item-id').value,
      inward_raw_qty: document.getElementById('inward-raw-qty').value || 0,
      inward_plates: document.getElementById('inward-plates').value,
      purchase_price: document.getElementById('inward-purchase-price').value || null,
      vendor_name: document.getElementById('inward-vendor').value.trim() || null,
      invoice_no: document.getElementById('inward-invoice').value.trim() || null,
      remarks: document.getElementById('inward-remarks').value.trim() || null
    };

    try {
      const res = await fetch("{{ route('stores.inward') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (data.success) {
        closeInwardModal();
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Stock Replenished!', text: data.message, timer: 1800, showConfirmButton: false });
        }
        setTimeout(() => window.location.reload(), 1400);
      } else {
        alert(data.message || 'Error recording inward stock.');
      }
    } catch(err) {
      alert('Network error replenishing stock.');
    }
  }
</script>
@endpush
