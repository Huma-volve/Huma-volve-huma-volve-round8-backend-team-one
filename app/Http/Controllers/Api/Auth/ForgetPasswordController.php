<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ForgetPasswordRequest;
use App\Repositories\VerificationCodeRepository;
use App\Models\User;
use App\Traits\ApiResponse;

class ForgetPasswordController extends Controller
{
    use ApiResponse;
    public function __construct(protected VerificationCodeRepository $repo)
    {
    }

    public function forgetPassword(ForgetPasswordRequest $request){

        $user = User::where('phone',$request->phone)->first();
        $otp = 1234;

        if(!$user->phone_verified_at){
            $this->repo->deleteOld($request->phone);
            $this->repo->createOtp($request->phone, $otp);

            // send sms
            return $this->fail('Your account is not verified, OTP is sent to your phone number for verification',400);
        }

        DB::table('password_reset_tokens')->updateOrInsert(
            ['phone' => $request->phone],
            ['token' => $otp, 'created_at' => now()]
        );
            // send sms
            return $this->success(null,'OTP is sent to your phone number for reseting password');
    }
}
