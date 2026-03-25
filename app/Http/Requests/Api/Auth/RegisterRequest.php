<?php

namespace App\Http\Requests\Api\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name'      => 'required|string|min:4|max:255|regex:/^[A-Za-z\s]+$/',
            'email'     => 'required|regex:/^(?!.*\.com\.com$).*/|email:rfc,dns|unique:users,email',
            'phone'     => ['required','regex:/^(\+2)?01[0-2,5][0-9]{8}$/','unique:users,phone'],
            'password'  => [
                        'required',
                        'confirmed',
                        Password::min(8)
                                ->max(255)
                                ->letters()
                                ->mixedCase()
                                ->numbers()
                                ->symbols()
            ]
        ];
    }
}
