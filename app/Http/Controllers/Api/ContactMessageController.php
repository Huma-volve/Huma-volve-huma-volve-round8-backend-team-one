<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactMessageRequest;
use App\Models\ContactMessage;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContactMessageController extends Controller
{
    use ApiResponse;

    /**
     * Store a new contact message.
     *
     * Anyone can send a contact message (no authentication required).
     */
    public function store(ContactMessageRequest $request): JsonResponse
    {
        $contactMessage = ContactMessage::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'message' => $request->validated('message'),
        ]);

        return $this->successResponse(
            [
                'id' => $contactMessage->id,
                'name' => $contactMessage->name,
                'email' => $contactMessage->email,
                'message' => $contactMessage->message,
                'created_at' => $contactMessage->created_at->toIso8601String(),
            ],
            'Your message has been sent successfully. We will get back to you soon.',
            201
        );
    }
}
