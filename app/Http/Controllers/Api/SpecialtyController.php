<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpecialtyResource;
use App\Models\Speciality;
use Illuminate\Http\JsonResponse;

class SpecialtyController extends Controller
{
    /**
     * Get all specialties
     */
    public function index(): JsonResponse
    {
        $specialties = Speciality::all();

        return response()->json([
            'success' => true,
            'data' => SpecialtyResource::collection($specialties)
        ]);
    }
}
