<?php

namespace Database\Seeders;

use App\Models\ChatParticipant;
use App\Models\Conversation;
use App\Models\DoctorProfile;
use App\Models\Message;
use App\Models\PatientProfile;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = DoctorProfile::with('user')->get();
        $patients = PatientProfile::with('user')->get();

        if ($doctors->isEmpty() || $patients->isEmpty()) {
            return;
        }

        $messageTypes = ['text', 'text', 'text', 'image', 'video', 'audio', 'file'];
        $sampleMessages = [
            'Hello, how can I help you today?',
            'I have been experiencing some symptoms lately.',
            'Can you describe your symptoms in more detail?',
            'I would like to schedule a follow-up appointment.',
            'Your test results look good.',
            'Thank you for your help, doctor.',
            'Please remember to take your medication.',
            'When should I come back for a check-up?',
            'Let me know if you have any questions.',
            'I feel much better now, thank you.',
        ];

        // Create 15 conversations
        foreach (range(1, min(15, $doctors->count() * $patients->count())) as $index) {
            $doctorIndex = ($index - 1) % $doctors->count();
            $patientIndex = ($index - 1) % $patients->count();

            $doctor = $doctors[$doctorIndex];
            $patient = $patients[$patientIndex];

            $conversation = Conversation::create([
                'last_message_at' => now(),
            ]);

            // Add participants
            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $doctor->user_id,
                'last_read_at' => $index % 3 === 0 ? null : now()->subHours($index),
                'is_archived' => false,
                'is_favorite' => $index % 5 === 0,
            ]);

            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $patient->user_id,
                'last_read_at' => $index % 4 === 0 ? null : now()->subHours($index + 1),
                'is_archived' => false,
                'is_favorite' => $index % 7 === 0,
            ]);

            // Create 5-10 messages per conversation
            $messageCount = 5 + ($index % 6);
            foreach (range(1, $messageCount) as $msgIndex) {
                $isFromDoctor = $msgIndex % 2 === 0;
                $sender = $isFromDoctor ? $doctor->user_id : $patient->user_id;

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $sender,
                    'type' => $messageTypes[($index + $msgIndex) % count($messageTypes)],
                    'body' => $sampleMessages[($index + $msgIndex) % count($sampleMessages)],
                    'read_at' => $msgIndex % 3 === 0 ? null : now()->subMinutes($msgIndex * 10),
                ]);
            }

            // Update last message time
            $lastMessage = Message::where('conversation_id', $conversation->id)
                ->latest()
                ->first();

            if ($lastMessage) {
                $conversation->update([
                    'last_message_at' => $lastMessage->created_at,
                ]);
            }
        }
    }
}
