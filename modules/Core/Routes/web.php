<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Core Module
|--------------------------------------------------------------------------
| Las rutas de autenticación son manejadas por Laravel Fortify.
| Este archivo solo contiene las rutas del módulo Core.
*/

Route::prefix('{locale}')
    ->where(['locale' => 'es|en'])
    ->middleware(['web', 'setlocale'])
    ->group(function () {
        
        // Dashboard principal (requiere autenticación)
        Route::middleware(['auth'])->group(function () {
            Route::get('/', function () {
                return view('core::dashboard');
            })->name('dashboard');

            // Rutas de Core (usuarios, roles, permisos)
            Route::prefix('core')->name('core.')->middleware(['can:access.core'])->group(function () {
                // Usuarios
                Route::get('/users', function () {
                    return view('core::users.index');
                })->name('users.index');

                // Roles
                Route::get('/roles', function () {
                    return view('core::roles.index');
                })->name('roles.index');

                // Permisos
                Route::get('/permissions', function () {
                    return view('core::permissions.index');
                })->name('permissions.index');

                // Configuración
                Route::get('/settings', function () {
                    return view('core::settings.index');
                })->name('settings.index');

                // Auditoría
                Route::get('/audit', function () {
                    return view('core::audit.index');
                })->name('audit.index');
            });
        });
    });

// Redirección raíz al idioma por defecto
Route::get('/', function () {
    return redirect('/' . config('app.locale', 'es'));
});
