<?php

use App\Http\Controllers\Device\DeviceController;
use App\Http\Controllers\Device\DeviceShareController;
use App\Http\Controllers\UserController;
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
    });

    Route::controller(DeviceController::class)->group(function () {
        Route::post('devices', 'registerDevice');
        Route::get('devices/{device}', 'viewDevice');
    });

    Route::controller(DeviceShareController::class)->group(function () {
        Route::post('devices/{device}/share', 'generateShareLink');
    });
});

require __DIR__ . '/auth.php';
require __DIR__ . '/public.php';
