<?php

namespace Domain\UserPreferences\Models;

use Domain\Authors\Models\Author;
use Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPreferredAuthor extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'user_id',
		'author_id',
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function author(): BelongsTo
	{
		return $this->belongsTo(Author::class);
	}
}
