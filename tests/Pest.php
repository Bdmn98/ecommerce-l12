<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');

/**
 * Quick helper to create an admin user.
 */
function makeAdmin(array $overrides = [])
{
    return \App\Models\User::factory()->create(array_merge([
        'role' => 'admin',
        'password' => Hash::make('password'),
    ], $overrides));
}

/**
 * Quick helper to create a customer user.
 */
function makeCustomer(array $overrides = [])
{
    return \App\Models\User::factory()->create(array_merge([
        'role' => 'customer',
        'password' => Hash::make('password'),
    ], $overrides));
}

/**
 * Shortcut for Sanctum actingAs.
 * Returns the Pest test instance so you can chain ->getJson(), ->postJson(), etc.
 */
function asUser($user)
{
    return test()->actingAs($user, 'sanctum');
}
