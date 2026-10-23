<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDoctors = Doctor::count();
        $totalPatients = Patient::count();
        $avgPatientsPerDoctor = $totalDoctors > 0 ? round($totalPatients / $totalDoctors, 1) : 0;
        $criticalCases = Patient::where('condition', 'Critical')->count();

        // 1. Patients per Doctor chart data
        $patientsPerDoctor = Doctor::withCount('patients')
            ->orderByDesc('patients_count')
            ->get();

        // 2. Condition breakdown chart data
        $conditionCounts = Patient::select('condition', DB::raw('count(*) as count'))
            ->groupBy('condition')
            ->pluck('count', 'condition')
            ->toArray();

        // Ensure all conditions exist in array
        $allConditions = ['Stable' => 0, 'Critical' => 0, 'Under Observation' => 0, 'Recovered' => 0];
        $conditionCounts = array_merge($allConditions, $conditionCounts);

        // 3. Date-based admissions trend
        $dateCounts = Patient::select(DB::raw('date(admission_date) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // 4. Specialization distribution
        $specialtyCounts = Doctor::select('specialization', DB::raw('count(*) as count'))
            ->groupBy('specialization')
            ->pluck('count', 'specialization')
            ->toArray();

        // 5. Recent Patients
        $recentPatients = Patient::with('doctor')
            ->latest('admission_date')
            ->take(6)
            ->get();

        // 6. Top Doctors
        $topDoctors = $patientsPerDoctor->take(5);

        return view('dashboard', compact(
            'totalDoctors',
            'totalPatients',
            'avgPatientsPerDoctor',
            'criticalCases',
            'patientsPerDoctor',
            'conditionCounts',
            'dateCounts',
            'specialtyCounts',
            'recentPatients',
            'topDoctors'
        ));
    }
}
