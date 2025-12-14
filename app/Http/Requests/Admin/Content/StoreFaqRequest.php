<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['required', 'array'],
            'question.en' => ['required', 'string', 'max:255'],
            'question.ar' => ['nullable', 'string', 'max:255'],

            'answer' => ['required', 'array'],
            'answer.en' => ['required', 'string'],
            'answer.ar' => ['nullable', 'string'],

            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'question.en' => 'English Question',
            'question.ar' => 'Arabic Question',
            'answer.en' => 'English Answer',
            'answer.ar' => 'Arabic Answer',
        ];
    }
}