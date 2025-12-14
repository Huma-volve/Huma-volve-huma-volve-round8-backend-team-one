<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Booking;
use App\Models\Transaction;
use App\Services\Payment\PaymentFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function process(PaymentRequest $request)
    {
        $user = Auth::user();
        $booking = Booking::findOrFail($request->booking_id);

        // Authorization check
        if ($booking->patient_id !== $user->patientProfile->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($booking->payment_status === 'paid') {
            return response()->json(['message' => 'Booking is already paid'], 400);
        }

        try {
            $gateway = PaymentFactory::create($request->gateway);

            $paymentMethodId = $request->payment_method_id;

            // If no explicit method provided, look for default saved card
            if (! $paymentMethodId) {
                $defaultCard = $user->savedCards()->where('is_default', true)->first();
                if ($defaultCard) {
                    $paymentMethodId = $defaultCard->provider_token;
                }
            }

            // If still no method, check if user has ANY cards to provide a better message?
            // Or just fail as requested. Ideally we should probably list cards if ambiguous,
            // but requirements say: "if user doesn't have credit card return friendly message"
            if (! $paymentMethodId) {
                 // Check if user has saved cards but none default? Or just none at all?
                 if ($user->savedCards()->count() === 0) {
                     return response()->json(['message' => 'Please add a credit card to proceed.'], 400);
                 }
                 return response()->json(['message' => 'Please select a payment method.'], 400);
            }

            // Charge the user
            $result = $gateway->charge(
                $booking->price_at_booking,
                'egp', // Default currency
                $paymentMethodId
            );

            if ($result['success']) {
                DB::beginTransaction();

                // Update Booking
                $booking->update([
                    'payment_status' => 'paid',
                    'payment_transaction_id' => $result['transaction_id'],
                    'status' => 'confirmed', // Auto-confirm after payment
                ]);

                // Create Transaction Record
                $transaction = Transaction::create([
                    'booking_id' => $booking->id,
                    'external_id' => $result['transaction_id'],
                    'amount' => $booking->price_at_booking,
                    'type' => 'payment',
                    'status' => 'success',
                    'gateway' => $request->gateway,
                    'payload' => $result['data'],
                    'currency' => 'egp',
                ]);

                DB::commit();

                return new PaymentResource($transaction);
            } else {
                // Log failed transaction attempt if needed
                Transaction::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->price_at_booking,
                    'type' => 'payment',
                    'status' => 'failed',
                    'gateway' => $request->gateway,
                    'failure_reason' => $result['message'],
                ]);

                // Update booking status to failed
                $booking->update(['payment_status' => 'failed']);

                return response()->json(['message' => 'Payment failed: '.$result['message']], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'An error occurred: '.$e->getMessage()], 500);
        }
    }
}
