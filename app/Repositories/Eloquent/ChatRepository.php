<?php

namespace App\Repositories\Eloquent;

use App\Models\Conversation;
use App\Models\Message;
use App\Repositories\Contracts\ChatRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatRepository implements ChatRepositoryInterface
{
    public function getUserConversations(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $type = $filters['type'] ?? '';
        $isArchivedRequest = $type === 'archived';

        return Conversation::query()
            ->whereHas('participants', function ($q) use ($userId, $isArchivedRequest, $type) {
                $q->where('user_id', $userId)
                  ->where('is_archived', $isArchivedRequest)
                  ->when($type === 'favorites', function ($subQ) {
                      $subQ->where('is_favorite', true);
                  })
                  ->when($type === 'unread', function ($subQ) {
                      $subQ->whereColumn('last_read_at', '<', 'conversations.updated_at');
                  });
            })
            ->when(!empty($filters['search']), function ($q) use ($filters, $userId) {
                $q->whereHas('participants', function ($subQ) use ($filters, $userId) {
                    $subQ->where('user_id', '!=', $userId)
                        ->whereHas('user', function ($userQ) use ($filters) {
                            $userQ->where('name', 'like', '%' . $filters['search'] . '%');
                        });
                });
            })
            ->with(['lastMessage.sender', 'participants.user'])
            ->latest('updated_at')
            ->paginate($perPage);
    }

    public function getConversationMessages(int $conversationId, int $perPage = 50): LengthAwarePaginator
    {
        return Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
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