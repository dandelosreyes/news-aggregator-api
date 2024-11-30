<?php

use Domain\NewsProviders\Models\NewsProvider;
use Domain\NewsProviders\Models\NewsProviderCredential;
use Domain\NewsProviders\Services\NewsAPIService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    Cache::flush();
    $this->withoutExceptionHandling();
});

it('fetches news and processes articles', function () {
    $newsProvider = NewsProvider::factory()->create(['name' => 'NewsAPI']);

    NewsProviderCredential::factory()->create([
        'news_provider_id' => $newsProvider->id,
        'api_key' => Crypt::encryptString('fake-api-key'),
        'secret_key' => Crypt::encryptString('fake-api-key'),
    ]);

    Http::fake([
        'https://newsapi.org/v2/*' => Http::response([
            'status' => 'ok',
            'totalResults' => 52988,
            'articles' => [
                [
                    'source' => [
                        'id' => null,
                        'name' => '[Removed]',
                    ],
                    'author' => null,
                    'title' => '[Removed]',
                    'description' => '[Removed]',
                    'url' => 'https://removed.com',
                    'urlToImage' => null,
                    'publishedAt' => '2024-11-19T18:33:13Z',
                    'content' => '[Removed]',
                ],
                [
                    'source' => [
                        'id' => 'wired',
                        'name' => 'Wired',
                    ],
                    'author' => 'Geraldine Castro',
                    'title' => "How Researchers Are Using Geospatial Technology to Uncover Mexico's Clandestine Graves",
                    'description' => 'Thousands of hidden graves contain the bodies of the disappeared all across Mexico. Using drones, hyperspectral imaging, and other technologies, scientists and members of the public are uncovering them.',
                    'url' => 'https://www.wired.com/story/how-researchers-are-using-geospatial-technology-to-uncover-mexicos-clandestine-graves/',
                    'urlToImage' => 'https://media.wired.com/photos/671ff78848cef3aaedab6818/191:100/w_1280,c_limit/Science_Ehecati_GettyImages-2166963686.jpg',
                    'publishedAt' => '2024-11-01T11:00:00Z',
                    'content' => 'In 2014, after the disappearance of 43 Ayotzinapa normalistas in Mexico, Silván and other CentroGeo professionals joined the scientific advisory board on the case. During the search for the students,… [+2586 chars]',
                ],
            ],
        ], 200),
    ]);

    $newsApiService = new NewsAPIService;

    $newsApiService->getNews('technology');

    Http::assertSent(fn ($request) => $request->url() === 'https://newsapi.org/v2/everything?q=technology'
        && $request->hasHeader('X-Api-Key', 'fake-api-key'));

    assertDatabaseHas('articles', ['title' => "How Researchers Are Using Geospatial Technology to Uncover Mexico's Clandestine Graves"]);
    assertDatabaseMissing('articles', ['title' => '[Removed]']);
});

//it('ensures rate limit is set', function () {
//	Cache::shouldReceive('has')
//		->with(NewsAPIService::NEWS_API_RATE_LIMIT)
//		->andReturn(false);
//
//	Cache::shouldReceive('remember')
//		->with(NewsAPIService::NEWS_API_RATE_LIMIT, now()->addDay(), Closure::class)
//		->andReturn(1000);
//
//	$newsApiService = new NewsAPIService();
//
//	// Act
//	$newsApiService->ensureRateLimitIsSet();
//
//	// Assert
//	Cache::shouldHaveReceived('has')->once();
//	Cache::shouldHaveReceived('remember')->once();
//})->skip();
//
//it('reduces rate limit', function () {
//	// Arrange
//})->skip();
