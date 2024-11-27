<?php

namespace Database\Seeders;

use Domain\NewsProviders\Models\NewsProvider;
use Illuminate\Database\Seeder;

class NewsProviderSeeder extends Seeder
{
    public function run(): void
    {
        $newsProviders = collect([
            'New York Times',
            'NewsAPI',
            'The Guardian',
        ]);

        $newsProviders->each(function ($newsProvider) {
            NewsProvider::create([
                'name' => $newsProvider,
            ]);
        });
    }
}
