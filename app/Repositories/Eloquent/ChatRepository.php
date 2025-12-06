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
        // 1. get conversations where this user is a participant
        $query = Conversation::whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        // 2. apply search by participant name
        if (request()->has('search') && request('search') != null) {
            $searchTerm = request('search');
            $query->whereHas('participants.user', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        // 3. apply filters favorites or unread
        if (request()->has('type')) {

            // filter favorite conversations
            if (request('type') === 'favorites') {
                $query->whereHas('participants', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->where('is_favorite', true);
                });
            }

            // filter unread conversations
            if (request('type') === 'unread') {
                $query->whereHas('participants', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->whereRaw('(SELECT COUNT(*) FROM messages WHERE messages.conversation_id = conversations.id AND messages.created_at > chat_participants.last_read_at) > 0');
                });
            }
        }

        return $query->with(['participants.user', 'lastMessage'])->orderByDesc('updated_at')->get();
    }

    public function getConversationMessages(int $conversationId): Collection
    {
        return Message::where('conversation_id', $conversationId)->with('sender')->orderBy('created_at', 'asc')->get();
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
