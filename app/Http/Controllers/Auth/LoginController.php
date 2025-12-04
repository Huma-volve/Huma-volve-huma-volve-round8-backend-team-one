<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\LoginService;



class LoginController extends Controller
{

    public function __construct(protected LoginService $service)
    {
    }

    public function sendOtp(LoginRequest $request)
    {
        return response()->json(
            $this->service->sendOtpToUser($request->phone)
        );
    }


}
