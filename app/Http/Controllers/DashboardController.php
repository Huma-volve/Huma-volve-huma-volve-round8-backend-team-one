<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        if ($request->user()->user_type === 'admin') {
            return view('admin.dashboard');
        }

        if ($request->user()->user_type === 'doctor') {
            $doctorProfile = $request->user()->doctorProfile;
            $bookingsCount = $doctorProfile ? $doctorProfile->bookings()->count() : 0;
            return view('doctor.dashboard', compact('bookingsCount'));
        }

        return view('dashboard');
    }
}
