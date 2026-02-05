<?php

namespace Modules\Core\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\ModuleService;

class ListModulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'modules:list {--enabled : Only show enabled modules}';

    /**
     * The console command description.
     */
    protected $description = 'List all installed modules';

    /**
     * Execute the console command.
     */
    public function handle(ModuleService $moduleService): int
    {
        $modules = $this->option('enabled') 
            ? $moduleService->enabled() 
            : $moduleService->all();
        
        if (empty($modules)) {
            $this->warn('No modules found.');
            return Command::SUCCESS;
        }
        
        $locale = config('app.locale', 'es');
        
        $rows = collect($modules)->map(function ($module) use ($locale) {
            $displayName = is_array($module['display_name']) 
                ? ($module['display_name'][$locale] ?? $module['display_name']['en'] ?? $module['name'])
                : $module['display_name'];
                
            return [
                $module['name'],
                $displayName,
                $module['version'],
                $module['enabled'] ? '<fg=green>✓</>' : '<fg=red>✗</>',
                $module['order'],
                implode(', ', $module['dependencies']),
            ];
        })->toArray();
        
        $this->table(
            ['Alias', 'Name', 'Version', 'Enabled', 'Order', 'Dependencies'],
            $rows
        );
        
        return Command::SUCCESS;
    }
}
