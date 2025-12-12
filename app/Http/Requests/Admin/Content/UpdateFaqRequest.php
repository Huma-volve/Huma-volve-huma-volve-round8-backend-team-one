<?php

namespace App\Http\Requests\Admin\Content;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => ['sometimes', 'array'],
            'question.en' => ['required_with:question', 'string', 'max:255'],
            'question.ar' => ['required_with:question', 'string', 'max:255'],

            'answer' => ['sometimes', 'array'],
            'answer.en' => ['required_with:answer', 'string'],
            'answer.ar' => ['required_with:answer', 'string'],

            'is_active' => ['boolean'],
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