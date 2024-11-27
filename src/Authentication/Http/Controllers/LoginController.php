<?php

namespace Domain\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Authentication\Http\Request\LoginUserRequest;

class LoginController extends Controller
{
	public function __invoke (
		LoginUserRequest $request
	)
	{
		if (!auth()->attempt($request->only('email', 'password'))) {
			return response()->json([
				'message' => 'Invalid credentials',
			], 401);
		}

		return response()->json([
			'message' => 'You are logged in',
			'user' => auth()->user(),
			'token' => auth()->user()->createToken('authToken')->plainTextToken,
		]);
	}
}
