<?php

namespace Domain\UserNewsfeed\Repositories;

use Domain\Articles\Models\Article;
use Domain\Users\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserNewsfeedRepository
{
    public function getNewsfeed(
        User $user,
        int $perPage,
        ?array $categories,
        ?array $providers,
        ?array $authors,
        ?string $publishedAt,
        ?string $query
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
            ->when($preferredCategories->isNotEmpty(), function ($query) use ($preferredCategories) {
                return $query->whereHas('categories', function ($query) use ($preferredCategories) {
                    foreach ($preferredCategories as $category) {
                        $query->orWhereLike('name', '%'.$category.'%');
                    }
                });
            })
            ->when($preferredNewsProvider->isNotEmpty(), function ($query) use ($preferredNewsProvider) {
                return $query->whereHas('newsProvider', function ($query) use ($preferredNewsProvider) {
                    foreach ($preferredNewsProvider as $newsProvider) {
                        $query->orWhereLike('name', '%'.$newsProvider.'%');
                    }
                });
            })
            ->when($preferredAuthors->isNotEmpty(), function ($query) use ($preferredAuthors) {
                return $query->whereHas('authors', function ($query) use ($preferredAuthors) {
                    foreach ($preferredAuthors as $author) {
                        $query->orWhereLike('name', '%'.$author.'%');
                    }
                });
            })
            ->when($categories, function ($query) use ($categories) {
                return $query->whereHas('categories', function ($query) use ($categories) {
                    foreach ($categories as $category) {
                        $query->orWhereLike('name', '%'.$category.'%');
                    }
                });
            })
            ->when($providers, function ($query) use ($providers) {
                return $query->whereHas('newsProvider', function ($query) use ($providers) {
                    foreach ($providers as $provider) {
                        $query->orWhereLike('name', '%'.$provider.'%');
                    }
                });
            })
            ->when($authors, function ($query) use ($authors) {
                return $query->whereHas('authors', function ($query) use ($authors) {
                    foreach ($authors as $author) {
                        $query->orWhereLike('name', '%'.$author.'%');
                    }
                });
            })
            ->when($publishedAt, function ($query) use ($publishedAt) {
                return $query->whereDate('published_at', $publishedAt);
            })
            ->when($query, function ($q) use ($query) {
                return $q->orWhereLike('title', '%'.$query.'%')
                    ->orWhereLike('content', '%'.$query.'%');
            })
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }
}
