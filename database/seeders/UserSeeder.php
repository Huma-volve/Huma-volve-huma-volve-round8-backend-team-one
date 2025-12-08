<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admin
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'user_type' => 'admin',
                'phone' => '01000000001',
                'address' => '123 Admin Street, City',
            ],
            // Doctors
            [
                'name' => 'Doctor 1',
                'email' => 'doctor1@example.com',
                'user_type' => 'doctor',
                'phone' => '01000000002',
                'address' => 'Medical Center 1, City',
            ],
            [
                'name' => 'Doctor 2',
                'email' => 'doctor2@example.com',
                'user_type' => 'doctor',
                'phone' => '01000000003',
                'address' => 'Medical Center 2, City',
            ],
            [
                'name' => 'Doctor 3',
                'email' => 'doctor3@example.com',
                'user_type' => 'doctor',
                'phone' => '01000000004',
                'address' => 'Medical Center 3, City',
            ],
            [
                'name' => 'Doctor 4',
                'email' => 'doctor4@example.com',
                'user_type' => 'doctor',
                'phone' => '01000000005',
                'address' => 'Medical Center 4, City',
            ],
            [
                'name' => 'Doctor 5',
                'email' => 'doctor5@example.com',
                'user_type' => 'doctor',
                'phone' => '01000000006',
                'address' => 'Medical Center 5, City',
            ],
            // Patients
            [
                'name' => 'Patient 1',
                'email' => 'patient1@example.com',
                'user_type' => 'patient',
                'phone' => '01000000007',
                'address' => 'Patient Avenue 1, City',
            ],
            [
                'name' => 'Patient 2',
                'email' => 'patient2@example.com',
                'user_type' => 'patient',
                'phone' => '01000000008',
                'address' => 'Patient Avenue 2, City',
            ],
            [
                'name' => 'Patient 3',
                'email' => 'patient3@example.com',
                'user_type' => 'patient',
                'phone' => '01000000009',
                'address' => 'Patient Avenue 3, City',
            ],
            [
                'name' => 'Patient 4',
                'email' => 'patient4@example.com',
                'user_type' => 'patient',
                'phone' => '01000000010',
                'address' => 'Patient Avenue 4, City',
            ],
            [
                'name' => 'Patient 5',
                'email' => 'patient5@example.com',
                'user_type' => 'patient',
                'phone' => '01000000011',
                'address' => 'Patient Avenue 5, City',
            ],
            [
                'name' => 'Patient 6',
                'email' => 'patient6@example.com',
                'user_type' => 'patient',
                'phone' => '01000000012',
                'address' => 'Patient Avenue 6, City',
            ],
        ];

        foreach ($users as $user) {
    User::firstOrCreate(
        ['email' => $user['email']], // تشيك على الإيميل
        [
            'name' => $user['name'],
            'password' => Hash::make('password'),
            'user_type' => $user['user_type'],
            'phone' => $user['phone'],
            'address' => $user['address'],
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]
    );
}

    }
}
