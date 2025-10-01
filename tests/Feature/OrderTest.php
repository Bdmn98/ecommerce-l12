<?php

use App\Models\Product;

test('customer can place an order from cart', function () {
    $user = makeCustomer();
    $product = Product::factory()->create(['stock' => 5, 'price' => 80]);

    // add to cart
    asUser($user)->postJson('/api/cart', [
        'product_id' => $product->id,
        'quantity' => 2,
    ])->assertCreated();

    // place order
    $resp = asUser($user)->postJson('/api/orders');

    $resp->assertCreated()
        ->assertJsonStructure(['data' => ['id', 'total_amount', 'status']])
        ->assertJsonPath('data.status', 'pending');

    $orderId = $resp->json('data.id');

    // stock decreased?
    $product->refresh();
    expect($product->stock)->toBe(3);

    // order exists
    $this->assertDatabaseHas('Order', ['id' => $orderId, 'user_id' => $user->id]);
});

test('admin can update order status', function () {
    $user = makeCustomer();
    $admin = makeAdmin();
    $product = Product::factory()->create(['stock' => 3, 'price' => 50]);

    asUser($user)->postJson('/api/cart', [
        'product_id' => $product->id,
        'quantity' => 1,
    ])->assertCreated();

    $orderId = asUser($user)->postJson('/api/orders')
        ->assertCreated()->json('data.id');

    // admin updates status
    asUser($admin)->putJson("/api/orders/{$orderId}/status", [
        'status' => 'confirmed',
    ])->assertOk();

    $this->assertDatabaseHas('Order', ['id' => $orderId, 'status' => 'confirmed']);
});
