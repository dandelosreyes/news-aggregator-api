<?php

namespace Database\Seeders;

use Domain\Categories\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'politics',
            'business',
            'technology',
            'health',
            'sports',
            'entertainment',
            'science',
            'environment',
            'world',
            'lifestyle',
            'education',
            'opinion',
            'culture',
            'travel',
            'food',
            'art',
            'finance',
            'law',
            'history',
            'automotive',
        ];

        collect($categories)->each(function ($category) {
            Category::create([
                'name' => $category,
            ]);
        });
    }
}
