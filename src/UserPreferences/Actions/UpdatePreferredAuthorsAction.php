<?php

namespace Domain\UserPreferences\Actions;

use Domain\Authors\Models\Author;
use Domain\UserPreferences\Models\UserPreferredAuthor;
use Domain\Users\Models\User;
use Illuminate\Support\Str;

class UpdatePreferredAuthorsAction
{
    private User $user;

    public function execute($user, array $authors)
    {
        $authorsCollection = collect($authors);

        $this->user = $user;

        $authorsCollection->whenNotEmpty(function ($authors) {
            UserPreferredAuthor::where('user_id', $this->user->id)->delete();

            $authors->each(function ($author) {
                $authorRecord = Author::firstOrCreate([
                    'name' => Str::lower($author),
                ]);

                UserPreferredAuthor::create([
                    'user_id' => $this->user->id,
                    'author_id' => $authorRecord->id,
                ]);
            });
        });
    }
}
