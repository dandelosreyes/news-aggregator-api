<?php

namespace Domain\UserPreferences\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\UserPreferences\Actions\GetUserPreferencesAction;
use Domain\UserPreferences\Actions\UpdatePreferredAuthorsAction;
use Domain\UserPreferences\Actions\UpdatePreferredCategoriesAction;
use Domain\UserPreferences\Actions\UpdatePreferredNewsProviderAction;
use Domain\UserPreferences\Http\Request\UpdateUserPreferenceRequest;

/**
 * @tags User Preference
 */
class UserPreferenceController extends Controller
{
    /**
     * Get User Preferences
     *
     * @operationId Get User Preferences
     *
     * @response array{message: string, data: { news_providers: array, categories: array, authors: array }}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(
        GetUserPreferencesAction $getUserPreferencesAction
    ) {
        $userPreferences = $getUserPreferencesAction->execute(auth()->user());

        return response()->json([
            'message' => 'User preferences retrieved successfully',
            'data' => [
                'news_providers' => $userPreferences->newsProviders,
                'categories' => $userPreferences->categories,
                'authors' => $userPreferences->authors,
            ],
        ]);
    }

    /**
     * Update User Preferences
     *
     * @operationId Update User Preferences
     *
     * @response array{message: string}
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(
        UpdateUserPreferenceRequest $request,
        UpdatePreferredNewsProviderAction $updatePreferredNewsProviderAction,
        UpdatePreferredAuthorsAction $updatePreferredAuthorsAction,
        UpdatePreferredCategoriesAction $updatePreferredCategoriesAction
    ) {
        $user = auth()->user();

        $updatePreferredNewsProviderAction->execute(
            user: $user,
            newsProviders: $request->get('news_providers')
        );

        $updatePreferredAuthorsAction->execute(
            user: $user,
            authors: $request->get('authors')
        );

        $updatePreferredCategoriesAction->execute(
            user: $user,
            categories: $request->get('categories')
        );

        return response()->json([
            'message' => 'User preferences updated successfully',
        ]);
    }
}
