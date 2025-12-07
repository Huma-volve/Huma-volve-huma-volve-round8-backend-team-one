<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\PatientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            if (!$doctorProfile) {
                return response()->json(['message' => 'Doctor profile not found'], 404);
            }
            $bookings = Booking::where('doctor_id', $doctorProfile->id)
                ->with(['patient.user', 'doctor.user'])
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get();
        } else {
            $patientProfile = $user->patientProfile;
             if (!$patientProfile) {
                return response()->json(['message' => 'Patient profile not found'], 404);
            }
            $bookings = Booking::where('patient_id', $patientProfile->id)
                ->with(['doctor.user', 'doctor.speciality', 'patient.user'])
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get();
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

        if (!$patientProfile) {
            return response()->json(['message' => 'Only patients can book appointments'], 403);
        }

        $doctor = DoctorProfile::findOrFail($request->doctor_id);

        // Check availability (Basic check, can be expanded)
        // Ideally we should check against AvailabilitySlot model here

        $booking = Booking::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patientProfile->id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending',
            'price_at_booking' => $doctor->session_price ?? 0,
            'payment_method' => $request->payment_method,
            'payment_status' => 'unpaid',
            'notes' => $request->notes,
        ]);

        return new BookingResource($booking->load(['doctor.user', 'patient.user']));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = Booking::with(['doctor.user', 'patient.user'])->findOrFail($id);
        $this->authorizeBookingAccess($booking);

        return new BookingResource($booking);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookingRequest $request, string $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorizeBookingAccess($booking);

        $booking->update($request->validated());

        if ($request->has('appointment_date') || $request->has('appointment_time')) {
             $booking->status = 'rescheduled';
             $booking->save();
        }

        return new BookingResource($booking->load(['doctor.user', 'patient.user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cancel(Request $request, string $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id(),
        ]);

        return new BookingResource($booking->load(['doctor.user', 'patient.user']));
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
}
