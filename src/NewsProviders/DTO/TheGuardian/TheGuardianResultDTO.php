<?php

namespace Domain\NewsProviders\DTO\TheGuardian;

use Spatie\LaravelData\Data;

class TheGuardianResultDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?string $type,
        public ?string $sectionId,
        public ?string $sectionName,
        public ?string $webPublishedDate,
        public ?string $webTitle,
        public ?string $webUrl,
        public ?string $apiUrl,
        public ?bool $isHosted,
        public ?string $pillarId,
        public ?string $pillarName
    ) {}
}
