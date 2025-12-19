<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\PatientProfile;
use App\Models\DoctorProfile;

class PatientChatSeeder extends Seeder
{
    public function run(): void
    {
        // ---------------------------------------------------------
        // 1. Setup Demo Patient (Fixed Credentials for Frontend)
        // ---------------------------------------------------------
        $patientEmail = 'demo_patient@app.com';
        $patient = User::firstOrCreate(
            ['email' => $patientEmail],
            [
                'name' => 'Demo Patient',
                'password' => bcrypt('password'), // 12345678
                'user_type' => 'patient',
                'phone' => '01099999999',
                'email_verified_at' => now(),
            ]
        );
        // Ensure profile exists
        PatientProfile::firstOrCreate(['user_id' => $patient->id]);

        // ---------------------------------------------------------
        // 2. Ensure We Have Doctors
        // ---------------------------------------------------------
        if (User::where('user_type', 'doctor')->count() < 5) {
            User::factory(5)->state(['user_type' => 'doctor'])->create()->each(function ($u) {
                DoctorProfile::factory()->create(['user_id' => $u->id]);
            });
        }
        $doctors = User::where('user_type', 'doctor')->take(5)->get();

        // ---------------------------------------------------------
        // 3. Scenario A: Active Conversation (Text Only)
        // ---------------------------------------------------------
        $this->createConversationScenario(
            $patient,
            $doctors[0],
            messageCount: 15,
            type: 'text'
        );

        // ---------------------------------------------------------
        // 4. Scenario B: Multimedia Conversation (Images & Files)
        // ---------------------------------------------------------
        $this->createConversationScenario(
            $patient,
            $doctors[1],
            messageCount: 8,
            type: 'mixed'
        );

        // ---------------------------------------------------------
        // 5. Scenario C: Unread Messages (Test Notification Badge)
        // ---------------------------------------------------------
        $unreadConv = $this->createConversationScenario(
            $patient,
            $doctors[2],
            messageCount: 5,
            type: 'text'
        );
        
        // Force the last message to be unread and from the doctor
        $lastMsg = Message::create([
            'conversation_id' => $unreadConv->id,
            'sender_id' => $doctors[2]->id,
            'type' => 'text',
            'body' => 'Hello? Are you there? This is an unread message.',
            'created_at' => now(),
            'read_at' => null // Explicitly unread
        ]);
        
        // Update conversation timestamp
        $unreadConv->update(['last_message_at' => $lastMsg->created_at]);
        
        // Set patient's last read time to be older than the new message
        $unreadConv->participants()->where('user_id', $patient->id)->update([
            'last_read_at' => now()->subHours(1)
        ]);

        // ---------------------------------------------------------
        // 6. Scenario D: Empty/New Conversation
        // ---------------------------------------------------------
        $emptyConv = Conversation::factory()->create();
        // Reset participants to be our specific users
        $emptyConv->participants()->delete();
        $emptyConv->participants()->createMany([
            ['user_id' => $patient->id, 'last_read_at' => now()],
            ['user_id' => $doctors[3]->id, 'last_read_at' => now()],
        ]);
    }

    /**
     * Helper to create a conversation with messages between two users.
     */
    private function createConversationScenario(User $patient, User $doctor, int $messageCount, string $type)
    {
        // Create Conversation
        $conversation = Conversation::factory()->create();

        // Fix Participants (Delete auto-generated ones from Factory hook to be safe, then add ours)
        $conversation->participants()->delete();
        $conversation->participants()->createMany([
            ['user_id' => $patient->id, 'last_read_at' => now()],
            ['user_id' => $doctor->id, 'last_read_at' => now()],
        ]);

        // Create Messages
        for ($i = 0; $i < $messageCount; $i++) {
            // Alternate sender
            $sender = ($i % 2 === 0) ? $patient : $doctor;
            
            // Determine Message Type
            $msgFactory = Message::factory();
            if ($type === 'mixed') {
                 if ($i % 3 === 0) $msgFactory = $msgFactory->image();
                 elseif ($i % 4 === 0) $msgFactory = $msgFactory->file();
            }

            $msgFactory->create([
                'conversation_id' => $conversation->id,
                'sender_id' => $sender->id,
                'created_at' => now()->subMinutes(($messageCount - $i) * 10), // Chronological order
            ]);
        }

        // Update last_message_at
        $lastMsg = $conversation->messages()->latest()->first();
        if ($lastMsg) {
            $conversation->update(['last_message_at' => $lastMsg->created_at]);
        }

        return $conversation;
    }
}