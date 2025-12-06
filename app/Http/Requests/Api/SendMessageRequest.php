<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
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
            'body' => 'required_without:attachment|string|max:5000',
            'attachment' => 'required_without:body|file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,mp3,wav,ogg,m4a,aac|max:51200',
        ];
    }
}
