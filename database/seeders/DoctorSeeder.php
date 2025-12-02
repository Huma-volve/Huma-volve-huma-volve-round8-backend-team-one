<?php

namespace Database\Seeders;

use App\Models\AvailabilitySlot;
use App\Models\DoctorProfile;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $specialities = Speciality::all();

        foreach (range(1, 10) as $index) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "doctor{$index}@example.com",
                'password' => Hash::make('password'),
                'user_type' => 'doctor',
                'status' => 1,
                'mobile' => $faker->phoneNumber,
                'email_verified_at' => now(),
            ]);

            $profile = DoctorProfile::create([
                'user_id' => $user->id,
                'specialty_id' => $specialities->random()->id,
                'license_number' => $faker->unique()->bothify('LIC-#####'),
                'bio' => $faker->paragraph,
                'session_price' => $faker->randomFloat(2, 50, 300),
                'clinic_address' => $faker->address,
                'latitude' => $faker->latitude,
                'longitude' => $faker->longitude,
                'rating_avg' => $faker->randomFloat(2, 3, 5),
                'total_reviews' => $faker->numberBetween(0, 100),
                'is_approved' => true,
                'experience_length' => $faker->numberBetween(1, 30),
            ]);

            // Create availability slots for the next 7 days
            for ($i = 0; $i < 7; $i++) {
                $date = now()->addDays($i)->format('Y-m-d');
                AvailabilitySlot::create([
                    'doctor_profile_id' => $profile->id,
                    'date' => $date,
                    'start_time' => '09:00:00',
                    'end_time' => '10:00:00',
                    'is_active' => true,
                    'is_booked' => false,
                ]);
                AvailabilitySlot::create([
                    'doctor_profile_id' => $profile->id,
                    'date' => $date,
                    'start_time' => '10:00:00',
                    'end_time' => '11:00:00',
                    'is_active' => true,
                    'is_booked' => false,
                ]);
                AvailabilitySlot::create([
                    'doctor_profile_id' => $profile->id,
                    'date' => $date,
                    'start_time' => '14:00:00',
                    'end_time' => '15:00:00',
                    'is_active' => true,
                    'is_booked' => false,
                ]);
            }
        }
    }
}
