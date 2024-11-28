<?php

namespace Domain\NewsProviders\Models;

use Domain\NewsProviders\Observers\NewsProviderObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(NewsProviderObserver::class)]
class NewsProvider extends Model
{
    use SoftDeletes;

	const PROVIDER_NEW_YORK_TIMES = 'new-york-times';

	const PROVIDER_NEWS_API = 'newsapi';

	const PROVIDER_THE_GUARDIAN = 'the-guardian';


    protected $fillable = [
        'name',
        'slug',
    ];

	public function credentials()
	{
		return $this->hasMany(NewsProviderCredential::class);
	}
}
