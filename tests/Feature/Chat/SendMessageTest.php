<?php

namespace Tests\Feature\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\ChatParticipant;

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
                    'type' => 'text',
                ],
            ]);

        $this->assertDatabaseHas('messages', [
            'body' => 'Hello World',
            'conversation_id' => $conversation->id,
        ]);

        Event::assertDispatched(MessageSent::class);
    }

    public function test_user_can_send_image_message()
    {
        $this->withoutExceptionHandling();

        // Arrange
        Event::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->participants()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->image('test-image.jpg');

        // Act
        $response = $this->actingAs($user)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'attachment' => $file,
            ]);

        // Assert
        $response->assertCreated();

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'type' => 'image',
        ]);

        $message = Message::where('conversation_id', $conversation->id)->latest()->first();
        Storage::disk('public')->assertExists($message->body);

        $response->assertJson([
            'data' => [
                'type' => 'image',
                'body' => Storage::url($message->body),
            ],
        ]);

        Event::assertDispatched(MessageSent::class);
    }

    public function test_user_can_send_video_message()
    {
        $this->withoutExceptionHandling();

        // Arrange
        Event::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->participants()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('test-video.mp4', 5000, 'video/mp4');

        // Act
        $response = $this->actingAs($user)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'attachment' => $file,
            ]);

        // Assert
        $response->assertCreated();

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'type' => 'video',
        ]);

        $message = Message::where('conversation_id', $conversation->id)->latest()->first();
        Storage::disk('public')->assertExists($message->body);

        $response->assertJson([
            'data' => [
                'type' => 'video',
                'body' => Storage::url($message->body),
            ],
        ]);

        Event::assertDispatched(MessageSent::class);
    }

    public function test_user_can_send_audio_message()
    {
        $this->withoutExceptionHandling();

        // Arrange
        Event::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $conversation->participants()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->create('voice-message.mp3', 1000, 'audio/mpeg');

        // Act
        $response = $this->actingAs($user)
            ->postJson("/api/conversations/{$conversation->id}/messages", [
                'attachment' => $file,
            ]);

        // Assert
        $response->assertCreated();

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'type' => 'audio',
        ]);

        $message = Message::where('conversation_id', $conversation->id)->latest()->first();
        Storage::disk('public')->assertExists($message->body);

        $response->assertJson([
            'data' => [
                'type' => 'audio',
                'body' => Storage::url($message->body),
            ],
        ]);

        Event::assertDispatched(MessageSent::class);
    }

    public function test_cannot_send_empty_message()
    {
        // Arrange
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $conversation = Conversation::create();
        ChatParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $user1->id]);
        ChatParticipant::create(['conversation_id' => $conversation->id, 'user_id' => $user2->id]);

        // Act try to send empty message
        $response = $this->actingAs($user1)->postJson("/api/conversations/{$conversation->id}/messages", []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }
}
