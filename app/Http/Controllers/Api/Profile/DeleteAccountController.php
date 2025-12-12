<?php
namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DeleteAccountController extends Controller
{
    use ApiResponse;
    public function deleteAccount(Request $request){
        $request->user()->delete();
        return $this->success(null,'Your account is deleted successfully',200);
    }
}
