<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Traits\ApiResponse;

class ResetPasswordController extends Controller
{
    use ApiResponse;
    public function resetPassword(ResetPasswordRequest $request){
        $user = User::where('phone' , $request->phone)->first();

        if($user->can_reset_password){
            $user->update(['password' => Hash::make($request->new_password) , 'can_reset_password' => 0]);
            return $this->success(null,'Password updated successfully',200);
        }

        return $this->fail('You can\'t reset your password! , you must verify OTP code ',400);
    }
}
