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
    // public function configure()
    // {
    //     return $this->afterCreating(function (Conversation $conversation) {
    //         // Automatic participant creation removed to prevent test side effects
    //     });
    // }
}
