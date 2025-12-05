<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
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
            'status' => 1,
            'phone' => '+1234567890',
            'address' => '123 Admin Street, City',
            'email_verified_at' => now(),
            'phone_verified_at' => now(), // العمود موجود في الميرجيشن
        ]);

        // Doctor User
        User::create([
            'name' => 'Dr. John Smith',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
            'user_type' => 'doctor',
            'status' => 1,
            'phone' => '+1234567891',
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
            'status' => 1,
            'phone' => '+1234567892',
            'address' => '789 Patient Avenue, City',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
    }
}
