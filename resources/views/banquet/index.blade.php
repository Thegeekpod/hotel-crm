@extends('frontoffice.layouts.app')

@section('title', 'Banquet Halls & Event Management - Hotel Sagar Sonnet')

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
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Banquet Venues &amp; Lawns</h4>
        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $totalHalls }} <span class="text-xs text-slate-500 font-sans font-medium">Venues</span></div>
      </div>
    </div>

    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-calendar-check"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Confirmed Bookings</h4>
        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $totalEventsBooked }} <span class="text-xs text-slate-500 font-sans font-medium">Events</span></div>
      </div>
    </div>

    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-users"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Guests (Pax) Hosted</h4>
        <div class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ number_format($totalPaxBooked) }} <span class="text-xs text-slate-500 font-sans font-medium">Pax</span></div>
      </div>
    </div>

    <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 hover:shadow-md transition-all">
      <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-200/80 text-purple-600 flex items-center justify-center text-lg shrink-0">
        <i class="fa-solid fa-indian-rupee-sign"></i>
      </div>
      <div class="min-w-0">
        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider truncate">Banquet Revenue</h4>
        <div class="text-xl font-bold font-mono text-purple-600 mt-0.5">₹ {{ number_format($totalBanquetRevenue, 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Header Action Banner -->
  <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-600 border border-amber-200/60 flex items-center justify-center text-xl shrink-0 shadow-xs">
        <i class="fa-solid fa-champagne-glasses"></i>
      </div>
      <div>
        <h2 class="text-lg font-bold text-slate-900 tracking-tight">Banquet Halls, Venues &amp; Event Management</h2>
        <p class="text-xs text-slate-500 mt-0.5">Manage event halls, conference facilities, weddings, catering function sheets &amp; reservations</p>
      </div>
    </div>
    <div class="flex items-center gap-2.5 flex-wrap">
      <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition-all shadow-xs cursor-pointer" onclick="openNewBanquetModal()">
        <i class="fa-solid fa-calendar-plus"></i> Book Event Hall
      </button>
    </div>
  </div>

  <!-- Segmented Tabs Navigation -->
  <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 overflow-x-auto scrollbar-none">
    <button type="button" class="bnq-tab-btn active inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-slate-900 text-white shadow-xs cursor-pointer" id="tab-btn-bookings" onclick="switchBanquetTab('bookings')">
      <i class="fa-solid fa-calendar-days"></i> Event Reservations &amp; Function Sheets
      <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-200">{{ $banquetBookings->count() }} Booked</span>
    </button>
    <button type="button" class="bnq-tab-btn inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs font-bold transition-all bg-white border border-slate-200/80 text-slate-600 hover:text-slate-900 hover:bg-slate-50 cursor-pointer" id="tab-btn-venues" onclick="switchBanquetTab('venues')">
      <i class="fa-solid fa-building-columns"></i> Banquet Venues &amp; Facilities
      <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">{{ $halls->count() }} Venues</span>
    </button>
  </div>

  <!-- TAB 1: EVENT RESERVATIONS & FUNCTION SHEETS -->
  <div id="bnq-tab-bookings" class="bnq-tab-pane space-y-4">
    <!-- Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3">
      <div class="flex items-center gap-3 flex-wrap flex-1">
        <div class="relative flex-1 min-w-[240px] max-w-md">
          <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
          <input type="text" id="booking-search-box" placeholder="Search Ref #, Event, Client, Phone, Hall..." onkeyup="filterBanquetBookings()" class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-xl text-xs placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all">
        </div>

        <select id="filter-booking-status" class="px-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 cursor-pointer" onchange="filterBanquetBookings()">
          <option value="all">All Event Statuses</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Tentative">Tentative</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
        </select>

        <select id="filter-booking-hall" class="px-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 cursor-pointer" onchange="filterBanquetBookings()">
          <option value="all">All Venues &amp; Halls</option>
          @foreach($halls as $hl)
            <option value="{{ strtolower($hl->name) }}">{{ $hl->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="flex items-center gap-2">
        <button type="button" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition-all shadow-xs shrink-0 cursor-pointer" onclick="openNewBanquetModal()">
          <i class="fa-solid fa-plus"></i> Book Event Hall
        </button>
      </div>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200/70 text-xs">
          <thead>
            <tr class="bg-slate-50/75 border-b border-slate-200/70">
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Ref. No</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Hall / Venue</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Event Name &amp; Client</th>
              <th class="py-3 px-4 text-left font-bold text-slate-500 uppercase tracking-wider">Event Date &amp; Slot</th>
              <th class="py-3 px-4 text-center font-bold text-slate-500 uppercase tracking-wider">Pax</th>
              <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Rental &amp; Catering</th>
              <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Total Contract</th>
              <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Advance / Due</th>
              <th class="py-3 px-4 text-center font-bold text-slate-500 uppercase tracking-wider">Status</th>
              <th class="py-3 px-4 text-right font-bold text-slate-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody id="bookings-table-body" class="divide-y divide-slate-100 bg-white">
            @forelse($banquetBookings as $bk)
              <tr data-ref="{{ strtolower($bk->booking_reference) }}"
                  data-hall="{{ strtolower($bk->hall?->name ?? '') }}"
                  data-event="{{ strtolower($bk->event_name) }}"
                  data-client="{{ strtolower($bk->client_name) }}"
                  data-phone="{{ strtolower($bk->phone ?? '') }}"
                  data-status="{{ $bk->status }}"
                  class="hover:bg-slate-50/80 transition-colors">
                <td class="py-3 px-4 font-mono font-bold text-amber-600 whitespace-nowrap">
                  {{ $bk->booking_reference }}
                </td>
                <td class="py-3 px-4 font-bold text-slate-900 whitespace-nowrap">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-800 text-[11px] font-bold">
                    <i class="fa-solid fa-building-columns text-[10px]"></i>
                    {{ $bk->hall?->name ?? 'Grand Ballroom' }}
                  </span>
                </td>
                <td class="py-3 px-4">
                  <div class="font-bold text-slate-900">{{ $bk->event_name }}</div>
                  <div class="text-[11px] text-slate-600 font-medium flex items-center gap-1.5 mt-0.5">
                    <span><i class="fa-solid fa-user text-[10px] text-slate-400"></i> {{ $bk->client_name }}</span>
                    @if($bk->phone)
                      <span class="font-mono text-[10px] text-slate-400">({{ $bk->phone }})</span>
                    @endif
                  </div>
                  @if($bk->company_name)
                    <div class="text-[10px] text-slate-400 mt-0.5"><i class="fa-solid fa-briefcase text-[9px]"></i> {{ $bk->company_name }}</div>
                  @endif
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <div class="font-bold text-slate-900">{{ $bk->event_date ? $bk->event_date->format('d M Y') : '-' }}</div>
                  <div class="text-[10px] text-slate-500 font-medium">{{ $bk->time_slot }}</div>
                </td>
                <td class="py-3 px-4 text-center font-mono font-bold text-slate-800">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                    <i class="fa-solid fa-users text-[10px] text-slate-500"></i> {{ $bk->guaranteed_pax }}
                  </span>
                </td>
                <td class="py-3 px-4 text-right font-mono text-[11px] text-slate-600 whitespace-nowrap">
                  <div>Hall: ₹ {{ number_format($bk->hall_rental, 2) }}</div>
                  <div class="text-slate-400 text-[10px]">Plate: ₹ {{ number_format($bk->catering_per_plate, 2) }}</div>
                </td>
                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                  ₹ {{ number_format($bk->total_amount, 2) }}
                </td>
                <td class="py-3 px-4 text-right font-mono text-xs whitespace-nowrap">
                  <div class="font-bold text-emerald-600">Paid: ₹ {{ number_format($bk->advance_paid, 2) }}</div>
                  <div class="text-[10px] text-amber-700 font-semibold">Due: ₹ {{ number_format($bk->balance_due, 2) }}</div>
                </td>
                <td class="py-3 px-4 text-center whitespace-nowrap">
                  @if($bk->status === 'Confirmed')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                      <i class="fa-solid fa-circle-check text-[10px]"></i> Confirmed
                    </span>
                  @elseif($bk->status === 'Completed')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                      <i class="fa-solid fa-flag-checkered text-[10px]"></i> Completed
                    </span>
                  @elseif($bk->status === 'Tentative')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                      <i class="fa-solid fa-clock text-[10px]"></i> Tentative
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                      Cancelled
                    </span>
                  @endif
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <button type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="viewFunctionSheet({{ $bk->id }})">
                    <i class="fa-solid fa-file-lines text-[11px] text-amber-600"></i> Function Sheet
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="py-12 px-4 text-center text-slate-500">
                  <div class="flex flex-col items-center justify-center">
                    <i class="fa-solid fa-calendar-xmark text-3xl text-slate-300 mb-2"></i>
                    <p class="font-medium text-slate-600">No banquet event bookings recorded.</p>
                    <p class="text-xs text-slate-400 mt-1">Click <strong class="font-semibold text-slate-700">"Book Event Hall"</strong> above to schedule an upcoming banquet reservation.</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- TAB 2: BANQUET VENUES & FACILITIES -->
  <div id="bnq-tab-venues" class="bnq-tab-pane space-y-6 hidden">
    <div>
      <div class="text-xs font-bold text-slate-900 mb-3 flex items-center justify-between uppercase tracking-wider">
        <span class="flex items-center gap-2">
          <i class="fa-solid fa-building-columns text-amber-600"></i> Banquet Venues &amp; Conference Facilities
        </span>
        <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs transition-all shadow-xs cursor-pointer" onclick="openNewBanquetModal()">
          <i class="fa-solid fa-plus text-[10px]"></i> Book Event Hall
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($halls as $hall)
          <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition-all p-5 flex flex-col justify-between">
            <div class="space-y-3">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <h3 class="text-sm font-bold text-slate-900">{{ $hall->name }}</h3>
                  <span class="text-[11px] font-medium text-slate-500">{{ $hall->type }}</span>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $hall->status === 'Available' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                  {{ $hall->status }}
                </span>
              </div>
              <div class="flex items-center gap-3 text-xs text-slate-600 flex-wrap">
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-users text-indigo-500"></i> Up to <strong class="font-bold text-slate-800">{{ $hall->capacity_pax }} Pax</strong></span>
                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-snowflake text-cyan-600"></i> Air Conditioned</span>
              </div>
              <div class="text-xs text-slate-600 flex items-start gap-1.5 leading-relaxed bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 shrink-0 text-xs"></i>
                <span>{{ $hall->amenities ?? 'Projector, Stage, Audio System, Ambient Lighting' }}</span>
              </div>
            </div>
            <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-100">
              <div>
                <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Base Hall Rental</div>
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
  </div>

  </div>
</main>

<!-- MODAL: BOOK BANQUET HALL / EVENT -->
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
            <input type="number" name="catering_per_plate" id="bq-plate" placeholder="e.g. 1000" value="1000" onkeyup="calculateBanquetTariff()" onchange="calculateBanquetTariff()" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
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

<!-- MODAL: VIEW EVENT FUNCTION SHEET -->
<div class="bnq-modal-overlay hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto" id="modal-function-sheet">
  <div class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-200/80 overflow-hidden my-8">
    <div class="bg-linear-to-r from-amber-600 to-amber-700 px-6 py-4 flex items-center justify-between text-white">
      <h3 class="text-base font-bold flex items-center gap-2 text-white" id="fs-modal-title">
        <i class="fa-solid fa-file-lines"></i> Event Function Sheet
      </h3>
      <button type="button" class="w-8 h-8 rounded-lg flex items-center justify-center text-white/80 hover:text-white hover:bg-white/10 transition-colors text-xl leading-none cursor-pointer" onclick="closeBnqModal('modal-function-sheet')">&times;</button>
    </div>
    <div class="p-6 max-h-[75vh] overflow-y-auto space-y-4" id="fs-modal-content">
      <!-- Loaded dynamically via AJAX -->
    </div>
    <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200/80 flex justify-between items-center">
      <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold cursor-pointer" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Print Function Sheet
      </button>
      <button type="button" class="px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition-all cursor-pointer" onclick="closeBnqModal('modal-function-sheet')">Close</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Tab switching
  function switchBanquetTab(tabName) {
    document.querySelectorAll('.bnq-tab-pane').forEach(el => {
      el.classList.add('hidden');
      el.style.display = 'none';
    });
    document.querySelectorAll('.bnq-tab-btn').forEach(btn => {
      btn.classList.remove('active', 'bg-slate-900', 'text-white', 'shadow-xs');
      btn.classList.add('bg-white', 'border', 'border-slate-200/80', 'text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
      const badge = btn.querySelector('span');
      if (badge) {
        if (btn.id === 'tab-btn-bookings') {
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
      targetBtn.classList.add('active', 'bg-slate-900', 'text-white', 'shadow-xs');
      targetBtn.classList.remove('bg-white', 'border', 'border-slate-200/80', 'text-slate-600', 'hover:text-slate-900', 'hover:bg-slate-50');
      const badge = targetBtn.querySelector('span');
      if (badge) {
        badge.className = 'inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white';
      }
    }
  }

  // Modal helpers
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

  function openNewBanquetModal() {
    document.getElementById('form-banquet-booking').reset();
    calculateBanquetTariff();
    openBnqModal('modal-new-banquet');
  }

  function openBookSpecificHallModal(hallId, hallName, baseTariff) {
    openNewBanquetModal();
    const sel = document.getElementById('bq-hall-select');
    if (sel) {
      sel.value = hallId;
    }
    const rentalInput = document.getElementById('bq-rental');
    if (rentalInput) {
      rentalInput.value = baseTariff;
    }
    calculateBanquetTariff();
  }

  // Calculate banquet tariff
  function calculateBanquetTariff() {
    const hallSelect = document.getElementById('bq-hall-select');
    const opt = hallSelect ? hallSelect.options[hallSelect.selectedIndex] : null;
    const rentalInput = document.getElementById('bq-rental');

    if (opt && opt.value && rentalInput && (!rentalInput.value || rentalInput.value == '50000')) {
      rentalInput.value = opt.getAttribute('data-tariff') || 50000;
    }

    const rental = parseFloat(document.getElementById('bq-rental')?.value) || 0;
    const pax = parseInt(document.getElementById('bq-pax')?.value) || 0;
    const plate = parseFloat(document.getElementById('bq-plate')?.value) || 0;
    const advance = parseFloat(document.getElementById('bq-advance')?.value) || 0;

    const total = rental + (pax * plate);
    const balance = Math.max(0, total - advance);

    const totElem = document.getElementById('bq-total-display');
    const balElem = document.getElementById('bq-balance-display');
    if (totElem) totElem.innerText = `₹ ${total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    if (balElem) balElem.innerText = `₹ ${balance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }

  // Filter Bookings Table
  function filterBanquetBookings() {
    const q = (document.getElementById('booking-search-box')?.value || '').toLowerCase().trim();
    const st = document.getElementById('filter-booking-status')?.value || 'all';
    const hl = (document.getElementById('filter-booking-hall')?.value || 'all').toLowerCase();

    const rows = document.querySelectorAll('#bookings-table-body tr[data-ref]');
    rows.forEach(r => {
      const ref = r.getAttribute('data-ref') || '';
      const hall = r.getAttribute('data-hall') || '';
      const ev = r.getAttribute('data-event') || '';
      const client = r.getAttribute('data-client') || '';
      const phone = r.getAttribute('data-phone') || '';
      const status = r.getAttribute('data-status') || '';

      const matchQ = !q || ref.includes(q) || hall.includes(q) || ev.includes(q) || client.includes(q) || phone.includes(q);
      const matchSt = (st === 'all') || (status === st);
      const matchHl = (hl === 'all') || (hall.includes(hl));

      r.style.display = (matchQ && matchSt && matchHl) ? '' : 'none';
    });
  }

  // AJAX Submit Banquet Booking
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

  // View Function Sheet
  async function viewFunctionSheet(id) {
    try {
      const res = await fetch(`/banquet/bookings/${id}/function-sheet`);
      const data = await res.json();
      if (data.success && data.booking) {
        const b = data.booking;
        const modalTitle = document.getElementById('fs-modal-title');
        if (modalTitle) {
          modalTitle.innerHTML = `<i class="fa-solid fa-file-lines"></i> Function Sheet - ${b.event_name}`;
        }

        const dateStr = b.event_date ? new Date(b.event_date).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';

        document.getElementById('fs-modal-content').innerHTML = `
          <div class="grid grid-cols-2 gap-2.5 bg-slate-50 border border-slate-200/80 p-3.5 rounded-xl text-xs">
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Booking Ref:</span> <span class="font-mono font-bold text-amber-700">${b.booking_reference}</span></div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Venue / Hall:</span> <strong class="text-slate-900">${b.hall ? b.hall.name : 'Grand Hall'}</strong></div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Client / Host:</span> ${b.client_name}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Contact:</span> <span class="font-mono">${b.phone || '-'}</span></div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Event Date:</span> ${dateStr}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Time Slot:</span> ${b.time_slot}</div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Guaranteed Pax:</span> <strong class="text-slate-900 font-mono">${b.guaranteed_pax} Guests</strong></div>
            <div class="text-slate-600"><span class="font-semibold text-slate-800">Booking Status:</span> <span class="capitalize font-bold text-emerald-700">${b.status}</span></div>
          </div>
          <div class="font-bold text-xs text-slate-800 uppercase tracking-wider pt-2">Event Setup &amp; AV Notes</div>
          <div class="bg-white border border-slate-200/80 rounded-xl p-3.5 text-xs leading-relaxed text-slate-700 shadow-xs">
            ${b.function_sheet_notes || 'Standard banquet cluster setup with audio-visual system, projectors and stage lighting.'}
          </div>
          <div class="flex items-center justify-between bg-amber-50 border border-amber-200 rounded-xl p-3.5 text-xs font-bold">
            <div>Contract Total: <span class="font-mono text-slate-900">₹ ${parseFloat(b.total_amount).toFixed(2)}</span></div>
            <div class="text-emerald-700">Advance Paid: <span class="font-mono">₹ ${parseFloat(b.advance_paid).toFixed(2)}</span></div>
            <div class="text-amber-800">Balance Due: <span class="font-mono">₹ ${parseFloat(b.balance_due).toFixed(2)}</span></div>
          </div>
        `;

        openBnqModal('modal-function-sheet');
      }
    } catch (err) {
      console.error(err);
      alert('Unable to load function sheet.');
    }
  }
</script>
@endpush
