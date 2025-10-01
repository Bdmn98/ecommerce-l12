<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsSmokeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function category_product_relation_and_soft_delete()
    {
        $cat = Category::factory()->create();
        $p = Product::factory()->create(['category_id' => $cat->id]);

        $this->assertTrue($p->category->is($cat));

        $cat->delete();
        $this->assertSoftDeleted('Category', ['id' => $cat->id]);
    }

    /** @test */
    public function payment_belongs_to_order()
    {
        $order = Order::factory()->create();
        $pay = Payment::factory()->create(['order_id' => $order->id, 'amount' => 123.45]);

        $this->assertTrue($pay->order->is($order));
        $this->assertEquals('123.45', (string)$pay->amount);
    }

    /** @test */
    public function product_has_category_and_prices_are_casted()
    {
        $cat = Category::factory()->create();
        $p = Product::factory()->create(['category_id' => $cat->id, 'price' => 99.99]);

        $this->assertTrue($p->category->is($cat));
        $this->assertEquals('99.99', (string)$p->price);
    }
}
