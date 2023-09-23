<?php

use App\Http\Controllers\Device\DeviceShareController;
use Illuminate\Support\Facades\Route;

Route::get('devices/{device}/share', [
    DeviceShareController::class, 'viewDeviceSharedByLink'
])->name('share.device')->middleware('signed');
