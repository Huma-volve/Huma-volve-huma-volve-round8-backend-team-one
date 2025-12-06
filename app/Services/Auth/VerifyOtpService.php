<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\VerificationCodeRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VerifyOtpService
{
    public function __construct(protected VerificationCodeRepository $repo)
    {
    }

    public function verifyOtp(string $phone, string $otp)
    {
        $user    = User::where('phone', $phone)->first();
        $userOtpVerify = $this->repo->getOtp($phone);

        $userOtpResetPassword = DB::table('password_reset_tokens')
                                    ->where('phone', $phone)
                                    ->where('token', $otp)
                                    ->first();

        if (!$user->phone_verified_at) {

            if(
                !$userOtpVerify ||
                $userOtpVerify->code != $otp ||
                now()->greaterThan($userOtpVerify->expires_at)
            ) {

                return [
                    'status'  => 'fail',
                    'message' => 'Invalid or expired OTP'
                ];
            }

            $userOtpVerify->delete();

            $user->update(['phone_verified_at' => now()]);

            return [
                'status' => 'success',
                'message' => 'Account verified successfully'
            ];
        }

        if (
            !$userOtpResetPassword ||
            $userOtpResetPassword->token != $otp
        ) {
            return [
                'status'  => 'fail',
                'message' => 'Invalid or expired OTP'
            ];
        }

        $createdAt = Carbon::parse($userOtpResetPassword->created_at);

        if (now()->greaterThan($createdAt->addMinutes(3))) {
            return [
                'status' => 'fail',
                'message' => 'OTP expired'
            ];
        }

        $user->update(['can_reset_password' => 1]);

        DB::table('password_reset_tokens')
            ->where('phone', $phone)
            ->where('token', $otp)
            ->delete();

        return [
            'status'  => 'success',
            'message' => 'You can reset your password'
        ];
    }
}
