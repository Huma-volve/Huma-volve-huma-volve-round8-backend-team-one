<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\VerificationCodeRepository;
use Illuminate\Support\Facades\Hash;
use App\Services\SendSMSService;

class LoginService {

    public function __construct(protected VerificationCodeRepository $repo)
    {
    }


    public function login(string $phone , string $password): array
    {
        $user = User::where('phone', $phone)->first();

        if(!$user || !Hash::check($password, $user->password)){
            return [
                'status'  => 'fail',
                'message' => 'Invalid credentials!'
            ];
        }

        if(!$user->phone_verified_at){

            $this->repo->deleteOld($phone);
            $otp = random_int(1000, 9999);
            $this->repo->createOtp($phone, $otp);

            // send sms
            app(SendSMSService::class)->sendSms($phone, "Your verification code is: $otp");

                return [
                    'status'  =>'fail',
                    'message' => 'Your account is not verified, OTP sent for verification'
                ];
        }

        $user->tokens()->delete();
        $token = $user->createToken('authToken', ['*'], now()->addDay())->plainTextToken;

        return [
            'status'  => 'success',
            'message' => 'Logged in successfully',
            'token'   => $token
        ];


    }


}
