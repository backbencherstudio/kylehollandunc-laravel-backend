<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PassowordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/request-otp', [PassowordResetController::class, 'requestOTP']);
Route::post('/verify-otp', [PassowordResetController::class, 'verifyOTP']);
Route::post('/reset-password', [PassowordResetController::class, 'resetPassword']);

Route::group(['middleware' => ['auth:sanctum'], 'role:admin'], function () {
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);
});