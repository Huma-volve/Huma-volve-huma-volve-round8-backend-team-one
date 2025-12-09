<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\PaymentMethodRequest;
use App\Models\SavedCard;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    use ApiResponse;
    public function index()
    {
        if(Auth::user()->savedCards->isEmpty()){
            return $this->success(null,'Nothing to display here!');
        }

        return $this->success(Auth::user()->savedCards,"");

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

        return $this->success($method,'Card Added Successfully',201);
    }

    public function setDefault($id)
    {
        $user = Auth::user();

        if($user->savedCards->isEmpty()){
            return $this->success(null,'Nothing to display here!');
        }

        $user->savedCards->update(['is_default' => false]);
        $user->savedCards->where('id', $id)->update(['is_default' => true]);
        return $this->success(null,'Default Updated successfully');
    }
}
