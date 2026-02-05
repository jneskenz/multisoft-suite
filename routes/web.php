<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Ruta raíz - redirige al locale por defecto o al preferido del usuario
Route::get('/', function () {
    $locale = session('locale', config('app.locale', 'es'));
    return redirect("/{$locale}");
})->name('home');

// Página informativa pública con locale (sin autenticación)
Route::prefix('{locale}')
    ->where(['locale' => 'es|en'])
    ->middleware(['web', 'setlocale'])
    ->group(function () {
        // Landing page pública
        Route::get('/', function () {
            if (auth()->check()) {
                return redirect(app()->getLocale() . '/welcome');
            }
            return view('index');
        })->name('locale.home');
    });

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren autenticación)
|--------------------------------------------------------------------------
*/

Route::prefix('{locale}')
    ->where(['locale' => 'es|en'])
    ->middleware(['web', 'setlocale', 'auth'])
    ->group(function () {
        // Página de bienvenida post-login (selector de módulos)
        Route::get('/welcome', function () {
            return view('welcome');
        })->name('welcome');

        /*
        |----------------------------------------------------------------------
        | Módulo Core - Administración del Sistema
        |----------------------------------------------------------------------
        */
        Route::prefix('core')->middleware('can:access.core')->group(function () {
            Route::get('/', fn () => view('core::dashboard'))->name('core.index');
            Route::get('/users', fn () => view('core::users.index'))->name('core.users.index');
            Route::get('/roles', fn () => view('core::roles.index'))->name('core.roles.index');
            Route::get('/settings', fn () => view('core::settings.index'))->name('core.settings.index');
        });

        /*
        |----------------------------------------------------------------------
        | Módulo ERP - Planificación de Recursos
        |----------------------------------------------------------------------
        */
        Route::prefix('erp')->middleware('can:access.erp')->group(function () {
            Route::get('/', fn () => view('erp::index'))->name('erp.index');
            Route::get('/inventory', fn () => view('erp::inventory.index'))->name('erp.inventory.index');
            Route::get('/sales', fn () => view('erp::sales.index'))->name('erp.sales.index');
            Route::get('/purchases', fn () => view('erp::purchases.index'))->name('erp.purchases.index');
        });

        /*
        |----------------------------------------------------------------------
        | Módulo HR - Recursos Humanos
        |----------------------------------------------------------------------
        */
        Route::prefix('hr')->middleware('can:access.hr')->group(function () {
            Route::get('/', fn () => view('hr::index'))->name('hr.index');
            Route::get('/employees', fn () => view('hr::employees.index'))->name('hr.employees.index');
            Route::get('/attendance', fn () => view('hr::attendance.index'))->name('hr.attendance.index');
            Route::get('/payroll', fn () => view('hr::payroll.index'))->name('hr.payroll.index');
        });

        /*
        |----------------------------------------------------------------------
        | Módulo CRM - Gestión de Clientes
        |----------------------------------------------------------------------
        */
        Route::prefix('crm')->middleware('can:access.crm')->group(function () {
            Route::get('/', fn () => view('crm::index'))->name('crm.index');
            Route::get('/leads', fn () => view('crm::leads.index'))->name('crm.leads.index');
            Route::get('/opportunities', fn () => view('crm::opportunities.index'))->name('crm.opportunities.index');
            Route::get('/activities', fn () => view('crm::activities.index'))->name('crm.activities.index');
        });

        /*
        |----------------------------------------------------------------------
        | Módulo FMS - Sistema Financiero
        |----------------------------------------------------------------------
        */
        Route::prefix('fms')->middleware('can:access.fms')->group(function () {
            Route::get('/', fn () => view('fms::index'))->name('fms.index');
            Route::get('/accounts', fn () => view('fms::accounts.index'))->name('fms.accounts.index');
            Route::get('/entries', fn () => view('fms::entries.index'))->name('fms.entries.index');
            Route::get('/reports', fn () => view('fms::reports.index'))->name('fms.reports.index');
        });

        /*
        |----------------------------------------------------------------------
        | Módulo Reports - Centro de Reportes
        |----------------------------------------------------------------------
        */
        Route::prefix('reports')->middleware('can:access.reports')->group(function () {
            Route::get('/', fn () => view('reports::index'))->name('reports.index');
            Route::get('/generate', fn () => view('reports::generate.index'))->name('reports.generate.index');
            Route::get('/scheduled', fn () => view('reports::scheduled.index'))->name('reports.scheduled.index');
            Route::get('/templates', fn () => view('reports::templates.index'))->name('reports.templates.index');
        });
    });
