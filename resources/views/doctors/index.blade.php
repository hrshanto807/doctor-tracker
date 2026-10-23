@extends('layouts.app')

@section('title', 'Doctor Management')

@section('content')
<!-- Page Title & Header Actions -->
<div class="page-header">
  <div>
    <h1 class="page-title">Doctor Management</h1>
    <p class="page-subtitle">Register new doctors, manage hospital affiliations, track patient loads, and view assigned rosters.</p>
  </div>
  <div class="page-actions">
    <button type="button" class="btn-custom btn-primary-custom" onclick="openAddDoctorModal()">
      <i class="fa-solid fa-user-plus"></i> Add Doctor
    </button>
  </div>
</div>

<!-- ==========================================
     SEARCH & MULTI-FILTER TOOLBAR
     ========================================== -->
<div class="toolbar-card">
  <form action="{{ route('doctors.index') }}" method="GET">
    <div class="filter-grid">
      <!-- Search Input -->
      <div>
        <label class="form-label-custom">Search Doctors</label>
        <div class="position-relative">
          <input type="text" name="search" value="{{ request('search') }}" class="form-control-custom ps-5" placeholder="Search by name, hospital, specialty..." />
          <i class="fa-solid fa-magnifying-glass input-icon-left"></i>
        </div>
      </div>

      <!-- Specialization Filter -->
      <div>
        <label class="form-label-custom">Specialization</label>
        <select name="specialization" class="form-select-custom" onchange="this.form.submit()">
          <option value="">All Specializations</option>
          @foreach($specializations as $spec)
            <option value="{{ $spec }}" {{ request('specialization') === $spec ? 'selected' : '' }}>{{ $spec }}</option>
          @endforeach
        </select>
      </div>

      <!-- Hospital Filter -->
      <div>
        <label class="form-label-custom">Hospital / Facility</label>
        <select name="hospital" class="form-select-custom" onchange="this.form.submit()">
          <option value="">All Hospitals</option>
          @foreach($hospitals as $hosp)
            <option value="{{ $hosp }}" {{ request('hospital') === $hosp ? 'selected' : '' }}>{{ $hosp }}</option>
          @endforeach
        </select>
      </div>

      <!-- Date-wise Filter -->
      <div>
        <label class="form-label-custom">Joined Since (Date)</label>
        <input type="date" name="date" value="{{ request('date') }}" class="form-control-custom" onchange="this.form.submit()" />
      </div>

      <!-- Reset Filters -->
      <div>
        <label class="form-label-custom d-none d-md-block">&nbsp;</label>
        <a href="{{ route('doctors.index') }}" class="btn-custom btn-outline-custom w-100" title="Reset all filters">
          <i class="fa-solid fa-arrow-rotate-left"></i> Reset
        </a>
      </div>
    </div>
  </form>
</div>

<!-- ==========================================
     DOCTORS DATA TABLE
     ========================================== -->
