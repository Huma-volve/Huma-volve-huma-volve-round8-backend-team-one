<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_DOCTOR = 'doctor';
    const ROLE_PATIENT = 'patient';

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::ROLE_ADMIN;
    }

    /**
     * Check if the user is a doctor.
     */
    public function isDoctor(): bool
    {
        return $this->user_type === self::ROLE_DOCTOR;
    }

    /**
     * Check if the user is a patient.
     */
    public function isPatient(): bool
    {
        return $this->user_type === self::ROLE_PATIENT;
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->user_type === $role;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'phone_verified_at',
        'email_verified_at',
        'profile_photo_path',
        'status',
        'user_type',
        'phone',
        'address',
        'can_reset_password',
        'stripe_customer_id ',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'integer',
        ];
    }

    public function doctorProfile()
    {
        return $this->hasOne(DoctorProfile::class);
    }

    public function patientProfile()
    {
        return $this->hasOne(PatientProfile::class, 'user_id');
    }

    public function savedCards()
    {
        return $this->hasMany(SavedCard::class);
    }

    public function searchHistories()
    {
        return $this->hasMany(SearchHistory::class);
    }

    public function chatParticipants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    public function favoriteDoctors()
    {
        return $this->hasMany(FavoriteDoctor::class, 'user_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'chat_participants', 'user_id', 'conversation_id')
            ->withPivot(['last_read_at'])
            ->withTimestamps();
    }
}
