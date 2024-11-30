<?php

namespace Domain\Authors\Models;

use Database\Factories\AuthorFactory;
use Domain\Authors\Observers\AuthorObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[ObservedBy(AuthorObserver::class)]
class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function newFactory()
    {
        return AuthorFactory::new();
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Str::of($value)->replace('by ', '')->title()->value(),
            set: fn (string $value) => Str::lower($value)
        );
    }
}
