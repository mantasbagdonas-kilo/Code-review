<?php

namespace App\Services\Payment;

use Illuminate\Support\Str;

class StripeGateway implements PaymentGatewayInterface
{
    public function charge(float $amount, array $card): string
    {
        $apiKey = env('STRIPE_SECRET');

        // Pretend we call the Stripe SDK here with $apiKey.
        return 'stripe_' . Str::random(16);
    }

    public function refund(string $transactionId): bool
    {
        return true;
    }

    public function createSubscription(int $customerId, string $plan): string
    {
        return 'sub_' . Str::random(10);
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        return true;
    }

    public function capturePartial(string $transactionId, float $amount): bool
    {
        return true;
    }

    public function payout(int $vendorId, float $amount): string
    {
        return 'po_' . Str::random(10);
    }
}
