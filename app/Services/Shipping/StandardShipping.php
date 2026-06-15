<?php

namespace App\Services\Shipping;

use App\Models\Order;

class StandardShipping extends ShippingMethod
{
    public function cost(Order $order): float
    {
        // Flat 5.00 under 100, free over.
        return $order->total >= 100 ? 0.0 : 5.0;
    }
}
