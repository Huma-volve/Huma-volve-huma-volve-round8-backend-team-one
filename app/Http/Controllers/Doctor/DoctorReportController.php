<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DoctorReportController extends Controller
{
    public function index()
    {
        $doctorId = Auth::user()->doctorProfile->id;

        // Total Earnings (Success transactions linked to my bookings)
        $totalEarnings = Transaction::whereHas('booking', function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->where('status', 'success')->sum('amount');

        // Earnings by Month (for Chart)
        $monthlyEarnings = Transaction::whereHas('booking', function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })
            ->where('status', 'success')
            ->select(
                DB::raw('sum(amount) as total'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month")
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        // Booking Stats
        $completedBookings = Booking::where('doctor_id', $doctorId)->where('status', 'completed')->count();
        $cancelledBookings = Booking::where('doctor_id', $doctorId)->where('status', 'cancelled')->count();
        $totalBookings = Booking::where('doctor_id', $doctorId)->count();

        return view('doctor.reports.index', compact(
            'totalEarnings',
            'monthlyEarnings',
            'completedBookings',
            'cancelledBookings',
            'totalBookings'
        ));
    }
}
