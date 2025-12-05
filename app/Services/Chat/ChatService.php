<?php

namespace App\Services\Chat;

use App\Events\MessageSent;
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

    /**
     * Send a new message in a conversation.
     *
     * Handles text and file attachments, persists to database,
     * updates conversation timestamp, and dispatches real-time event.
     *
     * @param User $sender
     * @param Conversation $conversation
     * @param array $data Validated data containing 'body' and optional 'attachment'
     * @return Message
     */
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

        MessageSent::dispatch($message);

        return $message->load('sender');
    }


    /**
     * Determine the media type based on the file MIME type.
     *
     * @param UploadedFile $file
     * @return string 'image', 'video', or 'file'
     */
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
