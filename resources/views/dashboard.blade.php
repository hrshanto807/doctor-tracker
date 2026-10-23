@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="page-header">
  <div>
    <h1 class="page-title">Executive Clinical Dashboard</h1>
    <p class="page-subtitle">Real-time doctor workloads, patient health metrics, and institutional capacity analytics.</p>
  </div>
  <div class="page-actions">
    <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 border rounded-3 text-muted small">
      <i class="fa-regular fa-calendar text-primary"></i>
      <span>{{ now()->format('M d, Y') }}</span>
    </div>
    <button type="button" class="btn-custom btn-outline-custom" onclick="window.print()">
      <i class="fa-solid fa-print"></i> Print Report
    </button>
  </div>
</div>

<!-- ==========================================
     KPI STAT CARDS
     ========================================== -->
<div class="row g-3 mb-4">
  <!-- Total Doctors -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="kpi-info">
        <h6>Total Doctors</h6>
        <div class="kpi-number">{{ $totalDoctors }}</div>
        <span class="kpi-trend trend-up">
          <i class="fa-solid fa-arrow-trend-up"></i> Active Medical Staff
        </span>
      </div>
      <div class="kpi-icon-box icon-teal">
        <i class="fa-solid fa-user-doctor"></i>
      </div>
    </div>
  </div>

  <!-- Total Patients -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="kpi-info">
        <h6>Total Patients</h6>
        <div class="kpi-number">{{ $totalPatients }}</div>
        <span class="kpi-trend trend-up">
          <i class="fa-solid fa-arrow-trend-up"></i> Inpatients & Active
        </span>
      </div>
      <div class="kpi-icon-box icon-blue">
        <i class="fa-solid fa-hospital-user"></i>
      </div>
    </div>
  </div>

  <!-- Patients per Doctor -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="kpi-info">
        <h6>Patients per Doctor</h6>
        <div class="kpi-number">{{ $avgPatientsPerDoctor }}</div>
        <span class="kpi-trend trend-neutral">
          <i class="fa-solid fa-scale-balanced"></i> Average Census
        </span>
      </div>
      <div class="kpi-icon-box icon-emerald">
        <i class="fa-solid fa-scale-balanced"></i>
      </div>
    </div>
  </div>

  <!-- Critical Cases -->
  <div class="col-12 col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="kpi-info">
        <h6>Critical Cases</h6>
        <div class="kpi-number">{{ $criticalCases }}</div>
        <span class="kpi-trend trend-down">
          <i class="fa-solid fa-heart-pulse"></i> High Priority
        </span>
      </div>
      <div class="kpi-icon-box icon-amber">
        <i class="fa-solid fa-triangle-exclamation"></i>
      </div>
    </div>
  </div>
</div>

<!-- ==========================================
     DATA VISUALIZATION & CHARTS ROW 1
     ========================================== -->
<div class="charts-grid-2">
  <!-- Patients Per Doctor Bar Chart -->
  <div class="card-custom">
    <div class="card-header-custom">
      <h5 class="card-title-custom">
        <i class="fa-solid fa-chart-column text-primary"></i> Patients per Doctor
      </h5>
      <span class="badge bg-light text-muted fw-normal">Live Workload</span>
    </div>
    <div class="card-body-custom">
      <div class="chart-canvas-wrapper">
        <canvas id="chartPatientsPerDoctor"></canvas>
      </div>
    </div>
  </div>

  <!-- Patient Condition Breakdown Doughnut Chart -->
  <div class="card-custom">
    <div class="card-header-custom">
      <h5 class="card-title-custom">
        <i class="fa-solid fa-chart-pie text-primary"></i> Condition Distribution
      </h5>
      <span class="badge bg-light text-muted fw-normal">Current Census</span>
    </div>
    <div class="card-body-custom">
      <div class="chart-canvas-wrapper">
        <canvas id="chartConditionDistribution"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- ==========================================
     DATA VISUALIZATION & CHARTS ROW 2
     ========================================== -->
<div class="charts-grid-equal">
  <!-- Date-Based Admissions Trend (Line/Area) -->
  <div class="card-custom">
    <div class="card-header-custom">
      <h5 class="card-title-custom">
        <i class="fa-solid fa-chart-line text-primary"></i> Date-Based Admissions Trend
      </h5>
      <span class="badge bg-light text-muted fw-normal">Admissions Velocity</span>
    </div>
    <div class="card-body-custom">
      <div class="chart-canvas-wrapper">
        <canvas id="chartAdmissionsTrend"></canvas>
      </div>
    </div>
  </div>

  <!-- Specialization Share Chart -->
  <div class="card-custom">
    <div class="card-header-custom">
      <h5 class="card-title-custom">
        <i class="fa-solid fa-stethoscope text-primary"></i> Medical Specializations
      </h5>
      <span class="badge bg-light text-muted fw-normal">Faculty Distribution</span>
    </div>
    <div class="card-body-custom">
      <div class="chart-canvas-wrapper">
        <canvas id="chartSpecialtyShare"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- ==========================================
     RECENT ADMISSIONS & TOP DOCTORS
     ========================================== -->
