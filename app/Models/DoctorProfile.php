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
        'rating_avg'      => 'float',
        'total_reviews'   => 'integer',
        'is_approved'     => 'boolean',
        'password_changed' => 'boolean',
        'session_price'   => 'float',
        'latitude'        => 'float',
        'longitude'       => 'float',
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

    public function favoritedByUsers()
    {
        return $this->hasMany(FavoriteDoctor::class, 'doctor_profile_id');
    }
}
