<?php

namespace Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array lookupDni(string $dni)
 * @method static array lookupRuc(string $ruc)
 * @method static void clearCache(string $type, string $number)
 *
 * @see \Modules\Core\Services\DocumentLookupService
 */
class DocumentLookup extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'document-lookup';
    }
}
