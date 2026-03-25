<?php

namespace App\Repositories;

use App\Models\ChatParticipant;
use App\Models\Conversation;
use App\Models\Message;
use App\Repositories\Contracts\DoctorChatRepositoryInterface;
use Illuminate\Support\Collection;

class DoctorChatRepository implements DoctorChatRepositoryInterface
{
    public function getDoctorConversations(int $doctorId): Collection
    {
        return Conversation::whereHas('participants', function ($query) use ($doctorId) {
            $query->where('user_id', $doctorId);
        })
            ->with(['participants.user', 'lastMessage.sender'])
            ->orderByDesc('updated_at')
            ->get();
    }

    public function getConversationMessages(int $conversationId): Collection
    {
        return Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function findParticipant(int $conversationId, int $userId): ?ChatParticipant
    {
        return ChatParticipant::where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->first();
    }

    public function createMessage(array $data): Message
    {
        return Message::create($data);
    }

    public function updateConversationTimestamp(Conversation $conversation): void
    {
        $conversation->touch();
    }

    public function updateParticipantLastRead(ChatParticipant $participant): void
    {
        $participant->update(['last_read_at' => now()]);
    }

    public function toggleParticipantFavorite(ChatParticipant $participant): void
    {
        $participant->update(['is_favorite' => !$participant->is_favorite]);
    }

    public function toggleParticipantArchive(ChatParticipant $participant): void
    {
        $participant->update(['is_archived' => !$participant->is_archived]);
    }
}
