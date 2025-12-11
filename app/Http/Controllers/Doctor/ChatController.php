<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\SendMessageRequest;
use App\Models\Conversation;
use App\Services\Doctor\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        protected ChatService $chatService
    ) {}

    public function index(): View
    {
        $conversations = $this->chatService->getConversationsForDoctor(Auth::id());

        return view('doctor.chat.index', compact('conversations'));
    }

    public function getMessages(Conversation $conversation): JsonResponse
    {
        $messages = $this->chatService->getMessagesForConversation($conversation, Auth::id());

        return response()->json(['messages' => $messages]);
    }

    public function sendMessage(SendMessageRequest $request, Conversation $conversation): JsonResponse
    {
        $message = $this->chatService->sendMessage(
            $conversation,
            Auth::id(),
            $request->validated('body')
        );

        return response()->json(['message' => $message]);
    }

    public function markAsRead(Conversation $conversation): JsonResponse
    {
        $this->chatService->markConversationAsRead($conversation, Auth::id());

        return response()->json(['success' => true]);
    }

    public function toggleFavorite(Conversation $conversation): JsonResponse
    {
        $result = $this->chatService->toggleFavorite($conversation, Auth::id());

        return response()->json($result);
    }

    public function toggleArchive(Conversation $conversation): JsonResponse
    {
        $result = $this->chatService->toggleArchive($conversation, Auth::id());

        return response()->json($result);
    }
}