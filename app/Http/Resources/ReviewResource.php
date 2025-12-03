<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'patient_name' => $this->patient->user->name,
            'patient_photo' => $this->patient->user->profile_photo_path,
            'created_at' => $this->created_at->diffForHumans(),
            'doctor_response' => $this->doctor_response,
            'responded_at' => $this->responded_at ? $this->responded_at->diffForHumans() : null,
        ];
    }
}
