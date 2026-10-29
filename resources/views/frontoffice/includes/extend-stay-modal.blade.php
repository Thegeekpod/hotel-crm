<!-- ========================================================================= -->
<!-- EXTEND GUEST STAY MODAL (SHARED FRONT OFFICE COMPONENT)                   -->
<!-- Real-Time Conflict Detection, Alternative Room Referral & Tariff Updates  -->
<!-- ========================================================================= -->

<div class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" id="extend-stay-modal">
  <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-150">
    
    <!-- Modal Header -->
    <div class="bg-linear-to-r from-indigo-600 to-indigo-700 text-white px-6 py-4 flex justify-between items-center shrink-0">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg text-white shadow-xs">
          <i class="fa-solid fa-calendar-plus text-amber-300"></i>
        </div>
        <div>
          <h4 class="text-base font-black text-white tracking-tight">Extend Guest Stay</h4>
          <p class="text-xs text-indigo-100 font-medium mt-0.5">Booking Dossier &amp; Real-Time Room Availability Check</p>
        </div>
      </div>
      <button type="button" onclick="closeExtendStayModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl transition-all cursor-pointer leading-none">&times;</button>
    </div>

    <!-- Modal Body (Scrollable) -->
    <div class="p-6 overflow-y-auto flex-1 flex flex-col gap-4 bg-slate-50">
      
      <!-- Card 1: Current Booking & Stay Summary -->
      <div class="bg-white border border-slate-200/80 rounded-xl p-4.5 shadow-xs">
        <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-2.5">
          <div>
            <div class="text-sm font-extrabold text-slate-800 flex items-center gap-2">
              <span id="esm-guest-name">Loading...</span>
              <span id="esm-status-badge" class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">Active</span>
            </div>
            <div class="text-xs text-slate-500 mt-1 flex items-center gap-2">
              <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-phone text-emerald-600 text-[10px]"></i> <span id="esm-guest-phone">-</span></span>
              <span class="text-slate-300">•</span>
              <span class="inline-flex items-center gap-1.5 font-mono font-bold text-indigo-600"><i class="fa-solid fa-receipt text-[10px]"></i> <span id="esm-reserve-id">-</span></span>
            </div>
          </div>
          <div class="text-right">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Current Stay Duration</div>
            <div class="text-xs font-black text-slate-800 mt-0.5">
              <span id="esm-current-nights" class="text-indigo-600">1</span> Night(s)
            </div>
          </div>
        </div>

        <!-- Room(s) & Stay Dates Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
              <i class="fa-solid fa-door-open text-indigo-500"></i> Allocated Room(s)
            </div>
            <div id="esm-rooms-list" class="font-extrabold text-slate-800 text-sm">
              -
            </div>
            <div id="esm-rooms-tariff" class="text-xs text-indigo-600 font-bold mt-0.5">
              ₹ 0.00 / night
            </div>
          </div>

          <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3">
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
              <i class="fa-regular fa-calendar-days text-indigo-500"></i> Current Stay Dates
            </div>
            <div class="font-extrabold text-slate-800 text-xs flex items-center gap-1.5">
              <span id="esm-current-checkin">-</span>
              <span class="text-slate-400">&rarr;</span>
              <span id="esm-current-checkout" class="text-rose-600 underline">-</span>
            </div>
            <div class="text-[10px] font-semibold text-slate-400 mt-0.5">
              Standard Checkout: 11:00 AM
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2: Date Selector & Quick Pills -->
      <div class="bg-white border border-slate-200/80 rounded-xl p-4.5 shadow-xs">
        <label class="block text-xs font-bold text-slate-700 mb-2">
          <i class="fa-solid fa-calendar-check text-indigo-600 mr-1"></i> Select New Check-Out Date:
        </label>
        
        <div class="flex gap-3 items-center flex-wrap">
          <input type="date" 
                 id="esm-new-checkout-input" 
                 class="h-10 text-xs font-bold px-3.5 border border-indigo-400 rounded-xl text-slate-800 bg-white flex-1 min-w-[200px] focus:outline-none focus:ring-2 focus:ring-indigo-500/20" 
                 onchange="onExtendStayDateChanged(this.value)">

          <!-- Quick Extension Pills -->
          <div class="flex gap-1.5 items-center flex-wrap">
            <button type="button" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 text-xs font-bold text-slate-700 cursor-pointer transition-all" onclick="quickSetExtendNights(1)">
              +1 Night
            </button>
            <button type="button" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 text-xs font-bold text-slate-700 cursor-pointer transition-all" onclick="quickSetExtendNights(2)">
              +2 Nights
            </button>
            <button type="button" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 text-xs font-bold text-slate-700 cursor-pointer transition-all" onclick="quickSetExtendNights(3)">
              +3 Nights
            </button>
            <button type="button" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 hover:border-slate-300 text-xs font-bold text-slate-700 cursor-pointer transition-all" onclick="quickSetExtendNights(5)">
              +5 Nights
            </button>
          </div>
        </div>

        <div class="text-[11px] text-slate-400 mt-1.5">
          Choose the extended date to check live room availability and financial adjustments.
        </div>
      </div>

      <!-- Card 3: Live Availability & Conflict Box -->
      <div id="esm-availability-container" class="hidden">
        
        <!-- State 1: Available (Green) -->
        <div id="esm-available-box" class="hidden bg-emerald-50/80 border border-emerald-300 rounded-xl p-4 shadow-xs">
          <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-base shrink-0">
              <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="flex-1">
              <div class="text-xs font-black text-emerald-900 flex items-center gap-2">
                <span>Room Available For Stay Extension!</span>
                <span id="esm-avail-nights-badge" class="bg-emerald-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full">+1 Night</span>
              </div>
              <div id="esm-available-msg" class="text-xs text-emerald-700 mt-1 leading-relaxed">
                Room is completely available for the extended stay dates.
              </div>

              <!-- Financial Breakdown Preview -->
              <div class="mt-3 bg-white border border-emerald-200 rounded-xl p-3">
                <div class="text-[10px] font-black text-emerald-700 uppercase tracking-wider mb-1.5">
                  Billing Adjustment Preview:
                </div>
                <div class="grid grid-cols-3 gap-3 text-xs">
                  <div>
                    <span class="text-slate-400 text-[10px] block">Additional Tariff:</span>
                    <div id="esm-prev-add-tariff" class="font-mono font-black text-emerald-600 text-xs sm:text-sm">+ ₹ 0.00</div>
                  </div>
                  <div>
                    <span class="text-slate-400 text-[10px] block">New Total Gross:</span>
                    <div id="esm-prev-new-gross" class="font-mono font-black text-slate-800 text-xs sm:text-sm">₹ 0.00</div>
                  </div>
                  <div>
                    <span class="text-slate-400 text-[10px] block">Estimated Balance Due:</span>
                    <div id="esm-prev-new-balance" class="font-mono font-black text-rose-600 text-xs sm:text-sm">₹ 0.00</div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- State 2: Conflict Detected (Red/Orange) -->
        <div id="esm-conflict-box" class="hidden bg-rose-50/70 border border-rose-300 rounded-xl overflow-hidden shadow-xs">
          <div class="bg-linear-to-r from-rose-500 to-rose-600 text-white px-4 py-2.5 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <i class="fa-solid fa-triangle-exclamation text-sm"></i>
              <span class="text-xs font-black uppercase tracking-wider">Current Room Conflict / Already Booked!</span>
            </div>
            <span class="bg-white/20 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase">Not Available</span>
          </div>

          <div class="p-3.5 text-rose-800 text-xs leading-relaxed">
            <div id="esm-conflict-msg" class="font-bold mb-1">
              Room is already booked for these dates.
            </div>
            <div class="text-[11px] text-rose-700">
              The current room cannot be extended further. Please select one of the available alternative rooms below for the extended stay:
            </div>
          </div>

          <!-- Alternative Rooms List -->
          <div class="bg-white border-t border-rose-200 p-3.5">
            <div class="text-[11px] font-black text-slate-800 uppercase tracking-wider flex items-center justify-between mb-2">
              <div class="flex items-center gap-1.5">
                <i class="fa-solid fa-wand-magic-sparkles text-indigo-600"></i>
                <span>Available Alternative Rooms for this Extended Period:</span>
              </div>
              <span id="esm-alt-count-badge" class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                0 Available
              </span>
            </div>

            <div id="esm-alt-rooms-list" class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto pr-1">
              <!-- Injected dynamically -->
            </div>
          </div>

        </div>

        <!-- Selected Alternative Room Display -->
        <div id="esm-selected-alt-display" class="hidden mt-2.5 bg-emerald-50 border border-emerald-300 rounded-xl p-3 flex justify-between items-center">
          <div class="flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <div>
              <div class="text-xs font-extrabold text-emerald-800">
                Alternative Room Selected: <span id="esm-selected-alt-text">Room #-</span>
              </div>
              <div class="text-[10px] font-medium text-emerald-700" id="esm-selected-alt-sub">
                Guest will shift to this room for the extended period.
              </div>
            </div>
          </div>
          <button type="button" onclick="clearSelectedAltRoom()" class="px-2.5 py-1 text-[10px] font-bold rounded-lg border border-emerald-300 bg-white text-emerald-700 hover:bg-emerald-50 cursor-pointer transition-all">
            Change
          </button>
        </div>

      </div>

      <!-- Remarks / Extension Reason -->
      <div class="bg-white border border-slate-200/80 rounded-xl p-3.5 shadow-xs">
        <label class="block text-xs font-bold text-slate-700 mb-1.5">
          Extension Remarks / Special Notes (Optional):
        </label>
        <input type="text" 
               id="esm-remarks-input" 
               class="w-full px-3 py-2 text-xs font-medium text-slate-700 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 placeholder:text-slate-400" 
               placeholder="e.g. Guest requested 2 additional nights stay...">
      </div>

    </div>

    <!-- Modal Footer -->
    <div class="px-6 py-4 bg-white border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
      <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
        <i class="fa-solid fa-shield-halved text-indigo-500"></i> Master Ledger &amp; Housekeeping updated automatically.
      </div>

      <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
        <button type="button" onclick="closeExtendStayModal()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs font-bold text-slate-600 transition-all cursor-pointer">
          Cancel
        </button>
        <button type="button" 
                id="esm-btn-confirm" 
                onclick="submitExtendStay()" 
                class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed" 
                disabled>
          <i class="fa-solid fa-check"></i> Confirm &amp; Extend Stay
        </button>
      </div>
    </div>

  </div>
