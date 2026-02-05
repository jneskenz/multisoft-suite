<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\Api\LeadController;

Route::prefix('v1/crm')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::apiResource('leads', LeadController::class)->names('api.crm.leads');
});
