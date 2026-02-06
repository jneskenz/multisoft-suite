<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureUrlDefaults();
        $this->registerRouteMacros();
        $this->configureSuperAdminGate();
    }

    /**
     * Configurar Gate::before para superadmin.
     * El superadmin tiene todos los permisos automáticamente.
     */
    protected function configureSuperAdminGate(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });
    }

    /**
     * Configurar valores por defecto para parámetros de URL
     */
    protected function configureUrlDefaults(): void
    {
        // Establecer el locale por defecto para todas las rutas que lo requieran
        URL::defaults([
            'locale' => request()->route('locale') ?? session('locale', config('app.locale', 'es')),
            'group' => request()->route('group') ?? session('current_group_code'),
        ]);
    }

    /**
     * Registrar macros personalizadas para el Router.
     *
     * Estas macros simplifican la definición de rutas con locale y grupo:
     * - localePublic(): Para rutas públicas solo con locale (login, landing)
     * - localeGroup(): Para rutas protegidas con locale + grupo
     */
    protected function registerRouteMacros(): void
    {
        /**
         * Macro para rutas públicas con locale (sin grupo).
         *
         * Uso:
         *   Route::localePublic(function () {
         *       Route::get('/', fn() => view('landing'))->name('landing');
         *   });
         *
         * Genera rutas como: /{locale}/
         */
        Route::macro('localePublic', function (callable $callback) {
            return Route::prefix('{locale}')
                ->where(['locale' => 'es|en'])
                ->middleware(['web', 'setlocale'])
                ->group($callback);
        });

        /**
         * Macro para rutas protegidas con locale y grupo.
         *
         * Uso:
         *   Route::localeGroup(function () {
         *       Route::prefix('core')->middleware('can:access.core')->group(function () {
         *           Route::get('/users', fn() => view('core::users.index'))->name('core.users.index');
         *       });
         *   });
         *
         * Genera rutas como: /{locale}/{group}/core/users
         */
        Route::macro('localeGroup', function (callable $callback) {
            return Route::prefix('{locale}/{group}')
                ->where([
                    'locale' => 'es|en',
                    'group' => '[A-Z]{2,5}', // Códigos de país: PE, EC, CO, etc.
                ])
                ->middleware(['web', 'setlocale', 'auth', 'validate.group'])
                ->group($callback);
        });

        /**
         * Macro para rutas protegidas solo con locale (sin grupo).
         *
         * Para páginas de transición como selección de grupo después del login.
         *
         * Uso:
         *   Route::localeAuth(function () {
         *       Route::get('/select-group', fn() => view('select-group'))->name('select.group');
         *   });
         */
        Route::macro('localeAuth', function (callable $callback) {
            return Route::prefix('{locale}')
                ->where(['locale' => 'es|en'])
                ->middleware(['web', 'setlocale', 'auth'])
                ->group($callback);
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
