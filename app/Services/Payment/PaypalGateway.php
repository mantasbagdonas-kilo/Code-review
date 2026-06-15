<?php

namespace App\Services\Payment;

use Illuminate\Support\Str;
use RuntimeException;

class PaypalGateway implements PaymentGatewayInterface
{
    public function charge(float $amount, array $card): string
    {
        return 'pp_' . Str::random(16);
    }

    public function refund(string $transactionId): bool
    {
        return true;
    }

    public function createSubscription(int $customerId, string $plan): string
    {
        throw new RuntimeException('PayPal subscriptions are not supported');
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        throw new RuntimeException('Not implemented');
    }

    public function capturePartial(string $transactionId, float $amount): bool
    {
        throw new RuntimeException('Not implemented');
    }

    public function payout(int $vendorId, float $amount): string
    {
        throw new RuntimeException('Not implemented');
    }
}
