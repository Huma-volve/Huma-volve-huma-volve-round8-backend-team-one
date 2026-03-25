<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DoctorSettingController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->doctorProfile;

        return view('doctor.settings.edit', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = $user->doctorProfile;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'session_price' => 'required|numeric|min:0',
            'clinic_address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        // Profile Photo Upload
        if ($request->hasFile('photo')) {
            if ($user->profile_photo_path) {
                Storage::delete($user->profile_photo_path);
            }
            $path = $request->file('photo')->store('public/profile-photos');
            $user->profile_photo_path = $path;
            $user->save();
        }

        $profile->update([
            'bio' => $request->bio,
            'session_price' => $request->session_price,
            'clinic_address' => $request->clinic_address,
        ]);

        return redirect()->route('doctor.settings.edit')->with('success', 'Settings updated successfully.');
    }
}
