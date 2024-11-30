<?php

use Domain\Articles\Models\Article;
use Domain\NewsProviders\Models\NewsProvider;
use Domain\Users\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\artisan;

beforeEach(function () {
    $this->user = User::factory()->create();
    artisan('db:seed');
});

it('returns articles from the newsfeed', function () {
    $newsProvidersId = NewsProvider::pluck('id')->toArray();

    Article::factory()->count(10)
        ->hasAuthors(2)
        ->hasCategories(2)
        ->state([
            'news_provider_id' => fn () => Arr::random($newsProvidersId),
        ])
        ->create();

    $response = actingAs($this->user)
        ->getJson(route('api.v1.newsfeed.index', [
            'per_page' => 10,
        ]));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'meta',
            'links',
        ]);
});

it('returns a no article message found when the user have no matching preferences', function () {
    actingAs($this->user)
        ->postJson(route('api.v1.user.preferences.store'), [
            'news_providers' => [
                'NewsAPI',
            ],
            'categories' => [
                'movies',
                'technology',
                'bitcoin',
            ],
            'authors' => ['John Doe', 'Elizabeth'],
        ])
        ->assertOk();

    Article::factory()
        ->count(10)
        ->hasAuthors(2)
        ->hasCategories(2)
        ->state([
            'news_provider_id' => NewsProvider::where('slug', NewsProvider::PROVIDER_THE_GUARDIAN)->first()->id,
        ])
        ->create();

    $response = actingAs($this->user)
        ->getJson(route('api.v1.newsfeed.index', [
            'per_page' => 10,
        ]));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'message',
        ]);
});

it('can adjust paginated response to desired numbers', function () {
    $newsProvidersId = NewsProvider::pluck('id')->toArray();

    Article::factory()->count(20)
        ->hasAuthors(2)
        ->hasCategories(2)
        ->state([
            'news_provider_id' => fn () => Arr::random($newsProvidersId),
        ])
        ->create();

    $perPage = 5;

    $response = actingAs($this->user)
        ->getJson(route('api.v1.newsfeed.index', [
            'per_page' => $perPage,
        ]));

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'meta',
            'links',
        ]);

    expect($response->collect()->get('meta')['per_page'])->toBe($perPage);
});

it('can query a category', function () {
    $newsProvidersId = NewsProvider::pluck('id')->toArray();

    $article = Article::factory()->count(20)
        ->hasAuthors(2)
        ->hasCategories(2)
        ->state([
            'news_provider_id' => fn () => Arr::random($newsProvidersId),
        ])
        ->create();

    $category = $article->first()->categories->pluck('name')->toArray();
    $author = $article->first()->authors->pluck('name')->toArray();

    actingAs($this->user)
        ->postJson(route('api.v1.user.preferences.store'), [
            'news_providers' => [
                'NewsAPI',
            ],
            'categories' => $category,
            'authors' => $author,
        ]);

    $perPage = 10;

    $response = actingAs($this->user)
        ->getJson(route('api.v1.newsfeed.index', [
            'per_page' => $perPage,
            'categories' => $category,
            'authors' => [],
            'sources' => [
                'New York Times',
            ],
        ]))
        ->assertOk();

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'meta',
            'links',
        ]);

    expect($response->collect()->get('meta')['per_page'])->toBe($perPage);
});
