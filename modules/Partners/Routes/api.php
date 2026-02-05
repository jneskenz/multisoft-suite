<?php

use Illuminate\Support\Facades\Route;
use Modules\Partners\Http\Controllers\Api\PartnerController;

Route::prefix('v1/partners')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::apiResource('/', PartnerController::class)
        ->parameters(['' => 'partner'])
        ->names([
            'index' => 'api.partners.index',
            'store' => 'api.partners.store',
            'show' => 'api.partners.show',
            'update' => 'api.partners.update',
            'destroy' => 'api.partners.destroy',
        ]);
});
