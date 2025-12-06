<?php

use App\Http\Controllers\Auth\
{
    LoginController,
    GoogleLoginController,
    ForgetPasswordController,
    VerifyOtpController,
    RegisterController,
    LogoutController,
    ResetPasswordController,
    GoogleRegisterController
};

use App\Http\Controllers\Profile\
{
    DeleteAccountController,
    ChangePasswordController,
    ProfileAccountController,
    NotificationController,
    PaymentMethodController,
    FavoriteController
};
use Illuminate\Support\Facades\Route;



Route::post('/login',[LoginController::class,'login']);
Route::post('/google-login',[GoogleLoginController::class,'googleLogin']);
Route::post('/verify-otp',[VerifyOtpController::class,'verifyOtp']);
Route::post('/register',[RegisterController::class,'Register']);
Route::post('/google-register',[GoogleRegisterController::class,'googleRegister']);
Route::post('/forget-password',[ForgetPasswordController::class,'forgetPassword']);
Route::post('/reset-password',[ResetPasswordController::class,'resetPassword']);
Route::middleware(['auth:sanctum'])->group(function (){
    Route::prefix('profile')->group(function(){
        Route::post('/logout',[LogoutController::class,'logout']);
        Route::delete('/delete-account',[DeleteAccountController::class,'deleteAccount']);
        Route::post('/change-password',[ChangePasswordController::class,'changePassword']);
        Route::post('/edit-profile',[ProfileAccountController::class,'editProfile']);
        Route::post('/notifications',[NotificationController::class,'toggle']);
        Route::get('/payment-methods',[PaymentMethodController::class,'index']);
        Route::post('/payment-methods',[PaymentMethodController::class,'store']);
        Route::post('/payment-methods/{id}/default',[PaymentMethodController::class,'setDefault']);
        Route::get('/favorites',[FavoriteController::class,'index']);
    });
});


