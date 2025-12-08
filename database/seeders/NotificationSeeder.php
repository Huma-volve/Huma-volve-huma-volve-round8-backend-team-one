<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $notificationsData = [
            [
                'notifiable_id' => 1,
                'notifiable_type' => User::class,
                'type' => 'info',
                'data' => [
                    'title' => 'Welcome Admin',
                    'body' => 'Your admin account has been successfully created.',
                ],
                'read_at' => null,
            ],
            [
                'notifiable_id' => 2,
                'notifiable_type' => User::class,
                'type' => 'info',
                'data' => [
                    'title' => 'New Appointment',
                    'body' => 'You have a new appointment scheduled.',
                ],
                'read_at' => null,
            ],
            [
                'notifiable_id' => 3,
                'notifiable_type' => User::class,
                'type' => 'info',
                'data' => [
                    'title' => 'Profile Approved',
                    'body' => 'Your doctor profile has been approved.',
                ],
                'read_at' => null,
            ],
            [
                'notifiable_id' => 4,
                'notifiable_type' => User::class,
                'type' => 'info',
                'data' => [
                    'title' => 'Reminder',
                    'body' => 'You have an upcoming appointment tomorrow.',
                ],
                'read_at' => null,
            ],
            [
                'notifiable_id' => 5,
                'notifiable_type' => User::class,
                'type' => 'info',
                'data' => [
                    'title' => 'Payment Received',
                    'body' => 'Your payment for the last appointment has been confirmed.',
                ],
                'read_at' => null,
            ],
        ];

        foreach ($notificationsData as $data) {
            Notification::create([
                'id' => Str::uuid(),
                'notifiable_id' => $data['notifiable_id'],
                'notifiable_type' => $data['notifiable_type'],
                'type' => $data['type'],
                'data' => json_encode($data['data']),
                'read_at' => $data['read_at'],
            ]);
        }
    }
}
