<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class VerificationCode extends Model
{
    use HasFactory,Prunable;

    protected $fillable = [
        'identifier',
        'code',
        'type',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];


    public function prunable()
    {
        return static::where('expires_at', '<', now());
    }
}
