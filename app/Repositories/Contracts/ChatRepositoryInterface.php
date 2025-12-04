<?php

namespace App\Repositories\Contracts;

use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;

interface ChatRepositoryInterface
{
    public function getUserConversations(int $userId): Collection;

    public function getConversationMessages(int $conversationId): Collection;

    public function createMessage(array $data): Message;

    public function updateConversationTimestamp(int $conversationId): void;
}
