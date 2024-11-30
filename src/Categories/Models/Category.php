<?php

namespace Domain\Categories\Models;

use Database\Factories\CategoryFactory;
use Domain\Categories\Observers\CategoryObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(CategoryObserver::class)]
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }
}
