<?php

namespace Modules\ERP\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\ERP\Livewire\CatalogoMedidasManager;
use Modules\ERP\Livewire\CatalogoModalManager;
use Modules\ERP\Livewire\CatalogoTableManager;
use Modules\ERP\Livewire\OrdenTrabajoManager;
use Modules\ERP\Livewire\RecetaDetalleForm;
use Modules\ERP\Livewire\RecetaForm;
use Modules\ERP\Livewire\OrdenTrabajoForm;
use Modules\ERP\Livewire\TicketManager;
use Modules\ERP\Livewire\RecetaManager;

class ERPServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'ERP';
    protected string $moduleNameLower = 'erp';

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

    protected function registerLivewireComponents(): void
    {
        Livewire::component('erp-catalogo-medidas-manager', CatalogoMedidasManager::class);
        Livewire::component('erp-catalogo-modal-manager', CatalogoModalManager::class);
        Livewire::component('erp-catalogo-table-manager', CatalogoTableManager::class);
        Livewire::component('erp-ticket-manager', TicketManager::class);
        Livewire::component('erp-receta-manager', RecetaManager::class);
        Livewire::component('erp-receta-form', RecetaForm::class);
        Livewire::component('erp-receta-detalle-form', RecetaDetalleForm::class);
        Livewire::component('erp-orden-trabajo-manager', OrdenTrabajoManager::class);
        Livewire::component('erp-orden-trabajo-form', OrdenTrabajoForm::class);
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
