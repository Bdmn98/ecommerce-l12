<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->svc = app(OrderService::class);
    }

    /** @test */
    public function it_calculates_total_and_clears_cart()
    {
        $user = User::factory()->create();
        $p1 = Product::factory()->create(['price' => 100, 'stock' => 5]);
        $p2 = Product::factory()->create(['price' => 50, 'stock' => 5]);

        Cart::create(['user_id' => $user->id, 'product_id' => $p1->id, 'quantity' => 2]); // 200
        Cart::create(['user_id' => $user->id, 'product_id' => $p2->id, 'quantity' => 1]); // 50

        $order = $this->svc->createFromCart($user);

        $this->assertEquals('250.00', $order->total_amount);
        $this->assertCount(0, Cart::where('user_id', $user->id)->get());

        $p1->refresh();
        $this->assertEquals(3, $p1->stock);
    }

    /** @test */
    public function it_throws_when_stock_insufficient()
    {
        $user = User::factory()->create();
        $p = Product::factory()->create(['price' => 100, 'stock' => 1]);

        Cart::create(['user_id' => $user->id, 'product_id' => $p->id, 'quantity' => 5]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->svc->createFromCart($user);
    }

    /** @test */
    public function it_applies_discount_coupon()
    {
        $user = User::factory()->create();
        $p = Product::factory()->create(['price' => 120, 'stock' => 5]);

        Cart::create(['user_id' => $user->id, 'product_id' => $p->id, 'quantity' => 1]);

        $order = $this->svc->createFromCart($user, 'SAVE10');

        $this->assertEquals('108.00', $order->total_amount);
    }
}
