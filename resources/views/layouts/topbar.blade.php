<header class="app-topbar">
  <div class="topbar-left">
    <button id="sidebarToggleBtn" class="topbar-toggle-btn" aria-label="Toggle Navigation">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="topbar-search">
      <form action="{{ route('patients.index') }}" method="GET" class="m-0 position-relative">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patients or doctors..." />
      </form>
    </div>
  </div>

  <div class="topbar-right">
    <a href="{{ route('doctors.index') }}" class="btn-custom btn-primary-custom btn-sm-custom d-none d-sm-inline-flex">
      <i class="fa-solid fa-user-plus"></i> New Doctor
    </a>
    <a href="{{ route('patients.index') }}" class="btn-custom btn-outline-custom btn-sm-custom d-none d-sm-inline-flex">
      <i class="fa-solid fa-bed"></i> Admit Patient
    </a>

    <div class="dropdown">
      <button class="profile-dropdown-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200" alt="Admin" class="profile-avatar" />
        <div class="d-none d-md-block text-start">
          <div class="profile-name">{{ auth()->user()->name ?? 'Admin User' }}</div>
          <div class="profile-role">Super Administrator</div>
        </div>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2">
        <li><h6 class="dropdown-header">Authenticated Session</h6></li>
        <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fa-solid fa-house-chimney me-2 text-primary"></i> Dashboard</a></li>
        <li><a class="dropdown-item" href="{{ route('doctors.index') }}"><i class="fa-solid fa-user-doctor me-2 text-primary"></i> Doctors Directory</a></li>
        <li><a class="dropdown-item" href="{{ route('patients.index') }}"><i class="fa-solid fa-bed-pulse me-2 text-primary"></i> Patients List</a></li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="fa-solid fa-power-off me-2"></i> Log Out
            </button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</header>
