<?php

use App\Models\Product;

test('customer can add and update cart item', function () {
    $user = makeCustomer();
    $product = Product::factory()->create(['stock' => 20, 'price' => 50]);

    // add
    $add = asUser($user)->postJson('/api/cart', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $add->assertCreated()
        ->assertJsonStructure(['data' => ['id', 'product_id', 'quantity']]);

    $cartId = $add->json('data.id');

    // update quantity (note: request validator wants product_id too)
    $upd = asUser($user)->putJson("/api/cart/{$cartId}", [
        'product_id' => $product->id,
        'quantity' => 3,
    ]);

    $upd->assertOk();

    $this->assertDatabaseHas('Cart', [
        'id' => $cartId,
        'user_id' => $user->id,
        'quantity' => 3,
    ]);
});
