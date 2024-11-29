<?php

namespace Domain\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Authentication\Http\Request\LoginUserRequest;

/**
 * @tags Auth
 */
class LoginController extends Controller
{
    /**
     * Login
     *
     * @route POST /api/v1/login
     *
     * @description: Login a user
     *
     * @operationId Login
     **/
    public function __invoke(
        LoginUserRequest $request
    ) {
        if (! auth()->attempt($request->only('email', 'password'))) {
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
