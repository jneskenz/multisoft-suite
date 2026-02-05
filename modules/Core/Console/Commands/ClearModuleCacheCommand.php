<?php

namespace Modules\Core\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\Services\ModuleService;

class ClearModuleCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'modules:clear-cache';

    /**
     * The console command description.
     */
    protected $description = 'Clear the modules discovery cache';

    /**
     * Execute the console command.
     */
    public function handle(ModuleService $moduleService): int
    {
        $moduleService->clearCache();
        
        $this->info('Modules cache cleared successfully!');
        
        return Command::SUCCESS;
    }
}
