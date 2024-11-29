<?php

namespace Domain\NewsProviders\Services;

use Domain\NewsProviders\Actions\SaveArticleAction;
use Domain\NewsProviders\DTO\NewsAPI\NewsApiArticleDTO;
use Domain\NewsProviders\Interfaces\NewsProviderServiceInterface;
use Domain\NewsProviders\Models\NewsProvider;
use Domain\NewsProviders\Models\NewsProviderCredential;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewsAPIService implements NewsProviderServiceInterface
{
	const NEWS_API_RATE_LIMIT = 'news_api_rate_limit';

	const NEWS_API_LAST_REQUEST = 'news_api_last_request';

	private SaveArticleAction $saveArticleAction;

	public function __construct()
	{
		$this->ensureRateLimitIsSet();
		$this->saveArticleAction = new SaveArticleAction;
	}

	public function baseEndpoint()
	{
		return 'https://newsapi.org/v2/';
	}

	public function getCredentials()
	{
		$credential = Cache::remember('new_york_times_credential', 300, function () {
			return NewsProviderCredential::query()
				->where('news_provider_id', NewsProvider::where('name', 'NewsAPI')->first()->id)
				->first();
		});

		return [
			'api_key' => $credential?->api_key ?
				Crypt::decryptString($credential->api_key) : null,
			'secret_key' => $credential?->secret_key ? Crypt::decryptString($credential->secret_key) : null,
		];
	}

	public function getNews(string $query)
	{
		$endpoint = Str::of($this->baseEndpoint())
			->append('everything?q=')
			->append($query);

		$request = Http::withHeaders([
			'X-Api-Key' => $this->getCredentials()['api_key'],
		])
			->get($endpoint);

		$response = $request->collect();

		collect($response['articles'])
			->filter(function ($article) {
				return $article['author'] !== null || $article['title'] !== '[Removed]';
			})
			->map(function ($article) use ($query) {
				$article['keywords'] = [$query];
				$articleDTO = NewsApiArticleDTO::from($article);

				$this->saveArticleAction->onQueue()->execute($articleDTO);
			});
	}

	public function rateLimit()
	{
		return Cache::remember(self::NEWS_API_RATE_LIMIT, now()->addDay(), function () {
			return 1000;
		});
	}

	public function reduceRateLimit()
	{
		$this->ensureRateLimitIsSet();

		$currentRateLimit = Cache::get(self::NEWS_API_RATE_LIMIT);

		Cache::set(self::NEWS_API_RATE_LIMIT, $currentRateLimit - 1);
	}

	public function ensureRateLimitIsSet()
	{
		if (! Cache::has(self::NEWS_API_RATE_LIMIT)) {
			$this->rateLimit();
		}
	}

	public function ensureTimeoutPerRequestIsEnforced()
	{
		return;
	}
}