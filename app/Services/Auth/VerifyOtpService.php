<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\VerificationCodeRepository;

class VerifyOtpService{

    public function __construct(protected VerificationCodeRepository $repo)
    {
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
                'message' => 'Account verified successfully'
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
