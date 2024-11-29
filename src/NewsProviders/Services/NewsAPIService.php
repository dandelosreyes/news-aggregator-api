<?php

namespace Domain\NewsProviders\Services;

use Domain\NewsProviders\Interfaces\NewsProviderServiceInterface;

class NewsAPIService implements NewsProviderServiceInterface
{
	const NEWS_API_RATE_LIMIT = 'news_api_rate_limit';

	const NEWS_API_LAST_REQUEST = 'news_api_last_request';

	public function baseEndpoint()
	{
		return 'https://newsapi.org/v2/';
	}

	public function getCredentials()
	{
		// TODO: Implement getCredentials() method.
	}

	public function getNews(string $query)
	{
		// TODO: Implement getNews() method.
	}

	public function rateLimit()
	{
		// TODO: Implement rateLimit() method.
	}

	public function reduceRateLimit()
	{
		return;
	}

	public function ensureRateLimitIsSet()
	{
		return;
	}

	public function ensureTimeoutPerRequestIsEnforced()
	{
		return;
	}
}