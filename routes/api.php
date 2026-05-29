<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UnitsController;
use App\Http\Controllers\Api\TenantsController;
use App\Http\Controllers\Api\PaymentsController;

Route::get('/dashboard', [DashboardController::class, 'index']);
Route::apiResource('units', UnitsController::class)->only(['index', 'show', 'update']);
Route::apiResource('tenants', TenantsController::class)->only(['index', 'show', 'store', 'update']);
Route::post('/tenants/{tenant}/payments', [PaymentsController::class, 'store']);
Route::get('/tenants/{tenant}/payments', [PaymentsController::class, 'index']);
