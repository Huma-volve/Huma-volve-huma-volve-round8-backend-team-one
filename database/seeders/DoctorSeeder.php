<?php

namespace Database\Seeders;

use App\Models\AvailabilitySlot;
use App\Models\DoctorProfile;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // اختاري 3 تخصصات ثابتة
        $specialities = Speciality::take(3)->get();

        $doctorsData = [
            [
                'name' => 'Dr. Ahmed Ali',
                'email' => 'doctor1@example.com',
                'phone' => '01012345678',
                'specialty_id' => $specialities[0]->id,
                'license_number' => 'LIC-10001',
                'bio' => 'Experienced cardiologist with 10 years of practice.',
                'session_price' => 150,
                'clinic_address' => 'Clinic 1, Cairo',
                'latitude' => 30.0444,
                'longitude' => 31.2357,
                'rating_avg' => 4.5,
                'total_reviews' => 25,
                'experience_length' => 10,
            ],
            [
                'name' => 'Dr. Sara Mahmoud',
                'email' => 'doctor2@example.com',
                'phone' => '01123456789',
                'specialty_id' => $specialities[1]->id,
                'license_number' => 'LIC-10002',
                'bio' => 'Dermatologist specializing in skin treatments.',
                'session_price' => 120,
                'clinic_address' => 'Clinic 2, Cairo',
                'latitude' => 30.0450,
                'longitude' => 31.2360,
                'rating_avg' => 4.7,
                'total_reviews' => 30,
                'experience_length' => 8,
            ],
            [
                'name' => 'Dr. Mohamed Hassan',
                'email' => 'doctor3@example.com',
                'phone' => '01234567890',
                'specialty_id' => $specialities[2]->id,
                'license_number' => 'LIC-10003',
                'bio' => 'General surgeon with 15 years of experience.',
                'session_price' => 200,
                'clinic_address' => 'Clinic 3, Cairo',
                'latitude' => 30.0460,
                'longitude' => 31.2370,
                'rating_avg' => 4.8,
                'total_reviews' => 40,
                'experience_length' => 15,
            ],
        ];

        foreach ($doctorsData as $data) {
            // استخدمي firstOrCreate عشان تمنعي التكرار
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'user_type' => 'doctor',
                    'phone' => $data['phone'],
                    'email_verified_at' => now(),
                ]
            );

            $profile = DoctorProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty_id' => $data['specialty_id'],
                    'license_number' => $data['license_number'],
                    'bio' => $data['bio'],
                    'session_price' => $data['session_price'],
                    'clinic_address' => $data['clinic_address'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'rating_avg' => $data['rating_avg'],
                    'total_reviews' => $data['total_reviews'],
                    'is_approved' => true,
                    'experience_length' => $data['experience_length'],
                ]
            );

            // Slots ثابتة لكل دكتور، استخدمي firstOrCreate عشان تمنعي التكرار
            $slots = [
                ['date' => '2025-12-08', 'start_time' => '09:00:00', 'end_time' => '10:00:00'],
                ['date' => '2025-12-08', 'start_time' => '10:00:00', 'end_time' => '11:00:00'],
                ['date' => '2025-12-08', 'start_time' => '14:00:00', 'end_time' => '15:00:00'],
            ];

            foreach ($slots as $slot) {
                AvailabilitySlot::firstOrCreate(
                    [
                        'doctor_profile_id' => $profile->id,
                        'date' => $slot['date'],
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                    ],
                    [
                        'is_active' => true,
                        'is_booked' => false,
                    ]
                );
            }
        }
    }
}
