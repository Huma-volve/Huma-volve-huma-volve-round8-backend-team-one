<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Notifications\DoctorNotification;
use App\Notifications\PatientNotification;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $completedBookings = Booking::where('status', 'completed')->get();

        foreach ($completedBookings as $booking) {
            if ($faker->boolean(70)) {
                $review = Review::create([
                    'doctor_id' => $booking->doctor_id,
                    'patient_id' => $booking->patient_id,
                    'booking_id' => $booking->id,
                    'rating' => $faker->numberBetween(1, 5),
                    'comment' => $faker->optional(80)->paragraph,
                    'doctor_response' => null,
                    'responded_at' => null,
                ]);

                // Notification للدكتور
                $booking->doctor->user->notify(new DoctorNotification([
                    'type' => 'New Review',
                    'message' => "You received a new review from {$booking->patient->user->name}.",
                    'review_id' => $review->id,
                ]));

                // 50% chance للرد
                if ($faker->boolean(50)) {
                    $review->update([
                        'doctor_response' => $faker->sentence,
                        'responded_at' => now(),
                    ]);

                    // Notification للبيشنت
                    $booking->patient->user->notify(new PatientNotification([
                        'type' => 'Doctor Reply',
                        'message' => "Your doctor {$booking->doctor->user->name} replied to your review.",
                        'review_id' => $review->id,
                    ]));
                }
            }
        }
    }
}
