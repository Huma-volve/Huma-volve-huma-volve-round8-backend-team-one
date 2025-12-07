<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'doctor_id' => DoctorProfile::factory(),
            'patient_id' => PatientProfile::factory(),
            'appointment_date' => $this->faker->date(),
            'appointment_time' => $this->faker->time('H:i'),
            'status' => 'pending',
            'price_at_booking' => $this->faker->randomFloat(2, 50, 500),
            'payment_method' => 'stripe',
            'payment_status' => 'unpaid',
            'notes' => $this->faker->sentence,
        ];
    }
}
