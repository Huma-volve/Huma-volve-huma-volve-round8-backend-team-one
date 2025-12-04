<?php

use App\Http\Controllers\Auth\OtpLoginController;
use Illuminate\Support\Facades\Route;



Route::post('/login/send-otp',[OtpLoginController::class,'sendOtp']);
Route::post('/login/verify-otp',[OtpLoginController::class,'verifyOtp']);

