<?php

use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('store/review', [ReviewController::class, 'store']);
Route::get('doctor/reviews', [ReviewController::class, 'reviews']);
Route::post('/doctor/review/{review}/reply', [ReviewController::class, 'reply']);
Route::get('/notifications', [NotificationController::class, 'index']);
Route::get('notifications/unread', [NotificationController::class, 'unread']);
Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);

