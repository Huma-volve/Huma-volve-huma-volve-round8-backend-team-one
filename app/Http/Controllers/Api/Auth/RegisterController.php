<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories\VerificationCodeRepository;
use App\Traits\ApiResponse;
use App\Models\
{
    PatientProfile,
    User
};

class RegisterController extends Controller
{
    use ApiResponse;
    public function __construct(protected VerificationCodeRepository $repo )
    {
    }

    public function Register(RegisterRequest $request){
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone
        ]);

        PatientProfile::create(['user_id' => $user->id ]);

        $otp = 1234;
        $this->repo->deleteOld($request->phone);
        $this->repo->createOtp($request->phone,$otp);

        // send sms
        $user = User::where('phone' , $request->phone)->first();
        $data = [
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => $user->phone
        ];
        return $this->success($data,'Account created. Please verify using the OTP which sent to your phone.',201);
    }
}
