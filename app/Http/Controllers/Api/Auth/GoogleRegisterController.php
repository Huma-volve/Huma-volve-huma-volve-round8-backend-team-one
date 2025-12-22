<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Http;

class GoogleRegisterController extends Controller
{
    use ApiResponse;

    public function googleRegister(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        try {
            // تبادل الـ code مع Google للحصول على id_token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $request->code,
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect_uri'),
                'grant_type' => 'authorization_code',
            ]);

            if (!$response->successful()) {
                return $this->fail('Failed to get token from Google', 400);
            }

            $idToken = $response->json()['id_token'] ?? null;

            if (!$idToken) {
                return $this->fail('No id_token returned by Google', 400);
            }

            // التحقق من الـ id_token باستخدام Google Client
            $client = new GoogleClient(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($idToken);

            if (!$payload) {
                return $this->fail('Invalid Google token', 401);
            }

            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'] ?? null;

            $user = User::where('google_id', $googleId)
                        ->orWhere('email', $email)
                        ->first();

            if (!$user) {
                $user = User::create([
                    'google_id' => $googleId,
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(str()->random(20)),
                    'email_verified_at' => now(),
                ]);
            } elseif (!$user->google_id) {
                $user->update(['google_id' => $googleId]);
            }

            $user->tokens()->delete();
            $token = $user->createToken('authToken')->plainTextToken;

            return $this->success([
                'token' => $token
            ], 'You are logged in successfully');

        } catch (\Throwable $e) {
            return $this->fail(
                'Google Registration Failed: ' . $e->getMessage(),
                500
            );
        }
    }
}

