<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\Device\DeviceController;
use App\Http\Controllers\Device\DeviceSharingController;
use App\Http\Controllers\Device\DeviceTransferController;
use App\Http\Controllers\DeviceModel\DeviceModelController;
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

Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register')->name('api.auth.register');
        Route::post('login', 'login')->name('api.auth.login');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::delete('logout', 'logout')->name('api.auth.logout');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('user', 'view')->name('api.user.view');
        Route::patch('user', 'update')->name('api.user.update');
        Route::get('user/search-by-email', 'searchByEmail')->name('api.user.search');
        Route::get('user/devices', 'devices')->name('api.user.devices');

        Route::get('user/devices-transfers', 'userDevicesTransfers');
    });

    Route::controller(DeviceController::class)->group(function () {
        Route::get('devices/{device}', 'view')->name('api.device.view');
        Route::post('devices', 'register')->name('api.device.register');
        Route::delete('devices/{device}', 'delete')->name('api.device.delete');
        Route::post('devices/{device}/validate', 'validation')->name('api.device.validation');
        Route::post('devices/{device}/invalidate', 'invalidation')->name('api.device.invalidation');
    });

    Route::controller(DeviceTransferController::class)->group(function () {
        Route::post('devices/{device}', 'create')->name('api.device.transfer.create');
        Route::post('device-transfers/{deviceTransfer}/accept', 'accept')->name('api.device.transfer.accept');
        Route::post('device-transfers/{deviceTransfer}/cancel', 'cancel')->name('api.device.transfer.cancel');
        Route::post('device-transfers/{deviceTransfer}/reject', 'reject')->name('api.device.transfer.reject');
    });

    Route::controller(DeviceSharingController::class)->group(function () {
        Route::post('devices/{device}/share', 'createSharingToken')->name('api.device.share.create');
        Route::get('devices', 'viewDeviceByToken')->name('api.device.share.view');
    });

    Route::controller(BrandController::class)->group(function () {
        Route::get('brands', 'brands');
    });

    Route::controller(DeviceModelController::class)->group(function () {
        Route::get('device-models/brands/{brand}', 'deviceModelsByBrand');
    });
});
