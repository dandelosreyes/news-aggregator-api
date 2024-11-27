<?php

namespace Domain\UserPreferences\Actions;

use Domain\NewsProviders\Models\NewsProvider;
use Domain\UserPreferences\Models\UserPreferredNewsProvider;

class UpdatePreferredNewsProviderAction
{
	public function execute($user, array $newsProviders)
	{
		$newsProvidersId = NewsProvider::query()
			->whereIn('name', $newsProviders)
			->pluck('id');

		UserPreferredNewsProvider::where('user_id', $user->id)->delete();

		$preferences = $newsProvidersId->map(fn ($newsProviderId) => [
			'user_id' => $user->id,
			'news_provider_id' => $newsProviderId,
		]);

		UserPreferredNewsProvider::insert($preferences->toArray());
	}
}