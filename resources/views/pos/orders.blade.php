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
      <form method="GET" action="{{ route('pos.orders.index') }}" class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2.5 flex-1 min-w-[280px]">
          <!-- Search -->
          <div class="relative flex-1 min-w-[220px] max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" name="search" class="h-9.5 pl-8 pr-3 text-xs font-medium rounded-xl border border-slate-200 bg-white focus:outline-none focus:border-indigo-500 w-full" placeholder="Search order #, guest, room, table, item..." value="{{ request('search') }}">
          </div>

          <!-- Order Type -->
          <select name="type" class="h-9.5 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
            <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>All Order Types</option>
            <option value="Room Dining" {{ request('type') === 'Room Dining' ? 'selected' : '' }}>Room Dining</option>
            <option value="Table Dine-in" {{ request('type') === 'Table Dine-in' ? 'selected' : '' }}>Table Dine-in</option>
            <option value="Takeaway" {{ request('type') === 'Takeaway' ? 'selected' : '' }}>Takeaway</option>
          </select>

          <!-- Status -->
          <select name="status" class="h-9.5 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
            <option value="Received" {{ request('status') === 'Received' ? 'selected' : '' }}>Received</option>
            <option value="In Kitchen" {{ request('status') === 'In Kitchen' ? 'selected' : '' }}>In Kitchen</option>
            <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
            <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>

          <!-- Billing Mode -->
          <select name="billing_type" class="h-9.5 px-3 text-xs font-semibold rounded-xl border border-slate-200 bg-white text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
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
      <div class="px-5 py-4 border-b border-slate-200/80 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">KOT Tickets &amp; Order Register</h2>
          <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
            {{ $orders->total() }} Records
          </span>
        </div>
        <span class="text-xs text-slate-400 font-medium">Page {{ $orders->currentPage() }} of {{ $orders->lastPage() ?: 1 }}</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs" id="pos-orders-table">
          <thead>
            <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
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
                </td>

                <!-- Status Select -->
                <td class="px-4 py-3 text-center">
                  <select class="h-8 text-xs font-bold rounded-lg border border-slate-200 bg-white px-2.5 focus:outline-none focus:border-indigo-500 cursor-pointer shadow-2xs" onchange="updateOrderStatus({{ $order->id }}, this.value)">
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
                <td colspan="8" class="text-center text-slate-400 py-16 text-xs font-medium">
                  <i class="fa-solid fa-receipt text-3xl mb-2 block text-slate-300"></i>
                  No POS orders or KOT tickets found matching the selected criteria.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if($orders->hasPages())
        <div class="px-5 py-4 bg-slate-50/70 border-t border-slate-200/80 flex items-center justify-between flex-wrap gap-3">
          <div class="text-xs text-slate-500 font-medium">
            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} orders
          </div>
          <div>
            {{ $orders->links() }}
          </div>
        </div>
      @endif
    </div>

  </div>
</main>

