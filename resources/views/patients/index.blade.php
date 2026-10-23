@extends('layouts.app')

@section('title', 'Patient Management')

@section('content')
<!-- Page Title & Header Actions -->
<div class="page-header">
  <div>
    <h1 class="page-title">Patient Management</h1>
    <p class="page-subtitle">Track patient admissions, clinical health conditions, assigned medical specialists, and room allocations.</p>
  </div>
  <div class="page-actions">
    <button type="button" class="btn-custom btn-primary-custom" onclick="openAddPatientModal()">
      <i class="fa-solid fa-user-plus"></i> Admit Patient
    </button>
  </div>
</div>

<!-- ==========================================
     SEARCH & MULTI-FILTER TOOLBAR
     ========================================== -->
<div class="toolbar-card">
  <form action="{{ route('patients.index') }}" method="GET">
    <div class="filter-grid">
      <!-- Search Input -->
      <div>
        <label class="form-label-custom">Search Patients</label>
        <div class="position-relative">
          <input type="text" name="search" value="{{ request('search') }}" class="form-control-custom ps-5" placeholder="Search by name, code, contact, doctor..." />
          <i class="fa-solid fa-magnifying-glass input-icon-left"></i>
        </div>
      </div>

      <!-- Condition Filter -->
      <div>
        <label class="form-label-custom">Patient Condition</label>
        <select name="condition" class="form-select-custom" onchange="this.form.submit()">
          <option value="">All Conditions</option>
          <option value="Critical" {{ request('condition') === 'Critical' ? 'selected' : '' }}>Critical</option>
          <option value="Stable" {{ request('condition') === 'Stable' ? 'selected' : '' }}>Stable</option>
          <option value="Under Observation" {{ request('condition') === 'Under Observation' ? 'selected' : '' }}>Under Observation</option>
          <option value="Recovered" {{ request('condition') === 'Recovered' ? 'selected' : '' }}>Recovered</option>
        </select>
      </div>

      <!-- Doctor Filter -->
      <div>
        <label class="form-label-custom">Assigned Doctor</label>
        <select name="doctor_id" class="form-select-custom" onchange="this.form.submit()">
          <option value="">All Doctors</option>
          @foreach($doctors as $d)
            <option value="{{ $d->id }}" {{ request('doctor_id') == $d->id ? 'selected' : '' }}>{{ $d->name }} ({{ $d->specialization }})</option>
          @endforeach
        </select>
      </div>

      <!-- Date-wise Filter -->
      <div>
        <label class="form-label-custom">Admitted Since (Date)</label>
        <input type="date" name="date" value="{{ request('date') }}" class="form-control-custom" onchange="this.form.submit()" />
      </div>

      <!-- Reset Filters -->
      <div>
        <label class="form-label-custom d-none d-md-block">&nbsp;</label>
        <a href="{{ route('patients.index') }}" class="btn-custom btn-outline-custom w-100" title="Reset all filters">
          <i class="fa-solid fa-arrow-rotate-left"></i> Reset
        </a>
      </div>
    </div>
  </form>
</div>

<!-- ==========================================
     PATIENTS DATA TABLE
     ========================================== -->
