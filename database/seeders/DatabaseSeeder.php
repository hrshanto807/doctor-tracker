<?php
namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        User::firstOrCreate(
            ['email' => 'admin@doctor-tracker.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Seed Doctors
        $doctorsData = [
            [
                'name'           => 'Dr. Sarah Jenkins',
                'specialization' => 'Cardiology',
                'hospital'       => 'Care Guide Medical Center',
                'phone'          => '+1 (555) 234-5678',
                'email'          => 'sarah.jenkins@careguide.com',
                'avatar'         => 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=200',
                'joined_date'    => '2026-01-15',
                'status'         => 'Active',
            ],
            [
                'name'           => 'Dr. Marcus Vance',
                'specialization' => 'Neurology',
                'hospital'       => 'City Central Hospital',
                'phone'          => '+1 (555) 345-6789',
                'email'          => 'marcus.vance@citycentral.org',
                'avatar'         => 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=200',
                'joined_date'    => '2026-02-10',
                'status'         => 'Active',
            ],
            [
                'name'           => 'Dr. Elena Rostova',
                'specialization' => 'Pediatrics',
                'hospital'       => 'Apollo Specialized Hospital',
                'phone'          => '+1 (555) 456-7890',
                'email'          => 'elena.rostova@apollohealth.com',
                'avatar'         => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&q=80&w=200',
                'joined_date'    => '2026-03-01',
                'status'         => 'Active',
            ],
            [
                'name'           => 'Dr. James Wilson',
                'specialization' => 'Orthopedics',
                'hospital'       => 'Square Health Center',
                'phone'          => '+1 (555) 567-8901',
                'email'          => 'james.wilson@squarehealth.org',
                'avatar'         => 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=200',
                'joined_date'    => '2026-03-20',
                'status'         => 'Active',
            ],
            [
                'name'           => 'Dr. Aisha Patel',
                'specialization' => 'Dermatology',
                'hospital'       => 'Evercare General',
                'phone'          => '+1 (555) 678-9012',
                'email'          => 'aisha.patel@evercare.com',
                'avatar'         => 'https://images.unsplash.com/photo-1614608682850-e0d6ed316d47?auto=format&fit=crop&q=80&w=200',
                'joined_date'    => '2026-04-12',
                'status'         => 'Active',
            ],
            [
                'name'           => 'Dr. Robert Chen',
                'specialization' => 'Oncology',
                'hospital'       => 'Care Guide Medical Center',
                'phone'          => '+1 (555) 789-0123',
                'email'          => 'robert.chen@careguide.com',
                'avatar'         => 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&q=80&w=200',
                'joined_date'    => '2026-05-04',
                'status'         => 'Active',
            ],
            [
                'name'           => 'Dr. Amina Begum',
                'specialization' => 'Nephrology',
                'hospital'       => 'Apollo Specialized Hospital',
                'phone'          => '+1 (555) 890-1234',
                'email'          => 'amina.begum@apollohealth.com',
                'avatar'         => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&q=80&w=200',
                'joined_date'    => '2026-06-18',
                'status'         => 'Active',
            ],
            [
                'name'           => 'Dr. David Kim',
                'specialization' => 'General Surgery',
                'hospital'       => 'City Central Hospital',
                'phone'          => '+1 (555) 901-2345',
                'email'          => 'david.kim@citycentral.org',
                'avatar'         => 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?auto=format&fit=crop&q=80&w=200',
                'joined_date'    => '2026-07-22',
                'status'         => 'Active',
            ],
        ];

        $doctors = [];
        foreach ($doctorsData as $doc) {
            $doctors[] = Doctor::firstOrCreate(['email' => $doc['email']], $doc);
        }

        // 3. Seed Patients
        $patientsData = [
            [
                'code'           => 'PT-1001',
                'name'           => 'Jonathan Miller',
                'age'            => 58,
                'gender'         => 'Male',
                'phone'          => '+1 (555) 111-2233',
                'doctor_id'      => $doctors[0]->id,
                'condition'      => 'Critical',
                'admission_date' => '2026-10-18',
                'room'           => 'ICU-02',
                'notes'          => 'Acute Coronary Syndrome, continuous ECG monitoring',
            ],
            [
                'code'           => 'PT-1002',
                'name'           => 'Clara Hughes',
                'age'            => 42,
                'gender'         => 'Female',
                'phone'          => '+1 (555) 222-3344',
                'doctor_id'      => $doctors[0]->id,
                'condition'      => 'Stable',
                'admission_date' => '2026-10-19',
                'room'           => 'Room 304',
                'notes'          => 'Post-angioplasty recovery, stable vitals',
            ],
            [
                'code'           => 'PT-1003',
                'name'           => 'Michael Bennett',
                'age'            => 67,
                'gender'         => 'Male',
                'phone'          => '+1 (555) 333-4455',
                'doctor_id'      => $doctors[0]->id,
                'condition'      => 'Under Observation',
                'admission_date' => '2026-10-21',
                'room'           => 'Ward 2B',
                'notes'          => 'Hypertensive urgency, blood pressure adjusting',
            ],
            [
                'code'           => 'PT-1004',
                'name'           => 'Sophia Martinez',
                'age'            => 34,
                'gender'         => 'Female',
                'phone'          => '+1 (555) 444-5566',
                'doctor_id'      => $doctors[1]->id,
                'condition'      => 'Recovered',
                'admission_date' => '2026-10-14',
                'room'           => 'Room 108',
                'notes'          => 'Severe migraine treated, ready for discharge',
            ],
            [
                'code'           => 'PT-1005',
                'name'           => 'William Wright',
                'age'            => 51,
                'gender'         => 'Male',
                'phone'          => '+1 (555) 555-6677',
                'doctor_id'      => $doctors[1]->id,
                'condition'      => 'Critical',
                'admission_date' => '2026-10-20',
                'room'           => 'Neuro ICU-01',
                'notes'          => 'Ischemic stroke episode, neurological assessment ongoing',
            ],
            [
                'code'           => 'PT-1006',
                'name'           => 'Emma Watson',
                'age'            => 8,
                'gender'         => 'Female',
                'phone'          => '+1 (555) 666-7788',
                'doctor_id'      => $doctors[2]->id,
                'condition'      => 'Stable',
                'admission_date' => '2026-10-22',
                'room'           => 'Pedia Ward 4',
                'notes'          => 'Acute bronchitis, nebulization therapy',
            ],
            [
                'code'           => 'PT-1007',
                'name'           => 'Liam O\'Connor',
                'age'            => 5,
                'gender'         => 'Male',
                'phone'          => '+1 (555) 777-8899',
                'doctor_id'      => $doctors[2]->id,
                'condition'      => 'Recovered',
                'admission_date' => '2026-10-15',
                'room'           => 'Room 402',
                'notes'          => 'Viral gastroenteritis treated successfully',
            ],
            [
                'code'           => 'PT-1008',
                'name'           => 'Grace Taylor',
                'age'            => 72,
                'gender'         => 'Female',
                'phone'          => '+1 (555) 888-9900',
                'doctor_id'      => $doctors[3]->id,
                'condition'      => 'Under Observation',
                'admission_date' => '2026-10-19',
                'room'           => 'Ortho Bed 12',
                'notes'          => 'Total hip replacement, post-op physiotherapy',
            ],
            [
                'code'           => 'PT-1009',
                'name'           => 'Daniel Garcia',
                'age'            => 29,
                'gender'         => 'Male',
                'phone'          => '+1 (555) 999-0011',
                'doctor_id'      => $doctors[3]->id,
                'condition'      => 'Stable',
                'admission_date' => '2026-10-21',
                'room'           => 'Ortho Bed 08',
                'notes'          => 'ACL reconstruction, cast placed',
            ],
            [
                'code'           => 'PT-1010',
                'name'           => 'Olivia Anderson',
                'age'            => 27,
                'gender'         => 'Female',
                'phone'          => '+1 (555) 123-9876',
                'doctor_id'      => $doctors[4]->id,
                'condition'      => 'Recovered',
                'admission_date' => '2026-10-16',
                'room'           => 'OPD Suite 3',
                'notes'          => 'Acute contact dermatitis, topical corticosteroid administered',
            ],
            [
                'code'           => 'PT-1011',
                'name'           => 'Ethan Thomas',
                'age'            => 63,
                'gender'         => 'Male',
                'phone'          => '+1 (555) 234-8765',
                'doctor_id'      => $doctors[5]->id,
                'condition'      => 'Under Observation',
                'admission_date' => '2026-10-17',
                'room'           => 'Onco Ward 5',
                'notes'          => 'Cycle 3 Chemotherapy infusion completed',
            ],
            [
                'code'           => 'PT-1012',
                'name'           => 'Hannah White',
                'age'            => 46,
                'gender'         => 'Female',
                'phone'          => '+1 (555) 345-7654',
                'doctor_id'      => $doctors[6]->id,
                'condition'      => 'Critical',
                'admission_date' => '2026-10-22',
                'room'           => 'Renal ICU-03',
                'notes'          => 'Acute kidney injury, hemodialysis initiated',
            ],
            [
                'code'           => 'PT-1013',
                'name'           => 'Alexander Scott',
                'age'            => 39,
                'gender'         => 'Male',
                'phone'          => '+1 (555) 456-6543',
                'doctor_id'      => $doctors[7]->id,
                'condition'      => 'Stable',
                'admission_date' => '2026-10-20',
                'room'           => 'Surgical Ward 1',
                'notes'          => 'Laparoscopic appendectomy recovery',
            ],
            [
                'code'           => 'PT-1014',
                'name'           => 'Zoe Robinson',
                'age'            => 31,
                'gender'         => 'Female',
                'phone'          => '+1 (555) 567-5432',
                'doctor_id'      => $doctors[0]->id,
                'condition'      => 'Stable',
                'admission_date' => '2026-10-23',
                'room'           => 'Room 306',
                'notes'          => 'Palpitations investigation, Holter monitor attached',
            ],
            [
                'code'           => 'PT-1015',
                'name'           => 'Lucas Green',
                'age'            => 12,
                'gender'         => 'Male',
                'phone'          => '+1 (555) 678-4321',
                'doctor_id'      => $doctors[2]->id,
                'condition'      => 'Under Observation',
                'admission_date' => '2026-10-23',
                'room'           => 'Pedia Ward 2',
                'notes'          => 'Persistent fever and dehydration monitoring',
            ],
        ];

        foreach ($patientsData as $pt) {
            Patient::firstOrCreate(['code' => $pt['code']], $pt);
        }
    }
}
