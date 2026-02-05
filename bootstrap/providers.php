<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    Modules\CRM\Providers\CRMServiceProvider::class,
    Modules\Core\Providers\CoreServiceProvider::class,
    Modules\ERP\Providers\ERPServiceProvider::class,
    Modules\FMS\Providers\FMSServiceProvider::class,
    Modules\HR\Providers\HRServiceProvider::class,
    Modules\Partners\Providers\PartnersServiceProvider::class,
    Modules\Reports\Providers\ReportsServiceProvider::class,
];
