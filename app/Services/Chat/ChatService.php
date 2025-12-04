<?php

namespace App\Services\Chat;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Repositories\Contracts\ChatRepositoryInterface;
use Illuminate\Http\UploadedFile;

class ChatService
{
    public function __construct(
        protected ChatRepositoryInterface $chatRepository
    ) {}

    public function sendMessage(User $sender, Conversation $conversation, array $data): Message
    {
        $messageData = [
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $data['body'] ?? null,
            'type' => 'text',
        ];

        if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            $path = $data['attachment']->store('chat_media', 'public');
            $messageData['body'] = $path;
            $messageData['type'] = $this->getMediaType($data['attachment']);
        }

        $message = $this->chatRepository->createMessage($messageData);

        $this->chatRepository->updateConversationTimestamp($conversation->id);

        return $message;
    }

    protected function getMediaType(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        if (str_contains($mime, 'image')) {
            return 'image';
        }

        if (str_contains($mime, 'video')) {
            return 'video';
        }

        return 'file';
    }
}
