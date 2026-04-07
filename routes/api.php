<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Pos\PosInitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─── Public API ───
Route::post('/login', [AuthController::class, 'login']);

// ─── Protected API ───
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // POS Desktop / Mobile Actions
    Route::prefix('pos')->group(function () {
        Route::get('/init', [PosInitController::class, 'init']);
        Route::post('/sync', [App\Http\Controllers\POS\OfflineSyncController::class, 'sync']);
    });
});
