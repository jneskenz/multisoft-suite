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
];
