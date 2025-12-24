<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PatientProfileResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name'       => $this->name,
            'email'      => $this->email,
            'phone'      => $this->phone,
            'image'      => $this->profile_photo_path ? asset('storage/' . $this->profile_photo_path):null,
            'address'    => $this->address,
            'extra_data' => new PatientProfileResource($this->whenLoaded('patientProfile')) ,
        ];
    }
}
