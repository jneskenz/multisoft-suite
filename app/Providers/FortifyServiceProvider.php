<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Idiomas soportados
     */
    protected array $supportedLocales = ['es', 'en'];

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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Vistas de autenticación con Vuexy UI (reciben locale de la URL)
        Fortify::loginView(fn (Request $request) => $this->authView($request, 'auth.login'));
        Fortify::registerView(fn (Request $request) => $this->authView($request, 'auth.register'));
        Fortify::requestPasswordResetLinkView(fn (Request $request) => $this->authView($request, 'auth.forgot-password'));
        Fortify::resetPasswordView(fn (Request $request) => $this->authView($request, 'auth.reset-password', ['request' => $request]));
        Fortify::verifyEmailView(fn (Request $request) => $this->authView($request, 'auth.verify-email'));
        Fortify::confirmPasswordView(fn (Request $request) => $this->authView($request, 'auth.confirm-password'));
        Fortify::twoFactorChallengeView(fn (Request $request) => $this->authView($request, 'auth.two-factor-challenge'));

        // Redirección dinámica después del login (según locale actual)
        Fortify::redirects('login', fn (Request $request) => $this->getHomeUrl($request));
        Fortify::redirects('logout', fn (Request $request) => $this->getLoginUrl($request));
        Fortify::redirects('register', fn (Request $request) => $this->getHomeUrl($request));

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

    /**
     * Renderizar vista de autenticación con locale configurado
     */
    protected function authView(Request $request, string $view, array $data = [])
    {
        $locale = $this->getLocaleFromRequest($request);
        app()->setLocale($locale);
        
        return view($view, array_merge($data, ['locale' => $locale]));
    }

    /**
     * Obtener locale de la request
     */
    protected function getLocaleFromRequest(Request $request): string
    {
        $locale = $request->route('locale') ?? session('locale', config('app.locale', 'es'));
        
        return in_array($locale, $this->supportedLocales) ? $locale : 'es';
    }

    /**
     * Obtener URL home con locale y grupo (post-login)
     *
     * Redirige a:
     * - /{locale}/{group}/welcome si el usuario tiene un solo grupo
     * - /{locale}/select-group si tiene múltiples grupos
     */
    protected function getHomeUrl(Request $request): string
    {
        $locale = $this->getLocaleFromRequest($request);

        // Si el usuario tiene un solo grupo, redirigir directamente
        if (auth()->check()) {
            $groups = auth()->user()->group_companies;

            if ($groups->count() === 1) {
                $groupCode = $groups->first()->code;

                return "/{$locale}/{$groupCode}/welcome";
            }

            // Si tiene múltiples grupos, ir a selección
            if ($groups->count() > 1) {
                return "/{$locale}/select-group";
            }
        }

        // Fallback: usar grupo por defecto PE
        return "/{$locale}/PE/welcome";
    }

    /**
     * Obtener URL login con locale
     */
    protected function getLoginUrl(Request $request): string
    {
        return '/' . $this->getLocaleFromRequest($request) . '/login';
    }
}
