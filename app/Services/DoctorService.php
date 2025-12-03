<?php

namespace App\Services;

use App\Models\DoctorProfile;
use App\Repositories\DoctorRepository;
use App\Repositories\FavoriteRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorService
{
    protected $doctorRepository;
    protected $favoriteRepository;

    public function __construct(
        DoctorRepository $doctorRepository,
        FavoriteRepository $favoriteRepository
    ) {
        $this->doctorRepository = $doctorRepository;
        $this->favoriteRepository = $favoriteRepository;
    }

    /**
     * Get doctors with filters and pagination
     */
    public function getDoctors(array $filters): LengthAwarePaginator
    {
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
            'message' => $isFavorite
                ? 'Doctor added to favorites'
                : 'Doctor removed from favorites'
        ];
    }

    /**
     * Get doctor availability slots
     */
    public function getAvailability(int $doctorId): array
    {
        return $this->doctorRepository->getAvailabilitySlots($doctorId);
    }

    /**
     * Search doctors by keyword
     */
    public function searchDoctors(string $keyword, int $userId): array
    {
        // Save search history
        $this->saveSearchHistory($keyword, $userId);

        return $this->doctorRepository->search($keyword);
    }

    /**
     * Save search history
     */
    protected function saveSearchHistory(string $keyword, int $userId): void
    {
        \App\Models\SearchHistory::create([
            'user_id' => $userId,
            'keyword' => $keyword,
            'filters' => null,
        ]);
    }
}
