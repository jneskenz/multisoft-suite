<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Company;
use Modules\Core\Models\GroupCompany;
use Modules\Core\Models\Location;
use Modules\HR\Models\Empleado;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el primer grupo, empresa y local disponibles
        $group = GroupCompany::first();
        $company = Company::first();
        $location = Location::first();

        if (!$group || !$company) {
            $this->command->warn('No se encontraron grupos o empresas. Ejecuta primero los seeders del módulo Core.');
            return;
        }

        $empleados = [
            [
                'nombre' => 'Juan Carlos Pérez García',
                'email' => 'juan.perez@empresa.com',
                'documento_tipo' => 'DNI',
                'documento_numero' => '12345678',
                'telefono' => '+51 999 888 777',
                'codigo_empleado' => 'EMP-001',
                'cargo' => 'Gerente General',
                'fecha_ingreso' => '2024-01-15',
                'estado' => 1,
                'tenant_id' => $group->tenant_id,
                'group_company_id' => $group->id,
                'company_id' => $company->id,
                'location_id' => $location?->id,
            ],
            [
                'nombre' => 'María Elena Rodríguez López',
                'email' => 'maria.rodriguez@empresa.com',
                'documento_tipo' => 'DNI',
                'documento_numero' => '87654321',
                'telefono' => '+51 999 777 666',
                'codigo_empleado' => 'EMP-002',
                'cargo' => 'Gerente de Recursos Humanos',
                'fecha_ingreso' => '2024-02-01',
                'estado' => 1,
                'tenant_id' => $group->tenant_id,
                'group_company_id' => $group->id,
                'company_id' => $company->id,
                'location_id' => $location?->id,
            ],
            [
                'nombre' => 'Carlos Alberto Sánchez Díaz',
                'email' => 'carlos.sanchez@empresa.com',
                'documento_tipo' => 'DNI',
                'documento_numero' => '45678912',
                'telefono' => '+51 999 666 555',
                'codigo_empleado' => 'EMP-003',
                'cargo' => 'Analista de Sistemas',
                'fecha_ingreso' => '2024-03-10',
                'estado' => 1,
                'tenant_id' => $group->tenant_id,
                'group_company_id' => $group->id,
                'company_id' => $company->id,
                'location_id' => $location?->id,
            ],
            [
                'nombre' => 'Ana Lucía Torres Mendoza',
                'email' => 'ana.torres@empresa.com',
                'documento_tipo' => 'CE',
                'documento_numero' => '001234567',
                'telefono' => '+51 999 555 444',
                'codigo_empleado' => 'EMP-004',
                'cargo' => 'Asistente Administrativa',
                'fecha_ingreso' => '2024-04-05',
                'estado' => 0, // Suspendido
                'tenant_id' => $group->tenant_id,
                'group_company_id' => $group->id,
                'company_id' => $company->id,
                'location_id' => $location?->id,
            ],
            [
                'nombre' => 'Roberto Martínez Flores',
                'email' => null, // Sin email
                'documento_tipo' => 'DNI',
                'documento_numero' => '78945612',
                'telefono' => '+51 999 444 333',
                'codigo_empleado' => 'EMP-005',
                'cargo' => 'Operario de Producción',
                'fecha_ingreso' => '2023-06-15',
                'fecha_cese' => '2024-12-31',
                'estado' => 2, // Cesado
                'tenant_id' => $group->tenant_id,
                'group_company_id' => $group->id,
                'company_id' => $company->id,
                'location_id' => $location?->id,
            ],
        ];

        foreach ($empleados as $data) {
            Empleado::updateOrCreate(
                ['documento_numero' => $data['documento_numero']],
                $data
            );
        }

        $this->command->info('Empleados de ejemplo creados correctamente.');
    }
}
