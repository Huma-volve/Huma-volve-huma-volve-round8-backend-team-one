<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\ProfileAccountRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\
{
    Auth,
    Storage
};
use App\Models\User;
use App\Traits\ApiResponse;
use Carbon\Carbon;

class ProfileAccountController extends Controller
{
    use ApiResponse;

    public function showProfile(){
        $user = User::with('patientProfile')->find(Auth::id());
        return $this->success( new UserResource($user),'Success');
    }

    public function editProfile(ProfileAccountRequest $request){

        $user = User::with('patientProfile')->find(Auth::id());
        $data = array_filter($request->only(['name','email','phone','address']), fn($value) => !is_null($value) && $value !== '' );

        if(is_null($user->patientProfile->birthdate)){
            if (is_null($request->birthDay)&&is_null($request->birthMonth)&&is_null($request->birthYear)) {
                $birthdate = null;
            }else if(!checkdate($request->birthMonth,$request->birthDay,$request->birthYear)){
                    return $this->fail('Birthdate is invalid format!');
            }else{
                $birthdate = Carbon::createFromDate(
                                        $request->birthYear,
                                        $request->birthMonth,
                                        $request->birthDay
                                    )->format('Y-m-d');
            }
        }else{
            $birthdate_factor = Carbon::parse($user->patientProfile->birthdate);
            $birthdate = Carbon::createFromDate(
                                $request->birthYear ?? $birthdate_factor->year,
                                $request->birthMonth ?? $birthdate_factor->month,
                                $request->birthDay ?? $birthdate_factor->day
                            )->format('Y-m-d');
        }


        if($request->hasFile('image')){

            $path = $request->file('image')->store('profile-photos','public');

            if($user->profile_photo_path){
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->update([
                'profile_photo_path' => $path,
            ]);
        }

        $user->update($data);
        $user->patientProfile()->update(
            [
                'birthdate' => $birthdate
            ]
        );

        $user = User::with('patientProfile')->find(Auth::id());
        $birthdate_factor = $user->patientProfile->birthdate
                            ? Carbon::parse($user->patientProfile->birthdate)
                            : null;
        $data = [
            'name'      => $user->name,
            'email'     => $user->email,
            'phone'     => $user->phone,
            'image'     => $user->profile_photo_path,
            'address'   => $user->address,
            'birthdate' => [
                'Day'   => $birthdate_factor ? $birthdate_factor->day   : null,
                'Month' => $birthdate_factor ? $birthdate_factor->month : null,
                'Year'  => $birthdate_factor ? $birthdate_factor->year  : null,
            ],
        ];
        return $this->success($data,'Profile updated successfully');
    }
}
