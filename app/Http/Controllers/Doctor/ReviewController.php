<?php

namespace App\Http\Controllers\Doctor;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $user = Auth::user(); 
        if (!$user || !$user->doctorProfile) {
            return redirect()->route('login')->with('error', 'You must be logged in as a doctor to access this page.');
        }

        $doctorId = $user->doctorProfile->id;

        $reviews = Review::with(['patient.user'])
                        ->where('doctor_id', $doctorId)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('doctor.reviews.index', compact('reviews'));
    }

    public function reply($review)
    {
        $review = Review::with('patient.user')->findOrFail($review);

        $doctorId = Auth::user()->doctorProfile->id;
        if ($review->doctor_id != $doctorId) {
            abort(403, 'Unauthorized action.');
        }

        return view('doctor.reviews.reply', compact('review'));
    }

    public function saveReply(Request $request, Review $review)
    {
        $request->validate([
            'doctor_response' => 'required|string|max:1000',
        ]);

        $review->doctor_response = $request->doctor_response;
        $review->save();

        return redirect()->route('doctor.reviews.index')
                         ->with('success', __('Your response has been sent successfully.'));
    }
}
