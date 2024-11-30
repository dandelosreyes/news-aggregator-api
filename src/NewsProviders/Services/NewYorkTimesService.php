<?php

namespace Domain\NewsProviders\Services;

use Domain\NewsProviders\Actions\SaveArticleAction;
use Domain\NewsProviders\DTO\NewsProviders\GetCredentialsDTO;
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

    const NEW_YORK_TIMES_CREDENTIAL = 'new_york_times_credential';

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

    public function getCredentials(): GetCredentialsDTO
    {
        $credential = Cache::remember(self::NEW_YORK_TIMES_CREDENTIAL, 300, function () {
            return NewsProviderCredential::query()
                ->where('news_provider_id', NewsProvider::where('name', 'New York Times')->first()->id)
                ->first();
        });

        return new GetCredentialsDTO(
            apiKey: $credential->api_key ?
                Crypt::decryptString($credential->api_key) : null,
            secretKey: $credential->secret_key ? Crypt::decryptString($credential->secret_key) : null
        );
    }

    public function getNews(string $query)
    {
        if (! in_array($query, $this->allowedTopStories())) {
            return null;
        }

        $endpoint = Str::of($this->baseEndpoint())
            ->append($query)
            ->append('.json')
            ->append('?api-key=')
            ->append($this->getCredentials()->apiKey);

        $this->ensureTimeoutPerRequestIsEnforced();

        $response = Http::get($endpoint);

        $this->reduceRateLimit();

        if (! $response->ok()) {
            return null;
        }

        $results = collect($response->collect()->get('results'));

        return $results->each(function ($result) {
            $newYorkTimesDTO = TopStoriesDTO::from($result);

            $this->saveArticleAction->onQueue()->execute($newYorkTimesDTO);
        })->toArray();
    }

    public function ensureTimeoutPerRequestIsEnforced(): void
    {
        $lastRequestTime = Cache::get(self::NEW_YORK_TIMES_LAST_REQUEST);

        if ($lastRequestTime !== null || now()->diffInSeconds($lastRequestTime) >= 12) {
            Cache::put(self::NEW_YORK_TIMES_LAST_REQUEST, now());

            return;
        }

        sleep(12);
    }
}
