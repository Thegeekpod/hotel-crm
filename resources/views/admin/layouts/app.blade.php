<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Administrator Master - Hotel Sagar Sonnet PMS')</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Select2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <style>
    /* Select2 Luxury Theme Enhancements */
    .modal-window .select2-container {
      width: 100% !important;
    }
    .select2-container {
      max-width: 100%;
    }
    .select2-container--default .select2-selection--single {
      height: 38px !important;
      padding: 4px 12px !important;
      border: 1px solid var(--border-medium, #cbd5e1) !important;
      border-radius: var(--radius-md, 8px) !important;
      background: #ffffff !important;
      font-size: 12px !important;
      font-weight: 600 !important;
      color: var(--text-primary, #0f172a) !important;
      display: flex !important;
      align-items: center !important;
      box-sizing: border-box !important;
      transition: all 0.2s ease !important;
    }
    .modal-window .select2-container--default .select2-selection--single {
      height: 42px !important;
      padding: 6px 14px !important;
      font-size: 13px !important;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
      border-color: var(--accent-primary, #6366f1) !important;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
      outline: none !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 36px !important;
      right: 8px !important;
    }
    .modal-window .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 40px !important;
      right: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      padding-left: 0 !important;
      color: #0f172a !important;
      line-height: normal !important;
      font-weight: 600 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 40px !important;
      right: 10px !important;
    }
    .select2-dropdown {
      border: 1px solid var(--border-medium, #cbd5e1) !important;
      border-radius: var(--radius-md, 8px) !important;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
      z-index: 999999 !important;
      background: #ffffff !important;
      overflow: hidden !important;
    }
    .select2-search--dropdown {
      padding: 8px 10px !important;
      background: #f8fafc !important;
      border-bottom: 1px solid #e2e8f0 !important;
    }
    .select2-search--dropdown .select2-search__field {
      height: 34px !important;
      padding: 6px 12px !important;
      border-radius: 6px !important;
      border: 1px solid #cbd5e1 !important;
      font-size: 12px !important;
      background: #ffffff !important;
      outline: none !important;
    }
    .select2-results__option {
      padding: 8px 14px !important;
      font-size: 13px !important;
      color: #334155 !important;
      font-weight: 500 !important;
      transition: background 0.15s ease !important;
    }
    .select2-results__option--highlighted[aria-selected] {
      background: rgba(99, 102, 241, 0.1) !important;
      color: var(--accent-primary, #6366f1) !important;
      font-weight: 700 !important;
    }
    .select2-results__option[aria-selected=true] {
      background: var(--accent-primary, #6366f1) !important;
      color: #ffffff !important;
      font-weight: 700 !important;
    }
    /* Custom SweetAlert2 Theme for Luxury Hotel PMS */
    .swal2-popup.pms-swal-popup {
      font-family: var(--font-main, 'Plus Jakarta Sans', sans-serif) !important;
      border-radius: 20px !important;
      background: #ffffff !important;
      border: 1px solid #e2e8f0 !important;
      box-shadow: 0 25px 70px rgba(15, 23, 42, 0.16) !important;
      padding: 30px 24px 24px !important;
    }

    .swal2-icon.swal2-warning {
      border-color: #f59e0b !important;
      color: #f59e0b !important;
      margin: 0 auto 16px auto !important;
      width: 60px !important;
      height: 60px !important;
    }
    .swal2-icon.swal2-warning .swal2-icon-content {
      font-size: 34px !important;
      font-weight: 700 !important;
      color: #f59e0b !important;
    }

    .swal2-title.pms-swal-title {
      font-size: 19px !important;
      font-weight: 800 !important;
      color: #0f172a !important;
      letter-spacing: -0.4px !important;
      margin-bottom: 8px !important;
      padding: 0 !important;
    }

    .swal2-html-container.pms-swal-text {
      font-size: 13px !important;
      color: #64748b !important;
      line-height: 1.5 !important;
      margin: 0 0 22px 0 !important;
      padding: 0 10px !important;
    }

    .swal2-actions {
      gap: 12px !important;
      margin: 0 !important;
      width: 100% !important;
      justify-content: center !important;
    }

    .swal2-confirm.pms-swal-confirm-btn {
      background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
      color: #ffffff !important;
      border-radius: 10px !important;
      font-size: 13px !important;
      font-weight: 700 !important;
      padding: 10px 22px !important;
      box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35) !important;
      border: none !important;
      cursor: pointer !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 8px !important;
      transition: all 0.2s ease !important;
    }

    .swal2-confirm.pms-swal-confirm-btn:hover {
      transform: translateY(-1px) !important;
      box-shadow: 0 6px 20px rgba(99, 102, 241, 0.45) !important;
      color: #ffffff !important;
    }

    .swal2-confirm.pms-swal-danger-btn {
      background: linear-gradient(135deg, #ef4444, #f43f5e) !important;
      color: #ffffff !important;
      box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35) !important;
    }

    .swal2-confirm.pms-swal-danger-btn,
    .swal2-confirm.pms-swal-danger-btn * {
      color: #ffffff !important;
    }

    .swal2-confirm.pms-swal-danger-btn:hover {
      background: linear-gradient(135deg, #dc2626, #e11d48) !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 6px 22px rgba(239, 68, 68, 0.48) !important;
      color: #ffffff !important;
    }

    .swal2-cancel.pms-swal-cancel-btn {
      background: #f1f5f9 !important;
      color: #475569 !important;
      border-radius: 10px !important;
      font-size: 13px !important;
      font-weight: 700 !important;
      padding: 10px 22px !important;
      border: 1px solid #e2e8f0 !important;
      cursor: pointer !important;
      transition: all 0.2s ease !important;
    }

    .swal2-cancel.pms-swal-cancel-btn:hover {
      background: #e2e8f0 !important;
      color: #1e293b !important;
      transform: translateY(-1px) !important;
    }

    /* Modal Backdrop & Display Fixes */
    .modal-backdrop {
      position: fixed !important;
      inset: 0 !important;
      background: rgba(15, 23, 42, 0.55) !important;
      backdrop-filter: blur(8px) !important;
      -webkit-backdrop-filter: blur(8px) !important;
      z-index: 99999 !important;
      display: none;
      align-items: center !important;
      justify-content: center !important;
      padding: 20px !important;
    }

    .modal-backdrop.open,
    .modal-backdrop.active,
    .modal-backdrop.show {
      display: flex !important;
    }

    .modal-window {
      background: #ffffff !important;
      border-radius: 16px !important;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25) !important;
      overflow: hidden !important;
      border: 1px solid #e2e8f0 !important;
      position: relative !important;
      z-index: 100000 !important;
      display: flex !important;
      flex-direction: column !important;
      max-height: 90vh !important;
      animation: modalSlideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .modal-window form {
      display: flex !important;
      flex-direction: column !important;
      flex: 1 1 auto !important;
      min-height: 0 !important;
      overflow: hidden !important;
    }

    .modal-content-area {
      overflow-y: auto !important;
      -webkit-overflow-scrolling: touch !important;
      flex: 1 1 auto !important;
      min-height: 0 !important;
    }

    @keyframes modalSlideUp {
      from {
        opacity: 0;
        transform: translateY(20px) scale(0.97);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .modal-top {
      background: linear-gradient(135deg, var(--accent-primary, #6366f1), var(--accent-secondary, #a855f7)) !important;
      color: #ffffff !important;
      padding: 16px 22px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      flex-shrink: 0 !important;
    }

    .modal-bot {
      flex-shrink: 0 !important;
    }

    .modal-top h3 {
      color: #ffffff !important;
      font-size: 15px !important;
      font-weight: 800 !important;
      margin: 0 !important;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .modal-close {
      background: rgba(255, 255, 255, 0.15) !important;
      border: none !important;
      color: #ffffff !important;
      font-size: 18px !important;
      width: 28px !important;
      height: 28px !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      cursor: pointer !important;
      transition: all 0.2s ease !important;
      line-height: 1 !important;
    }

    /* Toast Popup Notification System */
    .toast-popup {
      position: fixed !important;
      bottom: 60px !important;
      right: 24px !important;
      background: #0f172a !important;
      color: #ffffff !important;
      padding: 12px 20px !important;
      border-radius: var(--radius-md, 12px) !important;
      font-size: 12px !important;
      font-weight: 700 !important;
      display: flex !important;
      align-items: center !important;
      gap: 10px !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
      z-index: 999999 !important;
      transform: translateY(100px) !important;
      opacity: 0 !important;
      pointer-events: none !important;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .toast-popup.show {
      transform: translateY(0) !important;
      opacity: 1 !important;
      pointer-events: auto !important;
    }

    /* Bottom System Bar */
    .pms-bottom-bar {
      height: 46px !important;
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
      border-top: 1px solid var(--border-subtle, rgba(148, 163, 184, 0.2)) !important;
      display: flex !important;
      align-items: center !important;
      justify-content: space-between !important;
      padding: 0 20px !important;
      flex-shrink: 0 !important;
      position: relative !important;
      z-index: 90 !important;
    }
  </style>
  @stack('styles')
</head>
<body>

  <!-- Top PMS App Header & Navigation -->
  @include('admin.includes.header')

  <!-- Main Content Area -->
  @yield('content')

  <!-- Bottom App Bar -->
  <footer class="pms-bottom-bar">
    <div style="font-size: 11px; font-weight: 700; color: var(--text-secondary);">
      <i class="fa-solid fa-code-branch" style="color: var(--accent-primary);"></i> Hotel Sagar Sonnet PMS v2026.1 • Enterprise Administrator Engine
    </div>
    <div style="font-size: 11px; font-weight: 600; color: var(--text-muted);">
      Active Modules: Front Office & Room Management
    </div>
  </footer>

  <!-- Toast Notification Feedback -->
  <div class="toast-popup" id="app-toast">
    <i class="fa-solid fa-circle-check" style="color: var(--accent-emerald); font-size: 16px;"></i>
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
          customClass: {
            popup: 'pms-swal-popup'
          }
        });
      },
      confirmDelete: function(title = 'Are you sure?', text = "This record will be permanently deleted from master database.") {
        return Swal.fire({
          title: title,
          text: text,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: '<i class="fa-solid fa-trash-can" style="color: #ffffff !important; margin-right: 6px;"></i> Yes, Delete',
          cancelButtonText: 'Cancel',
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
        m.classList.add('open');
        m.classList.add('active');
        m.classList.add('show');
        m.style.display = 'flex';
      }
    }

    function closeModal(id) {
      const m = document.getElementById(id);
      if (m) {
        m.classList.remove('open');
        m.classList.remove('active');
        m.classList.remove('show');
        m.style.display = 'none';
      }
    }

    // Modals are closed explicitly only via Cancel or X button
    // (Backdrop click and ESC auto-close disabled per requirements)
  </script>

  @stack('scripts')
</body>
</html>
