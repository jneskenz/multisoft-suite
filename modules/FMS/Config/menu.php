<?php

return [
    'module' => [
        'name' => 'fms',
        'display_name' => ['es' => 'Finanzas', 'en' => 'Finance'],
        'icon' => 'ti tabler-calculator',
        'color' => 'warning',
    ],
    'items' => [
        [
            'key' => 'dashboard',
            'title' => ['es' => 'Dashboard', 'en' => 'Dashboard'],
            'icon' => 'ti tabler-smart-home',
            'route' => 'fms.index',
            'permission' => 'access.fms',
        ],
        [
            'key' => 'accounts',
            'title' => ['es' => 'Plan de Cuentas', 'en' => 'Chart of Accounts'],
            'icon' => 'ti tabler-list-tree',
            'route' => 'fms.accounts.index',
            'permission' => 'fms.accounts.view',
        ],
        [
            'key' => 'entries',
            'title' => ['es' => 'Asientos', 'en' => 'Journal Entries'],
            'icon' => 'ti tabler-receipt',
            'route' => 'fms.entries.index',
            'permission' => 'fms.entries.view',
        ],
        [
            'key' => 'reports',
            'title' => ['es' => 'Estados Financieros', 'en' => 'Financial Reports'],
            'icon' => 'ti tabler-chart-bar',
            'route' => 'fms.reports.index',
            'permission' => 'fms.reports.view',
        ],
    ],
];
