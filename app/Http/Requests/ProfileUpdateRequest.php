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
        return [
            'bio' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'name' => ['required', 'string', 'min:8', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email' => 'required|regex:/^(?!.*\.com\.com$).*/|email|unique:users,email,'.Auth::id(),
            'clinic_address' => [Rule::requiredIf(fn () => Auth::user()->user_type === 'doctor'), 'nullable', 'string'],
            'experience' => [Rule::requiredIf(fn () => Auth::user()->user_type === 'doctor'), 'nullable', 'integer', 'min:0'],
            'session_price' => [Rule::requiredIf(fn () => Auth::user()->user_type === 'doctor'), 'nullable', 'numeric', 'min:0'],
            'license_number' => [
                Rule::requiredIf(fn () => Auth::user()->user_type === 'doctor'),
                'nullable',
                'string',
                'max:20',
                'unique:doctor_profiles,license_number,'.(Auth::user()->doctorProfile->id ?? 'NULL'),
            ],
        ];
    }
}
