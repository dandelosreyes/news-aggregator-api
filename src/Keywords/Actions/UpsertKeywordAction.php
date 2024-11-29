<?php

namespace Domain\Keywords\Actions;

use Domain\Keywords\Models\Keyword;
use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

class UpsertKeywordAction
{
    use QueueableAction;

    public function __construct() {}

    public function execute(
        string $name
    ) {
        return Keyword::createOrFirst([
			'name' => Str::slug($name),
        ]);
    }
}
