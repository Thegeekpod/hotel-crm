@extends('frontoffice.layouts.app')

@section('title', 'POS Sales & Dining Reports - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">

    <!-- 1. Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-linear-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white shadow-xs shrink-0">
            <i class="fa-solid fa-chart-pie text-base"></i>
          </div>
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <h1 class="text-base font-extrabold text-slate-900 tracking-tight">POS Sales &amp; Dining Analytics Report</h1>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                <i class="fa-solid fa-check-circle text-[10px] mr-1"></i> Uniform POS Analytics
              </span>
              @if($dateFilter === 'all')
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                  <i class="fa-solid fa-calendar-days text-[10px] mr-1"></i> All Time Records
                </span>
              @elseif($dateFilter === 'today')
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200">
                  <i class="fa-solid fa-clock text-[10px] mr-1"></i> Today's Report
                </span>
              @elseif($dateFilter === 'yesterday')
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                  <i class="fa-solid fa-clock-rotate-left text-[10px] mr-1"></i> Yesterday
                </span>
              @elseif($dateFilter === 'this_week')
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                  <i class="fa-solid fa-calendar-week text-[10px] mr-1"></i> This Week
                </span>
              @elseif($dateFilter === 'this_month')
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                  <i class="fa-solid fa-calendar text-[10px] mr-1"></i> This Month
                </span>
              @elseif(!empty($startDate) && !empty($endDate))
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                  <i class="fa-solid fa-calendar-range text-[10px] mr-1"></i> {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
              @endif
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
              Comprehensive breakdown of restaurant dining revenue, room service orders, table performances, dish sales, and settlement channels.
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
          <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer">
            <i class="fa-solid fa-utensils"></i> Open POS
          </a>
          <a href="{{ route('pos.orders.index') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer">
            <i class="fa-solid fa-clock-rotate-left text-cyan-600"></i> KOT History
          </a>
          <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer">
            <i class="fa-solid fa-print text-slate-500"></i> Print Report
          </button>
          <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer">
            <i class="fa-solid fa-file-csv text-emerald-600"></i> Export CSV
          </a>
        </div>
      </div>
    </div>

    <!-- 2. Master Filter Bar (Date Wise All, By Table, By Room, Type, Billing) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 sm:p-5">
      <form method="GET" action="{{ route('pos.reports') }}" id="pos-reports-filter-form" class="flex flex-col gap-3.5">
        
        <!-- Row 1: Date Presets & Custom Date Range -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-slate-100">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
              <i class="fa-solid fa-calendar-day text-indigo-600"></i> Date Filter:
            </span>
            <div class="inline-flex items-center bg-slate-100 p-0.5 rounded-xl gap-0.5 flex-wrap">
              <button type="button" onclick="setDateFilter('all')" class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $dateFilter === 'all' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">All Time</button>
              <button type="button" onclick="setDateFilter('today')" class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $dateFilter === 'today' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Today</button>
              <button type="button" onclick="setDateFilter('yesterday')" class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $dateFilter === 'yesterday' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Yesterday</button>
              <button type="button" onclick="setDateFilter('this_week')" class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $dateFilter === 'this_week' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">This Week</button>
              <button type="button" onclick="setDateFilter('this_month')" class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $dateFilter === 'this_month' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">This Month</button>
              <button type="button" onclick="setDateFilter('custom')" class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $dateFilter === 'custom' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">Custom Range</button>
            </div>
            <input type="hidden" name="date_filter" id="date_filter_input" value="{{ $dateFilter }}">
          </div>

          <!-- Custom Dates Picker (Visible or used when custom) -->
          <div class="flex items-center gap-2 flex-wrap {{ $dateFilter === 'custom' ? '' : 'sm:flex hidden opacity-90' }}" id="custom-date-container">
            <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200/90 rounded-xl px-2.5 py-1">
              <span class="text-[11px] font-bold text-slate-500 uppercase">From:</span>
              <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="text-xs font-semibold text-slate-800 bg-transparent focus:outline-none" onchange="markCustomDate()">
            </div>
            <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200/90 rounded-xl px-2.5 py-1">
              <span class="text-[11px] font-bold text-slate-500 uppercase">To:</span>
              <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="text-xs font-semibold text-slate-800 bg-transparent focus:outline-none" onchange="markCustomDate()">
            </div>
          </div>
        </div>

        <!-- Row 2: Selectable Dropdowns (By Table, By Room, Order Type, Billing Mode, Search) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-2.5">
          
          <!-- By Table Filter -->
          <div>
            <label class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
              <i class="fa-solid fa-chair text-purple-600 mr-1"></i> By Table
            </label>
            <select name="table_id" class="w-full h-9.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 px-2.5 focus:outline-none focus:border-indigo-500 shadow-2xs">
              <option value="all" {{ $tableId === 'all' || empty($tableId) ? 'selected' : '' }}>-- All Dining Tables --</option>
              @foreach($tablesList as $tbl)
                <option value="{{ $tbl->id }}" {{ (string)$tableId === (string)$tbl->id ? 'selected' : '' }}>
                  {{ $tbl->table_number }} ({{ $tbl->table_name }})
                </option>
              @endforeach
            </select>
          </div>

          <!-- By Room Filter -->
          <div>
            <label class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
              <i class="fa-solid fa-door-open text-indigo-600 mr-1"></i> By Room
            </label>
            <select name="room_id" class="w-full h-9.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 px-2.5 focus:outline-none focus:border-indigo-500 shadow-2xs">
              <option value="all" {{ $roomId === 'all' || empty($roomId) ? 'selected' : '' }}>-- All Guest Rooms --</option>
              @foreach($roomsList as $rm)
                <option value="{{ $rm->id }}" {{ (string)$roomId === (string)$rm->id ? 'selected' : '' }}>
                  Room {{ $rm->room_number }}
                </option>
              @endforeach
            </select>
          </div>

          <!-- Order Type Filter -->
          <div>
            <label class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
              <i class="fa-solid fa-utensils text-emerald-600 mr-1"></i> Order Type
            </label>
            <select name="order_type" class="w-full h-9.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 px-2.5 focus:outline-none focus:border-indigo-500 shadow-2xs">
              <option value="all" {{ $orderType === 'all' ? 'selected' : '' }}>All Order Types</option>
              <option value="Room Dining" {{ $orderType === 'Room Dining' ? 'selected' : '' }}>Room Dining</option>
              <option value="Table Dine-in" {{ $orderType === 'Table Dine-in' ? 'selected' : '' }}>Table Dine-in</option>
              <option value="Takeaway" {{ $orderType === 'Takeaway' ? 'selected' : '' }}>Takeaway</option>
            </select>
          </div>

          <!-- Billing Mode Filter -->
          <div>
            <label class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
              <i class="fa-solid fa-credit-card text-blue-600 mr-1"></i> Billing Mode
            </label>
            <select name="billing_type" class="w-full h-9.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 px-2.5 focus:outline-none focus:border-indigo-500 shadow-2xs">
              <option value="all" {{ $billingType === 'all' ? 'selected' : '' }}>All Billing Modes</option>
              <option value="Charged to Room Folio" {{ $billingType === 'Charged to Room Folio' ? 'selected' : '' }}>Charged to Room Folio</option>
              <option value="Cash Paid" {{ $billingType === 'Cash Paid' ? 'selected' : '' }}>Cash Paid</option>
              <option value="Digital UPI / Card Paid" {{ $billingType === 'Digital UPI / Card Paid' ? 'selected' : '' }}>UPI / Online / Card</option>
              <option value="Pending Settlement" {{ $billingType === 'Pending Settlement' ? 'selected' : '' }}>Pending Settlement</option>
            </select>
          </div>

          <!-- Nature Filter (Bill vs KOT) -->
          <div>
            <label class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
              <i class="fa-solid fa-receipt text-amber-600 mr-1"></i> Nature
            </label>
            <select name="nature" class="w-full h-9.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 px-2.5 focus:outline-none focus:border-indigo-500 shadow-2xs">
              <option value="all" {{ $nature === 'all' ? 'selected' : '' }}>All (Bills &amp; KOTs)</option>
              <option value="bill" {{ $nature === 'bill' ? 'selected' : '' }}>Final Master Bills Only</option>
              <option value="kot" {{ $nature === 'kot' ? 'selected' : '' }}>Kitchen Tickets (KOT) Only</option>
            </select>
          </div>

          <!-- Search & Submit -->
          <div>
            <label class="block text-[11px] font-extrabold uppercase text-slate-500 mb-1">
              <i class="fa-solid fa-magnifying-glass text-slate-400 mr-1"></i> Search Keyword
            </label>
            <div class="flex items-center gap-1.5">
              <input type="text" name="search" value="{{ $search }}" placeholder="Order #, guest, item..." class="w-full h-9.5 text-xs font-medium rounded-xl border border-slate-200 bg-white text-slate-700 px-2.5 focus:outline-none focus:border-indigo-500 shadow-2xs">
              <button type="submit" class="h-9.5 px-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs shrink-0 cursor-pointer" title="Apply Filters">
                <i class="fa-solid fa-filter"></i>
              </button>
              <a href="{{ route('pos.reports') }}" class="h-9.5 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-all flex items-center justify-center shrink-0" title="Reset Filters">
                <i class="fa-solid fa-rotate-left"></i>
              </a>
            </div>
          </div>

        </div>
      </form>
    </div>

    <!-- 3. Key Financial & Operational Metric Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3">
      
      <!-- Total POS Revenue -->
      <div class="bg-white rounded-2xl border border-emerald-200/90 bg-linear-to-b from-white to-emerald-50/30 p-3.5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400">
          <span class="text-[10px] font-black uppercase tracking-wider text-emerald-800">Net POS Revenue</span>
          <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-[11px]">
            <i class="fa-solid fa-indian-rupee-sign"></i>
          </span>
        </div>
        <div class="mt-2">
          <div class="text-xl font-black font-mono text-emerald-700">₹ {{ number_format($totalRevenue, 2) }}</div>
          <div class="text-[10px] font-semibold text-emerald-600/90 mt-0.5">Settled Dining Sales</div>
        </div>
      </div>

      <!-- Total Orders Count -->
      <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400">
          <span class="text-[10px] font-black uppercase tracking-wider text-slate-600">Total Records</span>
          <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[11px]">
            <i class="fa-solid fa-receipt"></i>
          </span>
        </div>
        <div class="mt-2">
          <div class="text-xl font-black font-mono text-slate-900">{{ number_format($totalOrdersCount) }}</div>
          <div class="text-[10px] font-semibold text-slate-500 mt-0.5">{{ $masterBillsCount }} Bills • {{ $kotCount }} KOTs</div>
        </div>
      </div>

      <!-- Room Dining Revenue -->
      <div class="bg-white rounded-2xl border border-indigo-200/80 bg-linear-to-b from-white to-indigo-50/20 p-3.5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400">
          <span class="text-[10px] font-black uppercase tracking-wider text-indigo-800">Room Dining</span>
          <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-[11px]">
            <i class="fa-solid fa-bell-concierge"></i>
          </span>
        </div>
        <div class="mt-2">
          <div class="text-xl font-black font-mono text-indigo-700">₹ {{ number_format($roomDiningRevenue, 2) }}</div>
          <div class="text-[10px] font-semibold text-indigo-600 mt-0.5">{{ $roomDiningCount }} Room Orders</div>
        </div>
      </div>

      <!-- Table Dine-in Revenue -->
      <div class="bg-white rounded-2xl border border-purple-200/80 bg-linear-to-b from-white to-purple-50/20 p-3.5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400">
          <span class="text-[10px] font-black uppercase tracking-wider text-purple-800">Table Dine-in</span>
          <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-[11px]">
            <i class="fa-solid fa-chair"></i>
          </span>
        </div>
        <div class="mt-2">
          <div class="text-xl font-black font-mono text-purple-700">₹ {{ number_format($tableDineInRevenue, 2) }}</div>
          <div class="text-[10px] font-semibold text-purple-600 mt-0.5">{{ $tableDineInCount }} Table Bills</div>
        </div>
      </div>

      <!-- Takeaway Revenue -->
      <div class="bg-white rounded-2xl border border-cyan-200/80 bg-linear-to-b from-white to-cyan-50/20 p-3.5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400">
          <span class="text-[10px] font-black uppercase tracking-wider text-cyan-800">Takeaway Sales</span>
          <span class="w-6 h-6 rounded-lg bg-cyan-100 text-cyan-700 flex items-center justify-center text-[11px]">
            <i class="fa-solid fa-bag-shopping"></i>
          </span>
        </div>
        <div class="mt-2">
          <div class="text-xl font-black font-mono text-cyan-700">₹ {{ number_format($takeawayRevenue, 2) }}</div>
          <div class="text-[10px] font-semibold text-cyan-600 mt-0.5">{{ $takeawayCount }} Parcels</div>
        </div>
      </div>

      <!-- Room Folio Ledger Total -->
      <div class="bg-white rounded-2xl border border-blue-200/80 bg-linear-to-b from-white to-blue-50/20 p-3.5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400">
          <span class="text-[10px] font-black uppercase tracking-wider text-blue-800">Room Folio Debits</span>
          <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-[11px]">
            <i class="fa-solid fa-door-closed"></i>
          </span>
        </div>
        <div class="mt-2">
          <div class="text-xl font-black font-mono text-blue-700">₹ {{ number_format($roomFolioRevenue, 2) }}</div>
          <div class="text-[10px] font-semibold text-blue-600 mt-0.5">Charged to Folio</div>
        </div>
      </div>

      <!-- GST & Discounts -->
      <div class="bg-white rounded-2xl border border-amber-200/80 bg-linear-to-b from-white to-amber-50/20 p-3.5 shadow-xs flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-400">
          <span class="text-[10px] font-black uppercase tracking-wider text-amber-800">Taxes &amp; Disc</span>
          <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-[11px]">
            <i class="fa-solid fa-tags"></i>
          </span>
        </div>
        <div class="mt-2">
          <div class="text-base font-black font-mono text-amber-800">₹ {{ number_format($totalTax, 2) }}</div>
          <div class="text-[10px] font-semibold text-amber-700 mt-0.5">GST | Disc: ₹ {{ number_format($totalDiscounts, 2) }}</div>
        </div>
      </div>

    </div>

    <!-- 4. Interactive Report Tabs Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      
      <!-- Tab Header Switcher -->
      <div class="bg-slate-50/80 px-4 sm:px-6 py-3 border-b border-slate-200/80 flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-1.5 flex-wrap">
          <button type="button" onclick="switchReportTab('orders')" id="tab-btn-orders" class="report-tab-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-indigo-600 text-white shadow-xs cursor-pointer flex items-center gap-1.5">
            <i class="fa-solid fa-table-list text-[11px]"></i>
            <span>Orders Ledger</span>
            <span class="bg-white/20 px-1.5 py-0.2 rounded-md text-[10px]">{{ $orders->total() }}</span>
          </button>
          
          <button type="button" onclick="switchReportTab('dishes')" id="tab-btn-dishes" class="report-tab-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/90 cursor-pointer flex items-center gap-1.5">
            <i class="fa-solid fa-utensils text-emerald-600 text-[11px]"></i>
            <span>Top Dishes Sold</span>
            <span class="bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded-md text-[10px]">{{ $itemSales->count() }}</span>
          </button>

          <button type="button" onclick="switchReportTab('tables')" id="tab-btn-tables" class="report-tab-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/90 cursor-pointer flex items-center gap-1.5">
            <i class="fa-solid fa-chair text-purple-600 text-[11px]"></i>
            <span>Table Performance</span>
            <span class="bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded-md text-[10px]">{{ $tableBreakdown->count() }}</span>
          </button>

          <button type="button" onclick="switchReportTab('rooms')" id="tab-btn-rooms" class="report-tab-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/90 cursor-pointer flex items-center gap-1.5">
            <i class="fa-solid fa-door-open text-indigo-600 text-[11px]"></i>
            <span>Room Dining Breakdown</span>
            <span class="bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded-md text-[10px]">{{ $roomBreakdown->count() }}</span>
          </button>

          <button type="button" onclick="switchReportTab('payments')" id="tab-btn-payments" class="report-tab-btn px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all bg-white text-slate-700 hover:bg-slate-100 border border-slate-200/90 cursor-pointer flex items-center gap-1.5">
            <i class="fa-solid fa-wallet text-amber-600 text-[11px]"></i>
            <span>Payment Modes</span>
            <span class="bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded-md text-[10px]">{{ $paymentBreakdown->count() }}</span>
          </button>
        </div>

        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-slate-500">Showing Rows:</span>
          <select class="h-8 text-xs font-semibold rounded-lg border border-slate-200 bg-white px-2 focus:outline-none" onchange="changeReportPerPage(this.value)">
            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
            <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option>
          </select>
        </div>
      </div>

      <!-- TAB 1: Orders Ledger -->
      <div id="report-content-orders" class="report-tab-content p-4 sm:p-5">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[950px]" id="pos-reports-orders-table">
            <thead>
              <tr class="border-b border-slate-200 text-[11px] font-black uppercase text-slate-500 tracking-wider bg-slate-50/50">
                <th class="py-3 px-3">Order / KOT #</th>
                <th class="py-3 px-3">Date &amp; Time</th>
                <th class="py-3 px-3">Type &amp; Nature</th>
                <th class="py-3 px-3">Location / Room</th>
                <th class="py-3 px-3">Customer / Guest</th>
                <th class="py-3 px-3">Items Summary</th>
                <th class="py-3 px-3 text-right">Subtotal</th>
                <th class="py-3 px-3 text-right">Discount</th>
                <th class="py-3 px-3 text-right">GST</th>
                <th class="py-3 px-3 text-right">Grand Total</th>
                <th class="py-3 px-3">Billing Mode</th>
                <th class="py-3 px-3 text-center">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              @forelse($orders as $ord)
                @php
                  $isKot = (bool)$ord->is_kot;
                  $guestName = $ord->customer_name ?: ($ord->booking?->guest?->name ?? 'Direct Walk-in');
                  $itemsCount = $ord->items->sum('quantity');
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                  <!-- Order Number -->
                  <td class="py-3 px-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                    {{ $ord->order_number ?: $ord->kot_number }}
                    @if($isKot)
                      <span class="block text-[10px] font-semibold text-cyan-600">KOT Ticket</span>
                    @else
                      <span class="block text-[10px] font-semibold text-emerald-600">Master Bill</span>
                    @endif
                  </td>

                  <!-- Date & Time -->
                  <td class="py-3 px-3 whitespace-nowrap text-slate-600 font-medium">
                    <div>{{ $ord->created_at ? $ord->created_at->format('d M Y') : 'N/A' }}</div>
                    <div class="text-[11px] text-slate-400 font-mono">{{ $ord->created_at ? $ord->created_at->format('h:i A') : '' }}</div>
                  </td>

                  <!-- Type & Nature -->
                  <td class="py-3 px-3 whitespace-nowrap">
                    @if($ord->order_type === 'Room Dining')
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="fa-solid fa-bell-concierge text-[9px]"></i> Room Dining
                      </span>
                    @elseif($ord->order_type === 'Table Dine-in')
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                        <i class="fa-solid fa-chair text-[9px]"></i> Table Dine-in
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200">
                        <i class="fa-solid fa-bag-shopping text-[9px]"></i> Takeaway
                      </span>
                    @endif
                  </td>

                  <!-- Location / Room -->
                  <td class="py-3 px-3 whitespace-nowrap">
                    @if(!empty($ord->room_number) || $ord->room)
                      <span class="inline-flex items-center gap-1 font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md text-xs border border-indigo-200/80">
                        <i class="fa-solid fa-door-open text-[10px]"></i> Room {{ $ord->room_number ?: $ord->room?->room_number }}
                      </span>
                    @endif
                    @if(!empty($ord->table_number) || $ord->tableManage)
                      <span class="inline-flex items-center gap-1 font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md text-xs border border-purple-200/80 mt-0.5">
                        <i class="fa-solid fa-chair text-[10px]"></i> Table {{ $ord->table_number ?: $ord->tableManage?->table_number }}
                      </span>
                    @endif
                    @if(empty($ord->room_number) && empty($ord->table_number) && !$ord->room && !$ord->tableManage)
                      <span class="text-slate-400 font-medium text-xs">Counter / Direct</span>
                    @endif
                  </td>

                  <!-- Customer / Guest -->
                  <td class="py-3 px-3">
                    <div class="font-bold text-slate-800">{{ $guestName }}</div>
                    @if(!empty($ord->customer_phone))
                      <div class="text-[11px] text-slate-500 font-mono">{{ $ord->customer_phone }}</div>
                    @endif
                  </td>

                  <!-- Items Summary -->
                  <td class="py-3 px-3 max-w-[260px]">
                    <div class="text-xs text-slate-700 line-clamp-1">
                      @foreach($ord->items->take(2) as $it)
                        <span class="font-medium">{{ $it->item_name }} (x{{ $it->quantity }})</span>{{ !$loop->last ? ', ' : '' }}
                      @endforeach
                      @if($ord->items->count() > 2)
                        <span class="text-slate-400 font-medium">+{{ $ord->items->count() - 2 }} more</span>
                      @endif
                    </div>
                    <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                      {{ $itemsCount }} total plate(s)
                    </div>
                  </td>

                  <!-- Subtotal -->
                  <td class="py-3 px-3 text-right font-mono font-bold text-slate-700 whitespace-nowrap">
                    ₹ {{ number_format($ord->subtotal, 2) }}
                  </td>

                  <!-- Discount -->
                  <td class="py-3 px-3 text-right font-mono text-emerald-700 whitespace-nowrap">
                    @if($ord->discount_amount > 0)
                      - ₹ {{ number_format($ord->discount_amount, 2) }}
                    @else
                      <span class="text-slate-300">₹ 0.00</span>
                    @endif
                  </td>

                  <!-- Tax (GST) -->
                  <td class="py-3 px-3 text-right font-mono text-slate-600 whitespace-nowrap">
                    ₹ {{ number_format($ord->tax_amount, 2) }}
                  </td>

                  <!-- Grand Total -->
                  <td class="py-3 px-3 text-right font-mono font-black text-slate-900 whitespace-nowrap text-sm">
                    ₹ {{ number_format($ord->total_amount, 2) }}
                  </td>

                  <!-- Billing Mode -->
                  <td class="py-3 px-3 whitespace-nowrap">
                    @if($ord->billing_type === 'Charged to Room Folio')
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <i class="fa-solid fa-door-closed text-[9px]"></i> Room Folio
                      </span>
                    @elseif($ord->billing_type === 'Cash Paid')
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="fa-solid fa-money-bill-wave text-[9px]"></i> Cash Paid
                      </span>
                    @elseif(str_contains($ord->billing_type, 'UPI') || str_contains($ord->billing_type, 'Card'))
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                        <i class="fa-solid fa-qrcode text-[9px]"></i> Digital / UPI
                      </span>
                    @else
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <i class="fa-solid fa-clock text-[9px]"></i> {{ $ord->billing_type ?: 'Pending' }}
                      </span>
                    @endif
                  </td>

                  <!-- Action -->
                  <td class="py-3 px-3 text-center whitespace-nowrap">
                    <button type="button" onclick="viewOrderDetailsModal({{ $ord->id }})" class="px-2.5 py-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-all cursor-pointer">
                      <i class="fa-solid fa-eye mr-1"></i> View
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="12" class="py-12 text-center text-slate-400">
                    <i class="fa-solid fa-receipt text-3xl mb-2 text-slate-300 block"></i>
                    <p class="font-bold text-slate-600">No POS orders match your filter criteria.</p>
                    <p class="text-xs text-slate-400 mt-1">Try broadening your date range, table, or room filters above.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div class="mt-4 pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="text-xs font-semibold text-slate-500">
            Showing <span class="font-bold text-slate-800">{{ $orders->firstItem() ?? 0 }}</span> to <span class="font-bold text-slate-800">{{ $orders->lastItem() ?? 0 }}</span> of <span class="font-bold text-slate-800">{{ $orders->total() }}</span> records
          </div>
          <div>
            {{ $orders->links() }}
          </div>
        </div>
      </div>

      <!-- TAB 2: Top Selling Dishes -->
      <div id="report-content-dishes" class="report-tab-content p-4 sm:p-5 hidden">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
          <div>
            <h3 class="text-sm font-extrabold text-slate-900">Menu Dish Sales Breakdown</h3>
            <p class="text-xs text-slate-500">Sorted by top grossing items sold across all restaurant tables and room services</p>
          </div>
          <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
            {{ $itemSales->count() }} Unique Dishes Sold
          </span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
              <tr class="border-b border-slate-200 text-[11px] font-black uppercase text-slate-500 tracking-wider bg-slate-50/50">
                <th class="py-3 px-3 w-16 text-center">Rank</th>
                <th class="py-3 px-3">Item Name</th>
                <th class="py-3 px-3 text-center">Orders Count</th>
                <th class="py-3 px-3 text-right">Qty Sold</th>
                <th class="py-3 px-3 text-right">Avg Price</th>
                <th class="py-3 px-3 text-right">Total Revenue</th>
                <th class="py-3 px-3 w-44">Revenue Share</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              @php $grandDishRev = $itemSales->sum('total_revenue') ?: 1; @endphp
              @forelse($itemSales as $idx => $dish)
                @php
                  $sharePct = round(($dish->total_revenue / $grandDishRev) * 100, 1);
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                  <td class="py-3 px-3 text-center">
                    @if($idx === 0)
                      <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-800 font-black text-xs inline-flex items-center justify-center">1</span>
                    @elseif($idx === 1)
                      <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-black text-xs inline-flex items-center justify-center">2</span>
                    @elseif($idx === 2)
                      <span class="w-6 h-6 rounded-full bg-amber-50 text-amber-900 border border-amber-300 font-black text-xs inline-flex items-center justify-center">3</span>
                    @else
                      <span class="text-xs font-bold text-slate-400">#{{ $idx + 1 }}</span>
                    @endif
                  </td>
                  <td class="py-3 px-3 font-bold text-slate-900">
                    {{ $dish->item_name }}
                  </td>
                  <td class="py-3 px-3 text-center font-semibold text-slate-700">
                    {{ number_format($dish->order_count) }} orders
                  </td>
                  <td class="py-3 px-3 text-right font-mono font-extrabold text-slate-800">
                    {{ number_format($dish->total_qty) }}
                  </td>
                  <td class="py-3 px-3 text-right font-mono font-medium text-slate-600">
                    ₹ {{ number_format($dish->avg_price, 2) }}
                  </td>
                  <td class="py-3 px-3 text-right font-mono font-black text-emerald-700 text-sm">
                    ₹ {{ number_format($dish->total_revenue, 2) }}
                  </td>
                  <td class="py-3 px-3">
                    <div class="flex items-center gap-2">
                      <div class="flex-1 bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full" style="width: {{ min(100, $sharePct) }}%"></div>
                      </div>
                      <span class="text-[11px] font-mono font-bold text-slate-600 w-10 text-right">{{ $sharePct }}%</span>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="py-10 text-center text-slate-400">
                    <i class="fa-solid fa-bowl-food text-2xl mb-1 text-slate-300 block"></i>
                    No item sales data found for the current filter.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 3: Table-wise Performance -->
      <div id="report-content-tables" class="report-tab-content p-4 sm:p-5 hidden">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
          <div>
            <h3 class="text-sm font-extrabold text-slate-900">Restaurant Table Revenue Performance</h3>
            <p class="text-xs text-slate-500">Track which dining tables generate highest ticket volume and dining revenues</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[650px]">
            <thead>
              <tr class="border-b border-slate-200 text-[11px] font-black uppercase text-slate-500 tracking-wider bg-slate-50/50">
                <th class="py-3 px-3">Table Identifier</th>
                <th class="py-3 px-3 text-center">Orders Served</th>
                <th class="py-3 px-3 text-right">Subtotal</th>
                <th class="py-3 px-3 text-right">Net Revenue</th>
                <th class="py-3 px-3 text-right">Avg Ticket Size</th>
                <th class="py-3 px-3 text-center">Filter By Table</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              @forelse($tableBreakdown as $tbl)
                @php
                  $avgTicket = $tbl->orders_count > 0 ? ($tbl->total_revenue / $tbl->orders_count) : 0;
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors">
                  <td class="py-3 px-3 font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-700 flex items-center justify-center text-xs">
                      <i class="fa-solid fa-chair"></i>
                    </span>
                    <span>Table {{ $tbl->tbl_num }}</span>
                  </td>
                  <td class="py-3 px-3 text-center font-mono font-bold text-slate-700">
                    {{ $tbl->orders_count }}
                  </td>
                  <td class="py-3 px-3 text-right font-mono font-medium text-slate-600">
                    ₹ {{ number_format($tbl->total_subtotal, 2) }}
                  </td>
                  <td class="py-3 px-3 text-right font-mono font-black text-purple-700 text-sm">
                    ₹ {{ number_format($tbl->total_revenue, 2) }}
                  </td>
                  <td class="py-3 px-3 text-right font-mono font-bold text-slate-800">
                    ₹ {{ number_format($avgTicket, 2) }}
                  </td>
                  <td class="py-3 px-3 text-center">
                    <a href="{{ route('pos.reports', array_merge(request()->query(), ['table_id' => $tbl->table_id ?: $tbl->tbl_num])) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-purple-700 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-2.5 py-1 rounded-lg transition-all">
                      <i class="fa-solid fa-filter text-[10px]"></i> View Only This
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="py-10 text-center text-slate-400">
                    <i class="fa-solid fa-chair text-2xl mb-1 text-slate-300 block"></i>
                    No table dine-in orders match the selected filter.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 4: Room-wise Dining Charges -->
      <div id="report-content-rooms" class="report-tab-content p-4 sm:p-5 hidden">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
          <div>
            <h3 class="text-sm font-extrabold text-slate-900">Room Dining &amp; Folio Service Breakdown</h3>
            <p class="text-xs text-slate-500">In-Room dining charges, corporate discounts, and room service billings by room number</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[650px]">
            <thead>
              <tr class="border-b border-slate-200 text-[11px] font-black uppercase text-slate-500 tracking-wider bg-slate-50/50">
                <th class="py-3 px-3">Room Number</th>
                <th class="py-3 px-3 text-center">Orders Served</th>
                <th class="py-3 px-3 text-right">Discounts Given</th>
                <th class="py-3 px-3 text-right">Total Food Revenue</th>
                <th class="py-3 px-3 text-center">Filter By Room</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs">
              @forelse($roomBreakdown as $rm)
                <tr class="hover:bg-slate-50/70 transition-colors">
                  <td class="py-3 px-3 font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs">
                      <i class="fa-solid fa-door-open"></i>
                    </span>
                    <span>Room {{ $rm->rm_num }}</span>
                  </td>
                  <td class="py-3 px-3 text-center font-mono font-bold text-slate-700">
                    {{ $rm->orders_count }}
                  </td>
                  <td class="py-3 px-3 text-right font-mono font-medium text-emerald-700">
                    ₹ {{ number_format($rm->total_discount, 2) }}
                  </td>
                  <td class="py-3 px-3 text-right font-mono font-black text-indigo-700 text-sm">
                    ₹ {{ number_format($rm->total_revenue, 2) }}
                  </td>
                  <td class="py-3 px-3 text-center">
                    <a href="{{ route('pos.reports', array_merge(request()->query(), ['room_id' => $rm->room_id ?: $rm->rm_num])) }}" class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-700 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-all">
                      <i class="fa-solid fa-filter text-[10px]"></i> View Only This Room
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="py-10 text-center text-slate-400">
                    <i class="fa-solid fa-door-open text-2xl mb-1 text-slate-300 block"></i>
                    No room dining orders match the selected filter.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- TAB 5: Payment Modes Breakdown -->
      <div id="report-content-payments" class="report-tab-content p-4 sm:p-5 hidden">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
          <div>
            <h3 class="text-sm font-extrabold text-slate-900">Payment Modes &amp; Settlement Channels</h3>
            <p class="text-xs text-slate-500">Breakdown of settled revenue between Room Folio accounts, direct cash collections, and digital UPI/Card</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-slate-200 text-[11px] font-black uppercase text-slate-500 tracking-wider bg-slate-50/50">
                  <th class="py-3 px-3">Billing Mode</th>
                  <th class="py-3 px-3 text-center">Bills Count</th>
                  <th class="py-3 px-3 text-right">Settled Amount</th>
                  <th class="py-3 px-3 w-32">Share %</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 text-xs">
                @php $grandPay = $paymentBreakdown->sum('total_amount') ?: 1; @endphp
                @forelse($paymentBreakdown as $pay)
                  @php
                    $payShare = round(($pay->total_amount / $grandPay) * 100, 1);
                  @endphp
                  <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-3 px-3 font-bold text-slate-800">
                      {{ $pay->mode }}
                    </td>
                    <td class="py-3 px-3 text-center font-mono font-bold text-slate-700">
                      {{ $pay->count }}
                    </td>
                    <td class="py-3 px-3 text-right font-mono font-black text-slate-900">
                      ₹ {{ number_format($pay->total_amount, 2) }}
                    </td>
                    <td class="py-3 px-3">
                      <div class="flex items-center gap-1.5">
                        <div class="flex-1 bg-slate-100 h-2 rounded-full overflow-hidden">
                          <div class="bg-indigo-600 h-full rounded-full" style="width: {{ min(100, $payShare) }}%"></div>
                        </div>
                        <span class="text-[11px] font-mono font-bold text-slate-600 w-10 text-right">{{ $payShare }}%</span>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="py-8 text-center text-slate-400">
                      No payment data available.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Summary Highlights -->
          <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 flex flex-col justify-around gap-3">
            <div>
              <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500">Folio vs Immediate Cash/UPI Split</span>
              <div class="mt-2 text-xs text-slate-700 font-medium">
                • <strong>Room Folio:</strong> ₹ {{ number_format($roomFolioRevenue, 2) }} is transferred to front-desk room folios to be settled during guest checkout.<br>
                • <strong>Direct Cash:</strong> ₹ {{ number_format($cashRevenue, 2) }} collected at the restaurant counter.<br>
                • <strong>Digital / UPI / Card:</strong> ₹ {{ number_format($upiCardRevenue, 2) }} collected digitally.
              </div>
            </div>
            <div class="p-3 bg-white rounded-lg border border-slate-200 text-xs">
              <span class="font-bold text-slate-700">Night Audit Reconciliation Tip:</span>
              <p class="text-slate-500 mt-1">POS Room Folio charges automatically integrate with front-office Folio &amp; Night Audit daily revenue summary.</p>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</main>

<!-- Order Details Modal (View Bill / Thermal Slip) -->
<div id="pos-report-order-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
  <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
    <div class="bg-slate-50 px-5 py-3.5 border-b border-slate-200 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <i class="fa-solid fa-receipt text-indigo-600"></i>
        <h3 class="text-sm font-extrabold text-slate-900" id="modal-order-title">Order Details</h3>
      </div>
      <button type="button" onclick="closeOrderModal()" class="w-7 h-7 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 flex items-center justify-center text-xs transition-colors cursor-pointer">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    
    <div class="p-5 overflow-y-auto flex-1 text-xs" id="modal-order-content">
      <div class="py-8 text-center text-slate-400">
        <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 text-indigo-600"></i>
        <p>Loading order details...</p>
      </div>
    </div>

    <div class="bg-slate-50 px-5 py-3 border-t border-slate-200 flex items-center justify-between">
      <button type="button" onclick="printOrderThermal()" class="px-3 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition-colors cursor-pointer">
        <i class="fa-solid fa-print mr-1"></i> Print Slip
      </button>
      <button type="button" onclick="closeOrderModal()" class="px-4 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition-colors cursor-pointer">
        Close
      </button>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
  @media print {
    body * {
      visibility: hidden;
    }
    #pos-reports-orders-table, #pos-reports-orders-table * {
      visibility: visible;
    }
    #pos-reports-orders-table {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
    }
  }
