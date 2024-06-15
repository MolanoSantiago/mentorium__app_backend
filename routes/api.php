<?php

use App\Constants\TokenAbilityEnum;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('login');
    Route::post('/signup', 'signup')->name('signup');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/refresh-token', 'refresh')
            ->middleware('ability:' . TokenAbilityEnum::ISSUE_ACCESS_TOKEN->value)
            ->name('refresh-token');
        Route::get('/logout', 'logout')
            ->middleware('ability:' . TokenAbilityEnum::ACCESS_API->value)
            ->name('logout');
    });
});
