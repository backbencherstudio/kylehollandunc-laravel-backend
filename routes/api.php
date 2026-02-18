<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PassowordResetController;
use App\Http\Controllers\Contact\ContactController;
use App\Http\Controllers\Setting\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/request-otp', [PassowordResetController::class, 'requestOTP']);
Route::post('/verify-otp', [PassowordResetController::class, 'verifyOTP']);
Route::post('/reset-password', [PassowordResetController::class, 'resetPassword']);
// Contacts
Route::post('/contacts', [ContactController::class, 'store']);

Route::group(['middleware' => ['auth:sanctum'], 'role:admin'], function () {
    Route::get('user', [AuthController::class, 'getUser']);
    Route::post('logout', [AuthController::class, 'logout']);
    // Contacts
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::post('/contacts/{id}/reply', [ContactController::class, 'reply']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings/notifications', [SettingController::class, 'updateNotification']);
});