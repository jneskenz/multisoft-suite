<?php

return [
    'name' => 'Core',
    
    /*
    |--------------------------------------------------------------------------
    | Configuración de Permisos
    |--------------------------------------------------------------------------
    */
    'permissions' => [
        'access.core',
        'core.users.view',
        'core.users.create',
        'core.users.edit',
        'core.users.delete',
        'core.roles.view',
        'core.roles.create',
        'core.roles.edit',
        'core.roles.delete',
        'core.permissions.view',
        'core.permissions.manage',
        'core.settings.view',
        'core.settings.edit',
        'core.audit.view',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Auditoría
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => true,
        'retention_days' => 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Sesión
    |--------------------------------------------------------------------------
    */
    'session' => [
        'timeout' => 120, // minutos
    ],

    /*
    |--------------------------------------------------------------------------
    | Consulta de Documentos (DNI / RUC)
    |--------------------------------------------------------------------------
    |
    | Configuración de la API externa para validar DNI y RUC.
    | API compatible: https://apis.net.pe  (u otra con formato /dni/{num}, /ruc/{num})
    |
    */
    'document_lookup' => [
        'base_url'  => env('DOCUMENT_LOOKUP_URL', 'https://api.decolecta.com/v1'),
        'token'     => env('DOCUMENT_LOOKUP_TOKEN', ''),
        'timeout'   => (int) env('DOCUMENT_LOOKUP_TIMEOUT', 10),
        'cache_ttl' => (int) env('DOCUMENT_LOOKUP_CACHE_TTL', 3600),
    ],
];
