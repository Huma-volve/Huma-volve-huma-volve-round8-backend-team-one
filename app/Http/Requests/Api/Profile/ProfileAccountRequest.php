<?php

namespace App\Http\Requests\Api\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileAccountRequest extends FormRequest
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
            'name'      => 'nullable|string|min:8|max:255|regex:/^[A-Za-z\s]+$/',
            'email'     => 'nullable|email|unique:users,email,'.Auth::id(),
            'phone'     => ['nullable','regex:/^(\+2)?01[0-2,5][0-9]{8}$/','unique:users,phone,'.Auth::id()],
            'birthdate' => 'nullable|date' ,
        ];
    }
}
