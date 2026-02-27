<?php

return [
    'module' => [
        'name' => 'partners',
        'display_name' => ['es' => 'Terceros', 'en' => 'Partners'],
        'icon' => 'ti tabler-address-book',
        'color' => 'dark',
    ],
    'items' => [
        [
            'key' => 'dashboard',
            'title' => ['es' => 'Dashboard', 'en' => 'Dashboard'],
            'icon' => 'ti tabler-smart-home',
            'route' => 'partners.index',
            'permission' => 'partners.view',
        ],
        [
            'key' => 'personas',
            'title' => ['es' => 'Personas', 'en' => 'People'],
            'icon' => 'ti tabler-users',
            'route' => 'partners.personas.index',
            'permission' => 'partners.personas.view',
        ],
        [
            'key' => 'empresas',
            'title' => ['es' => 'Empresas', 'en' => 'Companies'],
            'icon' => 'ti tabler-building',
            'route' => 'partners.empresas.index',
            'permission' => 'partners.empresas.view',
        ],
        [
            'key' => 'relaciones',
            'title' => ['es' => 'Relacion Persona - Empresa', 'en' => 'Person - Company Links'],
            'icon' => 'ti tabler-link',
            'route' => 'partners.relaciones.index',
            'permission' => 'partners.relaciones.view',
        ],
        [
            'key' => 'clientes',
            'title' => ['es' => 'Clientes', 'en' => 'Customers'],
            'icon' => 'ti tabler-user-check',
            'route' => 'partners.clientes.index',
            'permission' => 'partners.clientes.view',
        ],
        [
            'key' => 'proveedores',
            'title' => ['es' => 'Proveedores', 'en' => 'Suppliers'],
            'icon' => 'ti tabler-truck',
            'route' => 'partners.proveedores.index',
            'permission' => 'partners.proveedores.view',
        ],
        [
            'key' => 'pacientes',
            'title' => ['es' => 'Pacientes', 'en' => 'Patients'],
            'icon' => 'ti tabler-heartbeat',
            'route' => 'partners.pacientes.index',
            'permission' => 'partners.pacientes.view',
        ],
    ],
];
