<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Profile\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function changePassword(ChangePasswordRequest $request){

        $user = User::find(Auth::id());
        if(!Hash::check($request->current_password,$user->password)){
            return response()->json([
                'status' => 'fail',
                'message' => 'Your password is incorrect!'
            ],422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Your password is changed successfully'
        ],200);
    }
}
