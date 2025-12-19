<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'is_private' => true,
            'last_message_at' => now(),
        ];
    }

    /**
     * Configure the factory to automatically add participants after creation.
     */
    public function configure()
    {
        return $this->afterCreating(function (Conversation $conversation) {
            // 1. Get or Create a Doctor
            $doctor = User::where('user_type', 'doctor')->inRandomOrder()->first();
            if (!$doctor) {
                $doctor = User::factory()->state(['user_type' => 'doctor'])->create();
                DoctorProfile::factory()->create(['user_id' => $doctor->id]);
            }

            // 2. Get or Create a Patient
            $patient = User::where('user_type', 'patient')->inRandomOrder()->first();
            if (!$patient) {
                $patient = User::factory()->state(['user_type' => 'patient'])->create();
                PatientProfile::factory()->create(['user_id' => $patient->id]);
            }

            // 3. Attach Participants 
            $conversation->participants()->firstOrCreate(
                ['user_id' => $doctor->id],
                ['last_read_at' => now()]
            );

            $conversation->participants()->firstOrCreate(
                ['user_id' => $patient->id],
                ['last_read_at' => now()]
            );
        });
    }
}