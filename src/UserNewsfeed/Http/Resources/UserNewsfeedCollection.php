<?php

namespace Domain\UserNewsfeed\Http\Resources;

use Carbon\Carbon;
use Domain\Articles\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserNewsfeedCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function (Article $article) {
                return [
                    'id' => $article->article_unique_id,
                    'news_provider' => $article->newsProvider->name,
                    'slug' => $article->slug ?? str($article->title)->slug(),
                    'title' => $article->title,
                    'summary' => $article->summary,
                    'original_url' => $article->original_url,
                    'author' => $article->authors->map(fn ($author) => $author->name),
                    'categories' => $article->categories->map(fn ($category) => $category->name),
                    'published_at' => Carbon::parse($article->published_at)->format(' F j Y, g:i a'),
                ];
            }),
        ];
    }
}
