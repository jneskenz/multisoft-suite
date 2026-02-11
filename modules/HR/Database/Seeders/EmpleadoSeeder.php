<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\HR\Models\Empleado;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = [
            [
                'nombre' => 'Empleado Demo',
                'email' => 'empleado@demo.com',
                'password' => Hash::make('password123'),
                'estado' => 1,
                'tenant_id' => 1,
            ],
            [
                'nombre' => 'Ana Pérez',
                'email' => 'ana.perez@empresa.com',
                'password' => Hash::make('password123'),
                'estado' => 1,
                'tenant_id' => 1,
            ],
        ];

        foreach ($empleados as $data) {
            Empleado::updateOrCreate([
                'email' => $data['email']
            ], $data);
        }
    }
}
