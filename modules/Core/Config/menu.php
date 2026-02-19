<?php

return [
    'module' => [
        'name' => 'core',
        'display_name' => ['es' => 'Core', 'en' => 'Core'],
        'icon' => 'ti tabler-settings',
        'color' => 'secondary',
    ],
    'items' => [
        [
            'key' => 'dashboard',
            'title' => ['es' => 'Dashboard', 'en' => 'Dashboard'],
            'icon' => 'ti tabler-smart-home',
            'route' => 'core.index',
            'permission' => 'access.core',
        ],
        [
            'key' => 'admin',
            'title' => ['es' => 'Administración', 'en' => 'Administration'],
            'icon' => 'ti tabler-shield',
            'permission' => 'core.users.view',
            'children' => [
                [
                    'key' => 'users',
                    'title' => ['es' => 'Usuarios', 'en' => 'Users'],
                    'icon' => 'ti tabler-users',
                    'route' => 'core.users.index',
                    'permission' => 'core.users.view',
                ],
                [
                    'key' => 'roles',
                    'title' => ['es' => 'Roles', 'en' => 'Roles'],
                    'icon' => 'ti tabler-lock',
                    'route' => 'core.roles.index',
                    'permission' => 'core.roles.view',
                ],
                [
                    'key' => 'permissions',
                    'title' => ['es' => 'Permisos', 'en' => 'Permissions'],
                    'icon' => 'ti tabler-lock',
                    'route' => 'core.permissions.index',
                    'permission' => 'core.permissions.view',
                ],
                [
                    'key' => 'group-companies',
                    'title' => ['es' => 'Grupos de Empresa', 'en' => 'Group Companies'],
                    'icon' => 'ti tabler-building-skyscraper',
                    'route' => 'core.group_companies.index',
                    'permission' => 'core.groups.view', // ajusta al permiso real
                ],
                [
                    'key' => 'companies',
                    'title' => ['es' => 'Empresas', 'en' => 'Companies'],
                    'icon' => 'ti tabler-building',
                    'route' => 'core.companies.index',
                    'permission' => 'core.companies.view',
                ],
                [
                    'key' => 'locations',
                    'title' => ['es' => 'Locales', 'en' => 'Locations'],
                    'icon' => 'ti tabler-map-pin',
                    'route' => 'core.locations.index',
                    'permission' => 'core.locations.view',
                ],

            ],
        ],
        [
            'key' => 'settings',
            'title' => ['es' => 'Configuración', 'en' => 'Settings'],
            'icon' => 'ti tabler-settings',
            'route' => 'core.settings.index',
            'permission' => 'core.settings.view',
        ],
    ],
];
