# Doctor Tracker – Project Documentation & Evaluation Guide

> **Candidate:** Md. Shanto (`shantosss4907@gmail.com`)  
> **Position:** Full-Stack Developer Assessment  
> **Company:** Care Guide BD  
> **Date:** October 2026

---

## 1. Quick Access Links

| Asset | Description | Link |
|---|---|---|
| 🌐 **Live Demo (Vercel)** | Instant browser preview (Zero setup required) | [https://doctor-tracker-frontend-xot1.vercel.app/](https://doctor-tracker-frontend-xot1.vercel.app/) |
| 🐘 **Laravel Repository** | Full dynamic backend & Blade application | [https://github.com/hrshanto807/doctor-tracker](https://github.com/hrshanto807/doctor-tracker) |
| 💻 **HTML/CSS/JS Repository** | Standalone pure client-side prototype | [https://github.com/hrshanto807/doctor-tracker-frontend](https://github.com/hrshanto807/doctor-tracker-frontend) |

---

## 2. Executive Summary

**Doctor Tracker** is an administrative clinical management portal engineered for healthcare operators. The system empowers hospital administrators to monitor physician allocations, manage patient admissions across diverse health condition tiers (*Critical, Stable, Under Observation, Recovered*), track ward/bed assignments, and inspect real-time institutional metrics through dynamic data visualizations.

---

## 3. Key Features Implemented

### A. Authentication & Security
- **Role:** Administrator access.
- **Protected Routes:** Middleware enforces authentication on `/dashboard`, `/doctors`, and `/patients`.
- **Demo Access:** 1-click **"Fill Demo Credentials"** button on the login screen for instantaneous testing without manual typing.
- **Security:** CSRF protection, secure hashed passwords (`bcrypt`), and session-based auth guards.

### B. Doctor Management (CRUD)
- **Roster Directory:** Tabular listing with dynamic pagination (6 per page) and total count badges.
- **Multi-Field Search:** Real-time query matching across Doctor Name, Hospital, Specialization, Email, and Phone.
- **Multi-Filter Toolbar:**
  - *Specialization Filter* (Cardiology, Pediatrics, Orthopedics, etc.)
  - *Hospital/Facility Filter* (Apollo, Square, Evercare, etc.)
  - *Joined Date Filter* (Date-wise tracking)
- **Doctor Profile View (`/doctors/{id}`):** Dedicated physician profile page showing clinical stats (*assigned patients, critical cases, stable/recovered ratio*) and the list of patients under their care.
- **Modal Operations:** Add Doctor, Edit Doctor Details, and Delete Confirmation with safety warnings.

### C. Patient Management (CRUD)
- **Inpatient Directory:** Comprehensive patient list with status badges and dynamic pagination (7 per page).
- **Multi-Field Search:** Searches by Patient Name, Patient Code (`PT-1001`), Phone, or Attending Doctor.
- **Multi-Filter Toolbar:**
  - *Condition Status Filter* (`Critical`, `Stable`, `Under Observation`, `Recovered`)
  - *Attending Doctor Filter*
  - *Admission Date Filter* (Date-wise admissions tracking)
- **Modal Operations:** Admit Patient with auto-generated unique ID, Edit Details/Condition/Room, and Delete Confirmation.

### D. Real-Time Clinical Dashboard & Visual Analytics
- **4 Real-Time KPI Cards:**
  1. *Total Doctors* (Active medical staff)
  2. *Total Patients* (Active inpatients)
  3. *Patients per Doctor* (Average physician census)
  4. *Critical Cases* (High-priority alert count)
- **4 Interactive Chart.js Visualizations:**
  1. **Patients per Doctor:** Bar chart displaying patient workload per physician.
  2. **Admissions Trend:** Smooth area/line chart tracking daily patient admission velocity.
  3. **Condition Distribution:** Doughnut chart breaking down patient health statuses.
  4. **Specialization Share:** Horizontal bar chart displaying doctor distribution by department.
- **Recent Admissions Feed:** Quick tabular view of the latest admitted patients with direct action triggers.
- **Top Active Specialists:** Ranked physicians by active patient load.

### E. Code Quality & Standards
- **100% External Modular CSS:** Strictly zero inline styles (`style="..."`) and zero internal `<style>` tags.
- **100% Responsive Design:** Layouts adapt seamlessly across mobile (375px), tablet (768px), and laptop/desktop viewports (1366px, 1440px, 1920px) with zero horizontal overflow.
- **Automated Testing Suite:** 8 automated feature/unit tests passing with 27 assertions.

---

## 4. How to Run the Dynamic Laravel Project Locally

```bash
# 1. Clone the Laravel repository
git clone https://github.com/hrshanto807/doctor-tracker.git
cd doctor-tracker

# 2. Install PHP dependencies
composer install

# 3. Environment configuration & key generation
cp .env.example .env
php artisan key:generate

# 4. Create SQLite database & seed preloaded data (8 Doctors, 15 Patients, 1 Admin)
touch database/database.sqlite
php artisan migrate:fresh --seed

# 5. Start development server
php artisan serve
```
> Open **`http://127.0.0.1:8000`** in your browser.

### Run Automated Tests:
```bash
php artisan test
```
> **Output:** `8 passed (27 assertions)` — 100% Green.

---

## 5. Demo Admin Credentials

- **Email:** `admin@doctor-tracker.com`
- **Password:** `admin123`
*(Or click the **"Fill Demo Credentials"** button on the login page)*
