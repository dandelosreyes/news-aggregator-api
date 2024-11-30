<?php

namespace Domain\NewsProviders\DTO\NewsProviders;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class GetCredentialsDTO extends Data
{
    public function __construct(
        public ?string $apiKey,
        public ?string $secretKey
    ) {}
}
