<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\HR\Models\Departamento;

class CargoSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = Departamento::query()
            ->get(['id', 'codigo'])
            ->keyBy('codigo');

        if ($departamentos->isEmpty()) {
            $this->command?->warn('No hay departamentos. Ejecuta primero DepartamentoSeeder.');
            return;
        }

        $cargos = [
            ['departamento_codigo' => 'GER', 'codigo' => 'GER-GEN', 'name' => 'Gerente General', 'descripcion' => 'Responsable de la direccion general', 'nivel' => 'Gerencial', 'estado' => 1],

            ['departamento_codigo' => 'ADM', 'codigo' => 'ADM-JEF', 'name' => 'Jefe Administrativo', 'descripcion' => 'Lidera la gestion administrativa', 'nivel' => 'Jefatura', 'estado' => 1],
            ['departamento_codigo' => 'ADM', 'codigo' => 'ADM-ANA', 'name' => 'Analista Administrativo', 'descripcion' => 'Soporte analitico y documental', 'nivel' => 'Analista', 'estado' => 1],

            ['departamento_codigo' => 'RRHH', 'codigo' => 'RRH-JEF', 'name' => 'Jefe de Recursos Humanos', 'descripcion' => 'Gestiona el area de talento humano', 'nivel' => 'Jefatura', 'estado' => 1],
            ['departamento_codigo' => 'RRHH', 'codigo' => 'RRH-ANA', 'name' => 'Analista de RRHH', 'descripcion' => 'Administra procesos de RRHH', 'nivel' => 'Analista', 'estado' => 1],
            ['departamento_codigo' => 'RRHH', 'codigo' => 'RRH-ASI', 'name' => 'Asistente de RRHH', 'descripcion' => 'Soporte operativo de RRHH', 'nivel' => 'Asistente', 'estado' => 1],

            ['departamento_codigo' => 'FIN', 'codigo' => 'FIN-JEF', 'name' => 'Jefe de Finanzas', 'descripcion' => 'Responsable financiero del negocio', 'nivel' => 'Jefatura', 'estado' => 1],
            ['departamento_codigo' => 'FIN', 'codigo' => 'FIN-CON', 'name' => 'Contador', 'descripcion' => 'Gestion contable y tributaria', 'nivel' => 'Analista', 'estado' => 1],
            ['departamento_codigo' => 'FIN', 'codigo' => 'FIN-TES', 'name' => 'Tesorero', 'descripcion' => 'Gestion de tesoreria y pagos', 'nivel' => 'Asistente', 'estado' => 1],

            ['departamento_codigo' => 'COM', 'codigo' => 'COM-JEF', 'name' => 'Jefe Comercial', 'descripcion' => 'Lidera ventas y estrategia comercial', 'nivel' => 'Jefatura', 'estado' => 1],
            ['departamento_codigo' => 'COM', 'codigo' => 'COM-EJE', 'name' => 'Ejecutivo de Ventas', 'descripcion' => 'Gestion de clientes y cierre comercial', 'nivel' => 'Operativo', 'estado' => 1],

            ['departamento_codigo' => 'OPE', 'codigo' => 'OPE-JEF', 'name' => 'Jefe de Operaciones', 'descripcion' => 'Coordina la operacion del negocio', 'nivel' => 'Jefatura', 'estado' => 1],
            ['departamento_codigo' => 'OPE', 'codigo' => 'OPE-SUP', 'name' => 'Supervisor de Operaciones', 'descripcion' => 'Supervisa la ejecucion operativa', 'nivel' => 'Analista', 'estado' => 1],
            ['departamento_codigo' => 'OPE', 'codigo' => 'OPE-OPE', 'name' => 'Operador', 'descripcion' => 'Ejecuta tareas operativas del area', 'nivel' => 'Operativo', 'estado' => 1],

            ['departamento_codigo' => 'TI', 'codigo' => 'TI-JEF', 'name' => 'Jefe de Tecnologia', 'descripcion' => 'Lider tecnico del area de TI', 'nivel' => 'Jefatura', 'estado' => 1],
            ['departamento_codigo' => 'TI', 'codigo' => 'TI-DEV', 'name' => 'Desarrollador', 'descripcion' => 'Desarrollo y mantenimiento de sistemas', 'nivel' => 'Analista', 'estado' => 1],
            ['departamento_codigo' => 'TI', 'codigo' => 'TI-SOP', 'name' => 'Soporte TI', 'descripcion' => 'Soporte tecnico a usuarios', 'nivel' => 'Asistente', 'estado' => 1],
        ];

        $insertados = 0;
        $omitidos = 0;

        foreach ($cargos as $item) {
            $departamento = $departamentos->get($item['departamento_codigo']);

            if (!$departamento) {
                $omitidos++;
                continue;
            }

            DB::table('hr_cargos')->updateOrInsert(
                [
                    'departamento_id' => $departamento->id,
                    'codigo' => $item['codigo'],
                ],
                [
                    'name' => $item['name'],
                    'descripcion' => $item['descripcion'],
                    'nivel' => $item['nivel'],
                    'estado' => $item['estado'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            $insertados++;
        }

        $this->command?->info("Cargos creados/actualizados correctamente. Procesados: {$insertados}, omitidos: {$omitidos}.");
    }
}
