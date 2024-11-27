<?php

namespace Domain\Authentication\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
            'token' => [
                'required',
            ],
            'password' => [
                'required',
                'confirmed',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
