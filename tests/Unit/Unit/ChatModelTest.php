<?php

namespace Tests\Unit;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_conversations()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->participants()->create(['user_id' => $user->id]);

        // Act
        $userConversations = $user->conversations()->get();

        // Assert
        $this->assertTrue($userConversations->contains($conversation));
    }

    public function test_conversation_has_messages()
    {
        // Arrange
        $conversation = Conversation::factory()->create();
        $message = Message::factory()->create(['conversation_id' => $conversation->id]);

        // Act
        $conversationMessages = $conversation->messages;

        // Assert
        $this->assertTrue($conversationMessages->contains($message));
    }

    public function test_messages_are_deleted_when_conversation_is_deleted()
    {
        // Arrange
        $conversation = Conversation::factory()->create();
        Message::factory()->count(3)->create(['conversation_id' => $conversation->id]);
        $this->assertDatabaseCount('messages', 3);

        // Act
        $conversation->delete();

        // Assert
        $this->assertDatabaseCount('messages', 0);
    }
}
