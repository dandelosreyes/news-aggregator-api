<?php

namespace Domain\Authors\Actions;

use Domain\Authors\Models\Author;
use Illuminate\Support\Str;
use Spatie\QueueableAction\QueueableAction;

class UpsertAuthorAction
{
    use QueueableAction;

    public function __construct() {}

    public function execute(
        string $name
    ) {
        return Author::firstOrCreate(compact('name'));
    }
}
