<?php

namespace Database\Seeders;

use App\Models\PatientProfile;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    // ✅ Function تولد رقم مصري
    private function generateEgyptianPhone()
    {
        $prefixes = ['010', '011', '012', '015'];
        return $prefixes[array_rand($prefixes)] . rand(10000000, 99999999);
    }

    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "patient{$index}@example.com",
                'password' => Hash::make('password'),
                'user_type' => 'patient',
                'phone' => $this->generateEgyptianPhone(), // تم التعديل هنا
                'address' => $faker->address,
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]);

            PatientProfile::create([
                'user_id' => $user->id,
                'birthdate' => $faker->date('Y-m-d', '-18 years'),
                'gender' => $faker->randomElement(['male', 'female']),
                'latitude' => $faker->latitude,
                'longitude' => $faker->longitude,
            ]);
        }
    }
}
