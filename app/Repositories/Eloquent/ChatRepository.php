<?php

namespace App\Repositories\Eloquent;

use App\Models\Conversation;
use App\Models\Message;
use App\Repositories\Contracts\ChatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ChatRepository implements ChatRepositoryInterface
{
    public function getUserConversations(int $userId): Collection
    {
        return Conversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->orderByDesc('updated_at')->get();
    }

    public function getConversationMessages(int $conversationId): Collection
    {
        return Message::where('conversation_id', $conversationId)
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
