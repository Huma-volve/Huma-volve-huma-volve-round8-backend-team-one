<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use App\Models\User;

class GoogleRegisterController extends Controller
{
    public function googleRegister(Request $request)
    {
        $request->validate([
            'id_token' => 'required'
        ]);

        $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($request->id_token);

        if (!$payload) {
            return response()->json(['error' => 'Invalid Google Token'], 401);
        }


        $googleId = $payload['sub'];
        $email    = $payload['email'];
        $name     = $payload['name'] ?? null;


        $user = User::where('google_id', $googleId)
                    ->orWhere('email', $email)
                    ->first();

        if (!$user) {
            $user = User::create([
                'google_id' => $googleId,
                'name'      => $name,
                'email'     => $email,
                'password'  => bcrypt(str()->random(20)),
            ]);
        } else {
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleId,
                ]);
            }
        }

        $user->tokens()->delete();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Authenticated Successfully',
            'token'   => $token,
        ]);
    }
}

