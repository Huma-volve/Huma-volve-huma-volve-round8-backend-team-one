<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\VerificationCodeRepository;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
// use App\Services\SendSMSService;

class LoginService {
use ApiResponse;
    public function __construct(protected VerificationCodeRepository $repo)
    {
    }


    public function login(string $phone , string $password , $remember_me = "off")
    {
        $user = User::where('phone', $phone)->first();

        if(!$user || !Hash::check($password, $user->password)){
            return $this->fail('Invalid credentials!');
        }

        if(!$user->phone_verified_at){

            $this->repo->deleteOld($phone);
            // $otp = random_int(1000, 9999);
            $otp = 1234;
            $this->repo->createOtp($phone, $otp);

            // send sms
            // $message = $this->send->SendSMS($phone , $otp);
            // if($message->getStatus() == 0){
            return $this->fail('Your account is not verified, OTP sent for verification');
            // }else{
            // $this->fail("The message failed with status: " . $message->getStatus() . "\n","fail",500);
            // }
        }

        $user->tokens()->delete();
        $token = $user->createToken('authToken')->plainTextToken;
        Auth::login($user,$remember_me);
        return $this->success(['token'=>$token],'You are logged in successfully');
    }
}
