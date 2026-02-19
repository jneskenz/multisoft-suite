<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\CargoController;
use Modules\HR\Http\Controllers\ContratoController;
use Modules\HR\Http\Controllers\PlantillaController;
use Modules\HR\Http\Controllers\DepartamentoController;
use Modules\Core\Services\ModuleService;
// use Modules\HR\Services\ModuleService;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
|
| Estas rutas no requieren autenticación ni grupo.
| Usan el patrón: /{locale}/...
|
*/

// Ruta raíz - redirige al locale por defecto
Route::get('/', function () {
    $locale = session('locale', config('app.locale', 'es'));

    return redirect("/{$locale}");
})->name('home');

// Rutas públicas con locale (login, landing, etc.)
Route::localePublic(function () {
    // Landing page pública
    Route::get('/', function () {
        if (auth()->check()) {
            // Redirigir al grupo por defecto del usuario
            $defaultGroup = auth()->user()->groupCompanies->first()?->code ?? 'PE';

            return redirect('/' . app()->getLocale() . '/' . $defaultGroup . '/welcome');
        }

        return view('index');
    })->name('locale.home');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas sin Grupo
|--------------------------------------------------------------------------
|
| Rutas que requieren autenticación pero NO un grupo específico.
| Útil para selección de grupo después del login.
| Patrón: /{locale}/...
|
*/

Route::localeAuth(function () {
    // Selector de grupo (cuando el usuario tiene acceso a múltiples grupos)
    Route::get('/select-group', function () {
        $groups = auth()->user()->group_companies;

        // Si solo tiene un grupo, redirigir directamente
        if ($groups->count() === 1) {
            $group = $groups->first();

            return redirect('/' . app()->getLocale() . '/' . $group->code . '/welcome');
        }

        return view('select-group', compact('groups'));
    })->name('select.group');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas con Grupo
|--------------------------------------------------------------------------
|
| Estas rutas requieren autenticación y un grupo válido.
| Usan el patrón: /{locale}/{group}/...
| Ejemplo: /es/PE/core/users
|
*/

Route::localeGroup(function () {
    // Página de bienvenida post-login (selector de módulos)
    Route::get('/welcome', function () {
        return view('welcome');
    })->name('welcome');

    // Dashboard general (redirige al módulo por defecto)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Módulo Core - Administración del Sistema
    |----------------------------------------------------------------------
    */
    Route::prefix('core')->middleware('can:access.core')->group(function () {
        Route::get('/', fn() => view('core::dashboard'))->name('core.index');
        Route::get('/users', fn() => view('core::users.index'))->name('core.users.index');
        Route::get('/roles', fn() => view('core::roles.index'))->name('core.roles.index');
        Route::get('/permissions', fn() => view('core::permissions.index'))->name('core.permissions.index');
        Route::get('/settings', fn() => view('core::settings.index'))->name('core.settings.index');
        Route::get('/audit', fn() => view('core::audit.index'))->name('core.audit.index');

        // Menu de Gestión de Grupos de Empresa
        Route::middleware('can:core.groups.view')->group(function () {
            Route::get('/group-companies', [\Modules\Core\Http\Controllers\GroupCompanyController::class, 'index'])->name('core.group_companies.index');
            Route::get('/group-companies/create', [\Modules\Core\Http\Controllers\GroupCompanyController::class, 'create'])->name('core.group_companies.create');
            Route::post('/group-companies', [\Modules\Core\Http\Controllers\GroupCompanyController::class, 'store'])->name('core.group_companies.store');
            Route::get('/group-companies/{grupo}', [\Modules\Core\Http\Controllers\GroupCompanyController::class, 'show'])->name('core.group_companies.show');
            Route::get('/group-companies/{grupo}/edit', [\Modules\Core\Http\Controllers\GroupCompanyController::class, 'edit'])->name('core.group_companies.edit');
            Route::put('/group-companies/{grupo}', [\Modules\Core\Http\Controllers\GroupCompanyController::class, 'update'])->name('core.group_companies.update');
            Route::delete('/group-companies/{grupo}', [\Modules\Core\Http\Controllers\GroupCompanyController::class, 'destroy'])->name('core.group_companies.destroy');
        });

        // Menu de Gestion de Empresas
        Route::middleware('can:core.companies.view')->group(function () {
            Route::get('/companies', fn() => view('core::companies.index'))->name('core.companies.index');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Módulo ERP - Planificación de Recursos
    |----------------------------------------------------------------------
    */
    Route::prefix('erp')->middleware('can:access.erp')->group(function () {
        Route::get('/', fn() => view('erp::index'))->name('erp.index');
        Route::get('/inventory', fn() => view('erp::inventory.index'))->name('erp.inventory.index');
        Route::get('/sales', fn() => view('erp::sales.index'))->name('erp.sales.index');
        Route::get('/purchases', fn() => view('erp::purchases.index'))->name('erp.purchases.index');
    });

    /*
    |----------------------------------------------------------------------
    | Módulo HR - Recursos Humanos
    |----------------------------------------------------------------------
    */
    Route::prefix('hr')->middleware('can:access.hr')->group(function () {
        Route::get('/', fn() => view('hr::index'))->name('hr.index');

        // Empleados
        Route::get('/empleados', fn() => view('hr::empleados.index'))->name('hr.empleados.index');
        Route::get('/empleados/{empleados}', fn() => view('hr::empleados.show'))->name('hr.empleados.show');

        // Contratos
        Route::prefix('contratos')
            ->name('hr.contratos.')
            ->group(function () {
                Route::get('/', [ContratoController::class, 'index'])->name('index');
                Route::post('/{contrato}/generar-documento', [ContratoController::class, 'generarDocumento'])->name('generar-documento');
                Route::get('/documento/{documento}/ver', [ContratoController::class, 'verDocumento'])->name('ver-documento')->whereNumber('documento');
                Route::get('/{contrato}', [ContratoController::class, 'show'])->name('show')->whereNumber('contrato');
            });

        // Configuracion > Departamentos
        Route::prefix('departamentos')
            ->name('hr.departamentos.')
            ->group(function () {
                Route::get('/', [DepartamentoController::class, 'index'])->name('index');
                Route::get('/create', [DepartamentoController::class, 'create'])->name('create');
                Route::post('/', [DepartamentoController::class, 'store'])->name('store');
                Route::get('/{departamento}', [DepartamentoController::class, 'show'])->name('show');
                Route::get('/{departamento}/edit', [DepartamentoController::class, 'edit'])->name('edit');
                Route::put('/{departamento}', [DepartamentoController::class, 'update'])->name('update');
                Route::delete('/{departamento}', [DepartamentoController::class, 'destroy'])->name('destroy');
            });

        // Configuracion > Cargos
        Route::prefix('cargos')
            ->name('hr.cargos.')
            ->group(function () {
                Route::get('/', [CargoController::class, 'index'])->name('index');
                Route::get('/{cargo}', [CargoController::class, 'show'])->name('show');
            });

        Route::prefix('plantillas')
            ->name('hr.plantillas.')
            ->group(function () {
                Route::get('/', [PlantillaController::class, 'index'])->name('index');
                Route::get('/preview-pdf', [PlantillaController::class, 'previewPdf'])->name('preview-pdf');
                Route::get('/{plantilla}', [PlantillaController::class, 'show'])->name('show')->whereNumber('plantilla');
            });


        Route::get('/attendance', fn() => view('hr::attendance.index'))->name('hr.attendance.index');
        Route::get('/payroll', fn() => view('hr::payroll.index'))->name('hr.payroll.index');

        Route::get('/{seccion}', function (string $seccion, ModuleService $moduleService) {
            $menu = $moduleService->getAccessibleMenu('hr');
            $seccionMenu = collect($menu['items'] ?? [])->first(function ($item) use ($seccion) {
                return ($item['key'] ?? null) === $seccion && !empty($item['children']);
            });

            abort_if(!$seccionMenu, 404);

            return view('hr::modulos.index', [
                'seccion' => $seccion,
                'seccionMenu' => $seccionMenu,
            ]);
        })
            ->whereIn('seccion', ['personal', 'asistencia', 'vacaciones', 'planilla', 'evaluaciones', 'reportes', 'configuracion'])
            ->name('hr.modulo');
    });

    /*
    |----------------------------------------------------------------------
    | Módulo CRM - Gestión de Clientes
    |----------------------------------------------------------------------
    */
    Route::prefix('crm')->middleware('can:access.crm')->group(function () {
        Route::get('/', fn() => view('crm::index'))->name('crm.index');
        Route::get('/leads', fn() => view('crm::leads.index'))->name('crm.leads.index');
        Route::get('/opportunities', fn() => view('crm::opportunities.index'))->name('crm.opportunities.index');
        Route::get('/activities', fn() => view('crm::activities.index'))->name('crm.activities.index');
    });

    /*
    |----------------------------------------------------------------------
    | Módulo FMS - Sistema Financiero
    |----------------------------------------------------------------------
    */
    Route::prefix('fms')->middleware('can:access.fms')->group(function () {
        Route::get('/', fn() => view('fms::index'))->name('fms.index');
        Route::get('/accounts', fn() => view('fms::accounts.index'))->name('fms.accounts.index');
        Route::get('/entries', fn() => view('fms::entries.index'))->name('fms.entries.index');
        Route::get('/reports', fn() => view('fms::reports.index'))->name('fms.reports.index');
    });

    /*
    |----------------------------------------------------------------------
    | Módulo Reports - Centro de Reportes
    |----------------------------------------------------------------------
    */
    Route::prefix('reports')->middleware('can:access.reports')->group(function () {
        Route::get('/', fn() => view('reports::index'))->name('reports.index');
        Route::get('/generate', fn() => view('reports::generate.index'))->name('reports.generate.index');
        Route::get('/scheduled', fn() => view('reports::scheduled.index'))->name('reports.scheduled.index');
        Route::get('/templates', fn() => view('reports::templates.index'))->name('reports.templates.index');
    });
});
