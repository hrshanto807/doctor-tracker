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

        // Multi-field search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('specialization', 'like', "%{$s}%")
                  ->orWhere('hospital', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        // Specialization filter
        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        // Hospital filter
        if ($request->filled('hospital')) {
            $query->where('hospital', $request->hospital);
        }

        // Date-wise filter
        if ($request->filled('date')) {
            $query->whereDate('joined_date', '>=', $request->date);
        }

        $doctors = $query->latest('id')->paginate(6)->withQueryString();

        $specializations = Doctor::distinct()->orderBy('specialization')->pluck('specialization');
        $hospitals = Doctor::distinct()->orderBy('hospital')->pluck('hospital');

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
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $patients = $patientsQuery->latest('admission_date')->get();

        $criticalCount = $doctor->patients()->where('condition', 'Critical')->count();
        $stableCount = $doctor->patients()->whereIn('condition', ['Stable', 'Recovered'])->count();

        return view('doctors.show', compact('doctor', 'patients', 'criticalCount', 'stableCount'));
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
            'avatar' => 'nullable|string',
        ]);

        if (empty($validated['joined_date'])) {
            $validated['joined_date'] = now()->toDateString();
        }
        if (empty($validated['avatar'])) {
            $validated['avatar'] = 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=200';
        }

        Doctor::create($validated);

        return redirect()->route('doctors.index')->with('success', "Dr. {$validated['name']} registered successfully.");
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'hospital' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => "required|email|unique:doctors,email,{$doctor->id}",
            'joined_date' => 'nullable|date',
        ]);

        $doctor->update($validated);

        return redirect()->route('doctors.index')->with('success', "Dr. {$doctor->name} updated successfully.");
    }

    public function destroy(Doctor $doctor)
    {
        $name = $doctor->name;
        $doctor->delete();

        return redirect()->route('doctors.index')->with('success', "Doctor Dr. {$name} removed from registry.");
    }
}
