<?php

namespace Modules\Partners\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\GroupCompany;
use Modules\Partners\Models\Persona;
use Modules\Partners\Models\TipoPersona;

class PartnersTipoPersonasSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('partners_tipo_personas') || !Schema::hasTable('partners_personas')) {
            $this->command?->warn('Tablas partners_tipo_personas/partners_personas no encontradas.');

            return;
        }

        if (!Schema::hasTable('core_group_companies')) {
            $this->command?->warn('Tabla core_group_companies no encontrada. Ejecuta migraciones de Core.');

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
            ['group_code' => 'PE', 'tipo_documento' => 'DNI', 'numero_documento' => '72345678', 'tipos' => ['cliente', 'paciente']],
            ['group_code' => 'PE', 'tipo_documento' => 'DNI', 'numero_documento' => '45678912', 'tipos' => ['cliente']],
            ['group_code' => 'PE', 'tipo_documento' => 'DNI', 'numero_documento' => '47896521', 'tipos' => ['proveedor']],
            ['group_code' => 'PE', 'tipo_documento' => 'DNI', 'numero_documento' => '49876543', 'tipos' => ['cliente', 'proveedor']],
            ['group_code' => 'EC', 'tipo_documento' => 'CEDULA', 'numero_documento' => '0912345678', 'tipos' => ['cliente']],
            ['group_code' => 'EC', 'tipo_documento' => 'CEDULA', 'numero_documento' => '0923456789', 'tipos' => ['proveedor', 'paciente']],
        ];

        foreach ($rows as $row) {
            $group = $groups->get($row['group_code']);
            if (!$group) {
                continue;
            }

            $persona = Persona::query()
                ->where('tenant_id', (int) $group->tenant_id)
                ->where('tipo_documento', $row['tipo_documento'])
                ->where('numero_documento', $row['numero_documento'])
                ->first();

            if (!$persona) {
                continue;
            }

            foreach ($row['tipos'] as $tipo) {
                TipoPersona::updateOrCreate(
                    [
                        'persona_id' => $persona->id,
                        'tipo' => $tipo,
                    ],
                    [
                        'estado' => true,
                        'observacion' => null,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );
            }
        }

        $this->command?->info('Partners tipos de personas creados/actualizados correctamente.');
    }
}
