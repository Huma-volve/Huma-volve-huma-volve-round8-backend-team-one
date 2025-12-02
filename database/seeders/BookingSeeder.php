<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $doctors = DoctorProfile::all();
        $patients = PatientProfile::all();

        foreach (range(1, 30) as $index) {
            $doctor = $doctors->random();
            $patient = $patients->random();
            $status = $faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']);
            $paymentStatus = $status === 'completed' ? 'paid' : ($status === 'cancelled' ? 'refunded' : $faker->randomElement(['paid', 'unpaid']));

            $booking = Booking::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'appointment_date' => $faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
                'appointment_time' => $faker->time('H:i:s'),
                'status' => $status,
                'price_at_booking' => $doctor->session_price,
                'payment_method' => $faker->randomElement(['paypal', 'stripe', 'cash']),
                'payment_status' => $paymentStatus,
                'payment_transaction_id' => $paymentStatus === 'paid' ? $faker->uuid : null,
                'notes' => $faker->optional()->sentence,
                'cancellation_reason' => $status === 'cancelled' ? $faker->sentence : null,
                'cancelled_at' => $status === 'cancelled' ? now() : null,
                'cancelled_by' => $status === 'cancelled' ? $patient->user_id : null,
            ]);

            // Create transaction if paid
            if ($paymentStatus === 'paid') {
                Transaction::create([
                    'booking_id' => $booking->id,
                    'external_id' => $faker->uuid,
                    'amount' => $doctor->session_price,
                    'type' => 'payment',
                    'status' => 'success',
                    'gateway' => $booking->payment_method,
                    'currency' => 'USD',
                    'payload' => json_encode(['transaction_id' => $faker->uuid]),
                ]);
            }
        }
    }
}
