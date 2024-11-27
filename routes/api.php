<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('v1.')
    ->group(function () {
        require_once 'api/v1.php';
    });
