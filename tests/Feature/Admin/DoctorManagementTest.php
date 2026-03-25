<?php

namespace Tests\Feature\Admin;

use App\Models\DoctorProfile;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create([
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Needed for doctor creation
        Speciality::factory()->create();
    }

    public function test_admin_can_block_doctor()
    {
        $doctor = User::factory()->create(['user_type' => 'doctor']);
        DoctorProfile::factory()->create(['user_id' => $doctor->id]);

        $this->assertFalse((bool)$doctor->is_blocked);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.doctors.toggle-block', $doctor->id));

        $response->assertRedirect(route('admin.doctors.index'));
        $response->assertSessionHas('success', 'Doctor blocked successfully.');

        $this->assertTrue((bool)$doctor->fresh()->is_blocked);
    }

    public function test_admin_can_unblock_doctor()
    {
        $doctor = User::factory()->create([
            'user_type' => 'doctor',
            'is_blocked' => true
        ]);
        DoctorProfile::factory()->create(['user_id' => $doctor->id]);

        $this->assertTrue((bool)$doctor->is_blocked);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.doctors.toggle-block', $doctor->id));

        $response->assertRedirect(route('admin.doctors.index'));
        $response->assertSessionHas('success', 'Doctor unblocked successfully.');

        $this->assertFalse((bool)$doctor->fresh()->is_blocked);
    }
}
