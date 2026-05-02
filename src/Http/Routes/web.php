<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Jemgdevp\Domo\Http\Controllers\DashboardController;

Route::prefix(config('domo.dashboard.route', 'domo'))
    ->name('domo.')
    ->middleware(config('domo.dashboard.middleware', ['web']))
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/schema', [DashboardController::class, 'schema'])->name('schema');
        Route::get('/models', [DashboardController::class, 'models'])->name('models');
        Route::get('/analyze', [DashboardController::class, 'analyzePage'])->name('analyze');
        Route::post('/analyze', [DashboardController::class, 'analyze'])->name('analyze.post');
    });
