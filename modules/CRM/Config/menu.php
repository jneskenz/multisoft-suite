<?php

return [
    'module' => [
        'name' => 'crm',
        'display_name' => ['es' => 'CRM', 'en' => 'CRM'],
        'icon' => 'ti tabler-chart-pie',
        'color' => 'info',
    ],
    'items' => [
        [
            'key' => 'dashboard',
            'title' => ['es' => 'Dashboard', 'en' => 'Dashboard'],
            'icon' => 'ti tabler-smart-home',
            'route' => 'crm.index',
            'permission' => 'access.crm',
        ],
        [
            'key' => 'leads',
            'title' => ['es' => 'Leads', 'en' => 'Leads'],
            'icon' => 'ti tabler-user-search',
            'route' => 'crm.leads.index',
            'permission' => 'crm.leads.view',
        ],
        [
            'key' => 'opportunities',
            'title' => ['es' => 'Oportunidades', 'en' => 'Opportunities'],
            'icon' => 'ti tabler-target-arrow',
            'route' => 'crm.opportunities.index',
            'permission' => 'crm.opportunities.view',
        ],
        [
            'key' => 'activities',
            'title' => ['es' => 'Actividades', 'en' => 'Activities'],
            'icon' => 'ti tabler-calendar-event',
            'route' => 'crm.activities.index',
            'permission' => 'crm.activities.view',
        ],
    ],
];
