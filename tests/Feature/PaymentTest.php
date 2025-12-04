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
}
