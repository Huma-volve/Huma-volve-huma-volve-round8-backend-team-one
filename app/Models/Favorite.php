<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
    ];

    public function patient()
    {
        return $this->belongsTo(PatientProfile::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_id');
    }
}
