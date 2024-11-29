<?php

namespace Domain\Articles\Models;

use Domain\Articles\Observers\ArticleObserver;
use Domain\Authors\Models\Author;
use Domain\Categories\Models\Category;
use Domain\Keywords\Models\Keyword;
use Domain\NewsProviders\Models\NewsProvider;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(ArticleObserver::class)]
class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'article_unique_id',
        'news_provider_id',
        'title',
        'content',
        'published_at',
        'original_url',
        'featured_image_url',
        'summary',
    ];

    public function newsProvider(): BelongsTo
    {
        return $this->belongsTo(NewsProvider::class);
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_categories');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'article_authors');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'article_keywords');
    }
}
