<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\Auth\VerifyOtpService;

class VerifyOtpController extends Controller
{
    public function __construct(protected VerifyOtpService $service)
    {
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        return response()->json(
            $this->service->verifyOtp($request->phone, $request->otp)
        );
    }
}
