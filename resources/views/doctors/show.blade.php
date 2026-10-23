@extends('layouts.app')

@section('title', "Dr. {$doctor->name}")

@section('content')
<div class="mb-3">
  <a href="{{ route('doctors.index') }}" class="btn-custom btn-outline-custom btn-sm-custom">
    <i class="fa-solid fa-arrow-left"></i> Back to Doctors List
  </a>
</div>

<!-- ==========================================
     DOCTOR PROFILE HERO BANNER
     ========================================== -->
<div class="doctor-profile-banner">
  <div class="doctor-profile-content">
    <img src="{{ $doctor->avatar ?? asset('assets/images/default-doctor.svg') }}" alt="{{ $doctor->name }}" class="doctor-avatar-lg" onerror="this.onerror=null;this.src='{{ asset('assets/images/default-doctor.svg') }}';" />
    <div class="doctor-details-info">
      <span class="badge bg-white text-dark fw-bold px-3 py-1 rounded-pill mb-2">{{ $doctor->specialization }}</span>
      <h2>{{ $doctor->name }}</h2>
      <div class="doctor-details-meta">
        <div class="doctor-meta-item">
          <i class="fa-solid fa-hospital"></i>
          <span>{{ $doctor->hospital }}</span>
        </div>
        <div class="doctor-meta-item">
          <i class="fa-regular fa-envelope"></i>
          <span>{{ $doctor->email }}</span>
        </div>
        <div class="doctor-meta-item">
          <i class="fa-solid fa-phone"></i>
          <span>{{ $doctor->phone }}</span>
        </div>
        <div class="doctor-meta-item">
          <i class="fa-regular fa-calendar-check"></i>
          <span>Joined: {{ $doctor->joined_date?->format('M d, Y') ?? 'N/A' }}</span>
        </div>
      </div>
    </div>

    <!-- Doctor Quick Stats Pills -->
    <div class="doctor-stat-pills">
      <div class="doctor-stat-pill">
        <span class="pill-val">{{ $patients->count() }}</span>
        <span class="pill-lbl">Patients</span>
      </div>
      <div class="doctor-stat-pill">
        <span class="pill-val">{{ $criticalCount }}</span>
        <span class="pill-lbl">Critical</span>
      </div>
      <div class="doctor-stat-pill">
        <span class="pill-val">{{ $stableCount }}</span>
        <span class="pill-lbl">Stable/Rec</span>
      </div>
    </div>
  </div>
</div>

<!-- ==========================================
     CORRESPONDING PATIENT ROSTER SECTION
     ========================================== -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
  <div>
    <h4 class="fw-bold text-dark mb-1">Corresponding Patients</h4>
    <p class="text-muted small mb-0">Patients currently admitted or assigned directly under this doctor.</p>
  </div>
  <button type="button" class="btn-custom btn-primary-custom" onclick="openAddPatientUnderDoctorModal()">
    <i class="fa-solid fa-user-plus"></i> Add New Patient Under Doctor
  </button>
</div>

<!-- Filter toolbar for Doctor's patients -->
<div class="toolbar-card mb-3 p-3">
  <form action="{{ route('doctors.show', $doctor->id) }}" method="GET">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-6">
        <div class="position-relative">
          <input type="text" name="search" value="{{ request('search') }}" class="form-control-custom ps-5" placeholder="Search patients by name, code or phone..." />
          <i class="fa-solid fa-magnifying-glass input-icon-left"></i>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <select name="condition" class="form-select-custom" onchange="this.form.submit()">
          <option value="">All Health Conditions</option>
          <option value="Critical" {{ request('condition') === 'Critical' ? 'selected' : '' }}>Critical</option>
          <option value="Stable" {{ request('condition') === 'Stable' ? 'selected' : '' }}>Stable</option>
          <option value="Under Observation" {{ request('condition') === 'Under Observation' ? 'selected' : '' }}>Under Observation</option>
          <option value="Recovered" {{ request('condition') === 'Recovered' ? 'selected' : '' }}>Recovered</option>
        </select>
      </div>
      <div class="col-12 col-md-2 text-md-end">
        <span class="badge bg-light text-dark border py-2 px-3 fw-semibold">{{ $patients->count() }} Patients</span>
      </div>
    </div>
  </form>
</div>

