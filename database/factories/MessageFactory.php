<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(), 
            'sender_id' => User::factory(), 
            'type' => 'text',
            'body' => $this->faker->realText(rand(30, 100)),
            'read_at' => now(),
            'created_at' => now(),
        ];
    }

    public function image()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'image',
            'body' => 'https://placehold.co/600x400/png', 
        ]);
    }

    public function file()
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'file',
            'body' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
        ]);
    }
}