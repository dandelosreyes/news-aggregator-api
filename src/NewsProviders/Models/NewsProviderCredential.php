<?php

namespace Domain\NewsProviders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsProviderCredential extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'news_provider_id',
		'api_key',
		'secret_key',
	];

	public function newsProvider(): BelongsTo
	{
		return $this->belongsTo(NewsProvider::class);
	}
}
