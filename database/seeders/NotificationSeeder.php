<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Notifications\DoctorNotification;
use App\Notifications\PatientNotification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $patients = User::where('user_type', 'patient')->get();
        $doctors = User::where('user_type', 'doctor')->get();

        // -------------------------------
        // 1. Notifications للدكتور لما مريض يكتب تقييم
        // -------------------------------
        $completedBookings = Booking::where('status', 'completed')->get();

        foreach ($completedBookings as $booking) {
            if ($faker->boolean(70)) { // 70% chance patient leaves a review
                $review = Review::create([
                    'doctor_id' => $booking->doctor_id,
                    'patient_id' => $booking->patient_id,
                    'booking_id' => $booking->id,
                    'rating' => $faker->numberBetween(1, 5),
                    'comment' => $faker->optional(80)->paragraph,
                    // أحيانًا نضيف doctor_response حتى يكون realistic
                    'doctor_response' => $faker->optional(30)->paragraph,
                    'responded_at' => $faker->optional(30)->dateTimeBetween('-7 days', 'now'),
                ]);

                $doctor = $booking->doctor->user;

                // Notification للدكتور
                $doctor->notify(new DoctorNotification([
                    'type' => 'New Review',
                    'message' => "You received a new review from {$booking->patient->user->name}.",
                    'review_id' => $review->id,
                ]));
            }
        }

        // -------------------------------
        // 2. Notifications للمرضى
        // -------------------------------
        foreach ($patients as $patient) {
            // Reminder لحجوزات قريبة
            $upcomingBookings = Booking::where('patient_id', $patient->id)
                                       ->where('status', 'confirmed')
                                       ->whereDate('appointment_date', '<=', now()->addDays(2))
                                       ->get();
            foreach ($upcomingBookings as $upcomingBooking) {
                $patient->notify(new PatientNotification([
                    'type' => 'Upcoming Booking',
                    'message' => "Reminder: You have an upcoming appointment with Dr. {$upcomingBooking->doctor->user->name} on {$upcomingBooking->appointment_date} at {$upcomingBooking->appointment_time}.",
                    'booking_id' => $upcomingBooking->id,
                ]));
            }

            // Doctor replies على كل reviews للـpatient
            $reviewsWithResponse = Review::where('patient_id', $patient->id)
                                         ->whereNotNull('doctor_response')
                                         ->get();
            foreach ($reviewsWithResponse as $review) {
                $patient->notify(new PatientNotification([
                    'type' => 'Doctor Reply',
                    'message' => "Dr. {$review->doctor->user->name} replied to your review.",
                    'review_id' => $review->id,
                ]));
            }
        }

        $this->command->info('Notifications seeder executed successfully!');
    }
}
