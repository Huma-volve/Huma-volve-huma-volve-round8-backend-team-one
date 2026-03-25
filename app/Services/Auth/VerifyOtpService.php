<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\VerificationCodeRepository;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VerifyOtpService
{
    use ApiResponse;
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
            return $this->fail('Invalid or expired OTP');
            }

            $userOtpVerify->delete();
            $user->update(['phone_verified_at' => now()]);

            return $this->success(null,'Account verified successfully');
        }

        if (
            !$userOtpResetPassword ||
            $userOtpResetPassword->token != $otp
        ) {
            return $this->fail('Invalid or expired OTP');
        }

        $createdAt = Carbon::parse($userOtpResetPassword->created_at);

        if (now()->greaterThan($createdAt->addMinutes(3))) {

            DB::table('password_reset_tokens')
            ->where('phone', $phone)
            ->where('token', $otp)
            ->delete();

            return $this->fail('OTP expired');
        }

        $user->update(['can_reset_password' => 1]);

        DB::table('password_reset_tokens')
            ->where('phone', $phone)
            ->where('token', $otp)
            ->delete();

        return $this->success(null,'You can reset your password now');
    }
}
