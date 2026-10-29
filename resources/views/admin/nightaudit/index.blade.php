@extends('admin.layouts.app')

@section('title', 'Night Audit History - Hotel Sagar Sonnet Admin')


@section('content')
<div class="pms-page-container">

  <!-- Top App Breadcrumb Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 mb-5 border-b border-slate-200/80">
    <div class="flex items-center gap-3">
      <div class="w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-100 shrink-0"></div>
      <div>
        <h1 class="text-xl font-black text-slate-900 tracking-tight">Administrator • Master Data Management</h1>
        <div class="text-xs text-slate-500 font-medium mt-0.5">Configure, review, and inspect permanent archives of daily hotel night audits.</div>
      </div>
    </div>
    <div class="flex items-center gap-2.5">
      <a href="{{ route('frontoffice.nightaudit') }}" class="inline-flex items-center gap-2 h-9.5 px-4 text-xs font-bold text-slate-700 bg-white border border-slate-200/80 rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-all shadow-xs" title="Open Front Desk Night Audit Screen">
        <i class="fa-solid fa-table-cells-large text-indigo-600"></i> Front Desk Live Audit
      </a>
    </div>
  </div>

  <div class="admin-layout">

    <!-- Administrator Master Sidebar -->
    @include('admin.includes.sidebar')

    <!-- Main Content Pane -->
    <section class="admin-content-pane">

      <!-- Module Top Card -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 mb-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <div class="flex items-center gap-2.5 flex-wrap">
              <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-moon text-indigo-600"></i> Night Audit History &amp; Reports
              </h2>
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80" id="audit-count">
                <i class="fa-solid fa-database text-[10px]"></i> {{ $audits->total() }} Archived Audits
              </span>
            </div>
            <div class="text-xs text-slate-500 font-medium mt-1">
              Permanent immutable archives of daily night audits, revenue reconciliations, and manager sign-offs.
            </div>
          </div>
        </div>
      </div>

      <!-- Top 4 KPI Metrics Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Completed Audits -->
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Completed Audits</span>
            <div class="text-2xl font-black font-mono text-purple-600 mt-1">{{ $totalAuditsCount }}</div>
            <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
              <i class="fa-solid fa-lock text-emerald-500 text-[10px]"></i> Archived Locked Audits
            </div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-200/80 text-purple-600 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
        </div>

        <!-- Card 2: Audited Total Bill -->
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Audited Total Bill</span>
            <div class="text-2xl font-black font-mono text-indigo-600 mt-1">₹ {{ number_format($totalAuditedRevenue, 2) }}</div>
            <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
              <i class="fa-solid fa-receipt text-indigo-500 text-[10px]"></i> Total Hotel Billing Recorded
            </div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-receipt"></i>
          </div>
        </div>

        <!-- Card 3: Audited Collections -->
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Audited Collections</span>
            <div class="text-2xl font-black font-mono text-emerald-600 mt-1">₹ {{ number_format($totalAuditedPayments, 2) }}</div>
            <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
              <i class="fa-solid fa-cash-register text-emerald-500 text-[10px]"></i> Total Payments Reconciled
            </div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-hand-holding-dollar"></i>
          </div>
        </div>

        <!-- Card 4: Average Occupancy -->
        <div class="bg-white p-4.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-3 hover:shadow-md transition-all">
          <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Avg Occupancy</span>
            <div class="text-2xl font-black font-mono text-amber-600 mt-1">{{ number_format($avgOccupancy, 1) }}%</div>
            <div class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5 mt-1">
              <i class="fa-solid fa-percent text-amber-500 text-[10px]"></i> Overall Historical Average
            </div>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-600 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-door-open"></i>
          </div>
        </div>
      </div>

      <!-- Control Toolbar & Filter Form -->
      <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-xs mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <form action="{{ route('admin.nightaudit.index') }}" method="GET" class="flex items-center gap-2.5 flex-wrap">
          
          <!-- From Date Chip -->
          <div class="inline-flex items-center gap-2 h-9.5 px-3 rounded-xl border border-slate-200 bg-slate-50/70 focus-within:border-indigo-500 focus-within:bg-white transition-all text-xs">
            <span class="text-slate-500 font-bold flex items-center gap-1.5 shrink-0">
              <i class="fa-regular fa-calendar text-indigo-600"></i> From:
            </span>
            <input type="date" name="from_date" value="{{ $fromDate }}" class="border-none bg-transparent text-xs font-mono font-bold text-slate-800 focus:outline-none p-0 cursor-pointer">
          </div>

          <!-- To Date Chip -->
          <div class="inline-flex items-center gap-2 h-9.5 px-3 rounded-xl border border-slate-200 bg-slate-50/70 focus-within:border-indigo-500 focus-within:bg-white transition-all text-xs">
            <span class="text-slate-500 font-bold flex items-center gap-1.5 shrink-0">
              <i class="fa-regular fa-calendar-check text-indigo-600"></i> To:
            </span>
            <input type="date" name="to_date" value="{{ $toDate }}" class="border-none bg-transparent text-xs font-mono font-bold text-slate-800 focus:outline-none p-0 cursor-pointer">
          </div>

          <!-- Search Input -->
          <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search auditor, remarks, date..." class="h-9.5 pl-8 pr-3 w-56 text-xs font-medium rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white focus:border-indigo-500 focus:outline-none transition-all placeholder:text-slate-400">
          </div>

          <!-- Submit Filter Button -->
          <button type="submit" class="inline-flex items-center gap-1.5 h-9.5 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer">
            <i class="fa-solid fa-filter text-[11px]"></i> Filter
          </button>

          <!-- Reset Filter Button -->
          @if($fromDate || $toDate || $search)
            <a href="{{ route('admin.nightaudit.index') }}" class="inline-flex items-center gap-1.5 h-9.5 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs" title="Clear Filters">
              <i class="fa-solid fa-rotate-left text-[11px]"></i> Reset
            </a>
          @endif
        </form>

        <div class="text-xs font-semibold text-slate-500 whitespace-nowrap self-center">
          Showing <strong class="text-slate-800 font-bold">{{ $audits->count() }}</strong> of <strong class="text-slate-800 font-bold">{{ $audits->total() }}</strong> Audit Records
        </div>
      </div>

      <!-- Master Audits Table Card -->
      <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden mb-6">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
                <th class="px-3.5 py-3 text-center w-12">SL</th>
                <th class="px-3.5 py-3">Audit Date</th>
                <th class="px-3.5 py-3">Completion Time</th>
                <th class="px-3.5 py-3">Auditor Name</th>
                <th class="px-3.5 py-3 text-center">Occupancy</th>
                <th class="px-3.5 py-3 text-right">Total Bill (₹)</th>
                <th class="px-3.5 py-3 text-right">Collections (₹)</th>
                <th class="px-3.5 py-3 text-right">Outstanding (₹)</th>
                <th class="px-3.5 py-3 text-center">Status</th>
                <th class="px-3.5 py-3 text-center w-28">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @forelse($audits as $idx => $a)
                @php
                  $sl = ($audits->currentPage() - 1) * $audits->perPage() + $idx + 1;
                  $occRate = (float)$a->occupancy_rate;
                  $occPillClass = $occRate >= 70 ? 'bg-emerald-50 text-emerald-700 border-emerald-200/80' : ($occRate >= 30 ? 'bg-indigo-50 text-indigo-700 border-indigo-200/80' : 'bg-amber-50 text-amber-700 border-amber-200/80');

                  // Accurate calculations from snapshot
                  $snap = $a->audit_snapshot;
                  $rowBill = 0;
                  $rowPaid = (float)$a->total_payments;
                  $rowDue = 0;
                  if (!empty($snap['guests_list']) && is_array($snap['guests_list'])) {
                    foreach ($snap['guests_list'] as $g) {
                      $rowBill += (float)($g['total_bill'] ?? 0);
                      $rowDue += (float)($g['remaining_amount'] ?? 0);
                    }
                  }
                  if ($rowBill <= 0) {
                    $rowBill = (float)$a->total_revenue;
                    $rowDue = max(0, $rowBill - $rowPaid);
                  }
                  $isTodayAudit = $a->audit_date ? \Carbon\Carbon::parse($a->audit_date)->isToday() : false;
                @endphp
                <tr class="hover:bg-slate-50/80 transition-colors">
                  <!-- SL -->
                  <td class="px-3.5 py-3 text-center font-mono font-bold text-slate-400 text-[11px]">
                    #{{ sprintf('%02d', $sl) }}
                  </td>

                  <!-- Audit Date -->
                  <td class="px-3.5 py-3">
                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                      <i class="fa-regular fa-calendar-check text-indigo-600 text-[11px]"></i>
                      {{ $a->audit_date->format('d M Y') }}
                    </div>
                    <div class="text-[10px] text-slate-400 font-semibold pl-4">
                      {{ $a->audit_date->format('l') }}
                    </div>
                  </td>

                  <!-- Completion Time -->
                  <td class="px-3.5 py-3 font-mono text-[11px] text-slate-500">
                    @if($a->completed_at)
                      <span class="inline-flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-slate-400 text-[10px]"></i>
                        {{ $a->completed_at->format('h:i A') }}
                      </span>
                    @else
                      <span class="text-slate-300">N/A</span>
                    @endif
                  </td>

                  <!-- Auditor Name -->
                  <td class="px-3.5 py-3 font-semibold text-slate-700">
                    <div class="flex items-center gap-1.5">
                      <i class="fa-solid fa-user-check text-emerald-500 text-[11px]"></i>
                      <span>{{ $a->auditor_name ?: ($a->auditor?->name ?? 'Front Office Auditor') }}</span>
                    </div>
                  </td>

                  <!-- Occupancy Pill -->
                  <td class="px-3.5 py-3 text-center">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $occPillClass }}" title="{{ $a->occupied_rooms }} of {{ $a->total_rooms }} rooms occupied">
                      <i class="fa-solid fa-bed text-[10px]"></i> {{ number_format($a->occupancy_rate, 1) }}%
                    </span>
                  </td>

                  <!-- Total Bill (₹) -->
                  <td class="px-3.5 py-3 text-right font-bold font-mono text-indigo-700 text-xs">
                    ₹ {{ number_format($rowBill, 2) }}
                  </td>

                  <!-- Collections (₹) -->
                  <td class="px-3.5 py-3 text-right font-bold font-mono text-emerald-700 text-xs" title="Cash: ₹{{ number_format($a->cash_collection, 2) }} • UPI: ₹{{ number_format($a->upi_collection, 2) }} • Card: ₹{{ number_format($a->card_collection, 2) }} • Bank: ₹{{ number_format($a->bank_collection, 2) }}">
                    ₹ {{ number_format($rowPaid, 2) }}
                  </td>

                  <!-- Outstanding (₹) -->
                  <td class="px-3.5 py-3 text-right font-bold font-mono text-xs {{ $rowDue > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    ₹ {{ number_format($rowDue, 2) }}
                  </td>

                  <!-- Status -->
                  <td class="px-3.5 py-3 text-center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                      <i class="fa-solid fa-lock text-[9px]"></i> COMPLETED
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="px-3.5 py-3 text-center">
                    <div class="inline-flex items-center gap-1.5 justify-center">
                      <button type="button" class="w-8 h-8 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 flex items-center justify-center transition-all cursor-pointer shadow-2xs" onclick="viewAuditDossier({{ $a->id }})" title="View Details">
                        <i class="fa-solid fa-eye text-xs"></i>
                      </button>
                      <button type="button" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all cursor-pointer shadow-2xs" onclick="printAuditById({{ $a->id }})" title="Print Executive Dossier">
                        <i class="fa-solid fa-print text-xs"></i>
                      </button>
                      @if($isTodayAudit)
                        <a href="{{ route('frontoffice.nightaudit') }}" class="w-8 h-8 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition-all shadow-2xs" title="Re-Run Today's Audit">
                          <i class="fa-solid fa-rotate text-xs"></i>
                        </a>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="text-center py-12 px-4 text-slate-400">
                    <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3 text-xl">
                      <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <div class="text-sm font-bold text-slate-700">No night audit records found</div>
                    <div class="text-xs mt-1 text-slate-500">
                      Try adjusting your date range filters or search keywords above.
                    </div>
                    @if($fromDate || $toDate || $search)
                      <div class="mt-4">
                        <a href="{{ route('admin.nightaudit.index') }}" class="inline-flex items-center gap-1.5 h-8 px-3.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs">
                          <i class="fa-solid fa-rotate-left text-[10px]"></i> Clear All Filters
                        </a>
                      </div>
                    @endif
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($audits->hasPages())
          <div class="px-5 py-3.5 border-t border-slate-200/80 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500">
            <div class="font-medium">
              Showing {{ $audits->firstItem() }} to {{ $audits->lastItem() }} of {{ $audits->total() }} entries
            </div>
            <div>
              {{ $audits->links() }}
            </div>
          </div>
        @endif
      </div>

    </section>
  </div>
