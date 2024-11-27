<?php

namespace Domain\UserPreferences\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\UserPreferences\Actions\GetUserPreferencesAction;
use Domain\UserPreferences\Actions\UpdatePreferredNewsProviderAction;
use Domain\UserPreferences\Http\Request\UpdateUserPreferenceRequest;
use Illuminate\Support\Facades\Request;

class UserPreferenceController extends Controller
{
    public function index(
        GetUserPreferencesAction $getUserPreferencesAction
    ) {
        $userPreferences = $getUserPreferencesAction->execute(auth()->user());

        return response()->json([
            'news_providers' => $userPreferences->newsProviders,
            'categories' => $userPreferences->categories,
            'authors' => $userPreferences->authors,
        ]);
    }

    public function store(
	    UpdateUserPreferenceRequest $request,
	    UpdatePreferredNewsProviderAction $updatePreferredNewsProviderAction
    )
    {
		$user = auth()->user();

		$newsProviders = $request->get('news_providers');

		$updatePreferredNewsProviderAction->execute($user, $newsProviders);

		return response()->json([
			'message' => 'User preferences updated successfully',
		]);
    }
}
