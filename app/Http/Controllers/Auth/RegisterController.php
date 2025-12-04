<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\PatientProfile;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Repositories\VerificationCodeRepository;

class RegisterController extends Controller
{
    public function __construct(protected VerificationCodeRepository $repo)
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

        $otp = random_int(1000,9999);
        $this->repo->deleteOld($request->phone);
        $this->repo->createOtp($request->phone,$otp);

        // send sms

        return response()->json([
            'status'  => 'success',
            'message' => 'Account created. Please verify using the OTP sent to your phone.'
        ],201);
    }
}
