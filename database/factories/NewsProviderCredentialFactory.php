<?php

namespace Database\Factories;

use Domain\NewsProviders\Models\NewsProviderCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsProviderCredentialFactory extends Factory
{
    protected $model = NewsProviderCredential::class;

    public function definition(): array
    {
        return [
            'api_key' => $this->faker->uuid,
            'secret_key' => $this->faker->uuid,
        ];
    }
}
