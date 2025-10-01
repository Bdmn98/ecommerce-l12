<?php

namespace App\Http\Middleware;

use App\Models\Cart;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventCheckoutIfOutOfStock
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $items = Cart::with('product')->where('user_id', $user->id)->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('Cart is empty'),
            ], 422);
        }

        foreach ($items as $item) {
            if (!$item->product || $item->product->stock < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => __('Insufficient stock for :name', ['name' => $item->product?->name ?? 'product']),
                ], 422);
            }
        }

        return $next($request);
    }
}
