<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorFilterRequest;
use App\Http\Resources\DoctorResource;
use App\Services\DoctorService;
use Illuminate\Http\JsonResponse;

class DoctorController extends Controller
{
    protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    /**
     * Get all doctors with filters
     */
    public function index(DoctorFilterRequest $request): JsonResponse
    {
        $doctors = $this->doctorService->getDoctors($request->validated());

        return response()->json([
            'success' => true,
            'data' => DoctorResource::collection($doctors),
            'meta' => [
                'total' => $doctors->total(),
                'per_page' => $doctors->perPage(),
                'current_page' => $doctors->currentPage(),
                'last_page' => $doctors->lastPage(),
            ]
        ]);
    }

    /**
     * Get single doctor details
     */
    public function show(int $id): JsonResponse
    {
        $doctor = $this->doctorService->getDoctorById($id);

        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Doctor not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new DoctorResource($doctor)
        ]);
    }

    /**
     * Toggle favorite doctor
     */
    public function toggleFavorite(int $id): JsonResponse
    {
        $result = $this->doctorService->toggleFavorite($id, auth()->id());

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'is_favorite' => $result['is_favorite']
        ]);
    }

    /**
     * Get doctor availability slots
     */
    public function availability(int $id): JsonResponse
    {
        $slots = $this->doctorService->getAvailability($id);

        return response()->json([
            'success' => true,
            'data' => $slots
        ]);
    }
}
