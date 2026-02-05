<?php

return [
    'module' => [
        'name' => 'reports',
        'display_name' => ['es' => 'Reportes', 'en' => 'Reports'],
        'icon' => 'ti tabler-file-analytics',
        'color' => 'danger',
    ],
    'items' => [
        [
            'key' => 'dashboard',
            'title' => ['es' => 'Dashboard', 'en' => 'Dashboard'],
            'icon' => 'ti tabler-smart-home',
            'route' => 'reports.index',
            'permission' => 'access.reports',
        ],
        [
            'key' => 'generate',
            'title' => ['es' => 'Generar', 'en' => 'Generate'],
            'icon' => 'ti tabler-file-plus',
            'route' => 'reports.generate.index',
            'permission' => 'reports.generate',
        ],
        [
            'key' => 'scheduled',
            'title' => ['es' => 'Programados', 'en' => 'Scheduled'],
            'icon' => 'ti tabler-calendar-repeat',
            'route' => 'reports.scheduled.index',
            'permission' => 'reports.schedule',
        ],
        [
            'key' => 'templates',
            'title' => ['es' => 'Plantillas', 'en' => 'Templates'],
            'icon' => 'ti tabler-template',
            'route' => 'reports.templates.index',
            'permission' => 'reports.templates.view',
        ],
    ],
];
