<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Administrator Master - Hotel Sagar Sonnet PMS')</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  @stack('styles')
</head>
<body class="min-h-full flex flex-col font-sans text-slate-800 bg-slate-100 antialiased overflow-x-hidden">

  <!-- Top PMS App Header & Navigation -->
  @include('admin.includes.header')

  <!-- Main Content Area -->
  <div class="flex-1 flex flex-col min-w-0">
    @yield('content')
  </div>

  <!-- Bottom App Bar -->
  <footer class="h-11 bg-white/95 backdrop-blur-md border-t border-slate-200/90 flex items-center justify-between px-6 shrink-0 z-30 shadow-2xs pms-bottom-bar">
    <div class="text-[11px] font-bold text-slate-600 flex items-center gap-1.5">
      <i class="fa-solid fa-code-branch text-indigo-600"></i>
      <span>Hotel Sagar Sonnet PMS v2026.1 • Enterprise Administrator Engine</span>
    </div>
    <div class="text-[11px] font-semibold text-slate-400 hidden sm:block">
      Active Modules: Front Office &amp; Room Management
    </div>
  </footer>

  <!-- Toast Notification Feedback -->
  <div class="fixed bottom-14 right-6 bg-slate-900 text-white px-5 py-3 rounded-xl text-xs font-bold flex items-center gap-2.5 shadow-2xl z-50 transition-all duration-300 translate-y-24 opacity-0 pointer-events-none toast-popup" id="app-toast">
    <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
    <span id="toast-msg">Action completed successfully!</span>
  </div>

  <!-- jQuery 3.7.1 -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Uniform Global SweetAlert Helper for Hotel Sagar Sonnet PMS
    const PmsAlert = {
      toast: function(title, icon = 'success') {
        Swal.fire({
          toast: true,
          position: 'top-end',
          icon: icon,
          title: title,
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          heightAuto: false,
          scrollbarPadding: false,
          customClass: {
            popup: 'pms-swal-popup'
          }
        });
      },
      confirm: function(title = 'Are you sure?', text = '', confirmText = '<i class="fa-solid fa-check mr-1.5"></i> Yes, Confirm', icon = 'question') {
        return Swal.fire({
          title: title,
          text: text,
          icon: icon,
          showCancelButton: true,
          confirmButtonText: confirmText,
          cancelButtonText: 'Cancel',
          heightAuto: false,
          scrollbarPadding: false,
          customClass: {
            popup: 'pms-swal-popup',
            title: 'pms-swal-title',
            htmlContainer: 'pms-swal-text',
            confirmButton: 'pms-swal-confirm-btn',
            cancelButton: 'pms-swal-cancel-btn',
            actions: 'swal2-actions'
          },
          buttonsStyling: false
        });
      },
      confirmDelete: function(title = 'Are you sure?', text = "This record will be permanently deleted from master database.") {
        return Swal.fire({
          title: title,
          text: text,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: '<i class="fa-solid fa-trash-can text-white mr-1.5"></i> Yes, Delete',
          cancelButtonText: 'Cancel',
          heightAuto: false,
          scrollbarPadding: false,
          customClass: {
            popup: 'pms-swal-popup',
            title: 'pms-swal-title',
            htmlContainer: 'pms-swal-text',
            confirmButton: 'pms-swal-confirm-btn pms-swal-danger-btn',
            cancelButton: 'pms-swal-cancel-btn',
            actions: 'swal2-actions'
          },
          buttonsStyling: false
        });
      },
      success: function(title, text = '') {
        return Swal.fire({
          icon: 'success',
          title: title,
          text: text,
          heightAuto: false,
          scrollbarPadding: false,
          customClass: {
            popup: 'pms-swal-popup',
            title: 'pms-swal-title',
            htmlContainer: 'pms-swal-text',
            confirmButton: 'pms-swal-confirm-btn'
          },
          buttonsStyling: false
        });
      },
      error: function(title, text = '') {
        return Swal.fire({
          icon: 'error',
          title: title,
          text: text,
          heightAuto: false,
          scrollbarPadding: false,
          customClass: {
            popup: 'pms-swal-popup',
            title: 'pms-swal-title',
            htmlContainer: 'pms-swal-text',
            confirmButton: 'pms-swal-confirm-btn pms-swal-danger-btn'
          },
          buttonsStyling: false
        });
      }
    };

    function showToast(msg) {
      PmsAlert.toast(msg, 'success');
    }

    // Robust Global Modal Helpers
    function openModal(id) {
      const m = document.getElementById(id);
      if (m) {
        m.classList.add('open', 'active', 'show');
        m.classList.remove('hidden');
        m.style.display = 'flex';
      }
    }

    function closeModal(id) {
      const m = document.getElementById(id);
      if (m) {
        m.classList.remove('open', 'active', 'show');
        m.classList.add('hidden');
        m.style.display = 'none';
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
  </script>

  @stack('scripts')
</body>
</html>
