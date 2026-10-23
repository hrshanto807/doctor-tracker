/**
 * DOCTOR TRACKER - CENTRAL DATA STORE & MOCK REPOSITORY
 * Realistic seed data with LocalStorage persistence for complete offline functionality
 */

const SEED_DOCTORS = [
  {
    id: "doc-1",
    name: "Dr. Sarah Jenkins",
    specialization: "Cardiology",
    hospital: "Care Guide Medical Center",
    phone: "+1 (555) 234-5678",
    email: "sarah.jenkins@careguide.com",
    avatar: "https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=200",
    joinedDate: "2026-01-15",
    status: "Active"
  },
  {
    id: "doc-2",
    name: "Dr. Marcus Vance",
    specialization: "Neurology",
    hospital: "City Central Hospital",
    phone: "+1 (555) 345-6789",
    email: "marcus.vance@citycentral.org",
    avatar: "https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=200",
    joinedDate: "2026-02-10",
    status: "Active"
  },
  {
    id: "doc-3",
    name: "Dr. Elena Rostova",
    specialization: "Pediatrics",
    hospital: "Apollo Specialized Hospital",
    phone: "+1 (555) 456-7890",
    email: "elena.rostova@apollohealth.com",
    avatar: "https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&q=80&w=200",
    joinedDate: "2026-03-01",
    status: "Active"
  },
  {
    id: "doc-4",
    name: "Dr. James Wilson",
    specialization: "Orthopedics",
    hospital: "Square Health Center",
    phone: "+1 (555) 567-8901",
    email: "james.wilson@squarehealth.org",
    avatar: "https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=200",
    joinedDate: "2026-03-20",
    status: "Active"
  },
  {
    id: "doc-5",
    name: "Dr. Aisha Patel",
    specialization: "Dermatology",
    hospital: "Evercare General",
    phone: "+1 (555) 678-9012",
    email: "aisha.patel@evercare.com",
    avatar: "https://images.unsplash.com/photo-1614608682850-e0d6ed316d47?auto=format&fit=crop&q=80&w=200",
    joinedDate: "2026-04-12",
    status: "Active"
  },
  {
    id: "doc-6",
    name: "Dr. Robert Chen",
    specialization: "Oncology",
    hospital: "Care Guide Medical Center",
    phone: "+1 (555) 789-0123",
    email: "robert.chen@careguide.com",
    avatar: "https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&q=80&w=200",
    joinedDate: "2026-05-04",
    status: "Active"
  },
  {
    id: "doc-7",
    name: "Dr. Amina Begum",
    specialization: "Nephrology",
    hospital: "Apollo Specialized Hospital",
    phone: "+1 (555) 890-1234",
    email: "amina.begum@apollohealth.com",
    avatar: "https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&q=80&w=200",
    joinedDate: "2026-06-18",
    status: "Active"
  },
  {
    id: "doc-8",
    name: "Dr. David Kim",
    specialization: "General Surgery",
    hospital: "City Central Hospital",
    phone: "+1 (555) 901-2345",
    email: "david.kim@citycentral.org",
    avatar: "https://images.unsplash.com/photo-1582750433449-648ed127bb54?auto=format&fit=crop&q=80&w=200",
    joinedDate: "2026-07-22",
    status: "Active"
  }
];

