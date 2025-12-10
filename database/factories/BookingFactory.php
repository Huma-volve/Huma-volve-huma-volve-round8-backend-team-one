<?php

namespace Database\Factories;

use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => DoctorProfile::factory(),
            'patient_id' => PatientProfile::factory(),
            'appointment_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'appointment_time' => fake()->time('H:i'),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'price_at_booking' => fake()->randomFloat(2, 50, 500),
            'payment_method' => fake()->randomElement(['paypal', 'stripe', 'cash']),
            'payment_status' => fake()->randomElement(['unpaid', 'paid', 'failed', 'refunded']),
            'payment_transaction_id' => fake()->uuid(),
            'notes' => fake()->sentence(),
            'cancellation_reason' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
        ];
    }
}
