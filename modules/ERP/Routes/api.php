<?php

use Illuminate\Support\Facades\Route;
use Modules\ERP\Http\Controllers\Api\InventoryController;

Route::prefix('v1/erp')->middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('inventory', InventoryController::class)->names('api.erp.inventory');
});
