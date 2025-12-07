<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\PatientProfile;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
// use App\Services\SendSMSService;
use App\Repositories\VerificationCodeRepository;
use App\Traits\ApiResponse;

class RegisterController extends Controller
{
    use ApiResponse;
    public function __construct(protected VerificationCodeRepository $repo )
    {
    }


    public function Register(RegisterRequest $request){
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone
        ]);

        PatientProfile::create(['user_id' => $user->id ]);

        // $otp = random_int(1000,9999);
        $otp = 1234;
        $this->repo->deleteOld($request->phone);
        $this->repo->createOtp($request->phone,$otp);

        // send sms
        // $message = $this->send->SendSMS($request->phone , $otp);
        $data = User::where('phone' , $request->phone)->first();
        // if($message->getStatus() == 0){
                return $this->success(new UserResource($data),'Account created. Please verify using the OTP sent to your phone.','success',201);
        // }else{
        // return $this->fail("The message failed with status: " . $message->getStatus() . "\n","fail",500);
        // }
    }
}
