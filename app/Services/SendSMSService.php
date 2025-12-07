<?php

namespace App\Services;

class SendSMSService
{
    // public function SendSMS($phone ,$otp){
    //     $basic  = new \Vonage\Client\Credentials\Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
    //     $client = new \Vonage\Client($basic);
    //     $response = $client->sms()->send(
    //         new \Vonage\SMS\Message\SMS($phone, env('VONAGE_SENDER'), "Your OTP Code is $otp")
    //     );

    //     $message = $response->current();

    //     return $message;
    // }
}
