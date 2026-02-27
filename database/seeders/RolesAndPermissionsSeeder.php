<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Permission;
use Modules\Core\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $this->createPermissions();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear roles
        $this->createRoles();
    }

    /**
     * Crear todos los permisos del sistema
     */
    protected function createPermissions(): void
    {
        $permissions = [
            // Core Module
            ['name' => 'access.core', 'display_name' => 'Acceso a Core', 'module' => 'core', 'description' => 'Acceso al módulo Core'],
            ['name' => 'core.users.view', 'display_name' => 'Ver Usuarios', 'module' => 'core', 'description' => 'Ver listado de usuarios'],
            ['name' => 'core.users.create', 'display_name' => 'Crear Usuarios', 'module' => 'core', 'description' => 'Crear nuevos usuarios'],
            ['name' => 'core.users.edit', 'display_name' => 'Editar Usuarios', 'module' => 'core', 'description' => 'Editar usuarios existentes'],
            ['name' => 'core.users.delete', 'display_name' => 'Eliminar Usuarios', 'module' => 'core', 'description' => 'Eliminar usuarios'],
            ['name' => 'core.roles.view', 'display_name' => 'Ver Roles', 'module' => 'core', 'description' => 'Ver listado de roles'],
            ['name' => 'core.roles.create', 'display_name' => 'Crear Roles', 'module' => 'core', 'description' => 'Crear nuevos roles'],
            ['name' => 'core.roles.edit', 'display_name' => 'Editar Roles', 'module' => 'core', 'description' => 'Editar roles existentes'],
            ['name' => 'core.roles.delete', 'display_name' => 'Eliminar Roles', 'module' => 'core', 'description' => 'Eliminar roles'],
            ['name' => 'core.settings.view', 'display_name' => 'Ver Configuración', 'module' => 'core', 'description' => 'Ver configuración del sistema'],
            ['name' => 'core.settings.edit', 'display_name' => 'Editar Configuración', 'module' => 'core', 'description' => 'Modificar configuración del sistema'],
            ['name' => 'core.companies.view', 'display_name' => 'Ver Empresas', 'module' => 'core', 'description' => 'Ver listado de empresas'],
            ['name' => 'core.companies.create', 'display_name' => 'Crear Empresas', 'module' => 'core', 'description' => 'Crear nuevas empresas'],
            ['name' => 'core.companies.edit', 'display_name' => 'Editar Empresas', 'module' => 'core', 'description' => 'Editar empresas existentes'],
            ['name' => 'core.companies.delete', 'display_name' => 'Eliminar Empresas', 'module' => 'core', 'description' => 'Eliminar empresas'],
            ['name' => 'core.locations.view', 'display_name' => 'Ver Locales', 'module' => 'core', 'description' => 'Ver listado de locales'],
            ['name' => 'core.locations.create', 'display_name' => 'Crear Locales', 'module' => 'core', 'description' => 'Crear nuevos locales'],
            ['name' => 'core.locations.edit', 'display_name' => 'Editar Locales', 'module' => 'core', 'description' => 'Editar locales existentes'],
            ['name' => 'core.locations.delete', 'display_name' => 'Eliminar Locales', 'module' => 'core', 'description' => 'Eliminar locales'],

            // Partners Module
            ['name' => 'access.partners', 'display_name' => 'Acceso a Partners', 'module' => 'partners', 'description' => 'Acceso al módulo Partners'],
            ['name' => 'partners.view', 'display_name' => 'Ver Partners', 'module' => 'partners', 'description' => 'Ver dashboard de partners'],
            ['name' => 'partners.create', 'display_name' => 'Crear Partners', 'module' => 'partners', 'description' => 'Crear partners'],
            ['name' => 'partners.edit', 'display_name' => 'Editar Partners', 'module' => 'partners', 'description' => 'Editar partners'],
            ['name' => 'partners.delete', 'display_name' => 'Eliminar Partners', 'module' => 'partners', 'description' => 'Eliminar partners'],
            ['name' => 'partners.export', 'display_name' => 'Exportar Partners', 'module' => 'partners', 'description' => 'Exportar partners'],
            ['name' => 'partners.import', 'display_name' => 'Importar Partners', 'module' => 'partners', 'description' => 'Importar partners'],
            ['name' => 'partners.customers.view', 'display_name' => 'Ver Clientes', 'module' => 'partners', 'description' => 'Ver clientes'],
            ['name' => 'partners.suppliers.view', 'display_name' => 'Ver Proveedores', 'module' => 'partners', 'description' => 'Ver proveedores'],
            ['name' => 'partners.contacts.view', 'display_name' => 'Ver Contactos', 'module' => 'partners', 'description' => 'Ver contactos'],
            ['name' => 'partners.personas.view', 'display_name' => 'Ver Personas', 'module' => 'partners', 'description' => 'Ver personas'],
            ['name' => 'partners.empresas.view', 'display_name' => 'Ver Empresas Partners', 'module' => 'partners', 'description' => 'Ver empresas de partners'],
            ['name' => 'partners.relaciones.view', 'display_name' => 'Ver Relaciones Persona Empresa', 'module' => 'partners', 'description' => 'Ver relaciones entre personas y empresas'],
            ['name' => 'partners.clientes.view', 'display_name' => 'Ver Clientes (Personas)', 'module' => 'partners', 'description' => 'Ver personas con tipo cliente'],
            ['name' => 'partners.proveedores.view', 'display_name' => 'Ver Proveedores (Personas)', 'module' => 'partners', 'description' => 'Ver personas con tipo proveedor'],
            ['name' => 'partners.pacientes.view', 'display_name' => 'Ver Pacientes (Personas)', 'module' => 'partners', 'description' => 'Ver personas con tipo paciente'],
            // ERP Module
            ['name' => 'access.erp', 'display_name' => 'Acceso a ERP', 'module' => 'erp', 'description' => 'Acceso al módulo ERP'],
            ['name' => 'erp.inventory.view', 'display_name' => 'Ver Inventario', 'module' => 'erp', 'description' => 'Ver inventario'],
            ['name' => 'erp.inventory.create', 'display_name' => 'Crear Inventario', 'module' => 'erp', 'description' => 'Crear registros de inventario'],
            ['name' => 'erp.inventory.edit', 'display_name' => 'Editar Inventario', 'module' => 'erp', 'description' => 'Editar inventario'],
            ['name' => 'erp.inventory.delete', 'display_name' => 'Eliminar Inventario', 'module' => 'erp', 'description' => 'Eliminar del inventario'],
            ['name' => 'erp.sales.view', 'display_name' => 'Ver Ventas', 'module' => 'erp', 'description' => 'Ver ventas'],
            ['name' => 'erp.sales.create', 'display_name' => 'Crear Ventas', 'module' => 'erp', 'description' => 'Crear ventas'],
            ['name' => 'erp.sales.edit', 'display_name' => 'Editar Ventas', 'module' => 'erp', 'description' => 'Editar ventas'],
            ['name' => 'erp.sales.delete', 'display_name' => 'Eliminar Ventas', 'module' => 'erp', 'description' => 'Eliminar ventas'],
            ['name' => 'erp.purchases.view', 'display_name' => 'Ver Compras', 'module' => 'erp', 'description' => 'Ver compras'],
            ['name' => 'erp.purchases.create', 'display_name' => 'Crear Compras', 'module' => 'erp', 'description' => 'Crear compras'],
            ['name' => 'erp.purchases.edit', 'display_name' => 'Editar Compras', 'module' => 'erp', 'description' => 'Editar compras'],
            ['name' => 'erp.purchases.delete', 'display_name' => 'Eliminar Compras', 'module' => 'erp', 'description' => 'Eliminar compras'],
            ['name' => 'erp.catalogos.view', 'display_name' => 'Ver catalogos', 'module' => 'erp', 'description' => 'Ver catalogos'],
            ['name' => 'erp.catalogos.create', 'display_name' => 'Crear catalogos', 'module' => 'erp', 'description' => 'Crear registros de catalogos'],
            ['name' => 'erp.catalogos.edit', 'display_name' => 'Editar catalogos', 'module' => 'erp', 'description' => 'Editar catalogos'],
            ['name' => 'erp.catalogos.delete', 'display_name' => 'Eliminar catalogos', 'module' => 'erp', 'description' => 'Eliminar del catalogos'],
            
            // HR Module
            ['name' => 'access.hr', 'display_name' => 'Acceso a RRHH', 'module' => 'hr', 'description' => 'Acceso al módulo RRHH'],
            ['name' => 'hr.employees.view', 'display_name' => 'Ver Empleados', 'module' => 'hr', 'description' => 'Ver empleados'],
            ['name' => 'hr.employees.create', 'display_name' => 'Crear Empleados', 'module' => 'hr', 'description' => 'Crear empleados'],
            ['name' => 'hr.employees.edit', 'display_name' => 'Editar Empleados', 'module' => 'hr', 'description' => 'Editar empleados'],
            ['name' => 'hr.employees.delete', 'display_name' => 'Eliminar Empleados', 'module' => 'hr', 'description' => 'Eliminar empleados'],
            ['name' => 'hr.empleados.view', 'display_name' => 'Ver Empleados', 'module' => 'hr', 'description' => 'Ver empleados'],
            ['name' => 'hr.empleados.create', 'display_name' => 'Crear Empleados', 'module' => 'hr', 'description' => 'Crear empleados'],
            ['name' => 'hr.empleados.edit', 'display_name' => 'Editar Empleados', 'module' => 'hr', 'description' => 'Editar empleados'],
            ['name' => 'hr.empleados.delete', 'display_name' => 'Eliminar Empleados', 'module' => 'hr', 'description' => 'Eliminar empleados'],
            ['name' => 'hr.attendance.view', 'display_name' => 'Ver Asistencia', 'module' => 'hr', 'description' => 'Ver asistencia'],
            ['name' => 'hr.attendance.create', 'display_name' => 'Registrar Asistencia', 'module' => 'hr', 'description' => 'Registrar asistencia'],
            ['name' => 'hr.attendance.edit', 'display_name' => 'Editar Asistencia', 'module' => 'hr', 'description' => 'Editar asistencia'],
            ['name' => 'hr.payroll.view', 'display_name' => 'Ver Planilla', 'module' => 'hr', 'description' => 'Ver planilla'],
            ['name' => 'hr.payroll.create', 'display_name' => 'Crear Planilla', 'module' => 'hr', 'description' => 'Crear planilla'],
            ['name' => 'hr.payroll.process', 'display_name' => 'Procesar Planilla', 'module' => 'hr', 'description' => 'Procesar planilla'],

            // CRM Module
            ['name' => 'access.crm', 'display_name' => 'Acceso a CRM', 'module' => 'crm', 'description' => 'Acceso al módulo CRM'],
            ['name' => 'crm.leads.view', 'display_name' => 'Ver Leads', 'module' => 'crm', 'description' => 'Ver leads'],
            ['name' => 'crm.leads.create', 'display_name' => 'Crear Leads', 'module' => 'crm', 'description' => 'Crear leads'],
            ['name' => 'crm.leads.edit', 'display_name' => 'Editar Leads', 'module' => 'crm', 'description' => 'Editar leads'],
            ['name' => 'crm.leads.delete', 'display_name' => 'Eliminar Leads', 'module' => 'crm', 'description' => 'Eliminar leads'],
            ['name' => 'crm.opportunities.view', 'display_name' => 'Ver Oportunidades', 'module' => 'crm', 'description' => 'Ver oportunidades'],
            ['name' => 'crm.opportunities.create', 'display_name' => 'Crear Oportunidades', 'module' => 'crm', 'description' => 'Crear oportunidades'],
            ['name' => 'crm.opportunities.edit', 'display_name' => 'Editar Oportunidades', 'module' => 'crm', 'description' => 'Editar oportunidades'],
            ['name' => 'crm.opportunities.delete', 'display_name' => 'Eliminar Oportunidades', 'module' => 'crm', 'description' => 'Eliminar oportunidades'],
            ['name' => 'crm.activities.view', 'display_name' => 'Ver Actividades', 'module' => 'crm', 'description' => 'Ver actividades'],
            ['name' => 'crm.activities.create', 'display_name' => 'Crear Actividades', 'module' => 'crm', 'description' => 'Crear actividades'],
            ['name' => 'crm.activities.edit', 'display_name' => 'Editar Actividades', 'module' => 'crm', 'description' => 'Editar actividades'],

            // FMS Module
            ['name' => 'access.fms', 'display_name' => 'Acceso a FMS', 'module' => 'fms', 'description' => 'Acceso al módulo FMS'],
            ['name' => 'fms.accounts.view', 'display_name' => 'Ver Cuentas', 'module' => 'fms', 'description' => 'Ver plan de cuentas'],
            ['name' => 'fms.accounts.create', 'display_name' => 'Crear Cuentas', 'module' => 'fms', 'description' => 'Crear cuentas'],
            ['name' => 'fms.accounts.edit', 'display_name' => 'Editar Cuentas', 'module' => 'fms', 'description' => 'Editar cuentas'],
            ['name' => 'fms.accounts.delete', 'display_name' => 'Eliminar Cuentas', 'module' => 'fms', 'description' => 'Eliminar cuentas'],
            ['name' => 'fms.entries.view', 'display_name' => 'Ver Asientos', 'module' => 'fms', 'description' => 'Ver asientos contables'],
            ['name' => 'fms.entries.create', 'display_name' => 'Crear Asientos', 'module' => 'fms', 'description' => 'Crear asientos'],
            ['name' => 'fms.entries.edit', 'display_name' => 'Editar Asientos', 'module' => 'fms', 'description' => 'Editar asientos'],
            ['name' => 'fms.entries.delete', 'display_name' => 'Eliminar Asientos', 'module' => 'fms', 'description' => 'Eliminar asientos'],
            ['name' => 'fms.reports.view', 'display_name' => 'Ver Reportes FMS', 'module' => 'fms', 'description' => 'Ver reportes financieros'],
            ['name' => 'fms.reports.generate', 'display_name' => 'Generar Reportes FMS', 'module' => 'fms', 'description' => 'Generar reportes financieros'],

            // Reports Module
            ['name' => 'access.reports', 'display_name' => 'Acceso a Reportes', 'module' => 'reports', 'description' => 'Acceso al módulo Reportes'],
            ['name' => 'reports.view', 'display_name' => 'Ver Reportes', 'module' => 'reports', 'description' => 'Ver reportes'],
            ['name' => 'reports.generate', 'display_name' => 'Generar Reportes', 'module' => 'reports', 'description' => 'Generar reportes'],
            ['name' => 'reports.export', 'display_name' => 'Exportar Reportes', 'module' => 'reports', 'description' => 'Exportar reportes'],
            ['name' => 'reports.schedule', 'display_name' => 'Programar Reportes', 'module' => 'reports', 'description' => 'Programar reportes'],
            ['name' => 'reports.templates.view', 'display_name' => 'Ver Plantillas', 'module' => 'reports', 'description' => 'Ver plantillas de reportes'],
            ['name' => 'reports.templates.create', 'display_name' => 'Crear Plantillas', 'module' => 'reports', 'description' => 'Crear plantillas'],
            ['name' => 'reports.templates.edit', 'display_name' => 'Editar Plantillas', 'module' => 'reports', 'description' => 'Editar plantillas'],
            ['name' => 'reports.templates.delete', 'display_name' => 'Eliminar Plantillas', 'module' => 'reports', 'description' => 'Eliminar plantillas'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }

    /**
     * Crear roles predeterminados
     */
    protected function createRoles(): void
    {
        // Super Admin - Tiene todos los permisos (manejado por Gate::before)
        $superAdmin = Role::updateOrCreate(
            ['name' => 'superadmin', 'guard_name' => 'web'],
            [
                'display_name' => 'Super Administrador',
                'description' => 'Acceso total al sistema',
                'is_system' => true,
            ]
        );

        // Admin - Tiene acceso a todos los módulos
        $admin = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            [
                'display_name' => 'Administrador',
                'description' => 'Administrador del sistema',
                'is_system' => true,
            ]
        );
        $admin->syncPermissions(Permission::all());

        // Manager - Acceso a módulos operativos
        $manager = Role::updateOrCreate(
            ['name' => 'manager', 'guard_name' => 'web'],
            [
                'display_name' => 'Gerente',
                'description' => 'Gerente con acceso a múltiples módulos',
                'is_system' => false,
            ]
        );
        $manager->syncPermissions($this->resolvePermissions([
            'access.partners',
            'partners.view',
            'partners.customers.view',
            'partners.suppliers.view',
            'partners.contacts.view',
            'partners.personas.view',
            'partners.empresas.view',
            'partners.relaciones.view',
            'partners.clientes.view',
            'partners.proveedores.view',
            'partners.pacientes.view',
            'access.erp',
            'erp.inventory.view',
            'erp.sales.view',
            'erp.sales.create',
            'erp.purchases.view',
            'erp.purchases.create',
            'access.hr',
            'hr.employees.view',
            'hr.empleados.view',
            'hr.attendance.view',
            'hr.payroll.view',
            'access.crm',
            'crm.leads.view',
            'crm.leads.create',
            'crm.opportunities.view',
            'crm.opportunities.create',
            'crm.activities.view',
            'crm.activities.create',
            'access.fms',
            'fms.accounts.view',
            'fms.entries.view',
            'fms.reports.view',
            'access.reports',
            'reports.view',
            'reports.generate',
            'reports.export',
        ]));

        // Contador - Acceso a FMS y reportes
        $accountant = Role::updateOrCreate(
            ['name' => 'accountant', 'guard_name' => 'web'],
            [
                'display_name' => 'Contador',
                'description' => 'Contador con acceso a módulo financiero',
                'is_system' => false,
            ]
        );
        $accountant->syncPermissions($this->resolvePermissions([
            'access.fms',
            'fms.accounts.view',
            'fms.accounts.create',
            'fms.accounts.edit',
            'fms.entries.view',
            'fms.entries.create',
            'fms.entries.edit',
            'fms.reports.view',
            'fms.reports.generate',
            'access.reports',
            'reports.view',
            'reports.generate',
            'reports.export',
        ]));

        // Vendedor - Acceso a CRM y ventas ERP
        $salesperson = Role::updateOrCreate(
            ['name' => 'salesperson', 'guard_name' => 'web'],
            [
                'display_name' => 'Vendedor',
                'description' => 'Vendedor con acceso a CRM y ventas',
                'is_system' => false,
            ]
        );
        $salesperson->syncPermissions($this->resolvePermissions([
            'access.erp',
            'erp.sales.view',
            'erp.sales.create',
            'erp.inventory.view',
            'access.crm',
            'crm.leads.view',
            'crm.leads.create',
            'crm.leads.edit',
            'crm.opportunities.view',
            'crm.opportunities.create',
            'crm.opportunities.edit',
            'crm.activities.view',
            'crm.activities.create',
            'crm.activities.edit',
        ]));

        // RRHH - Acceso a módulo de recursos humanos
        $hrManager = Role::updateOrCreate(
            ['name' => 'hr-manager', 'guard_name' => 'web'],
            [
                'display_name' => 'Gestor RRHH',
                'description' => 'Gestor de recursos humanos',
                'is_system' => false,
            ]
        );
        $hrManager->syncPermissions($this->resolvePermissions([
            'access.hr',
            'hr.employees.view',
            'hr.employees.create',
            'hr.employees.edit',
            'hr.empleados.view',
            'hr.empleados.create',
            'hr.empleados.edit',
            'hr.attendance.view',
            'hr.attendance.create',
            'hr.attendance.edit',
            'hr.payroll.view',
            'hr.payroll.create',
            'hr.payroll.process',
            'access.reports',
            'reports.view',
            'reports.generate',
        ]));

        // Usuario básico - Solo lectura
        $user = Role::updateOrCreate(
            ['name' => 'user', 'guard_name' => 'web'],
            [
                'display_name' => 'Usuario',
                'description' => 'Usuario con acceso limitado',
                'is_system' => true,
            ]
        );
        $user->syncPermissions($this->resolvePermissions([
            'access.erp',
            'erp.inventory.view',
            'erp.sales.view',
        ]));
    }

    /**
     * Obtener modelos de permisos existentes por nombre.
     */
    protected function resolvePermissions(array $names)
    {
        return Permission::query()
            ->whereIn('name', $names)
            ->get();
    }
}

