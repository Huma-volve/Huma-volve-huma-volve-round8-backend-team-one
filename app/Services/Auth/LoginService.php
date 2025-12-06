<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\VerificationCodeRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\SendSMSService;

class LoginService {

    public function __construct(protected VerificationCodeRepository $repo,protected SendSMSService $send)
    {
    }


    public function login(string $phone , string $password , $remember_me = "off"): array
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
            $message = $this->send->SendSMS($phone , $otp);
            if($message->getStatus() == 0){
                return [
                    'status'  => 'fail',
                    'message' => 'Your account is not verified, OTP sent for verification'
                ];
            }else{
                return [
                    'status'  => 'fail',
                    'message' => "The message failed with status: " . $message->getStatus() . "\n"
                ];
            }
        }

        $user->tokens()->delete();
        $token = $user->createToken('authToken')->plainTextToken;
        Auth::login($user,$remember_me);

        return [
            'status'  => 'success',
            'message' => 'Logged in successfully',
            'token'   => $token
        ];
    }
}
