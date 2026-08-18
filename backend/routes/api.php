<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ChartOfAccountController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\ProfitLossController;
use App\Http\Controllers\Api\V1\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('chart-of-accounts', ChartOfAccountController::class);
    Route::apiResource('transactions', TransactionController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::get('profit-loss', ProfitLossController::class);
    Route::get('dashboard/chart', DashboardController::class);
});