<!-- Doctor's Patients Table -->
<div class="card-custom">
  <div class="table-responsive-custom">
    <table class="table-custom">
      <thead>
        <tr>
          <th>Patient Code</th>
          <th>Patient Name & Demographics</th>
          <th>Phone Contact</th>
          <th>Condition Status</th>
          <th>Room / Bed</th>
          <th>Admission Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($patients as $p)
          <tr>
            <td><span class="fw-bold text-dark">{{ $p->code }}</span></td>
            <td>
              <div class="user-cell">
                <div>
                  <span class="user-name-cell">{{ $p->name }}</span>
                  <span class="user-email-cell">{{ $p->age }} Years • {{ $p->gender }}</span>
                </div>
              </div>
            </td>
            <td><span class="text-muted"><i class="fa-solid fa-phone me-1 text-primary"></i>{{ $p->phone }}</span></td>
            <td>
              @php
                $cls = match($p->condition) {
                  'Critical' => 'badge-critical',
                  'Stable' => 'badge-stable',
                  'Under Observation' => 'badge-observation',
                  'Recovered' => 'badge-recovered',
                  default => 'badge-stable'
                };
              @endphp
              <span class="badge-status {{ $cls }}">{{ $p->condition }}</span>
            </td>
            <td><span class="fw-medium text-dark">{{ $p->room ?? 'General Ward' }}</span></td>
            <td><span class="text-muted small">{{ $p->admission_date?->format('M d, Y') ?? 'N/A' }}</span></td>
            <td>
              <div class="action-btn-group">
                <button type="button" class="action-btn action-btn-delete" onclick="promptDeletePatient('{{ $p->id }}', '{{ addslashes($p->name) }}')" title="Delete Patient from Doctor">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-5 text-muted">
              <i class="fa-solid fa-hospital-user empty-state-icon"></i>
              <h6>No patients found under this doctor</h6>
              <p class="small mb-3">Click below to assign or admit a new patient directly under this doctor.</p>
              <button type="button" class="btn-custom btn-primary-custom btn-sm-custom" onclick="openAddPatientUnderDoctorModal()">
                <i class="fa-solid fa-user-plus"></i> Add First Patient
              </button>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- ==========================================
     MODAL: ADD PATIENT UNDER THIS DOCTOR
     ========================================== -->
<div class="modal-overlay" id="addPatientUnderDoctorModal">
  <div class="modal-dialog-custom">
    <div class="modal-header-custom">
      <h5 class="modal-title-custom">
        <i class="fa-solid fa-user-plus text-primary me-2"></i> Add Patient Under <span class="text-primary">{{ $doctor->name }}</span>
      </h5>
      <button type="button" class="modal-close-btn" data-dismiss="modal">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form action="{{ route('patients.store') }}" method="POST">
      @csrf
      <input type="hidden" name="doctor_id" value="{{ $doctor->id }}" />
      <div class="modal-body-custom">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-custom">Patient Full Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control-custom" placeholder="e.g. Benjamin Ross" required />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Age <span class="text-danger">*</span></label>
            <input type="number" name="age" class="form-control-custom" min="1" max="120" value="38" required />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Gender <span class="text-danger">*</span></label>
            <select name="gender" class="form-select-custom" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Phone Number <span class="text-danger">*</span></label>
            <input type="tel" name="phone" class="form-control-custom" placeholder="+1 (555) 000-0000" required />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Patient Condition <span class="text-danger">*</span></label>
            <select name="condition" class="form-select-custom" required>
              <option value="Stable">Stable</option>
              <option value="Critical">Critical</option>
              <option value="Under Observation">Under Observation</option>
              <option value="Recovered">Recovered</option>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Room / Bed Assignment</label>
            <input type="text" name="room" class="form-control-custom" placeholder="e.g. Room 204" value="Room 201" />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Admission Date</label>
            <input type="date" name="admission_date" value="{{ now()->toDateString() }}" class="form-control-custom" />
          </div>

          <div class="col-12">
            <label class="form-label-custom">Clinical Diagnosis / Medical Notes</label>
            <textarea name="notes" class="form-control-custom" rows="2" placeholder="Initial findings, medication, or physician notes..."></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer-custom">
        <button type="button" class="btn-custom btn-outline-custom" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn-custom btn-primary-custom">
          <i class="fa-solid fa-check"></i> Admit Patient Under Doctor
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================
     MODAL: DELETE PATIENT CONFIRMATION
     ========================================== -->
<div class="modal-overlay" id="deletePatientModal">
  <div class="modal-dialog-custom modal-dialog-sm">
    <div class="modal-header-custom border-0 pb-0">
      <h5 class="modal-title-custom text-danger">
        <i class="fa-solid fa-triangle-exclamation me-1"></i> Delete Patient
      </h5>
      <button type="button" class="modal-close-btn" data-dismiss="modal">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="deletePatientForm" action="" method="POST">
      @csrf
      @method('DELETE')
      <div class="modal-body-custom pt-3">
        <p class="text-muted mb-2">
          Are you sure you want to remove <strong id="deletePatientName" class="text-dark">this patient</strong> from this doctor’s roster?
        </p>
        <div class="p-3 bg-danger-subtle text-danger rounded-3 small">
          This will permanently delete the patient admission record.
        </div>
      </div>

      <div class="modal-footer-custom border-0">
        <button type="button" class="btn-custom btn-outline-custom" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn-custom btn-danger-custom">
          <i class="fa-solid fa-trash-can"></i> Yes, Delete Patient
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function openAddPatientUnderDoctorModal() {
  openModal('addPatientUnderDoctorModal');
}

function promptDeletePatient(id, name) {
  const form = document.getElementById('deletePatientForm');
  form.action = `/patients/${id}`;
  document.getElementById('deletePatientName').textContent = name;
  openModal('deletePatientModal');
}
</script>
@endpush
