<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
     public function index()
    {
        $user = Auth::user();

        $notifications =  $user->notifications()
            ->latest()
            ->get()
            ->map(function ($notif) {
                $notif->data = is_array($notif->data)
                    ? $notif->data
                    : json_decode($notif->data, true);
                return $notif;
            });

        $user->unreadNotifications->markAsRead();

        return view('admin.notifications.index', compact('notifications'));
    }

     public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('notifiable_id', Auth::id())
            ->firstOrFail();

        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted successfully');
    }
}
