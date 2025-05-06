<?php

use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/balance', [TransactionController::class, 'balance']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
