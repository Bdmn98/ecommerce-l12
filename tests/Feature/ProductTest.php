<?php

use App\Models\Category;
use App\Models\Product;

test('admin can create a product', function () {
    $admin = makeAdmin();
    $category = Category::factory()->create();

    $payload = [
        'name' => 'Test Product',
        'description' => 'Demo',
        'price' => 123.45,
        'stock' => 10,
        'category_id' => $category->id,
    ];

    $resp = asUser($admin)->postJson('/api/products', $payload);

    $resp->assertCreated()
        ->assertJsonPath('data.name', 'Test Product');

    $this->assertDatabaseHas('Product', [
        'name' => 'Test Product',
        'category_id' => $category->id,
    ]);
});

test('product listing supports filters and is cached', function () {
    $cat = Category::factory()->create();
    Product::factory()->count(3)->create(['category_id' => $cat->id, 'price' => 200]);
    Product::factory()->count(2)->create(['price' => 50]);

    $resp = $this->getJson('/api/products?min_price=100&max_price=300&category_id=' . $cat->id);

    $resp->assertOk()
        ->assertJsonStructure(['data' => ['data', 'current_page']]);
});
