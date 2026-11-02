@extends('frontoffice.layouts.app')

@section('title', 'Recent POS Orders & KOT History - Hotel Sagar Sonnet PMS')

@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">

    <!-- 1. Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
      <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-linear-to-br from-cyan-500 to-indigo-600 flex items-center justify-center text-white shadow-xs">
            <i class="fa-solid fa-clock-rotate-left text-base"></i>
          </div>
          <div>
            <div class="flex items-center gap-2.5">
              <h1 class="text-base font-extrabold text-slate-900 tracking-tight">Recent POS Orders &amp; KOT History</h1>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-200/80">
                Live Kitchen Tickets
              </span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
              Track real-time kitchen order tickets (KOT), dining destinations, bill reconciliations, and reprint thermal slips.
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
          <a href="{{ route('pos.index') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer">
            <i class="fa-solid fa-utensils"></i> Open POS Terminal
          </a>
          <a href="{{ route('stores.index') }}" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer">
            <i class="fa-solid fa-boxes-stacked text-amber-600"></i> Manage Store
          </a>
          <button type="button" class="inline-flex items-center gap-1.5 h-9 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="exportOrdersTableCSV()">
            <i class="fa-solid fa-file-csv text-slate-500"></i> Export CSV
          </button>
        </div>
      </div>
    </div>

    <!-- 2. Metrics Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
      <!-- Total Orders -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
          <span class="text-[11px] font-bold uppercase tracking-wider">Total Orders</span>
          <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
            <i class="fa-solid fa-receipt"></i>
          </span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-black font-mono text-slate-900">{{ number_format($totalOrdersCount) }}</span>
          <span class="text-[11px] font-semibold text-slate-400">All-time</span>
        </div>
      </div>

      <!-- Total Sales -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
          <span class="text-[11px] font-bold uppercase tracking-wider">Total Sales</span>
          <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
            <i class="fa-solid fa-indian-rupee-sign"></i>
          </span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-black font-mono text-emerald-600">₹ {{ number_format($totalSales, 2) }}</span>
        </div>
      </div>

      <!-- Active in Kitchen -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
          <span class="text-[11px] font-bold uppercase tracking-wider">In Kitchen (KOT)</span>
          <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
            <i class="fa-solid fa-fire-burner animate-pulse"></i>
          </span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-black font-mono text-amber-600">{{ number_format($kitchenActiveCount) }}</span>
          <span class="text-[11px] font-bold text-amber-500">Pending</span>
        </div>
      </div>

      <!-- Charged to Folio -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
          <span class="text-[11px] font-bold uppercase tracking-wider">Room Folio Due</span>
          <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs">
            <i class="fa-solid fa-door-open"></i>
          </span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-black font-mono text-sky-700">{{ number_format($roomFolioCount) }}</span>
          <span class="text-[11px] font-semibold text-slate-400">Orders</span>
        </div>
      </div>

      <!-- Completed -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between">
        <div class="flex items-center justify-between text-slate-500">
          <span class="text-[11px] font-bold uppercase tracking-wider">Completed</span>
          <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
            <i class="fa-solid fa-circle-check"></i>
          </span>
        </div>
        <div class="mt-2 flex items-baseline gap-2">
          <span class="text-2xl font-black font-mono text-emerald-700">{{ number_format($completedCount) }}</span>
          <span class="text-[11px] font-semibold text-slate-400">Served</span>
        </div>
      </div>
    </div>

    <!-- 3. Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
      <form method="GET" action="{{ route('pos.orders.index') }}" id="pos-orders-filter-form" class="flex flex-wrap items-center justify-between gap-3">
        <input type="hidden" name="per_page" id="pos-filter-per-page" value="{{ request('per_page', 10) }}">
        <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[280px]">
          <!-- Search -->
          <div class="relative flex-1 min-w-[220px] max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" name="search" class="h-9.5 pl-8 pr-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="Search order #, guest, room, table, item..." value="{{ request('search') }}">
          </div>

          <!-- Order Type -->
          <select name="type" class="h-9.5 pl-3.5 pr-8.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
            <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>All Order Types</option>
            <option value="Room Dining" {{ request('type') === 'Room Dining' ? 'selected' : '' }}>Room Dining</option>
            <option value="Table Dine-in" {{ request('type') === 'Table Dine-in' ? 'selected' : '' }}>Table Dine-in</option>
            <option value="Takeaway" {{ request('type') === 'Takeaway' ? 'selected' : '' }}>Takeaway</option>
          </select>

          <!-- Status -->
          <select name="status" class="h-9.5 pl-3.5 pr-8.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
            <option value="Received" {{ request('status') === 'Received' ? 'selected' : '' }}>Received</option>
            <option value="In Kitchen" {{ request('status') === 'In Kitchen' ? 'selected' : '' }}>In Kitchen</option>
            <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
            <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>

          <!-- Billing Mode -->
          <select name="billing_type" class="h-9.5 pl-3.5 pr-8.5 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
            <option value="all" {{ request('billing_type', 'all') === 'all' ? 'selected' : '' }}>All Billing Modes</option>
            <option value="Charged to Room Folio" {{ request('billing_type') === 'Charged to Room Folio' ? 'selected' : '' }}>Charged to Room Folio</option>
            <option value="Cash Paid" {{ request('billing_type') === 'Cash Paid' ? 'selected' : '' }}>Cash Paid</option>
            <option value="Digital UPI / Card Paid" {{ request('billing_type') === 'Digital UPI / Card Paid' ? 'selected' : '' }}>Digital UPI / Card Paid</option>
          </select>

          <!-- Date -->
          <input type="date" name="date" class="h-9.5 px-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 cursor-pointer" value="{{ request('date') }}" title="Filter by Order Date">
        </div>

        <div class="flex items-center gap-2">
          <button type="submit" class="inline-flex items-center gap-1.5 h-9.5 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer">
            <i class="fa-solid fa-filter"></i> Apply Filters
          </button>
          <a href="{{ route('pos.orders.index') }}" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer">
            <i class="fa-solid fa-rotate-left"></i> Reset
          </a>
        </div>
      </form>
    </div>

    <!-- 4. Orders Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col">
      <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-2.5">
          <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">KOT Tickets &amp; Order Register</h2>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
            {{ $orders->total() }} Records
          </span>
        </div>
        
        <div class="flex items-center gap-3">
          <div class="pms-per-page-wrap">
            <span>Show</span>
            <select name="per_page" class="pms-per-page-select" id="pos-orders-per-page" onchange="changePosPerPage(this.value)">
              <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>5</option>
              <option value="10" {{ request('per_page') == '10' || !request()->filled('per_page') ? 'selected' : '' }}>10</option>
              <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>20</option>
              <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
              <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
            </select>
            <span>entries</span>
          </div>
          <span class="text-xs text-slate-400 font-medium">Page {{ $orders->currentPage() }} of {{ $orders->lastPage() ?: 1 }}</span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs" id="pos-orders-table">
          <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
              <th class="px-3.5 py-3 text-center w-12">SL</th>
              <th class="px-4 py-3">Order # &amp; Time</th>
              <th class="px-4 py-3">Destination / Type</th>
              <th class="px-4 py-3">Guest / Customer</th>
              <th class="px-4 py-3">Items Ordered</th>
              <th class="px-4 py-3 text-right">Total Bill</th>
              <th class="px-4 py-3">Billing Mode</th>
              <th class="px-4 py-3 text-center">Kitchen Status</th>
              <th class="px-4 py-3 text-center">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @forelse($orders as $order)
              <tr class="hover:bg-slate-50/70 transition-colors">
                <!-- SL -->
                <td class="px-3.5 py-3 text-center font-mono font-bold text-slate-400 text-xs">
                  {{ ($orders->firstItem() ?? 1) + $loop->index }}
                </td>

                <!-- Order Number & Timestamp -->
                <td class="px-4 py-3">
                  <div class="font-mono font-black text-indigo-700 text-xs">
                    {{ $order->order_number }}
                  </div>
                  <div class="text-[11px] text-slate-400 font-medium mt-0.5 flex items-center gap-1">
                    <i class="fa-regular fa-clock text-[10px]"></i>
                    {{ $order->created_at->format('d M Y, h:i A') }}
                  </div>
                </td>

                <!-- Type / Destination -->
                <td class="px-4 py-3">
                  @if($order->order_type === 'Room Dining')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                      <i class="fa-solid fa-door-open text-[10px]"></i> Room {{ $order->room_number }}
                    </span>
                  @elseif($order->order_type === 'Table Dine-in')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                      <i class="fa-solid fa-chair text-[10px]"></i> {{ $order->table_number ?: 'Table Dine-in' }}
                    </span>
                    @if($order->room_number)
                      <div class="text-[10px] text-indigo-600 font-bold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-door-open text-[9px]"></i> Folio Room {{ $order->room_number }}
                      </div>
                    @endif
                  @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                      <i class="fa-solid fa-bag-shopping text-[10px]"></i> Takeaway
                    </span>
                    @if($order->table_number)
                      <div class="text-[10px] text-emerald-700 font-bold mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-chair text-[9px]"></i> {{ $order->table_number }}
                      </div>
                    @endif
                  @endif
                </td>

                <!-- Customer Details -->
                <td class="px-4 py-3">
                  <strong class="text-slate-800 block">{{ $order->customer_name ?: 'Walk-in Guest' }}</strong>
                  @if($order->customer_phone)
                    <div class="text-[11px] text-slate-400 font-mono mt-0.5 flex items-center gap-1">
                      <i class="fa-solid fa-phone text-[9px]"></i> {{ $order->customer_phone }}
                    </div>
                  @endif
                </td>

                <!-- Items -->
                <td class="px-4 py-3 max-w-xs">
                  <div class="flex flex-wrap gap-1 max-w-sm">
                    @foreach($order->items as $it)
                      <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200/80 text-[11px] font-medium text-slate-700">
                        <span class="font-bold text-indigo-600 font-mono">{{ $it->quantity }}x</span>
                        <span class="truncate max-w-[140px]">{{ $it->item_name }}</span>
                      </span>
                    @endforeach
                  </div>
                  @if($order->special_instructions)
                    <div class="text-[10px] text-slate-500 italic mt-1 truncate max-w-xs" title="{{ $order->special_instructions }}">
                      <i class="fa-regular fa-comment-dots text-[9px] mr-0.5"></i> {{ $order->special_instructions }}
                    </div>
                  @endif
                </td>

                <!-- Total Amount -->
                <td class="px-4 py-3 text-right">
                  <div class="font-mono font-black text-slate-900 text-sm">
                    ₹ {{ number_format($order->total_amount, 2) }}
                  </div>
                  @if($order->tax_amount > 0)
                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                      Tax: ₹ {{ number_format($order->tax_amount, 2) }}
                    </div>
                  @endif
                  @if(!empty($order->special_instructions) && str_contains($order->special_instructions, 'Food Advance:'))
                    @php
                      preg_match('/\[Food Advance: (₹[0-9,.]+ cut from advance[^\]]*)\]/', $order->special_instructions, $fam);
                      $faNote = $fam[1] ?? 'Cut from Food Advance';
                    @endphp
                    <div class="mt-1">
                      <span class="inline-flex items-center gap-1 text-[9.5px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 rounded px-1.5 py-0.5 whitespace-nowrap" title="{{ $order->special_instructions }}">
                        <i class="fa-solid fa-utensils text-[8px] text-emerald-600"></i> {{ $faNote }}
                      </span>
                    </div>
                  @endif
                </td>

                <!-- Billing Mode -->
                <td class="px-4 py-3">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $order->billing_type === 'Charged to Room Folio' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $order->billing_type === 'Charged to Room Folio' ? 'bg-indigo-500' : 'bg-emerald-500' }}"></span>
                    {{ $order->billing_type }}
                  </span>
                  @if($order->room_number)
                    <div class="text-[10px] font-bold mt-1 {{ $order->billing_type === 'Charged to Room Folio' ? 'text-indigo-600' : 'text-emerald-700' }}">
                      Room {{ $order->room_number }} ({{ $order->billing_type === 'Charged to Room Folio' ? 'Folio Due' : 'Paid Direct' }})
                    </div>
                  @endif
                  @if(!empty($order->special_instructions) && str_contains($order->special_instructions, 'Food Advance:'))
                    <div class="text-[9.5px] font-bold text-emerald-700 mt-1 flex items-center gap-1">
                      <i class="fa-solid fa-circle-check text-emerald-600 text-[8px]"></i> Paid via Company Food Allowance
                    </div>
                  @endif
                </td>

                <!-- Status Select -->
                <td class="px-4 py-3 text-center">
                  <select class="h-8 text-xs font-bold rounded-lg border border-slate-200 bg-white pl-2.5 pr-7 focus:outline-none focus:border-indigo-500 cursor-pointer shadow-2xs" onchange="updateOrderStatus({{ $order->id }}, this.value)">
                    <option value="Received" {{ $order->status === 'Received' ? 'selected' : '' }}>Received</option>
                    <option value="In Kitchen" {{ $order->status === 'In Kitchen' ? 'selected' : '' }}>In Kitchen</option>
                    <option value="Completed" {{ $order->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ $order->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                  </select>
                </td>

                <!-- Action Button -->
                <td class="px-4 py-3 text-center">
                  <button type="button" class="inline-flex items-center gap-1.5 h-8 px-3 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:border-slate-300 cursor-pointer" onclick="reprintOrderKot({{ $order->id }})" title="View and Print Thermal KOT Slip">
                    <i class="fa-solid fa-print text-indigo-600"></i> KOT
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center text-slate-400 py-16 text-xs font-medium">
                  <i class="fa-solid fa-receipt text-3xl mb-2 block text-slate-300"></i>
                  No POS orders or KOT tickets found matching the selected criteria.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <div class="px-5 py-3.5 bg-slate-50/70 border-t border-slate-200/80 flex items-center justify-between flex-wrap gap-3">
        <div class="text-xs text-slate-500 font-medium">
          Showing <span class="font-bold text-slate-800">{{ $orders->firstItem() ?? ($orders->total() > 0 ? 1 : 0) }}</span> to <span class="font-bold text-slate-800">{{ $orders->lastItem() ?? $orders->total() }}</span> of <span class="font-bold text-slate-800">{{ $orders->total() }}</span> orders
        </div>

        <div class="pms-page-nav flex items-center gap-1">
          @if ($orders->hasPages())
            {{-- Previous Page Link --}}
            @if ($orders->onFirstPage())
              <span class="pms-page-btn opacity-40 cursor-not-allowed" title="Previous Page"><i class="fa-solid fa-chevron-left"></i></span>
            @else
              <a href="{{ $orders->previousPageUrl() }}" class="pms-page-btn" title="Previous Page"><i class="fa-solid fa-chevron-left"></i></a>
            @endif

            @php
              $start = max(1, $orders->currentPage() - 2);
              $end = min($orders->lastPage(), $orders->currentPage() + 2);
            @endphp

            @if ($start > 1)
              <a href="{{ $orders->url(1) }}" class="pms-page-btn">1</a>
              @if ($start > 2)
                <span class="pms-page-btn border-none cursor-default bg-transparent text-slate-400">...</span>
              @endif
            @endif

            {{-- Pagination Number Elements --}}
            @foreach ($orders->getUrlRange($start, $end) as $page => $url)
              @if ($page == $orders->currentPage())
                <span class="pms-page-btn active">{{ $page }}</span>
              @else
                <a href="{{ $url }}" class="pms-page-btn">{{ $page }}</a>
              @endif
            @endforeach

            @if ($end < $orders->lastPage())
              @if ($end < $orders->lastPage() - 1)
                <span class="pms-page-btn border-none cursor-default bg-transparent text-slate-400">...</span>
              @endif
              <a href="{{ $orders->url($orders->lastPage()) }}" class="pms-page-btn">{{ $orders->lastPage() }}</a>
            @endif

            {{-- Next Page Link --}}
            @if ($orders->hasMorePages())
              <a href="{{ $orders->nextPageUrl() }}" class="pms-page-btn" title="Next Page"><i class="fa-solid fa-chevron-right"></i></a>
            @else
              <span class="pms-page-btn opacity-40 cursor-not-allowed" title="Next Page"><i class="fa-solid fa-chevron-right"></i></span>
            @endif
          @else
            <span class="pms-page-btn opacity-40 cursor-not-allowed"><i class="fa-solid fa-chevron-left"></i></span>
            <span class="pms-page-btn active">1</span>
            <span class="pms-page-btn opacity-40 cursor-not-allowed"><i class="fa-solid fa-chevron-right"></i></span>
          @endif
        </div>
      </div>
    </div>

  </div>
</main>

<!-- Thermal KOT Ticket Modal -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="kot-modal">
  <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
    <div class="bg-slate-900 text-white px-5 py-3.5 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
          <i class="fa-solid fa-print text-indigo-400"></i> KOT Ticket &amp; POS Guest Bill
        </h3>
        <p class="text-[11px] text-slate-400 mt-0.5">Original Copy (Guest) &amp; Duplicate Copy (Kitchen)</p>
      </div>
      <button type="button" onclick="closeKotModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-base cursor-pointer transition-colors">&times;</button>
    </div>

    <!-- Copy Filter Switcher -->
    <div class="flex items-center justify-between px-5 py-2.5 bg-slate-100 border-b border-slate-200 text-xs">
      <span class="font-bold text-slate-600"><i class="fa-solid fa-copy text-slate-400 mr-1"></i> Preview Copies:</span>
      <div class="inline-flex p-0.5 bg-white border border-slate-200 rounded-lg shadow-2xs">
        <button type="button" id="tab-kot-both" onclick="switchKotPreview('both')" class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-600 text-white shadow-xs cursor-pointer transition-all">Both Copies</button>
        <button type="button" id="tab-kot-guest" onclick="switchKotPreview('guest')" class="px-2.5 py-1 rounded-md text-[11px] font-bold text-slate-600 hover:text-slate-900 cursor-pointer transition-all">Guest Copy</button>
        <button type="button" id="tab-kot-kitchen" onclick="switchKotPreview('kitchen')" class="px-2.5 py-1 rounded-md text-[11px] font-bold text-slate-600 hover:text-slate-900 cursor-pointer transition-all">Kitchen Copy</button>
      </div>
    </div>

    <div class="p-5 bg-slate-50 overflow-y-auto max-h-[68vh]">
      <div id="printable-kot-area" class="space-y-4">
        
        <!-- 1. ORIGINAL COPY (FOR GUEST) -->
        <div id="kot-ticket-guest" class="kot-ticket-slip bg-white p-4 border border-dashed border-slate-300 rounded-xl font-mono text-xs shadow-xs text-slate-900">
          <h3 class="text-center font-black text-sm tracking-wide">HOTEL SAGAR SONNET</h3>
          <div class="center-sub text-center text-[11px] text-slate-500 font-semibold my-1">
            Sea Breeze Dining &amp; Room Dining<br>
            <strong class="text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200 inline-block mt-0.5">*** ORIGINAL COPY (FOR GUEST) ***</strong>
          </div>
          <div class="divider-line border-b border-dashed border-slate-300 my-2"></div>
          <div class="flex justify-between text-[11px]">
            <span id="kot-guest-order-num"><strong>KOT: --</strong></span>
            <span id="kot-guest-time">Time: --:--</span>
          </div>
          <div class="text-[11px] mt-0.5"><strong>Type:</strong> <span id="kot-guest-dest">--</span></div>
          <div class="text-[11px] mt-0.5"><strong>Guest / Host:</strong> <span id="kot-guest-name">--</span></div>
          <div class="text-[11px] mt-0.5"><strong>Billing Mode:</strong> <span id="kot-guest-billing" class="font-bold text-indigo-700">--</span></div>
          <div class="divider-line border-b border-dashed border-slate-300 my-2"></div>
          <table class="w-full border-collapse text-left text-[11px]">
            <thead>
              <tr class="border-b border-slate-300">
                <th class="py-1">Item</th>
                <th class="text-center w-12 py-1">Qty</th>
                <th class="text-right w-16 py-1">Amount</th>
              </tr>
            </thead>
            <tbody id="kot-guest-items-table" class="divide-y divide-slate-100">
              <!-- Dynamic Guest Items -->
            </tbody>
          </table>
          <div class="divider-line border-b border-dashed border-slate-300 my-2"></div>
          <div class="flex justify-between font-black text-xs">
            <span>Total Qty: <span id="kot-guest-total-qty">0</span></span>
            <span>Grand Total: ₹ <span id="kot-guest-grand-total">0.00</span></span>
          </div>
          <div class="divider-line border-b border-dashed border-slate-300 my-2"></div>
          <div id="kot-guest-notes-line" class="italic text-[10px] text-slate-600">
            Instructions: None
          </div>
          <div class="divider-line border-b border-dashed border-slate-300 my-2"></div>
          <div class="center-sub text-center text-[10px] text-slate-400 mt-1">Served by: Debashis Roy | ORIGINAL GUEST COPY</div>
        </div>

        <!-- Perforation / Tear Strip -->
        <div id="kot-tear-strip" class="kot-tear-strip text-center py-2 text-[10px] text-slate-400 font-mono select-none">
          - - - - - - - - - - - - - - - - - - - - - - - - - - -<br>
          ✂ TEAR / CUT HERE (KITCHEN COPY BELOW) ✂<br>
          - - - - - - - - - - - - - - - - - - - - - - - - - - -
        </div>

        <!-- 2. DUPLICATE COPY (FOR KITCHEN) -->
        <div id="kot-ticket-kitchen" class="kot-ticket-slip bg-white p-4 border border-dashed border-amber-300 rounded-xl font-mono text-xs shadow-xs text-slate-900 bg-amber-50/20">
          <h3 class="text-center font-black text-sm tracking-wide">HOTEL SAGAR SONNET</h3>
          <div class="center-sub text-center text-[11px] text-slate-500 font-semibold my-1">
            Kitchen Order Routing (KOT)<br>
            <strong class="text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 inline-block mt-0.5">*** DUPLICATE COPY (FOR KITCHEN) ***</strong>
          </div>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <div class="flex justify-between text-[11px]">
            <span id="kot-kitchen-order-num"><strong>KOT: --</strong></span>
            <span id="kot-kitchen-time">Time: --:--</span>
          </div>
          <div class="text-[11px] mt-0.5"><strong>Destination:</strong> <span id="kot-kitchen-dest" class="font-extrabold text-indigo-800">--</span></div>
          <div class="text-[11px] mt-0.5"><strong>Guest / Host:</strong> <span id="kot-kitchen-name">--</span></div>
          <div class="text-[11px] mt-0.5"><strong>Billing Mode:</strong> <span id="kot-kitchen-billing">--</span></div>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <table class="w-full border-collapse text-left text-[11px]">
            <thead>
              <tr class="border-b border-slate-300">
                <th class="py-1">Kitchen Preparation Item</th>
                <th class="text-center w-12 py-1">Qty</th>
                <th class="text-right w-16 py-1">Amount</th>
              </tr>
            </thead>
            <tbody id="kot-kitchen-items-table" class="divide-y divide-slate-100">
              <!-- Dynamic Kitchen Items -->
            </tbody>
          </table>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <div class="flex justify-between font-black text-xs">
            <span>Total Qty: <span id="kot-kitchen-total-qty">0</span></span>
            <span>Total Bill: ₹ <span id="kot-kitchen-grand-total">0.00</span></span>
          </div>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <div id="kot-kitchen-notes-line" class="text-[10.5px] font-bold text-amber-900 bg-amber-50 p-2 rounded-lg border border-amber-200">
            Kitchen Instructions: None
          </div>
          <div class="divider-line border-b border-dashed border-amber-300 my-2"></div>
          <div class="center-sub text-center text-[10px] text-slate-400 mt-1">Kitchen Order Routing | DUPLICATE KITCHEN COPY</div>
        </div>

      </div>
    </div>
    
    <!-- Modal Footer Actions -->
    <div class="px-5 py-3.5 bg-white border-t border-slate-200 flex justify-between items-center flex-wrap gap-2">
      <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeKotModal()">Close</button>
      
      <div class="flex items-center gap-2 flex-wrap">
        <button type="button" class="inline-flex items-center gap-1.5 h-9 px-3 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all shadow-xs cursor-pointer" onclick="printThermalTicket('guest')">
          <i class="fa-solid fa-receipt text-indigo-600"></i> Guest Copy
        </button>
        <button type="button" class="inline-flex items-center gap-1.5 h-9 px-3 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all shadow-xs cursor-pointer" onclick="printThermalTicket('kitchen')">
          <i class="fa-solid fa-kitchen-set text-rose-600"></i> Kitchen Copy
        </button>
        <button type="button" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer" onclick="printThermalTicket('both')">
          <i class="fa-solid fa-print text-[11px]"></i> Print Both (Guest + Kitchen)
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // 1. Order Status Updating
  async function updateOrderStatus(orderId, newStatus) {
    try {
      const url = "{{ url('pos/orders') }}/" + orderId + "/status";
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
      });

      const data = await res.json();
      if (res.ok && data.success) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: data.message || 'Order status updated',
            showConfirmButton: false,
            timer: 2000
          });
        } else if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message || 'Order status updated', 'success');
        } else {
          alert(data.message || 'Order status updated');
        }
      } else {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: data.message || 'Failed to update order status.'
          });
        } else {
          alert(data.message || 'Error updating order status.');
        }
      }
    } catch(e) {
      console.error('Error updating order status:', e);
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Update Error',
          text: 'Network or server error while updating status.'
        });
      } else {
        alert('Error updating order status.');
      }
    }
  }

  // 2. KOT Ticket Retrieval & Modal
  async function reprintOrderKot(orderId) {
    try {
      const url = "{{ url('pos/orders') }}/" + orderId;
      const res = await fetch(url, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();
      if (data.success && data.order) {
        showKotModal(data.order);
      } else {
        alert(data.message || 'Unable to retrieve KOT.');
      }
    } catch(e) {
      alert('Unable to retrieve KOT.');
    }
  }

  let activeKotOrder = null;

  function showKotModal(order) {
    activeKotOrder = order;
    const createdAt = order.created_at ? new Date(order.created_at) : new Date();
    const timeStr = createdAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    let dest = order.order_type;
    if (order.order_type === 'Room Dining') {
      dest = 'Room ' + (order.room_number || (order.room ? order.room.room_number : ''));
    } else if (order.order_type === 'Table Dine-in') {
      dest = (order.table_number || 'Table') + (order.room_number ? ' (Room ' + order.room_number + ')' : '');
    } else if (order.order_type === 'Takeaway') {
      dest = 'Takeaway Counter' + (order.table_number ? ' (' + order.table_number + ')' : '');
    }

    const guestName = order.customer_name || 'Guest';
    const billingType = order.billing_type || 'Cash Paid';
    const grandTotalFmt = Number(order.total_amount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const notesStr = order.special_instructions ? `Instructions: ${order.special_instructions}` : 'Instructions: None';

    let guestItemsHtml = '';
    let kitchenItemsHtml = '';
    let totQty = 0;

    (order.items || []).forEach(it => {
      const q = parseInt(it.quantity, 10);
      totQty += q;
      const lineTotal = Number(it.line_total || (it.unit_price * q)).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      
      guestItemsHtml += `
        <tr>
          <td class="py-1">
            <div class="font-bold text-slate-900">${escapeHtml(it.item_name)}</div>
            ${it.instructions ? `<div class="text-[9.5px] italic text-slate-500">${escapeHtml(it.instructions)}</div>` : ''}
          </td>
          <td class="text-center font-bold font-mono py-1">${q}</td>
          <td class="text-right font-mono py-1">₹ ${lineTotal}</td>
        </tr>
      `;

      kitchenItemsHtml += `
        <tr>
          <td class="py-1">
            <div class="font-bold text-slate-900">${escapeHtml(it.item_name)}</div>
            ${it.instructions ? `<div class="text-[10px] font-bold text-rose-700 bg-rose-50 px-1 rounded inline-block mt-0.5">Note: ${escapeHtml(it.instructions)}</div>` : ''}
          </td>
          <td class="text-center font-black text-xs font-mono py-1 text-slate-900">${q}</td>
          <td class="text-right font-mono text-[10px] text-slate-600 py-1">₹ ${lineTotal}</td>
        </tr>
      `;
    });

    // Populate Guest Ticket
    document.getElementById('kot-guest-order-num').innerHTML = `<strong>KOT: ${order.order_number || 'POS'}</strong>`;
    document.getElementById('kot-guest-time').textContent = `Time: ${timeStr}`;
    document.getElementById('kot-guest-dest').textContent = dest;
    document.getElementById('kot-guest-name').textContent = guestName;
    document.getElementById('kot-guest-billing').textContent = billingType;
    document.getElementById('kot-guest-items-table').innerHTML = guestItemsHtml;
    document.getElementById('kot-guest-total-qty').textContent = totQty;
    document.getElementById('kot-guest-grand-total').textContent = grandTotalFmt;
    document.getElementById('kot-guest-notes-line').textContent = notesStr;

    // Populate Kitchen Ticket
    document.getElementById('kot-kitchen-order-num').innerHTML = `<strong>KOT: ${order.order_number || 'POS'}</strong>`;
    document.getElementById('kot-kitchen-time').textContent = `Time: ${timeStr}`;
    document.getElementById('kot-kitchen-dest').textContent = dest;
    document.getElementById('kot-kitchen-name').textContent = guestName;
    document.getElementById('kot-kitchen-billing').textContent = billingType;
    document.getElementById('kot-kitchen-items-table').innerHTML = kitchenItemsHtml;
    document.getElementById('kot-kitchen-total-qty').textContent = totQty;
    document.getElementById('kot-kitchen-grand-total').textContent = grandTotalFmt;
    document.getElementById('kot-kitchen-notes-line').textContent = notesStr;

    switchKotPreview('both');

    const m = document.getElementById('kot-modal');
    if (m) {
      m.classList.remove('hidden');
      m.style.display = 'flex';
    }
  }

  function switchKotPreview(mode) {
    const tGuest = document.getElementById('kot-ticket-guest');
    const tKitchen = document.getElementById('kot-ticket-kitchen');
    const strip = document.getElementById('kot-tear-strip');
    const bBoth = document.getElementById('tab-kot-both');
    const bGuest = document.getElementById('tab-kot-guest');
    const bKitchen = document.getElementById('tab-kot-kitchen');

    [bBoth, bGuest, bKitchen].forEach(b => {
      if (b) b.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold text-slate-600 hover:text-slate-900 cursor-pointer transition-all';
    });

    if (mode === 'guest') {
      if (tGuest) tGuest.style.display = 'block';
      if (tKitchen) tKitchen.style.display = 'none';
      if (strip) strip.style.display = 'none';
      if (bGuest) bGuest.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-600 text-white shadow-xs cursor-pointer transition-all';
    } else if (mode === 'kitchen') {
      if (tGuest) tGuest.style.display = 'none';
      if (tKitchen) tKitchen.style.display = 'block';
      if (strip) strip.style.display = 'none';
      if (bKitchen) bKitchen.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-600 text-white shadow-xs cursor-pointer transition-all';
    } else {
      // both
      if (tGuest) tGuest.style.display = 'block';
      if (tKitchen) tKitchen.style.display = 'block';
      if (strip) strip.style.display = 'block';
      if (bBoth) bBoth.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-600 text-white shadow-xs cursor-pointer transition-all';
    }
  }

  function closeKotModal() {
    const m = document.getElementById('kot-modal');
    if (m) {
      m.classList.add('hidden');
      m.style.display = 'none';
    }
  }

  function printThermalTicket(mode = 'both') {
    const tGuest = document.getElementById('kot-ticket-guest');
    const tKitchen = document.getElementById('kot-ticket-kitchen');
    if (!tGuest || !tKitchen) return;

    let printHtml = '';
    if (mode === 'guest') {
      printHtml = tGuest.outerHTML;
    } else if (mode === 'kitchen') {
      printHtml = tKitchen.outerHTML;
    } else {
      printHtml = `
        ${tGuest.outerHTML}
        <div class="print-cut-line">
          - - - - - - - - - - - - - - - - - - - -<br>
          ✂ CUT / TEAR HERE (KITCHEN COPY) ✂<br>
          - - - - - - - - - - - - - - - - - - - -
        </div>
        ${tKitchen.outerHTML}
      `;
    }

    let iframe = document.getElementById('kot-print-frame');
    if (!iframe) {
      iframe = document.createElement('iframe');
      iframe.id = 'kot-print-frame';
      iframe.style.position = 'fixed';
      iframe.style.right = '0';
      iframe.style.bottom = '0';
      iframe.style.width = '0';
      iframe.style.height = '0';
      iframe.style.border = '0';
      document.body.appendChild(iframe);
    }
    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write('<!DOCTYPE html><html><' + 'head><title>Kitchen Order Ticket</title><style>');
    doc.write(`
      @page { size: 80mm auto; margin: 2mm; }
      body { font-family: monospace, -apple-system, sans-serif; padding: 2mm; font-size: 11.5px; color: #000; margin: 0; background: #fff; }
      .kot-ticket-slip { width: 100%; max-width: 310px; margin: 0 auto; page-break-inside: avoid; }
      h3 { text-align: center; margin: 0 0 3px; font-size: 14px; font-weight: 900; }
      .center-sub { text-align: center; font-size: 10.5px; margin: 3px 0; }
      .divider-line { border-bottom: 1px dashed #000; margin: 6px 0; }
      table { width: 100%; border-collapse: collapse; margin: 5px 0; font-size: 11px; }
      th, td { padding: 3px 0; vertical-align: top; }
      th { border-bottom: 1px solid #000; }
      .text-center { text-align: center; }
      .text-right { text-align: right; }
      .font-bold { font-weight: bold; }
      .font-black { font-weight: 900; }
      .font-mono { font-family: monospace; }
      .print-cut-line { text-align: center; font-size: 9.5px; margin: 10px 0; page-break-after: always; padding: 6px 0; border-top: 1px dashed #000; border-bottom: 1px dashed #000; }
    `);
    doc.write('</' + 'style></' + 'head><' + 'body>');
    doc.write(printHtml);
    doc.write('</' + 'body></' + 'html>');
    doc.close();

    setTimeout(() => {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
    }, 250);
  }

  // 3. Export CSV Helper
  function exportOrdersTableCSV() {
    const table = document.getElementById('pos-orders-table');
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');
    rows.forEach(r => {
      let rowData = [];
      const cols = r.querySelectorAll('th, td');
      cols.forEach((c, idx) => {
        // Skip status select & action button columns
        if (idx === 6 || idx === 7) return;
        let text = c.innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""').trim();
        rowData.push('"' + text + '"');
      });
      if (rowData.length > 0) csv.push(rowData.join(','));
    });

    const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'pos_orders_kot_' + new Date().toISOString().slice(0, 10) + '.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, function(m) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
    });
  }

  function changePosPerPage(val) {
    const form = document.getElementById('pos-orders-filter-form');
    if (form) {
      const input = document.getElementById('pos-filter-per-page');
      if (input) input.value = val;
      form.submit();
    } else {
      const url = new URL(window.location.href);
      url.searchParams.set('per_page', val);
      url.searchParams.set('page', '1');
      window.location.href = url.toString();
    }
  }
</script>
@endpush
