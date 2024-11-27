<?php

it('can register a new user', function () {
    $user = [
        'name' => 'John Doe',
        'email' => 'jdoe@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    \Pest\Laravel\post(route('v1.auth.register'), $user)
        ->assertJsonStructure([
            'message', 'user',
        ])
        ->assertOk();
});
