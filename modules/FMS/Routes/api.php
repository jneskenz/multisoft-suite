<?php

use Illuminate\Support\Facades\Route;
use Modules\FMS\Http\Controllers\Api\AccountController;

Route::prefix('v1/fms')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::apiResource('accounts', AccountController::class)->names('api.fms.accounts');
});
