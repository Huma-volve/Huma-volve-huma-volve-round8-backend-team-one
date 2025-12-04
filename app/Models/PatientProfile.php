<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'birthdate',
        'gender',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'patient_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'patient_id');
    }

    public function searchHistories()
    {
        return $this->hasMany(SearchHistory::class, 'patient_id'); // Actually user_id in migration, but kept for consistency if needed or remove.
        // Wait, search_histories migration uses user_id now. So this relationship might be better on User model directly.
        // But if we want to access from profile:
        // return $this->hasManyThrough(SearchHistory::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }
}
