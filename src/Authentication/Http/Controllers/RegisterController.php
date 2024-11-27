<?php

namespace Domain\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Authentication\Http\Request\RegisterUserRequest;
use Domain\Users\Models\User;

class RegisterController extends Controller
{
    /**
     * @unauthenticated
     */
    public function __invoke(
        RegisterUserRequest $request
    ) {
        $user = User::create($request->validated());

        return response()->json([
            'message' => 'Registered successfully!',
            'user' => $user,
        ]);
    }
}
