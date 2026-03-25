<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteDoctor extends Model
{
    protected $fillable = [
        'user_id',
        'doctor_profile_id'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function doctorProfile(){
        return $this->belongsTo(DoctorProfile::class);
    }
}
