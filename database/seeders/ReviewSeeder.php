<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // جلب البوكينجات اللي completed
        $completedBookings = Booking::where('status', 'completed')->get();

        // اختر اتنين منهم عشوائياً
        $bookingsToReview = $completedBookings->random(2);

        foreach ($bookingsToReview as $booking) {
            Review::create([
                'booking_id' => $booking->id,
                'doctor_id' => $booking->doctor_id,
                'patient_id' => $booking->patient_id,
                'rating' => rand(4, 5), // قيمة rating عشوائية
                'comment' => 'Review for booking #' . $booking->id,
            ]);
        }
    }
}
