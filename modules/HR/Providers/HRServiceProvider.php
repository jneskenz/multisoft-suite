<?php

namespace Modules\HR\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\HR\Livewire\EmpleadoManager;

class HRServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'HR';
    protected string $moduleNameLower = 'hr';

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerLivewireComponents();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Registrar componentes Livewire del módulo.
     */
    protected function registerLivewireComponents(): void
    {
        // Registrar con nombre simple (sin ::)
        Livewire::component('hr-empleado-manager', EmpleadoManager::class);
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    protected function registerViews(): void
    {
        $sourcePath = module_path($this->moduleName, 'Resources/views');
        $this->loadViewsFrom($sourcePath, $this->moduleNameLower);
    }

    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
    }
}
