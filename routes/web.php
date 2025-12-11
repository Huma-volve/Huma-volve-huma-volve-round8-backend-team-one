<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\ChatController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:web'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/{conversation}/mark-read', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
});

// الراوتس الجاية كلها للتجربة بس  يا شباب علشان اللي حابب يشوف شكل التصميم لكن همسحها بعدين
Route::get('test-design', function () {
    return view('test-design');
});
Route::get('test-chat', function () {
    $doctorId = 2;
    
    $chatRepository = app(\App\Repositories\Contracts\DoctorChatRepositoryInterface::class);
    $chatService = new \App\Services\Doctor\ChatService($chatRepository);
    
    $conversations = $chatService->getConversationsForDoctor($doctorId);

    return view('doctor.chat.index', compact('conversations'));
});

Route::get('test-chat/{conversation}/messages', function (\App\Models\Conversation $conversation) {
    $doctorId = 2;
    
    $chatRepository = app(\App\Repositories\Contracts\DoctorChatRepositoryInterface::class);
    $chatService = new \App\Services\Doctor\ChatService($chatRepository);
    
    $messages = $chatService->getMessagesForConversation($conversation, $doctorId);

    return response()->json(['messages' => $messages]);
});

Route::post('test-chat/{conversation}/send', function (\Illuminate\Http\Request $request, \App\Models\Conversation $conversation) {
    $doctorId = 2;
    
    $chatRepository = app(\App\Repositories\Contracts\DoctorChatRepositoryInterface::class);
    $chatService = new \App\Services\Doctor\ChatService($chatRepository);
    
    $message = $chatService->sendMessage($conversation, $doctorId, $request->input('body'));

    return response()->json(['message' => $message]);
});