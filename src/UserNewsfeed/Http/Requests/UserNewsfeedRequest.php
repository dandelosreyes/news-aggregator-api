<?php

namespace Domain\UserNewsfeed\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserNewsfeedRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => [
                'int', 'nullable',
            ],
            'per_page' => [
                'int', 'nullable',
            ],
            'keywords' => [
                'nullable', 'array',
            ],
            'keywords.*' => [
                'string',
            ],
            'published_at' => [
                'nullable', 'array',
            ],
            'categories' => [
                'nullable',
                'array',
                'min:1',
            ],
            'categories.*' => [
                'string',
            ],
            'sources' => [
                'nullable', 'array',
                Rule::exists('news_providers', 'name'),
            ],
            'sources.*' => [
                'string',
            ],
            'authors' => [
                'nullable', 'array',
            ],
            'authors.*' => [
                'string',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
