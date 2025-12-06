<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Google\Client as GoogleClient;

class GoogleLoginController extends Controller
{
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required'
        ]);

        $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);


        $payload = $client->verifyIdToken($request->id_token);

        if (!$payload) {
            return response()->json(['error' => 'Invalid Google token'], 401);
        }

        $googleId = $payload['sub'];
        $email    = $payload['email'];
        $name     = $payload['name'] ?? '';

        $user = User::firstOrCreate(
            ['google_id' => $googleId],
            [
                'name' => $name,
                'email' => $email,
                'password' => bcrypt(str()->random(20)),
            ]
        );

        $user->tokens()->delete();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Logged in successfully',
            'token' => $token
        ]);
    }
}
