/**
 * DOCTOR TRACKER - DOCTOR DETAILS & CORRESPONDING PATIENTS
 * Dynamic doctor profile header, patient roster, add/delete patient under specific doctor
 */

let currentDoctorId = null;
let patientDeleteTargetId = null;

document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  currentDoctorId = urlParams.get("id");

  if (!currentDoctorId && window.db) {
    const allDocs = window.db.getDoctors();
    if (allDocs.length > 0) {
      currentDoctorId = allDocs[0].id;
    }
  }

  loadDoctorProfile();
  initDoctorPatientsSearch();
  initAddPatientUnderDoctorForm();
});

function loadDoctorProfile() {
  if (!window.db || !currentDoctorId) return;
  const doc = window.db.getDoctorById(currentDoctorId);

  if (!doc) {
    window.location.href = "doctors.html";
    return;
  }

  // Update Doctor details in UI
  const avatarEl = document.getElementById("docDetailAvatar");
  if (avatarEl) {
    avatarEl.src = doc.avatar || "assets/images/default-doctor.svg";
    avatarEl.onerror = function() { this.src = "assets/images/default-doctor.svg"; };
  }
  document.getElementById("docDetailName").textContent = doc.name;
  document.getElementById("docDetailSpecialty").textContent = doc.specialization;
  document.getElementById("docDetailHospital").textContent = doc.hospital;
  document.getElementById("docDetailEmail").textContent = doc.email;
  document.getElementById("docDetailPhone").textContent = doc.phone;
  document.getElementById("docDetailJoined").textContent = formatDate(doc.joinedDate);

  const patients = window.db.getPatientsByDoctor(currentDoctorId);
  const criticalCount = patients.filter(p => p.condition === "Critical").length;
  const stableCount = patients.filter(p => p.condition === "Stable" || p.condition === "Recovered").length;

  document.getElementById("docDetailPatientCount").textContent = patients.length;
  document.getElementById("docDetailCriticalCount").textContent = criticalCount;
  document.getElementById("docDetailStableCount").textContent = stableCount;

  renderDoctorPatients(patients);
}

function initDoctorPatientsSearch() {
  const searchInput = document.getElementById("doctorPatientSearch");
  const conditionFilter = document.getElementById("doctorPatientConditionFilter");

  const filterHandler = () => {
    const q = (searchInput?.value || "").toLowerCase().trim();
    const cond = conditionFilter?.value || "";

    const allDoctorPatients = window.db.getPatientsByDoctor(currentDoctorId);
    const filtered = allDoctorPatients.filter(p => {
      const matchQ = !q || p.name.toLowerCase().includes(q) || p.code.toLowerCase().includes(q) || p.phone.includes(q);
      const matchC = !cond || p.condition === cond;
      return matchQ && matchC;
    });

    renderDoctorPatients(filtered);
  };

  if (searchInput) searchInput.addEventListener("input", filterHandler);
  if (conditionFilter) conditionFilter.addEventListener("change", filterHandler);
}

function renderDoctorPatients(patients) {
  const tbody = document.getElementById("doctorPatientsTableBody");
  const counter = document.getElementById("docPatientListCount");
  if (!tbody) return;

  if (counter) {
    counter.textContent = `${patients.length} Registered Patients`;
  }

  if (patients.length === 0) {
    tbody.innerHTML = `
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
    `;
    return;
  }

  tbody.innerHTML = patients.map(p => {
    return `
      <tr>
        <td><span class="fw-bold text-dark">${p.code}</span></td>
        <td>
          <div class="user-cell">
            <div>
              <span class="user-name-cell">${p.name}</span>
              <span class="user-email-cell">${p.age} Years • ${p.gender}</span>
            </div>
          </div>
        </td>
        <td><span class="text-muted"><i class="fa-solid fa-phone me-1 text-primary"></i>${p.phone}</span></td>
        <td>${getConditionBadgeHtml(p.condition)}</td>
        <td><span class="fw-medium text-dark">${p.room || "General Ward"}</span></td>
        <td><span class="text-muted small">${formatDate(p.admissionDate)}</span></td>
        <td>
          <div class="action-btn-group">
            <button type="button" class="action-btn action-btn-delete" onclick="promptDeletePatientFromDoctor('${p.id}', '${p.name.replace(/'/g, "\\'")}')" title="Delete Patient from Doctor">
              <i class="fa-solid fa-trash-can"></i>
            </button>
          </div>
        </td>
      </tr>
    `;
  }).join("");
}

// Add Patient under THIS Doctor Modal Form
function initAddPatientUnderDoctorForm() {
  const form = document.getElementById("addPatientUnderDoctorForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const name = document.getElementById("ptDocName").value.trim();
    const age = document.getElementById("ptDocAge").value;
    const gender = document.getElementById("ptDocGender").value;
    const phone = document.getElementById("ptDocPhone").value.trim();
    const condition = document.getElementById("ptDocCondition").value;
    const room = document.getElementById("ptDocRoom").value.trim();
    const notes = document.getElementById("ptDocNotes").value.trim();
    const admissionDate = document.getElementById("ptDocAdmissionDate")?.value || new Date().toISOString().split("T")[0];

    if (!name || !phone) {
      showToast("Validation Error", "Please provide patient name and phone number.", "warning");
      return;
    }

    window.db.addPatient({
      name,
      age,
      gender,
      phone,
      doctorId: currentDoctorId,
      condition,
      room,
      notes,
      admissionDate
    });

    showToast("Patient Added", `${name} was admitted under this doctor.`, "success");
    closeModal("addPatientUnderDoctorModal");
    form.reset();
    loadDoctorProfile();
    if (typeof updateSidebarCounts === "function") updateSidebarCounts();
  });
}

function openAddPatientUnderDoctorModal() {
  const doc = window.db.getDoctorById(currentDoctorId);
  if (doc) {
    document.getElementById("modalAssignedDoctorName").textContent = doc.name;
  }
  document.getElementById("ptDocAdmissionDate").value = new Date().toISOString().split("T")[0];
  openModal("addPatientUnderDoctorModal");
}

function promptDeletePatientFromDoctor(patientId, patientName) {
  patientDeleteTargetId = patientId;
  const nameEl = document.getElementById("deletePatientName");
  if (nameEl) nameEl.textContent = patientName;
  openModal("deletePatientModal");
}

function confirmDeletePatient() {
  if (patientDeleteTargetId && window.db) {
    window.db.deletePatient(patientDeleteTargetId);
    showToast("Patient Removed", "Patient was deleted from doctor's list.", "danger");
    closeModal("deletePatientModal");
    patientDeleteTargetId = null;
    loadDoctorProfile();
    if (typeof updateSidebarCounts === "function") updateSidebarCounts();
  }
}

