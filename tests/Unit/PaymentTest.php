<?php

use App\Enums\OrderStatusEnum;
use App\Models\Product;

test('customer can create a payment for order (happy path)', function () {
    $user = makeCustomer();
    $p = Product::factory()->create(['price' => 50, 'stock' => 5]);


    asUser($user)->postJson('/api/cart', ['product_id' => $p->id, 'quantity' => 2])->assertCreated();
    $orderId = asUser($user)->postJson('/api/orders')->assertCreated()->json('data.id');


    $resp = asUser($user)->postJson("/api/orders/{$orderId}/payments");

    $resp->assertCreated()->assertJsonPath('data.order_id', $orderId);

    $this->assertDatabaseHas('Order', [
        'id' => $orderId,
        'status' => OrderStatusEnum::PAID->value
    ]);

});
