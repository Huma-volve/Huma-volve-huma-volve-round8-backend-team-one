<?php

namespace Tests\Unit;

use App\Services\Payment\StripePaymentGateway;
use Mockery;
use PHPUnit\Framework\TestCase;
use Stripe\PaymentIntent;
use Stripe\Service\PaymentIntentService;
use Stripe\StripeClient;

class PaymentServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_stripe_gateway_can_charge()
    {
        // Mock Stripe PaymentMethod and PaymentIntent response
        $paymentIntent = PaymentIntent::constructFrom([
            'id' => 'pi_123456',
            'amount' => 10000,
            'currency' => 'egp',
            'status' => 'succeeded',
        ]);

        $paymentIntentServiceMock = Mockery::mock(PaymentIntentService::class);
        $paymentIntentServiceMock->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($args) {
                return $args['amount'] === 10000 &&
                       $args['currency'] === 'egp' &&
                       $args['payment_method'] === 'pm_card_visa';
            }))
            ->andReturn($paymentIntent);

        $stripeClientMock = Mockery::mock(StripeClient::class);
        $stripeClientMock->paymentIntents = $paymentIntentServiceMock;

        // Inject Mock
        $gateway = new StripePaymentGateway($stripeClientMock);

        $result = $gateway->charge(100.00, 'egp', 'pm_card_visa', ['return_url' => 'http://test.com']);

        $this->assertTrue($result['success']);
        $this->assertEquals('pi_123456', $result['transaction_id']);
    }
}