<div class="card-custom">
  <div class="card-header-custom">
    <h5 class="card-title-custom">
      <i class="fa-solid fa-user-doctor text-primary"></i> Registered Doctors Roster
    </h5>
    <span class="badge bg-light text-muted fw-semibold py-2 px-3 border">
      Total {{ $doctors->total() }} Doctors Registered
    </span>
  </div>

  <div class="table-responsive-custom">
    <table class="table-custom">
      <thead>
        <tr>
          <th>Doctor</th>
          <th>Specialization</th>
          <th>Hospital</th>
          <th>Phone Contact</th>
          <th>Assigned Patients</th>
          <th>Joined Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($doctors as $doc)
          <tr>
            <td>
              <div class="user-cell">
                <img src="{{ $doc->avatar ?? asset('assets/images/default-doctor.svg') }}" alt="{{ $doc->name }}" class="user-avatar-sm" onerror="this.onerror=null;this.src='{{ asset('assets/images/default-doctor.svg') }}';" />
                <div>
                  <a href="{{ route('doctors.show', $doc->id) }}" class="user-name-cell">{{ $doc->name }}</a>
                  <span class="user-email-cell"><i class="fa-regular fa-envelope me-1"></i>{{ $doc->email }}</span>
                </div>
              </div>
            </td>
            <td>
              <span class="badge-specialty">{{ $doc->specialization }}</span>
            </td>
            <td>
              <span class="fw-medium text-dark">{{ $doc->hospital }}</span>
            </td>
            <td>
              <span class="text-muted"><i class="fa-solid fa-phone me-1 text-primary"></i>{{ $doc->phone }}</span>
            </td>
            <td>
              <a href="{{ route('doctors.show', $doc->id) }}" class="badge-count text-decoration-none">
                <i class="fa-solid fa-user-injured me-1"></i>{{ $doc->patients_count }} Patients
              </a>
            </td>
            <td>
              <span class="text-muted small">{{ $doc->joined_date?->format('M d, Y') ?? 'N/A' }}</span>
            </td>
            <td>
              <div class="action-btn-group">
                <a href="{{ route('doctors.show', $doc->id) }}" class="action-btn action-btn-view" title="View Doctor & Patients">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <button type="button" class="action-btn action-btn-edit" onclick="openEditDoctorModal({{ json_encode($doc) }})" title="Edit Doctor">
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button type="button" class="action-btn action-btn-delete" onclick="promptDeleteDoctor('{{ $doc->id }}', '{{ addslashes($doc->name) }}')" title="Delete Doctor">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center py-5">
              <div class="text-muted">
                <i class="fa-solid fa-user-doctor empty-state-icon"></i>
                <h6>No doctors found</h6>
                <p class="small mb-0">Try clearing or adjusting your search filters.</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination Bar -->
  <div class="pagination-wrapper">
    <div class="pagination-info">
      Showing {{ $doctors->firstItem() ?? 0 }} to {{ $doctors->lastItem() ?? 0 }} of {{ $doctors->total() }} doctors
    </div>
    <div>
      {{ $doctors->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

<!-- ==========================================
     MODAL: CREATE / EDIT DOCTOR
     ========================================== -->
<div class="modal-overlay" id="doctorModal">
  <div class="modal-dialog-custom">
    <div class="modal-header-custom">
      <h5 class="modal-title-custom" id="doctorModalTitle">Add New Doctor</h5>
      <button type="button" class="modal-close-btn" data-dismiss="modal">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="doctorForm" action="{{ route('doctors.store') }}" method="POST">
      @csrf
      <input type="hidden" name="_method" id="doctorMethodInput" value="POST" />
      <div class="modal-body-custom">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-custom">Full Name <span class="text-danger">*</span></label>
            <div class="position-relative">
              <input type="text" name="name" id="doctorNameInput" class="form-control-custom ps-5" placeholder="e.g. Dr. Julian Montgomery" required />
              <i class="fa-solid fa-user-doctor input-icon-left"></i>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Specialization <span class="text-danger">*</span></label>
            <select name="specialization" id="doctorSpecializationInput" class="form-select-custom" required>
              <option value="">Select Specialization...</option>
              <option value="Cardiology">Cardiology</option>
              <option value="Neurology">Neurology</option>
              <option value="Pediatrics">Pediatrics</option>
              <option value="Orthopedics">Orthopedics</option>
              <option value="Dermatology">Dermatology</option>
              <option value="Oncology">Oncology</option>
              <option value="Nephrology">Nephrology</option>
              <option value="General Surgery">General Surgery</option>
              <option value="Psychiatry">Psychiatry</option>
              <option value="Gynecology">Gynecology</option>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Hospital / Facility <span class="text-danger">*</span></label>
            <input type="text" name="hospital" id="doctorHospitalInput" class="form-control-custom" placeholder="e.g. Care Guide Medical Center" required />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Phone Number <span class="text-danger">*</span></label>
            <div class="position-relative">
              <input type="tel" name="phone" id="doctorPhoneInput" class="form-control-custom ps-5" placeholder="+1 (555) 000-0000" required />
              <i class="fa-solid fa-phone input-icon-left"></i>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
            <div class="position-relative">
              <input type="email" name="email" id="doctorEmailInput" class="form-control-custom ps-5" placeholder="doctor@careguide.com" required />
              <i class="fa-regular fa-envelope input-icon-left"></i>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label-custom">Joined Date (Date-wise Tracking)</label>
            <input type="date" name="joined_date" id="doctorJoinedDateInput" value="{{ now()->toDateString() }}" class="form-control-custom" />
          </div>
        </div>
      </div>

      <div class="modal-footer-custom">
        <button type="button" class="btn-custom btn-outline-custom" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn-custom btn-primary-custom">
          <i class="fa-solid fa-floppy-disk"></i> Save Doctor Record
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================
     MODAL: DELETE DOCTOR CONFIRMATION
     ========================================== -->
<div class="modal-overlay" id="deleteDoctorModal">
  <div class="modal-dialog-custom modal-dialog-sm">
    <div class="modal-header-custom border-0 pb-0">
      <h5 class="modal-title-custom text-danger">
        <i class="fa-solid fa-triangle-exclamation me-1"></i> Delete Doctor
      </h5>
      <button type="button" class="modal-close-btn" data-dismiss="modal">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="deleteDoctorForm" action="" method="POST">
      @csrf
      @method('DELETE')
      <div class="modal-body-custom pt-3">
        <p class="text-muted mb-2">
          Are you sure you want to remove <strong id="deleteDoctorName" class="text-dark">this doctor</strong>?
        </p>
        <div class="p-3 bg-danger-subtle text-danger rounded-3 small">
          <i class="fa-solid fa-circle-exclamation me-1"></i>
          Deleting this doctor will also remove their assignment from corresponding patient records.
        </div>
      </div>

      <div class="modal-footer-custom border-0">
        <button type="button" class="btn-custom btn-outline-custom" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn-custom btn-danger-custom">
          <i class="fa-solid fa-trash-can"></i> Yes, Delete Doctor
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function openAddDoctorModal() {
  const form = document.getElementById('doctorForm');
  form.reset();
  form.action = "{{ route('doctors.store') }}";
  document.getElementById('doctorMethodInput').value = "POST";
  document.getElementById('doctorModalTitle').textContent = "Add New Doctor";
  document.getElementById('doctorJoinedDateInput').value = "{{ now()->toDateString() }}";
  openModal('doctorModal');
}

function openEditDoctorModal(doc) {
  const form = document.getElementById('doctorForm');
  form.action = `/doctors/${doc.id}`;
  document.getElementById('doctorMethodInput').value = "PUT";
  document.getElementById('doctorModalTitle').textContent = "Edit Doctor Details";

  document.getElementById('doctorNameInput').value = doc.name;
  document.getElementById('doctorSpecializationInput').value = doc.specialization;
  document.getElementById('doctorHospitalInput').value = doc.hospital;
  document.getElementById('doctorPhoneInput').value = doc.phone;
  document.getElementById('doctorEmailInput').value = doc.email;
  if (doc.joined_date) {
    document.getElementById('doctorJoinedDateInput').value = doc.joined_date.split('T')[0];
  }
  openModal('doctorModal');
}

function promptDeleteDoctor(id, name) {
  const form = document.getElementById('deleteDoctorForm');
  form.action = `/doctors/${id}`;
  document.getElementById('deleteDoctorName').textContent = name;
  openModal('deleteDoctorModal');
}
</script>
@endpush
