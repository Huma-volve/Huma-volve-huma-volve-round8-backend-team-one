<?php

namespace App\Http\Requests\Profile;

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
            'name'      => 'nullable|string|max:255',
            'email'     => 'nullable|email|unique:users,email,'.Auth::id(),
            'phone'     => ['nullable','regex:/^(?:\+?20|0)?1[0-2,5][0-9]{8}$/','unique:users,phone,'.Auth::id()],
            'birthdate' => 'nullable|date' ,
        ];
    }
}
