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
                    'title' => ['es' => 'Artículos', 'en' => 'Products'],
                    'icon' => 'ti tabler-box',
                    'route' => 'erp.inventory.products.index',
                    'permission' => 'erp.inventory.view',
                ],
                [
                    'key' => 'kardex',
                    'title' => ['es' => 'Kardex', 'en' => 'Kardex'],
                    'icon' => 'ti tabler-clipboard-list',
                    'route' => 'erp.inventory.kardex.index',
                    'permission' => 'erp.inventory.view',
                ],
                [
                    'key' => 'categories',
                    'title' => ['es' => 'Categorías', 'en' => 'Categories'],
                    'icon' => 'ti tabler-category',
                    'route' => 'erp.inventory.categories.index',
                    'permission' => 'erp.inventory.view',
                ],
                [
                    'key' => 'warehouses',
                    'title' => ['es' => 'Almacenes', 'en' => 'Warehouses'],
                    'icon' => 'ti tabler-building-warehouse',
                    'route' => 'erp.inventory.warehouses.index',
                    'permission' => 'erp.inventory.view',
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
                    'key' => 'suppliers',
                    'title' => ['es' => 'Proveedores', 'en' => 'Suppliers'],
                    'icon' => 'ti tabler-users',
                    'route' => 'erp.purchases.suppliers.index',
                    'permission' => 'erp.purchases.view',
                ],
                [
                    'key' => 'purchase-orders',
                    'title' => ['es' => 'Órdenes de Compra', 'en' => 'Purchase Orders'],
                    'icon' => 'ti tabler-file-text',
                    'route' => 'erp.purchases.orders.index',
                    'permission' => 'erp.purchases.view',
                ],
                [
                    'key' => 'receipts',
                    'title' => ['es' => 'Recepciones', 'en' => 'Receipts'],
                    'icon' => 'ti tabler-package-import',
                    'route' => 'erp.purchases.receipts.index',
                    'permission' => 'erp.purchases.view',
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
                    'key' => 'customers',
                    'title' => ['es' => 'Clientes', 'en' => 'Customers'],
                    'icon' => 'ti tabler-user-dollar',
                    'route' => 'erp.sales.customers.index',
                    'permission' => 'erp.sales.view',
                ],
                [
                    'key' => 'quotes',
                    'title' => ['es' => 'Cotizaciones', 'en' => 'Quotes'],
                    'icon' => 'ti tabler-file-description',
                    'route' => 'erp.sales.quotes.index',
                    'permission' => 'erp.sales.view',
                ],
                [
                    'key' => 'invoices',
                    'title' => ['es' => 'Facturas', 'en' => 'Invoices'],
                    'icon' => 'ti tabler-file-invoice',
                    'route' => 'erp.sales.invoices.index',
                    'permission' => 'erp.sales.view',
                ],
            ],
        ],
    ],
];
