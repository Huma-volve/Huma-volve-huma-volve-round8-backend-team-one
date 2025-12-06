<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\FavoriteDoctor;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index(){

        $favorits = FavoriteDoctor::with('doctorProfile.user')
                                    ->where('user_id', Auth::id())
                                    ->get();

        if($favorits->count() > 0){
            return response()->json([
                'status' => 'success',
                'data'   => $favorits
            ],200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'There is no favorites to display!'
        ],200);
    }
}
