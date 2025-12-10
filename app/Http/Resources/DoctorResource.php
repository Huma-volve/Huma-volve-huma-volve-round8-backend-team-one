<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'mobile' => $this->user->mobile,
            'profile_photo' => $this->user->profile_photo_path,
            'specialty' => [
                'id' => $this->speciality->id,
                'name' => $this->speciality->name,
                'image' => $this->speciality->image,
            ],
            'license_number' => $this->license_number,
            'bio' => $this->bio,
            'session_price' => (float) $this->session_price,
            'clinic_address' => $this->clinic_address,
            'location' => [
                'latitude' => (float) $this->latitude,
                'longitude' => (float) $this->longitude,
            ],
            'rating' => [
                'average' => (float) $this->rating_avg,
                'total_reviews' => $this->total_reviews,
            ],
            'experience_years' => $this->experience_length,
            'is_approved' => (bool) $this->is_approved,
            'is_favorite' => $this->when(
                auth()->check(),
                fn() => $this->isFavoritedBy(auth()->id())
            ),
            'availability' => $this->when(
                $request->include_availability,
                fn() => $this->getUpcomingSlots()
            ),
            'reviews' => $this->when(
                $request->include_reviews,
                fn() => ReviewResource::collection($this->reviews()->latest()->take(5)->get())
            ),
        ];
    }
}
