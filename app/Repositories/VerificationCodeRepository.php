<?php

namespace App\Repositories;

use App\Models\VerificationCode;

class VerificationCodeRepository {

    public function deleteOld(string $phone, string $type = 'phone')
    {
        VerificationCode::where('identifier', $phone)
            ->where('type', $type)
            ->delete();
    }

    public function createOtp(string $phone, int $otp, string $type = 'phone'): VerificationCode
    {
        return VerificationCode::create([
            'identifier' => $phone,
            'code'       => $otp,
            'type'       => $type,
            'expires_at' => now()->addMinutes(3),
        ]);
    }

    public function getOtp(string $phone, string $type = 'phone')
    {
        return VerificationCode::where('identifier', $phone)
            ->where('type', $type)
            ->first();
    }
}
