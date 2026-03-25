<?php

namespace Tests\Feature\Doctor;

use App\Models\ChatParticipant;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $doctor;
    private User $patient;
    private Conversation $conversation;
    private ChatParticipant $doctorParticipant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->doctor = User::factory()->create(['user_type' => 'doctor']);
        $this->patient = User::factory()->create(['user_type' => 'patient']);

        $this->conversation = Conversation::create(['updated_at' => now()]);

        $this->doctorParticipant = ChatParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->doctor->id,
            'is_favorite' => false,
            'is_archived' => false,
        ]);

        ChatParticipant::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->patient->id,
        ]);
    }

    public function test_guest_cannot_access_chat_index(): void
    {
        // Act
        $response = $this->get(route('doctor.chat.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    public function test_doctor_can_view_chat_index(): void
    {
        // Act
        $response = $this->actingAs($this->doctor)->get(route('doctor.chat.index'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('doctor.chat.index');
        $response->assertViewHas('conversations');
    }

    public function test_doctor_can_get_conversation_messages(): void
    {
        // Arrange
        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->patient->id,
            'body' => 'Hello Doctor',
            'type' => 'text',
        ]);

        // Act
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.chat.messages', $this->conversation));

        // Assert
        $response->assertOk();
        $response->assertJsonStructure(['messages']);
        $response->assertJsonCount(1, 'messages');
    }

    public function test_doctor_cannot_access_other_conversation(): void
    {
        // Arrange
        $otherConversation = Conversation::create(['updated_at' => now()]);

        // Act
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.chat.messages', $otherConversation));

        // Assert
        $response->assertForbidden();
    }

    public function test_doctor_can_send_text_message(): void
    {
        // Arrange
        $messageBody = 'Hello Patient';

        // Act
        $response = $this->actingAs($this->doctor)
            ->postJson(route('doctor.chat.send', $this->conversation), [
                'body' => $messageBody,
            ]);

        // Assert
        $response->assertOk();
        $response->assertJsonPath('message.body', $messageBody);
        $response->assertJsonPath('message.type', 'text');
        $response->assertJsonPath('message.is_mine', true);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->doctor->id,
            'body' => $messageBody,
        ]);
    }

    public function test_send_message_validates_body(): void
    {
        // Act
        $response = $this->actingAs($this->doctor)
            ->postJson(route('doctor.chat.send', $this->conversation), [
                'body' => '',
            ]);

        // Assert
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['body']);
    }

    public function test_doctor_can_mark_conversation_as_read(): void
    {
        // Act
        $response = $this->actingAs($this->doctor)
            ->postJson(route('doctor.chat.mark-read', $this->conversation));

        // Assert
        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertNotNull($this->doctorParticipant->fresh()->last_read_at);
    }

    public function test_doctor_can_toggle_favorite(): void
    {
        // Assert - Initial state
        $this->assertFalse($this->doctorParticipant->is_favorite);

        // Act - Toggle ON
        $response = $this->actingAs($this->doctor)
            ->postJson(route('doctor.chat.toggle-favorite', $this->conversation));

        // Assert
        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('is_favorite', true);
        $this->assertTrue($this->doctorParticipant->fresh()->is_favorite);

        // Act - Toggle OFF
        $response = $this->actingAs($this->doctor)
            ->postJson(route('doctor.chat.toggle-favorite', $this->conversation));

        // Assert
        $response->assertJsonPath('is_favorite', false);
        $this->assertFalse($this->doctorParticipant->fresh()->is_favorite);
    }

    public function test_doctor_can_toggle_archive(): void
    {
        // Assert - Initial state
        $this->assertFalse($this->doctorParticipant->is_archived);

        // Act - Toggle ON
        $response = $this->actingAs($this->doctor)
            ->postJson(route('doctor.chat.toggle-archive', $this->conversation));

        // Assert
        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('is_archived', true);
        $this->assertTrue($this->doctorParticipant->fresh()->is_archived);

        // Act - Toggle OFF
        $response = $this->actingAs($this->doctor)
            ->postJson(route('doctor.chat.toggle-archive', $this->conversation));

        // Assert
        $response->assertJsonPath('is_archived', false);
        $this->assertFalse($this->doctorParticipant->fresh()->is_archived);
    }

    public function test_conversations_include_favorite_and_archive_status(): void
    {
        // Arrange
        $this->doctorParticipant->update([
            'is_favorite' => true,
            'is_archived' => false,
        ]);

        // Act
        $response = $this->actingAs($this->doctor)->get(route('doctor.chat.index'));

        // Assert
        $response->assertOk();
        $conversations = $response->viewData('conversations');
        $conversation = $conversations->firstWhere('id', $this->conversation->id);
        $this->assertTrue($conversation['is_favorite']);
        $this->assertFalse($conversation['is_archived']);
    }

    public function test_message_includes_correct_structure(): void
    {
        // Arrange
        Message::create([
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->patient->id,
            'body' => 'Test message',
            'type' => 'text',
        ]);

        // Act
        $response = $this->actingAs($this->doctor)
            ->getJson(route('doctor.chat.messages', $this->conversation));

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'messages' => [
                '*' => [
                    'id',
                    'body',
                    'type',
                    'sender_id',
                    'sender_name',
                    'sender_avatar',
                    'is_mine',
                    'created_at',
                    'date',
                ],
            ],
        ]);
    }
}