<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ChatRepositoryInterface
{
    public function getUserConversations(int $userId, array $filters = []): Collection;
    
    public function getConversationMessages(int $conversationId): Collection;
    
    public function createMessage(array $data);
    
    public function updateConversationTimestamp(int $conversationId): void;
}