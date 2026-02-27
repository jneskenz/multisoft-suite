<?php

namespace Modules\Partners\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Contracts\PatientDirectoryContract;
use Modules\Partners\Services\PatientDirectoryService;

class PartnersServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Partners';
    protected string $moduleNameLower = 'partners';

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(PatientDirectoryContract::class, function () {
            return new PatientDirectoryService();
        });
    }

    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerTranslations();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
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
