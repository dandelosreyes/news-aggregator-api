<?php

namespace Domain\NewsProviders\Actions;

use Domain\Articles\Models\Article;
use Domain\Authors\Actions\UpsertAuthorAction;
use Domain\Categories\Actions\UpsertCategoryAction;
use Domain\Keywords\Actions\UpsertKeywordAction;
use Domain\NewsProviders\DTO\NewsAPI\NewsApiArticleDTO;
use Domain\NewsProviders\DTO\NewYorkTimes\TopStoriesDTO;
use Domain\NewsProviders\Models\NewsProvider;
use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

class SaveArticleAction
{
    use QueueableAction;

    public function __construct() {}

    public function execute(
        TopStoriesDTO|NewsApiArticleDTO $articleDTO
    ): Article {
	    return match (true) {
		    $articleDTO instanceof TopStoriesDTO => $this->processTopStories($articleDTO),
		    $articleDTO instanceof NewsApiArticleDTO => $this->processNewsApiArticle($articleDTO),
		    default => new Article(),
	    };
    }

    private function processTopStories(TopStoriesDTO $articleDTO)
    {
        $featuredImage = collect($articleDTO->multimedia)
            ->whenNotEmpty(function ($images) {
                return $images->filter(function ($image) {
                    return $image['format'] === 'Super Jumbo';
                })->first()['url'] ?? null;
            });

        $article = Article::updateOrCreate([
            'original_url' => $articleDTO->url,
        ], [
            'article_unique_id' => Str::uuid(),
            'news_provider_id' => NewsProvider::where('slug', NewsProvider::PROVIDER_NEW_YORK_TIMES)->first()->id,
            'title' => $articleDTO->title,
            'content' => $articleDTO->abstract,
            'published_at' => $articleDTO->publishedDate,
            'original_url' => $articleDTO->url,
            'featured_image_url' => $featuredImage,
        ]);

        $authors = Str::of($articleDTO->byline)
            ->lower()
            ->replace('by ', '')
            ->explode('and')
            ->map(function ($name) {
                $name = Str::of($name)
                    ->trim();

                return (new UpsertAuthorAction)->execute($name);
            })
            ->pluck('id');

        $categoriesId = collect([
            $articleDTO->section, $articleDTO->subsection,
        ])
            ->filter()
            ->map(function ($category) {
                return (new UpsertCategoryAction)->execute($category);
            })
            ->pluck('id');

        $keywords = collect($articleDTO->desFacet)
            ->map(fn ($keyword) => (new UpsertKeywordAction)->execute($keyword))
            ->pluck('id');

        $article->authors()->sync($authors);

        $article->categories()->sync($categoriesId);

        $article->keywords()->sync($keywords);

        return $article;
    }

	private function processNewsApiArticle(NewsApiArticleDTO $articleDTO)
	{
		$article = Article::updateOrCreate([
			'original_url' => $articleDTO->url,
		], [
			'article_unique_id' => Str::uuid(),
			'news_provider_id' => NewsProvider::where('slug', NewsProvider::PROVIDER_NEWS_API)->first()->id,
			'title' => $articleDTO->title,
			'content' => $articleDTO->content,
			'published_at' => $articleDTO->publishedAt,
			'original_url' => $articleDTO->url,
			'featured_image_url' => $articleDTO->urlToImage,
		]);

		$authors = Str::of($articleDTO->author)
			->lower()
			->explode(',')
			->map(function ($name) {
				$name = Str::of($name)
					->trim();

				return (new UpsertAuthorAction)->execute($name);
			})
			->pluck('id');

		$categories = collect($articleDTO->keywords)
			->filter()
			->map(function ($category) {
				return (new UpsertCategoryAction)->execute($category);
			})
			->pluck('id');

		$keywords = collect($articleDTO->keywords)
			->map(fn ($keyword) => (new UpsertKeywordAction)->execute($keyword))
			->pluck('id');

		$article->authors()->sync($authors);

		$article->categories()->sync($categories);

		$article->keywords()->sync($keywords);

		return $article;
	}
}
