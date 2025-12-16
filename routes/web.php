<?php

use App\Http\Controllers\Admin\SupportContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Doctor\AvailabilityController;
use App\Http\Controllers\Doctor\ChatController;
use App\Http\Controllers\Doctor\DoctorBookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Doctor\ReviewController;
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
| Doctor Routes (Chat System & Booking System)
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

        // Booking Management
        Route::prefix('bookings')->name('bookings.')->controller(DoctorBookingController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{booking}', 'show')->name('show');
            Route::post('/{booking}/cancel', 'cancel')->name('cancel');
            Route::post('/{booking}/reschedule', 'reschedule')->name('reschedule');
        });

        // Availability Management
        Route::prefix('availability')->name('availability.')->controller(AvailabilityController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::delete('/{schedule}', 'destroy')->name('destroy');
        });

        // Patient Management
        Route::resource('patients', \App\Http\Controllers\Doctor\DoctorPatientController::class);

        // Reports & Earnings
        Route::get('/reports', [\App\Http\Controllers\Doctor\DoctorReportController::class, 'index'])->name('reports.index');

        //Reviews
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');
        Route::post('/reviews/{review}/reply', [ReviewController::class, 'saveReply'])->name('reviews.saveReply');

        // // Settings
        // Route::get('/settings', [\App\Http\Controllers\Doctor\DoctorSettingController::class, 'edit'])->name('settings.edit');
        // Route::put('/settings', [\App\Http\Controllers\Doctor\DoctorSettingController::class, 'update'])->name('settings.update');
    });

/*
|--------------------------------------------------------------------------
| Admin Content Management Routes (Policies & FAQs)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

        // 1. Policies Management
        Route::prefix('policies')->name('policies.')->group(function () {
            Route::get('/', [SupportContentController::class, 'indexPolicies'])->name('index');
            Route::post('/', [SupportContentController::class, 'storePolicy'])->name('store');
            Route::put('/{slug}', [SupportContentController::class, 'updatePolicy'])->name('update');
            Route::delete('/{slug}', [SupportContentController::class, 'destroyPolicy'])->name('destroy');
        });

        // 2. FAQs Management
        Route::prefix('faqs')->name('faqs.')->group(function () {
            Route::get('/', [SupportContentController::class, 'indexFaqs'])->name('index');
            Route::post('/', [SupportContentController::class, 'storeFaq'])->name('store');
            Route::put('/{id}', [SupportContentController::class, 'updateFaq'])->name('update');
            Route::delete('/{id}', [SupportContentController::class, 'destroyFaq'])->name('destroy');

            // AJAX Route for Drag & Drop
            Route::post('/reorder', [SupportContentController::class, 'reorderFaqs'])->name('reorder');
        });
        // 3. Patient Management
        Route::prefix('patients')->name('patients.')->controller(\App\Http\Controllers\Admin\AdminPatientController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{patient}', 'show')->name('show');
            Route::post('/{patient}/toggle-block', 'toggleBlock')->name('toggle-block');
        });

        // 4. Booking Management
        Route::prefix('bookings')->name('bookings.')->controller(\App\Http\Controllers\Admin\AdminBookingController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{booking}', 'show')->name('show');
        });

        // Doctors Management
        Route::prefix('doctors')->name('doctors.')->controller(DoctorController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit'); // Added edit route
            Route::put('/{id}', 'update')->name('update'); // Added update route
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
    });

require __DIR__ . '/auth.php';
