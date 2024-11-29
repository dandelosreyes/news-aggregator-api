<?php

namespace Domain\UserNewsfeed\Repositories;

use Domain\Articles\Models\Article;
use Domain\Users\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserNewsfeedRepository
{
    public function getNewsfeed(
        User $user,
        int $perPage = 10
    ): LengthAwarePaginator {
        $user->loadMissing([
            'preferredCategories', 'preferredNewsProviders', 'preferredAuthors',
        ]);

        $preferredCategories = $user->preferredCategories;
        $preferredNewsProvider = $user->preferredNewsProviders;
        $preferredAuthors = $user->preferredAuthors;

        return Article::query()
            ->with([
                'authors', 'categories', 'newsProvider',
            ])
            ->when($preferredCategories, function ($query) use ($preferredCategories) {
                return $query->whereHas('categories', function ($query) use ($preferredCategories) {
                    $query->whereIn('category_id', $preferredCategories->pluck('id'));
                });
            })
            ->when($preferredNewsProvider, function ($query) use ($preferredNewsProvider) {
                return $query->whereHas('newsProvider', function ($query) use ($preferredNewsProvider) {
                    $query->whereIn('news_provider_id', $preferredNewsProvider->pluck('id'));
                });
            })
            ->when($preferredAuthors, function ($query) use ($preferredAuthors) {
                return $query->whereHas('authors', function ($query) use ($preferredAuthors) {
                    foreach ($preferredAuthors as $author) {
                        $query->orWhereLike('name', '%'.$author.'%');
                    }
                });
            })
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }
}
