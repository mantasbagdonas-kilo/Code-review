<?php

namespace App\Services\Shipping;

use App\Models\Order;
use RuntimeException;

class FreeShipping extends ShippingMethod
{
    public function cost(Order $order): float
    {
        if ($order->total < 50) {
            throw new RuntimeException('Free shipping requires an order over 50');
        }

        return 0.0;
    }
}
