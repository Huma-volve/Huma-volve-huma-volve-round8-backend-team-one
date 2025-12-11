<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\NotificationRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\ApiResponse;

class NotificationController extends Controller
{
    use ApiResponse;
    public function toggle(NotificationRequest $request){

        $user = User::find(Auth::id());
        $user->update(['status' => $request->enable]);

        return $this->success(null,$request->enable ? 'Notifications are enabled' : 'Notifications are disabled');

    }
}
