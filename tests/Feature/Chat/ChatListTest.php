<?php

namespace Tests\Feature\Chat;

use App\Models\ChatParticipant;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_conversations_with_correct_structure()
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create(['name' => 'Dr. House']);

        $conversation = Conversation::factory()->create();

        ChatParticipant::factory()->create(['user_id' => $user->id, 'conversation_id' => $conversation->id]);
        ChatParticipant::factory()->create(['user_id' => $otherUser->id, 'conversation_id' => $conversation->id]);

        Message::factory()->create([
            'conversation_id' => $conversation->id,
            'body' => 'Lupus?',
            'created_at' => now(),
        ]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/conversations');

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'is_private',
                        'other_user' => [
                            'id',
                            'name',
                            'avatar'
                        ],
                        'last_message' => [
                            'body',
                            'created_at',
                            'sender_avatar'
                        ],
                        'unread_count',
                    ]
                ]
            ]);
    }

    public function test_user_can_search_conversations_by_name()
    {
        // Arrange
        $user = User::factory()->create();

        $targetUser = User::factory()->create(['name' => 'Dr. Target']);
        $ignoredUser = User::factory()->create(['name' => 'Dr. Ignored']);

        $conv1 = Conversation::factory()->create();
        ChatParticipant::factory()->create(['user_id' => $user->id, 'conversation_id' => $conv1->id]);
        ChatParticipant::factory()->create(['user_id' => $targetUser->id, 'conversation_id' => $conv1->id]);

        $conv2 = Conversation::factory()->create();
        ChatParticipant::factory()->create(['user_id' => $user->id, 'conversation_id' => $conv2->id]);
        ChatParticipant::factory()->create(['user_id' => $ignoredUser->id, 'conversation_id' => $conv2->id]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/conversations?search=Target');

        // Assert
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.other_user.name', 'Dr. Target');
    }

    public function test_user_can_filter_favorite_conversations()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();

        ChatParticipant::factory()->create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'is_favorite' => true
        ]);

        $conv2 = Conversation::factory()->create();
        ChatParticipant::factory()->create(['user_id' => $user->id, 'conversation_id' => $conv2->id, 'is_favorite' => false]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/conversations?type=favorites');

        // Assert
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $conversation->id);
    }

    public function test_user_can_filter_unread_conversations()
    {
        // Arrange
        $user = User::factory()->create();

        $unreadConv = Conversation::factory()->create();
        ChatParticipant::factory()->create([
            'user_id' => $user->id,
            'conversation_id' => $unreadConv->id,
            'last_read_at' => now()->subDay()
        ]);
        Message::factory()->create(['conversation_id' => $unreadConv->id, 'created_at' => now()]);

        $readConv = Conversation::factory()->create();
        ChatParticipant::factory()->create([
            'user_id' => $user->id,
            'conversation_id' => $readConv->id,
            'last_read_at' => now()->addHour()
        ]);
        Message::factory()->create(['conversation_id' => $readConv->id, 'created_at' => now()]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/conversations?type=unread');

        // Assert
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $unreadConv->id);
    }

    public function test_user_can_toggle_favorite_status()
    {
        // Arrange
        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $participant = ChatParticipant::factory()->create([
            'user_id' => $user->id,
            'conversation_id' => $conversation->id,
            'is_favorite' => false
        ]);

        // Act
        $this->actingAs($user)->patchJson("/api/conversations/{$conversation->id}/favorite")->assertNoContent();

        // Assert
        $this->assertDatabaseHas('chat_participants', [
            'id' => $participant->id,
            'is_favorite' => true
        ]);

        // Act
        $this->actingAs($user)->patchJson("/api/conversations/{$conversation->id}/favorite")->assertNoContent();

        // Assert
        $this->assertDatabaseHas('chat_participants', [
            'id' => $participant->id,
            'is_favorite' => false
        ]);
    }

    public function test_user_cannot_access_other_users_conversation()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $outsider = User::factory()->create();

        // create conversation between user1 and user2
        $conversation = Conversation::create();
        ChatParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $user1->id]);
        ChatParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $user2->id]);

        // Act - outsider tries to access the conversation
        $response = $this->actingAs($outsider)->getJson("/api/conversations/{$conversation->id}");

        // Assert
        $response->assertStatus(403);
    }
}
    