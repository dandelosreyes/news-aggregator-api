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

    protected $fillable = [
        'name',
        'slug',
    ];
}
