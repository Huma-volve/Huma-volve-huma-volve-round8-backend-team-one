<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        'id'              => $this->id,
        'rating'          => $this->rating,
        'comment'         => $this->comment,
        'doctor_response' => $this->doctor_response ?? null,
        'responded_at'    => $this->responded_at ? $this->responded_at->format('Y-m-d H:i:s') : null,

        'patient' => [
            'name'  => optional($this->patient->user)->name,
            'image' => optional($this->patient->user)->image,
        ]
    ];
    }
}