<!-- Thermal KOT Ticket Modal -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="kot-modal">
  <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
    <div class="bg-slate-900 text-white px-5 py-3.5 flex items-center justify-between">
      <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
        <i class="fa-solid fa-print text-indigo-400"></i> Kitchen Order Ticket (KOT)
      </h3>
      <button type="button" onclick="closeKotModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-base cursor-pointer transition-colors">&times;</button>
    </div>
    <div class="p-5 bg-slate-50 overflow-y-auto max-h-[70vh]">
      <div class="thermal-ticket-card bg-white p-4 border border-dashed border-slate-300 rounded-lg font-mono text-xs shadow-xs text-slate-900" id="printable-kot-area">
        <h3 class="text-center font-black text-sm tracking-wide">HOTEL SAGAR SONNET</h3>
        <div class="text-center text-[11px] text-slate-500 font-semibold my-1">
          Sea Breeze Dining &amp; Room Dining<br>*** KITCHEN ORDER TICKET (KOT) ***
        </div>
        <div class="border-b border-dashed border-slate-300 my-2"></div>
        <div class="flex justify-between text-[11px]">
          <span id="kot-order-num"><strong>KOT: --</strong></span>
          <span id="kot-time">Time: --:--</span>
        </div>
        <div class="text-[11px] mt-0.5"><strong>Type:</strong> <span id="kot-dest">--</span></div>
        <div class="text-[11px] mt-0.5"><strong>Guest / Host:</strong> <span id="kot-guest">--</span></div>
        <div class="text-[11px] mt-0.5"><strong>Billing Mode:</strong> <span id="kot-billing" class="font-bold text-indigo-700">--</span></div>
        <div class="border-b border-dashed border-slate-300 my-2"></div>
        <table class="w-full border-collapse text-left text-[11px]">
          <thead>
            <tr class="border-b border-slate-300">
              <th class="py-1">Item</th>
              <th class="text-center w-10 py-1">Qty</th>
              <th class="text-right w-16 py-1">Amount</th>
            </tr>
          </thead>
          <tbody id="kot-items-table" class="divide-y divide-slate-100">
            <!-- Dynamic KOT Items -->
          </tbody>
        </table>
        <div class="border-b border-dashed border-slate-300 my-2"></div>
        <div class="flex justify-between font-black text-xs">
          <span>Total Qty: <span id="kot-total-qty">0</span></span>
          <span>Grand Total: ₹ <span id="kot-grand-total">0</span></span>
        </div>
        <div class="border-b border-dashed border-slate-300 my-2"></div>
        <div id="kot-notes-line" class="italic text-[10px] text-slate-600">
          Instructions: None
        </div>
        <div class="border-b border-dashed border-slate-300 my-2"></div>
        <div class="text-center text-[10px] text-slate-400 mt-1">Served by: Debashis Roy | System Printed</div>
      </div>
    </div>
    <div class="px-5 py-3.5 bg-white border-t border-slate-200 flex justify-end items-center gap-2">
      <button type="button" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer" onclick="closeKotModal()">Close</button>
      <button type="button" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer" onclick="printThermalTicket()">
        <i class="fa-solid fa-print text-[11px]"></i> Print KOT Ticket
      </button>
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

  function showKotModal(order) {
    document.getElementById('kot-order-num').innerHTML = '<strong>KOT: ' + (order.order_number || 'POS') + '</strong>';
    const createdAt = new Date(order.created_at);
    document.getElementById('kot-time').textContent = createdAt.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    let dest = order.order_type;
    if (order.order_type === 'Room Dining') {
      dest = 'Room ' + (order.room_number || (order.room ? order.room.room_number : ''));
    } else if (order.order_type === 'Table Dine-in') {
      dest = (order.table_number || 'Table') + (order.room_number ? ' (Room ' + order.room_number + ')' : '');
    } else if (order.order_type === 'Takeaway') {
      dest = 'Takeaway Counter' + (order.table_number ? ' (' + order.table_number + ')' : '');
    }
    document.getElementById('kot-dest').textContent = dest;
    document.getElementById('kot-guest').textContent = order.customer_name || 'Guest';
    document.getElementById('kot-billing').textContent = order.billing_type || 'Cash Paid';

    const tbody = document.getElementById('kot-items-table');
    tbody.innerHTML = '';
    let totalQty = 0;
    (order.items || []).forEach(it => {
      totalQty += parseInt(it.quantity, 10);
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="py-1">
          <div class="font-bold">${escapeHtml(it.item_name)}</div>
          ${it.instructions ? '<div class="text-[9.5px] italic text-slate-500">' + escapeHtml(it.instructions) + '</div>' : ''}
        </td>
        <td class="text-center py-1 font-bold font-mono">${it.quantity}</td>
        <td class="text-right py-1 font-mono">₹ ${(parseFloat(it.line_total || (it.unit_price * it.quantity))).toFixed(2)}</td>
      `;
      tbody.appendChild(tr);
    });

    document.getElementById('kot-total-qty').textContent = totalQty;
    document.getElementById('kot-grand-total').textContent = parseFloat(order.total_amount).toFixed(2);
    document.getElementById('kot-notes-line').textContent = 'Instructions: ' + (order.special_instructions || 'None');

    const m = document.getElementById('kot-modal');
    if (m) {
      m.classList.remove('hidden');
      m.style.display = 'flex';
    }
  }

  function closeKotModal() {
    const m = document.getElementById('kot-modal');
    if (m) {
      m.classList.add('hidden');
      m.style.display = 'none';
    }
  }

  function printThermalTicket() {
    const printArea = document.getElementById('printable-kot-area');
    if (!printArea) return;

    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    document.body.appendChild(iframe);

    const doc = iframe.contentWindow.document;
    doc.open();
    doc.write('<html><head><title>Kitchen Order Ticket</title>');
    doc.write('<style>');
    doc.write('@page { size: 80mm auto; margin: 3mm; }');
    doc.write('body { font-family: monospace, -apple-system, sans-serif; font-size: 11px; margin: 0; padding: 2mm; color: #000; }');
    doc.write('h3 { margin: 0 0 4px 0; font-size: 14px; text-align: center; }');
    doc.write('.center-sub { text-align: center; font-size: 10px; margin-bottom: 6px; }');
    doc.write('.divider-line { border-bottom: 1px dashed #000; margin: 6px 0; }');
    doc.write('table { width: 100%; border-collapse: collapse; font-size: 10.5px; }');
    doc.write('th, td { padding: 3px 0; }');
    doc.write('th { border-bottom: 1px solid #000; }');
    doc.write('.text-right { text-align: right; }');
    doc.write('.text-center { text-align: center; }');
    doc.write('</style></head><body>');
    doc.write(printArea.innerHTML);
    doc.write('</body></html>');
    doc.close();

    setTimeout(() => {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
      setTimeout(() => {
        document.body.removeChild(iframe);
      }, 1000);
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
</script>
@endpush
