<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::whereHas('doctorProfile')->with(['doctorProfile.speciality', 'doctorProfile.doctorSchedules'])->get();
        return view('admin.doctors.index', compact('users'));
    }

    public function create()
    {
        $specialties = \App\Models\Speciality::all();
        return view('admin.doctors.create', compact('specialties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:8|max:255|regex:/^[A-Za-z\s]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'specialty_id' => 'required|exists:specialities,id',
            'license_number' => 'required|string|unique:doctor_profiles,license_number',
            'clinic_address' => 'required|string',
            'session_price' => 'required|numeric|min:0',
            'experience_length' => 'required|integer|min:0',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'user_type' => 'doctor',
            'status' => 1,
        ]);

        $user->doctorProfile()->create([
            'specialty_id' => $validated['specialty_id'],
            'license_number' => $validated['license_number'],
            'clinic_address' => $validated['clinic_address'],
            'session_price' => $validated['session_price'],
            'experience_length' => $validated['experience_length']
        ]);

        // Send email with credentials
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\NewDoctorAccount($user, $validated['password']));

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor created successfully.');
    }

    public function edit($id)
    {
        $user = \App\Models\User::with('doctorProfile')->findOrFail($id);
        $specialties = \App\Models\Speciality::all();
        return view('admin.doctors.edit', compact('user', 'specialties'));
    }

    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'specialty_id' => 'required|exists:specialities,id',
            'license_number' => 'required|string|unique:doctor_profiles,license_number,'. $user->doctorProfile->id,
            'clinic_address' => 'required|string',
            'session_price' => 'required|numeric|min:0',
            'experience_length' => 'required|integer|min:0',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => \Illuminate\Support\Facades\Hash::make($validated['password'])]);
        }

        $user->doctorProfile()->update([
            'specialty_id' => $validated['specialty_id'],
            'license_number' => $validated['license_number'],
            'clinic_address' => $validated['clinic_address'],
            'session_price' => $validated['session_price'],
            'experience_length' => $validated['experience_length'],
        ]);

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}
