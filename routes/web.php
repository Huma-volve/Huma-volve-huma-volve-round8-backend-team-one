<?php

use App\Http\Controllers\Admin\SupportContentController;
use App\Http\Controllers\Doctor\ChatController;
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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
| Doctor Routes (Chat System)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{conversation}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/chat/{conversation}/send', [ChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/chat/{conversation}/mark-read', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
        Route::post('/chat/{conversation}/toggle-favorite', [ChatController::class, 'toggleFavorite'])->name('chat.toggle-favorite');
        Route::post('/chat/{conversation}/toggle-archive', [ChatController::class, 'toggleArchive'])->name('chat.toggle-archive');
    });

/*
|--------------------------------------------------------------------------
| Admin Content Management Routes (Policies & FAQs)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // 1. Policies Management
        Route::prefix('policies')->name('policies.')->group(function () {
            Route::get('/', [SupportContentController::class, 'indexPolicies'])->name('index');
            Route::put('/{slug}', [SupportContentController::class, 'updatePolicy'])->name('update');
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
    });

require __DIR__.'/auth.php';