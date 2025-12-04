<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorResponseRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Models\Review;
use App\Notifications\DoctorNotification;
use App\Notifications\PatientNotification;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
     public function store(ReviewRequest $request)
    {
        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'You can submit a review only after your session is completed.'
            ], 403);
        }

        if (Review::where('booking_id', $booking->id)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'You have already submitted a review for this session.'
        ], 422);
        }

        $review = Review::create([
        'doctor_id' => $booking->doctor_id,
        'patient_id' =>$request->patient_id,
        'booking_id' => $booking->id,
        'rating' => $request->rating,
        'comment' => $request->comment ?? null,
        ]);

        $doctor = $booking->doctor->user;

        $doctor->notify(new DoctorNotification([
            'type' => 'New Review',
            'message' => "You received a new review from {$booking->patient->user->name}.",
            'review_id' => $review->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully.'
        ], 201);
    }

    public function reviews()
    {

    $doctorId = 1;
    $reviews = Review::with(['patient.user'])   
                    ->where('doctor_id', $doctorId)
                    ->orderBy('created_at', 'desc')
                    ->get();

    return response()->json([
        'success' => true,
        'data' => ReviewResource::collection($reviews)
    ]);
    }

     public function reply(DoctorResponseRequest $request, Review $review)
    {

        // if ($review->doctor_id != auth()->id()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'It is not appropriate for you to reply to this review'
        //     ], 403);
        // }

        $review->doctor_response = $request->doctor_response;
        $review->responded_at = now();
        $review->save();
        $review->patient->user->notify(new PatientNotification([
            'type' => 'doctor_reply',
            'message' => "Dr. {$review->doctor->user->name} has replied to your review.",
            'review_id' => $review->id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Review successfully replied',
        ], 200);

    }

}


