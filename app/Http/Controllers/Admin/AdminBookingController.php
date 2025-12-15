<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with(['patient', 'doctor.user']);

        // Search by Patient Name
        if ($request->filled('search')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            });
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Date
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('appointment_date', Carbon::today());
                    break;
                case 'week':
                    $query->whereBetween('appointment_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('appointment_date', Carbon::now()->month)
                        ->whereYear('appointment_date', Carbon::now()->year);
                    break;
            }
        }

        // Filter by Doctor
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $bookings = $query->latest()->paginate(12);

        // Get doctors for filter dropdown
        $doctors = User::where('user_type', 'doctor')->get();

        return view('admin.bookings.index', compact('bookings', 'doctors'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['patient.user', 'doctor.user', 'transactions']);

        return view('admin.bookings.show', compact('booking'));
    }
}
