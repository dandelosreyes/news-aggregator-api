<?php

use Illuminate\Support\Facades\Route;

Route::get('/up', \Domain\Healthcheck\Http\Controllers\HealthCheckController::class)
    ->name('health-check');

Route::prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/login', \Domain\Authentication\Http\Controllers\LoginController::class)
            ->name('login');
        Route::post('/register', \Domain\Authentication\Http\Controllers\RegisterController::class)
            ->name('register');
        Route::post('/forgot-password', \Domain\Authentication\Http\Controllers\ForgotPasswordController::class)
            ->name('forgot-password');
        Route::post('/reset-password', \Domain\Authentication\Http\Controllers\ResetPasswordController::class)
            ->name('reset-password');
    });

Route::apiResource('newsfeed', \Domain\UserNewsfeed\Http\Controllers\UserNewsfeedController::class)
    ->middleware('auth:sanctum')
    ->only([
        'index', 'show',
    ]);

Route::prefix('user')
    ->name('user.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/', \Domain\Authentication\Http\Controllers\AuthController::class)
            ->name('me');
        Route::apiResource('/preferences', \Domain\UserPreferences\Http\Controllers\UserPreferenceController::class)
            ->except([
                'destroy', 'show', 'update',
            ]);
    });
