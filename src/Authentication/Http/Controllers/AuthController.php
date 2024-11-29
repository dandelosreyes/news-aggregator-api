<?php

namespace Domain\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @tags Auth, User
 */
class AuthController extends Controller
{
    /**
     * @route GET /api/v1/user
     *
     * @middleware: auth:api
     *
     * @description: Get the current user
     *
     * @operationId Get Current User
     *
     * @return JsonResponse
     */
    public function __invoke()
    {
        return response()->json([
            'user' => auth()->user(),
        ]);
    }
}
