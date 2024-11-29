<?php

namespace Domain\NewsProviders\Actions;

use Domain\Articles\Models\Article;
use Domain\Authors\Actions\UpsertAuthorAction;
use Domain\Categories\Actions\UpsertCategoryAction;
use Domain\NewsProviders\DTO\NewYorkTimes\TopStoriesDTO;
use Domain\NewsProviders\Models\NewsProvider;
use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

class SaveArticleAction
{
    use QueueableAction;

    public function __construct() {}

    public function execute(
        TopStoriesDTO $articleDTO
    ) {
        if ($articleDTO instanceof TopStoriesDTO) {
            $this->processTopStories($articleDTO);
        }

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
            ->explode('and')
            ->map(function ($name) {
				$name = Str::of($name)
					->trim()
					->lower()
					->replace('by ', '');

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

	    $article->authors()->sync($authors);

        $article->categories()->sync($categoriesId);
    }
}
