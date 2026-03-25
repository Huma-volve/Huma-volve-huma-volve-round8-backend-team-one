<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Get completed bookings that don't already have reviews
        $bookingsToReview = Booking::where('status', 'completed')
            ->whereDoesntHave('reviews')
            ->take(2)
            ->get();

        foreach ($bookingsToReview as $booking) {
            Review::create([
                'booking_id' => $booking->id,
                'doctor_id' => $booking->doctor_id,
                'patient_id' => $booking->patient_id,
                'rating' => rand(4, 5),
                'comment' => 'Review for booking #'.$booking->id,
            ]);
        }
    }
}
