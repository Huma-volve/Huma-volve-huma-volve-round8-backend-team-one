<?php

namespace App\Repositories\Contracts;

use App\Models\Message;
use Illuminate\Pagination\LengthAwarePaginator;

interface ChatRepositoryInterface
{
    public function getUserConversations(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    
    public function getConversationMessages(int $conversationId, int $perPage = 50): LengthAwarePaginator;
    
    public function createMessage(array $data): Message; 
    
    public function updateConversationTimestamp(int $conversationId): void;
}