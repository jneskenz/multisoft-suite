<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ValidateGroup;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registrar middleware de locale y grupo
        $middleware->alias([
            'setlocale' => SetLocale::class,
            'validate.group' => ValidateGroup::class,
        ]);

        // Configurar redirección de autenticación
        $middleware->redirectGuestsTo(fn () => route('login', ['locale' => app()->getLocale() ?: 'es']));

        // Usuarios autenticados: redirigir a selección de grupo (el FortifyServiceProvider maneja el flujo post-login)
        $middleware->redirectUsersTo(function () {
            $locale = app()->getLocale() ?: 'es';
            $groupCode = session('current_group_code');

            // Si tiene un grupo en sesión, redirigir al dashboard con grupo
            if ($groupCode) {
                return "/{$locale}/{$groupCode}/dashboard";
            }

            // Si no tiene grupo en sesión, ir a selección
            return "/{$locale}/select-group";
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
