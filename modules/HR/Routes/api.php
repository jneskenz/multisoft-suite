<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\Api\EmployeeController;

Route::prefix('v1/hr')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::apiResource('employees', EmployeeController::class)->names('api.hr.employees');
});
