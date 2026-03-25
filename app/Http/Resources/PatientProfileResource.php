<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'birthdate' => [
                'Day'   => $this->birthdate ? Carbon::parse($this->birthdate)->day     : null,
                'Month' => $this->birthdate ? Carbon::parse($this->birthdate)->month     : null,
                'Year'  => $this->birthdate ? Carbon::parse($this->birthdate)->year  : null
            ],
        ];
    }
}
