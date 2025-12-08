<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DoctorProfile;
use App\Services\DoctorService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
    use ApiResponse;

    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    /**
     * Toggle favorite doctor
     */
    public function __invoke(DoctorProfile $doctor): JsonResponse
    {
        $result = $this->doctorService->toggleFavorite($doctor->id, auth()->id());

        return $this->successResponse([
            'is_favorite' => $result['is_favorite']
        ], $result['message']);
    }
}
