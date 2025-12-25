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
            'appointment_date' => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'appointment_time' => $this->faker->time('H:i'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'price_at_booking' => $this->faker->randomFloat(2, 50, 500),
            'payment_method' => $this->faker->randomElement(['paypal', 'stripe', 'cash']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'paid', 'failed', 'refunded']),
            'payment_transaction_id' => $this->faker->uuid(),
            'notes' => $this->faker->sentence(),
            'cancellation_reason' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
        ];
    }
}
