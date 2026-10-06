import os
import glob

new_modal_html = """<!-- NEW RESERVATION MODAL -->
  <div class="modal-backdrop" id="reserve-modal">
    <div class="modal-window large" style="width: 960px; max-width: 95vw;">
      <div class="modal-top" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0;"><i class="fa-solid fa-calendar-plus"></i> New Reservation • Hotel Sagar Sonnet</h3>
        </div>
        <button class="modal-close" onclick="closeModal('reserve-modal')">&times;</button>
      </div>
      <div class="modal-content-area" style="max-height: 75vh; overflow-y: auto; padding: 24px; background: #f8fafc;">
        <form onsubmit="handleReserve(event)">
          <!-- Radio Buttons -->
          <div style="display: flex; gap: 24px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-medium);">
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; cursor: pointer; font-size: 13px; color: var(--accent-primary);">
              <input type="radio" name="res_type" value="new" checked onchange="toggleResType()" style="accent-color: var(--accent-primary); transform: scale(1.2);"> New
            </label>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; cursor: pointer; font-size: 13px; color: var(--text-secondary);">
              <input type="radio" name="res_type" value="regular" onchange="toggleResType()" style="accent-color: var(--accent-primary); transform: scale(1.2);"> Regular
            </label>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; cursor: pointer; font-size: 13px; color: var(--text-secondary);">
              <input type="radio" name="res_type" value="company" onchange="toggleResType()" style="accent-color: var(--accent-primary); transform: scale(1.2);"> Company
            </label>
            <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; cursor: pointer; font-size: 13px; color: var(--text-secondary);">
              <input type="radio" name="res_type" value="privilege" onchange="toggleResType()" style="accent-color: var(--accent-primary); transform: scale(1.2);"> Privilege
            </label>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 280px; gap: 28px;">
            <!-- Left Column: Form Fields -->
            <div>
              <!-- Regular Search Section -->
              <div id="section-regular-search" style="display: none; margin-bottom: 24px; padding: 18px; background: #fff; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 8px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Search Regular Guest</label>
                <div style="display: flex; gap: 12px;">
                  <input type="text" style="flex: 1; height:42px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary); font-family: var(--font-main);" placeholder="Enter Mobile Number">
                  <button type="button" class="btn-ui-primary" style="height: 42px; padding: 0 24px;" onclick="alert('Searching database...')"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                  <button type="button" class="btn-ui-secondary" style="height: 42px; padding: 0 20px;"><i class="fa-solid fa-pen"></i> Edit</button>
                </div>
              </div>

              <!-- General Fields Container -->
              <div id="section-guest-details" style="display: flex; flex-direction: column; background: #fff; padding: 24px; border: 1px solid var(--border-medium); border-radius: var(--radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                
                <div style="display: flex; gap: 16px; margin-bottom: 20px; order: -2;">
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Reserve ID</label>
                    <input type="text" class="pms-input-field" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #f1f5f9; color: var(--text-primary); font-weight: 700; opacity: 0.8;" value="829\\2026-2027" disabled>
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Reserve Date</label>
                    <input type="date" id="reserve-date-input" class="pms-input-field" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Reserve Time</label>
                    <input type="time" id="reserve-time-input" class="pms-input-field" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                  </div>
                </div>
                <div style="margin-bottom: 20px;">
                  <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Mode of Reserve</label>
                  <select class="pms-input-field" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                    <option>Phone</option>
                    <option>Physical</option>
                    <option>Website</option>
                    <option>Enquiry</option>
                  </select>
                </div>

                <!-- Company Specific Fields -->
                <div id="section-company-fields" style="display: none; padding: 20px; background: rgba(16, 185, 129, 0.05); border: 1px dashed var(--accent-emerald); border-radius: var(--radius-md); margin-bottom: 20px;">
                  <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--accent-emerald); text-transform: uppercase; letter-spacing: 1px;"><i class="fa-solid fa-building"></i> Company Name</label>
                    <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--accent-emerald); border-radius: var(--radius-md); background: #fff; color: var(--text-primary); font-weight: 700;" onchange="toggleNewCompany(this)">
                      <option value="">-- Select Corporate Account --</option>
                      <option value="infosys">Infosys Technologies Ltd.</option>
                      <option value="tcs">Tata Consultancy Services</option>
                      <option value="wipro">Wipro Limited</option>
                      <option value="new" style="color: var(--accent-primary); font-weight: 900;">+ Register New Company</option>
                    </select>
                  </div>
                  <div id="new-company-details" style="display: none; border-top: 1px solid rgba(16, 185, 129, 0.2); padding-top: 16px;">
                    <div style="margin-bottom: 16px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">New Company Name</label>
                      <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="Full Corporate Entity Name">
                    </div>
                    <div style="margin-bottom: 16px;">
                      <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Company Address</label>
                      <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="Billing Address">
                    </div>
                    <div style="display: flex; gap: 16px;">
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">GSTIN</label>
                        <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; font-family: monospace;" placeholder="29ABCDE1234F1Z5">
                      </div>
                      <div style="flex: 1;">
                        <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Corporate Phone</label>
                        <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="Office / Mobile">
                      </div>
                    </div>
                  </div>
                </div>

                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                  <div style="width: 100px;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Title</label>
                    <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; color: var(--text-primary);">
                      <option>Mr.</option>
                      <option>Mrs.</option>
                      <option>Ms.</option>
                      <option>Dr.</option>
                      <option>Prof.</option>
                    </select>
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Guest Name</label>
                    <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="First and Last Name" required>
                  </div>
                </div>

                <div style="margin-bottom: 16px;">
                  <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Guest Address</label>
                  <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="House No, Street, Landmark">
                </div>

                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Nationality</label>
                    <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;">
                      <option>Indian</option>
                      <option>Foreign National</option>
                      <option>NRI</option>
                    </select>
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">City</label>
                    <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;">
                      <option>Kolkata</option>
                      <option>Delhi</option>
                      <option>Mumbai</option>
                      <option>Chennai</option>
                      <option>Bengaluru</option>
                      <option>Other</option>
                    </select>
                  </div>
                </div>

                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Telephone / Mobile</label>
                    <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="+91" required>
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Email ID</label>
                    <input type="email" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;" placeholder="guest@email.com">
                  </div>
                </div>

                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Date of Birth</label>
                    <input type="date" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;">
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Anniversary</label>
                    <input type="date" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;">
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Status</label>
                    <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; font-weight: 700; color: var(--accent-emerald);">
                      <option>Confirmed</option>
                      <option>Tentative</option>
                      <option>Waitlisted</option>
                    </select>
                  </div>
                </div>
                


                <div style="border-top: 1px dashed var(--border-medium); margin: 24px 0;"></div>

                <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 8px; margin-bottom: 24px; padding: 12px 16px; background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-md); order: 0;" id="privilege-checkbox-container">
                  <div style="width: 100%;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 13px; cursor: pointer; color: #059669;">
                      <input type="checkbox" id="chk-privilege" onchange="togglePrivilegeInput()" style="width: 16px; height: 16px; cursor: pointer; accent-color: #059669;"> Has Privilege Card
                    </label>
                  </div>
                  <div id="privilege-input-container" style="display: none; width: 100%;">
                    <input type="text" style="width:100%; max-width: 250px; height:36px; padding:6px 12px; font-size:13px; border:1px solid #059669; border-radius: var(--radius-sm); background: #fff; color: var(--text-primary); font-weight: 600;" placeholder="Privilege Card No.">
                  </div>
                </div>

                <h4 style="font-size: 12px; color: var(--accent-primary); margin-bottom: 16px; text-transform: uppercase; font-weight: 900; letter-spacing: 1px;"><i class="fa-solid fa-id-card"></i> Identity Details</h4>
                <div style="display: flex; gap: 16px; margin-bottom: 16px;">
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Identity Card Type</label>
                    <select style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff;">
                      <option>Aadhar Card</option>
                      <option>PAN Card</option>
                      <option>Driving License</option>
                      <option>Passport</option>
                      <option>Voter ID</option>
                    </select>
                  </div>
                  <div style="flex: 1;">
                    <label style="display:block; font-size: 10px; font-weight: 800; margin-bottom: 6px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;">Card Number</label>
                    <input type="text" style="width:100%; height:40px; padding:8px 14px; font-size:13px; border:1px solid var(--border-medium); border-radius: var(--radius-md); background: #fff; font-family: monospace;" placeholder="ID Number">
                  </div>
                </div>


              </div>
            </div>

            <!-- Right Column: Media / Action / QR Code -->
            <div style="display: flex; flex-direction: column; align-items: center; background: #fff; padding: 24px; border-radius: var(--radius-md); border: 1px solid var(--border-medium); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
              <div style="width: 180px; height: 180px; background: #f8fafc; border: 2px dashed var(--border-medium); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); text-align: center; font-size: 12px; margin-bottom: 24px; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.borderColor='var(--accent-primary)'; this.style.background='rgba(99,102,241,0.05)';" onmouseout="this.style.borderColor='var(--border-medium)'; this.style.background='#f8fafc';">
                <div>
                  <i class="fa-solid fa-camera" style="font-size: 32px; margin-bottom: 12px; color: var(--accent-primary);"></i><br>
                  <strong style="color: var(--text-primary);">Capture Guest Photo</strong><br>
                  <span style="font-size: 10px;">or browse image</span>
                </div>
              </div>
              
              <div style="width: 140px; height: 140px; background: #fff; border: 1px solid var(--border-medium); border-radius: 12px; padding: 12px; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=116x116&data=Reservation829" alt="QR Code" style="max-width: 100%; opacity: 0.8;">
              </div>
              <div style="text-align: center; font-size: 10px; color: var(--text-secondary); font-weight: 800; margin-top: 12px; text-transform: uppercase; letter-spacing: 1px;"><i class="fa-solid fa-qrcode"></i> Scan for Check-In</div>
              
              <div style="margin-top: auto; width: 100%; padding-top: 32px;">
                <button type="submit" class="btn-ui-success" style="width: 100%; height: 48px; justify-content: center; margin-bottom: 12px; font-size: 14px; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);">
                  <i class="fa-solid fa-floppy-disk"></i> Save Reservation
                </button>
                <button type="button" class="btn-ui-secondary" style="width: 100%; height: 44px; justify-content: center;" onclick="closeModal('reserve-modal')">
                  Cancel
                </button>
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
  
  <style>
    .modal-window input:not([type="radio"]):not([type="checkbox"]):focus, 
    .modal-window select:focus {
      outline: none !important;
      border-color: var(--accent-primary) !important;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
    }
  </style>
"""

