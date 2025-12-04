<?php

namespace App\Http\Controllers;

use App\Http\Requests\SavedCardRequest;
use App\Http\Resources\SavedCardResource;
use App\Models\SavedCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavedCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cards = Auth::user()->savedCards()->orderBy('is_default', 'desc')->get();
        return SavedCardResource::collection($cards);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SavedCardRequest $request)
    {
        $user = Auth::user();

        if ($request->is_default) {
            $user->savedCards()->update(['is_default' => false]);
        }

        $card = $user->savedCards()->create($request->validated());

        return new SavedCardResource($card);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $card = Auth::user()->savedCards()->findOrFail($id);
        $card->delete();

        return response()->json(['message' => 'Card deleted successfully']);
    }
}
