<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\CartRequest;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = $request->user()->carts()->with('product')->paginate(10);
        return $this->jsonResponseWithPagination($carts, $carts->total());
    }

    public function store(CartRequest $request)
    {
        $userId    = $request->user()->id;
        $productId = $request->validated('product_id');
        $qty       = (int) $request->validated('quantity');

        $cart = DB::transaction(function () use ($userId, $productId, $qty) {
            $row = Cart::firstOrCreate(
                ['user_id' => $userId, 'product_id' => $productId],
                ['quantity' => 0]
            );


            $row->increment('quantity', $qty);

            return $row->fresh('product');
        });

        return $this->successfulResponse($cart, __('Cart updated'), 201);
    }

    public function update(CartRequest $request, Cart $cart)
    {
        $this->authorizeCart($request, $cart);
        $cart->update($request->validated());
        return $this->successfulResponse($cart, __('Cart updated'));
    }

    public function destroy(Request $request, Cart $cart)
    {
        $this->authorizeCart($request, $cart);
        $cart->delete();
        return $this->successfulResponse(null, __('Cart item removed'));
    }

    private function authorizeCart($request,$cart)
    {
        abort_unless($cart->user_id === $request->user()->id, 403, __('Unauthorized'));
    }
}

