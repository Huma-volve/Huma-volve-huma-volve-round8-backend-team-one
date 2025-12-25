<?php

namespace Database\Factories;

use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use Faker\Factory as FakerFactory;
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
        $faker = FakerFactory::create();

        return [
            'doctor_id' => DoctorProfile::factory(),
            'patient_id' => PatientProfile::factory(),
            'appointment_date' => $faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'appointment_time' => $faker->time('H:i'),
            'status' => $faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'price_at_booking' => $faker->randomFloat(2, 50, 500),
            'payment_method' => $faker->randomElement(['paypal', 'stripe', 'cash']),
            'payment_status' => $faker->randomElement(['unpaid', 'paid', 'failed', 'refunded']),
            'payment_transaction_id' => $faker->uuid(),
            'notes' => $faker->sentence(),
            'cancellation_reason' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
        ];
    }
}
