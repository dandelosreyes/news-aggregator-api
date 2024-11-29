<?php

namespace Domain\Categories\Actions;

use Domain\Categories\Models\Category;
use Illuminate\Support\Str;

class UpsertCategoryAction
{
    public function __construct() {}

    public function execute(
        string $name
    ) {
        return Category::firstOrCreate([
            'name' => Str::lower($name),
        ]);
    }
}
