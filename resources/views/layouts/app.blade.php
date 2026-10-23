<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Doctor Tracker') - Clinical Management Portal</title>
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230d9488'><path d='M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z'/></svg>" />
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  
  <!-- 100% External Design System Styles -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
  @stack('styles')
</head>
<body>
  <!-- Mobile Backdrop Overlay -->
  <div id="sidebarBackdrop" class="modal-overlay sidebar-backdrop"></div>

  <!-- Toast Notification Container -->
  <div id="toastContainer" class="toast-container">
    @if(session('success'))
      <div class="toast-item toast-success">
        <div class="toast-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="toast-content">
          <div class="toast-title">Success</div>
          <div class="toast-msg">{{ session('success') }}</div>
        </div>
      </div>
    @endif
    @if(session('error') || (isset($errors) && $errors->any()))
      <div class="toast-item toast-danger">
        <div class="toast-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="toast-content">
          <div class="toast-title">Validation / Error</div>
          <div class="toast-msg">{{ session('error') ?? ($errors->first() ?? 'An error occurred.') }}</div>
        </div>
      </div>
    @endif
    @if(session('info'))
      <div class="toast-item toast-info">
        <div class="toast-icon"><i class="fa-solid fa-circle-info"></i></div>
        <div class="toast-content">
          <div class="toast-title">Information</div>
          <div class="toast-msg">{{ session('info') }}</div>
        </div>
      </div>
    @endif
  </div>

  <div class="app-wrapper">
    <!-- Sidebar Partial -->
    @include('layouts.sidebar')

    <!-- Main Content Area -->
    <main class="app-main">
      <!-- Topbar Header Partial -->
      @include('layouts.topbar')

      <!-- Page Content -->
      <div class="page-content">
        @yield('content')
      </div>
    </main>
  </div>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
  
  <script>
    // Global mobile sidebar toggle & modal helpers
    document.addEventListener("DOMContentLoaded", function () {
      const toggleBtn = document.getElementById("sidebarToggleBtn");
      const sidebar = document.querySelector(".app-sidebar");
      const overlay = document.getElementById("sidebarBackdrop");

      if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function () {
          sidebar.classList.toggle("show");
          if (overlay) overlay.classList.toggle("show");
        });
      }

      if (overlay && sidebar) {
        overlay.addEventListener("click", function () {
          sidebar.classList.remove("show");
          overlay.classList.remove("show");
        });
      }

      // Auto dismiss session toast notifications
      const toasts = document.querySelectorAll('.toast-item');
      toasts.forEach(t => {
        setTimeout(() => {
          t.classList.add('toast-hide');
          setTimeout(() => t.remove(), 300);
        }, 4000);
      });
    });

    function openModal(id) {
      const modal = document.getElementById(id);
      if (modal) {
        modal.classList.add('show');
        document.body.classList.add('modal-open');
      }
    }

    function closeModal(id) {
      const modal = document.getElementById(id);
      if (modal) {
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
      }
    }

    // Close on Escape or click outside
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        const active = document.querySelector('.modal-overlay.show:not(.sidebar-backdrop)');
        if (active) closeModal(active.id);
      }
    });

    document.addEventListener('click', function(e) {
      if (e.target.matches('[data-dismiss="modal"]') || e.target.closest('[data-dismiss="modal"]')) {
        const modal = e.target.closest('.modal-overlay');
        if (modal) closeModal(modal.id);
      }
      if (e.target.classList.contains('modal-overlay') && !e.target.classList.contains('sidebar-backdrop')) {
        closeModal(e.target.id);
      }
    });
  </script>
  @stack('scripts')
</body>
</html>
