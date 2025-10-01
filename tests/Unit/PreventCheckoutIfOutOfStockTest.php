<?php

namespace Tests\Unit;

use App\Http\Middleware\PreventCheckoutIfOutOfStock;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PreventCheckoutIfOutOfStockTest extends TestCase
{
    use RefreshDatabase;

    private function makeRequestFor(User $user): Request
    {
        $req = Request::create('/api/orders', 'POST');
        $req->setUserResolver(fn() => $user);
        return $req;
    }

    /** @test */
    public function it_blocks_checkout_when_any_item_has_insufficient_stock()
    {
        $user = User::factory()->create();
        $p = Product::factory()->create(['stock' => 1]);
        Cart::create(['user_id' => $user->id, 'product_id' => $p->id, 'quantity' => 5]);

        $mw = new PreventCheckoutIfOutOfStock();
        $resp = $mw->handle($this->makeRequestFor($user), function () {
            return response()->json(['should_not' => 'reach'], 200);
        });

        $this->assertInstanceOf(Response::class, $resp);
        $this->assertEquals(422, $resp->getStatusCode());
    }

    /** @test */
    public function it_allows_checkout_when_all_items_have_enough_stock()
    {
        $user = User::factory()->create();
        $p = Product::factory()->create(['stock' => 10]);
        Cart::create(['user_id' => $user->id, 'product_id' => $p->id, 'quantity' => 2]);

        $mw = new PreventCheckoutIfOutOfStock();
        $resp = $mw->handle($this->makeRequestFor($user), function () {
            return response()->json(['ok' => true], 200);
        });

        $this->assertEquals(200, $resp->getStatusCode());
    }
}
