<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerGates();
    }

    /**
     * Registrar los Gates de la aplicación
     */
    protected function registerGates(): void
    {
        // Gate para super admin (tiene acceso a todo)
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // Gates de acceso a módulos
        $modules = ['core', 'partners', 'erp', 'hr', 'crm', 'fms', 'reports'];

        foreach ($modules as $module) {
            Gate::define("access.{$module}", function (User $user) use ($module) {
                return $user->hasModuleAccess($module);
            });
        }

        // Gates de permisos específicos por módulo
        $this->registerCoreGates();
        $this->registerPartnersGates();
        $this->registerErpGates();
        $this->registerHrGates();
        $this->registerCrmGates();
        $this->registerFmsGates();
        $this->registerReportsGates();
    }

    /**
     * Gates del mÃ³dulo Partners
     */
    protected function registerPartnersGates(): void
    {
        $permissions = [
            'partners.view',
            'partners.create',
            'partners.edit',
            'partners.delete',
            'partners.export',
            'partners.import',
            'partners.personas.view',
            'partners.empresas.view',
            'partners.relaciones.view',
            'partners.clientes.view',
            'partners.proveedores.view',
            'partners.pacientes.view',
            // Compatibilidad con nomenclatura inicial
            'partners.customers.view',
            'partners.suppliers.view',
            'partners.contacts.view',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
    }

    /**
     * Gates del módulo Core
     */
    protected function registerCoreGates(): void
    {
        $permissions = [
            'core.users.view',
            'core.users.create',
            'core.users.edit',
            'core.users.delete',
            'core.roles.view',
            'core.roles.create',
            'core.roles.edit',
            'core.roles.delete',
            'core.settings.view',
            'core.settings.edit',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
    }

    /**
     * Gates del módulo ERP
     */
    protected function registerErpGates(): void
    {
        $permissions = [
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
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
    }

    /**
     * Gates del módulo HR
     */
    protected function registerHrGates(): void
    {
        $permissions = [
            'hr.employees.view',
            'hr.employees.create',
            'hr.employees.edit',
            'hr.employees.delete',
            'hr.attendance.view',
            'hr.attendance.create',
            'hr.attendance.edit',
            'hr.payroll.view',
            'hr.payroll.create',
            'hr.payroll.process',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
    }

    /**
     * Gates del módulo CRM
     */
    protected function registerCrmGates(): void
    {
        $permissions = [
            'crm.leads.view',
            'crm.leads.create',
            'crm.leads.edit',
            'crm.leads.delete',
            'crm.opportunities.view',
            'crm.opportunities.create',
            'crm.opportunities.edit',
            'crm.opportunities.delete',
            'crm.activities.view',
            'crm.activities.create',
            'crm.activities.edit',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
    }

    /**
     * Gates del módulo FMS
     */
    protected function registerFmsGates(): void
    {
        $permissions = [
            'fms.accounts.view',
            'fms.accounts.create',
            'fms.accounts.edit',
            'fms.accounts.delete',
            'fms.entries.view',
            'fms.entries.create',
            'fms.entries.edit',
            'fms.entries.delete',
            'fms.reports.view',
            'fms.reports.generate',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
    }

    /**
     * Gates del módulo Reports
     */
    protected function registerReportsGates(): void
    {
        $permissions = [
            'reports.view',
            'reports.generate',
            'reports.export',
            'reports.schedule',
            'reports.templates.view',
            'reports.templates.create',
            'reports.templates.edit',
            'reports.templates.delete',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
    }
}
