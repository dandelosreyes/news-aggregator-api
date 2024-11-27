<?php

namespace Domain\Users\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Domain\Authors\Models\Author;
use Domain\Categories\Models\Category;
use Domain\NewsProviders\Models\NewsProvider;
use Domain\UserPreferences\Models\UserPreferredAuthor;
use Domain\UserPreferences\Models\UserPreferredCategory;
use Domain\UserPreferences\Models\UserPreferredNewsProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function preferredNewsProviders(): HasManyThrough
    {
        return $this->hasManyThrough(
            NewsProvider::class,
            UserPreferredNewsProvider::class,
            'user_id',
            'id',
            'id',
            'news_provider_id'
        );
    }

    public function preferredCategories(): HasManyThrough
    {
        return $this->hasManyThrough(
            Category::class,
            UserPreferredCategory::class,
            'user_id',
            'id',
            'id',
            'category_id'
        );
    }

    public function preferredAuthors(): HasManyThrough
    {
        return $this->hasManyThrough(
            Author::class,
            UserPreferredAuthor::class,
            'user_id',
            'id',
            'id',
            'author_id'
        );
    }
}
