<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPatientController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('user_type', 'patient');

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $patients = $query->paginate(12);

        return view('admin.patients.index', compact('patients'));
    }

    public function show(User $patient): View
    {
        if ($patient->user_type !== 'patient') {
            abort(404);
        }

        $patient->load(['bookings' => function($query) {
            $query->latest();
        }]);

        // Calculate age requires birthdate. Assuming patient_profiles has birthdate or it's on user.
        // Based on previous knowledge, details might be on patientProfile.
        // Let's check patient_profiles table structure if needed, but for now assuming relation exists.
        $patient->load('patientProfile');

        $bookings = $patient->bookings()->paginate(10);

        return view('admin.patients.show', compact('patient', 'bookings'));
    }

    public function toggleBlock(User $patient)
    {
        $patient->is_blocked = !$patient->is_blocked;
        $patient->save();

        $status = $patient->is_blocked ? 'blocked' : 'unblocked';

        return redirect()->back()->with('success', "Patient has been {$status}.");
    }
}
