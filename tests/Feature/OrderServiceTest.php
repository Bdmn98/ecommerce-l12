<?php

use App\Models\Cart;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
    $this->svc = app(OrderService::class);
});

test('createFromCart calculates total and clears cart', function () {
    $user = makeCustomer();
    $p1 = Product::factory()->create(['price' => 100, 'stock' => 10]);
    $p2 = Product::factory()->create(['price' => 50, 'stock' => 10]);

    Cart::create(['user_id' => $user->id, 'product_id' => $p1->id, 'quantity' => 2]); // 200
    Cart::create(['user_id' => $user->id, 'product_id' => $p2->id, 'quantity' => 1]); //  50

    $order = $this->svc->createFromCart($user);

    expect($order->total_amount)->toBe(250.0)
        ->and(Cart::where('user_id', $user->id)->count())->toBe(0);

    // stoklar düştü mü?
    $p1->refresh();
    $p2->refresh();
    expect($p1->stock)->toBe(8)
        ->and($p2->stock)->toBe(9);
});

test('createFromCart throws validation when stock insufficient', function () {
    $user = makeCustomer();
    $p = Product::factory()->create(['price' => 100, 'stock' => 1]);
    Cart::create(['user_id' => $user->id, 'product_id' => $p->id, 'quantity' => 5]);

    $this->expectException(\Illuminate\Validation\ValidationException::class);

    $this->svc->createFromCart($user);
});

test('createFromCart applies SAVE10 discount', function () {
    $user = makeCustomer();
    $p = Product::factory()->create(['price' => 120, 'stock' => 10]);

    Cart::create(['user_id' => $user->id, 'product_id' => $p->id, 'quantity' => 1]); // 120

    $order = $this->svc->createFromCart($user, 'SAVE10');

    expect($order->total_amount)->toBe(108.0);
});
