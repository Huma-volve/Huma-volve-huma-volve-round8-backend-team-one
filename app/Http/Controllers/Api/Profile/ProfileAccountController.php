<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\ProfileAccountRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\ApiResponse;

class ProfileAccountController extends Controller
{
    use ApiResponse;
    public function editProfile(ProfileAccountRequest $request){
        
        $user = User::with('patientProfile')->find(Auth::id());
        $data = array_filter($request->only(['name','email','phone']), fn($value) => !is_null($value) && $value !== '' );
        $user->update($data);

        $user->patientProfile()->updateOrCreate(
            ['user_id' => $user->id],
            ['birthdate' => $request->birthdate]
        );

        $user = User::with('patientProfile')->find(Auth::id());
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'birthdate' => $request->birthdate ? $user->patientProfile->birthdate->format('d-m-Y') : null,
        ];
        return $this->success($data,'Profile updated successfully');
    }
}
