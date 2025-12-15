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
        $notifications = [
            1 => [ // Admin
                [
                    'type' => 'Welcome Admin',
                    'message'  => 'Your admin account has been successfully created.',
                    'read'  => true,
                ],
                [
                    'type' => 'System Update',
                    'message'  => 'The system will undergo maintenance tonight.',
                    'read'  => true,
                ],
                [
                    'type' => 'New User Registered',
                    'message'  => 'A new user has registered and needs approval.',
                    'read'  => false,
                ],
                [
                    'type' => 'Policy Change',
                    'message'  => 'There is an update in the company policy.',
                    'read'  => false,
                ],
                [
                    'type' => 'Reminder',
                    'message'  => 'Don’t forget to review pending tasks.',
                    'read'  => false,
                ],
            ],
            2 => [ // Doctor
                [
                    'type' => 'New Appointment',
                    'message'  => 'You have a new appointment scheduled.',
                    'read'  => true,
                ],
                [
                    'type' => 'Patient Review',
                    'message'  => 'You received a new review from a patient.',
                    'read'  => true,
                ],
                [
                    'type' => 'Profile Approved',
                    'message'  => 'Your doctor profile has been approved.',
                    'read'  => false,
                ],
                [
                    'type' => 'Reminder',
                    'message'  => 'You have an upcoming appointment tomorrow.',
                    'read'  => false,
                ],
                [
                    'type' => 'Payment Received',
                    'message'  => 'Your payment for the last appointment has been confirmed.',
                    'read'  => false,
                ],
            ],
            7 => [ // Patient
                [
                    'type' => 'Appointment Confirmed',
                    'message'  => 'Your appointment with Dr. Ahmed is confirmed.',
                    'read'  => true,
                ],
                [
                    'type' => 'New Prescription',
                    'message'  => 'Your doctor has added a new prescription.',
                    'read'  => true,
                ],
                [
                    'type' => 'Test Results',
                    'message'  => 'Your recent test results are available.',
                    'read'  => false,
                ],
                [
                    'type' => 'Reminder',
                    'message'  => 'Your next appointment is tomorrow at 10 AM.',
                    'read'  => false,
                ],
                [
                    'type' => 'Feedback Request',
                    'message'  => 'Please provide feedback for your last visit.',
                    'read'  => false,
                ],
            ],
        ];

        foreach ($notifications as $userId => $userNotifications) {
            foreach ($userNotifications as $notif) {
                Notification::create([
                    'id' => Str::uuid(),
                    'notifiable_id' => $userId,
                    'notifiable_type' => User::class,
                    'type' => 'info',
                    'data' => json_encode([
                        'type' => $notif['type'],
                        'message'  => $notif['message'],
                    ]),
                    'read_at' => $notif['read'] ? now() : null,
                ]);
            }
        }
    }
}
