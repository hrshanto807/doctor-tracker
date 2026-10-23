<aside class="app-sidebar">
  <div class="sidebar-header">
    <a href="{{ route('dashboard') }}" class="brand-logo">
      <div class="brand-icon">
        <i class="fa-solid fa-notes-medical"></i>
      </div>
      <span>Doctor Tracker</span>
    </a>
  </div>

  <ul class="sidebar-nav">
    <div class="nav-section-title">Core Management</div>
    <li class="nav-item">
      <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <span><i class="fa-solid fa-chart-pie nav-icon"></i> Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('doctors.index') }}" class="nav-link-custom {{ request()->routeIs('doctors.*') ? 'active' : '' }}">
        <span><i class="fa-solid fa-user-doctor nav-icon"></i> Doctors</span>
        <span class="nav-badge badge-primary">{{ \App\Models\Doctor::count() }}</span>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{ route('patients.index') }}" class="nav-link-custom {{ request()->routeIs('patients.*') ? 'active' : '' }}">
        <span><i class="fa-solid fa-bed-pulse nav-icon"></i> Patients</span>
        <span class="nav-badge">{{ \App\Models\Patient::count() }}</span>
      </a>
    </li>

    <div class="nav-section-title">Institutional Analytics</div>
    <li class="nav-item">
      <a href="{{ route('dashboard') }}" class="nav-link-custom">
        <span><i class="fa-solid fa-chart-line nav-icon"></i> Real-Time Metrics</span>
      </a>
    </li>
  </ul>

  <div class="sidebar-footer">
    <div class="user-card-mini">
      <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200" alt="Admin" class="user-avatar-mini" />
      <div class="user-info-mini">
        <h6>{{ auth()->user()->name ?? 'Admin User' }}</h6>
        <span>Clinical Administrator</span>
      </div>
      <form action="{{ route('logout') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-link text-light p-1" title="Sign Out">
          <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </button>
      </form>
    </div>
  </div>
</aside>
