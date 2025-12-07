<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavedCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'provider_token' => 'required|string', // Stripe Payment Method ID
            'brand' => 'required|string',
            'last_four' => 'required|string|size:4',
            'exp_month' => 'required|integer|min:1|max:12',
            'exp_year' => 'required|integer|min:' . date('Y'),
            'is_default' => 'boolean',
        ];
    }
}
