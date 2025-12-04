<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $otherParticipant = $this->participants->where('user_id', '!=', Auth::id())->first()?->user;

        $currentUserParticipant = $this->participants->where('user_id', Auth::id())->first();

        $lastReadAt = $currentUserParticipant?->last_read_at ?? $this->created_at;

        $unreadCount = $this->messages()->where('created_at', '>', $lastReadAt)->count();

        return [
            'id' => $this->id,
            'is_private' => (bool) $this->is_private,
            'other_user' => $otherParticipant ? [
                'id' => $otherParticipant->id,
                'name' => $otherParticipant->name,
            ] : null,
            'last_message' => new MessageResource($this->messages->last()),
            'unread_count' => $unreadCount,
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
