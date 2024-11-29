<?php

use Illuminate\Database\Eloquent\Model;

test('models extend base models', function () {
    $domains = File::directories(base_path('src'));
    foreach ($domains as $domain) {
        if (File::isDirectory($domain.'/Models')) {
            $modelFiles = File::allFiles($domain.'/Models');
            foreach ($modelFiles as $file) {
                $className = 'Domain\\'.basename($domain).'\\Models\\'.pathinfo($file, PATHINFO_FILENAME);

                $reflection = new ReflectionClass($className);

                expect($reflection)
                    ->isSubclassOf(Model::class)
                    ->toBeTrue();
            }
        }
    }
});
