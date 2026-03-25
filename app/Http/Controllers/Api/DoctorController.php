<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorFilterRequest;
use App\Http\Resources\DoctorResource;
use App\Models\DoctorProfile;
use App\Services\DoctorService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DoctorController extends Controller
{
    use ApiResponse;

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

        return $this->paginatedResponse(
            DoctorResource::collection($doctors),
            'Doctors retrieved successfully'
        );
    }

    /**
     * Get single doctor details
     */
    public function show(DoctorProfile $doctor): JsonResponse
    {
        // Load necessary relationships
        $doctor->load(['user', 'speciality', 'reviews.patient.user'])
                ->loadCount(['reviews','bookings as patients_count' => function ($query) {$query->select(DB::raw('COUNT(DISTINCT patient_id)'));}])
                ->loadAvg('reviews','rating');

        return $this->successResponse(
            new DoctorResource($doctor),
            'Doctor details retrieved successfully'
        );
    }
}