const SEED_PATIENTS = [
  {
    id: "pt-101",
    code: "PT-1001",
    name: "Jonathan Miller",
    age: 58,
    gender: "Male",
    phone: "+1 (555) 111-2233",
    doctorId: "doc-1",
    condition: "Critical",
    admissionDate: "2026-10-18",
    room: "ICU-02",
    notes: "Acute Coronary Syndrome, continuous ECG monitoring"
  },
  {
    id: "pt-102",
    code: "PT-1002",
    name: "Clara Hughes",
    age: 42,
    gender: "Female",
    phone: "+1 (555) 222-3344",
    doctorId: "doc-1",
    condition: "Stable",
    admissionDate: "2026-10-19",
    room: "Room 304",
    notes: "Post-angioplasty recovery, stable vitals"
  },
  {
    id: "pt-103",
    code: "PT-1003",
    name: "Michael Bennett",
    age: 67,
    gender: "Male",
    phone: "+1 (555) 333-4455",
    doctorId: "doc-1",
    condition: "Under Observation",
    admissionDate: "2026-10-21",
    room: "Ward 2B",
    notes: "Hypertensive urgency, blood pressure adjusting"
  },
  {
    id: "pt-104",
    code: "PT-1004",
    name: "Sophia Martinez",
    age: 34,
    gender: "Female",
    phone: "+1 (555) 444-5566",
    doctorId: "doc-2",
    condition: "Recovered",
    admissionDate: "2026-10-14",
    room: "Room 108",
    notes: "Severe migraine treated, ready for discharge"
  },
  {
    id: "pt-105",
    code: "PT-1005",
    name: "William Wright",
    age: 51,
    gender: "Male",
    phone: "+1 (555) 555-6677",
    doctorId: "doc-2",
    condition: "Critical",
    admissionDate: "2026-10-20",
    room: "Neuro ICU-01",
    notes: "Ischemic stroke episode, neurological assessment ongoing"
  },
  {
    id: "pt-106",
    code: "PT-1006",
    name: "Emma Watson",
    age: 8,
    gender: "Female",
    phone: "+1 (555) 666-7788",
    doctorId: "doc-3",
    condition: "Stable",
    admissionDate: "2026-10-22",
    room: "Pedia Ward 4",
    notes: "Acute bronchitis, nebulization therapy"
  },
  {
    id: "pt-107",
    code: "PT-1007",
    name: "Liam O'Connor",
    age: 5,
    gender: "Male",
    phone: "+1 (555) 777-8899",
    doctorId: "doc-3",
    condition: "Recovered",
    admissionDate: "2026-10-15",
    room: "Room 402",
    notes: "Viral gastroenteritis treated successfully"
  },
  {
    id: "pt-108",
    code: "PT-1008",
    name: "Grace Taylor",
    age: 72,
    gender: "Female",
    phone: "+1 (555) 888-9900",
    doctorId: "doc-4",
    condition: "Under Observation",
    admissionDate: "2026-10-19",
    room: "Ortho Bed 12",
    notes: "Total hip replacement, post-op physiotherapy"
  },
  {
    id: "pt-109",
    code: "PT-1009",
    name: "Daniel Garcia",
    age: 29,
    gender: "Male",
    phone: "+1 (555) 999-0011",
    doctorId: "doc-4",
    condition: "Stable",
    admissionDate: "2026-10-21",
    room: "Ortho Bed 08",
    notes: "ACL reconstruction, cast placed"
  },
  {
    id: "pt-110",
    code: "PT-1010",
    name: "Olivia Anderson",
    age: 27,
    gender: "Female",
    phone: "+1 (555) 123-9876",
    doctorId: "doc-5",
    condition: "Recovered",
    admissionDate: "2026-10-16",
    room: "OPD Suite 3",
    notes: "Acute contact dermatitis, topical corticosteroid administered"
  },
  {
    id: "pt-111",
    code: "PT-1011",
    name: "Ethan Thomas",
    age: 63,
    gender: "Male",
    phone: "+1 (555) 234-8765",
    doctorId: "doc-6",
    condition: "Under Observation",
    admissionDate: "2026-10-17",
    room: "Onco Ward 5",
    notes: "Cycle 3 Chemotherapy infusion completed"
  },
  {
    id: "pt-112",
    code: "PT-1012",
    name: "Hannah White",
    age: 46,
    gender: "Female",
    phone: "+1 (555) 345-7654",
    doctorId: "doc-7",
    condition: "Critical",
    admissionDate: "2026-10-22",
    room: "Renal ICU-03",
    notes: "Acute kidney injury, hemodialysis initiated"
  },
  {
    id: "pt-113",
    code: "PT-1013",
    name: "Alexander Scott",
    age: 39,
    gender: "Male",
    phone: "+1 (555) 456-6543",
    doctorId: "doc-8",
    condition: "Stable",
    admissionDate: "2026-10-20",
    room: "Surgical Ward 1",
    notes: "Laparoscopic appendectomy recovery"
  },
  {
    id: "pt-114",
    code: "PT-1014",
    name: "Zoe Robinson",
    age: 31,
    gender: "Female",
    phone: "+1 (555) 567-5432",
    doctorId: "doc-1",
    condition: "Stable",
    admissionDate: "2026-10-23",
    room: "Room 306",
    notes: "Palpitations investigation, Holter monitor attached"
  },
  {
    id: "pt-115",
    code: "PT-1015",
    name: "Lucas Green",
    age: 12,
    gender: "Male",
    phone: "+1 (555) 678-4321",
    doctorId: "doc-3",
    condition: "Under Observation",
    admissionDate: "2026-10-23",
    room: "Pedia Ward 2",
    notes: "Persistent fever and dehydration monitoring"
  }
];

