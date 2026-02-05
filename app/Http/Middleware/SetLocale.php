<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        // Validar que el locale sea soportado
        $supportedLocales = ['es', 'en'];
        
        if (! in_array($locale, $supportedLocales)) {
            $locale = config('app.locale', 'es');
        }

        // Establecer el locale de la aplicación
        app()->setLocale($locale);

        // También establecer locale para Carbon (fechas)
        \Carbon\Carbon::setLocale($locale);

        return $next($request);
    }
}
