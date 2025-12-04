<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\ChatRepositoryInterface;
use App\Models\Conversation;
use App\Services\Chat\ChatService;
use App\Http\Requests\Api\SendMessageRequest;

class ChatController extends Controller
{
    public function __construct(
        protected ChatRepositoryInterface $chatRepository,
        protected ChatService $chatService
    ) {}

    public function index(Request $request)
    {
        $conversations = $this->chatRepository->getUserConversations($request->user()->id);

        return ConversationResource::collection($conversations);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $participant = $conversation->participants()->where('user_id', $request->user()->id)->firstOrFail();

        $participant->update([
            'last_read_at' => now(),
        ]);

        $messages = $this->chatRepository->getConversationMessages($conversation->id);

        return MessageResource::collection($messages);
    }

    public function store(SendMessageRequest $request, Conversation $conversation)
    {
        if (! $conversation->participants()->where('user_id', $request->user()->id)->exists()) {
            abort(403);
        }

        $message = $this->chatService->sendMessage(
            $request->user(),
            $conversation,
            $request->validated()
        );

        return new MessageResource($message);
    }

    public function toggleArchive(Request $request, Conversation $conversation)
    {
        $participant = $conversation->participants()->where('user_id', $request->user()->id)->firstOrFail();

        $participant->update([
            'is_archived' => ! $participant->is_archived,
        ]);

        return response()->noContent();
    }

    public function toggleFavorite(Request $request, Conversation $conversation)
    {
        $participant = $conversation->participants()->where('user_id', $request->user()->id)->firstOrFail();

        $participant->update([
            'is_favorite' => ! $participant->is_favorite,
        ]);

        return response()->noContent();
    }
}
