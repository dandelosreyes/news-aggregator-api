<?php

namespace Domain\NewsProviders\DTO\NewYorkTimes;

use Spatie\LaravelData\Data;

class MultimediaDataDTO extends Data
{
    public function __construct(
        public ?string $url,
        public ?string $format,
        public ?int $height,
        public ?int $width,
        public ?string $type,
        public ?string $subtype,
        public ?string $caption,
        public ?string $copyright,
    ) {}
}
