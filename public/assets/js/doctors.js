/**
 * DOCTOR TRACKER - DOCTOR MANAGEMENT SCRIPT
 * Full CRUD, multi-criteria filtering, search, pagination, and patient association
 */

let doctorCurrentPage = 1;
const doctorPageSize = 6;
let doctorDeleteTargetId = null;

document.addEventListener("DOMContentLoaded", function () {
  initDoctorFilters();
  initDoctorForm();
  renderDoctors();
});

function initDoctorFilters() {
  const searchInput = document.getElementById("doctorSearchInput");
  const specialtyFilter = document.getElementById("doctorSpecialtyFilter");
  const hospitalFilter = document.getElementById("doctorHospitalFilter");
  const dateFilter = document.getElementById("doctorDateFilter");
  const resetBtn = document.getElementById("doctorResetFiltersBtn");

  // Populate dynamic filter dropdowns
  if (window.db) {
    const doctors = window.db.getDoctors();

    if (specialtyFilter) {
      const specialties = Array.from(new Set(doctors.map(d => d.specialization)));
      specialties.forEach(spec => {
        const opt = document.createElement("option");
        opt.value = spec;
        opt.textContent = spec;
        specialtyFilter.appendChild(opt);
      });
    }

    if (hospitalFilter) {
      const hospitals = Array.from(new Set(doctors.map(d => d.hospital)));
      hospitals.forEach(hosp => {
        const opt = document.createElement("option");
        opt.value = hosp;
        opt.textContent = hosp;
        hospitalFilter.appendChild(opt);
      });
    }
  }

  // Event listeners
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      doctorCurrentPage = 1;
      renderDoctors();
    });
  }

  if (specialtyFilter) {
    specialtyFilter.addEventListener("change", () => {
      doctorCurrentPage = 1;
      renderDoctors();
    });
  }

  if (hospitalFilter) {
    hospitalFilter.addEventListener("change", () => {
      doctorCurrentPage = 1;
      renderDoctors();
    });
  }

  if (dateFilter) {
    dateFilter.addEventListener("change", () => {
      doctorCurrentPage = 1;
      renderDoctors();
    });
  }

  if (resetBtn) {
    resetBtn.addEventListener("click", () => {
      if (searchInput) searchInput.value = "";
      if (specialtyFilter) specialtyFilter.value = "";
      if (hospitalFilter) hospitalFilter.value = "";
      if (dateFilter) dateFilter.value = "";
      doctorCurrentPage = 1;
      renderDoctors();
    });
  }
}

function getFilteredDoctors() {
  if (!window.db) return [];
  let doctors = window.db.getDoctors();

  const searchVal = (document.getElementById("doctorSearchInput")?.value || "").toLowerCase().trim();
  const specialtyVal = document.getElementById("doctorSpecialtyFilter")?.value || "";
  const hospitalVal = document.getElementById("doctorHospitalFilter")?.value || "";
  const dateVal = document.getElementById("doctorDateFilter")?.value || "";

  return doctors.filter(doc => {
    // Search filter across name, specialization, hospital, email, phone
    const matchesSearch = !searchVal ||
      doc.name.toLowerCase().includes(searchVal) ||
      doc.specialization.toLowerCase().includes(searchVal) ||
      doc.hospital.toLowerCase().includes(searchVal) ||
      doc.email.toLowerCase().includes(searchVal) ||
      doc.phone.toLowerCase().includes(searchVal);

    // Specialty filter
    const matchesSpecialty = !specialtyVal || doc.specialization === specialtyVal;

    // Hospital filter
    const matchesHospital = !hospitalVal || doc.hospital === hospitalVal;

    // Date-wise filter (exact date or month prefix)
    const matchesDate = !dateVal || doc.joinedDate >= dateVal;

    return matchesSearch && matchesSpecialty && matchesHospital && matchesDate;
  });
}

function renderDoctors() {
  const tbody = document.getElementById("doctorsTableBody");
  const countDisplay = document.getElementById("doctorCountDisplay");
  if (!tbody || !window.db) return;

  const filtered = getFilteredDoctors();
  const totalCount = filtered.length;

  if (countDisplay) {
    countDisplay.textContent = `Showing ${Math.min((doctorCurrentPage - 1) * doctorPageSize + 1, totalCount)} to ${Math.min(doctorCurrentPage * doctorPageSize, totalCount)} of ${totalCount} doctors`;
  }

  if (totalCount === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="7" class="text-center py-5">
          <div class="text-muted">
            <i class="fa-solid fa-user-doctor empty-state-icon"></i>
            <h6>No doctors found</h6>
            <p class="small mb-0">Try clearing or adjusting your search filters.</p>
          </div>
        </td>
      </tr>
    `;
    renderDoctorPagination(0);
    return;
  }

  // Pagination slice
  const startIndex = (doctorCurrentPage - 1) * doctorPageSize;
  const paginated = filtered.slice(startIndex, startIndex + doctorPageSize);

  tbody.innerHTML = paginated.map(doc => {
    const assignedPatients = window.db.getPatientsByDoctor(doc.id);
    const patientCount = assignedPatients.length;

    return `
      <tr>
        <td>
          <div class="user-cell">
            <img src="${doc.avatar || 'assets/images/default-doctor.svg'}" alt="${doc.name}" class="user-avatar-sm" onerror="this.onerror=null;this.src='assets/images/default-doctor.svg';" />
            <div>
              <a href="doctor-details.html?id=${doc.id}" class="user-name-cell">${doc.name}</a>
              <span class="user-email-cell"><i class="fa-regular fa-envelope me-1"></i>${doc.email}</span>
            </div>
          </div>
        </td>
        <td>
          <span class="badge-specialty">${doc.specialization}</span>
        </td>
        <td>
          <span class="fw-medium text-dark">${doc.hospital}</span>
        </td>
        <td>
          <span class="text-muted"><i class="fa-solid fa-phone me-1 text-primary"></i>${doc.phone}</span>
        </td>
        <td>
          <a href="doctor-details.html?id=${doc.id}" class="badge-count text-decoration-none">
            <i class="fa-solid fa-user-injured me-1"></i>${patientCount} Patients
          </a>
        </td>
        <td>
          <span class="text-muted small">${formatDate(doc.joinedDate)}</span>
        </td>
        <td>
          <div class="action-btn-group">
            <a href="doctor-details.html?id=${doc.id}" class="action-btn action-btn-view" title="View Doctor & Patients">
              <i class="fa-solid fa-eye"></i>
            </a>
            <button type="button" class="action-btn action-btn-edit" onclick="openEditDoctorModal('${doc.id}')" title="Edit Doctor">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button type="button" class="action-btn action-btn-delete" onclick="promptDeleteDoctor('${doc.id}', '${doc.name.replace(/'/g, "\\'")}')" title="Delete Doctor">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
  }).join("");

  renderDoctorPagination(totalCount);
}

