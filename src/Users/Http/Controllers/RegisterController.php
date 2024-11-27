<?php

namespace Domain\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Users\Http\Request\RegisterUserRequest;
use Domain\Users\Models\User;

class RegisterController extends Controller
{
	public function __invoke(
		RegisterUserRequest $request
	)
	{
		$user = User::create($request->validated());

		return response()->json([
			'message' => 'Registered successfully!',
			'user' => $user,
		], 201);
	}
}
