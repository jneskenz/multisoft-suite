<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\HR\Models\Departamento;
use Modules\HR\Models\Empleado;
use Modules\HR\Models\TipoDepartamento;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = TipoDepartamento::query()
            ->get()
            ->keyBy('codigo');

        if ($tipos->isEmpty()) {
            $this->command?->warn('No hay tipos de departamento. Ejecuta primero TipoDepartamentoSeeder.');
            return;
        }

        $jefeGeneral = Empleado::query()->where('codigo_empleado', 'EMP-001')->first();
        $jefeRRHH = Empleado::query()->where('codigo_empleado', 'EMP-002')->first();

        $departamentos = [
            [
                'codigo' => 'GER',
                'name' => 'Gerencia General',
                'descripcion' => 'Direccion general de la organizacion',
                'estado' => 1,
                'tipo_codigo' => 'ADM',
                'padre_codigo' => null,
                'jefe_empleado_id' => $jefeGeneral?->id,
            ],
            [
                'codigo' => 'ADM',
                'name' => 'Administracion',
                'descripcion' => 'Gestion administrativa y servicios generales',
                'estado' => 1,
                'tipo_codigo' => 'ADM',
                'padre_codigo' => 'GER',
                'jefe_empleado_id' => null,
            ],
            [
                'codigo' => 'RRHH',
                'name' => 'Recursos Humanos',
                'descripcion' => 'Gestion del talento y relaciones laborales',
                'estado' => 1,
                'tipo_codigo' => 'ADM',
                'padre_codigo' => 'ADM',
                'jefe_empleado_id' => $jefeRRHH?->id,
            ],
            [
                'codigo' => 'FIN',
                'name' => 'Finanzas',
                'descripcion' => 'Control financiero y contable',
                'estado' => 1,
                'tipo_codigo' => 'FIN',
                'padre_codigo' => 'ADM',
                'jefe_empleado_id' => null,
            ],
            [
                'codigo' => 'COM',
                'name' => 'Comercial',
                'descripcion' => 'Ventas y relacion con clientes',
                'estado' => 1,
                'tipo_codigo' => 'COM',
                'padre_codigo' => 'GER',
                'jefe_empleado_id' => null,
            ],
            [
                'codigo' => 'OPE',
                'name' => 'Operaciones',
                'descripcion' => 'Ejecucion operativa del negocio',
                'estado' => 1,
                'tipo_codigo' => 'OPE',
                'padre_codigo' => 'GER',
                'jefe_empleado_id' => null,
            ],
            [
                'codigo' => 'TI',
                'name' => 'Tecnologia',
                'descripcion' => 'Sistemas, infraestructura y desarrollo',
                'estado' => 1,
                'tipo_codigo' => 'SOP',
                'padre_codigo' => 'ADM',
                'jefe_empleado_id' => null,
            ],
        ];

        $hasJefeColumn = Schema::hasColumn('hr_departamentos', 'jefe_id');

        // 1) Crear/actualizar base sin jerarquia.
        foreach ($departamentos as $item) {
            $payload = [
                'tipo_departamento_id' => $tipos[$item['tipo_codigo']]->id ?? null,
                'name' => $item['name'],
                'descripcion' => $item['descripcion'],
                'estado' => $item['estado'],
            ];

            if ($hasJefeColumn) {
                $payload['jefe_id'] = $item['jefe_empleado_id'];
            }

            Departamento::updateOrCreate(
                ['codigo' => $item['codigo']],
                $payload
            );
        }

        // 2) Actualizar relaciones padre-hijo.
        foreach ($departamentos as $item) {
            if (empty($item['padre_codigo'])) {
                continue;
            }

            $departamento = Departamento::query()->where('codigo', $item['codigo'])->first();
            $padre = Departamento::query()->where('codigo', $item['padre_codigo'])->first();

            if ($departamento && $padre) {
                $departamento->update(['padre_id' => $padre->id]);
            }
        }

        $this->command?->info('Departamentos creados/actualizados correctamente.');
    }
}
