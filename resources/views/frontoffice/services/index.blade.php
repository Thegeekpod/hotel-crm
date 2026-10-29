@extends('frontoffice.layouts.app')

@section('title', 'Extra Services & In-Room Dining - Hotel Sagar Sonnet')


@section('content')
<main class="flex-1 w-full p-3 sm:p-4 lg:p-5 flex flex-col gap-4 pms-main-viewport services-container">
  <div class="w-full flex flex-col gap-4 pms-page-container">

  <!-- KPI Overview Cards -->
  <div class="services-kpi-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="kpi-card-srv bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex items-center gap-3.5">
      <div class="kpi-icon-srv w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
        <i class="fa-solid fa-bell-concierge"></i>
      </div>
      <div class="kpi-meta-srv">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider m-0">Available Services</h4>
        <div class="kpi-num text-xl font-extrabold text-slate-800 mt-0.5">{{ $totalServicesCount }} <span class="text-xs text-slate-400 font-semibold">Active Items</span></div>
      </div>
    </div>

    <div class="kpi-card-srv bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex items-center gap-3.5">
      <div class="kpi-icon-srv w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
        <i class="fa-solid fa-fire"></i>
      </div>
      <div class="kpi-meta-srv">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider m-0">Active Room Orders</h4>
        <div class="kpi-num text-xl font-extrabold text-slate-800 mt-0.5">{{ $activeRoomOrdersCount }} <span class="text-xs text-slate-400 font-semibold">In Progress</span></div>
      </div>
    </div>

    <div class="kpi-card-srv bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex items-center gap-3.5">
      <div class="kpi-icon-srv w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-xl shrink-0">
        <i class="fa-solid fa-circle-check"></i>
      </div>
      <div class="kpi-meta-srv">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider m-0">Delivered Orders</h4>
        <div class="kpi-num text-xl font-extrabold text-slate-800 mt-0.5">{{ $deliveredOrdersCount }} <span class="text-xs text-slate-400 font-semibold">Completed</span></div>
      </div>
    </div>

    <div class="kpi-card-srv bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex items-center gap-3.5">
      <div class="kpi-icon-srv w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
        <i class="fa-solid fa-indian-rupee-sign"></i>
      </div>
      <div class="kpi-meta-srv">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider m-0">Services Revenue</h4>
        <div class="kpi-num text-xl font-extrabold text-emerald-600 font-mono mt-0.5">₹ {{ number_format($totalServicesRevenue, 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Header Action Banner -->
  <div class="srv-header-banner bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-xs flex flex-wrap items-center justify-between gap-4">
    <div class="srv-header-left flex items-center gap-3.5">
      <div class="srv-header-badge w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shrink-0">
        <i class="fa-solid fa-bell-concierge"></i>
      </div>
      <div>
        <h2 class="m-0 text-lg font-extrabold text-slate-800">Extra Services &amp; In-Room Dining</h2>
        <p class="text-xs text-slate-500 mt-0.5 m-0">Manage guest room service orders, food dishes, laundry tariffs, and room amenities.</p>
      </div>
    </div>
  </div>

  <!-- Segmented Nav Tabs -->
  <div class="srv-nav-tabs flex items-center gap-2 border-b border-slate-200/80 pb-1">
    <button type="button" class="srv-tab-btn active py-2 px-4 rounded-xl font-extrabold text-xs inline-flex items-center gap-2 cursor-pointer transition-all border-none bg-indigo-600 text-white shadow-xs" id="tab-btn-orders" onclick="switchServicesTab('orders')">
      <i class="fa-solid fa-receipt"></i> Room Orders 
      <span class="badge-pill px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-white/20 text-white">{{ count($roomOrders) }}</span>
    </button>
    <button type="button" class="srv-tab-btn py-2 px-4 rounded-xl font-bold text-xs inline-flex items-center gap-2 cursor-pointer transition-all border border-slate-200 bg-white text-slate-600 hover:bg-slate-50" id="tab-btn-menu" onclick="switchServicesTab('menu')">
      <i class="fa-solid fa-book-open text-indigo-500"></i> Services Catalog
      <span class="badge-pill px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700">{{ count($services) }}</span>
    </button>
  </div>

  <!-- TAB 1: ROOM SERVICE & AMENITY ORDERS -->
  <div id="srv-tab-orders" class="srv-tab-pane">
    <div class="srv-filter-toolbar bg-white border border-slate-200/80 rounded-2xl p-3 sm:p-4 shadow-xs flex flex-wrap items-center justify-between gap-3 mb-4">
      <div class="flex items-center gap-2.5 flex-wrap flex-1">
        <div class="srv-search-box relative min-w-[260px] flex-1 max-w-md">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input type="text" id="order-search-box" placeholder="Search Room #, Guest, Order ID..." onkeyup="filterOrdersTable()" class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-hidden transition-all">
        </div>

        <select id="filter-order-status" class="py-1.5 px-3 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-700 focus:bg-white focus:border-indigo-500 focus:outline-hidden" onchange="filterOrdersTable()">
          <option value="all">All Order Statuses</option>
          <option value="Received">Received / New</option>
          <option value="Preparing">Preparing in Kitchen</option>
          <option value="Out for Delivery">Out for Delivery</option>
          <option value="Delivered">Delivered</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>

      <button type="button" class="btn-ui-primary py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs inline-flex items-center gap-1.5 shadow-xs cursor-pointer border-none transition-colors" onclick="openNewRoomOrderModal()">
        <i class="fa-solid fa-plus"></i> Place Room Order
      </button>
    </div>

    <!-- Orders Table -->
    <div class="srv-table-card bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-x-auto">
      <table class="srv-table w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
            <th class="py-3 px-3">Order #</th>
            <th class="py-3 px-3">Room</th>
            <th class="py-3 px-3">Guest Name</th>
            <th class="py-3 px-3">Order Items &amp; Qty</th>
            <th class="py-3 px-3 text-right">Total Bill</th>
            <th class="py-3 px-3">Billing Mode</th>
            <th class="py-3 px-3 text-center">Delivery Status</th>
            <th class="py-3 px-3">Order Time</th>
            <th class="py-3 px-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody id="orders-table-body">
          @forelse($roomOrders as $ord)
            <tr data-order="{{ strtolower($ord->order_number) }}"
                data-room="{{ strtolower($ord->room_number ?? '') }}"
                data-guest="{{ strtolower($ord->guest_name ?? '') }}"
                data-status="{{ $ord->order_status }}"
                class="hover:bg-slate-50/70 border-b border-slate-100 transition-colors">
              <td class="py-3 px-3 whitespace-nowrap">
                <span class="font-mono font-bold text-indigo-600">{{ $ord->order_number }}</span>
              </td>
              <td class="py-3 px-3 whitespace-nowrap">
                <span class="room-tag-pill inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-mono font-extrabold text-xs bg-indigo-50 text-indigo-700 border border-indigo-200">
                  <i class="fa-solid fa-door-open text-[10px]"></i>
                  Room {{ $ord->room_number }}
                </span>
              </td>
              <td class="py-3 px-3">
                <div class="font-bold text-slate-900">{{ $ord->guest_name ?? 'In-House Guest' }}</div>
                @if($ord->booking?->settle_number)
                  <div class="text-[10px] text-slate-400 font-mono mt-0.5">Folio: {{ $ord->booking->settle_number }}</div>
                @endif
              </td>
              <td class="py-3 px-3">
                <div class="text-[11px] text-slate-700 leading-snug">
                  @foreach($ord->items as $it)
                    <div>&bull; <strong>{{ $it->quantity }}x</strong> {{ $it->item_name }}</div>
                  @endforeach
                </div>
              </td>
              <td class="py-3 px-3 text-right font-mono font-extrabold text-slate-900 whitespace-nowrap">
                ₹ {{ number_format($ord->total_amount, 2) }}
              </td>
              <td class="py-3 px-3 whitespace-nowrap">
                <span class="text-xs font-semibold {{ $ord->payment_status === 'Charged to Room Folio' ? 'text-indigo-600' : 'text-emerald-600' }}">
                  <i class="fa-solid {{ $ord->payment_status === 'Charged to Room Folio' ? 'fa-file-invoice-dollar' : 'fa-check' }}"></i>
                  {{ $ord->payment_status }}
                </span>
              </td>
              <td class="py-3 px-3 text-center whitespace-nowrap">
                @if($ord->order_status === 'Received')
                  <span class="order-status-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200"><i class="fa-solid fa-bell text-[9px]"></i> Received</span>
                @elseif($ord->order_status === 'Preparing')
                  <span class="order-status-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200"><i class="fa-solid fa-fire text-[9px]"></i> Preparing</span>
                @elseif($ord->order_status === 'Out for Delivery')
                  <span class="order-status-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-fuchsia-50 text-fuchsia-700 border border-fuchsia-200"><i class="fa-solid fa-person-walking-luggage text-[9px]"></i> Out for Delivery</span>
                @elseif($ord->order_status === 'Delivered')
                  <span class="order-status-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fa-solid fa-circle-check text-[9px]"></i> Delivered</span>
                @else
                  <span class="order-status-badge inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">Cancelled</span>
                @endif
              </td>
              <td class="py-3 px-3 text-[11px] text-slate-500 font-mono whitespace-nowrap">
                {{ $ord->created_at->format('h:i A') }}
              </td>
              <td class="py-3 px-3 text-right whitespace-nowrap">
                <div class="inline-flex items-center justify-end gap-1.5">
                  <button type="button" class="btn-ui-secondary py-1 px-2.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 text-xs font-bold inline-flex items-center gap-1 cursor-pointer" onclick="viewOrderDossier({{ $ord->id }})">
                    <i class="fa-solid fa-eye text-slate-400"></i> Details
                  </button>
                  @if($ord->order_status === 'Received')
                    <button type="button" class="py-1 px-2.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold cursor-pointer border-none transition-colors" onclick="updateOrderStatusAjax({{ $ord->id }}, 'Preparing')">
                      Prep
                    </button>
                  @elseif($ord->order_status === 'Preparing')
                    <button type="button" class="py-1 px-2.5 rounded-lg bg-fuchsia-600 hover:bg-fuchsia-700 text-white text-xs font-bold cursor-pointer border-none transition-colors" onclick="updateOrderStatusAjax({{ $ord->id }}, 'Out for Delivery')">
                      Dispatch
                    </button>
                  @elseif($ord->order_status === 'Out for Delivery')
                    <button type="button" class="py-1 px-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold cursor-pointer border-none transition-colors" onclick="updateOrderStatusAjax({{ $ord->id }}, 'Delivered')">
                      Delivered
                    </button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-8 px-4 text-slate-500">
                <i class="fa-solid fa-bell-concierge text-3xl mb-2 text-slate-300 block"></i>
                No room service orders placed today. Click <strong class="text-slate-700">"Place Room Order"</strong> above to send food, beverages, or amenities directly to an in-house room.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- TAB 2: IN-ROOM MENU & SERVICES CATALOG -->
  <div id="srv-tab-menu" class="srv-tab-pane hidden">
    <div class="srv-filter-toolbar bg-white border border-slate-200/80 rounded-2xl p-3 sm:p-4 shadow-xs flex flex-wrap items-center justify-between gap-3 mb-4">
      <div class="font-extrabold text-slate-900 text-xs flex items-center gap-2">
        <i class="fa-solid fa-book-open text-cyan-600"></i> Available Room Dining Dishes &amp; Guest Amenities
      </div>
      <div class="flex items-center gap-2.5">
        <a href="{{ route('admin.services.index') }}" class="btn-ui-secondary py-1.5 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs inline-flex items-center gap-1.5 no-underline">
          <i class="fa-solid fa-plus text-indigo-500"></i> Add New in Admin
        </a>
      </div>
    </div>

    <div class="srv-table-card bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-x-auto">
      <table class="srv-table w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
            <th class="w-20 py-3 px-3">Code</th>
            <th class="py-3 px-3">Service / Dish Name</th>
            <th class="py-3 px-3">Category</th>
            <th class="py-3 px-3">Unit / Serving</th>
            <th class="py-3 px-3 text-right">Tariff / Price</th>
            <th class="py-3 px-3">Description</th>
            <th class="py-3 px-3 text-center">Status</th>
            <th class="py-3 px-3 text-right">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($services as $srv)
            <tr class="hover:bg-slate-50/70 border-b border-slate-100 transition-colors">
              <td class="py-3 px-3 whitespace-nowrap"><span class="font-mono font-bold text-cyan-700 bg-cyan-50 border border-cyan-200 px-1.5 py-0.5 rounded text-xs">{{ $srv->code }}</span></td>
              <td class="py-3 px-3 font-bold text-slate-900">{{ $srv->name }}</td>
              <td class="py-3 px-3 whitespace-nowrap"><span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-extrabold px-2 py-0.5 rounded-md">{{ $srv->category }}</span></td>
              <td class="py-3 px-3 text-slate-500">{{ $srv->unit }}</td>
              <td class="py-3 px-3 text-right font-mono font-extrabold text-slate-900 whitespace-nowrap">₹ {{ number_format($srv->price, 2) }}</td>
              <td class="py-3 px-3 text-xs text-slate-600 max-w-xs truncate">{{ $srv->description ?: '-' }}</td>
              <td class="py-3 px-3 text-center whitespace-nowrap"><span class="bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-extrabold px-2 py-0.5 rounded-md">Available</span></td>
              <td class="py-3 px-3 text-right whitespace-nowrap">
                <button type="button" class="btn-ui-primary py-1.5 px-3 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs inline-flex items-center gap-1 cursor-pointer border-none transition-colors" onclick="quickOrderThisService({{ $srv->id }}, '{{ addslashes($srv->name) }}', {{ $srv->price }})">
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
</main>

<!-- MODAL 1: NEW ROOM SERVICE ORDER -->
<div class="srv-modal-overlay modal-backdrop fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4" id="modal-new-room-order">
  <div class="srv-modal-window modal-window w-[720px] max-w-[95vw] max-h-[90vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 bg-white">
    <div class="srv-modal-header bg-gradient-to-r from-emerald-600 to-teal-600 p-4 sm:p-5 flex justify-between items-center text-white shrink-0">
      <h3 class="m-0 text-base font-extrabold text-white flex items-center gap-2"><i class="fa-solid fa-utensils"></i> In-Room Dining &amp; Service Order</h3>
      <button type="button" class="srv-modal-close bg-white/20 hover:bg-white/30 text-white w-7.5 h-7.5 rounded-full text-lg cursor-pointer flex items-center justify-center transition-colors border-none" onclick="closeSrvModal('modal-new-room-order')">&times;</button>
    </div>
    <form id="form-room-order" onsubmit="submitRoomOrder(event)" class="flex flex-col flex-1 overflow-hidden m-0">
      @csrf
      <div class="srv-modal-body p-4 sm:p-5 overflow-y-auto flex-1 bg-white">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Select In-House Room *</label>
            <select name="room_id" id="ro-room-select" required class="w-full text-xs font-medium py-2 px-3 border border-slate-200 rounded-lg">
              <option value="">Choose Occupied Room</option>
              @foreach($activeRooms as $rm)
                @php
                  $occGuest = $rm->activeBooking?->guest?->guest_name ?? ($rm->status === 'Occupied' ? 'Occupied Guest' : 'Vacant Room');
                @endphp
                <option value="{{ $rm->id }}" data-guest="{{ $occGuest }}" data-room="{{ $rm->room_number }}">
                  Room #{{ $rm->room_number }} ({{ $rm->categoryRelation?->name ?? 'Standard' }}) - {{ $occGuest }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Guest Name</label>
            <input type="text" id="ro-guest-display" readonly placeholder="Guest name will load here" class="w-full text-xs font-semibold py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 focus:outline-hidden">
          </div>
        </div>

        <div class="flex justify-between items-center text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-2.5 mt-4 pt-3 border-t border-slate-100">
          <span><i class="fa-solid fa-bowl-food text-emerald-600"></i> Order Line Items</span>
          <button type="button" class="btn-ui-secondary py-1 px-2.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-bold inline-flex items-center gap-1 cursor-pointer" onclick="addOrderLineItem()">
            <i class="fa-solid fa-plus text-emerald-600"></i> Add Item
          </button>
        </div>

        <!-- Dynamic Order Line Items -->
        <div id="order-line-items-container" class="space-y-2.5">
          <!-- Populated by JS -->
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Billing &amp; Settlement *</label>
            <select name="payment_status" id="ro-payment-status" required class="w-full text-xs font-medium py-2 px-3 border border-slate-200 rounded-lg">
              <option value="Charged to Room Folio">Charged to Room Folio (Bill at Checkout)</option>
              <option value="Paid Cash">Cash Paid upon Delivery</option>
              <option value="Paid UPI/Card">Digital UPI / POS Card Paid</option>
              <option value="Pending">Pending Collection</option>
            </select>
          </div>
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700">Delivery Staff / Order Taker</label>
            <input type="text" name="served_by" placeholder="Server Name" value="Room Service Desk" class="w-full text-xs font-medium py-2 px-3 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-500">
          </div>
        </div>

        <div class="space-y-1.5 mt-4">
          <label class="block text-xs font-bold text-slate-700">Kitchen / Delivery Instructions</label>
          <textarea name="special_instructions" rows="2" placeholder="e.g. Less spicy, send extra napkins & cutlery, deliver at 8:30 PM" class="w-full text-xs font-medium p-2.5 border border-slate-200 rounded-lg focus:outline-hidden focus:border-emerald-500"></textarea>
        </div>

        <!-- Order Total Summary -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex justify-between items-center mt-4">
          <div>
            <div class="text-[11px] text-slate-500">Subtotal + 5% F&amp;B Tax:</div>
            <div class="text-base font-extrabold font-mono text-slate-900 mt-0.5" id="ro-total-display">₹ 0.00</div>
          </div>
          <div class="text-xs font-extrabold text-emerald-600 inline-flex items-center gap-1.5">
            <i class="fa-solid fa-clock"></i> Est. Delivery: 30-40 Mins
          </div>
        </div>
      </div>
      <div class="srv-modal-footer p-3 sm:px-5 bg-slate-50 border-t border-slate-200 flex justify-end gap-2.5 items-center shrink-0">
        <button type="button" class="btn-ui-secondary py-1.5 px-4 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-bold text-xs cursor-pointer" onclick="closeSrvModal('modal-new-room-order')">Cancel</button>
        <button type="submit" class="btn-ui-primary py-1.5 px-5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs cursor-pointer border-none shadow-xs transition-colors" id="btn-submit-order">
          <i class="fa-solid fa-check"></i> Place Room Order
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL 2: VIEW ORDER DOSSIER -->
<div class="srv-modal-overlay modal-backdrop fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4" id="modal-order-dossier">
  <div class="srv-modal-window modal-window w-[600px] max-w-[95vw] max-h-[90vh] flex flex-col rounded-2xl overflow-hidden shadow-2xl border border-slate-200/80 bg-white">
    <div class="srv-modal-header bg-gradient-to-r from-blue-600 to-indigo-600 p-4 sm:p-5 flex justify-between items-center text-white shrink-0">
      <h3 id="dossier-order-title" class="m-0 text-base font-extrabold text-white flex items-center gap-2"><i class="fa-solid fa-receipt"></i> Room Order Dossier</h3>
      <button type="button" class="srv-modal-close bg-white/20 hover:bg-white/30 text-white w-7.5 h-7.5 rounded-full text-lg cursor-pointer flex items-center justify-center transition-colors border-none" onclick="closeSrvModal('modal-order-dossier')">&times;</button>
    </div>
    <div class="srv-modal-body p-4 sm:p-5 overflow-y-auto flex-1 bg-slate-50" id="dossier-order-content">
      <!-- Loaded dynamically -->
    </div>
    <div class="srv-modal-footer p-3 sm:px-5 bg-white border-t border-slate-200 flex justify-end items-center shrink-0">
      <button type="button" class="btn-ui-secondary py-1.5 px-4 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 font-bold text-xs cursor-pointer" onclick="closeSrvModal('modal-order-dossier')">Close</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const servicesCatalog = @json($services);
  const orderStoreUrl = "{{ route('frontoffice.services.orders.store') }}";
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

  // Tab switching
  function switchServicesTab(tabName) {
    document.querySelectorAll('.srv-tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.srv-tab-btn').forEach(btn => btn.classList.remove('active'));

    const targetPane = document.getElementById('srv-tab-' + tabName);
    const targetBtn = document.getElementById('tab-btn-' + tabName);

    if (targetPane) targetPane.style.display = 'block';
    if (targetBtn) targetBtn.classList.add('active');
  }

  // Modal handlers
  function openSrvModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'flex';
  }

  function closeSrvModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
  }

  function openNewRoomOrderModal(initialServiceId = null) {
    document.getElementById('form-room-order').reset();
    document.getElementById('order-line-items-container').innerHTML = '';
    
    // Reset Select2 fields
    if ($('#ro-room-select').data('select2')) {
      $('#ro-room-select').val('').trigger('change');
    }
    if ($('#ro-payment-status').data('select2')) {
      $('#ro-payment-status').val('Charged to Room Folio').trigger('change');
    }
    document.getElementById('ro-guest-display').value = '';

    if (initialServiceId) {
      addOrderLineItem(initialServiceId);
    } else if (servicesCatalog.length > 0) {
      addOrderLineItem(servicesCatalog[0].id);
    } else {
      addOrderLineItem(null);
    }
    calculateRoomOrderTotal();
    openSrvModal('modal-new-room-order');
  }

  function quickOrderThisService(serviceId, name, price) {
    openNewRoomOrderModal(serviceId);
  }

  function updateRoomOrderGuest() {
    const select = document.getElementById('ro-room-select');
    if (!select || select.selectedIndex < 0) return;
    const opt = select.options[select.selectedIndex];
    const guestInput = document.getElementById('ro-guest-display');
    if (opt && opt.value) {
      guestInput.value = opt.getAttribute('data-guest') || 'In-House Guest';
    } else {
      guestInput.value = '';
    }
  }

  // Order Line Items Builder with Select2
  let itemCounter = 0;
  function addOrderLineItem(selectedServiceId = null) {
    itemCounter++;
    const container = document.getElementById('order-line-items-container');
    const row = document.createElement('div');
    row.className = 'order-item-row flex items-center gap-2.5 bg-slate-50/80 p-2.5 rounded-xl border border-slate-200/80 transition-colors hover:border-slate-300';
    row.id = `item-row-${itemCounter}`;

    let optionsHtml = '<option value="">-- Search &amp; Choose Hotel Service / Food Item --</option>';
    servicesCatalog.forEach(s => {
      const isSelected = selectedServiceId && String(s.id) === String(selectedServiceId) ? 'selected' : '';
      optionsHtml += `<option value="${s.id}" data-price="${s.price}" data-name="${s.name}" ${isSelected}>[${s.code || 'SRV'}] ${s.name} (₹ ${parseFloat(s.price).toFixed(2)})</option>`;
    });

    row.innerHTML = `
      <div class="flex-[2] min-w-0">
        <select class="item-service-select w-full" id="item-service-select-${itemCounter}">
          ${optionsHtml}
        </select>
        <input type="hidden" name="items[${itemCounter}][service_id]" id="input-service-id-${itemCounter}" value="${selectedServiceId || ''}">
        <input type="text" name="items[${itemCounter}][name]" id="input-item-name-${itemCounter}" placeholder="Custom item description" class="hidden px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs w-full mt-1.5 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 box-border">
      </div>
      <div class="w-24 shrink-0">
        <input type="number" step="0.01" name="items[${itemCounter}][price]" id="input-item-price-${itemCounter}" placeholder="Price" value="0.00" onkeyup="calculateRoomOrderTotal()" onchange="calculateRoomOrderTotal()" class="px-2.5 py-2 border border-slate-300 rounded-lg text-xs w-full font-mono font-bold text-slate-800 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 box-border text-right">
      </div>
      <div class="w-16 shrink-0">
        <input type="number" name="items[${itemCounter}][quantity]" id="input-item-qty-${itemCounter}" value="1" min="1" onkeyup="calculateRoomOrderTotal()" onchange="calculateRoomOrderTotal()" class="px-2.5 py-2 border border-slate-300 rounded-lg text-xs w-full font-mono font-bold text-slate-800 text-center focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 box-border">
      </div>
      <button type="button" onclick="removeOrderLineItem(${itemCounter})" class="bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 hover:text-rose-700 w-8.5 h-8.5 rounded-lg cursor-pointer flex items-center justify-center shrink-0 transition-colors shadow-xs" title="Remove Item">
        <i class="fa-solid fa-trash-can text-xs"></i>
      </button>
    `;

    container.appendChild(row);

    const currIdx = itemCounter;
    $(`#item-service-select-${currIdx}`).select2({
      dropdownParent: $('#modal-new-room-order'),
      width: '100%',
      placeholder: 'Search dish, laundry, amenity...'
    }).on('select2:select change', function() {
      onServiceSelectChange(currIdx);
    });

    if (selectedServiceId) {
      $(`#item-service-select-${currIdx}`).val(selectedServiceId).trigger('change');
    }
  }

  function removeOrderLineItem(index) {
    if ($(`#item-service-select-${index}`).data('select2')) {
      $(`#item-service-select-${index}`).select2('destroy');
    }
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

    const tax = subtotal * 0.05;
    const total = subtotal + tax;

    document.getElementById('ro-total-display').innerText = `₹ ${total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
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

  // Submit Order via AJAX
  async function submitRoomOrder(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const submitBtn = document.getElementById('btn-submit-order');
    submitBtn.disabled = true;

    try {
      const response = await fetch(orderStoreUrl, {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': csrfToken,
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
        closeSrvModal('modal-new-room-order');
        setTimeout(() => location.reload(), 700);
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
      alert('Network or server error.');
    } finally {
      submitBtn.disabled = false;
    }
  }

  // Quick Order Status Update
  async function updateOrderStatusAjax(orderId, newStatus) {
    try {
      const response = await fetch(`{{ url('front-office/services/orders') }}/${orderId}/status`, {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
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

  // View Order Details Dossier
  async function viewOrderDossier(id) {
    try {
      const res = await fetch(`{{ url('front-office/services/orders') }}/${id}`);
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
          <div class="grid grid-cols-2 gap-2.5 bg-slate-50/80 border border-slate-200/80 p-3 rounded-xl mb-4 text-xs">
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Guest:</span> ${o.guest_name}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Room #:</span> ${o.room_number}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Billing:</span> <span class="capitalize font-semibold text-slate-800">${o.payment_status}</span></div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Status:</span> <span class="capitalize font-semibold text-indigo-700">${o.order_status}</span></div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Served By:</span> ${o.served_by || 'Room Service'}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Est. Delivery:</span> ${o.delivery_time || '-'}</div>
            <div class="col-span-2 text-slate-600"><span class="font-semibold text-slate-800">Special Instructions:</span> ${o.special_instructions || 'None'}</div>
          </div>
          <div class="font-bold text-xs mb-2 text-slate-800 uppercase tracking-wider">Order Items</div>
          <div class="bg-white border border-slate-200/80 rounded-xl p-3.5 mb-3 shadow-xs">
            ${itemsHtml}
            <div class="flex items-center justify-between pt-3 font-bold text-sm text-slate-900 border-t border-slate-200/80 mt-1">
              <div>Total (inc. 5% tax):</div>
              <div class="font-mono text-emerald-600 text-base font-extrabold">₹ ${parseFloat(o.total_amount).toFixed(2)}</div>
            </div>
          </div>
        `;

        openSrvModal('modal-order-dossier');
      }
    } catch (err) {
      console.error(err);
      alert('Unable to load order dossier.');
    }
  }

  // Initialize Select2 dropdowns on document ready
  $(document).ready(function() {
    // Auto-focus search input when any Select2 opens
    $(document).on('select2:open', () => {
      setTimeout(() => {
        const searchField = document.querySelector('.select2-container--open .select2-search__field');
        if (searchField) searchField.focus();
      }, 50);
    });

    $('#filter-order-status').select2({
      width: '200px',
      placeholder: 'All Order Statuses'
    }).on('select2:select change', function() {
      filterOrdersTable();
    });

    $('#ro-room-select').select2({
      dropdownParent: $('#modal-new-room-order'),
      width: '100%',
      placeholder: 'Search Room # or Guest Name...'
    }).on('select2:select change', function() {
      updateRoomOrderGuest();
    });

    $('#ro-payment-status').select2({
      dropdownParent: $('#modal-new-room-order'),
      width: '100%'
    });
  });
</script>
@endpush
