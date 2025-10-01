<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create an order from the user's cart.
     * - Validates stock
     * - Creates the order
     * - Attaches products via pivot with quantity/price
     * - Calculates total (with optional discount)
     * - Decrements stock
     * - Clears the cart
     * - Queues a notification after successful commit
     *
     * @param User $user
     * @param string|null $discountCode
     * @return Order
     *
     * @throws ValidationException
     */
    public function createFromCart(User $user, ?string $discountCode = null): Order
    {
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => __('messages.cart_empty'),
            ]);
        }

        return DB::transaction(function () use ($user, $cartItems, $discountCode): Order {
            $total = 0.0;

            // Stock validation
            foreach ($cartItems as $item) {
                /** @var Product|null $product */
                $product = $item->product;

                if (!$product || $product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => __('messages.insufficient_stock', ['name' => $product?->name]),
                    ]);
                }
            }

            // Create the order (initial total 0, status pending)
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0,
                'status' => OrderStatusEnum::PENDING->value,
            ]);

            // Attach each cart item to the order and decrement stock
            foreach ($cartItems as $item) {
                $product = $item->product;

                $order->products()->attach($product->id, [
                    'quantity' => (int)$item->quantity,
                    'price' => $product->price,
                ]);

                $total += (float)$product->price * (int)$item->quantity;

                // Decrement product stock
                $product->decrement('stock', (int)$item->quantity);
            }

            // Apply discount (if any)
            $total = $this->applyDiscount($total, $discountCode);

            // Persist final total
            $order->update(['total_amount' => $total]);

            // Clear user's cart
            Cart::where('user_id', $user->id)->delete();

            // Notify the user after transaction is successfully committed
            DB::afterCommit(function () use ($user, $order): void {
                $user->notify(new OrderPlacedNotification($order));
                Log::info('OrderPlacedNotification queued', [
                    'order_id' => $order->id,
                    'user_id'  => $user->id,
                ]);
            });

            return $order->load('products');
        });
    }

    /**
     * Apply discount codes if provided.
     * Example rule: SAVE10 => 10% off for totals >= 100
     *
     * @param float $total
     * @param string|null $code
     * @return float
     */
    public function applyDiscount(float $total, ?string $code): float
    {
        if (!$code) {
            return $total;
        }

        $normalized = strtoupper(trim($code));

        if ($normalized === 'SAVE10' && $total >= 100) {
            return round($total * 0.9, 2);
        }

        return $total;
    }
}
