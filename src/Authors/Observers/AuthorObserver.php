<?php

namespace Domain\Authors\Observers;

use Domain\Authors\Models\Author;
use Illuminate\Support\Str;

class AuthorObserver
{
    public function creating(Author $author): void
    {
        $author->name = Str::lower($author->name);
        $author->slug = Str::slug($author->name);
    }
}
