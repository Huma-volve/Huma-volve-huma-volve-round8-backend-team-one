<?php

namespace Tests\Feature\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SendMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_send_text_message()
    {
        $this->withoutExceptionHandling();
        // Arrange
        Event::fake();

        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->participants()->create(['user_id' => $user->id]);

        // Act
        $response = $this->actingAs($user)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'body' => 'Hello World',
            ]);

        // Assert
        $response->assertCreated()
            ->assertJson([
                'data' => [
                    'body' => 'Hello World',
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                ]
            ]);

        $this->assertDatabaseHas('messages', [
            'body' => 'Hello World',
            'conversation_id' => $conversation->id,
        ]);

        Event::assertDispatched(MessageSent::class);
    }
}
