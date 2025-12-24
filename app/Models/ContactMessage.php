<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Scope to get unread messages.
     */
    public function scopeUnread(Builder $query): void
    {
        $query->where('is_read', false);
    }

    /**
     * Scope to get read messages.
     */
    public function scopeRead(Builder $query): void
    {
        $query->where('is_read', true);
    }

    /**
     * Scope to order by newest first.
     */
    public function scopeNewest(Builder $query): void
    {
        $query->orderBy('created_at', 'desc');
    }
}
