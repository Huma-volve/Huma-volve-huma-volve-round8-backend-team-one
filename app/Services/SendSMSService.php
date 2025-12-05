<?php

namespace App\Services;

use Twilio\Rest\Client;

class SendSMSService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }

    public function sendSms($to, $message)
    {
        return $this->client->messages->create($to, [
            'from' => env('TWILIO_FROM'),
            'body' => $message
        ]);
    }
}
