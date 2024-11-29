<?php

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

it('can register a new user', function () {
    $user = [
        'name' => 'John Doe',
        'email' => 'jdoe@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    postJson(route('api.v1.auth.register'), $user)
        ->assertJsonStructure([
            'message', 'user',
        ])
        ->assertOk();
});

it('can\'t register a new user with incomplete details', function () {
	$user = [
		'name' => null,
		'email' => 'jdoe@example.com',
		'password' => null,
		'password_confirmation' => 'password',
	];

	postJson(route('api.v1.auth.register'), $user)
		->assertJsonStructure([
			'message', 'user',
		])
		->assertOk();
});