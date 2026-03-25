<?php

namespace Tests\Feature\Api;

use App\Models\DoctorProfile;
use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SameDayBookingTest extends TestCase
{
    use RefreshDatabase;

    protected $patientUser;
    protected $patientProfile;
    protected $doctorUser;
    protected $doctorProfile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->patientUser = User::factory()->create(['user_type' => 'patient']);
        $this->patientProfile = PatientProfile::factory()->create(['user_id' => $this->patientUser->id]);

        $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $this->doctorProfile = DoctorProfile::factory()->create(['user_id' => $this->doctorUser->id]);
    }

    public function test_can_book_same_day_future_time()
    {
        $now = now();
        // Assume doctor works today
        DoctorSchedule::create([
            'doctor_profile_id' => $this->doctorProfile->id,
            'day_of_week' => $now->dayOfWeek,
            'start_time' => '00:00:00', // All day for simplicity
            'end_time' => '23:59:00',
            'avg_consultation_time' => 30,
        ]);

        $futureTime = $now->copy()->addMinutes(60)->format('H:i'); // 1 hour from now

        // Ensure alignment with 30 min slots
        // This simple calculation might fail if current time is not aligned, but let's try to align it
        // A better approach is to mock 'now' or force a specific time.
        // Let's force "now" to be 10:00 and try to book 11:00

        Carbon::setTestNow(Carbon::parse('2025-01-01 10:00:00'));
        $futureTime = '11:00';

        // Re-create schedule for this "fixed" day
        DoctorSchedule::where('doctor_profile_id', $this->doctorProfile->id)->delete();
         DoctorSchedule::create([
            'doctor_profile_id' => $this->doctorProfile->id,
            'day_of_week' => Carbon::parse('2025-01-01')->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'avg_consultation_time' => 30,
        ]);

        $response = $this->actingAs($this->patientUser)->postJson('/api/bookings', [
            'doctor_id' => $this->doctorProfile->id,
            'appointment_date' => '2025-01-01',
            'appointment_time' => $futureTime,
            'payment_method' => 'cash',
            'notes' => 'Urgent',
        ]);

        $response->assertStatus(201);
    }

    public function test_cannot_book_same_day_past_time()
    {
        Carbon::setTestNow(Carbon::parse('2025-01-01 15:00:00'));

        // Schedule
         DoctorSchedule::create([
            'doctor_profile_id' => $this->doctorProfile->id,
            'day_of_week' => Carbon::parse('2025-01-01')->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'avg_consultation_time' => 30,
        ]);

        // Try to book 10:00 AM (past)
        $response = $this->actingAs($this->patientUser)->postJson('/api/bookings', [
            'doctor_id' => $this->doctorProfile->id,
            'appointment_date' => '2025-01-01',
            'appointment_time' => '10:00',
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'You cannot book an appointment in the past.']);
    }
}
