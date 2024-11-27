<?php

namespace Domain\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Users\Http\Request\LoginUserRequest;
use Illuminate\Http\Request;

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
