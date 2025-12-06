<?php

use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Auth\{
    LoginController,
    VerifyOtpController,
    RegisterController,
    LogoutController,
};

use App\Http\Controllers\Profile\
{
    DeleteAccountController,
    ChangePasswordController,
    ProfileAccountController,
    NotificationController as ProfileNotificationController,
    PaymentMethodController,
    FavoriteController
};

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\SavedCardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/login',[LoginController::class,'login']);
Route::post('/verify-otp',[VerifyOtpController::class,'verifyOtp']);
Route::post('/register',[RegisterController::class,'Register']);
Route::middleware(['auth:sanctum'])->group(function (){
    Route::prefix('profile')->group(function(){
        Route::post('/logout',[LogoutController::class,'logout']);
        Route::delete('/delete-account',[DeleteAccountController::class,'deleteAccount']);
        Route::post('/change-password',[ChangePasswordController::class,'changePassword']);
        Route::post('/edit-profile',[ProfileAccountController::class,'editProfile']);
        Route::post('/notifications',[ProfileNotificationController::class,'toggle']);
        Route::get('/payment-methods',[PaymentMethodController::class,'index']);
        Route::post('/payment-methods',[PaymentMethodController::class,'store']);
        Route::post('/payment-methods/{id}/default',[PaymentMethodController::class,'setDefault']);
        Route::get('/favorites',[FavoriteController::class,'index']);
    });
});

/*
|--------------------------------------------------------------------------
| Current User Route
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Chat Routes
    Route::get('/conversations', [ChatController::class, 'index']);
    Route::get('/conversations/{conversation}', [ChatController::class, 'show']);
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'store']);
    Route::patch('/conversations/{conversation}/archive', [ChatController::class, 'toggleArchive']);
    Route::patch('/conversations/{conversation}/favorite', [ChatController::class, 'toggleFavorite']);

    // Booking Routes
    Route::apiResource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

    // Payment Routes
    Route::post('/payments/process', [PaymentController::class, 'process']);

    // Saved Cards Routes
    Route::apiResource('saved-cards', SavedCardController::class)->only(['index', 'store', 'destroy']);


    Route::post('/store/review', [ReviewController::class, 'store']);
    Route::get('/doctor/reviews', [ReviewController::class, 'reviews']);
    Route::post('/doctor/review/{review}/reply', [ReviewController::class, 'reply']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});
