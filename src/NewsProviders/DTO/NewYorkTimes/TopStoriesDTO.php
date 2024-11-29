<?php

namespace Domain\NewsProviders\DTO\NewYorkTimes;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class TopStoriesDTO extends Data
{
    public function __construct(
        public ?string $section,
        public ?string $subsection,
        public ?string $title,
        public ?string $abstract,
        public ?string $url,
        public ?string $uri,
        public ?string $byline,
        public ?string $itemType,
        public ?string $updatedDate,
        public ?string $createdDate,
        public ?string $publishedDate,
        public ?string $materialTypeFacet,
        public ?string $kicker,
        public ?array $desFacet,
        public ?array $orgFacet,
        public ?array $perFacet,
        public ?array $geoFacet,
        public array $multimedia,
        public ?string $shortUrl,
    ) {}
}