</div>

<!-- AUDIT DOSSIER MODAL -->
<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 hidden" id="audit-dossier-modal">
  <div class="bg-white w-full max-w-6xl h-[90vh] max-h-[92vh] rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden">
    
    <!-- Modal Top Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-6 py-4 flex items-center justify-between shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-white shrink-0">
          <i class="fa-solid fa-moon text-base"></i>
        </div>
        <div>
          <h3 id="md-title" class="text-base font-extrabold text-white">Night Audit Details</h3>
          <div id="md-subtitle" class="text-xs text-indigo-100 font-medium mt-0.5"></div>
        </div>
      </div>
      <button type="button" onclick="closeAuditModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors text-lg font-bold cursor-pointer" title="Close Modal">&times;</button>
    </div>

    <!-- Modal Body -->
    <div id="md-content" class="p-0 overflow-hidden bg-slate-50 flex-1 min-h-0 flex flex-col">
      <div class="text-center py-12 text-slate-500">
        <i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-600"></i>
        <div class="mt-2 text-xs font-bold text-slate-600">Loading audit details...</div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="px-6 py-3.5 bg-white border-t border-slate-200 flex justify-end items-center gap-2.5 shrink-0">
      <button type="button" id="btn-print-dossier" class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-xs cursor-pointer">
        <i class="fa-solid fa-print text-[11px]"></i> Print Dossier
      </button>
      <button type="button" onclick="closeAuditModal()" class="inline-flex items-center h-9 px-4 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-xl transition-all shadow-xs cursor-pointer">
        Close
      </button>
    </div>

  </div>
</div>

<!-- HIDDEN PRINT AREA -->
<div id="printable-audit-container" class="hidden print:block"></div>

@endsection

