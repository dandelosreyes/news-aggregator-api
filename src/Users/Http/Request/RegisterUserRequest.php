<?php

namespace Domain\Users\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
	public function rules(): array
	{
		return [
			'name' => [
				'required'
			],
			'email' => [
				'required',
				'email',
				'unique:users,email'
			],
			'password' => [
				'required',
				'min:8'
			]
		];
	}

	public function authorize(): bool
	{
		return true;
	}
}
