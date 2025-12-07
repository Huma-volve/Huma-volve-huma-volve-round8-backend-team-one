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


    // get existing conversation or create new one between patient and doctor
    public function getOrCreateConversation(int $patientId, int $doctorId): Conversation
    {
        // check if conversation already exists between these two users
        $conversation = Conversation::whereHas('participants', function ($q) use ($patientId) {
            $q->where('user_id', $patientId);
        })->whereHas('participants', function ($q) use ($doctorId) {
            $q->where('user_id', $doctorId);
        })->first();

        // if exists return it
        if ($conversation) {
            return $conversation->load(['participants.user', 'lastMessage']);
        }

        // create new conversation
        $conversation = Conversation::create();

        // add both users as participants
        $conversation->participants()->createMany([
            ['user_id' => $patientId],
            ['user_id' => $doctorId],
        ]);

        return $conversation->load(['participants.user', 'lastMessage']);
    }

    // send a new message in a conversation
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

    // determine the media type based on file MIME type
    protected function getMediaType(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        if (str_contains($mime, 'image')) {
            return 'image';
        }

        if (str_contains($mime, 'video')) {
            return 'video';
        }

        if (str_contains($mime, 'audio')) {
            return 'audio';
        }

        return 'file';
    }
}
