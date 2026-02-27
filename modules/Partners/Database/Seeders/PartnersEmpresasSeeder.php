<?php

namespace Modules\Partners\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\GroupCompany;
use Modules\Partners\Models\Empresa;

class PartnersEmpresasSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('partners_empresas') || !Schema::hasTable('core_group_companies')) {
            $this->command?->warn('Tabla partners_empresas no encontrada. Ejecuta migraciones primero.');

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
                'ruc' => '20612345671',
                'razon_social' => 'Optica Vision Integral SAC',
                'nombre_comercial' => 'Vision Integral',
                'direccion' => 'Av. Arequipa 1001, Lima',
                'email' => 'contacto@visionintegral.pe',
                'telefono' => '+51 1 5010101',
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'ruc' => '20612345672',
                'razon_social' => 'Suministros Opticos Andinos SRL',
                'nombre_comercial' => 'SOA Distribuciones',
                'direccion' => 'Jr. Comercio 458, Lima',
                'email' => 'ventas@soa.pe',
                'telefono' => '+51 1 5020202',
                'estado' => true,
            ],
            [
                'group_code' => 'PE',
                'ruc' => '20612345673',
                'razon_social' => 'Clinica Ocular del Pacifico SA',
                'nombre_comercial' => 'Clinica Ocular Pacifico',
                'direccion' => 'Av. Javier Prado 2250, Lima',
                'email' => 'atencion@cop.pe',
                'telefono' => '+51 1 5030303',
                'estado' => true,
            ],
            [
                'group_code' => 'EC',
                'ruc' => '1791234567001',
                'razon_social' => 'Optica Andina del Ecuador SA',
                'nombre_comercial' => 'Optica Andina',
                'direccion' => 'Av. 9 de Octubre 123, Guayaquil',
                'email' => 'contacto@opticaandina.ec',
                'telefono' => '+593 4 2304040',
                'estado' => true,
            ],
            [
                'group_code' => 'EC',
                'ruc' => '1791234567002',
                'razon_social' => 'Distribuidora Visual Quito Cia Ltda',
                'nombre_comercial' => 'Visual Quito',
                'direccion' => 'Av. Amazonas 845, Quito',
                'email' => 'ventas@visualquito.ec',
                'telefono' => '+593 2 2909090',
                'estado' => true,
            ],
        ];

        foreach ($rows as $row) {
            $group = $groups->get($row['group_code']);
            if (!$group) {
                continue;
            }

            $empresa = Empresa::withTrashed()->firstOrNew([
                'tenant_id' => (int) $group->tenant_id,
                'ruc' => $row['ruc'],
            ]);

            $empresa->group_company_id = (int) $group->id;
            $empresa->razon_social = $row['razon_social'];
            $empresa->nombre_comercial = $row['nombre_comercial'];
            $empresa->direccion = $row['direccion'];
            $empresa->email = $row['email'];
            $empresa->telefono = $row['telefono'];
            $empresa->estado = (bool) $row['estado'];
            $empresa->created_by = $empresa->exists ? $empresa->created_by : $userId;
            $empresa->updated_by = $userId;
            $empresa->deleted_at = null;
            $empresa->save();
        }

        $this->command?->info('Partners empresas creadas/actualizadas correctamente.');
    }
}
