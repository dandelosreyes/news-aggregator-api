<?php

namespace Domain\NewsProviders\Interfaces;

use Domain\NewsProviders\DTO\NewsProviders\GetCredentialsDTO;

interface NewsProviderServiceInterface
{
    public function baseEndpoint(): string;

    public function getCredentials(): GetCredentialsDTO;

    public function getNews(string $query);

    public function rateLimit(): int;

    public function reduceRateLimit(): void;

    public function ensureRateLimitIsSet(): void;

    public function ensureTimeoutPerRequestIsEnforced(): void;
}
