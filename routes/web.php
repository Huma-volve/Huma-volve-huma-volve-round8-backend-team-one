<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\ChatController;
use App\Http\Controllers\Doctor\DoctorBookingController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:web'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/{conversation}/mark-read', [ChatController::class, 'markAsRead'])->name('chat.mark-read');

    // Booking Routes
    Route::get('/bookings', [DoctorBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [DoctorBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [DoctorBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/reschedule', [DoctorBookingController::class, 'reschedule'])->name('bookings.reschedule');
});

// الراوت ده للتجربة بس  يا شباب علشان اللي حابب يشوف شكل التصميم
Route::get('test-design', function () {
    return view('test-design');
});
