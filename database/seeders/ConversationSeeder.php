<?php

namespace Database\Seeders;

use App\Models\ChatParticipant;
use App\Models\Conversation;
use App\Models\DoctorProfile;
use App\Models\Message;
use App\Models\PatientProfile;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $doctors = DoctorProfile::with('user')->get();
        $patients = PatientProfile::with('user')->get();

        // Create 15 conversations
        foreach (range(1, 15) as $index) {
            $doctor = $doctors->random();
            $patient = $patients->random();

            $conversation = Conversation::create([
                'last_message_at' => now(),
            ]);

            // Add participants
            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $doctor->user_id,
                'last_read_at' => $faker->optional(70)->dateTimeBetween('-2 days', 'now'),
                'is_archived' => false,
                'is_favorite' => $faker->boolean(20),
            ]);

            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $patient->user_id,
                'last_read_at' => $faker->optional(70)->dateTimeBetween('-2 days', 'now'),
                'is_archived' => false,
                'is_favorite' => $faker->boolean(20),
            ]);

            // Create 5-15 messages per conversation
            $messageCount = $faker->numberBetween(5, 15);
            foreach (range(1, $messageCount) as $msgIndex) {
                $sender = $faker->boolean(50) ? $doctor->user_id : $patient->user_id;

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $sender,
                    'type' => $faker->randomElement(['text', 'text', 'text', 'image', 'video']), // More text messages
                    'body' => $faker->sentence,
                    'read_at' => $faker->optional(60)->dateTimeBetween('-1 day', 'now'),
                ]);
            }

            // Update last message time
            $conversation->update([
                'last_message_at' => Message::where('conversation_id', $conversation->id)
                    ->latest()
                    ->first()
                    ->created_at ?? now(),
            ]);
        }
    }
}
