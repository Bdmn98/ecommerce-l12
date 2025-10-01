<?php

namespace Database\Seeders;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        for ($i = 0; $i < 15; $i++) {
            $user = $customers->random();
            $order = Order::factory()->create([
                'user_id' => $user->id,
                'status' => fake()->randomElement(OrderStatusEnum::cases())->value,
            ]);


            $orderProducts = $products->random(rand(1, 3));

            foreach ($orderProducts as $product) {
                $qty = rand(1, 3);
                $order->products()->attach($product->id, [
                    'quantity' => $qty,
                    'price' => $product->price,
                ]);
            }


            $order->update([
                'total_amount' => $order->products->sum(fn($p) => $p->pivot->price * $p->pivot->quantity),
            ]);
        }
    }
}
