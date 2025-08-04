<?php

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest');
