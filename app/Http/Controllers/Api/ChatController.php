<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
use App\Http\Requests\Api\StartConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Services\Chat\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        protected ChatRepositoryInterface $chatRepository,
        protected ChatService $chatService
    ) {}

    public function index(Request $request)
    {
        $conversations = $this->chatRepository->getUserConversations(
            $request->user()->id,
            $request->only(['search', 'type'])
        );

        return ConversationResource::collection($conversations);
    }

    public function startConversation(StartConversationRequest $request)
    {
        $conversation = $this->chatService->getOrCreateConversation(
            $request->user()->id,
            $request->validated('doctor_id')
        );

        return new ConversationResource($conversation);
    }

    public function show(Request $request, Conversation $conversation)
    {
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            abort(403, 'Unauthorized');
        }

        $messages = $this->chatRepository->getConversationMessages($conversation->id);
        return MessageResource::collection($messages);
    }

    public function sendMessage(SendMessageRequest $request, Conversation $conversation)
    {
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            abort(403, 'Unauthorized');
        }

        $message = $this->chatService->sendMessage(
            $request->user(),
            $conversation,
            $request->validated()
        );

        return new MessageResource($message);
    }

    public function markAsRead(Request $request, Conversation $conversation)
    {
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            abort(403, 'Unauthorized');
        }

        $participant = $conversation->participants()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $participant->update(['last_read_at' => now()]);

        return response()->noContent();
    }

    public function toggleArchive(Request $request, Conversation $conversation)
    {
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            abort(403, 'Unauthorized');
        }

        $participant = $conversation->participants()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $participant->update(['is_archived' => !$participant->is_archived]);

        return response()->noContent();
    }

    public function toggleFavorite(Request $request, Conversation $conversation)
    {
        if (!$conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            abort(403, 'Unauthorized');
        }

        $participant = $conversation->participants()
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $participant->update(['is_favorite' => !$participant->is_favorite]);

        return response()->noContent();
    }
}