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
        'id_token' => 'required|string'
    ]);

    try {
        $client = new GoogleClient([
            'client_id' => config('services.google.client_id'),
        ]);

        $payload = $client->verifyIdToken(trim($request->id_token));

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
