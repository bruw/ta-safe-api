<?php

use App\Http\Controllers\Device\DeviceController;
use App\Http\Controllers\Device\DeviceSharingController;
use App\Http\Controllers\Device\DeviceTransferController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

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

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('user', 'currentUser');
        Route::put('user', 'update');
        Route::get('user/search', 'search');

        Route::get('user/devices', 'userDevices');
        Route::get('user/devices-transfers', 'userDevicesTransfers');
    });

    Route::controller(DeviceController::class)->group(function () {
        Route::post('devices', 'registerDevice');
        Route::get('devices/{device}', 'viewDevice');
    });

    Route::controller(DeviceTransferController::class)->group(function () {
        Route::post('devices/{device}', 'createDeviceTransfer');
        Route::put('device-transfers/{deviceTransfer}/accept', 'acceptDeviceTransfer');
        Route::put('device-transfers/{deviceTransfer}/reject', 'rejectDeviceTransfer');
    });

    Route::controller(DeviceSharingController::class)->group(function () {
        Route::post('devices/{device}/share', 'createSharingToken');
        Route::get('devices', 'viewDeviceByToken');
    });
});
