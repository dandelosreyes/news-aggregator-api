<?php

namespace Domain\Authors\Models;

use Domain\Authors\Observers\AuthorObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(AuthorObserver::class)]
class Author extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];
}
