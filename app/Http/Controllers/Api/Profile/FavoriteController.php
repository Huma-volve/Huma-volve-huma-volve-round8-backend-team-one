<?php

namespace App\Http\Controllers\Api\Profile;

use App\Models\Favorite;
use App\Traits\ApiResponse;
use App\Models\FavoriteDoctor;
use App\Models\PatientProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    use ApiResponse;
    public function index(){

        $patient = PatientProfile::where('user_id', Auth::id())->first();
        if(!$patient){
            return $this->errorResponse('Patient not found!',404);
        }
        $favorits =    Favorite::where('patient_id', $patient->id)
            ->with('doctor')
            
                                    ->get();

        if($favorits->count() > 0){
            return $this->success($favorits,'',200);
        }

        return $this->success(null,'There is no favorites to display!',200);
    }
}
