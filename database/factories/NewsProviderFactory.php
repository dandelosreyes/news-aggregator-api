<?php

namespace Database\Factories;

use Domain\NewsProviders\Models\NewsProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\NewsProviders\Models\NewsProvider>
 */
class NewsProviderFactory extends Factory
{
    protected $model = NewsProvider::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
