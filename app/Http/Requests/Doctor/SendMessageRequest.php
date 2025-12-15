<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string', 'max:5000', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,mp4,mp3,wav', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'Message content is required.',
            'body.max' => 'Message cannot exceed 5000 characters.',
        ];
    }
}