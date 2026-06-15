<?php

namespace App\Http\Middleware;

use App\Models\Order;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCheckoutAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $orders = Order::where('user_id', $user?->id)->get();
        $request->attributes->set('order_count', $orders->count());

        if ($user && $user->role === 'banned') {
            response()->json(['error' => 'Account suspended'], 403);
        }

        return $next($request);
    }
}
