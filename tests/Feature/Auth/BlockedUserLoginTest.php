<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockedUserLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_doctor_cannot_login()
    {
        $doctor = User::factory()->create([
            'user_type' => 'doctor',
            'is_blocked' => true,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $doctor->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'Your account has been suspended. Please contact support.']);
        $this->assertGuest();
    }

    public function test_active_doctor_can_login()
    {
        $doctor = User::factory()->create([
            'user_type' => 'doctor',
            'is_blocked' => false,
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $doctor->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($doctor);
    }
}
