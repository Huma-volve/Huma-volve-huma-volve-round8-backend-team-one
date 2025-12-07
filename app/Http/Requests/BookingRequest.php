<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
        $rules = [
            'doctor_id' => 'required|exists:doctor_profiles,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'required|in:paypal,stripe,cash',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
             $rules = [
                'appointment_date' => 'sometimes|date|after_or_equal:today',
                'appointment_time' => 'sometimes|date_format:H:i',
                'notes' => 'nullable|string|max:500',
            ];
        }

        return $rules;
    }
}
