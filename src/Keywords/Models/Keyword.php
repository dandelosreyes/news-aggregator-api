<?php

namespace Domain\Keywords\Models;

use Domain\Keywords\Observers\KeywordObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Regex\Helpers\Str;

#[ObservedBy(KeywordObserver::class)]
class Keyword extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Str::of($value)->title()->replace('-', ' '),
            set: fn (string $value) => Str::of($value)->slug(),
        );
    }
}
