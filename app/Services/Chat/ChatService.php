<?php

namespace App\Services\Chat;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Repositories\Contracts\ChatRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ChatService
{
    public function __construct(
        protected ChatRepositoryInterface $chatRepository
    ) {}

    public function getOrCreateConversation(int $patientId, int $doctorId): Conversation
    {
        $conversation = Conversation::whereHas('participants', function ($q) use ($patientId) {
            $q->where('user_id', $patientId);
        })->whereHas('participants', function ($q) use ($doctorId) {
            $q->where('user_id', $doctorId);
        })->first();

        if ($conversation) {
            return $conversation->load(['participants.user', 'lastMessage']);
        }

        $conversation = Conversation::create();

        $conversation->participants()->createMany([
            ['user_id' => $patientId],
            ['user_id' => $doctorId],
        ]);

        return $conversation->load(['participants.user', 'lastMessage']);
    }

    public function sendMessage(User $sender, Conversation $conversation, array $data): Message
    {
        $messageData = [
            'conversation_id' => $conversation->id,
            'sender_id' => $sender->id,
            'body' => $data['body'] ?? null,
            'type' => 'text',
        ];

        if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            try {
                if (!$data['attachment']->isValid()) {
                    throw ValidationException::withMessages([
                        'attachment' => 'The file was not uploaded correctly.'
                    ]);
                }

                $path = $data['attachment']->store('chat_media', 'public');
                
                if (!$path) {
                    throw new \Exception('Failed to store file path.');
                }

                $messageData['body'] = $path;
                $messageData['type'] = $this->getMediaType($data['attachment']);

            } catch (\Exception $e) {
                Log::error('File Upload Error: ' . $e->getMessage());

                throw ValidationException::withMessages([
                    'attachment' => 'Failed to upload attachment. Please try again or check file size/type.'
                ]);
            }
        }

        $message = $this->chatRepository->createMessage($messageData);
        $this->chatRepository->updateConversationTimestamp($conversation->id);

        MessageSent::dispatch($message);

        return $message->load('sender');
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

        if (str_contains($mime, 'audio')) {
            return 'audio';
        }

        return 'file';
    }
}