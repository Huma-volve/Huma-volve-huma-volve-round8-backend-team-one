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
        'rating_avg',
        'total_reviews',
        'is_approved',
        'experience_length',
        'temporary_password',
        'password_changed',
    ];

    protected $casts = [
        'rating_avg' => 'float',
        'total_reviews' => 'integer',
        'is_approved' => 'boolean',
        'password_changed' => 'boolean',
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

    public function availabilitySlots()
    {
        return $this->hasMany(AvailabilitySlot::class, 'doctor_profile_id');
    }

    public function isFavoritedBy(int $userId): bool
    {
        $patient = PatientProfile::where('user_id', $userId)->first();
        if (!$patient) return false;

        return \Illuminate\Support\Facades\DB::table('favorites')
            ->where('doctor_id', $this->id)
            ->where('patient_id', $patient->id)
            ->exists();
    }

    public function getUpcomingSlots()
    {
        return $this->availabilitySlots()
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->where('is_booked', false)
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get()
            ->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'date' => $slot->date->format('Y-m-d'),
                    'start_time' => $slot->start_time->format('H:i'),
                    'end_time' => $slot->end_time->format('H:i'),
                ];
            });
    }

    // Query Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

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

    public function scopeMinRating($query, $rating)
    {
        return $query->where('rating_avg', '>=', $rating);
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
