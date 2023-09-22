<?php

use App\Http\Controllers\Device\DeviceController;
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
});

require __DIR__ . '/auth.php';
