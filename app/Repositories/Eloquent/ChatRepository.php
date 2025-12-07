<?php

namespace App\Repositories\Eloquent;

use App\Models\Conversation;
use App\Models\Message;
use App\Repositories\Contracts\ChatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository implements ChatRepositoryInterface
{
    public function getUserConversations(int $userId, array $filters = []): Collection
    {
        return Conversation::query()
            // user must be participant
            ->whereHas('participants', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            // search by other participant's name
            ->when(!empty($filters['search']), function ($q) use ($filters, $userId) {
                $q->whereHas('participants', function ($subQ) use ($filters, $userId) {
                    $subQ->where('user_id', '!=', $userId)
                         ->whereHas('user', function ($userQ) use ($filters) {
                             $userQ->where('name', 'like', '%' . $filters['search'] . '%');
                         });
                });
            })
            // filter by favorites
            ->when(($filters['type'] ?? '') === 'favorites', function ($q) use ($userId) {
                $q->whereHas('participants', function ($subQ) use ($userId) {
                    $subQ->where('user_id', $userId)
                         ->where('is_favorite', true);
                });
            })
            // filter by unread
            ->when(($filters['type'] ?? '') === 'unread', function ($q) use ($userId) {
                $q->whereHas('participants', function ($subQ) use ($userId) {
                    $subQ->where('user_id', $userId)
                         ->whereColumn('last_read_at', '<', 'conversations.updated_at');
                });
            })
            // filter archived
            ->when(($filters['type'] ?? '') === 'archived', function ($q) use ($userId) {
                $q->whereHas('participants', function ($subQ) use ($userId) {
                    $subQ->where('user_id', $userId)
                         ->where('is_archived', true);
                });
            })
            ->with(['lastMessage.sender', 'participants.user'])
            ->latest('updated_at')
            ->get();
    }

    public function getConversationMessages(int $conversationId): Collection
    {
        return Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function createMessage(array $data): Message
    {
        return Message::create($data);
    }

    public function updateConversationTimestamp(int $conversationId): void
    {
        Conversation::where('id', $conversationId)->update(['updated_at' => now()]);
    }
}