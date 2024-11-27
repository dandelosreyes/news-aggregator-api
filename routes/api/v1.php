<?php

use Illuminate\Support\Facades\Route;

Route::get('/user', \Domain\Authentication\Http\Controllers\AuthController::class)->middleware('auth:sanctum')
    ->name('user');

Route::get('/up', \Domain\Healthcheck\Http\Controllers\HealthCheckController::class)
    ->name('health-check');

Route::prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/login', \Domain\Authentication\Http\Controllers\LoginController::class)
            ->name('login');
        Route::post('/register', \Domain\Authentication\Http\Controllers\RegisterController::class)
            ->name('register');
        Route::post('/forgot-password', \Domain\Authentication\Http\Controllers\ForgotPasswordController::class);
        Route::post('/reset-password', \Domain\Authentication\Http\Controllers\ResetPasswordController::class);
    });
