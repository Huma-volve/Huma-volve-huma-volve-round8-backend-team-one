<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->sender_id,
            'sender_name' => $this->sender->name,
            'sender_avatar' => $this->sender->profile_photo_path 
                ? Storage::url($this->sender->profile_photo_path) 
                : null,
            'body' => $this->type === 'text' 
                ? $this->body 
                : Storage::url($this->body),
            'type' => $this->type,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}