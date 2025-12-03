<?php

namespace App\Repositories;

use App\Models\DoctorProfile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DoctorRepository
{
    /**
     * Get filtered doctors with pagination
     */
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $query = DoctorProfile::with(['user', 'speciality', 'reviews'])
            ->where('is_approved', true);

        // Search by name or specialty
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })
                    ->orWhereHas('speciality', function ($specQuery) use ($search) {
                        $specQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by specialty
        if (!empty($filters['specialty_id'])) {
            $query->where('specialty_id', $filters['specialty_id']);
        }

        // Filter by minimum rating
        if (!empty($filters['min_rating'])) {
            $query->where('rating_avg', '>=', $filters['min_rating']);
        }

        // Filter by price range
        if (!empty($filters['min_price'])) {
            $query->where('session_price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('session_price', '<=', $filters['max_price']);
        }

        // Filter by location (radius search)
        if (!empty($filters['latitude']) && !empty($filters['longitude'])) {
            $lat = $filters['latitude'];
            $lng = $filters['longitude'];
            $radius = $filters['radius'] ?? 10; // Default 10km

            $query->select('doctor_profiles.*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$lat, $lng, $lat]
                )
                ->having('distance', '<=', $radius);
        }

        // Filter by available date
        if (!empty($filters['available_date'])) {
            $query->whereHas('availabilitySlots', function ($slotQuery) use ($filters) {
                $slotQuery->where('date', $filters['available_date'])
                    ->where('is_active', true)
                    ->where('is_booked', false);
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'rating';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        switch ($sortBy) {
            case 'rating':
                $query->orderBy('rating_avg', $sortOrder);
                break;
            case 'price':
                $query->orderBy('session_price', $sortOrder);
                break;
            case 'experience':
                $query->orderBy('experience_length', $sortOrder);
                break;
            case 'distance':
                if (!empty($filters['latitude']) && !empty($filters['longitude'])) {
                    $query->orderBy('distance', $sortOrder);
                }
                break;
            default:
                $query->orderBy('rating_avg', 'desc');
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Find doctor by ID
     */
    public function findById(int $id): ?DoctorProfile
    {
        return DoctorProfile::with(['user', 'speciality', 'reviews', 'availabilitySlots'])
            ->find($id);
    }

    /**
     * Get doctor availability slots
     */
    public function getAvailabilitySlots(int $doctorId): array
    {
        $doctor = DoctorProfile::find($doctorId);

        if (!$doctor) {
            return [];
        }

        return $doctor->availabilitySlots()
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->where('is_booked', false)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy('date')
            ->map(function ($slots) {
                return $slots->map(function ($slot) {
                    return [
                        'id' => $slot->id,
                        'start_time' => $slot->start_time,
                        'end_time' => $slot->end_time,
                    ];
                });
            })
            ->toArray();
    }

    /**
     * Search doctors by keyword
     */
    public function search(string $keyword): array
    {
        return DoctorProfile::with(['user', 'speciality'])
            ->where('is_approved', true)
            ->where(function ($query) use ($keyword) {
                $query->whereHas('user', function ($userQuery) use ($keyword) {
                    $userQuery->where('name', 'like', "%{$keyword}%");
                })
                    ->orWhereHas('speciality', function ($specQuery) use ($keyword) {
                        $specQuery->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhere('clinic_address', 'like', "%{$keyword}%");
            })
            ->limit(20)
            ->get()
            ->toArray();
    }
}