<div class="card-custom">
  <div class="card-header-custom">
    <h5 class="card-title-custom">
      <i class="fa-solid fa-bed-pulse text-primary"></i> Admitted Patients Directory
    </h5>
    <span class="badge bg-light text-muted fw-semibold py-2 px-3 border">
      Total {{ $patients->total() }} Patients
    </span>
  </div>

  <div class="table-responsive-custom">
    <table class="table-custom">
      <thead>
        <tr>
          <th>Patient ID</th>
          <th>Patient Details</th>
          <th>Assigned Doctor</th>
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
                  <span class="user-email-cell">{{ $p->age }} yrs • {{ $p->gender }}</span>
                </div>
              </div>
            </td>
            <td>
              @if($p->doctor)
                <a href="{{ route('doctors.show', $p->doctor->id) }}" class="fw-medium text-dark text-decoration-none">
                  {{ $p->doctor->name }}
                </a>
                <span class="d-block text-muted small">{{ $p->doctor->specialization }}</span>
              @else
                <span class="text-muted">Unassigned</span>
              @endif
            </td>
            <td>
              <span class="text-muted"><i class="fa-solid fa-phone me-1 text-primary"></i>{{ $p->phone }}</span>
            </td>
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
            <td>
              <span class="badge-specialty">{{ $p->room ?? 'General Ward' }}</span>
            </td>
            <td>
              <span class="text-muted small">{{ $p->admission_date?->format('M d, Y') ?? 'N/A' }}</span>
            </td>
            <td>
              <div class="action-btn-group">
                <button type="button" class="action-btn action-btn-edit" onclick="openEditPatientModal({{ json_encode($p) }})" title="Edit Patient">
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button type="button" class="action-btn action-btn-delete" onclick="promptDeletePatient('{{ $p->id }}', '{{ addslashes($p->name) }}')" title="Delete Patient Record">
                  <i class="fa-solid fa-trash-can"></i>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center py-5">
              <div class="text-muted">
                <i class="fa-solid fa-bed-pulse empty-state-icon"></i>
                <h6>No patient records match the criteria</h6>
                <p class="small mb-0">Try clearing filters or search query.</p>
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
      Showing {{ $patients->firstItem() ?? 0 }} to {{ $patients->lastItem() ?? 0 }} of {{ $patients->total() }} patients
    </div>
    <div>
      {{ $patients->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>

<!-- ==========================================
     MODAL: ADMIT / EDIT PATIENT
     ========================================== -->
<div class="modal-overlay" id="patientModal">
  <div class="modal-dialog-custom">
    <div class="modal-header-custom">
      <h5 class="modal-title-custom" id="patientModalTitle">Admit New Patient</h5>
      <button type="button" class="modal-close-btn" data-dismiss="modal">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="patientForm" action="{{ route('patients.store') }}" method="POST">
      @csrf
      <input type="hidden" name="_method" id="patientMethodInput" value="POST" />
      <div class="modal-body-custom">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label-custom">Patient Full Name <span class="text-danger">*</span></label>
            <div class="position-relative">
              <input type="text" name="name" id="patientNameInput" class="form-control-custom ps-5" placeholder="e.g. Clara Oswald" required />
              <i class="fa-solid fa-user input-icon-left"></i>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Age <span class="text-danger">*</span></label>
            <input type="number" name="age" id="patientAgeInput" class="form-control-custom" min="1" max="120" value="35" required />
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Gender <span class="text-danger">*</span></label>
            <select name="gender" id="patientGenderSelect" class="form-select-custom" required>
              <option value="Female">Female</option>
              <option value="Male">Male</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Phone Number <span class="text-danger">*</span></label>
            <div class="position-relative">
              <input type="tel" name="phone" id="patientPhoneInput" class="form-control-custom ps-5" placeholder="+1 (555) 000-0000" required />
              <i class="fa-solid fa-phone input-icon-left"></i>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Assigned Doctor <span class="text-danger">*</span></label>
            <select name="doctor_id" id="patientDoctorSelect" class="form-select-custom" required>
              <option value="">Select Attending Physician...</option>
              @foreach($doctors as $d)
                <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->specialization }})</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Condition Status <span class="text-danger">*</span></label>
            <select name="condition" id="patientConditionSelect" class="form-select-custom" required>
              <option value="Stable">Stable</option>
              <option value="Critical">Critical</option>
              <option value="Under Observation">Under Observation</option>
              <option value="Recovered">Recovered</option>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label-custom">Room / Bed Assignment</label>
            <input type="text" name="room" id="patientRoomInput" class="form-control-custom" placeholder="e.g. Ward 3B, Bed 04" />
          </div>

          <div class="col-12">
            <label class="form-label-custom">Admission Date (Date-wise Tracking)</label>
            <input type="date" name="admission_date" id="patientAdmissionDateInput" value="{{ now()->toDateString() }}" class="form-control-custom" />
          </div>

          <div class="col-12">
            <label class="form-label-custom">Diagnosis / Clinical Notes</label>
            <textarea name="notes" id="patientNotesInput" class="form-control-custom" rows="2" placeholder="Symptoms, treatment plan, or medical observations..."></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer-custom">
        <button type="button" class="btn-custom btn-outline-custom" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn-custom btn-primary-custom">
          <i class="fa-solid fa-floppy-disk"></i> Save Patient Record
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
        <i class="fa-solid fa-triangle-exclamation me-1"></i> Delete Patient Record
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
          Are you sure you want to permanently delete patient <strong id="deletePatientName" class="text-dark">record</strong>?
        </p>
        <div class="p-3 bg-danger-subtle text-danger rounded-3 small">
          <i class="fa-solid fa-circle-exclamation me-1"></i>
          This action will remove the patient admission data from the database and doctor workload metrics.
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
function openAddPatientModal() {
  const form = document.getElementById('patientForm');
  form.reset();
  form.action = "{{ route('patients.store') }}";
  document.getElementById('patientMethodInput').value = "POST";
  document.getElementById('patientModalTitle').textContent = "Admit New Patient";
  document.getElementById('patientAdmissionDateInput').value = "{{ now()->toDateString() }}";
  openModal('patientModal');
}

function openEditPatientModal(pt) {
  const form = document.getElementById('patientForm');
  form.action = `/patients/${pt.id}`;
  document.getElementById('patientMethodInput').value = "PUT";
  document.getElementById('patientModalTitle').textContent = "Edit Patient Details";

  document.getElementById('patientNameInput').value = pt.name;
  document.getElementById('patientAgeInput').value = pt.age;
  document.getElementById('patientGenderSelect').value = pt.gender;
  document.getElementById('patientPhoneInput').value = pt.phone;
  document.getElementById('patientDoctorSelect').value = pt.doctor_id;
  document.getElementById('patientConditionSelect').value = pt.condition;
  document.getElementById('patientRoomInput').value = pt.room || '';
  document.getElementById('patientNotesInput').value = pt.notes || '';
  if (pt.admission_date) {
    document.getElementById('patientAdmissionDateInput').value = pt.admission_date.split('T')[0];
  }
  openModal('patientModal');
}

function promptDeletePatient(id, name) {
  const form = document.getElementById('deletePatientForm');
  form.action = `/patients/${id}`;
  document.getElementById('deletePatientName').textContent = name;
  openModal('deletePatientModal');
}
</script>
@endpush
