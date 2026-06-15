<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function charge(float $amount, array $card): string;

    public function refund(string $transactionId): bool;

    public function createSubscription(int $customerId, string $plan): string;

    public function cancelSubscription(string $subscriptionId): bool;

    public function capturePartial(string $transactionId, float $amount): bool;

    public function payout(int $vendorId, float $amount): string;
}
