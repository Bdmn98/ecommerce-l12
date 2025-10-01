<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        $used = [];

        for ($i = 0; $i < 10; $i++) {
            $user = $customers->random();
            $product = $products->random();

            if (in_array($user->id . '-' . $product->id, $used)) {
                continue;
            }

            Cart::factory()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);

            $used[] = $user->id . '-' . $product->id;
        }
    }
}
