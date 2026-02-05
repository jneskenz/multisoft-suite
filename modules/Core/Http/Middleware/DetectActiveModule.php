<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Services\ModuleService;
use Symfony\Component\HttpFoundation\Response;

class DetectActiveModule
{
    public function __construct(
        protected ModuleService $moduleService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Detect the active module from the URL path
        $activeModule = $this->moduleService->detectActive($request);
        
        // Share the active module with all views
        view()->share('activeModule', $activeModule);
        
        // Store in request for controller access
        $request->attributes->set('activeModule', $activeModule);
        
        // Also share the module's menu if available
        if ($activeModule) {
            $menu = $this->moduleService->getMenu($activeModule['alias']);
            view()->share('moduleMenu', $menu);
        } else {
            view()->share('moduleMenu', null);
        }
        
        return $next($request);
    }
}
