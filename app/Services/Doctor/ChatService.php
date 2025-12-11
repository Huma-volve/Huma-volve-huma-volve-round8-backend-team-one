<?php

namespace App\Services\Doctor;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Repositories\Contracts\DoctorChatRepositoryInterface;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ChatService
{
    public function __construct(
        protected DoctorChatRepositoryInterface $chatRepository
    ) {}

    public function getConversationsForDoctor(int $doctorId): Collection
    {
        $conversations = $this->chatRepository->getDoctorConversations($doctorId);

        return $conversations->map(function ($conversation) use ($doctorId) {
            return $this->mapConversationData($conversation, $doctorId);
        });
    }

    public function getMessagesForConversation(Conversation $conversation, int $doctorId): Collection
    {
        $this->authorizeParticipant($conversation->id, $doctorId);

        $messages = $this->chatRepository->getConversationMessages($conversation->id);

        return $messages->map(function ($message) use ($doctorId) {
            return $this->mapMessageData($message, $doctorId);
        });
    }

    public function sendMessage(Conversation $conversation, int $doctorId, string $body): array
    {
        $this->authorizeParticipant($conversation->id, $doctorId);

        $message = $this->chatRepository->createMessage([
            'conversation_id' => $conversation->id,
            'sender_id' => $doctorId,
            'body' => $body,
            'type' => 'text',
        ]);

        $this->chatRepository->updateConversationTimestamp($conversation);

        $message->load('sender');

        MessageSent::dispatch($message);

        return $this->mapMessageData($message, $doctorId);
    }

    public function markConversationAsRead(Conversation $conversation, int $doctorId): void
    {
        $participant = $this->chatRepository->findParticipant($conversation->id, $doctorId);

        if (!$participant) {
            throw new AccessDeniedHttpException('Unauthorized');
        }

        $this->chatRepository->updateParticipantLastRead($participant);
    }

    protected function authorizeParticipant(int $conversationId, int $userId): void
    {
        $participant = $this->chatRepository->findParticipant($conversationId, $userId);

        if (!$participant) {
            throw new AccessDeniedHttpException('Unauthorized');
        }
    }

    protected function mapConversationData(Conversation $conversation, int $doctorId): array
    {
        $otherParticipant = $conversation->participants
            ->where('user_id', '!=', $doctorId)
            ->first();

        $currentParticipant = $conversation->participants
            ->where('user_id', $doctorId)
            ->first();

        $lastReadAt = $currentParticipant?->last_read_at ?? $conversation->created_at;

        $unreadCount = $conversation->messages()
            ->where('sender_id', '!=', $doctorId)
            ->where('created_at', '>', $lastReadAt)
            ->count();

        return [
            'id' => $conversation->id,
            'patient' => $otherParticipant?->user,
            'last_message' => $conversation->lastMessage,
            'unread_count' => $unreadCount,
            'updated_at' => $conversation->updated_at,
            'is_favorite' => (bool) $currentParticipant?->is_favorite,
            'is_archived' => (bool) $currentParticipant?->is_archived,
        ];
    }

    protected function mapMessageData(Message $message, int $doctorId): array
    {
        return [
            'id' => $message->id,
            'body' => $message->type === 'text'
                ? $message->body
                : asset('storage/' . $message->body),
            'type' => $message->type,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->name,
            'sender_avatar' => $message->sender->profile_photo_path
                ? asset('storage/' . $message->sender->profile_photo_path)
                : null,
            'is_mine' => $message->sender_id === $doctorId,
            'created_at' => $message->created_at->format('h:i A'),
            'date' => $message->created_at->format('M d, Y'),
        ];
    }
}
