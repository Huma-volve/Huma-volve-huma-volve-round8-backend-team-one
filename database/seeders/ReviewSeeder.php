<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // جلب البوكينجز رقم 5 و 6 فقط لو حالتهم completed
        $bookingsToReview = Booking::whereIn('id', [5, 6])
            ->where('status', 'completed')
            ->get();

        foreach ($bookingsToReview as $booking) {
            Review::create([
                'booking_id' => $booking->id,
                'doctor_id' => $booking->doctor_id,
                'patient_id' => $booking->patient_id,
                'rating' => rand(4, 5),
                'comment' => 'Review for booking #' . $booking->id,
            ]);
        }
    }
}
