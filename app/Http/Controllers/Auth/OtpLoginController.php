<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Login\
{
    SendOtpRequest,
    VerifyOtpRequest
};
use App\Services\Auth\LoginOtpService;



class OtpLoginController extends Controller
{

    public function __construct(protected LoginOtpService $service)
    {
    }

    public function sendOtp(SendOtpRequest $request)
    {
        return response()->json(
            $this->service->sendOtpToUser($request->phone)
        );
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        return response()->json(
            $this->service->verifyOtp($request->phone, $request->otp)
        );
    }
}
