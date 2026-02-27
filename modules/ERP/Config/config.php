<?php

return [
    'name' => 'ERP',
    'permissions' => [
        'access.erp',
        'erp.inventory.view',
        'erp.inventory.create',
        'erp.inventory.edit',
        'erp.inventory.delete',
        'erp.sales.view',
        'erp.sales.create',
        'erp.sales.edit',
        'erp.sales.delete',
        'erp.purchases.view',
        'erp.purchases.create',
        'erp.purchases.edit',
        'erp.purchases.delete',
        'erp.catalogos.view',
        'erp.catalogos.create',
        'erp.catalogos.edit',
        'erp.catalogos.delete',
    ],
    'default_currency' => env('ERP_DEFAULT_CURRENCY', 'PEN'),
];
