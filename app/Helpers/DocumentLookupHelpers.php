<?php

use Modules\Core\Services\DocumentLookupService;

if (! function_exists('lookup_dni')) {
    /**
     * Consultar datos de persona por DNI.
     *
     * @return array{success: bool, data: array|null, message: string|null}
     */
    function lookup_dni(string $dni): array
    {
        return app(DocumentLookupService::class)->lookupDni($dni);
    }
}

if (! function_exists('lookup_ruc')) {
    /**
     * Consultar datos de empresa por RUC.
     *
     * @return array{success: bool, data: array|null, message: string|null}
     */
    function lookup_ruc(string $ruc): array
    {
        return app(DocumentLookupService::class)->lookupRuc($ruc);
    }
}
