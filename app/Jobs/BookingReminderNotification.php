<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Booking;
use App\Notifications\DoctorNotification;
use App\Notifications\PatientNotification;
use Carbon\Carbon;

class BookingReminderNotification implements ShouldQueue
{
    use Queueable;

    public function handle(): void
{
    $now = Carbon::now();

    $tomorrow = $now->copy()->addDay();

    $bookings = Booking::where('status', 'confirmed')
        ->whereDate('appointment_date', $tomorrow->format('Y-m-d'))
        ->where('reminder_sent', false)
        ->get();

    foreach ($bookings as $booking) {
        $booking->patient->user->notify(new PatientNotification([
            'type' => 'Booking Reminder',
            'message' => "Reminder: Your booking with Dr. {$booking->doctor->user->name} is tomorrow at {$booking->appointment_time}.",
            'booking_id' => $booking->id,
        ]));
        $booking->doctor->user->notify(new DoctorNotification([
                'type' => 'Booking Reminder',
                'message' => "Reminder: You have a booking with {$booking->patient->user->name} tomorrow at {$booking->appointment_time}.",
                'booking_id' => $booking->id,
            ]));
        $booking->update(['reminder_sent' => true]);
    }
}
}
