<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorTrackerFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_login_with_demo_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@doctor-tracker.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_authenticated_admin_can_access_patients_and_doctors(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get('/doctors');
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get('/patients');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_update_and_delete_doctor(): void
    {
        $user = User::first();

        // 1. Create doctor
        $createResponse = $this->actingAs($user)->post('/doctors', [
            'name' => 'Dr. Alexander Fleming',
            'specialization' => 'Immunology',
            'hospital' => 'St Mary Hospital',
            'phone' => '+1 (555) 777-8899',
            'email' => 'fleming@stmary.org',
            'joined_date' => '2026-05-15',
        ]);
        $createResponse->assertRedirect('/doctors');
        $this->assertDatabaseHas('doctors', ['email' => 'fleming@stmary.org']);

        $doctor = Doctor::where('email', 'fleming@stmary.org')->first();

        // 2. Update doctor
        $updateResponse = $this->actingAs($user)->put("/doctors/{$doctor->id}", [
            'name' => 'Dr. Sir Alexander Fleming',
            'specialization' => 'Immunology & Microbiology',
            'hospital' => 'Care Guide Central',
            'phone' => '+1 (555) 777-8899',
            'email' => 'fleming@stmary.org',
            'joined_date' => '2026-05-15',
        ]);
        $updateResponse->assertRedirect('/doctors');
        $this->assertDatabaseHas('doctors', ['name' => 'Dr. Sir Alexander Fleming']);

        // 3. Delete doctor
        $deleteResponse = $this->actingAs($user)->delete("/doctors/{$doctor->id}");
        $deleteResponse->assertRedirect('/doctors');
        $this->assertDatabaseMissing('doctors', ['id' => $doctor->id]);
    }

    public function test_admin_can_admit_update_and_delete_patient(): void
    {
        $user = User::first();
        $doctor = Doctor::first();

        // 1. Admit patient
        $storeResponse = $this->actingAs($user)->post('/patients', [
            'name' => 'Marcus Aurelius',
            'age' => 45,
            'gender' => 'Male',
            'phone' => '+1 (555) 321-7654',
            'doctor_id' => $doctor->id,
            'condition' => 'Critical',
            'room' => 'ICU-Room-04',
            'admission_date' => '2026-10-20',
            'notes' => 'Severe acute condition requiring cardiac monitoring.',
        ]);
        $storeResponse->assertStatus(302);
        $this->assertDatabaseHas('patients', ['name' => 'Marcus Aurelius', 'condition' => 'Critical']);

        $patient = Patient::where('name', 'Marcus Aurelius')->first();

        // 2. Update patient
        $updateResponse = $this->actingAs($user)->put("/patients/{$patient->id}", [
            'name' => 'Marcus Aurelius',
            'age' => 45,
            'gender' => 'Male',
            'phone' => '+1 (555) 321-7654',
            'doctor_id' => $doctor->id,
            'condition' => 'Recovered',
            'room' => 'Discharge Lounge',
            'admission_date' => '2026-10-20',
            'notes' => 'Patient stabilized and cleared for discharge.',
        ]);
        $updateResponse->assertStatus(302);
        $this->assertDatabaseHas('patients', ['name' => 'Marcus Aurelius', 'condition' => 'Recovered']);

        // 3. Delete patient
        $deleteResponse = $this->actingAs($user)->delete("/patients/{$patient->id}");
        $deleteResponse->assertStatus(302);
        $this->assertDatabaseMissing('patients', ['id' => $patient->id]);
    }

    public function test_search_and_filters_work(): void
    {
        $user = User::first();

        // Search doctors
        $searchResponse = $this->actingAs($user)->get('/doctors?search=Cardiology');
        $searchResponse->assertStatus(200);

        // Filter patients by condition
        $filterResponse = $this->actingAs($user)->get('/patients?condition=Critical');
        $filterResponse->assertStatus(200);
    }
}