@push('scripts')
<script>
  let currentLoadedAudit = null;

  function viewAuditDossier(auditId) {
    const modal = document.getElementById('audit-dossier-modal');
    const content = document.getElementById('md-content');
    const title = document.getElementById('md-title');
    const subtitle = document.getElementById('md-subtitle');
    const printBtn = document.getElementById('btn-print-dossier');

    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }
    content.innerHTML = `
      <div class="text-center py-12 text-slate-500">
        <i class="fa-solid fa-spinner fa-spin text-2xl text-indigo-600"></i>
        <div class="mt-2 text-xs font-bold text-slate-600">Loading audit details...</div>
      </div>
    `;

    fetch(`{{ url('/admin/nightaudit') }}/${auditId}`)
      .then(res => res.json())
      .then(data => {
        if (!data.success || !data.audit) {
          content.innerHTML = `<div class="text-center py-10 text-rose-500 font-bold text-xs">Failed to load audit record.</div>`;
          return;
        }

        const a = data.audit;
        currentLoadedAudit = a;

        const auditDateFormatted = a.audit_date ? new Date(a.audit_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'N/A';
        const auditDay = a.audit_date ? new Date(a.audit_date).toLocaleDateString('en-US', { weekday: 'long' }) : '';
        const completedTime = a.completed_at ? new Date(a.completed_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : 'N/A';
        const auditorName = a.auditor_name || (a.auditor ? a.auditor.name : 'Auditor');

        title.textContent = `Night Audit Details • ${auditDateFormatted}`;
        subtitle.textContent = `${auditorName} • ${completedTime}`;

        const snapshot = a.audit_snapshot || {};
        const payments = snapshot.payments_list || [];
        const occupiedRooms = snapshot.occupied_rooms || [];
        const hkHistories = snapshot.housekeeping_histories || [];
        const opHistories = snapshot.operational_histories || [];
        const guestsList = snapshot.guests_list || [];
        const unsettled = snapshot.unsettled_summary || {};
        const inwardStock = snapshot.inward_stock || [];
        const totalInwardAmount = Number(snapshot.total_inward_amount || 0);

        let totalRoomBill = 0;
        let totalExtraServices = 0;
        let totalGrandBill = 0;
        let totalPaid = 0;
        let totalDue = 0;
        let settledCount = 0;
        let unsettledCount = 0;

        if (guestsList && guestsList.length > 0) {
          guestsList.forEach(g => {
            const r = Number(g.room_charges !== undefined ? g.room_charges : ((g.total_bill || 0) - Number(g.extra_service_charges || 0)));
            const e = Number(g.extra_service_charges || 0);
            const b = Number(g.grand_total_bill !== undefined ? g.grand_total_bill : (g.total_bill || 0));
            const p = Number(g.paid_amount || 0);
            const d = Number(g.remaining_amount || 0);
            totalRoomBill += r;
            totalExtraServices += e;
            totalGrandBill += b;
            totalPaid += p;
            totalDue += d;
            if (d <= 0.01) {
              settledCount++;
            } else {
              unsettledCount++;
            }
          });
        } else {
          totalGrandBill = Number(a.total_revenue || a.room_revenue || 0);
          totalRoomBill = Number(a.room_revenue || totalGrandBill);
          totalExtraServices = Math.max(0, totalGrandBill - totalRoomBill);
          totalPaid = Number(a.total_payments || 0);
          totalDue = Math.max(0, totalGrandBill - totalPaid);
        }

        content.innerHTML = `
          <!-- Fixed Modal Navigation Tabs -->
          <div class="flex items-center gap-2 p-3 bg-white border-b border-slate-200/80 overflow-x-auto shrink-0">
            <button type="button" class="dossier-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer bg-indigo-50 text-indigo-700 border border-indigo-200/80" onclick="switchDossierTab('md-tab-summary', this)">
              <i class="fa-solid fa-chart-pie text-[11px]"></i> Summary &amp; Reconciliations
            </button>
            <button type="button" class="dossier-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all whitespace-nowrap cursor-pointer" onclick="switchDossierTab('md-tab-payments', this)">
              <i class="fa-solid fa-receipt text-[11px]"></i> Today Bookings / Payments (${guestsList.length})
            </button>
            <button type="button" class="dossier-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all whitespace-nowrap cursor-pointer" onclick="switchDossierTab('md-tab-rooms', this)">
              <i class="fa-solid fa-bed text-[11px]"></i> Occupied Rooms (${occupiedRooms.length})
            </button>
            <button type="button" class="dossier-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all whitespace-nowrap cursor-pointer" onclick="switchDossierTab('md-tab-hk', this)">
              <i class="fa-solid fa-broom text-[11px]"></i> Housekeeping (${hkHistories.length})
            </button>
            <button type="button" class="dossier-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all whitespace-nowrap cursor-pointer" onclick="switchDossierTab('md-tab-op', this)">
              <i class="fa-solid fa-sliders text-[11px]"></i> Operational (${opHistories.length})
            </button>
            <button type="button" class="dossier-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all whitespace-nowrap cursor-pointer" onclick="switchDossierTab('md-tab-inward', this)">
              <i class="fa-solid fa-boxes-stacked text-[11px]"></i> Store Inward (${inwardStock.length})
              ${totalInwardAmount > 0 ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">₹${totalInwardAmount.toFixed(2)}</span>` : ''}
            </button>
          </div>

          <!-- Scrollable Tab Panes Container -->
          <div class="overflow-y-auto p-4 sm:p-5 flex-1 space-y-4">

            <!-- TAB 1: SUMMARY & RECONCILIATIONS -->
            <div id="md-tab-summary" class="dossier-tab-pane space-y-4">
              
              <!-- Top Key Data Strip -->
              <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div>
                  <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Audit Date</div>
                  <div class="text-sm font-extrabold text-slate-900 mt-0.5">${auditDateFormatted} <span class="text-xs text-slate-400 font-medium">(${auditDay})</span></div>
                </div>
                <div>
                  <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Completion Time</div>
                  <div class="text-sm font-extrabold font-mono text-slate-900 mt-0.5">${completedTime}</div>
                </div>
                <div>
                  <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Auditor Name</div>
                  <div class="text-sm font-extrabold text-slate-900 mt-0.5">${escapeHtml(auditorName)}</div>
                </div>
                <div>
                  <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Status</div>
                  <div class="mt-0.5">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                      <i class="fa-solid fa-lock text-[9px]"></i> ${escapeHtml(a.status || 'COMPLETED')}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Collections & Revenue 2-Columns Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Collections Box -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
                  <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-slate-700 pb-2.5 border-b border-slate-100">
                    <i class="fa-solid fa-cash-register text-emerald-600"></i> Collections Breakdown
                  </div>
                  <div class="divide-y divide-slate-100 text-xs">
                    <div class="flex items-center justify-between py-2"><span class="text-slate-500 font-semibold">Cash:</span><span class="font-bold font-mono text-emerald-600">₹ ${fmt(a.cash_collection)}</span></div>
                    <div class="flex items-center justify-between py-2"><span class="text-slate-500 font-semibold">UPI / Digital:</span><span class="font-bold font-mono text-indigo-600">₹ ${fmt(a.upi_collection)}</span></div>
                    <div class="flex items-center justify-between py-2"><span class="text-slate-500 font-semibold">Card / POS:</span><span class="font-bold font-mono text-amber-600">₹ ${fmt(a.card_collection)}</span></div>
                    <div class="flex items-center justify-between py-2"><span class="text-slate-500 font-semibold">Bank Transfer:</span><span class="font-bold font-mono text-sky-600">₹ ${fmt(a.bank_collection)}</span></div>
                    <div class="flex items-center justify-between pt-2.5 border-t-2 border-slate-200 text-sm font-extrabold">
                      <span class="text-slate-900">Total Collections:</span>
                      <span class="font-mono text-emerald-600 font-black">₹ ${fmt(a.total_payments)}</span>
                    </div>
                  </div>
                </div>

                <!-- Revenue Box -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
                  <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-slate-700 pb-2.5 border-b border-slate-100">
                    <i class="fa-solid fa-chart-line text-indigo-600"></i> Revenue Reconciliation
                  </div>
                  <div class="divide-y divide-slate-100 text-xs">
                    <div class="flex items-center justify-between py-2"><span class="text-slate-500 font-semibold">Room Tariff Revenue:</span><span class="font-bold font-mono text-indigo-600">₹ ${fmt(a.room_revenue)}</span></div>
                    ${Number(a.food_revenue || 0) > 0 ? `<div class="flex items-center justify-between py-2"><span class="text-slate-500 font-semibold">Food / Meal Plans:</span><span class="font-bold font-mono text-slate-800">₹ ${fmt(a.food_revenue)}</span></div>` : ''}
                    <div class="flex items-center justify-between py-2"><span class="text-slate-500 font-semibold">Tax / GST:</span><span class="font-bold font-mono text-slate-800">₹ ${fmt(a.tax_amount)}</span></div>
                    <div class="flex items-center justify-between py-2"><span class="text-slate-500 font-semibold">Discount:</span><span class="font-bold font-mono text-rose-600">- ₹ ${fmt(a.discount_amount)}</span></div>
                    <div class="flex items-center justify-between pt-2.5 border-t-2 border-slate-200 text-sm font-extrabold">
                      <span class="text-slate-900">Total Revenue:</span>
                      <span class="font-mono text-purple-600 font-black">₹ ${fmt(a.total_revenue)}</span>
                    </div>
                  </div>
                </div>

              </div>

              <!-- Rooms & Occupancy KPI Grid -->
              <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
                <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-slate-700 pb-2.5 border-b border-slate-100">
                  <i class="fa-solid fa-bed text-indigo-600"></i> Rooms &amp; Occupancy
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center mt-3">
                  <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-3">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Occupancy Rate</div>
                    <div class="text-lg font-black font-mono text-indigo-600 mt-1">${Number(a.occupancy_rate || 0).toFixed(1)}%</div>
                  </div>
                  <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-3">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Occupied Rooms</div>
                    <div class="text-lg font-black font-mono text-slate-800 mt-1">${a.occupied_rooms || 0} / ${a.total_rooms || 0}</div>
                  </div>
                  <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-3">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Vacant Rooms</div>
                    <div class="text-lg font-black font-mono text-emerald-600 mt-1">${a.vacant_rooms || 0}</div>
                  </div>
                  <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-3">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ADR / RevPAR</div>
                    <div class="text-base font-black font-mono text-slate-800 mt-1">₹ ${Number(a.adr || 0).toFixed(0)} / ₹ ${Number(a.revpar || 0).toFixed(0)}</div>
                  </div>
                </div>
              </div>

              <!-- Unsettled Folio Balances Box -->
              <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
                <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-slate-700 pb-2.5 border-b border-slate-100">
                  <i class="fa-solid fa-clock text-amber-600"></i> Folio Reconciliations
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-center mt-3">
                  <div class="bg-rose-50/60 border border-rose-200/80 rounded-xl p-3">
                    <div class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Outstanding Unsettled Due</div>
                    <div class="text-lg font-black font-mono text-rose-700 mt-1">₹ ${fmt(unsettled.total_unsettled_amount || 0)}</div>
                  </div>
                  <div class="bg-slate-50/70 border border-slate-200/80 rounded-xl p-3">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Unsettled Folios</div>
                    <div class="text-lg font-black font-mono text-rose-600 mt-1">${unsettled.unsettled_bookings_count || 0} Folio(s)</div>
                  </div>
                  <div class="bg-emerald-50/60 border border-emerald-200/80 rounded-xl p-3">
                    <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Settled Folios</div>
                    <div class="text-lg font-black font-mono text-emerald-700 mt-1">${unsettled.settled_bookings_count || 0} Folio(s)</div>
                  </div>
                </div>
              </div>

              <!-- Remarks -->
              ${a.remarks ? `
                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
                  <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-slate-700 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-comment-dots text-indigo-600"></i> Auditor Remarks
                  </div>
                  <div class="text-xs text-slate-600 leading-relaxed mt-2">${escapeHtml(a.remarks)}</div>
                </div>
              ` : ''}

            </div>

            <!-- TAB 2: TODAY PAYMENTS (Booking Billings & Extra Services) -->
            <div id="md-tab-payments" class="dossier-tab-pane hidden">
              <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                  <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
                      <th class="px-3.5 py-3 text-center w-11">SL</th>
                      <th class="px-3.5 py-3">BOOKING NUMBER</th>
                      <th class="px-3.5 py-3">ROOM(S) &amp; FLOOR</th>
                      <th class="px-3.5 py-3">PRIMARY GUEST &amp; OCCUPANTS</th>
                      <th class="px-3.5 py-3">SETTLE / FOLIO #</th>
                      <th class="px-3.5 py-3">STAY DATES</th>
                      <th class="px-3.5 py-3 text-center">STATUS</th>
                      <th class="px-3.5 py-3 text-right">ROOM BILL</th>
                      <th class="px-3.5 py-3 text-right">EXTRA SERVICES</th>
                      <th class="px-3.5 py-3 text-right">TOTAL BILL</th>
                      <th class="px-3.5 py-3 text-right">PAID</th>
                      <th class="px-3.5 py-3 text-right">REMAINING</th>
                      <th class="px-3.5 py-3 text-center">SETTLEMENT</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    ${guestsList.length > 0 ? guestsList.map((g, idx) => {
                      const roomsList = Array.isArray(g.rooms) && g.rooms.length > 0 ? g.rooms : [{ room_number: g.room_number || 'Room', floor: '1' }];
                      const extraServicesVal = Number(g.extra_service_charges || 0);
                      const extraCount = Number(g.extra_orders_count || 0);
                      const occupants = Array.isArray(g.occupants_names) ? g.occupants_names : [];
                      const totalOccupants = Number(g.total_occupants || occupants.length || 1);
                      const pName = g.primary_guest_name || g.guest_name || 'Guest';
                      const pPhone = g.primary_mobile || g.mobile_no || '-';

                      return `
                        <tr class="hover:bg-slate-50/80 transition-colors">
                          <td class="px-3.5 py-2.5 text-center font-mono font-bold text-slate-400 text-xs">${idx + 1}</td>
                          <td class="px-3.5 py-2.5">
                            <div class="font-bold text-indigo-700 font-mono text-xs whitespace-nowrap">
                              ${escapeHtml(g.booking_number || ('RES-' + g.booking_id))}
                            </div>
                            <div class="text-[10px] text-slate-400 font-medium mt-0.5">
                              ${roomsList.length} Room(s)
                            </div>
                          </td>
                          <td class="px-3.5 py-2.5">
                            <div class="flex flex-wrap gap-1.5 items-center">
                              ${roomsList.map(rm => `
                                <span class="inline-flex items-center gap-1 font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg font-mono text-xs whitespace-nowrap border border-indigo-200/60">
                                  <i class="fa-solid fa-door-open text-[9px]"></i> ${escapeHtml(rm.room_number)}
                                  <span class="text-[10px] opacity-75">(Fl ${escapeHtml(rm.floor)})</span>
                                </span>
                              `).join('')}
                              ${Array.isArray(g.transfers) && g.transfers.length > 0 ? g.transfers.map(tf => `
                                <span class="text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded-md inline-flex items-center gap-1" title="Room swiped: R${escapeHtml(tf.from)} to R${escapeHtml(tf.to)} on ${escapeHtml(tf.date)}">
                                  <i class="fa-solid fa-repeat text-[8px]"></i> R${escapeHtml(tf.from)}&rarr;R${escapeHtml(tf.to)}
                                </span>
                              `).join('') : ''}
                            </div>
                          </td>
                          <td class="px-3.5 py-2.5">
                            <div class="font-bold text-slate-800 text-xs">
                              ${escapeHtml(pName)}
                              ${totalOccupants > 1 ? `
                                <span class="text-[10px] font-bold bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded-full ml-1 whitespace-nowrap">
                                  +${totalOccupants - 1}
                                </span>
                              ` : ''}
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                              <i class="fa-solid fa-phone text-[9px] opacity-60"></i> ${escapeHtml(pPhone)}
                            </div>
                          </td>
                          <td class="px-3.5 py-2.5">
                            <span class="font-mono text-xs font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-lg border border-teal-200/80 whitespace-nowrap">
                              ${escapeHtml(g.settle_number || g.folio_number || '-')}
                            </span>
                          </td>
                          <td class="px-3.5 py-2.5 whitespace-nowrap text-xs">
                            <div class="text-slate-700 flex items-center gap-1">
                              <i class="fa-solid fa-arrow-right-to-bracket text-[9px] text-emerald-600"></i> ${escapeHtml(g.checkin_date || '-')}
                            </div>
                            <div class="text-slate-400 text-[11px] flex items-center gap-1 mt-0.5">
                              <i class="fa-solid fa-arrow-right-from-bracket text-[9px] text-rose-500"></i> ${escapeHtml(g.checkout_date || '-')}
                            </div>
                          </td>
                          <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                              ${escapeHtml(g.stay_status || g.status || 'Active')}
                            </span>
                          </td>
                          <td class="px-3.5 py-2.5 text-right font-mono font-bold text-slate-800 whitespace-nowrap">
                            ₹ ${fmt(g.room_charges !== undefined ? g.room_charges : (Number(g.total_bill || 0) - extraServicesVal))}
                          </td>
                          <td class="px-3.5 py-2.5 text-right font-mono whitespace-nowrap">
                            ${(extraServicesVal > 0 || Number(g.extra_paid_charges || 0) > 0) ? `
                              ${extraServicesVal > 0 ? `<div class="font-bold text-amber-700">+₹ ${fmt(extraServicesVal)}</div>` : ''}
                              ${Number(g.extra_paid_charges || 0) > 0 ? `<div class="font-bold text-emerald-700 text-[11px]"><i class="fa-solid fa-circle-check"></i> ₹ ${fmt(g.extra_paid_charges)} <span class="text-[9px] opacity-75">(Paid)</span></div>` : ''}
                              <div class="text-[10px] font-bold text-amber-600 mt-0.5">
                                <i class="fa-solid fa-bell-concierge"></i> ${extraCount} order(s)
                              </div>
                              ${Array.isArray(g.extra_orders_detailed) && g.extra_orders_detailed.length > 0 ? `
                                <div class="flex flex-col gap-1 mt-1.5 items-end">
                                  ${g.extra_orders_detailed.map(eo => eo.is_paid ? `
                                    <div class="text-[9px] text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-md px-1.5 py-0.5 inline-flex items-center gap-1" title="${escapeHtml(eo.order_number)}: ${escapeHtml(eo.items_summary || '')} (Paid Direct)">
                                      <span class="opacity-75"><i class="fa-solid fa-check text-[7.5px]"></i> ${escapeHtml(eo.date_short)}:</span>
                                      <span class="font-bold">₹ ${fmt(eo.total_amount)} (PAID)</span>
                                      ${eo.table_number ? `<span class="text-[8px] bg-emerald-200 text-emerald-900 px-1 rounded-sm font-bold"><i class="fa-solid fa-chair text-[7px]"></i> ${escapeHtml(eo.table_number)}</span>` : ''}
                                    </div>
                                  ` : `
                                    <div class="text-[9px] text-orange-900 bg-orange-50 border border-orange-200 rounded-md px-1.5 py-0.5 inline-flex items-center gap-1" title="${escapeHtml(eo.order_number)}: ${escapeHtml(eo.items_summary || '')} (Charged to Folio)">
                                      <span class="opacity-75"><i class="fa-regular fa-clock text-[7.5px]"></i> ${escapeHtml(eo.date_short)}:</span>
                                      <span class="font-bold">+ ₹ ${fmt(eo.total_amount)}</span>
                                      ${eo.table_number ? `<span class="text-[8px] bg-orange-200 text-orange-900 px-1 rounded-sm font-bold"><i class="fa-solid fa-chair text-[7px]"></i> ${escapeHtml(eo.table_number)}</span>` : ''}
                                    </div>
                                  `).join('')}
                                </div>
                              ` : ''}
                            ` : `
                              <span class="text-slate-400 text-xs">₹ 0.00</span>
                            `}
                          </td>
                          <td class="px-3.5 py-2.5 text-right font-mono font-black text-slate-900 whitespace-nowrap">
                            ₹ ${fmt(g.grand_total_bill !== undefined ? g.grand_total_bill : g.total_bill)}
                          </td>
                          <td class="px-3.5 py-2.5 text-right font-mono text-emerald-600 font-bold whitespace-nowrap">
                            ₹ ${fmt(g.paid_amount)}
                          </td>
                          <td class="px-3.5 py-2.5 text-right font-mono font-bold ${Number(g.remaining_amount || 0) > 0.01 ? 'text-rose-600' : 'text-emerald-600'} whitespace-nowrap">
                            <div>₹ ${fmt(g.remaining_amount)}</div>
                            ${extraServicesVal > 0 && Number(g.remaining_amount || 0) > 0.01 ? `
                              <div class="text-[10px] font-bold text-amber-700 mt-0.5">
                                (incl. +₹${fmt(extraServicesVal)} serv.)
                              </div>
                            ` : ''}
                          </td>
                          <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                            ${g.is_settled ? `
                              <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/80 inline-flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[9px]"></i> Settled
                              </span>
                            ` : `
                              <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200/80 inline-flex items-center gap-1">
                                <i class="fa-solid fa-clock text-[9px]"></i> Unsettled
                              </span>
                            `}
                          </td>
                        </tr>
                      `;
                    }).join('') : `
                      <tr>
                        <td colspan="13" class="text-center py-8 text-slate-400">No booking payment records recorded for this audit date.</td>
                      </tr>
                    `}
                  </tbody>
                  ${guestsList.length > 0 ? `
                    <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-200 text-xs">
                      <tr>
                        <td colspan="7" class="px-3.5 py-3 text-right text-slate-500 uppercase tracking-wider text-[11px]">
                          TOTALS (ROOMS / EXTRA SERVICES / GRAND BILL / PAID / REMAINING):
                        </td>
                        <td class="px-3.5 py-3 text-right font-mono text-slate-800 whitespace-nowrap">
                          ₹ ${fmt(totalRoomBill)}
                        </td>
                        <td class="px-3.5 py-3 text-right font-mono text-amber-700 whitespace-nowrap">
                          ₹ ${fmt(totalExtraServices)}
                        </td>
                        <td class="px-3.5 py-3 text-right font-mono text-slate-900 font-black whitespace-nowrap">
                          ₹ ${fmt(totalGrandBill)}
                        </td>
                        <td class="px-3.5 py-3 text-right font-mono text-emerald-700 whitespace-nowrap">
                          ₹ ${fmt(totalPaid)}
                        </td>
                        <td class="px-3.5 py-3 text-right font-mono ${totalDue > 0.01 ? 'text-rose-600' : 'text-slate-600'} whitespace-nowrap">
                          ₹ ${fmt(totalDue)}
                        </td>
                        <td class="px-3.5 py-3 text-center text-xs ${unsettledCount > 0 ? 'text-rose-600' : 'text-emerald-600'} whitespace-nowrap">
                          ${unsettledCount} Unsettled
                        </td>
                      </tr>
                    </tfoot>
                  ` : ''}
                </table>
              </div>
            </div>

            <!-- TAB 3: OCCUPIED ROOMS -->
            <div id="md-tab-rooms" class="dossier-tab-pane hidden">
              <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                  <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
                      <th class="px-3.5 py-3 text-center w-11">SL</th>
                      <th class="px-3.5 py-3">Room</th>
                      <th class="px-3.5 py-3">Category</th>
                      <th class="px-3.5 py-3">Floor</th>
                      <th class="px-3.5 py-3">Guest &amp; Settle #</th>
                      <th class="px-3.5 py-3">Check-in</th>
                      <th class="px-3.5 py-3">Check-out</th>
                      <th class="px-3.5 py-3 text-center">Operational Status</th>
                      <th class="px-3.5 py-3 text-center">Housekeeping</th>
                      <th class="px-3.5 py-3 text-right">Rate / Day</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    ${occupiedRooms.length > 0 ? occupiedRooms.map((rm, idx) => `
                      <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-3.5 py-2.5 text-center font-mono font-bold text-slate-400 text-xs">${idx + 1}</td>
                        <td class="px-3.5 py-2.5"><span class="inline-flex items-center gap-1 font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg font-mono text-xs border border-indigo-200/60"><i class="fa-solid fa-door-open text-[9px]"></i> ${escapeHtml(rm.room_number)}</span></td>
                        <td class="px-3.5 py-2.5 font-semibold text-slate-700">${escapeHtml(rm.category || 'Standard')}</td>
                        <td class="px-3.5 py-2.5 text-slate-500 text-xs">${escapeHtml(rm.floor || 'Floor 1')}</td>
                        <td class="px-3.5 py-2.5">
                          <div class="font-bold text-slate-800 text-xs">${escapeHtml(rm.guest_name || 'Guest')}</div>
                          <div class="text-[11px] text-indigo-600 font-bold font-mono mt-0.5">${escapeHtml(rm.settle_number || rm.folio_number || '-')}</div>
                        </td>
                        <td class="px-3.5 py-2.5 text-slate-600 text-xs">${escapeHtml(rm.checkin_date || '-')}</td>
                        <td class="px-3.5 py-2.5 text-slate-600 text-xs">${escapeHtml(rm.checkout_date || '-')}</td>
                        <td class="px-3.5 py-2.5 text-center">
                          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                            ${escapeHtml(rm.operational_status || 'Active')}
                          </span>
                        </td>
                        <td class="px-3.5 py-2.5 text-center">
                          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200/80">
                            ${escapeHtml(rm.housekeeping_status || 'Clean')}
                          </span>
                        </td>
                        <td class="px-3.5 py-2.5 text-right font-bold font-mono text-slate-800">₹ ${fmt(rm.day_rate)}</td>
                      </tr>
                    `).join('') : `
                      <tr>
                        <td colspan="10" class="text-center py-8 text-slate-400">No occupied rooms recorded on this date.</td>
                      </tr>
                    `}
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 4: HOUSEKEEPING HISTORY -->
            <div id="md-tab-hk" class="dossier-tab-pane hidden">
              <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                  <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
                      <th class="px-3.5 py-3 text-center w-11">SL</th>
                      <th class="px-3.5 py-3 w-32">Time / Date</th>
                      <th class="px-3.5 py-3">Room</th>
                      <th class="px-3.5 py-3">Category</th>
                      <th class="px-3.5 py-3">Floor</th>
                      <th class="px-3.5 py-3 text-center">Housekeeping State</th>
                      <th class="px-3.5 py-3 text-center">Task Status</th>
                      <th class="px-3.5 py-3">Start Time</th>
                      <th class="px-3.5 py-3">Completion Time</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    ${hkHistories.length > 0 ? hkHistories.map((hk, idx) => `
                      <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-3.5 py-2.5 text-center font-mono font-bold text-slate-400 text-xs">${idx + 1}</td>
                        <td class="px-3.5 py-2.5 font-mono text-xs text-slate-600">
                          ${escapeHtml(hk.time || '-')}
                          <span class="block text-[10px] text-slate-400">${escapeHtml(hk.date || '')}</span>
                        </td>
                        <td class="px-3.5 py-2.5"><span class="inline-flex items-center gap-1 font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg font-mono text-xs border border-indigo-200/60">${escapeHtml(hk.room_number)}</span></td>
                        <td class="px-3.5 py-2.5 font-semibold text-slate-700">${escapeHtml(hk.category || 'Standard')}</td>
                        <td class="px-3.5 py-2.5 text-slate-500 text-xs">${escapeHtml(hk.floor || 'Floor 1')}</td>
                        <td class="px-3.5 py-2.5 text-center">
                          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200/80">
                            <i class="fa-solid fa-broom text-[9px]"></i> ${escapeHtml(hk.housekeeping_status || 'Clean')}
                          </span>
                        </td>
                        <td class="px-3.5 py-2.5 text-center">
                          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                            ${escapeHtml(hk.status || 'Complete')}
                          </span>
                        </td>
                        <td class="px-3.5 py-2.5 font-mono text-xs text-slate-600">${escapeHtml(hk.start_time || '-')}</td>
                        <td class="px-3.5 py-2.5 font-mono text-xs text-slate-600">${escapeHtml(hk.completion_time || '-')}</td>
                      </tr>
                    `).join('') : `
                      <tr>
                        <td colspan="9" class="text-center py-8 text-slate-400">No housekeeping events recorded on this date.</td>
                      </tr>
                    `}
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 5: OPERATIONAL HISTORY -->
            <div id="md-tab-op" class="dossier-tab-pane hidden">
              <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                  <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-200/80">
                      <th class="px-3.5 py-3 text-center w-11">SL</th>
                      <th class="px-3.5 py-3 w-32">Time / Date</th>
                      <th class="px-3.5 py-3">Room</th>
                      <th class="px-3.5 py-3">Category</th>
                      <th class="px-3.5 py-3">Floor</th>
                      <th class="px-3.5 py-3 text-center">Operational Status</th>
                      <th class="px-3.5 py-3 text-center">Room Model State</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    ${opHistories.length > 0 ? opHistories.map((op, idx) => `
                      <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-3.5 py-2.5 text-center font-mono font-bold text-slate-400 text-xs">${idx + 1}</td>
                        <td class="px-3.5 py-2.5 font-mono text-xs text-slate-600">
                          ${escapeHtml(op.time || '-')}
                          <span class="block text-[10px] text-slate-400">${escapeHtml(op.date || '')}</span>
                        </td>
                        <td class="px-3.5 py-2.5"><span class="inline-flex items-center gap-1 font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-lg font-mono text-xs border border-indigo-200/60">${escapeHtml(op.room_number)}</span></td>
                        <td class="px-3.5 py-2.5 font-semibold text-slate-700">${escapeHtml(op.category || 'Standard')}</td>
                        <td class="px-3.5 py-2.5 text-slate-500 text-xs">${escapeHtml(op.floor || 'Floor 1')}</td>
                        <td class="px-3.5 py-2.5 text-center">
                          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                            <i class="fa-solid fa-circle-check text-[9px]"></i> ${escapeHtml(op.operational_status || 'Active')}
                          </span>
                        </td>
                        <td class="px-3.5 py-2.5 text-center">
                          <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            ${escapeHtml(op.room_status || 'Active')}
                          </span>
                        </td>
                      </tr>
                    `).join('') : `
                      <tr>
                        <td colspan="7" class="text-center py-8 text-slate-400">No operational status changes logged on this date.</td>
                      </tr>
                    `}
                  </tbody>
                </table>
              </div>
            </div>

            <!-- TAB 6: STORE INWARD STOCK PURCHASES -->
            <div id="md-tab-inward" class="dossier-tab-pane hidden space-y-4">
              <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
                <div class="p-3.5 border-b border-slate-200/80 flex items-center justify-between bg-slate-50/50">
                  <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span class="text-xs font-bold text-slate-900">Store Inward Stock Purchases (${inwardStock.length})</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-xl border border-amber-200">
                      Total Inward: ₹${totalInwardAmount.toFixed(2)}
                    </span>
                  </div>
                </div>
                <table class="min-w-full divide-y divide-slate-200/80 text-xs">
                  <thead class="bg-slate-50/80 text-slate-600 font-semibold text-[11px] uppercase tracking-wider">
                    <tr>
                      <th class="px-3.5 py-2.5 text-center w-12">SL</th>
                      <th class="px-3.5 py-2.5 text-left">Time</th>
                      <th class="px-3.5 py-2.5 text-left">Dish / Item Name</th>
                      <th class="px-3.5 py-2.5 text-left">Reference #</th>
                      <th class="px-3.5 py-2.5 text-center">Plates Added</th>
                      <th class="px-3.5 py-2.5 text-right">Unit Cost</th>
                      <th class="px-3.5 py-2.5 text-right">Total Amount</th>
                      <th class="px-3.5 py-2.5 text-left">Receiver</th>
                      <th class="px-3.5 py-2.5 text-left">Remarks</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    ${inwardStock.length > 0 ? inwardStock.map((m, idx) => `
                      <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-3.5 py-2.5 text-center font-mono text-slate-500">${idx + 1}</td>
                        <td class="px-3.5 py-2.5 font-mono text-slate-600 whitespace-nowrap">
                          ${escapeHtml(m.time || '-')}
                          <span class="block text-[10px] text-slate-400">${escapeHtml(m.date || '')}</span>
                        </td>
                        <td class="px-3.5 py-2.5 font-bold text-slate-900 whitespace-nowrap">
                          <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            ${escapeHtml(m.item_name || 'Item')}
                          </span>
                        </td>
                        <td class="px-3.5 py-2.5 font-mono text-slate-700 whitespace-nowrap">${escapeHtml(m.reference_no || '-')}</td>
                        <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            +${m.quantity} Plates
                          </span>
                        </td>
                        <td class="px-3.5 py-2.5 text-right font-mono text-slate-600 whitespace-nowrap">
                          ${m.unit_cost ? '₹' + Number(m.unit_cost).toFixed(2) : '-'}
                        </td>
                        <td class="px-3.5 py-2.5 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                          ₹${Number(m.total_cost || 0).toFixed(2)}
                        </td>
                        <td class="px-3.5 py-2.5 text-slate-700 whitespace-nowrap">${escapeHtml(m.performed_by || '-')}</td>
                        <td class="px-3.5 py-2.5 text-slate-500 max-w-xs truncate" title="${escapeHtml(m.remarks || '')}">
                          ${escapeHtml(m.remarks || '-')}
                        </td>
                      </tr>
                    `).join('') : `
                      <tr>
                        <td colspan="9" class="text-center py-8 text-slate-400">No store inward stock purchases with entered amounts logged on this date.</td>
                      </tr>
                    `}
                  </tbody>
                  ${inwardStock.length > 0 ? `
                    <tfoot class="bg-slate-50/80 font-bold border-t border-slate-200">
                      <tr>
                        <td colspan="6" class="px-3.5 py-2.5 text-right uppercase tracking-wider text-slate-600">Total Store Inward Purchases:</td>
                        <td class="px-3.5 py-2.5 text-right font-mono text-amber-900">₹${totalInwardAmount.toFixed(2)}</td>
                        <td colspan="2"></td>
                      </tr>
                    </tfoot>
                  ` : ''}
                </table>
              </div>
            </div>

          </div>
        `;
        if (printBtn) {
          printBtn.onclick = () => printAuditObject(a);
        }
      })
      .catch(err => {
        content.innerHTML = `<div class="text-center py-10 text-rose-500 font-bold text-xs">Error: ${err.message}</div>`;
      });
  }

  function switchDossierTab(tabId, btn) {
    const modal = document.getElementById('audit-dossier-modal');
    if (!modal) return;
    modal.querySelectorAll('.dossier-tab-btn').forEach(t => {
      t.className = 'dossier-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-white border border-slate-200/80 hover:bg-slate-50 transition-all whitespace-nowrap cursor-pointer';
    });
    btn.className = 'dossier-tab-btn inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80 transition-all whitespace-nowrap cursor-pointer';
    modal.querySelectorAll('.dossier-tab-pane').forEach(p => p.classList.add('hidden'));
    const target = document.getElementById(tabId);
    if (target) target.classList.remove('hidden');
  }

  function fmt(val) {
    const n = parseFloat(val);
    return isNaN(n) ? '0.00' : n.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function closeAuditModal() {
    const modal = document.getElementById('audit-dossier-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }

  // Close modal when clicking outside
  window.addEventListener('click', function(e) {
    const modal = document.getElementById('audit-dossier-modal');
    if (e.target === modal) {
      closeAuditModal();
    }
  });

  function printAuditById(auditId) {
    var toast = document.createElement('div');
    toast.id = 'print-loading-toast';
    toast.innerHTML = '<div class="fixed bottom-6 right-6 bg-slate-900 text-white px-5 py-3 rounded-xl shadow-xl z-50 flex items-center gap-3 text-xs font-bold"><i class="fa-solid fa-spinner fa-spin text-indigo-400"></i> Preparing audit print dossier...</div>';
    document.body.appendChild(toast);

    fetch(`{{ url('/admin/nightaudit') }}/${auditId}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(res => res.json())
      .then(data => {
        var t = document.getElementById('print-loading-toast');
        if (t) t.remove();
        if (data.success && data.audit) {
          printAuditObject(data.audit);
        } else {
          alert(data.error || 'Failed to load audit record for printing.');
        }
      })
      .catch(err => {
        var t = document.getElementById('print-loading-toast');
        if (t) t.remove();
        console.error(err);
        alert('Could not connect to server to print night audit.');
      });
  }

  function printAuditObject(a) {
    const printArea = document.getElementById('printable-audit-container');
    if (!printArea) return;

    const snap = a.audit_snapshot || {};
    const payments = snap.payments_list || [];
    const occupied = snap.occupied_rooms || [];
    const hks = snap.housekeeping_histories || [];
    const ops = snap.operational_histories || [];
    const guests = snap.guests_list || [];
    const unsettled = snap.unsettled_summary || {};
    const inward = snap.inward_stock || [];
    const totalInward = Number(snap.total_inward_amount || 0);

    let totalBill = 0;
    let totalDue = 0;
    let settledCount = 0;
    let unsettledCount = 0;

    if (guests && guests.length > 0) {
      guests.forEach(g => {
        totalBill += Number(g.total_bill || 0);
        const due = Number(g.remaining_amount || 0);
        totalDue += due;
        if (due <= 0.01) settledCount++;
        else unsettledCount++;
      });
    } else {
      totalBill = Number(a.total_revenue || 0);
      totalDue = Math.max(0, totalBill - Number(a.total_payments || 0));
    }

    const totalPaid = Number(a.total_payments || 0);
    const occupancyRate = Number(a.occupancy_rate || 0).toFixed(1);
    const totalRooms = a.total_rooms || 0;
    const occupiedCount = a.occupied_rooms || occupied.length || 0;
    const vacantCount = a.vacant_rooms || 0;
    const maintCount = a.maintenance_rooms || 0;
    const hkCount = hks.length;
    const opCount = ops.length;

    const auditDateFormatted = a.audit_date ? new Date(a.audit_date).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : 'N/A';
    const auditDay = a.audit_date ? new Date(a.audit_date).toLocaleDateString('en-US', { weekday: 'long' }) : '';
    const completedTime = a.completed_at ? new Date(a.completed_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : 'N/A';
    const auditorName = a.auditor_name || (a.auditor ? a.auditor.name : 'Front Office Night Auditor');

    printArea.innerHTML = `
      <div class="max-w-3xl mx-auto font-sans text-slate-900 leading-normal p-2.5">
        
        <!-- Header -->
        <div class="border-b-2 border-slate-900 pb-3 mb-3.5 flex justify-between items-start">
          <div>
            <h1 class="m-0 text-xl font-black tracking-wide uppercase text-slate-900">Hotel Sagar Sonnet</h1>
            <div class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider mt-0.5">Daily Night Audit Executive Dossier &amp; Financial Reconciliation</div>
            <div class="text-[9.5px] text-slate-500 mt-0.5">Station Road, Hotel Sagar Sonnet PMS Verification System</div>
          </div>
          <div class="text-right">
            <div class="text-xs font-black text-slate-900">Audit Date: ${auditDateFormatted} (${auditDay})</div>
            <div class="text-[10px] text-slate-600 mt-0.5">Completed at: <strong>${completedTime}</strong></div>
            <div class="text-[10px] text-slate-600">Auditor: <strong>${escapeHtml(auditorName)}</strong></div>
            <span class="inline-block text-[9px] font-extrabold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 mt-1 uppercase">
              Audit Completed &amp; Verified
            </span>
          </div>
        </div>

        <!-- 1. Daily Operational Activity Summary Strip -->
        <div class="grid grid-cols-4 gap-2 mb-3">
          <div class="bg-slate-50 border border-slate-300 rounded-lg p-2 text-center">
            <div class="text-[9px] font-extrabold text-slate-600 uppercase">Today Payments</div>
            <div class="text-base font-black text-emerald-600 font-mono mt-0.5">${payments.length || (totalPaid > 0 ? 1 : 0)}</div>
            <div class="text-[8.5px] text-slate-500">₹ ${fmt(totalPaid)} Reconciled</div>
          </div>
          <div class="bg-slate-50 border border-slate-300 rounded-lg p-2 text-center">
            <div class="text-[9px] font-extrabold text-slate-600 uppercase">Occupied Rooms</div>
            <div class="text-base font-black text-blue-600 font-mono mt-0.5">${occupiedCount}</div>
            <div class="text-[8.5px] text-slate-500">${occupancyRate}% Occupancy</div>
          </div>
          <div class="bg-slate-50 border border-slate-300 rounded-lg p-2 text-center">
            <div class="text-[9px] font-extrabold text-slate-600 uppercase">Housekeeping</div>
            <div class="text-base font-black text-emerald-600 font-mono mt-0.5">${hkCount}</div>
            <div class="text-[8.5px] text-slate-500">Activity Logged</div>
          </div>
          <div class="bg-slate-50 border border-slate-300 rounded-lg p-2 text-center">
            <div class="text-[9px] font-extrabold text-slate-600 uppercase">Operational</div>
            <div class="text-base font-black text-amber-600 font-mono mt-0.5">${opCount}</div>
            <div class="text-[8.5px] text-slate-500">Room Status Logs</div>
          </div>
        </div>

        <!-- 2. Clean 4 Financial KPI Cards -->
        <div class="grid grid-cols-4 gap-2 mb-3.5">
          <div class="bg-white border border-indigo-500 rounded-lg p-2">
            <div class="text-[9px] font-extrabold text-indigo-700 uppercase">Total Bill</div>
            <div class="text-sm font-black text-indigo-700 font-mono mt-0.5">₹ ${fmt(totalBill)}</div>
            <div class="text-[8px] text-slate-500">${guests.length} booking folio(s)</div>
          </div>
          <div class="bg-white border border-emerald-500 rounded-lg p-2">
            <div class="text-[9px] font-extrabold text-emerald-700 uppercase">Collections</div>
            <div class="text-sm font-black text-emerald-700 font-mono mt-0.5">₹ ${fmt(totalPaid)}</div>
            <div class="text-[8px] text-slate-500">Reconciled on date</div>
          </div>
          <div class="bg-white border border-blue-500 rounded-lg p-2">
            <div class="text-[9px] font-extrabold text-blue-700 uppercase">Room Occupancy</div>
            <div class="text-sm font-black text-blue-700 font-mono mt-0.5">${occupancyRate}%</div>
            <div class="text-[8px] text-slate-500">${occupiedCount} / ${totalRooms} rooms</div>
          </div>
          <div class="bg-white border border-rose-500 rounded-lg p-2">
            <div class="text-[9px] font-extrabold text-rose-700 uppercase">Unsettled Balance</div>
            <div class="text-sm font-black text-rose-700 font-mono mt-0.5">₹ ${fmt(totalDue)}</div>
            <div class="text-[8px] text-slate-500">${unsettledCount} pending folio(s)</div>
          </div>
        </div>

        <!-- 3. Collections Breakdown & Revenue Reconciliations (2 columns) -->
        <div class="grid grid-cols-2 gap-2.5 mb-3.5">
          <!-- Collections Table -->
          <div>
            <div class="text-xs font-black uppercase text-indigo-950 border-b border-slate-300 pb-1 mb-1.5">
              Reconciled Collections By Mode
            </div>
            <table class="print-table text-[9.5px] w-full">
              <thead>
                <tr>
                  <th>Payment Mode</th>
                  <th class="text-right">Amount (₹)</th>
                </tr>
              </thead>
              <tbody>
                <tr><td>Cash Collection</td><td class="text-right font-mono font-bold text-emerald-600">₹ ${fmt(a.cash_collection)}</td></tr>
                <tr><td>UPI / Digital (GPay/PhonePe)</td><td class="text-right font-mono font-bold text-indigo-700">₹ ${fmt(a.upi_collection)}</td></tr>
                <tr><td>Card / POS Machine</td><td class="text-right font-mono font-bold text-amber-700">₹ ${fmt(a.card_collection)}</td></tr>
                <tr><td>Bank Transfer (NEFT/RTGS)</td><td class="text-right font-mono font-bold text-sky-700">₹ ${fmt(a.bank_collection)}</td></tr>
                ${Number(a.other_collection || 0) > 0 ? `<tr><td>Other Modes</td><td class="text-right font-mono font-bold">₹ ${fmt(a.other_collection)}</td></tr>` : ''}
              </tbody>
              <tfoot>
                <tr class="bg-slate-100 font-extrabold">
                  <td>Total Collections:</td>
                  <td class="text-right font-mono text-emerald-700">₹ ${fmt(totalPaid)}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- Room & Inventory Metrics -->
          <div>
            <div class="text-xs font-black uppercase text-indigo-950 border-b border-slate-300 pb-1 mb-1.5">
              Rooms &amp; Operational Statistics
            </div>
            <table class="print-table text-[9.5px] w-full">
              <tbody>
                <tr><td>Total Hotel Rooms Inventory</td><td class="text-right font-mono font-bold">${totalRooms} Rooms</td></tr>
                <tr><td>Occupied Rooms Sold</td><td class="text-right font-mono font-bold text-blue-600">${occupiedCount} Rooms (${occupancyRate}%)</td></tr>
                <tr><td>Vacant Available Rooms</td><td class="text-right font-mono font-bold text-emerald-600">${vacantCount} Rooms</td></tr>
                <tr><td>Maintenance / Out of Order</td><td class="text-right font-mono font-bold text-amber-600">${maintCount} Rooms</td></tr>
                <tr><td>Average Daily Rate (ADR) / RevPAR</td><td class="text-right font-mono font-bold">₹ ${fmt(a.adr)} / ₹ ${fmt(a.revpar)}</td></tr>
                <tr class="bg-slate-100 font-extrabold">
                  <td>Active Meal Plans (EP/CP/MAP/AP)</td>
                  <td class="text-right font-mono">${a.ep_plan_count || 0} / ${a.cp_plan_count || 0} / ${a.map_plan_count || 0} / ${a.ap_plan_count || 0}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- 4. Guest Billings, Settlements & Outstanding Balances Table -->
        <div class="print-avoid-break mb-3.5">
          <div class="text-xs font-black uppercase text-indigo-950 border-b border-slate-300 pb-1 mb-1.5 flex justify-between">
            <span>Guest Billings, Settlements &amp; Outstanding Balances (${guests.length} Bookings)</span>
            <span class="text-[9.5px] text-slate-500 normal-case">All amounts in INR (₹)</span>
          </div>
          <table class="print-table text-[9px] w-full">
            <thead>
              <tr>
                <th class="w-6 text-center">SL</th>
                <th>Guest Name</th>
                <th class="text-center">Room</th>
                <th>Settle #</th>
                <th>Dates</th>
                <th class="text-right">Total Bill</th>
                <th class="text-right">Paid Amt</th>
                <th class="text-right">Remaining</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              ${guests.length > 0 ? guests.map((g, idx) => `
                <tr>
                  <td class="text-center font-mono">${idx + 1}</td>
                  <td class="font-bold">${escapeHtml(g.guest_name || 'Guest')}</td>
                  <td class="text-center font-bold font-mono">${escapeHtml(g.room_number || '-')}</td>
                  <td class="font-mono font-bold text-indigo-700">${escapeHtml(g.settle_number || g.folio_number || '-')}</td>
                  <td class="text-[8.5px] text-slate-600">${escapeHtml(g.checkin_date || '-')} - ${escapeHtml(g.checkout_date || '-')}</td>
                  <td class="text-right font-mono font-bold">₹ ${fmt(g.total_bill)}</td>
                  <td class="text-right font-mono text-emerald-700 font-bold">₹ ${fmt(g.paid_amount)}</td>
                  <td class="text-right font-mono font-extrabold ${Number(g.remaining_amount || 0) > 0.01 ? 'text-rose-700' : 'text-emerald-700'}">₹ ${fmt(g.remaining_amount)}</td>
                  <td class="text-center text-[8.5px] font-extrabold uppercase">${g.is_settled ? 'SETTLED' : 'UNSETTLED'}</td>
                </tr>
              `).join('') : `
                <tr><td colspan="9" class="text-center py-3 text-slate-400">No guest settlement records logged for this audit date.</td></tr>
              `}
            </tbody>
            ${guests.length > 0 ? `
              <tfoot>
                <tr class="bg-slate-100 font-extrabold">
                  <td colspan="5" class="text-right uppercase text-[9.5px]">Audit Grand Totals:</td>
                  <td class="text-right font-mono text-[9.5px]">₹ ${fmt(totalBill)}</td>
                  <td class="text-right font-mono text-[9.5px] text-emerald-700">₹ ${fmt(totalPaid)}</td>
                  <td class="text-right font-mono text-[9.5px] ${totalDue > 0.01 ? 'text-rose-700' : 'text-emerald-700'}">₹ ${fmt(totalDue)}</td>
                  <td class="text-center text-[8.5px]">${settledCount}S / ${unsettledCount}U</td>
                </tr>
              </tfoot>
            ` : ''}
          </table>
        </div>

        <!-- 5. Occupied Rooms Sold Detail Table (if any) -->
        ${occupied.length > 0 ? `
          <div class="print-avoid-break mb-3.5">
            <div class="text-xs font-black uppercase text-indigo-950 border-b border-slate-300 pb-1 mb-1.5">
              Occupied Rooms Sold Summary (${occupied.length} Rooms)
            </div>
            <table class="print-table text-[9px] w-full">
              <thead>
                <tr>
                  <th class="w-6 text-center">SL</th>
                  <th>Room</th>
                  <th>Category</th>
                  <th>Floor</th>
                  <th>Primary Guest</th>
                  <th>Settle #</th>
                  <th class="text-center">Housekeeping</th>
                  <th class="text-right">Rate / Day</th>
                </tr>
              </thead>
              <tbody>
                ${occupied.map((rm, idx) => `
                  <tr>
                    <td class="text-center font-mono">${idx + 1}</td>
                    <td class="font-bold font-mono">${escapeHtml(rm.room_number)}</td>
                    <td>${escapeHtml(rm.category || 'Standard')}</td>
                    <td>${escapeHtml(rm.floor || 'Floor 1')}</td>
                    <td>${escapeHtml(rm.guest_name || 'Guest')}</td>
                    <td class="font-mono font-bold text-indigo-700">${escapeHtml(rm.settle_number || rm.folio_number || '-')}</td>
                    <td class="text-center">${escapeHtml(rm.housekeeping_status || 'Clean')}</td>
                    <td class="text-right font-mono font-bold">₹ ${fmt(rm.day_rate)}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : ''}

        <!-- 6. Housekeeping History Logs (Print) -->
        ${hks.length > 0 ? `
          <div class="print-avoid-break mb-3.5">
            <div class="text-xs font-black uppercase text-indigo-950 border-b border-slate-300 pb-1 mb-1.5 flex justify-between">
              <span>Housekeeping Activity &amp; Status Logs (${hks.length} Records)</span>
              <span class="text-[9px] text-slate-500 normal-case">Audited date activity</span>
            </div>
            <table class="print-table text-[8.5px] w-full">
              <thead>
                <tr>
                  <th class="w-6 text-center">SL</th>
                  <th>Time</th>
                  <th>Room</th>
                  <th>Category</th>
                  <th>Floor</th>
                  <th class="text-center">Housekeeping State</th>
                  <th class="text-center">Status</th>
                  <th>Start Time</th>
                  <th>Completion Time</th>
                </tr>
              </thead>
              <tbody>
                ${hks.map((hk, idx) => `
                  <tr>
                    <td class="text-center font-mono">${idx + 1}</td>
                    <td class="font-mono">${escapeHtml(hk.time || '-')}</td>
                    <td class="font-bold font-mono">${escapeHtml(hk.room_number)}</td>
                    <td>${escapeHtml(hk.category || 'Standard')}</td>
                    <td>${escapeHtml(hk.floor || 'Floor 1')}</td>
                    <td class="text-center font-bold">${escapeHtml(hk.housekeeping_status || 'Clean')}</td>
                    <td class="text-center uppercase">${escapeHtml(hk.status || 'Complete')}</td>
                    <td class="font-mono">${escapeHtml(hk.start_time || '-')}</td>
                    <td class="font-mono">${escapeHtml(hk.completion_time || '-')}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : ''}

        <!-- 7. Operational Room Status Logs (Print) -->
        ${ops.length > 0 ? `
          <div class="print-avoid-break mb-3.5">
            <div class="text-xs font-black uppercase text-indigo-950 border-b border-slate-300 pb-1 mb-1.5 flex justify-between">
              <span>Operational Room Status Logs (${ops.length} Records)</span>
              <span class="text-[9px] text-slate-500 normal-case">Service &amp; Maintenance Tracking</span>
            </div>
            <table class="print-table text-[8.5px] w-full">
              <thead>
                <tr>
                  <th class="w-6 text-center">SL</th>
                  <th>Time</th>
                  <th>Room</th>
                  <th>Category</th>
                  <th>Floor</th>
                  <th class="text-center">Operational Status</th>
                  <th class="text-center">Model State</th>
                </tr>
              </thead>
              <tbody>
                ${ops.map((op, idx) => `
                  <tr>
                    <td class="text-center font-mono">${idx + 1}</td>
                    <td class="font-mono">${escapeHtml(op.time || '-')}</td>
                    <td class="font-bold font-mono">${escapeHtml(op.room_number)}</td>
                    <td>${escapeHtml(op.category || 'Standard')}</td>
                    <td>${escapeHtml(op.floor || 'Floor 1')}</td>
                    <td class="text-center font-bold">${escapeHtml(op.operational_status || 'Active')}</td>
                    <td class="text-center uppercase">${escapeHtml(op.room_status || 'Active')}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        ` : ''}

        <!-- 7b. Store Inward Stock Purchases (Print) -->
        ${inward.length > 0 ? `
          <div class="print-avoid-break mb-3.5">
            <div class="text-xs font-black uppercase text-indigo-950 border-b border-slate-300 pb-1 mb-1.5 flex justify-between">
              <span>Store Inward Stock Purchases (${inward.length} Entries)</span>
              <span class="text-[9px] font-bold text-amber-800 normal-case">Total Inward: ₹${totalInward.toFixed(2)}</span>
            </div>
            <table class="print-table text-[8.5px] w-full">
              <thead>
                <tr>
                  <th class="w-6 text-center">SL</th>
                  <th>Time</th>
                  <th>Dish / Item</th>
                  <th>Ref #</th>
                  <th class="text-center">Plates Added</th>
                  <th class="text-right">Unit Cost</th>
                  <th class="text-right">Total Amount</th>
                  <th>Receiver</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                ${inward.map((m, idx) => `
                  <tr>
                    <td class="text-center font-mono">${idx + 1}</td>
                    <td class="font-mono">${escapeHtml(m.time || '-')}</td>
                    <td class="font-bold">${escapeHtml(m.item_name || 'Item')}</td>
                    <td class="font-mono">${escapeHtml(m.reference_no || '-')}</td>
                    <td class="text-center font-bold text-emerald-700">+${m.quantity} Plates</td>
                    <td class="text-right font-mono">${m.unit_cost ? '₹' + Number(m.unit_cost).toFixed(2) : '-'}</td>
                    <td class="text-right font-mono font-bold">₹${Number(m.total_cost || 0).toFixed(2)}</td>
                    <td>${escapeHtml(m.performed_by || '-')}</td>
                    <td class="truncate">${escapeHtml(m.remarks || '-')}</td>
                  </tr>
                `).join('')}
              </tbody>
              <tfoot>
                <tr class="font-bold bg-slate-100">
                  <td colspan="6" class="text-right uppercase">Total Store Inward Purchases:</td>
                  <td class="text-right font-mono text-amber-900">₹${totalInward.toFixed(2)}</td>
                  <td colspan="2"></td>
                </tr>
              </tfoot>
            </table>
          </div>
        ` : ''}

        <!-- 8. Closing Remarks & Sign-off Footer -->
        <div class="print-avoid-break mt-5 pt-2 border-t border-slate-300">
          ${a.remarks ? `
            <div class="text-[9.5px] text-slate-600 mb-4 bg-slate-50 p-2 border border-slate-200 rounded">
              <strong>Closing Remarks:</strong> ${escapeHtml(a.remarks)}
            </div>
          ` : ''}

          <div class="flex justify-between items-end mt-7">
            <div class="text-center w-52">
              <div class="border-b border-slate-900 h-6.5 mb-1.5"></div>
              <div class="text-[10.5px] font-black text-slate-900">Night Auditor / Front Desk</div>
              <div class="text-[9px] text-slate-500">${escapeHtml(auditorName)}</div>
            </div>
            <div class="text-center w-36 text-[9px] text-slate-500">
              Hotel Sagar Sonnet PMS<br>
              Official Verification Stamp
            </div>
            <div class="text-center w-52">
              <div class="border-b border-slate-900 h-6.5 mb-1.5"></div>
              <div class="text-[10.5px] font-black text-slate-900">Duty Manager / General Manager</div>
              <div class="text-[9px] text-slate-500">Signature &amp; Verification</div>
            </div>
          </div>
        </div>

      </div>
    `;

    printArea.style.display = 'block';

    setTimeout(() => {
      window.print();
      setTimeout(() => {
        printArea.style.display = 'none';
      }, 500);
    }, 250);
  }

  function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }
</script>
@endpush
