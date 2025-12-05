<?php

use App\Http\Controllers\Api\{
    ReviewController,
    NotificationController,
    ChatController
};
use App\Http\Controllers\Auth\{
    LoginController,
    VerifyOtpController,
    RegisterController
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [LoginController::class, 'login']);
Route::post('/verify-otp', [VerifyOtpController::class, 'verifyOtp']);
Route::post('/register', [RegisterController::class, 'Register']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Chat Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/conversations', [ChatController::class, 'index']);
    Route::get('/conversations/{conversation}', [ChatController::class, 'show']);
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'store']);
    Route::patch('/conversations/{conversation}/archive', [ChatController::class, 'toggleArchive']);
    Route::patch('/conversations/{conversation}/favorite', [ChatController::class, 'toggleFavorite']);

    /*
    |--------------------------------------------------------------------------
    | Reviews
    |--------------------------------------------------------------------------
    */
    Route::post('/store/review', [ReviewController::class, 'store']);
    Route::get('/doctor/reviews', [ReviewController::class, 'reviews']);
    Route::post('/doctor/review/{review}/reply', [ReviewController::class, 'reply']);

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});
