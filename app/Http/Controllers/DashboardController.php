<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        if ($request->user()->user_type === 'admin') {
            // 1. Total Patients & Doctors
            $totalPatients = User::where('user_type', 'patient')->count();
            $totalDoctors = User::where('user_type', 'doctor')->count();

            // 2. Monthly Stats (Bookings & Profit)
            // Group by year and month
            $monthlyStats = Booking::selectRaw('
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
        $ratingAvg = $doctorProfile
            ? Review::where('doctor_id', $doctorProfile->id)
                    ->avg('rating')
            : null;

        return view('doctor.dashboard', compact('bookingsCount', 'ratingAvg'));
    }
}
