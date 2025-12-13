<?php
namespace App\Repositories;

use App\Models\DoctorProfile;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorRepository
{
    /**
     * Get filtered doctors with pagination
     */
    public function getFiltered(array $filters): LengthAwarePaginator
    {
        $query = DoctorProfile::with(['user', 'speciality', 'reviews'])
            ->approved();

        // Search by name or specialty
        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }
        // filter By location

        if (! empty($filters['location_query'])) {
            $query->where('clinic_address', 'LIKE', '%' . $filters['location_query'] . '%');
        }

        // Filter by specialty
        if (! empty($filters['specialty_id'])) {
            $query->bySpecialty($filters['specialty_id']);
        }

        // Filter by minimum rating
        if (! empty($filters['min_rating'])) {
            $query->minRating($filters['min_rating']);
        }

        // Filter by price range
        if (! empty($filters['min_price']) || ! empty($filters['max_price'])) {
            $query->priceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null);
        }



        // Filter by available date
        if (! empty($filters['available_date'])) {
            $query->availableOn($filters['available_date']);
        }
        // Sorting
        $sortBy    = $filters['sort_by'] ?? 'rating';
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
                if (! empty($filters['latitude']) && ! empty($filters['longitude'])) {
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
}
