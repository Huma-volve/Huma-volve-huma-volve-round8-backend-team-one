<?php

use App\Http\Controllers\Auth\
{
    LoginController,
    VerifyOtpController,
    RegisterController
};
use Illuminate\Support\Facades\Route;



Route::post('/login',[LoginController::class,'login']);
Route::post('/verify-otp',[VerifyOtpController::class,'verifyOtp']);
Route::post('/register',[RegisterController::class,'Register']);


