<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\DoctorSchedule;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSlotTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_book_unaligned_slot()
    {
        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        // Create Schedule: 9:00 to 17:00, 30 min slots
        DoctorSchedule::create([
            'doctor_profile_id' => $doctorProfile->id,
            'day_of_week' => now()->addDay()->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'avg_consultation_time' => 30,
        ]);

        // Try booking at 09:15 (Invalid)
        $response = $this->actingAs($patientUser)->postJson('/api/bookings', [
            'doctor_id' => $doctorProfile->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '09:15',
            'payment_method' => 'cash',
            'notes' => 'Test booking',
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Invalid time slot. Appointments must be every 30 minutes starting from 09:00']);

        // Try booking at 09:30 (Valid)
        $responseValid = $this->actingAs($patientUser)->postJson('/api/bookings', [
            'doctor_id' => $doctorProfile->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '09:30',
            'payment_method' => 'cash',
            'notes' => 'Test booking',
        ]);

        $responseValid->assertStatus(201);
    }

    public function test_cannot_update_to_unaligned_slot()
    {
        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        $dateObj = now()->addDays(2);

        DoctorSchedule::create([
            'doctor_profile_id' => $doctorProfile->id,
            'day_of_week' => $dateObj->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'avg_consultation_time' => 30,
        ]);

        // Create initial valid booking at 10:00
        $booking = Booking::create([
            'doctor_id' => $doctorProfile->id,
            'patient_id' => $patientProfile->id,
            'appointment_date' => $dateObj->format('Y-m-d'),
            'appointment_time' => '10:00',
            'status' => 'pending',
            'price_at_booking' => 100,
            'payment_method' => 'cash',
            'payment_status' => 'unpaid',
        ]);

        // Update to 10:15 (Invalid)
        $response = $this->actingAs($patientUser)->putJson("/api/bookings/{$booking->id}", [
            'appointment_time' => '10:15',
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Invalid time slot. Appointments must be every 30 minutes starting from 09:00']);

        // Update to 10:30 (Valid)
        $responseValid = $this->actingAs($patientUser)->putJson("/api/bookings/{$booking->id}", [
            'appointment_time' => '10:30',
        ]);

        $responseValid->assertStatus(200);

        $booking->refresh();
        $this->assertEquals('10:30', $booking->appointment_time->format('H:i'));
    }
}
