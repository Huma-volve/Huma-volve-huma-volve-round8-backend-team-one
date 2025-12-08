<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'doctor_response' => $this->doctor_response,
            'created_at' => $this->created_at->diffForHumans(),
            'responded_at' => $this->responded_at?->diffForHumans(),
            'patient' => [
                'id' => optional($this->patient)->id,
                'name' => optional($this->patient->user)->name,
                'photo' => optional($this->patient->user)->profile_photo_path,
            ],
        ];
    }
}
