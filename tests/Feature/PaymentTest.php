<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\User;
use App\Services\Payment\PaymentFactory;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_process_payment()
    {
        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        $booking = Booking::factory()->create([
            'doctor_id' => $doctorProfile->id,
            'patient_id' => $patientProfile->id,
            'price_at_booking' => 100.00,
            'payment_status' => 'unpaid',
        ]);

        // Mock Payment Gateway
        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('charge')
            ->once()
            ->andReturn([
                'success' => true,
                'transaction_id' => 'txn_123456',
                'data' => [],
            ]);

        PaymentFactory::mock($mockGateway);

        $response = $this->actingAs($patientUser)->postJson('/api/payments/process', [
            'booking_id' => $booking->id,
            'payment_method_id' => 'pm_card_visa',
            'gateway' => 'stripe',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['transaction_id', 'status']]);

        $this->assertDatabaseHas('transactions', [
            'booking_id' => $booking->id,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        PaymentFactory::clearMock();
    }

    public function test_payment_failure_updates_booking_status()
    {
        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        $booking = Booking::factory()->create([
            'doctor_id' => $doctorProfile->id,
            'patient_id' => $patientProfile->id,
            'price_at_booking' => 100.00,
            'payment_status' => 'unpaid',
        ]);

        // Mock Payment Gateway Failure
        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('charge')
            ->once()
            ->andReturn([
                'success' => false,
                'message' => 'Insufficient funds',
            ]);

        PaymentFactory::mock($mockGateway);

        $response = $this->actingAs($patientUser)->postJson('/api/payments/process', [
            'booking_id' => $booking->id,
            'payment_method_id' => 'pm_card_failure',
            'gateway' => 'stripe',
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Payment failed: Insufficient funds']);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => 'failed',
        ]);

        // Ensure failed transaction is logged
        $this->assertDatabaseHas('transactions', [
            'booking_id' => $booking->id,
            'status' => 'failed',
            'failure_reason' => 'Insufficient funds',
        ]);

        PaymentFactory::clearMock();
    }
    public function test_payment_uses_default_card_if_not_provided()
    {
        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        // Create a default card
        $card = \App\Models\SavedCard::factory()->create([
            'user_id' => $patientUser->id,
            'is_default' => true,
            'provider_token' => 'pm_default_card',
        ]);

        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        $booking = Booking::factory()->create([
            'doctor_id' => $doctorProfile->id,
            'patient_id' => $patientProfile->id,
            'price_at_booking' => 100.00,
            'payment_status' => 'unpaid',
        ]);

        // Mock Payment Gateway
        $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
        $mockGateway->shouldReceive('charge')
            ->once()
            ->with(100.00, 'usd', 'pm_default_card') // Expect default card token
            ->andReturn([
                'success' => true,
                'transaction_id' => 'txn_default',
                'data' => [],
            ]);

        PaymentFactory::mock($mockGateway);

        $response = $this->actingAs($patientUser)->postJson('/api/payments/process', [
            'booking_id' => $booking->id,
            'gateway' => 'stripe',
            // No payment_method_id provided
        ]);

        $response->assertStatus(201);
        PaymentFactory::clearMock();
    }

    public function test_payment_fails_friendly_if_no_card_provided_and_no_default()
    {
        $patientUser = User::factory()->create(['user_type' => 'patient']);
        $patientProfile = PatientProfile::factory()->create(['user_id' => $patientUser->id]);

        // No saved cards for this user

        $doctorUser = User::factory()->create(['user_type' => 'doctor']);
        $doctorProfile = DoctorProfile::factory()->create(['user_id' => $doctorUser->id]);

        $booking = Booking::factory()->create([
            'doctor_id' => $doctorProfile->id,
            'patient_id' => $patientProfile->id,
            'price_at_booking' => 100.00,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($patientUser)->postJson('/api/payments/process', [
            'booking_id' => $booking->id,
            'gateway' => 'stripe',
            // No payment_method_id
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Please add a credit card to proceed.']);
    }
}
