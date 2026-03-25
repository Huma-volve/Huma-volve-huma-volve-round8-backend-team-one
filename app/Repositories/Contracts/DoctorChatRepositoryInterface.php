<?php

namespace App\Repositories\Contracts;

use App\Models\ChatParticipant;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Collection;

interface DoctorChatRepositoryInterface
{
    public function getDoctorConversations(int $doctorId): Collection;

    public function getConversationMessages(int $conversationId): Collection;

    public function findParticipant(int $conversationId, int $userId): ?ChatParticipant;

    public function createMessage(array $data): Message;

    public function updateConversationTimestamp(Conversation $conversation): void;

    public function updateParticipantLastRead(ChatParticipant $participant): void;

    public function toggleParticipantFavorite(ChatParticipant $participant): void;

    public function toggleParticipantArchive(ChatParticipant $participant): void;
}