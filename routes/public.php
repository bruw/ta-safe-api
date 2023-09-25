<?php

use App\Http\Controllers\Device\DeviceSharingController;
use Illuminate\Support\Facades\Route;

Route::get('devices/{device}/share', [
    DeviceSharingController::class, 'viewDeviceSharedByUrl'
])->name('share.device')->middleware('signed');
