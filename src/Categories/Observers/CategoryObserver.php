<?php

namespace Domain\Categories\Observers;

use Domain\Categories\Models\Category;
use Illuminate\Support\Str;

class CategoryObserver
{
    public function creating(Category $category): void
    {
        $category->name = Str::lower($category->name);
        $category->slug = Str::slug($category->name);
    }
}
