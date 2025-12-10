<?php

namespace Tests\Feature;

use App\Models\DoctorProfile;
use App\Models\DoctorSchedule;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_doctor_availability()
    {
        // Create Doctor and Schedule
        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $specialty = Speciality::factory()->create();
        $doctorProfile = DoctorProfile::factory()->create([
            'user_id' => $doctorUser->id,
            'specialty_id' => $specialty->id
        ]);

        // Create Schedule for today (or tomorrow to be safe)
        $today = now();
        DoctorSchedule::create([
            'doctor_profile_id' => $doctorProfile->id,
            'day_of_week' => $today->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'avg_consultation_time' => 30,
        ]);

        // Act
        $response = $this->getJson("/api/doctors/{$doctorProfile->id}/availability");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['date', 'start_time', 'end_time', 'day_name']
                ],
                'message'
            ]);

        // Check that we have slots
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals('09:00', $data[0]['start_time']);
    }
}
