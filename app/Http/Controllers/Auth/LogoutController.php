<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    use ApiResponse;
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->remember_token = null ;
        $user->save();

        $request->user()->currentAccessToken()->delete();
        return $this->success(null,'Logged out successfully',200);
    }
}
