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

        // Multi-field search
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

        // Condition filter
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Doctor filter
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Date-wise filter
        if ($request->filled('date')) {
            $query->whereDate('admission_date', '>=', $request->date);
        }

        $patients = $query->latest('admission_date')->paginate(7)->withQueryString();
        $doctors = Doctor::orderBy('name')->get(['id', 'name', 'specialization']);

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
            'admission_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['admission_date'])) {
            $validated['admission_date'] = now()->toDateString();
        }

        $lastId = Patient::max('id') ?? 0;
        $validated['code'] = 'PT-' . (1000 + $lastId + 1);

        Patient::create($validated);

        return back()->with('success', "Patient {$validated['name']} admitted successfully.");
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:120',
            'gender' => 'required|in:Male,Female,Other',
            'phone' => 'required|string|max:50',
            'doctor_id' => 'required|exists:doctors,id',
            'condition' => 'required|in:Critical,Stable,Under Observation,Recovered',
            'room' => 'nullable|string|max:100',
            'admission_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $patient->update($validated);

        return back()->with('success', "Patient {$patient->name}'s record updated successfully.");
    }

    public function destroy(Patient $patient)
    {
        $name = $patient->name;
        $patient->delete();

        return back()->with('success', "Patient record for {$name} removed.");
    }
}