class DataStore {
  constructor() {
    this.STORAGE_KEY_DOCS = "doc_tracker_doctors";
    this.STORAGE_KEY_PTS = "doc_tracker_patients";
    this.STORAGE_KEY_USER = "doc_tracker_auth_user";
    this.init();
  }

  init() {
    if (!localStorage.getItem(this.STORAGE_KEY_DOCS)) {
      localStorage.setItem(this.STORAGE_KEY_DOCS, JSON.stringify(SEED_DOCTORS));
    }
    if (!localStorage.getItem(this.STORAGE_KEY_PTS)) {
      localStorage.setItem(this.STORAGE_KEY_PTS, JSON.stringify(SEED_PATIENTS));
    }
  }

  // --- Auth helpers ---
  login(email, password) {
    // Standard mock authentication
    if (email && password) {
      const user = {
        name: "Admin User",
        email: email,
        role: "Super Administrator",
        avatar: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&q=80&w=200",
        token: "jwt_mock_token_" + Date.now()
      };
      localStorage.setItem(this.STORAGE_KEY_USER, JSON.stringify(user));
      return { success: true, user };
    }
    return { success: false, message: "Invalid credentials" };
  }

  logout() {
    localStorage.removeItem(this.STORAGE_KEY_USER);
  }

  getAuthUser() {
    const raw = localStorage.getItem(this.STORAGE_KEY_USER);
    if (!raw) return null;
    try {
      return JSON.parse(raw);
    } catch {
      return null;
    }
  }

  // --- Doctors CRUD ---
  getDoctors() {
    const raw = localStorage.getItem(this.STORAGE_KEY_DOCS);
    return raw ? JSON.parse(raw) : [];
  }

  getDoctorById(id) {
    const doctors = this.getDoctors();
    return doctors.find(d => d.id === id) || null;
  }

  addDoctor(data) {
    const doctors = this.getDoctors();
    const newDoc = {
      id: "doc-" + Date.now(),
      name: data.name,
      specialization: data.specialization,
      hospital: data.hospital,
      phone: data.phone,
      email: data.email,
      avatar: data.avatar || "https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=200",
      joinedDate: data.joinedDate || new Date().toISOString().split("T")[0],
      status: "Active"
    };
    doctors.unshift(newDoc);
    localStorage.setItem(this.STORAGE_KEY_DOCS, JSON.stringify(doctors));
    return newDoc;
  }

  updateDoctor(id, data) {
    const doctors = this.getDoctors();
    const idx = doctors.findIndex(d => d.id === id);
    if (idx !== -1) {
      doctors[idx] = { ...doctors[idx], ...data };
      localStorage.setItem(this.STORAGE_KEY_DOCS, JSON.stringify(doctors));
      return doctors[idx];
    }
    return null;
  }

  deleteDoctor(id) {
    let doctors = this.getDoctors();
    doctors = doctors.filter(d => d.id !== id);
    localStorage.setItem(this.STORAGE_KEY_DOCS, JSON.stringify(doctors));

    // Also re-assign or handle patients under this doctor
    let patients = this.getPatients();
    patients = patients.filter(p => p.doctorId !== id);
    localStorage.setItem(this.STORAGE_KEY_PTS, JSON.stringify(patients));
    return true;
  }

