<?php

namespace Domain\NewsProviders\Observers;

use Domain\NewsProviders\Models\NewsProvider;
use Illuminate\Support\Str;

class NewsProviderObserver
{
    public function creating(NewsProvider $newsProvider)
    {
        $newsProvider->slug = Str::slug($newsProvider->name);
    }
}
