<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockedUserLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_user_cannot_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'is_blocked' => true,
            'phone' => '01012345678', // Valid Egyptian phone
        ]);

        $response = $this->postJson('/api/auth/login', [
            'phone' => $user->phone,
            'password' => 'password',
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Your account is blocked by admin');
    }

    public function test_active_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'is_blocked' => false,
            'phone' => '01112345678', // Valid Egyptian phone
            'phone_verified_at' => now(), // ensure phone is verified so we don't hit that check
        ]);

        $response = $this->postJson('/api/auth/login', [
            'phone' => $user->phone,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }
}
