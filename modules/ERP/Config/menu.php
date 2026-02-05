<?php

return [
    'module' => [
        'name' => 'erp',
        'display_name' => ['es' => 'ERP', 'en' => 'ERP'],
        'icon' => 'ti tabler-building',
        'color' => 'primary',
    ],
    'items' => [
        [
            'key' => 'dashboard',
            'title' => ['es' => 'Dashboard', 'en' => 'Dashboard'],
            'icon' => 'ti tabler-smart-home',
            'route' => 'erp.index',
            'permission' => 'access.erp',
        ],
        [
            'key' => 'inventory',
            'title' => ['es' => 'Inventario', 'en' => 'Inventory'],
            'icon' => 'ti tabler-package',
            'permission' => 'erp.inventory.view',
            'children' => [
                [
                    'key' => 'products',
                    'title' => ['es' => 'Productos', 'en' => 'Products'],
                    'icon' => 'ti tabler-box',
                    'route' => 'erp.inventory.index',
                    'permission' => 'erp.inventory.view',
                ],
            ],
        ],
        [
            'key' => 'sales',
            'title' => ['es' => 'Ventas', 'en' => 'Sales'],
            'icon' => 'ti tabler-shopping-cart',
            'permission' => 'erp.sales.view',
            'children' => [
                [
                    'key' => 'invoices',
                    'title' => ['es' => 'Facturas', 'en' => 'Invoices'],
                    'icon' => 'ti tabler-file-invoice',
                    'route' => 'erp.sales.index',
                    'permission' => 'erp.sales.view',
                ],
            ],
        ],
        [
            'key' => 'purchases',
            'title' => ['es' => 'Compras', 'en' => 'Purchases'],
            'icon' => 'ti tabler-truck',
            'permission' => 'erp.purchases.view',
            'children' => [
                [
                    'key' => 'orders',
                    'title' => ['es' => 'Órdenes', 'en' => 'Orders'],
                    'icon' => 'ti tabler-clipboard-list',
                    'route' => 'erp.purchases.index',
                    'permission' => 'erp.purchases.view',
                ],
            ],
        ],
    ],
];
