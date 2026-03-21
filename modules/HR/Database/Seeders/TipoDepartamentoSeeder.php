<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Models\TipoDepartamento;

class TipoDepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'codigo' => 'ADM',
                'nombre' => 'Administrativo',
                'descripcion' => 'Departamentos de gestion administrativa',
                'estado' => 1,
            ],
            [
                'codigo' => 'OPE',
                'nombre' => 'Operativo',
                'descripcion' => 'Departamentos orientados a la operacion',
                'estado' => 1,
            ],
            [
                'codigo' => 'COM',
                'nombre' => 'Comercial',
                'descripcion' => 'Departamentos de ventas y marketing',
                'estado' => 1,
            ],
            [
                'codigo' => 'FIN',
                'nombre' => 'Financiero',
                'descripcion' => 'Departamentos contables y de finanzas',
                'estado' => 1,
            ],
            [
                'codigo' => 'SOP',
                'nombre' => 'Soporte',
                'descripcion' => 'Departamentos de soporte interno y externo',
                'estado' => 1,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoDepartamento::updateOrCreate(
                ['codigo' => $tipo['codigo']],
                $tipo
            );
        }

        $this->command?->info('Tipos de departamento creados/actualizados correctamente.');
    }
}
