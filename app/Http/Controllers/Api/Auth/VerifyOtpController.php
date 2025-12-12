<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\VerifyOtpRequest;
use App\Services\Auth\VerifyOtpService;

class VerifyOtpController extends Controller
{
    public function __construct(protected VerifyOtpService $service)
    {
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        return $this->service->verifyOtp($request->phone, $request->otp);
    }
}
