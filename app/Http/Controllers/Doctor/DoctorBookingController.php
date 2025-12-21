<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorBookingController extends Controller
{
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctorProfile;

        if (! $doctor) {
            abort(403, 'Unauthorized access.');
        }

        $query = Booking::where('doctor_id', $doctor->id)
            ->with('patient.user');

        // Search by patient name or date
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient.user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('appointment_date', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Payment Status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by Date Range
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('appointment_date', now());
                    break;
                case 'week':
                    $query->whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('appointment_date', now()->month)
                        ->whereYear('appointment_date', now()->year);
                    break;
                case 'upcoming':
                    $query->where('appointment_date', '>=', now()->toDateString());
                    break;
                case 'past':
                    $query->where('appointment_date', '<', now()->toDateString());
                    break;
            }
        }

        $bookings = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('doctor.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Ensure the booking belongs to the logged-in doctor
        if ($booking->doctor_id !== Auth::user()->doctorProfile->id) {
            abort(403, 'Unauthorized access.');
        }

        $booking->load(['patient.user', 'transactions', 'cancelledBy']);

        $slots = Auth::user()->doctorProfile->getUpcomingSlots();

        // Filter out today's slots and group by date
        $groupedSlots = $slots->filter(function ($slot) {
            return $slot['date'] !== now()->toDateString();
        })->groupBy('date');

        return view('doctor.bookings.show', compact('booking', 'groupedSlots'));
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
            'reschedule_note' => 'nullable|string|max:500',
        ]);

        // Basic check if slot is available (should be more robust in real app)
        // For now, assuming the doctor selects from available slots shown in UI

        $newNotes = $booking->notes;
        if ($request->filled('reschedule_note')) {
            $doctorName = Auth::user()->name;
            $note = "Rescheduled by Dr. {$doctorName}: ".$request->reschedule_note.' (Date: '.now()->format('Y-m-d').')';
            $newNotes = $newNotes ? $newNotes."\n\n".$note : $note;
        }

        $booking->update([
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'rescheduled', // Or keeping it 'confirmed' or 'pending' depending on logic
            'notes' => $newNotes,
            // Reset cancellation info if it was previously cancelled?
            // For now, let's assume we are just moving a valid booking
        ]);

        return redirect()->route('doctor.bookings.show', $booking)->with('success', 'Booking rescheduled successfully.');
    }
}
