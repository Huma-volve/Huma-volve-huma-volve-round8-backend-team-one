<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailabilitySlotResource;
use App\Models\DoctorProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class GetDoctorAvailabilityController extends Controller
{
    use ApiResponse;

    /**
     * Get doctor availability slots
     */
    public function __invoke(DoctorProfile $doctor): JsonResponse
    {
        $slots = $doctor->availabilitySlots()
            ->where('date', '>=', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->where('is_booked', false)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return $this->successResponse(
            AvailabilitySlotResource::collection($slots),
            'Availability slots retrieved successfully'
        );
    }
}
