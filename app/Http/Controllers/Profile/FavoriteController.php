<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\FavoriteDoctor;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    use ApiResponse;
    public function index(){

        $favorits = FavoriteDoctor::with('doctorProfile.user')
                                    ->where('user_id', Auth::id())
                                    ->get();

        if($favorits->count() > 0){
            return $this->success($favorits,"","success",200);
        }

        return $this->success(null,'There is no favorites to display!',"success",200);
    }
}
