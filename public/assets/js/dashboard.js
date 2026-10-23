/**
 * DOCTOR TRACKER - DASHBOARD ANALYTICS & VISUALIZATIONS
 * Built with Chart.js 4.x and dynamic metrics data aggregation
 */

document.addEventListener("DOMContentLoaded", function () {
  initDashboardKPIs();
  initDashboardCharts();
  renderRecentPatientsTable();
  renderTopDoctorsList();
});

function initDashboardKPIs() {
  if (!window.db) return;
  const analytics = window.db.getAnalytics();

  // 1. Total Doctors
  const docCountEl = document.getElementById("kpiTotalDoctors");
  if (docCountEl) docCountEl.textContent = analytics.totalDoctors;

  // 2. Total Patients
  const ptCountEl = document.getElementById("kpiTotalPatients");
  if (ptCountEl) ptCountEl.textContent = analytics.totalPatients;

  // 3. Avg Patients per Doctor
  const avgPtEl = document.getElementById("kpiAvgPatients");
  if (avgPtEl) avgPtEl.textContent = analytics.avgPatientsPerDoctor;

  // 4. Critical & Recovered stats
  const criticalCountEl = document.getElementById("kpiCriticalCases");
  if (criticalCountEl) {
    criticalCountEl.textContent = analytics.conditionCounts["Critical"] || 0;
  }
}

function initDashboardCharts() {
  if (!window.db) return;
  const analytics = window.db.getAnalytics();

  // ----------------------------------------------------
  // Chart 1: Patients per Doctor (Horizontal/Vertical Bar)
  // ----------------------------------------------------
  const ctxDoctorPatients = document.getElementById("chartPatientsPerDoctor");
  if (ctxDoctorPatients) {
    const docLabels = analytics.patientsPerDoctor.map(d => d.doctorName.replace("Dr. ", ""));
    const docCounts = analytics.patientsPerDoctor.map(d => d.count);

    new Chart(ctxDoctorPatients, {
      type: "bar",
      data: {
        labels: docLabels,
        datasets: [{
          label: "Assigned Patients",
          data: docCounts,
          backgroundColor: "rgba(13, 148, 136, 0.85)",
          hoverBackgroundColor: "rgba(15, 118, 110, 1)",
          borderRadius: 8,
          borderSkipped: false,
          maxBarThickness: 36
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: "#0f172a",
            titleFont: { size: 13, family: "Plus Jakarta Sans" },
            bodyFont: { size: 12, family: "Plus Jakarta Sans" },
            padding: 10,
            cornerRadius: 8
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              color: "#64748b",
              font: { family: "Plus Jakarta Sans", size: 11 }
            }
          },
          y: {
            grid: { color: "#f1f5f9" },
            ticks: {
              stepSize: 1,
              color: "#64748b",
              font: { family: "Plus Jakarta Sans", size: 11 }
            },
            beginAtZero: true
          }
        }
      }
    });
  }

  // ----------------------------------------------------
  // Chart 2: Date-based Patient Admissions Trend (Area Line)
  // ----------------------------------------------------
  const ctxAdmissions = document.getElementById("chartAdmissionsTrend");
  if (ctxAdmissions) {
    // Generate ordered dates
    const dateKeys = Object.keys(analytics.dateCounts).sort();
    const formattedDateLabels = dateKeys.map(d => {
      const parts = d.split("-");
      return parts.length === 3 ? `${parts[1]}/${parts[2]}` : d;
    });
    const dateValues = dateKeys.map(k => analytics.dateCounts[k]);

    new Chart(ctxAdmissions, {
      type: "line",
      data: {
        labels: formattedDateLabels.length > 0 ? formattedDateLabels : ["10/18", "10/19", "10/20", "10/21", "10/22", "10/23"],
        datasets: [{
          label: "Daily Admissions",
          data: dateValues.length > 0 ? dateValues : [2, 3, 4, 3, 5, 4],
          borderColor: "#0d9488",
          backgroundColor: "rgba(13, 148, 136, 0.12)",
          fill: true,
          tension: 0.4,
          borderWidth: 2.5,
          pointBackgroundColor: "#0d9488",
          pointBorderColor: "#ffffff",
          pointBorderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: "#0f172a",
            padding: 10,
            cornerRadius: 8
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: "#64748b", font: { family: "Plus Jakarta Sans", size: 11 } }
          },
          y: {
            grid: { color: "#f1f5f9" },
            ticks: {
              stepSize: 1,
              color: "#64748b",
              font: { family: "Plus Jakarta Sans", size: 11 }
            },
            beginAtZero: true
          }
        }
      }
    });
  }

  // ----------------------------------------------------
  // Chart 3: Patient Condition Distribution (Doughnut)
  // ----------------------------------------------------
  const ctxCondition = document.getElementById("chartConditionDistribution");
  if (ctxCondition) {
    const conditions = ["Stable", "Critical", "Under Observation", "Recovered"];
    const conditionData = conditions.map(c => analytics.conditionCounts[c] || 0);

    new Chart(ctxCondition, {
      type: "doughnut",
      data: {
        labels: conditions,
        datasets: [{
          data: conditionData,
          backgroundColor: [
            "#10b981", // Stable
            "#ef4444", // Critical
            "#f59e0b", // Observation
            "#0d9488"  // Recovered
          ],
          borderWidth: 3,
          borderColor: "#ffffff",
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: "70%",
        plugins: {
          legend: {
            position: "bottom",
            labels: {
              usePointStyle: true,
              pointStyle: "circle",
              padding: 16,
              font: { family: "Plus Jakarta Sans", size: 12, weight: "500" }
            }
          },
          tooltip: {
            backgroundColor: "#0f172a",
            padding: 10,
            cornerRadius: 8
          }
        }
      }
    });
  }

  // ----------------------------------------------------
  // Chart 4: Department / Specialization Share
  // ----------------------------------------------------
  const ctxSpecialty = document.getElementById("chartSpecialtyShare");
  if (ctxSpecialty) {
    const specialties = Object.keys(analytics.specialtyCounts);
    const specialtyVals = Object.values(analytics.specialtyCounts);

    new Chart(ctxSpecialty, {
      type: "bar",
      data: {
        labels: specialties,
        datasets: [{
          label: "Doctors",
          data: specialtyVals,
          backgroundColor: [
            "#3b82f6",
            "#0d9488",
            "#8b5cf6",
            "#ec4899",
            "#f97316",
            "#06b6d4",
            "#10b981",
            "#6366f1"
          ],
          borderRadius: 6
        }]
      },
      options: {
        indexAxis: "y",
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { backgroundColor: "#0f172a" }
        },
        scales: {
          x: {
            grid: { color: "#f1f5f9" },
            ticks: { stepSize: 1, color: "#64748b" },
            beginAtZero: true
          },
          y: {
            grid: { display: false },
            ticks: { color: "#334155", font: { weight: "500" } }
          }
        }
      }
    });
  }
}

