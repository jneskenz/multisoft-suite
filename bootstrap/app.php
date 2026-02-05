<?php

use App\Http\Middleware\SetLocale;
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
        // Registrar middleware de locale
        $middleware->alias([
            'setlocale' => SetLocale::class,
        ]);

        // Configurar redirección de autenticación
        $middleware->redirectGuestsTo(fn () => route('login', ['locale' => app()->getLocale() ?: 'es']));
        $middleware->redirectUsersTo(fn () => route('dashboard', ['locale' => app()->getLocale() ?: 'es']));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
