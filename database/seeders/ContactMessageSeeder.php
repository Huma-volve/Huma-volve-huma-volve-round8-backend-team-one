<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'message' => 'Hello, I would like to inquire about booking an appointment with a cardiologist. Can you please provide more information about available doctors?',
                'is_read' => true,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@example.com',
                'message' => 'I am having trouble logging into my account. I have tried resetting my password but still cannot access it. Please help.',
                'is_read' => true,
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'message' => 'I would like to know if you accept insurance payments. Also, what are your consultation fees for a general practitioner?',
                'is_read' => false,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'message' => 'Thank you for the excellent service! Dr. Ahmed was very professional and helpful during my appointment.',
                'is_read' => false,
            ],
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@example.com',
                'message' => 'I need to cancel my upcoming appointment scheduled for next week. How can I do that through the app?',
                'is_read' => false,
            ],
            [
                'name' => 'Lisa Wilson',
                'email' => 'lisa.wilson@example.com',
                'message' => 'Is there a way to get a prescription refill through your platform? I need to renew my medication.',
                'is_read' => false,
            ],
        ];

        foreach ($messages as $index => $message) {
            ContactMessage::create([
                'name' => $message['name'],
                'email' => $message['email'],
                'message' => $message['message'],
                'is_read' => $message['is_read'],
                'created_at' => now()->subDays(count($messages) - $index),
                'updated_at' => now()->subDays(count($messages) - $index),
            ]);
        }
    }
}
