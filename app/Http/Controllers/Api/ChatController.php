<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendMessageRequest;
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

    /**
     * List user conversations with pagination.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $conversations = $this->chatRepository->getUserConversations($request->user()->id);

        return ConversationResource::collection($conversations);
    }

    /**
     * Display a conversation and mark it as read.
     *
     * @param Request $request
     * @param Conversation $conversation
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(Request $request, Conversation $conversation)
    {
        $participant = $conversation->participants()->where('user_id', $request->user()->id)->firstOrFail();

        $participant->update([
            'last_read_at' => now(),
        ]);

        $messages = $this->chatRepository->getConversationMessages($conversation->id);

        return MessageResource::collection($messages);
    }

    /**
     * Send a new message to a conversation.
     *
     * @param SendMessageRequest $request
     * @param Conversation $conversation
     * @return MessageResource
     */
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

    /**
     * Toggle the archived status of a conversation.
     *
     * @param Request $request
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function toggleArchive(Request $request, Conversation $conversation)
    {
        $participant = $conversation->participants()->where('user_id', $request->user()->id)->firstOrFail();

        $participant->update([
            'is_archived' => ! $participant->is_archived,
        ]);

        return response()->noContent();
    }

    /**
     * Toggle the favorite status of a conversation.
     *
     * @param Request $request
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function toggleFavorite(Request $request, Conversation $conversation)
    {
        $participant = $conversation->participants()->where('user_id', $request->user()->id)->firstOrFail();

        $participant->update([
            'is_favorite' => ! $participant->is_favorite,
        ]);

        return response()->noContent();
    }
}
