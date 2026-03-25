<?php

namespace Database\Seeders;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // Static data for specific patients
        $staticPatientsData = [
            'patient1@example.com' => ['birthdate' => '1990-01-01', 'gender' => 'female', 'latitude' => 30.0444, 'longitude' => 31.2357],
            'patient2@example.com' => ['birthdate' => '1985-05-12', 'gender' => 'male', 'latitude' => 30.0500, 'longitude' => 31.2330],
            'patient3@example.com' => ['birthdate' => '1992-03-22', 'gender' => 'female', 'latitude' => 30.0460, 'longitude' => 31.2300],
            'patient4@example.com' => ['birthdate' => '1988-07-15', 'gender' => 'male', 'latitude' => 30.0480, 'longitude' => 31.2400],
            'patient5@example.com' => ['birthdate' => '1995-11-30', 'gender' => 'female', 'latitude' => 30.0420, 'longitude' => 31.2380],
            'patient6@example.com' => ['birthdate' => '1991-09-05', 'gender' => 'male', 'latitude' => 30.0450, 'longitude' => 31.2360],
        ];

        // Fetch ALL users of type 'patient'
        $patientUsers = User::where('user_type', 'patient')->get();

        foreach ($patientUsers as $user) {
            // Check if profile exists
            if ($user->patientProfile) {
                continue;
            }

            $email = $user->email;
            if (isset($staticPatientsData[$email])) {
                // Use static data
                $data = $staticPatientsData[$email];
                PatientProfile::create([
                    'user_id' => $user->id,
                    'birthdate' => $data['birthdate'],
                    'gender' => $data['gender'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ]);
            } else {
                // Use Factory for unknown patients
                PatientProfile::factory()->create([
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
