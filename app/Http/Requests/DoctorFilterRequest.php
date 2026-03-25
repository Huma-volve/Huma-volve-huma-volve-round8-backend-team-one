<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'         => 'nullable|string|max:255',
            'specialty_id'   => 'nullable|exists:specialities,id',
            'min_rating'     => 'nullable|numeric|min:0|max:5',
            'max_price'      => 'nullable|numeric|min:0',
            'min_price'      => 'nullable|numeric|min:0',
            'latitude'       => 'nullable|numeric',
            'longitude'      => 'nullable|numeric',
            'radius'         => 'nullable|numeric|min:1',
            'available_date' => 'nullable|date|after_or_equal:today',
            'sort_by'        => 'nullable|in:rating,price,distance,experience',
            'sort_order'     => 'nullable|in:asc,desc',
            'per_page'       => 'nullable|integer|min:1|max:100',
            'location_query' => 'nullable|string|min:1|max:255',

        ];
    }
}
