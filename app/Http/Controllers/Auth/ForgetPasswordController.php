<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Repositories\VerificationCodeRepository;
use Illuminate\Support\Facades\DB;
use App\Services\SendSMSService;
use App\Models\User;

class ForgetPasswordController extends Controller
{
    public function __construct(protected VerificationCodeRepository $repo , protected SendSMSService $send)
    {
    }

    public function forgetPassword(ForgetPasswordRequest $request){

        $user = User::where('phone',$request->phone)->first();
        $otp = random_int(1000, 9999);

        if(!$user->phone_verified_at){
            $this->repo->deleteOld($request->phone);
            $this->repo->createOtp($request->phone, $otp);

            // send sms
            $message = $this->send->SendSMS($request->phone , $otp);

            if($message->getStatus() == 0){
                return response()->json([
                    'status'  => 'fail',
                    'message' => 'Your account is not verified, OTP sent for verification'
                ],201);
            }else{
                return response()->json([
                    'status'  => 'fail',
                    'message' => "The message failed with status: " . $message->getStatus() . "\n"
                ],201);
            }
        }

        DB::table('password_reset_tokens')->updateOrInsert(
            ['phone' => $request->phone],
            ['token' => $otp, 'created_at' => now()]
        );
            // send sms
            $message = $this->send->SendSMS($request->phone , $otp);

            if($message->getStatus() == 0){
                return response()->json([
                    'status'  => 'success',
                    'message' => 'OTP is sent for your phone number for reseting password'
                ]);
            }else{
                return response()->json([
                    'status'  => 'fail',
                    'message' => "The message failed with status: " . $message->getStatus() . "\n"
                ]);
            }

    }
}
