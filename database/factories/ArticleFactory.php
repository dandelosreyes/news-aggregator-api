<?php

namespace Database\Factories;

use Domain\Articles\Models\Article;
use Domain\NewsProviders\Models\NewsProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'article_unique_id' => $this->faker->word(),
            'title' => $this->faker->word(),
            'content' => $this->faker->word(),
            'published_at' => Carbon::now(),
            'original_url' => $this->faker->url(),
            'featured_image_url' => $this->faker->imageUrl(),
            'summary' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'slug' => $this->faker->slug(),

            'news_provider_id' => NewsProvider::factory(),
        ];
    }
}
