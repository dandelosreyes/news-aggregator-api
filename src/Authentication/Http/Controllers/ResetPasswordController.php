<?php

namespace Domain\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Domain\Authentication\Http\Request\ResetPasswordRequest;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
     * @unauthenticated
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(
        ResetPasswordRequest $request
    ) {
        $isTokenValid = Password::broker()->tokenExists(
            User::where('email', $request->get('email'))->first(),
            $request->get('token')
        );

        if (! $isTokenValid) {
            return response()->json([
                'message' => 'Invalid token.',
            ], 400);
        }

        Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return response()->json([
            'message' => 'Password reset successfully.',
        ]);
    }
}
