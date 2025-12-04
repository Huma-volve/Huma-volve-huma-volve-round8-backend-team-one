<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'sender_id' => User::factory(),
            'body' => $this->faker->sentence(),
            'type' => 'text',
        ];
    }

    public function image()
    {
        return $this->state(fn(array $attributes) => [
            'type' => 'image',
            'body' => 'https://via.placeholder.com/640x480.png'
        ]);
    }
}
