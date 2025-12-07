<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand' => $this->brand,
            'last_four' => $this->last_four,
            'exp_month' => $this->exp_month,
            'exp_year' => $this->exp_year,
            'is_default' => $this->is_default,
        ];
    }
}