function renderRecentPatientsTable() {
  const tbody = document.getElementById("recentPatientsTableBody");
  if (!tbody || !window.db) return;

  const patients = window.db.getPatients().slice(0, 6);
  if (patients.length === 0) {
    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">No patients registered yet.</td></tr>`;
    return;
  }

  tbody.innerHTML = patients.map(p => {
    const doctor = window.db.getDoctorById(p.doctorId);
    const doctorName = doctor ? doctor.name : "Unassigned";
    return `
      <tr>
        <td>
          <span class="fw-bold text-dark">${p.code}</span>
        </td>
        <td>
          <div class="user-cell">
            <div>
              <span class="user-name-cell">${p.name}</span>
              <span class="user-email-cell">${p.age} yrs • ${p.gender}</span>
            </div>
          </div>
        </td>
        <td>
          <span class="text-dark fw-medium">${doctorName}</span>
          <span class="d-block text-muted small">${doctor ? doctor.specialization : ""}</span>
        </td>
        <td>${getConditionBadgeHtml(p.condition)}</td>
        <td>${formatDate(p.admissionDate)}</td>
        <td>
          <div class="action-btn-group">
            <a href="patients.html?search=${encodeURIComponent(p.code)}" class="action-btn action-btn-view" title="View Patient Details">
              <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
          </div>
        </td>
      </tr>
    `;
  }).join("");
}

function renderTopDoctorsList() {
  const container = document.getElementById("topDoctorsList");
  if (!container || !window.db) return;

  const analytics = window.db.getAnalytics();
  const topDoctors = analytics.patientsPerDoctor.slice(0, 5);

  container.innerHTML = topDoctors.map(item => {
    const doc = window.db.getDoctorById(item.doctorId);
    const avatar = doc ? doc.avatar : "assets/images/default-doctor.svg";
    return `
      <div class="activity-item">
        <img src="${avatar}" alt="${item.doctorName}" class="user-avatar-sm" onerror="this.onerror=null;this.src='assets/images/default-doctor.svg';" />
        <div class="activity-text">
          <div class="d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-dark">${item.doctorName}</h6>
            <span class="badge-count">${item.count} Patients</span>
          </div>
          <p class="text-muted small">${item.specialization} • ${doc ? doc.hospital : ""}</p>
        </div>
      </div>
    `;
  }).join("");
}

