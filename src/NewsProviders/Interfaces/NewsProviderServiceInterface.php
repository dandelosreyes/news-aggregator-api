<?php

namespace Domain\NewsProviders\Interfaces;

interface NewsProviderServiceInterface
{
	public function baseEndpoint();

	public function getCredentials();

	public function getNews(string $query);

	public function rateLimit();

	public function reduceRateLimit();

	public function ensureRateLimitIsSet();

	public function ensureTimeoutPerRequestIsEnforced();
}