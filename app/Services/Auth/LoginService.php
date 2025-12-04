<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\VerificationCodeRepository;

class LoginService {

    public function __construct(protected VerificationCodeRepository $repo)
    {
    }


    public function sendOtpToUser(string $phone): array
    {
        $user = User::where('phone', $phone)->first();

        $this->repo->deleteOld($phone);
        $otp = random_int(1000, 9999);
        $this->repo->createOtp($phone, $otp);

        // Send SMS
        // SmsService::send($phone, "Your OTP is $otp");

        return [
            'status'  => $user->phone_verified_at ? 'pending' : 'fail',
            'message' => $user->phone_verified_at
                ? 'OTP sent for login'
                : 'Your account is not verified, OTP sent for verification'
        ];
    }


}
