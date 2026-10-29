@extends('frontoffice.layouts.app')

@section('title', 'Banquet & Event Services, Room Dining & Amenities - Hotel Sagar Sonnet')


@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport">
  <div class="w-full flex flex-col gap-4 pms-page-container">

  <!-- KPI Overview Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-champagne-glasses"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Banquet Halls &amp; Lawns</h4>
        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $totalHalls }} <span class="text-xs text-slate-500 font-sans font-medium">Venues</span></div>
      </div>
    </div>

    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200/80 text-blue-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-bell-concierge"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Active Room Orders</h4>
        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $activeRoomOrdersCount }} <span class="text-xs text-slate-500 font-sans font-medium">In Progress</span></div>
      </div>
    </div>

    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-pink-50 border border-pink-200/80 text-pink-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-calendar-check"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Confirmed Events</h4>
        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $totalEventsBooked }} <span class="text-xs text-slate-500 font-sans font-medium">Bookings</span></div>
      </div>
    </div>

    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-indian-rupee-sign"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Services Revenue</h4>
        <div class="text-xl font-bold font-mono text-emerald-600 mt-0.5">₹ {{ number_format($totalServicesRevenue, 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Header Action Banner -->
  <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 border border-amber-200/60 flex items-center justify-center text-xl shrink-0 shadow-xs">
        <i class="fa-solid fa-bell-concierge"></i>
      </div>
      <div>
        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Banquet Halls, Event Catering &amp; In-Room Dining</h2>
        <p class="text-xs text-slate-500 mt-0.5">Manage guest room service orders, kitchen dispatch, event halls &amp; function sheets</p>
      </div>
    </div>
    <div class="flex items-center gap-2.5 flex-wrap">
      <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-all shadow-xs cursor-pointer" onclick="openNewRoomOrderModal()">
        <i class="fa-solid fa-utensils"></i> New Room Order
      </button>
      <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition-all shadow-xs cursor-pointer" onclick="openNewBanquetModal()">
        <i class="fa-solid fa-calendar-plus"></i> Book Event Hall
      </button>
    </div>
  </div>

  <!-- Segmented Tabs Navigation -->
  <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 overflow-x-auto scrollbar-none">
    <button type="button" class="bnq-tab-btn active inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-slate-900 text-white shadow-sm cursor-pointer" id="tab-btn-orders" onclick="switchBanquetTab('orders')">
      <i class="fa-solid fa-receipt"></i> Room Service Orders
      <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-200">{{ $roomOrders->count() }}</span>
    </button>
    <button type="button" class="bnq-tab-btn inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer" id="tab-btn-halls" onclick="switchBanquetTab('halls')">
      <i class="fa-solid fa-champagne-glasses"></i> Banquet Halls &amp; Events
      <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">{{ $banquetBookings->count() }} Booked</span>
    </button>
    <button type="button" class="bnq-tab-btn inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer" id="tab-btn-menu" onclick="switchBanquetTab('menu')">
      <i class="fa-solid fa-book-open"></i> In-Room Menu &amp; Services Catalog
      <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">{{ $services->count() }}</span>
    </button>
  </div>

  <!-- TAB 1: ROOM SERVICE & AMENITY ORDERS -->
  <div id="bnq-tab-orders" class="bnq-tab-pane space-y-4">
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
      <div class="flex items-center gap-3 flex-wrap flex-1">
        <div class="relative flex-1 min-w-[240px] max-w-md">
          <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input type="text" id="order-search-box" placeholder="Search Room #, Guest, Order ID..." onkeyup="filterOrdersTable()" class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-xl text-xs placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>

        <select id="filter-order-status" class="px-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" onchange="filterOrdersTable()">
          <option value="all">All Order Statuses</option>
          <option value="Received">Received / New</option>
          <option value="Preparing">Preparing in Kitchen</option>
          <option value="Out for Delivery">Out for Delivery</option>
          <option value="Delivered">Delivered</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>

      <button type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-all shadow-xs shrink-0 cursor-pointer" onclick="openNewRoomOrderModal()">
        <i class="fa-solid fa-plus"></i> Place Room Order
      </button>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200/70 text-xs">
          <thead>
            <tr class="bg-slate-50/75 border-b border-slate-200/70">
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Order #</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Room</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Guest Name</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Order Items &amp; Qty</th>
              <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Total Bill</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Billing Mode</th>
              <th class="py-3 px-4 text-center font-bold text-slate-500 uppercase tracking-wider">Delivery Status</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Order Time</th>
              <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody id="orders-table-body" class="divide-y divide-slate-100 bg-white">
            @forelse($roomOrders as $ord)
              <tr data-order="{{ strtolower($ord->order_number) }}"
                  data-room="{{ strtolower($ord->room_number ?? '') }}"
                  data-guest="{{ strtolower($ord->guest_name ?? '') }}"
                  data-status="{{ $ord->order_status }}"
                  class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 font-mono font-bold text-indigo-600 whitespace-nowrap">
                  {{ $ord->order_number }}
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold text-[11px]">
                    <i class="fa-solid fa-door-open text-[10px]"></i>
                    Room {{ $ord->room_number }}
                  </span>
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900">{{ $ord->guest_name ?? 'In-House Guest' }}</div>
                  @if($ord->booking?->settle_number)
                    <div class="text-[10px] text-slate-500 font-mono">Folio: {{ $ord->booking->settle_number }}</div>
                  @endif
                </td>
                <td class="py-3 px-4">
                  <div class="text-xs text-slate-700 space-y-0.5">
                    @foreach($ord->items as $it)
                      <div>&bull; <strong class="font-bold text-slate-900">{{ $it->quantity }}x</strong> {{ $it->item_name }}</div>
                    @endforeach
                  </div>
                </td>
                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                  ₹ {{ number_format($ord->total_amount, 2) }}
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $ord->payment_status === 'Charged to Room Folio' ? 'text-indigo-600' : 'text-emerald-600' }}">
                    <i class="fa-solid {{ $ord->payment_status === 'Charged to Room Folio' ? 'fa-file-invoice-dollar' : 'fa-check' }}"></i>
                    {{ $ord->payment_status }}
                  </span>
                </td>
                <td class="py-3 px-4 text-center whitespace-nowrap">
                  @if($ord->order_status === 'Received')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                      <i class="fa-solid fa-bell text-[10px]"></i> Received
                    </span>
                  @elseif($ord->order_status === 'Preparing')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                      <i class="fa-solid fa-fire text-[10px]"></i> Preparing
                    </span>
                  @elseif($ord->order_status === 'Out for Delivery')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                      <i class="fa-solid fa-person-walking-luggage text-[10px]"></i> Out for Delivery
                    </span>
                  @elseif($ord->order_status === 'Delivered')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      <i class="fa-solid fa-circle-check text-[10px]"></i> Delivered
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                      Cancelled
                    </span>
                  @endif
                </td>
                <td class="py-3 px-4 text-slate-500 font-mono text-[11px] whitespace-nowrap">
                  {{ $ord->created_at->format('h:i A') }}
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <div class="flex items-center justify-end gap-1.5">
                    <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="viewOrderDossier({{ $ord->id }})">
                      <i class="fa-solid fa-eye text-[11px]"></i> Details
                    </button>
                    @if($ord->order_status === 'Received')
                      <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="updateOrderStatusAjax({{ $ord->id }}, 'Preparing')">
                        Prep
                      </button>
                    @elseif($ord->order_status === 'Preparing')
                      <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-fuchsia-600 hover:bg-fuchsia-700 text-white text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="updateOrderStatusAjax({{ $ord->id }}, 'Out for Delivery')">
                        Dispatch
                      </button>
                    @elseif($ord->order_status === 'Out for Delivery')
                      <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="updateOrderStatusAjax({{ $ord->id }}, 'Delivered')">
                        Delivered
                      </button>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="py-12 px-4 text-center text-slate-500">
                  <div class="flex flex-col items-center justify-center">
                    <i class="fa-solid fa-bell-concierge text-3xl text-slate-300 mb-2"></i>
                    <p class="font-medium text-slate-600">No room service orders placed today.</p>
                    <p class="text-xs text-slate-400 mt-1">Click <strong class="font-semibold text-slate-700">"New Room Order"</strong> above to send food, beverages, or amenities directly to an in-house room.</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- TAB 2: BANQUET HALLS & EVENT BOOKINGS -->
  <div id="bnq-tab-halls" class="bnq-tab-pane space-y-6 hidden">
    <!-- Available Venues Section -->
    <div>
      <div class="text-xs font-bold text-slate-900 mb-3 flex items-center gap-2 uppercase tracking-wider">
        <i class="fa-solid fa-building-columns text-amber-600"></i> Banquet Venues &amp; Conference Facilities
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($halls as $hall)
          <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all p-5 flex flex-col justify-between">
            <div class="space-y-3">
              <div class="flex items-start justify-between gap-3">
                <h3 class="text-sm font-bold text-slate-900">{{ $hall->name }}</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $hall->status === 'Available' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                  {{ $hall->status }}
                </span>
              </div>
              <div class="flex items-center gap-3 text-xs text-slate-600 flex-wrap">
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-users text-indigo-500"></i> Up to <strong class="font-bold text-slate-800">{{ $hall->capacity_pax }} Pax</strong></span>
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-layer-group text-cyan-600"></i> {{ $hall->type }}</span>
              </div>
              <div class="text-xs text-slate-600 flex items-start gap-1.5 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 shrink-0 text-xs"></i>
                <span>{{ $hall->amenities }}</span>
              </div>
            </div>
            <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-100">
              <div>
                <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Base Rental</div>
                <div class="font-mono font-bold text-slate-900 text-sm">₹ {{ number_format($hall->base_tariff, 2) }}</div>
              </div>
              <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition-all shadow-xs cursor-pointer" onclick="openBookSpecificHallModal({{ $hall->id }}, '{{ addslashes($hall->name) }}', {{ $hall->base_tariff }})">
                <i class="fa-solid fa-calendar-plus text-[11px]"></i> Reserve Hall
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Active Event Bookings Table -->
    <div class="space-y-4">
      <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="font-bold text-slate-900 text-xs flex items-center gap-2 uppercase tracking-wider">
          <i class="fa-solid fa-calendar-days text-amber-600"></i> Confirmed Event Reservations &amp; Function Sheets
        </div>
        <button type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition-all shadow-xs shrink-0 cursor-pointer" onclick="openNewBanquetModal()">
          <i class="fa-solid fa-plus"></i> Book Event Hall
        </button>
      </div>

      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200/70 text-xs">
            <thead>
              <tr class="bg-slate-50/75 border-b border-slate-200/70">
                <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Ref. No</th>
                <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Hall Name</th>
                <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Event Name</th>
                <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Client &amp; Contact</th>
                <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Event Date &amp; Slot</th>
                <th class="py-3 px-4 text-center font-bold text-slate-500 uppercase tracking-wider">Pax</th>
                <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Total Tariff</th>
                <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Advance Paid</th>
                <th class="py-3 px-4 text-center font-bold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              @forelse($banquetBookings as $bk)
                <tr class="hover:bg-slate-50/80 transition-colors">
                  <td class="py-3 px-4 font-mono font-bold text-amber-600 whitespace-nowrap">{{ $bk->booking_reference }}</td>
                  <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">{{ $bk->hall?->name ?? 'Grand Ballroom' }}</td>
                  <td class="py-3 px-4">
                    <div class="font-bold text-slate-900">{{ $bk->event_name }}</div>
                    @if($bk->company_name)
                      <div class="text-[10px] text-slate-500">{{ $bk->company_name }}</div>
                    @endif
                  </td>
                  <td class="py-3 px-4">
                    <div class="font-semibold text-slate-800">{{ $bk->client_name }}</div>
                    <div class="text-[10px] text-slate-500 font-mono">{{ $bk->phone }}</div>
                  </td>
                  <td class="py-3 px-4 whitespace-nowrap">
                    <div class="font-bold text-slate-900">{{ $bk->event_date->format('d M Y') }}</div>
                    <div class="text-[10px] text-slate-500">{{ $bk->time_slot }}</div>
                  </td>
                  <td class="py-3 px-4 text-center font-mono font-bold text-slate-800">{{ $bk->guaranteed_pax }}</td>
                  <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">₹ {{ number_format($bk->total_amount, 2) }}</td>
                  <td class="py-3 px-4 text-right font-mono font-bold text-emerald-600 whitespace-nowrap">₹ {{ number_format($bk->advance_paid, 2) }}</td>
                  <td class="py-3 px-4 text-center whitespace-nowrap">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      <i class="fa-solid fa-circle-check text-[10px]"></i> {{ $bk->status }}
                    </span>
                  </td>
                  <td class="py-3 px-4 text-right whitespace-nowrap">
                    <button type="button" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="viewFunctionSheet({{ $bk->id }})">
                      <i class="fa-solid fa-file-lines text-[11px]"></i> Function Sheet
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="py-12 px-4 text-center text-slate-500">
                    <div class="flex flex-col items-center justify-center">
                      <i class="fa-solid fa-calendar-xmark text-3xl text-slate-300 mb-2"></i>
                      <p class="font-medium text-slate-600">No banquet event bookings recorded.</p>
                      <p class="text-xs text-slate-400 mt-1">Click "Book Event Hall" above to schedule an upcoming banquet reservation.</p>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- TAB 3: IN-ROOM MENU & SERVICES CATALOG -->
  <div id="bnq-tab-menu" class="bnq-tab-pane space-y-4 hidden">
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div class="text-xs font-bold text-slate-900 flex items-center gap-2 uppercase tracking-wider">
        <i class="fa-solid fa-book-open text-cyan-600"></i> Available Room Dining Dishes &amp; Guest Amenities
      </div>
      <div class="text-xs text-slate-500">
        Synchronized with Front Desk, Housekeeping and Room Service Kitchen
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200/70 text-xs">
          <thead>
            <tr class="bg-slate-50/75 border-b border-slate-200/70">
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider w-20">Code</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Service / Dish Name</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Category</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Unit / Serving</th>
              <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Tariff / Price</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Description</th>
              <th class="py-3 px-4 text-center font-bold text-slate-500 uppercase tracking-wider">Status</th>
              <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            @foreach($services as $srv)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="font-mono font-bold text-cyan-700 bg-cyan-50 border border-cyan-200 px-2 py-0.5 rounded-md text-[11px]">{{ $srv->code }}</span>
                </td>
                <td class="py-3 px-4 font-bold text-slate-900">{{ $srv->name }}</td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">{{ $srv->category }}</span>
                </td>
                <td class="py-3 px-4 text-slate-500 whitespace-nowrap">{{ $srv->unit }}</td>
                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">₹ {{ number_format($srv->price, 2) }}</td>
                <td class="py-3 px-4 text-slate-600 max-w-xs truncate">{{ $srv->description }}</td>
                <td class="py-3 px-4 text-center whitespace-nowrap">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Available</span>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-all shadow-xs cursor-pointer" onclick="quickOrderThisService({{ $srv->id }}, '{{ addslashes($srv->name) }}', {{ $srv->price }})">
                    <i class="fa-solid fa-plus text-[10px]"></i> Order to Room
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  </div>
</main>

<!-- MODAL 1: NEW ROOM SERVICE ORDER -->
<div class="bnq-modal-overlay hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" id="modal-new-room-order">
  <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden my-8">
    <div class="bg-linear-to-r from-emerald-600 to-teal-700 px-6 py-4 flex items-center justify-between text-white">
      <h3 class="text-base font-bold flex items-center gap-2 text-white">
        <i class="fa-solid fa-utensils"></i> In-Room Dining &amp; Service Order
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-colors text-xl leading-none cursor-pointer" onclick="closeBnqModal('modal-new-room-order')">&times;</button>
    </div>
    <form id="form-room-order" onsubmit="submitRoomOrder(event)">
      @csrf
      <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Select In-House Room *</label>
            <select name="room_id" id="ro-room-select" required onchange="updateRoomOrderGuest()" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
              <option value="">Choose Occupied Room</option>
              @foreach($activeRooms as $rm)
                @php
                  $occGuest = $rm->activeBooking?->guest?->guest_name ?? ($rm->status === 'Occupied' ? 'Occupied Guest' : 'Vacant Room');
                  $isOcc = $rm->status === 'Occupied' || $rm->activeBooking;
                @endphp
                <option value="{{ $rm->id }}" data-guest="{{ $occGuest }}" data-room="{{ $rm->room_number }}">
                  Room #{{ $rm->room_number }} ({{ $rm->categoryRelation?->name ?? 'Standard' }}) - {{ $occGuest }}
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Guest Name</label>
            <input type="text" id="ro-guest-display" readonly placeholder="Guest name will load here" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-xs font-medium text-slate-700 bg-slate-50 focus:outline-none cursor-not-allowed">
          </div>
        </div>

        <div class="flex items-center justify-between pt-2">
          <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
            <i class="fa-solid fa-bowl-food text-emerald-600"></i> Order Line Items
          </span>
          <button type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="addOrderLineItem()">
            <i class="fa-solid fa-plus text-[10px]"></i> Add Item
          </button>
        </div>

        <!-- Dynamic Order Line Items -->
        <div id="order-line-items-container" class="space-y-2.5">
          <!-- Populated by JS -->
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Billing &amp; Settlement *</label>
            <select name="payment_status" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
              <option value="Charged to Room Folio">Charged to Room Folio (Bill at Checkout)</option>
              <option value="Paid Cash">Cash Paid upon Delivery</option>
              <option value="Paid UPI/Card">Digital UPI / POS Card Paid</option>
              <option value="Pending">Pending Collection</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Delivery Staff / Order Taker</label>
            <input type="text" name="served_by" placeholder="Server Name" value="Room Service Desk" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kitchen / Preparation Instructions</label>
          <textarea name="special_instructions" rows="2" placeholder="e.g. Less spicy, send extra napkins & cutlery, deliver at 8:30 PM" class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500"></textarea>
        </div>

        <!-- Order Total Summary -->
        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex items-center justify-between">
          <div>
            <div class="text-[11px] text-slate-500 font-medium">Subtotal + <span id="label-banquet-gst">{{ rtrim(rtrim(number_format($defaultGstBanquet ?? 5, 2), '0'), '.') }}</span>% F&amp;B Tax:</div>
            <div class="text-lg font-bold font-mono text-slate-900" id="ro-total-display">₹ 0.00</div>
          </div>
          <div class="text-xs font-bold text-emerald-600 flex items-center gap-1.5">
            <i class="fa-solid fa-clock"></i> Est. Delivery: 30-40 Mins
          </div>
        </div>
      </div>
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-200/80 flex items-center justify-end gap-3">
        <button type="button" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="closeBnqModal('modal-new-room-order')">Cancel</button>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition-all cursor-pointer">
          <i class="fa-solid fa-check"></i> Place Room Order
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 2: BOOK BANQUET HALL / EVENT -->
<div class="bnq-modal-overlay hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" id="modal-new-banquet">
  <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden my-8">
    <div class="bg-linear-to-r from-amber-600 to-amber-700 px-6 py-4 flex items-center justify-between text-white">
      <h3 class="text-base font-bold flex items-center gap-2 text-white">
        <i class="fa-solid fa-calendar-plus"></i> Reserve Banquet Hall or Lawn
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-colors text-xl leading-none cursor-pointer" onclick="closeBnqModal('modal-new-banquet')">&times;</button>
    </div>
    <form id="form-banquet-booking" onsubmit="submitBanquetBooking(event)">
      @csrf
      <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Select Banquet Hall / Venue *</label>
            <select name="banquet_hall_id" id="bq-hall-select" required onchange="calculateBanquetTariff()" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
              <option value="">Select Venue</option>
              @foreach($halls as $hl)
                <option value="{{ $hl->id }}" data-tariff="{{ $hl->base_tariff }}">
                  {{ $hl->name }} (Up to {{ $hl->capacity_pax }} Pax)
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Event Name / Occasion *</label>
            <input type="text" name="event_name" required placeholder="e.g. Annual Gala, Wedding, Conference" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Client / Host Name *</label>
            <input type="text" name="client_name" required placeholder="Full Name" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Company / Organization</label>
            <input type="text" name="company_name" placeholder="Optional Company Name" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Contact Phone *</label>
            <input type="text" name="phone" required placeholder="+91 XXXXX XXXXX" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-mono text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Contact Email</label>
            <input type="email" name="email" placeholder="client@example.com" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Event Date *</label>
            <input type="date" name="event_date" required value="{{ date('Y-m-d', strtotime('+3 days')) }}" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Time Slot *</label>
            <select name="time_slot" required class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
              <option value="Evening (6:30 PM - 11:30 PM)">Evening (6:30 PM - 11:30 PM)</option>
              <option value="Morning (9:00 AM - 2:00 PM)">Morning (9:00 AM - 2:00 PM)</option>
              <option value="Afternoon (1:00 PM - 5:00 PM)">Afternoon (1:00 PM - 5:00 PM)</option>
              <option value="Full Day (9:00 AM - 10:00 PM)">Full Day (9:00 AM - 10:00 PM)</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Guaranteed Guest Count (Pax) *</label>
            <input type="number" name="guaranteed_pax" id="bq-pax" required min="10" value="100" onkeyup="calculateBanquetTariff()" onchange="calculateBanquetTariff()" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Catering Plate Rate (₹)</label>
            <input type="number" name="catering_per_plate" id="bq-plate" placeholder="e.g. 1200" value="1000" onkeyup="calculateBanquetTariff()" onchange="calculateBanquetTariff()" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Base Hall Rental (₹) *</label>
            <input type="number" name="hall_rental" id="bq-rental" required value="50000" onkeyup="calculateBanquetTariff()" onchange="calculateBanquetTariff()" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Advance Payment Paid (₹)</label>
            <input type="number" name="advance_paid" id="bq-advance" value="25000" onkeyup="calculateBanquetTariff()" onchange="calculateBanquetTariff()" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Function Sheet &amp; Audio-Visual Notes</label>
          <textarea name="function_sheet_notes" rows="2" placeholder="Stage setup, buffet menu, projectors, welcome drinks..." class="w-full px-3.5 py-2 border border-slate-300 rounded-xl text-xs text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"></textarea>
        </div>

        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex items-center justify-between">
          <div>
            <div class="text-[11px] text-slate-500 font-medium">Calculated Total Event Estimate:</div>
            <div class="text-lg font-bold font-mono text-amber-600" id="bq-total-display">₹ 0.00</div>
          </div>
          <div class="text-right">
            <div class="text-[11px] text-slate-500 font-medium">Balance Due at Event:</div>
            <div class="text-sm font-bold font-mono text-slate-900" id="bq-balance-display">₹ 0.00</div>
          </div>
        </div>
      </div>
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-200/80 flex items-center justify-end gap-3">
        <button type="button" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="closeBnqModal('modal-new-banquet')">Cancel</button>
        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-xs transition-all cursor-pointer">
          <i class="fa-solid fa-calendar-check"></i> Confirm Reservation
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 3: VIEW ORDER DOSSIER -->
<div class="bnq-modal-overlay hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" id="modal-order-dossier">
  <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden my-8">
    <div class="bg-linear-to-r from-blue-600 to-indigo-700 px-6 py-4 flex items-center justify-between text-white">
      <h3 class="text-base font-bold flex items-center gap-2 text-white" id="dossier-order-title">
        <i class="fa-solid fa-receipt"></i> Room Order Dossier
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-colors text-xl leading-none cursor-pointer" onclick="closeBnqModal('modal-order-dossier')">&times;</button>
    </div>
    <div class="p-6 max-h-[75vh] overflow-y-auto space-y-3" id="dossier-order-content">
      <!-- Loaded dynamically -->
    </div>
    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200/80 flex justify-end">
      <button type="button" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="closeBnqModal('modal-order-dossier')">Close</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Services catalog JSON for order builder
  const servicesCatalog = @json($services);

  // Tab switching
  function switchBanquetTab(tabName) {
    document.querySelectorAll('.bnq-tab-pane').forEach(el => {
      el.classList.add('hidden');
      el.style.display = 'none';
    });
    document.querySelectorAll('.bnq-tab-btn').forEach(btn => {
      btn.classList.remove('active', 'bg-slate-900', 'text-white', 'shadow-sm');
      btn.classList.add('bg-white', 'border', 'border-slate-200/80', 'text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
      const badge = btn.querySelector('span');
      if (badge) {
        if (btn.id === 'tab-btn-orders') {
          badge.className = 'inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200';
        } else if (btn.id === 'tab-btn-halls') {
          badge.className = 'inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200';
        } else {
          badge.className = 'inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200';
        }
      }
    });

    const targetPane = document.getElementById('bnq-tab-' + tabName);
    const targetBtn = document.getElementById('tab-btn-' + tabName);

    if (targetPane) {
      targetPane.classList.remove('hidden');
      targetPane.style.display = 'block';
    }
    if (targetBtn) {
      targetBtn.classList.add('active', 'bg-slate-900', 'text-white', 'shadow-sm');
      targetBtn.classList.remove('bg-white', 'border', 'border-slate-200/80', 'text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
      const badge = targetBtn.querySelector('span');
      if (badge) {
        badge.className = 'inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white';
      }
    }
  }

  // Modal handlers
  function openBnqModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.remove('hidden');
      el.style.display = 'flex';
    }
  }

  function closeBnqModal(id) {
    const el = document.getElementById(id);
    if (el) {
      el.classList.add('hidden');
      el.style.display = 'none';
    }
  }

  function openNewRoomOrderModal() {
    document.getElementById('form-room-order').reset();
    document.getElementById('order-line-items-container').innerHTML = '';
    // add first 2 default rows
    addOrderLineItem(servicesCatalog[0]?.id || null);
    addOrderLineItem(servicesCatalog[1]?.id || null);
    calculateRoomOrderTotal();
    openBnqModal('modal-new-room-order');
  }

  function openNewBanquetModal() {
    document.getElementById('form-banquet-booking').reset();
    calculateBanquetTariff();
    openBnqModal('modal-new-banquet');
  }

  function openBookSpecificHallModal(hallId, hallName, baseTariff) {
    openNewBanquetModal();
    const sel = document.getElementById('bq-hall-select');
    sel.value = hallId;
    document.getElementById('bq-rental').value = baseTariff;
    calculateBanquetTariff();
  }

  function quickOrderThisService(serviceId, name, price) {
    openNewRoomOrderModal();
    document.getElementById('order-line-items-container').innerHTML = '';
    addOrderLineItem(serviceId);
    calculateRoomOrderTotal();
  }

  function updateRoomOrderGuest() {
    const select = document.getElementById('ro-room-select');
    const opt = select.options[select.selectedIndex];
    const guestInput = document.getElementById('ro-guest-display');
    if (opt && opt.value) {
      guestInput.value = opt.getAttribute('data-guest') || 'In-House Guest';
    } else {
      guestInput.value = '';
    }
  }

  // Order Line Items Builder
  let itemCounter = 0;
  function addOrderLineItem(selectedServiceId = null) {
    itemCounter++;
    const container = document.getElementById('order-line-items-container');
    const row = document.createElement('div');
    row.className = 'order-item-row flex items-center gap-2.5 bg-slate-50/80 p-2.5 rounded-xl border border-slate-200/80 transition-colors hover:border-slate-300';
    row.id = `item-row-${itemCounter}`;

    let optionsHtml = '<option value="">-- Custom Food / Amenity Item --</option>';
    servicesCatalog.forEach(s => {
      const isSelected = selectedServiceId && s.id == selectedServiceId ? 'selected' : '';
      optionsHtml += `<option value="${s.id}" data-price="${s.price}" data-name="${s.name}" ${isSelected}>[${s.code}] ${s.name} (₹ ${s.price})</option>`;
    });

    row.innerHTML = `
      <div class="flex-[2] min-w-0">
        <select class="item-service-select w-full px-3 py-2 border border-slate-300 rounded-xl text-xs bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" onchange="onServiceSelectChange(${itemCounter})">
          ${optionsHtml}
        </select>
        <input type="hidden" name="items[${itemCounter}][service_id]" id="input-service-id-${itemCounter}" value="${selectedServiceId || ''}">
        <input type="text" name="items[${itemCounter}][name]" id="input-item-name-${itemCounter}" placeholder="Custom item description" class="hidden w-full mt-1.5 px-3 py-1.5 border border-slate-300 rounded-lg text-xs bg-white text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-500">
      </div>
      <div class="w-24 shrink-0">
        <input type="number" step="0.01" name="items[${itemCounter}][price]" id="input-item-price-${itemCounter}" placeholder="Price" value="0.00" onkeyup="calculateRoomOrderTotal()" onchange="calculateRoomOrderTotal()" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 text-right bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
      </div>
      <div class="w-16 shrink-0">
        <input type="number" name="items[${itemCounter}][quantity]" id="input-item-qty-${itemCounter}" value="1" min="1" onkeyup="calculateRoomOrderTotal()" onchange="calculateRoomOrderTotal()" class="w-full px-2 py-2 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 text-center bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
      </div>
      <button type="button" onclick="removeOrderLineItem(${itemCounter})" class="w-8.5 h-8.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 hover:text-rose-700 flex items-center justify-center shrink-0 transition-colors shadow-xs cursor-pointer" title="Remove Item">
        <i class="fa-solid fa-trash-can text-xs"></i>
      </button>
    `;

    container.appendChild(row);

    if (selectedServiceId) {
      onServiceSelectChange(itemCounter);
    }
  }

  function removeOrderLineItem(index) {
    const row = document.getElementById(`item-row-${index}`);
    if (row) row.remove();
    calculateRoomOrderTotal();
  }

  function onServiceSelectChange(index) {
    const row = document.getElementById(`item-row-${index}`);
    if (!row) return;
    const select = row.querySelector('.item-service-select');
    if (!select || select.selectedIndex < 0) return;
    const selectedOpt = select.options[select.selectedIndex];
    const nameInput = document.getElementById(`input-item-name-${index}`);
    const priceInput = document.getElementById(`input-item-price-${index}`);
    const serviceIdInput = document.getElementById(`input-service-id-${index}`);

    if (selectedOpt && selectedOpt.value) {
      nameInput.value = selectedOpt.getAttribute('data-name');
      priceInput.value = selectedOpt.getAttribute('data-price');
      serviceIdInput.value = selectedOpt.value;
      nameInput.style.display = 'none';
      nameInput.classList.add('hidden');
    } else {
      nameInput.style.display = 'block';
      nameInput.classList.remove('hidden');
      serviceIdInput.value = '';
    }

    calculateRoomOrderTotal();
  }

  function calculateRoomOrderTotal() {
    let subtotal = 0;
    const rows = document.querySelectorAll('.order-item-row');
    rows.forEach(r => {
      const price = parseFloat(r.querySelector('[id^="input-item-price-"]').value) || 0;
      const qty = parseInt(r.querySelector('[id^="input-item-qty-"]').value) || 1;
      subtotal += (price * qty);
    });

    const banquetGstRate = {{ (float) ($defaultGstBanquet ?? 5.00) }};
    const tax = (subtotal * banquetGstRate) / 100;
    const total = subtotal + tax;

    document.getElementById('ro-total-display').innerText = `₹ ${total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  function calculateBanquetTariff() {
    const hallSelect = document.getElementById('bq-hall-select');
    const opt = hallSelect.options[hallSelect.selectedIndex];
    if (opt && opt.value && !document.getElementById('bq-rental').value) {
      document.getElementById('bq-rental').value = opt.getAttribute('data-tariff') || 50000;
    }

    const rental = parseFloat(document.getElementById('bq-rental').value) || 0;
    const pax = parseInt(document.getElementById('bq-pax').value) || 0;
    const plate = parseFloat(document.getElementById('bq-plate').value) || 0;
    const advance = parseFloat(document.getElementById('bq-advance').value) || 0;

    const total = rental + (pax * plate);
    const balance = Math.max(0, total - advance);

    document.getElementById('bq-total-display').innerText = `₹ ${total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    document.getElementById('bq-balance-display').innerText = `₹ ${balance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  // Filter orders
  function filterOrdersTable() {
    const q = document.getElementById('order-search-box').value.toLowerCase().trim();
    const st = document.getElementById('filter-order-status').value;

    const rows = document.querySelectorAll('#orders-table-body tr[data-order]');
    rows.forEach(r => {
      const order = r.getAttribute('data-order') || '';
      const room = r.getAttribute('data-room') || '';
      const guest = r.getAttribute('data-guest') || '';
      const status = r.getAttribute('data-status') || '';

      const matchQ = !q || order.includes(q) || room.includes(q) || guest.includes(q);
      const matchSt = st === 'all' || status === st;

      r.style.display = (matchQ && matchSt) ? '' : 'none';
    });
  }

  // AJAX Submissions
  async function submitRoomOrder(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
      const response = await fetch("{{ route('banquet.room-orders.store') }}", {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: formData
      });

      const res = await response.json();
      if (response.ok && res.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        } else {
          alert(res.message);
        }
        closeBnqModal('modal-new-room-order');
        setTimeout(() => location.reload(), 800);
      } else {
        const msg = res.message || (res.errors ? Object.values(res.errors).flat().join('<br>') : 'Error placing room order.');
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(msg, 'error');
        } else {
          alert(msg);
        }
      }
    } catch (err) {
      console.error(err);
      alert('Network error.');
    }
  }

  async function submitBanquetBooking(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    try {
      const response = await fetch("{{ route('banquet.bookings.store') }}", {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: formData
      });

      const res = await response.json();
      if (response.ok && res.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        } else {
          alert(res.message);
        }
        closeBnqModal('modal-new-banquet');
        setTimeout(() => location.reload(), 800);
      } else {
        const msg = res.message || (res.errors ? Object.values(res.errors).flat().join('<br>') : 'Error creating event booking.');
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(msg, 'error');
        } else {
          alert(msg);
        }
      }
    } catch (err) {
      console.error(err);
      alert('Network error.');
    }
  }

  async function updateOrderStatusAjax(orderId, newStatus) {
    try {
      const response = await fetch(`/banquet/room-orders/${orderId}/status`, {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({ order_status: newStatus })
      });

      const res = await response.json();
      if (response.ok && res.success) {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(res.message, 'success');
        } else {
          alert(res.message);
        }
        setTimeout(() => location.reload(), 600);
      } else {
        alert(res.message || 'Error updating order status.');
      }
    } catch (err) {
      console.error(err);
      alert('Network error.');
    }
  }

  async function viewOrderDossier(id) {
    try {
      const res = await fetch(`/banquet/room-orders/${id}`);
      const data = await res.json();
      if (data.success && data.order) {
        const o = data.order;
        document.getElementById('dossier-order-title').innerHTML = `<i class="fa-solid fa-receipt"></i> Order #${o.order_number} (Room ${o.room_number})`;

        let itemsHtml = '';
        o.items.forEach(it => {
          itemsHtml += `
            <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-b-0 text-xs">
              <div class="text-slate-800 font-medium"><strong class="font-bold text-slate-900 mr-1">${it.quantity}x</strong> ${it.item_name}</div>
              <div class="font-mono font-bold text-slate-800">₹ ${parseFloat(it.line_total).toFixed(2)}</div>
            </div>
          `;
        });

        document.getElementById('dossier-order-content').innerHTML = `
          <div class="grid grid-cols-2 gap-2.5 bg-slate-50 border border-slate-200/80 p-3.5 rounded-xl text-xs">
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Guest:</span> ${o.guest_name}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Room #:</span> ${o.room_number}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Billing:</span> <span class="capitalize font-semibold text-slate-800">${o.payment_status}</span></div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Status:</span> <span class="capitalize font-semibold text-indigo-700">${o.order_status}</span></div>
            <div class="col-span-2 text-slate-600"><span class="font-semibold text-slate-800">Special Instructions:</span> ${o.special_instructions || 'None'}</div>
          </div>
          <div class="font-bold text-xs text-slate-800 uppercase tracking-wider pt-2">Order Items</div>
          <div class="bg-white border border-slate-200/80 rounded-xl p-3.5 shadow-xs">
            ${itemsHtml}
            <div class="flex items-center justify-between pt-3 font-bold text-sm text-slate-900 border-t border-slate-200/80 mt-1">
              <div>Total (inc. taxes):</div>
              <div class="font-mono text-emerald-600 text-base font-extrabold">₹ ${parseFloat(o.total_amount).toFixed(2)}</div>
            </div>
          </div>
        `;

        openBnqModal('modal-order-dossier');
      }
    } catch (err) {
      console.error(err);
      alert('Unable to load order dossier.');
    }
  }

  async function viewFunctionSheet(id) {
    try {
      const res = await fetch(`/banquet/bookings/${id}/function-sheet`);
      const data = await res.json();
      if (data.success && data.booking) {
        const b = data.booking;
        document.getElementById('dossier-order-title').innerHTML = `<i class="fa-solid fa-file-lines"></i> Function Sheet - ${b.event_name}`;

        document.getElementById('dossier-order-content').innerHTML = `
          <div class="grid grid-cols-2 gap-2.5 bg-slate-50 border border-slate-200/80 p-3.5 rounded-xl text-xs">
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Venue:</span> ${b.hall ? b.hall.name : 'Grand Hall'}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Client:</span> ${b.client_name} (${b.phone || '-'})</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Event Date:</span> ${b.event_date}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Time Slot:</span> ${b.time_slot}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Pax Capacity:</span> <strong class="text-slate-800">${b.guaranteed_pax} Guests</strong></div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Status:</span> <span class="capitalize font-semibold text-emerald-700">${b.status}</span></div>
          </div>
          <div class="font-bold text-xs text-slate-800 uppercase tracking-wider pt-2">Event Setup &amp; AV Notes</div>
          <div class="bg-white border border-slate-200/80 rounded-xl p-3.5 text-xs leading-relaxed text-slate-700 shadow-xs">
            ${b.function_sheet_notes || 'Standard banquet cluster setup with audio-visual system and stage lighting.'}
          </div>
          <div class="flex items-center justify-between bg-amber-50 border border-amber-200 rounded-xl p-3.5 text-xs font-bold">
            <div>Total: <span class="font-mono text-slate-900">₹ ${parseFloat(b.total_amount).toFixed(2)}</span></div>
            <div class="text-emerald-700">Advance: <span class="font-mono">₹ ${parseFloat(b.advance_paid).toFixed(2)}</span></div>
            <div class="text-amber-700">Due: <span class="font-mono">₹ ${parseFloat(b.balance_due).toFixed(2)}</span></div>
          </div>
        `;

        openBnqModal('modal-order-dossier');
      }
    } catch (err) {
      console.error(err);
      alert('Unable to load function sheet.');
    }
  }
</script>
@endpush