</style>
@endpush

@push('scripts')
<script>
  // Tab Switcher
  function switchReportTab(tabKey) {
    const tabs = ['orders', 'dishes', 'tables', 'rooms', 'payments'];
    tabs.forEach(t => {
      const btn = document.getElementById('tab-btn-' + t);
      const content = document.getElementById('report-content-' + t);
      if (t === tabKey) {
        btn.classList.remove('bg-white', 'text-slate-700', 'border', 'border-slate-200/90');
        btn.classList.add('bg-indigo-600', 'text-white', 'shadow-xs');
        content.classList.remove('hidden');
      } else {
        btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-xs');
        btn.classList.add('bg-white', 'text-slate-700', 'border', 'border-slate-200/90');
        content.classList.add('hidden');
      }
    });
  }

  // Quick Date Filter Click
  function setDateFilter(type) {
    document.getElementById('date_filter_input').value = type;
    if (type !== 'custom') {
      document.getElementById('start_date').value = '';
      document.getElementById('end_date').value = '';
    }
    document.getElementById('pos-reports-filter-form').submit();
  }

  function markCustomDate() {
    document.getElementById('date_filter_input').value = 'custom';
  }

  function changeReportPerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    window.location.href = url.toString();
  }

  // Order Details Modal via Fetch
  async function viewOrderDetailsModal(orderId) {
    const modal = document.getElementById('pos-report-order-modal');
    const content = document.getElementById('modal-order-content');
    const title = document.getElementById('modal-order-title');
    modal.classList.remove('hidden');
    content.innerHTML = `
      <div class="py-12 text-center text-slate-400">
        <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 text-indigo-600"></i>
        <p class="font-medium text-slate-600">Loading order summary...</p>
      </div>
    `;

    try {
      const res = await fetch("{{ url('pos/orders') }}/" + orderId);
      const data = await res.json();
      if (!data.success) {
        content.innerHTML = `<div class="p-4 text-center text-rose-600 font-bold">${data.message || 'Failed to load details.'}</div>`;
        return;
      }
      const ord = data.order;
      title.innerText = (ord.order_number || ord.kot_number) + ' - ' + ord.order_type;

      let itemsHtml = '';
      (ord.items || []).forEach(it => {
        itemsHtml += `
          <div class="flex items-center justify-between py-1.5 border-b border-slate-100">
            <div>
              <span class="font-bold text-slate-800">${escapeHtml(it.item_name)}</span>
              <span class="text-slate-500 font-mono ml-1">x${it.quantity}</span>
              ${it.instructions ? `<div class="text-[10px] text-amber-600 italic">${escapeHtml(it.instructions)}</div>` : ''}
            </div>
            <div class="font-mono font-bold text-slate-800">₹ ${parseFloat(it.line_total || (it.unit_price * it.quantity)).toFixed(2)}</div>
          </div>
        `;
      });

      content.innerHTML = `
        <div class="flex flex-col gap-3 font-sans" id="printable-thermal-slip">
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
            <div class="grid grid-cols-2 gap-2 text-[11px]">
              <div><span class="text-slate-400 font-bold uppercase">Order #</span> <strong class="block font-mono text-slate-800 text-xs">${ord.order_number || ord.kot_number}</strong></div>
              <div><span class="text-slate-400 font-bold uppercase">Date &amp; Time:</span> <strong class="block text-slate-800">${ord.created_at ? new Date(ord.created_at).toLocaleString() : 'N/A'}</strong></div>
              <div><span class="text-slate-400 font-bold uppercase">Location:</span> <strong class="block text-slate-800">${ord.room_number ? 'Room ' + ord.room_number : (ord.table_number ? 'Table ' + ord.table_number : 'Counter')}</strong></div>
              <div><span class="text-slate-400 font-bold uppercase">Guest / Customer:</span> <strong class="block text-slate-800">${escapeHtml(ord.customer_name || 'Direct')}</strong></div>
            </div>
          </div>

          <div>
            <div class="text-[11px] font-black uppercase tracking-wider text-slate-400 mb-1">Ordered Dishes &amp; Items</div>
            <div class="bg-white border border-slate-200 rounded-xl p-2.5">
              ${itemsHtml || '<div class="text-slate-400 italic">No items found</div>'}
            </div>
          </div>

          <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 flex flex-col gap-1 text-xs">
            <div class="flex justify-between text-slate-600">
              <span>Subtotal:</span>
              <span class="font-mono font-bold">₹ ${parseFloat(ord.subtotal || 0).toFixed(2)}</span>
            </div>
            ${ord.discount_amount > 0 ? `
              <div class="flex justify-between text-emerald-700">
                <span>Food Discount:</span>
                <span class="font-mono font-bold">- ₹ ${parseFloat(ord.discount_amount).toFixed(2)}</span>
              </div>
            ` : ''}
            <div class="flex justify-between text-slate-600">
              <span>GST (CGST + SGST):</span>
              <span class="font-mono font-bold">₹ ${parseFloat(ord.tax_amount || 0).toFixed(2)}</span>
            </div>
            <div class="flex justify-between text-slate-900 font-black text-sm pt-1.5 border-t border-slate-200 mt-1">
              <span>Grand Total:</span>
              <span class="font-mono text-emerald-700">₹ ${parseFloat(ord.total_amount || 0).toFixed(2)}</span>
            </div>
          </div>

          <div class="text-[11px] text-slate-500 flex items-center justify-between px-1">
            <span>Billing Mode: <strong class="text-slate-800">${escapeHtml(ord.billing_type || 'N/A')}</strong></span>
            <span>Status: <strong class="text-slate-800">${escapeHtml(ord.status || 'Completed')}</strong></span>
          </div>
        </div>
      `;
    } catch (e) {
      content.innerHTML = `<div class="p-4 text-center text-rose-600 font-bold">Error loading order details: ${escapeHtml(e.message)}</div>`;
    }
  }

  function closeOrderModal() {
    document.getElementById('pos-report-order-modal').classList.add('hidden');
  }

  function printOrderThermal() {
    const slip = document.getElementById('printable-thermal-slip');
    if (!slip) return;
    const printWin = window.open('', '', 'width=420,height=620');
    const slipHtml = slip.innerHTML;
    printWin.document.open();
    printWin.document.write('<!DOCTYPE html><' + 'html><' + 'head><title>POS Order Slip</title>');
    printWin.document.write('<' + 'style>body { font-family: monospace; padding: 15px; font-size: 12px; } .border-b { border-bottom: 1px dashed #000; padding-bottom: 6px; margin-bottom: 6px; } .flex { display: flex; justify-content: space-between; } .font-bold { font-weight: bold; }<' + '/style>');
    printWin.document.write('<' + '/head><' + 'body>');
    printWin.document.write('<h3 style="text-align:center;margin:0 0 5px 0;">HOTEL SAGAR SONNET</h3>');
    printWin.document.write('<p style="text-align:center;margin:0 0 10px 0;font-size:10px;">Restaurant &amp; In-Room Dining</p>');
    printWin.document.write(slipHtml);
    printWin.document.write('<' + '/body><' + '/html>');
    printWin.document.close();
    setTimeout(() => {
      printWin.focus();
      printWin.print();
    }, 250);
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
  }
</script>
@endpush
