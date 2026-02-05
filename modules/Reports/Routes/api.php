<?php

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\Api\ReportController;

Route::prefix('v1/reports')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('api.reports.index');
    Route::post('/generate', [ReportController::class, 'generate'])->name('api.reports.generate');
    Route::get('/{report}', [ReportController::class, 'show'])->name('api.reports.show');
    Route::post('/{report}/export', [ReportController::class, 'export'])->name('api.reports.export');
});
