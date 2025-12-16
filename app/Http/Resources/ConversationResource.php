<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $currentUserParticipant = $this->participants
            ->where('user_id', Auth::id())
            ->first();

        $otherParticipant = $this->participants
            ->where('user_id', '!=', Auth::id())
            ->first();

        $lastReadAt = $currentUserParticipant?->last_read_at ?? $this->created_at;
        $unreadCount = $this->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('created_at', '>', $lastReadAt)
            ->count();

        return [
            'id' => $this->id,
            'is_private' => true,
            'other_user' => $otherParticipant?->user ? [
                'id' => $otherParticipant->user->id,
                'name' => $otherParticipant->user->name,
                'avatar' => $otherParticipant->user->profile_photo_path 
                    ? asset('storage/' . $otherParticipant->user->profile_photo_path) 
                    : null,
            ] : null,
            'last_message' => $this->lastMessage 
                ? new MessageResource($this->lastMessage) 
                : null,
            'unread_count' => $unreadCount,
            'is_favorite' => (bool) ($currentUserParticipant?->is_favorite ?? false),
            'is_archived' => (bool) ($currentUserParticipant?->is_archived ?? false),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}