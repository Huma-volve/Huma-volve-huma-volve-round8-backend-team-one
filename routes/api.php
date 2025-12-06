<?php

use Illuminate\Support\Facades\Route;

// ============================================================================
// CONTROLLERS
// ============================================================================

use App\Http\Controllers\Auth\{
    LoginController,
    GoogleLoginController,
    ResetPasswordController,
    ForgetPasswordController,
    LogoutController,
    RegisterController,
    VerifyOtpController,
    GoogleRegisterController
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
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/verify-otp', [VerifyOtpController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('/google-login',[GoogleLoginController::class,'googleLogin'])->name('loginWithGoogle');
    Route::post('/google-register',[GoogleRegisterController::class,'googleRegister'])->name('registerWithGoogle');
    Route::post('/forget-password',[ForgetPasswordController::class,'forgetPassword'])->name('forget-password');
    Route::post('/reset-password',[ResetPasswordController::class,'resetPassword'])->name('reset-password');
});

// ============================================================================
// PROTECTED ROUTES (auth:sanctum)
// ============================================================================

Route::middleware('auth:sanctum')->group(function () {

    // ------------------------------------------------------------------------
    // PROFILE MANAGEMENT
    // ------------------------------------------------------------------------
    Route::prefix('profile')->name('profile.')->group(function () {

        // Account Management
        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
        Route::post('/edit', [ProfileAccountController::class, 'editProfile'])->name('edit');
        Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->name('change-password');
        Route::delete('/delete', [DeleteAccountController::class, 'deleteAccount'])->name('delete');

        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

        // Notification Settings
        Route::post('/notifications/toggle', [ProfileNotificationController::class, 'toggle'])->name('notifications.toggle');

        // Payment Methods
        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', [PaymentMethodController::class, 'index'])->name('index');
            Route::post('/', [PaymentMethodController::class, 'store'])->name('store');
            Route::post('/{id}/default', [PaymentMethodController::class, 'setDefault'])->name('set-default');
        });
    });

    // ------------------------------------------------------------------------
    // CHAT / CONVERSATIONS
    // ------------------------------------------------------------------------
    Route::prefix('conversations')->name('conversations.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
        Route::post('/{conversation}/messages', [ChatController::class, 'store'])->name('messages.store');
        Route::patch('/{conversation}/archive', [ChatController::class, 'toggleArchive'])->name('toggle-archive');
        Route::patch('/{conversation}/favorite', [ChatController::class, 'toggleFavorite'])->name('toggle-favorite');
    });

    // ------------------------------------------------------------------------
    // REVIEWS
    // ------------------------------------------------------------------------
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/', [ReviewController::class, 'store'])->name('store');
        Route::get('/doctor', [ReviewController::class, 'reviews'])->name('doctor.index');
        Route::post('/doctor/{review}/reply', [ReviewController::class, 'reply'])->name('doctor.reply');
    });

    // ------------------------------------------------------------------------
    // NOTIFICATIONS
    // ------------------------------------------------------------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [ApiNotificationController::class, 'index'])->name('index');
        Route::get('/unread', [ApiNotificationController::class, 'unread'])->name('unread');
        Route::post('/{id}/read', [ApiNotificationController::class, 'markAsRead'])->name('mark-read');
    });
});
