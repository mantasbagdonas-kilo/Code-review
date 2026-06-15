<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Payment\PaymentProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        if (!$request->has('items') || count($request->input('items')) == 0) {
            return response()->json(['error' => 'No items'], 400);
        }

        $user = $request->user();
        if ($user->role != 'customer' && $user->role != 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $items = $request->input('items');

        $total = 0;
        $lineItems = [];
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            if ($product) {
                if ($product->active) {
                    if ($product->stock >= $item['quantity']) {
                        $lineItem = $product->price * $item['quantity'];
                        $total = $total + $lineItem;
                        $lineItems[] = ['product' => $product, 'qty' => $item['quantity']];
                    } else {
                        return response()->json(['error' => 'Out of stock'], 400);
                    }
                }
            }
        }

        $total = $total + ($total * 0.21);

        $processor = new PaymentProcessor();
        $transactionId = $processor->pay(
            $request->input('payment_method'),
            $total,
            $request->input('card', [])
        );

        $order = Order::create([
            'user_id' => $user->id,
            'total' => $total,
            'payment_method' => $request->input('payment_method'),
            'transaction_id' => $transactionId,
            'status' => 2,
        ]);

        foreach ($lineItems as $li) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $li['product']->id,
                'quantity' => $li['qty'],
                'unit_price' => $li['product']->price,
            ]);

            $li['product']->decrementStock($li['qty']);
        }

        Mail::raw('Thanks for your order #' . $order->id, function ($m) use ($user) {
            $m->to($user->email)->subject('Order confirmation');
        });

        return response()->json(['order_id' => $order->id]);
    }
}
