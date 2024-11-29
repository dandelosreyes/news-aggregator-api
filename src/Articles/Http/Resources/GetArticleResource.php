<?php

namespace Domain\Articles\Http\Resources;

use Carbon\Carbon;
use Domain\Articles\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Article */
class GetArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->article_unique_id,
            'news_provider' => $this->newsProvider->name,
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'original_url' => $this->original_url,
            'author' => $this->authors->map(fn ($author) => $author->name),
            'categories' => $this->categories->map(fn ($category) => $category->name),
            'published_at' => Carbon::parse($this->published_at)->format(' F j Y, g:i a'),
            'keywords' => [],
        ];
    }
}
