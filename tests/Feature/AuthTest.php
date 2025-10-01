<?php

test('user can register', function () {
    $resp = $this->postJson('/api/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    $resp->assertCreated()
        ->assertJsonStructure(['data' => ['id', 'name', 'email']])
        ->assertJsonPath('data.email', 'john@example.com');

    $this->assertDatabaseHas('User', ['email' => 'john@example.com']);
});

test('user can login and gets token', function () {
    $user = makeCustomer(['email' => 'jane@example.com']);

    $resp = $this->postJson('/api/auth/login', [
        'email' => 'jane@example.com',
        'password' => 'password',
    ]);

    $resp->assertOk()
        ->assertJsonStructure(['data' => ['token']]);
});

test('me endpoint returns authenticated user', function () {
    $user = makeCustomer();

    asUser($user)->getJson('/api/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', $user->email);
});
