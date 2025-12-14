<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        if ($request->user()->user_type === 'admin') {
            // 1. Total Patients & Doctors
            $totalPatients = \App\Models\User::where('user_type', 'patient')->count();
            $totalDoctors = \App\Models\User::where('user_type', 'doctor')->count();

            // 2. Monthly Stats (Bookings & Profit)
            // Group by year and month
            $monthlyStats = \App\Models\Booking::selectRaw('
                    YEAR(appointment_date) as year,
                    MONTH(appointment_date) as month,
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN payment_status = "paid" THEN price_at_booking ELSE 0 END) as net_profit
                ')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            return view('admin.dashboard', compact('totalPatients', 'totalDoctors', 'monthlyStats'));
        }

        $doctorProfile = $request->user()->doctorProfile;
        $bookingsCount = $doctorProfile ? $doctorProfile->bookings()->count() : 0;

        return view('doctor.dashboard', compact('bookingsCount'));
    }
}
