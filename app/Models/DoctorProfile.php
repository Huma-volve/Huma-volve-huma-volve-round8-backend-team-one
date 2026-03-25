<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialty_id',
        'license_number',
        'bio',
        'session_price',
        'clinic_address',
        'latitude',
        'longitude',
        'experience_length',
    ];

    protected $casts = [
        'session_price' => 'float',
        'latitude' => 'float',
        'longitude' => 'float',
        'experience_length' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'specialty_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'doctor_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'doctor_id');
    }

    public function doctorSchedules()
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_profile_id');
    }

    public function isFavoritedBy(int $userId): bool
    {
        $patient = PatientProfile::where('user_id', $userId)->first();
        if (! $patient) {
            return false;
        }

        return \Illuminate\Support\Facades\DB::table('favorites')
            ->where('doctor_id', $this->id)
            ->where('patient_id', $patient->id)
            ->exists();
    }

    public function getUpcomingSlots()
    {
        $schedules = $this->doctorSchedules;
        $slots = collect([]);
        $today = now();

        for ($i = 0; $i < 7; $i++) {
            $date = $today->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeek; // 0 (Sunday) - 6 (Saturday)

            $schedule = $schedules->firstWhere('day_of_week', $dayOfWeek);

            if ($schedule) {
                // Generate slots for this day
                $startTime = \Carbon\Carbon::parse($schedule->start_time);
                $endTime = \Carbon\Carbon::parse($schedule->end_time);

                while ($startTime->lt($endTime)) {
                    $slotEnd = $startTime->copy()->addMinutes($schedule->avg_consultation_time ?? 30);

                    if ($slotEnd->gt($endTime)) {
                        break;
                    }

                    // Check if already booked
                    $isBooked = Booking::where('doctor_id', $this->id)
                        ->whereDate('appointment_date', $date->format('Y-m-d'))
                        ->where('appointment_time', $startTime->format('H:i:s'))
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->exists();

                    if (! $isBooked) {
                        $slots->push([
                            'date' => $date->format('Y-m-d'),
                            'start_time' => $startTime->format('H:i'),
                            'end_time' => $slotEnd->format('H:i'),
                            'day_name' => $date->format('l'),
                        ]);
                    }

                    $startTime->addMinutes($schedule->avg_consultation_time ?? 30);

                    if ($slots->count() >= 50) {
                        break 2;
                    } // Return first 50 slots
                }
            }
        }

        return $slots;
    }

    // Query Scopes


    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%");
            })
                ->orWhereHas('speciality', function ($specQuery) use ($search) {
                    $specQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    public function scopeBySpecialty($query, $specialtyId)
    {
        return $query->where('specialty_id', $specialtyId);
    }



    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('session_price', '>=', $minPrice);
        }
        if ($maxPrice !== null) {
            $query->where('session_price', '<=', $maxPrice);
        }

        return $query;
    }

    public function scopeWithinRadius($query, $latitude, $longitude, $radius = 10)
    {
        return $query->select('doctor_profiles.*')
            ->selectRaw(
                '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                [$latitude, $longitude, $latitude]
            )
            ->having('distance', '<=', $radius);
    }

    public function scopeAvailableOn($query, $date)
    {
        return $query->whereHas('availabilitySlots', function ($slotQuery) use ($date) {
            $slotQuery->where('date', $date)
                ->where('is_active', true)
                ->where('is_booked', false);
        });
    }
}
