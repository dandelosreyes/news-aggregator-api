<?php

namespace Domain\Authentication\Http\Controllers;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'user' => auth()->user(),
        ]);
    }
}
