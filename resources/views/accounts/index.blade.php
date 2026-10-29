@extends('frontoffice.layouts.app')

@section('title', 'Accounts & Financial Ledgers - Hotel Sagar Sonnet')

@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container print:hidden">

  <!-- 1. Header & Date Range Filter Bar -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
      <div>
        <h1 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2.5">
          <span class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-200/60 flex items-center justify-center text-indigo-600 shadow-xs">
            <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
          </span>
          <span>Accounts &amp; Financial Master Ledger</span>
        </h1>
        <p class="text-xs text-slate-500 font-medium mt-1">
          Audited Real-time Reconciliations: Front Office &bull; Kitchen Stores &bull; POS Dining
        </p>
      </div>

      <!-- Quick Preset Date Pills -->
      <div class="flex items-center gap-1.5 p-1 bg-slate-100/80 border border-slate-200/70 rounded-xl flex-wrap">
        <a href="{{ route('accounts.index', ['date_filter' => 'today', 'search' => $search]) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ $filterType === 'today' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
          Today
        </a>
        <a href="{{ route('accounts.index', ['date_filter' => 'yesterday', 'search' => $search]) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ $filterType === 'yesterday' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
          Yesterday
        </a>
        <a href="{{ route('accounts.index', ['date_filter' => 'this_week', 'search' => $search]) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ $filterType === 'this_week' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
          This Week
        </a>
        <a href="{{ route('accounts.index', ['date_filter' => 'this_month', 'search' => $search]) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ $filterType === 'this_month' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
          This Month
        </a>
        <a href="{{ route('accounts.index', ['date_filter' => 'all', 'search' => $search]) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all {{ $filterType === 'all' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/50' }}">
          All Time
        </a>
      </div>
    </div>

    <!-- Custom Date Range & Search Form -->
    <form method="GET" action="{{ route('accounts.index') }}" class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-3">
      <input type="hidden" name="date_filter" value="custom">
      <div class="flex items-center gap-2">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">From:</span>
        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
               class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500">
      </div>
      <div class="flex items-center gap-2">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">To:</span>
        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
               class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500">
      </div>
      <div class="relative flex-1 min-w-[200px] max-w-xs">
        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search guest, order, vendor..."
               class="h-9.5 pl-8 pr-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full">
      </div>
      <div class="flex items-center gap-2 ml-auto">
        <button type="submit"
                class="inline-flex items-center gap-1.5 h-9.5 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer">
          <i class="fa-solid fa-filter"></i> Apply Filter
        </button>
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-1.5 h-9.5 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer">
          <i class="fa-solid fa-print"></i> Print Ledger
        </button>
      </div>
    </form>
  </div>

  <!-- Active Date Filter Pill Banner -->
  <div class="flex flex-wrap items-center justify-between gap-3 px-1">
    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 flex-wrap">
      <i class="fa-regular fa-calendar-check text-indigo-500"></i>
      <span>Active Accounting Period:</span>
      <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">{{ $filterLabel }}</span>
      @if($search)
        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80">Keyword: "{{ $search }}"</span>
        <a href="{{ route('accounts.index', ['date_filter' => $filterType]) }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline">Clear Search &times;</a>
      @endif
    </div>
    <div class="text-xs font-medium text-slate-500">
      Hotel Sagar Sonnet &bull; Main Luxury Wing PMS Audit
    </div>
  </div>

  <!-- 2. Top 5 Key Performance Stat Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
    
    <!-- 1. Total Direct Collections -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Inflow Collected</span>
        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
          <i class="fa-solid fa-cash-register"></i>
        </span>
      </div>
      <div class="text-xl font-black font-mono text-slate-900 mt-2">
        ₹ {{ number_format($totalRealizedCollections, 2) }}
      </div>
      <div class="text-[11px] text-slate-500 font-medium flex items-center gap-1.5 mt-2 pt-2 border-t border-slate-100">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        <span class="truncate">FO (₹{{ number_format($foTotalPayments, 0) }}) + POS (₹{{ number_format($posDirectSales, 0) }})</span>
      </div>
    </div>

    <!-- 2. Front Office Revenue -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">FO Gross Bill</span>
        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
          <i class="fa-solid fa-hotel"></i>
        </span>
      </div>
      <div class="text-xl font-black font-mono text-slate-900 mt-2">
        ₹ {{ number_format($foTotalGrossBill, 2) }}
      </div>
      <div class="text-[11px] text-slate-500 font-medium mt-2 pt-2 border-t border-slate-100 truncate">
        {{ count($foBookingsLedger) }} Booking(s) &bull; Due: ₹{{ number_format($foTotalReceivables, 0) }}
      </div>
    </div>

    <!-- 3. POS Dining Gross Sales -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Restaurant POS Sales</span>
        <span class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xs">
          <i class="fa-solid fa-utensils"></i>
        </span>
      </div>
      <div class="text-xl font-black font-mono text-slate-900 mt-2">
        ₹ {{ number_format($posGrossSales, 2) }}
      </div>
      <div class="text-[11px] text-slate-500 font-medium mt-2 pt-2 border-t border-slate-100 truncate">
        {{ count($posOrdersList) }} Orders &bull; Direct: ₹{{ number_format($posDirectSales, 0) }}
      </div>
    </div>

    <!-- 4. Kitchen Stores Procurements (Expense) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Procurement Cost</span>
        <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
          <i class="fa-solid fa-boxes-stacked"></i>
        </span>
      </div>
      <div class="text-xl font-black font-mono text-slate-900 mt-2">
        ₹ {{ number_format($storeTotalPurchases, 2) }}
      </div>
      <div class="text-[11px] text-slate-500 font-medium mt-2 pt-2 border-t border-slate-100 truncate">
        {{ $storeInwardCount }} Inward (+{{ $storeTotalPlatesInward }} Plates Yield)
      </div>
    </div>

    <!-- 5. Net Operating Balance / Margin -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Net Operating Margin</span>
        <span class="w-8 h-8 rounded-xl {{ $netOperatingPosition >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center text-xs">
          <i class="fa-solid fa-chart-line"></i>
        </span>
      </div>
      <div class="text-xl font-black font-mono mt-2 {{ $netOperatingPosition >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
        ₹ {{ number_format($netOperatingPosition, 2) }}
      </div>
      <div class="text-[11px] text-slate-500 font-medium mt-2 pt-2 border-t border-slate-100 truncate">
        (Direct Inflow &minus; Store Purchases)
      </div>
    </div>

  </div>

  <!-- 3. Main Workspace Tabbed Container -->
  <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    
    <!-- Tab Navigation Ribbon -->
    <div class="p-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center gap-2 overflow-x-auto">
      <button type="button" 
              class="acc-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap bg-indigo-50 text-indigo-700 border border-indigo-200/80 shadow-xs" 
              onclick="switchAccTab('tab-overview', this)">
        <i class="fa-solid fa-chart-pie"></i> Executive Overview &amp; P&amp;L
      </button>
      <button type="button" 
              class="acc-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all cursor-pointer whitespace-nowrap shadow-xs" 
              onclick="switchAccTab('tab-frontoffice', this)">
        <i class="fa-solid fa-hotel"></i> Front Office Ledger 
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ count($foBookingsLedger) }}</span>
      </button>
      <button type="button" 
              class="acc-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all cursor-pointer whitespace-nowrap shadow-xs" 
              onclick="switchAccTab('tab-pos', this)">
        <i class="fa-solid fa-utensils"></i> POS &amp; Restaurant Sales 
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ count($posOrdersList) }}</span>
      </button>
      <button type="button" 
              class="acc-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all cursor-pointer whitespace-nowrap shadow-xs" 
              onclick="switchAccTab('tab-stores', this)">
        <i class="fa-solid fa-boxes-stacked"></i> Stores &amp; Procurement 
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ count($storeMovementsList) }}</span>
      </button>
      <button type="button" 
              class="acc-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all cursor-pointer whitespace-nowrap shadow-xs" 
              onclick="switchAccTab('tab-receipts', this)">
        <i class="fa-solid fa-receipt"></i> Transaction Receipts Trail 
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ count($unifiedReceipts) }}</span>
      </button>
    </div>

    <!-- TAB 1: EXECUTIVE OVERVIEW & P&L STATEMENT -->
    <div id="tab-overview" class="acc-tab-pane">
      
      <!-- 2 Columns: Revenue Reconciliations & Payment Drawer -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
        
        <!-- Section 1: Department Revenue Breakdown -->
        <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 space-y-3">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
              <i class="fa-solid fa-building-columns text-indigo-600"></i> Revenue Breakdown By Department
            </span>
            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/60 uppercase">Income Statement</span>
          </div>

          <div class="flex items-center justify-between text-xs py-1">
            <span class="font-medium text-slate-600 flex items-center gap-2">
              <i class="fa-solid fa-door-open text-indigo-500 w-4"></i> Room Tariffs &amp; Accommodations:
            </span>
            <span class="font-mono font-bold text-indigo-700">₹ {{ number_format($foTotalRoomTariffs, 2) }}</span>
          </div>

          <div class="flex items-center justify-between text-xs py-1">
            <span class="font-medium text-slate-600 flex items-center gap-2">
              <i class="fa-solid fa-bell-concierge text-amber-500 w-4"></i> Extra Services (Folio Transferred):
            </span>
            <span class="font-mono font-bold text-amber-600">+ ₹ {{ number_format($foTotalExtraCharged, 2) }}</span>
          </div>

          <div class="flex items-center justify-between text-xs py-1">
            <span class="font-medium text-slate-600 flex items-center gap-2">
              <i class="fa-solid fa-utensils text-cyan-500 w-4"></i> POS Dining Sales (Room &bull; Table &bull; Takeaway):
            </span>
            <span class="font-mono font-bold text-cyan-700">₹ {{ number_format($posGrossSales, 2) }}</span>
          </div>

          <div class="flex items-center justify-between text-xs py-1">
            <span class="font-medium text-slate-600 flex items-center gap-2">
              <i class="fa-solid fa-percent text-slate-400 w-4"></i> POS GST Tax Collected (12%):
            </span>
            <span class="font-mono font-bold text-slate-600">₹ {{ number_format($posTaxCollected, 2) }}</span>
          </div>

          <div class="flex items-center justify-between text-xs pt-3 border-t border-slate-200 font-extrabold text-slate-900">
            <span>Total Gross Billings Incurred:</span>
            <span class="font-mono text-sm text-indigo-700">₹ {{ number_format($totalGrossRevenue, 2) }}</span>
          </div>

          <div class="flex items-center justify-between text-xs py-1">
            <span class="font-medium text-slate-600 flex items-center gap-2">
              <i class="fa-solid fa-boxes-stacked text-rose-500 w-4"></i> Less: Kitchen Store Raw Procurement Expense:
            </span>
            <span class="font-mono font-bold text-rose-600">- ₹ {{ number_format($storeTotalPurchases, 2) }}</span>
          </div>

          <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-xl p-3.5 flex items-center justify-between mt-3">
            <span class="text-xs font-black text-slate-900">Net Realized Cash Operating Position:</span>
            <span class="text-base font-black font-mono {{ $netOperatingPosition >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
              ₹ {{ number_format($netOperatingPosition, 2) }}
            </span>
          </div>
        </div>

        <!-- Section 2: Unified Cash Drawer & Payment Modes Reconciliations -->
        <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5 space-y-3">
          <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-900 flex items-center gap-2">
              <i class="fa-solid fa-wallet text-emerald-600"></i> Consolidated Cash Drawer &amp; Digital Collections
            </span>
            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 uppercase">Realized Inflow</span>
          </div>

          <div class="flex items-center justify-between text-xs py-1">
            <span class="font-medium text-slate-600 flex items-center gap-2">
              <i class="fa-solid fa-money-bill-wave text-emerald-500 w-4"></i> Cash In Hand (Front Desk + POS Counter):
            </span>
            <span class="font-mono font-bold text-emerald-700">₹ {{ number_format($consolidatedCash, 2) }}</span>
          </div>

          <div class="flex items-center justify-between text-xs py-1">
            <span class="font-medium text-slate-600 flex items-center gap-2">
              <i class="fa-solid fa-qrcode text-indigo-500 w-4"></i> UPI / Digital Payments (GPay &bull; PhonePe &bull; Paytm):
            </span>
            <span class="font-mono font-bold text-indigo-700">₹ {{ number_format($consolidatedUpi, 2) }}</span>
          </div>

          <div class="flex items-center justify-between text-xs py-1">
            <span class="font-medium text-slate-600 flex items-center gap-2">
              <i class="fa-solid fa-credit-card text-amber-500 w-4"></i> Card / EDC POS Swipes:
            </span>
            <span class="font-mono font-bold text-amber-700">₹ {{ number_format($consolidatedCard, 2) }}</span>
          </div>

          <div class="flex items-center justify-between text-xs py-1">
            <span class="font-medium text-slate-600 flex items-center gap-2">
              <i class="fa-solid fa-landmark text-cyan-500 w-4"></i> Direct Bank Transfers (NEFT &bull; RTGS &bull; IMPS):
            </span>
            <span class="font-mono font-bold text-cyan-700">₹ {{ number_format($consolidatedBank, 2) }}</span>
          </div>

          @if($consolidatedOther > 0)
            <div class="flex items-center justify-between text-xs py-1">
              <span class="font-medium text-slate-600 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-slate-400 w-4"></i> Other Payment Modes:
              </span>
              <span class="font-mono font-bold text-slate-700">₹ {{ number_format($consolidatedOther, 2) }}</span>
            </div>
          @endif

          <div class="flex items-center justify-between text-xs pt-3 border-t border-slate-200 font-extrabold text-slate-900">
            <span>Total Realized Direct Inflow:</span>
            <span class="font-mono text-sm text-emerald-700">₹ {{ number_format($totalRealizedCollections, 2) }}</span>
          </div>

          <!-- Outstanding Receivables Warning -->
          <div class="bg-amber-50 border border-amber-200/80 rounded-xl p-3.5 flex items-center justify-between mt-3">
            <div class="text-xs text-amber-900 font-bold flex items-center gap-2">
              <i class="fa-solid fa-clock-rotate-left text-amber-600"></i> Outstanding Pending Receivables:
            </div>
            <div class="text-sm font-black font-mono text-amber-700">
              ₹ {{ number_format($foTotalReceivables, 2) }}
            </div>
          </div>
        </div>

      </div>

      <!-- POS Order Type Breakdown Mini Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-6 pb-6">
        <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4">
          <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Room Dining POS Sales</div>
          <div class="text-lg font-black font-mono text-slate-900 mt-1">
            ₹ {{ number_format($posRoomDiningSales, 2) }}
          </div>
          <div class="text-xs text-slate-500 font-medium mt-1">Direct In-Room orders</div>
        </div>
        <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4">
          <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Table Dine-in Sales</div>
          <div class="text-lg font-black font-mono text-slate-900 mt-1">
            ₹ {{ number_format($posTableDineInSales, 2) }}
          </div>
          <div class="text-xs text-slate-500 font-medium mt-1">Restaurant tables &amp; deck dining</div>
        </div>
        <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4">
          <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Walk-in Takeaway Sales</div>
          <div class="text-lg font-black font-mono text-slate-900 mt-1">
            ₹ {{ number_format($posTakeawaySales, 2) }}
          </div>
          <div class="text-xs text-slate-500 font-medium mt-1">Non-resident takeaway patrons</div>
        </div>
      </div>

    </div>

    <!-- TAB 2: FRONT OFFICE FINANCIAL LEDGER -->
    <div id="tab-frontoffice" class="acc-tab-pane hidden">
      <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
        <div>
          <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Front Office Guest Bookings &amp; Settlement Ledger</h3>
          <p class="text-xs text-slate-500 font-medium mt-0.5">All active room bookings, stay tariffs, extra charges, advance receipts &amp; balances.</p>
        </div>
        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">{{ count($foBookingsLedger) }} Record(s)</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <th class="py-3 px-3 text-center w-10">SL</th>
              <th class="py-3 px-3">Booking / Folio #</th>
              <th class="py-3 px-3">Primary Guest &amp; Mobile</th>
              <th class="py-3 px-3 text-center">Room</th>
              <th class="py-3 px-3">Stay Dates</th>
              <th class="py-3 px-3 text-right">Room Tariff</th>
              <th class="py-3 px-3 text-right">Extra Serv.</th>
              <th class="py-3 px-3 text-right">Total Bill</th>
              <th class="py-3 px-3 text-right">Paid Amt</th>
              <th class="py-3 px-3 text-right">Balance Due</th>
              <th class="py-3 px-3 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($foBookingsLedger as $idx => $fb)
              <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="py-3 px-3 text-center font-bold text-slate-400 font-mono">{{ $idx + 1 }}</td>
                <td class="py-3 px-3">
                  <div class="font-extrabold text-indigo-700 font-mono text-xs">{{ $fb['booking_number'] }}</div>
                  <div class="text-[10px] text-slate-500 font-medium mt-0.5">Folio: {{ $fb['folio_number'] }}</div>
                </td>
                <td class="py-3 px-3">
                  <div class="font-bold text-slate-900">{{ $fb['guest_name'] }}</div>
                  <div class="text-[11px] text-slate-500 font-mono mt-0.5 flex items-center gap-1">
                    <i class="fa-solid fa-phone text-[9px] opacity-60"></i> {{ $fb['mobile'] }}
                  </div>
                </td>
                <td class="py-3 px-3 text-center">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold font-mono bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                    <i class="fa-solid fa-door-open text-[9px]"></i> {{ $fb['room_number'] }}
                  </span>
                </td>
                <td class="py-3 px-3 whitespace-nowrap text-slate-600">
                  <div class="flex items-center gap-1 text-[11px]"><i class="fa-regular fa-calendar-check text-[10px] text-emerald-600"></i> {{ $fb['checkin_date'] }}</div>
                  <div class="flex items-center gap-1 text-[10px] text-slate-400 mt-0.5"><i class="fa-regular fa-calendar-xmark text-[10px] text-rose-500"></i> {{ $fb['checkout_date'] }}</div>
                </td>
                <td class="py-3 px-3 text-right font-mono font-bold text-slate-700">
                  ₹ {{ number_format($fb['room_charges'], 2) }}
                </td>
                <td class="py-3 px-3 text-right font-mono whitespace-nowrap">
                  @if(($fb['extra_orders_count'] ?? 0) > 0)
                    @if($fb['extra_charged'] > 0)
                      <div class="font-bold text-amber-600" title="Added to Folio & Total Bill">
                        + ₹ {{ number_format($fb['extra_charged'], 2) }}
                      </div>
                    @endif
                    @if($fb['extra_paid_direct'] > 0)
                      <div class="text-[10px] text-emerald-700 font-bold">
                        <i class="fa-solid fa-check"></i> ₹ {{ number_format($fb['extra_paid_direct'], 2) }} (Paid)
                      </div>
                    @endif
                    <div class="text-[10px] text-amber-700 font-bold mt-0.5">
                      <i class="fa-solid fa-bell-concierge text-[9px]"></i> {{ $fb['extra_orders_count'] }} extra bill(s)
                    </div>
                    @if(!empty($fb['extra_orders_detailed']))
                      <div class="flex flex-col gap-1 mt-1 items-end">
                        @foreach($fb['extra_orders_detailed'] as $eOrd)
                          @if(!empty($eOrd['is_paid']))
                            <div class="text-[9px] text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-md px-1.5 py-0.5 inline-flex items-center gap-1" title="{{ $eOrd['order_number'] }}: {{ $eOrd['items_summary'] }}">
                              <span>{{ $eOrd['date_short'] }}: ₹{{ number_format($eOrd['total_amount'], 2) }} (PAID)</span>
                              @if(!empty($eOrd['table_number']))
                                <span class="bg-emerald-100 text-emerald-800 px-1 rounded text-[8px] font-bold">{{ $eOrd['table_number'] }}</span>
                              @endif
                            </div>
                          @else
                            <div class="text-[9px] text-amber-800 bg-amber-50 border border-amber-200/80 rounded-md px-1.5 py-0.5 inline-flex items-center gap-1" title="{{ $eOrd['order_number'] }}: {{ $eOrd['items_summary'] }} (Added to Folio & Total Bill)">
                              <span><i class="fa-solid fa-plus text-[7px]"></i> {{ $eOrd['date_short'] }}: ₹{{ number_format($eOrd['total_amount'], 2) }}</span>
                              @if(!empty($eOrd['table_number']))
                                <span class="bg-amber-100 text-amber-800 px-1 rounded text-[8px] font-bold">{{ $eOrd['table_number'] }}</span>
                              @endif
                            </div>
                          @endif
                        @endforeach
                      </div>
                    @endif
                  @else
                    <span class="text-slate-400">₹ 0.00</span>
                  @endif
                </td>
                <td class="py-3 px-3 text-right font-mono font-black text-slate-900">
                  <div>₹ {{ number_format($fb['total_bill'], 2) }}</div>
                  @if($fb['extra_charged'] > 0)
                    <div class="text-[10px] text-amber-700 font-bold mt-0.5" title="Tariff (₹{{ number_format($fb['room_charges'], 2) }}) + Extra Services (₹{{ number_format($fb['extra_charged'], 2) }})">
                      (Room + Extra)
                    </div>
                  @endif
                </td>
                <td class="py-3 px-3 text-right font-mono font-bold text-emerald-700">
                  ₹ {{ number_format($fb['paid_amount'], 2) }}
                </td>
                <td class="py-3 px-3 text-right font-mono font-black {{ $fb['remaining_amount'] > 0.01 ? 'text-rose-600' : 'text-emerald-700' }}">
                  <div>₹ {{ number_format($fb['remaining_amount'], 2) }}</div>
                  @if($fb['extra_charged'] > 0 && $fb['remaining_amount'] > 0.01)
                    <div class="text-[9px] text-amber-700 font-bold mt-0.5">
                      (incl. +₹{{ number_format($fb['extra_charged'], 2) }} extra)
                    </div>
                  @endif
                </td>
                <td class="py-3 px-3 text-center whitespace-nowrap">
                  @if($fb['is_settled'])
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                      <i class="fa-solid fa-circle-check"></i> Settled
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                      <i class="fa-solid fa-clock"></i> Due
                    </span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="py-8 text-center text-slate-400 font-medium">
                  No front office bookings recorded for this date filter.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($foBookingsLedger) > 0)
            <tfoot>
              <tr class="bg-slate-50 font-bold border-t-2 border-slate-200 text-xs text-slate-700">
                <td colspan="5" class="py-3 px-3 text-right uppercase tracking-wider text-[11px] text-slate-500">Front Office Totals:</td>
                <td class="py-3 px-3 text-right font-mono">₹ {{ number_format($foTotalRoomTariffs, 2) }}</td>
                <td class="py-3 px-3 text-right font-mono text-amber-700">+₹ {{ number_format($foTotalExtraCharged, 2) }}</td>
                <td class="py-3 px-3 text-right font-mono font-black text-indigo-700 text-sm">₹ {{ number_format($foTotalGrossBill, 2) }}</td>
                <td class="py-3 px-3 text-right font-mono font-black text-emerald-700 text-sm">₹ {{ number_format($foTotalPaidOverall, 2) }}</td>
                <td class="py-3 px-3 text-right font-mono font-black text-rose-600 text-sm">₹ {{ number_format($foTotalReceivables, 2) }}</td>
                <td></td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

    <!-- TAB 3: POS & RESTAURANT SALES LEDGER -->
    <div id="tab-pos" class="acc-tab-pane hidden">
      <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
        <div>
          <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">POS Restaurant Dining &amp; Takeaway Sales Ledger</h3>
          <p class="text-xs text-slate-500 font-medium mt-0.5">Itemized restaurant orders, food items sold, tax collected, and settlement modes.</p>
        </div>
        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-200/80">{{ count($posOrdersList) }} POS Order(s)</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <th class="py-3 px-3 text-center w-10">SL</th>
              <th class="py-3 px-3">Order #</th>
              <th class="py-3 px-3">Order Type</th>
              <th class="py-3 px-3">Table / Room</th>
              <th class="py-3 px-3">Customer / Guest</th>
              <th class="py-3 px-3">Ordered Items</th>
              <th class="py-3 px-3 text-right">Subtotal</th>
              <th class="py-3 px-3 text-right">GST (12%)</th>
              <th class="py-3 px-3 text-right">Total Bill</th>
              <th class="py-3 px-3 text-center">Billing Mode</th>
              <th class="py-3 px-3 text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($posOrdersList as $idx => $po)
              <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="py-3 px-3 text-center font-bold text-slate-400 font-mono">{{ $idx + 1 }}</td>
                <td class="py-3 px-3">
                  <div class="font-extrabold text-cyan-700 font-mono text-xs">{{ $po['order_number'] }}</div>
                  <div class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $po['created_at'] }}</div>
                </td>
                <td class="py-3 px-3">
                  @if($po['order_type'] === 'Room Dining')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                      <i class="fa-solid fa-bell-concierge"></i> Room Dining
                    </span>
                  @elseif($po['order_type'] === 'Table Dine-in')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                      <i class="fa-solid fa-chair"></i> Table Dine-in
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                      <i class="fa-solid fa-bag-shopping"></i> Takeaway
                    </span>
                  @endif
                </td>
                <td class="py-3 px-3">
                  @if($po['table_number'] !== '-')
                    <div class="font-bold text-slate-900 flex items-center gap-1 text-xs">
                      <i class="fa-solid fa-chair text-emerald-600 text-[9px]"></i> {{ $po['table_number'] }}
                    </div>
                  @endif
                  @if($po['room_number'] !== '-')
                    <div class="text-[11px] text-indigo-700 font-bold font-mono mt-0.5 flex items-center gap-1">
                      <i class="fa-solid fa-door-open text-[9px]"></i> {{ $po['room_number'] }}
                    </div>
                  @endif
                  @if($po['table_number'] === '-' && $po['room_number'] === '-')
                    <span class="text-slate-400">-</span>
                  @endif
                </td>
                <td class="py-3 px-3">
                  <div class="font-bold text-slate-900">{{ $po['customer_name'] }}</div>
                  @if($po['customer_phone'] !== '-')
                    <div class="text-[11px] text-slate-500 font-mono mt-0.5 flex items-center gap-1">
                      <i class="fa-solid fa-phone text-[8px] opacity-60"></i> {{ $po['customer_phone'] }}
                    </div>
                  @endif
                </td>
                <td class="py-3 px-3 max-w-[240px]">
                  <div class="text-xs text-slate-800 font-semibold line-clamp-2">{{ $po['items_summary'] }}</div>
                  <div class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $po['total_plates'] }} plate(s) served</div>
                </td>
                <td class="py-3 px-3 text-right font-mono font-bold text-slate-700">
                  ₹ {{ number_format($po['subtotal'], 2) }}
                </td>
                <td class="py-3 px-3 text-right font-mono text-slate-500">
                  ₹ {{ number_format($po['tax_amount'], 2) }}
                </td>
                <td class="py-3 px-3 text-right font-mono font-black text-slate-900 text-xs">
                  ₹ {{ number_format($po['total_amount'], 2) }}
                </td>
                <td class="py-3 px-3 text-center whitespace-nowrap">
                  @if($po['billing_type'] === 'Cash Paid')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                      <i class="fa-solid fa-money-bill-wave"></i> Cash Paid
                    </span>
                  @elseif($po['billing_type'] === 'Digital UPI / Card Paid')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                      <i class="fa-solid fa-qrcode"></i> UPI Paid
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                      <i class="fa-solid fa-file-invoice"></i> Room Folio
                    </span>
                  @endif
                </td>
                <td class="py-3 px-3 text-center">
                  <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">{{ $po['status'] }}</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="py-8 text-center text-slate-400 font-medium">
                  No POS dining orders found for this date filter.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($posOrdersList) > 0)
            <tfoot>
              <tr class="bg-slate-50 font-bold border-t-2 border-slate-200 text-xs text-slate-700">
                <td colspan="6" class="py-3 px-3 text-right uppercase tracking-wider text-[11px] text-slate-500">POS Sales Totals:</td>
                <td class="py-3 px-3 text-right font-mono">₹ {{ number_format($posSubtotal, 2) }}</td>
                <td class="py-3 px-3 text-right font-mono text-slate-500">₹ {{ number_format($posTaxCollected, 2) }}</td>
                <td class="py-3 px-3 text-right font-mono font-black text-cyan-700 text-sm">₹ {{ number_format($posGrossSales, 2) }}</td>
                <td colspan="2" class="py-3 px-3 text-slate-600 font-medium text-xs">
                  Direct Cash/UPI: <strong class="text-emerald-700 font-mono">₹{{ number_format($posDirectSales, 2) }}</strong> &bull; Folio: <strong class="text-amber-700 font-mono">₹{{ number_format($posFolioCharged, 2) }}</strong>
                </td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

    <!-- TAB 4: STORES & PROCUREMENT (EXPENSES) LEDGER -->
    <div id="tab-stores" class="acc-tab-pane hidden">
      <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
        <div>
          <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Kitchen Stores &amp; Procurement Expense Ledger</h3>
          <p class="text-xs text-slate-500 font-medium mt-0.5">Raw ingredient inward receiving, purchase rates, vendor challans, and kitchen stock deductions.</p>
        </div>
        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200/80">{{ count($storeMovementsList) }} Movement(s)</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <th class="py-3 px-3 text-center w-10">SL</th>
              <th class="py-3 px-3">Date &amp; Time</th>
              <th class="py-3 px-3 text-center">Type</th>
              <th class="py-3 px-3">Dish / Raw Ingredient</th>
              <th class="py-3 px-3">Category</th>
              <th class="py-3 px-3 text-center">Quantity / Plates</th>
              <th class="py-3 px-3">Reference / Invoice #</th>
              <th class="py-3 px-3 text-right">Unit Rate</th>
              <th class="py-3 px-3 text-right">Procurement Expense</th>
              <th class="py-3 px-3">Issued / Remarks</th>
              <th class="py-3 px-3">Performed By</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($storeMovementsList as $idx => $sm)
              <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="py-3 px-3 text-center font-bold text-slate-400 font-mono">{{ $idx + 1 }}</td>
                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $sm['created_at'] }}</td>
                <td class="py-3 px-3 text-center">
                  @if($sm['type'] === 'IN')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                      <i class="fa-solid fa-arrow-down"></i> INWARD
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200/80">
                      <i class="fa-solid fa-arrow-up"></i> CONSUMED
                    </span>
                  @endif
                </td>
                <td class="py-3 px-3 font-bold text-slate-900">{{ $sm['item_name'] }}</td>
                <td class="py-3 px-3"><span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">{{ $sm['category'] }}</span></td>
                <td class="py-3 px-3 text-center font-bold font-mono">
                  @if($sm['type'] === 'IN')
                    <span class="text-emerald-700">+{{ $sm['quantity'] }}</span>
                  @else
                    <span class="text-slate-500">&minus;{{ $sm['quantity'] }}</span>
                  @endif
                </td>
                <td class="py-3 px-3 font-mono font-bold text-indigo-700 text-xs">
                  {{ $sm['reference_no'] }}
                </td>
                <td class="py-3 px-3 text-right font-mono text-slate-500">
                  {{ $sm['unit_cost'] ? '₹ ' . number_format($sm['unit_cost'], 2) : '-' }}
                </td>
                <td class="py-3 px-3 text-right font-mono font-bold {{ $sm['total_cost'] ? 'text-rose-600' : 'text-slate-400' }}">
                  {{ $sm['total_cost'] ? '₹ ' . number_format($sm['total_cost'], 2) : '-' }}
                </td>
                <td class="py-3 px-3 max-w-[200px] text-slate-600 truncate">
                  {{ $sm['remarks'] }}
                </td>
                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">
                  {{ $sm['performed_by'] }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="py-8 text-center text-slate-400 font-medium">
                  No store movements or procurement records logged for this period.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($storeMovementsList) > 0)
            <tfoot>
              <tr class="bg-slate-50 font-bold border-t-2 border-slate-200 text-xs text-slate-700">
                <td colspan="8" class="py-3 px-3 text-right uppercase tracking-wider text-[11px] text-slate-500">Total Store Procurement Purchases:</td>
                <td class="py-3 px-3 text-right font-mono font-black text-rose-600 text-sm">
                  ₹ {{ number_format($storeTotalPurchases, 2) }}
                </td>
                <td colspan="2" class="py-3 px-3 text-slate-600 font-medium text-xs">
                  Inward Shipments: <strong>{{ $storeInwardCount }}</strong> &bull; Plates Yielded: <strong>{{ $storeTotalPlatesInward }}</strong>
                </td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

    <!-- TAB 5: UNIFIED PAYMENT RECEIPTS AUDIT TRAIL -->
    <div id="tab-receipts" class="acc-tab-pane hidden">
      <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
        <div>
          <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-900">Consolidated Financial Transaction Receipts Trail</h3>
          <p class="text-xs text-slate-500 font-medium mt-0.5">Chronological audit log of all direct payments received and procurement disbursements.</p>
        </div>
        <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">{{ count($unifiedReceipts) }} Transactions</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
              <th class="py-3 px-3 text-center w-10">SL</th>
              <th class="py-3 px-3">Date &amp; Time</th>
              <th class="py-3 px-3">Department / Domain</th>
              <th class="py-3 px-3">Reference #</th>
              <th class="py-3 px-3">Party / Guest / Vendor</th>
              <th class="py-3 px-3">Category / Details</th>
              <th class="py-3 px-3 text-center">Payment Mode</th>
              <th class="py-3 px-3 text-right">Inflow (+)</th>
              <th class="py-3 px-3 text-right">Outflow (&minus;)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @php
              $totInflow = 0;
              $totOutflow = 0;
            @endphp
            @forelse($unifiedReceipts as $idx => $r)
              @php
                if ($r['is_inflow']) $totInflow += $r['amount'];
                else $totOutflow += $r['amount'];
              @endphp
              <tr class="hover:bg-slate-50/60 transition-colors">
                <td class="py-3 px-3 text-center font-bold text-slate-400 font-mono">{{ $idx + 1 }}</td>
                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $r['date_time'] }}</td>
                <td class="py-3 px-3">
                  @if($r['type'] === 'Front Office')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                      <i class="fa-solid fa-hotel"></i> Front Office
                    </span>
                  @elseif($r['type'] === 'POS Sales')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200/80">
                      <i class="fa-solid fa-utensils"></i> Restaurant POS
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/80">
                      <i class="fa-solid fa-boxes-stacked"></i> Store Purchase
                    </span>
                  @endif
                </td>
                <td class="py-3 px-3 font-mono font-bold text-indigo-700 text-xs">{{ $r['ref_no'] }}</td>
                <td class="py-3 px-3 font-bold text-slate-900">{{ $r['party'] }}</td>
                <td class="py-3 px-3 text-slate-600">{{ $r['category'] }}</td>
                <td class="py-3 px-3 text-center whitespace-nowrap">
                  @php
                    $mUpper = strtoupper($r['mode']);
                  @endphp
                  @if(str_contains($mUpper, 'CASH'))
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                      <i class="fa-solid fa-money-bill-wave"></i> Cash
                    </span>
                  @elseif(str_contains($mUpper, 'UPI') || str_contains($mUpper, 'GPAY'))
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                      <i class="fa-solid fa-qrcode"></i> UPI
                    </span>
                  @elseif(str_contains($mUpper, 'CARD'))
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                      <i class="fa-solid fa-credit-card"></i> Card
                    </span>
                  @elseif(str_contains($mUpper, 'BANK') || str_contains($mUpper, 'NEFT'))
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-200/80">
                      <i class="fa-solid fa-landmark"></i> Bank
                    </span>
                  @else
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">{{ $r['mode'] }}</span>
                  @endif
                </td>
                <td class="py-3 px-3 text-right font-mono font-bold text-emerald-700">
                  {{ $r['is_inflow'] ? '₹ ' . number_format($r['amount'], 2) : '-' }}
                </td>
                <td class="py-3 px-3 text-right font-mono font-bold text-rose-600">
                  {{ !$r['is_inflow'] ? '₹ ' . number_format($r['amount'], 2) : '-' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="py-8 text-center text-slate-400 font-medium">
                  No financial transactions recorded for this period.
                </td>
              </tr>
            @endforelse
          </tbody>
          @if(count($unifiedReceipts) > 0)
            <tfoot>
              <tr class="bg-slate-50 font-bold border-t-2 border-slate-200 text-xs text-slate-700">
                <td colspan="7" class="py-3 px-3 text-right uppercase tracking-wider text-[11px] text-slate-500">Total Reconciled Cash Flow:</td>
                <td class="py-3 px-3 text-right font-mono font-black text-emerald-700 text-sm">
                  ₹ {{ number_format($totInflow, 2) }}
                </td>
                <td class="py-3 px-3 text-right font-mono font-black text-rose-600 text-sm">
                  &minus; ₹ {{ number_format($totOutflow, 2) }}
                </td>
              </tr>
              <tr class="bg-slate-100/70 border-t border-slate-200 font-black text-xs text-slate-900">
                <td colspan="7" class="py-3 px-3 text-right uppercase tracking-wider text-[11px]">NET SURPLUS / CASH POSITION:</td>
                <td colspan="2" class="py-3 px-3 text-right font-mono text-base font-black {{ ($totInflow - $totOutflow) >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                  ₹ {{ number_format($totInflow - $totOutflow, 2) }}
                </td>
              </tr>
            </tfoot>
          @endif
        </table>
      </div>
    </div>

  </div>

  </div>
</main>

<!-- PRINTABLE CONTAINER (Only visible during print) -->
<div id="printable-accounts-area" class="hidden print:block">
  <div class="p-6 text-black font-sans">
    <div class="flex justify-between border-b-2 border-black pb-3 mb-4">
      <div>
        <h1 class="text-lg font-bold">HOTEL SAGAR SONNET</h1>
        <div class="text-xs mt-1">Comprehensive Accounts &amp; Financial Master Statement</div>
        <div class="text-[10px] text-gray-600">Station Road, Luxury Wing PMS Reconciliation</div>
      </div>
      <div class="text-right text-xs">
        <div><strong>Accounting Period:</strong> {{ $filterLabel }}</div>
        <div>Generated: {{ now()->format('d M Y, h:i A') }}</div>
        <div>Audited Cashier / System Administrator</div>
      </div>
    </div>

    <!-- 4 Print Summary Boxes -->
    <div class="grid grid-cols-4 gap-3 mb-4">
      <div class="border border-black p-2 text-center">
        <div class="text-[9px] font-bold uppercase">Total Inflow Collected</div>
        <div class="text-sm font-bold mt-1">₹ {{ number_format($totalRealizedCollections, 2) }}</div>
      </div>
      <div class="border border-black p-2 text-center">
        <div class="text-[9px] font-bold uppercase">Gross Billings Incurred</div>
        <div class="text-sm font-bold mt-1">₹ {{ number_format($totalGrossRevenue, 2) }}</div>
      </div>
      <div class="border border-black p-2 text-center">
        <div class="text-[9px] font-bold uppercase">Store Procurement Cost</div>
        <div class="text-sm font-bold mt-1">₹ {{ number_format($storeTotalPurchases, 2) }}</div>
      </div>
      <div class="border border-black p-2 text-center">
        <div class="text-[9px] font-bold uppercase">Net Operating Position</div>
        <div class="text-sm font-bold mt-1">₹ {{ number_format($netOperatingPosition, 2) }}</div>
      </div>
    </div>

    <!-- Print Tables Summary -->
    <h3 class="text-xs font-bold uppercase border-b border-black pb-1 mb-2">Department Highlights</h3>
    <table class="w-full border-collapse text-[10px] mb-4">
      <thead>
        <tr class="border-b border-black bg-gray-100 font-bold">
          <th class="p-1.5 text-left">Department</th>
          <th class="p-1.5 text-right">Transactions</th>
          <th class="p-1.5 text-right">Total Gross</th>
          <th class="p-1.5 text-right">Direct Paid</th>
          <th class="p-1.5 text-right">Balance / Pending</th>
        </tr>
      </thead>
      <tbody>
        <tr class="border-b border-gray-200">
          <td class="p-1.5 font-bold">Front Office Accommodations</td>
          <td class="p-1.5 text-right">{{ count($foBookingsLedger) }} Bookings</td>
          <td class="p-1.5 text-right">₹ {{ number_format($foTotalGrossBill, 2) }}</td>
          <td class="p-1.5 text-right">₹ {{ number_format($foTotalPaidOverall, 2) }}</td>
          <td class="p-1.5 text-right">₹ {{ number_format($foTotalReceivables, 2) }}</td>
        </tr>
        <tr class="border-b border-gray-200">
          <td class="p-1.5 font-bold">POS Restaurant &amp; Dining</td>
          <td class="p-1.5 text-right">{{ count($posOrdersList) }} Orders</td>
          <td class="p-1.5 text-right">₹ {{ number_format($posGrossSales, 2) }}</td>
          <td class="p-1.5 text-right">₹ {{ number_format($posDirectSales, 2) }}</td>
          <td class="p-1.5 text-right">₹ {{ number_format($posFolioCharged, 2) }} (Folio)</td>
        </tr>
        <tr class="border-b border-gray-200">
          <td class="p-1.5 font-bold">Kitchen Store Procurements (Expense)</td>
          <td class="p-1.5 text-right">{{ $storeInwardCount }} Shipments</td>
          <td class="p-1.5 text-right">&minus; ₹ {{ number_format($storeTotalPurchases, 2) }}</td>
          <td class="p-1.5 text-right">&minus; ₹ {{ number_format($storeTotalPurchases, 2) }}</td>
          <td class="p-1.5 text-right">0.00</td>
        </tr>
      </tbody>
    </table>

    <div class="text-[9px] text-gray-500 text-center mt-6 pt-2 border-t border-gray-200">
      Hotel Sagar Sonnet Accounting Management System &bull; Automatically Reconciled PMS Financial Audit Trail
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function switchAccTab(tabId, btn) {
    document.querySelectorAll('.acc-tab-btn').forEach(b => {
      b.className = 'acc-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all cursor-pointer whitespace-nowrap shadow-xs';
    });
    document.querySelectorAll('.acc-tab-pane').forEach(p => p.classList.add('hidden'));

    if (btn) {
      btn.className = 'acc-tab-btn inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap bg-indigo-50 text-indigo-700 border border-indigo-200/80 shadow-xs';
    }
    const pane = document.getElementById(tabId);
    if (pane) pane.classList.remove('hidden');
  }
</script>
@endpush
