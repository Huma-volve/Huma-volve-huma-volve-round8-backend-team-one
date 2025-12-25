<?php

use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\GoogleLoginController;
// ============================================================================
// CONTROLLERS
// ============================================================================

use App\Http\Controllers\Api\Auth\GoogleRegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResendOtpController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\VerifyOtpController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\FavoriteController as ToggleFavoriteController;
use App\Http\Controllers\Api\GetDoctorAvailabilityController;
use App\Http\Controllers\Api\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\Profile\ChangePasswordController;
use App\Http\Controllers\Api\Profile\DeleteAccountController;
use App\Http\Controllers\Api\Profile\FavoriteController as ProfileFavoriteController;
use App\Http\Controllers\Api\Profile\NotificationController as ProfileNotificationController;
use App\Http\Controllers\Api\Profile\ProfileAccountController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SavedCardController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\Api\SupportContentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

// Authentication
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/verify-otp', [VerifyOtpController::class, 'verifyOtp'])->name('verify-otp');
    Route::post('/resend-otp', [ResendOtpController::class, 'resendOtp'])->middleware('throttle:resend-otp')->name('resend-otp');
    Route::post('/google-login', [GoogleLoginController::class, 'googleLogin'])->name('loginWithGoogle');
    Route::post('/google-register', [GoogleRegisterController::class, 'googleRegister'])->name('registerWithGoogle');
    Route::post('/forget-password', [ForgetPasswordController::class, 'forgetPassword'])->name('forget-password');
    Route::put('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('reset-password');
});

Route::get('/policies', [SupportContentController::class, 'index']);
Route::get('/faqs', [SupportContentController::class, 'indexFaqs']);

// Specialties
Route::get('/specialties', [SpecialtyController::class, 'index'])->middleware('auth:sanctum');

// Doctors
Route::apiResource('doctors', DoctorController::class)->only(['index', 'show'])->middleware('auth:sanctum');
Route::get('/doctors/{doctor}/availability', GetDoctorAvailabilityController::class)->middleware('auth:sanctum');

// Contact Us
Route::post('/contact-us', [ContactMessageController::class, 'store']);

// ============================================================================
// PROTECTED ROUTES (auth:sanctum)
// ============================================================================

Route::middleware('auth:sanctum')->group(function () {

    // User Info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ------------------------------------------------------------------------
    // PROFILE MANAGEMENT
    // ------------------------------------------------------------------------
    Route::prefix('profile')->name('profile.')->group(function () {

        // Account Management
        Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
        Route::get('/show', [ProfileAccountController::class, 'showProfile']);
        Route::post('/edit', [ProfileAccountController::class, 'editProfile']);
        Route::put('/change-password', [ChangePasswordController::class, 'changePassword'])->name('change-password');
        Route::delete('/delete', [DeleteAccountController::class, 'deleteAccount'])->name('delete');

        // Favorites (List)
        Route::get('/favorites', [ProfileFavoriteController::class, 'index'])->name('favorites.index');

        // Notification Settings
        Route::put('/notifications/toggle', [ProfileNotificationController::class, 'toggle'])->name('notifications.toggle');

    });

    // Toggle Favorite Doctor
    Route::post('/doctors/{doctor}/favorite', ToggleFavoriteController::class);

    // ------------------------------------------------------------------------
    // CHAT / CONVERSATIONS
    // ------------------------------------------------------------------------
    Route::prefix('conversations')->name('conversations.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::post('/start', [ChatController::class, 'startConversation'])->name('start');

        Route::prefix('{conversation}')->group(function () {
            Route::get('/', [ChatController::class, 'show'])->name('show');
            Route::post('/messages', [ChatController::class, 'sendMessage'])->name('messages.store');
            Route::post('/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');
            Route::patch('/archive', [ChatController::class, 'toggleArchive'])->name('toggle-archive');
            Route::patch('/favorite', [ChatController::class, 'toggleFavorite'])->name('toggle-favorite');
        });
    });

    // ------------------------------------------------------------------------
    // REVIEWS
    // ------------------------------------------------------------------------
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/', [ReviewController::class, 'store'])->name('store');
        Route::get('/doctor', [ReviewController::class, 'reviews'])->name('doctor.index');
        Route::post('/doctor/{review}/reply', [ReviewController::class, 'reply'])->name('doctor.reply');
        Route::get('/doctors/avg', [ReviewController::class, 'doctorsWithAvg']);
        Route::get('/all', [ReviewController::class, 'allReviews']);
        Route::get('/doctor/{doctor}', [ReviewController::class, 'reviewsByDoctor'])->name('doctor.reviews');
    });

    // ------------------------------------------------------------------------
    // NOTIFICATIONS
    // ------------------------------------------------------------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [ApiNotificationController::class, 'index'])->name('index');
        Route::get('/unread', [ApiNotificationController::class, 'unread'])->name('unread');
        Route::post('/{id}/read', [ApiNotificationController::class, 'markAsRead'])->name('mark-read');
    });

    // Booking Routes
    Route::apiResource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);

    // Payment Routes
    Route::post('/payments/process', [PaymentController::class, 'process']);

    // Saved Cards Routes
    Route::apiResource('saved-cards', SavedCardController::class)->only(['index', 'store', 'destroy', 'update']);
    Route::put('/saved-cards/{id}/default', [SavedCardController::class, 'setDefault'])->name('saved-cards.set-default');
});
