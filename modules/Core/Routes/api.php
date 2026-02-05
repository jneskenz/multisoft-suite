<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\UserController;
use Modules\Core\Http\Controllers\Api\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes - Core Module
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware(['api'])->group(function () {
    
    // Autenticación
    Route::prefix('auth')->group(function () {
        Route::post('/login', function () {
            // TODO: Implementar LoginController
            return response()->json(['message' => 'Login endpoint']);
        })->name('api.auth.login');

        Route::post('/logout', function () {
            // TODO: Implementar LogoutController
            return response()->json(['message' => 'Logout exitoso']);
        })->middleware('auth:sanctum')->name('api.auth.logout');

        Route::get('/user', function () {
            return request()->user();
        })->middleware('auth:sanctum')->name('api.auth.user');
    });

    // Rutas protegidas
    Route::middleware(['auth:sanctum'])->group(function () {
        // Usuarios
        Route::apiResource('users', UserController::class)->names('api.users');
        
        // Roles
        Route::apiResource('roles', RoleController::class)->names('api.roles');
        
        // Permisos
        Route::get('/permissions', function () {
            return response()->json([
                'data' => [],
                'message' => 'Listado de permisos'
            ]);
        })->name('api.permissions.index');
    });
});
