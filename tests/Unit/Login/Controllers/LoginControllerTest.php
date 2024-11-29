<?php

use Domain\Users\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->route = route('api.v1.auth.login');
});

it('can allow user to login', function () {
    postJson($this->route, [
        'email' => $this->user->email,
        'password' => 'password',
    ])->assertOk()
        ->assertJsonStructure([
            'message', 'user', 'token',
        ]);

    assertDatabaseHas('personal_access_tokens', [
        'tokenable_id' => $this->user->id,
        'tokenable_type' => User::class,
    ]);
});

it('can\'t login with invalid credentials', function () {
    postJson($this->route,
        [
            'email' => $this->user->email,
            'password' => 'invalid-password',
        ])
        ->assertStatus(401)
        ->assertJsonStructure([
            'message',
        ]);
});
