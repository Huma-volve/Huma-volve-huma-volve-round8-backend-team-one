<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\VerificationCodeRepository;

class LoginOtpService {

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

    public function verifyOtp(string $phone, string $otp): array
    {
        $user = User::where('phone', $phone)->first();
        $userOtp = $this->repo->getOtp($phone);

        if (!$userOtp || $userOtp->code != $otp || now()->greaterThan($userOtp->expires_at)) {
            return [
                'status'  => 'fail',
                'message' => 'Invalid or expired OTP'
            ];
        }

        $userOtp->delete();

        if (!$user->phone_verified_at) {
            $user->update(['phone_verified_at' => now()]);

            return [
                'status' => 'success',
                'message' => 'Phone verified successfully'
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
