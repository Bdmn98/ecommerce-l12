<?php

it('handles validation exception globally', function () {
    $resp = $this->postJson('/api/auth/register', []); // empty payload
    $resp->assertStatus(422)->assertJsonValidationErrors(['name', 'email', 'password']);
});
