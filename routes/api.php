<?php

use App\Http\Controllers\Api\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/conversations', [ChatController::class, 'index']);
    Route::get('/conversations/{conversation}', [ChatController::class, 'show']);
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'store']);
    Route::patch('/conversations/{conversation}/archive', [ChatController::class, 'toggleArchive']);
    Route::patch('/conversations/{conversation}/favorite', [ChatController::class, 'toggleFavorite']);
});
