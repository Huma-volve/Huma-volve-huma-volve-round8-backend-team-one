<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePolicyRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.ar' => ['required', 'string', 'max:255'],

            'content' => ['required', 'array'],
            'content.en' => ['required', 'string'],
            'content.ar' => ['required', 'string'],

            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title.en' => 'English Title',
            'title.ar' => 'Arabic Title',
            'content.en' => 'English Content',
            'content.ar' => 'Arabic Content',
        ];
    }
}