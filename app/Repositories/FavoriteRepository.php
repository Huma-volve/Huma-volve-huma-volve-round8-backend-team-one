<?php

namespace App\Repositories;

use App\Models\PatientProfile;
use Illuminate\Support\Facades\DB;

class FavoriteRepository
{
    /**
     * Toggle favorite status
     */
    public function toggle(int $doctorId, int $userId): bool
    {
        // Get patient profile
        $patient = PatientProfile::where('user_id', $userId)->first();

        if (!$patient) {
            throw new \Exception('Patient profile not found');
        }

        // Check if already favorited
        $favorite = DB::table('favorites')
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctorId)
            ->first();

        if ($favorite) {
            // Remove from favorites
            DB::table('favorites')
                ->where('patient_id', $patient->id)
                ->where('doctor_id', $doctorId)
                ->delete();

            return false;
        } else {
            // Add to favorites
            DB::table('favorites')->insert([
                'patient_id' => $patient->id,
                'doctor_id' => $doctorId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        }
    }

    /**
     * Check if doctor is favorited by user
     */
    public function isFavorited(int $doctorId, int $userId): bool
    {
        $patient = PatientProfile::where('user_id', $userId)->first();

        if (!$patient) {
            return false;
        }

        return DB::table('favorites')
            ->where('patient_id', $patient->id)
            ->where('doctor_id', $doctorId)
            ->exists();
    }

    /**
     * Get user's favorite doctors
     */
    public function getUserFavorites(int $userId): array
    {
        $patient = PatientProfile::where('user_id', $userId)->first();

        if (!$patient) {
            return [];
        }

        return DB::table('favorites')
            ->where('patient_id', $patient->id)
            ->pluck('doctor_id')
            ->toArray();
    }
}
