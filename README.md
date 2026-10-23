# Doctor Tracker – Clinical Administrative Web Application

> **Care Guide BD Full-Stack Assessment**  
> Modern Healthcare Management Platform for Doctor Workloads, Patient Admissions & Clinical Analytics.

---

## 1. Project Overview & Repository Structure

This repository contains **both** the full-stack dynamic Laravel application and the standalone pure Raw HTML/CSS/JS prototype in a clean, unified structure on the `main` branch:

```
doctor-tracker/
├── app/                    # Laravel Models, Controllers & Providers
│   ├── Http/Controllers/  # AuthController, DashboardController, DoctorController, PatientController
│   └── Models/            # Doctor, Patient, User
├── database/               # Migrations, Factories & DatabaseSeeder
├── frontend-html/          # Standalone 100% Pure Raw HTML/CSS/JS Frontend
│   ├── index.html         # Entry point router
│   ├── login.html         # Admin authentication with 1-click demo fill
│   ├── dashboard.html     # Real-time analytics & 4 Chart.js charts
│   ├── doctors.html       # Doctor roster with multi-filters & CRUD
│   ├── doctor-details.html# Doctor profile & assigned patient roster
│   ├── patients.html      # Inpatient registry & condition tracking
│   └── assets/            # Pure modular CSS (zero inline/internal) & JS
├── public/                 # Laravel Web Root & Assets (public/assets/)
├── resources/views/        # Dynamic Blade Templates & Component Layouts
├── routes/web.php          # 22 RESTful Web Endpoints
├── tests/                  # Automated PHPUnit / Pest Test Suite (100% passing)
├── artisan                 # Laravel CLI binary
└── composer.json           # Dependencies
```

---

## 2. Quick Start Guide

### Option 1: Running the Dynamic Laravel Application

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Setup environment & generate app key:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Migrate and seed database (preloaded with 8 doctors and 15 patients):**
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```

4. **Start local development server:**
   ```bash
   php artisan serve
   ```
   Open **`http://127.0.0.1:8000`** in your browser.

5. **Run automated test suite:**
   ```bash
   php artisan test
   ```

---

### Option 2: Running the Standalone Pure HTML Frontend

If you wish to test or grade the pure client-side HTML/CSS/JS interface independently:

```bash
cd frontend-html
php -S localhost:3000
```
Open **`http://localhost:3000`** in your browser.

---

## 3. Demo Admin Credentials

- **Email:** `admin@doctor-tracker.com`
- **Password:** `admin123`
*(The login page also provides a 1-click **"Fill Demo Credentials"** button for instantaneous testing)*

---

## 4. Key Functional Features

### A. Authentication & Security
- Session-based admin authentication with protected route middleware (`/dashboard`, `/doctors`, `/patients`).
- Password visibility toggle and 1-click demo credentials autofill.
- CSRF protection across all forms and API endpoints.

### B. Doctor Management
- List doctors with dynamic pagination (6 per page) and total count badges.
- Multi-field search across Name, Hospital, Specialization, Email, and Phone.
- Multi-filter dropdowns: Filter by Specialization, Filter by Hospital, and Date-wise Joined filter.
- Doctor Detail Page: Displays assigned patient count, critical case count, stable/recovered count, and patient roster under that physician.
- Modals for Add Doctor, Edit Doctor, and Delete Confirmation with safety warnings.

### C. Patient Management
- List patients with pagination (7 per page) and status indicators.
- Multi-field search across Patient Name, Patient Code (`PT-1001`), Phone, and Doctor Name.
- Dynamic Filters: Condition (Critical, Stable, Under Observation, Recovered), Attending Doctor, and Date-wise Admission filter.
- Modals for Admitting Patient, Editing Details/Condition/Room, and Delete Confirmation.

### D. Real-Time Dashboard & Analytics
- **4 Real-time KPI Cards**: Total Doctors, Total Patients, Patients Per Doctor average, and Critical Cases.
- **4 Chart.js Data Visualizations**:
  1. *Patients per Doctor* (Bar Chart)
  2. *Admissions Velocity / Trend* (Area/Line Chart)
  3. *Health Condition Breakdown* (Doughnut Chart)
  4. *Doctor Specialization Distribution* (Horizontal Bar Chart)
- **Recent Admissions Table**: Latest 6 admitted patients with status badges and quick action links.
- **Top Active Specialists**: High-census physicians ranked dynamically by patient count.

### E. Code Quality & Responsive Design
- **100% External CSS**: Zero inline `style="..."` attributes and zero internal `<style>` tags.
- **Laptop & Mobile Responsive**: Viewport-constrained layouts avoiding any horizontal overflow on laptop screens (1366x768, 1440x900, 1920x1080) and mobile devices.
