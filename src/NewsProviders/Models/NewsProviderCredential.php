<?php

namespace Domain\NewsProviders\Models;

use Database\Factories\NewsProviderCredentialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsProviderCredential extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'news_provider_id',
        'api_key',
        'secret_key',
    ];

    protected static function newFactory()
    {
        return NewsProviderCredentialFactory::new();
    }

    public function newsProvider(): BelongsTo
    {
        return $this->belongsTo(NewsProvider::class);
    }
}
