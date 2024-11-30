<?php

use Domain\Categories\Models\Category;
use Domain\NewsProviders\Services\NewsApiService;
use Domain\NewsProviders\Services\NewYorkTimesService;
use Domain\NewsProviders\Services\TheGuardianService;

Schedule::call(function () {
    $categories = Category::all();

    $newsApiService = new NewsApiService;
    $newYorkTimesService = new NewYorkTimesService;
    $theGuardianService = new TheGuardianService;

    $categories->each(function ($category) use ($newsApiService, $newYorkTimesService, $theGuardianService) {
        $newYorkTimesService->getNews($category->name);
        $newsApiService->getNews($category->name);
        $theGuardianService->getNews($category->name);
    });
});
