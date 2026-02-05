<?php

namespace Modules\Core\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * El namespace del controlador del módulo.
     */
    protected string $moduleNamespace = 'Modules\Core\Http\Controllers';

    /**
     * Definir las rutas del módulo.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Mapear las rutas del módulo.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Mapear rutas web del módulo.
     * No aplicamos namespace para permitir componentes Livewire con FQCN.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(module_path('Core', '/Routes/web.php'));
    }

    /**
     * Mapear rutas API del módulo.
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(module_path('Core', '/Routes/api.php'));
    }
}
