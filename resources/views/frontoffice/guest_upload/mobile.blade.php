<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Guest Verification & KYC Upload | Hotel Sagar Sonnet</title>
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
    body {
      -webkit-tap-highlight-color: transparent;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }
    .pulse-ring {
      box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
      animation: pulse-ring 2s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    @keyframes pulse-ring {
      to {
        box-shadow: 0 0 0 14px rgba(79, 70, 229, 0);
      }
    }
  </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 pb-12 antialiased">

  <!-- Header Bar -->
  <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
    <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-sm text-sm">
          <i class="fa-solid fa-hotel"></i>
        </div>
        <div>
          <h1 class="text-xs font-black tracking-wider uppercase text-slate-900 leading-tight">Hotel Sagar Sonnet</h1>
          <p class="text-[10px] font-bold text-indigo-600 tracking-wide uppercase">Guest Fast Check-In KYC</p>
        </div>
      </div>
      
      @if(!$isExpired && !$isCompleted)
      <!-- 5-Minute Expiry Countdown Ticker -->
      <div id="countdown-badge" class="flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-800 rounded-full font-mono text-xs font-black shadow-2xs">
        <i class="fa-regular fa-clock text-amber-600 animate-spin" style="animation-duration: 4s;"></i>
        <span id="countdown-timer">05:00</span>
      </div>
      @endif
    </div>
  </header>

  <!-- Main Container -->
  <main class="max-w-md mx-auto px-4 pt-4">

    @if($isExpired)
      <!-- Expired Screen -->
      <div class="bg-white rounded-2xl p-6 border border-red-200 shadow-sm text-center mt-6">
        <div class="w-16 h-16 mx-auto bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-2xl mb-4">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h2 class="text-lg font-black text-slate-900 mb-1">QR Code Expired</h2>
        <div class="inline-block px-3 py-1 bg-red-50 text-red-700 text-xs font-bold rounded-full mb-3">
          5-Minute Security Window Expired
        </div>
        <p class="text-xs text-slate-600 leading-relaxed mb-5">
          For guest privacy and security, this upload link is valid for only 5 minutes. 
          Please inform the front desk receptionist to refresh the QR code on their screen to generate a new active link.
        </p>
        <div class="p-3 bg-slate-50 border border-dashed border-slate-200 rounded-xl text-[11px] text-slate-500 font-medium">
          <i class="fa-solid fa-circle-info text-indigo-500 mr-1"></i> Front desk can click <strong>"Refresh"</strong> in the check-in modal to immediately produce a new scan pass.
        </div>
      </div>

    @elseif($isCompleted)
      <!-- Completed Screen -->
      <div class="bg-white rounded-2xl p-6 border border-emerald-200 shadow-sm text-center mt-6">
        <div class="w-16 h-16 mx-auto bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl mb-4">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <h2 class="text-lg font-black text-slate-900 mb-1">Upload Successful!</h2>
        <div class="inline-block px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full mb-3">
          KYC Documents Received
        </div>
        <p class="text-xs text-slate-600 leading-relaxed mb-5">
          Thank you! Your documents have been securely transmitted to the front desk. The receptionist will complete your check-in right away.
        </p>
        <div class="p-3 bg-indigo-50/70 border border-indigo-200 rounded-xl text-xs text-indigo-700 font-bold">
          You may now safely close this browser window.
        </div>
      </div>

    @else
      <!-- Active Upload Form -->
      <form id="mobileUploadForm" onsubmit="handleMobileSubmit(event)">
        
        <!-- Guest Details Banner -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs mb-4">
          <div class="flex items-center justify-between mb-2 pb-2 border-b border-slate-100">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Guest Verification</span>
            <span class="text-[10px] font-extrabold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
              Room {{ $session->room_number ?? 'Auto-Assigned' }}
            </span>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-linear-to-br from-indigo-500 to-indigo-700 text-white flex items-center justify-center text-base font-bold shadow-xs">
              <i class="fa-solid fa-id-badge"></i>
            </div>
            <div class="flex-1">
              <div class="text-sm font-extrabold text-slate-900 leading-tight">
                {{ $session->guest_name ?: 'Guest / Primary Occupant' }}
              </div>
              <div class="text-xs text-slate-500 font-mono mt-0.5">
                {{ $session->mobile ?: 'Check-in Guest Verification' }}
              </div>
            </div>
          </div>
        </div>

        <!-- Identity Information -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs mb-4">
          <div class="text-xs font-black text-slate-800 uppercase tracking-wide flex items-center gap-2 mb-3">
            <i class="fa-solid fa-address-card text-indigo-600"></i> Identity Document Details
          </div>

          <div class="space-y-3">
            <div>
              <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1">
                ID Document Type <span class="text-indigo-600">*</span>
              </label>
              <select name="id_card_type" id="id_card_type" required class="w-full text-xs font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:border-indigo-600 focus:outline-none transition-all">
                <option value="Aadhaar Card" {{ ($session->id_card_type == 'Aadhaar Card') ? 'selected' : '' }}>Aadhaar Card</option>
                <option value="Passport" {{ ($session->id_card_type == 'Passport') ? 'selected' : '' }}>Passport</option>
                <option value="Driving License" {{ ($session->id_card_type == 'Driving License') ? 'selected' : '' }}>Driving License</option>
                <option value="Voter ID" {{ ($session->id_card_type == 'Voter ID') ? 'selected' : '' }}>Voter ID Card</option>
                <option value="PAN Card" {{ ($session->id_card_type == 'PAN Card') ? 'selected' : '' }}>PAN Card</option>
                <option value="Other Govt ID" {{ ($session->id_card_type == 'Other Govt ID') ? 'selected' : '' }}>Other Government ID</option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] font-black uppercase tracking-wider text-slate-500 mb-1">
                ID Card / Document Number <span class="text-slate-400 font-normal">(Optional)</span>
              </label>
              <input type="text" name="id_card_number" id="id_card_number" value="{{ $session->id_card_number }}" placeholder="e.g. 1234 5678 9012" class="w-full text-xs font-mono font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 focus:bg-white focus:border-indigo-600 focus:outline-none transition-all">
            </div>
          </div>
        </div>

        <!-- Document Capture Section -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs mb-4">
          <div class="flex items-center justify-between mb-3">
            <div class="text-xs font-black text-slate-800 uppercase tracking-wide flex items-center gap-2">
              <i class="fa-solid fa-camera text-indigo-600"></i> Capture Documents
            </div>
            <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
              Multi-Upload Enabled
            </span>
          </div>

          <!-- Quick Capture Slots: Live Selfie + Front + Back -->
          <div class="grid grid-cols-2 gap-2.5 mb-3">
            
            <!-- Slot 1: Live Guest Photo (Front camera) -->
            <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-xl p-3 bg-slate-50/60 text-center relative flex flex-col items-center justify-center min-h-[140px] transition-all cursor-pointer" onclick="document.getElementById('input-guest-selfie').click()">
              <input type="file" id="input-guest-selfie" accept="image/*" capture="user" class="hidden" onchange="handleFileSelected(this, 'Guest Live Photo', 'selfie-preview')">
              <div id="selfie-preview" class="w-full h-full flex flex-col items-center justify-center">
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-base mb-1.5">
                  <i class="fa-solid fa-user"></i>
                </div>
                <div class="text-[11px] font-black text-slate-800">Guest Photo</div>
                <div class="text-[9px] font-bold text-indigo-600 mt-0.5"><i class="fa-solid fa-camera"></i> Front Camera</div>
              </div>
            </div>

            <!-- Slot 2: ID Front Side (Rear camera) -->
            <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-xl p-3 bg-slate-50/60 text-center relative flex flex-col items-center justify-center min-h-[140px] transition-all cursor-pointer" onclick="document.getElementById('input-id-front').click()">
              <input type="file" id="input-id-front" accept="image/*" capture="environment" class="hidden" onchange="handleFileSelected(this, 'ID Front Side', 'front-preview')">
              <div id="front-preview" class="w-full h-full flex flex-col items-center justify-center">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-base mb-1.5">
                  <i class="fa-solid fa-id-card"></i>
                </div>
                <div class="text-[11px] font-black text-slate-800">ID Front Side</div>
                <div class="text-[9px] font-bold text-blue-600 mt-0.5"><i class="fa-solid fa-camera"></i> Rear Camera</div>
              </div>
            </div>

            <!-- Slot 3: ID Back Side (Rear camera) -->
            <div class="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-xl p-3 bg-slate-50/60 text-center relative flex flex-col items-center justify-center min-h-[140px] transition-all cursor-pointer" onclick="document.getElementById('input-id-back').click()">
              <input type="file" id="input-id-back" accept="image/*" capture="environment" class="hidden" onchange="handleFileSelected(this, 'ID Back Side', 'back-preview')">
              <div id="back-preview" class="w-full h-full flex flex-col items-center justify-center">
                <div class="w-10 h-10 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center text-base mb-1.5">
                  <i class="fa-solid fa-address-card"></i>
                </div>
                <div class="text-[11px] font-black text-slate-800">ID Back Side</div>
                <div class="text-[9px] font-bold text-cyan-600 mt-0.5"><i class="fa-solid fa-camera"></i> Rear Camera</div>
              </div>
            </div>

            <!-- Slot 4: Extra Document / Gallery Picker -->
            <div class="border-2 border-dashed border-indigo-200 hover:border-indigo-500 bg-indigo-50/30 rounded-xl p-3 text-center relative flex flex-col items-center justify-center min-h-[140px] transition-all cursor-pointer" onclick="document.getElementById('input-extra-files').click()">
              <input type="file" id="input-extra-files" accept="image/*" multiple class="hidden" onchange="handleMultipleFiles(this)">
              <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-base mb-1.5">
                <i class="fa-solid fa-images"></i>
              </div>
              <div class="text-[11px] font-black text-indigo-700">Add More Photos</div>
              <div class="text-[9px] font-bold text-slate-500 mt-0.5"><i class="fa-solid fa-plus"></i> Gallery / Camera</div>
            </div>

          </div>

          <!-- Captured Documents List Preview -->
          <div id="captured-files-container" class="space-y-2 mt-4 hidden">
            <div class="flex items-center justify-between text-[11px] font-black uppercase tracking-wider text-slate-600 pb-1 border-b border-slate-100">
              <span>Attached Documents (<span id="total-attached-count">0</span>)</span>
              <span class="text-[9px] text-indigo-600 font-bold">Tap to review</span>
            </div>
            <div id="captured-items-list" class="space-y-2"></div>
          </div>

          <!-- Add Another Document Custom Button -->
          <button type="button" onclick="document.getElementById('input-extra-files').click()" class="w-full mt-3 py-2.5 px-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-plus text-indigo-600"></i> Add Another Document / Photo
          </button>
        </div>

        <!-- Submit Button -->
        <button type="submit" id="submit-btn" class="w-full bg-linear-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-black text-sm py-3.5 px-4 rounded-xl shadow-md flex items-center justify-center gap-2 active:scale-98 transition-all">
          <i class="fa-solid fa-cloud-arrow-up text-base"></i>
          <span id="submit-btn-text">Submit KYC Documents</span>
        </button>

      </form>
    @endif

  </main>

  @if(!$isExpired && !$isCompleted)
  <script>
    // 5-Minute Countdown Timer
    let remainingSeconds = {{ (int)$expiresInSeconds }};
    const timerDisplay = document.getElementById('countdown-timer');
    const submitBtn = document.getElementById('submit-btn');

    function updateCountdown() {
      if (remainingSeconds <= 0) {
        if (timerDisplay) timerDisplay.textContent = '00:00';
        Swal.fire({
          icon: 'warning',
          title: 'Time Expired',
          text: 'The 5-minute security window has expired. Please ask the front desk to refresh the QR code.',
          confirmButtonColor: '#4f46e5'
        }).then(() => {
          window.location.reload();
        });
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        return;
      }

      const mins = Math.floor(remainingSeconds / 60);
      const secs = remainingSeconds % 60;
      if (timerDisplay) {
        timerDisplay.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
      }
      remainingSeconds--;
      setTimeout(updateCountdown, 1000);
    }
    updateCountdown();

    // In-memory list of document items to be uploaded
    // Array of { id: string, name: string, type: string, file: File, previewUrl: string }
    const uploadItems = [];

    function handleFileSelected(input, defaultType, previewContainerId) {
      if (!input.files || input.files.length === 0) return;
      const file = input.files[0];
      const previewUrl = URL.createObjectURL(file);

      // Render thumbnail inside the slot
      const container = document.getElementById(previewContainerId);
      if (container) {
        container.innerHTML = `
          <div class="relative w-full h-full flex flex-col items-center justify-center">
            <img src="${previewUrl}" class="w-full h-24 object-cover rounded-lg border border-slate-200">
            <div class="absolute bottom-1 bg-black/70 text-white text-[9px] font-bold px-1.5 py-0.5 rounded truncate max-w-[90%]">
              ${defaultType}
            </div>
            <div class="absolute top-1 right-1 w-5 h-5 bg-emerald-600 text-white rounded-full flex items-center justify-center text-[10px] shadow">
              <i class="fa-solid fa-check"></i>
            </div>
          </div>
        `;
      }

      // Add to upload list or update slot
      const existingIdx = uploadItems.findIndex(i => i.type === defaultType);
      const itemData = {
        id: 'slot_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
        name: file.name,
        type: defaultType,
        file: file,
        previewUrl: previewUrl
      };

      if (existingIdx >= 0) {
        uploadItems[existingIdx] = itemData;
      } else {
        uploadItems.push(itemData);
      }

      renderAttachedList();
    }

    function handleMultipleFiles(input) {
      if (!input.files || input.files.length === 0) return;
      for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];
        const previewUrl = URL.createObjectURL(file);
        uploadItems.push({
          id: 'extra_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5),
          name: file.name,
          type: 'Additional Document #' + (uploadItems.length + 1),
          file: file,
          previewUrl: previewUrl
        });
      }
      renderAttachedList();
      input.value = ''; // Reset input to allow selecting same file again if desired
    }

    function renderAttachedList() {
      const container = document.getElementById('captured-files-container');
      const list = document.getElementById('captured-items-list');
      const countLabel = document.getElementById('total-attached-count');

      if (!container || !list) return;

      if (uploadItems.length === 0) {
        container.classList.add('hidden');
        return;
      }

      container.classList.remove('hidden');
      if (countLabel) countLabel.textContent = uploadItems.length;

      list.innerHTML = uploadItems.map((item, idx) => `
        <div class="flex items-center justify-between p-2 bg-slate-50 border border-slate-200 rounded-xl gap-2">
          <div class="flex items-center gap-2.5 overflow-hidden">
            <img src="${item.previewUrl}" class="w-10 h-10 object-cover rounded-lg border border-slate-300 shrink-0">
            <div class="truncate">
              <div class="text-xs font-bold text-slate-800 truncate">${item.type}</div>
              <div class="text-[10px] text-slate-400 font-mono truncate">${item.name}</div>
            </div>
          </div>
          <button type="button" onclick="removeUploadItem('${item.id}')" class="text-slate-400 hover:text-red-500 p-1.5 text-xs transition-colors">
            <i class="fa-solid fa-trash-can"></i>
          </button>
        </div>
      `).join('');
    }

    function removeUploadItem(id) {
      const idx = uploadItems.findIndex(i => i.id === id);
      if (idx >= 0) {
        uploadItems.splice(idx, 1);
        renderAttachedList();
      }
    }

    async function handleMobileSubmit(e) {
      e.preventDefault();

      if (uploadItems.length === 0) {
        Swal.fire({
          icon: 'info',
          title: 'No Documents Captured',
          text: 'Please take or upload at least one photo of the Guest or ID Card.',
          confirmButtonColor: '#4f46e5'
        });
        return;
      }

      const submitBtn = document.getElementById('submit-btn');
      const btnText = document.getElementById('submit-btn-text');
      submitBtn.disabled = true;
      btnText.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Uploading documents...';

      try {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('id_card_type', document.getElementById('id_card_type').value);
        formData.append('id_card_number', document.getElementById('id_card_number').value);

        uploadItems.forEach((item, index) => {
          formData.append('images[]', item.file, item.name);
          formData.append(`document_types[${index}]`, item.type);
        });

        const response = await fetch('{{ route("guest.mobile-upload.submit", ["token" => $session->token]) }}', {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        });

        const result = await response.json();

        if (response.ok && result.success) {
          Swal.fire({
            icon: 'success',
            title: 'Uploaded Successfully!',
            text: 'Your documents have been received by reception.',
            confirmButtonColor: '#4f46e5',
            timer: 2500,
            showConfirmButton: false
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Upload Failed',
            text: result.message || 'Could not upload files. Please try again.',
            confirmButtonColor: '#4f46e5'
          });
          submitBtn.disabled = false;
          btnText.textContent = 'Submit KYC Documents';
        }
      } catch (err) {
        console.error('Upload error:', err);
        Swal.fire({
          icon: 'error',
          title: 'Network Error',
          text: 'An error occurred while uploading. Please check connection and retry.',
          confirmButtonColor: '#4f46e5'
        });
        submitBtn.disabled = false;
        btnText.textContent = 'Submit KYC Documents';
      }
    }
  </script>
  @endif

</body>
</html>
