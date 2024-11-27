<?php

namespace Domain\UserPreferences\Actions;

use Domain\UserPreferences\DTO\GetUserPrefencesDTO;
use Domain\Users\Models\User;

class GetUserPreferencesAction
{
    public function execute(
        User $user
    ) {
        $user->loadMissing([
            'preferredNewsProviders',
            'preferredCategories',
            'preferredAuthors',
        ]);

        $newsProviders = $user->preferredNewsProviders->whenNotEmpty(static function ($providers) {
            return $providers->map(static function ($provider) {
                return $provider->name;
            });
        })->toArray();

        $categories = $user->preferredCategories->whenNotEmpty(static function ($categories) {
            return $categories->map(static function ($category) {
                return $category->name;
            });
        })->toArray();

        $authors = $user->preferredAuthors->whenNotEmpty(static function ($categories) {
            return $categories->map(static function ($category) {
                return $category->name;
            });
        })->toArray();

        return new GetUserPrefencesDTO(
            newsProviders: $newsProviders,
            categories: $categories,
            authors: $authors,
        );
    }
}
