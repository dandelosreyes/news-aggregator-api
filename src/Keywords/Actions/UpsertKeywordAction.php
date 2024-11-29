<?php

namespace Domain\Keywords\Actions;

use Domain\Keywords\Models\Keyword;
use Spatie\QueueableAction\QueueableAction;

class UpsertKeywordAction
{
    use QueueableAction;

    public function __construct() {}

    public function execute(
        string $name
    ) {
        return Keyword::firstOrCreate(compact('name'));
    }
}
