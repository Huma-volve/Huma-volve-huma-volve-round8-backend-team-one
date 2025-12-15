<?php

namespace App\Http\Requests;

use App\Models\DoctorProfile;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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
            'bio' => ['nullable','string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'name' => ['required', 'string','min:8','max:255','regex:/^[A-Za-z\s]+$/'],
            'email' => 'required|regex:/^(?!.*\.com\.com$).*/|email:rfc,dns|unique:users,email,'.Auth::id(),
            'clinic_address' => ['required','string'],
            'experience' => ['required','integer','min:0'],
            'session_price' => ['required','numeric','min:0'],
            'license_number' => [
                'required',
                'string',
                'max:20',
                'unique:doctor_profiles,license_number,'.Auth::id() .',user_id',
            ]
        ];
    }
}