new_script_html = """<script>
    // -- RESERVATION FORM LOGIC --
    function toggleResType() {
      const type = document.querySelector('input[name="res_type"]:checked').value;
      const regSearch = document.getElementById('section-regular-search');
      const compFields = document.getElementById('section-company-fields');
      const privContainer = document.getElementById('privilege-checkbox-container');
      const privInput = document.getElementById('privilege-input-container');
      const chkPriv = document.getElementById('chk-privilege');
      const labels = document.querySelectorAll('input[name="res_type"]');
      
      // Update label colors
      labels.forEach(radio => {
        radio.parentElement.style.color = radio.checked ? 'var(--accent-primary)' : 'var(--text-secondary)';
      });

      // Reset visibilities
      regSearch.style.display = 'none';
      compFields.style.display = 'none';
      privContainer.style.display = 'flex';
      privContainer.style.order = '0';
      
      if (type === 'regular') {
        regSearch.style.display = 'block';
      } else if (type === 'company') {
        compFields.style.display = 'block';
        privContainer.style.display = 'none';
        privInput.style.display = 'none';
        chkPriv.checked = false;
      } else if (type === 'privilege') {
        chkPriv.checked = true;
        privContainer.style.display = 'flex'; 
        privContainer.style.order = '-1';
        privInput.style.display = 'block';
      }
      
      togglePrivilegeInput();
    }

    function togglePrivilegeInput() {
      const type = document.querySelector('input[name="res_type"]:checked').value;
      const chkPriv = document.getElementById('chk-privilege');
      const privInput = document.getElementById('privilege-input-container');
      
      if (type === 'company') {
        privInput.style.display = 'none';
      } else if (type === 'privilege') {
        privInput.style.display = 'block';
      } else {
        privInput.style.display = chkPriv.checked ? 'block' : 'none';
      }
    }

    function toggleNewCompany(selectElem) {
      const newCompDetails = document.getElementById('new-company-details');
      if (selectElem.value === 'new') {
        newCompDetails.style.display = 'block';
      } else {
        newCompDetails.style.display = 'none';
      }
    }

    function setInitialDateTime() {
      const now = new Date();
      const dateInput = document.getElementById('reserve-date-input');
      const timeInput = document.getElementById('reserve-time-input');
      
      if (dateInput && timeInput) {
        // Format YYYY-MM-DD
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        dateInput.value = `${year}-${month}-${day}`;
        
        // Format HH:MM
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        timeInput.value = `${hours}:${minutes}`;
      }
    }
    
    // Call once to set the time when script loads
    setInitialDateTime();
"""

for filename in glob.glob('*.html'):
    with open(filename, 'r', encoding='utf-8') as f:
        content = f.read()
    
    start_idx = content.find("<!-- NEW RESERVATION MODAL -->")
    end_idx = content.find('<div id="print-area"', start_idx)
    
    if start_idx != -1 and end_idx != -1:
        content = content[:start_idx] + new_modal_html + "\n\n  " + content[end_idx:]
        
    if '// -- RESERVATION FORM LOGIC --' not in content:
        content = content.replace('<script>', new_script_html, 1)

    with open(filename, 'w', encoding='utf-8') as f:
        f.write(content)

print("Updated all html files successfully!")
