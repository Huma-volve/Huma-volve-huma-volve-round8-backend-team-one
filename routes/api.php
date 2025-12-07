<?php

use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\GetDoctorAvailabilityController;
use App\Http\Controllers\Api\SpecialtyController;
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

// Specialties Routes
Route::get('/specialties', [SpecialtyController::class, 'index']);

// Doctors Routes
Route::apiResource('doctors', DoctorController::class)->only(['index', 'show']);

// Doctor Availability
Route::get('/doctors/{doctor}/availability', GetDoctorAvailabilityController::class);

// Protected Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Toggle favorite
    Route::post('/doctors/{doctor}/favorite', FavoriteController::class);
});
