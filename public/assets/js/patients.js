/**
 * DOCTOR TRACKER - PATIENTS MANAGEMENT SCRIPT
 * Dedicated patients page: search, condition & date-wise filters, pagination, full CRUD modal
 */

let patientCurrentPage = 1;
const patientPageSize = 7;
let patientDeleteTargetId = null;

document.addEventListener("DOMContentLoaded", function () {
  initPatientFilters();
  initPatientForm();
  renderPatients();
});

function initPatientFilters() {
  const searchInput = document.getElementById("patientSearchInput");
  const conditionFilter = document.getElementById("patientConditionFilter");
  const doctorFilter = document.getElementById("patientDoctorFilter");
  const dateFilter = document.getElementById("patientDateFilter");
  const resetBtn = document.getElementById("patientResetFiltersBtn");

  // Check URL query parameters for pre-filled search (e.g. from Dashboard)
  const urlParams = new URLSearchParams(window.location.search);
  const prefillSearch = urlParams.get("search");
  if (prefillSearch && searchInput) {
    searchInput.value = prefillSearch;
  }

  // Populate Doctor dropdown in filter & form
  if (window.db) {
    const doctors = window.db.getDoctors();
    const docSelects = [doctorFilter, document.getElementById("patientDoctorSelect")];

    docSelects.forEach(sel => {
      if (!sel) return;
      doctors.forEach(doc => {
        const opt = document.createElement("option");
        opt.value = doc.id;
        opt.textContent = `${doc.name} (${doc.specialization})`;
        sel.appendChild(opt);
      });
    });
  }

  // Event handlers
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      patientCurrentPage = 1;
      renderPatients();
    });
  }

  if (conditionFilter) {
    conditionFilter.addEventListener("change", () => {
      patientCurrentPage = 1;
      renderPatients();
    });
  }

  if (doctorFilter) {
    doctorFilter.addEventListener("change", () => {
      patientCurrentPage = 1;
      renderPatients();
    });
  }

  if (dateFilter) {
    dateFilter.addEventListener("change", () => {
      patientCurrentPage = 1;
      renderPatients();
    });
  }

  if (resetBtn) {
    resetBtn.addEventListener("click", () => {
      if (searchInput) searchInput.value = "";
      if (conditionFilter) conditionFilter.value = "";
      if (doctorFilter) doctorFilter.value = "";
      if (dateFilter) dateFilter.value = "";
      patientCurrentPage = 1;
      renderPatients();
    });
  }
}

function getFilteredPatients() {
  if (!window.db) return [];
  const patients = window.db.getPatients();

  const searchVal = (document.getElementById("patientSearchInput")?.value || "").toLowerCase().trim();
  const conditionVal = document.getElementById("patientConditionFilter")?.value || "";
  const doctorVal = document.getElementById("patientDoctorFilter")?.value || "";
  const dateVal = document.getElementById("patientDateFilter")?.value || "";

  return patients.filter(pt => {
    const doctor = window.db.getDoctorById(pt.doctorId);
    const docName = doctor ? doctor.name.toLowerCase() : "";

    // Search across Name, Patient Code, Phone, Doctor Name
    const matchesSearch = !searchVal ||
      pt.name.toLowerCase().includes(searchVal) ||
      pt.code.toLowerCase().includes(searchVal) ||
      pt.phone.includes(searchVal) ||
      docName.includes(searchVal);

    // Condition filter (Critical, Stable, etc.)
    const matchesCondition = !conditionVal || pt.condition === conditionVal;

    // Doctor filter
    const matchesDoctor = !doctorVal || pt.doctorId === doctorVal;

    // Date-wise filter (exact date or on/after)
    const matchesDate = !dateVal || pt.admissionDate >= dateVal;

    return matchesSearch && matchesCondition && matchesDoctor && matchesDate;
  });
}

