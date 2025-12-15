<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsDoctor extends Component
{
    public $notifications = [];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();

        $this->notifications = $user->notifications()->latest()->get()
            ->map(function($notif){
                $notif->data = is_array($notif->data) ? $notif->data : json_decode($notif->data, true);
                return $notif;
            });
    }

    public function markAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications-doctor');
    }
}
