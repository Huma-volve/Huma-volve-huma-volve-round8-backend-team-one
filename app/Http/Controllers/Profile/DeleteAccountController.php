<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteAccountController extends Controller
{
    public function deleteAccount(Request $request){
        $request->user()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Your account is deleted successfully'
        ]);
    }
}
