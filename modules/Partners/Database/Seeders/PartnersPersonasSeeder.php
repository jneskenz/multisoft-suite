<?php

namespace Modules\Partners\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\GroupCompany;
use Modules\Partners\Models\Persona;

class PartnersPersonasSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('partners_personas') || !Schema::hasTable('core_group_companies')) {
            $this->command?->warn('Tabla partners_personas no encontrada. Ejecuta migraciones primero.');

            return;
        }

        $groups = GroupCompany::query()
            ->select(['id', 'tenant_id', 'code'])
            ->whereIn('code', ['PE', 'EC'])
            ->get()
            ->keyBy('code');

        if ($groups->isEmpty()) {
            $this->command?->warn('No hay grupos base (PE/EC). Ejecuta seeders de Core primero.');

            return;
        }

        $userId = Schema::hasTable('users')
            ? (User::query()->where('email', 'admin@multisoft.test')->value('id') ?? User::query()->value('id'))
            : null;

        $rows = [
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '72345678',
                'nombres' => 'Juan',
                'apellido_paterno' => 'Perez',
                'apellido_materno' => 'Gomez',
                'email' => 'juan.perez@example.com',
                'telefono' => '+51 987654321',
                'fecha_nacimiento' => '1990-04-15',
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '45678912',
                'nombres' => 'Maria',
                'apellido_paterno' => 'Rojas',
                'apellido_materno' => 'Vega',
                'email' => 'maria.rojas@example.com',
                'telefono' => '+51 986111222',
                'fecha_nacimiento' => '1992-09-08',
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '47896521',
                'nombres' => 'Carlos',
                'apellido_paterno' => 'Soto',
                'apellido_materno' => 'Diaz',
                'email' => 'carlos.soto@example.com',
                'telefono' => '+51 985222333',
                'fecha_nacimiento' => '1987-12-03',
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'tipo_documento' => 'DNI',
                'numero_documento' => '49876543',
                'nombres' => 'Ana',
                'apellido_paterno' => 'Ruiz',
                'apellido_materno' => 'Campos',
                'email' => 'ana.ruiz@example.com',
                'telefono' => '+51 984333444',
                'fecha_nacimiento' => '1989-01-20',
                'estado' => true,
            ],
            [
                'group_code' => 'EC',
                'tipo_documento' => 'CEDULA',
                'numero_documento' => '0912345678',
                'nombres' => 'Diego',
                'apellido_paterno' => 'Mendoza',
                'apellido_materno' => 'Lopez',
                'email' => 'diego.mendoza@example.com',
                'telefono' => '+593 999111222',
                'fecha_nacimiento' => '1991-06-11',
                'estado' => true,
            ],
            [
                'group_code' => 'EC',
                'tipo_documento' => 'CEDULA',
                'numero_documento' => '0923456789',
                'nombres' => 'Laura',
                'apellido_paterno' => 'Paredes',
                'apellido_materno' => 'Mora',
                'email' => 'laura.paredes@example.com',
                'telefono' => '+593 999333444',
                'fecha_nacimiento' => '1994-02-26',
                'estado' => true,
            ],
        ];

        foreach ($rows as $row) {
            $group = $groups->get($row['group_code']);
            if (!$group) {
                continue;
            }

            $persona = Persona::withTrashed()->firstOrNew([
                'tenant_id' => (int) $group->tenant_id,
                'tipo_documento' => $row['tipo_documento'],
                'numero_documento' => $row['numero_documento'],
            ]);

            $persona->group_company_id = (int) $group->id;
            $persona->nombres = $row['nombres'];
            $persona->apellido_paterno = $row['apellido_paterno'];
            $persona->apellido_materno = $row['apellido_materno'];
            $persona->nombre_completo = trim(implode(' ', array_filter([
                $row['nombres'],
                $row['apellido_paterno'],
                $row['apellido_materno'],
            ])));
            $persona->email = $row['email'];
            $persona->telefono = $row['telefono'];
            $persona->fecha_nacimiento = $row['fecha_nacimiento'];
            $persona->estado = (bool) $row['estado'];
            $persona->created_by = $persona->exists ? $persona->created_by : $userId;
            $persona->updated_by = $userId;
            $persona->deleted_at = null;
            $persona->save();
        }

        $this->command?->info('Partners personas creadas/actualizadas correctamente.');
    }
}
