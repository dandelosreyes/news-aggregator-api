<?php

namespace Domain\HealthCheck\Http\Controllers;

use App\Http\Controllers\Controller;

/**
 * @tags Health Check
 */
class HealthCheckController extends Controller
{
    /**
     * Health Check
     *
     * @unauthenticated
     */
    public function __invoke()
    {
        return response()->json([
            'message' => 'Up and running...',
        ]);
    }
}
