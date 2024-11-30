<?php

use Domain\NewsProviders\Models\NewsProvider;
use Domain\NewsProviders\Models\NewsProviderCredential;
use Domain\NewsProviders\Services\NewYorkTimesService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Cache::flush();
    Queue::fake();

    $this->newsProvider = NewsProvider::factory()->create(['name' => 'New York Times']);
});

it('sets the initial rate limit if not already set', function () {
    $service = App::make(NewYorkTimesService::class);
    $service->ensureRateLimitIsSet();

    expect(Cache::has(NewYorkTimesService::NEW_YORK_TIMES_RATE_LIMIT))->tobeTrue()
        ->and(Cache::get(NewYorkTimesService::NEW_YORK_TIMES_RATE_LIMIT))->toBe(500);
});

it('reduces the rate limit correctly', function () {
    $service = App::make(NewYorkTimesService::class);

    Cache::put(NewYorkTimesService::NEW_YORK_TIMES_RATE_LIMIT, 500);

    $service->reduceRateLimit();

    expect(Cache::get(NewYorkTimesService::NEW_YORK_TIMES_RATE_LIMIT))->toBe(499);
});

it('fetches credentials correctly', function () {
    NewsProviderCredential::factory()
        ->create([
            'news_provider_id' => $this->newsProvider->id,
            'api_key' => Crypt::encryptString('it-api-key'),
            'secret_key' => Crypt::encryptString('it-secret-key'),
        ]);

    $service = App::make(NewYorkTimesService::class);

    $credentials = $service->getCredentials();

    expect($credentials->apiKey)->toBe('it-api-key')
        ->and($credentials->secretKey)->toBe('it-secret-key');
});

it('fetches top stories and saves articles', closure: function (int $status, array $responseBody) {
    Cache::shouldReceive('has')
        ->with(NewYorkTimesService::NEW_YORK_TIMES_RATE_LIMIT)
        ->andReturn(500);

    Cache::shouldReceive('get')
        ->with(NewYorkTimesService::NEW_YORK_TIMES_LAST_REQUEST)
        ->andReturn(
            now()->subSeconds(12)
        );

    $newsProviders = NewsProvider::factory()
        ->create(['name' => 'New York Times']);

    $newsProviderCredential = NewsProviderCredential::factory()
        ->create([
            'news_provider_id' => $newsProviders->id,
            'api_key' => Crypt::encryptString('it-api-key'),
            'secret_key' => Crypt::encryptString('it-secret-key'),
        ]);

    Cache::shouldReceive('remember')
        ->andReturn(
            $newsProviderCredential
        );

    Http::fake([
        'https://api.nytimes.com/*' => Http::response($responseBody, $status),
    ]);

    $service = App::make(NewYorkTimesService::class);

    Cache::shouldReceive('put')
        ->with(NewYorkTimesService::NEW_YORK_TIMES_LAST_REQUEST, \Mockery::type(\Illuminate\Support\Carbon::class))
        ->andReturn(true);

    Cache::shouldReceive('get')
        ->with(NewYorkTimesService::NEW_YORK_TIMES_RATE_LIMIT)
        ->andReturn(500);

    Cache::shouldReceive('set')
        ->with(NewYorkTimesService::NEW_YORK_TIMES_RATE_LIMIT, 499)
        ->andReturn(true);

    $stories = $service->getNews('movies');

    expect($stories)
        ->toHaveCount($responseBody['num_results'])
        ->and(Http::recorded())
        ->toHaveCount(1);
})->with('newYorkTimesTopArticles');

it('enforces timeout between requests', function () {
    Cache::shouldReceive('has')
        ->with(NewYorkTimesService::NEW_YORK_TIMES_RATE_LIMIT)
        ->andReturn(500);

    Cache::shouldReceive('get')
        ->with(NewYorkTimesService::NEW_YORK_TIMES_LAST_REQUEST)
        ->andReturn(
            now()->subSeconds(12)
        );

    $service = App::make(NewYorkTimesService::class);

    Cache::shouldReceive('put')
        ->once()
        ->with(NewYorkTimesService::NEW_YORK_TIMES_LAST_REQUEST, \Mockery::type(\Illuminate\Support\Carbon::class))
        ->andReturn(true);

    $service->ensureTimeoutPerRequestIsEnforced();

    $lastRequest = Cache::get(NewYorkTimesService::NEW_YORK_TIMES_LAST_REQUEST);

    expect($lastRequest->diffInSeconds(now()))->toBeGreaterThanOrEqual(12);
});
