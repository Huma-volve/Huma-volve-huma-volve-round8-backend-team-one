<?php

namespace App\Http\Requests\Api;

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
            'body' => 'nullable|string|max:5000|required_without:attachment',
            'attachment' => [
                'nullable',
                'file',
                'max:51200', // 50MB
                'mimes:jpeg,png,jpg,gif,webp,mp4,mp3,wav,m4a,ogg,aac,webm',
                'required_without:body'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required_without' => 'Either message text or attachment is required.',
            'attachment.required_without' => 'Either message text or attachment is required.',
            'attachment.max' => 'The file size is too large. Maximum allowed size is 50MB.',
            'attachment.mimes' => 'The file type is not supported. Allowed types: Images, Audio, and Video files.',
            'attachment.uploaded' => 'The file failed to upload. The file size might exceed the server configuration.', 
        ];
    }
}