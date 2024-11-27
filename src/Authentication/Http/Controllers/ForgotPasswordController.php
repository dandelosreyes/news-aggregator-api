<?php

namespace Domain\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Authentication\Http\Request\ForgotPasswordRequest;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * @unauthenticated
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
