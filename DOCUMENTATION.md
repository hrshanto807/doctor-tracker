# Doctor Tracker – Project Documentation & Evaluation Guide

> **Candidate:** hrshanto807 (`shantosss4907@gmail.com`)  
> **Repository:** [https://github.com/hrshanto807/doctor-tracker-frontend](https://github.com/hrshanto807/doctor-tracker-frontend)  
> **Assessment:** Care Guide BD Full-Stack Developer Assessment  
> **Date:** October 2026

---

## 1. Executive Summary

**Doctor Tracker** is a clinical administration and medical staff management web portal engineered for healthcare operators. The system empowers hospital administrators to monitor physician allocations, manage patient admissions across diverse health condition tiers, track bed assignments, and inspect real-time institutional metrics through dynamic data visualizations.

The project is delivered with **two deployment options in a unified repository on the `main` branch**:
1. **Dynamic Laravel Full-Stack Application:** Dynamic controllers, Eloquent ORM, SQLite database, seeders, authentication, and Blade components.
2. **Standalone Pure HTML/CSS/JS Prototype (`frontend-html/`):** Pure client-side version using LocalStorage with zero build steps.

---

## 2. System Architecture & Tech Stack

```
┌─────────────────────────────────────────────────────────────┐
│                      Client Layer                            │
│   Responsive HTML5 • Pure Modular CSS3 • Chart.js • Icons   │
│   (Zero inline styles • Zero internal <style> tags)         │
└──────────────────────────────┬──────────────────────────────┘
                               │ HTTP / CSRF / Blade
┌──────────────────────────────▼──────────────────────────────┐
│                    Application Layer                         │
│  Laravel 11/12 • Session Auth • RESTful Resource Routing    │
│  Controllers: AuthController, DashboardController,          │
│               DoctorController, PatientController            │
└──────────────────────────────┬──────────────────────────────┘
                               │ Eloquent ORM
┌──────────────────────────────▼──────────────────────────────┐
│                      Database Layer                          │
│   SQLite / MySQL (Users, Doctors, Patients with Indexes)     │
└─────────────────────────────────────────────────────────────┘
```

- **Backend Framework:** Laravel 11/12 (PHP 8.2+)
- **Frontend Architecture:** Laravel Blade Components, Bootstrap 5 (grid only), Font Awesome 6.5
- **Design System:** Custom CSS tokens, 100% external modular stylesheets (`style.css`, `dashboard.css`, `auth.css`)
- **Data Visualization:** Chart.js 4.4
- **Database:** SQLite (default for zero-configuration local review) with full MySQL compatibility
- **Automated Testing:** PHPUnit / Pest with RefreshDatabase and feature assertion suite

---

## 3. Database Schema

### `doctors` Table
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Unique Doctor Identifier |
| `name` | `VARCHAR(255)` | Indexed | Full physician name |
| `specialization` | `VARCHAR(255)` | Indexed | Clinical discipline (Cardiology, Pediatrics, etc.) |
| `hospital` | `VARCHAR(255)` | Indexed | Affiliated healthcare facility |
| `phone` | `VARCHAR(50)` | Not Null | Direct contact phone |
| `email` | `VARCHAR(255)` | Unique, Indexed | Doctor email address |
| `avatar` | `TEXT` | Nullable | Avatar image URL |
| `joined_date` | `DATE` | Indexed | Date physician joined registry |
| `status` | `VARCHAR(50)` | Default 'Active' | Active / On Leave / Inactive |
| `created_at` / `updated_at` | `TIMESTAMP` | Timestamps | Audit record stamps |

### `patients` Table
| Column | Type | Attributes | Description |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Unique Patient ID |
| `code` | `VARCHAR(50)` | Unique, Indexed | Unique clinical code (e.g. `PT-1001`) |
| `name` | `VARCHAR(255)` | Indexed | Full patient name |
| `age` | `INTEGER` | Not Null | Patient age in years |
| `gender` | `VARCHAR(20)` | Not Null | Male / Female / Other |
| `phone` | `VARCHAR(50)` | Not Null | Emergency / patient phone contact |
| `doctor_id` | `BIGINT UNSIGNED` | Foreign Key (doctors.id) | Assigned attending physician |
| `condition` | `VARCHAR(50)` | Indexed | Critical, Stable, Under Observation, Recovered |
| `room` | `VARCHAR(100)` | Nullable | Ward and bed allocation (e.g., Ward 3B, Bed 04) |
| `admission_date`| `DATE` | Indexed | Inpatient admission date |
| `notes` | `TEXT` | Nullable | Diagnosis and medical observations |

---

## 4. Endpoints & Route Reference

| HTTP Method | Route URI | Name | Purpose |
|---|---|---|---|
| `GET` | `/login` | `login` | Displays admin login portal |
| `POST` | `/login` | - | Authenticates administrator credentials |
| `POST` | `/logout` | `logout` | Ends session and logs out |
| `GET` | `/dashboard` | `dashboard` | Executive dashboard & Chart.js visual metrics |
| `GET` | `/doctors` | `doctors.index` | Doctor listing, search, multi-filters & pagination |
| `POST` | `/doctors` | `doctors.store` | Registers a new doctor into the database |
| `GET` | `/doctors/{id}` | `doctors.show` | Doctor profile view & assigned patient roster |
| `PUT` | `/doctors/{id}` | `doctors.update` | Updates existing doctor profile |
| `DELETE` | `/doctors/{id}` | `doctors.destroy` | Deletes doctor record and safely cascades |
| `GET` | `/patients` | `patients.index` | Inpatient directory with condition filters |
| `POST` | `/patients` | `patients.store` | Admits new patient with auto-generated code |
| `PUT` | `/patients/{id}`| `patients.update` | Updates patient condition, room, and details |
| `DELETE` | `/patients/{id}`| `patients.destroy`| Discharges / removes patient record |

---

## 5. Local Setup & Installation Guide

### Option A: Laravel Dynamic Application (Recommended)

1. **Clone the repository:**
   ```bash
   git clone https://github.com/hrshanto807/doctor-tracker-frontend.git
   cd doctor-tracker-frontend
   ```

2. **Install Composer dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Prepare SQLite Database & Seed Initial Records:**
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed
   ```
   > *The seeder preloads 1 Administrator, 8 Specialist Doctors, and 15 Patients across all clinical conditions.*

5. **Start Development Server:**
   ```bash
   php artisan serve
   ```
   > Access the live app at **`http://127.0.0.1:8000`**.

---

### Option B: Standalone Pure HTML/CSS/JS Prototype

```bash
cd frontend-html
php -S localhost:3000
```
> Access at **`http://localhost:3000`**.

---

## 6. Automated Testing Verification

The project includes automated Feature and Unit tests (`tests/Feature/DoctorTrackerFlowTest.php`, `ExampleTest.php`):

```bash
php artisan test
```

### Verified Test Cases:
- `test_login_page_returns_successful_response`
- `test_unauthenticated_user_redirected_to_login`
- `test_admin_can_login_with_demo_credentials`
- `test_authenticated_admin_can_access_patients_and_doctors`
- `test_admin_can_create_update_and_delete_doctor`
- `test_admin_can_admit_update_and_delete_patient`
- `test_search_and_filters_work`

**Test Results:** `Tests: 8 passed (27 assertions)` — 100% Green.

---

## 7. Demo Credentials

- **Email:** `admin@doctor-tracker.com`
- **Password:** `admin123`
*(A 1-click **"Fill Demo Credentials"** button is also available directly on the login form for rapid evaluation).*

---

## 8. Compliance & Deliverables Checklist

- [x] **Git Repository:** Single unified repository on GitHub (`main` branch).
- [x] **Zero Inline/Internal Styles:** 100% external modular CSS files.
- [x] **100% Responsive Design:** Tested on mobile (375px), tablet (768px), and laptop screens (1366px, 1440px, 1920px) without horizontal viewport overflow.
- [x] **Complete CRUD:** Both Doctors and Patients support Create, Read, Update, Delete with modal dialogs and validation feedback.
- [x] **Multi-Criteria Search & Filtering:** Filter doctors by specialty, hospital, and joined date; filter patients by condition, doctor, and admission date.
- [x] **Interactive Data Visualizations:** 4 Chart.js charts on dashboard responding to database metrics.
- [x] **Isolated HTML Deliverable:** Preserved in `frontend-html/` folder.

