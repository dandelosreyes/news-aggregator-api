<?php

use Domain\NewsProviders\Actions\SaveArticleAction;
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
    $credential = NewsProviderCredential::factory()
	    ->create([
        'news_provider_id' => $this->newsProvider->id,
        'api_key' => Crypt::encryptString('it-api-key'),
        'secret_key' => Crypt::encryptString('it-secret-key'),
    ]);

    $service = App::make(NewYorkTimesService::class);

    $credentials = $service->getCredentials();

    expect($credentials['api_key'])->toBe('it-api-key')
	    ->and($credentials['secret_key'])->toBe('it-secret-key');
});

it('fetches top stories and saves articles', closure: function (int $status, array $responseBody) {
	$newsProviders = NewsProvider::factory()
		->create(['name' => 'New York Times']);

	NewsProviderCredential::factory()
		->create([
			'news_provider_id' => $newsProviders->id,
			'api_key' => Crypt::encryptString('it-api-key'),
			'secret_key' => Crypt::encryptString('it-secret-key')
		]);

    Http::fake([
        'https://api.nytimes.com/*' => Http::response($responseBody, $status),
    ]);

    $service = App::make(NewYorkTimesService::class);

	$stories = $service->getTopStories('movies');

    expect($stories)
	    ->toHaveCount($responseBody['num_results'])
	    ->and(Http::recorded())
	    ->toHaveCount(1);
})->with('newYorkTimesTopArticles');

it('enforces timeout between requests', function () {
    $service = App::make(NewYorkTimesService::class);

    Cache::put(NewYorkTimesService::NEW_YORK_TIMES_LAST_REQUEST, now()->subSeconds(5));

    $start = now();

    $service->ensureTimeoutPerRequestIsEnforced();

    $end = now();

    expect($start->diffInSeconds(date: $end))->toBeGreaterThanOrEqual(12);
});
