<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialtyResource;
use App\Models\Speciality;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SpecialtyController extends Controller
{
    use ApiResponse;

    /**
     * Get all specialties
     */
    public function index(): JsonResponse
    {
        $specialties = Speciality::all();

        return $this->successResponse(
            SpecialtyResource::collection($specialties),
            'Specialties retrieved successfully'
        );
    }
}
