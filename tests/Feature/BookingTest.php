<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\User;
use App\Models\SavedCard;
use App\Services\Payment\PaymentFactory;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        // Mock Payment Gateway
        $mockGateway = \Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('charge')
            ->once()
            ->andReturn([
                'success' => true,
                'transaction_id' => 'txn_test_12345',
                'data' => ['id' => 'ch_test_12345', 'amount' => 1000],
            ]);

        PaymentFactory::mock($mockGateway);

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
            'payment_status' => 'paid',
            'payment_transaction_id' => 'txn_test_12345',
        ]);
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
