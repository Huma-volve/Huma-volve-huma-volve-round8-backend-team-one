<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    // ✅ Function تولد رقم مصري
    private function generateEgyptianPhone()
    {
        $prefixes = ['010', '011', '012', '015'];
        return $prefixes[array_rand($prefixes)] . rand(10000000, 99999999);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'phone' => $this->generateEgyptianPhone(), // تم التعديل هنا
            'address' => '123 Admin Street, City',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // Doctor User
        User::create([
            'name' => 'Dr. John Smith',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'doctor',
            'phone' => $this->generateEgyptianPhone(), // تم التعديل هنا
            'address' => '456 Medical Center, City',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        // Patient User
        User::create([
            'name' => 'Jane Doe',
            'email' => 'patient@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'patient',
            'phone' => $this->generateEgyptianPhone(), // تم التعديل هنا
            'address' => '789 Patient Avenue, City',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
    }
}
