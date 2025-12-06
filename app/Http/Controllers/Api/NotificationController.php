<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
        }
        $notifications = $user->notifications;

        return response()->json([
            'success' => true,
            'notifications' =>  NotificationResource::collection($notifications),
        ]);
    }

    public function unread(Request $request)
    {
        $user = $request->user();
        if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
        }
        $notifications = $user->unreadNotifications;
        return response()->json([
            'success' => true,
            'notifications' => NotificationResource::collection($notifications),
        ]);
    }

    public function markAsRead(Request $request , $id)
    {
        //  $user = \App\Models\User::find(22);
        $user = $request->user();
        if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
        }
        $notification = $user->unreadNotifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }


}
