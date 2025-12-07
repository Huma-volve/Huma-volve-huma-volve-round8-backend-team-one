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

    public function test_marking_conversation_as_read_updates_unread_count()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conversation = Conversation::create();
        ChatParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $user1->id, 'last_read_at' => now()->subHour()]);
        ChatParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $user2->id]);

        // create unread message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user2->id,
            'body' => 'New message',
            'type' => 'text',
        ]);

        // Act mark as read
        $response = $this->actingAs($user1)->postJson("/api/conversations/{$conversation->id}/mark-read");

        // Assert
        $response->assertStatus(204);

        $participant = ChatParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user1->id)
            ->first();

        $this->assertNotNull($participant->last_read_at);
        $this->assertTrue($participant->last_read_at->isAfter(now()->subMinute()));
    }

    public function test_user_can_toggle_archive_status()
    {
        // Arrange
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $conversation = Conversation::create();
        $participant = ChatParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'is_archived' => false
        ]);
        ChatParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $otherUser->id]);

        // Act archive
        $response = $this->actingAs($user)->patchJson("/api/conversations/{$conversation->id}/archive");

        // Assert
        $response->assertNoContent();
        $this->assertDatabaseHas('chat_participants', [
            'id' => $participant->id,
            'is_archived' => true,
        ]);

        // Act unarchive
        $response = $this->actingAs($user)->patchJson("/api/conversations/{$conversation->id}/archive");

        // Assert
        $response->assertNoContent();
        $this->assertDatabaseHas('chat_participants', [
            'id' => $participant->id,
            'is_archived' => false,
        ]);
    }

    public function test_user_can_filter_archived_conversations()
    {
        // Arrange
        $user = User::factory()->create();
        $doctor1 = User::factory()->create(['name' => 'Dr. Archived']);
        $doctor2 = User::factory()->create(['name' => 'Dr. Active']);

        // archived conversation
        $archivedConv = Conversation::create();
        ChatParticipant::create([
            'conversation_id' => $archivedConv->id,
            'user_id' => $user->id,
            'is_archived' => true
        ]);
        ChatParticipant::create(['conversation_id' => $archivedConv->id, 'user_id' => $doctor1->id]);

        // active conversation
        $activeConv = Conversation::create();
        ChatParticipant::create([
            'conversation_id' => $activeConv->id,
            'user_id' => $user->id,
            'is_archived' => false
        ]);
        ChatParticipant::create(['conversation_id' => $activeConv->id, 'user_id' => $doctor2->id]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/conversations?type=archived');

        // Assert
        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $archivedConv->id);
    }

    public function test_unread_count_only_includes_other_user_messages()
    {
        // Arrange
        $user = User::factory()->create();
        $doctor = User::factory()->create();

        $conversation = Conversation::create();
        ChatParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'last_read_at' => now()->subMinutes(10)
        ]);
        ChatParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $doctor->id]);

        // create 2 messages from doctor 
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $doctor->id,
            'body' => 'Message 1',
            'type' => 'text',
        ]);
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $doctor->id,
            'body' => 'Message 2',
            'type' => 'text',
        ]);

        // Create 1 message from user 
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'body' => 'My message',
            'type' => 'text',
        ]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/conversations');

        // Assert
        $response->assertOk()
            ->assertJsonPath('data.0.unread_count', 2);
    }

    public function test_conversations_ordered_by_most_recent()
    {
        // Arrange
        $user = User::factory()->create();
        $doctor1 = User::factory()->create();
        $doctor2 = User::factory()->create();

        // old conversation
        $oldConv = Conversation::create();
        $oldConv->update(['updated_at' => now()->subHours(2)]);
        ChatParticipant::create(['conversation_id' => $oldConv->id, 'user_id' => $user->id]);
        ChatParticipant::create(['conversation_id' => $oldConv->id, 'user_id' => $doctor1->id]);

        sleep(1);

        // recent conversation
        $recentConv = Conversation::create();
        ChatParticipant::create(['conversation_id' => $recentConv->id, 'user_id' => $user->id]);
        ChatParticipant::create(['conversation_id' => $recentConv->id, 'user_id' => $doctor2->id]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/conversations');

        // Assert
        $response->assertOk()
            ->assertJsonPath('data.0.id', $recentConv->id)
            ->assertJsonPath('data.1.id', $oldConv->id);
    }

    public function test_user_with_no_conversations_gets_empty_list()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->getJson('/api/conversations');

        // Assert
        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_search_term_is_saved_to_history()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->getJson('/api/conversations?search=Heart');

        // Assert
        $response->assertOk();

        $this->assertDatabaseHas('search_histories', [
            'user_id' => $user->id,
            'keyword' => 'Heart',
        ]);
    }
}
