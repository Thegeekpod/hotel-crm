<!-- ========================================================================= -->
<!-- EXTEND GUEST STAY MODAL (SHARED FRONT OFFICE COMPONENT)                   -->
<!-- Real-Time Conflict Detection, Alternative Room Referral & Tariff Updates  -->
<!-- ========================================================================= -->

<div class="modal-backdrop" id="extend-stay-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 20px;">
  <div class="modal-window" style="width: 720px; max-width: 95vw; max-height: 90vh; display: flex; flex-direction: column; border-radius: 18px; overflow: hidden; box-shadow: 0 25px 65px -12px rgba(0, 0, 0, 0.5); border: none; background: #ffffff; animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
    
    <!-- Modal Header -->
    <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; color: #ffffff; flex-shrink: 0;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 18px; color: #ffffff; box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.25);">
          <i class="fa-solid fa-calendar-plus"></i>
        </div>
        <div>
          <h4 style="margin: 0; font-size: 16px; font-weight: 800; color: #ffffff; letter-spacing: -0.2px;">Extend Guest Stay</h4>
          <div style="font-size: 11px; opacity: 0.9; color: #ede9fe; margin-top: 1px;">
            Booking Dossier &amp; Real-Time Room Availability Check
          </div>
        </div>
      </div>
      <button type="button" onclick="closeExtendStayModal()" style="background: rgba(255, 255, 255, 0.2); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease;">
        &times;
      </button>
    </div>

    <!-- Modal Body (Scrollable) -->
    <div style="padding: 20px 24px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 16px; background: #f8fafc;">
      
      <!-- Card 1: Current Booking & Stay Summary -->
      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
          <div>
            <div style="font-size: 14px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
              <span id="esm-guest-name">Loading...</span>
              <span id="esm-status-badge" style="font-size: 9px; font-weight: 800; padding: 2px 8px; border-radius: 12px; background: #e0e7ff; color: #4338ca;">Active</span>
            </div>
            <div style="font-size: 11px; color: #64748b; margin-top: 2px; display: flex; align-items: center; gap: 8px;">
              <span><i class="fa-solid fa-phone" style="color: #10b981; font-size: 10px;"></i> <span id="esm-guest-phone">-</span></span>
              <span>•</span>
              <span style="font-family: var(--font-mono, monospace); font-weight: 700; color: #4f46e5;"><i class="fa-solid fa-receipt" style="font-size: 10px;"></i> <span id="esm-reserve-id">-</span></span>
            </div>
          </div>
          <div style="text-align: right;">
            <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase;">Current Stay Duration</div>
            <div style="font-size: 12px; font-weight: 800; color: #1e293b; margin-top: 2px;">
              <span id="esm-current-nights" style="color: #4f46e5;">1</span> Night(s)
            </div>
          </div>
        </div>

        <!-- Room(s) & Stay Dates Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 12px;">
          <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
            <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">
              <i class="fa-solid fa-door-open" style="color: #6366f1;"></i> Allocated Room(s)
            </div>
            <div id="esm-rooms-list" style="font-weight: 800; color: #0f172a; font-size: 13px;">
              -
            </div>
            <div id="esm-rooms-tariff" style="font-size: 11px; color: #4338ca; font-weight: 700; margin-top: 2px;">
              ₹ 0.00 / night
            </div>
          </div>

          <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
            <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">
              <i class="fa-regular fa-calendar-days" style="color: #6366f1;"></i> Current Stay Dates
            </div>
            <div style="font-weight: 800; color: #0f172a; font-size: 12px; display: flex; align-items: center; gap: 6px;">
              <span id="esm-current-checkin">-</span>
              <span style="color: #94a3b8;">&rarr;</span>
              <span id="esm-current-checkout" style="color: #b91c1c; text-decoration: underline;">-</span>
            </div>
            <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
              Standard Checkout: 11:00 AM
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2: Date Selector & Quick Pills -->
      <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 16px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <label style="display: block; font-size: 12px; font-weight: 800; color: #1e293b; margin-bottom: 8px;">
          <i class="fa-solid fa-calendar-check" style="color: #4f46e5; margin-right: 4px;"></i> Select New Check-Out Date:
        </label>
        
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
          <input type="date" 
                 id="esm-new-checkout-input" 
                 class="pms-filter-date" 
                 style="height: 42px; font-size: 14px; font-weight: 800; padding: 0 14px; border: 1.5px solid #6366f1; border-radius: 10px; color: #0f172a; background: #ffffff; flex: 1; min-width: 200px;" 
                 onchange="onExtendStayDateChanged(this.value)">

          <!-- Quick Extension Pills -->
          <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
            <button type="button" class="btn-quick-pill" onclick="quickSetExtendNights(1)" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 11px; font-weight: 700; color: #334155; cursor: pointer; transition: all 0.15s;">
              +1 Night
            </button>
            <button type="button" class="btn-quick-pill" onclick="quickSetExtendNights(2)" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 11px; font-weight: 700; color: #334155; cursor: pointer; transition: all 0.15s;">
              +2 Nights
            </button>
            <button type="button" class="btn-quick-pill" onclick="quickSetExtendNights(3)" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 11px; font-weight: 700; color: #334155; cursor: pointer; transition: all 0.15s;">
              +3 Nights
            </button>
            <button type="button" class="btn-quick-pill" onclick="quickSetExtendNights(5)" style="padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; font-size: 11px; font-weight: 700; color: #334155; cursor: pointer; transition: all 0.15s;">
              +5 Nights
            </button>
          </div>
        </div>

        <div style="font-size: 11px; color: #64748b; margin-top: 6px;">
          Choose the extended date to check live room availability and financial adjustments.
        </div>
      </div>

      <!-- Card 3: Live Availability & Conflict Box -->
      <div id="esm-availability-container" style="display: none;">
        
        <!-- State 1: Available (Green) -->
        <div id="esm-available-box" style="display: none; background: #ecfdf5; border: 1.5px solid #10b981; border-radius: 12px; padding: 14px 16px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);">
          <div style="display: flex; align-items: flex-start; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 50%; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
              <i class="fa-solid fa-circle-check"></i>
            </div>
            <div style="flex: 1;">
              <div style="font-size: 13px; font-weight: 800; color: #065f46; display: flex; align-items: center; gap: 6px;">
                <span>Room Available For Stay Extension!</span>
                <span id="esm-avail-nights-badge" style="background: #059669; color: #ffffff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 10px;">+1 Night</span>
              </div>
              <div id="esm-available-msg" style="font-size: 11px; color: #047857; margin-top: 3px; line-height: 1.4;">
                Room is completely available for the extended stay dates.
              </div>

              <!-- Financial Breakdown Preview -->
              <div style="margin-top: 12px; background: #ffffff; border: 1px solid #a7f3d0; border-radius: 8px; padding: 10px 14px;">
                <div style="font-size: 10px; font-weight: 800; color: #047857; text-transform: uppercase; margin-bottom: 6px;">
                  Billing Adjustment Preview:
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; font-size: 11px;">
                  <div>
                    <span style="color: #64748b;">Additional Tariff:</span>
                    <div id="esm-prev-add-tariff" style="font-family: var(--font-mono, monospace); font-weight: 800; color: #059669; font-size: 13px;">+ ₹ 0.00</div>
                  </div>
                  <div>
                    <span style="color: #64748b;">New Total Gross:</span>
                    <div id="esm-prev-new-gross" style="font-family: var(--font-mono, monospace); font-weight: 800; color: #1e293b; font-size: 13px;">₹ 0.00</div>
                  </div>
                  <div>
                    <span style="color: #64748b;">Estimated Balance Due:</span>
                    <div id="esm-prev-new-balance" style="font-family: var(--font-mono, monospace); font-weight: 800; color: #dc2626; font-size: 13px;">₹ 0.00</div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>

        <!-- State 2: Conflict Detected (Red/Orange) -->
        <div id="esm-conflict-box" style="display: none; background: #fff5f5; border: 1.5px solid #f87171; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.08);">
          <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #ffffff; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-triangle-exclamation" style="font-size: 15px;"></i>
              <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">Current Room Conflict / Already Booked!</span>
            </div>
            <span style="background: rgba(255,255,255,0.25); font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Not Available</span>
          </div>

          <div style="padding: 12px 14px; color: #991b1b; font-size: 12px; line-height: 1.5;">
            <div id="esm-conflict-msg" style="font-weight: 700; margin-bottom: 4px;">
              Room is already booked for these dates.
            </div>
            <div style="font-size: 11px; color: #b91c1c; opacity: 0.9;">
              The current room cannot be extended further. Please select one of the available alternative rooms below for the extended stay:
            </div>
          </div>

          <!-- Alternative Rooms List -->
          <div style="background: #ffffff; border-top: 1px solid #fecaca; padding: 12px 14px;">
            <div style="font-size: 11px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
              <div style="display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-wand-magic-sparkles" style="color: #6366f1;"></i>
                <span>Available Alternative Rooms for this Extended Period:</span>
              </div>
              <span id="esm-alt-count-badge" style="font-size: 10px; font-weight: 700; color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 1px 8px; border-radius: 12px;">
                0 Available
              </span>
            </div>

            <div id="esm-alt-rooms-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 8px; max-height: 180px; overflow-y: auto; padding-right: 2px;">
              <!-- Injected dynamically -->
            </div>
          </div>

        </div>

        <!-- Selected Alternative Room Display -->
        <div id="esm-selected-alt-display" style="display: none; margin-top: 10px; background: #f0fdf4; border: 1.5px solid #22c55e; border-radius: 10px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;">
          <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="color: #16a34a; font-size: 16px;"></i>
            <div>
              <div style="font-size: 12px; font-weight: 800; color: #15803d;">
                Alternative Room Selected: <span id="esm-selected-alt-text">Room #-</span>
              </div>
              <div style="font-size: 10px; color: #166534;" id="esm-selected-alt-sub">
                Guest will shift to this room for the extended period.
              </div>
            </div>
          </div>
          <button type="button" onclick="clearSelectedAltRoom()" style="padding: 4px 8px; font-size: 10px; font-weight: 700; border-radius: 6px; border: 1px solid #bbf7d0; background: #ffffff; color: #15803d; cursor: pointer;">
            Change
          </button>
        </div>

      </div>

      <!-- Remarks / Extension Reason -->
      <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px;">
        <label style="display: block; font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 4px;">
          Extension Remarks / Special Notes (Optional):
        </label>
        <input type="text" 
               id="esm-remarks-input" 
               class="pms-input" 
               placeholder="e.g. Guest requested 2 additional nights stay..." 
               style="height: 36px; font-size: 12px; width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 10px;">
      </div>

    </div>

    <!-- Modal Footer -->
    <div style="padding: 14px 24px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
      <div style="font-size: 11px; color: #64748b;">
        <i class="fa-solid fa-shield-halved" style="color: #6366f1;"></i> Master Ledger &amp; Housekeeping updated automatically.
      </div>

      <div style="display: flex; gap: 10px; align-items: center;">
        <button type="button" onclick="closeExtendStayModal()" style="padding: 8px 18px; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-size: 12px; font-weight: 700; cursor: pointer; transition: all 0.15s;">
          Cancel
        </button>
        <button type="button" 
                id="esm-btn-confirm" 
                onclick="submitExtendStay()" 
                style="padding: 8px 22px; border-radius: 8px; border: none; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; font-size: 12px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3); transition: all 0.15s;" 
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
    if (availContainer) availContainer.style.display = 'none';
    const selAltDisplay = document.getElementById('esm-selected-alt-display');
    if (selAltDisplay) selAltDisplay.style.display = 'none';

    const confirmBtn = document.getElementById('esm-btn-confirm');
    if (confirmBtn) {
      confirmBtn.disabled = true;
      confirmBtn.innerHTML = '<i class="fa-solid fa-check"></i> Confirm &amp; Extend Stay';
    }

    modal.style.display = 'flex';

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
    if (modal) modal.style.display = 'none';
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
    if (selAltDisplay) selAltDisplay.style.display = 'none';

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

      if (availContainer) availContainer.style.display = 'block';

      if (data.available) {
        // Available!
        if (availBox) availBox.style.display = 'block';
        if (confBox) confBox.style.display = 'none';

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
        if (availBox) availBox.style.display = 'none';
        if (confBox) confBox.style.display = 'block';

        const confMsgEl = document.getElementById('esm-conflict-msg');
        if (confMsgEl) confMsgEl.textContent = data.message;

        const altCountEl = document.getElementById('esm-alt-count-badge');
        if (altCountEl) altCountEl.textContent = `${data.available_count || 0} Available`;

        const altListEl = document.getElementById('esm-alt-rooms-list');
        if (altListEl) {
          if (data.available_rooms && data.available_rooms.length > 0) {
            altListEl.innerHTML = data.available_rooms.map(r => `
              <div style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px 10px;">
                <div>
                  <div style="font-weight: 800; color: #0f172a; font-size: 12px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-door-open" style="color: #10b981; font-size: 11px;"></i>
                    <span>Room #${r.room_number}</span>
                    <span style="font-size: 9px; font-weight: 700; background: #e0e7ff; color: #4338ca; padding: 1px 6px; border-radius: 4px;">${r.category}</span>
                  </div>
                  <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
                    Floor ${r.floor} • ₹${Number(r.rate).toLocaleString()}/night
                  </div>
                </div>
                <button type="button" 
                        class="btn-ui-primary" 
                        style="height: 28px; padding: 0 10px; font-size: 10px; font-weight: 800; border-radius: 6px; white-space: nowrap; cursor: pointer; background: #4f46e5; color: #ffffff; border: none;"
                        onclick="selectAlternativeExtendRoom(${r.id}, '${r.room_number}', '${r.category}', ${r.rate})">
                  <i class="fa-solid fa-check"></i> Select
                </button>
              </div>
            `).join('');
          } else {
            altListEl.innerHTML = `
              <div style="grid-column: 1 / -1; padding: 14px; font-size: 11px; font-weight: 700; color: #94a3b8; text-align: center; background: #ffffff; border-radius: 8px; border: 1px dashed #cbd5e1;">
                <i class="fa-solid fa-circle-exclamation"></i> No alternative rooms are completely free for this extended window.
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
    if (selDisplay) selDisplay.style.display = 'flex';

    if (confirmBtn) confirmBtn.disabled = false;

    if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
      PmsAlert.toast(`Alternative Room #${roomNo} selected for the extended stay!`, 'success');
    }
  }

  function clearSelectedAltRoom() {
    selectedAlternativeRoomId = null;
    const selDisplay = document.getElementById('esm-selected-alt-display');
    if (selDisplay) selDisplay.style.display = 'none';

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
        if (typeof openFolioModalById === 'function' && document.getElementById('folio-modal')?.style.display !== 'none') {
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
