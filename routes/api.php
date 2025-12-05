<?php

use Illuminate\Support\Facades\Route;

// ============================================================================
// CONTROLLERS
// ============================================================================

use App\Http\Controllers\Auth\{
    LoginController,
    LogoutController,
    RegisterController,
    VerifyOtpController
};

use App\Http\Controllers\Profile\{
    ChangePasswordController,
    DeleteAccountController,
    FavoriteController,
    NotificationController as ProfileNotificationController,
    PaymentMethodController,
    ProfileAccountController
};

use App\Http\Controllers\Api\{
    ChatController,
    NotificationController as ApiNotificationController,
    ReviewController
};

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/register', [RegisterController::class, 'Register'])->name('register');
    Route::post('/verify-otp', [VerifyOtpController::class, 'verifyOtp'])->name('verify-otp');
});

// ============================================================================
// PROTECTED ROUTES
// ============================================================================

Route::middleware('auth:sanctum')->group(function () {

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
        Route::delete('/delete-account', [DeleteAccountController::class, 'deleteAccount'])->name('delete');
        Route::post('/edit-profile', [ProfileAccountController::class, 'editProfile'])->name('edit');
        Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('change-password');
        Route::post('/notifications', [ProfileNotificationController::class, 'toggle'])->name('notifications.toggle');
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');

        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
            Route::post('/', [PaymentMethodController::class, 'store'])->name('store');
            Route::post('/{id}/default', [PaymentMethodController::class, 'setDefault'])->name('set-default');
        });
    });

    // Chat
    Route::prefix('conversations')->name('conversations.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
        Route::post('/{conversation}/messages', [ChatController::class, 'store'])->name('messages.store');
        Route::patch('/{conversation}/archive', [ChatController::class, 'toggleArchive'])->name('toggle-archive');
        Route::patch('/{conversation}/favorite', [ChatController::class, 'toggleFavorite'])->name('toggle-favorite');
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/store', [ReviewController::class, 'store'])->name('store');
        Route::get('/doctor', [ReviewController::class, 'reviews'])->name('doctor.index');
        Route::post('/doctor/{review}/reply', [ReviewController::class, 'reply'])->name('doctor.reply');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [ApiNotificationController::class, 'index'])->name('index');
        Route::get('/unread', [ApiNotificationController::class, 'unread'])->name('unread');
        Route::post('/{id}/read', [ApiNotificationController::class, 'markAsRead'])->name('mark-read');
    });
});
