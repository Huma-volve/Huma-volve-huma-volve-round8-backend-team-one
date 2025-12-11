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
    Route::post('/chat/{conversation}/toggle-favorite', [ChatController::class, 'toggleFavorite'])->name('chat.toggle-favorite');
    Route::post('/chat/{conversation}/toggle-archive', [ChatController::class, 'toggleArchive'])->name('chat.toggle-archive');
});