</div>

<script>
  let activeExtendStayData = null;
  let activeExtendStayAvailability = null;
  let selectedAlternativeRoomId = null;
  let extendStayAbortController = null;

  function openExtendStayModal(bookingId) {
    if (!bookingId) return;

    const modal = document.getElementById('extend-stay-modal');
    if (!modal) return;

    // Reset fields
    activeExtendStayData = null;
    activeExtendStayAvailability = null;
    selectedAlternativeRoomId = null;

    document.getElementById('esm-guest-name').textContent = 'Loading guest dossier...';
    document.getElementById('esm-guest-phone').textContent = '-';
    document.getElementById('esm-reserve-id').textContent = '-';
    document.getElementById('esm-current-nights').textContent = '-';
    document.getElementById('esm-rooms-list').textContent = '-';
    document.getElementById('esm-rooms-tariff').textContent = '₹ 0.00 / night';
    document.getElementById('esm-current-checkin').textContent = '-';
    document.getElementById('esm-current-checkout').textContent = '-';
    document.getElementById('esm-new-checkout-input').value = '';
    document.getElementById('esm-remarks-input').value = '';

    const availContainer = document.getElementById('esm-availability-container');
    if (availContainer) availContainer.classList.add('hidden');
    const selAltDisplay = document.getElementById('esm-selected-alt-display');
    if (selAltDisplay) selAltDisplay.classList.add('hidden');

    const confirmBtn = document.getElementById('esm-btn-confirm');
    if (confirmBtn) {
      confirmBtn.disabled = true;
      confirmBtn.innerHTML = '<i class="fa-solid fa-check"></i> Confirm &amp; Extend Stay';
    }

    modal.classList.remove('hidden');

    // Fetch initial dossier
    const url = `{{ route('frontoffice.extend-stay.info') }}?booking_id=${bookingId}`;
    fetch(url, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
          PmsAlert.toast(data.message || 'Error loading booking info', 'error');
        } else {
          alert(data.message || 'Error loading booking info');
        }
        closeExtendStayModal();
        return;
      }

      activeExtendStayData = data;

      document.getElementById('esm-guest-name').textContent = data.guest_name;
      document.getElementById('esm-guest-phone').textContent = data.mobile || 'N/A';
      document.getElementById('esm-reserve-id').textContent = data.reserve_id;
      document.getElementById('esm-current-nights').textContent = data.total_nights;
      document.getElementById('esm-current-checkin').textContent = data.checkin_date_fmt;
      document.getElementById('esm-current-checkout').textContent = data.checkout_date_fmt;

      const roomNumbers = (data.rooms || []).map(r => `Room #${r.room_number} (${r.category})`).join(', ');
      document.getElementById('esm-rooms-list').textContent = roomNumbers || 'Standard';
      document.getElementById('esm-rooms-tariff').textContent = `₹ ${Number(data.active_tariff_per_night || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})} / night`;

      // Set min date and default +1 night
      const dateInp = document.getElementById('esm-new-checkout-input');
      if (dateInp) {
        dateInp.min = data.min_checkout_date;
        dateInp.value = data.min_checkout_date;
        onExtendStayDateChanged(data.min_checkout_date);
      }
    })
    .catch(err => {
      console.error('Error fetching extend stay info:', err);
      closeExtendStayModal();
    });
  }

  function closeExtendStayModal() {
    const modal = document.getElementById('extend-stay-modal');
    if (modal) modal.classList.add('hidden');
    if (extendStayAbortController) {
      extendStayAbortController.abort();
    }
  }

  function quickSetExtendNights(extraNights) {
    if (!activeExtendStayData || !activeExtendStayData.checkout_date) return;
    
    const curCheckout = new Date(activeExtendStayData.checkout_date + 'T00:00:00');
    curCheckout.setDate(curCheckout.getDate() + extraNights);

    const y = curCheckout.getFullYear();
    const m = String(curCheckout.getMonth() + 1).padStart(2, '0');
    const d = String(curCheckout.getDate()).padStart(2, '0');
    const newDateStr = `${y}-${m}-${d}`;

    const dateInp = document.getElementById('esm-new-checkout-input');
    if (dateInp) {
      dateInp.value = newDateStr;
      onExtendStayDateChanged(newDateStr);
    }
  }

  function onExtendStayDateChanged(newDateVal) {
    if (!activeExtendStayData || !newDateVal) return;

    if (newDateVal <= activeExtendStayData.checkout_date) {
      if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
        PmsAlert.toast(`New check-out date must be after ${activeExtendStayData.checkout_date_fmt}.`, 'warning');
      }
      return;
    }

    selectedAlternativeRoomId = null;
    const selAltDisplay = document.getElementById('esm-selected-alt-display');
    if (selAltDisplay) selAltDisplay.classList.add('hidden');

    if (extendStayAbortController) {
      extendStayAbortController.abort();
    }
    extendStayAbortController = new AbortController();

    const availContainer = document.getElementById('esm-availability-container');
    const availBox = document.getElementById('esm-available-box');
    const confBox = document.getElementById('esm-conflict-box');
    const confirmBtn = document.getElementById('esm-btn-confirm');

    if (confirmBtn) confirmBtn.disabled = true;

    const url = `{{ route('frontoffice.extend-stay.check') }}?booking_id=${activeExtendStayData.booking_id}&new_checkout_date=${encodeURIComponent(newDateVal)}`;

    fetch(url, {
      signal: extendStayAbortController.signal,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (!data) return;
      activeExtendStayAvailability = data;

      if (availContainer) availContainer.classList.remove('hidden');

      if (data.available) {
        // Available!
        if (availBox) availBox.classList.remove('hidden');
        if (confBox) confBox.classList.add('hidden');

        const nightBadge = document.getElementById('esm-avail-nights-badge');
        if (nightBadge) nightBadge.textContent = `+${data.additional_nights} Night${data.additional_nights > 1 ? 's' : ''}`;

        const msgEl = document.getElementById('esm-available-msg');
        if (msgEl) msgEl.textContent = data.message;

        const addTariffEl = document.getElementById('esm-prev-add-tariff');
        if (addTariffEl) addTariffEl.textContent = `+ ₹ ${Number(data.additional_tariff || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

        const newGrossEl = document.getElementById('esm-prev-new-gross');
        if (newGrossEl) newGrossEl.textContent = `₹ ${Number(data.estimated_gross || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

        const newBalEl = document.getElementById('esm-prev-new-balance');
        if (newBalEl) newBalEl.textContent = `₹ ${Number(data.estimated_balance || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}`;

        if (confirmBtn) confirmBtn.disabled = false;

      } else {
        // Conflict!
        if (availBox) availBox.classList.add('hidden');
        if (confBox) confBox.classList.remove('hidden');

        const confMsgEl = document.getElementById('esm-conflict-msg');
        if (confMsgEl) confMsgEl.textContent = data.message;

        const altCountEl = document.getElementById('esm-alt-count-badge');
        if (altCountEl) altCountEl.textContent = `${data.available_count || 0} Available`;

        const altListEl = document.getElementById('esm-alt-rooms-list');
        if (altListEl) {
          if (data.available_rooms && data.available_rooms.length > 0) {
            altListEl.innerHTML = data.available_rooms.map(r => `
              <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl p-2.5">
                <div>
                  <div class="font-extrabold text-slate-800 text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-door-open text-emerald-600 text-[11px]"></i>
                    <span>Room #${r.room_number}</span>
                    <span class="text-[9px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 px-1.5 py-0.5 rounded">${r.category}</span>
                  </div>
                  <div class="text-[10px] text-slate-500 mt-0.5">
                    Floor ${r.floor} • ₹${Number(r.rate).toLocaleString()}/night
                  </div>
                </div>
                <button type="button" 
                        class="h-7 px-2.5 text-[10px] font-extrabold rounded-lg whitespace-nowrap cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white transition-all shadow-xs"
                        onclick="selectAlternativeExtendRoom(${r.id}, '${r.room_number}', '${r.category}', ${r.rate})">
                  <i class="fa-solid fa-check"></i> Select
                </button>
              </div>
            `).join('');
          } else {
            altListEl.innerHTML = `
              <div class="col-span-full p-4 text-xs font-bold text-slate-400 text-center bg-white rounded-xl border border-dashed border-slate-200">
                <i class="fa-solid fa-circle-exclamation mr-1"></i> No alternative rooms are completely free for this extended window.
              </div>
            `;
          }
        }

        if (confirmBtn) confirmBtn.disabled = true; // Wait for alternative room selection
      }
    })
    .catch(err => {
      if (err.name !== 'AbortError') {
        console.error('Error checking room availability:', err);
      }
    });
  }

  function selectAlternativeExtendRoom(roomId, roomNo, category, rate) {
    selectedAlternativeRoomId = roomId;

    const selDisplay = document.getElementById('esm-selected-alt-display');
    const selText = document.getElementById('esm-selected-alt-text');
    const selSub = document.getElementById('esm-selected-alt-sub');
    const confirmBtn = document.getElementById('esm-btn-confirm');

    if (selText) selText.textContent = `Room #${roomNo} (${category} • ₹${Number(rate).toLocaleString()}/night)`;
    if (selSub) selSub.textContent = `Guest will shift to Room #${roomNo} for the extended period.`;
    if (selDisplay) selDisplay.classList.remove('hidden');

    if (confirmBtn) confirmBtn.disabled = false;

    if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
      PmsAlert.toast(`Alternative Room #${roomNo} selected for the extended stay!`, 'success');
    }
  }

  function clearSelectedAltRoom() {
    selectedAlternativeRoomId = null;
    const selDisplay = document.getElementById('esm-selected-alt-display');
    if (selDisplay) selDisplay.classList.add('hidden');

    const confirmBtn = document.getElementById('esm-btn-confirm');
    if (confirmBtn && activeExtendStayAvailability && !activeExtendStayAvailability.available) {
      confirmBtn.disabled = true;
    }
  }

  function submitExtendStay() {
    if (!activeExtendStayData) return;

    const newDate = document.getElementById('esm-new-checkout-input')?.value;
    if (!newDate) {
      if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
        PmsAlert.toast('Please select a valid new check-out date.', 'warning');
      } else {
        alert('Please select a valid new check-out date.');
      }
      return;
    }

    const remarks = document.getElementById('esm-remarks-input')?.value || '';
    const confirmBtn = document.getElementById('esm-btn-confirm');
    const originalBtnText = confirmBtn ? confirmBtn.innerHTML : '';

    if (confirmBtn) {
      confirmBtn.disabled = true;
      confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Extension...';
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`{{ route('frontoffice.extend-stay.process') }}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        booking_id: activeExtendStayData.booking_id,
        new_checkout_date: newDate,
        extension_room_id: selectedAlternativeRoomId,
        remarks: remarks
      })
    })
    .then(res => res.json())
    .then(data => {
      if (confirmBtn) {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalBtnText;
      }

      if (data.success) {
        closeExtendStayModal();

        if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
          PmsAlert.toast(data.message, 'success');
        } else {
          alert(data.message);
        }

        // Refresh active views / modals
        if (typeof openFolioModalById === 'function' && document.getElementById('folio-modal')?.classList.contains('hidden') === false) {
          openFolioModalById(activeExtendStayData.master_booking_id || activeExtendStayData.booking_id);
        }

        setTimeout(() => {
          window.location.reload();
        }, 800);

      } else {
        if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
          PmsAlert.toast(data.message || data.error || 'Failed to extend stay', 'error');
        } else {
          alert(data.message || data.error || 'Failed to extend stay');
        }
      }
    })
    .catch(err => {
      if (confirmBtn) {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalBtnText;
      }
      console.error('Error extending stay:', err);
      if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
        PmsAlert.toast('An unexpected error occurred while extending stay.', 'error');
      } else {
        alert('An unexpected error occurred.');
      }
    });
  }

  // Explicit window bindings
  window.openExtendStayModal = openExtendStayModal;
  window.closeExtendStayModal = closeExtendStayModal;
  window.quickSetExtendNights = quickSetExtendNights;
  window.onExtendStayDateChanged = onExtendStayDateChanged;
  window.selectAlternativeExtendRoom = selectAlternativeExtendRoom;
  window.clearSelectedAltRoom = clearSelectedAltRoom;
  window.submitExtendStay = submitExtendStay;
</script>
