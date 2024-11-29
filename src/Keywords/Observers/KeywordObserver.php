<?php

namespace Domain\Keywords\Observers;

use Domain\Keywords\Models\Keyword;
use Illuminate\Support\Str;

class KeywordObserver
{
    public function creating(Keyword $keyword): void
    {
        $keyword->slug = Str::slug($keyword->name);
    }
}
