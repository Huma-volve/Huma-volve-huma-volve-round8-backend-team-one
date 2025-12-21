<?php

namespace App\Http\Controllers\Api\Auth;

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

        $clientId = config('services.google.client_id');

        try {
            $client = new GoogleClient(['client_id' => $clientId]);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return $this->fail('Invalid Google token', 400);
            }

            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'] ?? '';

            // enhanced user lookup logic
            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                // If not found by google_id, try by email
                $user = User::where('email', $email)->first();

                if ($user) {
                    // Link existing user to Google
                    $user->update(['google_id' => $googleId]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'google_id' => $googleId,
                        'password' => bcrypt(str()->random(20)),
                        'email_verified_at' => now(), // Auto verify email from Google
                    ]);
                }
            }

            $user->tokens()->delete();
            $token = $user->createToken('authToken')->plainTextToken;

            return $this->success([
                'token' => $token
            ], 'You are logged in successfully', 200);

        } catch (\Exception $e) {
            return $this->fail('Google Login Failed: ' . $e->getMessage(), 500);
        }
    }
}
