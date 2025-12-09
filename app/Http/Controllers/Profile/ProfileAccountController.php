<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ProfileAccountRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\ApiResponse;

class ProfileAccountController extends Controller
{
    use ApiResponse;
    public function editProfile(ProfileAccountRequest $request){

        $user = User::with('patientProfile')->find(Auth::id());
        $user->update($request->only(['name','email','phone']));

        $user->patientProfile()->updateOrCreate(
                ['user_id'   => $user->id],
                ['birthdate' => $request->birthdate]
        );

        $user = User::with('patientProfile')->find(Auth::id());
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'birthdate' => $user->patientProfile->birthdate->format('d-m-Y'),
        ];
        return $this->success($data,'Profile updated successfully');
    }
}
