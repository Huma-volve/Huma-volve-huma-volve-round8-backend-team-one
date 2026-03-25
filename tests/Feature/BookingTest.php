<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\SavedCard;
use App\Models\User;
use App\Services\Payment\PaymentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        PaymentFactory::clearMock();
        parent::tearDown();
    }

    public function test_patient_can_create_booking()
    {
        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        // Create a saved card for the patient
        SavedCard::factory()->create([
            'user_id' => $patientUser->id,
            'is_default' => true,
        ]);

        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        // Create Schedule for the doctor
        \App\Models\DoctorSchedule::create([
            'doctor_profile_id' => $doctorProfile->id,
            'day_of_week' => now()->addDay()->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'avg_consultation_time' => 30,
        ]);

        // Mock Payment Gateway
        $response = $this->actingAs($patientUser)->postJson('/api/bookings', [
            'doctor_id' => $doctorProfile->id,
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'payment_method' => 'stripe',
            'notes' => 'Test booking',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'status', 'doctor', 'patient']]);

        $this->assertDatabaseHas('bookings', [
            'patient_id' => $patientProfile->id,
            'doctor_id' => $doctorProfile->id,
            'status' => 'pending',
            'payment_status' => 'unpaid', // Expect unpaid now
        ]);
    }

    public function test_cannot_book_taken_slot()
    {
        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        // Create existing booking
        $dateObj = now()->addDays(5);
        $date = $dateObj->format('Y-m-d');

        // Create Schedule for the doctor
        \App\Models\DoctorSchedule::create([
            'doctor_profile_id' => $doctorProfile->id,
            'day_of_week' => $dateObj->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'avg_consultation_time' => 30,
        ]);

        Booking::factory()->create([
            'doctor_id' => $doctorProfile->id,
            'appointment_date' => $date,
            'appointment_time' => '10:00',
            'status' => 'confirmed',
        ]);

        // Try to book same slot
        $response = $this->actingAs($patientUser)->postJson('/api/bookings', [
            'doctor_id' => $doctorProfile->id,
            'appointment_date' => $date,
            'appointment_time' => '10:00',
            'payment_method' => 'stripe',
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'This date and time is already booked. Please choose another slot.']);
    }

    public function test_doctor_can_view_bookings()
    {
        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        Booking::factory()->create([
            'doctor_id' => $doctorProfile->id,
            'patient_id' => $patientProfile->id,
        ]);

        $response = $this->actingAs($doctorUser)->getJson('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
}
