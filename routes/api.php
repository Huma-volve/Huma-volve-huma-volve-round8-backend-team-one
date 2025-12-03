<?php

use App\Http\Controllers\Api\DoctorController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Doctors Routes
Route::prefix('doctors')->group(function () {
    Route::get('/', [DoctorController::class, 'index']);
    Route::get('/specialties', [\App\Http\Controllers\Api\SpecialtyController::class, 'index']);
    Route::get('/{id}', [DoctorController::class, 'show']);
    Route::get('/{id}/availability', [DoctorController::class, 'availability']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{id}/favorite', [DoctorController::class, 'toggleFavorite']);
    });
});
