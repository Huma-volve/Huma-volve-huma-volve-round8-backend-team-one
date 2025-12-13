<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorBookingController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctorProfile;

        if (!$doctor) {
            abort(403, 'Unauthorized access.');
        }

        $bookings = Booking::where('doctor_id', $doctor->id)
            ->with('patient.user')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10);

        return view('doctor.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Ensure the booking belongs to the logged-in doctor
        if ($booking->doctor_id !== Auth::user()->doctorProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $booking->load(['patient.user', 'transactions', 'cancelledBy']);

        return view('doctor.bookings.show', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->doctor_id !== Auth::user()->doctorProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
        ]);

        return redirect()->route('doctor.bookings.show', $booking)->with('success', 'Booking cancelled successfully.');
    }

    public function reschedule(Request $request, Booking $booking)
    {
        if ($booking->doctor_id !== Auth::user()->doctorProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
        ]);

        // Basic check if slot is available (should be more robust in real app)
        // For now, assuming the doctor selects from available slots shown in UI

        $booking->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'rescheduled', // Or keeping it 'confirmed' or 'pending' depending on logic
            // Reset cancellation info if it was previously cancelled?
            // For now, let's assume we are just moving a valid booking
        ]);

        return redirect()->route('doctor.bookings.show', $booking)->with('success', 'Booking rescheduled successfully.');
    }
}
