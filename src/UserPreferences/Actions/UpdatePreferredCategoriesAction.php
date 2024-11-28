<?php

namespace Domain\UserPreferences\Actions;

use Domain\Categories\Models\Category;
use Domain\UserPreferences\Models\UserPreferredCategory;
use Illuminate\Support\Str;

class UpdatePreferredCategoriesAction
{
    public function execute($user, array $categories)
    {
        $categoriesCollection = collect($categories);

        $categoriesCollection->whenNotEmpty(function ($categories) use ($user) {
            UserPreferredCategory::where('user_id', $user->id)->delete();

            $categories->each(function ($category) use ($user) {
                $categoryRecord = Category::firstOrCreate([
                    'name' => Str::lower($category),
                ]);

                UserPreferredCategory::create([
                    'category_id' => $categoryRecord->id,
                    'user_id' => $user->id,
                ]);
            });
        });
    }
}
