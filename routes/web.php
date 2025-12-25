<?php

use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\AdminContactMessageController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SupportContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Doctor\AvailabilityController;
use App\Http\Controllers\Doctor\ChatController;
use App\Http\Controllers\Doctor\DoctorBookingController;
use App\Http\Controllers\Doctor\ReviewController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Dashboard Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/legal/{slug}', [SupportContentController::class, 'showPolicy'])->name('policy.show');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {

        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{conversation}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/chat/{conversation}/mark-read', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
        Route::post('/chat/{conversation}/toggle-favorite', [ChatController::class, 'toggleFavorite'])->name('chat.toggle-favorite');
        Route::post('/chat/{conversation}/toggle-archive', [ChatController::class, 'toggleArchive'])->name('chat.toggle-archive');

        Route::prefix('bookings')->name('bookings.')->controller(DoctorBookingController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{booking}', 'show')->name('show');
            Route::post('/{booking}/cancel', 'cancel')->name('cancel');
            Route::post('/{booking}/reschedule', 'reschedule')->name('reschedule');
            Route::post('/{booking}/complete', 'complete')->name('complete');
        });

        Route::prefix('availability')->name('availability.')->controller(AvailabilityController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{schedule}/edit', 'edit')->name('edit');
            Route::put('/{schedule}', 'update')->name('update');
            Route::delete('/{schedule}', 'destroy')->name('destroy');
        });

        Route::resource('patients', \App\Http\Controllers\Doctor\DoctorPatientController::class);

        Route::get('/reports', [\App\Http\Controllers\Doctor\DoctorReportController::class, 'index'])->name('reports.index');

        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
        Route::post('/reviews/{review}/reply', [ReviewController::class, 'saveReply'])->name('reviews.saveReply');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::prefix('policies')->name('policies.')->group(function () {
            Route::get('/', [SupportContentController::class, 'indexPolicies'])->name('index');
            Route::post('/', [SupportContentController::class, 'storePolicy'])->name('store');
            Route::put('/{slug}', [SupportContentController::class, 'updatePolicy'])->name('update');
            Route::delete('/{slug}', [SupportContentController::class, 'destroyPolicy'])->name('destroy');
        });

        Route::prefix('faqs')->name('faqs.')->group(function () {
            Route::get('/', [SupportContentController::class, 'indexFaqs'])->name('index');
            Route::post('/', [SupportContentController::class, 'storeFaq'])->name('store');
            Route::put('/{id}', [SupportContentController::class, 'updateFaq'])->name('update');
            Route::delete('/{id}', [SupportContentController::class, 'destroyFaq'])->name('destroy');
            Route::post('/reorder', [SupportContentController::class, 'reorderFaqs'])->name('reorder');
        });

        Route::prefix('patients')->name('patients.')->controller(\App\Http\Controllers\Admin\AdminPatientController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{patient}', 'show')->name('show');
            Route::post('/{patient}/toggle-block', 'toggleBlock')->name('toggle-block');
        });

        Route::prefix('bookings')->name('bookings.')->controller(\App\Http\Controllers\Admin\AdminBookingController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{booking}', 'show')->name('show');
        });

        Route::prefix('doctors')->name('doctors.')->controller(DoctorController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::post('/{id}/toggle-block', 'toggleBlock')->name('toggle-block');
        });

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // Contact Messages
        Route::prefix('contact-messages')->name('contact-messages.')->controller(AdminContactMessageController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{message}', 'show')->name('show');
            Route::delete('/{message}', 'destroy')->name('destroy');
        });
    });

require __DIR__.'/auth.php';

Route::get('/fix-system', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    \Illuminate\Support\Facades\Artisan::call('package:discover');

    return 'System Fixed & Caches Cleared!';
});
