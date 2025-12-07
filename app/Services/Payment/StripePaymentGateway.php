<?php

namespace App\Services\Payment;

use Exception;
use Stripe\StripeClient;

class StripePaymentGateway implements PaymentGatewayInterface
{
    protected $stripe;

    public function __construct(?StripeClient $stripe = null)
    {
        $this->stripe = $stripe ?? new StripeClient(config('services.stripe.secret'));
    }

    public function charge(float $amount, string $currency, string $source, array $options = []): array
    {
        try {
            $charge = $this->stripe->paymentIntents->create([
                'amount' => (int) ($amount * 100), // Stripe accepts amount in cents
                'currency' => $currency,
                'payment_method' => $source,
                'confirm' => true,
                'return_url' => $options['return_url'] ?? 'http://localhost', // Required for some payment methods
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never', // For immediate confirmation
                ],
            ]);

            return [
                'success' => true,
                'transaction_id' => $charge->id,
                'data' => $charge->toArray(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        try {
            $params = ['payment_intent' => $transactionId];
            if ($amount) {
                $params['amount'] = (int) ($amount * 100);
            }

            $refund = $this->stripe->refunds->create($params);

            return [
                'success' => true,
                'transaction_id' => $refund->id,
                'data' => $refund->toArray(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
