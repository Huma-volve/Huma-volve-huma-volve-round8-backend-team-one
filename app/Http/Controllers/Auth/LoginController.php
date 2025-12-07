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

    public function login(LoginRequest $request)
    {
        return $this->service->login($request->phone , $request->password , $request->remember_me);
    }


}