<div class="row g-4">
  <!-- Recent Patients Table -->
  <div class="col-12 col-xl-8">
    <div class="card-custom">
      <div class="card-header-custom">
        <h5 class="card-title-custom">
          <i class="fa-solid fa-hospital-user text-primary"></i> Recent Patient Admissions
        </h5>
        <a href="{{ route('patients.index') }}" class="btn-custom btn-outline-custom btn-sm-custom">
          View All Patients <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
      </div>
      <div class="table-responsive-custom">
        <table class="table-custom">
          <thead>
            <tr>
              <th>Patient Code</th>
              <th>Patient Info</th>
              <th>Assigned Doctor</th>
              <th>Status / Condition</th>
              <th>Admission Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentPatients as $p)
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
                    <a href="{{ route('doctors.show', $p->doctor->id) }}" class="text-dark fw-medium text-decoration-none">
                      {{ $p->doctor->name }}
                    </a>
                    <span class="d-block text-muted small">{{ $p->doctor->specialization }}</span>
                  @else
                    <span class="text-muted">Unassigned</span>
                  @endif
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
                <td><span class="text-muted small">{{ $p->admission_date?->format('M d, Y') ?? 'N/A' }}</span></td>
                <td>
                  <a href="{{ route('patients.index', ['search' => $p->code]) }}" class="action-btn action-btn-view" title="View Patient Details">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">No patient records admitted yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Top Active Doctors List -->
  <div class="col-12 col-xl-4">
    <div class="card-custom">
      <div class="card-header-custom">
        <h5 class="card-title-custom">
          <i class="fa-solid fa-ranking-star text-primary"></i> Top Active Doctors
        </h5>
        <a href="{{ route('doctors.index') }}" class="small fw-semibold text-primary text-decoration-none">View All</a>
      </div>
      <div class="card-body-custom">
        <div class="activity-list">
          @forelse($topDoctors as $doc)
            <div class="activity-item">
              <img src="{{ $doc->avatar ?? asset('assets/images/default-doctor.svg') }}" alt="{{ $doc->name }}" class="user-avatar-sm" onerror="this.onerror=null;this.src='{{ asset('assets/images/default-doctor.svg') }}';" />
              <div class="activity-text">
                <div class="d-flex align-items-center justify-content-between">
                  <a href="{{ route('doctors.show', $doc->id) }}" class="fw-bold text-dark text-decoration-none mb-0">
                    {{ $doc->name }}
                  </a>
                  <span class="badge-count">{{ $doc->patients_count }} Patients</span>
                </div>
                <p class="text-muted small">{{ $doc->specialization }} • {{ $doc->hospital }}</p>
              </div>
            </div>
          @empty
            <p class="text-muted text-center py-3">No active doctors found.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
  // 1. Chart: Patients per Doctor
  const ctxDoctorPatients = document.getElementById("chartPatientsPerDoctor");
  if (ctxDoctorPatients) {
    const docLabels = @json($patientsPerDoctor->pluck('name'));
    const docCounts = @json($patientsPerDoctor->pluck('patients_count'));

    new Chart(ctxDoctorPatients, {
      type: "bar",
      data: {
        labels: docLabels.map(name => name.replace("Dr. ", "")),
        datasets: [{
          label: "Assigned Patients",
          data: docCounts,
          backgroundColor: "rgba(13, 148, 136, 0.85)",
          hoverBackgroundColor: "rgba(15, 118, 110, 1)",
          borderRadius: 8,
          maxBarThickness: 36
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { grid: { color: "#f1f5f9" }, ticks: { stepSize: 1 }, beginAtZero: true }
        }
      }
    });
  }

  // 2. Chart: Condition Distribution (Doughnut)
  const ctxCondition = document.getElementById("chartConditionDistribution");
  if (ctxCondition) {
    const conditionLabels = @json(array_keys($conditionCounts));
    const conditionValues = @json(array_values($conditionCounts));

    new Chart(ctxCondition, {
      type: "doughnut",
      data: {
        labels: conditionLabels,
        datasets: [{
          data: conditionValues,
          backgroundColor: ["#10b981", "#ef4444", "#f59e0b", "#0d9488"],
          borderWidth: 3,
          borderColor: "#ffffff"
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "70%",
        plugins: {
          legend: {
            position: "bottom",
            labels: { usePointStyle: true, pointStyle: "circle", padding: 16 }
          }
        }
      }
    });
  }

  // 3. Chart: Admissions Trend (Line)
  const ctxAdmissions = document.getElementById("chartAdmissionsTrend");
  if (ctxAdmissions) {
    const rawDates = @json(array_keys($dateCounts));
    const dateValues = @json(array_values($dateCounts));

    new Chart(ctxAdmissions, {
      type: "line",
      data: {
        labels: rawDates,
        datasets: [{
          label: "Daily Admissions",
          data: dateValues,
          borderColor: "#0d9488",
          backgroundColor: "rgba(13, 148, 136, 0.12)",
          fill: true,
          tension: 0.4,
          borderWidth: 2.5,
          pointBackgroundColor: "#0d9488",
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { grid: { color: "#f1f5f9" }, ticks: { stepSize: 1 }, beginAtZero: true }
        }
      }
    });
  }

  // 4. Chart: Specializations (Horizontal Bar)
  const ctxSpecialty = document.getElementById("chartSpecialtyShare");
  if (ctxSpecialty) {
    const specLabels = @json(array_keys($specialtyCounts));
    const specValues = @json(array_values($specialtyCounts));

    new Chart(ctxSpecialty, {
      type: "bar",
      data: {
        labels: specLabels,
        datasets: [{
          label: "Doctors",
          data: specValues,
          backgroundColor: ["#3b82f6", "#0d9488", "#8b5cf6", "#ec4899", "#f97316", "#06b6d4", "#10b981", "#6366f1"],
          borderRadius: 6
        }]
      },
      options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { color: "#f1f5f9" }, ticks: { stepSize: 1 }, beginAtZero: true },
          y: { grid: { display: false } }
        }
      }
    });
  }
});
</script>
@endpush
