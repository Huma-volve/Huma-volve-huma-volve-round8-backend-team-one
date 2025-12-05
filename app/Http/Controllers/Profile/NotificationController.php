<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\NotificationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class NotificationController extends Controller
{
    public function toggle(NotificationRequest $request){

        $user = User::find(Auth::id());
        $user->update(['status' => $request->enable]);

        return response()->json([
            'status'  => 'success',
            'message' => $request->enable ? 'Notifications are enabled' : 'Notifications are disabled'
        ],200);
    }
}
