<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $slots = $doctor->getUpcomingSlots();

        return $this->successResponse(
            $slots,
            'Availability slots retrieved successfully'
        );
    }
}
