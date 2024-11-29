<?php

namespace Domain\UserNewsfeed\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Articles\Models\Article;
use Domain\UserNewsfeed\Http\Resources\UserNewsfeedCollection;
use Illuminate\Http\Request;

class UserNewsfeedController extends Controller
{
	/**
	 * @param  Request  $request
	 * @return UserNewsfeedCollection|\Illuminate\Http\JsonResponse
	 */
	public function index(
		Request $request
	)
	{
		$perPage = $request->get('per_page', 10);

		auth()->user()->loadMissing([
			'preferredCategories', 'preferredNewsProviders', 'preferredAuthors'
		]);

		$user = auth()->user();

		$preferredCategories = $user->preferredCategories;
		$preferredNewsProvider = $user->preferredNewsProviders;
		$preferredAuthors = $user->preferredAuthors;

		$articles = Article::query()
			->with([
				'authors', 'categories', 'newsProvider'
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
						$query->orWhereLike('name', "%" . $author . "%");
					}
				});
			})
			->orderBy('published_at', 'desc')
			->paginate($perPage);

		if ($articles->isEmpty()) {
			return response()->json([
				'message' => 'No articles found. Kindly adjust your preferences.'
			]);
		}

		return new UserNewsfeedCollection($articles);
	}
}
