<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Repositories\VerificationCodeRepository;
use Illuminate\Support\Facades\DB;
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
        // $otp = random_int(1000, 9999);
        $otp = 1234;

        if(!$user->phone_verified_at){
            $this->repo->deleteOld($request->phone);
            $this->repo->createOtp($request->phone, $otp);

            // send sms
            return $this->fail('Your account is not verified, OTP sent for verification','fail',400);
        }

        DB::table('password_reset_tokens')->updateOrInsert(
            ['phone' => $request->phone],
            ['token' => $otp, 'created_at' => now()]
        );
            // send sms
            return $this->success(['phone' => $request->phone],'OTP is sent for your phone number for reseting password',"success",200);


    }
}
