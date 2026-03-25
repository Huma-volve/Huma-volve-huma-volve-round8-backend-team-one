<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class NotificationsAdmin extends Component
{
    public $notifications = [];
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();

        $this->notifications = $user->notifications()
            ->latest()
            ->get()
            ->map(function ($notif) {
                $notif->data = is_array($notif->data)
                    ? $notif->data
                    : json_decode($notif->data, true);
                return $notif;
            });

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications-admin');
    }
}
