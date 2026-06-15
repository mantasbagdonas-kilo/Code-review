<?php

namespace App\Services\Payment;

use InvalidArgumentException;

class PaymentProcessor
{
    public function pay(string $method, float $amount, array $card): string
    {
        switch ($method) {
            case 'stripe':
                $gateway = new StripeGateway();
                return $gateway->charge($amount, $card);
            case 'paypal':
                $gateway = new PaypalGateway();
                return $gateway->charge($amount, $card);
            default:
                throw new InvalidArgumentException('Unknown payment method: ' . $method);
        }
    }
}
