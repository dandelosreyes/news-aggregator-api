<?php

namespace Domain\Articles\Models;

use Domain\Keywords\Models\Keyword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleKeyword extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'article_id',
        'keyword_id',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(Keyword::class);
    }
}
