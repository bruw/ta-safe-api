<?php

use App\Http\Controllers\Device\DeviceController;
use App\Http\Controllers\Device\DeviceSharingController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('user', 'currentUser');
        Route::put('user', 'update');
        Route::get('user/devices', 'getUserDevices');
        Route::get('user/search', 'search');
    });

    Route::controller(DeviceController::class)->group(function () {
        Route::post('devices', 'registerDevice');
        Route::get('devices/{device}', 'viewDevice');
    });

    Route::controller(DeviceSharingController::class)->group(function () {
        Route::post('devices/{device}/share', 'generateSharingUrl');
    });
});

require __DIR__ . '/auth.php';
require __DIR__ . '/public.php';
