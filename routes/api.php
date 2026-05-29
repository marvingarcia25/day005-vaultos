<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UnitsController;
use App\Http\Controllers\Api\TenantsController;
use App\Http\Controllers\Api\PaymentsController;

Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/units',      [UnitsController::class, 'index']);
Route::get('/units/{id}', [UnitsController::class, 'show']);
Route::put('/units/{id}', [UnitsController::class, 'update']);

Route::get('/tenants',      [TenantsController::class, 'index']);
Route::get('/tenants/{id}', [TenantsController::class, 'show']);
Route::post('/tenants',     [TenantsController::class, 'store']);
Route::put('/tenants/{id}', [TenantsController::class, 'update']);

Route::get('/tenants/{id}/payments',  [PaymentsController::class, 'index']);
Route::post('/tenants/{id}/payments', [PaymentsController::class, 'store']);
