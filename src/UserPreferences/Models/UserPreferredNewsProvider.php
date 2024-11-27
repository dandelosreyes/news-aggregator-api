<?php

namespace Domain\UserPreferences\Models;

use Domain\NewsProviders\Models\NewsProvider;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPreferredNewsProvider extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'user_id',
		'news_provider_id',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function newsProvider(): BelongsTo
	{
		return $this->belongsTo(NewsProvider::class);
	}
}
