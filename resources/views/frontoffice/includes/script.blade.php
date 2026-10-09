<script>
  let currentStatusFilter = 'ALL';

  function applyFilters() {
    const typeElem = document.getElementById('filter-type');
    const floorElem = document.getElementById('filter-floor');
    const searchElem = document.getElementById('filter-search');

    const type = typeElem ? typeElem.value : 'ALL';
    const floor = floorElem ? floorElem.value : 'ALL';
    const search = searchElem ? searchElem.value.trim().toLowerCase() : '';

    document.querySelectorAll('.rack-card').forEach(card => {
      const cType = card.getAttribute('data-type');
      const cFloor = card.getAttribute('data-floor');
      const cRoom = card.getAttribute('data-room').toLowerCase();
      const cStatus = card.getAttribute('data-status');
      const gNameElem = card.querySelector('.rack-guest-name');
      const gName = gNameElem ? gNameElem.textContent.toLowerCase() : '';

      const mType = (type === 'ALL' || cType === type);
      const mFloor = (floor === 'ALL' || cFloor === floor);
      const mSearch = (!search || cRoom.includes(search) || gName.includes(search));
      
      let mStatus = false;
      if (currentStatusFilter === 'ALL') {
        mStatus = true;
      } else if (currentStatusFilter === 'vacant') {
        mStatus = (cStatus === 'available' || cStatus === 'dirty');
      } else {
        mStatus = (cStatus === currentStatusFilter);
      }

      card.style.display = (mType && mFloor && mSearch && mStatus) ? 'flex' : 'none';
    });
  }

  function filterByStatus(status, btnElement) {
    currentStatusFilter = status;
    applyFilters();
    
    document.querySelectorAll('.legend-chip').forEach(chip => chip.classList.remove('active-chip'));
    if (btnElement) {
      btnElement.classList.add('active-chip');
    }
  }

  function resetFilters() {
    if (document.getElementById('filter-type')) document.getElementById('filter-type').value = 'ALL';
    if (document.getElementById('filter-floor')) document.getElementById('filter-floor').value = 'ALL';
    if (document.getElementById('filter-search')) document.getElementById('filter-search').value = '';
    currentStatusFilter = 'ALL';
    applyFilters();

    document.querySelectorAll('.legend-chip').forEach(chip => chip.classList.remove('active-chip'));
    const allChip = document.querySelector('.legend-chip.chip-all');
    if (allChip) allChip.classList.add('active-chip');
  }

  function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
  }

  function openReservationModal() {
    setInitialDateTime();
    openModal('reserve-modal');
  }

  function openRoomDetails(roomNo, category, cleaningStatus, pmsStatus, rate, guestObj, opStatus, hkStatus) {
    const titleEl = document.getElementById('m-title');
    const bodyEl = document.getElementById('m-body');
    const footerEl = document.getElementById('m-footer');

    if (!titleEl || !bodyEl || !footerEl) return;

    titleEl.innerHTML = `<i class="fa-solid fa-door-open"></i> Room ${roomNo} - ${category} (₹ ${Number(rate).toLocaleString()}/night)`;

    const opDisplay = opStatus || (pmsStatus === 'Blocked' ? 'Out of Order / Blocked' : 'Active In-Service');
    const hkDisplay = hkStatus || (cleaningStatus || 'Cleaned & Inspected');

    if (guestObj) {
      bodyEl.innerHTML = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div style="background: var(--bg-surface, #f8fafc); padding: 16px; border: 1px solid var(--border-subtle, #e2e8f0); border-radius: var(--radius-md, 8px);">
            <h4 style="font-size: 11px; color: var(--accent-primary, #6366f1); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;"><i class="fa-solid fa-user"></i> Guest Info</h4>
            <div style="font-size: 15px; font-weight: 800; color: var(--text-primary, #0f172a);">${escapeHtml(guestObj.name)}</div>
            <div style="font-size: 11px; color: var(--text-secondary, #64748b); margin-top: 6px;">Status: <span class="badge-tag green">${escapeHtml(guestObj.state)}</span></div>
            <div style="font-size: 11px; color: var(--text-muted, #94a3b8); margin-top: 4px;">Folio: <strong style="color: var(--accent-cyan, #06b6d4); font-family: var(--font-mono);">${escapeHtml(guestObj.folio)}</strong></div>
            <div style="font-size: 11px; color: var(--text-muted, #94a3b8); margin-top: 4px;">Housekeeping: <strong style="color: var(--accent-emerald, #10b981);">${escapeHtml(hkDisplay)}</strong></div>
          </div>
          <div style="background: var(--bg-surface, #f8fafc); padding: 16px; border: 1px solid var(--border-subtle, #e2e8f0); border-radius: var(--radius-md, 8px);">
            <h4 style="font-size: 11px; color: var(--accent-primary, #6366f1); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;"><i class="fa-solid fa-wallet"></i> Folio Outstanding</h4>
            <div style="font-size: 24px; font-weight: 900; color: var(--accent-rose, #f43f5e); font-family: var(--font-mono);">₹ ${Number(guestObj.balance).toLocaleString()}</div>
            <div style="font-size: 11px; color: var(--accent-emerald, #10b981); margin-top: 6px;"><i class="fa-solid fa-check"></i> Advance: ₹ 5,000</div>
            <div style="font-size: 11px; color: var(--text-muted, #94a3b8); margin-top: 4px;">Operational: <strong style="color: var(--accent-primary, #6366f1);">${escapeHtml(opDisplay)}</strong></div>
          </div>
        </div>
        <div class="pms-table-box" style="border: 1px solid var(--border-medium, #e2e8f0); border-radius: var(--radius-md, 8px); overflow: hidden;">
          <div style="background: var(--bg-surface, #f8fafc); padding: 10px 16px; font-weight: 800; font-size: 11px; color: var(--text-secondary, #64748b); text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa-solid fa-receipt" style="margin-right: 6px;"></i>Charges Breakdown</div>
          <table class="pms-table" style="width: 100%; border-collapse: collapse; font-size: 12px;">
            <thead><tr style="background: #f1f5f9; text-align: left;"><th style="padding: 8px 12px;">Date</th><th style="padding: 8px 12px;">Description</th><th style="padding: 8px 12px; text-align: right;">Amount</th></tr></thead>
            <tbody>
              <tr style="border-top: 1px solid #e2e8f0;"><td style="padding: 8px 12px;">Today</td><td style="padding: 8px 12px;">Room Charge (${escapeHtml(category)})</td><td style="padding: 8px 12px; text-align: right; font-family: var(--font-mono); font-weight: 700;">₹ ${Number(rate).toLocaleString()}</td></tr>
              <tr style="border-top: 1px solid #e2e8f0;"><td style="padding: 8px 12px;">Today</td><td style="padding: 8px 12px;">Restaurant Room Service</td><td style="padding: 8px 12px; text-align: right; font-family: var(--font-mono); font-weight: 700;">₹ 1,450</td></tr>
              <tr style="border-top: 1px solid #e2e8f0;"><td style="padding: 8px 12px;">Today</td><td style="padding: 8px 12px;">Laundry Express</td><td style="padding: 8px 12px; text-align: right; font-family: var(--font-mono); font-weight: 700;">₹ 450</td></tr>
              <tr style="border-top: 1px solid #e2e8f0;"><td style="padding: 8px 12px;">Today</td><td style="padding: 8px 12px;">GST (12%)</td><td style="padding: 8px 12px; text-align: right; font-family: var(--font-mono); font-weight: 700;">₹ 1,500</td></tr>
            </tbody>
          </table>
        </div>
      `;
      footerEl.innerHTML = `
        <button class="btn-ui-secondary" onclick="printInvoice('${roomNo}', '${escapeHtml(guestObj.name)}', '${escapeHtml(guestObj.folio)}', ${guestObj.balance})"><i class="fa-solid fa-print"></i> Print Invoice</button>
        <button class="btn-ui-primary" onclick="alert('Redirecting to POS Charge...')"><i class="fa-solid fa-utensils"></i> Add POS Charge</button>
        <button class="btn-ui-danger" onclick="checkoutGuest('${roomNo}', '${escapeHtml(guestObj.name)}')"><i class="fa-solid fa-right-from-bracket"></i> Check Out</button>
        <button class="btn-ui-secondary" onclick="closeModal('room-modal')">Close</button>
      `;
    } else {
      const isDirty = (cleaningStatus === 'Dirty' || (hkDisplay && hkDisplay.toLowerCase().includes('dirty')));
      const isBlocked = (pmsStatus === 'Blocked' || (opDisplay && (opDisplay.toLowerCase().includes('blocked') || opDisplay.toLowerCase().includes('maintenance'))));

      bodyEl.innerHTML = `
        <div style="background: var(--bg-surface, #f8fafc); padding: 18px; border: 1px solid var(--border-subtle, #e2e8f0); border-radius: var(--radius-md, 8px); margin-bottom: 14px;">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <div style="font-size: 16px; font-weight: 800; color: var(--text-primary, #0f172a);">Room ${roomNo} Status: ${escapeHtml(pmsStatus)}</div>
              <div style="font-size: 12px; color: var(--text-secondary, #64748b); margin-top: 6px;">
                Housekeeping: <strong style="color: ${isDirty ? '#d97706' : '#059669'};">${escapeHtml(hkDisplay)}</strong>
              </div>
              <div style="font-size: 12px; color: var(--text-secondary, #64748b); margin-top: 3px;">
                Operational: <strong style="color: ${isBlocked ? '#e11d48' : '#6366f1'};">${escapeHtml(opDisplay)}</strong>
              </div>
            </div>
            <span class="badge-tag ${isBlocked ? 'blue' : (isDirty ? 'yellow' : 'green')}" style="font-size: 12px; padding: 6px 14px;">
              ${escapeHtml(isBlocked ? opDisplay : hkDisplay)}
            </span>
          </div>
        </div>
        <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary, #64748b);">
          <i class="fa-solid fa-sparkles" style="color: var(--accent-gold, #fbc531);"></i> Room Features: Luxury Bedding, Free Wi-Fi, Climate Control AC, Smart TV, 24h Hot Water.
        </div>
      `;
      footerEl.innerHTML = `
        <button class="btn-ui-success" onclick="openReservationModal()"><i class="fa-solid fa-key"></i> New Check-In</button>
        ${isDirty ? `<button class="btn-ui-primary" onclick="markRoomCleaned('${roomNo}')"><i class="fa-solid fa-broom"></i> Mark Cleaned</button>` : ''}
        <button class="btn-ui-secondary" onclick="closeModal('room-modal')">Close</button>
      `;
    }
    openModal('room-modal');
  }

  function checkoutGuest(roomNo, guestName) {
    if (confirm(`Check out ${guestName} from Room ${roomNo}?`)) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast(`Room ${roomNo} checked out successfully. Marked as Dirty.`, 'success');
      } else {
        alert(`Room ${roomNo} checked out successfully. Marked as Dirty.`);
      }
      closeModal('room-modal');
      const card = document.querySelector(`.rack-card[data-room="${roomNo}"]`);
      if (card) {
        card.className = 'rack-card c-dirty';
        card.setAttribute('data-status', 'dirty');
        const mid = card.querySelector('.rack-card-mid');
        if (mid) mid.innerHTML = '';
        const badge = card.querySelector('.rack-badge');
        if (badge) badge.textContent = 'Dirty';
      }
    }
  }

  function markRoomCleaned(roomNo) {
    const card = document.querySelector(`.rack-card[data-room="${roomNo}"]`);
    if (card) {
      card.className = 'rack-card c-cleaned';
      card.setAttribute('data-status', 'available');
      const badge = card.querySelector('.rack-badge');
      if (badge) badge.textContent = 'Cleaned';
    }
    closeModal('room-modal');
    if (typeof PmsAlert !== 'undefined') {
      PmsAlert.toast(`Room ${roomNo} marked as Cleaned!`, 'success');
    } else {
      alert(`Room ${roomNo} marked as Cleaned!`);
    }
  }

  function handleReserve(e) {
    e.preventDefault();
    if (typeof PmsAlert !== 'undefined') {
      PmsAlert.toast('Reservation confirmed successfully for Hotel Sagar Sonnet!', 'success');
    } else {
      alert('Reservation confirmed successfully for Hotel Sagar Sonnet!');
    }
    closeModal('reserve-modal');
  }

  function printInvoice(roomNo, guestName, folio, balance) {
    const printArea = document.getElementById('print-area');
    if (!printArea) return;
    printArea.innerHTML = `
      <div style="padding: 40px; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b;">
        <div style="border-bottom: 2px solid #6366f1; padding-bottom: 16px; margin-bottom: 24px; display: flex; justify-content: space-between;">
          <div>
            <h1 style="color: #6366f1; margin: 0; font-size: 24px; font-weight: 900; letter-spacing: 1px;">HOTEL SAGAR SONNET</h1>
            <p style="margin: 6px 0 0 0; font-size: 12px; color: #64748b;">Luxury Sea Beach Resort & Suites | Phone: +91 98765 43210</p>
          </div>
          <div style="text-align: right;">
            <h2 style="margin: 0; font-size: 16px; color: #1e293b;">TAX INVOICE / FOLIO</h2>
            <p style="margin: 4px 0 0 0; font-size: 12px;"><strong>Folio:</strong> ${escapeHtml(folio)}</p>
            <p style="margin: 2px 0 0 0; font-size: 12px;"><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
          </div>
        </div>
        <div style="margin-bottom: 24px; background: #f8fafc; padding: 14px; border: 1px solid #cbd5e1; border-radius: 8px;">
          <p style="margin: 0 0 4px 0;"><strong>Guest Name:</strong> ${escapeHtml(guestName)}</p>
          <p style="margin: 0 0 4px 0;"><strong>Room:</strong> ${escapeHtml(roomNo)}</p>
          <p style="margin: 0;"><strong>GSTIN:</strong> 19AAGCS7712K1Z9</p>
        </div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px; font-size: 12px;">
          <thead><tr style="background: #6366f1; color: #fff;"><th style="padding: 10px; text-align:left;">Description</th><th style="padding: 10px; text-align:right;">Amount (₹)</th></tr></thead>
          <tbody>
            <tr style="border-bottom: 1px solid #cbd5e1;"><td style="padding: 10px;">Room Stay & Dining Charges</td><td style="padding: 10px; text-align:right;">${(Number(balance) + 5000).toLocaleString()}.00</td></tr>
            <tr style="border-bottom: 1px solid #cbd5e1;"><td style="padding: 10px; color: #10b981;">Less: Advance Deposit Paid</td><td style="padding: 10px; text-align:right; color: #10b981;">- 5,000.00</td></tr>
          </tbody>
          <tfoot>
            <tr style="font-size: 14px; font-weight: bold; background: #f1f5f9;"><td style="padding: 10px;">Net Balance Payable:</td><td style="padding: 10px; text-align:right; color: #dc2626;">₹ ${Number(balance).toLocaleString()}.00</td></tr>
          </tfoot>
        </table>
        <div style="margin-top: 40px; display: flex; justify-content: space-between; font-size: 12px;">
          <div>Guest Signature: __________________</div>
          <div>Cashier Signature: __________________</div>
        </div>
      </div>
    `;
    window.print();
  }

  // Master Rooms & Floors JSON Data for Dynamic Cascading Selection
  window.allPmsRooms = @json($rackRooms ?? $allRoomsData ?? []);
  window.allPmsFloors = @json($floors ?? []);
  window.allPmsCategories = @json($categories ?? []);

  function onCategoryChange(catSelect) {
    const block = catSelect.closest('.section-guest-details') || catSelect.closest('.guest-block');
    if (!block) return;
    const floorSelect = block.querySelector('.select-room-floor');
    const roomSelect = block.querySelector('.select-room-no');
    const selectedCatId = catSelect.value;
    const selectedCatName = catSelect.options[catSelect.selectedIndex]?.dataset?.categoryName || catSelect.options[catSelect.selectedIndex]?.text;

    if (floorSelect) floorSelect.innerHTML = '<option value="">Select Floor</option>';
    if (roomSelect) roomSelect.innerHTML = '<option value="">Select Room No.</option>';

    if (!selectedCatId) return;

    const matchingRooms = window.allPmsRooms.filter(r => {
      return String(r.category_id) === String(selectedCatId) || 
             (r.category && String(r.category).toLowerCase() === String(selectedCatName).toLowerCase()) ||
             (r.type && String(r.type).toLowerCase() === String(selectedCatName).toLowerCase());
    });

    const floorMap = new Map();
    matchingRooms.forEach(r => {
      const flId = r.floor_id || r.floor;
      if (!floorMap.has(String(flId))) {
        floorMap.set(String(flId), {
          id: flId,
          floor: r.floor,
          name: r.floor_name || ('Floor ' + r.floor)
        });
      }
    });

    if (floorSelect) {
      floorMap.forEach(fl => {
        const opt = document.createElement('option');
        opt.value = fl.id;
        opt.dataset.floorNo = fl.floor;
        opt.textContent = `Floor ${fl.floor} (${fl.name})`;
        floorSelect.appendChild(opt);
      });
    }
  }

  function onFloorChange(floorSelect) {
    const block = floorSelect.closest('.section-guest-details') || floorSelect.closest('.guest-block');
    if (!block) return;
    const catSelect = block.querySelector('.select-room-category');
    const roomSelect = block.querySelector('.select-room-no');
    
    const selectedCatId = catSelect ? catSelect.value : '';
    const selectedCatName = catSelect ? (catSelect.options[catSelect.selectedIndex]?.dataset?.categoryName || catSelect.options[catSelect.selectedIndex]?.text) : '';
    const selectedFloorVal = floorSelect.value;
    const selectedFloorNo = floorSelect.options[floorSelect.selectedIndex]?.dataset?.floorNo;

    if (roomSelect) roomSelect.innerHTML = '<option value="">Select Room No.</option>';

    if (!selectedFloorVal) return;

    const matchingRooms = window.allPmsRooms.filter(r => {
      const matchCat = !selectedCatId || 
                       String(r.category_id) === String(selectedCatId) || 
                       (r.category && String(r.category).toLowerCase() === String(selectedCatName).toLowerCase()) ||
                       (r.type && String(r.type).toLowerCase() === String(selectedCatName).toLowerCase());
      
      const matchFloor = String(r.floor_id) === String(selectedFloorVal) || 
                         String(r.floor) === String(selectedFloorVal) || 
                         (selectedFloorNo && String(r.floor) === String(selectedFloorNo));
      
      return matchCat && matchFloor;
    });

    if (roomSelect) {
      matchingRooms.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id || r.room;
        opt.dataset.roomNo = r.room;
        opt.dataset.rate = r.rate;
        const isAvail = (r.status === 'available');
        opt.textContent = `Room #${r.room} (₹${r.rate}) • ${r.status.toUpperCase()}`;
        if (!isAvail) {
          opt.style.color = '#ef4444';
        } else {
          opt.style.color = '#059669';
          opt.style.fontWeight = 'bold';
        }
        roomSelect.appendChild(opt);
      });
    }
  }

  function searchRegularGuest(btnElem) {
    const block = btnElem.closest('.guest-block') || btnElem.closest('.section-guest-details');
    if (!block) return;
    const phoneInput = block.querySelector('.regular-search-input');
    const phone = phoneInput ? phoneInput.value.trim() : '';
    if (!phone) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('Please enter a mobile number to lookup regular guest!', 'error');
      } else {
        alert('Please enter a mobile number to lookup regular guest!');
      }
      return;
    }

    // Sample database guest profile matching
    const sampleProfiles = {
      '9876543210': { name: 'Debabrata Mukherjee', address: 'Flat 4B, Salt Lake Sector 2', city: 'Kolkata', email: 'debabrata@gmail.com', idCard: '1982-4421-9981' },
      '9830012345': { name: 'Priyabrata Sengupta', address: '12/1 Southern Avenue', city: 'Kolkata', email: 'priyabrata@gmail.com', idCard: 'ABCDP1234F' }
    };

    const profile = sampleProfiles[phone] || {
      name: 'Regular Guest (Phone ' + phone + ')',
      address: 'Park Street Extension, Floor 3',
      city: 'Kolkata',
      email: 'guest.' + phone.slice(-4) + '@hotelcrm.com',
      idCard: 'REG-' + phone.slice(-6)
    };

    const nameInp = block.querySelector('.guest-name-input');
    const addrInp = block.querySelector('.guest-address-input');
    const cityInp = block.querySelector('.guest-city-input');
    const mobInp = block.querySelector('.guest-mobile-input');
    const emailInp = block.querySelector('.guest-email-input');
    const idInp = block.querySelector('.guest-id-number');

    if (nameInp) nameInp.value = profile.name;
    if (addrInp) addrInp.value = profile.address;
    if (cityInp) cityInp.value = profile.city;
    if (mobInp) mobInp.value = phone;
    if (emailInp) emailInp.value = profile.email;
    if (idInp) idInp.value = profile.idCard;

    if (typeof PmsAlert !== 'undefined') {
      PmsAlert.toast('Regular guest profile loaded for ' + profile.name, 'success');
    } else {
      alert('Regular guest profile loaded for ' + profile.name);
    }
  }

  function toggleResType() {
    const checkedRadio = document.querySelector('input[name="res_type"]:checked');
    if (!checkedRadio) return;
    const type = checkedRadio.value;
    const compFields = document.getElementById('section-company-fields');
    const labels = document.querySelectorAll('input[name="res_type"]');
    
    labels.forEach(radio => {
      if (radio.parentElement) {
        radio.parentElement.style.color = radio.checked ? 'var(--accent-primary, #6366f1)' : 'var(--text-secondary, #64748b)';
      }
    });

    if (compFields) {
      compFields.style.display = (type === 'company') ? 'block' : 'none';
    }

    const guestBlocks = document.querySelectorAll('.guest-block');
    guestBlocks.forEach(block => {
      const regSearch = block.querySelector('.section-regular-search');
      const privContainer = block.querySelector('.privilege-checkbox-container');
      const privInput = block.querySelector('.privilege-input-container');
      const chkPriv = block.querySelector('.chk-privilege');
      
      if (regSearch) regSearch.style.display = (type === 'regular') ? 'block' : 'none';
      
      if (privContainer) {
        if (type === 'company') {
          privContainer.style.display = 'none';
          if (privInput) privInput.style.display = 'none';
          if (chkPriv) chkPriv.checked = false;
        } else if (type === 'privilege') {
          privContainer.style.display = 'flex';
          privContainer.style.order = '-1';
          if (chkPriv) chkPriv.checked = true;
          if (privInput) privInput.style.display = 'block';
        } else {
          privContainer.style.display = 'flex';
          privContainer.style.order = '0';
          if (privInput) privInput.style.display = (chkPriv && chkPriv.checked) ? 'block' : 'none';
        }
      }
    });
  }

  function togglePrivilegeInput(checkboxElem) {
    const checkedRadio = document.querySelector('input[name="res_type"]:checked');
    const type = checkedRadio ? checkedRadio.value : 'new';
    const block = checkboxElem.closest('.guest-block');
    if (!block) return;
    const privInput = block.querySelector('.privilege-input-container');
    
    if (privInput) {
      if (type === 'company') {
        privInput.style.display = 'none';
      } else if (type === 'privilege') {
        privInput.style.display = 'block';
      } else {
        privInput.style.display = checkboxElem.checked ? 'block' : 'none';
      }
    }
  }

  function addAnotherGuest() {
    const container = document.getElementById('guests-container');
    if (!container) return;
    const blocks = container.querySelectorAll('.guest-block');
    if (!blocks.length) return;
    const newBlock = blocks[0].cloneNode(true);
    
    const guestNum = blocks.length + 1;
    const heading = newBlock.querySelector('h4.guest-heading');
    if (heading) heading.innerText = 'Guest #' + guestNum;
    
    newBlock.querySelectorAll('input:not([type="radio"]):not([type="checkbox"])').forEach(inp => inp.value = '');
    
    // Reset room selects in cloned block
    const catSel = newBlock.querySelector('.select-room-category');
    const flSel = newBlock.querySelector('.select-room-floor');
    const rmSel = newBlock.querySelector('.select-room-no');
    if (catSel) catSel.value = '';
    if (flSel) flSel.innerHTML = '<option value="">Select Floor</option>';
    if (rmSel) rmSel.innerHTML = '<option value="">Select Room No.</option>';

    const headerDiv = newBlock.querySelector('.guest-heading')?.parentElement;
    if (headerDiv && !newBlock.querySelector('.btn-remove-guest')) {
      let removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn-remove-guest';
      removeBtn.innerHTML = '<i class="fa-solid fa-trash"></i> Remove';
      removeBtn.style = 'color: #ef4444; background: none; border: none; font-size: 12px; font-weight: 800; cursor: pointer; text-transform: uppercase; letter-spacing: 1px;';
      removeBtn.onclick = function() { newBlock.remove(); updateGuestHeadings(); };
      headerDiv.appendChild(removeBtn);
    }
    
    const chkPriv = newBlock.querySelector('.chk-privilege');
    const checkedRadio = document.querySelector('input[name="res_type"]:checked');
    const type = checkedRadio ? checkedRadio.value : 'new';
    if (chkPriv) {
      chkPriv.checked = (type === 'privilege');
    }
    
    container.appendChild(newBlock);
    toggleResType();
  }

  function updateGuestHeadings() {
    const blocks = document.querySelectorAll('.guest-block');
    blocks.forEach((block, index) => {
      const heading = block.querySelector('h4.guest-heading');
      if (heading) heading.innerText = 'Guest #' + (index + 1);
    });
  }

  function toggleNewCompany(selectElem) {
    const newCompDetails = document.getElementById('new-company-details');
    if (newCompDetails) {
      newCompDetails.style.display = (selectElem.value === 'new') ? 'block' : 'none';
    }
  }

  function setInitialDateTime() {
    const now = new Date();
    const dateInput = document.getElementById('reserve-date-input');
    const timeInput = document.getElementById('reserve-time-input');
    
    if (dateInput) {
      const year = now.getFullYear();
      const month = String(now.getMonth() + 1).padStart(2, '0');
      const day = String(now.getDate()).padStart(2, '0');
      dateInput.value = `${year}-${month}-${day}`;
    }
    if (timeInput) {
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      timeInput.value = `${hours}:${minutes}`;
    }
  }

  function togglePaymentFields(selectElem) {
    const container = document.getElementById('payment-details-container');
    if (container) {
      container.style.display = (selectElem.value !== 'None') ? 'flex' : 'none';
    }
  }

  function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  window.addEventListener('DOMContentLoaded', () => {
    setInitialDateTime();
    applyFilters();
  });
</script>
