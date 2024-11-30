<?php

use Domain\NewsProviders\Models\NewsProvider;
use Domain\Users\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->artisan('db:seed');
});

it('can get preferences and must contain null', function () {
    $request = actingAs($this->user)
        ->getJson(route('api.v1.user.preferences.index'))
        ->assertJsonStructure([
            'message',
            'data' => [
                'news_providers',
                'categories',
                'authors',
            ],
        ]);

    $response = $request->collect();

    expect($response->get('data.news_providers'))
        ->toBeNull()
        ->and($response->get('data.categories'))
        ->toBeNull()
        ->and($response->get('data.authors'))
        ->toBeNull();
});

it('can update preferences and get the updated preferences', function () {
    $categories = ['technology'];
    $newsProvider = ['The Guardian',  'New York Times'];
    $authors = ['john doe'];

    Cache::shouldReceive('remember')
        ->with('available_news_providers', 300, Closure::class)
        ->andReturn(
            NewsProvider::pluck('name')->implode(', ')
        );

    actingAs($this->user)
        ->postJson(route('api.v1.user.preferences.store'), [
            'news_providers' => $newsProvider,
            'categories' => $categories,
            'authors' => $authors,
        ])
        ->assertOk()
        ->assertJsonStructure([
            'message',
        ]);

    $request = actingAs($this->user)
        ->getJson(route('api.v1.user.preferences.index'))
        ->assertJsonStructure([
            'message',
            'data' => [
                'news_providers',
                'categories',
                'authors',
            ],
        ]);

    $response = $request->collect();

    $data = $response->get('data');

    expect(ksort($data['news_providers']))
        ->toEqual(ksort($newsProvider))
        ->and(ksort($data['categories']))
        ->toEqual(ksort($categories))
        ->and(ksort($data['authors']))
        ->toEqual(ksort($authors));
});

it('can\'t update preferences with invalid news providers', function () {
    $categories = ['technology'];
    $authors = ['john doe'];

    actingAs($this->user)
        ->postJson(route('api.v1.user.preferences.store'), [
            'news_providers' => ['invalid'],
            'categories' => $categories,
            'authors' => $authors,
        ])
        ->assertJsonValidationErrorFor('news_providers');
});
