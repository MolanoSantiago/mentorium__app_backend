<?php

use App\Constants\TokenAbilityEnum;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/refresh-token', [AuthController::class, 'refresh'])
        ->middleware('ability:' . TokenAbilityEnum::ISSUE_ACCESS_TOKEN->value)
        ->name('refresh-token');
});
