<?php

use App\Http\Controllers\Api\ChatController;
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
| Current User Route
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Chat Routes (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/conversations', [ChatController::class, 'index']);
    Route::get('/conversations/{conversation}', [ChatController::class, 'show']);
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'store']);
    Route::patch('/conversations/{conversation}/archive', [ChatController::class, 'toggleArchive']);
    Route::patch('/conversations/{conversation}/favorite', [ChatController::class, 'toggleFavorite']);

    // Booking Routes
    Route::apiResource('bookings', \App\Http\Controllers\BookingController::class);
    Route::post('/bookings/{booking}/cancel', [\App\Http\Controllers\BookingController::class, 'cancel']);

    // Payment Routes
    Route::post('/payments/process', [\App\Http\Controllers\PaymentController::class, 'process']);

    // Saved Cards Routes
    Route::apiResource('saved-cards', \App\Http\Controllers\SavedCardController::class)->only(['index', 'store', 'destroy']);
});
