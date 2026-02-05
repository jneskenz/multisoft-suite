<?php

namespace Modules\Core\Http\ViewComposers;

use Illuminate\View\View;
use Modules\Core\Services\ModuleService;

class ModulesComposer
{
    public function __construct(
        protected ModuleService $moduleService
    ) {}

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Get all accessible modules for the current user
        $accessibleModules = $this->moduleService->accessible();
        
        // Get only enabled modules
        $enabledModules = $this->moduleService->enabled();
        
        // Share with view
        $view->with('accessibleModules', $accessibleModules);
        $view->with('enabledModules', $enabledModules);
    }
}
