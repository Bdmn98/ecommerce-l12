<?php

use App\Models\Category;

test('admin can list categories', function () {
    $admin = makeAdmin();
    Category::factory()->count(3)->create();

    asUser($admin)->getJson('/api/categories')
        ->assertOk()
        ->assertJsonStructure(['data' => ['data', 'current_page']]);
});

test('admin can create category', function () {
    $admin = makeAdmin();

    $payload = ['name' => 'Electronics', 'description' => 'All tech'];
    asUser($admin)->postJson('/api/categories', $payload)
        ->assertCreated()
        ->assertJsonPath('data.name', 'Electronics');

    $this->assertDatabaseHas('Category', ['name' => 'Electronics']);
});

test('admin can update category', function () {
    $admin = makeAdmin();
    $cat = Category::factory()->create(['name' => 'Old']);

    asUser($admin)->putJson("/api/categories/{$cat->id}", [
        'name' => 'New',
        'description' => 'updated',
    ])->assertOk()->assertJsonPath('data.name', 'New');

    $this->assertDatabaseHas('Category', ['id' => $cat->id, 'name' => 'New']);
});

test('admin can delete category', function () {
    $admin = makeAdmin();
    $cat = Category::factory()->create();

    asUser($admin)->deleteJson("/api/categories/{$cat->id}")
        ->assertOk();

    $this->assertSoftDeleted('Category', ['id' => $cat->id]);
});

test('category validation triggers handler (422)', function () {
    $admin = makeAdmin();

    asUser($admin)->postJson('/api/categories', [
        'description' => 'desc only'
    ])->assertStatus(422)->assertJsonStructure(['message', 'errors']);
});
