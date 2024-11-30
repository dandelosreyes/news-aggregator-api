<?php

namespace Domain\NewsProviders\Services;

use Cache;
use Domain\NewsProviders\Actions\SaveArticleAction;
use Domain\NewsProviders\DTO\NewsProviders\GetCredentialsDTO;
use Domain\NewsProviders\Interfaces\NewsProviderServiceInterface;
use Domain\NewsProviders\Models\NewsProvider;
use Domain\NewsProviders\Models\NewsProviderCredential;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TheGuardianService implements NewsProviderServiceInterface
{
    const THE_GUARDIAN_RATE_LIMIT = 'the_guardian_rate_limit';

    const THE_GUARDIAN_LAST_REQUEST = 'the_guardian_last_request';

    const THE_GUARDIAN_CREDENTIALS = 'the_guardian_credentials';

    private SaveArticleAction $saveArticleAction;

    public function __construct()
    {
        $this->saveArticleAction = new SaveArticleAction;
    }

    public function baseEndpoint(): string
    {
        return 'https://content.guardianapis.com/';
    }

    public function getCredentials(): GetCredentialsDTO
    {
        $credential = Cache::remember(self::THE_GUARDIAN_CREDENTIALS, 300, function () {
            return NewsProviderCredential::query()
                ->whereHas('newsProvider', function ($query) {
                    $query->where('slug', NewsProvider::PROVIDER_THE_GUARDIAN);
                })
                ->first();
        });

        return new GetCredentialsDTO(
            apiKey: $credential?->api_key ?
                Crypt::decryptString($credential->api_key) : null,
            secretKey: $credential?->secret_key ? Crypt::decryptString($credential->secret_key) : null,
        );
    }

    public function getNews(string $query)
    {
        $endpoint = Str::of($this->baseEndpoint())
            ->append("search?q={$query}")
            ->append("&api-key={$this->getCredentials()->apiKey}");

        $request = Http::get($endpoint);

        if ($request->failed()) {
            return;
        }

        $response = $request->collect();

        collect($response['results'])
            ->whenNotEmpty((function ($results) {
                $results->each(function ($result) {
                    $this->saveArticleAction->onQueue()->execute($result);
                });
            }));
    }

    public function rateLimit(): int
    {
        return Cache::remember(self::THE_GUARDIAN_RATE_LIMIT, now()->addDay(), function () {
            return 500;
        });
    }

    public function reduceRateLimit(): void
    {
        $this->ensureRateLimitIsSet();

        $currentRateLimit = Cache::get(self::THE_GUARDIAN_RATE_LIMIT);

        Cache::set(self::THE_GUARDIAN_RATE_LIMIT, $currentRateLimit - 1);
    }

    public function ensureRateLimitIsSet(): void
    {
        if (! Cache::has(self::THE_GUARDIAN_RATE_LIMIT)) {
            $this->rateLimit();
        }
    }

    public function ensureTimeoutPerRequestIsEnforced(): void
    {
        $lastRequestTime = Cache::get(self::THE_GUARDIAN_LAST_REQUEST);

        if (! $lastRequestTime || now()->diffInSeconds($lastRequestTime) >= 12) {
            Cache::put(self::THE_GUARDIAN_LAST_REQUEST, now());

            return;
        }

        sleep(12);
    }
}
