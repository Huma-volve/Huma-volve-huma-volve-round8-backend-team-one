<?php

namespace Database\Seeders;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        // داتا ثابتة للـ6 مرضى اللي عملناهم في UserSeeder
        $patients = [
            ['user_email' => 'patient1@example.com', 'birthdate' => '1990-01-01', 'gender' => 'female', 'latitude' => 30.0444, 'longitude' => 31.2357],
            ['user_email' => 'patient2@example.com', 'birthdate' => '1985-05-12', 'gender' => 'male', 'latitude' => 30.0500, 'longitude' => 31.2330],
            ['user_email' => 'patient3@example.com', 'birthdate' => '1992-03-22', 'gender' => 'female', 'latitude' => 30.0460, 'longitude' => 31.2300],
            ['user_email' => 'patient4@example.com', 'birthdate' => '1988-07-15', 'gender' => 'male', 'latitude' => 30.0480, 'longitude' => 31.2400],
            ['user_email' => 'patient5@example.com', 'birthdate' => '1995-11-30', 'gender' => 'female', 'latitude' => 30.0420, 'longitude' => 31.2380],
            ['user_email' => 'patient6@example.com', 'birthdate' => '1991-09-05', 'gender' => 'male', 'latitude' => 30.0450, 'longitude' => 31.2360],
        ];

        foreach ($patients as $patientData) {
            $user = User::where('email', $patientData['user_email'])->first();

            if ($user) {
                // استخدمي firstOrCreate عشان تمنعي التكرار
                PatientProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'birthdate' => $patientData['birthdate'],
                        'gender' => $patientData['gender'],
                        'latitude' => $patientData['latitude'],
                        'longitude' => $patientData['longitude'],
                    ]
                );
            }
        }
    }
}
