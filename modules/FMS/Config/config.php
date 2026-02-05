<?php

return [
    'name' => 'FMS',
    'permissions' => [
        'access.fms',
        'fms.accounts.view',
        'fms.accounts.create',
        'fms.accounts.edit',
        'fms.entries.view',
        'fms.entries.create',
        'fms.entries.approve',
        'fms.entries.cancel',
        'fms.reports.view',
        'fms.reports.export',
        'fms.close_period',
    ],
    'fiscal_year_start' => env('FMS_FISCAL_YEAR_START', '01-01'),
];
