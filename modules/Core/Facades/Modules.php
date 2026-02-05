<?php

namespace Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array all()
 * @method static array enabled()
 * @method static array accessible()
 * @method static array|null find(string $name)
 * @method static array|null detectActive($request = null)
 * @method static array getMenu(string $moduleName)
 * @method static array getAccessibleMenu(string $moduleName)
 * @method static void clearCache()
 *
 * @see \Modules\Core\Services\ModuleService
 */
class Modules extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'modules';
    }
}
