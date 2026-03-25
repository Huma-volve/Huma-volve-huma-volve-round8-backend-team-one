<?php

namespace Database\Seeders;

use App\Models\ChatParticipant;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorChatSeeder extends Seeder
{
    public function run(): void
    {
        $doctor = User::firstOrCreate(
            ['email' => 'doctor@test.com'],
            [
                'name' => 'Dr. Test',
                'password' => bcrypt('password'),
                'user_type' => 'doctor',
                'status' => 1,
            ]
        );

        $patients = User::factory(3)->create(['user_type' => 'patient']);

        foreach ($patients as $patient) {
            $conversation = Conversation::create(['updated_at' => now()]);

            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $doctor->id,
            ]);

            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $patient->id,
            ]);

            Message::factory(5)->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $patient->id,
                'type' => 'text',
            ]);

            Message::factory(3)->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $doctor->id,
                'type' => 'text',
            ]);
        }
    }
}