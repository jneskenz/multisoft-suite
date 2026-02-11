<?php

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Livewire\Livewire;
use Modules\Core\Services\ModuleService;
use Modules\Core\Http\Middleware\DetectActiveModule;
use Modules\Core\Http\ViewComposers\ModulesComposer;
use Modules\Core\Console\Commands\ClearModuleCacheCommand;
use Modules\Core\Console\Commands\ListModulesCommand;
use Modules\Core\Livewire\UserManager;
use Modules\Core\Livewire\RoleManager;
use Modules\Core\Livewire\PermissionManager;
use Modules\Core\Livewire\CompanyManager;
use Modules\Core\Livewire\LocationManager;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * El nombre del módulo.
     */
    protected string $moduleName = 'Core';

    /**
     * El namespace del módulo.
     */
    protected string $moduleNameLower = 'core';

    /**
     * Registrar servicios del módulo.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        // Registrar ModuleService como singleton
        $this->app->singleton(ModuleService::class, function ($app) {
            return new ModuleService();
        });

        // Alias corto para el servicio
        $this->app->alias(ModuleService::class, 'modules');
    }

    /**
     * Bootstrap de los servicios del módulo.
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerMiddleware();
        $this->registerViewComposers();
        $this->registerCommands();
        $this->registerLivewireComponents();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
    }

    /**
     * Registrar componentes Livewire del módulo.
     */
    protected function registerLivewireComponents(): void
    {
        // Registrar con nombre simple (sin ::)
        Livewire::component('core-user-manager', UserManager::class);
        Livewire::component('core-role-manager', RoleManager::class);
        Livewire::component('core-permission-manager', PermissionManager::class);
        Livewire::component('core-company-manager', CompanyManager::class);
        Livewire::component('core-location-manager', LocationManager::class);
    }

    /**
     * Registrar comandos Artisan.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearModuleCacheCommand::class,
                ListModulesCommand::class,
            ]);
        }
    }

    /**
     * Registrar middleware del módulo.
     */
    protected function registerMiddleware(): void
    {
        // Registrar el middleware en el grupo 'web'
        $router = $this->app['router'];
        $router->aliasMiddleware('detect.module', DetectActiveModule::class);

        // Añadir al grupo web automáticamente
        $router->pushMiddlewareToGroup('web', DetectActiveModule::class);
    }

    /**
     * Registrar view composers.
     */
    protected function registerViewComposers(): void
    {
        // Compartir módulos accesibles con vistas específicas
        View::composer(
            ['components.apps-switcher', 'layouts.app', 'layouts.partials.*'],
            ModulesComposer::class
        );
    }

    /**
     * Registrar configuración del módulo.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'),
            $this->moduleNameLower
        );
    }

    /**
     * Registrar vistas del módulo.
     */
    protected function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Registrar traducciones del módulo.
     */
    protected function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'));
        }
    }

    /**
     * Obtener rutas de vistas publicables.
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
