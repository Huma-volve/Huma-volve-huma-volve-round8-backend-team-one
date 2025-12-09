<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiResponse;
use Google\Client as GoogleClient;

class GoogleLoginController extends Controller
{
    use ApiResponse;
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required'
        ]);

        $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);


        $payload = $client->verifyIdToken($request->id_token);

        if (!$payload) {
            return $this->fail('Invalid Google token',400);
        }

        $googleId = $payload['sub'];
        $email    = $payload['email'];
        $name     = $payload['name'] ?? '';

        $user = User::firstOrCreate(
            ['google_id' => $googleId],
            [
                'name'     => $name,
                'email'    => $email,
                'password' => bcrypt(str()->random(20)),
            ]
        );

        $user->tokens()->delete();
        $token = $user->createToken('authToken')->plainTextToken;
        return $this->success(['token' => $token],'Logged in successfully',200);
    }
}
