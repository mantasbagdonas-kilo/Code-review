<?php

namespace App\Services;

use App\Models\Order;

class OrderExporter
{
    public function toCsv(): string
    {
        $orders = Order::all();

        $csv = "id,email,total,status\n";
        foreach ($orders as $order) {
            $csv .= $order->id . ','
                . $order->user->email . ','
                . $order->total . ','
                . $order->status . "\n";
        }

        return $csv;
    }
}
