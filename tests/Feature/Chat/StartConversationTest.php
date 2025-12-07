<?php

namespace Tests\Feature\Chat;

use App\Models\ChatParticipant;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StartConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_new_conversation()
    {
        // Arrange
        $patient = User::factory()->create();
        $doctor = User::factory()->create();

        // Act
        $response = $this->actingAs($patient)->postJson('/api/conversations/start', [
            'doctor_id' => $doctor->id
        ]);

        // Assert
        $response->assertSuccessful() 
            ->assertJsonStructure([
                'data' => ['id', 'is_private', 'other_user']
            ]);

        $this->assertDatabaseHas('conversations', [
            'id' => $response->json('data.id')
        ]);

        $conversationId = $response->json('data.id');
        $this->assertDatabaseHas('chat_participants', [
            'conversation_id' => $conversationId,
            'user_id' => $patient->id
        ]);
        $this->assertDatabaseHas('chat_participants', [
            'conversation_id' => $conversationId,
            'user_id' => $doctor->id
        ]);
    }

    public function test_returns_existing_conversation_if_already_exists()
    {
        // Arrange
        $patient = User::factory()->create();
        $doctor = User::factory()->create();

        $existingConversation = Conversation::create();
        ChatParticipant::create([
            'conversation_id' => $existingConversation->id,
            'user_id' => $patient->id
        ]);
        ChatParticipant::create([
            'conversation_id' => $existingConversation->id,
            'user_id' => $doctor->id
        ]);

        // Act
        $response = $this->actingAs($patient)->postJson('/api/conversations/start', [
            'doctor_id' => $doctor->id
        ]);

        // Assert
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $existingConversation->id
                ]
            ]);

        $this->assertDatabaseCount('conversations', 1);
    }

    public function test_fails_with_invalid_doctor_id()
    {
        // Arrange
        $patient = User::factory()->create();

        // Act
        $response = $this->actingAs($patient)->postJson('/api/conversations/start', [
            'doctor_id' => 99999
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['doctor_id']);
    }
}
