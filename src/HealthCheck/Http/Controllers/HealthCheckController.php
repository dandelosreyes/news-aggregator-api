<?php

namespace Domain\Healthcheck\Http\Controllers;

use App\Http\Controllers\Controller;

class HealthCheckController extends Controller
{
    /**
     * @unauthenticated
     */
    public function __invoke()
    {
        return response()->json([
            'message' => 'Up and running...',
        ]);
    }
}
