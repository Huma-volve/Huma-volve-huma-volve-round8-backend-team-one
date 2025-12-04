<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function charge(float $amount, string $currency, string $source, array $options = []): array;
}
