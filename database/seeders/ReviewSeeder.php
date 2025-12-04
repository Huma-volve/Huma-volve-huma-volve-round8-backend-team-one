<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $completedBookings = Booking::where('status', 'completed')->get();

        foreach ($completedBookings as $booking) {
            // 70% chance to have a review
            if ($faker->boolean(70)) {
                Review::create([
                    'doctor_id' => $booking->doctor_id,
                    'patient_id' => $booking->patient_id,
                    'booking_id' => $booking->id,
                    'rating' => $faker->numberBetween(1, 5),
                    'comment' => $faker->optional(80)->paragraph,
                    'doctor_response' => $faker->optional(30)->paragraph,
                    'responded_at' => $faker->optional(30)->dateTimeBetween('-7 days', 'now'),
                ]);
            }
        }
    }
}
