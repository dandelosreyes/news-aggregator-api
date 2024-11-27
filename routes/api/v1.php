<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
	return $request->user();
})->middleware('auth:sanctum');

Route::get('/up', function () {
	return response()->json([
		'message' => 'I am up',
	]);
});

Route::prefix('auth')
	->group(function () {
		Route::post('/login', 'Domain\Users\Http\Controllers\LoginController');
		Route::post('/register', 'Domain\Users\Http\Controllers\RegisterController');
	});