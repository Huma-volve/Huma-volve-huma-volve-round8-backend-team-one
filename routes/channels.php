<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{conversationId}', function (User $user, int $conversationId) {
    return $user->conversations()->where('conversations.id', $conversationId)->exists();
});
