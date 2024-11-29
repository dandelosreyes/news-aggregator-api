<?php

namespace Domain\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Authentication\Http\Request\ForgotPasswordRequest;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Password;

/**
 * @tags Auth
 */
class ForgotPasswordController extends Controller
{
    /**
     * Forgot Password
     *
     * @unauthenticated
     *
     * @operationId Forgot Password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(
        ForgotPasswordRequest $request
    ) {
        $user = User::where('email', $request->email)->first();

        $token = Password::createToken($user);

        return response()->json([
            'message' => 'Reset password token created.',
            'token' => $token,
        ]);
    }
}
