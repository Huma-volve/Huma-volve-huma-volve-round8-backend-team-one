<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodRequest extends FormRequest
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
            'provider_token' => 'required|string',
            'brand'          => 'required|string',
            'last_four'      => 'required|string|max_digits:4',
            'exp_month'      => 'required|string|max_digits:2',
            'exp_year'       => 'required|string|max_digits:4',
        ];
    }
}
