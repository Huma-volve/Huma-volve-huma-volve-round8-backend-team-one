<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\
{
    Auth,
    Hash
};

class ChangePasswordController extends Controller
{
    use ApiResponse;
    public function changePassword(ChangePasswordRequest $request){

        $user = User::find(Auth::id());
        if(!Hash::check($request->current_password,$user->password)){
            return $this->fail('Your password is incorrect!',"fail",400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();
        return $this->success(null,'Your password is changed successfully',"success",200);
    }
}
