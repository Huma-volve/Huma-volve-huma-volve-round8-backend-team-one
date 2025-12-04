<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileAccountRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileAccountController extends Controller
{
    public function editProfile(ProfileAccountRequest $request){

        $user = User::with('patientProfile')->find(Auth::id());
        $user->update($request->only(['name','email','phone']));

        $user->patientProfile()->updateOrCreate(
                ['user_id'   => $user->id],
                ['birthdate' => $request->birthdate]
            );

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile updated successfully',
        ]);
    }
}
