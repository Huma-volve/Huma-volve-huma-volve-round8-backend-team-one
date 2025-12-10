<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SavedCardRequest;
use App\Http\Resources\SavedCardResource;
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

        // Check for duplicate card (same brand and last four)
        $existingCard = $user->savedCards()
            ->where('brand', $request->brand)
            ->where('last_four', $request->last_four)
            ->first();

        if ($existingCard) {
            return response()->json(['message' => 'This card is already saved in your profile.'], 409); // 409 Conflict
        }

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
        $card = Auth::user()->savedCards()->find($id);

        if (! $card) {
            return response()->json(['message' => 'Card not found.'], 404);
        }

        $card->delete();

        return response()->json(['message' => 'Card deleted successfully']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $card = Auth::user()->savedCards()->find($id);

        if (! $card) {
            return response()->json(['message' => 'Card not found.'], 404);
        }

        $validated = $request->validate([
            'exp_month' => 'integer|min:1|max:12',
            'exp_year' => 'integer|min:'.date('Y'),
            'is_default' => 'boolean',
            // Allow updating metadata if needed, but not token usually
        ]);

        if ($request->has('is_default') && $request->is_default) {
            Auth::user()->savedCards()->where('id', '!=', $id)->update(['is_default' => false]);
            $card->is_default = true;
        }

        $card->update($request->except(['is_default'])); // Update other fields
        // Note: We manually handled is_default to ensure atomicity/logic before saving if needed,
        // but $card->update() with 'is_default' in array would also work if we did the mass update first.
        // Let's just do standard update but ensure single default.
        if (isset($validated['is_default']) && $validated['is_default'] == true) {
            // Logic already handled above for clearing others
        } else {
            // If setting to false, we just let it be false.
        }

        // Re-saving to ensure changes persist if update() didn't cover everything or to be clean
        $card->fill($request->only(['exp_month', 'exp_year', 'is_default']));
        $card->save();

        return new SavedCardResource($card);
    }

    /**
     * Set the card as default.
     */
    public function setDefault(string $id)
    {
        $user = Auth::user();
        $card = $user->savedCards()->find($id);

        if (! $card) {
            return response()->json(['message' => 'Card not found.'], 404);
        }

        // Unset other defaults
        $user->savedCards()->where('id', '!=', $id)->update(['is_default' => false]);

        // Set this one as default
        $card->update(['is_default' => true]);

        return new SavedCardResource($card);
    }
}