  // --- Patients CRUD ---
  getPatients() {
    const raw = localStorage.getItem(this.STORAGE_KEY_PTS);
    return raw ? JSON.parse(raw) : [];
  }

  getPatientById(id) {
    const patients = this.getPatients();
    return patients.find(p => p.id === id) || null;
  }

  getPatientsByDoctor(doctorId) {
    const patients = this.getPatients();
    return patients.filter(p => p.doctorId === doctorId);
  }

  addPatient(data) {
    const patients = this.getPatients();
    const nextCode = "PT-" + (1000 + patients.length + 1);
    const newPt = {
      id: "pt-" + Date.now(),
      code: nextCode,
      name: data.name,
      age: parseInt(data.age, 10) || 30,
      gender: data.gender || "Male",
      phone: data.phone,
      doctorId: data.doctorId,
      condition: data.condition || "Stable",
      admissionDate: data.admissionDate || new Date().toISOString().split("T")[0],
      room: data.room || "General Ward",
      notes: data.notes || ""
    };
    patients.unshift(newPt);
    localStorage.setItem(this.STORAGE_KEY_PTS, JSON.stringify(patients));
    return newPt;
  }

  updatePatient(id, data) {
    const patients = this.getPatients();
    const idx = patients.findIndex(p => p.id === id);
    if (idx !== -1) {
      patients[idx] = { ...patients[idx], ...data };
      localStorage.setItem(this.STORAGE_KEY_PTS, JSON.stringify(patients));
      return patients[idx];
    }
    return null;
  }

  deletePatient(id) {
    let patients = this.getPatients();
    patients = patients.filter(p => p.id !== id);
    localStorage.setItem(this.STORAGE_KEY_PTS, JSON.stringify(patients));
    return true;
  }

  // --- Analytics & Aggregations ---
  getAnalytics() {
    const doctors = this.getDoctors();
    const patients = this.getPatients();

    const totalDoctors = doctors.length;
    const totalPatients = patients.length;
    const avgPatientsPerDoctor = totalDoctors > 0 ? (totalPatients / totalDoctors).toFixed(1) : 0;

    // Condition distribution
    const conditionCounts = {
      Critical: 0,
      Stable: 0,
      "Under Observation": 0,
      Recovered: 0
    };
    patients.forEach(p => {
      if (conditionCounts[p.condition] !== undefined) {
        conditionCounts[p.condition]++;
      } else {
        conditionCounts[p.condition] = 1;
      }
    });

    // Patients per doctor
    const patientsPerDoctor = doctors.map(doc => {
      const count = patients.filter(p => p.doctorId === doc.id).length;
      return {
        doctorId: doc.id,
        doctorName: doc.name,
        specialization: doc.specialization,
        count: count
      };
    });

    // Sort by count desc
    patientsPerDoctor.sort((a, b) => b.count - a.count);

    // Admissions trend (by date)
    const dateCounts = {};
    patients.forEach(p => {
      const d = p.admissionDate || "2026-10-20";
      dateCounts[d] = (dateCounts[d] || 0) + 1;
    });

    // Specializations breakdown
    const specialtyCounts = {};
    doctors.forEach(d => {
      specialtyCounts[d.specialization] = (specialtyCounts[d.specialization] || 0) + 1;
    });

    return {
      totalDoctors,
      totalPatients,
      avgPatientsPerDoctor,
      conditionCounts,
      patientsPerDoctor,
      dateCounts,
      specialtyCounts
    };
  }

  resetDemo() {
    localStorage.setItem(this.STORAGE_KEY_DOCS, JSON.stringify(SEED_DOCTORS));
    localStorage.setItem(this.STORAGE_KEY_PTS, JSON.stringify(SEED_PATIENTS));
  }
}

// Global store singleton
window.db = new DataStore();

