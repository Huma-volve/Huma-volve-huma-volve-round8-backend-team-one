<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // 1. Overview Stats
        $doctorsCount = User::where('user_type', 'doctor')->count();
        $patientsCount = User::where('user_type', 'patient')->count();
        $bookingsCount = Booking::count();

        // Assuming 'status' = 'completed' or similar for revenue. 
        // If Transaction model exists, use it.
        $revenue = Transaction::where('status', 'success')->sum('amount');

        // 2. Recent Activity - New Doctors
        $newDoctors = User::where('user_type', 'doctor')
            ->with('doctorProfile')
            ->latest()
            ->take(5)
            ->get();

        // 3. Recent Activity - Latest Bookings
        $recentBookings = Booking::with(['doctor.user', 'patient.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'doctorsCount',
            'patientsCount',
            'bookingsCount',
            'revenue',
            'newDoctors',
            'recentBookings'
        ));
    }
}
