<?php

namespace Domain\UserPreferences\Http\Request;

use Domain\NewsProviders\Models\NewsProvider;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class UpdateUserPreferenceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'news_providers' => [
                'array', 'exists:news_providers,name',
            ],
            'categories' => [
                'array',
            ],
            'authors' => [
                'array',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

	public function messages(): array
	{
		$newsProvider = Cache::remember('available_news_providers', 300, function () {
			return NewsProvider::pluck('name')->implode(', ');
		});

		return [
			'news_providers.exists' => sprintf(
				'The selected news providers are invalid. Available options are: %s',
				$newsProvider
			),
		];
	}
}
