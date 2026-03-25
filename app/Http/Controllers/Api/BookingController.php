<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\DoctorSchedule;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Notifications\DoctorNotification;
use App\Notifications\PatientNotification;
use App\Services\Payment\PaymentFactory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->user_type === 'doctor') {
            $doctorProfile = $user->doctorProfile;
            if (! $doctorProfile) {
                return response()->json(['message' => 'Doctor profile not found'], 404);
            }
            $bookings = Booking::where('doctor_id', $doctorProfile->id)
                ->with(['patient.user', 'doctor.user'])
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get();
        } else {
            $patientProfile = $user->patientProfile;
            if (! $patientProfile) {
                return response()->json(['message' => 'Patient profile not found'], 404);
            }
            $bookings = Booking::where('patient_id', $patientProfile->id)
                ->with(['doctor.user', 'doctor.speciality', 'patient.user'])
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get();
        }
        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found'], 404);
        }

        return BookingResource::collection($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingRequest $request)
    {
        $user = Auth::user();
        $patientProfile = $user->patientProfile;

        if (! $patientProfile) {
            return response()->json(['message' => 'Only patients can book appointments'], 403);
        }

        $doctor = DoctorProfile::findOrFail($request->doctor_id);

        $validity = $this->checkAppointmentValidity($doctor->id, $request->appointment_date, $request->appointment_time);
        if ($validity) {
            return $validity;
        }

        return DB::transaction(function () use ($request, $patientProfile, $doctor) {
            $amount = $doctor->session_price ?? 0;

            // Create booking with unpaid status
            $booking = Booking::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patientProfile->id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $request->appointment_time,
                'status' => 'pending',
                'price_at_booking' => $amount,
                'payment_method' => $request->payment_method, // Store the intended method
                'payment_status' => 'unpaid',                 // Initial status
                'payment_transaction_id' => null,
                'notes' => $request->notes,
            ]);

            // Notification
            $doctorUser = $doctor->user;

            $doctorUser->notify(new DoctorNotification([
                'type' => 'New Booking',
                'message' => "You have a new booking from {$patientProfile->user->name}.",
                'booking_id' => $booking->id,
            ]));

            $admins = User::where('user_type', 'admin')->get();

            Notification::send($admins, new AdminNotification([
                'type' => 'Booking Created',
                'message' => "{$patientProfile->user->name} booked an appointment with Dr. {$doctor->user->name}.",
                'booking_id' => $booking->id,
                'doctor_id' => $doctor->id,
            ]));

            return new BookingResource($booking->load(['doctor.user', 'patient.user']));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = Booking::with(['doctor.user', 'patient.user'])->find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        $this->authorizeBookingAccess($booking);

        return new BookingResource($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookingRequest $request, string $id)
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        $this->authorizeBookingAccess($booking);

        if ($request->has('appointment_date') || $request->has('appointment_time')) {
            $date = $request->input('appointment_date', $booking->appointment_date->format('Y-m-d'));
            $timeInput = $request->input('appointment_time', $booking->appointment_time->format('H:i'));
            $time = substr($timeInput, 0, 5);

            $validity = $this->checkAppointmentValidity($booking->doctor_id, $date, $time, $booking->id);
            if ($validity) {
                return $validity;
            }

            $booking->status = 'rescheduled';
        }

        $booking->update($request->validated());

        return new BookingResource($booking->load(['doctor.user', 'patient.user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cancel(Request $request, string $id)
    {
        $booking = Booking::find($id);
        if (! $booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        // Calculate appointment datetime
        $appointmentDateTime = Carbon::parse($booking->appointment_date)->setTimeFromTimeString(Carbon::parse($booking->appointment_time)->format('H:i:s'));

        if (now()->addHours(24)->gt($appointmentDateTime)) {
            return response()->json(['message' => 'Cancellation must be made at least 24 hours in advance.'], 400);
        }

        return DB::transaction(function () use ($booking, $request) {
            // Check for payment to refund
            if ($booking->payment_status === 'paid' && $booking->payment_transaction_id) {
                if ($booking->payment_method === 'stripe') {
                    $paymentGateway = PaymentFactory::create('stripe');
                    $refundResult = $paymentGateway->refund($booking->payment_transaction_id);

                    if (! $refundResult['success']) {
                        return response()->json(['message' => 'Refund failed: '.($refundResult['message'] ?? 'Unknown error')], 400);
                    }

                    // Record refund transaction
                    Transaction::create([
                        'booking_id' => $booking->id,
                        'external_id' => $refundResult['transaction_id'],
                        'amount' => $booking->price_at_booking,
                        'type' => 'refund',
                        'status' => 'success',
                        'gateway' => $booking->payment_method,
                        'payload' => $refundResult['data'],
                        'currency' => 'egp',
                    ]);

                    $booking->payment_status = 'refunded';
                }
            }

            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
                'cancelled_by' => Auth::id(),
                'payment_status' => $booking->payment_status,
            ]);

            // Notification
            $user = Auth::user();

            if ($user->user_type === 'patient') {

                $doctorUser = $booking->doctor->user;

                $doctorUser->notify(new DoctorNotification([
                    'type' => 'Booking Cancelled',
                    'message' => "The patient {$booking->patient->user->name} cancelled the booking.",
                    'booking_id' => $booking->id,
                ]));

                $admins = User::where('user_type', 'admin')->get();

                Notification::send($admins, new AdminNotification([
                    'type' => 'Booking Cancelled',
                    'message' => "Patient {$booking->patient->user->name} cancelled a booking with Dr. {$booking->doctor->user->name}.",
                    'booking_id' => $booking->id,
                    'cancelled_by' => 'patient',
                ]));
            }

            if ($user->user_type === 'doctor') {

                $patientUser = $booking->patient->user;

                $patientUser->notify(new PatientNotification([
                    'type' => 'Booking Cancelled',
                    'message' => "Dr. {$booking->doctor->user->name} cancelled your booking.",
                    'booking_id' => $booking->id,
                ]));

                $admins = User::where('user_type', 'admin')->get();

                Notification::send($admins, new AdminNotification([
                    'type' => 'Booking Cancelled',
                    'message' => "Dr. {$booking->doctor->user->name} cancelled a booking for patient {$booking->patient->user->name}.",
                    'booking_id' => $booking->id,
                    'cancelled_by' => 'doctor',
                ]));
            }

            return new BookingResource($booking->load(['doctor.user', 'patient.user']));
        });
    }

    private function authorizeBookingAccess(Booking $booking)
    {
        $user = Auth::user();
        if ($user->user_type === 'doctor') {
            if ($booking->doctor_id !== $user->doctorProfile->id) {
                abort(403, 'Unauthorized access to this booking');
            }
        } else {
            if ($booking->patient_id !== $user->patientProfile->id) {
                abort(403, 'Unauthorized access to this booking');
            }
        }
    }

    private function checkAppointmentValidity($doctorId, $date, $time, $ignoreBookingId = null)
    {
        // 1. Check if the time is in the past (for today)
        $appointmentDateTime = Carbon::parse("$date $time");
        if ($appointmentDateTime->isPast()) {
             return response()->json(['message' => 'You cannot book an appointment in the past.'], 400);
        }

        // Check availability: if there is another booking in the same date and time
        $existingBookingQuery = Booking::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereIn('status', ['pending', 'confirmed']);

        if ($ignoreBookingId) {
            $existingBookingQuery->where('id', '!=', $ignoreBookingId);
        }

        if ($existingBookingQuery->exists()) {
            return response()->json(['message' => 'This date and time is already booked. Please choose another slot.'], 400);
        }

        // Validate that the doctor actually works on this day/time
        $appointmentDate = Carbon::parse($date);
        $dayOfWeek = $appointmentDate->dayOfWeek; // 0=Sunday
        $appointmentTime = Carbon::parse($time);

        $schedule = DoctorSchedule::where('doctor_profile_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (! $schedule) {
            return response()->json(['message' => 'The doctor does not work on this day.'], 400);
        }

        $shiftStart = Carbon::parse($schedule->start_time);
        $shiftEnd = Carbon::parse($schedule->end_time);
        $avgConsultationTime = $schedule->avg_consultation_time ?? 30;

        // Normalize dates for comparison
        $checkTime = $appointmentTime->setDate(2000, 1, 1);
        $start = $shiftStart->setDate(2000, 1, 1);
        $end = $shiftEnd->setDate(2000, 1, 1);

        if ($checkTime->lt($start) || $checkTime->gte($end)) {
            return response()->json(['message' => 'The doctor is not available at this time (Outside working hours).'], 400);
        }

        // Check slot alignment
        $minutesFromStart = $start->diffInMinutes($checkTime);
        if ($minutesFromStart % $avgConsultationTime !== 0) {
            return response()->json([
                'message' => "Invalid time slot. Appointments must be every {$avgConsultationTime} minutes starting from ".$start->format('H:i'),
            ], 400);
        }

        return null;
    }
}
