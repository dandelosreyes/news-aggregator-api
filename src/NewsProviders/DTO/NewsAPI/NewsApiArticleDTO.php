<?php

namespace Domain\NewsProviders\DTO\NewsAPI;

use Spatie\LaravelData\Data;

class NewsApiArticleDTO extends Data
{
	public function __construct(
		public ?array $source,
		public ?string $author,
		public ?string $title,
		public ?string $description,
		public ?string $url,
		public ?string $urlToImage,
		public ?string $publishedAt,
		public ?string $content,
		public ?array $keywords
	)
	{
	}
}