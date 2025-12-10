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

// الراوت ده للتجربة بس  يا شباب هبقي امسحه بعدين 
Route::get('test-design', function () {
    return view('layouts.app');
});