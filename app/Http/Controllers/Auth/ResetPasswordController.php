<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function resetPassword(ResetPasswordRequest $request){
        $user = User::where('phone' , $request->phone)->first();

        if($user->can_reset_password){
            $user->update(['password' => Hash::make($request->new_password) , 'can_reset_password' => 0]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully'
            ]);
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'You can\'t reset your password ! '
        ]);
    }
}
