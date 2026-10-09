<script>
  function applyBackendFilter(status) {
    const statusInput = document.getElementById('filter-status-val');
    if (statusInput) statusInput.value = status;
    submitBackendFilter();
  }

  function submitBackendFilter() {
    const form = document.getElementById('backend-filter-form');
    if (form) {
      form.submit();
    }
  }

  function printRack() {
    window.print();
  }

  function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
  }

  function fetchNextReserveId() {
    fetch("{{ route('frontoffice.reserve.next-id') }}")
      .then(res => res.json())
      .then(data => {
        if (data.success && data.reserve_id) {
          const resIdInp = document.getElementById('reserve-id-input');
          if (resIdInp) resIdInp.value = data.reserve_id;
        }
      })
      .catch(err => console.error('Error fetching next reserve ID:', err));
  }

  function openReservationModal(preselectRoomNo = null, preselectCatId = null, preselectFloorId = null) {
    setInitialDateTime();
    fetchNextReserveId();
    
    if (preselectRoomNo || preselectCatId || preselectFloorId) {
      if (preselectRoomNo && window.allPmsRooms) {
        const match = window.allPmsRooms.find(r => String(r.room) === String(preselectRoomNo) || String(r.id) === String(preselectRoomNo));
        if (match) {
          preselectCatId = preselectCatId || match.category_id;
          preselectFloorId = preselectFloorId || match.floor_id || match.floor;
        }
      }

      const firstGuest = document.querySelector('.guest-block');
      if (firstGuest) {
        const catSel = firstGuest.querySelector('.select-room-category');
        const flSel = firstGuest.querySelector('.select-room-floor');
        const rmSel = firstGuest.querySelector('.select-room-no');

        if (catSel && preselectCatId) {
          catSel.value = preselectCatId;
          onCategoryChange(catSel);
        }
        if (flSel && preselectFloorId) {
          flSel.value = preselectFloorId;
          onFloorChange(flSel);
        }
        if (rmSel && preselectRoomNo) {
          rmSel.value = preselectRoomNo;
          onRoomChange(rmSel);
        }
      }
    }
    openModal('reserve-modal');
  }

  function openRoomDetailsModal(dataOrEl, legacyCat, legacyCleaning, legacyPmsStatus, legacyRate, legacyGuest, legacyOpStatus, legacyHkStatus) {
    let roomData = null;

    if (dataOrEl instanceof HTMLElement) {
      const rawJson = dataOrEl.getAttribute('data-room-json');
      if (rawJson) {
        try {
          roomData = JSON.parse(rawJson);
        } catch (e) {
          console.error('Error parsing data-room-json', e);
        }
      }
      if (!roomData) {
        const rNum = dataOrEl.getAttribute('data-room');
        if (window.allPmsRooms) {
          roomData = window.allPmsRooms.find(r => String(r.room) === String(rNum) || String(r.id) === String(rNum));
        }
      }
    } else if (typeof dataOrEl === 'object' && dataOrEl !== null && dataOrEl.room) {
      roomData = dataOrEl;
    } else if (typeof dataOrEl === 'string' || typeof dataOrEl === 'number') {
      const rNum = String(dataOrEl);
      if (window.allPmsRooms) {
        const found = window.allPmsRooms.find(r => String(r.room) === rNum || String(r.id) === rNum);
        if (found) {
          roomData = Object.assign({}, found);
          if (legacyGuest) roomData.guest = legacyGuest;
          if (legacyRate) roomData.rate = legacyRate;
          if (legacyCat) roomData.category = legacyCat;
        }
      }
      if (!roomData) {
        roomData = {
          room: rNum,
          category: legacyCat || 'Deluxe',
          floor: '1',
          floor_name: 'Floor 1',
          rate: legacyRate || 4500,
          status: (legacyPmsStatus || 'Available').toLowerCase(),
          cleaning: legacyCleaning || 'Cleaned',
          operational_status: legacyOpStatus || 'Active In-Service',
          housekeeping_status: legacyHkStatus || 'Cleaned & Inspected',
          bedding: 'King Size Master (72x78)',
          max_adults: 2,
          max_children: 1,
          max_pax: 3,
          amenities: ['Free Wi-Fi', 'Air Conditioning (AC)', 'Smart 55" TV', '24h Hot & Cold Water'],
          guest: legacyGuest || null
        };
      }
    }

    if (!roomData) return;

    const roomNo = roomData.room;
    const category = roomData.category || roomData.type || 'Deluxe Room';
    const floorName = roomData.floor_name || ('Floor ' + (roomData.floor || '1'));
    const rate = Number(roomData.rate) || 4500;
    const status = (roomData.status || 'available').toLowerCase();
    const guestObj = roomData.guest || null;
    const bedding = roomData.bedding || 'King Size Master (72x78)';
    const maxAdults = roomData.max_adults || 2;
    const maxChildren = roomData.max_children || 1;
    const maxPax = roomData.max_pax || (maxAdults + maxChildren);
    const amenities = Array.isArray(roomData.amenities) && roomData.amenities.length > 0 
      ? roomData.amenities 
      : ['Free Wi-Fi', 'Air Conditioning (AC)', 'Smart 55" TV', '24h Hot & Cold Water'];
    
    const opDisplay = roomData.operational_status || (status === 'blocked' ? 'Out of Order / Blocked' : 'Active In-Service');
    const hkDisplay = roomData.housekeeping_status || (status === 'dirty' ? 'Dirty / Cleaning Due' : 'Cleaned & Inspected');
    const isDirty = status === 'dirty' || (hkDisplay && hkDisplay.toLowerCase().includes('dirty'));
    const isBlocked = status === 'blocked' || (opDisplay && (opDisplay.toLowerCase().includes('blocked') || opDisplay.toLowerCase().includes('maintenance')));
    const isOccupied = status === 'occupied' || !!guestObj;

    let statusBadgeClass = 'green';
    let statusBadgeText = 'Available / Cleaned';
    if (isOccupied) {
      statusBadgeClass = 'red';
      statusBadgeText = guestObj?.state || 'Occupied';
    } else if (isDirty) {
      statusBadgeClass = 'yellow';
      statusBadgeText = 'Dirty / Needs Cleaning';
    } else if (isBlocked) {
      statusBadgeClass = 'blue';
      statusBadgeText = 'Blocked / Maintenance';
    }

    const titleEl = document.getElementById('m-title');
    const bodyEl = document.getElementById('m-body');
    const footerEl = document.getElementById('m-footer');

    if (!titleEl || !bodyEl || !footerEl) return;

    titleEl.innerHTML = `<i class="fa-solid fa-door-open" style="color: var(--accent-primary, #6366f1); margin-right: 6px;"></i> Room Details • Room ${escapeHtml(roomNo)}`;

    const amenitiesHtml = amenities.map(amn => {
      return `<span style="background: rgba(99, 102, 241, 0.08); color: #4338ca; border: 1px solid rgba(99, 102, 241, 0.18); font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;">
        <i class="fa-solid fa-circle-check" style="font-size: 9px; color: #10b981;"></i> ${escapeHtml(amn)}
      </span>`;
    }).join('');

    let guestSectionHtml = '';
    if (isOccupied && guestObj) {
      guestSectionHtml = `
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px;">
            <h4 style="margin: 0; font-size: 12px; font-weight: 800; color: #e11d48; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
              <i class="fa-solid fa-user-check"></i> Residing Guest Information
            </h4>
            <span class="badge-tag green" style="font-size: 10px; padding: 2px 8px;">${escapeHtml(guestObj.state || 'Arrived')}</span>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
            <div>
              <div style="font-size: 15px; font-weight: 800; color: #0f172a;">${escapeHtml(guestObj.name)}</div>
              <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                <strong>Reservation ID:</strong> <span style="font-family: var(--font-mono); color: #6366f1; font-weight: 700;">${escapeHtml(guestObj.reserve_id || ('RES-2026-00' + roomNo))}</span>
              </div>
              <div style="font-size: 11px; color: #64748b; margin-top: 3px;">
                <strong>Folio:</strong> <span style="font-family: var(--font-mono); color: #0284c7; font-weight: 700;">${escapeHtml(guestObj.folio || ('FOL-' + roomNo + '-100'))}</span>
              </div>
              ${guestObj.mobile ? `<div style="font-size: 11px; color: #64748b; margin-top: 3px;"><i class="fa-solid fa-phone" style="color: #64748b; margin-right: 4px;"></i> ${escapeHtml(guestObj.mobile)}</div>` : ''}
            </div>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; text-align: right;">
              <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Outstanding Folio Balance</div>
              <div style="font-size: 20px; font-weight: 900; color: #e11d48; font-family: var(--font-mono); margin: 2px 0;">₹ ${Number(guestObj.balance || 0).toLocaleString()}</div>
              <div style="font-size: 11px; color: #059669; font-weight: 700;"><i class="fa-solid fa-check"></i> Advance Paid: ₹ ${Number(guestObj.advance || 5000).toLocaleString()}</div>
            </div>
          </div>
        </div>
      `;
    }

    bodyEl.innerHTML = `
      <!-- Top Highlights Banner -->
      <div style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.06), rgba(16, 185, 129, 0.04)); border: 1px solid rgba(99, 102, 241, 0.16); border-radius: 10px; padding: 14px 18px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, #4f46e5, #6366f1); color: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; font-family: var(--font-mono); font-weight: 900; font-size: 16px; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);">
            <span style="font-size: 9px; opacity: 0.85; font-family: var(--font-main); font-weight: 700; line-height: 1;">ROOM</span>
            <span style="line-height: 1.1;">${escapeHtml(roomNo)}</span>
          </div>
          <div>
            <div style="font-size: 16px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
              ${escapeHtml(category)}
              <span class="badge-tag ${statusBadgeClass}" style="font-size: 10px; font-weight: 800; text-transform: uppercase;">${escapeHtml(statusBadgeText)}</span>
            </div>
            <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-top: 3px;">
              <i class="fa-solid fa-layer-group" style="color: #6366f1; margin-right: 4px;"></i> ${escapeHtml(floorName)} • 
              <span style="color: #059669; font-weight: 700;">₹ ${rate.toLocaleString()} / night</span>
            </div>
          </div>
        </div>
        <div style="display: flex; flex-direction: column; align-items: flex-end; justify-content: center; gap: 4px;">
          <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px;">Operational State</div>
          <div style="display: inline-flex; align-items: center; gap: 6px; background: ${isBlocked ? '#fef2f2' : (isOccupied ? '#eff6ff' : '#ecfdf5')}; color: ${isBlocked ? '#dc2626' : (isOccupied ? '#2563eb' : '#059669')}; border: 1px solid ${isBlocked ? '#fecaca' : (isOccupied ? '#bfdbfe' : '#a7f3d0')}; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; box-shadow: 0 2px 4px rgba(0,0,0,0.03);">
            <span style="width: 7px; height: 7px; border-radius: 50%; background: ${isBlocked ? '#ef4444' : (isOccupied ? '#3b82f6' : '#10b981')}; display: inline-block;"></span>
            ${escapeHtml(opDisplay)}
          </div>
        </div>
      </div>

      <!-- Uniform Specifications & Capacity 4-Grid -->
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 14px;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
          <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa-solid fa-bed" style="color: #6366f1; margin-right: 4px;"></i> Bedding</div>
          <div style="font-size: 12px; font-weight: 800; color: #0f172a; margin-top: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${escapeHtml(bedding)}">${escapeHtml(bedding)}</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
          <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa-solid fa-users" style="color: #10b981; margin-right: 4px;"></i> Pax Capacity</div>
          <div style="font-size: 12px; font-weight: 800; color: #0f172a; margin-top: 4px;">${maxAdults}A, ${maxChildren}C (${maxPax} Pax)</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
          <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa-solid fa-broom" style="color: #f59e0b; margin-right: 4px;"></i> Housekeeping</div>
          <div style="font-size: 12px; font-weight: 800; color: ${isDirty ? '#d97706' : '#059669'}; margin-top: 4px;">${escapeHtml(hkDisplay)}</div>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
          <div style="font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><i class="fa-solid fa-shield-halved" style="color: #6366f1; margin-right: 4px;"></i> Operational</div>
          <div style="font-size: 12px; font-weight: 800; color: ${isBlocked ? '#e11d48' : '#6366f1'}; margin-top: 4px;">${escapeHtml(opDisplay)}</div>
        </div>
      </div>

      <!-- Residing Guest Info (if any) -->
      ${guestSectionHtml}

      <!-- Amenities Section -->
      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px;">
        <div style="font-size: 11px; font-weight: 800; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
          <i class="fa-solid fa-wand-magic-sparkles" style="color: #f59e0b;"></i> Room Amenities & Services
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
          ${amenitiesHtml}
        </div>
      </div>
    `;

    // Action Buttons Footer
    if (isOccupied && guestObj) {
      footerEl.innerHTML = `
        <button class="btn-ui-secondary" onclick="printInvoice('${roomNo}', '${escapeHtml(guestObj.name)}', '${escapeHtml(guestObj.folio || '')}', ${guestObj.balance || 0})"><i class="fa-solid fa-print"></i> Print Invoice</button>
        <button class="btn-ui-primary" onclick="if(typeof PmsAlert !== 'undefined') PmsAlert.toast('POS charge panel opened for Room ${roomNo}', 'info'); else alert('POS charge panel opened for Room ${roomNo}');"><i class="fa-solid fa-utensils"></i> Add POS Charge</button>
        <button class="btn-ui-danger" onclick="checkoutGuest('${roomNo}', '${escapeHtml(guestObj.name)}')"><i class="fa-solid fa-right-from-bracket"></i> Check Out</button>
        <button class="btn-ui-secondary" onclick="closeModal('room-modal')">Close</button>
      `;
    } else if (isDirty) {
      footerEl.innerHTML = `
        <button class="btn-ui-primary" onclick="markRoomCleaned('${roomNo}')"><i class="fa-solid fa-broom"></i> Mark Cleaned</button>
        <button class="btn-ui-success" onclick="closeModal('room-modal'); openReservationModal('${roomNo}');"><i class="fa-solid fa-key"></i> Check In</button>
        <button class="btn-ui-secondary" onclick="closeModal('room-modal')">Close</button>
      `;
    } else if (isBlocked) {
      footerEl.innerHTML = `
        <button class="btn-ui-primary" onclick="markRoomCleaned('${roomNo}')"><i class="fa-solid fa-unlock"></i> Set In-Service</button>
        <button class="btn-ui-secondary" onclick="closeModal('room-modal')">Close</button>
      `;
    } else {
      footerEl.innerHTML = `
        <button class="btn-ui-success" onclick="closeModal('room-modal'); openReservationModal('${roomNo}');"><i class="fa-solid fa-key"></i> New Check-In</button>
        <button class="btn-ui-warning" onclick="markRoomDirty('${roomNo}')"><i class="fa-solid fa-broom"></i> Mark Dirty</button>
        <button class="btn-ui-secondary" onclick="closeModal('room-modal')">Close</button>
      `;
    }

    openModal('room-modal');
  }

  // Alias
  window.openRoomDetails = openRoomDetailsModal;

  function markRoomDirty(roomNo) {
    const card = document.querySelector(`.rack-card[data-room="${roomNo}"]`);
    if (card) {
      card.className = 'rack-card c-dirty';
      card.setAttribute('data-status', 'dirty');
      const badge = card.querySelector('.rack-badge');
      if (badge) badge.textContent = 'Dirty';
    }
    closeModal('room-modal');
    if (typeof PmsAlert !== 'undefined') {
      PmsAlert.toast(`Room ${roomNo} marked as Dirty!`, 'warning');
    } else {
      alert(`Room ${roomNo} marked as Dirty!`);
    }
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

    const banner = block.querySelector('.room-capacity-banner');
    if (banner) banner.style.display = 'none';
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
    const banner = block.querySelector('.room-capacity-banner');
    if (banner) banner.style.display = 'none';
    
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
        opt.dataset.bedding = r.bedding || 'King Size Master (72x78)';
        opt.dataset.maxAdults = r.max_adults || 2;
        opt.dataset.maxChildren = r.max_children || 1;
        opt.dataset.maxPax = r.max_pax || 3;

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

  function onRoomChange(roomSelect) {
    const block = roomSelect.closest('.section-guest-details') || roomSelect.closest('.guest-block');
    if (!block) return;
    const banner = block.querySelector('.room-capacity-banner');
    const selectedOpt = roomSelect.options[roomSelect.selectedIndex];

    if (!roomSelect.value || !selectedOpt || !banner) {
      if (banner) banner.style.display = 'none';
      return;
    }

    const beddingName = selectedOpt.dataset.bedding || 'King Size Master (72x78)';
    const maxAdults = selectedOpt.dataset.maxAdults || '2';
    const maxChildren = selectedOpt.dataset.maxChildren || '1';
    const maxPax = selectedOpt.dataset.maxPax || '3';
    const rate = selectedOpt.dataset.rate || '4500';

    const beddingEl = banner.querySelector('.bedding-name-text');
    const paxEl = banner.querySelector('.max-pax-count');
    const adultsEl = banner.querySelector('.max-adults-count');
    const kidsEl = banner.querySelector('.max-children-count');
    const rateEl = banner.querySelector('.room-rate-text');

    if (beddingEl) beddingEl.textContent = beddingName;
    if (paxEl) paxEl.textContent = maxPax + ' Pax';
    if (adultsEl) adultsEl.textContent = maxAdults;
    if (kidsEl) kidsEl.textContent = maxChildren;
    if (rateEl) rateEl.textContent = '₹ ' + Number(rate).toLocaleString() + '/night';

    banner.style.display = 'block';
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

    const origBtnHtml = btnElem.innerHTML;
    btnElem.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Searching...';
    btnElem.disabled = true;

    fetch("{{ route('frontoffice.guest.search') }}?mobile=" + encodeURIComponent(phone))
      .then(res => res.json())
      .then(data => {
        btnElem.innerHTML = origBtnHtml;
        btnElem.disabled = false;

        if (data.success && data.guest) {
          const g = data.guest;
          const nameInp = block.querySelector('.guest-name-input');
          const addrInp = block.querySelector('.guest-address-input');
          const cityInp = block.querySelector('.guest-city-input');
          const mobInp = block.querySelector('.guest-mobile-input');
          const emailInp = block.querySelector('.guest-email-input');
          const idInp = block.querySelector('.guest-id-number');
          const titleSel = block.querySelector('.pms-select-title');
          const natSel = block.querySelector('.pms-select-nationality');
          const idTypeSel = block.querySelector('.pms-select-idtype');
          const dobInp = block.querySelector('.guest-dob-input');
          const annivInp = block.querySelector('.guest-anniversary-input');

          if (nameInp) nameInp.value = g.name || '';
          if (addrInp) addrInp.value = g.address || '';
          if (cityInp) cityInp.value = g.city || '';
          if (mobInp) mobInp.value = g.mobile || phone;
          if (emailInp) emailInp.value = g.email || '';
          if (idInp) idInp.value = g.id_card_number || '';
          if (titleSel && g.title_id) titleSel.value = g.title_id;
          if (natSel && g.nationality_id) natSel.value = g.nationality_id;
          if (idTypeSel && g.id_card_type_id) idTypeSel.value = g.id_card_type_id;
          if (dobInp && g.dob) dobInp.value = g.dob;
          if (annivInp && g.anniversary) annivInp.value = g.anniversary;

          if (typeof PmsAlert !== 'undefined') {
            PmsAlert.toast('Regular guest profile loaded for ' + g.name, 'success');
          } else {
            alert('Regular guest profile loaded for ' + g.name);
          }
        } else {
          const mobInp = block.querySelector('.guest-mobile-input');
          if (mobInp) mobInp.value = phone;
          if (typeof PmsAlert !== 'undefined') {
            PmsAlert.toast('No prior profile found. New guest details can be entered.', 'info');
          } else {
            alert('No prior profile found.');
          }
        }
      })
      .catch(err => {
        btnElem.innerHTML = origBtnHtml;
        btnElem.disabled = false;
        console.error('Guest search error:', err);
      });
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
    
    // Reset room selects and capacity banner in cloned block
    const catSel = newBlock.querySelector('.select-room-category');
    const flSel = newBlock.querySelector('.select-room-floor');
    const rmSel = newBlock.querySelector('.select-room-no');
    const banner = newBlock.querySelector('.room-capacity-banner');
    if (catSel) catSel.value = '';
    if (flSel) flSel.innerHTML = '<option value="">Select Floor</option>';
    if (rmSel) rmSel.innerHTML = '<option value="">Select Room No.</option>';
    if (banner) banner.style.display = 'none';

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

  function handleReserve(e) {
    e.preventDefault();
    const form = document.getElementById('reservation-form') || e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const origBtnHtml = submitBtn ? submitBtn.innerHTML : '';

    // Collect Registration Type ID
    const checkedRadio = form.querySelector('input[name="res_type"]:checked');
    const registrationTypeId = checkedRadio ? checkedRadio.dataset.registrationId : null;
    const resTypeSlug = checkedRadio ? checkedRadio.value : 'new';

    // Collect Reservation Meta
    const reserveDate = form.querySelector('input[name="reserve_date"]')?.value || '';
    const reserveTime = form.querySelector('input[name="reserve_time"]')?.value || '';
    const reservationModeId = form.querySelector('select[name="reservation_mode_id"]')?.value || null;

    // Collect Corporate Details
    const companySelect = form.querySelector('select[name="company_id"]');
    const companyId = companySelect ? companySelect.value : null;
    const newCompanyName = form.querySelector('input[name="new_company_name"]')?.value || '';
    const newCompanyAddress = form.querySelector('input[name="new_company_address"]')?.value || '';
    const newCompanyGstin = form.querySelector('input[name="new_company_gstin"]')?.value || '';
    const newCompanyPhone = form.querySelector('input[name="new_company_phone"]')?.value || '';

    // Collect Guests
    const guestBlocks = form.querySelectorAll('.guest-block');
    const guests = [];

    guestBlocks.forEach(block => {
      const titleId = block.querySelector('select[name="title_id"]')?.value;
      const guestName = block.querySelector('input[name="guest_name"]')?.value?.trim();
      const guestAddress = block.querySelector('input[name="guest_address"]')?.value?.trim();
      const nationalityId = block.querySelector('select[name="nationality_id"]')?.value;
      const city = block.querySelector('input[name="city"]')?.value?.trim();
      const mobile = block.querySelector('input[name="mobile"]')?.value?.trim();
      const email = block.querySelector('input[name="email"]')?.value?.trim();
      const dob = block.querySelector('input[name="dob"]')?.value;
      const anniversary = block.querySelector('input[name="anniversary"]')?.value;
      const status = block.querySelector('select[name="status"]')?.value || 'Confirmed';
      const hasPriv = block.querySelector('.chk-privilege')?.checked || false;
      const privNo = block.querySelector('input[name="privilege_card_no"]')?.value?.trim();
      const roomId = block.querySelector('select[name="room_id"]')?.value;
      const idCardTypeId = block.querySelector('select[name="id_card_type_id"]')?.value;
      const idCardNumber = block.querySelector('input[name="id_card_number"]')?.value?.trim();

      if (guestName) {
        guests.push({
          title_id: titleId,
          guest_name: guestName,
          guest_address: guestAddress,
          nationality_id: nationalityId,
          city: city,
          mobile: mobile,
          email: email,
          dob: dob,
          anniversary: anniversary,
          status: status,
          has_privilege_card: hasPriv,
          privilege_card_no: privNo,
          room_id: roomId,
          id_card_type_id: idCardTypeId,
          id_card_number: idCardNumber,
        });
      }
    });

    if (guests.length === 0) {
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('Please enter guest name for reservation!', 'error');
      } else {
        alert('Please enter guest name for reservation!');
      }
      return;
    }

    // Collect Payment
    const paymentModeId = form.querySelector('select[name="payment_mode_id"]')?.value;
    const advanceAmount = form.querySelector('input[name="advance_amount"]')?.value || 0;
    const paymentRemarks = form.querySelector('input[name="payment_remarks"]')?.value || '';

    const payload = {
      registration_type_id: registrationTypeId,
      res_type: resTypeSlug,
      reserve_date: reserveDate,
      reserve_time: reserveTime,
      reservation_mode_id: reservationModeId,
      company_id: companyId,
      new_company_name: newCompanyName,
      new_company_address: newCompanyAddress,
      new_company_gstin: newCompanyGstin,
      new_company_phone: newCompanyPhone,
      guests: guests,
      payment_mode_id: paymentModeId,
      advance_amount: advanceAmount,
      payment_remarks: paymentRemarks,
    };

    if (submitBtn) {
      submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
      submitBtn.disabled = true;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch("{{ route('frontoffice.reserve.store') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken || '',
      },
      body: JSON.stringify(payload),
    })
      .then(res => res.json())
      .then(data => {
        if (submitBtn) {
          submitBtn.innerHTML = origBtnHtml;
          submitBtn.disabled = false;
        }

        if (data.success) {
          const resId = data.reserve_id;
          const nextId = data.next_reserve_id;

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Reservation Confirmed!',
              html: `<b>Reserve ID:</b> <code style="font-size: 16px; color: #6366f1; font-weight: bold;">${escapeHtml(resId)}</code><br><br>${escapeHtml(data.message)}`,
              confirmButtonColor: '#10b981',
              confirmButtonText: '<i class="fa-solid fa-check"></i> Great',
            });
          } else if (typeof PmsAlert !== 'undefined') {
            PmsAlert.toast(`Reservation #${resId} confirmed successfully!`, 'success');
          } else {
            alert(`Reservation #${resId} confirmed successfully!`);
          }

          closeModal('reserve-modal');
          form.reset();
          setInitialDateTime();

          const resIdInp = document.getElementById('reserve-id-input');
          if (resIdInp && nextId) resIdInp.value = nextId;

          // Update room card in dashboard view if matched
          guests.forEach(g => {
            if (g.room_id) {
              const card = document.querySelector(`.rack-card[data-room="${g.room_id}"]`) || document.querySelector(`.rack-card[data-room-id="${g.room_id}"]`);
              if (card) {
                card.className = 'rack-card c-occupied';
                card.setAttribute('data-status', 'occupied');
                card.setAttribute('data-guest', g.guest_name);
                const mid = card.querySelector('.rack-card-mid');
                if (mid) {
                  mid.innerHTML = `
                    <div class="guest-name" style="font-size: 12px; font-weight: 800; color: #fff;">${escapeHtml(g.guest_name)}</div>
                    <div class="guest-state" style="font-size: 10px; color: rgba(255,255,255,0.85);">${escapeHtml(g.status || 'Confirmed')}</div>
                  `;
                }
                const badge = card.querySelector('.rack-badge');
                if (badge) badge.textContent = 'Occupied';
              }
            }
          });

          // If on other pages (arrivals, inhouse, guest), reload to display fresh DB data
          if (window.location.pathname.includes('/arrivals') || 
              window.location.pathname.includes('/inhouse') || 
              window.location.pathname.includes('/guest')) {
            setTimeout(() => window.location.reload(), 1200);
          }

        } else {
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'error',
              title: 'Reservation Failed',
              text: data.message || 'Unable to store reservation. Please try again.',
            });
          } else {
            alert('Error: ' + (data.message || 'Unable to store reservation.'));
          }
        }
      })
      .catch(err => {
        if (submitBtn) {
          submitBtn.innerHTML = origBtnHtml;
          submitBtn.disabled = false;
        }
        console.error('Reservation error:', err);
        alert('Network or server error while submitting reservation.');
      });
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
  });
</script>
