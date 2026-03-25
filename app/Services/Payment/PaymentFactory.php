<?php

namespace App\Services\Payment;

use Exception;

class PaymentFactory
{
    protected static $mock;

    public static function mock(PaymentGatewayInterface $mock)
    {
        self::$mock = $mock;
    }

    public static function clearMock()
    {
        self::$mock = null;
    }

    public static function create(string $gateway): PaymentGatewayInterface
    {
        if (self::$mock) {
            return self::$mock;
        }

        switch ($gateway) {
            case 'stripe':
                return new StripePaymentGateway();
            // Future gateways can be added here
            default:
                throw new Exception("Unsupported payment gateway: {$gateway}");
        }
    }
}
