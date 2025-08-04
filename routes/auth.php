<?php

use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticatedTokenController::class, 'store'])
    ->middleware('guest');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest');