function renderDoctorPagination(totalCount) {
  const container = document.getElementById("doctorPaginationContainer");
  if (!container) return;

  const totalPages = Math.ceil(totalCount / doctorPageSize) || 1;
  let html = `<ul class="pagination-custom">`;

  // Previous button
  html += `
    <li class="page-item-custom ${doctorCurrentPage === 1 ? 'disabled' : ''}">
      <button class="page-link-custom" onclick="changeDoctorPage(${doctorCurrentPage - 1})" aria-label="Previous">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
    </li>
  `;

  for (let i = 1; i <= totalPages; i++) {
    html += `
      <li class="page-item-custom ${i === doctorCurrentPage ? 'active' : ''}">
        <button class="page-link-custom" onclick="changeDoctorPage(${i})">${i}</button>
      </li>
    `;
  }

  // Next button
  html += `
    <li class="page-item-custom ${doctorCurrentPage >= totalPages ? 'disabled' : ''}">
      <button class="page-link-custom" onclick="changeDoctorPage(${doctorCurrentPage + 1})" aria-label="Next">
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    </li>
  `;
  html += `</ul>`;
  container.innerHTML = html;
}

function changeDoctorPage(page) {
  doctorCurrentPage = page;
  renderDoctors();
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Form logic: Add & Edit Doctor
function initDoctorForm() {
  const form = document.getElementById("doctorForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const docId = document.getElementById("doctorIdInput").value;
    const name = document.getElementById("doctorNameInput").value.trim();
    const specialization = document.getElementById("doctorSpecializationInput").value;
    const hospital = document.getElementById("doctorHospitalInput").value.trim();
    const phone = document.getElementById("doctorPhoneInput").value.trim();
    const email = document.getElementById("doctorEmailInput").value.trim();
    const joinedDate = document.getElementById("doctorJoinedDateInput")?.value || new Date().toISOString().split("T")[0];

    if (!name || !specialization || !hospital || !phone || !email) {
      showToast("Validation Error", "Please fill in all mandatory fields.", "warning");
      return;
    }

    if (docId) {
      // Update
      window.db.updateDoctor(docId, { name, specialization, hospital, phone, email, joinedDate });
      showToast("Doctor Updated", `Dr. ${name} was updated successfully.`, "success");
    } else {
      // Create new
      window.db.addDoctor({ name, specialization, hospital, phone, email, joinedDate });
      showToast("Doctor Created", `Dr. ${name} was added to the system.`, "success");
    }

    closeModal("doctorModal");
    form.reset();
    renderDoctors();
    if (typeof updateSidebarCounts === "function") updateSidebarCounts();
  });
}

function openAddDoctorModal() {
  const form = document.getElementById("doctorForm");
  if (form) form.reset();
  document.getElementById("doctorIdInput").value = "";
  document.getElementById("doctorModalTitle").textContent = "Add New Doctor";
  document.getElementById("doctorJoinedDateInput").value = new Date().toISOString().split("T")[0];
  openModal("doctorModal");
}

function openEditDoctorModal(id) {
  const doc = window.db.getDoctorById(id);
  if (!doc) return;

  document.getElementById("doctorIdInput").value = doc.id;
  document.getElementById("doctorNameInput").value = doc.name;
  document.getElementById("doctorSpecializationInput").value = doc.specialization;
  document.getElementById("doctorHospitalInput").value = doc.hospital;
  document.getElementById("doctorPhoneInput").value = doc.phone;
  document.getElementById("doctorEmailInput").value = doc.email;
  if (document.getElementById("doctorJoinedDateInput")) {
    document.getElementById("doctorJoinedDateInput").value = doc.joinedDate;
  }

  document.getElementById("doctorModalTitle").textContent = "Edit Doctor Details";
  openModal("doctorModal");
}

function promptDeleteDoctor(id, name) {
  doctorDeleteTargetId = id;
  const nameEl = document.getElementById("deleteDoctorName");
  if (nameEl) nameEl.textContent = name;
  openModal("deleteDoctorModal");
}

function confirmDeleteDoctor() {
  if (doctorDeleteTargetId && window.db) {
    window.db.deleteDoctor(doctorDeleteTargetId);
    showToast("Doctor Removed", "Doctor and assigned patient records updated.", "danger");
    closeModal("deleteDoctorModal");
    doctorDeleteTargetId = null;
    renderDoctors();
    if (typeof updateSidebarCounts === "function") updateSidebarCounts();
  }
}

