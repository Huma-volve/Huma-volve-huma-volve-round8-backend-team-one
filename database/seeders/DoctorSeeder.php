<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use App\Models\DoctorProfile;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Static data for specific doctors
        $staticDoctorsData = [
            'doctor1@example.com' => [
                'name' => 'Dr. Ahmed Ali', // Just for reference, mapped by email
                'phone' => '01012345678',
                'specialty_index' => 0, // Index from taken specialities
                'license_number' => 'LIC-10001',
                'bio' => 'Experienced cardiologist with 10 years of practice.',
                'session_price' => 150,
                'clinic_address' => 'Clinic 1, Cairo',
                'latitude' => 30.0444,
                'longitude' => 31.2357,
                'experience_length' => 10,
            ],
            'doctor2@example.com' => [
                'name' => 'Dr. Sara Mahmoud',
                'phone' => '01123456789',
                'specialty_index' => 1,
                'license_number' => 'LIC-10002',
                'bio' => 'Dermatologist specializing in skin treatments.',
                'session_price' => 120,
                'clinic_address' => 'Clinic 2, Cairo',
                'latitude' => 30.0450,
                'longitude' => 31.2360,
                'experience_length' => 8,
            ],
            'doctor3@example.com' => [
                'name' => 'Dr. Mohamed Hassan',
                'phone' => '01234567890',
                'specialty_index' => 2,
                'license_number' => 'LIC-10003',
                'bio' => 'General surgeon with 15 years of experience.',
                'session_price' => 200,
                'clinic_address' => 'Clinic 3, Cairo',
                'latitude' => 30.0460,
                'longitude' => 31.2370,
                'experience_length' => 15,
            ],
        ];

        // Get some specialities to assign
        $specialities = Speciality::take(3)->get();
        if ($specialities->count() == 0) {
            // Fallback if no specialities exist (though they should)
            $specialities = Speciality::factory(3)->create();
        }

        // Fetch ALL users of type 'doctor'
        $doctorUsers = User::where('user_type', 'doctor')->get();

        foreach ($doctorUsers as $user) {
            // Check if profile already exists to avoid duplication errors if run multiple times without fresh
            /* @var \App\Models\DoctorProfile $profile */
            if ($user->doctorProfile) {
                $profile = $user->doctorProfile;
            } else {
                // Determine data source
                $email = $user->email;
                if (isset($staticDoctorsData[$email])) {
                    // Use static data
                    $data = $staticDoctorsData[$email];
                    $specialtyId = $specialities[$data['specialty_index']]->id ?? $specialities->first()->id;

                    $profile = DoctorProfile::create([
                        'user_id' => $user->id,
                        'specialty_id' => $specialtyId,
                        'license_number' => $data['license_number'],
                        'bio' => $data['bio'],
                        'session_price' => $data['session_price'],
                        'clinic_address' => $data['clinic_address'],
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'experience_length' => $data['experience_length'],
                    ]);
                } else {
                    // Use Factory for unknown doctors (e.g. Doctor 4, 5 from UserSeeder)
                    // We simply call the factory state for this user_id
                    $profile = DoctorProfile::factory()->create([
                        'user_id' => $user->id,
                        'specialty_id' => $specialities->random()->id,
                    ]);
                }
            }

            // Ensure Schedules exist
            // Days: 0=Sunday, 1=Monday ...
            $days = [0, 1, 2, 3, 4]; // Sunday to Thursday
            foreach ($days as $day) {
                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_profile_id' => $profile->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time' => '09:00:00',
                        'end_time' => '17:00:00',
                        'avg_consultation_time' => 30,
                    ]
                );
            }
        }
    }
}
