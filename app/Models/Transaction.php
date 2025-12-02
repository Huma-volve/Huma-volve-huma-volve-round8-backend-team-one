<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'external_id',
        'amount',
        'type',
        'status',
        'gateway',
        'payload',
        'currency',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'float',
        'payload' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
