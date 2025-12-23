<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index()
    {
        $schedules = auth()->user()->doctorProfile->doctorSchedules;
        return view('doctor.availability.index', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'avg_consultation_time' => 'required|integer|min:5|max:120',
        ]);

        $doctorProfile = auth()->user()->doctorProfile;

        // Override: Delete overlapping schedules
        $doctorProfile->doctorSchedules()
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->delete();

        $doctorProfile->doctorSchedules()->create($request->all());

        return redirect()->back()->with('success', 'Availability added successfully.');
    }

    public function edit($id)
    {
        $doctorProfile = auth()->user()->doctorProfile;
        $schedule = $doctorProfile->doctorSchedules()->findOrFail($id);
        return view('doctor.availability.edit', compact('schedule'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'avg_consultation_time' => 'required|integer|min:5|max:120',
        ]);

        $doctorProfile = auth()->user()->doctorProfile;
        $schedule = $doctorProfile->doctorSchedules()->findOrFail($id);

        // Check for overlapping schedules excluding the current one
        $overlapping = $doctorProfile->doctorSchedules()
            ->where('day_of_week', $request->day_of_week)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($overlapping) {
            return redirect()->back()->withErrors(['error' => 'Ideally, this time slot overlaps with another schedule.']);
        }

        $schedule->update($request->all());

        return redirect()->route('doctor.availability.index')->with('success', 'Availability updated successfully.');
    }

    public function destroy($id)
    {
        $doctorProfile = auth()->user()->doctorProfile;
        $schedule = $doctorProfile->doctorSchedules()->findOrFail($id);
        $schedule->delete();

        return redirect()->back()->with('success', 'Availability deleted successfully.');
    }
}
