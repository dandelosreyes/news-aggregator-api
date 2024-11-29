<?php

namespace Domain\NewsProviders\Services;

use Domain\NewsProviders\Actions\SaveArticleAction;
use Domain\NewsProviders\DTO\NewYorkTimes\TopStoriesDTO;
use Domain\NewsProviders\Interfaces\NewsProviderServiceInterface;
use Domain\NewsProviders\Models\NewsProvider;
use Domain\NewsProviders\Models\NewsProviderCredential;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewYorkTimesService implements NewsProviderServiceInterface
{
    const NEW_YORK_TIMES_RATE_LIMIT = 'new_york_times_rate_limit';

    const NEW_YORK_TIMES_LAST_REQUEST = 'new_york_times_last_request';

    private SaveArticleAction $saveArticleAction;

    public function __construct()
    {
        $this->ensureRateLimitIsSet();
        $this->saveArticleAction = new SaveArticleAction;
    }

    public function reduceRateLimit(): void
    {
        $this->ensureRateLimitIsSet();

        $currentRateLimit = Cache::get(self::NEW_YORK_TIMES_RATE_LIMIT);

        Cache::set(self::NEW_YORK_TIMES_RATE_LIMIT, $currentRateLimit - 1);
    }

    public function ensureRateLimitIsSet(): void
    {
        if (! Cache::has(self::NEW_YORK_TIMES_RATE_LIMIT)) {
            $this->rateLimit();
        }
    }

    public function rateLimit(): int
    {
        return Cache::remember(self::NEW_YORK_TIMES_RATE_LIMIT, now()->addDay(), function () {
            return 500;
        });
    }

    public function baseEndpoint(): string
    {
        return 'https://api.nytimes.com/svc/topstories/v2/';
    }

    private function allowedTopStories(): array
    {
        return [
            'arts', 'automobiles', 'books/review', 'business', 'fashion', 'food', 'health', 'home', 'insider', 'magazine', 'movies', 'nyregion', 'obituaries', 'opinion', 'politics', 'realestate', 'science', 'sports', 'sundayreview', 'technology', 'theater', 't-magazine', 'travel', 'upshot', 'us', 'world',
        ];
    }

    public function getTopStories(
        string $category
    ): ?array {
        if (! in_array($category, $this->allowedTopStories())) {
            return null;
        }

        $endpoint = Str::of($this->baseEndpoint())
            ->append($category)
            ->append('.json')
            ->append('?api-key=')
            ->append($this->getCredentials()['api_key']);

        $this->ensureTimeoutPerRequestIsEnforced();

        $response = Http::get($endpoint);

        $this->reduceRateLimit();

        if (! $response->ok()) {
            return null;
        }

        $results = collect($response->collect()->get('results'));

        return $results->map(function ($result) {
            $newYorkTimesDTO = TopStoriesDTO::from($result);

            $this->saveArticleAction->onQueue()->execute($newYorkTimesDTO);
        })->toArray();
    }

    public function getCredentials(): array
    {
        $credential = Cache::remember('new_york_times_credential', 300, function () {
            return NewsProviderCredential::query()
                ->where('news_provider_id', NewsProvider::where('name', 'New York Times')->first()->id)
                ->first();
        });

        return [
            'api_key' => $credential?->api_key ?
                Crypt::decryptString($credential->api_key) : null,
            'secret_key' => $credential?->secret_key ? Crypt::decryptString($credential->secret_key) : null,
        ];
    }

    public function getNews(string $query) {}

    public function ensureTimeoutPerRequestIsEnforced(): void
    {
        $lastRequestTime = Cache::get(self::NEW_YORK_TIMES_LAST_REQUEST);

        if (! $lastRequestTime || now()->diffInSeconds($lastRequestTime) >= 12) {
            Cache::put('last_request_time', now());

            return;
        }

        sleep(12);
    }
}
