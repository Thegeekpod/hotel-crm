<script>
  function applyBackendFilter(status) {
    const statusInput = document.getElementById('filter-status-val');
    if (statusInput) statusInput.value = status;
    submitBackendFilter();
  }

  function onDateFilterChanged(newDate) {
    const dateInput = document.getElementById('filter-date-val');
    if (dateInput) {
      dateInput.value = newDate;
    }
    submitBackendFilter();
  }

  function setDateFilterToday() {
    const today = new Date().toISOString().split('T')[0];
    onDateFilterChanged(today);
  }

  function changeDateFilter(daysOffset) {
    const curVal = document.getElementById('pms-calendar-input')?.value || document.getElementById('filter-date-val')?.value || new Date().toISOString().split('T')[0];
    const parts = curVal.split('-');
    const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    d.setDate(d.getDate() + daysOffset);
    const yr = d.getFullYear();
    const mo = String(d.getMonth() + 1).padStart(2, '0');
    const da = String(d.getDate()).padStart(2, '0');
    const nextVal = `${yr}-${mo}-${da}`;
    onDateFilterChanged(nextVal);
  }

  function submitBackendFilter() {
    const form = document.getElementById('backend-filter-form');
    if (form) {
      form.submit();
    }
  }

  function submitTopRoomSearch(query) {
    const hiddenSearch = document.getElementById('filter-search-val');
    if (hiddenSearch) {
      hiddenSearch.value = query ? query.trim() : '';
    }
    submitBackendFilter();
  }

  function clearTopRoomSearch() {
    const input = document.getElementById('top-room-search');
    const clearBtn = document.getElementById('top-search-clear-btn');
    if (input) input.value = '';
    if (clearBtn) clearBtn.classList.add('hidden');
    const hiddenSearch = document.getElementById('filter-search-val');
    const hadSearch = hiddenSearch && hiddenSearch.value.trim().length > 0;
    if (hiddenSearch) hiddenSearch.value = '';
    
    // Reset cards visibility
    document.querySelectorAll('.rack-card').forEach(c => c.style.display = '');

    if (hadSearch) {
      submitBackendFilter();
    }
  }

  function onLiveRoomSearch(query) {
    const clearBtn = document.getElementById('top-search-clear-btn');
    if (clearBtn) {
      if (query && query.length > 0) {
        clearBtn.classList.remove('hidden');
      } else {
        clearBtn.classList.add('hidden');
      }
    }

    const q = (query || '').toLowerCase().trim();
    const hiddenSearch = document.getElementById('filter-search-val');
    if (hiddenSearch) hiddenSearch.value = q;

    // Instant local filtering
    const cards = document.querySelectorAll('.rack-card');
    cards.forEach(card => {
      if (!q) {
        card.style.display = '';
        return;
      }
      const roomNum = (card.getAttribute('data-room') || '').toLowerCase();
      const guestName = (card.getAttribute('data-guest') || '').toLowerCase();
      const type = (card.getAttribute('data-type') || '').toLowerCase();
      const category = (card.getAttribute('data-category') || '').toLowerCase();

      if (roomNum.includes(q) || guestName.includes(q) || type.includes(q) || category.includes(q)) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  }

  function printRack() {
    window.print();
  }

  function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
    if (id === 'reserve-modal') {
      try {
        setInitialDateTime();
        initPlanSelect2();
        if (typeof refreshPlanSelectDisplay === 'function') refreshPlanSelectDisplay();
        if (typeof calculateReservationBilling === 'function') calculateReservationBilling();
        if (typeof updateReservationStatusUI === 'function') updateReservationStatusUI();
        if (typeof initGuestUploadSession === 'function') initGuestUploadSession();
      } catch (e) {
        console.error('Error in setInitialDateTime:', e);
      }
    }
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
    if (id === 'reserve-modal') {
      const conflictBox = document.querySelector('.room-conflict-alert-box');
      if (conflictBox) conflictBox.style.display = 'none';
      const datesAlert = document.getElementById('dates-conflict-alert');
      if (datesAlert) datesAlert.style.display = 'none';
      if (typeof stopGuestUploadSession === 'function') stopGuestUploadSession();
    }
  }

  function checkInFromRoomModal(roomNo) {
    closeModal('room-modal');
    setTimeout(() => {
      openReservationModal(roomNo);
    }, 60);
  }

  function setRoomFieldsLocked(block, isLocked, roomNo = null) {
    if (!block) return;
    const catSel = block.querySelector('.select-room-category');
    const flSel = block.querySelector('.select-room-floor');
    const rmSel = block.querySelector('.select-room-no');
    const lockIndicator = block.querySelector('.room-lock-indicator') || document.querySelector('.room-lock-indicator');

    [catSel, flSel, rmSel].forEach(el => {
      if (!el) return;
      if (isLocked) {
        el.disabled = true;
        el.style.backgroundColor = '#f1f5f9';
        el.style.cursor = 'not-allowed';
        el.style.color = '#475569';
        el.style.borderColor = '#cbd5e1';
        el.setAttribute('title', 'Room details are pre-selected. Cannot be changed manually unless selecting an alternative available room on date conflict.');
      } else {
        el.disabled = false;
        el.style.backgroundColor = '#ffffff';
        el.style.cursor = 'pointer';
        el.style.color = '#1e293b';
        el.style.borderColor = '#cbd5e1';
        el.removeAttribute('title');
      }
    });

    if (lockIndicator) {
      if (isLocked) {
        lockIndicator.style.display = 'inline-flex';
        const lbl = lockIndicator.querySelector('.room-lock-text');
        if (lbl) {
          lbl.textContent = roomNo ? `Room #${roomNo} Locked` : 'Room Locked';
        }
      } else {
        lockIndicator.style.display = 'none';
      }
    }
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
    // Immediately open reserve modal so user sees the form without lag
    openModal('reserve-modal');

    try {
      setInitialDateTime();
      if (typeof updateReservationStatusUI === 'function') updateReservationStatusUI();
    } catch (e) {
      console.error('Error in setInitialDateTime:', e);
    }

    try {
      fetchNextReserveId();
    } catch (e) {
      console.error('Error in fetchNextReserveId:', e);
    }
    
    try {
      const firstGuest = document.querySelector('.guest-block');

      if (preselectRoomNo || preselectCatId || preselectFloorId) {
        // Temporarily unlock to apply option selections smoothly
        if (firstGuest) setRoomFieldsLocked(firstGuest, false);

        let match = null;
        if (preselectRoomNo && window.allPmsRooms) {
          match = window.allPmsRooms.find(r => String(r.room) === String(preselectRoomNo) || String(r.id) === String(preselectRoomNo));
          if (match) {
            preselectCatId = preselectCatId || match.category_id;
            preselectFloorId = preselectFloorId || match.floor_id || match.floor;
          }
        }

        if (firstGuest) {
          const catSel = firstGuest.querySelector('.select-room-category');
          const flSel = firstGuest.querySelector('.select-room-floor');
          const rmSel = firstGuest.querySelector('.select-room-no');

          if (catSel && preselectCatId) {
            catSel.value = preselectCatId;
            if (!catSel.value && match) {
              for (let opt of catSel.options) {
                if (opt.value == match.category_id || 
                    (match.category && opt.text.trim().toLowerCase() === match.category.toLowerCase()) ||
                    (match.type && opt.text.trim().toLowerCase() === match.type.toLowerCase())) {
                  catSel.value = opt.value;
                  break;
                }
              }
            }
            onCategoryChange(catSel);
          }

          if (flSel && (preselectFloorId || match)) {
            const targetFloorId = preselectFloorId || (match ? (match.floor_id || match.floor) : '');
            flSel.value = targetFloorId;
            if (!flSel.value && match) {
              for (let opt of flSel.options) {
                if (opt.value == match.floor_id || opt.value == match.floor || opt.dataset.floorNo == match.floor) {
                  flSel.value = opt.value;
                  break;
                }
              }
            }
            onFloorChange(flSel);
          }

          if (rmSel && (preselectRoomNo || match)) {
            const targetRoomNo = match ? String(match.room) : String(preselectRoomNo);
            const targetRoomId = match ? String(match.id) : String(preselectRoomNo);

            let foundOpt = false;
            for (let opt of rmSel.options) {
              if (opt.value === targetRoomId || opt.value === targetRoomNo || opt.dataset.roomNo === targetRoomNo) {
                rmSel.value = opt.value;
                foundOpt = true;
                break;
              }
            }

            if (!foundOpt && match) {
              const opt = document.createElement('option');
              opt.value = match.id || match.room;
              opt.dataset.roomNo = match.room;
              opt.dataset.rate = match.rate;
              opt.dataset.bedding = match.bedding || 'King Size Master (72x78)';
              opt.dataset.maxAdults = match.max_adults || 2;
              opt.dataset.maxChildren = match.max_children || 1;
              opt.dataset.maxPax = match.max_pax || 3;
              opt.dataset.planId = match.plan_id || '';
              opt.dataset.planName = match.plan_name || '';
              opt.dataset.planPrice = match.plan_price || 0;
              opt.textContent = `Room #${match.room} (₹${Number(match.rate).toLocaleString()}) • ${(match.status || 'available').toUpperCase()}`;
              opt.style.fontWeight = 'bold';
              opt.style.color = (match.status === 'available') ? '#059669' : '#ef4444';
              rmSel.appendChild(opt);
              rmSel.value = opt.value;
            }

            onRoomChange(rmSel);
          }

          // Lock fields since opened through a specific room
          const finalRoomNo = match ? match.room : preselectRoomNo;
          setRoomFieldsLocked(firstGuest, true, finalRoomNo);
        }
      } else {
        // General reservation: unlock fields so staff can choose room freely
        if (firstGuest) {
          setRoomFieldsLocked(firstGuest, false);
        }
      }
    } catch (e) {
      console.error('Error preselecting room:', e);
    }

    setTimeout(() => {
      try {
        checkSelectedRoomAvailability();
        const checkedResType = document.querySelector('input[name="res_type"]:checked');
        const isComp = (checkedResType?.dataset?.slug || checkedResType?.value || '').toLowerCase() === 'company';
        if (isComp) {
          applyCompanyDiscountFromSelect();
        } else {
          calculateReservationBilling();
        }
      } catch (err) {
        console.error('Error in post-reservation timeout:', err);
      }
    }, 60);
  }

  function getRegistrationTypeRosetteBadgeHtml(typeName, size = 46) {
    const t = (typeName || 'New').toLowerCase();
    let badgeText = 'NEW';
    let c1 = '#10b981'; // Green
    let c2 = '#059669';
    let glowColor = 'rgba(16, 185, 129, 0.35)';

    if (t.includes('regular')) {
      badgeText = 'REGULAR';
      c1 = '#3b82f6'; // Blue
      c2 = '#1d4ed8';
      glowColor = 'rgba(37, 99, 235, 0.35)';
    } else if (t.includes('company') || t.includes('corporate')) {
      badgeText = 'COMPANY';
      c1 = '#8b5cf6'; // Purple
      c2 = '#6d28d9';
      glowColor = 'rgba(124, 58, 237, 0.35)';
    } else if (t.includes('privilege') || t.includes('vip')) {
      badgeText = 'PRIVILEGE';
      c1 = '#f59e0b'; // Amber Gold
      c2 = '#b45309';
      glowColor = 'rgba(217, 119, 6, 0.35)';
    }

    const uid = 'rosette-' + Math.random().toString(36).substring(2, 9);
    const fontSize = badgeText.length > 7 ? 'text-[7px]' : (badgeText.length > 5 ? 'text-[8px]' : 'text-[9.5px]');

    return `
      <div class="rosette-reg-badge relative inline-flex items-center justify-center shrink-0 cursor-default select-none" style="width: ${size}px; height: ${size}px; filter: drop-shadow(0 3px 6px ${glowColor});" title="Guest Registration: ${badgeText}">
        <svg viewBox="0 0 100 100" class="w-full h-full block overflow-visible">
          <defs>
            <linearGradient id="${uid}" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="${c1}"/>
              <stop offset="100%" stop-color="${c2}"/>
            </linearGradient>
          </defs>
          <!-- 8-Point Smooth Scalloped Rosette Seal Shape -->
          <path d="M 50 2 C 57 2 62 11 68 14 C 74 17 84 13 89 19 C 94 25 90 35 93 41 C 96 47 105 52 105 59 C 105 66 96 71 93 77 C 90 83 94 93 89 99 C 84 105 74 101 68 104 C 62 107 57 116 50 116 C 43 116 38 107 32 104 C 26 101 16 105 11 99 C 6 93 10 83 7 77 C 4 71 -5 66 -5 59 C -5 52 4 47 7 41 C 10 35 6 25 11 19 C 16 13 26 17 32 14 C 38 11 43 2 50 2 Z" transform="scale(0.83) translate(10, 0)" fill="url(#${uid})" stroke="#ffffff" stroke-width="2.2"/>
          <circle cx="50" cy="50" r="30" fill="none" stroke="rgba(255,255,255,0.45)" stroke-dasharray="3,2" stroke-width="1.2"/>
        </svg>
        <span class="absolute text-white ${fontSize} font-black tracking-wider uppercase text-center leading-none pointer-events-none px-1 font-sans drop-shadow-sm">
          ${badgeText}
        </span>
      </div>
    `;
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
    const isBlocked = status === 'blocked' || (opDisplay && (/block|blok|maintenance/i.test(opDisplay)));
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

    const guestsList = Array.isArray(roomData.guests) && roomData.guests.length > 0
      ? roomData.guests
      : (guestObj ? [guestObj] : []);

    const guestRegType = (guestsList.length > 0 ? guestsList[0].registration_type : null) || roomData.registration_type || 'New';
    const primaryGuest = (isOccupied && guestsList.length > 0) ? guestsList[0] : null;

    // Meal Plan Extraction (Guest-selected plan has definitive precedence when guest is residing)
    let mealPlanName = '';
    let mealPlanPrice = 0;
    let mealPlanOriginalPrice = 0;
    let mealPlanDiscount = 0;

    if (primaryGuest && primaryGuest.plan_name !== undefined && primaryGuest.plan_name !== null && primaryGuest.plan_name !== '') {
      mealPlanName = primaryGuest.plan_name;
      mealPlanPrice = Number(primaryGuest.plan_price || 0);
      mealPlanOriginalPrice = Number(primaryGuest.plan_original_price !== undefined ? primaryGuest.plan_original_price : mealPlanPrice);
      mealPlanDiscount = Number(primaryGuest.plan_discount_amount !== undefined ? primaryGuest.plan_discount_amount : Math.max(0, mealPlanOriginalPrice - mealPlanPrice));
    } else if (primaryGuest) {
      mealPlanName = 'None';
      mealPlanPrice = 0;
      mealPlanOriginalPrice = 0;
      mealPlanDiscount = 0;
    } else if (roomData.plan_name) {
      mealPlanName = roomData.plan_name;
      mealPlanPrice = Number(roomData.plan_price || 0);
      mealPlanOriginalPrice = Number(roomData.plan_original_price !== undefined ? roomData.plan_original_price : mealPlanPrice);
      mealPlanDiscount = 0;
    } else {
      mealPlanName = 'None';
      mealPlanPrice = 0;
      mealPlanOriginalPrice = 0;
      mealPlanDiscount = 0;
    }

    function formatMealPlanDisplay(name, price, origPrice) {
      if (!name) return 'None';
      const clean = String(name).trim();
      const lower = clean.toLowerCase();
      const effPrice = Number(price || 0);
      const original = Number(origPrice !== undefined ? origPrice : effPrice);
      if (lower === 'none' || lower === 'no meal plan' || lower === 'room only (ep)' || lower === 'ep' || (effPrice <= 0 && original <= 0)) {
        return clean;
      }
      return `${clean} (+₹${effPrice.toLocaleString('en-IN')})`;
    }

    function formatMealPlanDisplayHtml(name, price, origPrice) {
      if (!name) return 'None';
      const clean = String(name).trim();
      const lower = clean.toLowerCase();
      const effPrice = Number(price || 0);
      const original = Number(origPrice !== undefined ? origPrice : effPrice);
      if (lower === 'none' || lower === 'no meal plan' || lower === 'room only (ep)' || lower === 'ep' || (effPrice <= 0 && original <= 0)) {
        return escapeHtml(clean);
      }
      if (original > effPrice) {
        return `${escapeHtml(clean)} (+<span class="line-through opacity-65 mr-1 font-semibold">₹${original.toLocaleString('en-IN')}</span><strong class="text-inherit">₹${effPrice.toLocaleString('en-IN')}</strong>)`;
      }
      return `${escapeHtml(clean)} (+₹${effPrice.toLocaleString('en-IN')})`;
    }

    const mealPlanDisplay = formatMealPlanDisplay(mealPlanName, mealPlanPrice, mealPlanOriginalPrice);
    const mealPlanDisplayHtml = formatMealPlanDisplayHtml(mealPlanName, mealPlanPrice, mealPlanOriginalPrice);

    const checkInDisplay = primaryGuest?.checkin_display || roomData.checkin_display || (primaryGuest?.reserve_date ? `${primaryGuest.reserve_date}${primaryGuest.reserve_time ? ' • ' + primaryGuest.reserve_time : ''}` : '');
    const checkOutDisplay = primaryGuest?.checkout_display || roomData.checkout_display || (primaryGuest?.checkout_date ? `${primaryGuest.checkout_date}${primaryGuest.checkout_time ? ' • ' + primaryGuest.checkout_time : ''}` : '');
    const totalNights = primaryGuest?.total_nights || roomData.total_nights || '';

    if (isOccupied) {
      const headerBadge = getRegistrationTypeRosetteBadgeHtml(guestRegType, 34);
      titleEl.innerHTML = `
        <div class="flex items-center justify-between w-full flex-wrap gap-2">
          <div class="flex items-center gap-2.5">
            ${headerBadge}
            <span class="text-sm font-extrabold text-white">Room Details • Room ${escapeHtml(roomNo)}</span>
          </div>
          ${(checkInDisplay || checkOutDisplay) ? `
            <div class="flex items-center gap-2 text-[11px] font-bold mr-2 bg-black/20 px-3 py-1 rounded-full border border-white/20">
              ${checkInDisplay ? `<span class="inline-flex items-center gap-1.5 text-white"><i class="fa-solid fa-plane-arrival text-emerald-400"></i> In: <span class="font-extrabold">${escapeHtml(checkInDisplay)}</span></span>` : ''}
              ${checkInDisplay && checkOutDisplay ? `<span class="text-white/40">|</span>` : ''}
              ${checkOutDisplay ? `<span class="inline-flex items-center gap-1.5 text-white"><i class="fa-solid fa-plane-departure text-rose-400"></i> Out: <span class="font-extrabold">${escapeHtml(checkOutDisplay)}</span></span>` : ''}
              ${totalNights ? `<span class="bg-amber-400 text-amber-950 px-1.5 py-0.5 rounded-full text-[10px] font-black ml-0.5"><i class="fa-solid fa-moon"></i> ${totalNights}N</span>` : ''}
            </div>
          ` : ''}
        </div>
      `;
    } else {
      titleEl.innerHTML = `
        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-sm">
          <i class="fa-solid fa-door-open"></i>
        </div>
        <span class="text-sm font-extrabold text-white">Room Details • Room ${escapeHtml(roomNo)}</span>
      `;
    }

    const amenitiesHtml = amenities.map(amn => {
      return `<span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[11px] font-bold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5">
        <i class="fa-solid fa-circle-check text-[9px] text-emerald-600"></i> ${escapeHtml(amn)}
      </span>`;
    }).join('');

    let guestSectionHtml = '';
    let topReservationBadgeHtml = '';

    if (isOccupied && guestsList.length > 0) {
      const primaryGuest = guestsList[0];
      const reservationId = primaryGuest.reserve_id || ('RES-2026-00' + roomNo);
      const extraServicesTotal = Number(primaryGuest.extra_service_charges || roomData.extra_service_charges || 0);
      const serviceOrders = Array.isArray(primaryGuest.room_service_orders) && primaryGuest.room_service_orders.length > 0
        ? primaryGuest.room_service_orders
        : (Array.isArray(roomData.room_service_orders) ? roomData.room_service_orders : []);

      const baseBalance = primaryGuest.balance !== undefined ? Number(primaryGuest.balance) : 0;
      // Add extra services to Balance Due (Payment Remaining)
      const totalBalance = baseBalance + extraServicesTotal;
      const advancePaid = primaryGuest.advance !== undefined ? Number(primaryGuest.advance) : 0;
      const overallState = primaryGuest.state || 'Confirmed';

      topReservationBadgeHtml = `
        <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-2 py-0.5 rounded-md font-mono text-[11px] font-extrabold inline-flex items-center gap-1" title="Reservation ID">
          <i class="fa-solid fa-receipt text-indigo-500"></i> ${escapeHtml(reservationId)}
        </span>
        <span class="bg-orange-50 text-orange-700 border border-orange-200 px-2 py-0.5 rounded-md text-[11px] font-extrabold inline-flex items-center gap-1" title="Meal Plan: ${escapeHtml(mealPlanDisplay)}">
          <i class="fa-solid fa-utensils text-orange-600 text-[10px]"></i> ${mealPlanDisplayHtml}
        </span>
        ${extraServicesTotal > 0 ? `
        <span class="bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-md text-[11px] font-extrabold inline-flex items-center gap-1" title="Extra Services Total">
          <i class="fa-solid fa-bell-concierge text-amber-600 text-[10px]"></i> Services: +₹${extraServicesTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
        </span>
        ` : ''}
      `;

      const grossAmt = Number(primaryGuest.total_amount || 0);
      const discAmt = Number(primaryGuest.discount_amount || 0);
      const discPct = Number(primaryGuest.discount_percentage || 0);
      const gstType = primaryGuest.gst_type || 'exclusive';
      const gstPct = Number(primaryGuest.gst_percentage || 0);
      const gstAmt = Number(primaryGuest.gst_amount || 0);
      const basePayable = Number(primaryGuest.payable_amount || (grossAmt > 0 ? (grossAmt - discAmt + (gstType === 'inclusive' ? 0 : gstAmt)) : baseBalance + advancePaid));
      const payableAmt = basePayable + extraServicesTotal;

      window.currentModalRoomGuest = primaryGuest;

      let servicesOrdersHtml = '';
      if (serviceOrders.length > 0) {
        const orderRows = serviceOrders.map(ro => {
          const itemsSummary = (ro.items || []).map(it => `${it.quantity}x ${escapeHtml(it.item_name)}`).join(', ') || 'Room Service & Amenity Items';
          const isPaid = (ro.payment_status && ro.payment_status !== 'Charged to Room Folio' && ro.payment_status !== 'Pending');
          return `
            <div class="bg-white border border-orange-200 rounded-lg p-2 px-3 flex justify-between items-center gap-2.5 text-[11px]">
              <div class="flex flex-col gap-0.5 min-w-0">
                <div class="flex items-center gap-1.5 flex-wrap">
                  <strong class="text-orange-700 font-mono text-xs">#${escapeHtml(ro.order_number)}</strong>
                  <span class="text-[9px] px-1.5 py-0.5 rounded font-bold ${isPaid ? 'bg-emerald-100 text-emerald-800' : 'bg-orange-100 text-orange-800'}">${escapeHtml(ro.payment_status || ro.order_status || 'Received')}</span>
                  <span class="text-[10px] text-slate-500">${escapeHtml(ro.date)}</span>
                </div>
                <div class="text-slate-700 text-[11px] truncate">${itemsSummary}</div>
              </div>
              <div class="text-right shrink-0">
                <div class="font-mono font-black text-sm ${isPaid ? 'text-emerald-600' : 'text-orange-600'}">₹ ${Number(ro.total_amount || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                ${isPaid 
                  ? `<span class="text-[9px] text-emerald-600 font-bold"><i class="fa-solid fa-circle-check"></i> PAID (Direct)</span>`
                  : `<span class="text-[9px] text-orange-600 font-bold"><i class="fa-solid fa-file-invoice"></i> Charged to Room</span>`
                }
              </div>
            </div>
          `;
        }).join('');

        servicesOrdersHtml = `
          <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 px-3.5 mb-3">
            <div class="flex justify-between items-center mb-2 border-b border-dashed border-amber-300 pb-1.5">
              <span class="text-[11px] font-extrabold text-amber-800 uppercase tracking-wide flex items-center gap-1.5">
                <i class="fa-solid fa-bell-concierge"></i> In-Room Dining &amp; Extra Services (${serviceOrders.length} Order${serviceOrders.length > 1 ? 's' : ''})
              </span>
              <strong class="font-mono text-amber-800 text-sm font-black">
                Total: ₹ ${extraServicesTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
              </strong>
            </div>
            <div class="flex flex-col gap-1.5">
              ${orderRows}
            </div>
          </div>
        `;
      }

      const topReservationSummaryBar = `
        <div class="bg-linear-to-br from-slate-50 to-indigo-50/40 border border-indigo-100 rounded-xl p-3 px-3.5 mb-3 shadow-xs">
          <div class="flex justify-between items-center flex-wrap gap-2.5 mb-2">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shadow-xs">
                <i class="fa-solid fa-receipt"></i>
              </div>
              <div>
                <div class="text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">Reservation ID</div>
                <div class="font-mono text-sm font-black text-indigo-950 tracking-wider">${escapeHtml(reservationId)}</div>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="text-right">
                <div class="text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">Advance Paid</div>
                <div class="font-mono text-xs font-extrabold text-emerald-600 leading-tight">₹ ${advancePaid.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
              </div>
              <div class="text-right border-l border-slate-200 pl-2.5">
                <div class="text-[9px] font-extrabold text-slate-500 uppercase tracking-wider">Balance Due</div>
                <div class="font-mono text-sm font-black ${totalBalance > 0 ? 'text-rose-600' : 'text-emerald-600'} leading-tight">₹ ${totalBalance.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                ${extraServicesTotal > 0 ? `<div class="text-[9px] font-bold text-orange-600 mt-0.5">(Extra Services: +₹${extraServicesTotal.toLocaleString('en-IN', {minimumFractionDigits: 2})})</div>` : ''}
              </div>
            </div>
          </div>

          <!-- Clean & Simple Billing Breakdown -->
          <div class="pt-2 border-t border-dashed border-slate-200 flex flex-wrap items-stretch gap-2 text-[11px]">
            <div class="flex-1 min-w-[70px] bg-white p-1.5 px-2 rounded-lg border border-slate-200">
              <span class="text-[9px] font-bold text-slate-500 uppercase block">Gross</span>
              <span class="font-extrabold text-slate-800 font-mono">₹ ${grossAmt > 0 ? grossAmt.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : rate.toLocaleString('en-IN', {minimumFractionDigits: 2})}</span>
            </div>
            <div class="flex-1 min-w-[70px] bg-white p-1.5 px-2 rounded-lg border border-slate-200">
              <span class="text-[9px] font-bold text-emerald-600 uppercase block">Discount</span>
              <span class="font-extrabold text-emerald-600 font-mono">${discAmt > 0 ? `- ₹ ${discAmt.toLocaleString('en-IN', {minimumFractionDigits: 2})} (${discPct}%)` : '₹ 0.00'}</span>
            </div>
            <div class="flex-1 min-w-[70px] bg-white p-1.5 px-2 rounded-lg border border-slate-200">
              <span class="text-[9px] font-bold text-indigo-600 uppercase block">GST (${gstPct}%)</span>
              <span class="font-extrabold text-indigo-600 font-mono">${gstAmt > 0 ? `${gstType === 'inclusive' ? 'Incl. ' : '+ '}₹ ${gstAmt.toLocaleString('en-IN', {minimumFractionDigits: 2})}` : '₹ 0.00'}</span>
            </div>
            ${extraServicesTotal > 0 ? `
            <div class="flex-1 min-w-[90px] bg-amber-50 p-1.5 px-2 rounded-lg border border-amber-200">
              <span class="text-[9px] font-extrabold text-amber-800 uppercase block whitespace-nowrap"><i class="fa-solid fa-bell-concierge"></i> Extra Services</span>
              <span class="font-black text-amber-800 font-mono whitespace-nowrap">+ ₹ ${extraServicesTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            </div>
            ` : ''}
            <div class="bg-orange-50 p-1.5 px-2.5 rounded-lg border border-orange-200 shrink-0 w-max min-w-max" style="width: max-content; min-width: max-content;">
              <span class="text-[9px] font-extrabold text-orange-800 uppercase block whitespace-nowrap"><i class="fa-solid fa-utensils"></i> Meal Plan</span>
              <span class="font-extrabold text-orange-900 text-[11px] whitespace-nowrap block" title="${escapeHtml(mealPlanDisplay)}">${mealPlanDisplayHtml}</span>
            </div>
            <div class="flex-1 min-w-[70px] bg-white p-1.5 px-2 rounded-lg border border-slate-200">
              <span class="text-[9px] font-extrabold text-indigo-700 uppercase block">Payable</span>
              <span class="font-black text-indigo-700 font-mono">₹ ${payableAmt.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            </div>
          </div>
        </div>
      `;

      if (guestsList.length === 1) {
        // Single Guest Layout
        const g = primaryGuest;

        guestSectionHtml = `
          <div class="bg-white border border-slate-200 rounded-xl p-3.5 sm:p-4 mb-3.5 shadow-xs">
            <div class="flex justify-between items-center mb-2.5 border-b border-slate-100 pb-2">
              <h4 class="m-0 text-xs font-black text-rose-600 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-user-check"></i> Residing Guest Information
              </h4>
              <div class="flex items-center gap-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">${escapeHtml(g.state || overallState)}</span>
              </div>
            </div>
            
            ${topReservationSummaryBar}

            ${servicesOrdersHtml}

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 sm:p-3.5">
              <div class="flex items-center gap-2 flex-wrap mb-2">
                <div class="text-sm font-extrabold text-slate-900">${escapeHtml(g.name)}</div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Primary Guest</span>
                ${g.company ? `<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200"><i class="fa-solid fa-building"></i> ${escapeHtml(g.company)}</span>` : ''}
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-1.5 sm:gap-3 text-xs text-slate-500">
                ${g.mobile ? `<div><strong class="text-slate-600"><i class="fa-solid fa-phone text-indigo-500 w-3.5"></i> Mobile:</strong> <span class="text-slate-800 font-semibold">${escapeHtml(g.mobile)}</span></div>` : ''}
                ${g.email ? `<div><strong class="text-slate-600"><i class="fa-solid fa-envelope text-indigo-500 w-3.5"></i> Email:</strong> <span class="text-slate-800 font-semibold">${escapeHtml(g.email)}</span></div>` : ''}
                ${g.id_card_number ? `<div><strong class="text-slate-600"><i class="fa-solid fa-id-card text-indigo-500 w-3.5"></i> ${escapeHtml(g.id_card_type || 'ID Card')}:</strong> <span class="font-mono font-bold text-slate-900">${escapeHtml(g.id_card_number)}</span></div>` : ''}
                ${g.city || g.address ? `<div><strong class="text-slate-600"><i class="fa-solid fa-location-dot text-indigo-500 w-3.5"></i> City / Address:</strong> <span class="text-slate-800 font-semibold">${escapeHtml(g.city || g.address)}</span></div>` : ''}
                <div class="col-span-1 sm:col-span-2 md:col-span-2 w-max max-w-full"><strong class="text-slate-600"><i class="fa-solid fa-utensils text-orange-600 w-3.5"></i> Meal Plan:</strong> <span class="text-orange-700 font-extrabold bg-orange-50 px-1.5 py-0.5 rounded border border-orange-200 inline-block whitespace-nowrap">${formatMealPlanDisplayHtml(g.plan_name, g.plan_price, g.plan_original_price)}</span></div>
                ${g.has_privilege_card && g.privilege_card_no ? `<div><strong class="text-slate-600"><i class="fa-solid fa-star text-amber-500 w-3.5"></i> Privilege:</strong> <span class="text-amber-700 font-bold">${escapeHtml(g.privilege_card_no)}</span></div>` : ''}
              </div>
            </div>
          </div>
        `;
      } else {
        // Multiple Guests Layout (Rendered one by one)
        const guestCardsHtml = guestsList.map((g, idx) => `
          <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 sm:p-3.5 flex flex-col gap-1.5">
            <div class="flex justify-between items-center border-b border-dashed border-slate-200 pb-1.5">
              <div class="flex items-center gap-2.5">
                <span class="w-5.5 h-5.5 rounded-full bg-indigo-100 text-indigo-700 text-[11px] font-black inline-flex items-center justify-center">${idx + 1}</span>
                <span class="text-sm font-extrabold text-slate-900">${escapeHtml(g.name)}</span>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold ${idx === 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600 border border-slate-200'}">
                  ${idx === 0 ? 'Primary Guest' : 'Additional Guest'}
                </span>
                ${g.company ? `<span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200"><i class="fa-solid fa-building"></i> ${escapeHtml(g.company)}</span>` : ''}
              </div>
              <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">${escapeHtml(g.state || overallState)}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-1.5 sm:gap-3 text-xs text-slate-500 mt-0.5">
              ${g.mobile ? `<div><strong class="text-slate-600"><i class="fa-solid fa-phone text-indigo-500 w-3.5"></i> Mobile:</strong> <span class="font-semibold text-slate-800">${escapeHtml(g.mobile)}</span></div>` : ''}
              ${g.email ? `<div><strong class="text-slate-600"><i class="fa-solid fa-envelope text-indigo-500 w-3.5"></i> Email:</strong> <span class="font-semibold text-slate-800">${escapeHtml(g.email)}</span></div>` : ''}
              ${g.id_card_number ? `<div><strong class="text-slate-600"><i class="fa-solid fa-id-card text-indigo-500 w-3.5"></i> ${escapeHtml(g.id_card_type || 'ID Card')}:</strong> <span class="font-mono font-bold text-slate-900">${escapeHtml(g.id_card_number)}</span></div>` : ''}
              ${g.city || g.address ? `<div><strong class="text-slate-600"><i class="fa-solid fa-location-dot text-indigo-500 w-3.5"></i> City / Address:</strong> <span class="font-semibold text-slate-800">${escapeHtml(g.city || g.address)}</span></div>` : ''}
              <div class="col-span-1 sm:col-span-2 md:col-span-2 w-max max-w-full"><strong class="text-slate-600"><i class="fa-solid fa-utensils text-orange-600 w-3.5"></i> Meal Plan:</strong> <span class="text-orange-700 font-extrabold bg-orange-50 px-1.5 py-0.5 rounded border border-orange-200 inline-block whitespace-nowrap">${formatMealPlanDisplayHtml(g.plan_name, g.plan_price, g.plan_original_price)}</span></div>
              ${g.has_privilege_card && g.privilege_card_no ? `<div><strong class="text-slate-600"><i class="fa-solid fa-star text-amber-500 w-3.5"></i> Privilege:</strong> <span class="text-amber-700 font-bold">${escapeHtml(g.privilege_card_no)}</span></div>` : ''}
            </div>
          </div>
        `).join('');

        guestSectionHtml = `
          <div class="bg-white border border-slate-200 rounded-xl p-3.5 sm:p-4 mb-3.5 shadow-xs">
            
            <!-- Header with Guest Count Badge -->
            <div class="flex justify-between items-center mb-2.5 border-b border-slate-100 pb-2">
              <h4 class="m-0 text-xs font-black text-rose-600 uppercase tracking-wider flex items-center gap-1.5">
                <i class="fa-solid fa-users"></i> Residing Guests (${guestsList.length} Guests)
              </h4>
              <div class="flex items-center gap-1.5">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                  <i class="fa-solid fa-user-group mr-1"></i> ${guestsList.length} Guests
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">${escapeHtml(overallState)}</span>
              </div>
            </div>

            <!-- Prominent Top Reservation & Financials Bar -->
            ${topReservationSummaryBar}

            ${servicesOrdersHtml}

            <!-- List of All Guests One by One -->
            <div class="flex flex-col gap-2">
              ${guestCardsHtml}
            </div>

          </div>
        `;
      }
    }

    bodyEl.innerHTML = `
      <!-- Top Highlights Banner -->
      <div class="bg-linear-to-br from-indigo-50/60 to-emerald-50/40 border border-indigo-100 rounded-2xl p-4 sm:p-5 mb-3.5 flex justify-between items-center flex-wrap gap-3.5">
        <div class="flex items-center gap-3.5">
          <div class="w-12 h-12 rounded-xl bg-linear-to-br from-indigo-600 to-indigo-700 text-white flex flex-col items-center justify-center font-mono font-black text-base shadow-sm">
            <span class="text-[9px] opacity-85 font-sans font-bold leading-none">ROOM</span>
            <span class="leading-tight">${escapeHtml(roomNo)}</span>
          </div>
          <div>
            <div class="text-base font-extrabold text-slate-900 flex items-center gap-2 flex-wrap">
              ${escapeHtml(category)}
              <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase ${statusBadgeClass}">${escapeHtml(statusBadgeText)}</span>
              ${topReservationBadgeHtml}
            </div>
            <div class="text-xs text-slate-500 font-semibold mt-1 flex items-center gap-1.5">
              <i class="fa-solid fa-layer-group text-indigo-600"></i> ${escapeHtml(floorName)}
            </div>
          </div>
        </div>

        <!-- Highlighted Right Side Price & State Card -->
        <div class="flex flex-col items-end justify-center gap-1 bg-white p-2.5 px-4.5 rounded-xl border border-slate-200 shadow-xs">
          <div class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Base Tariff</div>
          <div class="font-mono text-xl sm:text-2xl font-black text-indigo-600 leading-tight">
            ₹ ${rate.toLocaleString()} <span class="text-xs font-semibold text-slate-400">/ night</span>
          </div>
          <div class="inline-flex items-center gap-1.5 ${isBlocked ? 'bg-rose-50 text-rose-700 border-rose-200' : (isOccupied ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200')} border px-2.5 py-0.5 rounded-full text-[11px] font-bold mt-0.5">
            <span class="w-1.5 h-1.5 rounded-full ${isBlocked ? 'bg-rose-500' : (isOccupied ? 'bg-blue-500' : 'bg-emerald-500')} inline-block"></span>
            ${escapeHtml(opDisplay)}
          </div>
        </div>
      </div>

      <!-- Uniform Specifications & Capacity 5-Grid (Including Meal Plan) -->
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 mb-3.5">
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 px-3">
          <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider"><i class="fa-solid fa-bed text-indigo-600 mr-1"></i> Bedding</div>
          <div class="text-xs font-extrabold text-slate-900 mt-1 truncate" title="${escapeHtml(bedding)}">${escapeHtml(bedding)}</div>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-2.5 px-3">
          <div class="text-[10px] font-extrabold text-orange-700 uppercase tracking-wider"><i class="fa-solid fa-utensils text-orange-600 mr-1"></i> Meal Plan</div>
          <div class="text-xs font-extrabold text-orange-900 mt-1" title="${escapeHtml(mealPlanDisplay)}">${mealPlanDisplayHtml}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 px-3">
          <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider"><i class="fa-solid fa-users text-emerald-600 mr-1"></i> Pax Capacity</div>
          <div class="text-xs font-extrabold text-slate-900 mt-1">${maxAdults}A, ${maxChildren}C (${maxPax} Pax)</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 px-3">
          <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider"><i class="fa-solid fa-broom text-amber-500 mr-1"></i> Housekeeping</div>
          <div class="text-xs font-extrabold ${isDirty ? 'text-amber-600' : 'text-emerald-600'} mt-1">${escapeHtml(hkDisplay)}</div>
        </div>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 px-3 col-span-2 sm:col-span-1">
          <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider"><i class="fa-solid fa-shield-halved text-indigo-600 mr-1"></i> Operational</div>
          <div class="text-xs font-extrabold ${isBlocked ? 'text-rose-600' : 'text-indigo-600'} mt-1">${escapeHtml(opDisplay)}</div>
        </div>
      </div>

      <!-- Residing Guest Info (if any) -->
      ${guestSectionHtml}

      <!-- Amenities Section -->
      <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 sm:p-3.5">
        <div class="text-[11px] font-extrabold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
          <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i> Room Amenities & Services
        </div>
        <div class="flex flex-wrap gap-1.5">
          ${amenitiesHtml}
        </div>
      </div>
    `;

    // Action Buttons Footer
    footerEl.innerHTML = `
      <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 rounded-lg shadow-xs transition-colors cursor-pointer" onclick="checkInFromRoomModal('${roomNo}')"><i class="fa-solid fa-key"></i> New Check-In</button>
      <button type="button" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg shadow-xs transition-colors cursor-pointer" onclick="closeModal('room-modal')">Close</button>
    `;

    openModal('room-modal');
  }

  // Aliases & Global Functions
  window.checkInFromRoomModal = checkInFromRoomModal;
  window.openReservationModal = openReservationModal;
  window.openRoomDetails = openRoomDetailsModal;
  window.openRoomStatusPopover = openRoomStatusPopover;
  window.closeRoomStatusPopover = closeRoomStatusPopover;
  window.onRspSelectChanged = onRspSelectChanged;
  window.rspQuickSet = rspQuickSet;

  function openRoomStatusPopover(event, triggerEl) {
    if (event) {
      event.stopPropagation();
      event.preventDefault();
    }

    const card = triggerEl ? triggerEl.closest('.rack-card') : null;
    if (!card) return;

    const popover = document.getElementById('room-card-status-popover');
    if (!popover) return;

    // Remove active state from other cards
    document.querySelectorAll('.rack-card.is-popover-active').forEach(c => c.classList.remove('is-popover-active'));
    card.classList.add('is-popover-active');

    const roomNo = card.getAttribute('data-room') || '';
    const roomId = card.getAttribute('data-room-id') || '';
    const roomType = card.querySelector('.rack-room-type')?.textContent?.trim() || card.getAttribute('data-type') || '';
    const curHkId = card.getAttribute('data-housekeeping-id') || '';
    const curHkName = card.getAttribute('data-housekeeping') || '';
    const curOpId = card.getAttribute('data-operational-id') || '';
    const curOpName = card.getAttribute('data-operational') || '';

    popover.dataset.roomNo = roomNo;
    popover.dataset.roomId = roomId;

    const numEl = document.getElementById('rsp-room-num');
    const typeEl = document.getElementById('rsp-room-type');
    if (numEl) numEl.textContent = roomNo;
    if (typeEl) typeEl.textContent = roomType;

    // Populate Housekeeping Dropdown (Front Office dashboard assigns cleaning tasks to HK team)
    const hkSel = document.getElementById('rsp-hk-select');
    if (hkSel) {
      const allHk = window.allHousekeepingStates || [];
      // Filter for assign_task types (or current state if already on the room)
      const hkList = allHk.filter(hk => {
        if (!hk.type || hk.type === 'assign_task') return true;
        if (String(hk.id) === String(curHkId) || (hk.name && hk.name.toLowerCase() === curHkName.toLowerCase())) return true;
        return false;
      });
      let hkHtml = '<option value="">Select Housekeeping Status</option>';
      hkList.forEach(hk => {
        const isSel = (String(hk.id) === String(curHkId)) || (hk.name && hk.name.toLowerCase() === curHkName.toLowerCase());
        const suffix = (hk.type === 'completed_task') ? ' (Current: Cleaned)' : '';
        hkHtml += `<option value="${hk.id}" ${isSel ? 'selected' : ''}>${escapeHtml(hk.name)}${suffix}</option>`;
      });
      hkSel.innerHTML = hkHtml;
    }

    // Populate Operational Dropdown
    const opSel = document.getElementById('rsp-op-select');
    if (opSel) {
      const opList = window.allOperationalStatuses || [];
      let opHtml = '<option value="">Select Operational Status</option>';
      opList.forEach(op => {
        const isSel = (String(op.id) === String(curOpId)) || (op.name && op.name.toLowerCase() === curOpName.toLowerCase());
        opHtml += `<option value="${op.id}" ${isSel ? 'selected' : ''}>${escapeHtml(op.name)}</option>`;
      });
      opSel.innerHTML = opHtml;
    }

    // Position Popover
    popover.style.display = 'block';
    popover.style.visibility = 'hidden';
    popover.style.position = 'fixed';
    popover.style.zIndex = '999999';

    const triggerRect = triggerEl.getBoundingClientRect();
    const cardRect = card.getBoundingClientRect();
    const popWidth = 285;
    const popHeight = popover.offsetHeight || 270;

    // Calculate left: align center with trigger or card
    let left = triggerRect.left + (triggerRect.width / 2) - (popWidth / 2);
    if (left + popWidth > window.innerWidth - 12) {
      left = window.innerWidth - popWidth - 12;
    }
    if (left < 12) left = 12;

    // Calculate top: open below if space allows, otherwise open above
    let top = triggerRect.bottom + 6;
    if (top + popHeight > window.innerHeight - 12) {
      top = triggerRect.top - popHeight - 6;
      if (top < 12) {
        top = Math.max(12, Math.min(window.innerHeight - popHeight - 12, cardRect.top));
      }
    }

    popover.style.left = `${Math.round(left)}px`;
    popover.style.top = `${Math.round(top)}px`;
    popover.style.visibility = 'visible';
  }

  function closeRoomStatusPopover(event) {
    if (event) event.stopPropagation();
    const popover = document.getElementById('room-card-status-popover');
    if (popover) {
      popover.style.display = 'none';
      popover.dataset.roomNo = '';
      popover.dataset.roomId = '';
    }
    const spinner = document.getElementById('rsp-spinner');
    if (spinner) spinner.style.display = 'none';
    document.querySelectorAll('.rack-card.is-popover-active').forEach(c => c.classList.remove('is-popover-active'));
  }

  function onRspSelectChanged(type) {
    const popover = document.getElementById('room-card-status-popover');
    if (!popover) return;
    const roomNo = popover.dataset.roomNo;
    const roomId = popover.dataset.roomId;
    if (!roomNo) return;

    const hkId = document.getElementById('rsp-hk-select')?.value || null;
    const opId = document.getElementById('rsp-op-select')?.value || null;

    updateRoomStatusAjax(roomNo, roomId, hkId, opId, true);
  }

  function rspQuickSet(action, event) {
    if (event) {
      event.stopPropagation();
      event.preventDefault();
    }
    const popover = document.getElementById('room-card-status-popover');
    if (!popover) return;
    const roomNo = popover.dataset.roomNo;
    const roomId = popover.dataset.roomId;
    if (!roomNo) return;

    const hkList = window.allHousekeepingStates || [];
    const opList = window.allOperationalStatuses || [];

    let targetHkId = null;
    let targetOpId = null;

    if (action === 'clean') {
      // Find Cleaned / Clean
      const cleanHk = hkList.find(h => /cleaned|clean|inspected/i.test(h.name)) || hkList.find(h => !/dirty/i.test(h.name));
      const activeOp = opList.find(o => /^active/i.test(o.name)) || opList.find(o => /active/i.test(o.name)) || opList.find(o => !/block|blok|maintenance|order|inactive/i.test(o.name));
      if (cleanHk) targetHkId = cleanHk.id;
      if (activeOp) targetOpId = activeOp.id;
    } else if (action === 'dirty') {
      const dirtyHk = hkList.find(h => /dirty/i.test(h.name));
      if (dirtyHk) targetHkId = dirtyHk.id;
      // If room was currently blocked, unblock it by resetting operational status to active
      const curCard = document.querySelector(`.rack-card[data-room="${roomNo}"]`);
      if (curCard && curCard.getAttribute('data-status') === 'blocked') {
        const activeOp = opList.find(o => /^active/i.test(o.name)) || opList.find(o => /active/i.test(o.name)) || opList.find(o => !/block|blok|maintenance|order|inactive/i.test(o.name));
        if (activeOp) targetOpId = activeOp.id;
      }
    } else if (action === 'maintenance') {
      const maintOp = opList.find(o => /maintenance/i.test(o.name));
      if (maintOp) targetOpId = maintOp.id;
    } else if (action === 'blocked') {
      const blockOp = opList.find(o => /blocked|blok|order/i.test(o.name)) || opList.find(o => /inactive/i.test(o.name));
      if (blockOp) targetOpId = blockOp.id;
    }

    if (targetHkId) {
      const hkSel = document.getElementById('rsp-hk-select');
      if (hkSel) {
        if (!hkSel.querySelector(`option[value="${targetHkId}"]`)) {
          const opt = document.createElement('option');
          opt.value = targetHkId;
          const matched = (window.allHousekeepingStates || []).find(h => String(h.id) === String(targetHkId));
          opt.textContent = (matched ? matched.name : 'Cleaned') + ' (Completed)';
          hkSel.appendChild(opt);
        }
        hkSel.value = targetHkId;
      }
    }
    if (targetOpId) {
      const opSel = document.getElementById('rsp-op-select');
      if (opSel) opSel.value = targetOpId;
    }

    updateRoomStatusAjax(roomNo, roomId, targetHkId, targetOpId, true);
  }

  function updateRoomStatusAjax(roomNo, roomId, hkId, opId, isFromPopover) {
    const popSpinner = document.getElementById('rsp-spinner');
    if (isFromPopover && popSpinner) {
      popSpinner.style.display = 'inline-flex';
    }

    fetch("{{ route('frontoffice.room.update-status') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        room_no: roomNo,
        room_id: roomId || null,
        housekeeping_status_id: hkId || null,
        operational_status_id: opId || null,
      })
    })
    .then(res => res.json())
    .then(data => {
      if (popSpinner) popSpinner.style.display = 'none';

      if (data.success && data.room) {
        const rData = data.room;

        // Update live rack cards on dashboard / reserve page
        const cards = document.querySelectorAll(`.rack-card[data-room="${roomNo}"]`);
        cards.forEach(card => {
          let cardClass = 'c-available';
          if (rData.status === 'occupied') cardClass = 'c-occupied';
          else if (rData.status === 'dirty') cardClass = 'c-dirty';
          else if (rData.status === 'blocked') cardClass = 'c-blocked';
          else cardClass = 'c-cleaned';

          card.className = `rack-card ${cardClass}`;
          card.setAttribute('data-status', rData.status);
          card.setAttribute('data-cleaning', rData.cleaning);
          card.setAttribute('data-operational', rData.operational_status || '');
          card.setAttribute('data-operational-id', rData.operational_status_id || '');
          card.setAttribute('data-housekeeping', rData.housekeeping_status || '');
          card.setAttribute('data-housekeeping-id', rData.housekeeping_status_id || '');

          const badge = card.querySelector('.rack-badge');
          if (badge) {
            const label = badge.querySelector('.rack-badge-label');
            const displayText = rData.cleaning || rData.status;
            if (label) {
              label.textContent = displayText;
            } else {
              badge.innerHTML = `<span class="rack-badge-label">${escapeHtml(displayText)}</span> <i class="fa-solid fa-chevron-down rack-badge-arrow"></i>`;
            }
          }

          // Update data-room-json attribute
          try {
            const curJson = JSON.parse(card.getAttribute('data-room-json') || '{}');
            curJson.status = rData.status;
            curJson.cleaning = rData.cleaning;
            curJson.operational_status = rData.operational_status;
            curJson.operational_status_id = rData.operational_status_id;
            curJson.housekeeping_status = rData.housekeeping_status;
            curJson.housekeeping_status_id = rData.housekeeping_status_id;
            card.setAttribute('data-room-json', JSON.stringify(curJson));
          } catch(e) {}
        });

        // Update in global memory window.allPmsRooms
        if (window.allPmsRooms) {
          const m = window.allPmsRooms.find(r => String(r.room) === String(roomNo) || String(r.id) === String(roomId));
          if (m) {
            m.status = rData.status;
            m.cleaning = rData.cleaning;
            m.operational_status = rData.operational_status;
            m.operational_status_id = rData.operational_status_id;
            m.housekeeping_status = rData.housekeeping_status;
            m.housekeeping_status_id = rData.housekeeping_status_id;
          }
        }

        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message || `Room #${roomNo} status updated!`, 'success');
        } else {
          alert(data.message || `Room #${roomNo} status updated!`);
        }

        if (isFromPopover) {
          closeRoomStatusPopover();
        }

        // Must reload page on status change as required
        setTimeout(() => {
          window.location.reload();
        }, 500);
      } else {
        if (typeof PmsAlert !== 'undefined') {
          PmsAlert.toast(data.message || 'Error updating status', 'error');
        } else {
          alert(data.message || 'Error updating status');
        }
      }
    })
    .catch(err => {
      if (popSpinner) popSpinner.style.display = 'none';
      console.error('Status update error:', err);
      if (typeof PmsAlert !== 'undefined') {
        PmsAlert.toast('Network error updating room status', 'error');
      }
    });
  }

  // Global listeners for status popover
  document.addEventListener('click', function(e) {
    const pop = document.getElementById('room-card-status-popover');
    if (pop && pop.style.display !== 'none') {
      if (!pop.contains(e.target) && !e.target.closest('.rack-badge') && !e.target.closest('.rack-status-quick-btn')) {
        closeRoomStatusPopover();
      }
    }
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeRoomStatusPopover();
    }
  });

  function checkoutGuest(roomNo, guestName) {
    const performCheckout = () => {
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
        if (badge) {
          const label = badge.querySelector('.rack-badge-label');
          if (label) {
            label.textContent = 'Dirty';
          } else {
            badge.innerHTML = `<span class="rack-badge-label">Dirty</span> <i class="fa-solid fa-chevron-down rack-badge-arrow"></i>`;
          }
        }
      }
      setTimeout(() => window.location.reload(), 500);
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Confirm Room Check-Out',
        html: `
          <div class="text-xs text-slate-600 leading-relaxed text-left p-1">
            <p class="m-0 mb-2.5">Are you sure you want to check out this guest?</p>
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5 px-3.5 mb-2">
              <div class="flex justify-between mb-1">
                <span class="text-slate-500 text-[11px] font-bold">Guest:</span>
                <strong class="text-slate-900 text-xs">${escapeHtml(guestName)}</strong>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500 text-[11px] font-bold">Room:</span>
                <span class="font-extrabold text-indigo-600 text-xs">Room ${escapeHtml(roomNo)}</span>
              </div>
            </div>
            <p class="m-0 text-[11px] text-slate-400"><i class="fa-solid fa-broom text-amber-500 mr-1"></i> Room will be marked as Dirty and scheduled for housekeeping.</p>
          </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: '<i class="fa-solid fa-right-from-bracket mr-1.5"></i> Yes, Check Out',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        heightAuto: false,
        scrollbarPadding: false
      }).then(res => {
        if (res.isConfirmed) performCheckout();
      });
    } else if (confirm(`Check out ${guestName} from Room ${roomNo}?`)) {
      performCheckout();
    }
  }

  function printInvoice(roomNo, guestName, folio, balance) {
    const printArea = document.getElementById('print-area');
    if (!printArea) return;
    const g = window.currentModalRoomGuest || {};
    const grossVal = Number(g.total_amount || (Number(balance) + Number(g.advance || 0)));
    const discVal = Number(g.discount_amount || 0);
    const discPct = Number(g.discount_percentage || 0);
    const gstType = g.gst_type || 'exclusive';
    const gstVal = Number(g.gst_amount || 0);
    const gstPct = Number(g.gst_percentage || 0);
    const payableVal = Number(g.payable_amount || (grossVal - discVal + (gstType === 'inclusive' ? 0 : gstVal)));
    const advVal = Number(g.advance || 0);
    const balVal = Number(balance !== undefined ? balance : (g.balance || 0));
    const formatINR = num => '₹ ' + Number(num || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    printArea.innerHTML = `
      <div class="p-10 font-sans text-slate-800">
        <div class="border-b-2 border-indigo-600 pb-4 mb-6 flex justify-between">
          <div>
            <h1 class="text-indigo-600 m-0 text-2xl font-black tracking-wide">HOTEL SAGAR SONNET</h1>
            <p class="mt-1.5 text-xs text-slate-500">Luxury Sea Beach Resort & Suites | Phone: +91 98765 43210</p>
          </div>
          <div class="text-right">
            <h2 class="m-0 text-base text-slate-800 font-bold">TAX INVOICE / FOLIO</h2>
            <p class="mt-1 text-xs"><strong>Folio:</strong> ${escapeHtml(folio)}</p>
            <p class="mt-0.5 text-xs"><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
          </div>
        </div>
        <div class="mb-6 bg-slate-50 p-3.5 border border-slate-200 rounded-lg text-xs">
          <p class="mb-1"><strong>Guest Name:</strong> ${escapeHtml(guestName)}</p>
          <p class="mb-1"><strong>Room:</strong> ${escapeHtml(roomNo)}</p>
          <p class="m-0"><strong>GSTIN:</strong> 19AAGCS7712K1Z9</p>
        </div>
        <table class="w-full border-collapse mb-6 text-xs">
          <thead>
            <tr class="bg-indigo-600 text-white">
              <th class="p-2.5 px-3 text-left">Item / Description</th>
              <th class="p-2.5 px-3 text-right">Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b border-slate-200">
              <td class="p-2.5 px-3">Room Stay Charges</td>
              <td class="p-2.5 px-3 text-right font-mono font-bold">${formatINR(grossVal)}</td>
            </tr>
            ${discVal > 0 ? `
            <tr class="border-b border-slate-200 text-emerald-600">
              <td class="p-2.5 px-3">Less: Discount (${discPct}%)</td>
              <td class="p-2.5 px-3 text-right font-mono font-bold">- ${formatINR(discVal)}</td>
            </tr>` : ''}
            ${gstVal > 0 ? `
            <tr class="border-b border-slate-200 text-indigo-600">
              <td class="p-2.5 px-3">GST / Tax (${gstPct}% ${gstType === 'inclusive' ? 'Incl.' : 'Added'})</td>
              <td class="p-2.5 px-3 text-right font-mono font-bold">${gstType === 'inclusive' ? 'Incl. ' : '+ '}${formatINR(gstVal)}</td>
            </tr>` : ''}
            <tr class="border-b border-slate-200 font-extrabold bg-slate-50">
              <td class="p-2.5 px-3">Net Payable Amount</td>
              <td class="p-2.5 px-3 text-right font-mono font-extrabold">${formatINR(payableVal)}</td>
            </tr>
            <tr class="border-b border-slate-200 text-emerald-600">
              <td class="p-2.5 px-3">Less: Advance Deposit Paid</td>
              <td class="p-2.5 px-3 text-right font-mono font-bold">- ${formatINR(advVal)}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="text-sm font-bold bg-slate-100">
              <td class="p-2.5 px-3">Net Balance Payable:</td>
              <td class="p-2.5 px-3 text-right ${balVal > 0 ? 'text-rose-600' : 'text-emerald-600'} font-mono">${formatINR(balVal)}</td>
            </tr>
          </tfoot>
        </table>
        <div class="mt-10 flex justify-between text-xs text-slate-600">
          <div>Guest Signature: __________________</div>
          <div>Cashier Signature: __________________</div>
        </div>
      </div>
    `;
    window.print();
  }

  // Master Rooms, Floors, Statuses & Categories JSON Data for Dynamic Front Office Controls
  (function() {
    const _fullHotelRooms = @json($allRoomsData ?? []);
    const _rackRooms = @json($rackRooms ?? []);
    const _roomsMap = new Map();
    (_fullHotelRooms || []).forEach(r => _roomsMap.set(String(r.id), Object.assign({}, r)));
    (_rackRooms || []).forEach(r => {
      const key = String(r.id);
      if (_roomsMap.has(key)) {
        _roomsMap.set(key, Object.assign({}, _roomsMap.get(key), r));
      } else {
        _roomsMap.set(key, r);
      }
    });
    window.allPmsRooms = Array.from(_roomsMap.values());
    if (window.allPmsRooms.length === 0) {
      window.allPmsRooms = _rackRooms.length > 0 ? _rackRooms : _fullHotelRooms;
    }
  })();
  window.allPmsFloors = @json($floors ?? []);
  window.allPmsCategories = @json($categories ?? []);
  window.allHousekeepingStates = @json($housekeepingStates ?? \App\Models\HousekeepingState::where('status', 'Active')->orderBy('name', 'asc')->get() ?? []);
  window.allOperationalStatuses = @json($operationalStatuses ?? \App\Models\OperationalStatus::where('status', 'Active')->orderBy('name', 'asc')->get() ?? []);

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

    if (!selectedCatId) {
      if (floorSelect && Array.isArray(window.allPmsFloors)) {
        window.allPmsFloors.forEach(fl => {
          const opt = document.createElement('option');
          opt.value = fl.id;
          opt.dataset.floorNo = fl.floor;
          opt.textContent = fl.name?.toLowerCase().includes('floor') ? fl.name : `Floor ${fl.floor} (${fl.name})`;
          floorSelect.appendChild(opt);
        });
      }
      return;
    }

    const matchingRooms = (window.allPmsRooms || []).filter(r => {
      return String(r.category_id) === String(selectedCatId) || 
             (r.category && String(r.category).toLowerCase() === String(selectedCatName).toLowerCase()) ||
             (r.type && String(r.type).toLowerCase() === String(selectedCatName).toLowerCase());
    });

    const floorMap = new Map();
    matchingRooms.forEach(r => {
      const flId = r.floor_id || r.floor;
      if (!floorMap.has(String(flId))) {
        let fName = r.floor_name || ('Floor ' + r.floor);
        if (fName.toLowerCase().startsWith('floor ' + r.floor)) {
          // Keep as is e.g. Floor 5 (Royal Penthouse)
        } else {
          fName = `Floor ${r.floor} (${fName})`;
        }
        floorMap.set(String(flId), {
          id: flId,
          floor: r.floor,
          name: fName
        });
      }
    });

    if (floorSelect) {
      floorMap.forEach(fl => {
        const opt = document.createElement('option');
        opt.value = fl.id;
        opt.dataset.floorNo = fl.floor;
        opt.textContent = fl.name;
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

    const matchingRooms = (window.allPmsRooms || []).filter(r => {
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
        opt.dataset.planId = r.plan_id || '';
        opt.dataset.planName = r.plan_name || '';
        opt.dataset.planPrice = r.plan_price || 0;

        const isAvail = (r.status === 'available');
        opt.textContent = `Room #${r.room} (₹${Number(r.rate).toLocaleString()}) • ${r.status.toUpperCase()}`;
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

    if (!roomSelect.value || !selectedOpt || selectedOpt.value === '') {
      if (banner) banner.style.display = 'none';
      return;
    }

    const beddingName = selectedOpt.dataset.bedding || 'King Size Master (72x78)';
    const maxAdults = selectedOpt.dataset.maxAdults || '2';
    const maxChildren = selectedOpt.dataset.maxChildren || '1';
    const maxPax = selectedOpt.dataset.maxPax || '3';
    const rate = selectedOpt.dataset.rate || '4500';

    const beddingEl = banner?.querySelector('.bedding-name-text');
    const paxEl = banner?.querySelector('.max-pax-count');
    const adultsEl = banner?.querySelector('.max-adults-count');
    const kidsEl = banner?.querySelector('.max-children-count');
    const rateEl = banner?.querySelector('.room-rate-text');

    if (beddingEl) beddingEl.textContent = beddingName;
    if (paxEl) paxEl.textContent = maxPax + ' Pax';
    if (adultsEl) adultsEl.textContent = maxAdults;
    if (kidsEl) kidsEl.textContent = maxChildren;
    if (rateEl) rateEl.textContent = '₹ ' + Number(rate).toLocaleString() + '/night';

    if (banner) banner.style.display = 'block';

    // Auto-sync category and floor if they are currently unselected
    const roomVal = roomSelect.value;
    const roomNo = selectedOpt.dataset.roomNo;
    const match = (window.allPmsRooms || []).find(r => String(r.id) === String(roomVal) || String(r.room) === String(roomVal) || String(r.room) === String(roomNo));
    if (match) {
      const catSel = block.querySelector('.select-room-category');
      const flSel = block.querySelector('.select-room-floor');
      if (catSel && !catSel.value && match.category_id) {
        catSel.value = match.category_id;
      }
      if (flSel && !flSel.value && (match.floor_id || match.floor)) {
        flSel.value = match.floor_id || match.floor;
      }
    }

    // Auto-select room's associated meal plan if configured (remains fully choosable / changeable by user)
    const roomPlanId = selectedOpt.dataset.planId || match?.plan_id;
    const planSelect = $('#plan-id-select');
    if (planSelect.length) {
      if (roomPlanId) {
        planSelect.val(String(roomPlanId)).trigger('change');
      } else {
        planSelect.val('').trigger('change');
      }
    }

    // If primary guest room changed, propagate to additional guests who have 'Same Room' checked
    const allGuestBlocks = document.querySelectorAll('.guest-block');
    if (allGuestBlocks.length > 0 && (allGuestBlocks[0] === block || allGuestBlocks[0].contains(roomSelect))) {
      allGuestBlocks.forEach((b, idx) => {
        if (idx > 0) {
          const sameChk = b.querySelector('.chk-same-room');
          if (sameChk && sameChk.checked) {
            syncSameRoomForBlock(b);
          }
        }
      });
    }

    calculateReservationBilling();
    checkSelectedRoomAvailability(roomSelect);
  }

  function toggleSameRoom(checkboxElem) {
    const block = checkboxElem.closest('.guest-block') || checkboxElem.closest('.section-guest-details');
    if (!block) return;

    if (checkboxElem.checked) {
      syncSameRoomForBlock(block);
    } else {
      const catSel = block.querySelector('.select-room-category');
      const flSel = block.querySelector('.select-room-floor');
      const rmSel = block.querySelector('.select-room-no');
      const banner = block.querySelector('.room-capacity-banner');
      if (catSel) catSel.value = '';
      if (flSel) flSel.innerHTML = '<option value="">Select Floor</option>';
      if (rmSel) rmSel.innerHTML = '<option value="">Select Room No.</option>';
      if (banner) banner.style.display = 'none';
      calculateReservationBilling();
    }
  }

  function syncSameRoomForBlock(targetBlock) {
    const primaryBlock = document.querySelector('.guest-block');
    if (!primaryBlock || primaryBlock === targetBlock) return;

    const pCatSel = primaryBlock.querySelector('.select-room-category');
    const pFlSel = primaryBlock.querySelector('.select-room-floor');
    const pRmSel = primaryBlock.querySelector('.select-room-no');

    const catSel = targetBlock.querySelector('.select-room-category');
    const flSel = targetBlock.querySelector('.select-room-floor');
    const rmSel = targetBlock.querySelector('.select-room-no');

    if (pCatSel && catSel && pCatSel.value) {
      catSel.value = pCatSel.value;
      onCategoryChange(catSel);
    }
    if (pFlSel && flSel && pFlSel.value) {
      flSel.value = pFlSel.value;
      onFloorChange(flSel);
    }
    if (pRmSel && rmSel && pRmSel.value) {
      rmSel.value = pRmSel.value;
      onRoomChange(rmSel);
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
          if (idTypeSel && g.id_card_type_id) idTypeSel.value = g.id_card_type_id;

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
    const type = (checkedRadio.dataset.slug || checkedRadio.value || 'new').toLowerCase();
    const compFields = document.getElementById('section-company-fields');
    const labels = document.querySelectorAll('input[name="res_type"]');
    
    labels.forEach(radio => {
      if (radio.parentElement) {
        if (radio.checked) {
          radio.parentElement.style.background = '#eef2ff';
          radio.parentElement.style.borderColor = '#c7d2fe';
          radio.parentElement.style.color = '#4f46e5';
        } else {
          radio.parentElement.style.background = 'transparent';
          radio.parentElement.style.borderColor = 'transparent';
          radio.parentElement.style.color = '#64748b';
        }
      }
    });

    if (compFields) {
      compFields.style.display = (type === 'company') ? 'block' : 'none';
    }

    if (type === 'company') {
      applyCompanyDiscountFromSelect();
    } else {
      const noticeWrap = document.getElementById('company-discount-applied-text');
      if (noticeWrap) noticeWrap.style.display = 'none';
      if (typeof refreshPlanSelectDisplay === 'function') refreshPlanSelectDisplay();
      calculateReservationBilling();
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
    const type = (checkedRadio?.dataset?.slug || checkedRadio?.value || 'new').toLowerCase();
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
    
    // Enable "Same Room" checkbox for Guest #2 and above
    const sameRoomWrap = newBlock.querySelector('.same-room-checkbox-wrapper');
    const sameRoomChk = newBlock.querySelector('.chk-same-room');
    if (sameRoomWrap) {
      sameRoomWrap.style.display = 'inline-flex';
    }
    if (sameRoomChk) {
      sameRoomChk.checked = false;
    }

    // Reset room selects and capacity banner in cloned block
    const catSel = newBlock.querySelector('.select-room-category');
    const flSel = newBlock.querySelector('.select-room-floor');
    const rmSel = newBlock.querySelector('.select-room-no');
    const banner = newBlock.querySelector('.room-capacity-banner');
    if (catSel) catSel.value = '';
    if (flSel) flSel.innerHTML = '<option value="">Select Floor</option>';
    if (rmSel) rmSel.innerHTML = '<option value="">Select Room No.</option>';
    if (banner) banner.style.display = 'none';
    setRoomFieldsLocked(newBlock, false);

    const headerDiv = newBlock.querySelector('.guest-heading')?.parentElement;
    if (headerDiv && !newBlock.querySelector('.btn-remove-guest')) {
      let removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'btn-remove-guest';
      removeBtn.innerHTML = '<i class="fa-solid fa-trash"></i> Remove';
      removeBtn.style = 'color: #ef4444; background: none; border: none; font-size: 12px; font-weight: 800; cursor: pointer; text-transform: uppercase; letter-spacing: 1px;';
      removeBtn.onclick = function() { newBlock.remove(); updateGuestHeadings(); calculateReservationBilling(); };
      headerDiv.appendChild(removeBtn);
    }
    
    const chkPriv = newBlock.querySelector('.chk-privilege');
    const checkedRadio = document.querySelector('input[name="res_type"]:checked');
    const type = (checkedRadio?.dataset?.slug || checkedRadio?.value || 'new').toLowerCase();
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
      
      const sameRoomWrap = block.querySelector('.same-room-checkbox-wrapper');
      if (sameRoomWrap) {
        sameRoomWrap.style.display = (index === 0) ? 'none' : 'inline-flex';
      }
    });
  }

  function onDiscountTypeChange(type) {
    const pctWrap = document.getElementById('disc-percentage-input-wrapper');
    const amtWrap = document.getElementById('disc-amount-input-wrapper');
    const pctLabel = document.getElementById('discount-type-pct-label');
    const amtLabel = document.getElementById('discount-type-amt-label');

    if (type === 'amount') {
      if (pctWrap) pctWrap.style.display = 'none';
      if (amtWrap) amtWrap.style.display = 'block';

      if (amtLabel) {
        amtLabel.style.background = '#ecfdf5';
        amtLabel.style.borderColor = '#10b981';
        amtLabel.style.color = '#059669';
        amtLabel.style.fontWeight = '800';
      }
      if (pctLabel) {
        pctLabel.style.background = '#ffffff';
        pctLabel.style.borderColor = '#cbd5e1';
        pctLabel.style.color = '#64748b';
        pctLabel.style.fontWeight = '700';
      }
    } else {
      if (pctWrap) pctWrap.style.display = 'block';
      if (amtWrap) amtWrap.style.display = 'none';

      if (pctLabel) {
        pctLabel.style.background = '#eef2ff';
        pctLabel.style.borderColor = '#6366f1';
        pctLabel.style.color = '#4f46e5';
        pctLabel.style.fontWeight = '800';
      }
      if (amtLabel) {
        amtLabel.style.background = '#ffffff';
        amtLabel.style.borderColor = '#cbd5e1';
        amtLabel.style.color = '#64748b';
        amtLabel.style.fontWeight = '700';
      }
    }

    calculateReservationBilling();
  }

  function onGstTypeChange(type) {
    const incLabel = document.getElementById('gst-type-inc-label');
    const exclLabel = document.getElementById('gst-type-excl-label');

    if (type === 'inclusive') {
      if (incLabel) {
        incLabel.style.background = '#eef2ff';
        incLabel.style.borderColor = '#6366f1';
        incLabel.style.color = '#4f46e5';
        incLabel.style.fontWeight = '800';
      }
      if (exclLabel) {
        exclLabel.style.background = '#ffffff';
        exclLabel.style.borderColor = '#cbd5e1';
        exclLabel.style.color = '#64748b';
        exclLabel.style.fontWeight = '700';
      }
    } else {
      if (exclLabel) {
        exclLabel.style.background = '#eef2ff';
        exclLabel.style.borderColor = '#6366f1';
        exclLabel.style.color = '#4f46e5';
        exclLabel.style.fontWeight = '800';
      }
      if (incLabel) {
        incLabel.style.background = '#ffffff';
        incLabel.style.borderColor = '#cbd5e1';
        incLabel.style.color = '#64748b';
        incLabel.style.fontWeight = '700';
      }
    }

    calculateReservationBilling();
  }

  function applyCompanyDiscountFromSelect() {
    const compSelect = document.getElementById('company-select');
    if (!compSelect) return;

    const noticeWrap = document.getElementById('company-discount-applied-text');
    const noticeSpan = document.getElementById('company-discount-note');

    if (compSelect.value === 'new') {
      const newDiscType = document.querySelector('input[name="new_company_discount_type"]:checked')?.value || 'percentage';
      const newDiscVal = newDiscType === 'amount'
        ? (parseFloat(document.getElementById('new-comp-disc-amt')?.value) || 0)
        : (parseFloat(document.getElementById('new-comp-disc-pct')?.value) || 0);

      setDiscountTypeAndValue(newDiscType, newDiscVal);

      if (noticeWrap && noticeSpan) {
        noticeWrap.style.display = 'inline-flex';
        noticeSpan.textContent = `New Corporate Rate: ${newDiscType === 'amount' ? '₹ ' + newDiscVal : newDiscVal + '%'}`;
      }
    } else if (compSelect.selectedIndex >= 0) {
      const opt = compSelect.options[compSelect.selectedIndex];
      if (opt && opt.value) {
        const dType = opt.dataset.discountType || 'percentage';
        const dVal = parseFloat(opt.dataset.discountValue) || 0;
        const compName = opt.text.trim();

        setDiscountTypeAndValue(dType, dVal);

        if (noticeWrap && noticeSpan) {
          noticeWrap.style.display = 'inline-flex';
          noticeSpan.textContent = `Corporate discount auto-applied: ${compName} (${dType === 'amount' ? '₹ ' + dVal : dVal + '%'})`;
        }
      } else {
        if (noticeWrap) noticeWrap.style.display = 'none';
      }
    } else {
      if (noticeWrap) noticeWrap.style.display = 'none';
    }

    if (typeof refreshPlanSelectDisplay === 'function') {
      refreshPlanSelectDisplay();
    }
    calculateReservationBilling();
  }

  function setDiscountTypeAndValue(type, value) {
    const pctRadio = document.querySelector('input[name="discount_type"][value="percentage"]');
    const amtRadio = document.querySelector('input[name="discount_type"][value="amount"]');
    const pctInp = document.getElementById('calc-discount-percentage');
    const amtInp = document.getElementById('calc-discount-amount');

    if (type === 'amount') {
      if (amtRadio) amtRadio.checked = true;
      onDiscountTypeChange('amount');
      if (amtInp) amtInp.value = value > 0 ? value : 0;
    } else {
      if (pctRadio) pctRadio.checked = true;
      onDiscountTypeChange('percentage');
      if (pctInp) pctInp.value = value > 0 ? value : 0;
    }
  }

  function toggleNewCompany(selectElem) {
    const newCompDetails = document.getElementById('new-company-details');
    if (newCompDetails) {
      newCompDetails.style.display = (selectElem.value === 'new') ? 'block' : 'none';
    }
    applyCompanyDiscountFromSelect();
  }

  function toggleNewCompanyDiscountType(type) {
    const pctWrap = document.getElementById('new-company-disc-pct-wrap');
    const amtWrap = document.getElementById('new-company-disc-amt-wrap');
    if (type === 'amount') {
      if (pctWrap) pctWrap.style.display = 'none';
      if (amtWrap) amtWrap.style.display = 'block';
    } else {
      if (pctWrap) pctWrap.style.display = 'block';
      if (amtWrap) amtWrap.style.display = 'none';
    }
    applyCompanyDiscountFromSelect();
  }

  function getDashboardActiveDate() {
    const pmsCal = document.getElementById('pms-calendar-input');
    if (pmsCal && pmsCal.value && pmsCal.value.trim() !== '') {
      return pmsCal.value.trim();
    }
    const filterDate = document.getElementById('filter-date-val');
    if (filterDate && filterDate.value && filterDate.value.trim() !== '') {
      return filterDate.value.trim();
    }
    const reserveFrom = document.getElementById('reserve-from-date');
    if (reserveFrom && reserveFrom.value && reserveFrom.value.trim() !== '') {
      return reserveFrom.value.trim();
    }
    return null;
  }

  function getNextDayStr(dateStr) {
    if (!dateStr) return null;
    const parts = dateStr.split('-');
    if (parts.length === 3) {
      const d = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
      d.setDate(d.getDate() + 1);
      const yr = d.getFullYear();
      const mo = String(d.getMonth() + 1).padStart(2, '0');
      const da = String(d.getDate()).padStart(2, '0');
      return `${yr}-${mo}-${da}`;
    }
    return null;
  }

  function setInitialDateTime() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const todayStr = `${year}-${month}-${day}`;

    // Priority: Active date selected in dashboard navigator (#pms-calendar-input)
    const activeDashboardDate = getDashboardActiveDate();
    const checkInStr = activeDashboardDate || todayStr;
    const checkoutStr = getNextDayStr(checkInStr) || getNextDayStr(todayStr);

    const dateInput = document.getElementById('reserve-date-input');
    const timeInput = document.getElementById('reserve-time-input');
    const checkoutDateInput = document.getElementById('checkout-date-input');
    const checkoutTimeInput = document.getElementById('checkout-time-input');
    
    if (dateInput) {
      dateInput.value = checkInStr;
      const minDate = (checkInStr < todayStr) ? checkInStr : todayStr;
      dateInput.min = minDate;
    }
    if (timeInput) {
      const hours = String(now.getHours()).padStart(2, '0');
      const minutes = String(now.getMinutes()).padStart(2, '0');
      timeInput.value = `${hours}:${minutes}`;
    }
    if (checkoutDateInput) {
      checkoutDateInput.value = checkoutStr;
      checkoutDateInput.min = checkInStr;
    }
    if (checkoutTimeInput && !checkoutTimeInput.value) {
      checkoutTimeInput.value = '11:00';
    }

    calculateReservationBilling();
    const datesAlert = document.getElementById('dates-conflict-alert');
    if (datesAlert) datesAlert.style.display = 'none';
    if (typeof updateReservationStatusUI === 'function') updateReservationStatusUI();
  }

  function updateReservationStatusUI() {
    const dateInput = document.getElementById('reserve-date-input');
    const badge = document.getElementById('reservation-status-badge');
    const modalTitle = document.getElementById('reserve-modal-title');
    const modalSub = document.getElementById('reserve-modal-subtitle');
    const submitBtn = document.getElementById('reserve-submit-btn');

    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const todayStr = `${year}-${month}-${day}`;

    const checkInVal = (dateInput && dateInput.value) ? dateInput.value : todayStr;
    const isTodayOrPast = checkInVal <= todayStr;

    if (badge) {
      if (isTodayOrPast) {
        badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-800 border border-emerald-300';
        badge.innerHTML = '<i class="fa-solid fa-key"></i> Status: Checked In';
      } else {
        badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1.5 bg-blue-50 text-blue-700 border border-blue-200';
        badge.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Status: Confirmed';
      }
    }

    if (modalTitle) {
      modalTitle.innerHTML = isTodayOrPast 
        ? '<i class="fa-solid fa-key mr-1.5"></i> Guest Check-In' 
        : '<i class="fa-solid fa-calendar-plus mr-1.5"></i> Advance Reservation';
    }

    if (modalSub) {
      modalSub.innerHTML = isTodayOrPast 
        ? 'Hotel Sagar Sonnet • Immediate Check-In (Status: <strong>Checked In</strong>)' 
        : 'Hotel Sagar Sonnet • Future Booking (Status: <strong>Confirmed</strong>)';
    }

    if (submitBtn && !submitBtn.disabled) {
      submitBtn.innerHTML = isTodayOrPast 
        ? '<i class="fa-solid fa-key"></i> Complete Check-In' 
        : '<i class="fa-solid fa-calendar-plus"></i> Save Confirmed Reservation';
    }
  }

  function onCheckInDateChange() {
    const dateInput = document.getElementById('reserve-date-input');
    const checkoutDateInput = document.getElementById('checkout-date-input');
    if (dateInput && checkoutDateInput) {
      const checkInVal = dateInput.value;
      if (checkInVal) {
        // Disallow past dates relative to checkin date
        checkoutDateInput.min = checkInVal;
        if (!checkoutDateInput.value || checkoutDateInput.value < checkInVal) {
          const d = new Date(checkInVal + 'T00:00:00');
          d.setDate(d.getDate() + 1);
          const y = d.getFullYear();
          const m = String(d.getMonth() + 1).padStart(2, '0');
          const day = String(d.getDate()).padStart(2, '0');
          checkoutDateInput.value = `${y}-${m}-${day}`;
        }
      }
    }
    calculateReservationBilling();
    checkSelectedRoomAvailability();
    if (typeof updateReservationStatusUI === 'function') updateReservationStatusUI();
  }

  function onCheckOutDateChange() {
    const dateInput = document.getElementById('reserve-date-input');
    const checkoutDateInput = document.getElementById('checkout-date-input');
    if (dateInput && checkoutDateInput) {
      const checkInVal = dateInput.value;
      const checkOutVal = checkoutDateInput.value;
      if (checkInVal && checkOutVal && checkOutVal < checkInVal) {
        checkoutDateInput.value = checkInVal;
      }
    }
    calculateReservationBilling();
    checkSelectedRoomAvailability();
  }

  function calculateReservationBilling() {
    const reserveDateVal = document.getElementById('reserve-date-input')?.value;
    const checkoutDateVal = document.getElementById('checkout-date-input')?.value;
    
    let nights = 1;
    if (reserveDateVal && checkoutDateVal) {
      const d1 = new Date(reserveDateVal + 'T00:00:00');
      const d2 = new Date(checkoutDateVal + 'T00:00:00');
      const diffTime = d2.getTime() - d1.getTime();
      const diffDays = Math.round(diffTime / (1000 * 3600 * 24));
      nights = Math.max(1, diffDays);
    }

    const nightsBadge = document.getElementById('stay-nights-badge');
    if (nightsBadge) {
      nightsBadge.textContent = nights + (nights === 1 ? ' Night' : ' Nights');
    }

    // Calculate total room rate across distinct assigned rooms (or fallback to primary)
    const guestBlocks = document.querySelectorAll('.guest-block');
    const assignedRooms = new Map();
    let fallbackRate = 4500;

    guestBlocks.forEach((block, idx) => {
      const rmSel = block.querySelector('.select-room-no');
      if (rmSel && rmSel.selectedIndex >= 0 && rmSel.value) {
        const opt = rmSel.options[rmSel.selectedIndex];
        const rRate = (opt && opt.dataset.rate) ? parseFloat(opt.dataset.rate) : fallbackRate;
        const rKey = (opt && opt.dataset.roomNo) ? opt.dataset.roomNo : rmSel.value;
        assignedRooms.set(rKey, rRate);
        if (idx === 0) fallbackRate = rRate;
      }
    });

    let totalNightlyRate = 0;
    if (assignedRooms.size > 0) {
      for (const [rKey, rRate] of assignedRooms.entries()) {
        totalNightlyRate += rRate;
      }
    } else {
      totalNightlyRate = fallbackRate;
    }

    const totalRoomTariff = totalNightlyRate * nights;

    // Meal Plan Calculation
    const planSel = document.getElementById('plan-id-select');
    const selectedPlanOpt = (planSel && planSel.selectedIndex >= 0) ? planSel.options[planSel.selectedIndex] : null;
    const planIdVal = planSel ? planSel.value : '';
    const planBasePrice = (selectedPlanOpt && selectedPlanOpt.dataset.price) ? parseFloat(selectedPlanOpt.dataset.price) : 0;
    const planName = (selectedPlanOpt && planIdVal) ? (selectedPlanOpt.dataset.name || selectedPlanOpt.text.split('(')[0].trim()) : '';

    const pricing = typeof getEffectivePlanPricing === 'function' 
      ? getEffectivePlanPricing(planIdVal, planBasePrice) 
      : { hasDiscount: false, originalPrice: planBasePrice, effectivePrice: planBasePrice, discountText: '' };
    const planPrice = pricing.effectivePrice;

    const roomCount = assignedRooms.size > 0 ? assignedRooms.size : 1;
    let totalMealPlan = 0;
    const mealCard = document.getElementById('calc-meal-plan-card');
    const mealRateLabel = document.getElementById('calc-meal-plan-rate-label');
    const mealDisplay = document.getElementById('calc-meal-plan-display');
    const mealAmtInp = document.getElementById('calc-meal-plan-amount');
    const mealNameEl = document.getElementById('calc-meal-plan-name');
    const combinedCard = document.getElementById('calc-combined-gross-card');
    const combinedDisplay = document.getElementById('calc-combined-gross-display');

    if (planIdVal && planName) {
      totalMealPlan = planPrice * nights * roomCount;
      if (mealCard) mealCard.style.display = 'block';
      if (mealNameEl) {
        if (pricing.hasDiscount) {
          mealNameEl.innerHTML = `${escapeHtml(planName)} <span class="text-[8px] font-extrabold bg-emerald-100 text-emerald-800 px-1 py-0.5 rounded border border-emerald-200 uppercase"><i class="fa-solid fa-tag"></i> ${pricing.discountText}</span>`;
        } else {
          mealNameEl.textContent = planName;
        }
      }
      if (mealRateLabel) {
        const rmStr = roomCount > 1 ? ` (${roomCount} Rooms)` : '';
        if (pricing.hasDiscount) {
          mealRateLabel.innerHTML = `<span class="line-through opacity-65 mr-1 font-medium">₹ ${pricing.originalPrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span><strong class="text-emerald-600 font-extrabold">₹ ${pricing.effectivePrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong> x ${nights} ${nights === 1 ? 'Night' : 'Nights'}${rmStr}`;
        } else if (planPrice > 0) {
          mealRateLabel.textContent = `₹ ${planPrice.toLocaleString('en-IN')} x ${nights} ${nights === 1 ? 'Night' : 'Nights'}${rmStr}`;
        } else {
          mealRateLabel.textContent = `Complimentary / Included`;
        }
      }
      if (mealDisplay) {
        if (pricing.hasDiscount) {
          const origTotal = pricing.originalPrice * nights * roomCount;
          mealDisplay.innerHTML = `<span class="line-through text-[11px] opacity-60 mr-1.5 font-semibold">+ ₹ ${origTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span><span class="text-emerald-600 font-black">+ ₹ ${totalMealPlan.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>`;
        } else {
          mealDisplay.textContent = planPrice > 0 
            ? `+ ₹ ${totalMealPlan.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
            : `₹ 0.00`;
        }
      }
      if (mealAmtInp) mealAmtInp.value = totalMealPlan.toFixed(2);

      if (planPrice > 0 && combinedCard && combinedDisplay) {
        combinedCard.style.display = 'block';
        combinedDisplay.textContent = `₹ ${(totalRoomTariff + totalMealPlan).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
      } else if (combinedCard) {
        combinedCard.style.display = 'none';
      }
    } else {
      if (mealCard) mealCard.style.display = 'none';
      if (combinedCard) combinedCard.style.display = 'none';
      if (mealAmtInp) mealAmtInp.value = '0';
    }

    const totalGross = totalRoomTariff + totalMealPlan;

    // Update Nights & Gross UI
    const nightsRateLabel = document.getElementById('calc-nights-rate-label');
    if (nightsRateLabel) {
      const roomCountStr = assignedRooms.size > 1 ? ` (${assignedRooms.size} Rooms)` : '';
      nightsRateLabel.textContent = `₹ ${totalNightlyRate.toLocaleString('en-IN')} x ${nights} ${nights === 1 ? 'Night' : 'Nights'}${roomCountStr}`;
    }
    const grossDisplay = document.getElementById('calc-gross-display');
    if (grossDisplay) {
      grossDisplay.textContent = `₹ ${totalRoomTariff.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    const totalAmountInp = document.getElementById('calc-total-amount');
    if (totalAmountInp) {
      totalAmountInp.value = totalGross.toFixed(2);
    }

    // Discount Calculation (Dynamic: % or Solid Amount for all registration types)
    const discType = document.querySelector('input[name="discount_type"]:checked')?.value || 'percentage';
    let discountPct = 0;
    let discountAmount = 0;

    if (discType === 'amount') {
      const solidAmt = parseFloat(document.getElementById('calc-discount-amount')?.value) || 0;
      discountAmount = Math.min(totalGross, Math.max(0, solidAmt));
      discountPct = totalGross > 0 ? Math.round(((discountAmount / totalGross) * 100) * 100) / 100 : 0;

      const pctInp = document.getElementById('calc-discount-percentage');
      if (pctInp && document.activeElement !== pctInp) {
        pctInp.value = discountPct > 0 ? discountPct : '0';
      }
    } else {
      const pctVal = parseFloat(document.getElementById('calc-discount-percentage')?.value) || 0;
      discountPct = Math.min(100, Math.max(0, pctVal));
      discountAmount = Math.round(((totalGross * discountPct) / 100) * 100) / 100;

      const amtInp = document.getElementById('calc-discount-amount');
      if (amtInp && document.activeElement !== amtInp) {
        amtInp.value = discountAmount > 0 ? discountAmount.toFixed(2) : '0';
      }
    }

    const netAfterDiscount = Math.max(0, totalGross - discountAmount);

    const discValLabel = document.getElementById('calc-discount-val-label');
    if (discValLabel) {
      if (discountAmount > 0) {
        discValLabel.textContent = `- ₹ ${discountAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${discountPct}%)`;
      } else {
        discValLabel.textContent = `- ₹ 0.00`;
      }
    }
    const discAmtInput = document.getElementById('calc-discount-amount');
    if (discAmtInput && discType === 'percentage' && document.activeElement !== discAmtInput) {
      discAmtInput.value = discountAmount > 0 ? discountAmount.toFixed(2) : '0';
    }

    // Dynamic GST / Tax Calculation (Inclusive vs Excluding GST)
    const gstType = document.querySelector('input[name="gst_type"]:checked')?.value || 'exclusive';
    const gstSelect = document.getElementById('calc-gst-rate');
    const selectedGstOpt = gstSelect ? gstSelect.options[gstSelect.selectedIndex] : null;
    const gstRate = selectedGstOpt ? (parseFloat(selectedGstOpt.dataset.rate) || 0) : 0;

    let gstAmount = 0;
    let payableAmount = netAfterDiscount;

    if (gstRate > 0) {
      if (gstType === 'inclusive') {
        // Room tariff already includes GST: extract GST portion
        gstAmount = Math.round((netAfterDiscount - (netAfterDiscount / (1 + (gstRate / 100)))) * 100) / 100;
        payableAmount = netAfterDiscount;
      } else {
        // GST is added on top of the discounted tariff
        gstAmount = Math.round(((netAfterDiscount * gstRate) / 100) * 100) / 100;
        payableAmount = netAfterDiscount + gstAmount;
      }
    }

    const gstValLabel = document.getElementById('calc-gst-val-label');
    if (gstValLabel) {
      if (gstRate > 0) {
        if (gstType === 'inclusive') {
          gstValLabel.textContent = `Incl. ₹ ${gstAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${gstRate}%)`;
        } else {
          gstValLabel.textContent = `+ ₹ ${gstAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} (${gstRate}%)`;
        }
      } else {
        gstValLabel.textContent = `+ ₹ 0.00 (0%)`;
      }
    }

    const gstPctInp = document.getElementById('calc-gst-percentage');
    if (gstPctInp) gstPctInp.value = gstRate;
    const gstAmtInp = document.getElementById('calc-gst-amount');
    if (gstAmtInp) gstAmtInp.value = gstAmount.toFixed(2);

    const payableDisplay = document.getElementById('calc-payable-display');
    if (payableDisplay) {
      payableDisplay.textContent = `₹ ${payableAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    const payableAmtInp = document.getElementById('calc-payable-amount');
    if (payableAmtInp) payableAmtInp.value = payableAmount.toFixed(2);

    // Advance amount and remaining balance
    const advInput = document.getElementById('payment-advance-amount');
    const advanceVal = parseFloat(advInput?.value) || 0;
    const remainingBalance = Math.max(0, payableAmount - advanceVal);

    const balanceDisplay = document.getElementById('calc-balance-display');
    if (balanceDisplay) {
      balanceDisplay.textContent = `₹ ${remainingBalance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    const balanceInp = document.getElementById('calc-balance-amount');
    if (balanceInp) balanceInp.value = remainingBalance.toFixed(2);

    const balanceBox = document.getElementById('calc-balance-box');
    if (balanceBox) {
      const boxLabel = balanceBox.querySelector('span');
      if (remainingBalance <= 0) {
        balanceBox.style.background = 'rgba(16, 185, 129, 0.08)';
        balanceBox.style.borderColor = 'rgba(16, 185, 129, 0.3)';
        if (balanceDisplay) balanceDisplay.style.color = '#059669';
        if (boxLabel) {
          boxLabel.style.color = '#059669';
          boxLabel.innerHTML = '<i class="fa-solid fa-circle-check"></i> Fully Paid (No Balance):';
        }
      } else {
        balanceBox.style.background = 'rgba(225, 29, 72, 0.06)';
        balanceBox.style.borderColor = 'rgba(225, 29, 72, 0.2)';
        if (balanceDisplay) balanceDisplay.style.color = '#e11d48';
        if (boxLabel) {
          boxLabel.style.color = '#e11d48';
          boxLabel.innerHTML = 'Remaining Amount:';
        }
      }
    }
  }

  function togglePaymentFields(selectElem) {
    const container = document.getElementById('payment-details-container');
    if (container) {
      // Keep visible always, even when 'None' is chosen
      container.style.display = 'flex';
      calculateReservationBilling();
    }
  }

  // Real-Time Room Availability & Booking Conflict Detector
  let roomAvailabilityAbortController = null;

  function checkSelectedRoomAvailability(targetRoomSelect = null) {
    const checkInInput = document.getElementById('reserve-date-input');
    const checkoutDateInput = document.getElementById('checkout-date-input');
    if (!checkInInput || !checkoutDateInput) return;

    const checkInVal = checkInInput.value;
    const checkOutVal = checkoutDateInput.value;
    if (!checkInVal || !checkOutVal) return;

    // Room select element (from target or primary guest block)
    const rmSelect = targetRoomSelect || document.querySelector('.guest-block .select-room-no');
    if (!rmSelect) return;

    const block = rmSelect.closest('.guest-block') || rmSelect.closest('.section-guest-details') || document.querySelector('.guest-block');
    const conflictBox = block ? block.querySelector('.room-conflict-alert-box') : document.querySelector('.room-conflict-alert-box');
    if (!conflictBox) return;

    const roomId = rmSelect.value;
    if (!roomId) {
      conflictBox.style.display = 'none';
      const datesAlert = document.getElementById('dates-conflict-alert');
      if (datesAlert) datesAlert.style.display = 'none';
      return;
    }

    if (roomAvailabilityAbortController) {
      roomAvailabilityAbortController.abort();
    }
    roomAvailabilityAbortController = new AbortController();

    const url = `{{ route('frontoffice.room.check-availability') }}?room_id=${encodeURIComponent(roomId)}&checkin_date=${encodeURIComponent(checkInVal)}&checkout_date=${encodeURIComponent(checkOutVal)}`;

    fetch(url, {
      signal: roomAvailabilityAbortController.signal,
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (!data) return;

      if (!data.available && data.conflict) {
        // Display conflict alert box
        conflictBox.style.display = 'block';

        const msgEl = conflictBox.querySelector('.conflict-message-text');
        const subEl = conflictBox.querySelector('.conflict-sub-text');
        const altContainer = conflictBox.querySelector('.conflict-alt-rooms-list');

        const opt = rmSelect.options[rmSelect.selectedIndex];
        const roomNoText = opt ? (opt.dataset.roomNo || opt.textContent.trim()) : `Room #${data.room}`;

        if (msgEl) {
          msgEl.innerHTML = `<span class="text-xs font-black text-rose-700"><i class="fa-solid fa-ban mr-1"></i> Room #${escapeHtml(data.room)} is already booked</span> from <strong class="text-rose-800 underline">${escapeHtml(data.conflict.checkin_date)}</strong> to <strong class="text-rose-800 underline">${escapeHtml(data.conflict.checkout_date)}</strong> by <strong>${escapeHtml(data.conflict.guest_name)}</strong> (Status: <strong>${escapeHtml(data.conflict.status)}</strong> • Dossier: <strong>${escapeHtml(data.conflict.reserve_id)}</strong>).`;
        }

        if (subEl) {
          subEl.innerHTML = `Your requested stay duration (<strong>${formatDateDisplay(checkInVal)} &rarr; ${formatDateDisplay(checkOutVal)}</strong>) overlaps with this existing booking. Please select one of the matching available rooms below:`;
        }

        // Display dates conflict alert right below checkin & checkout dates
        const datesAlert = document.getElementById('dates-conflict-alert');
        const datesAlertText = document.getElementById('dates-conflict-text');
        if (datesAlert && datesAlertText) {
          datesAlert.style.display = 'block';
          datesAlertText.innerHTML = `Room #${escapeHtml(data.room)} is already booked from <strong class="underline">${escapeHtml(data.conflict.checkin_date)}</strong> to <strong class="underline">${escapeHtml(data.conflict.checkout_date)}</strong> by <strong>${escapeHtml(data.conflict.guest_name)}</strong>! Please change stay dates or select an alternative room.`;
        }

        // Render matching alternative rooms grouped floor-wise
        if (altContainer) {
          if (data.available_rooms && data.available_rooms.length > 0) {
            // Group rooms by floor
            const floorGroups = {};
            data.available_rooms.forEach(r => {
              const floorVal = (r.floor !== null && r.floor !== undefined && String(r.floor).trim() !== '') ? String(r.floor).trim() : '1';
              if (!floorGroups[floorVal]) {
                floorGroups[floorVal] = [];
              }
              floorGroups[floorVal].push(r);
            });

            // Sort floors numerically if possible
            const sortedFloors = Object.keys(floorGroups).sort((a, b) => {
              const numA = parseFloat(a);
              const numB = parseFloat(b);
              if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
              return a.localeCompare(b);
            });

            altContainer.innerHTML = sortedFloors.map(floorKey => {
              const rooms = floorGroups[floorKey];
              // Sort rooms numerically by room number
              rooms.sort((a, b) => {
                const ra = parseFloat(a.room);
                const rb = parseFloat(b.room);
                if (!isNaN(ra) && !isNaN(rb)) return ra - rb;
                return String(a.room).localeCompare(String(b.room));
              });

              const floorTitle = (String(floorKey).toLowerCase().includes('floor'))
                ? floorKey
                : `Floor ${floorKey}`;

              return `
                <div class="conflict-floor-group bg-slate-50 border border-slate-200 rounded-xl p-3">
                  <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-dashed border-slate-300">
                    <div class="text-[11px] font-black text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                      <i class="fa-solid fa-layer-group text-indigo-500 text-xs"></i>
                      <span>${escapeHtml(floorTitle)}</span>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                      ${rooms.length} ${rooms.length === 1 ? 'Room' : 'Rooms'} Available
                    </span>
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    ${rooms.map(r => `
                      <div class="flex items-center justify-between bg-white border border-slate-200 rounded-xl p-2.5 shadow-2xs transition-all">
                        <div class="min-w-0 flex-1 pr-2">
                          <div class="font-extrabold text-slate-900 text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-door-open text-emerald-600 text-[11px]"></i>
                            <span>Room #${escapeHtml(r.room)}</span>
                            <span class="text-[9px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 px-1.5 py-0.5 rounded">${escapeHtml(r.category)}</span>
                          </div>
                          <div class="text-[10px] text-slate-500 mt-0.5">
                            ₹${Number(r.rate).toLocaleString()}/night • Max ${r.max_pax} Pax
                          </div>
                        </div>
                        <button type="button" class="h-7 px-3 text-[10px] font-extrabold rounded-lg whitespace-nowrap shrink-0 cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white transition-all shadow-xs" onclick="selectAlternativeRoom('${r.id}', '${r.room}', '${r.category_id}', '${r.floor_id}', this)">
                          <i class="fa-solid fa-check"></i> Select
                        </button>
                      </div>
                    `).join('')}
                  </div>
                </div>
              `;
            }).join('');
          } else {
            altContainer.innerHTML = `
              <div class="p-3.5 text-xs font-bold text-slate-400 text-center bg-slate-50 rounded-xl border border-dashed border-slate-200">
                <i class="fa-solid fa-circle-exclamation mr-1"></i> No other alternative rooms are completely free for this date range.
              </div>
            `;
          }
        }

        // Trigger warning notification toast
        if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
          PmsAlert.toast(`Room #${data.room} is already booked for these dates! Alternative matching rooms are listed below.`, 'warning');
        }
      } else {
        // Room is available with no conflict!
        conflictBox.style.display = 'none';
        const datesAlert = document.getElementById('dates-conflict-alert');
        if (datesAlert) datesAlert.style.display = 'none';
      }
    })
    .catch(err => {
      if (err.name !== 'AbortError') {
        console.error('Room availability check error:', err);
      }
    });
  }

  function selectAlternativeRoom(roomId, roomNo, categoryId, floorId, btnEl) {
    const block = btnEl.closest('.guest-block') || btnEl.closest('.section-guest-details') || document.querySelector('.guest-block');
    if (!block) return;

    // Temporarily unlock to populate values smoothly
    setRoomFieldsLocked(block, false);

    const catSel = block.querySelector('.select-room-category');
    const flSel = block.querySelector('.select-room-floor');
    const rmSel = block.querySelector('.select-room-no');

    if (catSel && categoryId) {
      catSel.value = categoryId;
      onCategoryChange(catSel);
    }
    if (flSel && floorId) {
      flSel.value = floorId;
      onFloorChange(flSel);
    }

    if (rmSel) {
      let found = false;
      for (let opt of rmSel.options) {
        if (String(opt.value) === String(roomId) || String(opt.value) === String(roomNo) || String(opt.dataset.roomNo) === String(roomNo)) {
          rmSel.value = opt.value;
          found = true;
          break;
        }
      }
      if (!found) {
        const opt = document.createElement('option');
        opt.value = roomId;
        opt.dataset.roomNo = roomNo;
        opt.textContent = `Room #${roomNo}`;
        rmSel.appendChild(opt);
        rmSel.value = roomId;
      }
      onRoomChange(rmSel);
    }

    // Now re-lock to the newly selected alternative room!
    setRoomFieldsLocked(block, true, roomNo);

    // Hide conflict alert box
    const conflictBox = block.querySelector('.room-conflict-alert-box') || document.querySelector('.room-conflict-alert-box');
    if (conflictBox) {
      conflictBox.style.display = 'none';
    }
    const datesAlert = document.getElementById('dates-conflict-alert');
    if (datesAlert) {
      datesAlert.style.display = 'none';
    }

    if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
      PmsAlert.toast(`Room #${roomNo} successfully selected for your stay!`, 'success');
    }
  }

  function formatDateDisplay(dateStr) {
    if (!dateStr) return '';
    try {
      const d = new Date(dateStr + 'T00:00:00');
      return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    } catch(e) {
      return dateStr;
    }
  }

  function handleReserve(e) {
    e.preventDefault();
    const form = document.getElementById('reservation-form') || e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const origBtnHtml = submitBtn ? submitBtn.innerHTML : '';

    // Guard against submitting double-booked conflict
    const visibleConflict = document.querySelector('.room-conflict-alert-box');
    if (visibleConflict && visibleConflict.style.display !== 'none') {
      if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
        PmsAlert.toast('Please select an available room without booking conflicts before proceeding.', 'error');
      } else {
        alert('Please select an available room without booking conflicts before proceeding.');
      }
      return;
    }

    // Collect Registration Type ID
    const checkedRadio = form.querySelector('input[name="res_type"]:checked');
    const registrationTypeId = checkedRadio ? (checkedRadio.value || checkedRadio.dataset.registrationId) : null;
    const resTypeSlug = checkedRadio ? (checkedRadio.dataset.slug || 'new') : 'new';

    // Collect Reservation Meta
    const reserveDate = form.querySelector('input[name="reserve_date"]')?.value || '';
    const reserveTime = form.querySelector('input[name="reserve_time"]')?.value || '';
    const checkoutDate = form.querySelector('input[name="checkout_date"]')?.value || '';
    const checkoutTime = form.querySelector('input[name="checkout_time"]')?.value || '';
    const reservationModeId = form.querySelector('select[name="reservation_mode_id"]')?.value || null;

    // Collect Corporate Details
    const companySelect = form.querySelector('select[name="company_id"]');
    const companyId = companySelect ? companySelect.value : null;
    const newCompanyName = form.querySelector('input[name="new_company_name"]')?.value || '';
    const newCompanyAddress = form.querySelector('input[name="new_company_address"]')?.value || '';
    const newCompanyGstin = form.querySelector('input[name="new_company_gstin"]')?.value || '';
    const newCompanyPhone = form.querySelector('input[name="new_company_phone"]')?.value || '';

    // Evaluate date-based autoStatus: Today (or earlier) => 'Checked In', Future => 'Confirmed'
    const todayObj = new Date();
    const curY = todayObj.getFullYear();
    const curM = String(todayObj.getMonth() + 1).padStart(2, '0');
    const curD = String(todayObj.getDate()).padStart(2, '0');
    const todayIso = `${curY}-${curM}-${curD}`;
    const checkInDateVal = reserveDate || todayIso;
    const isTodayOrPast = checkInDateVal <= todayIso;
    const computedStatus = isTodayOrPast ? 'Checked In' : 'Confirmed';

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
      const statusSelect = block.querySelector('select[name="status"]');
      const status = statusSelect ? statusSelect.value : computedStatus;
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

    // Auto-attach any mobile scanned uploaded documents to primary guest
    const uploadedDocsJson = document.getElementById('reserve-uploaded-documents')?.value;
    let parsedUploadedDocs = [];
    if (uploadedDocsJson) {
      try {
        parsedUploadedDocs = JSON.parse(uploadedDocsJson);
      } catch (err) {}
    }
    if (Array.isArray(parsedUploadedDocs) && parsedUploadedDocs.length > 0 && guests.length > 0) {
      guests[0].documents = parsedUploadedDocs;
    }

    // Collect Payment & Billing Breakdown
    const planId = form.querySelector('select[name="plan_id"]')?.value || null;
    const paymentModeId = form.querySelector('select[name="payment_mode_id"]')?.value;
    const newCompDiscType = form.querySelector('input[name="new_company_discount_type"]:checked')?.value || 'percentage';
    const newCompDiscValue = newCompDiscType === 'amount'
      ? (parseFloat(form.querySelector('input[name="new_company_discount_amt"]')?.value) || 0)
      : (parseFloat(form.querySelector('input[name="new_company_discount_pct"]')?.value) || 0);
    const discountType = form.querySelector('input[name="discount_type"]:checked')?.value || 'percentage';
    const discountPercentage = parseFloat(form.querySelector('input[name="discount_percentage"]')?.value) || 0;
    const discountAmount = parseFloat(form.querySelector('input[name="discount_amount"]')?.value) || 0;
    const gstType = form.querySelector('input[name="gst_type"]:checked')?.value || 'exclusive';
    const gstPercentageId = form.querySelector('select[name="gst_percentage_id"]')?.value || null;
    const gstPercentage = parseFloat(form.querySelector('input[name="gst_percentage"]')?.value) || 0;
    const gstAmount = parseFloat(form.querySelector('input[name="gst_amount"]')?.value) || 0;
    const totalAmount = form.querySelector('input[name="total_amount"]')?.value || 0;
    const payableAmount = form.querySelector('input[name="payable_amount"]')?.value || 0;
    const advanceAmount = form.querySelector('input[name="advance_amount"]')?.value || 0;
    const paymentRemarks = form.querySelector('input[name="payment_remarks"]')?.value || '';

    const payload = {
      guest_upload_token: document.getElementById('reserve-guest-upload-token')?.value || null,
      uploaded_documents: parsedUploadedDocs,
      registration_type_id: registrationTypeId,
      res_type: resTypeSlug,
      reserve_date: reserveDate,
      reserve_time: reserveTime,
      checkout_date: checkoutDate,
      checkout_time: checkoutTime,
      reservation_mode_id: reservationModeId,
      company_id: companyId,
      new_company_name: newCompanyName,
      new_company_address: newCompanyAddress,
      new_company_gstin: newCompanyGstin,
      new_company_phone: newCompanyPhone,
      new_company_discount_type: newCompDiscType,
      new_company_discount_value: newCompDiscValue,
      guests: guests,
      payment_mode_id: paymentModeId,
      plan_id: planId,
      meal_plan_amount: form.querySelector('input[name="meal_plan_amount"]')?.value || 0,
      discount_type: discountType,
      discount_percentage: discountPercentage,
      discount_amount: discountAmount,
      gst_type: gstType,
      gst_percentage_id: gstPercentageId,
      gst_percentage: gstPercentage,
      gst_amount: gstAmount,
      total_amount: totalAmount,
      payable_amount: payableAmount,
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

          closeModal('reserve-modal');

          if (typeof Swal !== 'undefined') {
            const isCheckInSuccess = data.status === 'Checked In';
            Swal.fire({
              icon: 'success',
              title: isCheckInSuccess ? 'Guest Checked In Successfully!' : 'Reservation Confirmed!',
              html: `<b>Reserve ID:</b> <code class="text-base text-indigo-600 font-bold">${escapeHtml(resId)}</code><br><br>${escapeHtml(data.message)}`,
              confirmButtonColor: '#10b981',
              confirmButtonText: '<i class="fa-solid fa-check"></i> Great',
              heightAuto: false,
              scrollbarPadding: false,
              allowOutsideClick: false,
              allowEscapeKey: false,
            }).then((result) => {
              window.location.reload();
            });
          } else if (typeof PmsAlert !== 'undefined') {
            PmsAlert.toast(`Reservation #${resId} confirmed successfully!`, 'success');
            setTimeout(() => window.location.reload(), 800);
          } else {
            alert(`Reservation #${resId} confirmed successfully!`);
            window.location.reload();
          }
          form.reset();
          if (typeof $ !== 'undefined' && $('#plan-id-select').data('select2')) {
            $('#plan-id-select').val('').trigger('change');
          }
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
                    <div class="guest-name text-xs font-black text-white truncate">${escapeHtml(g.guest_name)}</div>
                    <div class="guest-state text-[10px] text-white/85 truncate">${escapeHtml(g.status || 'Confirmed')}</div>
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
          const errMsg = data.message || data.error || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Unable to store reservation. Please try again.');
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'error',
              title: 'Reservation Failed',
              html: errMsg,
            });
          } else {
            alert('Error: ' + errMsg);
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

  function getCompanyMealDiscount(planId) {
    if (!planId) return null;
    const checkedRadio = document.querySelector('input[name="res_type"]:checked');
    const type = (checkedRadio?.dataset?.slug || checkedRadio?.value || '').toLowerCase();
    if (type !== 'company') return null;

    const compSelect = document.getElementById('company-select');
    if (!compSelect || compSelect.selectedIndex < 0) return null;
    const opt = compSelect.options[compSelect.selectedIndex];
    if (!opt || !opt.value || opt.value === 'new') return null;

    let discounts = [];
    try {
      const raw = opt.dataset.mealDiscounts;
      if (raw) {
        discounts = typeof raw === 'string' ? JSON.parse(raw) : raw;
      }
    } catch (e) {
      console.error('Error parsing company meal discounts:', e);
    }

    if (!Array.isArray(discounts)) return null;

    const match = discounts.find(d => {
      const pid = d.meal_plan_id !== undefined ? d.meal_plan_id : d.plan_id;
      return String(pid) === String(planId);
    });
    if (!match) return null;

    const val = parseFloat(match.discount_value) || 0;
    if (val <= 0) return null;

    return {
      discount_type: match.discount_type || 'percentage',
      discount_value: val
    };
  }

  function getEffectivePlanPricing(planId, basePrice) {
    const originalPrice = parseFloat(basePrice) || 0;
    const disc = getCompanyMealDiscount(planId);
    if (!disc || originalPrice <= 0) {
      return {
        hasDiscount: false,
        originalPrice: originalPrice,
        effectivePrice: originalPrice,
        discountText: ''
      };
    }

    let effPrice = originalPrice;
    let tag = '';
    if (disc.discount_type === 'percentage') {
      const cut = (originalPrice * disc.discount_value) / 100;
      effPrice = Math.max(0, originalPrice - cut);
      tag = `${disc.discount_value}% OFF`;
    } else {
      effPrice = Math.max(0, originalPrice - disc.discount_value);
      tag = `₹${disc.discount_value} OFF`;
    }

    return {
      hasDiscount: true,
      originalPrice: originalPrice,
      effectivePrice: effPrice,
      discountText: tag,
      discountType: disc.discount_type,
      discountValue: disc.discount_value
    };
  }

  function formatPlanOption(opt) {
    if (!opt.id) return opt.text;
    const elem = opt.element;
    if (!elem) return opt.text;

    const planName = elem.dataset.name || (opt.text ? opt.text.split('(')[0].trim() : 'Meal Plan');
    const basePrice = parseFloat(elem.dataset.price) || 0;
    const pricing = getEffectivePlanPricing(opt.id, basePrice);

    if (basePrice <= 0) {
      return $(`
        <div class="flex justify-between items-center py-0.5 w-full text-xs">
          <span class="font-bold text-slate-800">${escapeHtml(planName)}</span>
          <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">Complimentary</span>
        </div>
      `);
    }

    if (pricing.hasDiscount) {
      return $(`
        <div class="flex justify-between items-center py-0.5 w-full text-xs">
          <div class="flex items-center gap-1.5">
            <span class="font-bold text-slate-800">${escapeHtml(planName)}</span>
            <span class="text-[9px] font-extrabold bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded border border-emerald-200">${pricing.discountText}</span>
          </div>
          <div class="flex items-center gap-1.5 font-mono">
            <span class="text-[11px] text-slate-400 line-through">₹ ${pricing.originalPrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
            <strong class="text-xs text-emerald-600 font-extrabold">₹ ${pricing.effectivePrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
          </div>
        </div>
      `);
    }

    return $(`
      <div class="flex justify-between items-center py-0.5 w-full text-xs">
        <span class="font-bold text-slate-800">${escapeHtml(planName)}</span>
        <strong class="text-xs text-slate-600 font-extrabold font-mono">₹ ${pricing.originalPrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>
      </div>
    `);
  }

  function formatPlanSelection(opt) {
    if (!opt || !opt.id) return opt ? (opt.text || '') : '';
    const elem = opt.element;
    if (!elem) return opt.text;

    const planName = elem.dataset.name || (opt.text ? opt.text.split('(')[0].trim() : 'Meal Plan');
    const basePrice = parseFloat(elem.dataset.price) || 0;
    const pricing = getEffectivePlanPricing(opt.id, basePrice);

    if (basePrice <= 0) {
      return $('<span>' + escapeHtml(planName) + ' <span class="text-slate-500 text-[11px] font-semibold">(Complimentary)</span></span>');
    }

    if (pricing.hasDiscount) {
      return $(`
        <span class="inline-flex items-center gap-1.5 font-bold text-xs">
          <span>${escapeHtml(planName)}</span>
          <span class="font-mono text-[11px] text-slate-400 line-through ml-0.5">₹ ${pricing.originalPrice.toFixed(2)}</span>
          <strong class="font-mono text-xs text-emerald-600 font-extrabold">₹ ${pricing.effectivePrice.toFixed(2)}</strong>
          <span class="text-[9px] font-extrabold bg-emerald-50 text-emerald-700 px-1 py-0.5 rounded border border-emerald-200">${pricing.discountText}</span>
        </span>
      `);
    }

    return $('<span>' + escapeHtml(planName) + ' <span class="font-mono font-extrabold text-slate-700 text-xs">(₹ ' + pricing.originalPrice.toFixed(2) + ')</span></span>');
  }

  function initPlanSelect2() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
      const planSelect = $('#plan-id-select');
      if (planSelect.length) {
        if (!planSelect.hasClass('select2-hidden-accessible')) {
          planSelect.select2({
            dropdownParent: $('#reserve-modal'),
            placeholder: '-- Select Meal Plan --',
            allowClear: true,
            width: '100%',
            templateResult: formatPlanOption,
            templateSelection: formatPlanSelection,
            escapeMarkup: function(m) { return m; }
          });
        }
        planSelect.off('change.planCalc').on('change.planCalc', function() {
          calculateReservationBilling();
        });
      }
    }
  }

  function refreshPlanSelectDisplay() {
    // 1. Update native option texts
    const planSelNative = document.getElementById('plan-id-select');
    if (planSelNative) {
      for (let i = 0; i < planSelNative.options.length; i++) {
        const opt = planSelNative.options[i];
        if (!opt.value) continue;
        const basePrice = parseFloat(opt.dataset.price) || 0;
        const pName = opt.dataset.name || opt.text.split('(')[0].trim();
        const pricing = getEffectivePlanPricing(opt.value, basePrice);
        if (pricing.hasDiscount) {
          opt.text = `${pName} (₹${pricing.effectivePrice.toFixed(2)} - was ₹${pricing.originalPrice.toFixed(2)})`;
        } else if (basePrice > 0) {
          opt.text = `${pName} (₹${basePrice.toFixed(2)})`;
        } else {
          opt.text = `${pName} (Complimentary)`;
        }
      }
    }

    // 2. Re-render Select2 if active
    if (typeof $ !== 'undefined' && $.fn.select2) {
      const planSelect = $('#plan-id-select');
      if (planSelect.length && planSelect.hasClass('select2-hidden-accessible')) {
        const currentVal = planSelect.val();
        planSelect.select2('destroy');
        initPlanSelect2();
        if (currentVal) {
          planSelect.val(currentVal).trigger('change.select2');
        }
      }
    }
  }

  window.addEventListener('DOMContentLoaded', () => {
    setInitialDateTime();
    initPlanSelect2();
    if (typeof $ !== 'undefined') {
      $('#company-select').off('change.corpSync').on('change.corpSync', function() {
        toggleNewCompany(this);
      });
    }
  });

  /* ==========================================================================
     Guest QR Code Mobile Document / Camera Upload (5-Minute Security Window)
     ========================================================================== */
  window.currentGuestUploadToken = null;
  window.currentGuestUploadUrl = null;
  window.guestUploadCountdownInterval = null;
  window.guestUploadPollInterval = null;

  async function initGuestUploadSession() {
    stopGuestUploadSession();

    // Reset card UI
    const overlay = document.getElementById('qr-expired-overlay');
    if (overlay) overlay.classList.add('hidden');
    const timerText = document.getElementById('qr-countdown-text');
    if (timerText) timerText.textContent = '05:00';
    const statusPill = document.getElementById('qr-live-status-pill');
    if (statusPill) {
      statusPill.className = 'text-[10px] font-extrabold text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-full inline-flex items-center gap-1';
      statusPill.innerHTML = '<i class="fa-solid fa-circle-check"></i> Live Ready';
    }

    // Read current primary guest inputs
    const guestBlock = document.querySelector('.guest-block');
    const gName = guestBlock?.querySelector('input[name="guest_name"]')?.value?.trim() || 'Guest #1';
    const gMobile = guestBlock?.querySelector('input[name="mobile"]')?.value?.trim() || '';
    const roomSelect = guestBlock?.querySelector('select[name="room_id"]');
    const roomNo = roomSelect?.selectedOptions?.[0]?.dataset?.roomNo || roomSelect?.value || '';
    const reserveIdInput = document.getElementById('input-reserve-id');
    const reserveId = reserveIdInput?.value?.trim() || '';

    // Update text labels on the card
    const nameLabel = document.getElementById('pass-guest-name-text');
    if (nameLabel) nameLabel.textContent = gName || 'Guest Primary';
    const mobileLabel = document.getElementById('pass-guest-mobile-text');
    if (mobileLabel) mobileLabel.textContent = gMobile ? `+91 ${gMobile}` : '+91 --';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    try {
      const response = await fetch("{{ route('frontoffice.guest-upload.init') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken || ''
        },
        body: JSON.stringify({
          guest_name: gName,
          mobile: gMobile,
          room: roomNo,
          reserve_id: reserveId
        })
      });

      const data = await response.json();
      if (!data.success) return;

      window.currentGuestUploadToken = data.token;
      window.currentGuestUploadUrl = data.upload_url;

      const tokenInput = document.getElementById('reserve-guest-upload-token');
      if (tokenInput) tokenInput.value = data.token;

      const qrImg = document.getElementById('guest-qr-image');
      if (qrImg) qrImg.src = data.qr_image_url;

      // Start 5-Minute Countdown
      let secondsLeft = parseInt(data.expires_in_seconds) || 300;
      window.guestUploadCountdownInterval = setInterval(() => {
        if (secondsLeft <= 0) {
          stopGuestUploadSession(false);
          if (overlay) overlay.classList.remove('hidden');
          if (timerText) timerText.textContent = '00:00 (Expired)';
          if (statusPill) {
            statusPill.className = 'text-[10px] font-extrabold text-amber-700 bg-amber-100/80 px-2 py-0.5 rounded-full inline-flex items-center gap-1';
            statusPill.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Link Expired';
          }
          return;
        }

        const m = Math.floor(secondsLeft / 60);
        const s = secondsLeft % 60;
        if (timerText) {
          timerText.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        }
        secondsLeft--;
      }, 1000);

      // Start Polling for uploads every 2.5 seconds
      window.guestUploadPollInterval = setInterval(async () => {
        if (!window.currentGuestUploadToken) return;
        try {
          const pollRes = await fetch(`/front-office/guest-upload/status/${window.currentGuestUploadToken}`);
          const pollData = await pollRes.json();

          if (!pollData.success) return;

          if (pollData.status === 'expired') {
            stopGuestUploadSession(false);
            if (overlay) overlay.classList.remove('hidden');
            if (timerText) timerText.textContent = '00:00 (Expired)';
            return;
          }

          if (pollData.status === 'completed' && Array.isArray(pollData.documents) && pollData.documents.length > 0) {
            clearInterval(window.guestUploadPollInterval);
            handleGuestUploadCompleted(pollData);
          }
        } catch (e) {
          console.error('QR Poll Error:', e);
        }
      }, 2500);

    } catch (err) {
      console.error('Error initializing guest upload session:', err);
    }
  }

  function stopGuestUploadSession(clearToken = true) {
    if (window.guestUploadCountdownInterval) {
      clearInterval(window.guestUploadCountdownInterval);
      window.guestUploadCountdownInterval = null;
    }
    if (window.guestUploadPollInterval) {
      clearInterval(window.guestUploadPollInterval);
      window.guestUploadPollInterval = null;
    }
    if (clearToken) {
      window.currentGuestUploadToken = null;
      window.currentGuestUploadUrl = null;
    }
  }

  function regenerateGuestQrSession() {
    stopGuestUploadSession();
    initGuestUploadSession();
    if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
      PmsAlert.toast('New 5-minute QR scan link generated!', 'success');
    }
  }

  function copyGuestUploadLink() {
    if (!window.currentGuestUploadUrl) {
      if (typeof PmsAlert !== 'undefined' && PmsAlert.toast) {
        PmsAlert.toast('Upload QR link is still generating...', 'info');
      }
      return;
    }
    navigator.clipboard.writeText(window.currentGuestUploadUrl).then(() => {
      if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 2500,
        });
        Toast.fire({
          icon: 'success',
          title: '📋 Mobile KYC Link copied! Share with guest.'
        });
      } else {
        alert('Upload link copied to clipboard!');
      }
    }).catch(err => {
      prompt('Copy this link:', window.currentGuestUploadUrl);
    });
  }

  function handleGuestUploadCompleted(data) {
    // 1. Store in hidden form field
    const docsInput = document.getElementById('reserve-uploaded-documents');
    if (docsInput) docsInput.value = JSON.stringify(data.documents);

    // 2. Update status badge
    const statusPill = document.getElementById('qr-live-status-pill');
    if (statusPill) {
      statusPill.className = 'text-[10px] font-extrabold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full inline-flex items-center gap-1';
      statusPill.innerHTML = `<i class="fa-solid fa-circle-check"></i> KYC Received (${data.documents_count})`;
    }

    // 3. Render thumbnails in Desktop Check-in modal
    const section = document.getElementById('guest-uploaded-docs-section');
    const grid = document.getElementById('guest-uploaded-docs-grid');
    const countLabel = document.getElementById('uploaded-docs-count');

    if (section && grid) {
      section.classList.remove('hidden');
      if (countLabel) countLabel.textContent = data.documents_count;

      grid.innerHTML = data.documents.map(doc => `
        <div class="relative group rounded-lg overflow-hidden border border-emerald-300 bg-white p-1 text-center shadow-2xs">
          <a href="${doc.url}" target="_blank" title="View Full Document">
            <img src="${doc.url}" class="w-full h-14 object-cover rounded hover:opacity-90 transition-opacity">
          </a>
          <div class="text-[9px] font-bold text-slate-700 truncate mt-1" title="${doc.name || doc.type}">
            ${doc.type || 'Document'}
          </div>
        </div>
      `).join('');
    }

    // 4. Auto-populate Identity Card Type & Number in the check-in modal if empty
    if (data.id_card_type) {
      const idSelect = document.querySelector('.pms-select-idtype');
      if (idSelect && !idSelect.value) {
        for (let opt of idSelect.options) {
          if (opt.text.toLowerCase().includes(data.id_card_type.toLowerCase())) {
            idSelect.value = opt.value;
            break;
          }
        }
      }
    }
    if (data.id_card_number) {
      const idInput = document.querySelector('.guest-id-number');
      if (idInput && !idInput.value) {
        idInput.value = data.id_card_number;
      }
    }

    // 5. Toast notification for receptionist
    if (typeof Swal !== 'undefined') {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true
      });
      Toast.fire({
        icon: 'success',
        title: `📸 ${data.documents_count} KYC Document(s) Received from Mobile!`
      });
    }
  }

  // Live Sync Primary Guest inputs to Pass Card
  document.addEventListener('input', (e) => {
    if (e.target.matches('input[name="guest_name"]') || e.target.classList.contains('guest-name-input')) {
      const firstGuestBlock = document.querySelector('.guest-block');
      if (firstGuestBlock && firstGuestBlock.contains(e.target)) {
        const passName = document.getElementById('pass-guest-name-text');
        if (passName) passName.textContent = e.target.value.trim() || 'Guest Primary';
      }
    }
    if (e.target.matches('input[name="mobile"]') || e.target.classList.contains('guest-mobile-input')) {
      const firstGuestBlock = document.querySelector('.guest-block');
      if (firstGuestBlock && firstGuestBlock.contains(e.target)) {
        const passMobile = document.getElementById('pass-guest-mobile-text');
        if (passMobile) passMobile.textContent = e.target.value.trim() ? `+91 ${e.target.value.trim()}` : '+91 --';
      }
    }
  });

  window.initGuestUploadSession = initGuestUploadSession;
  window.stopGuestUploadSession = stopGuestUploadSession;
  window.regenerateGuestQrSession = regenerateGuestQrSession;
  window.copyGuestUploadLink = copyGuestUploadLink;
</script>
