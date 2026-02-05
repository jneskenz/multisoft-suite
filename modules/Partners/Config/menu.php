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
            'permission' => 'access.partners',
        ],
        [
            'key' => 'customers',
            'title' => ['es' => 'Clientes', 'en' => 'Customers'],
            'icon' => 'ti tabler-users',
            'route' => 'partners.customers.index',
            'permission' => 'partners.customers.view',
        ],
        [
            'key' => 'suppliers',
            'title' => ['es' => 'Proveedores', 'en' => 'Suppliers'],
            'icon' => 'ti tabler-truck',
            'route' => 'partners.suppliers.index',
            'permission' => 'partners.suppliers.view',
        ],
        [
            'key' => 'contacts',
            'title' => ['es' => 'Contactos', 'en' => 'Contacts'],
            'icon' => 'ti tabler-phone',
            'route' => 'partners.contacts.index',
            'permission' => 'partners.contacts.view',
        ],
    ],
];
