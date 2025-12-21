<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use App\Models\User;
use App\Traits\ApiResponse;

class GoogleRegisterController extends Controller
{
    use ApiResponse;
    public function googleRegister(Request $request)
    {
        $request->validate([
            'id_token' => 'required'
        ]);

        $clientId = config('services.google.client_id');

        try {
            $client = new GoogleClient(['client_id' => $clientId]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return $this->fail('Invalid Google token', 400);
            }

            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'] ?? null;

            // Check if user already exists
            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                // If not found by google_id, try by email
                $user = User::where('email', $email)->first();

                if ($user) {
                    // Update existing user with google_id
                    $user->update(['google_id' => $googleId]);
                } else {
                    // Create new user
                    $user = User::create([
                        'google_id' => $googleId,
                        'name' => $name,
                        'email' => $email,
                        'password' => bcrypt(str()->random(20)),
                        'email_verified_at' => now(), // Auto verify
                    ]);
                }
            }

            $user->tokens()->delete();
            $token = $user->createToken('authToken')->plainTextToken;

            return $this->success([
                'token' => $token
            ], 'You are logged in successfully', 200);

        } catch (\Exception $e) {
            return $this->fail('Google Registration Failed: ' . $e->getMessage(), 500);
        }
    }
}

