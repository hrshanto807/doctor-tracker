# Laravel Dynamic Conversion Guide: Doctor Tracker

This guide explains how to convert the raw HTML/CSS/JS frontend in this repository into a full dynamic **Laravel 11 / 12** web application.

---

## 1. Directory Mapping to Laravel

```
Doctor Tracker Raw HTML                Laravel Project
├── assets/
│   ├── css/style.css           -->    public/assets/css/style.css
│   ├── css/dashboard.css       -->    public/assets/css/dashboard.css
│   ├── js/data.js              -->    (Replaced by Laravel Eloquent & DB)
│   ├── js/app.js               -->    public/assets/js/app.js
│   ├── js/dashboard.js         -->    public/assets/js/dashboard.js (fetches JSON)
│   ├── js/doctors.js           -->    public/assets/js/doctors.js
│   └── js/patients.js          -->    public/assets/js/patients.js
│
├── login.html                  -->    resources/views/auth/login.blade.php
├── dashboard.html              -->    resources/views/dashboard.blade.php
├── doctors.html                -->    resources/views/doctors/index.blade.php
├── doctor-details.html         -->    resources/views/doctors/show.blade.php
├── patients.html               -->    resources/views/patients/index.blade.php
└── (Common Layouts)            -->    resources/views/layouts/app.blade.php
                                       resources/views/layouts/sidebar.blade.php
                                       resources/views/layouts/topbar.blade.php
```

---

## 2. Database Migrations

### 2.1 Doctors Migration (`create_doctors_table.php`)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('specialization');
            $table->string('hospital');
            $table->string('phone');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->date('joined_date')->default(now());
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();

            // Indexes for fast search & filtering
            $table->index(['specialization', 'hospital']);
            $table->index('joined_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
```

### 2.2 Patients Migration (`create_patients_table.php`)
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., PT-1001
            $table->string('name');
            $table->integer('age');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('phone');
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->enum('condition', ['Critical', 'Stable', 'Under Observation', 'Recovered'])->default('Stable');
            $table->date('admission_date')->default(now());
            $table->string('room')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for performance optimization
            $table->index('condition');
            $table->index('admission_date');
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
```

---

## 3. Eloquent Models & Relationships

### `app/Models/Doctor.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialization',
        'hospital',
        'phone',
        'email',
        'avatar',
        'joined_date',
        'status',
    ];

    protected $casts = [
        'joined_date' => 'date',
    ];

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
}
```

### `app/Models/Patient.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'age',
        'gender',
        'phone',
        'doctor_id',
        'condition',
        'admission_date',
        'room',
        'notes',
    ];

    protected $casts = [
        'admission_date' => 'date',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
```

---

## 4. Controllers with Query Optimization

### 4.1 `DashboardController.php`
```php
<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDoctors = Doctor::count();
        $totalPatients = Patient::count();
        $avgPatientsPerDoctor = $totalDoctors > 0 ? round($totalPatients / $totalDoctors, 1) : 0;
        $criticalCases = Patient::where('condition', 'Critical')->count();

        // Optimized Aggregations
        $patientsPerDoctor = Doctor::withCount('patients')
            ->orderByDesc('patients_count')
            ->take(5)
            ->get();

        $conditionCounts = Patient::select('condition', DB::raw('count(*) as count'))
            ->groupBy('condition')
            ->pluck('count', 'condition')
            ->toArray();

        $recentPatients = Patient::with('doctor')
            ->latest('admission_date')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'totalDoctors',
            'totalPatients',
            'avgPatientsPerDoctor',
            'criticalCases',
            'patientsPerDoctor',
            'conditionCounts',
            'recentPatients'
        ));
    }
}
```

### 4.2 `DoctorController.php` (Searching, Date Filtering & Pagination)
```php
<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::withCount('patients');

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('specialization', 'like', "%{$s}%")
                  ->orWhere('hospital', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // Specialization Filter
        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        // Hospital Filter
        if ($request->filled('hospital')) {
            $query->where('hospital', $request->hospital);
        }

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('joined_date', '>=', $request->date);
        }

        $doctors = $query->latest()->paginate(10)->withQueryString();
        $specializations = Doctor::distinct()->pluck('specialization');
        $hospitals = Doctor::distinct()->pluck('hospital');

        return view('doctors.index', compact('doctors', 'specializations', 'hospitals'));
    }

    public function show(Doctor $doctor, Request $request)
    {
        $patientsQuery = $doctor->patients();

        if ($request->filled('condition')) {
            $patientsQuery->where('condition', $request->condition);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $patientsQuery->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        $patients = $patientsQuery->latest('admission_date')->get();

        return view('doctors.show', compact('doctor', 'patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'hospital' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|unique:doctors,email',
            'joined_date' => 'nullable|date',
        ]);

        Doctor::create($validated);
        return redirect()->route('doctors.index')->with('success', 'Doctor registered successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('doctors.index')->with('success', 'Doctor record removed.');
    }
}
```

### 4.3 `PatientController.php` (Filter by Condition & Date, Search, Pagination)
```php
<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::with('doctor');

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhereHas('doctor', function ($dq) use ($s) {
                      $dq->where('name', 'like', "%{$s}%");
                  });
            });
        }

        // Condition Filter
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Doctor Filter
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Date-wise Filter
        if ($request->filled('date')) {
            $query->whereDate('admission_date', '>=', $request->date);
        }

        $patients = $query->latest('admission_date')->paginate(10)->withQueryString();
        $doctors = Doctor::all(['id', 'name', 'specialization']);

        return view('patients.index', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:120',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string|max:50',
            'doctor_id' => 'required|exists:doctors,id',
            'condition' => 'required|in:Critical,Stable,Under Observation,Recovered',
            'room' => 'nullable|string|max:100',
            'admission_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['code'] = 'PT-' . (Patient::max('id') + 1001);
        Patient::create($validated);

        return back()->with('success', 'Patient admitted successfully.');
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string|max:50',
            'doctor_id' => 'required|exists:doctors,id',
            'condition' => 'required|in:Critical,Stable,Under Observation,Recovered',
            'room' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $patient->update($validated);
        return back()->with('success', 'Patient information updated.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return back()->with('success', 'Patient record deleted.');
    }
}
```

---

## 5. Routes Definition (`routes/web.php`)

```php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Doctor Management
    Route::resource('doctors', DoctorController::class);

    // Patient Management
    Route::resource('patients', PatientController::class);
});
```

