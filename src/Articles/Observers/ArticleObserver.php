<?php

namespace Domain\Articles\Observers;

use Domain\Articles\Models\Article;
use Illuminate\Support\Str;

class ArticleObserver
{
    public function creating(Article $article): void
    {
        $article->slug = Str::slug($article->title);
    }
}
