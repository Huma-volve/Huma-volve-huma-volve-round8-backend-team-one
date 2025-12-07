<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_booking_instance()
    {
        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        $booking = Booking::factory()->create([
            'patient_id' => $patientProfile->id,
            'doctor_id' => $doctorProfile->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertEquals('pending', $booking->status);
        $this->assertEquals('unpaid', $booking->payment_status);
        $this->assertEquals($patientProfile->id, $booking->patient->id);
        $this->assertEquals($doctorProfile->id, $booking->doctor->id);
    }
}
