<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Booking;
use App\Models\Transaction;
use App\Services\Payment\PaymentFactory;
use Illuminate\Http\Request;
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

            // Charge the user
            $result = $gateway->charge(
                $booking->price_at_booking,
                'usd', // Default currency
                $request->payment_method_id
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
                    'currency' => 'usd',
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

                return response()->json(['message' => 'Payment failed: ' . $result['message']], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
