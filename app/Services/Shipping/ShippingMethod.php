<?php

namespace App\Services\Shipping;

use App\Models\Order;

abstract class ShippingMethod
{
    abstract public function cost(Order $order): float;
}
