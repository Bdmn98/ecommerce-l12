<?php
// tests/Feature/ExampleTest.php
use Pest\Laravel\{get};

it('can access home', function () {
    get('/')->assertStatus(200);
});