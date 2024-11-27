<?php

namespace Domain\Users\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
	public function rules(): array
	{
		return [
			'email' => [
				'required',
				'exists:users,email',
			],
			'password' => [
				'required'
			]
		];
	}

	public function authorize(): bool
	{
		return true;
	}
}
