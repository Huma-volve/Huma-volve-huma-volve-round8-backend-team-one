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

        // Parse the new times (using parse for flexibility)
        $newStart = \Carbon\Carbon::parse($request->start_time);
        $newEnd = \Carbon\Carbon::parse($request->end_time);

        // Find all overlapping or touching schedules on the same day
        // Overlapping: start_time < new_end AND end_time > new_start
        // Touching: end_time == new_start OR start_time == new_end
        $overlappingSchedules = $doctorProfile->doctorSchedules()
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($query) use ($request) {
                // Overlapping condition
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                })
                // Or touching condition (adjacent schedules)
                    ->orWhere('end_time', $request->start_time)
                    ->orWhere('start_time', $request->end_time);
            })
            ->get();

        if ($overlappingSchedules->isNotEmpty()) {
            // Merge all overlapping schedules into one
            $mergedStart = $newStart->copy();
            $mergedEnd = $newEnd->copy();

            foreach ($overlappingSchedules as $schedule) {
                // Use parse() for flexibility with different time formats
                $existingStart = \Carbon\Carbon::parse($schedule->start_time);
                $existingEnd = \Carbon\Carbon::parse($schedule->end_time);

                // Get the minimum start time and maximum end time
                if ($existingStart->lt($mergedStart)) {
                    $mergedStart = $existingStart;
                }
                if ($existingEnd->gt($mergedEnd)) {
                    $mergedEnd = $existingEnd;
                }
            }

            $mergedStartTime = $mergedStart->format('H:i');
            $mergedEndTime = $mergedEnd->format('H:i');

            // Validate that the merged shift duration is perfectly divisible by consultation time
            $validationError = $this->validateSlotDivisibility(
                $mergedStartTime,
                $mergedEndTime,
                $request->avg_consultation_time
            );

            if ($validationError) {
                $totalMinutes = $mergedStart->diffInMinutes($mergedEnd);

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['slot_error' => "After merging with existing schedule(s), the new shift would be {$mergedStartTime} to {$mergedEndTime} ({$totalMinutes} minutes). ".$validationError]);
            }

            // Delete all overlapping schedules except the first one (which we'll update)
            $firstSchedule = $overlappingSchedules->first();
            $overlappingSchedules->where('id', '!=', $firstSchedule->id)->each->delete();

            // Update the first schedule with merged times
            $firstSchedule->update([
                'day_of_week' => $request->day_of_week,
                'start_time' => $mergedStartTime,
                'end_time' => $mergedEndTime,
                'avg_consultation_time' => $request->avg_consultation_time,
            ]);

            return redirect()->back()->with('success', "Schedule merged successfully! New time: {$mergedStartTime} to {$mergedEndTime}.");
        }

        // No overlapping schedules - validate and create new
        $validationError = $this->validateSlotDivisibility(
            $request->start_time,
            $request->end_time,
            $request->avg_consultation_time
        );

        if ($validationError) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['slot_error' => $validationError]);
        }

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

        // Parse the new times (using parse for flexibility)
        $newStart = \Carbon\Carbon::parse($request->start_time);
        $newEnd = \Carbon\Carbon::parse($request->end_time);

        // Find all overlapping or touching schedules on the same day (excluding current)
        $overlappingSchedules = $doctorProfile->doctorSchedules()
            ->where('day_of_week', $request->day_of_week)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                // Overlapping condition
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                })
                // Or touching condition (adjacent schedules)
                    ->orWhere('end_time', $request->start_time)
                    ->orWhere('start_time', $request->end_time);
            })
            ->get();

        if ($overlappingSchedules->isNotEmpty()) {
            // Merge all overlapping schedules into one
            $mergedStart = $newStart->copy();
            $mergedEnd = $newEnd->copy();

            foreach ($overlappingSchedules as $overlapping) {
                // Use parse() for flexibility with different time formats
                $existingStart = \Carbon\Carbon::parse($overlapping->start_time);
                $existingEnd = \Carbon\Carbon::parse($overlapping->end_time);

                // Get the minimum start time and maximum end time
                if ($existingStart->lt($mergedStart)) {
                    $mergedStart = $existingStart;
                }
                if ($existingEnd->gt($mergedEnd)) {
                    $mergedEnd = $existingEnd;
                }
            }

            $mergedStartTime = $mergedStart->format('H:i');
            $mergedEndTime = $mergedEnd->format('H:i');

            // Validate that the merged shift duration is perfectly divisible by consultation time
            $validationError = $this->validateSlotDivisibility(
                $mergedStartTime,
                $mergedEndTime,
                $request->avg_consultation_time
            );

            if ($validationError) {
                $totalMinutes = $mergedStart->diffInMinutes($mergedEnd);

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['slot_error' => "After merging with existing schedule(s), the new shift would be {$mergedStartTime} to {$mergedEndTime} ({$totalMinutes} minutes). ".$validationError]);
            }

            // Delete all overlapping schedules
            $overlappingSchedules->each->delete();

            // Update the current schedule with merged times
            $schedule->update([
                'day_of_week' => $request->day_of_week,
                'start_time' => $mergedStartTime,
                'end_time' => $mergedEndTime,
                'avg_consultation_time' => $request->avg_consultation_time,
            ]);

            return redirect()->route('doctor.availability.index')->with('success', "Schedule merged successfully! New time: {$mergedStartTime} to {$mergedEndTime}.");
        }

        // No overlapping schedules - validate and update
        $validationError = $this->validateSlotDivisibility(
            $request->start_time,
            $request->end_time,
            $request->avg_consultation_time
        );

        if ($validationError) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['slot_error' => $validationError]);
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

    /**
     * Validate that the shift duration is perfectly divisible by the consultation time.
     *
     * @return string|null Error message if invalid, null if valid
     */
    private function validateSlotDivisibility(string $startTime, string $endTime, int $avgConsultationTime): ?string
    {
        // Parse times and calculate duration in minutes
        $start = \Carbon\Carbon::createFromFormat('H:i', $startTime);
        $end = \Carbon\Carbon::createFromFormat('H:i', $endTime);
        $totalDurationMinutes = $start->diffInMinutes($end);

        // Check if duration is less than consultation time
        if ($totalDurationMinutes < $avgConsultationTime) {
            return "The shift duration ({$totalDurationMinutes} minutes) is shorter than the average consultation time ({$avgConsultationTime} minutes). Please adjust your schedule.";
        }

        // Check if duration is perfectly divisible by consultation time
        if ($totalDurationMinutes % $avgConsultationTime !== 0) {
            $slots = $totalDurationMinutes / $avgConsultationTime;
            $wholeSlots = floor($slots);
            $remainder = $totalDurationMinutes - ($wholeSlots * $avgConsultationTime);

            return "The shift duration ({$totalDurationMinutes} minutes) is not evenly divisible by the consultation time ({$avgConsultationTime} minutes). This would result in {$slots} slots with {$remainder} minutes remaining. Please adjust your times.";
        }

        return null; // Valid
    }
}