function renderPatients() {
  const tbody = document.getElementById("patientsTableBody");
  const countDisplay = document.getElementById("patientCountDisplay");
  if (!tbody || !window.db) return;

  const filtered = getFilteredPatients();
  const totalCount = filtered.length;

  if (countDisplay) {
    countDisplay.textContent = `Showing ${Math.min((patientCurrentPage - 1) * patientPageSize + 1, totalCount)} to ${Math.min(patientCurrentPage * patientPageSize, totalCount)} of ${totalCount} patients`;
  }

  if (totalCount === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="8" class="text-center py-5">
          <div class="text-muted">
            <i class="fa-solid fa-bed-pulse empty-state-icon"></i>
            <h6>No patient records match the criteria</h6>
            <p class="small mb-0">Try clearing filters or search query.</p>
          </div>
        </td>
      </tr>
    `;
    renderPatientPagination(0);
    return;
  }

  const startIndex = (patientCurrentPage - 1) * patientPageSize;
  const paginated = filtered.slice(startIndex, startIndex + patientPageSize);

  tbody.innerHTML = paginated.map(p => {
    const doctor = window.db.getDoctorById(p.doctorId);
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
          ${doctor ? `
            <a href="doctor-details.html?id=${doctor.id}" class="fw-medium text-dark text-decoration-none">
              ${doctor.name}
            </a>
            <span class="d-block text-muted small">${doctor.specialization}</span>
          ` : `<span class="text-muted">Unassigned</span>`}
        </td>
        <td>
          <span class="text-muted"><i class="fa-solid fa-phone me-1 text-primary"></i>${p.phone}</span>
        </td>
        <td>${getConditionBadgeHtml(p.condition)}</td>
        <td>
          <span class="badge-specialty">${p.room || "General Ward"}</span>
        </td>
        <td>
          <span class="text-muted small">${formatDate(p.admissionDate)}</span>
        </td>
        <td>
          <div class="action-btn-group">
            <button type="button" class="action-btn action-btn-edit" onclick="openEditPatientModal('${p.id}')" title="Edit Patient Information">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button type="button" class="action-btn action-btn-delete" onclick="promptDeletePatient('${p.id}', '${p.name.replace(/'/g, "\\'")}')" title="Delete Patient Record">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
  }).join("");

  renderPatientPagination(totalCount);
}

function renderPatientPagination(totalCount) {
  const container = document.getElementById("patientPaginationContainer");
  if (!container) return;

  const totalPages = Math.ceil(totalCount / patientPageSize) || 1;
  let html = `<ul class="pagination-custom">`;

  // Previous
  html += `
    <li class="page-item-custom ${patientCurrentPage === 1 ? 'disabled' : ''}">
      <button class="page-link-custom" onclick="changePatientPage(${patientCurrentPage - 1})" aria-label="Previous">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
    </li>
  `;

  for (let i = 1; i <= totalPages; i++) {
    html += `
      <li class="page-item-custom ${i === patientCurrentPage ? 'active' : ''}">
        <button class="page-link-custom" onclick="changePatientPage(${i})">${i}</button>
      </li>
    `;
  }

  // Next
  html += `
    <li class="page-item-custom ${patientCurrentPage >= totalPages ? 'disabled' : ''}">
      <button class="page-link-custom" onclick="changePatientPage(${patientCurrentPage + 1})" aria-label="Next">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    </li>
  `;

  html += `</ul>`;
  container.innerHTML = html;
}

function changePatientPage(page) {
  patientCurrentPage = page;
  renderPatients();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Patient Form: Add / Edit
function initPatientForm() {
  const form = document.getElementById("patientForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const ptId = document.getElementById("patientIdInput").value;
    const name = document.getElementById("patientNameInput").value.trim();
    const age = document.getElementById("patientAgeInput").value;
    const gender = document.getElementById("patientGenderSelect").value;
    const phone = document.getElementById("patientPhoneInput").value.trim();
    const doctorId = document.getElementById("patientDoctorSelect").value;
    const condition = document.getElementById("patientConditionSelect").value;
    const room = document.getElementById("patientRoomInput").value.trim();
    const notes = document.getElementById("patientNotesInput").value.trim();
    const admissionDate = document.getElementById("patientAdmissionDateInput")?.value || new Date().toISOString().split("T")[0];

    if (!name || !phone || !doctorId) {
      showToast("Validation Error", "Please fill in patient name, phone and assigned doctor.", "warning");
      return;
    }

    if (ptId) {
      // Edit
      window.db.updatePatient(ptId, { name, age, gender, phone, doctorId, condition, room, notes, admissionDate });
      showToast("Patient Updated", `${name}'s medical details were saved.`, "success");
    } else {
      // Create
      window.db.addPatient({ name, age, gender, phone, doctorId, condition, room, notes, admissionDate });
      showToast("Patient Admitted", `${name} was admitted successfully.`, "success");
    }

    closeModal("patientModal");
    form.reset();
    renderPatients();
    if (typeof updateSidebarCounts === "function") updateSidebarCounts();
  });
}

function openAddPatientModal() {
  const form = document.getElementById("patientForm");
  if (form) form.reset();
  document.getElementById("patientIdInput").value = "";
  document.getElementById("patientModalTitle").textContent = "Admit New Patient";
  document.getElementById("patientAdmissionDateInput").value = new Date().toISOString().split("T")[0];
  openModal("patientModal");
}

function openEditPatientModal(id) {
  const pt = window.db.getPatientById(id);
  if (!pt) return;

  document.getElementById("patientIdInput").value = pt.id;
  document.getElementById("patientNameInput").value = pt.name;
  document.getElementById("patientAgeInput").value = pt.age;
  document.getElementById("patientGenderSelect").value = pt.gender;
  document.getElementById("patientPhoneInput").value = pt.phone;
  document.getElementById("patientDoctorSelect").value = pt.doctorId;
  document.getElementById("patientConditionSelect").value = pt.condition;
  document.getElementById("patientRoomInput").value = pt.room || "";
  document.getElementById("patientNotesInput").value = pt.notes || "";
  if (document.getElementById("patientAdmissionDateInput")) {
    document.getElementById("patientAdmissionDateInput").value = pt.admissionDate;
  }

  document.getElementById("patientModalTitle").textContent = "Edit Patient Information";
  openModal("patientModal");
}

function promptDeletePatient(id, name) {
  patientDeleteTargetId = id;
  const nameEl = document.getElementById("deletePatientName");
  if (nameEl) nameEl.textContent = name;
  openModal("deletePatientModal");
}

function confirmDeletePatient() {
  if (patientDeleteTargetId && window.db) {
    window.db.deletePatient(patientDeleteTargetId);
    showToast("Patient Deleted", "Patient record was removed.", "danger");
    closeModal("deletePatientModal");
    patientDeleteTargetId = null;
    renderPatients();
    if (typeof updateSidebarCounts === "function") updateSidebarCounts();
  }
}

