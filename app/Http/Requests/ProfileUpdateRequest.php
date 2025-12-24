<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'bio' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email' => 'required|email:rfc|regex:/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/|not_regex:/\.com\.com$/|unique:users,email,'.Auth::id(),
        ];

        // Only require doctor-specific fields if the user is a doctor
        if (Auth::user() && Auth::user()->user_type === 'doctor') {
            $rules['clinic_address'] = ['required', 'string', 'max:255'];
            $rules['experience_length'] = ['required', 'integer', 'min:0', 'max:20'];
            $rules['session_price'] = ['required', 'numeric', 'min:0'];
            $rules['license_number'] = [
                'required',
                'string',
                'min:6',
                'max:20',
                'regex:/^[A-Z0-9\-\/]+$/',
                'unique:doctor_profiles,license_number,'.(Auth::user()->doctorProfile->id ?? 'NULL'),
            ];
        }

        return $rules;
    }
}
