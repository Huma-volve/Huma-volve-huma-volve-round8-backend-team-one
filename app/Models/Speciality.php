<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image'];

    public function doctors()
    {
        return $this->hasMany(DoctorProfile::class, 'specialty_id');
    }
}
