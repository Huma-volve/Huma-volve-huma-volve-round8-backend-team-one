<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\PaymentMethodRequest;
use App\Models\SavedCard;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    public function index()
    {
        if(Auth::user()->savedCards->isEmpty()){
            return response()->json([
                'message' => 'Nothing to display here!'
            ]);
        }

        return response()->json([
            'methods' => Auth::user()->savedCards
        ],200);
    }

    public function store(PaymentMethodRequest $request)
    {
        $isDefault = Auth::user()->savedCards->count() == 0;

        $method = SavedCard::create([
            'user_id'          => Auth::id(),
            'provider_token'   => $request->provider_token,
            'brand'            => $request->brand,
            'last_four'        => $request->last_four,
            'is_default'       => $isDefault,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Card Added Successfully',
            'data'    => $method,
        ]);
    }

    public function setDefault($id)
    {
        $user = Auth::user();

        if($user->savedCards->isEmpty()){
            return response()->json([
                'message' => 'Nothing to display here!'
            ]);
        }

        $user->savedCards->update(['is_default' => false]);
        $user->savedCards->where('id', $id)->update(['is_default' => true]);

        return response()->json(['message' => 'Default Updated']);

    }
}
