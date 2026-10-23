<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Doctor Tracker Portal</title>
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%230d9488'><path d='M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z'/></svg>" />
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- 100% External Custom Stylesheets -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
</head>
<body>
  <div class="login-container">
    <!-- Left Hero Banner Side -->
    <div class="login-banner-side">
      <div>
        <div class="brand-logo mb-4">
          <div class="brand-icon">
            <i class="fa-solid fa-notes-medical"></i>
          </div>
          <span class="login-brand-text">Doctor Tracker</span>
        </div>
        <span class="badge bg-white text-dark fw-semibold px-3 py-2 rounded-pill mb-3">Healthcare Management SaaS</span>
        <h1 class="login-banner-title">
          Intelligent Doctor & Patient Management System
        </h1>
        <p class="login-banner-desc">
          Centralized administrative portal built for medical directors, clinics, and hospitals to optimize doctor allocations, track patient conditions, and analyze clinical trends.
        </p>

        <div class="feature-check-list">
          <div class="feature-check-item">
            <div class="feature-check-icon"><i class="fa-solid fa-check"></i></div>
            <span>Comprehensive Doctor Directory & Specialization filters</span>
          </div>
          <div class="feature-check-item">
            <div class="feature-check-icon"><i class="fa-solid fa-check"></i></div>
            <span>Doctor-Patient relationship mapping with 1-click admissions</span>
          </div>
          <div class="feature-check-item">
            <div class="feature-check-icon"><i class="fa-solid fa-check"></i></div>
            <span>Interactive Chart.js visual analytics & condition tracking</span>
          </div>
          <div class="feature-check-item">
            <div class="feature-check-icon"><i class="fa-solid fa-check"></i></div>
            <span>Production-ready Laravel 11/12 Eloquent & Blade Architecture</span>
          </div>
        </div>
      </div>

      <div class="pt-4 border-top border-white border-opacity-10 d-flex justify-content-between align-items-center">
        <span class="small opacity-75">Care Guide BD • Doctor Tracker Platform</span>
        <span class="login-footer-badge">v2.4 Protected</span>
      </div>
    </div>

    <!-- Right Login Form Side -->
    <div class="login-form-side">
      <div class="login-box">
        <div class="mb-4 text-center text-sm-start">
          <div class="d-inline-flex brand-icon mb-3 d-lg-none">
            <i class="fa-solid fa-notes-medical"></i>
          </div>
          <h2 class="login-title">Welcome Back</h2>
          <p class="text-muted small">Please authenticate to access the Doctor Tracker administrative portal.</p>
        </div>

        <!-- Quick Demo Credentials Box -->
        <div class="demo-credentials-box">
          <div class="demo-credentials-header">
            <span class="fw-bold text-dark small"><i class="fa-solid fa-key text-primary me-1"></i> Demo Credentials</span>
            <button type="button" class="btn btn-outline-primary btn-autofill" onclick="fillDemoCredentials()">
              Auto Fill
            </button>
          </div>
          <div class="d-flex justify-content-between small text-muted">
            <span><strong>Email:</strong> admin@doctor-tracker.com</span>
            <span><strong>Pass:</strong> admin123</span>
          </div>
        </div>

        @if(isset($errors) && $errors->any())
          <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
          </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="loginEmail" class="form-label-custom">Email Address</label>
            <div class="position-relative">
              <input type="email" name="email" id="loginEmail" class="form-control-custom ps-5" placeholder="admin@doctor-tracker.com" required value="{{ old('email', 'admin@doctor-tracker.com') }}" />
              <i class="fa-regular fa-envelope input-icon-left"></i>
            </div>
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label for="loginPassword" class="form-label-custom mb-0">Password</label>
              <a href="javascript:void(0)" class="small text-decoration-none" onclick="alert('Demo Mode: Please use password admin123')">Forgot Password?</a>
            </div>
            <div class="password-input-wrapper">
              <input type="password" name="password" id="loginPassword" class="form-control-custom ps-5 pe-5" placeholder="••••••••" required value="admin123" />
              <i class="fa-solid fa-lock input-icon-left"></i>
              <button type="button" id="togglePasswordBtn" class="password-toggle-btn" title="Toggle password visibility">
                <i class="fa-regular fa-eye" id="passwordEyeIcon"></i>
              </button>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" checked />
              <label class="form-check-label text-muted small" for="rememberMe">
                Remember this device
              </label>
            </div>
          </div>

          <button type="submit" class="btn-custom btn-primary-custom w-100 py-2 mb-3">
            <i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In to Portal
          </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
          <i class="fa-solid fa-shield-halved text-success me-1"></i> End-to-end encrypted session
        </p>
      </div>
    </div>
  </div>

  <script>
    function fillDemoCredentials() {
      document.getElementById("loginEmail").value = "admin@doctor-tracker.com";
      document.getElementById("loginPassword").value = "admin123";
    }

    const toggleBtn = document.getElementById("togglePasswordBtn");
    const pwdInput = document.getElementById("loginPassword");
    const eyeIcon = document.getElementById("passwordEyeIcon");

    if (toggleBtn && pwdInput) {
      toggleBtn.addEventListener("click", function() {
        if (pwdInput.type === "password") {
          pwdInput.type = "text";
          eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
          pwdInput.type = "password";
          eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
        }
      });
    }
  </script>
</body>
</html>
