<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DoctorResponseRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Models\DoctorProfile;
use App\Models\Review;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Notifications\DoctorNotification;
use App\Notifications\PatientNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

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

        $patientProfile = Auth::user()->patientProfile;

        $review = Review::create([
        'doctor_id' => $booking->doctor_id,
        'patient_id' => $patientProfile->id,
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

        $admins = User::where('user_type', 'admin')->get();

        Notification::send($admins, new AdminNotification([
            'type' => 'New Review',
            'message' => "{$booking->patient->user->name} submitted a review for Dr. {$booking->doctor->user->name}.",
            'review_id' => $review->id,
            'doctor_id' => $booking->doctor_id,
        ]));


        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully.'
        ], 201);
    }

    public function reviews(Request $request)
    {

    $doctorUser = $request->user(); 
    $doctorProfile = $doctorUser->doctorProfile;

    if (!$doctorProfile) {
        return response()->json([
            'success' => false,
            'message' => 'Doctor profile not found for this user.'
        ], 404);
    }
    $reviews = Review::with(['patient.user'])   
                    ->where('doctor_id',  $doctorProfile->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

    return response()->json([
        'success' => true,
        'data' => ReviewResource::collection($reviews)
    ]);
    }

     public function reply(DoctorResponseRequest $request, Review $review)
    {
        $doctor = $request->user()->doctorProfile;
        if (!$doctor || $review->doctor_id != $doctor->id) {
        return response()->json([
            'success' => false,
            'message' => 'It is not appropriate for you to reply to this review'
        ], 403);
        }

        $review->doctor_response = $request->doctor_response;
        $review->responded_at = now();
        $review->save();
        $review->patient->user->notify(new PatientNotification([
            'type' => 'doctor_reply',
            'message' => "Dr. {$review->doctor->user->name} has replied to your review.",
            'review_id' => $review->id,
        ]));

        $admins = User::where('user_type', 'admin')->get();

        Notification::send($admins, new AdminNotification([
            'type' => 'Review Replied',
            'message' => "Dr. {$review->doctor->user->name} replied to a review by {$review->patient->user->name}.",
            'review_id' => $review->id,
            'doctor_id' => $review->doctor_id,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Review successfully replied',
        ], 200);

    }

    public function doctorsWithAvg()
    {
        $doctors = DoctorProfile::with('reviews') 
            ->withAvg('reviews', 'rating')            
            ->get();

        $data = $doctors->map(function($doctor) {
            return [
                'id' => $doctor->id,
                'name' => optional($doctor->user)->name,
                'average_rating' => round($doctor->reviews_avg_rating ?? 0, 1)
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function allReviews()
    {
        $reviews = Review::with(['doctor.user', 'patient.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ReviewResource::collection($reviews),
        ]);
    }


}


