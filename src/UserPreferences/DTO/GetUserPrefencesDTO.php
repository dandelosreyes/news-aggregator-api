<?php

namespace Domain\UserPreferences\DTO;

use Spatie\LaravelData\Data;

class GetUserPrefencesDTO extends Data
{
    public function __construct(
        public ?array $newsProviders,
        public ?array $categories,
        public ?array $authors,
    ) {}
}
