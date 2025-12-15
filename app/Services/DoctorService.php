<?php
namespace App\Services;

use App\Models\DoctorProfile;
use App\Models\SearchHistory;
use App\Repositories\DoctorRepository;
use App\Repositories\FavoriteRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorService
{
    protected $doctorRepository;
    protected $favoriteRepository;
    protected $geocodingService;

    public function __construct(
        DoctorRepository $doctorRepository,
        FavoriteRepository $favoriteRepository,
    ) {
        $this->doctorRepository   = $doctorRepository;
        $this->favoriteRepository = $favoriteRepository;
    }

    /**
     * Get doctors with filters and pagination
     */
    public function getDoctors(array $filters): LengthAwarePaginator
    {


// if user authenticated save search
        if (auth('sanctum')->check()) {
            $user = auth('sanctum')->user();
            if (! empty($filters['search'] || ! empty($filters['specialty_id']) || ! empty('location_query'))) {
                SearchHistory::create([
                    'user_id' => $user->id,
                    'keyword' => $filters['search'] ?? null,
                    'filters' => $filters,
                ]);
            }

        }
        return $this->doctorRepository->getFiltered($filters);

    }

    /**
     * Get single doctor by ID
     */
    public function getDoctorById(int $id): ?DoctorProfile
    {
        return $this->doctorRepository->findById($id);
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(int $doctorId, int $userId): array
    {
        $isFavorite = $this->favoriteRepository->toggle($doctorId, $userId);

        return [
            'is_favorite' => $isFavorite,
            'message'     => $isFavorite
                ? 'Doctor added to favorites'
                : 'Doctor removed from favorites',
        ];
    }
}
