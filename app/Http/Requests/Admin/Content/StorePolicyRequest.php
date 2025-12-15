<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StorePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:255', 'unique:policies,slug'],
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'content' => ['required', 'array'],
            'content.en' => ['required', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'slug' => 'Slug',
            'title.en' => 'Page Title',
            'content.en' => 'Content',
        ];
    }
}
