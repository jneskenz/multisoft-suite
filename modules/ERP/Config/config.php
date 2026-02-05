<?php

return [
    'name' => 'ERP',
    'permissions' => [
        'access.erp',
        'erp.companies.view',
        'erp.companies.create',
        'erp.companies.edit',
        'erp.locations.view',
        'erp.locations.manage',
        'erp.inventory.view',
        'erp.inventory.manage',
        'erp.sales.view',
        'erp.sales.create',
        'erp.sales.approve',
        'erp.purchases.view',
        'erp.purchases.create',
        'erp.purchases.approve',
    ],
    'default_currency' => env('ERP_DEFAULT_CURRENCY', 'PEN'),
];
