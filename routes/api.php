<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PassowordResetController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Contact\ContactController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Payment\CheckoutController;
use App\Http\Controllers\ProfileSetting\ProfileSettingController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\Request\RequestController;
use App\Http\Controllers\Setting\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::options('{any}', function (Request $request) {
    return response('', 200);
})->where('any', '.*');

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

// Request test
Route::post('/requests', [RequestController::class, 'store']);



// Reports
Route::get('/reports/{id}/download', [ReportController::class, 'downloadReport']);
Route::get('/reports/{id}/details', [ReportController::class, 'reportDetails']);

Route::group(['middleware' => ['auth:sanctum'], 'role:user, admin'], function () {
    Route::get('user', [AuthController::class, 'getUser']);
    Route::get('users', [AuthController::class, 'getAllUsers']);
    Route::delete('users/{id}', [AuthController::class, 'deleteUser']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Contacts
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::post('/contacts/{id}/reply', [ContactController::class, 'reply']);

    // Requests
    Route::get('/requests', [RequestController::class, 'index']);
    Route::get('/requests/{id}', [RequestController::class, 'show']);
    Route::post('/requests/{id}/reply', [RequestController::class, 'reply']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings/notifications', [SettingController::class, 'updateNotification']);

    // Profile Settings
    Route::get('/profile-settings', [ProfileSettingController::class, 'index']);
    Route::post('/profile-settings', [ProfileSettingController::class, 'profileUpdate']);

    // Cart
    Route::get('/carts', [CartController::class, 'index']);
    Route::post('/carts', [CartController::class, 'store']);
    Route::post('/carts/{id}/update-shipping', [CartController::class, 'updateShipping']);
    Route::delete('/carts/{id}', [CartController::class, 'destroy']);
    Route::post('/carts/sample', [CartController::class, 'cartSample']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/update-status', [OrderController::class, 'orderStatusUpdate']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    Route::post('/orders/sample', [OrderController::class, 'orderSample']);
    // user orders
    Route::get('/user-orders', [OrderController::class, 'userOrders']);

    // Payment
    Route::post('/checkout', [CheckoutController::class, 'makePayment']);
    Route::post('/checkout/stripe', [CheckoutController::class, 'completeStripePayment']);
    Route::post('/checkout/paypal', [CheckoutController::class, 'completePaypalPayment']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);
    Route::post('/reports/{id}', [ReportController::class, 'update']);
    Route::delete('/reports/{id}', [ReportController::class, 'destroy']);
    // Route::get('/reports/{id}/download', [ReportController::class, 'downloadReport']);
});
