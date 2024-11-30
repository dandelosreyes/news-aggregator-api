<?php

namespace Domain\UserNewsfeed\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Articles\Http\Resources\GetArticleResource;
use Domain\Articles\Models\Article;
use Domain\UserNewsfeed\Http\Requests\UserNewsfeedRequest;
use Domain\UserNewsfeed\Http\Resources\UserNewsfeedCollection;
use Domain\UserNewsfeed\Repositories\UserNewsfeedRepository;
use Illuminate\Http\JsonResponse;

/**
 * @tags Newsfeed
 */
class UserNewsfeedController extends Controller
{
    /**
     * Get the user's newsfeed
     *
     * Returns a paginated list of articles based on the user's preferences. If the user has not yet set their preferences it returns all the articles
     *
     * @operationId Get User Newsfeed
     *
     * @route GET /api/v1/newsfeed
     *
     * @middleware: auth:api
     *
     * @return UserNewsfeedCollection|JsonResponse
     */
    public function index(
        UserNewsfeedRequest $request,
        UserNewsfeedRepository $userNewsfeedRepository
    ) {
        $categories = $request->get('categories');
        $query = $request->get('query');
        $perPage = $request->get('per_page', 10);
        $publishedAt = $request->get('published_at');
        $providers = $request->get('sources');
        $authors = $request->get('authors');

        $user = auth()->user();

        $articles = $userNewsfeedRepository->getNewsfeed(
            user: $user,
            perPage: $perPage,
            categories: $categories,
            providers: $providers,
            authors: $authors,
            publishedAt: $publishedAt,
            query: $query
        );

        if ($articles->isEmpty()) {
            return response()->json([
                'message' => 'No articles found. Kindly adjust your preferences.',
            ]);
        }

        return new UserNewsfeedCollection($articles);
    }

    /**
     * Get a specific article
     *
     * @route GET /api/v1/newsfeed/{article_unique_id}
     *
     * @operationId Get Specific Article
     *
     * @response GetArticleResource
     *
     * @return GetArticleResource
     */
    public function show($id)
    {
        $article = Article::where('article_unique_id', $id)
            ->with([
                'authors', 'categories', 'newsProvider',
            ])
            ->first();

        return GetArticleResource::make($article);
    }
}
